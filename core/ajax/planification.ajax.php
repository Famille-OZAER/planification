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
        $cmds=$eqLogic-> searchCmdByConfiguration("Couleur");
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
    if (init('action') == 'Remove_log') {
      if (!config::byKey('UseLogByeqLogic', 'planification')){
        $liste_log=log::liste("planification_");
        foreach($liste_log as $log){
          log::remove($log);
        }
      }

      ajax::success($res);
    }
    if (init('action') == 'Santé'){
      $recherche='chauffage';
      $div="";
      suivant:
      $eqLogic_ids = array();
      $eqLogics = planification::byTypeAndSearchConfiguration(  'planification',  $recherche);
      foreach ($eqLogics as $eqLogic) {
        array_push($eqLogic_ids, $eqLogic->getId());
      }
      sort($eqLogic_ids);
      if (count($eqLogics) > 0){
        switch ($recherche) {
          case 'chauffage':
            $div .= '<tr class="santé_titre"><td colspan="10"><h3><span class="fa jeedom-pilote-conf"> Mes Chauffages</span></h3></td></tr>';
            break;
          case 'PAC':
            $div .= '<tr class="santé_titre"><td colspan="10"><h3><span class="fa jeedom-feu"> Mes pompes à chaleur</span></h3></td></tr>';
            break;
          case 'poele';
            $div .= '<tr class="santé_titre"><td colspan="10"><h3><span class="fa jeedom-feu"> Mes poêles à granules</span></h3></td></tr>';
            break;
          case 'volet';
            $div .= '<tr class="santé_titre"><td colspan="10"><h3><span class="fa jeedom-volet-ferme"> Mes volets</span></h3></td></tr>';
            break;
          case 'prise';
            $div .= '<tr class="santé_titre"><td colspan="10"><h3><span class="fa jeedom-prise"> Mes prises</span></h3></td></tr>';
            break;
          case 'perso';
            $div .= '<tr class="santé_titre"><td colspan="10"><h3><span class="fas fa-cogs"> Mes équipements perso</span></h3></td></tr>';
            break;
        }
      }

      foreach ($eqLogic_ids as $eqLogic_id) {
        $eqLogic=planification::byId($eqLogic_id);
        $type_eqLogic = strtolower($eqLogic->getConfiguration('type'));
        $image=$eqLogic->getConfiguration("chemin_image","none");
        if ( $image == "none"){
          if (file_exists(dirname(__FILE__) . '/../../core/img/' . $type_eqLogic . '.png')) {
            $img = '<img src="plugins/planification/core/img/' . $type_eqLogic . '.png" height="55" width="55"/>';
          } else {
            $img = "";
          } 
        }else{
          $img = '<img src="' . $image . '" height="55" width="55"/>';
        }
        if($eqLogic->getObject()==""){
          $Object = "Aucun";
        }else{
          $Object = $eqLogic->getObject()->getName();
        }
        $div .= '<tr class="santé ' .$recherche .'">';
        $div .= '<td><span class="label id">' . $eqLogic->getId() . '</span></td>';
        $div .= '<td>' . $img . '</td>';
        $div .= '<td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $Object . " " . $eqLogic->getName() . '</a></td>';

        if ($eqLogic->getIsEnable() ) {
          $div .=  '<td><span class="label label-success" style="font-size : 1em;"><i class="fas fa-check"></i></span></td>';
        }else{
          $div .=  '<td><span class="label label-danger" style="font-size : 1em;"><i class="fas fa-times"></i></span></td>';
          $div .= '<td><span></span></td>';
          $div .= '<td></td>';
          $div .= '<td></td>';
          $div .= '<td></td>';
          $div .= '<td></td>';
          $div .= '<td></td>';
          $div .= '</tr>';
          continue;
        }
        $valeur=$eqLogic->getCmd(null,'mode_fonctionnement')->execCmd();;
        if ($valeur == "Auto"){
          $div .= '<td><span class="label label-success" style="font-size : 1em;">'. $valeur.'</span></td>';
        }elseif ($valeur == "Manuel"){
          $cmd_auto= $eqLogic->getCmd(null,'auto');
          $div .= '<td><span title = "Remise en auto" class="label label-warning cursor manuel" cmd_id='. $cmd_auto->getId().' style="font-size : 1em;">'. $valeur.'</span></td>';
        }else{
          $div .= '<td><span class="label label-danger" style="font-size : 1em;">Inconnu</span></td>';
        }



        $valeur=$eqLogic->getCmd(null,'planification_en_cours')->execCmd();
        $div .= '<td><span class="label" style="font-size : 1em;">'. $valeur.'</span></td>';

        $valeur=$eqLogic->getCmd(null,'action_en_cours')->execCmd();
        $div .= '<td><span class="label" style="font-size : 1em;">'. $valeur.'</span></td>';



        $valeur = $eqLogic->getCmd(null,'heure_fin')->execCmd();
        if ($valeur != ""){
          $valeur = strtotime($valeur);

          if(date('d-m-Y',$valeur) != date('d-m-Y') ){
            $valeur = date('d-m-Y H:i',$valeur);
          }else{
            $valeur = date('H:i',$valeur);
          }
          $div .= '<td><span class="label" style="font-size : 1em;">'. $valeur.'</span></td>';
        }else{
          $div .= '<td></td>';
        }





        $valeur=$eqLogic->getCmd(null,'action_suivante')->execCmd();
        if ($valeur != ""){
          $div .= '<td><span class="label" style="font-size : 1em;">'. $valeur.'</span></td>';
        }else{
          $valeur = $eqLogic->getCmd(null,'heure_fin')->execCmd();
          if ($valeur != ""){
            $div .= '<td><span class="label" style="font-size : 1em;">Remise en Auto</span></td>';
          }else{
            $div .= '<td></td>';
          }


        }


        $valeur=$eqLogic->getCmd(null,'info')->execCmd();
        $div .= '<td><span class="label" style="font-size : 1em;">'. $valeur.'</span></td>';


        $div .= '</tr>';
      }
      switch ($recherche) {
        case 'chauffage':
          $recherche = 'PAC';
          goto suivant;
        case 'PAC':
          $recherche = 'poele';
          goto suivant;
        case 'poele';
          $recherche = 'volet';
          goto suivant;
        case 'volet';
          $recherche = 'prise';
          goto suivant;
        case 'prise';
          $recherche = 'perso';
          goto suivant;
      }
      ajax::success( $div);
    }
    if (init('action') == 'Santé1') {
      $eqLogics=eqLogic::byType('planification');
      $div="";
      foreach ($eqLogics as $eqLogic){
        $type_eqLogic = strtolower($eqLogic->getConfiguration('type'));
        $image=$eqLogic->getConfiguration("chemin_image","none");
        if ( $image == "none"){
          if (file_exists(dirname(__FILE__) . '/../../core/img/' . $type_eqLogic . '.png')) {
            $img = '<img src="plugins/planification/core/img/' . $type_eqLogic . '.png" height="55" width="55"/>';
          } else {
            $img = "";
          } 
        }else{

          $img = '<img src="' . $image . '" height="55" width="55"/>';

        }

        $div .= '<tr>';
        $div .= '<td>' . $img . '</td>';
        $div .= '<td><span class="label id">' . $eqLogic->getId() . '</span></td>';


        if($eqLogic->getObject()==""){
          $Object = "Aucun";
        }else{
          $Object = $eqLogic->getObject()->getName();
        }
        $div .= '<td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $Object . " " . $eqLogic->getName() . '</a></td>';

        if ($eqLogic->getIsEnable() ) {
          $div .=  '<td><span class="label label-success" style="height:21px;font-size : 1em;"><i class="fas fa-check"></i></span></td>';
        }else{
          $div .=  '<td><span class="label label-danger" style="height:21px;font-size : 1em;"><i class="fas fa-times"></i></span></td>';
        }
        $cmd= $eqLogic->getCmd(null,'mode_fonctionnement');
        if (is_object($cmd)){
          $valeur=$cmd->execCmd();;
          if ($valeur == "Auto"){
            $div .= '<td><span class="label label-success" style="height:21px;font-size : 1em;">'. $valeur.'</span></td>';
          }elseif ($valeur == "Manuel"){
            $cmd_auto= $eqLogic->getCmd(null,'auto');
            $div .= '<td><span class="label label-warning cursor manuel" cmd_id='. $cmd_auto->getId().' style="height:21px;font-size : 1em;">'. $valeur.'</span></td>';
          }else{
            $div .= '<td><span class="label label-danger" style="height:21px;font-size : 1em;">Inconnu</span></td>';
          }
        }else{
          $div .= '<td><span class="label label-danger" style="font-size : 1em;">Inconnu</span></td>';
        }

        $cmd= $eqLogic->getCmd(null,'planification_en_cours');
        if (is_object($cmd)){
          $valeur=$cmd->execCmd();
          $div .= '<td><span class="label" style="font-size : 1em;">'. $valeur.'</span></td>';
        }else{
          $div .= '<td><span class="label label-danger" style="font-size : 1em;">Inconnu</span></td>';
        }

        $cmd= $eqLogic->getCmd(null,'action_en_cours');
        if (is_object($cmd)){
          $valeur=$cmd->execCmd();
          $div .= '<td><span class="label" style="font-size : 1em;">'. $valeur.'</span></td>';
        }else{
          $div .= '<td><span class="label label-danger" style="font-size : 1em;">Inconnu</span></td>';
        }
        $cmd= $eqLogic->getCmd(null,'heure_fin');
        if (is_object($cmd)){
          $valeur = $cmd->execCmd();
          if ($valeur != ""){
            $valeur = strtotime($cmd->execCmd());

            if(date('d-m-Y',$valeur) != date('d-m-Y') ){
              $valeur = date('d-m-Y H:i',$valeur);
            }else{
              $valeur = date('H:i',$valeur);
            }
            $div .= '<td><span class="label" style="font-size : 1em;">'. $valeur.'</span></td>';
          }else{
            $div .= '<td><span class="label label-danger" style="font-size : 1em;">Inconnu</span></td>';
          }



        }else{
          $div .= '<td><span class="label label-danger" style="font-size : 1em;">Inconnu</span></td>';
        }
        $cmd= $eqLogic->getCmd(null,'action_suivante');
        if (is_object($cmd)){
          $valeur=$cmd->execCmd();
          if ($valeur != ""){
            $div .= '<td><span class="label" style="font-size : 1em;">'. $valeur.'</span></td>';
          }else{
            $cmd= $eqLogic->getCmd(null,'heure_fin');
            if (is_object($cmd)){
              $valeur = $cmd->execCmd();
              if ($valeur != ""){
                $div .= '<td><span class="label" style="font-size : 1em;">Remise en Auto</span></td>';
              }else{
                $div .= '<td><span class="label label-danger" style="font-size : 1em;">Aucune</span></td>';
              }
            }

          }
        }else{
          $div .= '<td><span class="label label-danger" style="font-size : 1em;">Inconnu</span></td>';
        }
        $cmd= $eqLogic->getCmd(null,'info');
        if (is_object($cmd)){
          $valeur=$cmd->execCmd();
          $div .= '<td><span class="label" style="font-size : 1em;">'. $valeur.'</span></td>';
        }else{
          $div .= '<td><span class="label label-danger" style="font-size : 1em;">Inconnu</span></td>';
        }

        $div .= '</tr>';
      }

      ajax::success( $div);
    }
    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
  } catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
  }