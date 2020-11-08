JSONCLIPBOARD = null
PROGRAM_MODE_LIST = []
JOURREF = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche']
JOURS = ['{{Lundi}}', '{{Mardi}}', '{{Mercredi}}', '{{Jeudi}}', '{{Vendredi}}', '{{Samedi}}', '{{Dimanche}}']
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
function set_custom_select(periode=false){
	
	/* Look for any elements with the class "custom-select": */
	Collection_custom_select = document.getElementsByClassName("custom-select");
  
	for (i = 0; i < Collection_custom_select.length; i++) {
		Select_original = Collection_custom_select[i].getElementsByTagName("select")[0];
      	if(typeof(Select_original) == "object"){
			if (typeof($(Collection_custom_select[i]).find('.select-selected')[0]) == "object"){continue}
			/* For each element, create a new DIV that will act as the selected item: */
				div_select_selected = document.createElement("DIV");
				if (periode){
					div_select_selected.classList.add("select-selected")
					span_select_selected = document.createElement("SPAN");
					if(Select_original.selectedIndex != -1){
						span_select_selected.innerHTML = Select_original.options[Select_original.selectedIndex].innerHTML;
						div_select_selected.setAttribute("Id",Select_original.options[Select_original.selectedIndex].getAttribute("Id"))
					}else{
						span_select_selected.innerHTML = "ERREUR";
						div_select_selected.classList.add('erreur')
					}		
					if(Select_original.length == 1){
						div_select_selected.classList.add("select-no-arrow")
					}else{
						div_select_selected.classList.remove("select-no-arrow")
					}
					
					div_select_selected.appendChild(span_select_selected);
				}else{
					div_select_selected.innerHTML = Select_original.options[Select_original.selectedIndex].innerHTML;
					div_select_selected.classList.add("select-selected" ,"expressionAttr" )
					div_select_selected.setAttribute('data-l1key','couleur')
				}
			if(Select_original.selectedIndex != -1){
				div_select_selected.classList.add(recup_class_couleur(Select_original.options[Select_original.selectedIndex].className.split(' ')))
			}
			
			
			
			
			
			Collection_custom_select[i].appendChild(div_select_selected);
			/* For each element, create a new DIV that will contain the option list: */
			div_select_items = document.createElement("DIV");
			div_select_items.setAttribute("class", "select-items");
			for (j = 0; j < Select_original.length; j++) {
				/* For each option in the original select element,    create a new DIV that will act as an option item: */
				div_item = document.createElement("DIV");
				
				div_item.classList.add(recup_class_couleur(Select_original.options[j].className.split(' ')))
				if(Select_original.selectedIndex != -1){
					if (div_item.classList.contains(recup_class_couleur(Select_original.options[Select_original.selectedIndex].className.split(' ')))){
						div_item.classList.add("same-as-selected")
						
					}
				}
			
				
				if (periode){

					div_item.setAttribute("Id",Select_original.options[j].getAttribute("Id"))
					div_item.setAttribute("value",Select_original.options[j].getAttribute("value"))
					span_item = document.createElement("SPAN");
					span_item.innerHTML =  Select_original.options[j].innerHTML;
					div_item.appendChild(span_item)
					
					div_item.addEventListener("click", function(e) {
						select_custom = this.parentNode.previousSibling;
						select_custom.innerHTML = this.innerHTML;
						select_custom.classList.remove(recup_class_couleur(select_custom.classList))
						select_custom.classList.add(recup_class_couleur(this.classList))
						select_custom.setAttribute("Id",this.getAttribute("Id"))
						y = this.parentNode.getElementsByClassName("same-as-selected");
						for (k = 0; k < y.length; k++) {
							y[k].classList.remove("same-as-selected")
						}
						this.classList.add("same-as-selected")
						
						MAJ_Graphique_jour($(this).closest('.JourSemaine'))
						select_custom.click();
					});
				}else{
						div_item.classList.add("commande")
						div_item.innerHTML = Select_original.options[j].getAttribute("value");
						
						div_item.addEventListener("click", function(e) {
							
							select_custom = this.parentNode.previousSibling;
							select_custom.innerHTML = this.innerHTML;
							select_custom.classList.remove(recup_class_couleur(select_custom.classList))
							select_custom.classList.add(recup_class_couleur(this.classList))
							
							y = this.parentNode.getElementsByClassName("same-as-selected");
							for (k = 0; k < y.length; k++) {
								y[k].classList.remove("same-as-selected")
							}
							this.classList.add("same-as-selected")
							select_custom.click();
						});
				}
				div_select_items.appendChild(div_item);

			}

			Collection_custom_select[i].appendChild(div_select_items);
			if (periode){
				Divjour = $(div_select_selected).closest('.JourSemaine')
				MAJ_Graphique_jour(Divjour)
				closeAllSelect(div_select_items);
			}else{
				div_select_selected.classList.add("commande")
				div_select_items.classList.add("commande")
				closeAllSelect(div_select_items);
			}
			Select_original.remove()
			
			div_select_selected.addEventListener("click", function(e) {
				/* When the select box is clicked, close any other select boxes,
				and open/close the current select box: */
				e.stopPropagation();
				closeAllSelect(this);
				this.nextSibling.classList.toggle("select-hide");
				this.classList.toggle("select-arrow-active");
			});
			
			
		}
	}
}

