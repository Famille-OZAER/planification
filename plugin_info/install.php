<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function planification_install() {
	$folderPath = dirname(__FILE__) . '/../../planification/planifications/';
	if (!is_dir($folderPath)) mkdir($folderPath, 0755, true);
}

function planification_update() {
	$cron = cron::byClassAndFunction('planification', 'pull');
	if (is_object($cron)) {
		$cron->remove();
	}
	//resave eqs for new cmd:
	try{
		$eqLogics = eqLogic::byType('planification');
		foreach ($eqLogics as $eqLogic){
			
			$commandes=$eqLogic->getConfiguration("commandes_planification","");
			if (is_array($commandes)){
				$nom_fichier=dirname(__FILE__) ."/../../../" . $eqLogic->getId() . ".json";
				if(file_exists ( $nom_fichier ) ){
					$json=file_get_contents ($nom_fichier);
				}
				log::add('planification', 'debug', 'fichier json: '.$nom_fichier);
				log::add('planification', 'debug', 'json debut: '.$json);	
				$nouvelles_cmds=[];
				if (is_array($commandes)){
					foreach ($commandes as $commande){
						$id=$commande["Id"];
						$nom=$commande["nom"];
						$commande['Id']=$commande["nom"];
						$json=str_replace($id,$nom,$json);
						array_push($nouvelles_cmds,$commande);
					}
				}
				//$fichier = fopen( $nom_fichier, 'w');
				//fwrite($fichier, $json);
				log::add('planification', 'debug', 'json fin: '.$json);
				//$eqLogic->setConfiguration("commandes_planification", "");
			}


			$cmd_temperature_consigne_par_defaut=cmd::byEqLogicIdAndLogicalId($eqLogic->getId(),'temperature_consigne_par_defaut');
			if (is_object($cmd_temperature_consigne_par_defaut)){
				$temperature_consigne_par_defaut=20;
				$temperature_consigne_par_defaut=$cmd_temperature_consigne_par_defaut->execCmd();
				$temperature_consigne_par_defaut->remove();
				$eqLogic->setConfiguration("temperature_consigne_par_defaut",$temperature_consigne_par_defaut);
			}
			$eqLogic->save();
		}
	}
	catch (Exception $e){
		$e = print_r($e, 1);
		log::add('planification', 'error', 'planification_update ERREUR: '.$e);
	}

}


function planification_remove() {
	$cron = cron::byClassAndFunction('planification', 'pull');
	if (is_object($cron)) {
		$cron->remove();
	}
}

?>
