<style type="text/css">

  tr.santé{
  border: black;
  border-style: solid;
  border-width: 0.5px;
}
tr.santé>td{
  border: black;
  border-style: solid;
  border-width: 0.5px;
  text-align: center;
}
tr.santé_titre>td{
  border: black;
  border-style: solid;
  text-align: center;
  border-width: 0px;
  border-bottom-width: 0.5px;
}
</style>
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

  <table id="table_santé" class="table table-condensed tablesorter">
  <thead>
  <tr>
  <th>{{ID}}</th>
  <th>{{Image}}</th>
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
    $recherche='chauffage';
    
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
                echo '<tr class="santé_titre"><td colspan="10"><h3><span>Mes Chauffages</span></h3></td></tr>';
                break;
            case 'PAC':
                echo '<tr class="santé_titre"><td colspan="10"><h3><span>Mes pompes à chaleur</span></h3></td></tr>';
                break;
            case 'poele';
                echo '<tr class="santé_titre"><td colspan="10"><h3><span>Mes poêles à granules</span></h3></td></tr>';
                break;
            case 'volet';
                echo '<tr class="santé_titre"><td colspan="10"><h3><span>Mes volets</span></h3></td></tr>';
                break;
            case 'prise';
                echo '<tr class="santé_titre"><td colspan="10"><h3><span>Mes prises</span></h3></td></tr>';
                break;
            case 'perso';
                echo '<tr class="santé_titre"><td colspan="10"><h3><span>Mes équipements perso</span></h3></td></tr>';
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
    echo '<tr class="santé ' .$recherche .'">';
    echo '<td><span class="label id">' . $eqLogic->getId() . '</span></td>';
    echo '<td>' . $img . '</td>';
    echo '<td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $Object . " " . $eqLogic->getName() . '</a></td>';

    if ($eqLogic->getIsEnable() ) {
      echo  '<td><span class="label label-success" style="font-size : 1em;"><i class="fas fa-check"></i></span></td>';
    }else{
      echo  '<td><span class="label label-danger" style="font-size : 1em;"><i class="fas fa-times"></i></span></td>';
      echo '<td><span></span></td>';
      echo '<td></td>';
      echo '<td></td>';
      echo '<td></td>';
      echo '<td></td>';
      echo '<td></td>';
      echo '</tr>';
      continue;
    }
    $valeur=$eqLogic->getCmd(null,'mode_fonctionnement')->execCmd();;
    if ($valeur == "Auto"){
        echo '<td><span class="label label-success" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
    }elseif ($valeur == "Manuel"){
        $cmd_auto= $eqLogic->getCmd(null,'auto');
        echo '<td><span class="label label-warning cursor manuel" cmd_id='. $cmd_auto->getId().' style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
    }else{
        echo '<td><span class="label label-danger" style="font-size : 1em;">{{Inconnu}}</span></td>';
      }
    

 
      $valeur=$eqLogic->getCmd(null,'planification_en_cours')->execCmd();
      echo '<td><span class="label" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
      
      $valeur=$eqLogic->getCmd(null,'action_en_cours')->execCmd();
      echo '<td><span class="label" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
    
    
   
      $valeur = $eqLogic->getCmd(null,'heure_fin')->execCmd();
      if ($valeur != ""){
        $valeur = strtotime($valeur);

        if(date('d-m-Y',$valeur) != date('d-m-Y') ){
          $valeur = date('d-m-Y H:i',$valeur);
        }else{
          $valeur = date('H:i',$valeur);
        }
        echo '<td><span class="label" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
      }else{
        //echo '<td><span class="label label-danger" style="font-size : 1em;">{{Inconnu}}</span></td>';
        echo '<td></td>';
      }



    
    
      $valeur=$eqLogic->getCmd(null,'action_suivante')->execCmd();
      if ($valeur != ""){
        echo '<td><span class="label" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
      }else{
          $valeur = $eqLogic->getCmd(null,'heure_fin')->execCmd();
          if ($valeur != ""){
            echo '<td><span class="label" style="font-size : 1em;">{{Remise en Auto}}</span></td>';
          }else{
            //echo '<td><span class="label label-danger" style="font-size : 1em;">{{Aucune}}</span></td>';
            echo '<td></td>';
          }
        

      }
   
    
      $valeur=$eqLogic->getCmd(null,'info')->execCmd();
      echo '<td><span class="label" style="font-size : 1em;">{{'. $valeur.'}}</span></td>';
   

    echo '</tr>';
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
?>
  <script>

$('.manuel').on('click', function() {

 // jeedom.cmd.execute({id: $(this).attr("cmd_id")});
  //setTimeout(() => {  creation_table(); }, 2000);
console.log($(this).closest("tr").children().first().find("span").text())
})

  //creation_table()
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