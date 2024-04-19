<?php

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function planification_install() {
	log::add('planification', 'debug', 'planification_install');
	$folderPath = dirname(__FILE__) . '/../../planification/planifications/';
	if (!is_dir($folderPath)) mkdir($folderPath, 0755, true);
	planification::deamon_start();
}
function find_all_files($dir){

    $root = scandir($dir);

    foreach($root as $value)

    {

        if($value === '.' || $value === '..') {continue;}

        if(is_file("$dir/$value")) {
			$result[]="$dir/$value";
			log::add('planification_update', 'debug', $dir/$value);
			continue;
		}

        foreach(find_all_files("$dir/$value") as $value)

        {

            $result[]=$value;
			log::add('planification_update', 'debug', $value);
        }

    }

    return $result;

}
function planification_update() {
	
	planification::deamon_stop();
	find_all_files("/var/www/html/plugins/planification/core/template/dashboard/");
	unlink ("/var/www/html/plugins/planification/core/template/dashboard/chauffage.html"); 
	unlink ("/var/www/html/plugins/planification/core/template/dashboard/pac.html");
	unlink ("/var/www/html/plugins/planification/core/template/dashboard/poele.html");
	unlink ("/var/www/html/plugins/planification/core/template/dashboard/prise.html");
	unlink ("/var/www/html/plugins/planification/core/template/dashboard/thermostat.html");
	unlink ("/var/www/html/plugins/planification/core/template/dashboard/volet.html"); 
	unlink ("/var/www/html/plugins/planification/core/template/dashboard/autre.html");
	$scandir = scandir("/var/www/html/plugins/planification/core/template/dashboard/images/chauffage/");
	foreach($scandir as $fichier){
		unlink ("/var/www/html/plugins/planification/core/template/dashboard/images/chauffage/" . $fichier); 
	}
	$scandir = scandir("/var/www/html/plugins/planification/core/template/dashboard/images/pac/");
	foreach($scandir as $fichier){
		unlink ("/var/www/html/plugins/planification/core/template/dashboard/images/pac/" . $fichier); 
	}
	$scandir = scandir("/var/www/html/plugins/planification/core/template/dashboard/images/poele/");
	foreach($scandir as $fichier){
		unlink ("/var/www/html/plugins/planification/core/template/dashboard/images/poele/" . $fichier); 
	}
	$scandir = scandir("/var/www/html/plugins/planification/core/template/dashboard/images/prise/");
	foreach($scandir as $fichier){
		unlink ("/var/www/html/plugins/planification/core/template/dashboard/images/prise/" . $fichier); 
	}
	$scandir = scandir("/var/www/html/plugins/planification/core/template/dashboard/images/thermostat/");
	foreach($scandir as $fichier){
		unlink ("/var/www/html/plugins/planification/core/template/dashboard/images/thermostat/" . $fichier); 
	}
	$scandir = scandir("/var/www/html/plugins/planification/core/template/dashboard/images/volet/");
	foreach($scandir as $fichier){
		unlink ("/var/www/html/plugins/planification/core/template/dashboard/images/volet/" . $fichier); 
	}
	rmdir ("/var/www/html/plugins/planification/core/template/dashboard/images/chauffage"); 
	rmdir ("/var/www/html/plugins/planification/core/template/dashboard/images/pac");
	rmdir ("/var/www/html/plugins/planification/core/template/dashboard/images/poele");
	rmdir ("/var/www/html/plugins/planification/core/template/dashboard/images/prise");
	rmdir ("/var/www/html/plugins/planification/core/template/dashboard/images/thermostat");
	rmdir ("/var/www/html/plugins/planification/core/template/dashboard/images/volet");
	
	try{
		
		$eqLogics=planification::byType('planification');   
		$planifications_new='';   
		foreach ($eqLogics as $eqLogic) {
			$planifications=$eqLogic->Recup_planifications(true,true);
  			if($planifications !=[] && isset($planifications[0]['nom_planification'])){
				$type_équipement = $eqLogic->getConfiguration('type','');
			
			if($type_équipement != ""){
				$eqLogic->setConfiguration('type', '');
				$eqLogic->setConfiguration('Type_équipement', $type_équipement);
				$eqLogic->save(true);
				
			}
			$type_équipement = $eqLogic->getConfiguration('Type_équipement','');
			switch (strtolower($type_équipement)){
				case 'volet':
					if ($type_équipement != "Volet"){
						$eqLogic->setConfiguration('Type_équipement', "Volet");
						$eqLogic->save(true);
					}
					break;
				case 'pac':
					if ($type_équipement != "PAC"){
						$eqLogic->setConfiguration('Type_équipement', "PAC");
						$eqLogic->save(true);
					}
					break;
				case 'poele':
					if ($type_équipement != "Poele"){
						$eqLogic->setConfiguration('Type_équipement', "Poele");
						$eqLogic->save(true);
					}
					break;
				case 'chauffage':
					if ($type_équipement != "Chauffage"){
						$eqLogic->setConfiguration('Type_équipement', "Chauffage");
						$eqLogic->save(true);
					}
					break;
				case "prise":
					if ($type_équipement != "Prise"){
						$eqLogic->setConfiguration('Type_équipement', "Prise");
						$eqLogic->save(true);
					}
					break;
				case "autre":
					if ($type_équipement != "Autre"){
						$eqLogic->setConfiguration('Type_équipement', "Autre");
						$eqLogic->save(true);
					}
					break;
				}  
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
		log::add('planification', 'error', 'planification_update ERREUR: '.$e);
	}
	planification::deamon_start();
}


function planification_remove() {
	
	planification::deamon_stop();
}

?>
