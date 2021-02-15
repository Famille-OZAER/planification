<?php
	if (!isConnect('admin')) {
		throw new Exception('{{401 - Accès non autorisé}}');
	}

	$plugin = plugin::byId('planification');
	sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());
	
	include_file('3rdparty', 'Moment', 'js', 'planification');
	include_file('3rdparty', 'TimePicker', 'js', 'planification');
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction" data-action="gotoPluginConf">
				<i class="fas fa-wrench"></i>
				<span>{{Configuration}}</span>
			</div>
			<div class="cursor ajouter_eqlogic"  style="background-color : #ffffff; height : 90px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
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
					if ($eqLogic->getConfiguration('type', 'Autre') == 'Prise') $imgPath = 'plugins/planification/core/img/prise.png';
					if ($eqLogic->getConfiguration('type', 'Autre') == 'Poele') $imgPath = 'plugins/planification/core/img/poele.png';
					if (!file_exists ( $imgPath )){
						$imgPath = 'plugins/planification/core/img/autre.png';
					}
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
		
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a>
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
				<a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
				<a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
				
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist" style="display:inline-block">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#tab_eqlogic" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#tab_commandes_planification" aria-controls="home" role="tab" data-toggle="tab" ><i class="fa fa-cog"></i> {{Commandes planification}}</a></li>
			<li role="presentation" id ="menu_tab_planifications"><a href="#tab_planifications" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Planifications}}</a></li>
			<li role="presentation" id ="menu_tab_gestion"><a href="#tab_gestion" aria-controls="home" role="tab" data-toggle="tab" ><i class="fa fa-cog"></i> {{Gestion Lever/Coucher soleil}}</a></li>
			<li role="presentation"><a href="#tab_commandes" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes équipement}}</a></li>
		</ul>	
		
			
		
		<div class="tab-content" style="height:calc(100% - 100px);overflow:auto;overflow-x: hidden;">


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
										 $options = '';
										 foreach ((jeeObject::buildTree(null, false)) as $object) {
											 $options .= '<option value="' . $object->getId() . '"><i class="icon divers-umbrella2"></i>' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber'))  . $object->getName() . '</option>';
										 }
										 
										 echo $options;
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
                                  
						<div class="form-group" style="display : none;" >
							<label class="col-sm-3 control-label">{{Type équipement}}</label>
							<div class="col-sm-3">
							<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="type" />
							</div>
						</div>

						<div class="form-group" style="display : block;" >
							<label class="col-sm-3 control-label">{{Durée mode manuel par defaut (en minutes)}}</label>
							<div class="col-sm-3">
							<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="Duree_mode_manuel_par_defaut" title="Mettre 0 pour réactivation manuelle"/>
						</div>
						</div>
						<div class="poele" style="display:none">
							<div class="form-group" style="display : block;" >
								<label class="col-sm-3 control-label">{{Température par défaut}}</label>
								<div class="col-sm-3">
								<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="temperature_consigne_par_defaut"/>
							</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Commande de température}}</label>
								<div class="col-sm-6 input-group">
									<input class="eqLogicAttr form-control input-sm cmdAction" data-l1key="configuration_poele" data-l2key="temperature_id"/>
									<span class="input-group-btn">
										<a class="btn btn-success btn-sm listCmdTemperature"><i class="fa fa-tasks"></i></a>
									</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Commande de l'état du poêle}}</label>
								<div class="col-sm-6 input-group">
									<input class="eqLogicAttr form-control input-sm cmdAction" data-l1key="configuration_poele" data-l2key="etat_allume_id"/>
									<span class="input-group-btn">
										<a class="btn btn-success btn-sm listCmdEtat"><i class="fa fa-tasks"></i></a>
									</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Commande de l'état boost du poêle}}</label>
								<div class="col-sm-6 input-group">
									<input class="eqLogicAttr form-control input-sm cmdAction" data-l1key="configuration_poele" data-l2key="etat_boost_id"/>
									<span class="input-group-btn">
										<a class="btn btn-success btn-sm listCmdEtatBoost"><i class="fa fa-tasks"></i></a>
									</span>
								</div>
							</div>
						</div>
						<div class="PAC" style="display:none">
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Commande de température}}</label>
								<div class="col-sm-6 input-group">
									<input class="eqLogicAttr form-control input-sm cmdAction" data-l1key="configuration_PAC" data-l2key="temperature_id"/>
									<span class="input-group-btn">
										<a class="btn btn-success btn-sm listCmdTemperature"><i class="fa fa-tasks"></i></a>
									</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Commande de l'état de la pompe à chaleur}}</label>
								<div class="col-sm-6 input-group">
									<input class="eqLogicAttr form-control input-sm cmdAction" data-l1key="configuration_PAC" data-l2key="etat_pac_id"/>
									<span class="input-group-btn">
										<a class="btn btn-success btn-sm listCmdInfoPAC"><i class="fa fa-tasks"></i></a>
									</span>
								</div>
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
			<span class="info_commandes_planification label label-danger" style="display:none">Pour ajouter une planification, veuillez enregistrer une ou plusieurs commandes dans l'onglet "Commandes planification"</span>
					<a class="btn btn-sm btn-success pull-right bt_ajouter_planification" style="margin-top: 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter planification}}</a>
					<br/><br/>
				<div id="div_planifications" class="panel-group"></div>
			</div>
			<!--fin planifications Tab-->    

			<div role="tabpanel" class="tab-pane" id="tab_commandes_planification">
		       	<div class="input-group pull-right" style="display:inline-flex">
				    <a class="btn btn-default btn-sm bt_Importer_Commandes_EqLogic" style="margin-top:5px;"><i class="fas fa-sign-in-alt" style=" transform:rotate(90deg)"></i> {{Importer équipement}}</a>
					<a class="btn btn-default btn-sm  pull-right bt_Ajout_commande_planification" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande}}</a>
                   
                </div> 
					
		
				<div class="col-sm-12">
					<table id="table_cmd_planification" class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th class="col-sm-2" style="display:none">{{ID}}</th>
								<th class="col-sm-2">{{Nom}}</th>
								<th class="col-sm-6">{{Commande}}</th>
								<th class="col-sm-1" >{{Couleur}}</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
			<!--Gestion-->
			<div role="tabpanel" class="tab-pane" id="tab_gestion">
			
			<div class="col-sm-12">
				<label class="col-sm-3 control-label">{{Jour à configurer}}</label>
				
				<select class="selection_jour col-sm-3">
					<option value="Lundi">{{Lundi}}<selected/option>
					<option value="Mardi">{{Mardi}}</option>
					<option value="Mercredi">{{Mercredi}}</option>
					<option value="Jeudi">{{Jeudi}}</option>
					<option value="Vendredi">{{Vendredi}}</option>
					<option value="Samedi">{{Samedi}}</option>
					<option value="Dimanche">{{Dimanche}}</option>
					</select>
				</div>
				<div class="Lundi col-sm-12">
					<div class="col-sm-6 Jour">
						<form class="form-horizontal">
							<legend> <i class="fa fa-sun"></i> Gestion lever de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de lever de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureLever_Lundi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_lever_Lundi label label-warning"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMin_Lundi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMin_Lundi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMax_Lundi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMax_Lundi"/>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="col-sm-6 Nuit">
						<form class="form-horizontal">
							<legend><i class="fa fa-moon"></i> Gestion coucher de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de coucher de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureCoucher_Lundi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_coucher_Lundi label label-warning"></span>
										</div>
									</div>
									<div class="form-group">
									
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMin_Lundi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMin_Lundi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMax_Lundi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMax_Lundi"/>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>	
				</div>
				<div class="Mardi col-sm-12" style='display:none'>
					<div class="col-sm-6 Jour">
						<form class="form-horizontal">
							
							<legend> <i class="fa fa-sun"></i> Gestion lever de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de lever de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureLever_Mardi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_lever_Mardi label label-warning"></span>
										</div>
									</div>
									
								
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMin_Mardi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMin_Mardi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMax_Mardi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMax_Mardi"/>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="col-sm-6 Nuit">
						<form class="form-horizontal">
							<legend><i class="fa fa-moon"></i> Gestion coucher de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de coucher de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureCoucher_Mardi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_coucher_Mardi label label-warning"></span>
										</div>
									</div>
									
									<div class="form-group">
									
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMin_Mardi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMin_Mardi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMax_Mardi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMax_Mardi"/>

											</div>
										</div>
									</div>
									
								</div>
							</fieldset>
						</form>
					</div>	
				</div>
				<div class="Mercredi col-sm-12" style='display:none'>
					<div class="col-sm-6 Jour">
						<form class="form-horizontal">
							
							<legend> <i class="fa fa-sun"></i> Gestion lever de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de lever de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureLever_Mercredi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_lever_Mercredi label label-warning"></span>
										</div>
									</div>
									
								
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMin_Mercredi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMin_Mercredi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMax_Mercredi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMax_Mercredi"/>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="col-sm-6 Nuit">
						<form class="form-horizontal">
							<legend><i class="fa fa-moon"></i> Gestion coucher de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de coucher de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureCoucher_Mercredi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_coucher_Mercredi label label-warning"></span>
										</div>
									</div>
									
									<div class="form-group">
									
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMin_Mercredi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMin_Mercredi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMax_Mercredi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMax_Mercredi"/>

											</div>
										</div>
									</div>
									
								</div>
							</fieldset>
						</form>
					</div>	
				</div>
				<div class="Jeudi col-sm-12" style='display:none'>
					<div class="col-sm-6 Jour">
						<form class="form-horizontal">
							
							<legend> <i class="fa fa-sun"></i> Gestion lever de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de lever de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureLever_Jeudi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_lever_Jeudi label label-warning"></span>
										</div>
									</div>
									
								
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMin_Jeudi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMin_Jeudi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMax_Jeudi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMax_Jeudi"/>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="col-sm-6 Nuit">
						<form class="form-horizontal">
							<legend><i class="fa fa-moon"></i> Gestion coucher de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de coucher de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureCoucher_Jeudi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_coucher_Jeudi label label-warning"></span>
										</div>
									</div>
									
									<div class="form-group">
									
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMin_Jeudi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMin_Jeudi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMax_Jeudi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMax_Jeudi"/>

											</div>
										</div>
									</div>
									
								</div>
							</fieldset>
						</form>
					</div>	
				</div>
				<div class="Vendredi col-sm-12" style='display:none'>
					<div class="col-sm-6 Jour">
						<form class="form-horizontal">
							
							<legend> <i class="fa fa-sun"></i> Gestion lever de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de lever de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureLever_Vendredi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_lever_Vendredi label label-warning"></span>
										</div>
									</div>
									
								
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMin_Vendredi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMin_Vendredi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMax_Vendredi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMax_Vendredi"/>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="col-sm-6 Nuit">
						<form class="form-horizontal">
							<legend><i class="fa fa-moon"></i> Gestion coucher de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de coucher de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureCoucher_Vendredi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_coucher_Vendredi label label-warning"></span>
										</div>
									</div>
									
									<div class="form-group">
									
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMin_Vendredi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMin_Vendredi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMax_Vendredi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMax_Vendredi"/>

											</div>
										</div>
									</div>
									
								</div>
							</fieldset>
						</form>
					</div>	
				</div>
				<div class="Samedi col-sm-12" style='display:none'>
					<div class="col-sm-6 Jour">
						<form class="form-horizontal">
							
							<legend> <i class="fa fa-sun"></i> Gestion lever de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de lever de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureLever_Samedi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_lever_Samedi label label-warning"></span>
										</div>
									</div>
									
								
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMin_Samedi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMin_Samedi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMax_Samedi eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMax_Samedi"/>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="col-sm-6 Nuit">
						<form class="form-horizontal">
							<legend><i class="fa fa-moon"></i> Gestion coucher de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de coucher de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureCoucher_Samedi label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_coucher_Samedi label label-warning"></span>
										</div>
									</div>
									
									<div class="form-group">
									
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMin_Samedi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMin_Samedi"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMax_Samedi eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMax_Samedi"/>

											</div>
										</div>
									</div>
									
								</div>
							</fieldset>
						</form>
					</div>	
				</div>
				<div class="Dimanche col-sm-12" style='display:none'>
					<div class="col-sm-6 Jour">
						<form class="form-horizontal">
							
							<legend> <i class="fa fa-sun"></i> Gestion lever de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de lever de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureLever_Dimanche label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_lever_Dimanche label label-warning"></span>
										</div>
									</div>
									
								
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMin_Dimanche eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMin_Dimanche"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureLeverMax_Dimanche eqLogicAttr form-control" data-l1key="configuration" data-l2key="LeverMax_Dimanche"/>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="col-sm-6 Nuit">
						<form class="form-horizontal">
							<legend><i class="fa fa-moon"></i> Gestion coucher de soleil</legend>
							<fieldset>
								<div class="well">
								<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure de coucher de soleil}}</label>
										<div class="col-sm-2">
											<span class="HeureCoucher_Dimanche label label-success"></span>
										</div>
									</div>
									<div class="form-group ">
										<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
										<div class="col-sm-2">
											<span class="Heure_prochaine_action_coucher_Dimanche label label-warning"></span>
										</div>
									</div>
									
									<div class="form-group">
									
										<label class="col-sm-7 control-label">{{Heure minimum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMin_Dimanche eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMin_Dimanche"/>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-7 control-label">{{Heure maximum}}</label>
										<div class="col-sm-5">
											<div class="input-group">
												<input type="time" class="HeureCoucherMax_Dimanche eqLogicAttr form-control" data-l1key="configuration" data-l2key="CoucherMax_Dimanche"/>

											</div>
										</div>
									</div>
									
								</div>
							</fieldset>
						</form>
					</div>	
				</div>
				
			</div>
			<!--Commands Tab-->
			<div role="tabpanel" class="tab-pane" id="tab_commandes">
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
	include_file('3rdparty', 'TimePicker', 'css', 'planification');
	include_file('core', 'plugin.template', 'js');
	
?>