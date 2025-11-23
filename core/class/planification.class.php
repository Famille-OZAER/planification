<?php
  require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class EquipementInfo1 { 


  public $planifications;
  public $Automatisation_Paramètres;
  public $Automatisation_Ouvrants;
  public $Automatisation_Gestion_planifications;
}
class planification extends eqLogic {

  public static $_widgetPossibility = array('custom' => true);
  public static function GetVersionPlugin(){
    // Version dans le fichier info du plugin
    $pluginVersion = 'introuvable';
    try {
      if (!file_exists(dirname(__FILE__) . '/../../plugin_info/info.json')) {
        planification::add_log( 'warning', 'fichier info.json manquant');
      }
      $data = json_decode(file_get_contents(dirname(__FILE__) . '/../../plugin_info/info.json'), true);
      if (!is_array($data)) {
        planification::add_log('warning', 'Impossible de décoder le fichier \'info.json\'');
      }
      try {
        $pluginVersion = $data['pluginVersion'];
        // Enregistrement de la version dans la configuration du plugin (BD Jeedom)
        //config::save('pluginVersion', $pluginVersion, 'planification');
      }
      catch (\Exception $e) {
        planification::add_log('warning', 'Impossible de mettre à jour la version du plugin: ' . $e->getMessage());
      }
    }
    catch (\Exception $e) {
      planification::add_log( 'warning', 'Erreur: ' . $e->getMessage());
    }
    planification::add_log('info', $pluginVersion);    

    return $pluginVersion;
  } 

  public static function deamon_info() {
    $return = array();
    $return['log'] = 'planification';
    $return['state'] = 'nok';
    $pid_file = jeedom::getTmpFolder('planification') . '/deamon_planification.pid';
    if (file_exists($pid_file)) {

      if (@posix_getsid(trim(file_get_contents($pid_file)))) {
        $return['state'] = 'ok';
      } else {
        shell_exec(system::getCmdSudo() . 'rm -rf ' . $pid_file . ' 2>&1 > /dev/null;rm -rf ' . $pid_file . ' 2>&1 > /dev/null;');
      }
    }
    $return['launchable'] = 'ok';
    return $return;
  }

  public static function deamon_start() {
    self::deamon_stop();
    self::deamon_info();
    $cmd = 'sudo /usr/bin/php ' . realpath(dirname(__FILE__) . '/../..') . '/deamon/deamon_planification.php start';

    //$commande = "sudo chmod 0777 ../../../../log/planification" . $nom_eq;
    //       echo "commande1: " . $commande;
    // $resultat = shell_exec($commande); 
    exec($cmd . ' >> ' . log::getPathToLog('planification') . ' 2>&1 &');
    return true;
  }

  public static function deamon_stop() {
    $pid_file = jeedom::getTmpFolder('planification') . '/deamon_planification.pid';
    if (file_exists($pid_file)) {
      $pid = intval(trim(file_get_contents($pid_file)));
      system::kill($pid);
    }
    system::kill('deamon_planification.php');
  }

  public static function Recup_infos_lever_coucher_soleil($eqLogic_id, $Json_heure_lever_coucher = null){

    $eqLogic = eqLogic::byId($eqLogic_id);

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
      $retour["Lever_soleil"] = $Lever_Soleil;
      $retour["Coucher_soleil"] = $Coucher_Soleil;
      $Lever_Soleil_int = intval(preg_replace( '/:.*/', '', date_sunrise(time(), SUNFUNCS_RET_STRING, $latitude, $longitude, 90.58, (date("O"))/100)).preg_replace( '/.*:/', '', date_sunrise(time(), SUNFUNCS_RET_STRING, $latitude, $longitude, 90.58, (date("O"))/100)));
      $Coucher_Soleil_int = intval(preg_replace( '/:.*/', '', date_sunset(time(), SUNFUNCS_RET_STRING, $latitude, $longitude, 90.58, (date("O"))/100)).preg_replace( '/.*:/', '', date_sunset(time(), SUNFUNCS_RET_STRING, $latitude, $longitude, 90.58, (date("O"))/100)));
      $retour["Heure_action_suivante_lever_lundi"] = $Lever_Soleil;
      $retour["Heure_action_suivante_coucher_lundi"] = $Coucher_Soleil;
      $retour["Heure_action_suivante_lever_mardi"] = $Lever_Soleil;
      $retour["Heure_action_suivante_coucher_mardi"] = $Coucher_Soleil;
      $retour["Heure_action_suivante_lever_mercredi"] = $Lever_Soleil;
      $retour["Heure_action_suivante_coucher_mercredi"] = $Coucher_Soleil;
      $retour["Heure_action_suivante_lever_jeudi"] = $Lever_Soleil;
      $retour["Heure_action_suivante_coucher_jeudi"] = $Coucher_Soleil;
      $retour["Heure_action_suivante_lever_vendredi"] = $Lever_Soleil;
      $retour["Heure_action_suivante_coucher_vendredi"] = $Coucher_Soleil;
      $retour["Heure_action_suivante_lever_samedi"] = $Lever_Soleil;
      $retour["Heure_action_suivante_coucher_samedi"] = $Coucher_Soleil;
      $retour["Heure_action_suivante_lever_dimanche"] = $Lever_Soleil;
      $retour["Heure_action_suivante_coucher_dimanche"] = $Coucher_Soleil;
      $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

      foreach ($jours as $jour) {
          $retour["Heure_lever_min_$jour"] = "00:00";
          $retour["Heure_lever_max_$jour"] = "23:59";
          $retour["Heure_coucher_min_$jour"] = "00:00";
          $retour["Heure_coucher_max_$jour"] = "23:59";
      }

      if($Json_heure_lever_coucher == null){
        $Json=$eqLogic::Get_Json($eqLogic_id);
        if ($Json == ''){
           return $retour;
        }        
        $Json_heure_lever_coucher = $Json["Lever_coucher"][0]; 
      }
      foreach ($Json_heure_lever_coucher as $key => $value) { 
        $Heure = $value;
        $Heure_int = str_replace ( ":" ,"" ,$value);
        if (strpos($key, "HeureLeverMin") !== false){
          $retour["Heure_lever_min_".strtolower(substr($key, 14, strlen($key)))] = $Heure;
          if (!is_nan(intval($Heure_int)) and $Lever_Soleil_int < intval($Heure_int)){
            $retour["Heure_action_suivante_lever_".strtolower(substr($key, 14, strlen($key)))] = $Heure;
          }
        }
        if (strpos($key, "HeureLeverMax") !== false){
          $retour["Heure_lever_max_".strtolower(substr($key, 14, strlen($key)))] = $Heure;
          if (!is_nan(intval($Heure_int)) and $Lever_Soleil_int > intval($Heure_int)){
            $retour["Heure_action_suivante_lever_".strtolower(substr($key, 14, strlen($key)))] = $Heure;
          }
        }
        if (strpos($key, "HeureCoucherMin") !== false){
          $retour["Heure_coucher_min_".strtolower(substr($key, 16, strlen($key)))] = $Heure;
          if (!is_nan(intval($Heure_int)) and $Coucher_Soleil_int < intval($Heure_int)){
            $retour["Heure_action_suivante_coucher_".strtolower(substr($key, 16, strlen($key)))] = $Heure;
          }
        }
        if (strpos($key, "HeureCoucherMax") !== false){
          $retour["Heure_coucher_max_".strtolower(substr($key, 16, strlen($key)))] = $Heure;
          if (!is_nan(intval($Heure_int)) and $Coucher_Soleil_int > intval($Heure_int)){
            $retour["Heure_action_suivante_coucher_".strtolower(substr($key, 16, strlen($key)))] = $Heure;
          }
        }
      }


    }


