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

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
    include_file('desktop', 'planification', 'js', 'planification');
    $type_eqlogic = $_GET['type'];

    //get all exported programs:
    $dossier = dirname(__FILE__) . '/../../programmations/';
    $commande = 'ls '.$dossier;
    $res = exec($commande, $fichiers, $return_var);
    $div = '<div class="col-sm-12" style="padding-top:5px;">';
        $div .= '<div class="form-group">';
            $div .= '<div class="col-sm-1">';
                $div .= '<label>{{Programme}}</label>';
            $div .= '</div>';
			$div .= '<div class="col-sm-1">';
                $div .= '<label>{{Type}}</label>';
            $div .= '</div>';
			$div .= '<div class="col-sm-3">';
                $div .= '<label>{{Date d\'exportation}}</label>';
            $div .= '</div>';
            $div .= '<div class="col-sm-3">';
                $div .= '<label>{{Actions}}</label>';
            $div .= '</div>';
        $div .= '</div>';
    $div .= '</div>';
    echo $div;

    foreach ($fichiers as $fichier)
    {
        $file = file_get_contents($dossier.$fichier);
		$_json = json_decode($file);
      	$nom_programme = $_json->nom_programme;
      	$mois = array("Janvier", "Fécrier", "Mars", "Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
      	$date=substr(explode("_",$fichier)[0],0,2) . " " . $mois[intval(substr(explode("_",$fichier)[0],2,2))]. " " . substr(explode("_",$fichier)[0],4,4) . " " .substr(explode("_",$fichier)[1],0,2) . ":" . substr(explode("_",$fichier)[1],2,2). ":" . substr(explode("_",$fichier)[1],4,2);
      	$type = $_json->type_programme;
        $div = '<div class="Programme col-sm-12" style="display:;padding-top:5px">';
            $div .= '<div class="form-group">';
                $div .= '<div class="col-sm-1">';
                    $div .= '<label>'.$nom_programme.'</label>';
                $div .= '</div>';
      			$div .= '<div class="col-sm-1">';
                    $div .= '<label>'.$type.'</label>';
                $div .= '</div>';
      			$div .= '<div class="col-sm-3">';
                    $div .= '<label>'.$date.'</label>';
                $div .= '</div>';
                $div .= '<div class="col-sm-3">';
      				if ($type == $type_eqlogic){
						$div .= '<a class="btn btn-success bt_Importer_Programme" nom_fichier="'.$fichier.'"><i class="fa fa-plus-circle"></i> {{Importer}}</a>';
                    }else{
						$div .= '<label style="font-size:12px;margin:0px;padding-top:7px;padding-right:8px;padding-bottom:7px;padding-left:8px"><i class="fa fa-plus-circle"></i> {{Importer}}</label>';
                    }
       				$div .= '  ';
                    $div .= '<a class="btn btn-success bt_Telecharger_Programme" nom_fichier="'.$fichier.'"><i class="fa fa-download"></i></a>';
                    $div .= '  ';
                    $div .= '<a class="btn btn-success bt_Supprimer_Programme" nom_fichier="'.$fichier.'"><i class="fa divers-slightly"></i></a>';
                $div .= '</div>';
            $div .= '</div>';
        $div .= '</div>';
        echo $div;
    }
?>
