<?php
  require_once  '/var/www/html/core/php/core.inc.php';
class planification extends eqLogic {

  public static $_widgetPossibility = array('custom' => true);
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

  function execute_action($eqLogic,$eqLogic_cmd,$cmd){//ok pour démon
    if(is_object($eqLogic_cmd)){
      if (is_numeric (trim($cmd, "#"))){
        $cmd=cmd::byId(trim($cmd, "#"));
        if(is_object($cmd)){
          $eqLogic_cmd=eqLogic::byId($cmd->getEqLogic_id()) ;

          if($eqLogic_cmd->getObject()==""){
            $Object="Aucun";
          }else{
            $Object=$eqLogic_cmd->getObject()->getName();
          }

          planification::add_log("debug",'execution action: #[' . $Object."][".$eqLogic_cmd->getName()."][".$cmd->getName()."]#",$eqLogic);
          $cmd->execCmd();
        }
      }else if ($cmd !=""){
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
        }

        planification::add_log("debug",'execution action: ' . $cmd . ":" .$options_str,$eqLogic);		
        scenarioExpression::createAndExec('action', $cmd, $options);
      }
    }

  }
  function Recup_liste_commandes_planification($eqLogic_id) {
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
  function add_log($level = 'debug',$Log,$eqLogic=null){
    if (is_array($Log)) $Log = json_encode($Log);
    if(count(debug_backtrace(false, 2)) == 1){
      $function_name = debug_backtrace(false, 2)[0]['function'];
      $ligne = debug_backtrace(false, 2)[0]['line'];
    }else{
      $function_name = debug_backtrace(false, 2)[1]['function'];
      $ligne = debug_backtrace(false, 2)[0]['line'];
    }
    $msg =  $function_name .' (' . $ligne . '): '.$Log;
    $UseLogByeqLogic=config::byKey('UseLogByeqLogic', 'planification');
    if( $eqLogic!=null){
       if($UseLogByeqLogic){
        $nom_eq= planification::supp_accents(str_replace(" " , "_",str_replace("[" , "_",str_replace("]" , "",$eqLogic->getHumanName(false)))));
     
        log::add('planification'.$nom_eq  , $level,$msg);
      }else{
        $nom_eq= planification::supp_accents($eqLogic->getHumanName(false));
     
        log::add('planification'  ,  $level,$nom_eq . ": " .$msg);
      }
    }else{
      log::add('planification'  , $level,$msg);
    }


  }

  function Recup_prochaine_action(){//OK POUR LE DEMON

    $eqLogic=$this;				
    $action_en_cours="";
    $infos_lever_coucher_soleil=planification::Recup_infos_lever_coucher_soleil($eqLogic->getId());	
    $cmd_action_en_cours=$eqLogic->getCmd(null, "action_en_cours");
    if(is_object($cmd_action_en_cours)){
      $action_en_cours=$cmd_action_en_cours->execCmd();
    }
    $maintenant=date_create_from_format ('Y-m-d H:i' ,date('Y-m-d H:i'));
    $numéro_jour=date('N');
    $Id_planification_en_cours=$eqLogic->getConfiguration("Id_planification_en_cours","");
    planification::add_log("debug","Id_planification_en_cours:". $Id_planification_en_cours,$eqLogic);
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
          //planification::add_log("debug","periodes:". implode( ",",$periode),$eqLogic);
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
        $nb1=0;
        foreach($periodes as $periode){
          $periode["Debut_periode"] = "0". $periode["Debut_periode"];
          $periode["Debut_periode"] = substr($periode["Debut_periode"],-5);
          $periodes[$nb1]=$periode;
          $nb1+=1;
        }

        array_multisort(array_column($periodes, 'Debut_periode'), SORT_ASC, $periodes);
        $trouve=false;
        foreach($periodes as $periode){
          //  planification::add_log("debug","periodes:". implode( ",",$periode),$eqLogic);
          $date=date_add(date_create_from_format ( 'Y-m-d H:i' ,date('Y-m-d '). $periode["Debut_periode"]), date_interval_create_from_date_string($i-1 .' days'));
          //$date=date_add(date_create_from_format ( 'Y-m-d H:i' ,date('Y-m-d '). $periode["Debut_periode"]), date_interval_create_from_date_string($i-1 .' days'));
          //	planification::add_log("debug", $date->format('d-m-Y H:i'),$eqLogic);
          //    planification::add_log("debug", $maintenant->format('d-m-Y H:i'),$eqLogic);	

          if($date->getTimestamp() > $maintenant->getTimestamp()){


            foreach ($CMD_LIST as $cmd) {
              if($periode["Id"]==$cmd["Id"] && strtolower($cmd["Nom"]) != strtolower($action_en_cours)){
                $action["datetime"]=$date->format('d-m-Y H:i');
                $action["nom"]=$cmd["Nom"];

                $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                if(is_object($cmd_action_suivante)){
                  $cmd_action_suivante->event($cmd["Nom"]);
                }
                $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                if(is_object($cmd_heure_fin)){
                  $cmd_heure_fin->event($date->format('d-m-Y H:i'));
                }
                // planification::add_log("debug","action suivante trouvée.",$eqLogic);
                $trouve=true;
                planification::add_log("debug","action suivante:" . $cmd["Nom"] . " à ".$date->format('H:i')  ,$eqLogic);
                //planification::add_log("debug","action :" . implode('|',$action),$eqLogic);
                return $action;
              }
            }
          }				
        }
      }
      $numéro_jour+=1;

    }
    if (!$trouve){
      planification::add_log("debug","Aucune action suivante trouvée.",$eqLogic);
      $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
      if(is_object($cmd_heure_fin)){
        $cmd_heure_fin->event('');
      }
      $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
      if(is_object($cmd_action_suivante)){
        $cmd_action_suivante->event('');
      }
    }
    return;
  }

  function Execute_action_actuelle(){//OK PROUR LE DEMON

    $mode_fonctionnement="Auto";
    $eqLogic=$this;
    planification::add_log("debug","Execute_action_actuelle",$eqLogic);
    $infos_lever_coucher_soleil=planification::Recup_infos_lever_coucher_soleil($eqLogic->getId());			
    $cmd_mode_fonctionnement = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), "mode_fonctionnement");
    if(is_object($cmd_mode_fonctionnement)){
      $mode_fonctionnement=$cmd_mode_fonctionnement->execCmd();	
    }
    //planification::add_log("debug","mode_fonctionnement: ".$mode_fonctionnement,$eqLogic);
    $action_en_cours="";
    $cmd_action_en_cours=$eqLogic->getCmd(null, "action_en_cours");
    if(is_object($cmd_action_en_cours)){
      $action_en_cours=$cmd_action_en_cours->execCmd();
    }
    //planification::add_log("debug","action_en_cours: ".$action_en_cours,$eqLogic);
    $Id_planification_en_cours=$eqLogic->getConfiguration("Id_planification_en_cours","");

    if($Id_planification_en_cours==""){
      planification::add_log("debug","Aucun Id de planification enregistré",$eqLogic);
      return;
    }

    if($mode_fonctionnement == "Auto"){
      $cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'heure_fin');
      $timestamp_prochaine_action=time();
      if (is_object($cmd)){
        $val=$cmd->execCmd();
        if (is_numeric($val)){
          $timestamp_prochaine_action=$val;
        }
      }
      $numéro_jour=date('N');


      $planifications=$eqLogic::Recup_planifications();
      $cette_planification=[];
      foreach($planifications as $planification){
        if($planification["Id"]==$Id_planification_en_cours){
          //planification::add_log("debug","planification en cours: " . $planification["nom_planification"],$eqLogic);
          $cette_planification=$planification["semaine"];
          break;
        }
      }
      if(count($cette_planification) == 0){
        planification::add_log("debug","Aucune planification enregistrée dans l'eqLogic-> fin de la fonction",$eqLogic);
        return;
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
              //planification::add_log("debug","periode:".implode("|",$periode),$eqLogic);
              $trouve=true;
              //$action["datetime"]=$date->format('d-m-Y H:i');
              $action["Id"]=$periode["Id"];
              $action["Nom"] = "";
              if (is_object($cmd->byId($periode["Id"]))){
                $action["Nom"]=$cmd->byId($periode["Id"])->getName();
              }

            }
          } 
        }
        if($trouve ){

          if($action["Nom"] == $action_en_cours){	
            planification::add_log("debug",'Action identique fin de la fonction.',$eqLogic);
            return;
          }
          if( is_numeric($action["Id"])){
            $eqLogic_cmd=$cmd->byId($action["Id"]);

          }
          planification::add_log("info","planification en cours: " . $planification["nom_planification"] ." (" . $Id_planification_en_cours . ") " . "mode_fonctionnement: ".$mode_fonctionnement . " " . "action_en_cours: ". $action_en_cours . " ",$eqLogic);
          planification::add_log("info",'Nom de l\'action actuelle: '.$eqLogic_cmd->getName(),$eqLogic);	
          if(is_object($eqLogic_cmd)){
            $eqLogic_cmd->execCmd(array('mode'=>"auto"));
            $cmd=$eqLogic_cmd->getConfiguration("commande","");
            planification::execute_action($eqLogic,$eqLogic_cmd,$cmd);
          }
          return;
        }		
        $numBoucle+=1;
      }
    }else if ($mode_fonctionnement != "Auto"){
      if($action_en_cours == "Absent"){
        $eqLogic_cmd=$eqLogic->getCmd(null,"arret");
      }else{
        $eqLogic_cmd=$eqLogic->getCmd(null,strtolower($action_en_cours));

      }
      if(is_object($eqLogic_cmd)){
        if($action_en_cours == $mode_fonctionnement || strtolower($action_en_cours) == $eqLogic_cmd->getLogicalId()){
          planification::add_log("debug",'Action identique fin de la fonction.',$eqLogic);
          return;
        }
        $cmd=$eqLogic_cmd->getConfiguration("commande","");
        planification::execute_action($eqLogic,$eqLogic_cmd,$cmd);

      }
    }

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

    if($eqLogic->getconfiguration("type","")=="Poele"){
      if($cmd->getLogicalId() == "allume"|| $cmd->getLogicalId()== "eteint"){
        $cmd->setConfiguration('Type',"Planification");
        if ($cmd->getConfiguration('Couleur',"")== ""){
          $cmd->setConfiguration('Couleur',"orange");
        }
        $cmd->save();
      }
    }
    if($eqLogic->getconfiguration("type","")=="PAC"){
      if($cmd->getLogicalId() == "arret" || $cmd->getLogicalId() == "climatisation" || $cmd->getLogicalId() == "ventilation" || $cmd->getLogicalId() == "chauffage" || $cmd->getLogicalId()== "chauffage ECO"){
        $cmd->setConfiguration('Type',"Planification");
        if ($cmd->getConfiguration('Couleur',"")== ""){
          $cmd->setConfiguration('Couleur',"orange");
        }
        $cmd->save();
      }
    }
    if($eqLogic->getconfiguration("type","")=="Volet"){
      if($cmd->getLogicalId() == "ouverture" || $cmd->getLogicalId() == "fermeture" || $cmd->getLogicalId() == "my"){
        $cmd->setConfiguration('Type',"Planification");
        if ($cmd->getConfiguration('Couleur',"")== ""){
          $cmd->setConfiguration('Couleur',"orange");
        }
        $cmd->save();
      }
    }
    if($eqLogic->getconfiguration("type","")=="Chauffage"){
      if($cmd->getLogicalId() == "eco" || $cmd->getLogicalId() == "confort" || $cmd->getLogicalId() == "arret"|| $cmd->getLogicalId() == "hors_gel"){
        $cmd->setConfiguration('Type',"Planification");
        if ($cmd->getConfiguration('Couleur',"")== ""){
          $cmd->setConfiguration('Couleur',"orange");
        }
        $cmd->save();
      }
    }
    if($eqLogic->getconfiguration("type","")=="Prise"){
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
    if($eqLogic->getConfiguration("numero_objet","vide") != "vide"){}
    $obj=jeeObject::byId($eqLogic->getConfiguration("numero_objet","NULL"));
    if (is_object($obj)){
      $nom_object=$obj->getName();	
    }else{
      $nom_object="Aucun";
    }
    $nouveau_fichier_log= "planification" . planification::supp_accents(str_replace(" " , "_",str_replace("[" , "_",str_replace("]" , "",$eqLogic->getHumanName(false)))));
    $nom_equipement=$eqLogic->getLogicalId();
    $ancien_fichier_log=planification::supp_accents("planification_".$nom_object."_".$nom_equipement);
    if(file_exists ( "/var/www/html/log/".$ancien_fichier_log )){
      if ("/var/www/html/log/".$ancien_fichier_log != "/var/www/html/log/".$nouveau_fichier_log){
        rename("/var/www/html/log/".$ancien_fichier_log, "/var/www/html/log/".$nouveau_fichier_log );
      }

    }
    $eqLogic->setConfiguration("numero_objet",$eqLogic->getObject_id());
    $eqLogic->setLogicalId(planification::supp_accents($eqLogic->getName()));
  }

  function postSave() {
    $eqLogic=$this;

    $retour=$eqLogic->getConfiguration("chemin_image","none");
    if($retour='none'){
      $eqLogic->setConfiguration("chemin_image","");
    }
    $eqLogic->Ajout_Commande('mode_fonctionnement','Mode fonctionnement','info','string',null,null,"Auto");
    $eqLogic->Ajout_Commande('heure_fin','Heure fin action en cours','info','string');
    $eqLogic->Ajout_Commande('action_en_cours','Action en cours','info','string');
    $eqLogic->Ajout_Commande('action_suivante','Action suivante','info','string');
    $eqLogic->Ajout_Commande('planification_en_cours','Planification en cours','info','string');
    $eqLogic->Ajout_Commande('refresh','Rafraichir','action','other');
    $eqLogic->Ajout_Commande('auto','Auto','action','other');
    $eqLogic->Ajout_Commande('set_heure_fin','Set heure fin','action','message');
    $eqLogic->Ajout_Commande('info','Info','info','string');
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

    if ($eqLogic->getConfiguration('type','') == 'Poele'){
      $eqLogic->Ajout_Commande('absent','Absent','action','message');
      $eqLogic->Ajout_Commande('force','Forcé','action','other');
      $eqLogic->Ajout_Commande('arret','Arrêt','action','other');
      $eqLogic->Ajout_Commande('allume','Allumé','action','other');
      $eqLogic->Ajout_Commande('eteint','Eteint','action','other');
      $eqLogic->Ajout_Commande('set_consigne_temperature','Set consigne température','action','slider',7,30);
      $eqLogic->Ajout_Commande('consigne_temperature','Consigne Temperature','info','numeric',null,null,20,'°C');
      if($eqLogic::getConfiguration("temperature_consigne_par_defaut") != ''){
          if($eqLogic::getConfiguration("temperature_consigne_par_defaut") > 30){
          $eqLogic->checkAndUpdateCmd('consigne_temperature',30);
          $eqLogic::setConfiguration("temperature_consigne_par_defaut",30);
          $eqLogic->save(true);
        }else if($eqLogic::getConfiguration("temperature_consigne_par_defaut") < 7){
          $eqLogic->checkAndUpdateCmd('consigne_temperature',7);
          $eqLogic::setConfiguration("temperature_consigne_par_defaut",7);
          $eqLogic->save(true);
        }else{
          $eqLogic->checkAndUpdateCmd('consigne_temperature',$eqLogic::getConfiguration("temperature_consigne_par_defaut"));
        }
       
      }

	   
																		   
    }

    if ($eqLogic->getConfiguration('type','') == 'PAC'){
      $eqLogic->Ajout_Commande('absent','Absent','action','message');
      $eqLogic->Ajout_Commande('climatisation','Climatisation','action','other');
      $eqLogic->Ajout_Commande('ventilation','Ventilation','action','other');
      $eqLogic->Ajout_Commande('chauffage','Chauffage','action','other');
      $eqLogic->Ajout_Commande('chauffage ECO','Chauffage ECO','action','other');
      $eqLogic->Ajout_Commande('arret','Arrêt','action','other');
      $eqLogic->Ajout_Commande('set_consigne_temperature','Set consigne température','action','slider',7,30);
      $eqLogic->Ajout_Commande('consigne_temperature','Consigne Temperature','info','numeric',null,null,20,'°C');
      $eqLogic->Ajout_Commande('consigne_temperature_chauffage','Consigne Temperature Chauffage','info','numeric',null,null,20,'°C');
      $eqLogic->Ajout_Commande('consigne_temperature_climatisation','Consigne Temperature Climatisation','info','numeric',null,null,20,'°C');
      $eqLogic->Ajout_Commande('boost','Boost','info','binary',null,null,0);
      $eqLogic->Ajout_Commande('boost_on','Boost On','action','other');
      $eqLogic->Ajout_Commande('boost_off','Boost Off','action','other');
    }

    if ($eqLogic->getConfiguration('type','') == 'Chauffage'){
      $eqLogic->Ajout_Commande('confort','Confort','action','other');
      $eqLogic->Ajout_Commande('eco','Eco','action','other');
      $eqLogic->Ajout_Commande('hors_gel','Hors gel','action','other');
      $eqLogic->Ajout_Commande('absent','Absent','action','other');
      $eqLogic->Ajout_Commande('arret','Arrêt','action','other');
    }

    if ($eqLogic->getConfiguration('type','') == 'Volet'){			
      $eqLogic->Ajout_Commande('ouverture','Ouverture','action','other');			
      $eqLogic->Ajout_Commande('my','My','action','other');
      $eqLogic->Ajout_Commande('fermeture','Fermeture','action','other');			
    }
    if ($eqLogic->getConfiguration('type','') == 'Prise'){
      $eqLogic->Ajout_Commande('on','On','action','other');
      $eqLogic->Ajout_Commande('off','Off','action','other');

    }

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
    $cmd_set_planification = $eqLogic->getCmd(null, "set_planification");
    $cmd_set_planification->setConfiguration("listValue",$liste);
    $cmd_set_planification->save();
    $set_new_planification=false;

    if(count($planifications) == 1 && $Id_planification_en_cours != $arr["Id_planification"]){
      $set_new_planification = true;
    }
    $cmd_planification_en_cours=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'planification_en_cours');
    $planification_en_cours = "";
    if (is_object($cmd_planification_en_cours)){
      $planification_en_cours = $cmd_planification_en_cours->execCmd();
    }
    if( $planification_en_cours == ""){
      $set_new_planification = true;
    }
    if(!isset($arr["select"]) && count($planifications)!=0){
      $arr["select"]=$planifications[0]["nom_planification"];
      $set_new_planification = true;
    }
    if(!isset($arr["Id_planification"]) && count($planifications)!=0){
      $arr["Id_planification"]=$planifications[0]["Id"];
      $set_new_planification = true;
    }

    if($set_new_planification){
      $cmd_set_planification->execute($arr);
    }

							
																	 
								
										 
															 
						  
		 
		
	  
	 
    if($eqLogic->getIsEnable() == 1){
      $cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
      if (is_object($cmd_refresh)){
        $cmd_refresh->execute();
      }
		  
																	 
								
										 
															 
						  
		 
		
    }


  }
  function replace_into_html(&$erreur,&$liste_erreur,&$replace,$parametre,$commande,$type,$convert_html){
    $eqLogic=$this;
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
      $replace['#type_equipement#']=$eqLogic->getConfiguration("type","");
      $replace['#calendar_selector#']='';

      if (is_object($eqLogic->getCmd(null, 'planification_en_cours'))){
        $liste_planifications = $eqLogic::Recup_planifications();
        $Id_planification_en_cours=$eqLogic->getConfiguration("Id_planification_en_cours","");


        foreach($liste_planifications as $planification){

          if($planification["Id"]==$Id_planification_en_cours){
            $valuecalendar = '"'.$planification["nom_planification"]. '" selected';
            $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#planification_en_cours#',$planification["nom_planification"],"value",true);

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

      $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#mode#',$eqLogic->getCmd(null, 'mode_fonctionnement'),"value",false);
      $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#refresh_id#',$eqLogic->getCmd(null, 'refresh'),"id",false);
      $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#auto_id#',$eqLogic->getCmd(null, 'auto'),"id",false);
      $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#action_en_cours#',$eqLogic->getCmd(null, 'action_en_cours'),"value",false);
      $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#prochaine_action#',$eqLogic->getCmd(null, 'action_suivante'),"value",false);
      $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#endtime_change_id#',$eqLogic->getCmd(null, 'set_heure_fin'),"id",false);
      $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#info_widget#',$eqLogic->getCmd(null, 'info'),"value",true);
      $cmd_heure_fin=$eqLogic->getCmd(null, 'heure_fin');
      if(is_object($cmd_heure_fin)){
        if($cmd_heure_fin->execCmd() != ""){

          if($eqLogic->getConfiguration("affichage_heure",false)){
            $heure_fin=strtotime($cmd_heure_fin->execCmd());
            $interval = date_diff( new DateTime($cmd_heure_fin->execCmd()), new DateTime("now"));
            if(intval($interval->format('%a')) ==0){
              $replace['#endtime#'] =date('H:i',$heure_fin);
            }else{
              $replace['#endtime#'] =date('d-m-Y H:i',$heure_fin);
            }
          }else{
            $heure_fin=strtotime($cmd_heure_fin->execCmd());
            if(date('d-m-Y',$heure_fin) != date('d-m-Y') ){
              $replace['#endtime#'] =date('d-m-Y H:i',$heure_fin);
            }else{
              $replace['#endtime#'] =date('H:i',$heure_fin);
            }
          }

          $replace['#datetimepicker#'] = date('Y/m/d H:i',$heure_fin);



        }else{
          $replace['#endtime#'] ="";
          $replace['#datetimepicker#'] = date('Y/m/d H:i');

        }
      }





      $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_planification_id#',$eqLogic->getCmd(null, 'set_planification'),"id",false);
     

      $page_active=$eqLogic->getCache('Page');
      if($page_active =="" || $page_active=="page1"){
        $replace['#page#']="page1";
      }else{
        $replace['#page#']="page2";
      }
      $replace['#type_eqlogic#'] = $eqLogic->getConfiguration("type","");
      if ($eqLogic->getConfiguration("type","")== "Poele"){
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#arret_id#',$eqLogic->getCmd(null, 'arret'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#consigne#',$eqLogic->getCmd(null, 'consigne_temperature'),"value",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#absent_id#',$eqLogic->getCmd(null, 'absent'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#chauffage_id#',$eqLogic->getCmd(null, 'force'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_id#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#consigne_min#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"min",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#consigne_max#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"max",false);


        $cmd_temperature=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('temperature_id',"")));
        if (is_object($cmd_temperature)){
          $replace['#temperature#'] = $cmd_temperature->execCmd();
          $replace['#temperature_id#'] = $cmd_temperature->getId();
        }else{
          $replace['#temperature#'] = "";
          $replace['#temperature_id#']="";
        }
        $image="Off.png";

        $cmd_Mode_fonctionnement=$eqLogic->getCmd(null, 'mode_fonctionnement');
        $Mode_fonctionnement=$cmd_Mode_fonctionnement->execCmd();
        //planification::add_log("debug","mode de fonctionnement" . $Mode_fonctionnement,$eqLogic);

        $cmd_Etat_Allume=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('etat_allume_id',"")));
        if (is_object($cmd_Etat_Allume)){
          if($cmd_Etat_Allume->execCmd() == 1){
            $image="On.png";
            $cmd_Etat_Boost=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('etat_boost_id',"")));
            if (is_object($cmd_Etat_Boost)){
              if($cmd_Etat_Boost->execCmd())
              {
                $image="OnBoost.png";
              }
            }
          }

        }else{
          $cmd_action_en_cours=$eqLogic->getCmd(null, 'action_en_cours');
          if (is_object($cmd_action_en_cours)){
            $action_en_cours=$cmd_action_en_cours->execCmd();
            switch (strtolower($action_en_cours)){
              case "allumé":
                $image="On.png";								
                break;	
              case "forcé";
                $image="On.png";
                break;			
              case "eteint";
                $image="Off.png";
                break;
            }
          }

        }



        $replace['#img#'] = $image;
        if ($erreur){
          $replace['#display_erreur#'] ="block";
        }else{
          $replace['#display_erreur#'] ="none";
        }	
        $html = template_replace($replace, getTemplate('core', $version, 'poele', 'planification'));
      }

      if ($eqLogic->getConfiguration("type","")== "PAC"){
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#arret_id#',$eqLogic->getCmd(null, 'arret'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#consigne#',$eqLogic->getCmd(null, 'consigne_temperature'),"value",false);

        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#boost_on_id#',$eqLogic->getCmd(null, 'boost_on'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#boost_off_id#',$eqLogic->getCmd(null, 'boost_off'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#boost#',$eqLogic->getCmd(null, 'boost'),"value",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#absent_id#',$eqLogic->getCmd(null, 'absent'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#climatisation_id#',$eqLogic->getCmd(null, 'climatisation'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#ventilation_id#',$eqLogic->getCmd(null, 'ventilation'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#chauffage_id#',$eqLogic->getCmd(null, 'chauffage'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#set_consigne_temperature_id#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"id",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#consigne_min#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"min",false);
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#consigne_max#',$eqLogic->getCmd(null, 'set_consigne_temperature'),"max",false);

        $cmd_temperature=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('temperature_id',"")));
        if (is_object($cmd_temperature)){
          $replace['#temperature#'] = $cmd_temperature->execCmd();
          $replace['#temperature_id#'] = $cmd_temperature->getId();
        }else{
          $replace['#temperature#'] = "";
          $replace['#temperature_id#']="";
        }

        $image="Arret.png";

        $cmd_action_en_cours=$eqLogic->getCmd(null, 'action_en_cours');
        if (is_object($cmd_action_en_cours)){
          $action_en_cours=$cmd_action_en_cours->execCmd();
        }
        switch (strtolower($action_en_cours)) {
          case "climatisation":
            $image="Climatisation.png";
            break;
          case "chauffage";
            $image="Chauffage.png";
            break;
          case "ventilation":
            $image="Ventilation.png";
            break;
          case "arrêt":
            $image="Arret.png";
            break;
        }



        $replace['#img#'] = $image;
        if ($erreur){
          $replace['#display_erreur#'] ="block";
        }else{
          $replace['#display_erreur#'] ="none";
        }
        $html = template_replace($replace, getTemplate('core', $version, 'PAC', 'planification'));
      }
      if ($eqLogic->getConfiguration("type","")== "Volet"){
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#ouvrir_id#',$eqLogic->getCmd(null, 'ouverture'),"id",false);		
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#fermer_id#',$eqLogic->getCmd(null, 'fermeture'),"id",false);		
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#my_id#',$eqLogic->getCmd(null, 'my'),"id",false);		

        $cmd_My=$eqLogic->getCmd(null, 'my');
        $commande=$cmd_My->getConfiguration("commande","");
        if($cmd_My->getConfiguration("commande","") == ""){
          $replace['#show_my#'] = "none";
        }else{
          $replace['#show_my#'] = "inline-block";
        }
        $cmd_Mode_fonctionnement=$eqLogic->getCmd(null, 'mode_fonctionnement');

        if (is_object($cmd_Mode_fonctionnement)){

          $Mode_fonctionnement=$cmd_Mode_fonctionnement->execCmd();
          //planification::add_log("debug","mode de fonctionnement" . $Mode_fonctionnement,$eqLogic);
          $mode="manu";
          if ($Mode_fonctionnement == "Auto"){
            $mode="auto";
          }

          $cmd_Etat=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('etat_id',"")));
          if (is_object($cmd_Etat)){
            $etat=$cmd_Etat->execCmd();
            $alias_ouverture=strtolower($eqLogic->getConfiguration('Alias_Ouvert',""));
            $alias_fermeture=strtolower($eqLogic->getConfiguration('Alias_Ferme',""));
            $alias_my=strtolower($eqLogic->getConfiguration('Alias_My',""));

            if(strtolower($etat) == $alias_ouverture){$etat = "ouvert";}
            if(strtolower($etat) == $alias_fermeture){$etat ="fermé";}
            if(strtolower($etat) == $alias_my){$etat = "my";}

            switch (strtolower($etat)) {
              case "ouverture":
              case "ouvert":
                $image="100-". $mode .".png";
                break;
              case "fermeture":
              case "fermé":
                $image="0-". $mode .".png";
                break;
              case "my":
                $image="50-". $mode .".png";
                break;

            }


          }else{
            $cmd_action_en_cours=$eqLogic->getCmd(null, 'action_en_cours');
            if (is_object($cmd_action_en_cours)){
              $action_en_cours=$cmd_action_en_cours->execCmd();

              switch (strtolower($action_en_cours)) {
                case "ouverture":
                  if ($Mode_fonctionnement == "Auto"){
                    $image="100-auto.png";
                  }else{
                    $image="100-manu.png";
                  }

                  break;				
                case "fermeture";
                  if ($Mode_fonctionnement == "Auto"){
                    $image="0-auto.png";
                  }else{
                    $image="0-manu.png";
                  }
                  break;
                case "my":
                  if ($Mode_fonctionnement == "Auto"){
                    $image="50-auto.png";
                  }else{
                    $image="50-manu.png";
                  }
                  break;

              }
            }
          }

        }
        $replace['#img#'] = $image;
        if ($erreur){
          $replace['#display_erreur#'] ="block";
        }else{
          $replace['#display_erreur#'] ="none";
        }	
        $html = template_replace($replace, getTemplate('core', $version, 'volet', 'planification'));
      }
      if ($eqLogic->getConfiguration("type","")== "Chauffage"){
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#confort_id#',$eqLogic->getCmd(null, 'confort'),"id",false);		
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#eco_id#',$eqLogic->getCmd(null, 'eco'),"id",false);		
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#hg_id#',$eqLogic->getCmd(null, 'hors_gel'),"id",false);	
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#arret_id#',$eqLogic->getCmd(null, 'arret'),"id",false);		
        $cmd_Mode_fonctionnement=$eqLogic->getCmd(null, 'mode_fonctionnement');

        if (is_object($cmd_Mode_fonctionnement)){

          $Mode_fonctionnement=$cmd_Mode_fonctionnement->execCmd();
          //planification::add_log("debug","mode de fonctionnement" . $Mode_fonctionnement,$eqLogic);
          $mode="manu";
          if ($Mode_fonctionnement == "Auto"){
            $mode="auto";
          }
          $cmd_Etat=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('etat_id',"")));

          if (is_object($cmd_Etat)){
            $etat=$cmd_Etat->execCmd();
            $alias_confort=strtolower($eqLogic->getConfiguration('Alias_Confort',""));
            $alias_eco=strtolower($eqLogic->getConfiguration('Alias_Eco',""));
            $alias_hg=strtolower($eqLogic->getConfiguration('Alias_Hg',""));
            $alias_arret=strtolower($eqLogic->getConfiguration('Alias_Arret',""));
            if(strtolower($etat) == $alias_confort){$etat = "confort";}
            if(strtolower($etat) == $alias_eco){$etat ="eco";}
            if(strtolower($etat) == $alias_hg){$etat = "hors gel";}
            if(strtolower($etat) == $alias_arret){$etat = "arrêt";}
            switch (strtolower($etat)) {
              case "confort":
                $image="confort-". $mode .".png";
                break;
              case "eco":
                $image="eco-". $mode .".png";
                break;
              case "hors gel":
                $image="hors_gel-". $mode .".png";
                break;
              case "arrêt":
                $image="arret-". $mode .".png";
                break;
            }


          }else{
            $cmd_action_en_cours=$eqLogic->getCmd(null, 'action_en_cours');
            if (is_object($cmd_action_en_cours)){
              $action_en_cours=$cmd_action_en_cours->execCmd();

              switch (strtolower($action_en_cours)) {
                case "confort":
                  $image="confort-". $mode .".png";
                  break;
                case "eco":
                  $image="eco-". $mode .".png";
                  break;
                case "hors gel":
                  $image="hors_gel-". $mode .".png";
                  break;
                case "arrêt":
                  $image="arret-". $mode .".png";
                  break;

              }
            }
          }

        }
        $replace['#img#'] = $image;
        if ($erreur){
          $replace['#display_erreur#'] ="block";
        }else{
          $replace['#display_erreur#'] ="none";
        }	
        $html = template_replace($replace, getTemplate('core', $version, 'chauffage', 'planification'));
      }
      if ($eqLogic->getConfiguration("type","")== "Prise"){
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#on_id#',$eqLogic->getCmd(null, 'on'),"id",false);		
        $eqLogic::replace_into_html($erreur,$liste_erreur,$replace,'#off_id#',$eqLogic->getCmd(null, 'off'),"id",false);	



        $cmd_Mode_fonctionnement=$eqLogic->getCmd(null, 'mode_fonctionnement');

        if (is_object($cmd_Mode_fonctionnement)){

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

        }
        $replace['#img#'] = $image;
        if ($erreur){
          $replace['#display_erreur#'] ="block";
        }else{
          $replace['#display_erreur#'] ="none";
        }	
        $html = template_replace($replace, getTemplate('core', $version, 'prise', 'planification'));
      }
      if ($erreur){
        $replace['#display_erreur#'] ="block";
        planification::add_log("debug",'Erreur: '. implode("//",$liste_erreur),$eqLogic);
      }else{
        $replace['#display_erreur#'] ="none";
      }

      cache::set('widgetHtml' . $version . $eqLogic->getId(), $html, 0);
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
    planification::add_log("debug","nom_fichier:".$nom_fichier,$eqLogic);
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

  function execute($_options = array()) {
    $cmd=$this;
    $eqLogic = $cmd->getEqLogic();
    planification::add_log("info","execute: " . $cmd->getLogicalId(),$eqLogic);
    switch ($cmd->getLogicalId()) {
      case 'refresh':
        // return;
        $cmd_mode=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'mode_fonctionnement');
        if (is_object($cmd_mode)){
          if ($cmd_mode->execCmd() != 'Manuel'){							
            $eqLogic->Execute_action_actuelle();
            $eqLogic->Recup_prochaine_action();
          }
        }
        //$eqLogic->refresh();

        
        break;
      case 'set_heure_fin':				
        if (strtotime("now") > strtotime($_options['message'])){
          $_options['message'] =date('d-m-Y H:i', strtotime("now") + 60);
        }
        $eqLogic->checkAndUpdateCmd('heure_fin', date('d-m-Y H:i',strtotime($_options['message'])));
        break;
      case 'set_planification':

        $planifications=$eqLogic->Recup_planifications();

        foreach($planifications as $planification){
          if($_options["select"]==$planification["nom_planification"]){
            $_options["Id_planification"]=$planification["Id"];
            break;	
          }
        }
        if($eqLogic->getConfiguration("Id_planification_en_cours",$_options["Id_planification"]) == $_options["Id_planification"]){
          if(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'planification_en_cours') != $_options["select"]){
            $eqLogic->checkAndUpdateCmd('planification_en_cours',$_options["select"]);
          }
          planification::add_log("debug","Planification identique.",$eqLogic);	
          return;
        }
        planification::add_log("debug","nom_planification: " . $_options["select"],$eqLogic);	
        $eqLogic->checkAndUpdateCmd('planification_en_cours',$_options["select"]);
        $eqLogic->setConfiguration("Id_planification_en_cours",$_options["Id_planification"]);
        $eqLogic->save(true);
        $cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
        if (is_object($cmd_refresh)){
          $cmd_refresh->execute();
        }
        break;
      default:
        switch ($eqLogic->getConfiguration("type","")) {
          case "Poele":
            switch ($cmd->getLogicalId()) {
              case 'auto':
                $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Auto");
                $cmd_temp_consigne=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature');
                if (is_object($cmd_temp_consigne)){
                  $temp_par_defaut=$eqLogic->getConfiguration("temperature_consigne_par_defaut","21");
                  $cmd_temp_consigne->event($temp_par_defaut);

                }
                $cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
                if (is_object($cmd_refresh)){
                  $cmd_refresh->execute();
                }
                break;
              case 'force':
              case 'arret':
              case 'absent':
              case 'allume':
              case 'eteint':
                $eqLogic->checkAndUpdateCmd('action_en_cours',ucwords($cmd->getName()));
                if(!isset($_options["mode"])){
                  $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Manuel");
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")",$eqLogic );
                  if($cmd->getLogicalId() == "arret" ){
                    $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                    if(is_object($cmd_heure_fin)){
                      $cmd_heure_fin->event("");
                    }
                    $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                    if(is_object($cmd_action_suivante)){
                      $cmd_action_suivante->event("");
                    }
                    planification::add_log("debug","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                    $cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
                    if (is_object($cmd_set_heure_fin)){
                      $duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",0);
                      if($duree_mode_manuel_par_defaut == 0 ){	
                        $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                        if(is_object($cmd_heure_fin)){
                          $cmd_heure_fin->event("");
                        }
                        $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                        if(is_object($cmd_action_suivante)){
                          $cmd_action_suivante->event("");
                        }
                        planification::add_log("debug","Réactivation manuelle",$eqLogic);
                        $eqLogic->refresh();
                        $eqLogic->refreshWidget() ;
                        return;
                      }else{
                        $date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
                        $arr=["message" => date ('d-m-Y H:i', $date_Fin)];
                        $cmd_set_heure_fin->execute( $arr) ;
                        $eqLogic->refresh();
                        $eqLogic->refreshWidget() ;
                        return;
                      }
                    }
                    return;
                  }else{
                    $cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
                    if (is_object($cmd_set_heure_fin)){
                      $duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",0);
                      if($duree_mode_manuel_par_defaut ==0 ){	
                        $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                        if(is_object($cmd_heure_fin)){
                          $cmd_heure_fin->event("");
                        }
                        $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                        if(is_object($cmd_action_suivante)){
                          $cmd_action_suivante->event("");
                        }

                        planification::add_log("debug","Réactivation manuelle",$eqLogic);
                        return;
                      }else{
                        $date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
                        //planification::add_log("debug","date_Fin: " . date ("d/m/Y H:i", $date_Fin),$eqLogic);
                        $arr=["message" => date ('d-m-Y H:i', $date_Fin)];
                        $cmd_set_heure_fin->execute( $arr) ;
                      }
                    }
                  }
                  $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                  planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                }else{
                  $eqLogic->refresh();
                  $eqLogic->refreshWidget() ;
                }

                break;
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
            }
          case "PAC":
            switch ($cmd->getLogicalId()) {

              case 'auto':
                $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Auto");
                $cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
                if (is_object($cmd_refresh)){
                  $cmd_refresh->execute();
                }
                break;

              case 'arret':
              case 'chauffage':
              case 'chauffage ECO':
              case 'climatisation':
              case 'ventilation':
              case 'absent':
                $eqLogic->checkAndUpdateCmd('action_en_cours',ucwords($cmd->getName()));

                if(!isset($_options["mode"])){
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                  $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Manuel");
                  if($cmd->getLogicalId() == "arret" ){
                    $eqLogic->checkAndUpdateCmd('boost', 0);
                    $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                    if(is_object($cmd_heure_fin)){
                      $cmd_heure_fin->event("");
                    }

                    $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                    if(is_object($cmd_action_suivante)){
                      $cmd_action_suivante->event("");
                    }
                  }else if($cmd->getLogicalId() == "absent"){
                    $eqLogic->checkAndUpdateCmd('boost', 0);
                    $cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
                    if (is_object($cmd_set_heure_fin)){
                      $cmd_heure_fin->event("");
                    }
                    $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                    if(is_object($cmd_action_suivante)){
                      $cmd_action_suivante->event("");
                    }
                  }else{
                    if ($cmd->getLogicalId() == "chauffage" || $cmd->getLogicalId() == "chauffage ECO"){
                      $cmd_temperature_chauffage=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature_chauffage');
                      $eqLogic->checkAndUpdateCmd('consigne_temperature',$cmd_temperature_chauffage->execCmd());
                     
                    }else if($cmd->getLogicalId() == "climatisation"){
                      $cmd_temperature_climatisation=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'consigne_temperature_climatisation');
                      $eqLogic->checkAndUpdateCmd('consigne_temperature',$cmd_temperature_climatisation->execCmd());
                       
                    }
                    $cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
                    if (is_object($cmd_set_heure_fin)){
                      $duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",0);
                      if($duree_mode_manuel_par_defaut ==0 ){
                         $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                        if(is_object($cmd_heure_fin)){
                          $cmd_heure_fin->event("");
                        }
                        $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                        if(is_object($cmd_action_suivante)){
                          $cmd_action_suivante->event("");
                        }

                        planification::add_log("debug","Réactivation manuelle",$eqLogic);
                        $eqLogic->refresh();
                        $eqLogic->refreshWidget() ;
                        return;
                      }else{
                        $date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
                        $arr=["message" => date ('d-m-Y H:i', $date_Fin)];
                        $cmd_set_heure_fin->execute( $arr) ;
                      }
                    }
                  }
                  $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                  planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                }else{
                  $eqLogic->refresh();
                  $eqLogic->refreshWidget() ;
                }
                
                break;
              case 'boost_on':
                if(isset($_options["mode"])){
                  planification::add_log("debug",$_options["mode"],$eqLogic);
                }else{
                  planification::add_log("debug","pas de mode",$eqLogic);
                }
                $eqLogic->checkAndUpdateCmd('boost', 1);
                $eqLogic->checkAndUpdateCmd('mode_fonctionnement', 'boost_on');
                $cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
                if (is_object($cmd_set_heure_fin)){
                  $duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",0);
                  if($duree_mode_manuel_par_defaut ==0 ){
                    planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                    planification::add_log("debug","Réactivation manuelle",$eqLogic);
                    return;
                  }
                  $date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
                  planification::add_log("debug","date_Fin: " . date ("Y/m/d H:i", $date_Fin),$eqLogic);
                  $arr=["message" => date ('d-m-Y H:i', $date_Fin)];
                  $cmd_set_heure_fin->execute( $arr) ;
                }
                $eqLogic->refresh();
                $eqLogic->refreshWidget() ;
                break;

              case 'boost_off':
                planification::add_log("debug","Réactivation manuelle",$eqLogic);
                planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                $eqLogic->checkAndUpdateCmd('boost', 0);
                $eqLogic->checkAndUpdateCmd('mode_fonctionnement', 'boost_off');
							  $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                if(is_object($cmd_heure_fin)){
                  $cmd_heure_fin->event("");
                }
                $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                if(is_object($cmd_action_suivante)){
                  $cmd_action_suivante->event("");
                }
                $eqLogic->refresh();
                $eqLogic->refreshWidget() ;
                break;

              case 'set_consigne_temperature':
                //planification::add_log("debug","nouvelle consigne: " . $_options["slider"],$eqLogic);
                $cmd_action_en_cours=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_en_cours');
                $cmd_prochaine_action=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'action_suivante');
                if ($cmd_action_en_cours->execCmd() == "Chauffage" || $cmd_action_en_cours->execCmd() == "Chauffage ECO"){
                  $eqLogic->checkAndUpdateCmd('consigne_temperature_chauffage',$_options["slider"]);                  
                }else if ($cmd_action_en_cours->execCmd() == "Climatisation"){
                  $eqLogic->checkAndUpdateCmd('consigne_temperature_climatisation',$_options["slider"]);
                }else if ($cmd_prochaine_action->execCmd() == "Climatisation"){
                  $eqLogic->checkAndUpdateCmd('consigne_temperature_climatisation',$_options["slider"]);
                }else if ($cmd_prochaine_action->execCmd() == "Chauffage"){
                  $eqLogic->checkAndUpdateCmd('consigne_temperature_chauffage',$_options["slider"]);
                }
                $eqLogic->checkAndUpdateCmd('consigne_temperature',$_options["slider"]);
                $eqLogic->refresh();
                $eqLogic->refreshWidget() ;
                break;
              default:
            }
            break;
          case "Chauffage":
            switch ($cmd->getLogicalId()) {

              case 'auto':
                $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Auto");
                $cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
                if (is_object($cmd_refresh)){
                  $cmd_refresh->execute();
                }
                break;

              case 'arret':
              case 'absent':
              case 'confort':
              case 'hors_gel':
              case 'eco':
              case 'stop':
                $eqLogic->checkAndUpdateCmd('action_en_cours',ucwords($cmd->getName()));

                if(!isset($_options["mode"])){
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                  $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Manuel");
                  if($cmd->getLogicalId() == "arret"){
                    $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                    if(is_object($cmd_heure_fin)){
                      $cmd_heure_fin->event("");
                    }
                    $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                    if(is_object($cmd_action_suivante)){
                      $cmd_action_suivante->event("");
                    }
                  }else if($cmd->getLogicalId() == "absent"){
                    $cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
                    if (is_object($cmd_set_heure_fin)){
                      $duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",0);
                      if($duree_mode_manuel_par_defaut ==0 ){
                        $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                        if(is_object($cmd_heure_fin)){
                          $cmd_heure_fin->event("");
                        }
                        $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                        if(is_object($cmd_action_suivante)){
                          $cmd_action_suivante->event("");
                        }
                        planification::add_log("debug","Réactivation manuelle",$eqLogic);
                        $eqLogic->refresh();
                        $eqLogic->Execute_action_actuelle();
                        $eqLogic->refreshWidget() ;
                        return;
                      }else{
                        $date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
                        $arr=["message" => date ('d-m-Y H:i', $date_Fin)];
                        $cmd_set_heure_fin->execute( $arr) ;
                      }
                    }
                  }else{
                    $cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
                    if (is_object($cmd_set_heure_fin)){
                      $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                      if(is_object($cmd_heure_fin)){
                        $cmd_heure_fin->event("");
                      }
                      $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                      if(is_object($cmd_action_suivante)){
                        $cmd_action_suivante->event("");
                      }
                      planification::add_log("debug","Réactivation manuelle",$eqLogic);
                    }
                  }
                  $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                  planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                }else{
                  $eqLogic->refresh();
                  $eqLogic->refreshWidget() ;
                }
                break;
              default:
            }
            break;
          case "Volet":
            switch ($cmd->getLogicalId()) {

              case 'auto':
                $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Auto");
                $cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
                if (is_object($cmd_refresh)){
                  $cmd_refresh->execute();
                }
                break;
              case 'ouverture':
              case 'fermeture':
              case 'my':

                $eqLogic->checkAndUpdateCmd('action_en_cours',ucwords($cmd->getName()));
                if(!isset($_options["mode"])){
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                  $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Manuel");
                  if($cmd->getLogicalId() == "arret"){
                    $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                    if(is_object($cmd_heure_fin)){
                      $cmd_heure_fin->event("");
                    }
                    $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                    if(is_object($cmd_action_suivante)){
                      $cmd_action_suivante->event("");
                    }
                  }else{
                    $cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
                    if (is_object($cmd_set_heure_fin)){
                      $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                      if(is_object($cmd_heure_fin)){
                        $cmd_heure_fin->event("");
                      }
                      $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                      if(is_object($cmd_action_suivante)){
                        $cmd_action_suivante->event("");
                      }
                      planification::add_log("debug","Réactivation manuelle",$eqLogic);
                     }
                  }
                  $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                  planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                }else{
                  $eqLogic->refresh();
                  $eqLogic->refreshWidget() ;
                }
                break;
              default:
            }
            break;
          case "Prise":
            switch ($cmd->getLogicalId()) {

              case 'auto':
                $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Auto");
                $cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
                if (is_object($cmd_refresh)){
                  $cmd_refresh->execute();
                }
                break;
              case 'on':
              case 'off':

                $eqLogic->checkAndUpdateCmd('action_en_cours',ucwords($cmd->getName()));
                if(!isset($_options["mode"])){
                  $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Manuel");
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                  if($cmd->getLogicalId() == "arret"){
                    $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                    if(is_object($cmd_heure_fin)){
                      $cmd_heure_fin->event("");
                    }
                    $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                    if(is_object($cmd_action_suivante)){
                      $cmd_action_suivante->event("");
                    }
                  }else{
                    $cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
                    if (is_object($cmd_set_heure_fin)){
                      $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                      if(is_object($cmd_heure_fin)){
                        $cmd_heure_fin->event("");
                      }
                      $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                      if(is_object($cmd_action_suivante)){
                        $cmd_action_suivante->event("");
                      }
                      planification::add_log("debug","Réactivation manuelle",$eqLogic);
                    }
                  }
                  $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                  planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                }else{
                  $eqLogic->refresh();
                  $eqLogic->refreshWidget() ;
                }
                break;
              default:
            }
          case "Perso":
            switch ($cmd->getLogicalId()) {

              case 'auto':
                $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Auto");
                $cmd_refresh=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'refresh');
                if (is_object($cmd_refresh)){
                  $cmd_refresh->execute();
                }
                break;
              default:

                $eqLogic->checkAndUpdateCmd('action_en_cours',ucwords($cmd->getName()));
                if(!isset($_options["mode"])){
                  planification::add_log("info","Passage en Manuel(" . $cmd->getName() . ")" ,$eqLogic);
                  $eqLogic->checkAndUpdateCmd('mode_fonctionnement', "Manuel");
                  if($cmd->getLogicalId() == "arret"){
                    $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                    if(is_object($cmd_heure_fin)){
                      $cmd_heure_fin->event("");
                    }
                    $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                    if(is_object($cmd_action_suivante)){
                      $cmd_action_suivante->event("");
                    }
                  }else{
                    $cmd_set_heure_fin=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin');
                    if (is_object($cmd_set_heure_fin)){
                      $cmd_heure_fin = $eqLogic->getCmd(null, "heure_fin");
                      if(is_object($cmd_heure_fin)){
                        $cmd_heure_fin->event("");
                      }
                      $cmd_action_suivante = $eqLogic->getCmd(null, "action_suivante");
                      if(is_object($cmd_action_suivante)){
                        $cmd_action_suivante->event("");
                      }
                      planification::add_log("debug","Réactivation manuelle",$eqLogic);
                     }
                  }
                  $eqLogic_cmd=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),$cmd->getLogicalId());
                  planification::execute_action($eqLogic,$cmd,$eqLogic_cmd->getConfiguration("commande",""));
                }else{
                  $eqLogic->refresh();
                  $eqLogic->refreshWidget() ;
                }
                break;
            }
        }
        break;

    }
    $eqLogic->refreshWidget() ;
  }
}
?>