function closeAllSelect(elmnt) {
  /* A function that will close all select boxes in the document,
  except the current select box: */
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

/* If the user clicks anywhere outside the select box,
then close all select boxes: */
document.addEventListener("click", closeAllSelect);
function UpdateplanificationNuit() {
  if ($('.HeureCoucher')[0].firstChild == null){ return null}
  $('.planificationNuit').text($('.HeureCoucher')[0].firstChild.data);
  if (!isNaN(parseInt($('.HeureFermetureMin')[0].value.replace(":", "")))) {
    if ( parseInt($('.HeureCoucher')[0].firstChild.data.replace(":", "")) <parseInt($('.HeureFermetureMin')[0].value.replace(":", ""))){ 
      $('.planificationNuit').text($('.HeureFermetureMin')[0].value);
    }
  }
  if (!isNaN(parseInt($('.HeureFermetureMax')[0].value.replace(":", "")))) { 
    if (parseInt($('.HeureCoucher')[0].firstChild.data.replace(":", "")) > parseInt($('.HeureFermetureMax')[0].value.replace(":", ""))){ 
      $('.planificationNuit').text($('.HeureFermetureMax')[0].value);
    }
  }
}
function UpdateplanificationJour() {
  if ($('.HeureLever')[0].firstChild == null){ return null}
  $('.planificationJour').text($('.HeureLever')[0].firstChild.data);
  if (!isNaN(parseInt($('.HeureOuvertureMin')[0].value.replace(":", "")))) {
    if ( parseInt($('.HeureLever')[0].firstChild.data.replace(":", "")) <parseInt($('.HeureOuvertureMin')[0].value.replace(":", ""))){ 
      $('.planificationJour').text($('.HeureOuvertureMin')[0].value);
    }
  }
  if (!isNaN(parseInt($('.HeureOuvertureMax')[0].value.replace(":", "")))) { 
    if (parseInt($('.HeureLever')[0].firstChild.data.replace(":", "")) > parseInt($('.HeureOuvertureMax')[0].value.replace(":", ""))){ 
      $('.planificationJour').text($('.HeureOuvertureMax')[0].value);
    }
  }
}
String.prototype.sansAccent = function(){//OK
    var accent = [
        /[\300-\306]/g, /[\340-\346]/g, // A, a
        /[\310-\313]/g, /[\350-\353]/g, // E, e
        /[\314-\317]/g, /[\354-\357]/g, // I, i
        /[\322-\330]/g, /[\362-\370]/g, // O, o
        /[\331-\334]/g, /[\371-\374]/g, // U, u
        /[\321]/g, /[\361]/g, // N, n
        /[\307]/g, /[\347]/g, // C, c
    ];
    var noaccent = ['A','a','E','e','I','i','O','o','U','u','N','n','C','c'];
     
    var str = this;
    for(var i = 0; i < accent.length; i++){
        str = str.replace(accent[i], noaccent[i]);
    }
     
    return str;
}

$("input[data-l1key='functionality::cron15::enable']").on('change',function(){//OK
  if ($(this).is(':checked')) {
    $("input[data-l1key='functionality::cron5::enable']").prop("checked", false);
    $("input[data-l1key='functionality::cron::enable']").prop("checked", false);
    }
});
//selection du type d'équipement et maj de son image:

$('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').on('change',function(){//OK
   
   if ($(this).value() == "PAC"){
	   $('#img_planificationModel').attr('src','plugins/planification/core/img/pac.png')
   }else if ($(this).value() == "Volet"){
	   $('#img_planificationModel').attr('src','plugins/planification/core/img/volet.png')
   }else if ($(this).value() == "Chauffage"){
	   $('#img_planificationModel').attr('src','plugins/planification/core/img/chauffage.png')
   }else{
	   $('#img_planificationModel').attr('src','plugins/planification/core/img/autre.png')
   }
})
  
//planifications:
$('#bt_ajouter_planification').off('click').on('click', function () {//OK
	bootbox.prompt({
        title: "Veuillez inserer le nouveau nom de la planification à ajouter.",
        buttons: {
            confirm: {label: 'Ajouter', className: 'btn-success'},
            cancel: {label: 'Annuler', className: 'btn-danger'}
        },
        callback: function (resultat) {
            if (resultat!== null && resultat != '') {
               Ajoutplanification({nom: resultat, nouveau: true})
            }
        }
    })
})
$('#bt_importer_planification').off('click').on('click', function () {//OK
     $('#md_modal').dialog({title: "{{Importation de planification}}"});
    $('#md_modal').load('index.php?v=d&plugin=planification&modal=Importer_planification&type=' + $('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').value()).dialog('open');
})

function Ajoutplanification(_planification, _updateProgram) {//OK
    if (init(_planification.nom) == '') return
    var random = Math.floor((Math.random() * 1000000) + 1)
    var div = '<div class="planification panel panel-default">'
			div += '<div class="panel-heading">'
				div += '<h3 class="panel-title" style="padding-bottom: 4px;">'
					div += '<a class="accordion-toggle collapsed" style="width: calc(100% - 420px);" data-toggle="collapse" data-parent="" href="#collapse' + random + '">'
						div += '<span class="nom_planification">' + _planification.nom + '</span>'
							div += '<span class="input-group-btn pull-right">'
								div += '<a class="btn btn-sm bt_renommer_planification btn-warning roundedLeft"><i class="fas fa-copy"></i> {{Renommer}}</a>'																										 
								div += '<a class="btn btn-sm bt_dupliquer_planification btn-primary roundedLeft"><i class="fas fa-copy"></i> {{Dupliquer}}</a>'
								div += '<a class="btn btn-sm bt_exporter_planification btn-default"><i class="fas fa-sign-out-alt"></i> {{Exporter}}</a>'
								div += '<a class="btn btn-sm bt_appliquer_planification btn-danger" title="Appliquez le planification maintenant"><i class="fas fa-check-circle"></i> {{Appliquer}}</a>'
								div += '<a class="btn btn-sm bt_supprimer_planification btn-danger roundedRight"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>'
							div += '</span>'
					div += '</a>'
					
						
				div += '</h3>'
			div += '</div>'
			div += '<div id="collapse' + random + '" class="panel-collapse collapse" style="height: 0px;">'
				div += '<div class="panel-body">'
					div += '<div>'
						div += '<form class="form-horizontal" role="form">'
							div += '<div class="div_programDays">'
							
								JOURS.forEach(function(jour) {
									div += '<div class="JourSemaine '+jour+'" style="width:14%; float:left">'
										div += '<center>'
											div += '<strong class="NomJour">'+jour+'</strong>'
												div += '</br>'
											div += '<div class="input-group" style="display:inline-flex">'
												div += '<span class="input-group-btn">'
													div += '<span><i class="fa fa-plus-circle cursor bt_ajout_periode" title="{{Ajouter une période}}"></i> </span>'
													div += '<span><i class="fas fa-sign-out-alt cursor bt_copier_jour" title="{{Copier le jour}}"></i> </span>'
													div += '<span><i class="fas fa-sign-in-alt cursor bt_coller_jour" title="{{Coller le jour}}"></i> </span>'
												div += '</span></br>'
											div += '</div>'

										div += '</center>'
									div += '</div>'	 
								})
								//graphiques:
								div += '<div class="graphJours" style="width:100%; clear:left">'
								div += '<hr>'
									
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
					div += '</div>'
				div += '</form>'
			div += '</div>'
		div += '</div>'
    div += '</div>'
    div += '</div>'

    $('#div_planifications').append(div)
    $('#div_planifications .planification:last').setValues(_planification, '.programAttr')

    //init jours:
    if (_planification.nouveau) {
        $('#div_planifications .planification:last .JourSemaine').each(function () {
            jour = $(this).closest('.JourSemaine')
            Ajout_Periode(jour)
        })
    }
	
}
  
$('#div_planifications').off('click','.bt_exporter_planification').on('click','.bt_exporter_planification',  function () {//OK
    planification = $(this).closest('.planification')
	planification_a_exporter = {}
    planification_a_exporter.nom_planification = planification.find('.nom_planification').html()
	planification_a_exporter.type_planification=$('.eqLogicAttr[data-l1key=configuration][data-l2key=type]').val()
	planification_a_exporter.edLogicId=$('.eqLogicAttr[data-l1key=id ]').val()
	
    semaine = []
	erreur=false
    planification.find('.JourSemaine').each(function () {
		jour = {}
        jour.jour = $(this).find('.NomJour').html()
        periodes = []
        $(this).find('.Periode_jour').each(function () {
            period = {}
            debut_periode = $(this).find('.timePicker').val()
			
			Id = $(this).find('.select-selected')[0].getAttribute('id')
			
			if(typeof(Id) != 'string'){
				erreur=true
			}
			
            periodes.push({'Debut_periode':debut_periode, 'Id':Id })
			
        })
        jour.periodes = periodes
        semaine.push(jour)
    })
    planification_a_exporter.semaine = semaine
	if (erreur){
		$('#div_alert').showAlert({
			message: "{{Impossible d'exporter le planification. Celui-ci comporte des erreurs.}}",
			level: 'danger'
		})
		return;
	}
	$.ajax({
		type: "POST",
		url: "plugins/planification/core/ajax/planification.ajax.php",
		data: {
			action: "exporter_planification",
			nom: planification_a_exporter.nom_planification,
			planification: planification_a_exporter
		},
		dataType: 'json',
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
			$('#div_alert').showAlert({
				message: '{{Exportation réussie!}}',
				level: 'success'
			})
		}
	})
})

