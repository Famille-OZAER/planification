<?php
require_once  '/var/www/html/core/php/core.inc.php';

class planification extends eqLogic {
	
	public static $_widgetPossibility = array('custom' => true);
	//debut fonctions ajax
	public function Set_widget_cache($_id,$_page){
		$eqLogic=eqLogic::byId($_id);
		$eqLogic->setCache('Page', $_page);
	}
  	public function Recup_planifications(){
		$eqLogic=$this;
		$nom_fichier=dirname(__FILE__) ."/../../planifications/" . $eqLogic->getId() . ".json";
		$planifications="";
		if(file_exists ( $nom_fichier ) ){$planifications=file_get_contents ($nom_fichier);}
		if($planifications==""){return [] ;}
		return json_decode($planifications,true);
	}
	public function Recup_select($type,$eqLogic_id) {
		$eqLogic = eqLogic::byId($eqLogic_id);
		if (!is_object($eqLogic)) {
		  throw new Exception(__('Impossible de trouver l\'équipement : ', __FILE__) . $eqLogic_id);
	  }
	  if ($type == "planification"){
		  $cmds=$eqLogic->getConfiguration("commandes_planification","");

		  $div ='<div class="select-selected expressionAttr #COULEUR#" id="#ID#" data-l1key = "couleur">';
			  $div .='<span>#VALUE#</span></div>';

			  $div .='<div class="select-items select-hide">';
			  
			if ($cmds !=""){
			  foreach ($cmds as $cmd) {
				  $div.='<div class="couleur-'.$cmd["couleur"].'" id="'.$cmd["Id"].'" value="'. $cmd["nom"] . '">';
					  $div.='<span>'.$cmd["nom"] .'</span>';
				  $div.='</div>';
			  }
			}
		  $div .='</div>';

	  }else{
		  $div ='<div class="select-selected commande expressionAttr #COULEUR#" data-l1key = "couleur">';
			  $div .='<span>#VALUE#</span></div>';
			  $div .='<div class="select-items select-hide">';
				  $div .=  '<div class ="commande couleur-orange" value="orange">orange</div>';
				  $div .=  '<div class ="commande couleur-jaune" value="jaune">jaune</div>';
				  $div .=  '<div class ="commande couleur-vert" value="vert">vert</div>';
				  $div .=  '<div class ="commande couleur-bleu" value="bleu">bleu</div>';
				  $div .=  '<div class ="commande couleur-rouge" value="rouge">rouge</div>';
				  $div .=  '<div class ="commande couleur-magenta" value="magenta">magenta</div>';
				  $div .=  '<div class ="commande couleur-marron" value="marron">marron</div>';
				  $div .=  '<div class ="commande couleur-violet" value="violet">violet</div>';	
			  $div.='</div>';
		  $div .='</div>';

	  }
		return $div;
  	}
 	 public function Recup_liste_commandes_planification($eqLogic_id) {
	  $eqLogic = eqLogic::byId($eqLogic_id);
	  if (!is_object($eqLogic)) {
		  throw new Exception(__('Impossible de trouver l\'équipement : ', __FILE__) . $eqLogic_id);
		}
	
	  return $eqLogic->getConfiguration("commandes_planification","");
		
	}
	public function Ajout_equipement($nom,$type){
		$eqLogic = new self();
		$eqLogic->setLogicalId($nom);
		$eqLogic->setName($nom);
		$eqLogic->setEqType_name('planification');
		$eqLogic->setIsVisible(0);
		$eqLogic->setIsEnable(1);
		$eqLogic->setConfiguration('type', $type);
		$eqLogic->save();
		return $eqLogic->getId();
	}
//fin fonctions ajax
function supp_accents( $str, $charset='utf-8' ) {
	$str = htmlentities( $str, ENT_NOQUOTES, $charset );
	$str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
	$str = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
	$str = preg_replace( '#&[^;]+;#', '', $str );
	return $str;
}
static function add_log($_eqLogic,$level = 'debug',$Log){
        if (is_array($Log)) $Log = json_encode($Log);
		$function_name = debug_backtrace(false, 2)[1]['function'];
		$ligne = debug_backtrace(false, 2)[0]['line'];
		//$class_name = debug_backtrace(false, 2)[1]['class'];
		$msg = '<'. $function_name .' (' . $ligne . ')> '.$Log;
		//$nom_eq=mb_convert_encoding (str_replace("[" , "_",str_replace("]" , "",$_eqLogic->getHumanName(false))), 'HTML-ENTITIES', 'UTF-8');
		$nom_eq= planification::supp_accents(str_replace(" " , "_",str_replace("[" , "_",str_replace("]" , "",$_eqLogic->getHumanName(false)))));
		log::add('planification'.$nom_eq  , $level,$msg);
					
}
	function pull($_option){
		$crons = cron::searchClassAndFunction('planification', 'pull');
		$cron_id="";
		foreach ($crons as $cron){
			if($cron_id=="" ){
				$options_cron=$cron->getOption();
				if($options_cron["eqLogic_Id"]== ($_option['eqLogic_Id'])){
					$cron_id=$cron->getId();
				}
			}
		}
		$cron=cron::byId($cron_id);
		if($cron->getNextRunDate() ==""){
			$prochain_cron=time();
		}else{
			if(date_create_from_format("Y-m-d H:i:s",$cron->getNextRunDate())->getTimestamp()>time()){return;}
		}
		
		$eqLogic = self::byId($_option['eqLogic_Id']);
		planification::add_log($eqLogic,"debug","pull de : " . $eqLogic->getName());
		$commande_en_cours="";
		$cmd_mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement');
		/*$action_en_cours=$eqLogic->Recup_action_actuelle();
  		try {
			if (!isset($action_en_cours['cmd'])){return;}
			$cmd =$action_en_cours['cmd'];
			$options = array();
			$options = $action_en_cours['options'];
			if (is_numeric (trim($cmd, "#"))){
				$cmd=cmd::byId(trim($cmd, "#"));
				if(is_object($cmd)){
					$eqLogic_cmd=eqLogic::byId($cmd->getEqLogic_id()) ;
					planification::add_log($eqLogic,"debug",'execution action: #[' . $eqLogic_cmd->getObject()->getName()."][".$eqLogic_cmd->getName()."][".$cmd->getName()."]#");
					$cmd->execCmd();
				}
			}else{
				$options_str="";
				if ($cmd=="variable"){$options_str=$options["name"] . "=>" .$options["value"];}
				planification::add_log($eqLogic,"debug",'execution action: ' . $cmd . ":" .$options_str);
					
				scenarioExpression::createAndExec('action', $cmd, $options);
			}
		}catch (Exception $e) {
			planification::add_log($eqLogic,"error",'Erreur lors de l\'éxecution de ' . $cmd['cmd'] .'. Détails : '. $e->getMessage());
		}*/
		$eqLogic->Execute_action_actuelle();
		if ($cmd_mode->execCmd()=="auto"){
			$eqLogic->set_cron();
		}else{
			planification::add_log($eqLogic,"debug", "remise de l'équipement en mode auto");
			$cmd_auto=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'auto');
			$cmd_auto->execute();
		}
	}

	function set_cron(){
		$eqLogic=$this;
		$crons = cron::searchClassAndFunction('planification', 'pull');
		$cron_id="";
		foreach ($crons as $cron){
			if($cron_id=="" ){
				$options=$cron->getOption();
				if($options["eqLogic_Id"]== intval($eqLogic->getId())){
					$cron_id=$cron->getId();
				}
			}
		}
		$cron=cron::byId($cron_id);
		
		$cmd_mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement');
		if (is_object($cmd_mode)){
			$mode = $cmd_mode->execCmd();
			if ($mode == "auto"){
				$prochaine_action=$eqLogic::Recup_prochaine_action();
				if ($prochaine_action['datetime'] != null) {					
					planification::add_log($eqLogic,"debug","Mode: " . $mode . " Replanification le " . $prochaine_action['datetime'] ." => ". $prochaine_action['nom']);
					$prochaine_action['datetime'] = strtotime($prochaine_action['datetime']);
					if (!is_object($cron)) {
						$cron = new cron();
						$cron->setClass('planification');
						$cron->setFunction('pull');
					}
					$cron->setOption(array('eqLogic_Id' => intval($eqLogic->getId()),'eqLogic'=> mb_convert_encoding ($eqLogic->getHumanName(false), 'HTML-ENTITIES', 'UTF-8')));
					$cron->setLastRun(date('Y-m-d H:i:s'));
					$cron->setSchedule(date('i', $prochaine_action['datetime']) . ' ' . date('H', $prochaine_action['datetime']) . ' ' . date('d', $prochaine_action['datetime']) . ' ' . date('m', $prochaine_action['datetime']) . ' * ' . date('Y', $prochaine_action['datetime']));
					$cron->save();
				} else {
					$cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
					if(is_object($cmd_action_suivante)){
						$cmd_action_suivante->event("");
					}
					$cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
					if(is_object($cmd_heure_fin)){
						$cmd_heure_fin->event("");
					}
					if (is_object($cron)) {
						$cron->remove();
					}
				}
			}else{
				if($eqLogic->getConfiguration("type","")=="Volet" && $mode == "manuel" ){
					if (is_object($cron)) {
						//var_dump($cron);
						$cron->remove();
					}
				}else{
						
					$cmd_date_prochaine_action=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin');
					if (is_object($cmd_date_prochaine_action)) {
						$date="";
						$date=$cmd_date_prochaine_action->execCmd();
						if ($date !=""){
						//	planification::add_log($eqLogic,"debug","date: #" . $date ."#");
					
							$datetime=date_create_from_format("d-m-Y H:i",$date);
							//planification::add_log($eqLogic,"debug","datetime: " . $datetime->format('d-m-Y H:i'));
							if (!is_object($cron)) {
								$cron = new cron();
								$cron->setClass('planification');
								$cron->setFunction('pull');
							}
							$cron->setOption(array('eqLogic_Id' => intval($eqLogic->getId()),'eqLogic'=> mb_convert_encoding ($eqLogic->getHumanName(false), 'HTML-ENTITIES', 'UTF-8')));
							$cron->setLastRun(date('Y-m-d H:i:s'));
							planification::add_log($eqLogic,"debug","Mode: " . $mode ." Replanification le " . $date ." => Auto");
							$cron->setSchedule( $datetime->format("i") . ' ' .  $datetime->format("H") . ' ' .  $datetime->format("d") . ' ' . $datetime->format("m") . ' * ' .  $datetime->format("Y"));
							$cron->save();	
						}				
					}
				}
			}
		}
	}
	public function Recup_prochaine_action(){
		$eqLogic=$this;
		$action_en_cours="";
		$cmd_action_en_cours=$eqLogic->getCmd(null, "action_en_cours");
		if(is_object($cmd_action_en_cours)){
			$action_en_cours=$cmd_action_en_cours->execCmd();
		}
		$maintenant=date_create_from_format ('Y-m-d H:i' ,date('Y-m-d H:i'));
		$numéro_jour=date('N');
		$Id_planification_en_cours=$eqLogic->getConfiguration("Id_planification_en_cours","");
		if($Id_planification_en_cours==""){return;}
		$CMD_LIST=$eqLogic::Recup_liste_commandes_planification($eqLogic->getId());
		$planifications=$eqLogic::Recup_planifications();
		$cette_planification=[];
		foreach($planifications as $planification){
			if($planification["Id"]==$Id_planification_en_cours){
				planification::add_log($eqLogic,"debug","planification en cours: " . $planification["nom_planification"]);
				//planification::add_log($eqLogic,"debug","action en cours: " . $action_en_cours);
				$cette_planification=$planification["semaine"];
			}
		}
		if(count($cette_planification) == 0){return;}
		for ($i = 1; $i <= 7; $i++) {
			if($numéro_jour>7){$numéro_jour -=7;}
			if (isset($cette_planification[$numéro_jour-1]["periodes"])){
				$periodes=$cette_planification[$numéro_jour-1]["periodes"];
				foreach($periodes as $periode){
					$date=date_add(date_create_from_format ( 'Y-m-d H:i' ,date('Y-m-d '). $periode["Debut_periode"]), date_interval_create_from_date_string($i-1 .' days'));
					if($date->getTimestamp() > $maintenant->getTimestamp()){
						foreach ($CMD_LIST as $cmd) {
							if($periode["Id"]==$cmd["Id"] && $cmd["nom"] != $action_en_cours){
								$action["datetime"]=$date->format('d-m-Y H:i');
								$action["nom"]=$cmd["nom"];
														
								$cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
								if(is_object($cmd_action_suivante)){
									$cmd_action_suivante->event($cmd["nom"]);
								}
								$cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
								if(is_object($cmd_heure_fin)){
									$cmd_heure_fin->event($date->format('d-m-Y H:i'));
								}
								//planification::add_log($eqLogic,"debug","action :" . implode('|',$action));
								return $action;
							}
						}
					}				
				}
			}
			$numéro_jour+=1;
			
		}
		return;
	}
	public function Execute_action_actuelle(){
		$mode_fonctionnement="auto";
		$eqLogic=$this;
		$cmd_mode_fonctionnement = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), "mode_fonctionnement");
			if(is_object($cmd_mode_fonctionnement)){
				$mode_fonctionnement=$cmd_mode_fonctionnement->execCmd();
					
			}
		$action_en_cours="";
		$cmd_action_en_cours=$eqLogic->getCmd(null, "action_en_cours");
		if(is_object($cmd_action_en_cours)){
			$action_en_cours=$cmd_action_en_cours->execCmd();
		}





		if($mode_fonctionnement == "arret"){
			$crons = cron::searchClassAndFunction('planification', 'pull');
			$cron_id="";
			foreach ($crons as $cron){
				if($cron_id=="" ){
					$options=$cron->getOption();
					if($options["eqLogic_Id"]== intval($eqLogic->getId())){
						$cron_id=$cron->getId();
					}
				}
			}
			$cron=cron::byId($cron_id);
			if (is_object($cron)) {$cron->remove();}
			$eqLogic->checkAndUpdateCmd('heure_fin', '');
			return [];
		}
		$cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin');
		$timestamp_prochaine_action=time();
		if (is_object($cmd)){
			$val=$cmd->execCmd();
			if (is_numeric($val)){
				$timestamp_prochaine_action=$val;
			}
		}
		$numéro_jour=date('N');
		$Id_planification_en_cours=$eqLogic->getConfiguration("Id_planification_en_cours","");
		if($Id_planification_en_cours==""){return;}
		$CMD_LIST=$eqLogic::Recup_liste_commandes_planification($eqLogic->getId());
		$planifications=$eqLogic::Recup_planifications();
		$cette_planification=[];
		foreach($planifications as $planification){
			if($planification["Id"]==$Id_planification_en_cours){
				$cette_planification=$planification["semaine"];
				break;
			}
		}
		if(count($cette_planification) == 0){return;}
		//planification::add_log($eqLogic,"debug","numéro_jour:".$numéro_jour);
		$numBoucle=0;				
		for ($i = $numéro_jour; $i > $numéro_jour-7; $i--) {
			$num=$i;
			if($i<1){$num = 7-$i;}
			//planification::add_log($eqLogic,"debug","num :" . $num);	
			$trouve=false;
			if (isset($cette_planification[ $num-1]["periodes"])){
				//planification::add_log($eqLogic,"debug",$cette_planification[ $num-1]["jour"]);	
				$periodes=$cette_planification[$num-1]["periodes"];
				$action=[];
				
				foreach($periodes as $periode){
					$date=date_add(date_create_from_format ( 'Y-m-d H:i' ,date('Y-m-d '). $periode["Debut_periode"]), date_interval_create_from_date_string(-$numBoucle.' days'));
					//planification::add_log($eqLogic,"debug",implode("|",$periode));
					//planification::add_log($eqLogic,"debug","date:".$date->format(' d-m-Y H:i'));
					if($date->getTimestamp() <= $timestamp_prochaine_action){
						$trouve=true;
						foreach ($CMD_LIST as $cmd) {
							if($periode["Id"]==$cmd["Id"]){
								
								$action["datetime="]=$date->format(' d-m-Y H:i');
								$action["nom"]=$cmd["nom"];
								$action["cmd"]=$cmd["cmd"];
								$action["Id"]=$cmd["Id"];
								if (isset($cmd["options"])){
									$action["options"]=$cmd["options"];
								}else{
									$action["options"]="";	
								}
								break;
							}
						}
					}
				} 
				
			}
			if($trouve){
				
				
				if(is_object($cmd_action_en_cours)){
					if ($action_en_cours != $action['nom']){
						planification::add_log($eqLogic,"debug","action_actuelle:".$action["nom"]);
						try {
					
							$cmd =$action['cmd'];
							$options = array();
							$options = $action['options'];
							if (is_numeric (trim($cmd, "#"))){
								$cmd=cmd::byId(trim($cmd, "#"));
								if(is_object($cmd)){
									$eqLogic_cmd=eqLogic::byId($cmd->getEqLogic_id()) ;
									planification::add_log($eqLogic,"debug",'execution action: #[' . $eqLogic_cmd->getObject()->getName()."][".$eqLogic_cmd->getName()."][".$cmd->getName()."]#");
									$cmd->execCmd();
								}
							}else if ($cmd !=""){
								$options_str="";
								if ($cmd=="variable"){$options_str=$options["name"] . "=>" .$options["value"];}
								planification::add_log($eqLogic,"debug",'execution action: ' . $cmd . ":" .$options_str);
									
								scenarioExpression::createAndExec('action', $cmd, $options);
							}
						}catch (Exception $e) {
							planification::add_log($eqLogic,"error",'Erreur lors de l\'éxecution de ' . $cmd['cmd'] .'. Détails : '. $e->getMessage());
						}
						
					}
					$cmd_action_en_cours->event($action["nom"]);
				}
				return;
			}		
			$numBoucle+=1;
		}
	
	}
	public function Importer_commandes_eqlogic($_eqLogic_id) {
	
		$eqLogic = eqLogic::byId($_eqLogic_id);
		if (!is_object($eqLogic)) {
			throw new Exception(__('Impossible de trouver l\'équipement : ', __FILE__) . $_eqLogic_id);
		}
		if ($eqLogic->getEqType_name() == 'planification') {
			throw new Exception(__('Vous ne pouvez importer les commandes d\'un équipement planification', __FILE__));
		}
		$cmds_prog=[];
		$arr=$eqLogic->getConfiguration('commandes_planification', "");
		for ($i = 0; $i < count($arr); $i++) {
			$cmd_prog["Id"] = $arr[$i]["Id"];
			$cmd_prog["nom"] = $arr[$i]["nom"];
			$cmd_prog["cmd"] = $arr[$i]["cmd"];
			$cmd_prog["couleur"] = $arr[$i]["couleur"];
			array_push ( $cmds_prog, $cmd_prog );				
		}
		$cmd_prog=[];
		
		
		foreach ($eqLogic->getCmd() as $cmd) {
			if ($cmd->getName() != 'Rafraichir' and $cmd->getType() == 'action') {
				$cmd_prog["nom"] = $cmd->getName();
				$cmd_prog["cmd"] = '#' . $cmd->getId() .'#';
				$cmd_prog["couleur"] = "jaune";
				array_push ( $cmds_prog, $cmd_prog );
			}
		}
		
		$eqLogic->setConfiguration('commandes_planification', $cmds_prog);
		$eqLogic->save();
	} 
    function Ajout_Commande($logical_id,$name,$type,$sous_type,$min=null,$max=null,$valeur_par_defaut=null,$unite=null){
		$eqLogic=$this;
			$cmd = $eqLogic->getCmd(null, $logical_id);
			if (!is_object($cmd)) {
				$cmd = new planificationCmd();
				$cmd->setLogicalId($logical_id);
				$cmd->setIsVisible(1);
				$cmd->setName($name);
				$cmd->setType($type);
				$cmd->setSubType($sous_type);
				$cmd->setConfiguration('minValue',$min);
				$cmd->setConfiguration('maxValue',$max);
				$cmd->setEqLogic_id($eqLogic->getId());
				$cmd->save();
				if($valeur_par_defaut!=null){
					$eqLogic->checkAndUpdateCmd($logical_id,$valeur_par_defaut);
				}
			
			
			}
	}
	public function preSave() {
		$eqLogic=$this;
     if ($eqLogic->getConfiguration('type','') == ''){
		   	$eqLogic->setConfiguration('type', 'Autre');
			$eqLogic->setIsVisible(1);
           	$eqLogic->setIsEnable(1);
        }
    }
		
    public function postSave() {
		$eqLogic=$this;
		$eqLogic->Ajout_Commande('refresh','Rafraichir','action','other');
		$eqLogic->Ajout_Commande('mode_fonctionnement','Mode fonctionnement','info','string',null,null,"auto");
		$eqLogic->Ajout_Commande('auto','Auto','action','other');
		
		$eqLogic->Ajout_Commande('set_heure_fin','Set heure fin','action','message');
		$eqLogic->Ajout_Commande('heure_fin','Heure fin mode en cours','info','string');
		$eqLogic->Ajout_Commande('action_en_cours','Action en cours','info','string');
		$eqLogic->Ajout_Commande('action_suivante','Action suivante','info','string');
		$eqLogic->Ajout_Commande('planification_en_cours','Planification en cours','info','string');	
		
			
		//$eqLogic->Ajout_Commande('set_mode_fonctionnement','Set mode fonctionnement','action','other');
		$cmd = $eqLogic->getCmd(null, "set_planification");
		if (!is_object($cmd)) {
			$cmd = new planificationCmd();
			$cmd->setLogicalId("set_planification");
			$cmd->setIsVisible(1);
			$cmd->setName("Set planification");
			$cmd->setType("action");
			$cmd->setSubType("select");
			$cmd->setEqLogic_id($eqLogic->getId());
			$cmd->setConfiguration("infoName", $eqLogic->getCmd(null, "planification_en_cours")->getName());
			$cmd->setConfiguration("infoId", $eqLogic->getCmd(null, "planification_en_cours")->getId());
			$cmd->setValue( $eqLogic->getCmd(null, "planification_en_cours")->getId());
		}
		
		
		$liste="";
		$cmd->setConfiguration("listValue",$liste);
		$cmd->save();
		if ($eqLogic->getConfiguration('type','') == 'Poele'){
			$eqLogic->Ajout_Commande('duree_mode_manuel_par_defaut','Duree mode manuel par defaut (minutes)','info','numeric',null,null,60);
			$eqLogic->Ajout_Commande('absent','Absent','action','message');
			$eqLogic->Ajout_Commande('force','Forcé','action','other');
			$eqLogic->Ajout_Commande('arret','Arrêt','action','other');
			$eqLogic->Ajout_Commande('set_consigne_temperature','Set consigne température','action','slider',7,30);
			$eqLogic->Ajout_Commande('consigne_temperature','Consigne Temperature','info','numeric',null,null,20,'°C');
			$eqLogic->Ajout_Commande('temperature_consigne_par_defaut','Température consigne par defaut','info','numeric',null,null,20);	
		}
			
		if ($eqLogic->getConfiguration('type','') == 'PAC'){
			$eqLogic->Ajout_Commande('duree_mode_manuel_par_defaut','Duree mode manuel par defaut (minutes)','info','numeric',null,null,60);
			$eqLogic->Ajout_Commande('absent','Absent','action','message');
			$eqLogic->Ajout_Commande('climatisation','Climatisation','action','other');
			$eqLogic->Ajout_Commande('ventilation','Ventilation','action','other');
			$eqLogic->Ajout_Commande('chauffage','Chauffage','action','other');
			$eqLogic->Ajout_Commande('ventilation','Ventilation','action','other');
			$eqLogic->Ajout_Commande('arret','Arrêt','action','other');
			$eqLogic->Ajout_Commande('set_consigne_temperature','Set consigne température','action','slider',7,30);
			$eqLogic->Ajout_Commande('consigne_temperature','Consigne Temperature','info','numeric',null,null,20,'°C');
			$eqLogic->Ajout_Commande('boost','Boost','info','binary',null,null,0);
			$eqLogic->Ajout_Commande('boost_on','Boost On','action','other');
			$eqLogic->Ajout_Commande('boost_off','Boost Off','action','other');
			$eqLogic->Ajout_Commande('mode_PAC','Mode_PAC','info','string');
		}
	
		if ($eqLogic->getConfiguration('type','') == 'chauffage'){
			$eqLogic->Ajout_Commande('confort','Confort','action','other');
			$eqLogic->Ajout_Commande('eco','Eco','action','other');
			$eqLogic->Ajout_Commande('hors_gel','Hors gel','action','other');
			$eqLogic->Ajout_Commande('absent','Absent','action','other');
			$eqLogic->Ajout_Commande('arret','Arrêt','action','other');			
			$eqLogic->Ajout_Commande('set_action_en_cours','Set action en cours','action','other');
		}
		
		if ($eqLogic->getConfiguration('type','') == 'Volet'){
			$eqLogic->Ajout_Commande('set_action_en_cours','Set action en cours','action','other');
			$eqLogic->Ajout_Commande('manuel','Manuel','action','other');
			
		}

        $cmd_set_planification = $eqLogic->getCmd(null, "set_planification");
		if (!is_object($cmd_set_planification)){return;}
		$planifications=[];
		$planifications=$eqLogic::Recup_planifications();
		$arr=[];
		switch (count($planifications)) {
			case 0:
				$arr["select"]="";
				$arr["Id_planification"]="";
				break;
			case 1:
				$arr["select"]=$planifications[0]["nom_planification"];
				$arr["Id_planification"]=$planifications[0]["Id"];
				break;
			default:
		}

		$liste="";
		$Id_planification_en_cours=$eqLogic->getConfiguration("Id_planification_en_cours","");
		foreach($planifications as $planification){
			if($planification["Id"]==$Id_planification_en_cours){							
					$arr["select"]=$planification["nom_planification"];
					$arr["Id_planification"]=$planification["Id"];
			}
			if($liste==""){
				$liste .=$planification["nom_planification"] ."|" . $planification["nom_planification"];
			}else{
				$liste .= ";" .$planification["nom_planification"] ."|" . $planification["nom_planification"];
			}
		}
		
		$cmd_set_planification->setConfiguration("listValue",$liste);
		$cmd_set_planification->save();
		if(!isset($arr["select"]) && count($planifications)!=0){$arr["select"]=$planifications[0]["nom_planification"];}
		if(!isset($arr["Id_planification"]) && count($planifications)!=0){$arr["Id_planification"]=$planifications[0]["Id"];}
		$cmd_set_planification->execute($arr);

		$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
		if (is_object($cmd_refresh)){
			$cmd_refresh->execute();
		}
    }
	function replace_into_html(&$erreur,&$liste_erreur,&$replace,$parametre,$commande,$type){
		$eqLogic=$this;
		if (is_object($commande)){
			switch ($type) {
				case ("value"):
                	$valeur = $commande->execCmd();
                	
                	if(strlen($valeur) != 0){
                      $replace[$parametre] = $valeur;
                    }else{
					   //$replace[$parametre] = "non renseigné";
					    $replace[$parametre] = "";
                    }
					
					break;
				case("name"):
					$replace[$parametre] = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$commande->execCmd())->getName();
					break;
				case("id"):
					$replace[$parametre] = $commande->getId();
					break;
				case("max"):
					$replace[$parametre] = $commande->getConfiguration("maxValue",30);
					break;
				case("min"):
					$replace[$parametre] = $commande->getConfiguration("minValue",7);
					break;
				}
			
		}else{
			$erreur=true;
			array_push( $liste_erreur , $parametre) ;
			$replace[$parametre] = "";
		}
	}
		
	public function toHtml($_version = 'dashboard') {
		$eqLogic=$this;
		$replace = $eqLogic->preToHtml($_version);
		if (!is_array($replace)) {
			return $replace;
		}
		$version = jeedom::versionAlias($_version);
		if ($eqLogic->getDisplay('hideOn' . $version) == 1) {
			return '';
		}
		$erreur=false;
		$liste_erreur=[];

		
		$commande = $eqLogic->getCmd(null, 'planification_en_cours');
		//$planification_en_cours=$commande->execCmd();
		if (is_object($commande)){
			$liste_planifications = $eqLogic::Recup_planifications();
			$Id_planification_en_cours=$eqLogic->getConfiguration("Id_planification_en_cours","");

			
			foreach($liste_planifications as $planification){

				if($planification["Id"]==$Id_planification_en_cours){
					$valuecalendar = '"'.$planification["nom_planification"]. '" selected';
				}else{
					$valuecalendar = '"'.$planification["nom_planification"].'"';
				}
				if (!isset($replace['#calendar_selector#'])) {
					$replace['#calendar_selector#'] = '<option id=' .$planification["Id"]. ' value=' .$valuecalendar . '>' . $planification["nom_planification"] . '</option>';
				} else {
					$replace['#calendar_selector#'] .= '<option id=' .$planification["Id"]. ' value=' . $valuecalendar . '>' . $planification["nom_planification"]. '</option>';
				}
			}
		}

		$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#mode_fonctionnement#',$eqLogic->getCmd(null, 'mode_fonctionnement'),"value");
		$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#mode_fonctionnement_name#',$eqLogic->getCmd(null, 'mode_fonctionnement'),'name');
		$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#refresh_id#',$eqLogic->getCmd(null, 'refresh'),"id");
		$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#auto_id#',$eqLogic->getCmd(null, 'auto'),"id");
		
		$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#endtime_change_id#',$eqLogic->getCmd(null, 'set_heure_fin'),"id");
		$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#endtime#',$eqLogic->getCmd(null, 'heure_fin'),"value");
		$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_planification_id#',$eqLogic->getCmd(null, 'set_planification'),"id");
		$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#planification_en_cours#',$eqLogic->getCmd(null, 'planification_en_cours'),"value");
       
		
		$page_active=$eqLogic->getCache('Page');
		if($page_active =="" || $page_active=="page1"){
			$replace['#display_page_1#']="block";
			$replace['#display_page_2#']="none";
		}else{
			$replace['#display_page_1#']="none";
			$replace['#display_page_2#']="block";
		}

		if ($eqLogic->getConfiguration("type","")== "Poele"){
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#arret_id#',$eqLogic->getCmd(null, 'arret'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#consigne_temperature#',$eqLogic->getCmd(null, 'consigne_temperature'),"value");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#consigne_temperature_id#',$eqLogic->getCmd(null, 'consigne_temperature'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#absent_id#',$eqLogic->getCmd(null, 'absent'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#chauffage_id#',$eqLogic->getCmd(null, 'force'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_id#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_min#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"min");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_max#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"max");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#action_en_cours#',$eqLogic->getCmd(null, 'action_en_cours'),"value");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#prochaine_action#',$eqLogic->getCmd(null, 'action_suivante'),"value");
			$cmd_temperature=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('temperature_id',"")));
			
			if (is_object($cmd_temperature)){
				$replace['#temperature#'] = $cmd_temperature->execCmd() . " °C";
				$replace['#temperature_id#'] = $cmd_temperature->getId();
			}else{
				$replace['#temperature#'] = "";
				$replace['#temperature_id#']="";
			}

			$imagePoele="PoeleOff.png";
			$cmd_Etat_Allume=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('etat_allume_id',"")));
			if (is_object($cmd_Etat_Allume)){
				if($cmd_Etat_Allume->execCmd())
				{
					$imagePoele="PoeleOn.png";
					$cmd_Etat_Boost=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('etat_boost_id',"")));
					if (is_object($cmd_Etat_Boost)){
						if($cmd_Etat_Boost->execCmd())
						{
							$imagePoele="PoeleOnBoost.png";
						}
					}
				}
			}
			$replace['#img_poele#'] = $imagePoele;
			if ($erreur){
				$replace['#display_erreur#'] ="block";
			}else{
				$replace['#display_erreur#'] ="none";
			}	
			$html = template_replace($replace, getTemplate('core', $version, 'poele', 'planification'));
		}
		
		if ($eqLogic->getConfiguration("type","")== "PAC"){
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#arret_id#',$eqLogic->getCmd(null, 'arret'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#consigne_temperature#',$eqLogic->getCmd(null, 'consigne_temperature'),"value");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#consigne_temperature_id#',$eqLogic->getCmd(null, 'consigne_temperature'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#boost_on_id#',$eqLogic->getCmd(null, 'boost_on'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#boost_off_id#',$eqLogic->getCmd(null, 'boost_off'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#boost#',$eqLogic->getCmd(null, 'boost'),"value");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#absent_id#',$eqLogic->getCmd(null, 'absent'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#climatisation_id#',$eqLogic->getCmd(null, 'climatisation'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#ventilation_id#',$eqLogic->getCmd(null, 'ventilation'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#chauffage_id#',$eqLogic->getCmd(null, 'chauffage'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_id#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_min#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"min");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_max#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"max");
			$cmd_temperature=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('temperature_id',"")));
			if (is_object($cmd_temperature)){
				$replace['#temperature#'] = $cmd_temperature->execCmd() . " °C";
				$replace['#temperature_id#'] = $cmd_temperature->getId();
			}else{
				$replace['#temperature#'] = "";
				$replace['#temperature_id#']="";
			}

			
			$cmd_action_en_cours=$eqLogic->getCmd(null, 'action_en_cours');
			if (is_object($cmd_action_en_cours)){
				$action_en_cours=$cmd_action_en_cours->execCmd();
			}
			if($action_en_cours== "Allumé"){
				$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#action_en_cours#',$eqLogic->getCmd(null, 'mode_PAC'),"value");
				$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#prochaine_action#',$eqLogic->getCmd(null, 'action_suivante'),"value");
			}else{
				$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#action_en_cours#',$eqLogic->getCmd(null, 'action_en_cours'),"value");
				$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#prochaine_action#',$eqLogic->getCmd(null, 'action_suivante'),"value");

			}
			$imagePAC="PACArret.png";
			$cmd_Mode_fonctionnement=$eqLogic->getCmd(null, 'mode_fonctionnement');
			if (is_object($cmd_Mode_fonctionnement)){
				$Mode_fonctionnement=$cmd_Mode_fonctionnement->execCmd();
			}
			switch (strtolower($Mode_fonctionnement)) {
				case "climatisation":
					$imagePAC="PACClimatisation.png";
				break;
				case"chauffage";
					$imagePAC="PACChauffage.png";
				break;
				case"ventilation":
					$imagePAC="PACVentilation.png";
					break;
				case "arret":
					$imagePAC="PACArret.png";
					break;
				case "auto";
					
					if($action_en_cours== "Allumé"){
						$cmd_mode_PAC=$eqLogic->getCmd(null, 'mode_PAC');
						if (is_object($cmd_mode_PAC)){
							$mode_PAC=$cmd_mode_PAC->execCmd();
							switch (strtolower($mode_PAC)) {
								case "climatisation":
									$imagePAC="PACClimatisation.png";
								break;
								case"chauffage";
									$imagePAC="PACChauffage.png";
								break;
								case"ventilation":
									$imagePAC="PACVentilation.png";
									break;
								case "arrêt":
									$imagePAC="PACArret.png";
									break;
							}

						}
					}else{
						$imagePAC="PACArret.png";
					}
					
				break;
			}

				
			
			$replace['#img_pac#'] = $imagePAC;
			if ($erreur){
				$replace['#display_erreur#'] ="block";
			}else{
				$replace['#display_erreur#'] ="none";
			}	
			$html = template_replace($replace, getTemplate('core', $version, 'PAC', 'planification'));
		}
		if ($eqLogic->getConfiguration("type","")== "Volet"){
			$CMD_LIST=$eqLogic::Recup_liste_commandes_planification($eqLogic->getId());
			foreach ($CMD_LIST as $CMD) {
				switch (strtolower($CMD["nom"])) {
					case 'monter':
					case 'lever':
					case 'ouvrir':
					case 'ouvert':
					case 'ouverture':
						if (is_numeric(str_replace ("#" ,"" ,  $CMD["cmd"]))){
							$replace['#ouvrir_id#'] =str_replace ("#" ,"" ,  $CMD["cmd"]);
						}						
						break;
					case 'descendre':
					case 'fermer':
					case 'fermeture':
					case 'fermé':
						if (is_numeric(str_replace ("#" ,"" ,  $CMD["cmd"]))){
							$replace['#fermer_id#'] =str_replace ("#" ,"" ,  $CMD["cmd"]);
						}
						break;
					case 'stop my':
					case 'stop':
					case 'my':
						if (is_numeric(str_replace ("#" ,"" ,  $CMD["cmd"]))){
							$replace['#my_id#'] =str_replace ("#" ,"" ,  $CMD["cmd"]);
						}
						break;
					
				}
			}
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_action_en_cours_id#',$eqLogic->getCmd(null, 'set_action_en_cours'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#manuel_id#',$eqLogic->getCmd(null, 'manuel'),"id");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#action_en_cours#',$eqLogic->getCmd(null, 'action_en_cours'),"value");
			$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#prochaine_action#',$eqLogic->getCmd(null, 'action_suivante'),"value");
			$imageVolet="Volet-100.png";
			$cmd_Mode_fonctionnement=$eqLogic->getCmd(null, 'mode_fonctionnement');
			if (is_object($cmd_Mode_fonctionnement)){

				$Mode_fonctionnement=$cmd_Mode_fonctionnement->execCmd();
				//planification::add_log($eqLogic,"debug","mode de fonctionnement" . $Mode_fonctionnement);
				$replace['#img_auto_manu#'] = ucfirst($Mode_fonctionnement);
				
				$cmd_action_en_cours=$eqLogic->getCmd(null, 'action_en_cours');
				if (is_object($cmd_action_en_cours)){
					$action_en_cours=$cmd_action_en_cours->execCmd();
					planification::add_log($eqLogic,"debug",'action_en_cours:' . $action_en_cours);
					switch (strtolower($action_en_cours)) {
						case "ouvert":
						case "ouvrir":
						case "monter":
							$imageVolet="Volet-100.png";
							break;
						case "fermé";
						case "fermer";
						case "fermer";
						case "descendre";
							$imageVolet="Volet-0.png";
							break;
						case "my":
							$imageVolet="Volet-50.png";
							break;

					}
				}
			}
			$replace['#img_volet#'] = $imageVolet;
			if ($erreur){
				$replace['#display_erreur#'] ="block";
			}else{
				$replace['#display_erreur#'] ="none";
			}	
			$html = template_replace($replace, getTemplate('core', $version, 'volet', 'planification'));
		}
		
		if ($erreur){
			$replace['#display_erreur#'] ="block";
		}else{
			$replace['#display_erreur#'] ="none";
		}
		cache::set('widgetHtml' . $version . $eqLogic->getId(), $html, 0);
		return $html;
	}

    public function postRemove() {
		$eqLogic=$this;
		$nom_fichier = dirname(__FILE__)."/../../planifications/" . $eqLogic->getId() . ".json";
		planification::add_log($eqLogic,"debug","nom_fichier:".$nom_fichier);
        if (file_exists($nom_fichier)) {
            unlink($nom_fichier);
        }
	}
	public function preRemove() {
		
		$eqLogic=$this;
		
		$nom_fichier = dirname(__FILE__)."/../../planifications/" . $eqLogic->getId() . ".json";
		planification::add_log($eqLogic,"debug","nom_fichier:".$nom_fichier);
        if (file_exists($nom_fichier)) {
            unlink($nom_fichier);
        }
    }
}

