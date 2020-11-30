<?php
require_once  '/var/www/html/core/php/core.inc.php';

class planification extends eqLogic {
	public static $_widgetPossibility = array('custom' => true);
	
  	function recup_planifications(){
		$dossier = dirname(__FILE__) . '/../../planifications/';
		if (!is_dir($dossier)) mkdir($dossier, 0755, true);
		$nom_fichier=dirname(__FILE__) ."/../../planifications/" . $this->getId() . ".json";
		$planifications="";
		if(file_exists ( $nom_fichier ) ){$planifications=file_get_contents ($nom_fichier);}
		if($planifications==""){return ;}
		return json_decode($planifications,true);
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
		log::add('planification', 'debug', "pull de : " . $eqLogic->getName() );
		$commande_en_cours="";
		$cmd_mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement');
		if (!is_object($cmd_mode)) {return;	}
		$action_en_cours=$eqLogic->Recup_action_actuelle();
  		try {
			if (!isset($action_en_cours['cmd'])){return;}
			$cmd =$action_en_cours['cmd'];
			$options = array();
			$options = $action_en_cours['options'];
			if (is_numeric (trim($cmd, "#"))){
				$cmd=cmd::byId(trim($cmd, "#"));
				if(is_object($cmd)){
					$eqLogic_cmd=eqLogic::byId($cmd->getEqLogic_id()) ;
					log::add('planification', 'debug', 'execution action: #[' . $eqLogic_cmd->getObject()->getName()."][".$eqLogic_cmd->getName()."][".$cmd->getName()."]#");
					$cmd->execCmd();
				}
			}else{
				$options_str="";
				if ($cmd=="variable"){$options_str=$options["name"] . "=>" .$options["value"];}
				log::add('planification', 'debug', 'execution action: ' . $cmd . ":" .$options_str );
				scenarioExpression::createAndExec('action', $cmd, $options);
			}
		}catch (Exception $e) {
			log::add('planification', 'error', __('Erreur lors de l\'éxecution de ', __FILE__) . $cmd['cmd'] . __('. Détails : ', __FILE__) . $e->getMessage());
		}
		if ($cmd_mode->execCmd()=="auto"){
			$eqLogic->set_cron();
		}else{
			log::add('planification', 'debug', "remise de l'équipement en mode auto");
			$cmd_auto=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'auto');
			$cmd_auto->execute();
		}
	}

	function set_cron(){
		$crons = cron::searchClassAndFunction('planification', 'pull');
		$cron_id="";
		foreach ($crons as $cron){
			if($cron_id=="" ){
				$options=$cron->getOption();
				if($options["eqLogic_Id"]== intval($this->getId())){
					$cron_id=$cron->getId();
				}
			}
		}
		$cron=cron::byId($cron_id);
		if (!is_object($cron)) {
			$cron = new cron();
			$cron->setClass('planification');
			$cron->setFunction('pull');
		}
		$cmd_mode=cmd::byEqLogicIdAndLogicalId($this->getId(),'mode_fonctionnement');
		if (is_object($cmd_mode)){
			$mode = $cmd_mode->execCmd();
			if ($mode == "auto"){
				$prochaine_action=$this::Recup_prochaine_action();
				if ($prochaine_action['datetime'] != null) {					
					log::add('planification', 'debug', "Mode: " . $mode . " Replanification le " . $prochaine_action['datetime'] ." => ". $prochaine_action['nom']);
					$prochaine_action['datetime'] = strtotime($prochaine_action['datetime']);
					$cron->setOption(array('eqLogic_Id' => intval($this->getId())));
					$cron->setLastRun(date('Y-m-d H:i:s'));
					$cron->setSchedule(date('i', $prochaine_action['datetime']) . ' ' . date('H', $prochaine_action['datetime']) . ' ' . date('d', $prochaine_action['datetime']) . ' ' . date('m', $prochaine_action['datetime']) . ' * ' . date('Y', $prochaine_action['datetime']));
					$cron->save();
				} else {
					if (is_object($cron)) {
						$cron->remove();
					}
				}
			}else{
				$cmd_date_prochaine_action=cmd::byEqLogicIdAndLogicalId($this->getId(),'heure_fin');
				if (is_object($cmd_date_prochaine_action)) {
					$date=$cmd_date_prochaine_action->execCmd();
					log::add('planification', 'debug', "date: " . $date );
					
					$datetime=date_create_from_format("d/m/y H:i",$date);
					log::add('planification', 'debug', "datetime: " . $datetime );
					$cron->setOption(array('eqLogic_Id' => intval($this->getId())));
					$cron->setLastRun(date('Y-m-d H:i:s'));
					log::add('planification', 'debug', "Mode: " . $mode ." Replanification le " . $date ." => Auto");
					$cron->setSchedule( $datetime->format("i") . ' ' .  $datetime->format("H") . ' ' .  $datetime->format("d") . ' ' . $datetime->format("m") . ' * ' .  $datetime->format("Y"));
					$cron->save();					
				}
			}
		}
	}
	public function Recup_prochaine_action(){
		$maintenant=date_create_from_format ('Y-m-d H:i' ,date('Y-m-d H:i'));
		$numéro_jour=date('N');
		$Id_planification_en_cours=$this->getConfiguration("Id_planification_en_cours","");
		if($Id_planification_en_cours==""){return;}
		$CMD_LIST=$this::Recup_liste_commandes_planification($this->getId());
		$planifications=$this::recup_planifications();
		$cette_planification=[];
		foreach($planifications as $planification){
			if($planification["Id"]==$Id_planification_en_cours){
				log::add("planification","debug","planification en cours: " . $planification["nom_planification"]);
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
							if($periode["Id"]==$cmd["Id"]){
								$action["datetime"]=$date->format('d-m-Y H:i');
								$action["nom"]=$cmd["nom"];
														
								$cmd_action_suivante = $this->getCmd(null, "action_suivante");
								if(is_object($cmd_action_suivante)){
									$cmd_action_suivante->event($cmd["nom"]);
								}
								$cmd_heure_fin = $this->getCmd(null, "heure_fin");
								if(is_object($cmd_heure_fin)){
									$cmd_heure_fin->event($date->format('d-m-Y H:i'));
								}
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
	public function Recup_action_actuelle(){
		$cmd=cmd::byEqLogicIdAndLogicalId($this->getId(),'heure_fin');
		$timestamp_prochaine_action=time();
		if (is_object($cmd)){
			$val=$cmd->execCmd();
			if (is_numeric($val)){
				$timestamp_prochaine_action=$val;
			}
		}
		$numéro_jour=date('N');
		$Id_planification_en_cours=$this->getConfiguration("Id_planification_en_cours","");
		if($Id_planification_en_cours==""){return;}
		$CMD_LIST=$this::Recup_liste_commandes_planification($this->getId());
		$planifications=$this::recup_planifications();
		$cette_planification=[];
		foreach($planifications as $planification){
			if($planification["Id"]==$Id_planification_en_cours){
				$cette_planification=$planification["semaine"];
				break;
			}
		}
		if(count($cette_planification) == 0){return;}
		log::add("planification","debug","numéro_jour:".$numéro_jour);
		$numBoucle=0;				
		for ($i = $numéro_jour; $i > $numéro_jour-7; $i--) {
			$num=$i;
			if($i<1){$num = 7-$i;}
			log::add("planification","debug","num :" . $num);
				
			if (isset($cette_planification[ $num-1]["periodes"])){
				log::add("planification","debug",$cette_planification[ $num-1]["jour"]);
					
				$periodes=$cette_planification[$num-1]["periodes"];
				$action=[];
				$trouve=false;
				foreach($periodes as $periode){
					//$date=date_add(date_create_from_format ( 'Y-m-d H:i' ,date('Y-m-d '). $periode["Debut_periode"]), date_interval_create_from_date_string($i-9 .' days'));
					$date=date_add(date_create_from_format ( 'Y-m-d H:i' ,date('Y-m-d '). $periode["Debut_periode"]), date_interval_create_from_date_string(-$numBoucle.' days'));
					log::add("planification","debug",implode("|",$periode));
					log::add("planification","debug","date:".$date->format(' d-m-Y H:i'));
					if($date->getTimestamp() <= $timestamp_prochaine_action){
						$trouve=true;
						foreach ($CMD_LIST as $cmd) {
							if($periode["Id"]==$cmd["Id"]){
								log::add("planification","debug","id:".$cmd["Id"]);
								log::add("planification","debug","nom:".$cmd["nom"]);
								
								$action["datetime="]=$date->format(' d-m-Y H:i');
								$action["nom"]=$cmd["nom"];
								$action["cmd"]=$cmd["cmd"];
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
				$cmd_action_en_cours_fin = $this->getCmd(null, "action_en_cours");
				if(is_object($cmd_action_en_cours_fin)){
					$cmd_action_en_cours_fin->event($action["nom"]);
				}
				return $action;
			}			
			$numBoucle+=1;
		}
		return $action;
	
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
				
		
				foreach ($cmds as $cmd) {
					$div.='<div class="couleur-'.$cmd["couleur"].'" id="'.$cmd["Id"].'" value="'. $cmd["nom"] . '">';
						$div.='<span>'.$cmd["nom"] .'</span>';
					$div.='</div>';
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
	
	public function importer_commandes_eqlogic($_eqLogic_id) {
	
		$eqLogic = eqLogic::byId($_eqLogic_id);
		if (!is_object($eqLogic)) {
			throw new Exception(__('Impossible de trouver l\'équipement : ', __FILE__) . $_eqLogic_id);
		}
		if ($eqLogic->getEqType_name() == 'planification') {
			throw new Exception(__('Vous ne pouvez importer les commandes d\'un équipement planification', __FILE__));
		}
		$cmds_prog=[];
		$arr=$this->getConfiguration('commandes_planification', "");
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
		
		$this->setConfiguration('commandes_planification', $cmds_prog);
		$this->save();
	} 
    function Ajout_Commande($logical_id,$name,$type,$sous_type,$min=null,$max=null,$valeur_par_defaut=null,$unite=null){
		
			$cmd = $this->getCmd(null, $logical_id);
			if (!is_object($cmd)) {
				$cmd = new planificationCmd();
				$cmd->setLogicalId($logical_id);
				$cmd->setIsVisible(1);
				$cmd->setName($name);
				$cmd->setType($type);
				$cmd->setSubType($sous_type);
				$cmd->setConfiguration('minValue',$min);
				$cmd->setConfiguration('maxValue',$max);
				$cmd->setEqLogic_id($this->getId());
				$cmd->save();
				if($valeur_par_defaut!=null){
					$this->checkAndUpdateCmd($logical_id,$valeur_par_defaut);
				}
			
			
			}
	}
	public function preSave() {
     if ($this->getConfiguration('type','') == ''){
		   	$this->setConfiguration('type', 'Autre');
			$this->setIsVisible(1);
           	$this->setIsEnable(1);
        }
    }
	
	
    public function postSave() {
		$this->Ajout_Commande('refresh','Rafraichir','action','other');
		$this->Ajout_Commande('mode_fonctionnement','Mode fonctionnement','info','string',null,null,"auto");
		$this->Ajout_Commande('auto','Auto','action','other');
		$this->Ajout_Commande('set_heure_fin','Set heure fin','action','message');
		$this->Ajout_Commande('heure_fin','Heure fin mode en cours','info','string');
		$this->Ajout_Commande('action_en_cours','Action en cours','info','string');
		$this->Ajout_Commande('action_suivante','Action suivante','info','string');
		$this->Ajout_Commande('planification_en_cours','Planification en cours','info','string');	
		$this->Ajout_Commande('temperature_consigne_par_defaut','Température consigne par defaut','info','numeric',null,null,20);	
		$this->Ajout_Commande('duree_mode_manuel_par_defaut','Duree mode manuel par defaut (minutes)','info','numeric',null,null,60);	


		$cmd = $this->getCmd(null, "set_planification");
		if (!is_object($cmd)) {
			$cmd = new planificationCmd();
			$cmd->setLogicalId("set_planification");
			$cmd->setIsVisible(1);
			$cmd->setName("Set planification");
			$cmd->setType("action");
			$cmd->setSubType("select");
			$cmd->setEqLogic_id($this->getId());
			$cmd->setConfiguration("infoName", $this->getCmd(null, "planification_en_cours")->getName());
			$cmd->setConfiguration("infoId", $this->getCmd(null, "planification_en_cours")->getId());
			$cmd->setValue( $this->getCmd(null, "planification_en_cours")->getId());
		}
		
		
		$liste="";
		$cmd->setConfiguration("listValue",$liste);
		$cmd->save();
		if ($this->getConfiguration('type','') == 'Poele'){
			$this->Ajout_Commande('absent','Absent','action','message');
			$this->Ajout_Commande('force','Forcé','action','other');
			$this->Ajout_Commande('arret','Arrêt','action','other');
			$this->Ajout_Commande('set_consigne_temperature','Set consigne température','action','slider',7,30);
			$this->Ajout_Commande('consigne_temperature','Consigne Temperature','info','numeric',null,null,20,'°C');	
		}
			
		if ($this->getConfiguration('type','') == 'pac'){
			$this->Ajout_Commande('climatisation','Climatisation','action','other');
			$this->Ajout_Commande('ventilation','Ventilation','action','other');
			$this->Ajout_Commande('chauffage','Chauffage','action','other');
			$this->Ajout_Commande('arret','Arrêt','action','other');
			$this->Ajout_Commande('set_consigne_temperature','Set consigne température','action','slider',7,30);
			$this->Ajout_Commande('consigne_temperature','Consigne Temperature','info','numeric',null,null,20,'°C');	
			}
	
		if ($this->getConfiguration('type','') == 'chauffage'){
			$this->Ajout_Commande('confort','Confort','action','other');
			$this->Ajout_Commande('eco','Eco','action','other');
			$this->Ajout_Commande('hors_gel','Hors gel','action','other');
			$this->Ajout_Commande('absent','Absent','action','other');
			$this->Ajout_Commande('arret','Arrêt','action','other');			
		}
		
		if ($this->getConfiguration('type','') == 'volet'){
			$this->Ajout_Commande('ouvrir','Ouvrir','action','other');
			$this->Ajout_Commande('fermer','Fermer','action','other');
			$this->Ajout_Commande('stop','My/stop','action','other');
		}

        $cmd_set_planification = $this->getCmd(null, "set_planification");
		if (!is_object($cmd_set_planification)){return;}
		$planifications=$this::recup_planifications();
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
		$Id_planification_en_cours=$this->getConfiguration("Id_planification_en_cours","");
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
		/*$cmd_refresh = $this->getCmd(null, "refresh");
		if(is_object($cmd_refresh)){
			$cmd_refresh->execute();
		}*/
		//$this::Recup_action_actuelle();
    }
	function replace_into_html(&$erreur,&$liste_erreur,&$replace,$parametre,$commande,$type){

		if (is_object($commande)){
			switch ($type) {
				case ("value"):
                	$valeur = $commande->execCmd();
                	
                	if(strlen($valeur) != 0){
                      $replace[$parametre] = $valeur;
                    }else{
                       $replace[$parametre] = "non renseigné";
                    }
					
					break;
				case("name"):
					$replace[$parametre] = cmd::byEqLogicIdAndLogicalId($this->getId(),$commande->execCmd())->getName();
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
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) {
			return $replace;
		}
		$version = jeedom::versionAlias($_version);
		if ($this->getDisplay('hideOn' . $version) == 1) {
			return '';
		}
		$erreur=false;
		$liste_erreur=[];

		
		$commande = $this->getCmd(null, 'planification_en_cours');
		//$planification_en_cours=$commande->execCmd();
		if (is_object($commande)){
			$liste_planifications = $this::recup_planifications();
			$Id_planification_en_cours=$this->getConfiguration("Id_planification_en_cours","");

			
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
	
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#mode_fonctionnement#',$this->getCmd(null, 'mode_fonctionnement'),"value");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#mode_fonctionnement_name#',$this->getCmd(null, 'mode_fonctionnement'),'name');
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#consigne_temperature#',$this->getCmd(null, 'consigne_temperature'),"value");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#consigne_temperature_id#',$this->getCmd(null, 'consigne_temperature'),"id");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#refresh_id#',$this->getCmd(null, 'refresh'),"id");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#auto_id#',$this->getCmd(null, 'auto'),"id");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#absent_id#',$this->getCmd(null, 'absent'),"id");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#chauffage_id#',$this->getCmd(null, 'force'),"id");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#arret_id#',$this->getCmd(null, 'arret'),"id");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_id#',$this->getCmd(null, 'set_consigne_temperature'),"id");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_min#',$this->getCmd(null, 'set_consigne_temperature'),"min");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_max#',$this->getCmd(null, 'set_consigne_temperature'),"max");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#endtime_change_id#',$this->getCmd(null, 'set_heure_fin'),"id");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#endtime#',$this->getCmd(null, 'heure_fin'),"value");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#set_planification_id#',$this->getCmd(null, 'set_planification'),"id");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#planification_en_cours#',$this->getCmd(null, 'planification_en_cours'),"value");
        $this::replace_into_html($erreur,$liste_erreur,$replace,'#action_en_cours#',$this->getCmd(null, 'action_en_cours'),"value");
		$this::replace_into_html($erreur,$liste_erreur,$replace,'#prochaine_action#',$this->getCmd(null, 'action_suivante'),"value");
		
		
		$cmd_temperature=cmd::byId(str_replace ("#" ,"" , $this->getConfiguration('temperature_id',"")));
		if (is_object($cmd_temperature)){
			$replace['#temperature#'] = $cmd_temperature->execCmd() . " °C";
			$replace['#temperature_id#'] = $cmd_temperature->getId();
		}else{
			$replace['#temperature#'] = "";
			$replace['#temperature_id#']="";
		}

		$imagePoele="PoeleOff.png";
		$cmd_Etat_Allume=cmd::byId(str_replace ("#" ,"" , $this->getConfiguration('etat_allume_id',"")));
		if (is_object($cmd_Etat_Allume)){
			if($cmd_Etat_Allume->execCmd())
			{
				$imagePoele="PoeleOn.png";
				$cmd_Etat_Boost=cmd::byId(str_replace ("#" ,"" , $this->getConfiguration('etat_boost_id',"")));
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
		cache::set('widgetHtml' . $version . $this->getId(), $html, 0);
		return $html;
	}
   
}

class planificationCmd extends cmd {
    //public static $_widgetPossibility = array('custom' => false);

    public function dontRemoveCmd() {
        return true;
    }

    public function execute($_options = array()) {
		//log::add('planification', 'info', "execute: " . $this->getLogicalId());
		$eqLogic = $this->getEqLogic();
		
		
		switch ($this->getLogicalId()) {
			case 'refresh':
				$eqLogic->refresh();
				$eqLogic->Recup_action_actuelle();
				$eqLogic->set_cron();
				break;
			case 'set_consigne_temperature':
				$eqLogic->checkAndUpdateCmd('consigne_temperature',$_options["slider"]);
				$cmd_mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement');
				if (is_object($cmd_mode)){
					$mode = $cmd_mode->event("force");
				}
				break;
			case 'auto':
			case 'force':
			case 'arret':
			case 'hors_gel':
			case 'chauffage':
			case 'absent':
			case 'ventilation':
				$eqLogic->checkAndUpdateCmd('mode_fonctionnement', $this->getLogicalId());
				if($this->getLogicalId() == "auto"){
					$eqLogic->set_cron();
				}else if ($this->getLogicalId() == "arret"){
					$crons = cron::searchClassAndFunction('planification', 'pull');
					foreach ($crons as $cron){
							$options_cron=$cron->getOption();
							if($options_cron["eqLogic_Id"]== $eqLogic->getId()){
								$cron->remove();
							}
					}					
				}else{
					$cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
					$cmd_duree_mode_manuel_par_defaut=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'duree_mode_manuel_par_defaut');
					$duree_mode_manuel_par_defaut=60*60*1000;
					if (is_object($cmd_duree_mode_manuel_par_defaut)){
						$duree_mode_manuel_par_defaut=$cmd_duree_mode_manuel_par_defaut->execCmd()*60*1000;
					}
								
					
					if (is_object($cmd_set_heure_fin)){
						$date_Fin=date('d/m/y H:i',time() + $duree_mode_manuel_par_defaut);
						$arr=["message" => $date_Fin];
						$cmd_set_heure_fin->execute( $arr) ;
					}
				}
				break;
			case 'set_heure_fin':	
				if (strtotime("now") > strtotime($_options['message'])){
					throw new Exception("Veuillez selectionner une date et heure supérieure à maintenant");
				}
				$eqLogic->checkAndUpdateCmd('heure_fin', date('d/m/y H:i',strtotime($_options['message'])));
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
					$planifications=$eqLogic->recup_planifications();
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
		}
		
		$eqLogic->refreshWidget() ;
    }
	public function preSave() {
     
	}
}
?>