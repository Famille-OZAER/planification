<?php
	if (!isConnect('admin')) {
		throw new Exception('{{401 - Accès non autorisé}}');
	}

	$plugin = plugin::byId('planification');
	sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());

?>
<style>
	.tab-content > div {
		display: none;
	}
	.tab-content > .active {
		display: block;
	}
	.nav-tabs a.active {
		font-weight: bold;
		border-bottom: 1px var(--logo-primary-color) solid !important;
		z-index: 6;
	}
</style>
<div class="row row-overflow">
	<div class="col-lg-2 col-md-3 col-sm-4">
		<div class="bs-sidebar">
			<ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
				<li class="filter" style="margin-bottom: 5px;"><input class="filter form-control" placeholder="{{Rechercher}}" style="width: 100%"/></li>

				<ul id="ul_eqLogic" class="nav nav-list bs-sidenav chauffages" style="display:none !important"><i class="fa jeedom-pilote-conf"></i> Mes Chauffages
					<?php
						foreach ($eqLogics as $eqLogic) {
							if (strtolower($eqLogic->getConfiguration('Type_équipement')) == 'chauffage') {	
								$opacity = ($eqLogic->getIsEnable()) ? 1 : 0.4;
								echo '<li class="cursor li_eqLogic " data-eqLogic_id="' . $eqLogic->getId() . '" style= opacity:' . $opacity .'><a>' . $eqLogic->getHumanName(true) . '</a></li>';
							}
						}
					?>
				</ul>
				<ul id="ul_eqLogic" class="nav nav-list bs-sidenav PACs" style="display:none !important"><i class="far fa-snowflake"></i> Mes pompes à chaleur
					<?php
						foreach ($eqLogics as $eqLogic) {
							if (strtolower($eqLogic->getConfiguration('Type_équipement')) == 'pac') {	
								$opacity = ($eqLogic->getIsEnable()) ? 1 : 0.4;
								echo '<li class="cursor li_eqLogic " data-eqLogic_id="' . $eqLogic->getId() . '" style= opacity:' . $opacity .'><a>' . $eqLogic->getHumanName(true) . '</a></li>';
							}
						}
					?>
				</ul>
				
				<ul id="ul_eqLogic" class="nav nav-list bs-sidenav volets" style="display:none !important"><i class="fa jeedom-volet-ferme"></i> Mes volets
					<?php
						foreach ($eqLogics as $eqLogic) {
							if (strtolower($eqLogic->getConfiguration('Type_équipement')) == 'volet') {	
								$opacity = ($eqLogic->getIsEnable()) ? 1 : 0.4;
								echo '<li class="cursor li_eqLogic " data-eqLogic_id="' . $eqLogic->getId() . '" style= opacity:' . $opacity .'><a>' . $eqLogic->getHumanName(true) . '</a></li>';
							}
						}
					?>
				</ul>
				<ul id="ul_eqLogic" class="nav nav-list bs-sidenav prises" style="display:none !important"><i class="fa jeedom-prise"></i> Mes prises
					<?php
						foreach ($eqLogics as $eqLogic) {
							if (strtolower($eqLogic->getConfiguration('Type_équipement')) == 'prise') {	
								$opacity = ($eqLogic->getIsEnable()) ? 1 : 0.4;
								echo '<li class="cursor li_eqLogic " data-eqLogic_id="' . $eqLogic->getId() . '" style= opacity:' . $opacity .'><a>' . $eqLogic->getHumanName(true) . '</a></li>';
							}
						}
					?>


				</ul>
				<ul id="ul_eqLogic" class="nav nav-list bs-sidenav persos" style="display:none !important"><i class="fas fa-user-cog"></i> Mes équipements perso
					<?php
						foreach ($eqLogics as $eqLogic) {
							if (strtolower($eqLogic->getConfiguration('Type_équipement')) == 'perso') {
                              	$opacity = ($eqLogic->getIsEnable()) ? 1 : 0.4;
								echo '<li class="cursor li_eqLogic " data-eqLogic_id="' . $eqLogic->getId() . '" style= opacity:' . $opacity .'><a>' . $eqLogic->getHumanName(true) . '</a></li>';
							}
						}
					?>


				</ul>
			</ul>
		</div>
	</div>

	<div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay">     
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
			<div class="cursor sante">
				<i class="fa fa-medkit"></i>
				<br/>
				<span >{{Santé}}</span>
			</div>
			<div class="cursor restart_demon">
				<i class="fas fa-redo" style="color :rgb(195, 52, 52)"></i>
				<br/>
				<span style="color :rgb(195, 52, 52)" >{{Redémarrer le Démo}}</span>
			</div>
		</div>

		<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />


		<div class="eqLogicThumbnailContainer chauffages" style="display:none !important">
			<legend><i class="fa jeedom-pilote-conf"></i> {{Mes chauffages}}</legend>
			<?php
				foreach ($eqLogics as $eqLogic) {
					if (strtolower($eqLogic->getConfiguration('Type_équipement')) == 'chauffage') {	
						$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
						$imgPath = $eqLogic->getConfiguration('Chemin_image');
						if ($imgPath == '') {
							$imgPath = 'plugins/planification/core/img/chauffage.png';
						}
						echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
						echo '<img src="' . $imgPath . '"/>';
						echo '<br>';
						echo '<span class="name">' . $eqLogic->getHumanName(true, true) .'</span>';
						echo '</div>';
					}
				}
			?>
		</div>


		<div class="eqLogicThumbnailContainer PACs" style="display:none !important">
			<legend><i class="far fa-snowflake"></i> {{Mes pompes à chaleur}}</legend>
			<?php
				foreach ($eqLogics as $eqLogic) {
					if (strtolower($eqLogic->getConfiguration('Type_équipement')) == 'pac') {	
						$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
						$imgPath = $eqLogic->getConfiguration('Chemin_image');
						if ($imgPath == '') {
							$imgPath = 'plugins/planification/core/img/pac.png';
						}
						echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
						echo '<img src="' . $imgPath . '"/>';
						echo '<br>';
						echo '<span class="name">' . $eqLogic->getHumanName(true, true) .'</span>';
						echo '</div>';
					}
				}
			?>
		</div>

		

		<div class="eqLogicThumbnailContainer volets" style="display:none !important">
			<legend><i class="fa jeedom-volet-ferme"></i> {{Mes Volets}}</legend>
			<?php
				foreach ($eqLogics as $eqLogic) {
					if (strtolower($eqLogic->getConfiguration('Type_équipement')) == 'volet') {	
						$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
						$imgPath = $eqLogic->getConfiguration('Chemin_image');
						if ($imgPath == '') {
							$imgPath = 'plugins/planification/core/img/volet.png';
						}
						echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
						echo '<img src="' . $imgPath . '"/>';
						echo '<br>';
						echo '<span class="name">' . $eqLogic->getHumanName(true, true) .'</span>';
						echo '</div>';
					}
				}
			?>
		</div>

		<div class="eqLogicThumbnailContainer prises" style="display:none !important">
			<legend><i class="fa jeedom-prise"></i> {{Mes Prises}}</legend>
				<?php
				foreach ($eqLogics as $eqLogic) {
					if (strtolower($eqLogic->getConfiguration('Type_équipement')) == 'prise') {	
						$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
						$imgPath = $eqLogic->getConfiguration('Chemin_image');
						if ($imgPath == '') {
							$imgPath = 'plugins/planification/core/img/prise.png';
						}
						echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
						echo '<img src="' . $imgPath . '"/>';
						echo '<br>';
						echo '<span class="name">' . $eqLogic->getHumanName(true, true) .'</span>';
						echo '</div>';
					}
				}
			?>
		</div>

		<div class="eqLogicThumbnailContainer persos" style="display:none !important">
			<legend><i class="fas fa-user-cog"></i> {{Mes équipement perso}}</legend>
				<?php
				foreach ($eqLogics as $eqLogic) {
					if (strtolower($eqLogic->getConfiguration('Type_équipement')) == 'perso') {	
						$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
						$imgPath = $eqLogic->getConfiguration('Chemin_image');
						if ($imgPath == '') {
							$imgPath = 'plugins/planification/core/img/perso.png';
						}
						echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
						echo '<img src="' . $imgPath . '"/>';
						echo '<br>';
						echo '<span class="name">' . $eqLogic->getHumanName(true, true) .'</span>';
						echo '</div>';
					}
				}
			?>
		</div>
		
	</div>

	<!--Equipement page-->
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="display: none;">

		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-sm btn-default restart_demon"><i class="fas fa-redo" style="color :rgb(195, 52, 52)"></i> {{Redémarrer le Deamon}}</a>
				<a class="btn btn-default btn-sm dupliquer_equipement"><i class="fas fa-copy"> </i>{{Dupliquer}}</a>
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
				<a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
				<a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
			
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist" style="display:inline-block">
			<li class="PAC" role="presentation" id = ""><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li role="presentation" id = "menu_tab_eqlogic" ><a href="#tab_eqlogic" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation" id = "menu_tab_planifications"><a href="#tab_planifications" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Planifications}}</a></li>
			<li role="presentation" id = "menu_tab_gestion"><a href="#tab_gestion_heures_lever_coucher" aria-controls="home" role="tab" data-toggle="tab" ><i class="fa fa-cog"></i> {{Gestion Lever/Coucher soleil}}</a></li>
			<li role="presentation" id = "menu_tab_commandes"><a href="#tab_commandes" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes équipement}}</a></li>
			<li role="presentation" id = "menu_tab_Paramètres"><a href="#tab_Paramètres" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-cogs"></i> {{Paramètres}}</a></li>
			<li role="presentation" id = "menu_tab_Ouvrants"><a href="#tab_Ouvrants" aria-controls="profile" role="tab" data-toggle="tab"><i class="icon jeedom-fenetre-ouverte"></i> {{Ouvrants}}</a></li>
			<li role="presentation" id = "menu_tab_Gestion_planifications"><a href="#tab_Gestion_planifications" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-th-list"></i> {{Gestion planifications}}</a></li>
			<!--<li class="hidden" role="presentation" id = "menu_tab_automatisation"><a href="#tab_automatisation" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-cogs"></i> {{Automatisation}}</a></li>-->
		</ul>	



		<div class="tab-content" style="overflow:auto;overflow-x: hidden;">

			<!--Eqlogic Tab-->
			<div role="tabpanel" class="tab-pane active" id="tab_eqlogic">
				<br/>
				<form class="form-horizontal col-sm-10">
					<fieldset>
						<div class="form-group">
							<label class="col-sm-4 control-label">{{Nom de l&#39équipement}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" >{{Objet parent}}</label>
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
							<label class="col-sm-4 control-label">{{Catégorie}}</label>
							<div class="col-sm-6">
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
							<label class="col-sm-4 control-label">{{Options}}</label>
							<div class="col-sm-6">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
							</div>
						</div>

						<div class="form-group" style="display : none;" >
							<label class="col-sm-4 control-label">{{Type équipement}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="Type_équipement" />
							</div>
						</div>
						<div class="form-group image_perso" style="display : none;">
							<label class="col-sm-4 control-label">{{Chemin de l&#39;image}}</label>
							<div class="col-sm-6">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="Chemin_image" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">{{Options du widget}}</label>
							<div class="col-sm-6">
								<label class="checkbox-inline">
									<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="affichage_heure"/>Afficher uniquement l&#39;heure sur le widget tant que l&#39;heure de la prochaine action est inferieure à 24 heures
								</label>
							</div>

						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">{{Automatisation du plugin}}</label>
							<div class="col-sm-6">
								<label class="checkbox-inline">
									<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="utiliser_automatisation_plugin"\>Utiliser automatisation du plugin
								</label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">{{Durée mode manuel par defaut (en minutes)}}</label>
							<div class="col-sm-3">
								<input type="number" class="eqLogicAttr form-control" placeholder="0" data-l1key="configuration" data-l2key="Duree_mode_manuel_par_defaut" title="Mettre 0 pour réactivation manuelle."/>
							</div>
						</div>
						<div class="option PAC" style="display:none">
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Commande de température ambiante}}</label>
								<div class="col-sm-6 input-group">
									<input class="eqLogicAttr form-control cmdAction" data-l1key="configuration_PAC" data-l2key="Temperature_ambiante_id"/>
									<span class="input-group-btn">
										<a class="btn btn-success btn-sm list_Cmd_info_numeric"><i class="fa fa-tasks"></i></a>
									</span>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Commande du mode réel}}</label>
								<div class="col-sm-6 input-group">
									<input class="eqLogicAttr form-control cmdAction" data-l1key="configuration_PAC" data-l2key="Mode_id"/>
									<span class="input-group-btn">
										<a class="btn btn-success btn-sm list_Cmd_info"><i class="fa fa-tasks"></i></a>
									</span>
								</div>
							</div>
							<div class="alias" style="display:none">
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Climatisation:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control " data-l1key="configuration_PAC" data-l2key="Alias_Climatisation"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Ventilation:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_PAC" data-l2key="Alias_Ventilation"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Chauffage:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_PAC" data-l2key="Alias_Chauffage"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Eteint:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_PAC" data-l2key="Alias_Eteint"/>
									</div>
								</div>
							</div>
						</div>
						<div class="option Volet" style="display:none">
							
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Type de fenêrtre}}</label>
								<div class="col-sm-6 ">
									<label class="radio-inline"><input class="eqLogicAttr" type="radio" id="fenêtre" name="Type_fenêtre" data-l1key="configuration_volet" data-l2key="type_fenêtre" />Fenêtre</label>
									<label class="radio-inline"><input class="eqLogicAttr" type="radio" id="baie" name="Type_fenêtre" data-l1key="configuration_volet" data-l2key="type_fenêtre" />Baie</label>
								</div>
							</div>
							<div class="form-group sens_ouverture" style="display:none">
								<label class="col-sm-4 control-label">{{Sens ouverture}}</label>
								<div class="col-sm-6 ">
									<label class="radio-inline"><input class="eqLogicAttr" type="radio" id="gauche" name="sens_ouveture_fenêtre" data-l1key="configuration_volet" data-l2key="sens_ouveture_fenêtre" />Gauche</label>
									<label class="radio-inline"><input class="eqLogicAttr" type="radio" id="droite" name="sens_ouveture_fenêtre" data-l1key="configuration_volet" data-l2key="sens_ouveture_fenêtre" />Droite</label>
									<label class="radio-inline"><input class="eqLogicAttr" type="radio" id="gauche-droite" name="sens_ouveture_fenêtre" data-l1key="configuration_volet" data-l2key="sens_ouveture_fenêtre" />Deux sens</label>
								</div>
							</div>
							
							
							<div class='ouverture_gauche'>
								<fieldset style="border: var(--txt-color);border-style: solid;padding-bottom:20px;margin-bottom:10px">
    							<legend style="width: auto;text-align:center!important;padding:5px!important">Détecteur d'ouverture gauche</legend>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Commande de l&#39;ouverture}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control cmdAction" data-l1key="configuration_volet" data-l2key="Etat_fenêtre_gauche_id"/>
										<span class="input-group-btn">
											<a class="btn btn-success btn-sm list_Cmd_info_binary"><i class="fa fa-tasks"></i></a>
										</span>
									</div>
								</div>	
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Commande de la batterie}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control cmdAction" data-l1key="configuration_volet" data-l2key="Niveau_batterie_gauche_id"/>
										<span class="input-group-btn">
											<a class="btn btn-success btn-sm list_Cmd_info_numeric"><i class="fa fa-tasks"></i></a>
										</span>
									</div>
								</div>
								</fieldset>
							</div>
							<div class='ouverture_droite' style='display:none'>
								<fieldset style="border: var(--txt-color);border-style: solid;padding-bottom:20px;margin-bottom:10px">
    							<legend style="width: auto;text-align:center!important;padding:5px!important">Détecteur d'ouverture droite</legend>
								
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Commande de l&#39;ouverture}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control cmdAction" data-l1key="configuration_volet" data-l2key="Etat_fenêtre_droite_id"/>
										<span class="input-group-btn">
											<a class="btn btn-success btn-sm list_Cmd_info_binary"><i class="fa fa-tasks"></i></a>
										</span>
									</div>
								</div>	
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Commande de la batterie}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control cmdAction" data-l1key="configuration_volet" data-l2key="Niveau_batterie_droite_id"/>
										<span class="input-group-btn">
											<a class="btn btn-success btn-sm list_Cmd_info_numeric"><i class="fa fa-tasks"></i></a>
										</span>
									</div>
								</div>
								</fieldset>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Commande de l&#39;état du volet}}</label>
								<div class="col-sm-6 input-group">
									<input class="eqLogicAttr form-control cmdAction" data-l1key="configuration_volet" data-l2key="etat_id" title="Laissez vide pour utiliser l'état de la planification."/>
									<span class="input-group-btn">
										<a class="btn btn-success btn-sm list_Cmd_info"><i class="fa fa-tasks"></i></a>
									</span>
								</div>
							</div>
							<div class="alias" style="display:none">
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Ouvert:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_volet" data-l2key="Alias_Ouvert"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode My:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_volet" data-l2key="Alias_My"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Fermé:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_volet" data-l2key="Alias_Ferme"/>
									</div>
								</div>

							</div>
						</div>
						<div class="option Prise" style="display:none">							
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Commande de l&#39;état de la prise}}</label>
								<div class="col-sm-6 input-group">
									<input class="eqLogicAttr form-control  cmdAction" data-l1key="configuration_prise" data-l2key="etat_id" title="Laissez vide pour utiliser l'état de la planification."/>
									<span class="input-group-btn">
										<a class="btn btn-success btn-sm list_Cmd_info"><i class="fa fa-tasks"></i></a>
									</span>
								</div>
							</div>
							<div class="alias" style="display:none">
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode On:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control " data-l1key="configuration_prise" data-l2key="Alias_On"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Off:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_prise" data-l2key="Alias_Off"/>
									</div>
								</div>
							</div>
						</div>
						<div class="option Chauffage" style="display:none">							
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Commande de l&#39;état du chauffage}}</label>
								<div class="col-sm-6 input-group">
									<input class="eqLogicAttr form-control cmdAction" data-l1key="configuration_chauffage" data-l2key="etat_id" title="Laissez vide pour utiliser l'état de la planification."/>
									<span class="input-group-btn">
										<a class="btn btn-success btn-sm list_Cmd_info"><i class="fa fa-tasks"></i></a>
									</span>
								</div>
							</div>
							<div class="alias" style="display:none">
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Auto:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_chauffage" data-l2key="Alias_Auto"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Confort:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_chauffage" data-l2key="Alias_Confort"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode ECO:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_chauffage" data-l2key="Alias_Eco"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Hors-gel:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_chauffage" data-l2key="Alias_Hg"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Alias du mode Arrêt:}}</label>
									<div class="col-sm-6 input-group">
										<input class="eqLogicAttr form-control" data-l1key="configuration_chauffage" data-l2key="Alias_Arret"/>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
					
					  
				</form>

				<form class="form-horizontal col-sm-2">
					<fieldset>
						<div class="form-group">
							<div>
								<img src="<?php echo($plugin->getPathImgIcon())?>" id="img_planificationModel" style="height:130px;" />
							</div>
								<div>
									<a class="btn btn-sm btn-default bt_modifier_image"><i class="fa jeedomapp-folder"></i> {{Modifier l&#39;image}}</a>
								</div>
								<div>
									<a class="btn btn-sm btn-warning bt_image_défaut" style="display:none"><i class="fas fa-folder-minus"></i> {{Remettre l&#39;image par défaut}}</a>
								
								</div>

						</div>
					</fieldset>
				</form>

				<hr>
			</div>

			<!--planifications Tab-->
			<div role="tabpanel" class="tab-pane" id="tab_planifications">
				<a class="btn btn-sm btn-success pull-right bt_ajouter_planification" style="margin-top: 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter planification}}</a>
				<br/><br/>
				<div id="div_planifications" class="panel-group"></div>
			</div>

			<!--Gestion-->
			<div role="tabpanel" class="tab-pane" id="tab_gestion_heures_lever_coucher">

				<div class="col-sm-12">
					<label class="col-sm-3 control-label" style="height: 31px;margin: 0px;top: 5px;">{{Jour à configurer}}</label>

					<select class="selection_jour col-sm-3">
						<option value="Lundi" selected>{{Lundi}}</option>
						<option value="Mardi">{{Mardi}}</option>
						<option value="Mercredi">{{Mercredi}}</option>
						<option value="Jeudi">{{Jeudi}}</option>
						<option value="Vendredi">{{Vendredi}}</option>
						<option value="Samedi">{{Samedi}}</option>
						<option value="Dimanche">{{Dimanche}}</option>
					</select>
					<a class="btn btn-sm btn-default bt_copier_lever_coucher" style="margin: 2px"> {{Dupliquer pour le reste de la semaine}}</a>
				</div>

				<?php
				$display='block';
				for ($i = 1; $i <= 7; $i++) {
					switch ($i){
						case 1:
							$jour = "Lundi";
							break;
						case 2:
							$jour = "Mardi";
							break;
						case 3:
							$jour = "Mercredi";
							break;
						case 4:
							$jour = "Jeudi";
							break;
						case 5:
							$jour = "Vendredi";
							break;
						case 6:
							$jour = "Samedi";
							break;
						case 7:
							$jour = "Dimanche";
							break;
					}
					if ($jour != 'Lundi'){$display="none";}
					echo '<div class="' . $jour .' col-sm-12" style="display:'.  $display . '">';
					echo '<div class="col-sm-6 Lever">
								<form class="form-horizontal">
									<legend> <i class="fa fa-sun"></i> Gestion lever de soleil</legend>
									<fieldset>
										<div class="well">
											<div class="form-group ">
												<label class="col-sm-7 control-label">{{Heure de lever de soleil}}</label>
												<div class="col-sm-2">
													<span class="HeureLever ' . $jour . ' ' . $i . ' label label-success"></span>
												</div>
											</div>
											<div class="form-group ">
												<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
												<div class="col-sm-2">
													<span class="Heure_action_suivante_Lever ' . $jour . ' ' . $i . ' label label-warning"></span>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-7 control-label">{{Heure minimum}}</label>
												<div class="col-sm-5">
													<div class="input-group">
														<input class="in_timepicker HeureLeverMin '. $jour . ' ' . $i .' eqLogicAttr input-sm" data-l1key="configuration_lever_coucher" data-l2key="LeverMin_'.$jour.'">
															<a class="btn btn-default bt_afficher_timepicker btn-sm" style="background-color: var(--form-bg-color) !important;"><i class="icon far fa-clock"></i></a>
														</input>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-7 control-label">{{Heure maximum}}</label>
												<div class="col-sm-5">
													<div class="input-group">
														<input class="in_timepicker HeureLeverMax ' . $jour . ' ' . $i .' eqLogicAttr input-sm" data-l1key="configuration_lever_coucher" data-l2key="LeverMax_'.$jour.'">
															<a class="btn btn-default bt_afficher_timepicker btn-sm" style="background-color: var(--form-bg-color) !important;"><i class="icon far fa-clock"></i></a>
														</input>
													</div>
												</div>
											</div>
										</div>
									</fieldset>
								</form>
							</div>
							<div class="col-sm-6 Coucher">
								<form class="form-horizontal">
									<legend><i class="fa fa-moon"></i> Gestion coucher de soleil</legend>
									<fieldset>
										<div class="well">
											<div class="form-group ">
												<label class="col-sm-7 control-label">{{Heure de coucher de soleil}}</label>
												<div class="col-sm-2">
													<span class="HeureCoucher ' . $jour . ' ' . $i . ' label label-success"></span>
												</div>
											</div>
											<div class="form-group ">
												<label class="col-sm-7 control-label">{{Heure prochaine action}}</label>
												<div class="col-sm-2">
													<span class="Heure_action_suivante_Coucher ' . $jour . ' ' . $i . ' label label-warning"></span>
												</div>
											</div>
											<div class="form-group">

												<label class="col-sm-7 control-label">{{Heure minimum}}</label>
												<div class="col-sm-5">
													<div class="input-group">
														<input class="in_timepicker HeureCoucherMin ' . $jour . ' ' . $i . ' eqLogicAttr input-sm" data-l1key="configuration_lever_coucher" data-l2key="CoucherMin_'.$jour.'">
															<a class="btn btn-default bt_afficher_timepicker btn-sm" style="background-color: var(--form-bg-color) !important;"><i class="icon far fa-clock"></i></a>
														</input>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-7 control-label">{{Heure maximum}}</label>
												<div class="col-sm-5">
													<div class="input-group">
														<input class="in_timepicker HeureCoucherMax ' . $jour . ' ' . $i . ' eqLogicAttr input-sm" data-l1key="configuration_lever_coucher" data-l2key="CoucherMax_'.$jour.'">
															<a class="btn btn-default bt_afficher_timepicker btn-sm" style="background-color: var(--form-bg-color) !important;"><i class="icon far fa-clock"></i></a>
														</input>
													</div>
												</div>
											</div>
										</div>
									</fieldset>
								</form>
							</div>	
						</div>';
						}
						?>
			</div>

			<!--Commands Tab-->
			<div role="tabpanel" class="tab-pane" id="tab_commandes">
				<legend><i class="fa fa-list-alt"></i>  {{Commandes Infos}}</legend>
				<table id="table_infos" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="20%">{{Nom}}</th><th width="55%" align="center">{{Etat}}</th><th width="15%" align="right">{{Options}}</th><th width="20%" align="right"></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<legend><i class="fa fa-list-alt"></i>  {{Commandes Actions}}<a class="btn btn-sm btn-success bt_ajouter_commande pull-right" style="display:none"><i class="fa fa-plus-circle"></i> {{Ajouter une commande}}</a></legend>
				<table id="table_actions" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="20%">{{Nom}}</th><th width="55%" align="center">{{Commande}}</th><th width="15%" align="right">{{Couleur}}</th><th width="20%" align="right"></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>

			<div role="tabpanel" class="tab-pane Paramètres" id="tab_Paramètres">
				<div class="col-sm-12 PAC" style="display:none">	
					<legend><i class="fab fa-hotjar"></i>  {{Chauffage}}<a class="btn btn-sm btn-success bt_ajouter_commande pull-right" style="display:none"><i class="fa fa-plus-circle"></i> {{Ajouter une commande}}</a></legend>
					<table id="table_chauffage" class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th width="20%">{{Nom}}</th><th width="55%" align="center">{{Etat}}</th><th width="15%" align="right">{{Options}}</th><th width="20%" align="right"></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<div class="col-sm-12 PAC" style="display:none">
					<fieldset>
						<div class="form-group">
							<label class="col-sm-6 control-label">{{Type d'équipement qui pilote la PAC}}</label>
							<div class="col-sm-6">
								<select required class="expressionAttr form-control" data-l1key="Paramètres" data-l2key="Type_équipement_pilote">
									<option value="Aucun" selected>{{Aucun}}</option>
									<option value="broadlink">{{Broadlink}}</option>
								</select>
							</div>
						</div>
						<div class="form-group options_type_équipement_pilote" style="display:none">
							<label class="col-sm-6 control-label">{{Equipement qui pilote la PAC}}</label>
							<div class="col-sm-6">
								<select required class="expressionAttr form-control" data-l1key="Paramètres" data-l2key="Equipement_pilote">
									
								</select>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane Gestion_planifications" id="tab_Gestion_planifications">
				<div class="col-sm-12">
					<form class="form-horizontal">
						<fieldset>
							<div class="alert alert-warning col-xs-10 col-xs-offset-1">
								Les conditions si dessous servirons à déterminer la planification à utiliser
							</div>
							<div id="div_GestionPlanifications" class="col-xs-12">
							
							</div>
						</fieldset>
					</form>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane Ouvrants" id="tab_Ouvrants">
				<div class="col-sm-12 PAC Volet " style="display: none;">		
					<form class="form-horizontal">
						<fieldset>
							<div class="alert alert-warning col-xs-10 col-xs-offset-1 PAC">
								{{Déclarer les ouvrants concernés par cette planification}} <em>{{(portes, fenêtres, etc...)}}</em> {{permettra d'arrêter la PAC.}}
							</div>
							<div class="alert alert-warning col-xs-10 col-xs-offset-1 Volet">
								{{Déclarer les ouvrants concernés par cette planification}} <em>{{(portes, fenêtres, etc...)}}</em> {{permettra de laisser ouvert le volet.}}
							</div>
							<a class="btn btn-success ajoutOuvrant col-xs-6 col-xs-offset-3" data-type="ouvrants"><i class="fas fa-plus-circle"></i> {{Ajouter un ouvrant}}</a>
							
							<div id="div_ouvrants" class="col-xs-12">

							</div>
						</fieldset>
					</form>
				</div>
			</div>
			
			<!--Automatisation Tab
			<div role="tabpanel" class="tab-pane" id="tab_automatisation">
				<br/>
				
				

				<script>
					function showTab(event, tabId) {
						var i, tabcontent, tablinks;
						tabcontent = document.getElementsByClassName("tab-pane1");
						for (i = 0; i < tabcontent.length; i++) {
							tabcontent[i].classList.remove("active");
						}
						navtab1 =document.querySelector(".nav-tab1").querySelectorAll('a')
						for (i = 0; i < navtab1.length; i++) {
							navtab1[i].classList.remove("active");
						}			
						document.getElementById(tabId).classList.add("active");
						document.querySelector('.' + tabId ).classList.add("active");
						event.preventDefault();
					}
				
				</script>
				<ul class="nav nav-tabs nav-tab1" role="tablist" style="display:inline-block">
					<li role="presentation">
						<a href="#Paramètres" class= "Paramètres active" onclick="showTab(event, 'Paramètres')"><i class="fas fa-cogs"></i> Paramètres</a>
					</li>
					
					
					<li role="presentation">
						<a href="#Ouvrants" class= "Ouvrants" onclick="showTab(event, 'Ouvrants')"><i class="icon jeedom-fenetre-ouverte"></i> Ouvrants</a>
					</li>
					<li role="presentation">
						<a href="#Gestion_planifications" class= "Gestion_planifications" onclick="showTab(event, 'Gestion_planifications')"><i class="fas fa-th-list"></i> Gestion planifications</a>
					</li>
				</ul>

				<div class="tab-content" style="overflow:auto;overflow-x: hidden;">

				
					<div  class="tab-pane1 active" id="Paramètres">
					<br/>
						<legend><i class="fab fa-hotjar"></i>  {{Chauffage}}<a class="btn btn-sm btn-success bt_ajouter_commande pull-right" style="display:none"><i class="fa fa-plus-circle"></i> {{Ajouter une commande}}</a></legend>
				<table id="table_chauffage" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="20%">{{Nom}}</th><th width="55%" align="center">{{Etat}}</th><th width="15%" align="right">{{Options}}</th><th width="20%" align="right"></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<br/>
				<br/>
				<br/>
				<br/>



						<form class="form-horizontal col-sm-10">
							<fieldset>
								<div class="col-sm-10">
									<fieldset>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Type d'équipement qui pilote la PAC}}</label>
											<div class="col-sm-6">
												<select required class="expressionAttr form-control" data-l1key="Paramètres" data-l2key="Type_équipement_pilote">
													<option value="Aucun" selected>{{Aucun}}</option>
													<option value="broadlink">{{Broadlink}}</option>
												</select>
											</div>
										</div>
										<div class="form-group options_type_équipement_pilote" style="display:none">
											<label class="col-sm-6 control-label"></label>
											<div class="col-sm-6">
												<select required class="expressionAttr form-control" data-l1key="Paramètres" data-l2key="Equipement_pilote">
													
												</select>
											</div>
										</div>
									</fieldset>
								</div>
								<div class="col-sm-10">
									<fieldset>
										<legend><i class="fab fa-hotjar"></i> Chauffage</legend>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Delta température mode chauffage ECO}}</label>
											<div class="col-sm-3 input-group">
												<input  type="number" class="expressionAttr form-control" placeholder="2" data-l1key="Paramètres" data-l2key="Delta_chauffage_eco"/>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Delta température mode chauffage boost}}</label>
											<div class="col-sm-3 input-group">
												<input  type="number" class="expressionAttr form-control" placeholder="1" data-l1key="Paramètres" data-l2key="Delta_chauffage_boost"/>
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Température extérieure en dessous de laquelle la PAC doit être en chauffage continu}}</label>
											<div class="col-sm-3 input-group">
												<input  type="number" class="expressionAttr form-control" placeholder="5" data-l1key="Paramètres" data-l2key="Température_ext_continu"/>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Numéro de semaine minimum pour activer le chauffage}}</label>
											<div class="col-sm-3 input-group">
												<input  type="number" class="expressionAttr form-control" placeholder="39" data-l1key="Paramètres" data-l2key="Numero_semaine_min_chauffage"/>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Numéro de semaine maxmimum pour activer le chauffage}}</label>
											<div class="col-sm-3 input-group">
												<input  type="number" class="expressionAttr form-control" placeholder="18" data-l1key="Paramètres" data-l2key="Numero_semaine_max_chauffage"/>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Température extérieure en dessous de laquelle la PAC peut être en chauffage}}</label>
											<div class="col-sm-3 input-group">
												<input  type="number" class="expressionAttr form-control" placeholder="18" data-l1key="Paramètres" data-l2key="Température_ext_chauffage"/>
											</div>
										</div>
									</fieldset>
								</div>
								<div class="col-sm-10">
									<fieldset>
										<legend><i class="fas fa-icicles"></i> Climatisation</legend>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Delta température mode climatisation boost}}</label>
											<div class="col-sm-3 input-group">
												<input  type="number" class="expressionAttr form-control" placeholder="1" data-l1key="Paramètres" data-l2key="Delta_climatisation_boost"/>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Température extérieure en dessus de laquelle la PAC peut être en climatisation}}</label>
											<div class="col-sm-3 input-group">
												<input  type="number" class="expressionAttr form-control" placeholder="22" data-l1key="Paramètres" data-l2key="Température_ext_climatisation"/>
											</div>
										</div>
									</fieldset>
								</div>
								
								
								
							</fieldset>
						</form>
						
					</div>
					
					
					<div class="tab-pane1" id="Ouvrants">
						<br/>
						<form class="form-horizontal">
							<fieldset>
								<div class="alert alert-warning col-xs-10 col-xs-offset-1">
									{{Déclarer les ouvrants concernés par cette planification}} <em>{{(portes, fenêtres, etc...)}}</em> {{permettra d'arrêter la PAC.}}
								</div>
								<a class="btn btn-success ajoutOuvrant col-xs-6 col-xs-offset-3" data-type="ouvrants"><i class="fas fa-plus-circle"></i> {{Ajouter un ouvrant}}</a>
								
								<div id="div_ouvrants" class="col-xs-12">

								</div>
							</fieldset>
						</form>
					
					</div>
					<div class="tab-pane1" id="Gestion_planifications">
						<br/>
						<form class="form-horizontal">
							<fieldset>
								<div class="alert alert-warning col-xs-10 col-xs-offset-1">
									Les conditions si dessous servirons à déterminer la planification à utiliser
								</div>
								<div id="div_GestionPlanifications" class="col-xs-12">
								
								</div>
							</fieldset>
						</form>
					</div>
				</div>	
			</div>-->
		</div>
	</div>

</div>



<?php 

	
   
    	
	include_file('desktop', 'planification', 'js', 'planification');
 	include_file('desktop', 'planification', 'css', 'planification');
 	include_file('core', 'plugin.template', 'js');
?>