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
  <th>{{Mode planification}}</th>
  <th>{{Planification en cours}}</th>
  <th>{{Action en cours}}</th>
  <th>{{Heure Prochaine action}}</th>
  <th>{{Prochaine action}}</th>
  <th>{{Supprimer}}</th>
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
      echo '<tr class="santé_titre chauffage"><td colspan="11"><h3><span><i class="fa jeedom-pilote-conf"> Mes Chauffages</span></h3></td></tr>';
      break;
    case 'PAC':
      echo '<tr class="santé_titre PAC"><td colspan="11"><h3><span><i class="far fa-snowflake"></i> Mes pompes à chaleur</span></h3></td></tr>';
      break;
    case 'thermostat':
      echo '<tr class="santé_titre thermostat"><td colspan="11"><h3><span><i class="icon jeedomapp-thermostat"></i> Mes thermostats connectés</span></h3></td></tr>';
      break;
    case 'volet';
      echo '<tr class="santé_titre volet"><td colspan="11"><h3><span><i class="fa jeedom-volet-ferme"> Mes volets</span></h3></td></tr>';
      break;
    case 'prise';
      echo '<tr class="santé_titre prise"><td colspan="11"><h3><span><i class="fa jeedom-prise"></i> Mes prises</span></h3></td></tr>';
      break;
    case 'perso';
      echo '<tr class="santé_titre perso"><td colspan="11"><h3><span><i class="fas fa-user-cog"></i> Mes équipements perso</span></h3></td></tr>';
      break;
  }
}

foreach ($eqLogic_ids as $eqLogic_id) {
  $eqLogic=planification::byId($eqLogic_id);
  $type_eqLogic = strtolower($eqLogic->getConfiguration('type'));
  $image=$eqLogic->getConfiguration("Chemin_image","none");
  if ( $image == "none"){
    if (file_exists(dirname(__FILE__) . '/../../core/img/' . $type_eqLogic . '.png')) {
      $img = '<img src="plugins/planification/core/img/' . $type_eqLogic . '.png" height="55" width="55"/>';
    } else {
      $img = $image;
    } 
  }else{
    $img = '<img ' . $type_eqLogic . 'src="' . $image . '" height="55" width="55"/>';
  }
  if($eqLogic->getObject()==""){
    $Object = "Aucun";
  }else{
    $Object = $eqLogic->getObject()->getName();
  }
  echo '<tr class="santé ' .$recherche .'">';
  echo '<td><span class="label id">' . $eqLogic->getId() .'</span></td>';
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
  $valeur=$eqLogic->getCmd(null,'mode_planification')->execCmd();;
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



  echo '<td><span class="label label-danger cursor supprimer" style="font-size : 1em;">{{Supprimer}}</span></td>';


  echo '</tr>';
}
switch ($recherche) {
  case 'chauffage':
    $recherche = 'PAC';
    goto suivant;
  case 'PAC':
    $recherche = 'thermostat';
    goto suivant;
  case 'thermostat':
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

  if (document.querySelectorAll('.manuel').length > 0){
    document.querySelectorAll('.manuel').forEach(_el => {
      _el.onclick = function() {
        jeedom.cmd.execute({id: this.getAttribute("cmd_id")});
        setTimeout(() => {  creation_table(); }, 2000);
      }
    })
    }
if (document.querySelectorAll('.supprimer').length > 0){
  document.querySelectorAll('.supprimer').forEach(_el => {
    _el.onclick = function() {

      jeedom.eqLogic.remove({id:Number(this.closest('.santé').querySelector('.id').innerHTML),type:"planification"})

        var classes=this.closest('.santé').className  

        this.closest('.santé').remove()
        document.querySelector('.eqLogicDisplayCard[data-eqlogic_id="'+this.closest('.santé').querySelector('.id').innerHTML+'"]').remove()
        document.querySelector('.li_eqLogic[data-eqlogic_id="'+this.closest('.santé').querySelector('.id').innerHTML+'"]').remove()
        if(document.getElementsByClassName(classes).length == 0){
          document.getElementsByClassName(classes.replace("santé", "santé_titre"))[0].remove()
            console.log(classes.replace("santé", "eqLogicThumbnailContainer")+'s')
            document.getElementsByClassName(classes.replace("santé", "eqLogicThumbnailContainer")+'s')[0].remove()
            document.getElementsByClassName(classes.replace("santé", "bs-sidenav")+'s')[0].remove()
          }

    }
  })
  }

//creation_table()
function creation_table(){
  var table = document.querySelector('.health_planification')
    table.empty() 
    console.clear()

    domUtils.ajax({
      type: "POST",
      url: "plugins/planification/core/ajax/planification.ajax.php",
      data: {
        action: "Santé",
      },
      dataType: 'json',
      global: true,
      async: false,
      error: function(request, status, error) { 
        handleAjaxError(request, status, error) },
      success: function(data) {
        if (data.state != 'ok') {
          jeedomUtils.showAlert({
            message: data.result,
            level: 'danger'
            })
            return
          }

        document.querySelector('.health_planification').append(domUtils.parseHTML(data.result))

          if (document.querySelectorAll('.manuel').length > 0){
            document.querySelectorAll('.manuel').forEach(_el => {
              _el.onclick = function() {
                jeedom.cmd.execute({id: this.getAttribute("cmd_id")});
                setTimeout(() => {  creation_table(); }, 2000);
              }
            })
            }
        if (document.querySelectorAll('.supprimer').length > 0){
          document.querySelectorAll('.supprimer').forEach(_el => {
            _el.onclick = function() {

              jeedom.eqLogic.remove({id:Number(this.closest('.santé').querySelector('.id').innerHTML),type:"planification"})

                var classes=this.closest('.santé').className  

                this.closest('.santé').remove()
                document.querySelector('.eqLogicDisplayCard[data-eqlogic_id="'+this.closest('.santé').querySelector('.id').innerHTML+'"]').remove()
                document.querySelector('.li_eqLogic[data-eqlogic_id="'+this.closest('.santé').querySelector('.id').innerHTML+'"]').remove()
                if(document.getElementsByClassName(classes).length == 0){
                  document.getElementsByClassName(classes.replace("santé", "santé_titre"))[0].remove()
                    console.log(classes.replace("santé", "eqLogicThumbnailContainer")+'s')
                    document.getElementsByClassName(classes.replace("santé", "eqLogicThumbnailContainer")+'s')[0].remove()
                    document.getElementsByClassName(classes.replace("santé", "bs-sidenav")+'s')[0].remove()
                  }
            }
          })
          }

      }

    })

  }
</script>
  </tbody>
  </table>