    return $retour;
  }

  static function execute_action($eqLogic,$eqLogic_cmd = null,$cmd = null){
    $options_str='';
    
    planification::add_log("debug",'----------------------',$eqLogic);	
    if(is_object($eqLogic_cmd)){
    planification::add_log("debug",'objet eqlogic_cmd OK',$eqLogic);	
      if (is_numeric (trim($cmd, "#"))){
        $cmd=cmd::byId(trim($cmd, "#"));
        $nom_commande=$cmd->getName();
        if(is_object($cmd)){

          $eqLogic_cmd=eqLogic::byId($cmd->getEqLogic_id()) ;

          if($eqLogic_cmd->getObject()==""){
            $Object="Aucun";
          }else{
            $Object=$eqLogic_cmd->getObject()->getName();
          }

          planification::add_log("debug",'execution action: #[' . $Object."][".$eqLogic_cmd->getName()."][".$cmd->getName()."]#",$eqLogic);
          $cmd->execCmd($eqLogic_cmd->getConfiguration("options",""));
        }
      }else if ($cmd !=""){
       
        //$eqLogic_cmd->execCmd();
        $options_str="";
        $options=$eqLogic_cmd->getConfiguration("options","");

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
          $options_str='#[' . $Object."][".$eqLogic_cmd->getName()."][".$cmd_event->getName()."]#" . "=>" .$options["value"];
        }else{
          $options_str=implode("*",$options);
        }
        
         
         
        planification::add_log("debug",'execution action: ' . $cmd . ":" .$options_str,$eqLogic);		
        //$cmd=cmd::byId($eqLogic_cmd->getId());
        //$cmd->execCmd();
        scenarioExpression::createAndExec('action', $cmd, $options);

      }
    }


    $eqLogic_id = $eqLogic->getId();
    if (!isset($equipementsInfos[$eqLogic_id])) {
      $equipementsInfos[$eqLogic_id] = new EquipementInfo1($eqLogic_id); 
      $Json=$eqLogic::Get_Json($eqLogic_id);
      $equipementsInfos[$eqLogic_id]->planifications = $Json["Planifications"][0]; 
      if (isset($Json["Paramètres"])){

        $equipementsInfos[$eqLogic_id]->Automatisation_Paramètres = $Json["Paramètres"][0];
      }        
      if (isset($Json["Ouvrants"])){
        $equipementsInfos[$eqLogic_id]->Automatisation_Ouvrants = $Json["Ouvrants"][0];
      } 
      if (isset($Json["Gestion_planifications"])){
        $equipementsInfos[$eqLogic_id]->Automatisation_Gestion_planifications =$Json["Gestion_planifications"][0];
      }
    }
    if ($eqLogic->getConfiguration('Type_équipement','') == 'PAC'){
      planification::add_log("debug",$equipementsInfos[$eqLogic_id]->Automatisation_Paramètres["Type_équipement_pilote"],$eqLogic);
      $température_consigne = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature')->execCmd();
      if ($equipementsInfos[$eqLogic_id]->Automatisation_Paramètres["Type_équipement_pilote"] == "broadlink"){
        $nom_commande=$eqLogic_cmd->getName();
        $eqLogic_broalink = eqLogic::byId(trim($equipementsInfos[$eqLogic_id]->Automatisation_Paramètres["Equipement_pilote"], "#"));
        if (is_object($eqLogic_broalink)){
          if(strtolower( $nom_commande) == 'boost on' || strtolower($nom_commande) == 'boost off' ){
            $cmd = cmd::byEqLogicIdCmdName( $eqLogic_broalink->getId(),  $nom_commande);
            if (is_object($cmd)){
              $cmd->execCmd();
            }
            if(strtolower( $nom_commande) == 'boost on' && strtolower(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->execCmd()) == 'chauffage'){
                planification::add_log("debug","OK",$eqLogic);
              $nom_commande_broadlink = 'Chauffage ' . strval( $température_consigne + cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'delta_chauffage_boost')->execCmd());
             planification::add_log("debug",$nom_commande_broadlink,$eqLogic);
            }else if(strtolower( $nom_commande) == 'boost off' && strtolower(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->execCmd()) == 'chauffage'){
                $nom_commande_broadlink = 'Chauffage ' .  $température_consigne;
            }else if(strtolower( $nom_commande) == 'boost on' && strtolower(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->execCmd()) == 'climatisation'){
                $nom_commande_broadlink = 'Climatisation ' .  $température_consigne;
            }else if(strtolower( $nom_commande) == 'boost off' && strtolower(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->execCmd()) == 'climatisation'){
                $nom_commande_broadlink = 'Climatisation ' .  strval( $température_consigne + cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'delta_climatisation_boost')->execCmd());
            }
          } else{
            $température_consigne =  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature')->execCmd();
            if (strtolower($nom_commande) == 'chauffage eco') {
              $nom_commande_broadlink = 'Chauffage ' . strval( $température_consigne - cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'delta_chauffage_eco')->execCmd());
            }elseif (strtolower($nom_commande) == 'chauffage'){
              $nom_commande_broadlink = 'Chauffage ' .  $température_consigne;
            }elseif(strtolower($nom_commande) == 'climatisation'){
              $nom_commande_broadlink = 'Climatisation ' .  $température_consigne;
            }elseif(strtolower($nom_commande) == 'ventilation'){
              $nom_commande_broadlink = 'Ventilation';
            }elseif(strtolower($nom_commande) == 'arrêt'){
              $nom_commande_broadlink = 'Arrêt';
            }
            
          }
          planification::add_log("info",'exeution commande: ' . $nom_commande_broadlink . " sur l'équipement broadlink: " . $eqLogic_broalink->getName(),$eqLogic);	

          $cmd = cmd::byEqLogicIdCmdName( $eqLogic_broalink->getId(),  $nom_commande_broadlink);
          if (is_object($cmd)){
            $cmd->execute();
          }else{
            planification::add_log("info",'La commande: ' . $nom_commande_broadlink . " n'existe pas dans l'équipement broadlink: " . $eqLogic_broalink->getName(),$eqLogic);	
          }
        }



      }
    }
    if ($eqLogic->getConfiguration('Type_équipement','') == 'Thermostat'){
      planification::add_log("debug",$equipementsInfos[$eqLogic_id]->Automatisation_Paramètres["Type_équipement_pilote"],$eqLogic);
      $température_consigne = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature')->execCmd();
      if ($equipementsInfos[$eqLogic_id]->Automatisation_Paramètres["Type_équipement_pilote"] == "zwave"){
        $nom_commande=$eqLogic_cmd->getName();
        $eqLogic_zwave = eqLogic::byId(trim($equipementsInfos[$eqLogic_id]->Automatisation_Paramètres["Equipement_pilote"], "#"));
        if (is_object($eqLogic_zwave)){
         
            $température_consigne =  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature')->execCmd();
            if (strtolower($nom_commande) == 'chauffage eco') {
              $nom_commande_zwave = 'Chauffage ' . strval( $température_consigne - cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'delta_chauffage_eco')->execCmd());
            }elseif (strtolower($nom_commande) == 'chauffage'){
              $nom_commande_zwave = 'Chauffage ' .  $température_consigne;
            }elseif(strtolower($nom_commande) == 'arrêt'){
              $nom_commande_zwave = 'Arrêt';
            }
            
          
          planification::add_log("info",'exeution commande: ' . $nom_commande_zwave . " sur l'équipement zwave: " . $eqLogic_zwave->getName(),$eqLogic);	

          $cmd = cmd::byEqLogicIdCmdName( $eqLogic_zwave->getId(),  $nom_commande_zwave);
          if (is_object($cmd)){
            $cmd->execute();
          }else{
            planification::add_log("info",'La commande: ' . $nom_commande_zwave . " n'existe pas dans l'équipement zwave: " . $eqLogic_zwave->getName(),$eqLogic);	
          }
        }



      }
    }
  }

  static function Recup_liste_commandes_planification($eqLogic_id) {
    $eqLogic = eqLogic::byId($eqLogic_id);
    if (!is_object($eqLogic)) {
      throw new Exception(__('Impossible de trouver l\'équipement : ', __FILE__) . $eqLogic_id);
    }
    $cmds=$eqLogic-> searchCmdByConfiguration("Type");
    $res=[];

    if ($cmds !=""){
      foreach ($cmds as $cmd) {
        $cmd1["Id"] = $cmd->getId();    
        $cmd1["Nom"] = $cmd->getName();  
        $cmd1["couleur"] = $cmd->getConfiguration("Couleur");             
        array_push($res,$cmd1);
      }
    }
    return $res;

  }
  function Get_Json($eqLogic_id){
    $eqLogic = eqLogic::byId($eqLogic_id);
    $Json = "";
    $nom_fichier = dirname(__FILE__) ."/../../planifications/" . $eqLogic->getId() . ".json";
    if(file_exists ($nom_fichier) ){
      $Json = json_decode(file_get_contents ($nom_fichier),true)[0];
    }

    return $Json;
  }  
  function Get_planifications($all = false,$_objet = null){ //OK
    $eqLogic = $this;
    if ($_objet == null){
      $Json=$eqLogic::Get_Json($eqLogic_id);
      $planifications = $Json["Planifications"][0]; 
      planification::add_log("debug","_objet == null",$eqLogic);
    }else{
      $planifications = $_objet->planifications; 
    }


    if($all){     
      return $planifications;
    }
    if($planifications == ""){return [] ;}




    if($_objet->Id_planification_en_cours == ""){

      planification::add_log("debug","Aucun Id de planification enregistré",$eqLogic);
      $eqLogic->checkAndUpdateCmd('action_en_cours', '');
      return;
    }
    $cette_planification=[];
    $i=1;
    foreach($planifications as $planification){
      if($planification[0]["Id"] == $_objet->Id_planification_en_cours){
        $cette_planification["Id"] = $planification[0]["Id"];
        $cette_planification["Nom"] = $planification[0]["Nom"];
        $cette_planification["Lundi"] = $planification[0]["Lundi"];
        $cette_planification["Mardi"] = $planification[0]["Mardi"];
        $cette_planification["Mercredi"] = $planification[0]["Mercredi"];
        $cette_planification["Jeudi"] = $planification[0]["Jeudi"];
        $cette_planification["Vendredi"] = $planification[0]["Vendredi"];
        $cette_planification["Samedi"] = $planification[0]["Samedi"];
        $cette_planification["Dimanche"] = $planification[0]["Dimanche"];
        break;
      }
      $i++;
    }



    return $cette_planification;


  }
  static function TestExpression($expression){
    //la variable $scenario doit absolument rester
    $scenario = null;
    return evaluate(scenarioExpression::setTags($expression, $scenario, true));



  }
  function Get_actions_planification($_objet){//fonction pour le deamon
    $eqLogic=$this;   
    $eqLogic_id = $eqLogic->getId();
    if($_objet->Id_planification_en_cours == ""){return;}    
    if($_objet->mode_fonctionnement == "Auto"){  
      $infos_lever_coucher_soleil = $_objet->infos_lever_coucher_soleil;
      $planifications=$eqLogic::Get_planifications(false,$_objet);

      if(count($planifications) == 0){
        planification::add_log("debug",'pas de planification' ,$eqLogic);
        return;
      }
      //action actuelle
      if (true){
        if ($_objet->timestamp_action_suivante == ''){
          $_objet->timestamp_action_suivante = time();
        }
        $noms_jours = array("Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche");  
        $trouve=false;
        $i=1;
        retry:
        if ( date('N')-$i < 0){
          $periodes_jour =[[]];
        }else{ 
          $periodes_jour=$planifications[$noms_jours[date('N')-$i]];
        }





        if($periodes_jour ==[[]] && $i == 7){
          planification::add_log("debug","Planification vide-> fin de la fonction",$eqLogic);
          $eqLogic->getCmd(null, "action_en_cours")->set_value('');         
          return;
        }
        if($periodes_jour ==[[]]&& $i < 7){
          $i=$i+1;
          goto retry;        
        }
        $nb=0;
        $numBoucle=0;	
        //modification dynamique de l'heure de lever/coucher de soleil
        foreach($periodes_jour as $periode){       
          if($periode["Type"] == "lever"){
            $periode["Début"]=$infos_lever_coucher_soleil["Heure_action_suivante_lever_".strtolower ($noms_jours[date('N')-1])];
          }else if ($periode["Type"] == "coucher"){
            $periode["Début"]=$infos_lever_coucher_soleil["Heure_action_suivante_coucher_".strtolower ($noms_jours[date('N')-1])];
          }          
          $periodes_jour[$nb]=$periode;
          $nb+=1;
        }	
        array_multisort(array_column($periodes_jour, 'Début'), SORT_ASC, $periodes_jour);

        foreach($periodes_jour as $periode){
          $date=date_add(date_create_from_format ( 'Y-m-d H:i' ,date('Y-m-d '). $periode["Début"]), date_interval_create_from_date_string('0 days'));

          if($date->getTimestamp() <=  $_objet->timestamp_action_suivante  ){

            $trouve=true;
            $action["Id"]=$periode["Id"];
            $action["Nom"]=cmd::byId($periode["Id"])->getName();
          } 
          $numBoucle+=1;
        } 
        if($trouve){
          if($action["Nom"] !=  $_objet->action_en_cours){
            $cmd=cmd::byId($action["Id"]);          
            $cmd->execCmd(array('mode'=>"auto"));
            $_objet->action_en_cours = $action["Nom"];
            planification::add_log("debug","action_en_cours : " .$_objet->action_en_cours ,$eqLogic,"planification_deamon");
           

          }
        }else{         
          cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->set_value('');
          planification::add_log("debug","Aucune action en cours trouvée.",$eqLogic);
        }
      }

      //action suivante 
      if(true){
        $noms_jours = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
        $maintenant = date_create_from_format('Y-m-d H:i', date('Y-m-d H:i'));
        $jour_actuel = date('N');
        $trouve = false;

        for ($i = 0; $i < 7; $i++) {
          $index_jour = ($jour_actuel + $i - 1) % 7;
          $jour_nom = $noms_jours[$index_jour];
          $periodes = $planifications[$jour_nom];

          if ($periodes == [[]]) continue;

          foreach ($periodes as &$periode) {
            $jour_minuscule = strtolower($jour_nom);
            switch ($periode["Type"]) {
              case "lever":
                $periode["Début"] = $infos_lever_coucher_soleil["Heure_action_suivante_lever_$jour_minuscule"];
                
                break;
              case "coucher":
                $periode["Début"] = $infos_lever_coucher_soleil["Heure_action_suivante_coucher_$jour_minuscule"];
                break;
              default:
                $periode["Début"] = str_pad($periode["Début"], 5, "0", STR_PAD_LEFT);
            }
          }
          unset($periode); // protection contre modification de la dernière référence

          array_multisort(array_column($periodes, 'Début'), SORT_ASC, $periodes);

          foreach ($periodes as $periode) {
            $datetime_action = date_create_from_format('Y-m-d H:i', date('Y-m-d ') . $periode["Début"]);
            $datetime_action->modify("+$i days");

            $cmd_name = cmd::byId($periode["Id"])->getName();
            if ($datetime_action > $maintenant && !$trouve && $_objet->action_en_cours !== $cmd_name) {
              $trouve = true;
              $action = [
                "Id" => $periode["Id"],
                "Nom" => $cmd_name,
                "date" => $datetime_action
              ];
              planification::add_log("debug", "action suivante: " . $cmd_name . " à " . $datetime_action->format('H:i'), $eqLogic, "planification_deamon");
              break 2; // Sortir des deux boucles dès qu'on a trouvé
            }
          }
        }

        if ($trouve) {
          $cmd_nom = $eqLogic->getCmd(null, "action_suivante");
          $cmd_date = $eqLogic->getCmd(null, "heure_fin");

          if ($cmd_nom->execCmd() !== $action["Nom"] || $cmd_date->execCmd() !== $action["date"]->format('d-m-Y H:i')) {
            $cmd_nom->set_value($action["Nom"]);
            $cmd_date->set_value($action["date"]->format('d-m-Y H:i'));
          }
        } else {
          $cmd_nom = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), 'action_suivante');
          $cmd_date = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), 'heure_fin');

          if ($cmd_nom->getValue() !== "" || $cmd_date->getValue() !== "") {
            $cmd_nom->set_value('');
            $cmd_date->set_value('');
            planification::add_log("debug", "Aucune action suivante trouvée.", $eqLogic, "planification_deamon");
          }
        }
      }

    }else if ($_objet->mode_fonctionnement == "Manuel"){
       
        
      $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),strtolower( $_objet->action_en_cours));
       planification::add_log("debug",$_objet);
       planification::add_log("debug",$_objet->action_en_cours);
        planification::add_log("debug",$_objet->mode_fonctionnement,$eqLogic);
        planification::add_log("debug",$eqLogic_cmd,$eqLogic);
      if( $_objet->action_en_cours == $_objet->mode_fonctionnement || strtolower( $_objet->action_en_cours) == $eqLogic_cmd->getLogicalId()){
        planification::add_log("debug",'Action identique fin de la fonction.',$eqLogic);
        return;
      }
      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin')->set_value('');
      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_suivante')->event('');
      $cmd=$eqLogic_cmd->getConfiguration("commande","");
      planification::execute_action($eqLogic,$eqLogic_cmd,$cmd);
    }else{
      $eqLogic->Recup_valeur_sans_cache();
    }
  }  
  static function supp_accents( $str, $charset='utf-8' ) {
    $str = htmlentities( $str, ENT_NOQUOTES, $charset );
    $str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
    $str = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
    $str = preg_replace( '#&[^;]+;#', '', $str );
    return $str;
  }
  static function add_log($level = 'debug',$Log,$eqLogic = null,$nom_log = null){

    if (is_array($Log)) $Log = json_encode($Log);
    if (is_object($Log)) $Log = json_encode($Log);
    if(count(debug_backtrace(false, 2)) == 1){
      $function_name = debug_backtrace(false, 2)[0]['function'];
      $ligne = debug_backtrace(false, 2)[0]['line'];
    }else{
      $function_name = debug_backtrace(false, 2)[1]['function'];
      $ligne = debug_backtrace(false, 2)[0]['line'];
    }
    $msg =  $function_name .' (' . $ligne . '): '.$Log;
    $UseLogByeqLogic=config::byKey('UseLogByeqLogic', 'planification');
    if( $eqLogic != null){
      $nom_eq= planification::supp_accents(str_replace(" " , "_",str_replace("[" , "_",str_replace("]" , "",$eqLogic->getHumanName(false)))));

      if($UseLogByeqLogic){
        if($nom_log == null){  
          $msg =  $function_name .' (' . $ligne . '): '  .$Log;         
          log::add('planification'. $nom_eq  , $level,$msg);
        }else{ 
          //$nom_eq= replace($eqLogic->getHumanName(true,true));  
          $msg =  $function_name .' (' . $ligne . '): ' . $nom_eq . ": "  .$Log;      
          log::add($nom_log , $level, $msg);
        }

      }else{
        $nom_eq= $eqLogic->getHumanName();
        if($nom_log == null){
          $msg =  $function_name .' (' . $ligne . '): ' . $nom_eq . ": "  .$Log;  
          log::add('planification' , $level,$msg);
        }else{
          $msg =  $function_name .' (' . $ligne . '): ' . $nom_eq . ": "  .$Log;  
          log::add($nom_log , $level, $msg);
        }       
      }
    }else{

      if($nom_log == null){
        log::add('planification' , $level,$msg);
      }else{
        log::add($nom_log , $level,$msg);
      }

    }


  }
  function Recup_valeur_sans_cache(){

    $eqLogic=$this;
    $cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement');
    if($cmd->execCmd() == ""){
      $cmd->set_value($cmd->getValue());
      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin')->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin')->getValue());
      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->getValue());
      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_suivante')->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_suivante')->getValue());
      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'planification_en_cours')->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'planification_en_cours')->getValue());
      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'info')->set_value($cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'info')->getValue());
    }





    if ($eqLogic->getConfiguration('Type_équipement','') == 'PAC'){
      planification::add_log("info",'Recup_valeur_sans_cache');
      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature_chauffage')->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature_chauffage')->getValue());
      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature_climatisation')->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature_climatisation')->getValue());
      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'boost')->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'boost')->getValue());
      if(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->execCmd() == 'Climatisation'){
        cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature')->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature_climatisation')->getValue());
      }else{
        cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature')->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature_chauffage')->getValue());
      }






    }


  } 
  function Ajout_Commande($logical_id,$name,$type,$sous_type,$min=null,$max=null,$valeur_par_defaut=null,$unite=null){
    $eqLogic = $this;
    $eqLogic_id = $eqLogic->getId();
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
        $cmd->set_value($valeur_par_defaut);

      }
      if($logical_id == 'boost_off'){
        $cmd->set_value(0);
      }


    }

    if($eqLogic->getconfiguration("Type_équipement","")=="PAC" || $eqLogic->getconfiguration("Type_équipement","")=="Thermostat"){
      if($cmd->getLogicalId() == "arret" || $cmd->getLogicalId() == "climatisation" || $cmd->getLogicalId() == "ventilation" || $cmd->getLogicalId() == "chauffage" || $cmd->getLogicalId()== "chauffage ECO"){
        $cmd->setConfiguration('Type',"Planification");
        if ($cmd->getConfiguration('Couleur',"")== ""){
          $cmd->setConfiguration('Couleur',"orange");
        }
        $cmd->save();
      }
    }
    if($eqLogic->getconfiguration("Type_équipement","")=="Volet"){
      if($cmd->getLogicalId() == "ouverture" || $cmd->getLogicalId() == "fermeture" || $cmd->getLogicalId() == "my"){
        $cmd->setConfiguration('Type',"Planification");
        if ($cmd->getConfiguration('Couleur',"")== ""){
          $cmd->setConfiguration('Couleur',"orange");
        }
        $cmd->save();
      }
    }
    if($eqLogic->getconfiguration("Type_équipement","")=="Chauffage"){
      if($cmd->getLogicalId() == "eco" || $cmd->getLogicalId() == "confort" || $cmd->getLogicalId() == "arret"|| $cmd->getLogicalId() == "hors_gel"){
        $cmd->setConfiguration('Type',"Planification");
        if ($cmd->getConfiguration('Couleur',"")== ""){
          $cmd->setConfiguration('Couleur',"orange");
        }
        $cmd->save();
      }
    }
    if($eqLogic->getconfiguration("Type_équipement","")=="Prise"){
      if($cmd->getLogicalId() == "on" || $cmd->getLogicalId() == "off"){
        $cmd->setConfiguration('Type',"Planification");
        if ($cmd->getConfiguration('Couleur',"")== ""){
          $cmd->setConfiguration('Couleur',"orange");
        }
        $cmd->save();
      }
    }

  }
  function preSave() {
    $eqLogic=$this;
    $eqLogic->setLogicalId(planification::supp_accents($eqLogic->getName()));
    $eqLogic->save(true);

  }
  function postSave() {

    $eqLogic=$this;
    $eqLogic_id = $eqLogic->getId(); 
    $retour=$eqLogic->getConfiguration("Chemin_image","none");
    if($retour='none'){
      $eqLogic->setConfiguration("Chemin_image","");
    }   
    if (true){
      $eqLogic->Ajout_Commande('mode_fonctionnement','Mode fonctionnement','info','string',null,null,"Auto");
      $eqLogic->Ajout_Commande('heure_fin','Heure fin action en cours','info','string');
      $eqLogic->Ajout_Commande('action_en_cours','Action en cours','info','string');
      $eqLogic->Ajout_Commande('action_suivante','Action suivante','info','string');
      $eqLogic->Ajout_Commande('planification_en_cours','Planification en cours','info','string');
      $eqLogic->Ajout_Commande('mode_planification','Mode Planification','info','string',null,null,"Auto");
      $eqLogic->Ajout_Commande('refresh','Rafraichir','action','other');
      $eqLogic->Ajout_Commande('auto','Auto','action','other');
      $eqLogic->Ajout_Commande('set_heure_fin','Set heure fin','action','message');
      $cmd_set_planification = $eqLogic->getCmd(null, "set_planification");
    } 
    
    if (!is_object($cmd_set_planification)) {
      $cmd_set_planification = new planificationCmd();
      $cmd_set_planification->setLogicalId("set_planification");
      $cmd_set_planification->setIsVisible(1);
      $cmd_set_planification->setName("Set planification");
      $cmd_set_planification->setType("action");
      $cmd_set_planification->setSubType("select");
      $cmd_set_planification->setEqLogic_id($eqLogic->getId());
      $cmd_set_planification->setConfiguration("infoName", $eqLogic->getCmd(null, "planification_en_cours")->getName());
      $cmd_set_planification->setConfiguration("infoId", $eqLogic->getCmd(null, "planification_en_cours")->getId());
      $cmd_set_planification->setValue( $eqLogic->getCmd(null, "planification_en_cours")->getId());
      $liste="";
      $cmd_set_planification->setConfiguration("listValue",$liste);
      $cmd_set_planification->save();
    }


    if ($eqLogic->getConfiguration('Type_équipement','') == 'PAC'){
      $eqLogic->Ajout_Commande('climatisation','Climatisation','action','other');
      $eqLogic->Ajout_Commande('ventilation','Ventilation','action','other');
      $eqLogic->Ajout_Commande('chauffage','Chauffage','action','other');
      $eqLogic->Ajout_Commande('chauffage ECO','Chauffage ECO','action','other');
      $eqLogic->Ajout_Commande('arret','Arrêt','action','other');
      $eqLogic->Ajout_Commande('set_consigne_temperature','Set consigne température','action','slider',7,30);
      $eqLogic->Ajout_Commande('consigne_temperature','Consigne Temperature','info','numeric',null,null,20,'°C');
      $eqLogic->Ajout_Commande('consigne_temperature_chauffage','Consigne Temperature Chauffage','info','numeric',null,null,20,'°C');
      $eqLogic->Ajout_Commande('consigne_temperature_climatisation','Consigne Temperature Climatisation','info','numeric',null,null,20,'°C');
      $eqLogic->Ajout_Commande('boost','Boost','info','binary',null,null,0,null);
      $eqLogic->Ajout_Commande('boost_on','Boost On','action','other');
      $eqLogic->Ajout_Commande('boost_off','Boost Off','action','other');
      $eqLogic->Ajout_Commande('delta_chauffage_eco','Delta chauffage ECO','info','numeric',0,5,2,'°C');
      $eqLogic->Ajout_Commande('delta_chauffage_boost','Delta chauffage boost','info','numeric',0,5,1,'°C');
      $eqLogic->Ajout_Commande('temperature_mini_chauffage_continu','Température extérieure en dessous de laquelle la PAC doit être en chauffage continu','info','numeric',0,10,5,'°C');
      $eqLogic->Ajout_Commande('temperature_mini_chauffage','Température extérieure en dessous de laquelle la PAC peut être en chauffage','info','numeric',10,20,18,'°C');
      $eqLogic->Ajout_Commande('numéro_semaine_mini_chauffage','Numéro de semaine minimum pour activer le chauffage','info','numeric',1,52,39,'');
      $eqLogic->Ajout_Commande('numéro_semaine_max_chauffage','Numéro de semaine maximum pour activer le chauffage','info','numeric',1,52,18,'');
      $eqLogic->Ajout_Commande('delta_climatisation_boost','Delta climatisation boost','info','numeric',0,5,1,'°C');
      $eqLogic->Ajout_Commande('temperature_mini_climatisation','Température extérieure en dessus de laquelle la PAC peut être en climatisation','info','numeric',20,25,22,'°C');
    }
     if ($eqLogic->getConfiguration('Type_équipement','') == 'Thermostat'){    
      $eqLogic->Ajout_Commande('chauffage','Chauffage','action','other');
      $eqLogic->Ajout_Commande('chauffage ECO','Chauffage ECO','action','other');
      $eqLogic->Ajout_Commande('arret','Arrêt','action','other');
      $eqLogic->Ajout_Commande('set_consigne_temperature','Set consigne température','action','slider',7,30);
      $eqLogic->Ajout_Commande('consigne_temperature','Consigne Temperature','info','numeric',null,null,20,'°C');
      $eqLogic->Ajout_Commande('delta_chauffage_eco','Delta chauffage ECO','info','numeric',0,5,2,'°C');
      $eqLogic->Ajout_Commande('temperature_mini_chauffage_continu','Température extérieure en dessous de laquelle la PAC doit être en chauffage continu','info','numeric',0,10,5,'°C');
      $eqLogic->Ajout_Commande('temperature_mini_chauffage','Température extérieure en dessous de laquelle la PAC peut être en chauffage','info','numeric',10,20,18,'°C');
      $eqLogic->Ajout_Commande('numéro_semaine_mini_chauffage','Numéro de semaine minimum pour activer le chauffage','info','numeric',1,52,39,'');
      $eqLogic->Ajout_Commande('numéro_semaine_max_chauffage','Numéro de semaine maximum pour activer le chauffage','info','numeric',1,52,18,'');
    }

    if ($eqLogic->getConfiguration('Type_équipement','') == 'Chauffage'){
      $eqLogic->Ajout_Commande('confort','Confort','action','other');
      $eqLogic->Ajout_Commande('eco','Eco','action','other');
      $eqLogic->Ajout_Commande('hors_gel','Hors gel','action','other');
      $eqLogic->Ajout_Commande('arret','Arrêt','action','other');
    }

    if ($eqLogic->getConfiguration('Type_équipement','') == 'Volet'){			
      $eqLogic->Ajout_Commande('ouverture','Ouverture','action','other');			
      $eqLogic->Ajout_Commande('my','My','action','other');
      $eqLogic->Ajout_Commande('fermeture','Fermeture','action','other');
    }
    if ($eqLogic->getConfiguration('Type_équipement','') == 'Prise'){
      $eqLogic->Ajout_Commande('on','On','action','other');
      $eqLogic->Ajout_Commande('off','Off','action','other');

    }

    $planifications=[];
    planification::add_log("info","postSave",$eqLogic); 

    if (!isset($equipementsInfos[$eqLogic_id])) {
      $equipementsInfos[$eqLogic_id] = new EquipementInfo1($eqLogic_id); 
      $Json=$eqLogic::Get_Json($eqLogic_id);
      if (isset($Json["Planifications"])){
        $equipementsInfos[$eqLogic_id]->planifications = $Json["Planifications"][0]; 
      }
      
      
      if (isset($Json["Paramètres"])){
        $equipementsInfos[$eqLogic_id]->Automatisation_Paramètres = $Json["Paramètres"][0];
      }        
      if (isset($json["Ouvrants"])){
        $equipementsInfos[$eqLogic_id]->Automatisation_Ouvrants = $Json["Ouvrants"][0];
      } 
      if (isset($Json["Gestion_planifications"])){
        $equipementsInfos[$eqLogic_id]->Automatisation_Gestion_planifications =$Json["Gestion_planifications"][0];
      }
    }

    //cmd::byEqLogicIdAndLogicalId($eqLogic_id,"heure_fin")->event('');
    //cmd::byEqLogicIdAndLogicalId($eqLogic_id,"action_suivante")->event('');
    $planifications=$eqLogic::Get_planifications(true,$equipementsInfos[$eqLogic_id]);
    if($planifications=="" ||$planifications == []){
      $arr["select"]="";
      $arr["Id_planification"]="";
      $arr["planifications"] = $planifications;
      $cmd_set_planification = $eqLogic->getCmd(null, "set_planification");
      $cmd_set_planification->setConfiguration("listValue","");
      $cmd_set_planification->save();
      $cmd_set_planification->execute($arr);
      return;
    }
    $arr=[];
    if($planifications != []){
      switch (count($planifications)) {
        case 0:
          $arr["select"]="";
          $arr["Id_planification"]="";
          $arr["planifications"] = "";
          return;
          break;
        case 1:
          $arr["select"]=$planifications[0][0]["Nom"];
          $arr["Id_planification"]=$planifications[0][0]["Id"];
          break;
        default:
      }
    }
    $liste="";
    $Id_planification_en_cours=$eqLogic->getConfiguration("Id_planification_en_cours","");

    foreach($planifications as $planification){
      if($planification[0]["Id"]==$Id_planification_en_cours){							
        $arr["select"]=$planification[0]["Nom"];
        $arr["Id_planification"]=$planification[0]["Id"];
      }
      if($liste==""){
        $liste .=$planification[0]["Nom"] ."|" . $planification[0]["Nom"];
      }else{
        $liste .= ";" .$planification[0]["Nom"] ."|" . $planification[0]["Nom"];
      }
    }
    $cmd_set_planification = $eqLogic->getCmd(null, "set_planification");
    $cmd_set_planification->setConfiguration("listValue",$liste);
    $cmd_set_planification->save();
    $set_new_planification=false;

    if(count($planifications) == 1 && $Id_planification_en_cours != $arr["Id_planification"]){
      $set_new_planification = true;
    }
    $planification_en_cours = "";
    $planification_en_cours=cmd::byEqLogicIdAndLogicalId($eqLogic_id,'planification_en_cours')->execCmd();


    if( $planification_en_cours == ""){
      $set_new_planification = true;
    }
    if(!isset($arr["select"]) && count($planifications[0])!=0){
      $arr["select"]=$planifications[0][0]["Nom"];
      $set_new_planification = true;
    }
    if(!isset($arr["Id_planification"]) && count($planifications[0])!=0){
      $arr["Id_planification"]=$planifications[0][0]["Id"];
      $set_new_planification = true;
    }

    if($set_new_planification){  
      $arr["planifications"] = $planifications;
      $cmd_set_planification->execute($arr);
    }
   
    $cmds = $eqLogic->getCmd( 'info', null, null,true) ;
    foreach ($cmds as $cmd){
      if($cmd->getValue() != $cmd->execCmd()){
        $cmd->set_value($cmd->getValue());
      }
    }
      
   
  }
  function replace_into_html($eqLogic,&$erreur,&$liste_erreur,&$replace,$parametre,$commande,$type,$convert_html){



    if (is_object($commande)){
      switch ($type) {
        case ("value"):
          $valeur = $commande->execCmd();

          if(strlen($valeur) != 0){
            if ($convert_html){
              $replace[$parametre] = htmlentities($valeur,ENT_QUOTES);
            }else{
              $replace[$parametre] = $valeur;
            }

          }else{

            $replace[$parametre] = "";
          }

          break;
        case ("name"):
          $replace[$parametre] = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$commande->execCmd())->getName();
          break;
        case ("id"):
          $replace[$parametre] = $commande->getId();
          break;
        case ("max"):
          $replace[$parametre] = $commande->getConfiguration("maxValue",30);
          break;
        case ("min"):
          $replace[$parametre] = $commande->getConfiguration("minValue",7);
          break;
      }

    }else{
      $replace[$parametre] = $commande;
    }
  }

  function replaceCmds($eqLogic,$cache_array, &$replace, &$erreur, &$liste_erreur, $cmds) {

    foreach ($cmds as $parametre => $cmd_name) {

      $type = "value";
      if (strpos($parametre, '_id') !== false){
        $type  = 'id';
      }
      if (strpos($parametre, '_value') !== false){
        $type = "value";
        $parametre = str_replace("_value", "", $parametre);
      }
      if (strpos($parametre, '_min') !== false){
        $type = "min";
      }
      if (strpos($parametre, '_max') !== false){
        $type = "max";
      }

      if(isset($cache_array[$cmd_name]) && $type == 'value'){
        if($cmd_name == 'heure_fin'){
          $interval = date_diff( new DateTime($cache_array[$cmd_name]), new DateTime("now"));
          if(intval($interval->format('%a')) == 0){
            if($cache_array[$cmd_name] == ''){
              $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, '', 'value', true);
            }else{
              if($eqLogic->getConfiguration("affichage_heure",false)){
                $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, date('H:i',strtotime($cache_array[$cmd_name])), 'value', true);
              }else{
                $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, date('d/m/Y H:i',strtotime($cache_array[$cmd_name])), 'value', true);
              }              
            }              
          }else{
            $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, date('d/m/Y H:i',strtotime($cache_array[$cmd_name])), 'value', true);
          }
        }else if ($cmd_name == 'consigne_temperature'){
          $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd_name), 'value', true);
        }else{
          $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, $cache_array[$cmd_name], 'value', true);
        }     


      }else if(isset($cache_array['cmds_id']) && $type == 'id'){

        $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre,  $cache_array['cmds_id'][$cmd_name], 'value', true);
      }else if($parametre == '#nom_eqlogic#'){
        $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, $eqLogic::getHumanName(false), $type, true);
      }else if($type == 'id'){
        $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd_name), $type, false);
      }else{ 
        if($cmd_name == 'heure_fin'){
          $heure_fin = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd_name)->execCmd();
          $interval = date_diff( new DateTime($heure_fin), new DateTime("now"));
          if(intval($interval->format('%a')) == 0){
            if($heure_fin == ''){
              $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, '', 'value', true);
            }else{
              if($eqLogic->getConfiguration("affichage_heure",false)){
                $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, date('H:i',strtotime($heure_fin)), 'value', true);

              }else{
                $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, date('d/m/Y H:i',strtotime($heure_fin)), 'value', true);
              }              
            }              
          }else{
            $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, date('d/m/Y H:i',strtotime($heure_fin)), 'value', true);
          }
         
        }else{
          if (cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd_name) != null){
            $eqLogic::replace_into_html($eqLogic, $erreur, $liste_erreur, $replace, $parametre, cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd_name), $type, false);
           
          }

        }
      }

    }
  }
  function toHTML($_version = 'dashboard') { 

    try {

      $eqLogic=$this;
      $eqLogic_id=$eqLogic->getId();
      $replace = $eqLogic->preToHtml($_version);
      if (!is_array($replace)) {return $replace; }
      //planification::add_log("info","tohtml",$eqLogic);
      // planification::add_log("info", $eqLogic->getConfiguration("Type_équipement",""),$eqLogic);

      //$eqLogic = document.querySelector('.eqLogic[data-eqLogic_id="' + _params[i].eqLogic_id + '"]')
      //planification::add_log("info","debug_backtrace: " . json_encode(debug_backtrace(false, 2)),$eqLogic);
      //$in_use = planHeader::searchByUse('eqLogic', $this->getId());

      //planification::add_log("info",$in_use,$eqLogic);

     
      $cmds=$eqLogic->getCmd();
      foreach($cmds as $cmd){
        if($cmd->getType() == 'info'){
          $cache_array[$cmd->getLogicalId()] = $cmd->execCmd();
        }
        $cache_array["cmds_id"][str_replace(" " , "_", $cmd->getLogicalId())] = $cmd->getId();
      }

      $version_alias=jeedom::versionAlias($_version);
      //echo $version_alias;
      if ($eqLogic->getDisplay('hideOn' . $version_alias) == 1) { return ''; }
      $erreur=false;
      $liste_erreur=[];




      $replace['#premier_affichage#'] = true;
      $replaceKeys = [
        '#nom_eqlogic#' => 'nom_eqlogic',
        '#mode_id#' => 'mode_fonctionnement',
        '#mode_value#' => 'mode_fonctionnement',
        '#planification_en_cours_id#' => 'planification_en_cours',
        '#planification_en_cours_value#' => 'planification_en_cours',
        '#mode_planification_value#' => 'mode_planification',
        '#auto_id#' => 'auto',
        '#action_suivante_id#' => 'action_suivante',
        '#action_suivante_value#' => 'action_suivante',
        '#action_en_cours_value#' => 'action_en_cours',
        '#action_en_cours_id#' => 'action_en_cours',
        '#set_heure_fin_id#' => 'set_heure_fin',
        '#heure_fin_id#' => 'heure_fin',
        '#heure_fin_value#' => 'heure_fin',
        '#set_planification_id#' => 'set_planification'
        
      ];
      $eqLogic::replaceCmds($eqLogic, $cache_array, $replace, $erreur, $liste_erreur, $replaceKeys);


      $replace['#affichage_heure#'] = $eqLogic->getConfiguration("affichage_heure",false);
      $replace['#type_equipement#'] = $eqLogic->getConfiguration("Type_équipement","");
      if (isset($cache_array['planification_en_cours'])) {
        $Json = $eqLogic::Get_Json($eqLogic_id);
        $planifications = $Json["Planifications"][0];
        $id_planification_en_cours = $eqLogic->getConfiguration("Id_planification_en_cours", "");

        $calendar_selector = '';

        foreach ($planifications as $planification) {
          $selected = ($planification[0]["Id"] === $id_planification_en_cours) ? ' selected' : '';
          $calendar_selector .= sprintf(
            '<option id="%s" value="%s"%s>%s</option>',
            $planification[0]["Id"],
            $planification[0]["Nom"],
            $selected,
            $planification[0]["Nom"]
          );
        }

        $replace['#calendar_selector#'] = $calendar_selector;
      }

      if ($eqLogic->getConfiguration("Type_équipement","")== "PAC"){
        $eqLogic::replaceCmds($eqLogic,$cache_array, $replace, $erreur, $liste_erreur, [
          '#arret_id#' => 'arret',
          '#boost_on_id#' => 'boost_on',
          '#boost_off_id#' => 'boost_off',
          '#boost_id#' => 'boost',
          '#boost_value#' => 'boost',
          '#climatisation_id#' => 'climatisation',
          '#ventilation_id#' => 'ventilation',
          '#chauffage_id#' => 'chauffage',
          '#set_consigne_temperature_id#' => 'set_consigne_temperature',
          '#consigne_min#' => 'consigne_temperature',
          '#consigne_max#' => 'consigne_temperature',
          '#consigne_temperature_value#' => 'consigne_temperature',
          '#consigne_temperature_id#' => 'consigne_temperature'

        ]);

        $cmd_temperature=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('Temperature_ambiante_id',"")));
        if (is_object($cmd_temperature)){
          $replace['#temperature_ambiante#'] = $cmd_temperature->execCmd();
          $replace['#temperature_ambiante_id#'] = $cmd_temperature->getId();
        }else{
          $replace['#temperature_ambiante#'] = "";
          $replace['#temperature_ambiante_id#']="";
        }        
      }
      if ($eqLogic->getConfiguration("Type_équipement","") == "Thermostat"){
        $eqLogic::replaceCmds($eqLogic,$cache_array, $replace, $erreur, $liste_erreur, [
          '#arret_id#' => 'arret',
          '#pourcentage_ouverture_id#' => 'pourcentage_ouverture',
          '#pourcentage_ouverture_value#' => 'pourcentage_ouverture',
          '#boost_value#' => 'boost',
          '#chauffage_id#' => 'chauffage',
          '#set_consigne_temperature_id#' => 'set_consigne_temperature',
          '#consigne_min#' => 'consigne_temperature',
          '#consigne_max#' => 'consigne_temperature',
          '#consigne_temperature_value#' => 'consigne_temperature',
          '#consigne_temperature_id#' => 'consigne_temperature'

        ]);

        $cmd_temperature=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('Temperature_ambiante_id',"")));
        if (is_object($cmd_temperature)){
          $replace['#temperature_ambiante#'] = $cmd_temperature->execCmd();
          $replace['#temperature_ambiante_id#'] = $cmd_temperature->getId();
        }else{
          $replace['#temperature_ambiante#'] = "";
          $replace['#temperature_ambiante_id#']="";
        }        
      }



      if ($eqLogic->getConfiguration("Type_équipement","")== "Volet"){
        $eqLogic::replaceCmds($eqLogic,$cache_array, $replace, $erreur, $liste_erreur, [
          '#ouvrir_id#' => 'ouverture',
          '#fermer_id#' => 'fermeture',
          '#my_id#' => 'my'
        ]);
        $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#type_fenêtre#',$eqLogic->getConfiguration('Type_fenêtre',""),"value",false);
        $cmd_My=$eqLogic->getCmd(null, 'my');
        $commande=$cmd_My->getConfiguration("commande","");
        if($cmd_My->getConfiguration("commande","") != ""){
          $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#show_my#',1,"value",false); 
        }      

        $cmd_Etat=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('etat_id',"")));
        if (is_object($cmd_Etat)){
          $etat_volet = $cmd_Etat->ExecCmd();
          $alias_ouverture=strtolower($eqLogic->getConfiguration('Alias_Ouvert',""));
          $alias_fermeture=strtolower($eqLogic->getConfiguration('Alias_Ferme',""));
          $alias_my=strtolower($eqLogic->getConfiguration('Alias_My',""));
          if(strtolower($etat_volet) == $alias_ouverture){ $cmd_array['action_en_cours'] = "ouverture";}
          if(strtolower($etat_volet) == $alias_fermeture){$cmd_array['action_en_cours'] = "fermeture";}
          if(strtolower($etat_volet) == $alias_my){$cmd_array['action_en_cours'] = "my" ;}
          $eqLogic::replace_into_html($eqLogic,$erreur,$liste_erreur,$replace,'#etat_id#',$cmd_Etat,"id",false);
          $eqLogic::replace_into_html($eqLogic,$erreur,$liste_erreur,$replace,'#etat#',$cmd_Etat,"value",false);		
        }
        $cmd_niveau_batterie_gauche=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('Niveau_batterie_gauche_id',"")));
        if (is_object($cmd_niveau_batterie_gauche)){
          $eqLogic::replace_into_html($eqLogic,$erreur,$liste_erreur,$replace,'#niveau_batterie_gauche_id#',$cmd_niveau_batterie_gauche,"id",false);
          $eqLogic::replace_into_html($eqLogic,$erreur,$liste_erreur,$replace,'#niveau_batterie_gauche#',$cmd_niveau_batterie_gauche,"value",false);		
        }

        $cmd_niveau_batterie_groite=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('niveau_batterie_droite_id',"")));
        if (is_object($cmd_niveau_batterie_groite)){
          $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#niveau_batterie_droite_id#',$cmd_niveau_batterie_droite,"id",false);
          $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#niveau_batterie_droite#',$cmd_niveau_batterie_droite,"value",false);		
        }
        $sens_ouverture_fenêtre='';
        $cmd_Etat_fenêtre_gauche=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('Etat_fenêtre_gauche_id',"")));
        if (is_object($cmd_Etat_fenêtre_gauche)){
          $sens_ouverture_fenêtre='gauche';
          $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#ouverture_fenêtre_gauche_id#',$cmd_Etat_fenêtre_gauche,"id",false);
          if($cmd_Etat_fenêtre_gauche->execCmd() == 1){
            $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#ouverture_fenêtre_gauche#',1,"value",false);

          }else{
            $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#ouverture_fenêtre_gauche#',0,"value",false);
          }
        }
        $cmd_Etat_fenêtre_droite=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('Etat_fenêtre_droite_id',"")));
        if (is_object($cmd_Etat_fenêtre_droite)){
          $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#ouverture_fenêtre_droite_id#',$cmd_Etat_fenêtre_droite,"id",false);
          if($sens_ouverture_fenêtre !=''){
            $sens_ouverture_fenêtre =  "gauche-droite";
          }else{
            $sens_ouverture_fenêtre = 'droite';
          }    
          if($cmd_Etat_fenêtre_droite->execCmd() == 1){
            $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#ouverture_fenêtre_droite#',1,"value",false);

          }else{
            $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#ouverture_fenêtre_droite#',0,"value",false);
          }     

        }
        $eqLogic::replace_into_html($eqLogic, $erreur,$liste_erreur,$replace,'#sens_ouverture_fenêtre#',$sens_ouverture_fenêtre,"value",false);

      }
      if ($eqLogic->getConfiguration("Type_équipement","")== "Chauffage"){
        $eqLogic::replaceCmds($eqLogic,$cache_array, $replace, $erreur, $liste_erreur, [
          '#confort_id#' => 'confort',
          '#eco_id#' => 'eco',
          '#hg_id#' => 'hors_gel',
          '#arret_id#' => 'arret'
        ]);
      }
      if ($eqLogic->getConfiguration("Type_équipement","")== "Prise"){
        $eqLogic::replaceCmds($eqLogic,$cache_array, $replace, $erreur, $liste_erreur, [
          '#on_id#' => 'on',
          '#off_id#' => 'off'
        ]);
        $cmd_Mode_fonctionnement=$eqLogic->getCmd(null, 'mode_fonctionnement');


          $Mode_fonctionnement=$cmd_Mode_fonctionnement->execCmd();
          $mode="manu";
          if ($Mode_fonctionnement == "Auto"){
            $mode="auto";
          }
          $cmd_Etat=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('etat_id',"")));
          if (is_object($cmd_Etat)){
            $etat=$cmd_Etat->execCmd();
            $alias_on=strtolower($eqLogic->getConfiguration('Alias_On',""));
            $alias_off=strtolower($eqLogic->getConfiguration('Alias_Off',""));
            if(strtolower($etat) == $alias_on){$etat = "on";}
            if(strtolower($etat) == $alias_off){$etat ="off";}
            if(strtolower($etat) == "on"){
              $image="on_". $mode .".png";
            }
            if(strtolower($etat) == "off"){
              $image="off_". $mode .".png";

            }

          }else{
            $image="off_". $mode .".png";
            $cmd_action_en_cours=$eqLogic->getCmd(null, 'action_en_cours');
            if (is_object($cmd_action_en_cours)){
              $action_en_cours=$cmd_action_en_cours->execCmd();
              switch (strtolower($action_en_cours)) {
                case "on":
                  $image="on_". $mode .".png";

                  break;				
                case "off";
                  $image="off_". $mode .".png";
                  break;
              }
            }
          }


        $replace['#img#'] = $image;
      }


      if ($erreur){
        $replace['#display_erreur#'] ="block";
        planification::add_log("debug",'Erreur: '. implode("//",$liste_erreur),$eqLogic);

      }else{
        $replace['#display_erreur#'] ="none";
      }	
      $html = template_replace($replace, getTemplate('planification', $_version, $eqLogic->getConfiguration("Type_équipement",""), 'planification'));


    } catch (Exception $e) {
      planification::add_log("error",'Erreur lors de la création du widget Détails : '. $e->getMessage(),$eqLogic);
    }


    return $html;




  }

  function postRemove() {
  }
  function preRemove() {
    $eqLogic=$this;
    $nom_fichier = dirname(__FILE__)."/../../planifications/" . $eqLogic->getId() . ".json";
    if (file_exists($nom_fichier)) {
      unlink($nom_fichier);
    }
    $nom_eq= planification::supp_accents(str_replace(" " , "_",str_replace("[" , "_",str_replace("]" , "",$eqLogic->getHumanName(false)))));
    log::remove('planification'.$nom_eq); 
  }

}
class planificationCmd extends cmd {
  public static $_widgetPossibility = array('custom' => false);

