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
	throw new Exception('401 Unauthorized');
}
$plugin = plugin::byId('planification');
$eqLogics = planification::byType('planification');
?>

<table class="table table-condensed tablesorter">
	<thead>
		<tr>
			<th>{{Image}}</th>
			<th>{{ID}}</th>
			<th>{{Equipement}}</th>
			<th>{{Actif}}</th>
			<th>{{Mode}}</th>
			<th>{{Planification en cours}}</th>
            <th>{{Action en cours}}</th>
            <th>{{Heure Prochaine action}}</th>
            <th>{{Prochaine action}}</th>
            <th>{{Info}}</th>
		</tr>
	</thead>
	<tbody class="health_planification">
	 <?php
foreach ($eqLogics as $eqLogic) {
	$type_eqLogic = strtolower($eqLogic->getConfiguration('type'));

	if (file_exists(dirname(__FILE__) . '/../../core/img/' . $type_eqLogic . '.png')) {
		$img = '<img src="plugins/planification/core/img/' . $type_eqLogic . '.png" height="55" width="55"/>';
	} else {
		$img = $type_eqLogic;
	} 
    echo '<tr>';
	echo '<td>' . $img . '</td>';
    echo '<td><span class="label id">' . $eqLogic->getId() . '</span></td>';
    
					
    if($eqLogic->getObject()==""){
        $Object = "Aucun";
    }else{
        $Object = $eqLogic->getObject()->getName();
    }
    echo '<td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $Object . " " . $eqLogic->getName() . '</a></td>';
	
	if ($eqLogic->getIsEnable() ) {
		echo  '<td><span class="label label-success" style="font-size : 1em;"><i class="fas fa-check"></i></span></td>';
	}else{
        echo  '<td><span class="label label-danger" style="font-size : 1em;"><i class="fas fa-times"></i></span></td>';
    }
	$cmd= $eqLogic->getCmd(null,'mode_fonctionnement');
    if (is_object($cmd)){
        $valeur=$cmd->execCmd();;
        if ($valeur == "Auto"){
            echo '<td><span class="label label-success" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
        }elseif ($valeur == "Manuel"){
            $cmd_auto= $eqLogic->getCmd(null,'auto');
            echo '<td><span class="label label-warning cursor manuel" cmd_id='. $cmd_auto->getId().' style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
        }else{
            echo '<td><span class="label label-danger" style="font-size : 1em;">{{Inconnu}}</span></td>';
        }
    }else{
        echo '<td><span class="label label-danger" style="font-size : 1em;">{{Inconnu}}</span></td>';
    }

    $cmd= $eqLogic->getCmd(null,'planification_en_cours');
    if (is_object($cmd)){
        $valeur=$cmd->execCmd();
        echo '<td><span class="label" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
    }else{
        echo '<td><span class="label label-danger" style="font-size : 1em;">{{Inconnu}}</span></td>';
    }

    $cmd= $eqLogic->getCmd(null,'action_en_cours');
    if (is_object($cmd)){
        $valeur=$cmd->execCmd();
        echo '<td><span class="label" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
    }else{
        echo '<td><span class="label label-danger" style="font-size : 1em;">{{Inconnu}}</span></td>';
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
            echo '<td><span class="label" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
        }else{
            echo '<td><span class="label label-danger" style="font-size : 1em;">{{Inconnu}}</span></td>';
        }
        
        
      
    }else{
        echo '<td><span class="label label-danger" style="font-size : 1em;">{{Inconnu}}</span></td>';
    }
    $cmd= $eqLogic->getCmd(null,'action_suivante');
    if (is_object($cmd)){
        $valeur=$cmd->execCmd();
        if ($valeur != ""){
            echo '<td><span class="label" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
        }else{
            $cmd= $eqLogic->getCmd(null,'heure_fin');
            if (is_object($cmd)){
                $valeur = $cmd->execCmd();
                if ($valeur != ""){
                    echo '<td><span class="label" style="font-size : 1em;">{{Remise en Auto}}</span></td>';
                }else{
                    echo '<td><span class="label label-danger" style="font-size : 1em;">{{Aucune}}</span></td>';
                }
            }

        }
    }else{
        echo '<td><span class="label label-danger" style="font-size : 1em;">{{Inconnu}}</span></td>';
    }
    $cmd= $eqLogic->getCmd(null,'info');
    if (is_object($cmd)){
        $valeur=$cmd->execCmd();
        echo '<td><span class="label" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
    }else{
        echo '<td><span class="label label-danger" style="font-size : 1em;">{{Inconnu}}</span></td>';
    }

	echo '</tr>';
}
?>
<script>

creation_table()
function creation_table(){
    var table = $('.health_planification')
    table.empty() 
    console.clear()
    
    $.ajax({
        type: "POST",
        url: "plugins/planification/core/ajax/planification.ajax.php",
        data: {
            action: "Recup_eqLogic_ids",
        },
        dataType: 'json',
      
        async: false,
        error: function(request, status, error) { handleAjaxError(request, status, error) },
        success: function(data) {
           
            if (data.state != 'ok') {
                $('#div_alert').showAlert({
                    message: data.result,
                    level: 'danger'
                })
                return
            }
           
            
            $('.health_planification').append(data.result)
                
            $('.manuel').on('click', function() {
   
                jeedom.cmd.execute({id: $(this).attr("cmd_id")});
                setTimeout(() => {  creation_table(); }, 2000);
               
            })
           
        }

    })
    
}
</script>
	</tbody>
</table>
