<?php
require_once  '/var/www/html/core/php/core.inc.php';
//toto
class planification extends eqLogic {
	
	public static $_widgetPossibility = array('custom' => true);
	
	public function Recup_infos_lever_coucher_soleil($eqLogic_id){
		$EqLogic = eqLogic::byId($eqLogic_id);
		$longitude = config::byKey("info::longitude","core");
		$latitude = config::byKey("info::latitude","core");
		$lever = "";
		$coucher = "";
		$retour = array();
	
   		if (is_numeric($latitude) && is_numeric($longitude)){
			$longitude = floatval($longitude);
			$latitude = floatval($latitude);
			$Lever_Soleil = date_sunrise(time(), SUNFUNCS_RET_STRING, $latitude, $longitude, 90.58, (date("O"))/100);
			$Coucher_Soleil = date_sunset(time(), SUNFUNCS_RET_STRING, $latitude, $longitude, 90.58, (date("O"))/100);
			$Lever_Soleil_int=intval(preg_replace( '/:.*/', '', date_sunrise(time(), SUNFUNCS_RET_STRING, $latitude, $longitude, 90.58, (date("O"))/100)).preg_replace( '/.*:/', '', date_sunrise(time(), SUNFUNCS_RET_STRING, $latitude, $longitude, 90.58, (date("O"))/100)));
			$Coucher_Soleil_int=intval(preg_replace( '/:.*/', '', date_sunset(time(), SUNFUNCS_RET_STRING, $latitude, $longitude, 90.58, (date("O"))/100)).preg_replace( '/.*:/', '', date_sunset(time(), SUNFUNCS_RET_STRING, $latitude, $longitude, 90.58, (date("O"))/100)));
			$Heure_prochaine_action_lever_lundi = $Lever_Soleil;
			$Heure_prochaine_action_coucher_lundi = $Coucher_Soleil;
			$Heure_prochaine_action_lever_mardi = $Lever_Soleil;
			$Heure_prochaine_action_coucher_mardi = $Coucher_Soleil;
			$Heure_prochaine_action_lever_mercredi = $Lever_Soleil;
			$Heure_prochaine_action_coucher_mercredi = $Coucher_Soleil;
			$Heure_prochaine_action_lever_jeudi = $Lever_Soleil;
			$Heure_prochaine_action_coucher_jeudi = $Coucher_Soleil;
			$Heure_prochaine_action_lever_vendredi = $Lever_Soleil;
			$Heure_prochaine_action_coucher_vendredi = $Coucher_Soleil;
			$Heure_prochaine_action_lever_samedi = $Lever_Soleil;
			$Heure_prochaine_action_coucher_samedi = $Coucher_Soleil;
			$Heure_prochaine_action_lever_dimanche = $Lever_Soleil;
			$Heure_prochaine_action_coucher_dimanche = $Coucher_Soleil;
			//Lundi	
				$LeverMin_lundi = $EqLogic->getConfiguration('LeverMin_Lundi');
				$LeverMin_lundi_int = str_replace ( ":" ,"" ,$LeverMin_lundi);
				$LeverMax_lundi = $EqLogic->getConfiguration('LeverMax_Lundi');
				$LeverMax_lundi_int = str_replace ( ":" ,"" ,$LeverMax_lundi);
				$CoucherMin_lundi = $EqLogic->getConfiguration('CoucherMin_Lundi');
				$CoucherMin_lundi_int = str_replace ( ":" ,"" ,$CoucherMin_lundi);
				$CoucherMax_lundi = $EqLogic->getConfiguration('CoucherMax_Lundi');
				$CoucherMax_lundi_int = str_replace ( ":" ,"" ,$CoucherMax_lundi);
			
				if (!is_nan(intval($LeverMin_lundi_int)) and $LeverMin_lundi_int !="" and $Lever_Soleil_int < intval($LeverMin_lundi_int)){
					$Heure_prochaine_action_lever_lundi = $LeverMin_lundi;
				}
				if (!is_nan(intval($LeverMax_lundi_int)) and $LeverMax_lundi_int !="" and $Lever_Soleil_int > intval($LeverMax_lundi_int)){
					$Heure_prochaine_action_lever_lundi = $LeverMax_lundi;
				}
				if (!is_nan(intval($CoucherMin_lundi_int)) and $CoucherMin_lundi_int !="" and $Coucher_Soleil_int < intval($CoucherMin_lundi_int)){
					$Heure_prochaine_action_coucher_lundi = $CoucherMin_lundi;
				}
				if (!is_nan(intval($CoucherMax_lundi_int)) and $CoucherMax_lundi_int !="" and $Coucher_Soleil_int > intval($CoucherMax_lundi_int)){
					$Heure_prochaine_action_coucher_lundi = $CoucherMax_lundi;
				}
			//Mardi	
				$LeverMin_mardi = $EqLogic->getConfiguration('LeverMin_Mardi');
				$LeverMin_mardi_int = str_replace ( ":" ,"" ,$LeverMin_mardi);
				$LeverMax_mardi = $EqLogic->getConfiguration('LeverMax_Mardi');
				$LeverMax_mardi_int = str_replace ( ":" ,"" ,$LeverMax_mardi);
				$CoucherMin_mardi = $EqLogic->getConfiguration('CoucherMin_Mardi');
				$CoucherMin_mardi_int = str_replace ( ":" ,"" ,$CoucherMin_mardi);
				$CoucherMax_mardi = $EqLogic->getConfiguration('CoucherMax_Mardi');
				$CoucherMax_mardi_int = str_replace ( ":" ,"" ,$CoucherMax_mardi);
			
				if (!is_nan(intval($LeverMin_mardi_int)) and $LeverMin_mardi_int !="" and $Lever_Soleil_int < intval($LeverMin_mardi_int)){
					$Heure_prochaine_action_lever_mardi = $LeverMin_mardi;
				}
				if (!is_nan(intval($LeverMax_mardi_int)) and $LeverMax_mardi_int !="" and $Lever_Soleil_int > intval($LeverMax_mardi_int)){
					$Heure_prochaine_action_lever_mardi = $LeverMax_mardi;
				}
				if (!is_nan(intval($CoucherMin_mardi_int)) and $CoucherMin_mardi_int !="" and $Coucher_Soleil_int < intval($CoucherMin_mardi_int)){
					$Heure_prochaine_action_coucher_mardi = $CoucherMin_mardi;
				}
				if (!is_nan(intval($CoucherMax_mardi_int)) and $CoucherMax_mardi_int !="" and $Coucher_Soleil_int > intval($CoucherMax_mardi_int)){
					$Heure_prochaine_action_coucher_mardi = $CoucherMax_mardi;
				}
			//Mercredi	
				$LeverMin_mercredi = $EqLogic->getConfiguration('LeverMin_Mercredi');
				$LeverMin_mercredi_int = str_replace ( ":" ,"" ,$LeverMin_mercredi);
				$LeverMax_mercredi = $EqLogic->getConfiguration('LeverMax_Mercredi');
				$LeverMax_mercredi_int = str_replace ( ":" ,"" ,$LeverMax_mercredi);
				$CoucherMin_mercredi = $EqLogic->getConfiguration('CoucherMin_Mercredi');
				$CoucherMin_mercredi_int = str_replace ( ":" ,"" ,$CoucherMin_mercredi);
				$CoucherMax_mercredi = $EqLogic->getConfiguration('CoucherMax_Mercredi');
				$CoucherMax_mercredi_int = str_replace ( ":" ,"" ,$CoucherMax_mercredi);
			
				if (!is_nan(intval($LeverMin_mercredi_int)) and $LeverMin_mercredi_int !="" and $Lever_Soleil_int < intval($LeverMin_mercredi_int)){
					$Heure_prochaine_action_lever_mercredi = $LeverMin_mercredi;
				}
				if (!is_nan(intval($LeverMax_mercredi_int)) and $LeverMax_mercredi_int !="" and $Lever_Soleil_int > intval($LeverMax_mercredi_int)){
					$Heure_prochaine_action_lever_mercredi = $LeverMax_mercredi;
				}
				if (!is_nan(intval($CoucherMin_mercredi_int)) and $CoucherMin_mercredi_int !="" and $Coucher_Soleil_int < intval($CoucherMin_mercredi_int)){
					$Heure_prochaine_action_coucher_mercredi = $CoucherMin_mercredi;
				}
				if (!is_nan(intval($CoucherMax_mercredi_int)) and $CoucherMax_mercredi_int !="" and $Coucher_Soleil_int > intval($CoucherMax_mercredi_int)){
					$Heure_prochaine_action_coucher_mercredi = $CoucherMax_mercredi;
				}
			//Jeudi	
				$LeverMin_jeudi = $EqLogic->getConfiguration('LeverMin_Jeudi');
				$LeverMin_jeudi_int = str_replace ( ":" ,"" ,$LeverMin_jeudi);
				$LeverMax_jeudi = $EqLogic->getConfiguration('LeverMax_Jeudi');
				$LeverMax_jeudi_int = str_replace ( ":" ,"" ,$LeverMax_jeudi);
				$CoucherMin_jeudi = $EqLogic->getConfiguration('CoucherMin_Jeudi');
				$CoucherMin_jeudi_int = str_replace ( ":" ,"" ,$CoucherMin_jeudi);
				$CoucherMax_jeudi = $EqLogic->getConfiguration('CoucherMax_Jeudi');
				$CoucherMax_jeudi_int = str_replace ( ":" ,"" ,$CoucherMax_jeudi);
			
				if (!is_nan(intval($LeverMin_jeudi_int)) and $LeverMin_jeudi_int !="" and $Lever_Soleil_int < intval($LeverMin_jeudi_int)){
					$Heure_prochaine_action_lever_jeudi = $LeverMin_jeudi;
				}
				if (!is_nan(intval($LeverMax_jeudi_int)) and $LeverMax_jeudi_int !="" and $Lever_Soleil_int > intval($LeverMax_jeudi_int)){
					$Heure_prochaine_action_lever_jeudi = $LeverMax_jeudi;
				}
				if (!is_nan(intval($CoucherMin_jeudi_int)) and $CoucherMin_jeudi_int !="" and $Coucher_Soleil_int < intval($CoucherMin_jeudi_int)){
					$Heure_prochaine_action_coucher_jeudi = $CoucherMin_jeudi;
				}
				if (!is_nan(intval($CoucherMax_jeudi_int)) and $CoucherMax_jeudi_int !="" and $Coucher_Soleil_int > intval($CoucherMax_jeudi_int)){
					$Heure_prochaine_action_coucher_jeudi = $CoucherMax_jeudi;
				}
			//Vendredi
				$LeverMin_vendredi = $EqLogic->getConfiguration('LeverMin_Vendredi');
				$LeverMin_vendredi_int = str_replace ( ":" ,"" ,$LeverMin_vendredi);
				$LeverMax_vendredi = $EqLogic->getConfiguration('LeverMax_Vendredi');
				$LeverMax_vendredi_int = str_replace ( ":" ,"" ,$LeverMax_vendredi);
				$CoucherMin_vendredi = $EqLogic->getConfiguration('CoucherMin_Vendredi');
				$CoucherMin_vendredi_int = str_replace ( ":" ,"" ,$CoucherMin_vendredi);
				$CoucherMax_vendredi = $EqLogic->getConfiguration('CoucherMax_Vendredi');
				$CoucherMax_vendredi_int = str_replace ( ":" ,"" ,$CoucherMax_vendredi);
			
				if (!is_nan(intval($LeverMin_vendredi_int)) and $LeverMin_vendredi_int !="" and $Lever_Soleil_int < intval($LeverMin_vendredi_int)){
					$Heure_prochaine_action_lever_vendredi = $LeverMin_vendredi;
				}
				if (!is_nan(intval($LeverMax_vendredi_int)) and $LeverMax_vendredi_int !="" and $Lever_Soleil_int > intval($LeverMax_vendredi_int)){
					$Heure_prochaine_action_lever_vendredi = $LeverMax_vendredi;
				}
				if (!is_nan(intval($CoucherMin_vendredi_int)) and $CoucherMin_vendredi_int !="" and $Coucher_Soleil_int < intval($CoucherMin_vendredi_int)){
					$Heure_prochaine_action_coucher_vendredi = $CoucherMin_vendredi;
				}
				if (!is_nan(intval($CoucherMax_vendredi_int)) and $CoucherMax_vendredi_int !="" and $Coucher_Soleil_int > intval($CoucherMax_vendredi_int)){
					$Heure_prochaine_action_coucher_vendredi = $CoucherMax_vendredi;
				}
			//Samedi	
				$LeverMin_samedi = $EqLogic->getConfiguration('LeverMin_Samedi');
				$LeverMin_samedi_int = str_replace ( ":" ,"" ,$LeverMin_samedi);
				$LeverMax_samedi = $EqLogic->getConfiguration('LeverMax_Samedi');
				$LeverMax_samedi_int = str_replace ( ":" ,"" ,$LeverMax_samedi);
				$CoucherMin_samedi = $EqLogic->getConfiguration('CoucherMin_Samedi');
				$CoucherMin_samedi_int = str_replace ( ":" ,"" ,$CoucherMin_samedi);
				$CoucherMax_samedi = $EqLogic->getConfiguration('CoucherMax_Samedi');
				$CoucherMax_samedi_int = str_replace ( ":" ,"" ,$CoucherMax_samedi);
			
				if (!is_nan(intval($LeverMin_samedi_int)) and $LeverMin_samedi_int !="" and $Lever_Soleil_int < intval($LeverMin_samedi_int)){
					$Heure_prochaine_action_lever_samedi = $LeverMin_samedi;
				}
				if (!is_nan(intval($LeverMax_samedi_int)) and $LeverMax_samedi_int !="" and $Lever_Soleil_int > intval($LeverMax_samedi_int)){
					$Heure_prochaine_action_lever_samedi = $LeverMax_samedi;
				}
				if (!is_nan(intval($CoucherMin_samedi_int)) and $CoucherMin_samedi_int !="" and $Coucher_Soleil_int < intval($CoucherMin_samedi_int)){
					$Heure_prochaine_action_coucher_samedi = $CoucherMin_samedi;
				}
				if (!is_nan(intval($CoucherMax_samedi_int)) and $CoucherMax_samedi_int !="" and $Coucher_Soleil_int > intval($CoucherMax_samedi_int)){
					$Heure_prochaine_action_coucher_samedi = $CoucherMax_samedi;
				}
			//Dimanche	
				$LeverMin_dimanche = $EqLogic->getConfiguration('LeverMin_Dimanche');
				$LeverMin_dimanche_int = str_replace ( ":" ,"" ,$LeverMin_dimanche);
				$LeverMax_dimanche = $EqLogic->getConfiguration('LeverMax_Dimanche');
				$LeverMax_dimanche_int = str_replace ( ":" ,"" ,$LeverMax_dimanche);
				$CoucherMin_dimanche = $EqLogic->getConfiguration('CoucherMin_Dimanche');
				$CoucherMin_dimanche_int = str_replace ( ":" ,"" ,$CoucherMin_dimanche);
				$CoucherMax_dimanche = $EqLogic->getConfiguration('CoucherMax_Dimanche');
				$CoucherMax_dimanche_int = str_replace ( ":" ,"" ,$CoucherMax_dimanche);
			
				if (!is_nan(intval($LeverMin_dimanche_int)) and $LeverMin_dimanche_int !="" and $Lever_Soleil_int < intval($LeverMin_dimanche_int)){
					$Heure_prochaine_action_lever_dimanche = $LeverMin_dimanche;
				}
				if (!is_nan(intval($LeverMax_dimanche_int)) and $LeverMax_dimanche_int !="" and $Lever_Soleil_int > intval($LeverMax_dimanche_int)){
					$Heure_prochaine_action_lever_dimanche = $LeverMax_dimanche;
				}
				if (!is_nan(intval($CoucherMin_dimanche_int)) and $CoucherMin_dimanche_int !="" and $Coucher_Soleil_int < intval($CoucherMin_dimanche_int)){
					$Heure_prochaine_action_coucher_dimanche = $CoucherMin_dimanche;
				}
				if (!is_nan(intval($CoucherMax_dimanche_int)) and $CoucherMax_dimanche_int !="" and $Coucher_Soleil_int > intval($CoucherMax_dimanche_int)){
					$Heure_prochaine_action_coucher_dimanche = $CoucherMax_dimanche;
				}
		
			$retour["Lever_soleil"] = $Lever_Soleil;
			$retour["Coucher_soleil"] = $Coucher_Soleil;
			$retour["Heure_prochaine_action_lever_lundi"] = $Heure_prochaine_action_lever_lundi;
			$retour["Heure_prochaine_action_coucher_lundi"] = $Heure_prochaine_action_coucher_lundi;
			$retour["Heure_prochaine_action_lever_mardi"] = $Heure_prochaine_action_lever_mardi;
			$retour["Heure_prochaine_action_coucher_mardi"] = $Heure_prochaine_action_coucher_mardi;
			$retour["Heure_prochaine_action_lever_mercredi"] = $Heure_prochaine_action_lever_mercredi;
			$retour["Heure_prochaine_action_coucher_mercredi"] = $Heure_prochaine_action_coucher_mercredi;
			$retour["Heure_prochaine_action_lever_jeudi"] = $Heure_prochaine_action_lever_jeudi;
			$retour["Heure_prochaine_action_coucher_jeudi"] = $Heure_prochaine_action_coucher_jeudi;
			$retour["Heure_prochaine_action_lever_vendredi"] = $Heure_prochaine_action_lever_vendredi;
			$retour["Heure_prochaine_action_coucher_vendredi"] = $Heure_prochaine_action_coucher_vendredi;
			$retour["Heure_prochaine_action_lever_samedi"] = $Heure_prochaine_action_lever_samedi;
			$retour["Heure_prochaine_action_coucher_samedi"] = $Heure_prochaine_action_coucher_samedi;
			$retour["Heure_prochaine_action_lever_dimanche"] = $Heure_prochaine_action_lever_dimanche;
			$retour["Heure_prochaine_action_coucher_dimanche"] = $Heure_prochaine_action_coucher_dimanche;
		}
		return $retour;
	}
	
