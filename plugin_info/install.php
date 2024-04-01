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
	try{
		
		$eqLogics=planification::byType('planification');   
		$planifications_new='';   
		foreach ($eqLogics as $eqLogic) {
			$type_équipement = $eqLogic->getConfiguration('type','');
			rename('/../core/template/images/chauffage', '/../core/template/images/Chauffage');
			rename('/../core/template/images/pac', '/../core/template/images/PAC');
			rename('/../core/template/images/poele', '/../core/template/images/Poele');
			rename('/../core/template/images/prise', '/../core/template/images/Prise');
			rename('/../core/template/images/thermostat', '/../core/template/images/Thermostat');
			rename('/../core/template/images/volet', '/../core/template/images/Volet');
			
			rename('/../core/template/chauffage.html', '/../core/template/Chauffage.html');
			rename('/../core/template/pac.html', '/../core/template/images/PAC.html');
			rename('/../core/template/poele.html', '/../core/template/images/Poele.html');
			rename('/../core/template/prise.html', '/../core/template/images/Prise.html');
			rename('/../core/template/volet.html', '/../core/template/images/Volet.html');
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
