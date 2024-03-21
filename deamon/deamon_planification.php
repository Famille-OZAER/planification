<?php
  require_once dirname(__FILE__).'/../../../core/php/core.inc.php';
function deamon(){
  planification::add_log( 'info',"Démarrage du démon");
  $pid_file = jeedom::getTmpFolder('planification') . '/deamon_planification.pid';
  $pid=getmypid();
  file_put_contents($pid_file, $pid);
  planification::add_log( 'info', "pid $pid enregistré dans $pid_file");
  planification::add_log( 'info', "Listage des eqLogics utilisant le démon");
  $nb_eqLogic=0;
  while (1==1){

    $eqLogics = eqLogic::byType('planification',true); 
    if ($nb_eqLogic != count((array)$eqLogics)){
      $nb_eqLogic=count((array)$eqLogics);
      if (count((array)$eqLogics) <= 1){
        planification::add_log( 'info', count((array)$eqLogics) . " équipement découvert");
      }else{
        planification::add_log( 'info', count((array)$eqLogics) . " équipenments découverts");
      }
      foreach ($eqLogics as $eqLogic){
        if(!is_null($eqLogic->getObject_id())){
          planification::add_log( 'info', $eqLogic->getName()." " .  jeeObject::byId($eqLogic->getObject_id())->getName());
        }else{
          planification::add_log( 'info', $eqLogic->getName());
        }
      }
    }
    foreach ($eqLogics as $eqLogic){
      if(!$eqLogic->getIsEnable()){
        continue;
      }
      $date=time();
      $infos_lever_coucher_soleil=planification::Recup_infos_lever_coucher_soleil($eqLogic->getId());			
      $cmd_mode_fonctionnement = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), "mode_fonctionnement");
      if(is_object($cmd_mode_fonctionnement)){
        $mode_fonctionnement=$cmd_mode_fonctionnement->execCmd();	
      }
      //planification::add_log( 'info', "mode_fonctionnement: " . $mode_fonctionnement);
        
      if($mode_fonctionnement != "Auto"){
        $cmd_heure_fin=$eqLogic->getCmd(null, 'heure_fin');
        //planification::add_log( 'info',"cmd_heure_fin: " . $cmd_heure_fin->execCmd());
        if(is_object($cmd_heure_fin)){
          if ($cmd_heure_fin->execCmd() != ""){
            $timestamp_prochaine_action=strtotime($cmd_heure_fin->execCmd());
            if($date > $timestamp_prochaine_action){
              planification::add_log( 'info',"Remise en auto",$eqLogic);
              $cmd_auto=$eqLogic->getCmd(null, 'Auto');
              $cmd_auto->execute();
            }
          }
        }
      }
      //Laisser le if comme ceci ne pas mettre de elseif
      if($mode_fonctionnement == "Auto"){
        $action_en_cours="";
        $cmd_action_en_cours=$eqLogic->getCmd(null, "action_en_cours");
        if(is_object($cmd_action_en_cours)){
          $action_en_cours=$cmd_action_en_cours->execCmd();
        }
        $Id_planification_en_cours=$eqLogic->getConfiguration("Id_planification_en_cours","");
        if($Id_planification_en_cours!=""){
          $cmd_heure_fin=$eqLogic->getCmd(null, 'heure_fin');
          $timestamp_prochaine_action=strtotime($cmd_heure_fin->execCmd());

       
        if($cmd_heure_fin->execCmd() != "" && $date > $timestamp_prochaine_action){
          //planification::add_log( 'info',$eqLogic->getName() . " Recup action actuelle");
          
          $eqLogic->Execute_action_actuelle();
          $eqLogic->Recup_action_suivante();
        }
        }else{
          //planification::add_log("debug",$eqLogic->getName() .": Aucun Id de planification enregistré");
        }
       
      }
    }

    //  planification::add_log( 'info',"Fin de lancement des eqLogics");
    sleep(10);
  }
}

deamon();

?>