class planificationCmd extends cmd {
    //public static $_widgetPossibility = array('custom' => false);

    public function dontRemoveCmd() {
        return true;
    }

    public function execute($_options = array()) {
		
		
		$cmd=$this;
		$eqLogic = $cmd->getEqLogic();
		planification::add_log($eqLogic,"debug","execute: " . $cmd->getLogicalId());
		switch ($cmd->getLogicalId()) {
			case 'refresh':
				$eqLogic->refresh();
				$eqLogic->Execute_action_actuelle();
				$eqLogic->set_cron();
				break;
			case 'set_consigne_temperature':
				$eqLogic->checkAndUpdateCmd('consigne_temperature',$_options["slider"]);
				if ($eqLogic->getConfiguration("type","")== "Poele"){
					$cmd_mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement');
					if (is_object($cmd_mode)){
						$mode = $cmd_mode->event("force");
					}
				}
				break;
			case 'auto':
			case 'force':
			case 'arret':
			case 'hors_gel':
			case 'chauffage':
			case 'climatisation':
			case 'absent':
			case 'ventilation':
			case 'manuel':
				$eqLogic->checkAndUpdateCmd('mode_fonctionnement', $cmd->getLogicalId());
				if($cmd->getLogicalId() == "auto"){
					$cmd_temperature_consigne_par_defaut=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'temperature_consigne_par_defaut');
					if ($eqLogic->getConfiguration("type","")== "Poele"){
						$temperature_consigne_par_defaut=20;
						if (is_object($cmd_temperature_consigne_par_defaut)){
							$temperature_consigne_par_defaut=$cmd_temperature_consigne_par_defaut->execCmd();
						}
						$eqLogic->checkAndUpdateCmd('consigne_temperature', $temperature_consigne_par_defaut);
					}
					$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
					if (is_object($cmd_refresh)){
						$cmd_refresh->execute();
					}
				}else if ($cmd->getLogicalId() == "arret" || $cmd->getLogicalId() == "manuel" ){
					$crons = cron::searchClassAndFunction('planification', 'pull');
					foreach ($crons as $cron){
						$options_cron=$cron->getOption();
						if($options_cron["eqLogic_Id"]== $eqLogic->getId()){
							$cron->remove();
						}
					}					
				}else{
					$cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
					if (is_object($cmd_set_heure_fin)){
						$cmd_duree_mode_manuel_par_defaut=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'duree_mode_manuel_par_defaut');
						$duree_mode_manuel_par_defaut=60;
						if (is_object($cmd_duree_mode_manuel_par_defaut)){
							$duree_mode_manuel_par_defaut=$cmd_duree_mode_manuel_par_defaut->execCmd();
						}
						planification::add_log($eqLogic,"debug","Heure1: " . $duree_mode_manuel_par_defaut);
						$date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
						planification::add_log($eqLogic,"debug","date_Fin" . date ("Y/m/d H:i", $date_Fin));
						$arr=["message" => date ('d-m-Y H:i', $date_Fin)];
						$cmd_set_heure_fin->execute( $arr) ;
					}
				}
				
