<?php


require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<form class="form-horizontal">
    <fieldset>
	<legend><i class="fas fa-list-alt"></i> {{Général}}</legend>
      <div class="form-group">
        <label class="col-lg-4 control-label">{{Utiliser un fichier de log par équipement.}}</label>
        <div class="col-lg-3">
           <input type="checkbox" class="configKey" data-l1key="UseLogByeqLogic" />
       </div>
	</div>
	 
   </fieldset>
</form>

<script>

function blea_postSaveConfiguration(){
 
}


$('#bt_savePluginConfig').on('click',function(event){
    $.ajax({
        type: "POST",
        url: "plugins/planification/core/ajax/planification.ajax.php",
        data: {
        action: "Remove_log",
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
        handleAjaxError(request, status, error);
        },
        success: function (data) {
        if (data.state != 'ok') {
            $('#div_alert').showAlert({message: data.result, level: 'danger'});
            return;
        }
        //console.log(data)
        }
    });
    location.reload()
		
	});
</script>