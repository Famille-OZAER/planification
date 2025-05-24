<?php


require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
//if (!isConnect('admin')) {
//	throw new Exception('{{401 - Accès non autorisé}}');
//}
 $pluginVersion = planification::GetVersionPlugin();
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

//function planification_postSaveConfiguration(){
   /* domUtils.ajax({
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
            jeedomUtils.showAlert({message: data.result, level: 'danger'});
            return;
        }
        
        }
    });*/
    

//}



    /*var divState = document.getElementById("div_state");
    var secondForm = divState.querySelectorAll("form.form-horizontal")[1]; // Select second form

    // Create new form-group dynamically
    var newFormGroup = document.createElement("div");
    newFormGroup.className = "form-group";

    // Create elements for the new form-group
    var labelVersion = document.createElement("label");
    labelVersion.className = "col-sm-2 control-label";
    labelVersion.textContent = "Version plugin";

    var divVersion = document.createElement("div");
    divVersion.className = "col-sm-4";
    var spanVersion = document.createElement("span");
    spanVersion.textContent = "<?= $pluginVersion; ?>"; // Dynamic plugin version
    divVersion.appendChild(spanVersion);

    // Append elements to the form-group
    newFormGroup.appendChild(labelVersion);
    newFormGroup.appendChild(divVersion);

    // Add the new form-group to the second form
    secondForm.appendChild(newFormGroup);*/
    document.getElementById('span_plugin_install_date').textContent=document.getElementById('span_plugin_install_date').textContent + " (<?= $pluginVersion; ?>)"
</script>


