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
    if (init('action') == 'Recup_select') {
        $eqLogic = planification::byId(init('eqLogic_id'));
        if (!is_object($eqLogic)) {
            throw new Exception(__('Impossible de trouver l\'équipement : ', __FILE__) . init('id'));
        }
        if (init("type") == "planifications"){
            $cmds=$eqLogic-> searchCmdByConfiguration("Type");
            $div ='<div class="select-selected expressionAttr #COULEUR#" id="#ID#" data-l1key = "couleur">';
            $div .='<span>#VALUE#</span></div>';
            
            $div .='<div class="select-items select-hide">';
            
            if ($cmds !=""){
                foreach ($cmds as $cmd) {
                    $div.='<div class="couleur-'.$cmd->getConfiguration("Couleur").'" id="'.$cmd->getID().'" value="'. $cmd->getName() . '">';
                    $div.='<span>'.$cmd->getName() .'</span>';
                    $div.='</div>';
                }
            }
            $div .='</div>'; 
        }else if(init("type") == "commandes"){
            $div ='<div class="select-selected commande cmdAttr #COULEUR#" data-l1key = "configuration" data-l2key = "Couleur">';
            $div .='<span>#VALUE#</span></div>';
            $div .='<div class="select-items select-hide">';
            $div .=  '<div class ="commande couleur-orange" value="orange">orange</div>';
            $div .=  '<div class ="commande couleur-jaune" value="jaune">jaune</div>';
            $div .=  '<div class ="commande couleur-vert" value="vert">vert</div>';
            $div .=  '<div class ="commande couleur-bleu" value="bleu">bleu</div>';
            $div .=  '<div class ="commande couleur-rouge" value="rouge">rouge</div>';
            $div .=  '<div class ="commande couleur-magenta" value="magenta">magenta</div>';
            $div .=  '<div class ="commande couleur-marron" value="marron">marron</div>';
            $div .=  '<div class ="commande couleur-violet" value="violet">violet</div>';	
            $div .='</div>';
            
        }
        
        ajax::success($div);
    }
    if (init('action') == 'Recup_liste_commandes_planification') {
        $eqLogic = planification::byId(init('eqLogic_id'));
        if (!is_object($eqLogic)) {
            throw new Exception(__('Equipement planification introuvable : ', __FILE__) . init('id'));
        }        
        $cmds=$eqLogic->searchCmdByConfiguration("Type");
        $res=[];
        
        if ($cmds !=""){
            foreach ($cmds as $cmd) {
                $cmd1["Id"] = $cmd->getID();
                $cmd1["Nom"] = $cmd->getName();
                $cmd1["couleur"] = $cmd->getConfiguration("Couleur");             
                array_push($res,$cmd1);
            }
        }
        ajax::success($res);
    }
    if (init('action') == 'Ajout_equipement') {
        $eqLogic = new planification();
        $eqLogic->setLogicalId(init('nom'));
        $eqLogic->setName(init('nom'));
        $eqLogic->setEqType_name('planification');
        $eqLogic->setIsVisible(0);
        $eqLogic->setIsEnable(1);
        $eqLogic->setConfiguration('type', init('type'));
        $eqLogic->save();
        $res=$eqLogic->getId();
        ajax::success( $res);
    }
    if (init('action') == 'Set_widget_cache') {
        $eqLogic=planification::byId(init('id'));
        $eqLogic->setCache('Page', init('page'));
        
        ajax::success();
    }	
    if (init('action') == 'Recup_infos_lever_coucher_soleil') {
        $res=planification::Recup_infos_lever_coucher_soleil(init('id'));
        ajax::success($res);
    }
    if (init('action') == 'Save_EqLogic') {
        $eqLogic = planification::byId(init('eqLogic_id'));
        $eqLogic->save(false);
        ajax::success();
    }
    if (init('action') == 'Copy_JSON') {
        $nom_fichier_source = dirname(__FILE__) ."/../../planifications/" . init('id_source') . ".json"; 
        $nom_fichier_cible =  dirname(__FILE__) ."/../../planifications/" . init('id_cible') . ".json"; 
        if(file_exists ( $nom_fichier_source ) ){
            copy( $nom_fichier_source , $nom_fichier_cible);
        }
       
       
        ajax::success();
    }
   
    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}