				break;
			case 'set_heure_fin':
				//planification::add_log($eqLogic,"debug","Heure: " . $_options['message']);
				if (strtotime("now") > strtotime($_options['message'])){
					throw new Exception("Veuillez selectionner une date et heure supérieure à maintenant");
				}
				$eqLogic->checkAndUpdateCmd('heure_fin', date('d-m-Y H:i',strtotime($_options['message'])));
				$cmd_mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement');
				if (is_object($cmd_mode)){
					$mode = $cmd_mode->execCmd();
					if ($mode != "auto"){
						$eqLogic->set_cron();
					}
				}
				break;
			case 'set_planification':
		
				if (isset($_options["select"]) && !isset( $_options["Id_planification"])){
					$planifications=$eqLogic->Recup_planifications();
					foreach($planifications as $planification){
						if($_options["select"]==$planification["nom_planification"]){	
							$_options["Id_planification"]=$planification["Id"];
							break;
						}
					}
				}
				$eqLogic->checkAndUpdateCmd('planification_en_cours',$_options["select"]);
				$eqLogic->setConfiguration("Id_planification_en_cours",$_options["Id_planification"]);
				$eqLogic->save(true);
				$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
				if (is_object($cmd_refresh)){
					$cmd_refresh->execute();
				}
				break;
			case 'set_action_en_cours':
				planification::add_log($eqLogic,"debug",$_options["message"]);
				$eqLogic->checkAndUpdateCmd('action_en_cours',$_options["message"]);
				$cmd_action=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours');
				if (is_object($cmd_action)){
					$cmd_action_val=$cmd_action->execCmd();
					planification::add_log($eqLogic,"debug","val:" . $cmd_action_val);
				}
				$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
				if (is_object($cmd_refresh)){
					$cmd_refresh->execute();
				}
				break;
			case 'boost_on':
		
