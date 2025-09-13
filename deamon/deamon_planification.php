<?php
  require_once dirname(__FILE__).'/../../../core/php/core.inc.php';
  class EquipementInfo { 
   
    public $infos_lever_coucher_soleil = null; 
    public $Json_infos_lever_coucher_soleil;
    public $planifications;
    public $Paramètres;
    public $Ouvrants;
    public $Gestion_planifications;
    public $Id_planification_en_cours;
    public $mode_fonctionnement;
    public $mode_planification;
    public $type_équipement;
    
    public $action_en_cours;
    public $timestamp_action_suivante;
    public $eqLogic_sans_action_suivante;
    public $timestamp_Json;
    public $cmd_ids;
   
    //spécifique PAC
    public $temperature_consigne;
    public $temperature_ambiante = null;
    public $boost = null;
    public $Ouvrant_ouvert = null;

  }
  function MAJ_equipementsInfos($eqLogic_id,$_equipementsInfos,$complet = false){

    $eqLogic = eqLogic::ById($eqLogic_id);
    $Json = $eqLogic::Get_Json($eqLogic_id);
    
    $cache_array=[];
    $cmds=$eqLogic->getCmd();
    foreach($cmds as $cmd){
        if($cmd->getType() == 'info'){
          $cache_array[$cmd->getLogicalId()] = $cmd->execCmd();
        }
          $cache_array["cmds_id"][str_replace(" " , "_", $cmd->getLogicalId())] = $cmd->getId();
        }
  
    if ($complet){    
      $fichier = dirname(__FILE__) ."/../planifications/" .  $eqLogic_id . ".json";
      if(file_exists ($fichier) ){
        $_equipementsInfos->timestamp_Json = filemtime($fichier);
      }else{
        return $_equipementsInfos;
      }
      if($eqLogic->getConfiguration("Type_équipement","") == "Volet"){
        $_equipementsInfos->infos_lever_coucher_soleil = $eqLogic::Recup_infos_lever_coucher_soleil($eqLogic_id,$Json["Lever_coucher"][0]);
        $_equipementsInfos->Json_infos_lever_coucher_soleil = $Json["Lever_coucher"][0];

      }
      
      $_equipementsInfos->cmd_ids = $cache_array["cmds_id"]; 
      $_equipementsInfos->type_équipement = $eqLogic->getConfiguration("Type_équipement","");
      $_equipementsInfos->mode_arret_pac_via_ouvrant = false;
      
     
      $_equipementsInfos->planifications = $Json["Planifications"][0];
      if (isset($Json["Paramètres"])){
        $_equipementsInfos->Paramètres = $Json["Paramètres"][0];
        $_equipementsInfos->Paramètres["Delta_chauffage_boost"] = intval(cmd::ById($_equipementsInfos->cmd_ids["delta_climatisation_boost"])->execCmd());
        $_equipementsInfos->Paramètres["Delta_climatisation_boost"] = intval(cmd::ById($_equipementsInfos->cmd_ids["delta_chauffage_boost"])->execCmd());
        $_equipementsInfos->Paramètres["Delta_chauffage_eco"] = intval(cmd::ById($_equipementsInfos->cmd_ids["delta_chauffage_eco"])->execCmd());
       } 
      if (isset($Json["Ouvrants"])){
        $_equipementsInfos->Ouvrants = $Json["Ouvrants"][0];
      } 
      if (isset($Json["Gestion_planifications"])){
        $_equipementsInfos->Gestion_planifications =$Json["Gestion_planifications"][0];
      } 
      

    }
    if (isset($_equipementsInfos->cmd_ids["mode_planification"])){
      $_equipementsInfos->mode_planification = cmd::ById($_equipementsInfos->cmd_ids["mode_planification"])->execCmd();
    }
    if ($_equipementsInfos->type_équipement == "PAC"){
      
      $_equipementsInfos->temperature_consigne = $cache_array["consigne_temperature"];
      if(isset($cache_array["boost"])){
        $_equipementsInfos->boost = $cache_array["boost"];
      }
      
      $cmd_temperature=cmd::byId(str_replace ("#" ,"" , $eqLogic->getConfiguration('Temperature_ambiante_id',"")));
      if (is_object($cmd_temperature)){
       $_equipementsInfos->temperature_ambiante  = $cmd_temperature->execCmd();
      }
     

      if(isset( $_equipementsInfos->cmd_ids["temperature_consigne"] )){
        $_equipementsInfos->temperature_consigne = number_format(cmd::ById($_equipementsInfos->cmd_ids["temperature_consigne"])->execCmd(),1);
      }
    }
    
     
    $_equipementsInfos->Id_planification_en_cours = $eqLogic->getConfiguration("Id_planification_en_cours","");
    $_equipementsInfos->mode_fonctionnement = $cache_array["mode_fonctionnement"];
    $_equipementsInfos->action_en_cours = $cache_array["action_en_cours"];
    $_equipementsInfos->timestamp_action_suivante =  strtotime($cache_array["heure_fin"]);
    	
    return $_equipementsInfos;
    
  }
  
  function deamon(){
  
    
  
    planification::add_log( 'warning', "Démarrage du démon",null,"");
  
    $pid=getmypid();
    $pid_file = jeedom::getTmpFolder('planification') . '/deamon_planification.pid';
    file_put_contents($pid_file, $pid);
    planification::add_log( 'info', "pid $pid enregistré dans $pid_file",null);
    planification::add_log( 'info', "Listage des eqLogics utilisant le démon",null);
    $equipementsInfos = array(); // Tableau des objets equipementsInfos
    $nb_eqLogic=0;
   

    $last_communication_minute=null;
    $last_execution_15_minutes=null;
    $boucle=10;
    while (1==1){   
      
      $eqLogics = eqLogic::byType('planification',true); 
      $date = time(); 
      $currentHour = date('H', $date); 
      $currentMinute = date('i', $date);
      $currentSeconde = date('s', $date);
      $start=true;
      if ($nb_eqLogic != count((array)$eqLogics)){
        $nb_eqLogic=count((array)$eqLogics);
        if (count((array)$eqLogics) <= 1){
          planification::add_log( 'info', count((array)$eqLogics) . " équipement découvert",null);
        }else{
          planification::add_log( 'info', count((array)$eqLogics) . " équipenments découverts",null);
        }
        foreach ($eqLogics as $eqLogic){
          //$eqLogic->save();
          $eqLogic_id = $eqLogic->getId(); 
          if (!isset($equipementsInfos[$eqLogic_id])) {
            $equipementsInfos[$eqLogic_id] = new EquipementInfo($eqLogic_id); 
            $equipementsInfos[$eqLogic_id]=MAJ_equipementsInfos($eqLogic_id, $equipementsInfos[$eqLogic_id],true);
            planification::add_log('info', "Création equipementsInfos pour ". $eqLogic->getHumanName(),null); 
          }else{
            planification::add_log( 'info', $eqLogic->getHumanName(),null);
          }
        }
      }
      
      foreach ($eqLogics as $eqLogic){
        $execute_action=false;
        if(!$eqLogic->getIsEnable()){
          continue;
        }      
        $eqLogic_id = $eqLogic->getId();
        if($last_communication_minute != $currentMinute){
          cache::set('eqLogicStatusAttr' . $eqLogic_id, utils::setJsonAttr(cache::byKey('eqLogicStatusAttr' . $eqLogic_id)->getValue(), 'lastCommunication', date('Y-m-d H:i:s')));        
          $last_communication_minute = $currentMinute;
        }
        
      
     
        if ($currentHour == 2 && $currentMinute == 0 && $currentSeconde == 0 && $equipementsInfos[$eqLogic_id]->type_équipement == "Volet") {
           planification::add_log('info', "Infos lever coucher soleil mises à jour pour ". $eqLogic->getHumanName(),null,""); 
          $equipementsInfos[$eqLogic_id]->infos_lever_coucher_soleil = $eqLogic::Recup_infos_lever_coucher_soleil($eqLogic_id, $equipementsInfos[$eqLogic_id]->Json_infos_lever_coucher_soleil);
        planification::add_log('info', $equipementsInfos[$eqLogic_id]->infos_lever_coucher_soleil,$eqLogic); 
        }     
        //mise à jour de la commande en cas de changement de température consigne pour la PAC
        if ($equipementsInfos[$eqLogic_id]->type_équipement == "PAC") {
          $cmd_consigne = cmd::byEqLogicIdAndLogicalId($eqLogic_id, 'consigne_temperature')->execCmd();
          $cmd_action = cmd::byEqLogicIdAndLogicalId($eqLogic_id, 'action_en_cours')->execCmd();

          if ($equipementsInfos[$eqLogic_id]->temperature_consigne != $cmd_consigne && $cmd_action != "Ventilation" && $cmd_action != "Arrêt") {
            planification::add_log('info', "Modification de la consigne : " . $equipementsInfos[$eqLogic_id]->temperature_consigne . " => " . $cmd_consigne, $eqLogic);
            if ($equipementsInfos[$eqLogic_id]->mode_fonctionnement == "Auto"){
              foreach (['action_en_cours', 'action_suivante', 'heure_fin'] as $cmd) {
                cmd::byEqLogicIdAndLogicalId($eqLogic_id, $cmd)->set_value("");
              }
              $mapping = ["Chauffage" => "consigne_temperature_chauffage","Chauffage ECO" => "consigne_temperature_chauffage", "Climatisation" => "consigne_temperature_climatisation"];

              if (isset($mapping[$mapping[$cmd_action]])) {
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), $mapping[$mapping[$cmd_action]])->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), 'consigne_temperature')->execCmd());
              }
              $equipementsInfos[$eqLogic_id] = MAJ_equipementsInfos($eqLogic_id, $equipementsInfos[$eqLogic_id], true);
              $execute_action = true;
            }else{
              $mapping = ["Chauffage" => "consigne_temperature_chauffage","Chauffage ECO" => "consigne_temperature_chauffage", "Climatisation" => "consigne_temperature_climatisation"];

              if (isset($mapping[$mapping[$cmd_action]])) {
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), $mapping[$mapping[$cmd_action]])->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), 'consigne_temperature')->execCmd());
                
              }
              $equipementsInfos[$eqLogic_id] = MAJ_equipementsInfos($eqLogic_id, $equipementsInfos[$eqLogic_id], true);
              cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), $cmd_action)->execCmd(); 
            }
          }
        }



        //mise à jour des valeurs en cas de sauvegarde de l'équipement
        $planFile = dirname(__FILE__) . "/../planifications/" . $eqLogic_id . ".json";

        if (file_exists($planFile) && filemtime($planFile) > $equipementsInfos[$eqLogic_id]->timestamp_Json && $equipementsInfos[$eqLogic_id]->Ouvrant_ouvert == 0) {
            $equipementsInfos[$eqLogic_id]->mode_fonctionnement = cmd::byEqLogicIdAndLogicalId($eqLogic_id, 'mode_fonctionnement')->execCmd();

            if ($equipementsInfos[$eqLogic_id]->mode_fonctionnement != 'Manuel') {
                cmd::byEqLogicIdAndLogicalId($eqLogic_id, 'action_en_cours')->set_value("");
                cmd::byEqLogicIdAndLogicalId($eqLogic_id, 'action_suivante')->set_value("");
                cmd::byEqLogicIdAndLogicalId($eqLogic_id, 'heure_fin')->set_value("");
            }
          planification::add_log('info', "equipementsInfos mises à jour suite à enregistrement de l'équipement pour ". $eqLogic->getHumanName(),null,'');
          $equipementsInfos[$eqLogic_id] = MAJ_equipementsInfos($eqLogic_id, $equipementsInfos[$eqLogic_id],true);
          $execute_action = true;
          $eqLogic->Get_actions_planification($equipementsInfos[$eqLogic_id]);
              
        }

        $equipementsInfos[$eqLogic_id]->mode_fonctionnement = cmd::byEqLogicIdAndLogicalId($eqLogic_id,'mode_fonctionnement')->execCmd();
      
        if ($equipementsInfos[$eqLogic_id]->mode_fonctionnement != 'Manuel' && $equipementsInfos[$eqLogic_id]->mode_fonctionnement != "Auto"){
          planification::add_log('info', "mise en auto de l'équipement car ni manuel ni auto ",$eqLogic);
          cmd::byEqLogicIdAndLogicalId($eqLogic_id,'auto')->execCmd();
        }
          
        if($equipementsInfos[$eqLogic_id]->mode_fonctionnement == "Manuel"){
          if (cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd() == ""){    
              if($equipementsInfos[$eqLogic_id]->type_équipement == "PAC"){  
                  if($equipementsInfos[$eqLogic_id]->action_en_cours== "Arrêt" && cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'boost')->execCmd()){
                    $cmd = cmd::byId($equipementsInfos[$eqLogic_id]->cmd_ids["boost_off"]);
                    if ($cmd) {                  
                        $cmd->execCmd(); 
                        planification::add_log('debug', 'boost OFF' ,$eqLogic);
                    } 
                  }
                
                
              }
            



            $duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",0);
            if($duree_mode_manuel_par_defaut == 0  ){
              if($eqLogic->getCmd(null, "heure_fin")->execCmd() != ""){
                $eqLogic->getCmd(null, "heure_fin")->set_value("");
              }
              
            }else{
              
              $date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
              $arr=["message" => date ('d-m-Y H:i', $date_Fin)];
              if($eqLogic->getCmd(null, "heure_fin")->execCmd() != $date_Fin){
                planification::add_log("debug","Réactivation automatique le " . date ('d-m-Y H:i', $date_Fin),$eqLogic);
                cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin')->execute($arr);   
              }
                                 
            }
          }



          if(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_suivante')->execCmd() != ""){
            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_suivante')->set_value("");
          }
          
          $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante = false;
          
          if (cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd() != ""){
            $timestamp_prochaine_action=strtotime(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd());
            if($date > $timestamp_prochaine_action){
              planification::add_log( 'info',"Remise en auto",$eqLogic,"");
              cmd::byEqLogicIdAndLogicalId($eqLogic_id,'auto')->execCmd();
              $equipementsInfos[$eqLogic_id]->mode_fonctionnement = "Auto";
              $equipementsInfos[$eqLogic_id]->mode_planification = "Auto";
            }
          }
          
        }
        //Laisser le if comme ceci ne pas mettre de elseif
        if($equipementsInfos[$eqLogic_id]->mode_fonctionnement == "Auto" ){
         
          if($eqLogic->getConfiguration("Id_planification_en_cours","") != ""){
            if($equipementsInfos[$eqLogic_id]->type_équipement == "PAC"){
                 if($equipementsInfos[$eqLogic_id]->action_en_cours == "Arret" && cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'boost')->execCmd()){
                   $cmd = cmd::byId($equipementsInfos[$eqLogic_id]->cmd_ids["boost_off"]);
                    if ($cmd) {                  
                        $cmd->execCmd(); 
                        planification::add_log('debug', 'boost OFF' ,$eqLogic);
                    } 
                 }
                $mapping = [
                "Chauffage" => "consigne_temperature_chauffage",
                "Chauffage ECO" => "consigne_temperature_chauffage",
                "Climatisation" => "consigne_temperature_climatisation"
                ];
                $temperature_consigne = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), 'consigne_temperature')->execCmd();
                $temperature_consigne_chauffage = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), 'consigne_temperature_chauffage')->execCmd();
                $temperature_consigne_climatisation = cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), 'consigne_temperature_climatisation')->execCmd();
                
              
               
                

              if ($equipementsInfos[$eqLogic_id]->action_en_cours == "Arrêt"  && isset($mapping[$eqLogic->getCmd(null, "action_suivante")->execCmd()])) {
                if($temperature_consigne != $eqLogic->getCmd(null,$mapping[$eqLogic->getCmd(null, "action_suivante")->execCmd()])->execCmd()){
                  $eqLogic->getCmd(null, "consigne_temperature")->set_value($eqLogic->getCmd(null,$mapping[$eqLogic->getCmd(null, "action_suivante")->execCmd()])->execCmd());
                  planification::add_log("debug",'Modification temperature suite à action suivante: ',$eqLogic);
             
                }
                  
               } elseif (isset($mapping[$equipementsInfos[$eqLogic_id]->action_en_cours])) {
                if($temperature_consigne != $eqLogic->getCmd(null,$mapping[$equipementsInfos[$eqLogic_id]->action_en_cours])->execCmd()){
                  planification::add_log("debug",'Modification temperature suite à action actuelle: ',$eqLogic);
                  $eqLogic->getCmd(null, "consigne_temperature")->set_value($eqLogic->getCmd(null,$mapping[$equipementsInfos[$eqLogic_id]->action_en_cours->execCmd()])->execCmd());
                }
              }
            }
            $equipementsInfos[$eqLogic_id]->timestamp_action_suivante = strtotime(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd());
            //modification de la planification dans le cas où l'ancienne planification n'a aucune action suivante
            if($equipementsInfos[$eqLogic_id]->Id_planification_en_cours != $eqLogic->getConfiguration("Id_planification_en_cours","") && $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante ){
              //planification::add_log('info', "modification de la planification dans le cas où l'ancienne planification n'a aucune action suivante ",$eqLogic);
              $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante = false;
              $equipementsInfos[$eqLogic_id]->Id_planification_en_cours = $eqLogic->getConfiguration("Id_planification_en_cours","");
            }
           
           
            
            if($date > $equipementsInfos[$eqLogic_id]->timestamp_action_suivante && $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante == false && $equipementsInfos[$eqLogic_id]->Ouvrant_ouvert == 0 || $equipementsInfos[$eqLogic_id]->action_en_cours == ""){ 
              if($equipementsInfos[$eqLogic_id]->type_équipement == "PAC"){  
                 if($equipementsInfos[$eqLogic_id]->action_en_cours == "arret" && cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'boost')->execCmd()){
                    $cmd = cmd::byId($equipementsInfos[$eqLogic_id]->cmd_ids["boost_off"]);
                    if ($cmd) {                  
                        $cmd->execCmd(); 
                        planification::add_log('debug', 'boost OFF' ,$eqLogic);
                    } 
                 }
                $mapping = [
                "chauffage" => "consigne_temperature_chauffage",
                "chauffage ECO" => "consigne_temperature_chauffage",
                "climatisation" => "consigne_temperature_climatisation"
                ];

                if (isset($mapping[$equipementsInfos[$eqLogic_id]->action_en_cours])) {
                    cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), $mapping[$equipementsInfos[$eqLogic_id]->action_en_cours])->set_value(cmd::byEqLogicIdAndLogicalId($eqLogic->getId(), 'consigne_temperature')->execCmd());
                }

              }
              
              $equipementsInfos[$eqLogic_id]->action_en_cours =  cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_en_cours')->execCmd();
              $equipementsInfos[$eqLogic_id]->timestamp_action_suivante = strtotime(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd());
              $equipementsInfos[$eqLogic_id]->Id_planification_en_cours = $eqLogic->getConfiguration("Id_planification_en_cours","");
              $execute_action = true; 
            
            }
            
          }else{
            
            if($equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante == false){
              $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante = true;
              planification::add_log("debug",$eqLogic->getName() .": Aucun Id de planification enregistré",$eqLogic);
            }
          
            
          }
          //MAJ_equipementsInfos
          if ($currentMinute % 15 === 0 && $currentMinute !== $last_execution_15_minutes) {
              $equipementsInfos[$eqLogic_id] = MAJ_equipementsInfos($eqLogic_id,$equipementsInfos[$eqLogic_id],false);
              $last_execution_15_minutes = $currentMinute;
          }

          //gestion automatisation
          if ($boucle >= 10) {
            
             
            $equipementsInfos[$eqLogic_id] = MAJ_equipementsInfos($eqLogic_id,$equipementsInfos[$eqLogic_id],false);
            //gestion du mode boost sur une PAC
              
              if($equipementsInfos[$eqLogic_id]->type_équipement == "PAC" && $equipementsInfos[$eqLogic_id]->boost !== null){ 
                
                if ($equipementsInfos[$eqLogic_id]->action_en_cours == 'Chauffage ECO'  && $equipementsInfos[$eqLogic_id]->boost == 1){
                  
                  $cmd = cmd::byId($equipementsInfos[$eqLogic_id]->cmd_ids["boost_off"]);
                  if ($cmd) {                  
                      $cmd->execCmd(); 
                      planification::add_log('debug', 'boost_off suite à chauffage ECO' ,$eqLogic);
                  } 

                  
                }
                if ($equipementsInfos[$eqLogic_id]->action_en_cours == 'Arrêt'  && $equipementsInfos[$eqLogic_id]->boost == 1){
                  
                  $cmd = cmd::byId($equipementsInfos[$eqLogic_id]->cmd_ids["boost_off"]);
                  if ($cmd) {                  
                      $cmd->execCmd(); 
                      planification::add_log('debug', 'boost_off suite à arrêt' ,$eqLogic);
                  } 

                  
                }
                if ($equipementsInfos[$eqLogic_id]->action_en_cours == 'Chauffage' && $equipementsInfos[$eqLogic_id]->temperature_ambiante !== null){
                  if($equipementsInfos[$eqLogic_id]->temperature_ambiante <= $equipementsInfos[$eqLogic_id]->temperature_consigne - $equipementsInfos[$eqLogic_id]->Paramètres["Delta_chauffage_boost"] && $equipementsInfos[$eqLogic_id]->boost == 0){
                    $cmd = cmd::byId($equipementsInfos[$eqLogic_id]->cmd_ids["boost_on"]);
                    if ($cmd) {                  
                        $cmd->execCmd(); 
                        planification::add_log('debug', 'boost ON' ,$eqLogic);
                    } 
                  }
                  
                  
                  if($equipementsInfos[$eqLogic_id]->temperature_ambiante >= $equipementsInfos[$eqLogic_id]->temperature_consigne && $equipementsInfos[$eqLogic_id]->boost == 1){
                    $cmd = cmd::byId($equipementsInfos[$eqLogic_id]->cmd_ids["boost_off"]);
                    if ($cmd) {                  
                        $cmd->execCmd(); 
                        planification::add_log('debug', 'boost OFF' ,$eqLogic);
                    } 
                  }
                }
                if($equipementsInfos[$eqLogic_id]->action_en_cours == 'Climatisation' && $equipementsInfos[$eqLogic_id]->temperature_ambiante != null){
                  if($equipementsInfos[$eqLogic_id]->temperature_ambiante >= $equipementsInfos[$eqLogic_id]->temperature_consigne + $equipementsInfos[$eqLogic_id]->Paramètres["Delta_climatisation_boost"] && $equipementsInfos[$eqLogic_id]->boost == 0){
                    $cmd = cmd::byId($equipementsInfos[$eqLogic_id]->cmd_ids["boost_on"]);
                    if ($cmd) {                  
                        $cmd->execCmd(); 
                        planification::add_log('debug', 'boost ON' ,$eqLogic);
                    } 
                  }
                  if($equipementsInfos[$eqLogic_id]->temperature_ambiante <= $equipementsInfos[$eqLogic_id]->temperature_consigne && $equipementsInfos[$eqLogic_id]->boost == 1){
                    $cmd = cmd::byId($equipementsInfos[$eqLogic_id]->cmd_ids["boost_off"]);
                    if ($cmd) {                  
                        $cmd->execCmd(); 
                        planification::add_log('debug', 'boost OFF' ,$eqLogic);
                    } 
                  }
                }
              }
              //gestion des ouvrants pour arrêter la PAC ou le Chauffage ou le volet en cas d'ouverture de l'un d'entre eux
              if($equipementsInfos[$eqLogic_id]->Ouvrants != ""){
                $ouvert = 0;
                $delais_ouverture = 0;
                $delais_fermeture = 0;
                $alerte = 0;
                $ouvrant_valuedate=new DateTime();
                $currentTime = new DateTime();
                foreach($equipementsInfos[$eqLogic_id]->Ouvrants as $Ouvrant){
                  if(cmd::ById(trim($Ouvrant[0]["Commande"], "#"))->execCmd()){ 
                    $ouvert = 1;
                    $ouvrant_valuedate =  new DateTime(cmd::ById(trim($Ouvrant[0]["Commande"], "#"))->getValueDate()); // Exemple d'heure de début
                    if ($delais_ouverture < $Ouvrant[0]["Délai_ouverture"]){
                      $delais_ouverture = $Ouvrant[0]["Délai_ouverture"] ;
                    }                   
                    if ($alerte == 0){
                      $alerte = $Ouvrant[0]["Alerte"];
                    }
                    break;
                  }elseif ($ouvert == 0){
                    $ouvrant_valuedate =  new DateTime(cmd::ById(trim($Ouvrant[0]["Commande"], "#"))->getValueDate()); // Exemple d'heure de début
                    if ($delais_fermeture > $Ouvrant[0]["Délai_fermeture"]){
                      $delais_fermeture = $Ouvrant[0]["Délai_fermeture"] ;
                    }  
                    if ($alerte == 0){
                      $alerte = $Ouvrant[0]["Alerte"];
                    }                 
                  }
                }             

                  
                  $interval = $currentTime->diff($ouvrant_valuedate);
                  $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
              
                    if ($ouvert){ 
                    redo:
                      if($minutes >=  $delais_ouverture && $equipementsInfos[$eqLogic_id]->Ouvrant_ouvert == 0){
                        //if ($minutes > $delais_ouverture + 2 ){
                        // $equipementsInfos[$eqLogic_id]->Ouvrant_ouvert == 1;
                        //  goto redo;
                        //}
                        
                        if($equipementsInfos[$eqLogic_id]->action_en_cours != 'Arrêt' || $start){
                          $equipementsInfos[$eqLogic_id]->Ouvrant_ouvert = 1; 
                          $execute_action = false;
                      
                          planification::add_log("debug","Mise en mode arrêt suite à l'ouverture d'un ouvrant",$eqLogic);
                          
                          if($equipementsInfos[$eqLogic_id]->mode_fonctionnement == "Auto"){
                            cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["arret"])->execCmd(array('mode'=>"auto"));
                          }else{
                            cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["arret"])->execCmd();
                          }                    
                        
                          if($alerte){
                            planification::add_log("error","Un des ouvrants est ouvert: Arrêt de la PAC",$eqLogic,"");
                          }
                        }
                        
                      }
                    }else if ($minutes >= $delais_fermeture && $equipementsInfos[$eqLogic_id]->Ouvrant_ouvert == 1){
                      planification::add_log("debug","Tous les ouvrants sont fermé: redemarrage de la PAC",$eqLogic);
                      $equipementsInfos[$eqLogic_id]->Ouvrant_ouvert = 0;
                      
                      if($alerte){
                        planification::add_log("error","Tous les ouvrants sont fermé: redemarrage de la PAC",$eqLogic,"");
                      }
                    }
                      
                    
                
              }
              
              
          }

          if ($execute_action){
            planification::add_log("debug"," execute_action  ",$eqLogic);
            $eqLogic->Get_actions_planification($equipementsInfos[$eqLogic_id]);

            if(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd() == ''){
              planification::add_log("debug","Aucune action suivante ",$eqLogic);
              
              $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante = true;
            }
          }
        }
        //gestion des conditions de changement de planification
        if ($equipementsInfos[$eqLogic_id]->Gestion_planifications != "" && $equipementsInfos[$eqLogic_id]->mode_planification == "Auto"){
          $arr["select"] = "";
          $arr["Id_planification"] = "";
          $arr["mode"] = "Auto";
          foreach($equipementsInfos[$eqLogic_id]->Gestion_planifications as $Gestion_planification){
            
            if(planification::TestExpression($Gestion_planification[0]["Conditions"])){
            
              $arr["select"] = $Gestion_planification[0]["Nom"];
              $arr["Id_planification"] = $Gestion_planification[0]["Id"];
              if($Gestion_planification[0]["Stop"]){
                break;
              }
            }
          }
          
          if($equipementsInfos[$eqLogic_id]->Id_planification_en_cours != $arr["Id_planification"] && $arr["Id_planification"] !='' ){
            planification::add_log("debug",'Modification planification: ' .$Gestion_planification[0]["Nom"],$eqLogic);
            cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["set_planification"])->execCmd($arr);
              
          }
          
          

        }
        //modification de la planification 
        if($equipementsInfos[$eqLogic_id]->mode_planification == "Auto"){             
            if($equipementsInfos[$eqLogic_id]->Id_planification_en_cours != $eqLogic->getConfiguration("Id_planification_en_cours","") && !$equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante && $equipementsInfos[$eqLogic_id]->Ouvrant_ouvert == 0){
            planification::add_log("debug","modification de la planification ",$eqLogic);
            $arr["select"] = "";
            $arr["Id_planification"] = "";


            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_en_cours')->set_value("");
            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_suivante')->set_value("");
            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->set_value("");
            $equipementsInfos[$eqLogic_id]->action_en_cours =  "";
            $equipementsInfos[$eqLogic_id]->timestamp_action_suivante = strtotime(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd());
            $equipementsInfos[$eqLogic_id]->Id_planification_en_cours = $eqLogic->getConfiguration("Id_planification_en_cours","");
            $execute_action = true;               
          }
        }
      }
      
      
      $start=false;
    
      if ($boucle >= 10){
        $boucle=1;
      }else{
        $boucle += 1;
      }
      
      sleep(1);
    }
  }

  
deamon();

  
?>