	function Recup_liste_commandes_planification($eqLogic_id) {
		$eqLogic = eqLogic::byId($eqLogic_id);
		if (!is_object($eqLogic)) {
			throw new Exception(__('Impossible de trouver l\'équipement : ', __FILE__) . $eqLogic_id);
		  }
	  
		return $eqLogic->getConfiguration("commandes_planification","");
		  
	  }
	function Recup_planifications(){
		$eqLogic=$this;
		$nom_fichier=dirname(__FILE__) ."/../../planifications/" . $eqLogic->getId() . ".json";
		$planifications="";
		if(file_exists ( $nom_fichier ) ){$planifications=file_get_contents ($nom_fichier);}
		if($planifications==""){return [] ;}
		return json_decode($planifications,true);
	}
	function supp_accents( $str, $charset='utf-8' ) {
		$str = htmlentities( $str, ENT_NOQUOTES, $charset );
		$str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
		$str = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
		$str = preg_replace( '#&[^;]+;#', '', $str );
		return $str;
	}
	function add_log($_eqLogic,$level = 'debug',$Log){
        if (is_array($Log)) $Log = json_encode($Log);
		$function_name = debug_backtrace(false, 2)[1]['function'];
		$ligne = debug_backtrace(false, 2)[0]['line'];
		$msg = '<'. $function_name .' (' . $ligne . ')> '.$Log;
		$nom_eq= planification::supp_accents(str_replace(" " , "_",str_replace("[" , "_",str_replace("]" , "",$_eqLogic->getHumanName(false)))));
		log::add('planification'.$nom_eq  , $level,$msg);

					
	}
	function pull($_option){
		
		$eqLogic = self::byId($_option['eqLogic_Id']);
		planification::add_log($eqLogic,"debug","pull");
		planification::Verification_log($eqLogic);
		$crons = cron::searchClassAndFunction('planification', 'pull');
		$cron_id="";
		$next_run=$_option['Prochaine_execution'];
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
			$maintenant=time();
		
			if (date_create_from_format("Y-m-d H:i:s",$next_run)->getTimestamp() - $maintenant>59){
				planification::add_log($eqLogic,"debug","arrêt du pull date execution suppérieure à maintenant : " .date_create_from_format("Y-m-d H:i:s",$cron->getNextRunDate())->getTimestamp() . '>' . $maintenant);
				return;
			}	
		}
		
		
		
