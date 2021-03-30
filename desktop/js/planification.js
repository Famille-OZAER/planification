JSONCLIPBOARD = null
document.addEventListener("click", closeAllSelect);

$('.ajouter_eqlogic').on('click', function () {
	var dialog_title = '{{Choisissez le type d\'équipement que souhaitez ajouter}}';
	
	var dialog_message =
	
	'<form class="form-horizontal onsubmit="return false;"> '+
	'<div> <div class="radio"> <label > ' +
	'<input type="radio" name="type" id="Volet" value="Volet" checked="checked"> {{Volet}} </label> ' +
	'</div>' +
	'<div class="radio"> <label > ' +
	'<input type="radio" name="type" id="PAC" value="PAC"> {{Pompe à chaleur}}</label> ' +
	'</div> ' +
	'<div class="radio"> <label > ' +
	'<input type="radio" name="type" id="Poele" value="Poele"> {{Poêle à granules}}</label> ' +
	'</div> ' +
	'<div class="radio"> <label > ' +
	'<input type="radio" name="type" id="Prise" value="Prise"> {{Prise}}</label> ' +
	'</div> ' +
	'<div class="radio"> <label > ' +
	'<input type="radio" name="type" id="Autre" value="Autre" placeholder="Nom de l\'équipement"> {{Autre}}</label> ' +
	'</div> <br>' +
	'<div class="input">' +
	'<input class="col-sm-8" type="text" placeholder="Nom de l\'équipement" name="nom" id="nom" >  ' +
	
	'</div> <br>' +
	'</div>'+
	'</form>';
	bootbox.dialog({
		title: dialog_title,
		message: dialog_message,
		buttons: {
			"{{Annuler}}": {
				className: "btn-danger",
				callback: function () {
				}
			},
			success: {
				label: "{{Valider}}",
				className: "btn-success",
				
				callback: function () {
					if($("input[name='nom']").val() == ""){
						$('#div_alert').showAlert({message: "Le nom de l'équipement ne peut pas être vide.", level: 'danger'});
						return;
					}
					$.ajax({
						type: "POST",
						url: "plugins/planification/core/ajax/planification.ajax.php",
						data: {
							action : "Ajout_equipement",
							nom : $("input[name='nom']").val(),
							type : $("input[name='type']:checked").val()
						},
						global: true,
						async: false,
						error: function (request, status, error) {
							handleAjaxError(request, status, error);
						},
						success: function (data) {
							if (data.state != 'ok') {
								$('#div_alert').showAlert({message: data.result, level: 'danger'});
								
							}
							window.location.href = 'index.php?v=d&p=planification&m=planification&id=' + data.result;
							
						}
					});	
				}
			}
		},
	})
})

//équipement

$('#tab_eqlogic .eqLogicAttr[data-l1key=configuration][data-l2key=type]').on('change',function(){
	var img="plugins/planification/core/img/autre.png"
   if ($(this).value() == "PAC"){
		$(".poele").hide()
		$(".PAC").show()
		img='plugins/planification/core/img/pac.png'
   }else if ($(this).value() == "Volet"){
		$(".poele").hide()
		$(".PAC").hide()
		img="plugins/planification/core/img/volet.png"
   }else if ($(this).value() == "Chauffage"){
		$(".poele").hide()
		$(".PAC").hide()
		img="plugins/planification/core/img/chauffage.png"
   }else if ($(this).value() == "Poele"){
		$(".poele").show()
		$(".PAC").hide()
		img="plugins/planification/core/img/poele.png"
   	}else if ($(this).value() == "Prise"){
		$(".poele").hide()
		$(".PAC").hide()
		img="plugins/planification/core/img/prise.png"
	}
   	$.ajax({
		url:img,
		error: function(){$('#img_planificationModel').attr('src',"plugins/planification/core/img/autre.png")
		},
		success: function(){
			$('#img_planificationModel').attr('src',img)			
		}
	});
})
$('#tab_eqlogic').on('click','.listCmdTemperature',  function () {
//$("body").delegate(".listCmdTemperature", 'click', function () {
	var el = $(this).closest('div').find('.eqLogicAttr[data-l2key=temperature_id]');
	jeedom.cmd.getSelectModal({cmd: {type: 'info',subType:"numeric"}}, function (result) {
		el.value(result.human);
	});
});
$('#tab_eqlogic').on('click','.listCmdEtat',  function () {
//$("body").delegate(".listCmdEtat", 'click', function () {
	var el = $(this).closest('div').find('.eqLogicAttr[data-l2key=etat_allume_id]');
	jeedom.cmd.getSelectModal({cmd: {type: 'info',subType:"binary"}}, function (result) {
		el.value(result.human);
	});
});
$('#tab_eqlogic').on('click','.listCmdInfoPAC',  function () {
	var el = $(this).closest('div').find('.eqLogicAttr[data-l2key=etat_pac_id]');
	jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
		el.value(result.human);
	});
});
$('#tab_eqlogic').on('click','.listCmdEtatBoost',  function () {
	var el = $(this).closest('div').find('.eqLogicAttr[data-l2key=etat_boost_id]');
	jeedom.cmd.getSelectModal({cmd: {type: 'info',subType:"binary"}}, function (result) {
		el.value(result.human);
	});
});
//commandes
$('#tab_commandes').on('click','.select-selected',  function (e) {
	modifyWithoutSave = true;
	e.stopPropagation();
	closeAllSelect(this);
	this.nextSibling.classList.toggle("select-hide");
	this.classList.toggle("select-arrow-active");
});
$('#tab_commandes').on('click','.select-items div',  function () {
	modifyWithoutSave = true;
	select = this.parentNode.previousSibling;
	select.innerHTML = this.innerHTML;
	select.classList.remove(recup_class_couleur(select.classList))
	select.classList.add(recup_class_couleur(this.classList))
	select.setAttribute("Id",this.getAttribute("Id"))
	y = this.parentNode.getElementsByClassName("same-as-selected");
	for (k = 0; k < y.length; k++) {
		y[k].classList.remove("same-as-selected")
	}
	this.classList.add("same-as-selected")
	select.click();
})
$('#tab_commandes').on('click','.listCmdAction',  function () {
	//$("body").delegate(".listCmdAction", 'click', function() {
		var el = $(this).closest('div div').find('.cmdAttr[data-l2key=commande]');
		jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
			el.value(result.human);
			jeedom.cmd.displayActionOption(el.value(), '', function(html) {
				el.closest('div td').find('.actionOptions').html(html);
			});
		});
});
$('#tab_commandes').on('click','.listAction',  function () {
		var el = $(this).closest('div div').find('.cmdAttr[data-l2key=commande]');
		jeedom.getSelectActionModal({}, function (result) {
			el.value(result.human);
			jeedom.cmd.displayActionOption(el.value(), '', function (html) {
				el.closest('div td').find('.actionOptions').html(html);
				taAutosize();
			});
		});
});
$('#tab_commandes').on('focusout','.cmdAction',  function () {
	var el = $(this);
	var expression = el.closest('td').getValues('.expressionAttr');
	jeedom.cmd.displayActionOption(el.value(), expression[0].options, function (html) {
		el.closest('div td').find('.actionOptions').html(html);
		taAutosize();
	});
});

$("#tab_commandes #table_actions").sortable({axis: "y", cursor: "move", items: ".cmd",distance:30, placeholder: "highlight", tolerance: "intersect", forcePlaceholderSize: true})
$("#tab_commandes #table_infos").sortable({axis: "y", cursor: "move", items: ".cmd",distance:30, placeholder: "highlight", tolerance: "intersect", forcePlaceholderSize: true})
 
