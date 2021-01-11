JSONCLIPBOARD = null
JOURREF = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche']
JOURS = ['{{Lundi}}', '{{Mardi}}', '{{Mercredi}}', '{{Jeudi}}', '{{Vendredi}}', '{{Samedi}}', '{{Dimanche}}']
document.addEventListener("click", closeAllSelect);
function recup_class_couleur(classes){
	class_color="erreur"
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
$('.ajouter_eqlogic').on('click', function () {
	var dialog_title = '{{Choisissez le type d\'équipement que souhaitez ajouter}}';
	var dialog_message = '<form class="form-horizontal onsubmit="return false;"> ';
	dialog_message += 
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
	'<input type="radio" name="type" id="Autre" value="Autre" placeholder="Nom de l\'équipement"> {{Autre}}</label> ' +
	'</div> <br>' +
	'<div class="input">' +
	'<input class="col-sm-8" type="text" placeholder="Nom de l\'équipement" name="nom" id="nom" >  ' +
	
	'</div> <br>' +
	'</div>';
	
	dialog_message += '</form>';
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
$('#div_planifications').off('click','.select-selected').on('click','.select-selected',  function (e) {
	/* When the select box is clicked, close any other select boxes,
	and open/close the current select box: */
	e.stopPropagation();
	closeAllSelect(this);
	this.nextSibling.classList.toggle("select-hide");
	this.classList.toggle("select-arrow-active");
});
$('#div_planifications').off('click','.select-items div').on('click','.select-items div',  function () {
	
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
$('#table_cmd_planification').off('click','.select-selected').on('click','.select-selected',  function (e) {
	/* When the select box is clicked, close any other select boxes,
	and open/close the current select box: */
	e.stopPropagation();
	closeAllSelect(this);
	this.nextSibling.classList.toggle("select-hide");
	this.classList.toggle("select-arrow-active");
});
$('#table_cmd_planification').off('click','.select-items div').on('click','.select-items div',  function () {
	
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
$("#div_planifications").sortable({
	axis: "y", cursor: "move", items: ".planification", handle: ".panel-heading", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true
})
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

$("input[data-l1key='functionality::cron15::enable']").on('change',function(){
  if ($(this).is(':checked')) {
    $("input[data-l1key='functionality::cron5::enable']").prop("checked", false);
    $("input[data-l1key='functionality::cron::enable']").prop("checked", false);
    }
});
//selection du type d'équipement et maj de son image:

$('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').on('change',function(){
   
   if ($(this).value() == "PAC"){
		$(".poele").hide()
		$(".PAC").show()
	  	$('#img_planificationModel').attr('src','plugins/planification/core/img/pac.png')
   }else if ($(this).value() == "Volet"){
		$('#img_planificationModel').attr('src','plugins/planification/core/img/volet.png')
		$(".poele").hide()
		$(".PAC").hide()
   }else if ($(this).value() == "Chauffage"){
		$('#img_planificationModel').attr('src','plugins/planification/core/img/chauffage.png')
		$(".poele").hide()
		$(".PAC").hide()
   }else if ($(this).value() == "Poele"){
		$(".poele").show()
		$(".PAC").hide()
		$('#img_planificationModel').attr('src','plugins/planification/core/img/poele.png')	
   }else{
	   	$('#img_planificationModel').attr('src','plugins/planification/core/img/autre.png')
	   	$(".poele").hide()
		$(".PAC").hide()
   }
})
  
//planifications:
$('#bt_ajouter_planification').off('click').on('click', function () {
	bootbox.prompt({
        title: "Veuillez inserer le nouveau nom de la planification à ajouter.",
        buttons: {
            confirm: {label: 'Ajouter', className: 'btn-success'},
            cancel: {label: 'Annuler', className: 'btn-danger'}
        },
        callback: function (resultat) {
            if (resultat!== null && resultat != '') {
               Ajoutplanification({nom: resultat})
            }
        }
	})
})

$("#div_planifications").off('click','.bt_supprimer_planification').on('click', '.bt_supprimer_planification',function () {
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
                Ce_progamme.remove()
            }
        }
    })
})

$('#div_planifications').off('click','.bt_dupliquer_planification').on('click','.bt_dupliquer_planification',  function () {
    var planification = $(this).closest('.planification').clone()
     bootbox.prompt({
        title: "Veuillez inserer le nom pour la planification dupliquée.",
        buttons: {
            confirm: {label: 'Dupliquer', className: 'btn-success'},
            cancel: {label: 'Annuler', className: 'btn-danger'}
        },
        callback: function (resultat) {
            if (resultat!== null && resultat != '') {
				var random = Math.floor((Math.random() * 1000000) + 1)
				planification.find('a[data-toggle=collapse]').attr('href', '#collapse' + random)
				planification.find('.panel-collapse.collapse').attr('id', 'collapse' + random)
				planification.find('.nom_planification').html(resultat)
				$('#div_planifications').append(planification)
				$('.collapse').collapse()
            }
        }
    })
})

$('#div_planifications').off('click','.bt_appliquer_planification').on('click','.bt_appliquer_planification',  function () {
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

$('#div_planifications').off('click','.bt_renommer_planification').on('click','.bt_renommer_planification',  function () {
		var el = $(this)
	bootbox.prompt({
        title: "Veuillez inserer le nouveau nom pour la planification:" + $(this).closest('.planification').find('.nom_planification').html() +".",
        buttons: {
            confirm: {label: 'Modifier', className: 'btn-success'},
            cancel: {label: 'Annuler', className: 'btn-danger'}
        },
        callback: function (resultat) {
            if (resultat !== null && resultat != '') {
               el.closest('.panel.panel-default').find('span.nom_planification').text(resultat)
            }
        }
    })
})

								   
$("body").off('click', '.bt_supprimer_perdiode').on( 'click', '.bt_supprimer_perdiode',function () {
    Divjour = $(this).closest('.JourSemaine')
    $(this).closest('.Periode_jour').remove()
    MAJ_Graphique_jour(Divjour)
});

$('body').off('click','.bt_ajout_periode').on('click','.bt_ajout_periode',  function () {
	$(this).closest("th").find(".collapsible")[0].classList.add("active")
	$(this).closest("th").find(".collapsible")[0].classList.add("cursor")
	$(this).closest("th").find(".collapsible")[0].classList.remove("no-arrow")
	Divjour=$(this).closest('th').find('.JourSemaine')

	
	var SELECT_LIST= Recup_select("planification")
	var CMD_LIST=Recup_liste_commandes_planification()
	Couleur="erreur"
	Nom=""
	Couleur="couleur-" + CMD_LIST[0]["couleur"]
	Nom=CMD_LIST[0]["nom"]
	Id=CMD_LIST[0]["Id"]
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

$('body').off('click','.bt_copier_jour').on('click','.bt_copier_jour',  function () {
    var jour = $(this).closest('th').find('.JourSemaine')
	JSONCLIPBOARD = { data : []}
    jour.find('.Periode_jour').each(function  () {
        debut_periode = $(this).find('.clock-timepicker').val()
		Id = $(this).find('.select-selected').attr("id")
		Nom=$(this).find('.select-selected span')[0].innerHTML
		Couleur=recup_class_couleur($(this).find('.select-selected')[0].classList)
		JSONCLIPBOARD.data.push({debut_periode, Id,Nom,Couleur})
    })
})

$('body').off('click','.bt_coller_jour').on('click','.bt_coller_jour',  function () {
	if (JSONCLIPBOARD == null) return
	
	Divjour = $(this).closest('th').find('.JourSemaine')
	Divjour.find('.Periode_jour').each(function  () {
        $(this).remove()
	})
	var SELECT_LIST= Recup_select("planification")
    JSONCLIPBOARD.data.forEach(function(periode) {
		
		Couleur=periode["Couleur"]
		Nom=periode["Nom"]
		Id=periode["Id"]
		var element = SELECT_LIST.replace("#COULEUR#",Couleur);
		element=element.replace("#VALUE#",Nom)
		element=element.replace("#ID#",Id)
		Ajout_Periode(element,Divjour, periode.debut_periode)
		
	})
	Divjour.css("overflow","visible")
	Divjour.css("max-height","fit-content")
	$(this).closest("th").find(".collapsible")[0].classList.add("active")
		$(this).closest("th").find(".collapsible")[0].classList.add("cursor")
		$(this).closest("th").find(".collapsible")[0].classList.remove("no-arrow")
	MAJ_Graphique_jour(Divjour)
})
$('body').off('click','.bt_vider_jour').on('click','.bt_vider_jour',  function () {
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

$('body').off('click','.collapsible').on('click','.collapsible',  function () {
	this.classList.toggle("active");
	var Divjour=$(this).closest("th").find(".JourSemaine")
	//var Divjour = this.nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling;
   /* if (Divjour.style.maxHeight){
		Divjour.style.maxHeight = null;
		Divjour.style.overflow ="hidden"
    } else {
		Divjour.style.maxHeight = Divjour.scrollHeight + "px";
		Divjour.style.overflow ="visible"
	} */
	if(Divjour.css("overflow")=="visible"){
		Divjour.css("max-height","0px"	)
		Divjour.css("overflow","hidden")
	}else{
		Divjour.css("overflow","visible")
		Divjour.css("max-height","fit-content")
	}
//$(this).closest('form').find('.JourSemaine.' + $(this)[0].innerText).toggle();
})
$('body').off('click','.planification_collapsible').on('click','.planification_collapsible',  function () {
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

function Ajoutplanification(_planification) {

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

function Ajout_Periode(PROGRAM_MODE_LIST, Div_jour, time=null, Mode_periode=null){
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
        if (time == null){
			time_int=parseInt(parseInt(dernier_debut.split(':')[0] * 60) + parseInt(dernier_debut.split(':')[1]))
			
			if(time_int==1439){
				return;
			}else if(time_int>=1425){
				time = 23 + ':' + 59
			}else{
				time_int+=15
				heure=parseInt(time_int/60)
			
				minute=time_int-(heure*60)
			
            	time = heure + ':' + minute
			}
			
        }else if (Mode_periode == null){
            last_timeStart = (parseInt(dernier_debut.split(':')[0]) * 60) + parseInt(dernier_debut.split(':')[1])
            heure_debut = (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1])
			if (heure_debut <= last_timeStart) {
				time = dernier_debut.split(':')[0] + ':' + (parseInt(dernier_debut.split(':')[1]) + 1)
			}
        }
    }
    if (time == null) time = '00:00'
	div = '<div class="Periode_jour input-group" style="width:100% !important; line-height:1.4px !important;display: inline-grid">'
		div += '<div>'
			div += '<input class="checkbox form-control input-sm cursor" type="checkbox" onchange="Maj_checkbox(this)">'
			div += '<input class="clock-timepicker form-control input-sm cursor" type="text"  value="'+time+'" style="width:60px;display:inline-block;position: relative" >'
			
			div += '<a class="btn btn-default bt_supprimer_perdiode btn-sm" style="vertical-align: bottom;right: 0px;position: absolute;" title="Supprimer cette période"><i class="fa fa-minus-circle"></i></a>'
		div += '</div>'
		div += '<div class="custom-select">'
			div += PROGRAM_MODE_LIST
		div += '</div>'
		
	div += '</div>'
	

    nouvelle_periode = $(div)
    if (time != '00:00'){
		nouvelle_periode.find('.clock-timepicker').TimePicker({
			
			date: false,
			shortTime: false,
			format: 'HH:mm',
			switchOnClick : true,
			
		})
			.on('open', function(e, date){
				debut_periode_precedente=""
				debut_periode_suivante=""
				debut_periode_precedente=$(this).closest(".Periode_jour").prev().find(".clock-timepicker").val()
				if (debut_periode_precedente != ""){
					debut_periode_precedente_int = (parseInt(debut_periode_precedente.split(':')[0]) * 60) + parseInt(debut_periode_precedente.split(':')[1])+1
					heures_debut_str="0"+Math.trunc(debut_periode_precedente_int/60)
					heures_debut_str=heures_debut_str.substr(heures_debut_str.length -  2)
					minutes_debut_str="0"+ (debut_periode_precedente_int - (Math.trunc(debut_periode_precedente_int/60)* 60))
					minutes_debut_str=minutes_debut_str.substr(minutes_debut_str.length -  2)
				}
				if (debut_periode_suivante!=""){
					debut_periode_suivante=$(this).closest(".Periode_jour").next().find(".clock-timepicker").val()
					debut_periode_suivante_int = (parseInt(debut_periode_suivante.split(':')[0]) * 60) + parseInt(debut_periode_suivante.split(':')[1])-1
					heures_suivante_str="0"+Math.trunc(debut_periode_suivante_int/60)
					heures_suivante_str=heures_suivante_str.substr(heures_suivante_str.length -  2)
					minutes_suivante_str="0"+ (debut_periode_suivante - (Math.trunc(debut_periode_suivante/60)* 60))
					minutes_suivante_str=minutes_suivante_str.substr(minutes_suivante_str.length -  2)
				}else{
					heures_suivante_str="23"
					minutes_suivante_str="59"
				}
				
				$(this).TimePicker('setMinDate', heures_debut_str + ":"  + minutes_debut_str );
				$(this).TimePicker('setMaxDate', heures_suivante_str + ":"  + minutes_suivante_str);
				
			})
			.on("change",function(e){
				MAJ_Graphique_jour($(this).closest('.JourSemaine'));
			})
		
    }else{
		nouvelle_periode.find('.clock-timepicker').prop('readonly', true)
	}
	if(Mode_periode!=null){
		
		for (i=0; i<nouvelle_periode.find('.select-items').find("div").length; i++){
			if(nouvelle_periode.find('.select-items').find("div")[i].id == nouvelle_periode.find('.select-selected').prop("id")){
				nouvelle_periode.find('.select-items').find("div")[i].classList.add('same-as-selected')
			}
		}
		
	}else{
		nouvelle_periode.find('.select-items').find("div")[0].classList.add('same-as-selected')
	}
	Div_jour.closest("th").find(".collapsible")[0].classList.remove("no-arrow")
	Div_jour.closest("th").find(".collapsible")[0].classList.add("cursor")
	Div_jour.append(nouvelle_periode)
}

function MAJ_Graphique_jour(Div_jour){
	graphDiv = $(Div_jour).closest('.planification-body').find(".graphJours").find('.graphique_jour_' + $(Div_jour).attr("class").split(' ')[1])
	graphDiv.empty()
	Periode_jour = $(Div_jour).find('.Periode_jour')
	for (i=0; i<Periode_jour.length; i++){
        isFirst = (i == 0) ? true : false
        isLast = (i == Periode_jour.length-1) ? true : false
		periode = Periode_jour[i]
        debut_periode = $(periode).find('.clock-timepicker').val()
        heure_debut = (parseInt(debut_periode.split(':')[0]) * 60) + parseInt(debut_periode.split(':')[1])
        if(isFirst && heure_debut != 0){
			heure_fin = heure_debut
			delta = heure_fin
			width = (delta*100) / 1440
			class_periode = ""
			mode = "Aucun"
			nouveau_graph = '<div style="width:'+width+'%; height:20px; display:inline-block;" title="'+ debut_periode +" - 23:59<br>" +mode+'"></div>'
			graphDiv.append(nouveau_graph)
		}
		if (isLast){
			heure_fin = 1439
			fin_periode="23:59"
        }else{
            fin_periode = $(Periode_jour[i+1]).find('.clock-timepicker').val()
			heure_fin = (parseInt(fin_periode.split(':')[0]) * 60) + parseInt(fin_periode.split(':')[1])
        }
        delta = heure_fin - heure_debut
        width = (delta*100) / 1440
		class_periode=recup_class_couleur($(periode).find('.select-selected').attr('class').split(' ')) 
		mode = $(periode).find('.select-selected').text()
        //nouveau_graph = '<div class="'+class_periode+'" style="width:'+width+'%; height:20px; display:inline-block;" title="'+debut_periode +" - " +fin_periode+  "<br>" +mode+'"></div>'
		nouveau_graph = '<div class="graph '+class_periode+'" style="width:'+width+'%; height:20px; display:inline-block;">'
		 //title="'+debut_periode +" - " +fin_periode+  "<br>" +mode+'">
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
  
$('.bt_showExpressionTest').off('click').on('click', function () {
  $('#md_modal').dialog({title: "{{Testeur d'expression}}"});
  $("#md_modal").load('index.php?v=d&modal=expression.test').dialog('open');
});

$('.bt_Importer_Commandes_EqLogic').off('click').on('click', function () {
  
  jeedom.eqLogic.getSelectModal({}, function (result) {
    $.ajax({
      type: "POST",
      url: "plugins/planification/core/ajax/planification.ajax.php",
      data: {
        action: "Importer_commandes_eqlogic",
        eqLogic_id: result.id,
        id: $('.eqLogicAttr[data-l1key=id]').value()
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
		window.location.reload()
		
        //$('.eqLogicDisplayCard[data-eqLogic_id='+$('.eqLogicAttr[data-l1key=id]').value()+']').click();
      }
    });
  });
});

$('.bt_Ajout_commande_planification').on('click', function () {
  addCmdPlanificationToTable({});
  modifyWithoutSave = true;
});

$("body").delegate(".listCmdAction", 'click', function() {
    var el = $(this).closest('div div').find('.expressionAttr[data-l1key=cmd]');
    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
        el.value(result.human);
        jeedom.cmd.displayActionOption(el.value(), '', function(html) {
            el.closest('div td').find('.actionOptions').html(html);
        });
    });
});

$("body").delegate(".listAction", 'click', function () {
	var el = $(this).closest('div div').find('.expressionAttr[data-l1key=cmd]');
	jeedom.getSelectActionModal({}, function (result) {
		el.value(result.human);
		jeedom.cmd.displayActionOption(el.value(), '', function (html) {
			el.closest('div td').find('.actionOptions').html(html);
			taAutosize();
		});
	});
});

$("body").delegate(".listCmdTemperature", 'click', function () {
	var el = $(this).closest('div').find('.eqLogicAttr[data-l2key=temperature_id]');
	jeedom.cmd.getSelectModal({cmd: {type: 'info',subType:"numeric"}}, function (result) {
		el.value(result.human);
	});
});
$("body").delegate(".listCmdEtat", 'click', function () {
	var el = $(this).closest('div').find('.eqLogicAttr[data-l2key=etat_allume_id]');
	jeedom.cmd.getSelectModal({cmd: {type: 'info',subType:"binary"}}, function (result) {
		el.value(result.human);
	});
});
$("body").delegate(".listCmdEtatBoost", 'click', function () {
	var el = $(this).closest('div').find('.eqLogicAttr[data-l2key=etat_boost_id]');
	jeedom.cmd.getSelectModal({cmd: {type: 'info',subType:"binary"}}, function (result) {
		el.value(result.human);
	});
});

$("body").delegate('.bt_Suppression_commande_planification', 'click', function() {
	var progs = [];

	var cmd_id=$(this).closest('div tr').getValues('.expressionAttr')[0]['Id']
	$('#div_planifications').find('.select-selected').each(function () {
		if($(this)[0].getAttribute("id")==cmd_id){
			if (!progs.includes($(this).closest('div .planification').find(".nom_planification")[0].innerHTML)){
				progs.push($(this).closest('div .planification').find(".nom_planification")[0].innerHTML);
			}					
		}
	})
	var div=$(this).closest('div tr')
	if (progs.length >0){
		if (progs.length >1){
			message ="Êtes-vous certain de vouloir supprimer cette commande ?<br>Celle-ci est utilisée dans les planifications suivante:<br>" 
			progs.forEach(element => message+= "- " + element + "<br>");
			message+= "Ces planifications ne fonctionneront plus correctement..." 
		}else{
			message ="Êtes-vous certain de vouloir supprimer cette commande ?<br>Celle-ci est utilisée dans la planification suivante:<br> - " + progs + '<br>' + "Cette planification ne fonctionnera plus correctement..." 
			
		}
		bootbox.confirm({
			
			message: message,
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
				if(result){
					div.remove();
				}
			}
		});
	}else{
		$(this).closest('div tr').remove();
	}
})

$('body').delegate('.row_cmd_planification .expressionAttr[data-l1key=cmd]', 'focusout', function (event) {
  	var el = $(this);
	 var expression = el.closest('td').getValues('.expressionAttr');
    jeedom.cmd.displayActionOption(el.value(), expression[0].options, function (html) {
	  el.closest('div td').find('.actionOptions').html(html);
	  taAutosize();
    });
});

function printEqLogic(_eqLogic) {
	$('#div_planifications').empty()
	$('#table_cmd_planification tbody').empty()
	if(_eqLogic.configuration.type == 'Poele') {
		$('#tab_eqlogic .poele .eqLogicAttr[data-l2key=temperature_id]').val(_eqLogic.configuration.temperature_id )
		$('#tab_eqlogic .poele .eqLogicAttr[data-l2key=etat_allume_id]').val(_eqLogic.configuration.etat_allume_id)
		$('#tab_eqlogic .poele .eqLogicAttr[data-l2key=etat_boost_id]').val(_eqLogic.configuration.etat_boost_id)
	}
	if(_eqLogic.configuration.type == 'PAC') {
		$('#tab_eqlogic .PAC .eqLogicAttr[data-l2key=temperature_id]').val(_eqLogic.configuration.temperature_id )
	}
	nom_planification_erreur=[]	
	if (isset(_eqLogic.configuration.commandes_planification)) {
		for (var i in _eqLogic.configuration.commandes_planification) {
			actionOptions = [];
			addCmdPlanificationToTable(_eqLogic.configuration.commandes_planification[i]);
			jeedom.cmd.displayActionsOption({
				params : actionOptions,
				async : false,
				error: function (error) {
					$('#div_alert').showAlert({message: error.message, level: 'danger'});
				},
				success : function(data){
					for(var i in data){
						$('#'+data[i].id).append(data[i].html.html);
					}
					taAutosize();
				}
			});	
		}
	}
	
	
	var SELECT_LIST= Recup_select("planification")
	var CMD_LIST=Recup_liste_commandes_planification()
			$.ajax({
			type: "POST",
			url: "plugins/planification/core/ajax/planification.ajax.php",
			data: {
				action: "Recup_planification",
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
										CMD_LIST.forEach(function(cmd){
											if(periode.Id == cmd["Id"] ){
												Couleur="couleur-" + cmd["couleur"]
												Nom=cmd["nom"]
												Id=cmd["Id"]
											}
										});
										var element = SELECT_LIST.replace("#COULEUR#",Couleur);
										element=element.replace("#VALUE#",Nom)
										element=element.replace("#ID#",Id)
										Ajout_Periode(element, $('.planification:last .JourSemaine.' + jour_en_cours), periode.Debut_periode, periode.Id)
									})
								}
							}
						})
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
	_eqLogic.configuration.commandes_planification = []
	planifications = [];
	erreur=false
	
	$('#div_planifications .planification').each(function () {
		_Cette_planification = {}
		_Cette_planification.nom_planification = $(this).find('.nom_planification').html()
		_Cette_planification.Id=$(this).attr("Id")
		semaine = []
		$(this).find('th').find(".JourSemaine").each(function () {
			jour = {}
			jour.jour = $(this).attr("class").split(' ')[1]
			periodes = []
			$(this).find('.Periode_jour').each(function () {
				debut_periode = $(this).find('.clock-timepicker').val()
				Id = $(this).find('.select-selected')[0].getAttribute('id')
				if(typeof(Id) != 'string'){
					erreur=true
					$(this).find('.select-selected')[0].classList.add("erreur")
				}
				periodes.push({'Debut_periode':debut_periode, 'Id':Id})
			})
			jour.periodes = periodes
			semaine.push(jour)
			
		})
		_Cette_planification.semaine = semaine
		planifications.push(_Cette_planification)
	})
	if (erreur){
		alert("Impossible d'enregistrer la planification. Celle-ci comporte des erreurs.")
		return ;
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
	}								   	
	if(_eqLogic.configuration.type == 'PAC') {
		_eqLogic.configuration.temperature_id = $('#tab_eqlogic .PAC .eqLogicAttr[data-l2key=temperature_id]').val();
	}	

	$('#table_cmd_planification tbody tr').each(function(){
		_eqLogic.configuration.commandes_planification.push($(this).getValues('.expressionAttr')[0])
	})
	return _eqLogic
}

function addCmdToTable(_cmd) {
  
    if (!isset(_cmd)) var _cmd = {configuration:{}}
    if (!isset(_cmd.configuration)) _cmd.configuration = {}
	
	
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
			tr += '<td>'
			tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none">'
			tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none">'
			tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none">'
			tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 60%" placeholder="{{Nom}}"></td>'
			tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="display : none" />'
			tr += '</td>'
			
			tr += '<td>'
			if (_cmd.subType == "numeric" || _cmd.subType == "binary") {
				tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized"/>{{Historiser}}</label></span> '
			}
			tr += '</td>'
			
			tr += '<td>'
			if (is_numeric(_cmd.id)){
				tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> '
				tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>'
			}
			tr += '</td>'
		tr += '</tr>'

		if (_cmd.type == 'info'){
			$('#table_infos tbody').append(tr)
			$('#table_infos tbody tr:last').setValues(_cmd, '.cmdAttr')
			if (isset(_cmd.type)) $('#table_infos tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type))
			jeedom.cmd.changeType($('#table_infos tbody tr:last'), init(_cmd.subType))
		}else{
			$('#table_actions tbody').append(tr)
			$('#table_actions tbody tr:last').setValues(_cmd, '.cmdAttr')
			if (isset(_cmd.type)) $('#table_actions tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type))
			jeedom.cmd.changeType($('#table_actions tbody tr:last'), init(_cmd.subType))
		}
		
		if (_cmd.logicalId == 'set_planification'){
			set_planification_Id = _cmd.id
		} 
		
}

function addCmdPlanificationToTable(_action,id) {
	if (!isset(_action)) {
        _action = {};
    }
    if (!isset(_action.options)) {
        _action.options = {};
	}
	
	var actionOption_id = uniqId();
	var Id = uniqId();
	actionOptions = [];
	var SELECT_LIST=Recup_select("commande");
	var CMD_LIST=Recup_liste_commandes_planification()
	tr = '';
			tr += '<tr class="row_cmd_planification">';
			tr += '<td style="display : none">';
					tr += '<div class="row">';
						tr += '<div class="col-sm-6" style="width:100%;">';
							tr += '<input class="expressionAttr form-control input-sm" data-l1key="Id" style="display : none" value="' + Id+ '" disabled>';
						tr += '</div>';
					tr += '</div>';
				tr += '</td>';
				tr += '<td>';
					tr += '<div class="row">';
						
						tr += '<div class="col-sm-6" style="width:100%;">';
							tr += '<input class="expressionAttr form-control input-sm" data-l1key="nom">';
						tr += '</div>';
					tr += '</div>';
				tr += '</td>';
				
				tr += '<td>';
					tr += '<div class="input-group" style=" width:100%;">';
						tr += '<input class="expressionAttr form-control input-sm cmdAction" data-l1key="cmd"/>';
						tr += '<span class="input-group-btn">';
							tr += '<a class="btn btn-success btn-sm listCmdAction"><i class="fa fa-tasks"></i></a>';
							tr += '<a class="btn btn-success btn-sm listAction"><i class="fa fa-list-alt"></i></a>';
							tr += '<a class="btn btn-default bt_Suppression_commande_planification btn-sm"><i class="fa fa-minus-circle"></i></a>';
						tr += '</span>';
						
					tr += '</div>';
					tr += '<div class="actionOptions" id="'+actionOption_id+'">';
					tr += '</div>';						
				tr += '</td>';
				
				//n'est pas affiché mais dois rester
					
			
				tr += '<td>';
					
					tr += '<div class="custom-select">'
						tr += SELECT_LIST
						
					tr += '</div>'

				tr += '</td>';
			tr+= '</tr>';
			
			$('#table_cmd_planification tbody').append(tr);
			$('#table_cmd_planification tbody tr:last').setValues(_action, '.expressionAttr');
			if(Object.keys( _action ).length>1){
			  $('#table_cmd_planification tbody tr:last').find(".select-selected")[0].classList.replace("#COULEUR#","couleur-" + $('#table_cmd_planification tbody tr:last td:last').find(".select-selected")[0].innerHTML);
			  $('#table_cmd_planification tbody tr:last .select-items ').find("."+"couleur-" + $('#table_cmd_planification tbody tr:last td:last').find(".select-selected")[0].innerHTML)[0].classList.add("same-as-selected")
			}else{
				$('#table_cmd_planification tbody tr:last').find(".select-selected")[0].classList.replace("#COULEUR#","couleur-orange");
				$('#table_cmd_planification tbody tr:last').find(".select-selected")[0].innerHTML="orange"
				$('#table_cmd_planification tbody tr:last .select-items ').find("."+"couleur-orange")[0].classList.add("same-as-selected")
				  
			}
			 
				
    
    actionOptions.push({
        expression : init(_action.cmd, ''),
        options : _action.options,
        id : actionOption_id
    });

}