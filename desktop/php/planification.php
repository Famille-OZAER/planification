<?php
	if (!isConnect('admin')) {
		throw new Exception('{{401 - Accès non autorisé}}');
	}

	$plugin = plugin::byId('planification');
	sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());
	include_file('3rdparty', 'jquery-clock-timepicker.min', 'js', 'planification');
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction" data-action="gotoPluginConf">
				<i class="fas fa-wrench"></i>
				<span>{{Configuration}}</span>
			</div>
			<div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 90px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
				<i class="fa fa-plus-circle" style="font-size : 70px;color:#94ca02;"></i>
				<span style="font-size : 1.1em;color:#94ca02"><center>{{Ajouter}}</center></span>
			</div>
		</div>

		<legend><i class="fa fa-table"></i> {{Mes Equipements}}</legend>
		<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
		<div class="eqLogicThumbnailContainer">

			<?php
				foreach ($eqLogics as $eqLogic) {
					$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
					echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';

					$imgPath = 'plugins/planification/core/img/autre.png';
					if ($eqLogic->getConfiguration('type', 'Autre') == 'Chauffage') $imgPath = 'plugins/planification/core/img/chauffage.png';
					if ($eqLogic->getConfiguration('type', 'Autre') == 'PAC') $imgPath = 'plugins/planification/core/img/pac.png';
					if ($eqLogic->getConfiguration('type', 'Autre') == 'Volet') $imgPath = 'plugins/planification/core/img/volet.png';
					if ($eqLogic->getConfiguration('type', 'Autre') == 'Autre') $imgPath = 'plugins/planification/core/img/autre.png';
					echo '<img src="' . $imgPath . '"/>';

					echo '<br>';
					echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
					echo '</div>';
				}
			?>
		</div>
	</div>

	<!--Equipement page-->
	<div class="col-xs-12 eqLogic" style="display: none;">
		<div>
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
               	<a class="btn btn-primary btn-sm bt_showExpressionTest roundedLeft"><i class="fas fa-check"></i> {{Expression}}</a>
              	<a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a>
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
				<a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
				<a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
              	
			</span>
		</div>
