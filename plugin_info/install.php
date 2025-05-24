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
	rmdir ($dir);
	log::add('planification_update', 'debug',"scan:". $dir);

    foreach($root as $value){
		
        if($value === '.' || $value === '..') {continue;}

        if(is_file($dir.'/'.$value)) {
			$result[]=$dir.$value;
			//log::add('planification_update', 'debug', $dir.'/'.$value);
			//unlink ($dir.'/'.$value); 
	
			continue;
		}else{
			
			foreach(find_all_files($dir.'/'.$value) as $value){
				
			//	rmdir ($dir);
			//$result[]=$value;
        	}
		}
    }

    return $result;

}
function planification_update() {
	planification::deamon_stop();
	//find_all_files("/var/www/html/plugins/planification/core/template/dashboard");
	//rmdir -p ("/var/www/html/plugins/planification/core/template/dashboard");
	//log::add('planification_update', 'debug', sizeof(scandir('/var/www/html/plugins/planification/core/template/dashboard')));
	//log::add('planification_update', 'debug', glob('/var/www/html/plugins/planification/core/template/dashboard'));
	//rm ('-r /var/www/html/plugins/planification/core/template/dashboard');
	//glob('/var/www/html/plugins/planification/core/template/dashboard'."*")
	//sizeof(scandir('/var/www/html/plugins/planification/core/template/dashboard'))
	/*unlink ("/var/www/html/plugins/planification/core/template/dashboard/chauffage.html"); 
	unlink ("/var/www/html/plugins/planification/core/template/dashboard/pac.html");
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
	rmdir ("/var/www/html/plugins/planification/core/template/dashboard/images/prise");
	rmdir ("/var/www/html/plugins/planification/core/template/dashboard/images/thermostat");
	rmdir ("/var/www/html/plugins/planification/core/template/dashboard/images/volet");
	*/

	try{
		
		$eqLogics=planification::byType('planification');   
		$planifications_new='';   
		foreach ($eqLogics as $eqLogic) {
			$eqLogic->save();			
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
