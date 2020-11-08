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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    ajax::init();

    if (init('action') == 'exporter_programme') {//OK
		$dossier = dirname(__FILE__) . '/../../programmations/';
        if (!is_dir($dossier)) mkdir($dossier, 0755, true);
		$now = date("dmY_His");
        $fichier = fopen(dirname(__FILE__) . '/../../programmations/'.$now.'_'.init('nom').'.json', 'w');
        $res = fwrite($fichier, json_encode(init('programmation')));
		log::add('planification', 'debug', 'ajax exporter_programme: '.dirname(__FILE__) . '/../../programmations/'.$now.'_'.init('nom').'.json');
        ajax::success();
    }

    if (init('action') == 'supprimer_programme') {//OK
        log::add('planification', 'debug', 'ajax supprimer_programme: '.dirname(__FILE__) . '/../../programmations/'.init('nom_fichier'));
        @unlink(dirname(__FILE__) . '/../../programmations/'.init('nom_fichier'));
        ajax::success();
    }
	
	if (init('action') == 'importer_eqlogic') {
		$planification = planification::byId(init('id'));
		if (!is_object($planification)) {
			throw new Exception(__('Equipement planification introuvable : ', __FILE__) . init('id'));
		}
		$planification->importer_eqlogic(init('eqLogic_id'));
		ajax::success();
	}
  
  	if (init('action') == 'Recup_liste_mode_programme') {
      $planification = planification::byId(init('eqLogic_id'));
      if (!is_object($planification)) {
			throw new Exception(__('Equipement planification introuvable : ', __FILE__) . init('id'));
		}
      $res=$planification->Recup_liste_mode_programme(init('eqLogic_id'));
      ajax::success($res);
  	}
  
	if (init('action') == 'Sauvegarde_programmations') {
      $planification = planification::byId(init('eqLogic_id'));
	  $planification->setConfiguration("programmations",init('programmations'));
      $planification->save();
      if (!is_object($planification)) {
			throw new Exception(__('Equipement planification introuvable : ', __FILE__) . init('id'));
		}
      ajax::success();
  	}

	if (init('action') == "Verificarion_programme_avant_suppression_commande"){
		 $planification = planification::byId(init('eqLogic_id'));
		  if (!is_object($planification)) {
			throw new Exception(__('Equipement planification introuvable : ', __FILE__) . init('id'));
		}
      $res=$planification->Verificarion_programme_avant_suppression_commande(init('eqLogic_id'),init('cmd_id'));
      ajax::success($res);
  	}
	
	
    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}

