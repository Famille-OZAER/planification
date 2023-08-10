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
	planification::deamon_start();
}

function planification_update() {
	log::add('planification', 'debug', 'planification_update');
	planification::deamon_stop();
	$cron = cron::byClassAndFunction('planification', 'pull');
	foreach ($crons as $cron){
		$options_cron=$cron->getOption();
		if($options_cron["eqLogic_Id"] == $eqLogic->getId()){
			$cron->remove();
		}
	}	
	//resave eqs for new cmd:
	try{
		$eqLogics = eqLogic::byType('planification');
		foreach ($eqLogics as $eqLogic){
			$eqLogic->postSave();
			$eqLogic->save();
		}
	}
	catch (Exception $e){
		$e = print_r($e, 1);
		log::add('planification', 'error', 'planification_update ERREUR: '.$e);
	}
	planification::deamon_start();
}


function planification_remove() {
	$cron = cron::byClassAndFunction('planification', 'pull');
	if (is_object($cron)) {
		$cron->remove();
	}
	planification::deamon_stop();
}

?>