				$eqLogic->checkAndUpdateCmd('boost', 1);
				$eqLogic->checkAndUpdateCmd('mode_fonctionnement', $cmd->getLogicalId());
				$cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
				if (is_object($cmd_set_heure_fin)){
					$cmd_duree_mode_manuel_par_defaut=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'duree_mode_manuel_par_defaut');
					$duree_mode_manuel_par_defaut=60;
					if (is_object($cmd_duree_mode_manuel_par_defaut)){
						$duree_mode_manuel_par_defaut=$cmd_duree_mode_manuel_par_defaut->execCmd();
					}
					planification::add_log($eqLogic,"debug","Heure1: " . $duree_mode_manuel_par_defaut);
					$date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
					planification::add_log($eqLogic,"debug","date_Fin" . date ("Y/m/d H:i", $date_Fin));
					$arr=["message" => date ('d-m-Y H:i', $date_Fin)];
					$cmd_set_heure_fin->execute( $arr) ;
				}
				$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
				if (is_object($cmd_refresh)){
					$cmd_refresh->execute();
				}
				break;

			case 'boost_off':
	
				$eqLogic->checkAndUpdateCmd('boost', 0);
				$eqLogic->checkAndUpdateCmd('mode_fonctionnement', 'auto');
				$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
				if (is_object($cmd_refresh)){
					$cmd_refresh->execute();
				}
				break;
		}
		
		$eqLogic->refreshWidget() ;
    }
	public function preSave() {
     
	}
}
?>