  public function dontRemoveCmd() {
    return true;
  }
  public function set_value($value){

    $eqLogic = $this->getEqLogic();
    $logical_id = $this->getLogicalId();
    $cmd_id= $this->getId();
    planification::add_log("debug",$logical_id . ":" . $value,$eqLogic);
    //if(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$logical_id)->execCmd() != $value){
    cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$logical_id)->setValue($value)->save();
    cmd::byId($cmd_id)->event($value);
    //}

    //$eqLogic->checkAndUpdateCmd($logical_id, $value);
  }
  function execute($_options = array()) {
    $cmd=$this;
    $eqLogic = $cmd->getEqLogic();
    $eqLogic_id = $eqLogic->getId();
    planification::add_log("info", $cmd->getName() . "(" . $cmd->getLogicalId() .  ")" ,$eqLogic);
    switch ($cmd->getLogicalId()) {
        /*case 'refresh':

          $mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->execCmd();
          if ($mode == ''){	
            planification::add_log("info","récup valeur sans cache",$eqLogic);
            $eqLogic->Recup_valeur_sans_cache();
          }
          $mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->execCmd();
          if ($mode != 'Manuel'){		
            if(isset($_options["planifications"])){


              //$eqLogic->Execute_action_actuelle($_options["Planifications"]);
              //$eqLogic->Recup_action_suivante($_options["Planifications"]);
              //$eqLogic->Execute_action_actuelle();
              // $eqLogic->Recup_action_suivante();
              // $eqLogic->Get_actions_planification();
            }else{
              //planification::add_log("info", "_options: Planifications n'existe pas",$eqLogic);

              // $eqLogic->Get_actions_planification();
            }


          }
          break;*/
      case 'set_heure_fin':
        if (strtotime("now") > strtotime($_options['message'])){
          $_options['message'] =date('d-m-Y H:i', strtotime("now") + 60);
        }
        cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin')->set_value(date('d-m-Y H:i',strtotime($_options['message'])));


        break;
      case 'set_planification':
        $planification_actuelle = $eqLogic->getCmd('info','planification_en_cours')->execCmd();

        if (!isset($_options["select"])){
          return;
        }
        if ($_options["select"] == $planification_actuelle &&  !isset($_options["user_login"])){
          return;
        }

        if(!isset($_options["mode"])){
          planification::add_log("info","Passage planification en Manuel" ,$eqLogic);
          $eqLogic->getCmd(null, "mode_planification")->set_value("Manuel");
        }

        $Json=$eqLogic::Get_Json($eqLogic_id);
        $planifications = $Json["Planifications"][0]; 

        if($planifications == "" || $planifications == []){
          planification::add_log("debug","pas de planification",$eqLogic);
          cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'planification_en_cours')->set_value('');
         

          break;
        }

    
        $planification_id='';
        $planification_nom='';
        $planification_actuelle=$eqLogic->getCmd('info','planification_en_cours')->execCmd();

        foreach($planifications as $planification){
          $target = $planification[0];
          if ($_options["select"] === $target["Nom"]) {
              $mode = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), 'mode_fonctionnement')->execCmd();
              if ($mode != 'Manuel') {
                  foreach (['action_en_cours', 'action_suivante', 'heure_fin'] as $cmdName) {
                      cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), $cmdName)->set_value("");
                  }
              }
              $planification_id = $target["Id"];
              $planification_nom = $target["Nom"];
          }                   
        }
    
       	

        if ($_options["select"] ==""){
          cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'planification_en_cours')->set_value("");
          $eqLogic->setConfiguration("Id_planification_en_cours","");
          return;
        }

        if( $planification_nom != '' &&  $planification_id != '' ){
          planification::add_log("debug","Mise à jour planification: ". $planification_actuelle ." =>". $planification_nom . "(" . $planification_id . ")",$eqLogic);	
          cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'planification_en_cours')->set_value($planification_nom);
          cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin')->set_value("");
          cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_suivante')->set_value("");
          $eqLogic->setConfiguration("Id_planification_en_cours",$planification_id);
          $eqLogic->save(true);

          //cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh')->execute($arr);
          return;
        }
        // if(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->execCmd() == "Auto"){
        // cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'auto')->execCmd();

        // }
        break;
      
      case 'auto':
        cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->set_value("Auto");
        cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_planification')->set_value("Auto");
        cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin')->set_value("");
        cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->set_value("");
        cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_suivante')->set_value("");
        break;
      default:
        switch ($eqLogic->getConfiguration("Type_équipement","")) {
          case "PAC":
            switch ($cmd->getLogicalId()) {
              case 'arret':
              case 'chauffage':
              case 'chauffage ECO':
              case 'climatisation':
              case 'ventilation':

                
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->set_value(ucwords($cmd->getName()));
                if(!isset($_options["mode"])){
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);

                  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->set_value("Manuel");
                  $eqLogic->getCmd(null, "heure_fin")->set_value("");
                  $eqLogic->getCmd(null, "action_suivante")->set_value("");
                  $eqLogic->getCmd(null, "set_planification")->execCmd();

                }
                $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                break;
              case 'boost_on':
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'boost')->set_value(1);
                if(isset($_options["mode"])){                  
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->set_value("Manuel");
                  $eqLogic->getCmd(null, "heure_fin")->set_value("");
                  $eqLogic->getCmd(null, "action_suivante")->set_value("");

                }
                $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());                 
                planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));

                break;

              case 'boost_off':
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'boost')->set_value(0);
                if(isset($_options["mode"])){
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);                  
                  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->set_value("Manuel");
                  $eqLogic->getCmd(null, "heure_fin")->set_value("");
                  $eqLogic->getCmd(null, "action_suivante")->set_value("");                  
                }
                $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                break;

              case 'set_consigne_temperature':
                //planification::add_log("debug","nouvelle consigne: " . $_options["slider"],$eqLogic);
                $cmd_action_en_cours=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours');
                $cmd_action_suivante=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_suivante');
                if ($cmd_action_en_cours->execCmd() == "Chauffage" || $cmd_action_en_cours->execCmd() == "Chauffage ECO"){
                  planification::add_log("info",'set_consigne_temperature');
                  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature_chauffage')->set_value($_options["slider"]);         
                }else if ($cmd_action_en_cours->execCmd() == "Climatisation"){
                  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature_climatisation')->set_value($_options["slider"]);
                }
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature')->set_value($_options["slider"]);
                break;
              default:
            }
            break;
          case "Thermostat":
            switch ($cmd->getLogicalId()) {
              case 'arret':
              case 'chauffage':
              case 'chauffage ECO':
                         
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->set_value(ucwords($cmd->getName()));
                if(!isset($_options["mode"])){
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);

                  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->set_value("Manuel");
                  $eqLogic->getCmd(null, "heure_fin")->set_value("");
                  $eqLogic->getCmd(null, "action_suivante")->set_value("");
                  $eqLogic->getCmd(null, "set_planification")->execCmd();

                }
                $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                break;            

              case 'set_consigne_temperature':
                //planification::add_log("debug","nouvelle consigne: " . $_options["slider"],$eqLogic);
                $cmd_action_en_cours=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours');
                $cmd_action_suivante=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_suivante');
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature')->set_value($_options["slider"]);
                break;
              default:
            }
            break;
          case "Chauffage":
            switch ($cmd->getLogicalId()) {
              case 'arret':
              case 'confort':
              case 'hors_gel':
              case 'eco':
              case 'stop':
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->set_value(ucwords($cmd->getName()));
                if(!isset($_options["mode"])){
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->set_value("Manuel");
                  $eqLogic->getCmd(null, "heure_fin")->set_value("");
                  $eqLogic->getCmd(null, "action_suivante")->set_value("");

                }
                $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                break;
                break;
              default:
            }
            break;
          case "Volet":
            switch ($cmd->getLogicalId()) {
              case 'ouverture':
              case 'fermeture':
              case 'my':
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->set_value(ucwords($cmd->getName()));
                if(!isset($_options["mode"])){
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->set_value("Manuel");
                  $eqLogic->getCmd(null, "heure_fin")->set_value("");
                  $eqLogic->getCmd(null, "action_suivante")->set_value("");

                }
                $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
               

                break;
              default:
            }
            break;
          case "Prise":
            switch ($cmd->getLogicalId()) {
              case 'on':
              case 'off':
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours')->set_value(ucwords($cmd->getName()));
                
                if(!isset($_options["mode"])){
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                  cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement')->set_value("Manuel");
                  $eqLogic->getCmd(null, "heure_fin")->set_value("");
                  $eqLogic->getCmd(null, "action_suivante")->set_value("");

                }
                $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                planification::add_log("info",$cmd ,$eqLogic);
                planification::add_log("info",$eqLogic_cmd->getConfiguration("commande","") ,$eqLogic);
                planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                   

                break;
              default:
            }
            break;
          case "Perso":

        }
        break;

    }

  }
}
?>