</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#tab_eqlogic" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
			<li role="presentation" id ="menu_tab_planifications"><a href="#tab_planifications" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{planifications}}</a></li>
			<li role="presentation"><a href="#tab_commandes_planification" aria-controls="home" role="tab" data-toggle="tab" ><i class="fa fa-cog"></i> {{Commandes planification}}</a></li>
			<li role="presentation" id ="menu_tab_gestion"><a href="#tab_gestion" aria-controls="home" role="tab" data-toggle="tab" ><i class="fa fa-cog"></i> {{Gestion Lever/Coucher soleil}}</a></li>
			<li role="presentation"><a href="#tab_commandes" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes équipement}}</a></li>
			
		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">


			<div role="tabpanel" class="tab-pane active" id="tab_eqlogic">
				<br/>
				<form class="form-horizontal col-sm-9">
					<fieldset>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Nom de l&#39équipement}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" >{{Objet parent}}</label>
							<div class="col-sm-3">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<option value="">{{Aucun}}</option>
									<?php
										foreach (jeeObject::all() as $object) {
											echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Catégorie}}</label>
							<div class="col-sm-9">
								<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
										echo '</label>';
									}
								?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-9">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
							</div>
						</div>
                                  
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Type équipement}}</label>
							<div class="col-sm-3">
                                  <select class="eqLogicAttr" data-l1key="configuration" data-l2key="type">
                                  	 <option value="Volet">Volet Roulant</option>
                                     <option value="Chauffage">Chauffage avec fil pilote</option>
                                     <option value="PAC">Pompe à chaleur</option>
									 <option value="Poelle">Poêlle à granules</option>
                                     <option value="Autre">Autre</option>
                                  </select>
							</div>
						</div>

					</fieldset>
				</form>

				<form class="form-horizontal col-sm-3">
					<fieldset>
						<div class="form-group">
							<img src="<?php echo($plugin->getPathImgIcon())?>" id="img_planificationModel" style="height:130px;" />
						</div>
					</fieldset>
				</form>

				<hr>
			</div>

			<!--planifications Tab-->
			<div role="tabpanel" class="tab-pane" id="tab_planifications">
					<a class="btn btn-sm btn-success pull-right" id="bt_ajouter_planification" style="margin-top: 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter planification}}</a>
					<a class="btn btn-sm btn-success pull-right" id="bt_importer_planification" style="margin-top: 5px;"><i class="fas fa-sign-in-alt" style=" transform:rotate(90deg)"></i> {{Importer planification}}</a><br/><br/>
				<div id="div_planifications" class="panel-group"></div>
			</div>
			<!--fin planifications Tab-->    

		


			
			<div role="tabpanel" class="tab-pane" id="tab_commandes_planification">
			<!--<fieldset>-->
			<div class="alert alert-danger">
				ATTENTION. Si vous modifiez ou supprimez des commandes qui sont utilisées dans vos planifications, celles-ci risquent de ne plus fonctionner correctement.<br>
				Un ajout de commande n'a aucune inscidence sur le fonctionnement de vos planifications.
			</div>
            	<div class="input-group pull-right" style="display:inline-flex">
				    <a class="btn btn-default btn-sm bt_Importer_Commandes_EqLogic" style="margin-top:5px;"><i class="fas fa-sign-in-alt" style=" transform:rotate(90deg)"></i> {{Importer équipement}}</a>
					<a class="btn btn-default btn-sm  pull-right bt_Ajout_commande_planification" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande}}</a>
                   
                </div> 
					
		
				<div class="col-sm-12">
					<table id="table_cmd_planification" class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th class="col-sm-2">{{ID}}</th>
								<th class="col-sm-2">{{Nom}}</th>
								<th class="col-sm-6">{{Commande}}</th>
								<th class="col-sm-2" >{{Couleur}}</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
					
				</div>
				
				
				
				<!--</fieldset>-->
			</div>
			<!--Gestion-->
			<!--<div role="tabpanel" class="tab-pane" id="tab_gestion">
				<div class="col-sm-6 Jour">
					<form class="form-horizontal">
						<div class="Periode_jour">
							<div class="input-group" style="width:100% !important; line-height:1.4px !important;">
								<input class="checkbox form-control input-sm cursor" type="checkbox" onchange="Maj_checkbox(this)">
								<input class="timePicker form-control input-sm cursor" type="text" value="00:00" style="width:60px; min-width:60px;" onchange="checkTimePicker(this)">



								<a class="btn btn-default bt_supprimer_perdiode btn-sm" title="Supprimer cette période"><i class="fa fa-minus-circle"></i></a>
							</div>

						</div>
						<div class="Periode_jour">
							<div class="input-group" style="width:100% !important; line-height:1.4px !important;">
								<input class="checkbox form-control input-sm cursor" type="checkbox" onchange="Maj_checkbox(this)">
								<input class="timePicker form-control input-sm cursor" type="text" value="01:00" style="width:60px; min-width:60px;" onchange="checkTimePicker(this)">



								<a class="btn btn-default bt_supprimer_perdiode btn-sm" title="Supprimer cette période"><i class="fa fa-minus-circle"></i></a>
							</div>

						</div>
						<legend> <i class="fa fa-sun"></i> Gestion Jour</legend>
						<fieldset>
							<div class="well">
								<div class="form-group ">
									<label class="col-sm-5 control-label">{{Prochaine ouverture du volet}}</label>
									<div class="col-sm-2">
										<span class="planificationJour label label-success eqLogicAttr" data-l1key="configuration" data-l2key="planificationJour"></span>
									</div>
								</div>
								<div class="form-group ">
									<label class="col-sm-5 control-label">{{Heure de lever de soleil}}</label>
									<div class="col-sm-2">
										<span class="HeureLever label label-success eqLogicAttr" data-l1key="configuration" data-l2key="Heure_Lever"></span>
									</div>
								</div>
							
								<div class="form-group">
									<label class="col-sm-5 control-label">{{Heure ouverture minimum}}</label>
									<div class="col-sm-5">
										<div class="input-group">
											<input type="time" class="HeureOuvertureMin eqLogicAttr form-control" data-l1key="configuration" data-l2key="OuvertureMin" onchange="UpdateplanificationJour()"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label">{{Heure ouverture maximum}}</label>
									<div class="col-sm-5">
										<div class="input-group">
											<input type="time" class="HeureOuvertureMax eqLogicAttr form-control" data-l1key="configuration" data-l2key="OuvertureMax" onchange="UpdateplanificationJour()"/>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<div class="col-sm-6 Nuit">
					<form class="form-horizontal">
						<legend><i class="fa fa-moon-o"></i> Gestion Nuit</legend>
						<fieldset>
							<div class="well">
								<div class="form-group ">
									<label class="col-sm-5 control-label">{{Prochaine fermeture du volet}}</label>
									<div class="col-sm-2">
										<span class="planificationNuit label label-warning eqLogicAttr" data-l1key="configuration" data-l2key="planificationNuit"></span>
									</div>
								</div>
                                <div class="form-group ">
									<label class="col-sm-5 control-label">{{Heure de coucher de soleil}}</label>
									<div class="col-sm-2">
										<span class="HeureCoucher label label-warning eqLogicAttr" data-l1key="configuration" data-l2key="Heure_Coucher"></span>
									</div>
								</div>
								<div class="form-group">
								<
									<label class="col-sm-5 control-label">{{Heure de fermeture minimum}}</label>
									<div class="col-sm-5">
										<div class="input-group">
											<input type="time" class="HeureFermetureMin eqLogicAttr form-control" data-l1key="configuration" data-l2key="FermetureMin" onchange="UpdateplanificationNuit()"/>
										</div>
										
									</div>
								</div>
                                <div class="form-group">
									<label class="col-sm-5 control-label">{{Heure de fermeture maximum}}</label>
									<div class="col-sm-5">
                                    	<div class="input-group">
                                    		<input type="time" class="HeureFermetureMax eqLogicAttr form-control" data-l1key="configuration" data-l2key="FermetureMax" onchange="UpdateplanificationNuit()"/>

                                        </div>
                                    </div>
                                </div>
								
                            </div>
						</fieldset>
					</form>
				</div>	
					
            </div>-->
			<!--Commands Tab-->
			<div role="tabpanel" class="tab-pane" id="tab_commandes">
				<!--<div id="div_cmds"></div>-->
				<legend><i class="fa fa-list-alt"></i>  {{Commandes Infos}}</legend>
				<table id="table_infos" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="65%">{{Nom}}</th><th width="25%" align="center">{{Options}}</th><th width="10%" align="right">{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<legend><i class="fa fa-list-alt"></i>  {{Commandes Actions}}</legend>
				<table id="table_actions" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="65%">{{Nom}}</th><th width="25%" align="center">{{Options}}</th><th width="10%" align="right">{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>

		</div>
	</div>
<?php
	
	include_file('desktop', 'planification', 'js', 'planification');
	include_file('desktop', 'planification', 'css', 'planification');
	include_file('core', 'plugin.template', 'js');
	
?>
