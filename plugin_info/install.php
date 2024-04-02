<?php

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function planification_install() {
	log::add('planification', 'debug', 'planification_install');
	$folderPath = dirname(__FILE__) . '/../../planification/planifications/';
	if (!is_dir($folderPath)) mkdir($folderPath, 0755, true);
	planification::deamon_start();
}

function planification_update() {
	log::add('planification', 'debug', 'planification_update');
	planification::deamon_stop();
	if (intval($version_arr[0]) >= 4 && intval($version_arr[1]) < 4){
        $file="/var/www/html/desktop/custom/custom.js";
	    $read=file($file);
	    $existe=false;
        $write_tmp="";
        foreach($read as $line){
          $write_tmp .= $line;
          if(strpos($line, 'flatpickr v4.6.13')!==FALSE){
            $existe=true;
          } 
        }
        
        if (!$existe){
          $file2="/var/www/html/plugins/planification/3rdparty/flatpickr/flatpickr.min.js";
	        $read2=file($file2);
	       foreach($read2 as $line2){
            $write_tmp .= $line2;
          
          }
            copy($file, $file.".bak");
            $write=fopen($file , 'w+');
            fwrite ( $write ,  $write_tmp);
          
             fclose($write);
        
        }

        $file="/var/www/html/desktop/custom/custom.css";
       
	      $read=file($file);
	      $existe=false;
        $write_tmp="";
        foreach($read as $line){
          $write_tmp .= $line;
          
          if(strpos($line, '.flatpickr-calendar {')!==FALSE){
         
            $existe=true;
          } 
        }
        
        if (!$existe){
          $file2="/var/www/html/plugins/planification/3rdparty/flatpickr/flatpickr.dark.css";
	        $read2=file($file2);
	       foreach($read2 as $line2){
            $write_tmp .= $line2;
          
          }
            copy($file, $file.".bak");
            $write=fopen($file , 'w+');
            fwrite ( $write ,  $write_tmp);
          
             fclose($write);
        
        }
      }

	try{
		
		$eqLogics=planification::byType('planification');   
		$planifications_new='';   
		foreach ($eqLogics as $eqLogic) {
			$type_équipement = $eqLogic->getConfiguration('type','');
			
			if($type_équipement !=""){
				$eqLogic->setConfiguration('type', '');
				$eqLogic->setConfiguration('Type_équipement', $type_équipement);
				$eqLogic->save();
				
			}
			$type_équipement = $eqLogic->getConfiguration('Type_équipement','');
			switch (strtolower($type_équipement)){
				case 'volet':
					$eqLogic->setConfiguration('Type_équipement', "Volet");
					$eqLogic->save();
					break;
				case 'pac':
					$eqLogic->setConfiguration('Type_équipement', "PAC");
					$eqLogic->save();
					break;
				case 'poele':
					$eqLogic->setConfiguration('Type_équipement', "Poele");
					$eqLogic->save();
					break;
				case 'chauffage':
					$eqLogic->setConfiguration('Type_équipement', "Chauffage");
					$eqLogic->save();
					break;
				case "prise":
					$eqLogic->setConfiguration('Type_équipement', "Prise");
					$eqLogic->save();
					break;
				case "autre":
					$eqLogic->setConfiguration('Type_équipement', "Autre");
					$eqLogic->save();
					break;
			}  
			
		  	$planifications=$eqLogic->Recup_planifications(true,true);
  
			if($planifications !=[] && isset($planifications[0]['nom_planification'])){
				$planifications=$eqLogic->Recup_planifications(true,true);
				$planifications_new = '[{';
				$numéro_planification=0;
				foreach ($planifications as $planification) {
				if ($numéro_planification!=0){
					$planifications_new .=',';
				}
				$planifications_new .= '"'. $numéro_planification . '":';
				$planifications_new .='[';
				$planifications_new .='{"Nom":"'.$planification['nom_planification'] .'",';
				$planifications_new .='"Id":"'. $planification["Id"] . '",';
				foreach ($planification["semaine"] as $semaine) {
					if ($semaine['jour'] != "Lundi"){
						$planifications_new .=',';
					}
					$planifications_new .='"'. $semaine['jour'] .'":[{';
					$nb_période=0;
					foreach ($semaine["periodes"] as $periode) {
						if($nb_période>0){
							$planifications_new .='},{';
						}
						$planifications_new .='"Type":"' . $periode['Type_periode'] .'", "Début":"' . $periode['Debut_periode'] .'", "Id":"' . $periode['Id'] . '"';
						$nb_période +=1;
					}
					$planifications_new .='}]';
				}
			  $planifications_new .='}]';          
			  $numéro_planification +=1;
			}          
			$planifications_new .='}]';
			$nom_fichier_source = dirname(__FILE__) ."/../planifications/" . $eqLogic->getId() . ".json"; 
			$nom_fichier_cible =  dirname(__FILE__) ."/../planifications/" . $eqLogic->getId() . "_old.json"; 
			planification::add_log('debug',$planifications_new);  
			
			copy( $nom_fichier_source , $nom_fichier_cible);
			$fichier = fopen($nom_fichier_source, 'w');
			fwrite($fichier, $planifications_new);
			
			}
		}
	}
	catch (Exception $e){
//$e = print_r($e, 1);
		log::add('planification', 'error', 'planification_update ERREUR: '.$e);
	}
	planification::deamon_start();
}


function planification_remove() {
	
	planification::deamon_stop();
}

?>
