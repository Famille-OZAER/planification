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
	//import-export:
	$folderPath = dirname(__FILE__) . '/../../planification/programmations/';
	if (!is_dir($folderPath)) mkdir($folderPath, 0755, true);
}

function planification_update() {
	$cron = cron::byClassAndFunction('planification', 'pull');
	if (is_object($cron)) {
		$cron->remove();
	}
	
	//import-export:
	$folderPath = dirname(__FILE__) . '/../../planification/programmations/';
	if (!is_dir($folderPath)) mkdir($folderPath, 0755, true);
	
	//resave eqs for new cmd:
	try
	{
		$eqs = eqLogic::byType('planification');
		foreach ($eqs as $eq)
		{
			$eq->save();
		}
	}
	catch (Exception $e)
	{
		$e = print_r($e, 1);
		log::add('planification', 'error', 'planification_update ERREUR: '.$e);
	}

}


function planification_remove() {

}

?>