$("#div_planifications").off('click','.bt_supprimer_planification').on('click', '.bt_supprimer_planification',function () {//OK
    Ce_progamme = $(this).closest('.planification')
    bootbox.confirm({
        message: "Voulez vous vraiment supprimer ce planification ?",
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

$('#div_planifications').off('click','.bt_dupliquer_planification').on('click','.bt_dupliquer_planification',  function () {//OK
    var planification = $(this).closest('.planification').clone()
     bootbox.prompt({
        title: "Veuillez inserer le nom pour le planification dupliqué.",
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

$('#div_planifications').off('click','.bt_appliquer_planification').on('click','.bt_appliquer_planification',  function () {//OK
    planification = $(this).closest('.planification')
    programName=planification.find('.nom_planification').html()
    bootbox.confirm({
        message: "Voulez vous vraiment appliquer le planification "+programName+" maintenant ?",
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
                jeedom.cmd.execute( {id: _setProgramId_, value: {select: programName} })
            }
        }
    })
})

$('#div_planifications').off('click','.bt_renommer_planification').on('click','.bt_renommer_planification',  function () {//OK
	var el = $(this)
	bootbox.prompt({
        title: "Veuillez inserer le nouveau nom pour le planification:" + $(this).closest('.planification').find('.nom_planification').html() +".",
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

$("#div_planifications").sortable({
	axis: "y", cursor: "move", items: ".planification", handle: ".panel-heading", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true
})
  
$("body").off('click', '.bt_supprimer_perdiode').on( 'click', '.bt_supprimer_perdiode',function () {//OK
    Divjour = $(this).closest('.JourSemaine')
    $(this).closest('.Periode_jour').remove()
    MAJ_Graphique_jour(Divjour)
});

$('body').off('click','.bt_ajout_periode').on('click','.bt_ajout_periode',  function () {//OK
    Divjour = $(this).closest('.JourSemaine')
    Ajout_Periode(Divjour)
})

$('body').off('click','.bt_copier_jour').on('click','.bt_copier_jour',  function () {//OK
    var jour = $(this).closest('.JourSemaine')
	JSONCLIPBOARD = { data : []}
    jour.find('.Periode_jour').each(function  () {
        debut_periode = $(this).find('.timePicker').val()
		Id = $(this).find('.select-selected').attr("id")
		JSONCLIPBOARD.data.push({debut_periode, Id})
    })
})

$('body').off('click','.bt_coller_jour').on('click','.bt_coller_jour',  function () {//OK
    Divjour = $(this).closest('.JourSemaine')
	if (JSONCLIPBOARD == null) return
    Vider_jour(Divjour)
	Divjour.find('.Periode_jour').each(function  () {
        $(this).remove()
    })
    JSONCLIPBOARD.data.forEach(function(item) {
        Ajout_Periode(Divjour, item.debut_periode, item.Id)
    })
})

function MAJ_Graphique_jour(Div_jour){//OK
    graphDiv = $(Div_jour).closest('.div_programDays').find('.graphique_jour_' + $(Div_jour).attr("class").split(' ')[1])
	graphDiv.empty()
    Periode_jour = $(Div_jour).find('.Periode_jour')

    for (i=0; i<Periode_jour.length; i++)
    {
        isFirst = (i == 0) ? true : false
        isLast = (i == Periode_jour.length-1) ? true : false
        periode = Periode_jour[i]
        debut_periode = $(periode).find('.timePicker').val()
        heure_debut = (parseInt(debut_periode.split(':')[0]) * 60) + parseInt(debut_periode.split(':')[1])
        if(isFirst && heure_debut != 0){
			heure_fin = heure_debut
			delta = heure_fin
			width = (delta*100) / 1440
			class_periode = ""
			mode = "Aucun"
			nouveau_graph = '<div style="width:'+width+'%; height:20px; display:inline-block;" title="'+mode+'"></div>'
			graphDiv.append(nouveau_graph)
		}
		if (isLast){
            heure_fin = 1439
        }else{
            fin_periode = $(Periode_jour[i+1]).find('.timePicker').val()
			heure_fin = (parseInt(fin_periode.split(':')[0]) * 60) + parseInt(fin_periode.split(':')[1])
        }
        delta = heure_fin - heure_debut
        width = (delta*100) / 1440
		class_periode=recup_class_couleur($(periode).find('.select-selected').attr('class').split(' ')) 
		mode = $(periode).find('.select-selected').text()
        nouveau_graph = '<div class="'+class_periode+'" style="width:'+width+'%; height:20px; display:inline-block;" title="'+mode+'"></div>'
        graphDiv.append(nouveau_graph)
    }
}

function checkTimePicker(picker){
    val = $(picker).val()
    Div_jour = $(picker).closest('.JourSemaine')
    periode_jour = $(Div_jour).find('.Periode_jour')
    if (periode_jour.length > 0){
        for (i=0; i<periode_jour.length; i++){
			if (i==0){
				$(picker).clockTimePicker('value', "00:00")
			}else{
				periode = periode_jour[i]
				debut = $(periode).find('.timePicker').val()
				if (debut == val){
					
					debut_periode_precedente = $(periode_jour[i-1]).find('.timePicker').val()
					heure_debut_periode_precedente = (parseInt(debut_periode_precedente.split(':')[0]) * 60) + parseInt(debut_periode_precedente.split(':')[1])
				
					heure_debut = (parseInt(val.split(':')[0]) * 60) + parseInt(val.split(':')[1])

					if (heure_debut <= heure_debut_periode_precedente){
						nouvelle_valeur = debut_periode_precedente.split(':')[0] + ':' + (parseInt(debut_periode_precedente.split(':')[1]) + 1)
						$(picker).clockTimePicker('value', nouvelle_valeur)
					}
				}else{
					continue
				}
			}
        }
    }
}

function Ajout_Periode(Div_jour, time=null, Mode_periode=null){//OK
    Periode_jours = $(Div_jour).find('.Periode_jour')
    if (Periode_jours.length > 0){
        periode_precedente = Periode_jours[Periode_jours.length-1]
        dernier_debut = $(periode_precedente).find('.timePicker').val()	
				
        if (time == null){
			time_int=parseInt(parseInt(dernier_debut.split(':')[0] * 60) + parseInt(dernier_debut.split(':')[1]))
			time_int+=15
			heure=parseInt(time_int/60)
			
			minute=time_int-(heure*60)
			
            time = heure + ':' + minute
        }else if (Mode_periode == null){
            last_timeStart = (parseInt(dernier_debut.split(':')[0]) * 60) + parseInt(dernier_debut.split(':')[1])
            heure_debut = (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1])
			if (heure_debut <= last_timeStart) {
				time = dernier_debut.split(':')[0] + ':' + (parseInt(dernier_debut.split(':')[1]) + 1)
			}
        }
    }
	
	PROGRAM_MODE_LIST = Recup_liste_mode_planification()
    if (time == null) time = '00:00'
	div = '<div class="Periode_jour">'
		div += '<div class="input-group" style="width:100% !important; line-height:1.4px !important;">'
			div += '<input class="checkbox form-control input-sm cursor" type="checkbox" onchange="Maj_checkbox(this)">'
			div += '<input class="timePicker form-control input-sm cursor" type="text" value="'+time+'" style="width:60px; min-width:60px;" onchange="checkTimePicker(this)">'
			div += '<div class="custom-select">'
			div += '<select class="expressionAttr form-control input-sm Select_Mode_Periode" data-l2key="graphColor" style="width:calc(100% - 93px);display:inline-block">'
				for (var i = 0; i < PROGRAM_MODE_LIST.length; i++) {
					div += PROGRAM_MODE_LIST[i]
				}
			div += '</select>'
			div += '</div>'
 
 
			div += '<a class="btn btn-default bt_supprimer_perdiode btn-sm" title="Supprimer cette période"><i class="fa fa-minus-circle"></i></a>'
		div += '</div>'
		
	div += '</div>'
	
 
    nouvelle_periode = $(div)
    if (time != '00:00'){
        nouvelle_periode.find('.timePicker').clockTimePicker()
        nouvelle_periode.find('.clock-timepicker').attr('style','display: inline')
    }else{
		nouvelle_periode.find('.timePicker').prop('readonly', true)
	}
	select = nouvelle_periode.find('.Select_Mode_Periode')
	
    if (Mode_periode != null){
		trouve=false
		
		for(it = 0; it < select[0].options.length; it++){
			if(Mode_periode == select[0].options[it].getAttribute("id")){
				
				trouve=true
				select.selectedIndex = select[0].options[it];
				select.val(select.selectedIndex.getAttribute("value"))
				break;
			}
		}
		
	if(!trouve){
		select.selectedIndex = -1;
	select.val("")
	}
    }else{
		
		select = nouvelle_periode.find('.Select_Mode_Periode')
		select.selectedIndex = 0;
		select.val(select.value().sansAccent())
	}
    Div_jour.append(nouvelle_periode)
	set_custom_select(periode=true)
   
	
}

function Vider_jour(jour){//OK
    jour.find('.Periode_jour').each(function  () {
        $(this).remove()
    })
}

function Recup_liste_mode_planification(){
	temp = []
 $.ajax({
      type: "POST",
      url: "plugins/planification/core/ajax/planification.ajax.php",
      data: {
        action: "Recup_liste_mode_planification",
        eqLogic_id: $('.eqLogicAttr[data-l1key=id]').value()
      },
	  dataType: 'json',
      global: true,
	  async: false,
      error: function (request, status, error) {
        handleAjaxError(request, status, error);
      },
      success: function (data) {
        if (data.state != 'ok') {
          $('#div_alert').showAlert({message: data.result, level: 'danger'});
          return;
        }
		temp.push(data.result);
		return;
		
      }
    });	
	PROGRAM_MODE_LIST=temp
	return temp
	
}
$('.bt_Importer_planification').on('click',function(){ //appelé par modal!
    programFilePath = "/plugins/planification/planifications/" + $(this).attr("nom_fichier")
    $.getJSON(programFilePath, function(jsonDatas){
		divDays = null
        $('#div_planifications .planification').each(function (){
            if ($(this).find('.nom_planification').html() == jsonDatas.nom_planification){
				divDays = $(this).find(".div_programDays")
				return
			}
        })
		if(divDays == null){
			Ajoutplanification({nom:jsonDatas.nom_planification,nouveau:false})
			
			$('#div_planifications .planification:last .JourSemaine').each(function () {
				dayElName = $(this).find('.NomJour').html()
				dayElNameRef = JOURREF[JOURS.indexOf(dayElName)]
				for (j in jsonDatas.semaine) {
					planification_jour = jsonDatas.semaine[j]
					if (planification_jour.jour == dayElNameRef){
						periodes = planification_jour.periodes
						for (k in periodes) {
							periode = periodes[k]
							Ajout_Periode($(this), periode.Debut_periode, periode.Id)
						}
					}
				}
			})
		}else{
			divDays.find('.JourSemaine').each(function () {
				Vider_jour($(this))
				dayElName = $(this).find('.NomJour').html()
				dayElNameRef = JOURREF[JOURS.indexOf(dayElName)]
				for (j in jsonDatas.semaine) {
					jour = jsonDatas.semaine[j]
					dayNamefr = jour.jour
					NomJour = JOURS[JOURREF.indexOf(dayElName)]
					if (dayNamefr == dayElNameRef){
						periodes = jour.periodes
						for (k in periodes) {
							periode = periodes[k]
							//Ajout_Periode($(this), periode.Debut_periode, periode.Mode,periode.Value)
							Ajout_Periode($(this), periode.Debut_periode, periode.id)
						}
					}
				}
			})
		}		
    });
    $('#md_modal').dialog("close")
})

$('.bt_Supprimer_planification').on('click',function(){ //appelé par modal!
    _Programe_Div_a_supprimer = $(this).closest(".planification")
    _nom_fichier = $(this).closest(".bt_Supprimer_planification").attr("nom_fichier")
	_nom_fichier = $(this).attr("nom_fichier")
    bootbox.confirm({
        message: "Voulez vous vraiment supprimer ce fichier de planification ?",
        buttons: {
            confirm: {label: 'Supprimer', className: 'btn-danger'},
            cancel: {label: 'Annuler', className: 'btn-success'}
        },
        callback: function (result) {
            if (result === true) {
                $.ajax({
                    type: "POST",
                    url: "plugins/planification/core/ajax/planification.ajax.php",
                    data: { action: "supprimer_planification", nom_fichier: _nom_fichier},
                    dataType: 'json',
                    error: function (request, status, error) {
                        handleAjaxError(request, status, error)
                    },
                    success: function (data) {
                        if (data.state == 'ok') {
                            _Programe_Div_a_supprimer.remove()
                        }
                    }
                })
            }
        }
    })
})

$('.bt_Telecharger_planification').on('click',function(){ //appelé par modal!
    Nom_fichier = $(this).attr("nom_fichier")
    programFilePath = "/plugins/planification/planifications/" + Nom_fichier
    $.getJSON(programFilePath, function(data){
        dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data))
        downloadAnchorNode = document.createElement('a')
        downloadAnchorNode.setAttribute("href",     dataStr)
        downloadAnchorNode.setAttribute("target", "_blank")
        downloadAnchorNode.setAttribute("download", Nom_fichier)
        document.body.appendChild(downloadAnchorNode)
        downloadAnchorNode.click()
        downloadAnchorNode.remove()
    })
})
  
$('.bt_showExpressionTest').off('click').on('click', function () {
  $('#md_modal').dialog({title: "{{Testeur d'expression}}"});
  $("#md_modal").load('index.php?v=d&modal=expression.test').dialog('open');
});

$('.bt_Importer_EqLogic').off('click').on('click', function () {
  
  jeedom.eqLogic.getSelectModal({}, function (result) {
    $.ajax({
      type: "POST",
      url: "plugins/planification/core/ajax/planification.ajax.php",
      data: {
        action: "importer_eqlogic",
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
    //var type = $(this).attr('data-type');
    var el = $(this).closest('div div').find('.expressionAttr[data-l1key=cmd]');
    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
        el.value(result.human);
        jeedom.cmd.displayActionOption(el.value(), '', function(html) {
            el.closest('div td').find('.actionOptions').html(html);
        });
    });
});

$("body").delegate(".listAction", 'click', function () {
	//var type = $(this).attr('data-type');
	var el = $(this).closest('div div').find('.expressionAttr[data-l1key=cmd]');
	jeedom.getSelectActionModal({}, function (result) {
		el.value(result.human);
		jeedom.cmd.displayActionOption(el.value(), '', function (html) {
			el.closest('div td').find('.actionOptions').html(html);
			taAutosize();
		});
	});
});

$("body").delegate('.bt_removeAction', 'click', function() {
			var progs = [];

			cmd_id=$(this).closest('div tr').getValues('.expressionAttr')[0]['Id']
			$('#div_planifications').find('.select-items .same-as-selected').each(function () {
				if($(this)[0].getAttribute("id")==cmd_id){
					if (!progs.includes($(this).closest('div .planification').find(".nom_planification")[0].innerHTML)){
						progs.push($(this).closest('div .planification').find(".nom_planification")[0].innerHTML);
					}					
				}
			})
			if (progs.length >0){
				bootbox.confirm('{{Êtes-vous sûr de vouloir supprimer cette commande ?}}', function (result) {
					if (result) {
						$(this).closest('div tr').remove();
					}
				});

				
				
				
				console.log(progs)
			}
			
	/*$.ajax({
		type: "POST",
		url: "plugins/planification/core/ajax/planification.ajax.php",
		data: {
			action: "Verificarion_planification_avant_suppression_commande",
			eqLogic_id: $('.eqLogicAttr[data-l1key=id]').value(),
			cmd_id:$(this).closest('div tr').getValues('.expressionAttr')[0]['Id'],
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
			}else{
				$('#div_alert').showAlert({message: data.result, level: 'success'});
				return;
			}
		//window.location.reload()

		//$('.eqLogicDisplayCard[data-eqLogic_id='+$('.eqLogicAttr[data-l1key=id]').value()+']').click();
		}
	});*/
})
$('body').delegate('.row_cmd_planification .expressionAttr[data-l1key=cmd]', 'focusout', function (event) {
  	var el = $(this);
 	var expression = el.closest('div div').getValues('.expressionAttr');
    jeedom.cmd.displayActionOption(el.value(), init(expression[0].options), function (html) {
      el.closest('div td').find('.actionOptions').html(html);
    });
});
function printEqLogic(_eqLogic) {//OK
	$('#div_planifications').empty()
	$('#table_cmd_planification tbody').empty()
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
	
			
	PROGRAM_MODE_LIST = Recup_liste_mode_planification()
	for (var i = 0; i < PROGRAM_MODE_LIST.length; i++) {
		if (PROGRAM_MODE_LIST[i] == ""){
			$('#menu_tab_planifications').hide();
		}
		if (isset(_eqLogic.configuration) && isset(_eqLogic.configuration.planifications)) {
			for (i in _eqLogic.configuration.planifications) {
				try{
					Ce_progamme = _eqLogic.configuration.planifications[i]
					Ajoutplanification({nom: Ce_progamme.nom_planification, nouveau: false})
					$('#div_planifications .planification:last .JourSemaine').each(function () {
						dayElName = $(this).find('.NomJour').html()
						dayElNameRef = JOURREF[JOURS.indexOf(dayElName)]
						for (j in Ce_progamme.semaine) {
							planification_jour = Ce_progamme.semaine[j]
							if (planification_jour.jour == dayElNameRef){
								periodes = planification_jour.periodes
								for (k in periodes) {
									periode = periodes[k]
									Ajout_Periode($(this), periode.Debut_periode, periode.Id)
								}
							}
						}
					})
				}catch(err) {
					throw (err)
					progamme = $('#div_planifications .planification:last').remove()
					nom_planification_erreur.push(Ce_progamme.nom_planification);
				} 
			}
			
			//set_custom_select(periode=true)

			if (nom_planification_erreur.length>0){
				$('#div_alert').showAlert({
								message: "Erreur",
								level: 'warning'
						})
				return
				_eqLogic=saveEqLogic(_eqLogic)
				$.ajax({
					type: "POST",
					url: "plugins/planification/core/ajax/planification.ajax.php",
					data: {
						action: "Sauvegarde_planifications",
						eqLogic_id: _eqLogic.id,
						planifications:_eqLogic.configuration.planifications
					},
					async: false,
					global: false,
					error: function (request, status, error) {handleAjaxError(request, status, error)},
					success: function (data) {
						if(nom_planification_erreur.length==1){
							message= "Votre planification " + nom_planification_erreur[0]+" contient des erreurs.<br>Elle a été automatiquement supprimée."
						}else{
							message= "Vos planifications :<br>"
							for (e in nom_planification_erreur) {
								message+=nom_planification_erreur[e] + "<br>"
							}
							message+=" contiennent des erreurs.<br>Elles ont été automatiquement supprimées."
						}
						$('#div_alert').showAlert({
								message: message,
								level: 'warning'
						})
					}
				})
				return
			}
		}
	}

	
}

function saveEqLogic(_eqLogic) {//OK
    if (!isset(_eqLogic.configuration)) {
        _eqLogic.configuration = {}
    }
	_eqLogic.configuration.planifications = []
	_eqLogic.configuration.commandes_planification = []
	
	for (var i = 0; i < PROGRAM_MODE_LIST.length; i++) {
		if (PROGRAM_MODE_LIST[i] != ""){
   			$('#div_planifications .planification').each(function () {
				_Ceplanification = {}
				_Ceplanification.nom_planification = $(this).find('.nom_planification').html()
				semaine = []
				$(this).find('.JourSemaine').each(function () {
					jour = {}
					NomJour = $(this).find('.NomJour').html()
					idx = JOURS.indexOf(NomJour)
					jour.jour = JOURREF[idx]
					periodes = []
					$(this).find('.Periode_jour').each(function () {
						debut_periode = $(this).find('.timePicker').val()
						if ($(this).find('.select-items .same-as-selected').length >0){
							id=$(this).find('.select-items .same-as-selected')[0].getAttribute("id")
						}else{
							id="erreur";
						}
						periodes.push({'Debut_periode':debut_periode, 'Id':id})
					})
					jour.periodes = periodes
					semaine.push(jour)
				})
				_Ceplanification.semaine = semaine
				
				_eqLogic.configuration.planifications.push(_Ceplanification)
			})
		}
	}
	
	$('#table_cmd_planification tbody tr').each(function(){
		_eqLogic.configuration.commandes_planification.push($(this).getValues('.expressionAttr')[0])
	})
	return _eqLogic
}

//Commandes:
function addCmdToTable(_cmd) {//OK
  
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
		//if (_cmd.nom_planification == 'SetProgram') _setProgramId_ = _cmd.id
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
	tr = '';
			tr += '<tr class="row_cmd_planification">';
			tr += '<td>';
					tr += '<div class="row">';
						tr += '<div class="col-sm-6" style="width:100%;">';
							tr += '<input class="expressionAttr form-control input-sm" data-l1key="Id" value="' + Id+ '" disabled>';
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
							tr += '<a class="btn btn-default bt_removeAction btn-sm"><i class="fa fa-minus-circle"></i></a>';
						tr += '</span>';
						
					tr += '</div>';
					tr += '<div class="actionOptions" id="'+actionOption_id+'">';
					tr += '</div>';						
				tr += '</td>';
				
				//n'est pas affiché mais dois rester
					tr += '<div class="custom-select">'
						tr += '<select class="expressionAttr form-control input-sm Select_Mode_Periode" data-l1key="couleur1" data-l2key="graphColor" style="width:calc(100% - 93px);display:inline-block">'
							for (var i = 0; i < PROGRAM_MODE_LIST.length; i++) {
								tr += PROGRAM_MODE_LIST[i]
							}
						tr += '</select>'
					tr += '</div>'
			
				tr += '<td>';
					
					
					tr += '<div class="custom-select commande">'
						tr += '<select class="couleur-orange expressionAttr form-control input-sm " data-l1key="couleur">';
							tr +=  '<option class ="couleur-orange" value="orange">orange</option>';
							tr +=  '<option class ="couleur-jaune" value="jaune">jaune</option>';
							tr +=  '<option class ="couleur-vert" value="vert">vert</option>';
							tr +=  '<option class ="couleur-bleu" value="bleu">bleu</option>';
							tr +=  '<option class ="couleur-rouge" value="rouge">rouge</option>';
							tr +=  '<option class ="couleur-magenta" value="magenta">magenta</option>';
							tr +=  '<option class ="couleur-marron" value="marron">marron</option>';
							tr +=  '<option class ="couleur-violet" value="violet">violet</option>';	
						tr += '</select>';
					tr +='</div>'
				tr += '</td>';
			tr+= '</tr>';
			
			$('#table_cmd_planification tbody').append(tr);
			$('#table_cmd_planification tbody tr:last').setValues(_action, '.expressionAttr');
			set_custom_select(periode=false)
				
    
    actionOptions.push({
        expression : init(_action.cmd, ''),
        options : _action.options,
        id : actionOption_id
    });

}
