<?php
  require_once dirname(__FILE__).'/../../../core/php/core.inc.php';
  class EquipementInfo { 
   
    public $infos_lever_coucher_soleil = null; 
    public $Json_infos_lever_coucher_soleil;
    public $planifications;
    public $Automatisation_Paramètres;
    public $Automatisation_Ouvrants;
    public $Automatisation_Gestion_planifications;
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
    public $Ouvrants = null;

  }
  function MAJ_equipementsInfos($eqLogic_id,$_equipementsInfos,$complet = false){

    $eqLogic = eqLogic::ById($eqLogic_id);
    $Json = $eqLogic::Get_Json($eqLogic_id);
    if (config::byKey('cache::engine') == "MariadbCache"){       
      $sql = "SELECT `key`,`datetime`,`value`,`lifetime` FROM cache WHERE cache.key LIKE 'Planification_". $eqLogic_id . "%'";
      $caches =  DB::Prepare($sql,array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS,'cache');
      $cache_array=[];
      foreach($caches as $cache){
          $cache_array[str_replace("Planification_" . $eqLogic_id . "_", "",$cache->getKey())] = unserialize($cache->getValue());
        }
    }
    if ($complet){
      if($_equipementsInfos->infos_lever_coucher_soleil === null && $eqLogic->getConfiguration("Type_équipement","") == "Volet"){
        $_equipementsInfos->infos_lever_coucher_soleil = $eqLogic::Recup_infos_lever_coucher_soleil($eqLogic_id,$Json["Lever_coucher"][0]);
        $_equipementsInfos->Json_infos_lever_coucher_soleil = $Json["Lever_coucher"][0];
      }
      
      $_equipementsInfos->cmd_ids = $cache_array["cmds_id"]; 
      $_equipementsInfos->type_équipement = $eqLogic->getConfiguration("Type_équipement","");
      $_equipementsInfos->mode_arret_pac_via_ouvrant = false;
      if (isset($_equipementsInfos->cmd_ids["mode_planification"])){
        $_equipementsInfos->mode_planification = cmd::ById($_equipementsInfos->cmd_ids["mode_planification"])->execCmd();
      }
     
      $_equipementsInfos->planifications = $Json["Planifications"][0];
      if (isset($Json["Paramètres"])){
        $_equipementsInfos->Automatisation_Paramètres = $Json["Paramètres"][0];
        $_equipementsInfos->Automatisation_Paramètres["Delta_chauffage_boost"] = intval(cmd::ById($_equipementsInfos->cmd_ids["delta_climatisation_boost"])->execCmd());
        $_equipementsInfos->Automatisation_Paramètres["Delta_climatisation_boost"] = intval(cmd::ById($_equipementsInfos->cmd_ids["delta_chauffage_boost"])->execCmd());
        $_equipementsInfos->Automatisation_Paramètres["Delta_chauffage_eco"] = intval(cmd::ById($_equipementsInfos->cmd_ids["delta_chauffage_eco"])->execCmd());
       } 
      if (isset($Json["Ouvrants"])){
        $_equipementsInfos->Automatisation_Ouvrants = $Json["Ouvrants"][0];
      } 
      if (isset($Json["Gestion_planifications"])){
        $_equipementsInfos->Automatisation_Gestion_planifications =$Json["Gestion_planifications"][0];
      } 
      $fichier = dirname(__FILE__) ."/../planifications/" .  $eqLogic_id . ".json";
      $_equipementsInfos->timestamp_Json = filemtime($fichier);
    }

    if ($_equipementsInfos->type_équipement == "PAC"){
      
      $_equipementsInfos->temperature_consigne = $cache_array["consigne_temperature"];
      if(isset($cache_array["boost"])){
        $_equipementsInfos->boost = $cache_array["boost"];
      }
    
      if(isset( $_equipementsInfos->cmd_ids["temperature_ambiante"])){
        $_equipementsInfos->temperature_ambiante = cmd::ById($_equipementsInfos->cmd_ids["temperature_ambiante"])->execCmd();
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
  
    
  
    planification::add_log( 'error', "Démarrage du démon",null,"planification");
  
    $pid=getmypid();
    $pid_file = jeedom::getTmpFolder('planification') . '/deamon_planification.pid';
    file_put_contents($pid_file, $pid);
    planification::add_log( 'info', "pid $pid enregistré dans $pid_file",null,"planification_deamon");
    planification::add_log( 'info', "Listage des eqLogics utilisant le démon",null,"planification_deamon");
    $equipementsInfos = array(); // Tableau des objets equipementsInfos
    $nb_eqLogic=0;
    $boucle = 60;
    $boucle1 = 60;

    $last_communication_minute='';





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
          planification::add_log( 'info', count((array)$eqLogics) . " équipement découvert",null,"planification_deamon");
        }else{
          planification::add_log( 'info', count((array)$eqLogics) . " équipenments découverts",null,"planification_deamon");
        }
        foreach ($eqLogics as $eqLogic){
          //$eqLogic->save();
          $eqLogic_id = $eqLogic->getId(); 
          if (!isset($equipementsInfos[$eqLogic_id])) {
            $equipementsInfos[$eqLogic_id] = new EquipementInfo($eqLogic_id); 
            $equipementsInfos[$eqLogic_id]=MAJ_equipementsInfos($eqLogic_id, $equipementsInfos[$eqLogic_id],true);
            planification::add_log('info', "Création equipementsInfos pour ". $eqLogic->getHumanName(),null,"planification_deamon"); 
          }else{
            planification::add_log( 'info', $eqLogic->getHumanName(),null,"planification_deamon");
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
		


         // $eqLogic->setStatus("lastCommunication",date('Y-m-d H:i:s'));
          //planification::add_log("debug","mise à jour des communications",$eqLogic,"planification_deamon");
              
        }
        
      
     
        
        if ($currentHour == 2 && $currentMinute == 0 && $currentSeconde == 0 && $equipementsInfos[$eqLogic_id]->type_équipement == "Volet") {
          $equipementsInfos[$eqLogic_id]->infos_lever_coucher_soleil = $eqLogic::Recup_infos_lever_coucher_soleil($eqLogic_id, $equipementsInfos[$eqLogic_id]->Json_infos_lever_coucher_soleil);
          planification::add_log('info', "Infos lever coucher soleil mises à jour pour ". $eqLogic->getHumanName(),null,"planification_deamon"); 
          if($equipementsInfos[$eqLogic_id]->mode_planification == "Auto"){
            
             cmd::ById($_equipementsInfos->cmd_ids["mode_planification"])->setvalue("Auto");
          }
        }     
        //mise à jour de la commande en cas de changement de température consigne pour la PAC
        if($equipementsInfos[$eqLogic_id]->type_équipement == "PAC"){
          if($equipementsInfos[$eqLogic_id]->temperature_consigne != cmd::byEqLogicIdAndLogicalId($eqLogic_id,'consigne_temperature')->execCmd()){

            planification::add_log('info', "Modification de la consigne ". $equipementsInfos[$eqLogic_id]->temperature_consigne . " => " . cmd::byEqLogicIdAndLogicalId($eqLogic_id,'consigne_temperature')->execCmd(),$eqLogic); 
            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_en_cours')->set_value("");
            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_suivante')->set_value("");
            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->set_value("");
            $equipementsInfos[$eqLogic_id] = MAJ_equipementsInfos($eqLogic_id, $equipementsInfos[$eqLogic_id],true);
            $execute_action = true;
          } 
        }
        //mise à jour des valeurs en cas de sauvegarde de l'équipement
        if(filemtime(dirname(__FILE__) ."/../planifications/" .  $eqLogic_id . ".json") > $equipementsInfos[$eqLogic_id]->timestamp_Json && $equipementsInfos[$eqLogic_id]->Ouvrants == 0){
           $equipementsInfos[$eqLogic_id]->mode_fonctionnement = cmd::byEqLogicIdAndLogicalId($eqLogic_id,'mode_fonctionnement')->execCmd();
          if ($equipementsInfos[$eqLogic_id]->mode_fonctionnement != 'Manuel'){
            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_en_cours')->set_value("");
            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_suivante')->set_value("");
            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->set_value("");
            
          }
          planification::add_log('info', "equipementsInfos mises à jour suite à enregistrement de l'équipement pour ". $eqLogic->getHumanName(),null,"planification_deamon");
          $equipementsInfos[$eqLogic_id] = MAJ_equipementsInfos($eqLogic_id, $equipementsInfos[$eqLogic_id],true);
          $execute_action = true;
          //$eqLogic->Get_actions_planification($equipementsInfos[$eqLogic_id]);
              
        }

        $equipementsInfos[$eqLogic_id]->mode_fonctionnement = cmd::byEqLogicIdAndLogicalId($eqLogic_id,'mode_fonctionnement')->execCmd();
      
        if ($equipementsInfos[$eqLogic_id]->mode_fonctionnement != 'Manuel' && $equipementsInfos[$eqLogic_id]->mode_fonctionnement != "Auto"){
          planification::add_log('info', "mise en auto de l'équipement car ni manuel ni auto ",$eqLogic);
          cmd::byEqLogicIdAndLogicalId($eqLogic_id,'auto')->execCmd();
        }
          
        if($equipementsInfos[$eqLogic_id]->mode_fonctionnement == "Manuel" ){
          if (cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd() == ""){    
              if($equipementsInfos[$eqLogic_id]->type_équipement == "PAC"){  
                 if($equipementsInfos[$eqLogic_id]->action_en_cours== "arret" ){
                  if (cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'boost')->execCmd()){
                      $eqLogic->getCmd(null, "boost_off")->execCmd();
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



            $duree_mode_manuel_par_defaut=$eqLogic->getConfiguration("Duree_mode_manuel_par_defaut",0);
            if($duree_mode_manuel_par_defaut ==0 ){
              $eqLogic->getCmd(null, "heure_fin")->set_value("");
            }else{
              
              $date_Fin=strtotime('+'.($duree_mode_manuel_par_defaut)." minute");
              $arr=["message" => date ('d-m-Y H:i', $date_Fin)];
              planification::add_log("debug","Réactivation automatique le " . date ('d-m-Y H:i', $date_Fin),$eqLogic);
              cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'set_heure_fin')->execute($arr);                      
            }
          }



          if(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_suivante')->execCmd() != ""){
            cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_suivante')->set_value("");
          }
          
          $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante = false;
          
          if (cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd() != ""){
            $timestamp_prochaine_action=strtotime(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd());
            if($date > $timestamp_prochaine_action){
              planification::add_log( 'info',"Remise en auto",$eqLogic,"planification_deamon");
              cmd::byEqLogicIdAndLogicalId($eqLogic_id,'auto')->execCmd();cmd::byEqLogicIdAndLogicalId($eqLogic_id,'auto')->execCmd();
            }
          }
          
        }
        //Laisser le if comme ceci ne pas mettre de elseif
        if($equipementsInfos[$eqLogic_id]->mode_fonctionnement == "Auto"){
         
          if($eqLogic->getConfiguration("Id_planification_en_cours","") != ""){
            
            $equipementsInfos[$eqLogic_id]->timestamp_action_suivante = strtotime(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd());
            //modification de la planification dans le cas où l'ancienne planification n'a aucune action suivante
            if($equipementsInfos[$eqLogic_id]->Id_planification_en_cours != $eqLogic->getConfiguration("Id_planification_en_cours","") && $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante ){
              planification::add_log('info', "modification de la planification dans le cas où l'ancienne planification n'a aucune action suivante ",$eqLogic);
              $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante = false;
              $equipementsInfos[$eqLogic_id]->Id_planification_en_cours = $eqLogic->getConfiguration("Id_planification_en_cours","");
            }
            //modification de la planification 
            if($equipementsInfos[$eqLogic_id]->mode_planification == "Auto"){
               if($equipementsInfos[$eqLogic_id]->Id_planification_en_cours != $eqLogic->getConfiguration("Id_planification_en_cours","") && !$equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante && $equipementsInfos[$eqLogic_id]->Ouvrants == 0){
                planification::add_log("debug","modification de la planification ",$eqLogic,"planification_deamon");
                cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_en_cours')->set_value("");
                cmd::byEqLogicIdAndLogicalId($eqLogic_id,'action_suivante')->set_value("");
                cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->set_value("");
                $equipementsInfos[$eqLogic_id]->action_en_cours =  "";
                $equipementsInfos[$eqLogic_id]->timestamp_action_suivante = strtotime(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd());
                $equipementsInfos[$eqLogic_id]->Id_planification_en_cours = $eqLogic->getConfiguration("Id_planification_en_cours","");
                $execute_action = true;               
              }
            }
           
            
            if($date > $equipementsInfos[$eqLogic_id]->timestamp_action_suivante && $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante == false && $equipementsInfos[$eqLogic_id]->Ouvrants == 0 || $equipementsInfos[$eqLogic_id]->action_en_cours == ""){ 
              planification::add_log("debug","date > timestamp_prochaine_action ",$eqLogic,"planification_deamon");
              planification::add_log("debug",$date,$eqLogic,"planification_deamon");
              planification::add_log("debug", $equipementsInfos[$eqLogic_id]->timestamp_action_suivante,$eqLogic,"planification_deamon");
              if($equipementsInfos[$eqLogic_id]->type_équipement == "PAC"){  
                 if($equipementsInfos[$eqLogic_id]->action_en_cours== "arret" && cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'boost')->execCmd()){
                    $eqLogic->getCmd(null, "boost_off")->execCmd();
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
              planification::add_log("debug",$eqLogic->getName() .": Aucun Id de planification enregistré",$eqLogic,"");
            }
          
            
          }
          
          //gestion automatisation
          if ($boucle >= 10){
            $equipementsInfos[$eqLogic_id] = MAJ_equipementsInfos($eqLogic_id,$equipementsInfos[$eqLogic_id],true);
            //gestion du mode boost sur une PAC
              if($equipementsInfos[$eqLogic_id]->type_équipement == "PAC" && $equipementsInfos[$eqLogic_id]->boost !== null){ 
                if ($equipementsInfos[$eqLogic_id]->action_en_cours == 'Chauffage ECO' && $equipementsInfos[$eqLogic_id]->boost == 1){
                  planification::add_log('debug', 'boost_off suite à chauffage ECO' ,$eqLogic,'aaa');
                  cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["boost_off"])->execCmd();
                }
                //planification::add_log('debug', $equipementsInfos[$eqLogic_id]->temperature_ambiante  ,$eqLogic,'aaa');
                //planification::add_log('debug', $equipementsInfos[$eqLogic_id] ,$eqLogic,'aaa');
                
                if ($equipementsInfos[$eqLogic_id]->action_en_cours == 'Chauffage' && $equipementsInfos[$eqLogic_id]->temperature_ambiante !== null){
                  if($equipementsInfos[$eqLogic_id]->temperature_ambiante <= $equipementsInfos[$eqLogic_id]->temperature_consigne - $equipementsInfos[$eqLogic_id]->Automatisation_Paramètres["Delta_chauffage_boost"] && $equipementsInfos[$eqLogic_id]->boost == 0){
                    planification::add_log('debug', 'boost ON' ,$eqLogic);
                    cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["boost_on"])->execCmd();
                  }
                  
                  
                  if($equipementsInfos[$eqLogic_id]->temperature_ambiante >= $equipementsInfos[$eqLogic_id]->temperature_consigne && $equipementsInfos[$eqLogic_id]->boost == 1){
                    planification::add_log('debug', 'boost OFF' ,$eqLogic);
                    cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["boost_off"])->execCmd();
                  }
                }
                if($equipementsInfos[$eqLogic_id]->action_en_cours == 'Climatisation' && $equipementsInfos[$eqLogic_id]->temperature_ambiante != null){
                  if($equipementsInfos[$eqLogic_id]->temperature_ambiante >= $equipementsInfos[$eqLogic_id]->temperature_consigne + $equipementsInfos[$eqLogic_id]->Automatisation_Paramètres["Delta_climatisation_boost"] && $equipementsInfos[$eqLogic_id]->boost == 0){
                    planification::add_log('debug', 'boost ON' ,$eqLogic);
                    cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["boost_on"])->execCmd();
                  }
                  if($equipementsInfos[$eqLogic_id]->temperature_ambiante <= $equipementsInfos[$eqLogic_id]->temperature_consigne && $equipementsInfos[$eqLogic_id]->boost == 1){
                    planification::add_log('debug', 'boost OFF' ,$eqLogic);
                    cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["boost_off"])->execCmd();
                  }
                }
              }
              //gestion des ouvrants pour arrêter la PAC ou le Chauffage ou le volet en cas d'ouverture de l'un d'entre eux
              if($equipementsInfos[$eqLogic_id]->Automatisation_Ouvrants != ""){
                $ouvert = 0;
                $delais_ouverture = 0;
                $delais_fermeture = 0;
                $alerte = 0;
                $ouvrant_valuedate=new DateTime();
                $currentTime = new DateTime();
                foreach($equipementsInfos[$eqLogic_id]->Automatisation_Ouvrants as $Ouvrants){
                  if(cmd::ById(trim($Ouvrants[0]["Commande"], "#"))->execCmd()){
                    //planification::add_log("debug","ouvrant ouvert",$eqLogic,"aa"); 
                    $ouvert = 1;
                    $ouvrant_valuedate =  new DateTime(cmd::ById(trim($Ouvrants[0]["Commande"], "#"))->getValueDate()); // Exemple d'heure de début
                    if ($delais_ouverture < $Ouvrants[0]["Délai_ouverture"]){
                      $delais_ouverture = $Ouvrants[0]["Délai_ouverture"] ;
                    }                   
                    if ($alerte == 0){
                      $alerte = $Ouvrants[0]["Alerte"];
                    }
                    break;
                  }elseif ($ouvert == 0){
                    $ouvrant_valuedate =  new DateTime(cmd::ById(trim($Ouvrants[0]["Commande"], "#"))->getValueDate()); // Exemple d'heure de début
                    if ($delais_fermeture > $Ouvrants[0]["Délai_fermeture"]){
                      $delais_fermeture = $Ouvrants[0]["Délai_fermeture"] ;
                    }  
                    if ($alerte == 0){
                      $alerte = $Ouvrants[0]["Alerte"];
                    }                 
                  }
                }             

                  
                  $interval = $currentTime->diff($ouvrant_valuedate);
                  $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
              
                    if ($ouvert){ 
                    redo:
                      if($minutes >=  $delais_ouverture && $equipementsInfos[$eqLogic_id]->Ouvrants == 0){
                        //if ($minutes > $delais_ouverture + 2 ){
                        // $equipementsInfos[$eqLogic_id]->Ouvrants == 1;
                        //  goto redo;
                        //}
                        
                        if($equipementsInfos[$eqLogic_id]->action_en_cours != 'Arrêt' || $start){
                          $equipementsInfos[$eqLogic_id]->Ouvrants = 1; 
                          $execute_action = false;
                      
                          planification::add_log("debug","Mise en mode arrêt suite à l'ouverture d'un ouvrant",$eqLogic);
                          
                          if($equipementsInfos[$eqLogic_id]->mode_fonctionnement == "Auto"){
                            cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["arret"])->execCmd(array('mode'=>"auto"));
                          }else{
                            cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["arret"])->execCmd();
                          }                    
                        
                          if($alerte){
                            planification::add_log("error","Un des ouvrants est ouvert: Arrêt de la PAC",$eqLogic,"aa");
                          }
                        }
                        
                      }
                    }else if ($minutes >= $delais_fermeture && $equipementsInfos[$eqLogic_id]->Ouvrants == 1){
                      planification::add_log("debug","Tous les ouvrants sont fermé: redemarrage de la PAC",$eqLogic);
                      $equipementsInfos[$eqLogic_id]->Ouvrants = 0;
                      
                      if($alerte){
                        planification::add_log("error","Tous les ouvrants sont fermé: redemarrage de la PAC",$eqLogic,"aa");
                      }
                    }
                      
                    
                
              }
              //gestion des conditions de changement de planification
              if ($equipementsInfos[$eqLogic_id]->Automatisation_Gestion_planifications != ""){
                $arr["select"] = "";
                $arr["Id_planification"] = "";
                foreach($equipementsInfos[$eqLogic_id]->Automatisation_Gestion_planifications as $Gestion_planifications){
                  //planification::add_log("debug",$Gestion_planifications[0]["Nom"],$eqLogic,"aa"); 
                  //planification::add_log("debug",$Gestion_planifications[0]["Id"],$eqLogic,"aa"); 
                  //planification::add_log("debug",$Gestion_planifications[0]["Conditions"],$eqLogic,"aa"); 
                  //planification::add_log("debug",$Gestion_planifications[0]["Stop"],$eqLogic,"aa"); 

                  //planification::add_log("debug",planification::TestExpression($Gestion_planifications[0]["Conditions"]),$eqLogic,"aa");
                  if(planification::TestExpression($Gestion_planifications[0]["Conditions"])){
                  
                    $arr["select"] = $Gestion_planifications[0]["Nom"];
                    $arr["Id_planification"] = $Gestion_planifications[0]["Id"];
                    if($Gestion_planifications[0]["Stop"]){
                      break;
                    }
                  }
                }
              
                if($equipementsInfos[$eqLogic_id]->Id_planification_en_cours != $arr["Id_planification"] && $arr["Id_planification"] !=''){
                  planification::add_log("debug",'Modification planification: ' .$Gestion_planifications[0]["Nom"],$eqLogic);
                  cmd::ById($equipementsInfos[$eqLogic_id]->cmd_ids["set_planification"])->execCmd($arr);
                  
                }

              }
            
            
            
            






          }

          if ($execute_action){
             planification::add_log('debug', 'Get_actions_planification' ,$eqLogic);
            $eqLogic->Get_actions_planification($equipementsInfos[$eqLogic_id]);
            planification::add_log('debug', cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd() ,$eqLogic);
            if(cmd::byEqLogicIdAndLogicalId($eqLogic_id,'heure_fin')->execCmd() == ''){
              planification::add_log("debug","eqLogic sans action suivante ",$eqLogic);
              
              $equipementsInfos[$eqLogic_id]->eqLogic_sans_action_suivante = true;
            }
          }
        }
      }
      $last_communication_minute = $currentMinute;
      
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