		//$commande_en_cours="";
		$eqLogic->Execute_action_actuelle();
		$cmd_mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement');
		$type_eqLogic=$eqLogic->getConfiguration("type","");
		if ($type_eqLogic=="PAC" && $cmd_mode->execCmd()=="boost_on"){
			$cmd_boost_off=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'boost_off');
			$cmd_boost_off->execute();
		}
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
					$cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
					if(is_object($cmd_heure_fin)){
						$cmd_heure_fin->event(date('d-m-Y H:i',$prochaine_action['datetime']));
					}
					$cron->setOption(array('eqLogic_Id' => intval($eqLogic->getId()),'eqLogic'=> mb_convert_encoding ($eqLogic->getHumanName(false), 'HTML-ENTITIES', 'UTF-8'),'Prochaine_execution'=> date('Y-m-d H:i:s', $prochaine_action['datetime'])));
					$cron->setLastRun(date('Y-m-d H:i:s'));
					$cron->setSchedule(date('i', $prochaine_action['datetime']) . ' ' . date('H', $prochaine_action['datetime']) . ' ' . date('d', $prochaine_action['datetime']) . ' ' . date('m', $prochaine_action['datetime']) . ' *');
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
						$cron->remove();
					}
				}else{
						
					$cmd_date_prochaine_action=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin');
					if (is_object($cmd_date_prochaine_action)) {
						$date="";
						$date=$cmd_date_prochaine_action->execCmd();
						if ($date !=""){
							$datetime=date_create_from_format("d-m-Y H:i",$date);
							if (!is_object($cron)) {
								$cron = new cron();
								$cron->setClass('planification');
								$cron->setFunction('pull');
							}
							$cron->setOption(array('eqLogic_Id' => intval($eqLogic->getId()),
							'eqLogic'=> mb_convert_encoding ($eqLogic->getHumanName(false), 'HTML-ENTITIES', 'UTF-8'),
							'Prochaine_execution'=> $datetime->format('Y-m-d H:i:s')
						));
							$cron->setLastRun(date('Y-m-d H:i:s'));
							planification::add_log($eqLogic,"debug","Mode: " . $mode ." Replanification le " . $date ." => Auto");
							$cron->setSchedule( $datetime->format("i") . ' ' .  $datetime->format("H") . ' ' .  $datetime->format("d") . ' ' . $datetime->format("m") . ' *');
							$cron->save();	
						}				
					}
				}
			}
		}
	}
	function Recup_prochaine_action(){
		$eqLogic=$this;				
		$action_en_cours="";
		$infos_lever_coucher_soleil=self::Recup_infos_lever_coucher_soleil($eqLogic->getId());	
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
				$cette_planification=$planification["semaine"];
			}
		}
		if(count($cette_planification) == 0){return;}
		for ($i = 1; $i <= 7; $i++) {
			if($numéro_jour>7){$numéro_jour -=7;}
			if (isset($cette_planification[$numéro_jour-1]["periodes"])){
				$jour = $cette_planification[$numéro_jour-1]["jour"];
				$periodes = $cette_planification[$numéro_jour-1]["periodes"];
				$nb=0;
				foreach($periodes as $periode){
					if(isset($periode["Type_periode"])){
						if($periode["Type_periode"] == "lever"){
							$periode["Debut_periode"]=$infos_lever_coucher_soleil["Heure_prochaine_action_lever_".strtolower ($jour)];
						}else if ($periode["Type_periode"] == "coucher"){
							$periode["Debut_periode"]=$infos_lever_coucher_soleil["Heure_prochaine_action_coucher_".strtolower ($jour)];
						}
					}
					$periodes[$nb]=$periode;
					$nb+=1;
				}	
				array_multisort(array_column($periodes, 'Debut_periode'), SORT_ASC, $periodes);
				foreach($periodes as $periode){
					$date=date_add(date_create_from_format ( 'Y-m-d H:i' ,date('Y-m-d '). $periode["Debut_periode"]), date_interval_create_from_date_string($i-1 .' days'));
					if($date->getTimestamp() > $maintenant->getTimestamp()){
						planification::add_log($eqLogic,"debug","periode:".implode("//",$periode));
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
	function Execute_action_actuelle(){
		$mode_fonctionnement="auto";
		$eqLogic=$this;
		$infos_lever_coucher_soleil=self::Recup_infos_lever_coucher_soleil($eqLogic->getId());			
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
				planification::add_log($eqLogic,"debug","arret fin de la fonction");
			return [];
		}
		if($mode_fonctionnement == "auto"){
			$cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin');
			$timestamp_prochaine_action=time();
			if (is_object($cmd)){
				$val=$cmd->execCmd();
				if (is_numeric($val)){
					$timestamp_prochaine_action=$val;
				}
			}
			$numéro_jour=date('N');
			if(count($cette_planification) == 0){
				planification::add_log($eqLogic,"debug","Aucune planification enregistrée dans l'eqLogic-> fin de la fonction");
				return;
			}
			$Id_planification_en_cours=$eqLogic->getConfiguration("Id_planification_en_cours","");
			if($Id_planification_en_cours==""){
				planification::add_log($eqLogic,"debug","Aucun Id de planification enregistré");
				return;
			}
			$CMD_LIST=$eqLogic::Recup_liste_commandes_planification($eqLogic->getId());
			$planifications=$eqLogic::Recup_planifications();
			$cette_planification=[];
			foreach($planifications as $planification){
				if($planification["Id"]==$Id_planification_en_cours){
					planification::add_log($eqLogic,"debug","planification en cours: " . $planification["nom_planification"]);
					$cette_planification=$planification["semaine"];
					break;
				}
			}
			
			$numBoucle=0;				
			for ($i = $numéro_jour; $i > $numéro_jour-7; $i--) {
				$num=$i;
				if($i<1){$num = 7-$i;}
				$trouve=false;
				if (isset($cette_planification[ $num-1]["periodes"])){
					$jour=$cette_planification[ $num-1]["jour"];
					$periodes=$cette_planification[$num-1]["periodes"];
					$action=[];
					$nb=0;
					foreach($periodes as $periode){
						if(isset($periode["Type_periode"])){
							if($periode["Type_periode"] == "lever"){
								$periode["Debut_periode"]=$infos_lever_coucher_soleil["Heure_prochaine_action_lever_".strtolower ($jour)];
							}else if ($periode["Type_periode"] == "coucher"){
								$periode["Debut_periode"]=$infos_lever_coucher_soleil["Heure_prochaine_action_coucher_".strtolower ($jour)];
							}
						}
						$periodes[$nb]=$periode;
						$nb+=1;
					}	
					array_multisort(array_column($periodes, 'Debut_periode'), SORT_ASC, $periodes);
					foreach($periodes as $periode){
						$date=date_add(date_create_from_format ( 'Y-m-d H:i' ,date('Y-m-d '). $periode["Debut_periode"]), date_interval_create_from_date_string(-$numBoucle.' days'));
						if($date->getTimestamp() <= $timestamp_prochaine_action){
							planification::add_log($eqLogic,"debug","periode:".implode("|",$periode));
							$trouve=true;
							foreach ($CMD_LIST as $cmd) {
								if($periode["Id"]==$cmd["Id"]){
									$action["datetime"]=$date->format(' d-m-Y H:i');
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
						planification::add_log($eqLogic,"debug",'heure execution actuelle:'.$action["datetime"]);
						if ($action_en_cours != $action['nom']){
							try {
								
								$cmd =$action['cmd'];
								$options = array();
								$options = $action['options'];
								if (is_numeric (trim($cmd, "#"))){
									
									$cmd=cmd::byId(trim($cmd, "#"));
									if(is_object($cmd)){
										$eqLogic_cmd=eqLogic::byId($cmd->getEqLogic_id()) ;
										
										if($eqLogic_cmd->getObject()==""){
											$Object="Aucun";
										}else{
											$Object=$eqLogic_cmd->getObject()->getName();
										}
									
										planification::add_log($eqLogic,"debug",'execution action: #[' . $Object."][".$eqLogic_cmd->getName()."][".$cmd->getName()."]#");
										$cmd->execCmd();
									}
								}else if ($cmd !=""){
									$options_str="";
									if ($cmd=="variable"){$options_str=$options["name"] . "=>" .$options["value"];}
									if ($cmd=="scenario"){$options_str=implode("*",$options);}
									if ($cmd=="event"){
										$cmd_event= cmd::byId(trim($options["cmd"], "#"));
										$eqLogic_cmd=eqLogic::byId($cmd_event->getEqLogic_id());
										if($eqLogic_cmd->getObject()==""){
											$Object="Aucun";
										}else{
											$Object=$eqLogic_cmd->getObject()->getName();
										}
										$options_str="Evènement: ".'#[' . $Object."][".$eqLogic_cmd->getName()."][".$cmd_event->getName()."]#" . "=>" .$options["value"];
									}
									
									planification::add_log($eqLogic,"debug",'execution action: ' . $cmd . ":" .$options_str);		
									scenarioExpression::createAndExec('action', $cmd, $options);
								}
							}catch (Exception $e) {
								planification::add_log($eqLogic,"error",'Erreur lors de l\'éxecution de ' . $cmd['cmd'] .'. Détails : '. $e->getMessage());
							}
							$cmd_action_en_cours->event($action["nom"]);
							
						}else{
							
							planification::add_log($eqLogic,"debug","action_en_cours identique à l'action a executer -> fin de la fonction");
						}
					
					}
					
					return;
				}		
				$numBoucle+=1;
			}
		}
	
	}
	function Verification_log($eqLogic){
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
	function preSave() {
		
		$eqLogic=$this;
     if ($eqLogic->getConfiguration('type','') == ''){
		   	$eqLogic->setConfiguration('type', 'Autre');
			$eqLogic->setIsVisible(1);
           	$eqLogic->setIsEnable(1);
        }
    }
		
    function postSave() {
		$eqLogic=$this;
		$eqLogic->Ajout_Commande('refresh','Rafraichir','action','other');
		$eqLogic->Ajout_Commande('auto','Auto','action','other');
		$eqLogic->Ajout_Commande('set_heure_fin','Set heure fin','action','message');
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
			$liste="";
			$cmd->setConfiguration("listValue",$liste);
			$cmd->save();
		}


		$eqLogic->Ajout_Commande('mode_fonctionnement','Mode fonctionnement','info','string',null,null,"auto");
		$eqLogic->Ajout_Commande('heure_fin','Heure fin mode en cours','info','string');
		$eqLogic->Ajout_Commande('action_en_cours','Action en cours','info','string');
		$eqLogic->Ajout_Commande('action_suivante','Action suivante','info','string');
		$eqLogic->Ajout_Commande('planification_en_cours','Planification en cours','info','string');
		
		
		if ($eqLogic->getConfiguration('type','') == 'Poele'){
			//$eqLogic->Ajout_Commande('duree_mode_manuel_par_defaut','Duree mode manuel par defaut (minutes)','info','numeric',null,null,60);
			$eqLogic->Ajout_Commande('absent','Absent','action','message');
			$eqLogic->Ajout_Commande('force','Forcé','action','other');
			$eqLogic->Ajout_Commande('arret','Arrêt','action','other');
			$eqLogic->Ajout_Commande('set_consigne_temperature','Set consigne température','action','slider',7,30);
			$eqLogic->Ajout_Commande('consigne_temperature','Consigne Temperature','info','numeric',null,null,20,'°C');
			//$eqLogic->Ajout_Commande('temperature_consigne_par_defaut','Température consigne par defaut','info','numeric',null,null,20);	
		}
			
		if ($eqLogic->getConfiguration('type','') == 'PAC'){
			//$eqLogic->Ajout_Commande('duree_mode_manuel_par_defaut','Duree mode manuel par defaut (minutes)','info','numeric',null,null,60);
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
		if ($eqLogic->getConfiguration('type','') == 'Prise'){
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
		$set_new_planification=false;
	
		if(count($planifications) == 1 && $Id_planification_en_cours != $arr["Id_planification"]){
			$set_new_planification=true;
		}
	
		if(!isset($arr["select"]) && count($planifications)!=0){
			$arr["select"]=$planifications[0]["nom_planification"];
			$set_new_planification=true;
		}
		if(!isset($arr["Id_planification"]) && count($planifications)!=0){
			$arr["Id_planification"]=$planifications[0]["Id"];
			$set_new_planification=true;
		}
		
		if($set_new_planification){
			$cmd_set_planification->execute($arr);
		}
		

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
		
	function toHtml($_version = 'dashboard') {
		try {
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
			//$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#endtime#',$eqLogic->getCmd(null, 'heure_fin'),"value");


			$cmd_heure_fin=$eqLogic->getCmd(null, 'heure_fin');
			if(is_object($cmd_heure_fin)){
				$heure_fin=strtotime($cmd_heure_fin->execCmd());
				$maintenant=strtotime("now");
				$jour_fin=date('d',$heure_fin);
				$mois_fin=date('m',$heure_fin);
				$année_fin=date('Y',$heure_fin);
				$jour_maintenant=date('d',$maintenant);
				$mois_maintenant=date('m',$maintenant);
				$année_maintenant=date('Y',$maintenant);
				if($jour_fin==$jour_maintenant && $mois_fin==$mois_maintenant && $année_fin==$année_maintenant){
					$replace['#endtime#'] =date('H:i',$heure_fin);
				}else{
					$replace['#endtime#'] =date('d-m-Y H:i',$heure_fin);
				}
			}
			
			



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
			
				$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#action_en_cours#',$eqLogic->getCmd(null, 'action_en_cours'),"value");
				$eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#prochaine_action#',$eqLogic->getCmd(null, 'action_suivante'),"value");

				$imagePAC="PACArret.png";
				$cmd_Mode_fonctionnement=$eqLogic->getCmd(null, 'mode_fonctionnement');
				if (is_object($cmd_Mode_fonctionnement)){
					$Mode_fonctionnement=$cmd_Mode_fonctionnement->execCmd();
				}
				switch (strtolower($Mode_fonctionnement)) {
					case "climatisation":
						$imagePAC="PACClimatisation.png";
					break;
					case "chauffage";
						$imagePAC="PACChauffage.png";
					break;
					case "ventilation":
						$imagePAC="PACVentilation.png";
						break;
					case "arret":
						$imagePAC="PACArret.png";
						break;
					case "boost_on";
					case "auto";
						switch (strtolower($action_en_cours)) {
							case "climatisation":
								$imagePAC="PACClimatisation.png";
							break;
							case "chauffage";
								$imagePAC="PACChauffage.png";
							break;
							case "ventilation":
								$imagePAC="PACVentilation.png";
								break;
							case "arrêt":
								$imagePAC="PACArret.png";
								break;
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
		} catch (Exception $e) {
			planification::add_log($eqLogic,"error",'Erreur lors de la création du widget Détails : '. $e->getMessage());
		}
		return $html;
	}

    function postRemove() {
		$eqLogic=$this;
		$nom_fichier = dirname(__FILE__)."/../../planifications/" . $eqLogic->getId() . ".json";
		planification::add_log($eqLogic,"debug","nom_fichier:".$nom_fichier);
        if (file_exists($nom_fichier)) {
            unlink($nom_fichier);
        }
	}
	function preRemove() {
		
		$eqLogic=$this;
		
		$nom_fichier = dirname(__FILE__)."/../../planifications/" . $eqLogic->getId() . ".json";
		planification::add_log($eqLogic,"debug","nom_fichier:".$nom_fichier);
        if (file_exists($nom_fichier)) {
            unlink($nom_fichier);
		}
		$nom_eq= planification::supp_accents(str_replace(" " , "_",str_replace("[" , "_",str_replace("]" , "",$eqLogic->getHumanName(false)))));
		log::remove('planification'.$nom_eq);
    }
}

class planificationCmd extends cmd {
    //public static $_widgetPossibility = array('custom' => false);

    public function dontRemoveCmd() {
        return true;
    }

    function execute($_options = array()) {
		$cmd=$this;
		$eqLogic = $cmd->getEqLogic();
		planification::add_log($eqLogic,"debug","execute: " . $cmd->getLogicalId());
		switch ($cmd->getLogicalId()) {
			case 'refresh':
				$eqLogic->Verification_log($eqLogic);
				$eqLogic->refresh();
				$eqLogic->Execute_action_actuelle();
				$eqLogic->set_cron();
				break;
			case 'set_heure_fin':				
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
				$planifications=$eqLogic->Recup_planifications();
				
				foreach($planifications as $planification){
					if($_options["select"]==$planification["nom_planification"]){
					$_options["Id_planification"]=$planification["Id"];
					break;	
					}
				}
				planification::add_log($eqLogic,"debug","nom_planification: " . $_options["select"]);	
				planification::add_log($eqLogic,"debug","nom_planification: " . $_options["Id_planification"]);
				$eqLogic->checkAndUpdateCmd('planification_en_cours',$_options["select"]);
				$eqLogic->setConfiguration("Id_planification_en_cours",$_options["Id_planification"]);
				$eqLogic->save(true);
				$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
				if (is_object($cmd_refresh)){
					$cmd_refresh->execute();
				}
				break;
			default:
				if($eqLogic->getConfiguration("type","")== "Poele"){
					switch ($cmd->getLogicalId()) {
						case 'set_consigne_temperature':
							$eqLogic->checkAndUpdateCmd('consigne_temperature',$_options["slider"]);
							$cmd_mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement');
							if (is_object($cmd_mode)){
								if ($cmd_mode->execCmd() != 'force'){							
									$cmd_mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'force');
									if (is_object($cmd_mode)){
										$cmd_mode->execute();
									}
								}
							}
							break;
						case 'auto':
						case 'force':
						case 'arret':
						case 'absent':
							$eqLogic->checkAndUpdateCmd('mode_fonctionnement', $cmd->getLogicalId());
							if($cmd->getLogicalId() == "auto"){
								$temperature_consigne_par_defaut=$eqLogic->getConfiguration("temperature_consigne_par_defaut",20);
								$eqLogic->checkAndUpdateCmd('consigne_temperature', $temperature_consigne_par_defaut);
								$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
								if (is_object($cmd_refresh)){
									$cmd_refresh->execute();
								}
							}else if ($cmd->getLogicalId() == "arret"){
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
									/*$cmd_duree_mode_manuel_par_defaut=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'duree_mode_manuel_par_defaut');
									$duree_mode_manuel_par_defaut=60;
									if (is_object($cmd_duree_mode_manuel_par_defaut)){
										$duree_mode_manuel_par_defaut=$cmd_duree_mode_manuel_par_defaut->execCmd();
									}*/
									$duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",60);
									planification::add_log($eqLogic,"debug","Heure1: " . $duree_mode_manuel_par_defaut);
									$date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
									planification::add_log($eqLogic,"debug","date_Fin" . date ("Y/m/d H:i", $date_Fin));
									$arr=["message" => date ('d-m-Y H:i', $date_Fin)];
									$cmd_set_heure_fin->execute( $arr) ;
								}
							}
							
							break;
					}
		
				}else if($eqLogic->getConfiguration("type","")== "PAC"){
					switch ($cmd->getLogicalId()) {
						case 'set_consigne_temperature':
							$eqLogic->checkAndUpdateCmd('consigne_temperature',$_options["slider"]);
							break;
						case 'auto':
						case 'arret':
						case 'chauffage':
						case 'climatisation':
						case 'ventilation':
						case 'absent':
							$eqLogic->checkAndUpdateCmd('mode_fonctionnement', $cmd->getLogicalId());
							if($cmd->getLogicalId() == "auto"){
								$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
								if (is_object($cmd_refresh)){
									$cmd_refresh->execute();
								}
							}else if ($cmd->getLogicalId() == "arret"){
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
									$duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",60);
									planification::add_log($eqLogic,"debug","Heure1: " . $duree_mode_manuel_par_defaut);
									$date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
									planification::add_log($eqLogic,"debug","date_Fin" . date ("Y/m/d H:i", $date_Fin));
									$arr=["message" => date ('d-m-Y H:i', $date_Fin)];
									$cmd_set_heure_fin->execute( $arr) ;
								}
							}
							break;
						case 'boost_on':
							$eqLogic->checkAndUpdateCmd('boost', 1);
							$eqLogic->checkAndUpdateCmd('mode_fonctionnement', 'boost_on');
							$cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
							if (is_object($cmd_set_heure_fin)){
								$duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",60);
								//planification::add_log($eqLogic,"debug","Heure1: " . $duree_mode_manuel_par_defaut);
								$date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
								planification::add_log($eqLogic,"debug","date_Fin: " . date ("Y/m/d H:i", $date_Fin));
								$arr=["message" => date ('d-m-Y H:i', $date_Fin)];
								$cmd_set_heure_fin->execute( $arr) ;
							}
							$eqLogic->refresh();
							/*$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
							if (is_object($cmd_refresh)){
								$cmd_refresh->execute();
							}*/
							break;
		
						case 'boost_off':
				
							$eqLogic->checkAndUpdateCmd('boost', 0);
							$eqLogic->checkAndUpdateCmd('mode_fonctionnement', 'auto');
							$eqLogic->refresh();
							/*$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
							if (is_object($cmd_refresh)){
								$cmd_refresh->execute();
							}*/
							break;
						default:
					}
				}else if ($eqLogic->getConfiguration("type","")== "Chauffage"){
					switch ($cmd->getLogicalId()) {
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
						case 'auto':
						case 'confort':
						case 'eco':
						case 'hors_gel':
						case 'absent':
						case 'arret':
							$eqLogic->checkAndUpdateCmd('mode_fonctionnement', $cmd->getLogicalId());
							if($cmd->getLogicalId() == "auto"){
								$cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
								if (is_object($cmd_refresh)){
									$cmd_refresh->execute();
								}
							}else if ($cmd->getLogicalId() == "arret"){
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
									$duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",60);
									planification::add_log($eqLogic,"debug","Heure1: " . $duree_mode_manuel_par_defaut);
									$date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
									planification::add_log($eqLogic,"debug","date_Fin" . date ("Y/m/d H:i", $date_Fin));
									$arr=["message" => date ('d-m-Y H:i', $date_Fin)];
									$cmd_set_heure_fin->execute( $arr) ;
								}
							}
							break;
						default:
					}
					
				}else if ($eqLogic->getConfiguration("type","")== "Volet"){
					switch ($cmd->getLogicalId()) {
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
						default:
					}
					
				}else if ($eqLogic->getConfiguration("type","")== "Prise"){
		
					switch ($cmd->getLogicalId()) {
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
						default:
						
					}
				}
				break;
				
		}
		
		
		$eqLogic->refreshWidget() ;
    }
}
?>
