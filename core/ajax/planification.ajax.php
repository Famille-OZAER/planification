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

   

    if (init('action') == 'Enregistrer_planifications') {//OK
		$dossier = dirname(__FILE__) . '/../../planifications/';
        if (!is_dir($dossier)) mkdir($dossier, 0755, true);
        $nom_fichier_json=dirname(__FILE__) ."/../../planifications/" . init('id') . ".json";
        $fichier = fopen( $nom_fichier_json, 'w');
        if(init('planifications')!=""){
            fwrite($fichier, json_encode(init('planifications')));
        }else{
            unlink ($nom_fichier_json) ;
        }
        ajax::success();
    }

    if (init('action') == 'Recup_planification') {//OK
		$dossier = dirname(__FILE__) . '/../../planifications/';
        if (!is_dir($dossier)) mkdir($dossier, 0755, true);
        $nom_fichier=dirname(__FILE__) ."/../../planifications/" . init('id') . ".json";
        $res="";
        if(file_exists ( $nom_fichier ) ){
            $res=file_get_contents ($nom_fichier);
        }
        ajax::success($res);
    }
      	
	if (init('action') == 'Importer_commandes_eqlogic') {
		$planification = planification::byId(init('id'));
		if (!is_object($planification)) {
			throw new Exception(__('Equipement planification introuvable : ', __FILE__) . init('id'));
		}
		$planification->Importer_commandes_eqlogic(init('eqLogic_id'));
		ajax::success();
	}
  
  	if (init('action') == 'Recup_select') {
      $planification = planification::byId(init('eqLogic_id'));
      if (!is_object($planification)) {
			throw new Exception(__('Equipement planification introuvable : ', __FILE__) . init('id'));
		}
      $res=$planification->Recup_select(init("type"),init('eqLogic_id'));
      ajax::success($res);
    }
    if (init('action') == 'Recup_liste_commandes_planification') {
        $eqLogic = planification::byId(init('eqLogic_id'));
        if (!is_object($eqLogic)) {
              throw new Exception(__('Equipement planification introuvable : ', __FILE__) . init('id'));
          }
        $res=$eqLogic->Recup_liste_commandes_planification(init('eqLogic_id'));
        ajax::success($res);
    }
    if (init('action') == 'Ajout_equipement') {
        $res=planification::Ajout_equipement(init('nom'),init('type'));
        ajax::success( $res);
    }
    if (init('action') == 'Set_widget_cache') {
        planification::Set_widget_cache(init('id'),init('page'));
        ajax::success();
    }
    if (init('action') == 'Get_widget_cache') {
        $res=planification::Get_widget_cache(init('id'));
        ajax::success($res);
    }
	
	
	
    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}