//planifications:
$("#tab_planifications #div_planifications").sortable({
	axis: "y", cursor: "move", items: ".planification", handle: ".panel-heading", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true
})
$("#tab_planifications").on('click', '.bt_ajouter_planification',function () {
	bootbox.prompt({
        title: "Veuillez inserer le nouveau nom de la planification à ajouter.",
        buttons: {
            confirm: {label: 'Ajouter', className: 'btn-success'},
            cancel: {label: 'Annuler', className: 'btn-danger'}
        },
        callback: function (resultat) {
            if (resultat!== null && resultat != '') {
				modifyWithoutSave = true;
               Ajoutplanification({nom: resultat})
            }
        }
	})
})
$("#tab_planifications").on('click', '.bt_supprimer_planification',function () {
    Ce_progamme = $(this).closest('.planification')
    bootbox.confirm({
        message: "Voulez vous vraiment supprimer cette planification ?",
        buttons: {
            confirm: {
                label: 'Oui',
                className: 'btn-success'
            },
            cancel: {
                label: 'Non',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result === true) {
				modifyWithoutSave = true;
                Ce_progamme.remove()
            }
        }
    })
})
$('#tab_planifications').on('click','.bt_dupliquer_planification',  function () {
	var planification = $(this).closest('.planification').clone()
	
    bootbox.prompt({
        title: "Veuillez inserer le nom pour la planification dupliquée.",
        buttons: {
            confirm: {label: 'Dupliquer', className: 'btn-success'},
            cancel: {label: 'Annuler', className: 'btn-danger'}
        },
        callback: function (resultat) {
            if (resultat!== null && resultat != '') {
				modifyWithoutSave = true;
				var random = Math.floor((Math.random() * 1000000) + 1)
				planification.find('a[data-toggle=collapse]').attr('href', '#collapse' + random)
				planification.find('.panel-collapse.collapse').attr('id', 'collapse' + random)
				planification.find('.nom_planification').html(resultat)
				$(planification).attr('id',uniqId())
				$('#div_planifications').append(planification)
				$('.collapse').collapse()
            }
        }
    })
})
$('#tab_planifications').on('click','.bt_appliquer_planification',  function () {
    planification = $(this).closest('.planification')
    programName=planification.find('.nom_planification').html()
    bootbox.confirm({
        message: "Voulez vous vraiment appliquer la planification "+programName+" maintenant ?",
        buttons: {
            confirm: {
                label: 'Oui',
                className: 'btn-success'
            },
            cancel: {
                label: 'Non',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result === true) {
				jeedom.cmd.execute( {id: set_planification_Id, value: {select: programName ,Id_planification: planification.attr("Id") } })
            }
        }
    })
})
$('#tab_planifications').on('click','.bt_renommer_planification',  function () {
		var el = $(this)
	bootbox.prompt({
        title: "Veuillez inserer le nouveau nom pour la planification:" + $(this).closest('.planification').find('.nom_planification').html() +".",
        buttons: {
            confirm: {label: 'Modifier', className: 'btn-success'},
            cancel: {label: 'Annuler', className: 'btn-danger'}
        },
        callback: function (resultat) {
            if (resultat !== null && resultat != '') {
				modifyWithoutSave = true;
               el.closest('.panel.panel-default').find('span.nom_planification').text(resultat)
            }
        }
    })
})
$('#tab_planifications').on( 'click', '.bt_supprimer_perdiode',function () {
    Divjour = $(this).closest('.JourSemaine')
	$(this).closest('.Periode_jour').remove()
	modifyWithoutSave = true;
    MAJ_Graphique_jour(Divjour)
});								   
$('#tab_planifications').on('click','.bt_ajout_periode',  function () {
	modifyWithoutSave = true;
	$(this).closest("th").find(".collapsible")[0].classList.add("active")
	$(this).closest("th").find(".collapsible")[0].classList.add("cursor")
	$(this).closest("th").find(".collapsible")[0].classList.remove("no-arrow")
	Divjour=$(this).closest('th').find('.JourSemaine')

	
	var SELECT_LIST= Recup_select("planifications")
	var CMD_LIST=Recup_liste_commandes_planification()
	Couleur="erreur"
	Nom=""
	Couleur="couleur-" + CMD_LIST[0].couleur
	Nom=CMD_LIST[0].Id
	Id=CMD_LIST[0].Id
	var element = SELECT_LIST.replace("#COULEUR#",Couleur);
	element=element.replace("#VALUE#",Nom)
	element=element.replace("#ID#",Id)
	Ajout_Periode(element, Divjour)

	MAJ_Graphique_jour(Divjour)
	Divjour.css("max-height","fit-content")
	Divjour.css("overflow","visible")
	DivprogramDays=$(this).closest('.div_programDays')
	DivprogramDays.css("overflow","visible")
	DivprogramDays.css("max-height","fit-content")
	Divplanification=$(this).closest('.planification-body')
	Divplanification.css("overflow","visible")
	Divplanification.css("max-height","fit-content")
})
$('#tab_planifications').on('click','.bt_copier_jour',  function () {
    var jour = $(this).closest('th').find('.JourSemaine')
	JSONCLIPBOARD = { data : []}
    jour.find('.Periode_jour').each(function  () {
		 if($(this).find('.checkbox_lever_coucher').prop("checked")){
			type_periode=$(this).find('.select_lever_coucher').val()
		}else{
			type_periode="heure_fixe"
		}

		
        debut_periode = $(this).find('.clock-timepicker').val()
		Id = $(this).find('.select-selected').attr("id")
		Nom=$(this).find('.select-selected span')[0].innerHTML
		Couleur=recup_class_couleur($(this).find('.select-selected')[0].classList)
		JSONCLIPBOARD.data.push({type_periode,debut_periode, Id,Nom,Couleur})
    })
})
$('#tab_planifications').on('click','.bt_coller_jour',  function () {
	if (JSONCLIPBOARD == null) return
	modifyWithoutSave = true;
	Divjour = $(this).closest('th').find('.JourSemaine')
	Divjour.find('.Periode_jour').each(function  () {
        $(this).remove()
	})
	var SELECT_LIST= Recup_select("planifications")
    JSONCLIPBOARD.data.forEach(function(periode) {
		
		Type_periode=periode["type_periode"]

		Couleur=periode["Couleur"]
		Nom=periode["Id"]
		Id=periode["Id"]
		var element = SELECT_LIST.replace("#COULEUR#",Couleur);
		element=element.replace("#VALUE#",Nom)
		element=element.replace("#ID#",Id)
		Ajout_Periode(element,Divjour, periode.debut_periode,null,Type_periode)
	
	})
	Divjour.css("overflow","visible")
	Divjour.css("max-height","fit-content")
	$(this).closest("th").find(".collapsible")[0].classList.add("active")
	$(this).closest("th").find(".collapsible")[0].classList.add("cursor")
	$(this).closest("th").find(".collapsible")[0].classList.remove("no-arrow")
	MAJ_Graphique_jour(Divjour)
})
$('#tab_planifications').on('click','.bt_vider_jour',  function () {
	modifyWithoutSave = true;
	$(this).closest("th").find(".collapsible")[0].classList.remove("active")
	$(this).closest("th").find(".collapsible")[0].classList.remove("cursor")
	$(this).closest("th").find(".collapsible")[0].classList.add("no-arrow")
	Divjour=$(this).closest('th').find('.JourSemaine')
	Divjour.css("overflow","hidden")
	Divjour.css("max-height",0)
	Divjour.find('.Periode_jour').each(function  () {
        $(this).remove()
	})
	MAJ_Graphique_jour(Divjour)
})
$('#tab_planifications').on('click','.collapsible',  function () {
	this.classList.toggle("active");
	var Divjour=$(this).closest("th").find(".JourSemaine")
	
	if(Divjour.css("overflow")=="visible"){
		Divjour.css("max-height","0px"	)
		Divjour.css("overflow","hidden")
	}else{
		Divjour.css("overflow","visible")
		Divjour.css("max-height","fit-content")
	}
})
$('#tab_planifications').on('click','.planification_collapsible',  function () {
	this.classList.toggle("active");
	
	var DivPlanification=$(this).closest(".planification").find(".planification-body")

	
	if(DivPlanification.css("overflow")=="visible"){
		DivPlanification.css("max-height","0px"	)
		DivPlanification.css("overflow","hidden")
	}else{
		DivPlanification.css("overflow","visible")
		DivPlanification.css("max-height","fit-content")
	}
	var DivProgrammation=$(this).closest(".planification").find(".planification-body").find(".div_programDays")
	if(DivProgrammation.css("overflow")=="visible"){
		DivProgrammation.css("max-height","0px"	)
		DivProgrammation.css("overflow","hidden")
	}else{
		DivProgrammation.css("overflow","visible")
		DivProgrammation.css("max-height","fit-content")
	}
	var DivgraphJours=$(this).closest(".planification").find(".planification-body").find(".graphJours")
	if(DivgraphJours.css("overflow")=="visible"){
		DivgraphJours.css("max-height","0px")
		DivgraphJours.css("overflow","hidden")
	}else{
		DivgraphJours.css("overflow","visible")
		DivgraphJours.css("max-height","fit-content")
	}

})
$('#tab_planifications').on('click','.select-selected',  function (e) {
	/* When the select box is clicked, close any other select boxes,
	and open/close the current select box: */
	modifyWithoutSave = true;
	e.stopPropagation();
	closeAllSelect(this);
	this.nextSibling.classList.toggle("select-hide");
	this.classList.toggle("select-arrow-active");
});
$('#tab_planifications').on('click','.select-items div',  function () {
	modifyWithoutSave = true;
	select = this.parentNode.previousSibling;
	select.innerHTML = this.innerHTML;
	select.classList.remove(recup_class_couleur(select.classList))
	select.classList.add(recup_class_couleur(this.classList))
	select.setAttribute("Id",this.getAttribute("Id"))
	y = this.parentNode.getElementsByClassName("same-as-selected");
	for (k = 0; k < y.length; k++) {
		y[k].classList.remove("same-as-selected")
	}
	this.classList.add("same-as-selected")
	
	MAJ_Graphique_jour($(this).closest('.JourSemaine'))
	select.click();
})
$('#tab_planifications').on('change','.select_lever_coucher',  function () {
//$("body").delegate( '.select_lever_coucher',"change" ,function () {
	modifyWithoutSave = true;
	var Divjour = $(this).closest('.JourSemaine')
	var Periode = $(this).closest('.Periode_jour')
	var numero_cette_periode=0
	var autre_valeur_select_lever_coucher = ""
	Periode.prop("classList").forEach(function(classe){
		if (classe.includes("periode")){
			numero_cette_periode = classe.substr(7, classe.length-7)
		}
	})
	
	Divjour.find('.checkbox_lever_coucher').each(function(checkbox) {
		if ( $(this).is (':checked')){
			var cette_periode=$(this).closest('.Periode_jour')
			cette_periode.prop("classList").forEach(function(classe){
				if (classe.includes("periode")){
					if (classe.substr(7, classe.length-7) != numero_cette_periode){
						autre_valeur_select_lever_coucher = cette_periode.find('.select_lever_coucher').value()
					}
				}
			})
		}
	})
	
	if(this.value == "lever" &&  autre_valeur_select_lever_coucher == "lever"){
		modifyWithoutSave = false;
		Periode.find('.select_lever_coucher').prop('selectedIndex',1)
	}
	if(this.value == "coucher" &&  autre_valeur_select_lever_coucher == "coucher"){
		modifyWithoutSave = false;
		Periode.find('.select_lever_coucher').prop('selectedIndex',0)
	}
	if(Periode.find('.select_lever_coucher').prop('selectedIndex')  == 0){
		if($(Divjour).hasClass("Lundi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Lundi')[0].innerText
		}else if($(Divjour).hasClass("Mardi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Mardi')[0].innerText
		}else if($(Divjour).hasClass("Mercredi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Mercredi')[0].innerText
		}else if($(Divjour).hasClass("Jeudi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Jeudi')[0].innerText
		}else if($(Divjour).hasClass("Vendredi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Vendredi')[0].innerText
		}else if($(Divjour).hasClass("Samedi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Samedi')[0].innerText
		}else if($(Divjour).hasClass("Dimanche")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Dimanche')[0].innerText
		}
	}else{
		if($(Divjour).hasClass("Lundi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Lundi')[0].innerText
		}else if($(Divjour).hasClass("Mardi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mardi')[0].innerText
		}else if($(Divjour).hasClass("Mercredi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mercredi')[0].innerText
		}else if($(Divjour).hasClass("Jeudi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Jeudi')[0].innerText
		}else if($(Divjour).hasClass("Vendredi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Vendredi')[0].innerText
		}else if($(Divjour).hasClass("Samedi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Samedi')[0].innerText
		}else if($(Divjour).hasClass("Dimanche")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Dimanche')[0].innerText
		}
	}
	Periode.find('.clock-timepicker').attr("oldvalue",Periode.find('.clock-timepicker').attr("value"))
	Periode.find('.clock-timepicker').attr("time_int",(parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
	Periode.find('.clock-timepicker').attr("value",time)
	triage_jour(Divjour)
	MAJ_Graphique_jour(Divjour)
});
$('#tab_planifications').on('click','.checkbox_lever_coucher',  function () {
//$("body").delegate( '.checkbox_lever_coucher',"change" ,function () {
	
	var Divjour = $(this).closest('.JourSemaine ')
	var Periode = $(this).closest('.Periode_jour')
	var numero_cette_periode=0
	var numero_autre_periode=0
	var valeur_select_lever_coucher = ""
	var autre_valeur_select_lever_coucher = ""
	
	var nb_checked=0
	Divjour.find('.checkbox_lever_coucher').each(function() {if ($(this).prop("checked")){nb_checked+=1}})
	
	if(nb_checked>2){
		$(this).prop("checked",false)
		return 
	}
	modifyWithoutSave = true;
	Periode.prop("classList").forEach(function(classe){
		if (classe.includes("periode")){
			numero_cette_periode = classe.substr(7, classe.length-7)
		}
	})
	
	valeur_select_lever_coucher = Periode.find('.select_lever_coucher').value()
	
var time='00:00'
	if ( $(this).is (':checked')){
		if (nb_checked ==  2){
			Divjour.find('.checkbox_lever_coucher').each(function() {
				if ($(this).is (':checked')){
					var cette_periode=$(this).closest('.Periode_jour')
					cette_periode.prop("classList").forEach(function(classe){
						if (classe.includes("periode")){
							if (classe.substr(7, classe.length-7) != numero_cette_periode){
								autre_valeur_select_lever_coucher = cette_periode.find('.select_lever_coucher').value()
								numero_autre_periode = classe.substr(7, classe.length-7)
							}
						}
					})
				}
			})
			if ((numero_cette_periode>numero_autre_periode  &&  autre_valeur_select_lever_coucher=="coucher") ||(numero_cette_periode<numero_autre_periode  &&  autre_valeur_select_lever_coucher=="lever")){
				$(this).prop("checked",false)
				return 
			}
			if(autre_valeur_select_lever_coucher=="coucher"){
				Periode.find('.select_lever_coucher').prop("selectedIndex",0)
				if($(Divjour).hasClass("Lundi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Lundi')[0].innerText
				}else if($(Divjour).hasClass("Mardi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Mardi')[0].innerText
				}else if($(Divjour).hasClass("Mercredi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Mercredi')[0].innerText
				}else if($(Divjour).hasClass("Jeudi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Jeudi')[0].innerText
				}else if($(Divjour).hasClass("Vendredi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Vendredi')[0].innerText
				}else if($(Divjour).hasClass("Samedi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Samedi')[0].innerText
				}else if($(Divjour).hasClass("Dimanche")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Dimanche')[0].innerText
				}
			}else if(autre_valeur_select_lever_coucher=="lever"){
				Periode.find('.select_lever_coucher').prop("selectedIndex",1)
				if($(Divjour).hasClass("Lundi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Lundi')[0].innerText
				}else if($(Divjour).hasClass("Mardi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mardi')[0].innerText
				}else if($(Divjour).hasClass("Mercredi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mercredi')[0].innerText
				}else if($(Divjour).hasClass("Jeudi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Jeudi')[0].innerText
				}else if($(Divjour).hasClass("Vendredi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Vendredi')[0].innerText
				}else if($(Divjour).hasClass("Samedi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Samedi')[0].innerText
				}else if($(Divjour).hasClass("Dimanche")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Dimanche')[0].innerText
				}
			}
			Periode.find('.clock-timepicker').attr("oldvalue",Periode.find('.clock-timepicker').attr("value"))
			Periode.find('.clock-timepicker').attr("time_int",(parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
			Periode.find('.clock-timepicker').attr("value",time)
			Periode.find('.clock-timepicker').hide()
			Periode.find('.select_lever_coucher').show()
			
		}else{
			Periode.find('.clock-timepicker').hide()
			Periode.find('.select_lever_coucher').show()
			
			if(valeur_select_lever_coucher == "lever"){
				
				if($(Divjour).hasClass("Lundi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Lundi')[0].innerText
				}else if($(Divjour).hasClass("Mardi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Mardi')[0].innerText
				}else if($(Divjour).hasClass("Mercredi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Mercredi')[0].innerText
				}else if($(Divjour).hasClass("Jeudi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Jeudi')[0].innerText
				}else if($(Divjour).hasClass("Vendredi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Vendredi')[0].innerText
				}else if($(Divjour).hasClass("Samedi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Samedi')[0].innerText
				}else if($(Divjour).hasClass("Dimanche")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Dimanche')[0].innerText
				}
				
			}else{
				if($(Divjour).hasClass("Lundi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Lundi')[0].innerText
				}else if($(Divjour).hasClass("Mardi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mardi')[0].innerText
				}else if($(Divjour).hasClass("Mercredi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mercredi')[0].innerText
				}else if($(Divjour).hasClass("Jeudi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Jeudi')[0].innerText
				}else if($(Divjour).hasClass("Vendredi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Vendredi')[0].innerText
				}else if($(Divjour).hasClass("Samedi")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Samedi')[0].innerText
				}else if($(Divjour).hasClass("Dimanche")){
					time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Dimanche')[0].innerText
				}
			}
			Periode.find('.clock-timepicker').attr("oldvalue",Periode.find('.clock-timepicker').attr("value"))
			Periode.find('.clock-timepicker').attr("time_int",(parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
			Periode.find('.clock-timepicker').attr("value",time)
		}
	}else{
		time=Periode.find('.clock-timepicker').attr("oldvalue")
		
		if (typeof( time) !=  "undefined") {
			Periode.find('.clock-timepicker').attr("value",time)
			Periode.find('.clock-timepicker').attr("time_int",(parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
			Periode.find('.clock-timepicker').removeAttr('oldvalue')
		}
	
		Periode.find('.clock-timepicker').show()
		Periode.find('.select_lever_coucher').hide()
	}
	triage_jour(Divjour)
	MAJ_Graphique_jour(Divjour)
});
$('#tab_planifications').on('click','.clock-timepicker',  function () {
	Divjour = $(this).closest('.JourSemaine ')
	$(this).datetimepicker({
		step: 5,
		theme:'dark',
		datepicker:false,
		format: 'H:i',
		onClose:function(dp,$input){
			$('.clock-timepicker').datetimepicker('destroy')
		},
		onSelectTime:function(dp,$input){
			modifyWithoutSave = true;
			time=$input.val()
			$($input).attr("time_int",(parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
			$($input).attr("value",time)
			triage_jour($(Divjour))
			MAJ_Graphique_jour($(Divjour).closest('.JourSemaine'));
		}
	});
	$(this).datetimepicker('show');
	});
//gestion lever coucher de soleil
$('#tab_gestion').on("change",".selection_jour", function() { 
	$('#tab_gestion ').find('.Lundi').css("display","none")
	$('#tab_gestion ').find('.Mardi').css("display","none")
	$('#tab_gestion ').find('.Mercredi').css("display","none")
	$('#tab_gestion ').find('.Jeudi').css("display","none")
	$('#tab_gestion ').find('.Vendredi').css("display","none")
	$('#tab_gestion ').find('.Samedi').css("display","none")
	$('#tab_gestion ').find('.Dimanche').css("display","none")
	switch ($(this).val()){
		case 'Lundi':
			$('#tab_gestion ').find('.Lundi').css("display","block")
			break
		case 'Mardi':
			$('#tab_gestion ').find('.Mardi').css("display","block")
			break
		case 'Mercredi':
			$('#tab_gestion ').find('.Mercredi').css("display","block")
			break
		case 'Jeudi':
			$('#tab_gestion ').find('.Jeudi').css("display","block")
			break
		case 'Vendredi':
			$('#tab_gestion ').find('.Vendredi').css("display","block")
			break
		case 'Samedi':
			$('#tab_gestion ').find('.Samedi').css("display","block")
			break
		case 'Dimanche':
			$('#tab_gestion ').find('.Dimanche').css("display","block")
			break
	}


});
//fonctions
function closeAllSelect(elmnt) {
	var x, y, i, arrNo = [];
	x = document.getElementsByClassName("select-items");
	y = document.getElementsByClassName("select-selected");
	for (i = 0; i < y.length; i++) {
		if (elmnt == y[i]) {
			arrNo.push(i)
		} else {
			y[i].classList.remove("select-arrow-active");
		}
	}
	for (i = 0; i < x.length; i++) {
		if (arrNo.indexOf(i)) {
			x[i].classList.add("select-hide");
		}
	}
}
function recup_class_couleur(classes){
	var class_color="erreur"
	try{
		for (classe in classes){
			if(classes[classe].includes("couleur")){
				class_color=classes[classe]
				break
			}
		}
	}catch(err){
	}
	
	return class_color
}
function Ajoutplanification(_planification) {
	var JOURS = ['{{Lundi}}', '{{Mardi}}', '{{Mercredi}}', '{{Jeudi}}', '{{Vendredi}}', '{{Samedi}}', '{{Dimanche}}']
	modifyWithoutSave = true;
	if (init(_planification.nom) == '') return
	if (init(_planification.Id) == '') {_planification.Id=uniqId();}
	var random = Math.floor((Math.random() * 1000000) + 1)
    var div = '<div class="planification panel panel-default" Id='+  _planification.Id+'>'
			div += '<div class="panel-heading">'
				div += '<h3 class="panel-title" style="padding-bottom: 4px;">'
					div+='<div class="planification_collapsible cursor" style="height:32px;padding-top: 10px;width: calc(100% - 345px)">'
							div += '<span class="nom_planification">' + _planification.nom + '</span>'
								div += '<span class="input-group-btn pull-right" style="top: -5px!important;">'
									div += '<a class="btn btn-sm bt_renommer_planification btn-warning roundedLeft"><i class="fas fa-copy"></i> {{Renommer}}</a>'																										 
									div += '<a class="btn btn-sm bt_dupliquer_planification btn-primary roundedLeft"><i class="fas fa-copy"></i> {{Dupliquer}}</a>'
									div += '<a class="btn btn-sm bt_appliquer_planification btn-success" title="Appliquez la planification maintenant"><i class="fas fa-check-circle"></i> {{Appliquer}}</a>'
									div += '<a class="btn btn-sm bt_supprimer_planification btn-danger roundedRight"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>'
								div += '</span>'
					div += '</div>'		
				div += '</h3>'
			div += '</div>'
				div += '<div class="planification-body" style=" background-color: rgb(var(--defaultBkg-color))  !important;>'
					div += '<form class="form-horizontal" role="form">'
						div += '<div class="div_programDays" style="width:100%">'
							div += '<table style="width:100%">'
								div += '<tr>'
									JOURS.forEach(function(jour) {
										div += '<th style="width:14%;text-align: center;vertical-align: top;">'
											div+='<div class="collapsible no-arrow">'
												div +=  jour
											div+='</div>'
											div += '<div class="input-group" style="display:inline-flex">'
												div += '<span class="input-group-btn">'
													div += '<span><i class="fa fa-plus-circle cursor bt_ajout_periode" title="{{Ajouter une période}}"></i> </span>'
													div += '<span><i class="fas fa-sign-out-alt cursor bt_copier_jour" title="{{Copier le jour}}"></i> </span>'
													div += '<span><i class="fas fa-sign-in-alt cursor bt_coller_jour" title="{{Coller le jour}}"></i> </span>'
													div += '<span><i class="fa fa-minus-circle cursor bt_vider_jour" title="{{Vider le jour}}"></i> </span>'
												div += '</span>'
											div+='</div>'
											div += '<br></br>'
												
											div+='<div class="JourSemaine ' + jour + '" style="width:100%; float:left">'
												
											div+='</div>'
										div+='</td>'
									})										
								div += '</tr>'
							div += '</table>'
							
						div += '</div>'
							//graphiques:
						div += '<div class="graphJours" style="width:100%; clear:left">'
							div += '<br></br>'
							
							div += '<div style="width:80px; display:inline-block;"></div>'
								div += '<div style="width:calc(100% - 80px); display:inline-block;">'
									div += '<div style="width: 25%; height:18px; display:inline-block;">00:00</div>'
									div += '<div style="width: 25%; height:18px; display:inline-block; position:inherit;">06:00</div>'
									div += '<div style="width: 25%; height:18px; display:inline-block; position:inherit;">12:00</div>'
									div += '<div style="width: 25%; height:18px; display:inline-block;">18:00</div>'
								div += '</div>'
							JOURS.forEach(function(jour) {
								div += '<div class="nom_graphique" style="width:80px; display:inline-block;">'
									div += jour
								div += '</div>'
									div += '<div class="graphique_jour_'+jour+'" style="width:calc(100% - 80px); display:inline-block;">'
								div += '</div>'
							})
						div += '</div>'
						div += '</div>'
					div += '</form>'
				div += '</div>'
			// += '</div>'
		div += '</div>'

	$('#div_planifications').append(div)
}
function Ajout_Periode(PROGRAM_MODE_LIST, Div_jour, time=null, Mode_periode=null,Type_periode=false){
	modifyWithoutSave = true;
	Periode_jours = $(Div_jour).find('.Periode_jour')
	prochain_debut="00:00"
    if (Periode_jours.length > 0){
        periode_precedente = Periode_jours[Periode_jours.length-1]
		dernier_debut = $(periode_precedente).find('.clock-timepicker').val()	
		prochain_debut_int=parseInt(dernier_debut.split(':')[0])*60 + parseInt(dernier_debut.split(':')[1])+1
		heures=Math.trunc(prochain_debut_int/60)
		heures_str="0"+heures
		heures_str=heures_str.substr(heures_str.length -  2)
		minutes_str="0"+ (prochain_debut_int - (heures * 60))
		minutes_str=minutes_str.substr(minutes_str.length -  2)
		prochain_debut=heures_str +":"+minutes_str
		
        if (time == null ){
			time_int=parseInt(parseInt(dernier_debut.split(':')[0] * 60) + parseInt(dernier_debut.split(':')[1]))
			
			if(time_int==1439){
				time=""
			}else if(time_int>=1425){
				time = 23 + ':' + 59
			}else if(dernier_debut==""){
				return
			}else{
				/*time_int+=15
				heures=parseInt(time_int/60)
			
				minutes=time_int-(heures*60)
				//=parseInt(heure_debut.split(':')[0])*60 + parseInt(heure_debut.split(':')[1])+1
				heures=Math.trunc(time_int/60)
				heures_str="0"+heures
				heures_str=heures_str.substr(heures_str.length -  2)
				minutes_str="0"+ (time_int - (heures * 60))
				minutes_str=minutes_str.substr(minutes_str.length -  2)
				prochain_debut=heures_str +":"+minutes_str
				time = prochain_debut*/
				time=""
			}
			
        }else if (Mode_periode == null){
            last_timeStart = (parseInt(dernier_debut.split(':')[0]) * 60) + parseInt(dernier_debut.split(':')[1])
            heure_debut = (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1])
			if (heure_debut <= last_timeStart) {				
				time = prochain_debut			
			}
        }
	}
	
	if (time == "" && Type_periode == "lever"){
		if($(Div_jour).hasClass("Lundi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Lundi')[0].innerText
		}else if($(Div_jour).hasClass("Mardi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Mardi')[0].innerText
		}else if($(Div_jour).hasClass("Mercredi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Mercredi')[0].innerText
		}else if($(Div_jour).hasClass("Jeudi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Jeudi')[0].innerText
		}else if($(Div_jour).hasClass("Vendredi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Vendredi')[0].innerText
		}else if($(Div_jour).hasClass("Samedi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Samedi')[0].innerText
		}else if($(Div_jour).hasClass("Dimanche")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_lever_Dimanche')[0].innerText
		}
	}else if (time == "" && Type_periode == "coucher"){
		if($(Div_jour).hasClass("Lundi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Lundi')[0].innerText
		}else if($(Div_jour).hasClass("Mardi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mardi')[0].innerText
		}else if($(Div_jour).hasClass("Mercredi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mercredi')[0].innerText
		}else if($(Div_jour).hasClass("Jeudi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Jeudi')[0].innerText
		}else if($(Div_jour).hasClass("Vendredi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Vendredi')[0].innerText
		}else if($(Div_jour).hasClass("Samedi")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Samedi')[0].innerText
		}else if($(Div_jour).hasClass("Dimanche")){
			time=$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Dimanche')[0].innerText
		}
	}else if (time == null){
		time = '00:00'
	} 
	var time_int=(parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1])
	div = '<div class="Periode_jour periode'+ (Periode_jours.length+1) +' input-group" style="width:100% !important; line-height:1.4px !important;display: inline-grid">'
		div += '<div>'
			div += '<input style="width: 28px !important;font-size: 20px!important;vertical-align: middle;padding: 5px;" title="activer/désactiver heure lever/coucher de soleil" class="checkbox_lever_coucher checkbox form-control input-sm cursor" type="checkbox">'
				div += '<select class="select_lever_coucher select form-control input-sm" style="width: calc(100% - 52px)!important;;display: none;" title="Type planification">'
					div += '<option value="lever" selected>Lever de soleil</option>'
					div += '<option value="coucher">Coucher de soleil</option>'
				div += '</select>'
			
			div += '<input class="clock-timepicker form-control input-sm cursor" type="text" time_int="'+ time_int +'"  value="'+time+'" style="width:calc(100% - 56px);display:inline-block;position: relative" >'
			
			div += '<a class="btn btn-default bt_supprimer_perdiode btn-sm" style="position: absolute;right: 0px;display: inline-block" title="Supprimer cette période"><i class="fa fa-minus-circle"></i></a>'
		div += '</div>'
		div += '<div class="custom-select">'
			div += PROGRAM_MODE_LIST
		div += '</div>'
	div += '</div>'
	
    nouvelle_periode = $(div)
	if(Mode_periode!=null){
		
		for (var i=0; i<nouvelle_periode.find('.select-items').find("div").length; i++){
			if(nouvelle_periode.find('.select-items').find("div")[i].id == nouvelle_periode.find('.select-selected').prop("id")){
				nouvelle_periode.find('.select-items').find("div")[i].classList.add('same-as-selected')
			}
		}
		
	}else{
		nouvelle_periode.find('.select-items').find("div")[0].classList.add('same-as-selected')
	}
	if($('#tab_gestion ').find('.Heure_prochaine_action_lever_Lundi')[0].innerText==""){
		nouvelle_periode.find('.checkbox_lever_coucher').css("display",'none')
		nouvelle_periode.find('.clock-timepicker').css("width",'calc(100% - 28px)')
	}
	if(Type_periode=="lever"){
		nouvelle_periode.find('.checkbox_lever_coucher').prop('checked', true)
		nouvelle_periode.find('.clock-timepicker').hide()
		nouvelle_periode.find('.select_lever_coucher').prop("selectedIndex",0)
		nouvelle_periode.find('.select_lever_coucher').show()
	}else if(Type_periode=="coucher"){
		nouvelle_periode.find('.checkbox_lever_coucher').prop('checked', true)
		nouvelle_periode.find('.clock-timepicker').hide()
		nouvelle_periode.find('.select_lever_coucher').prop("selectedIndex",1)
		nouvelle_periode.find('.select_lever_coucher').show()
	}
	Div_jour.closest("th").find(".collapsible")[0].classList.remove("no-arrow")
	Div_jour.closest("th").find(".collapsible")[0].classList.add("cursor")
	Div_jour.append(nouvelle_periode)
}
function triage_jour(Div_jour){
	$(Div_jour).find(".clock-timepicker").map(function () {
		return {val: $(this).attr("time_int"), el: this.closest(".Periode_jour ")};
		}).sort(function (a, b) {
			return a.val - b.val;
		}).map(function () {
			return this.el;
		}).appendTo($(Div_jour));
}


function MAJ_Graphique_jour(Div_jour){
	graphDiv = $(Div_jour).closest('.planification-body').find(".graphJours").find('.graphique_jour_' + $(Div_jour).attr("class").split(' ')[1])
	graphDiv.empty()
	Periode_jour = $(Div_jour).find('.Periode_jour')
	for (var i=0; i<Periode_jour.length; i++){
        var isFirst = (i == 0) ? true : false
        var isLast = (i == Periode_jour.length-1) ? true : false
		var periode = Periode_jour[i]
        var debut_periode =$(periode).find('.clock-timepicker').attr("value")
		var heure_debut = (parseInt(debut_periode.split(':')[0]) * 60) + parseInt(debut_periode.split(':')[1])
		var delta, class_periode, mode,nouveau_graph,heure_fin,width,fin_periode,fin_periode
		if(isFirst && heure_debut != 0){
			heure_fin = heure_debut
			delta = heure_fin
			width = (delta*100) / 1440
			class_periode = ""
			mode = "Aucun"
			nouveau_graph = '<div class="graph '+class_periode+'" style="width:'+width+'%; height:20px; display:inline-block;">'
			nouveau_graph +='<span class="tooltiptext  '+class_periode+'">'+debut_periode +" - 23:59<br>" +mode+'</span>'
			nouveau_graph +='</div>'
			graphDiv.append(nouveau_graph)
		}
		if (isLast){
			heure_fin = 1439
			fin_periode="23:59"
        }else{
			//fin_periode = $(Periode_jour[i+1]).find('.clock-timepicker').val()
			fin_periode =$(Periode_jour[i+1]).find('.clock-timepicker').attr("value")
			heure_fin = (parseInt(fin_periode.split(':')[0]) * 60) + parseInt(fin_periode.split(':')[1])
        }
        delta = heure_fin - heure_debut
        width = (delta*100) / 1440
		class_periode=recup_class_couleur($(periode).find('.select-selected').attr('class').split(' ')) 
		mode = $(periode).find('.select-selected').text()
       	nouveau_graph = '<div class="graph '+class_periode+'" style="width:'+width+'%; height:20px; display:inline-block;">'
		nouveau_graph +='<span class="tooltiptext  '+class_periode+'">'+debut_periode +" - " +fin_periode+  "<br>" +mode+'</span>'
		nouveau_graph +='</div>'
		graphDiv.append(nouveau_graph)
    }
}
function Recup_select($type) {
	var SELECT=""
	$.ajax({
		type: "POST",
		url: "plugins/planification/core/ajax/planification.ajax.php",
		data: {
			action : "Recup_select",
			eqLogic_id : $('.eqLogicAttr[data-l1key=id]').value(),
			type : $type
		},
		global: true,
		async: false,
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data, level: 'danger'});
				SELECT="";
			}
			SELECT= data.result;
		}
	});	
	return SELECT;

}
function Recup_liste_commandes_planification(){
	var COMMANDE_LIST=[]
	$.ajax({
		type: "POST",
		url: "plugins/planification/core/ajax/planification.ajax.php",
		data: {
			action: "Recup_liste_commandes_planification",
			eqLogic_id: $('.eqLogicAttr[data-l1key=id]').value(),
		},
		global: true,
		async: false,
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data, level: 'danger'});
				COMMANDE_LIST="";
			}
			COMMANDE_LIST= data.result;
		}
	});	
	return COMMANDE_LIST;

}
function printEqLogic(_eqLogic) {
	$('#div_planifications').empty()
	$('#table_cmd_planification tbody').empty()
	if(_eqLogic.configuration.type == 'Poele') {
		$('#tab_eqlogic .poele .eqLogicAttr[data-l2key=temperature_id]').val(_eqLogic.configuration.temperature_id )
		$('#tab_eqlogic .poele .eqLogicAttr[data-l2key=etat_allume_id]').val(_eqLogic.configuration.etat_allume_id)
		$('#tab_eqlogic .poele .eqLogicAttr[data-l2key=etat_boost_id]').val(_eqLogic.configuration.etat_boost_id)
		$('#tab_eqlogic .poele .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').val(_eqLogic.configuration.Duree_mode_manuel_par_defaut)
	}
	if(_eqLogic.configuration.type == 'PAC') {
		$('#tab_eqlogic .PAC .eqLogicAttr[data-l2key=temperature_id]').val(_eqLogic.configuration.temperature_id )
		$('#tab_eqlogic .PAC .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').val(_eqLogic.configuration.Duree_mode_manuel_par_defaut)
	}
	$.ajax({
		type: "POST",
		url: "plugins/planification/core/ajax/planification.ajax.php",
		data: {
			action: "Recup_infos_lever_coucher_soleil",
			id: _eqLogic["id"]
		},
		dataType: 'json',
		global: false,
		async:false,
		error: function (request, status, error) {handleAjaxError(request, status, error)},
		success: function (data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({
					message: data.result,
					level: 'danger'
				})
				return
			}
			
			if (data.result==false){
				$('#div_alert').showAlert({
					message: "Pour utiliser la fonction lever/coucher de soleil, veuillez enregistrer les coordonnées GPS (latitude et longitude) dans la configuration de jeedom.",
					level: 'warning'
				})
				return					
			}
			$('#tab_gestion ').find('.HeureLever_Lundi')[0].innerText=data.result["Lever_soleil"]
			$('#tab_gestion ').find('.HeureLever_Mardi')[0].innerText=data.result["Lever_soleil"]
			$('#tab_gestion ').find('.HeureLever_Mercredi')[0].innerText=data.result["Lever_soleil"]
			$('#tab_gestion ').find('.HeureLever_Jeudi')[0].innerText=data.result["Lever_soleil"]
			$('#tab_gestion ').find('.HeureLever_Vendredi')[0].innerText=data.result["Lever_soleil"]
			$('#tab_gestion ').find('.HeureLever_Samedi')[0].innerText=data.result["Lever_soleil"]
			$('#tab_gestion ').find('.HeureLever_Dimanche')[0].innerText=data.result["Lever_soleil"]
			$('#tab_gestion ').find('.HeureCoucher_Lundi')[0].innerText=data.result["Coucher_soleil"]
			$('#tab_gestion ').find('.HeureCoucher_Mardi')[0].innerText=data.result["Coucher_soleil"]
			$('#tab_gestion ').find('.HeureCoucher_Mercredi')[0].innerText=data.result["Coucher_soleil"]
			$('#tab_gestion ').find('.HeureCoucher_Jeudi')[0].innerText=data.result["Coucher_soleil"]
			$('#tab_gestion ').find('.HeureCoucher_Vendredi')[0].innerText=data.result["Coucher_soleil"]
			$('#tab_gestion ').find('.HeureCoucher_Samedi')[0].innerText=data.result["Coucher_soleil"]
			$('#tab_gestion ').find('.HeureCoucher_Dimanche')[0].innerText=data.result["Coucher_soleil"]
			$('#tab_gestion ').find('.Heure_prochaine_action_lever_Lundi')[0].innerText=data.result["Heure_prochaine_action_lever_lundi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_lever_Mardi')[0].innerText=data.result["Heure_prochaine_action_lever_mardi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_lever_Mercredi')[0].innerText=data.result["Heure_prochaine_action_lever_mercredi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_lever_Jeudi')[0].innerText=data.result["Heure_prochaine_action_lever_jeudi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_lever_Vendredi')[0].innerText=data.result["Heure_prochaine_action_lever_vendredi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_lever_Samedi')[0].innerText=data.result["Heure_prochaine_action_lever_samedi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_lever_Dimanche')[0].innerText=data.result["Heure_prochaine_action_lever_dimanche"]
			$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Lundi')[0].innerText=data.result["Heure_prochaine_action_coucher_lundi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mardi')[0].innerText=data.result["Heure_prochaine_action_coucher_mardi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mercredi')[0].innerText=data.result["Heure_prochaine_action_coucher_mercredi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Jeudi')[0].innerText=data.result["Heure_prochaine_action_coucher_jeudi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Vendredi')[0].innerText=data.result["Heure_prochaine_action_coucher_vendredi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Samedi')[0].innerText=data.result["Heure_prochaine_action_coucher_samedi"]
			$('#tab_gestion ').find('.Heure_prochaine_action_coucher_Dimanche')[0].innerText=data.result["Heure_prochaine_action_coucher_dimanche"]
		}

	})	
	nom_planification_erreur=[]	
	
	
	
	var SELECT_LIST= Recup_select("planifications")
	var CMD_LIST=Recup_liste_commandes_planification()
		$.ajax({
			type: "POST",
			url: "plugins/planification/core/ajax/planification.ajax.php",
			data: {
				action: "Recup_planification",
				id: _eqLogic["id"]
			},
			//dataType: 'json',
			global: false,
			async:false,
			error: function (request, status, error) {handleAjaxError(request, status, error)},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_alert').showAlert({
						message: data.result,
						level: 'danger'
					})
					return
				}
				if (data.result==false){
					return
				}
				var array = JSON.parse("[" + data.result + "]");
              	if(array[0].length ==0){return;}
             	array[0].forEach(function(planification) {
					Ajoutplanification({nom: planification.nom_planification,Id:planification.Id, nouveau: false})
					$('#div_planifications .planification:last .JourSemaine').each(function () {
						jour_en_cours = $(this)[0].classList[1]
						planification.semaine.forEach(function(jour){
							if (jour.jour == jour_en_cours){
								if (isset(jour.periodes)){
									jour.periodes.forEach(function(periode){
										Couleur="erreur"
										Nom=""
										Id=""
										CMD_LIST.forEach(function(cmd){
											
											if(periode.Id == cmd.Id ){
												Couleur="couleur-" + cmd.couleur
												Nom=cmd.Id
												Id=cmd.Id
											}
										});
										var element = SELECT_LIST.replace("#COULEUR#",Couleur);
										element=element.replace("#VALUE#",Nom)
										element=element.replace("#ID#",Id)
										Ajout_Periode(element, $('.planification:last .JourSemaine.' + jour_en_cours), periode.Debut_periode, periode.Id,periode.Type_periode)
									})
								}
							}
						})
						triage_jour($('#div_planifications .planification:last .JourSemaine.' + jour_en_cours))
						
						MAJ_Graphique_jour($('#div_planifications .planification:last .JourSemaine.' + jour_en_cours))
					})
					
				})
			}
		})
}
function saveEqLogic(_eqLogic) {
    if (!isset(_eqLogic.configuration)) {
        _eqLogic.configuration = {}
    }
	var planifications = [];
	var erreur=false
	
	$('#div_planifications .planification').each(function () {
		var _Cette_planification = {}
		_Cette_planification.nom_planification = $(this).find('.nom_planification').html()
		_Cette_planification.Id=$(this).attr("Id")
		var semaine = []
		$(this).find('th').find(".JourSemaine").each(function () {
			var jour = {}
			jour.jour = $(this).attr("class").split(' ')[1]
			var periodes = []
			$(this).find('.Periode_jour').each(function () {
				var type_periode = ""
				type_periode = "heure_fixe"
				if ($(this).find('.checkbox_lever_coucher').prop("checked")){
					type_periode=$(this).find('.select_lever_coucher').value()
					debut_periode =""
				}else{
					debut_periode = $(this).find('.clock-timepicker').val()
				}
				
				Id = $(this).find('.select-selected')[0].getAttribute('id')
				if(typeof(Id) != 'string'){
					erreur=true
					$(this).find('.select-selected')[0].classList.add("erreur")
				}
				
				if(type_periode == "heure_fixe" && debut_periode ==""){
					erreur=true
					$(this).find('.select-selected')[0].classList.add("erreur")
				}
				periodes.push({'Type_periode':type_periode,'Debut_periode':debut_periode, 'Id':Id})
			})
			jour.periodes = periodes
			semaine.push(jour)
			
		})
		_Cette_planification.semaine = semaine
		planifications.push(_Cette_planification)
	})
	if (erreur){
		alert("Impossible d'enregistrer la planification. Celle-ci comporte des erreurs.")
		return false ;
	}
	$.ajax({
		type: "POST",
		url: "plugins/planification/core/ajax/planification.ajax.php",
		data: {
			action: "Enregistrer_planifications",
			id: _eqLogic["id"],
			planifications: planifications
		},
		global: false,
		error: function (request, status, error) {handleAjaxError(request, status, error)},
		success: function (data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({
					message: data.result,
					level: 'danger'
				})
				return
			}
		}
	})	



	if(_eqLogic.configuration.type == 'Poele') {
		_eqLogic.configuration.temperature_id = $('#tab_eqlogic .poele .eqLogicAttr[data-l2key=temperature_id]').val();
		_eqLogic.configuration.etat_allume_id = $('#tab_eqlogic .poele .eqLogicAttr[data-l2key=etat_allume_id]').val();
		_eqLogic.configuration.etat_boost_id = $('#tab_eqlogic .poele .eqLogicAttr[data-l2key=etat_boost_id]').val();
		_eqLogic.configuration.temperature_consigne_par_defaut = $('#tab_eqlogic .poele .eqLogicAttr[data-l2key=temperature_consigne_par_defaut]').val();
		_eqLogic.configuration.Duree_mode_manuel_par_defaut = $('#tab_eqlogic .poele .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').val();
	}								   	
	if(_eqLogic.configuration.type == 'PAC') {
		_eqLogic.configuration.temperature_id = $('#tab_eqlogic .PAC .eqLogicAttr[data-l2key=temperature_id]').val();
		_eqLogic.configuration.Duree_mode_manuel_par_defaut = $('#tab_eqlogic .PAC .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').val();
	}	

	

	_eqLogic.cmd.forEach(function(_cmd){
		
		if(_cmd.type == "action" && _cmd.name != "Auto" && _cmd.name != "Absent"){
			$('#table_actions tbody tr').each(function(){
				if (!isset(_cmd.configuration)) {
					_cmd.configuration = {}
				}
				if(_eqLogic.configuration.type != 'Poele') {
					if( _cmd.name != "Absent" && _cmd.name != "Arrêt"){_cmd.configuration.Type="Planification"}
					if(typeof($(this).getValues('.expressionAttr')[0].options) != "undefined"){
						_cmd.configuration.options=($(this).getValues('.expressionAttr')[0].options)
					}else{
						_cmd.configuration.options=''
					}
				}else{
					if (_cmd.name == $(this).getValues('.cmdAttr[data-l1key=name]')[0].name){

						if( _cmd.name != "Absent"){_cmd.configuration.Type="Planification"}
						if(typeof($(this).getValues('.expressionAttr')[0].options) != "undefined"){
							_cmd.configuration.options=($(this).getValues('.expressionAttr')[0].options)
						}else{
							_cmd.configuration.options=''
						}
					}
				}
				
			})
		}
	})
	return _eqLogic
}
function addCmdToTable(_cmd) {
	if(_cmd.logicalId == "set_heure_fin" || _cmd.logicalId == "set_consigne_temperature" || _cmd.logicalId == "set_action_en_cours" || _cmd.logicalId == "manuel" || _cmd.logicalId == "refresh" || _cmd.logicalId == "boost_on" || _cmd.logicalId == "boost_off"){
		return
	}
	if (_cmd.logicalId == 'set_planification'){
		set_planification_Id = _cmd.id
		return
	} 
    if (!isset(_cmd)) var _cmd = {configuration:{}}
    if (!isset(_cmd.configuration)) _cmd.configuration = {}
	
	
		

		if (_cmd.type == 'info'){
			var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
			tr += '<td>'
			tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none" >'
			tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none">'
			tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none">'
			tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" disabled placeholder="{{Nom}} "</td>'
			tr += '</td>'
			tr += '<td>'
				
				
			tr += '</td>'
			tr += '<td>'
			
			tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized"/>{{Historiser}}</label></span> '
		
			tr += '</td>'
			
			tr += '<td>'
			if (is_numeric(_cmd.id)){
				tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> '
				tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>'
			}
			tr += '</td>'
		tr += '</tr>'
			$('#table_infos tbody').append(tr)
			$('#table_infos tbody tr:last').setValues(_cmd, '.cmdAttr')
			if (isset(_cmd.type)) $('#table_infos tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type))
			jeedom.cmd.changeType($('#table_infos tbody tr:last'), init(_cmd.subType))
		}else if (_cmd.type == 'action'){
			
			var SELECT_LIST=Recup_select("commandes");
			var tr = ''
			tr += '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
				tr += '<td>'
				tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none" >'
				tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none">'
				tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none">'
				tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" disabled  placeholder="{{Nom}} "</td>'
				tr += '</td>'
				
				tr += '<td>'
				if (_cmd.logicalId != 'auto' && _cmd.logicalId != "absent" && _cmd.logicalId != "force"){
						tr += '<div class="input-group" style=" width:100%;">';
						tr += '<input class="cmdAttr form-control input-sm cmdAction" data-l1key="configuration" data-l2key="commande"/>';
						tr += '<span class="input-group-btn">';
							tr += '<a class="btn btn-success btn-sm listAction"><i class="fa fa-list-alt"></i></a>';
							tr += '<a class="btn btn-success btn-sm listCmdAction"><i class="fa fa-tasks"></i></a>';
						tr += '</span>';
						
					tr += '</div>';
					tr += '<div class="actionOptions">';
					tr += '</div>';			
				}
				tr += '</td>'
				tr += '<td>'
				if (_cmd.logicalId != 'auto' && _cmd.logicalId != "absent" && _cmd.logicalId != "force"){
					tr += '<div class="custom-select">'
						tr += SELECT_LIST
						
					tr += '</div>'
				}				
				tr += '</td>'
				
				tr += '<td>'
					if (is_numeric(_cmd.id)){
						tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> '
						tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>'
					
					}
				tr += '</td>'
			tr += '</tr>'



			$('#table_actions tbody').append(tr)
			$('#table_actions tbody tr:last').setValues(_cmd, '.cmdAttr')
			if (isset(_cmd.type)) $('#table_actions tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type))
			jeedom.cmd.changeType($('#table_actions tbody tr:last'), init(_cmd.subType))
			$('#table_actions tbody tr:last').find(".actionOptions").append(jeedom.cmd.displayActionOption(_cmd.configuration.commande, init(_cmd.configuration.options)))
				
			if (_cmd.logicalId != 'auto' && _cmd.logicalId != "absent" && _cmd.logicalId != "force"){
				if (isset(_cmd.configuration.Couleur)){
					couleur=_cmd.configuration.Couleur
					if(_cmd.configuration.Couleur == "<span>#VALUE#<\/span>"){
						couleur="orange"
					}
				}else{
					couleur="orange"
				}
				$('#table_actions tbody tr:last').find(".select-selected")[0].classList.replace("#COULEUR#","couleur-"+couleur)
				$('#table_actions tbody tr:last .select-items ').find("."+"couleur-" + couleur)[0].classList.add("same-as-selected")
				$('#table_actions tbody tr:last').find(".select-selected")[0].innerHTML=couleur
				
			}
			
		}	
}
