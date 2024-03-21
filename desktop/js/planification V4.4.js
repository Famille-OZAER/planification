JSONCLIPBOARD = null
flatpickr.localize(flatpickr.l10ns.fr)



document.getElementById('div_pageContainer').addEventListener('click', function(event) {
    var _target = null
    closeAllSelect(event.target)
    if (_target = event.target.closest('.ajouter_eqlogic')) {

        var dialog_message =

            '<form class="form-horizontal onsubmit="return false;"> ' +
            '<div> <div class="radio"> <label > ' +
            '<input type="radio" name="Type_équipement" id="Volet" value="Volet" checked="checked"> {{Volet}} </label> ' +
            '</div>' +
            '<div class="radio"> <label > ' +
            '<input type="radio" name="Type_équipement" id="PAC" value="PAC"> {{Pompe à chaleur}}</label> ' +
            '</div> ' +
            '<div class="radio"> <label > ' +
            '<input type="radio" name="Type_équipement" id="Poele" value="Poele"> {{Poêle à granules}}</label> ' +
            '</div> ' +
            '<div class="radio"> <label > ' +
            '<input type="radio" name="Type_équipement" id="Prise" value="Prise"> {{Prise}}</label> ' +
            '</div> ' +
            '<div class="radio"> <label > ' +
            '<input type="radio" name="Type_équipement" id="Chauffage" value="Chauffage"> {{Chauffage avec fil pilote}}</label> ' +
            '</div>' +
            '</div> ' +
            '<div class="radio"> <label > ' +
            '<input type="radio" name="Type_équipement" id="Perso" value="Perso"> {{Perso}}</label> ' +
            '</div> ' +
            '</br> ' +
            '<div class="input">' +
            '<input class="col-sm-8" Type_équipement="text" placeholder="Nom de l\'équipement" name="nom" id="nom" >  ' +

            '</div> <br>' +
            '</div>' +
            '</form>';
        jeeDialog.dialog({
            id: 'mod_ajout_équipement',
            title: '{{Choisissez le type d\'équipement que souhaitez ajouter}}',
            message: dialog_message,
            width: 650,
            height: 320,
            buttons: {
                confirm: {
                    label: '{{Valider}}',
                    className: 'success',
                    callback: {
                        click: function() {

                            if (document.querySelectorAll("input[name='nom']")[0].value == "") {
                                jeedomUtils.showAlert({
                                    message: "Le nom de l'équipement ne peut pas être vide.",
                                    level: 'warning',
                                    timeout: 2000,
                                    emptyBefore: true,
                                });
                                return;
                            }
                            domUtils.ajax({
                                type: "POST",
                                url: "plugins/planification/core/ajax/planification.ajax.php",
                                data: {
                                    action: "Ajout_equipement",
                                    nom: document.querySelectorAll("input[name='nom']")[0].value,
                                    type: document.querySelectorAll("input[name='Type_équipement']:checked")[0].value
                                },
                                global: true,
                                async: false,
                                error: function(request, status, error) {
                                    handleAjaxError(request, status, error);
                                },
                                success: function(data) {
                                    if (data.state != 'ok') {
                                        jeedomUtils.showAlert({ message: data.result, level: 'danger' });

                                    }
                                    window.location.href = 'index.php?v=d&p=planification&m=planification&id=' + data.result;

                                }
                            });
                        }
                    }
                },
                cancel: {
                    label: '{{Annuler}}',
                    className: 'warning',
                    callback: {
                        click: function() {
                            jeeDialog.get('#mod_ajout_équipement').close()
                        }
                    }
                }
            },
            onClose: function() {
                jeeDialog.get('#mod_ajout_équipement').destroy() //No twice footer select/search
            },

        })
    }
    if (_target = event.target.closest('.sante')) {
        jeeDialog.dialog({
            id: 'mod_ajout_équipement',
            title: '{{Santé Planification}}',
            width: (window.innerWidth - 50) < 1500 ? window.innerWidth - 50 : window.innerHeight - 150,
            height: window.innerHeight - 150,
            contentUrl: "index.php?v=d&plugin=planification&modal=health",
            buttons: {

            },
            onClose: function() {
                jeeDialog.get('#mod_ajout_équipement').destroy() //No twice footer select/search
            },

        })

    }
    if (_target = event.target.closest('.dupliquer_equipement')) {
        if (document.querySelector('.eqLogicAttr[data-l1key=id]').value != undefined && document.querySelector('.eqLogicAttr[data-l1key=id]').value != '') {
            jeeDialog.prompt({
                title: '{{Nom de la copie de l\'équipement ?}}',
                value: document.querySelector('.eqLogicAttr[data-l1key=name]').value + "_copie",

                callback: function(result) {
                    if (result !== null) {
                        var id_source = document.querySelector('.eqLogicAttr[data-l1key=id]').value
                        jeedom.eqLogic.copy({
                            id: id_source,
                            name: result,
                            error: function(error) {
                                jeedomUtils.showAlert({ message: error.message, level: 'danger' });
                            },
                            success: function(data) {
                                modifyWithoutSave = false
                                var id_cible = data.id

                                domUtils.ajax({
                                    type: "POST",
                                    url: "plugins/planification/core/ajax/planification.ajax.php",
                                    data: {
                                        action: "Copy_JSON",
                                        id_source: id_source,
                                        id_cible: id_cible
                                    },
                                    global: true,
                                    async: false,
                                    error: function(request, status, error) {

                                        handleAjaxError(request, status, error)

                                    },
                                    success: function(data) {
                                        if (data.state != 'ok') {
                                            jeedomUtils.showAlert({
                                                message: data.result,
                                                level: 'danger'
                                            })
                                            return
                                        }
                                    }
                                })
                                var vars = getUrlVars()
                                var url = 'index.php?'
                                for (var i in vars) {
                                    if (i != 'id' && i != 'saveSuccessFull' && i != 'removeSuccessFull') {
                                        url += i + '=' + vars[i].replace('#', '') + '&'
                                    }
                                }
                                url += 'id=' + data.id + '&saveSuccessFull=1'

                                jeedomUtils.loadPage(url)

                            }
                        })
                        return false
                    }
                }
            })
        }
    }
    if (_target = event.target.closest('.modifier_json')) {

        domUtils.ajax({
            type: "POST",
            url: "plugins/planification/core/ajax/planification.ajax.php",
            data: {
                action: "Modifier_JSON"
            },
            global: true,
            async: false,
            error: function(error) {
                jeedomUtils.showAlert({
                    message: error.message,
                    level: 'danger'
                })
            },
            success: function(data) {
                if (data.state != 'ok') {
                    jeedomUtils.showAlert({
                        message: data.result,
                        level: 'danger'
                    })
                    return
                }
            }
        })
    }
    if (_target = event.target.closest('.li_eqLogic')) {
        let active_tabpane = document.querySelector(".tab-content .tab-pane.active").getAttribute("id")
        jeedomUtils.hideAlert()
        let type = document.body.getAttribute('data-page')
        let thisEqId = _target.getAttribute('data-eqlogic_id')
        if ((isset(event.detail) && event.detail.ctrlKey) || event.ctrlKey || event.metaKey) {
            window.open('index.php?v=d&m=' + type + '&p=' + type + '&id=' + thisEqId).focus()

        } else {
            let thisEqType = _target.getAttribute('data-eqLogic_type')
            jeeFrontEnd.pluginTemplate.displayEqlogic(thisEqType, thisEqId)
        }
        document.querySelectorAll('.li_eqLogic').forEach(_el1 => {
            _el1.removeClass('active');
        });
        this.addClass('active')
        setTimeout(() => {
            document.querySelectorAll("li").forEach(li => {
                if (li.id.toString().includes(active_tabpane)) {
                    li.querySelector('a').click()
                }
            })
        }, "50");

    }
    if (_target = event.target.closest('.bt_afficher_timepicker') || event.target.closest('.bt_afficher_timepicker_planification')) { // à laisser, utilisé dans la page planification et gestion lever coucher de soleil
        flatpickr(_target.closest('div').querySelector('.in_timepicker'), {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 1,
            allowInput: true,
            clickOpens: false,
            onChange: function(selectedDates, dateStr, instance) {

            },
            onOpen: function(selectedDates, dateStr, instance) {
                if (instance.element.value != '') {
                    instance.hourElement.value = instance.element.value.substring(0, 2)
                    instance.minuteElement.value = instance.element.value.substring(3, 5)
                } else {
                    instance.hourElement.value = '00'
                    instance.minuteElement.value = '00'
                }

            },
            onClose: function(selectedDates, dateStr, instance) {
                if (_target = event.target.closest('.bt_afficher_timepicker_planification')) {
                    time = instance.element.value
                    time_old = instance.element.getAttribute("value")

                    if (time != time_old) {
                        modifyWithoutSave = true;
                        instance.element.setAttribute("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
                        instance.element.setAttribute("value", time)
                        Divjour = _target.closest('.JourSemaine')
                        triage_jour(Divjour.closest('.JourSemaine'))
                        MAJ_Graphique_jour(Divjour.closest('.JourSemaine'));
                    }
                }
                instance.destroy()
            },
            onValueUpdate: function(selectedDates, dateStr, instance) {

            }
        })
        _target.closest("div").querySelector('.in_timepicker')._flatpickr.open()
    }
})
document.getElementById('tab_eqlogic').addEventListener('click', function(event) {
    var _target = null
    if (_target = event.target.closest('.list_Cmd_info_numeric')) {
        var el = _target.closest('div').querySelector('input')
        jeedom.cmd.getSelectModal({ cmd: { type: 'info', subType: "numeric" } }, function(result) {
            el.jeeValue(result.human);
        });
    }
    if (_target = event.target.closest('.list_Cmd_info_binary')) {
        var el = _target.closest('div').querySelector('input')
        jeedom.cmd.getSelectModal({ cmd: { type: 'info', subType: "binary" } }, function(result) {
            el.jeeValue(result.human);
        });
    }
    if (_target = event.target.closest('.list_Cmd_info')) {
        var div_alias = _target.closest('.option').querySelector(".alias")
        var el = _target.closest('div').querySelector('input')
        var show_alias = true
        if (_target.closest('div').querySelector('[data-l2key="Niveau_batterie_id"]') != null) {
            show_alias = false
        }
        jeedom.cmd.getSelectModal({ cmd: { type: 'info', } }, function(result) {
            el.jeeValue(result.human);
            if (show_alias) {
                div_alias.seen()
            }
        });


    }
    if (_target = event.target.closest('.bt_modifier_image')) {
        url = 'index.php?v=d&plugin=planification&modal=selectIcon&object_id=' + document.querySelector('#tab_eqlogic .eqLogicAttr[data-l1key=id]').jeeValue()

        jeeDialog.dialog({
            id: 'mod_selectIcon',
            title: '{{Choisir une illustration}}',
            width: (window.innerWidth - 50) < 1500 ? window.innerWidth - 50 : window.innerHeight - 150,
            height: window.innerHeight - 150,
            buttons: {
                confirm: {
                    label: '{{Appliquer}}',
                    className: 'success',
                    callback: {
                        click: function(event) {
                            if (document.getElementById('mod_selectIcon').querySelector('.iconSelected .iconSel') === null) {
                                jeeDialog.get('#mod_selectIcon').close()
                                return
                            }
                            var icon = document.getElementById('mod_selectIcon').querySelector('.iconSelected .iconSel img').getAttribute('src')
                            if (icon == undefined) {
                                icon = ''
                            }
                            icon = icon.replace(/"/g, "'")
                            if (icon == undefined) {
                                icon = ''
                            }
                            document.querySelector('#tab_eqlogic .eqLogicAttr[data-l1key=configuration][data-l2key="Chemin_image"]').jeeValue(icon)
                            document.querySelector('#img_planificationModel').setAttribute('src', icon)
                            document.querySelector("#tab_eqlogic .bt_image_défaut").seen()
                            modifyWithoutSave = true

                            jeeDialog.get('#mod_selectIcon').close()
                        }
                    }
                },
                cancel: {
                    label: '{{Annuler}}',
                    className: 'warning',
                    callback: {
                        click: function(event) {
                            jeeDialog.get('#mod_selectIcon').close()
                        }
                    }
                }
            },
            onClose: function() {
                jeeDialog.get('#mod_selectIcon').destroy() //No twice footer select/search
            },
            contentUrl: url

        })
    }
    if (_target = event.target.closest('.bt_image_défaut')) {
        modifyWithoutSave = true
        if (document.querySelector('.eqLogicAttr[data-l2key=Type_équipement]').jeeValue() == "PAC") {
            img = 'plugins/planification/core/img/pac.png'
        } else if (document.querySelector('.eqLogicAttr[data-l2key=Type_équipement]').jeeValue() == "Volet") {
            img = "plugins/planification/core/img/volet.png"
        } else if (document.querySelector('.eqLogicAttr[data-l2key=Type_équipement]').jeeValue() == "Chauffage") {
            img = "plugins/planification/core/img/chauffage.png"
        } else if (document.querySelector('.eqLogicAttr[data-l2key=Type_équipement]').jeeValue() == "Poele") {
            img = "plugins/planification/core/img/poele.png"
        } else if (document.querySelector('.eqLogicAttr[data-l2key=Type_équipement]').jeeValue() == "Prise") {
            img = "plugins/planification/core/img/prise.png"
        } else if (document.querySelector('.eqLogicAttr[data-l2key=Type_équipement]').jeeValue() == "Perso") {
            img = "plugins/planification/core/img/perso.png"
        }
        var http = new XMLHttpRequest();
        http.open('HEAD', img, false);
        http.send();
        if (http.status != 200) {
            jeedomUtils.showAlert({
                message: "L'image " + img + " n'existe pas.",
                level: 'danger'
            })

            img = "plugins/planification/plugin_info/planification_icon.png"
        }


        document.querySelector('#img_planificationModel').setAttribute('src', img)
        document.querySelector('input[data-l2key=Chemin_image]').jeeValue(img)
        document.querySelector("#tab_eqlogic .bt_image_défaut").unseen()
    }
    if (_target = event.target.closest('.eqLogicAttr[data-l2key="type_fenêtre"]')) {
        modifyWithoutSave = true
        if(_target.id == 'baie'){
            document.querySelector('#tab_eqlogic .Volet .sens_ouverture').seen()
            document.querySelector('#tab_eqlogic .Volet fieldset legend').innerHTML="Détecteur d'ouverture gauche"
        }else{
            document.querySelector('#tab_eqlogic .Volet .sens_ouverture').unseen()
            document.getElementById('gauche').checked = true
            document.querySelector('#tab_eqlogic .Volet .ouverture_droite').unseen()
            document.querySelector('#tab_eqlogic .Volet .ouverture_gauche').seen()
            document.querySelector('#tab_eqlogic .Volet fieldset legend').innerHTML="Détecteur d'ouverture"
        }
       
    }
    if (_target = event.target.closest('.eqLogicAttr[data-l2key="sens_ouveture_fenêtre"]')) {
        modifyWithoutSave = true
        if(_target.id == 'droite'){
            document.querySelector('#tab_eqlogic .Volet .ouverture_droite').seen()
            document.querySelector('#tab_eqlogic .Volet .ouverture_gauche').unseen()
        }else if(_target.id == 'gauche'){
            document.querySelector('#tab_eqlogic .Volet .ouverture_droite').unseen()
            document.querySelector('#tab_eqlogic .Volet .ouverture_gauche').seen()
        }else if(_target.id == 'gauche-droite'){
            document.querySelector('#tab_eqlogic .Volet .ouverture_droite').seen()
            document.querySelector('#tab_eqlogic .Volet .ouverture_gauche').seen()
        }
       
    }
    //console.log(event.target)
})
document.getElementById('tab_eqlogic').addEventListener('focusout', function(event) {
        var _target = null
        if (_target = event.target.closest('.cmdAction')) {
            var div_alias = _target.closest('.option').querySelector(".alias")
            var type_eq = _target.closest(".option").classList[1]
            if (_target.jeeValue() != "") {
                domUtils.ajax({
                    type: "POST",
                    url: "core/ajax/cmd.ajax.php",
                    data: {
                        action: 'byHumanName',
                        humanName: document.querySelector('#tab_eqlogic .' + type_eq + ' .eqLogicAttr[data-l2key=etat_id]').val()
                    },
                    global: true,
                    async: false,
                    error: function(request, status, error) {

                        return "erreur"
                    },
                    success: function(data) {
                        if (data.state != "ok") {
                            jeedomUtils.showAlert({
                                message: "La commande de l 'état du chauffage est invalide, veuillez insérer une commande valide.",
                                level: 'danger'
                            })

                            document.querySelector('#tab_eqlogic .' + type_eq + ' .eqLogicAttr[data-l2key=etat_id]').jeeValue("")
                            div_alias.unseen()
                        }
                        div_alias.seen()
                    }
                });

            } else {
                div_alias.unseen()
            }
        }
    })
    //commandes
document.getElementById('tab_commandes').addEventListener('click', function(event) {
    var _target = null
    if (_target = event.target.closest('.select-selected')) {
        modifyWithoutSave = false;
        event.stopPropagation();
        closeAllSelect(_target);
        _target.nextSibling.classList.toggle("select-hide");
        _target.classList.toggle("select-arrow-active");
    } else if (_target = event.target.closest('.select-items div')) {
        modifyWithoutSave = true;
        select = _target.parentNode.previousSibling;
        select.innerHTML = _target.innerHTML;
        select.classList.remove(recup_class_couleur(select.classList))
        select.classList.add(recup_class_couleur(_target.classList))
        select.setAttribute("Id", _target.getAttribute("Id"))
        y = _target.parentNode.getElementsByClassName("same-as-selected");
        for (k = 0; k < y.length; k++) {
            y[k].classList.remove("same-as-selected")
        }
        _target.classList.add("same-as-selected")
        select.click();
    } else if (_target = event.target.closest('.listCmdAction')) {
        var el = _target.closest('div div').querySelector('.cmdAttr[data-l2key=commande]');
        jeedom.cmd.getSelectModal({ cmd: { type: 'action' } }, function(result) {
            el.jeeValue(result.human);
            jeedom.cmd.displayActionOption(el.jeeValue(), '', function(html) {
                el.closest('div td').querySelector('.actionOptions').html(html);
            });
        });

    } else if (_target = event.target.closest('.listAction')) {
        var el = _target.closest('div div').querySelector('.cmdAttr[data-l2key=commande]');
        jeedom.getSelectActionModal({}, function(result) {
            el.jeeValue(result.human);

            jeedom.cmd.displayActionOption(el.jeeValue(), '', function(html) {
                el.closest('div td').querySelector('.actionOptions').html(html);
            });
        });
        console.log(document.getElementById('mod_insertActionValue').querySelector('mod_actionValue_sel'))
            //console.log(document.getElementById('mod_insertActionValue').querySelector('.jeeDialogContent').getElementById('mod_actionValue_sel'))
        console.log(document.getElementById('jeeDialogContent'))
    }

});
document.getElementById('tab_commandes').addEventListener('focusout', function(event) {
    var _target = null
    if (_target = event.target.closest('.cmdAction')) {
        jeedom.cmd.displayActionOption(_target.jeeValue(), _target.jeeValue().options, function(html) {
            _target.closest('div td').querySelector('.actionOptions').html(html);
        });
    }
})




//jeedomUtils.datePickerInit('Y-m-d H:i:00', '#myCustomDatetime') //Will init myCustomDatetime input with custom format
/*$('#tab_commandes').on('click', '.bt_ajouter_commande', function(e) {
    var SELECT_LIST = Recup_select("commandes");
    var tr = ''
    tr += '<tr class="cmd">'
    tr += '<td>'
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none" >'
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none">'
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none">'
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom}} "</td>'
    tr += '</td>'

    tr += '<td>'
    tr += '<div class="input-group" style=" width:100%;">'
    tr += '<input class="cmdAttr form-control input-sm cmdAction" data-l1key="configuration" data-l2key="commande"/>'
    tr += '<span class="input-group-btn">';
    tr += '<a class="btn btn-success btn-sm listAction"><i class="fa fa-list-alt"></i></a>'
    tr += '<a class="btn btn-success btn-sm listCmdAction"><i class="fa fa-tasks"></i></a>'
    tr += '</span>'
    tr += '</div>'
    tr += '<div class="actionOptions">'
    tr += '</div>'
    tr += '</td>'
    tr += '<td>'
    tr += '<div class="custom-select">'
    tr += SELECT_LIST
    tr += '</div>'
    tr += '</td>'
    tr += '<td>'
    tr += '</td>'
    tr += '</tr>'

    $('#table_actions tbody').append(tr)
    $('#table_actions tbody tr:last .cmdAttr[data-l1key=type]').value(init("action"))
    jeedom.cmd.changeType($('#table_actions tbody tr:last'), init("other"))
    couleur = "orange"
    $('#table_actions tbody tr:last').find(".select-selected")[0].classList.replace("#COULEUR#", "couleur-" + couleur)
    $('#table_actions tbody tr:last .select-items ').find("." + "couleur-" + couleur)[0].classList.add("same-as-selected")
    $('#table_actions tbody tr:last').find(".select-selected")[0].innerHTML = couleur


});*/
/*
$("#tab_commandes #table_actions").sortable({ axis: "y", cursor: "move", items: ".cmd", distance: 30, placeholder: "highlight", tolerance: "intersect", forcePlaceholderSize: true });
$("#tab_commandes #table_infos").sortable({ axis: "y", cursor: "move", items: ".cmd", distance: 30, placeholder: "highlight", tolerance: "intersect", forcePlaceholderSize: true });
*/


//planifications:
/*document.querySelector("#tab_planifications #ab_planifications").sortable({
    axis: "y",
    cursor: "move",
    items: ".planification",
    handle: ".panel-heading",
    placeholder: "ui-state-highlight",
    tolerance: "intersect",
    forcePlaceholderSize: true
});
*/
/*new Sortable(tab_planifications, {
    animation: 150,
    ghostClass: 'blue-background-class'
});*/
//sortable
document.getElementById('tab_planifications').addEventListener('click', function(event) {
    var _target = null
    if (_target = event.target.closest('.bt_ajouter_planification')) {
        jeeDialog.prompt({
            title: "Veuillez inserer le nouveau nom de la planification à ajouter.",
            buttons: {
                confirm: { label: 'Ajouter', className: 'success' },
                cancel: { label: 'Annuler', className: 'danger' }
            },
            callback: function(resultat) {
                if (resultat !== null && resultat != '') {
                    modifyWithoutSave = true;
                    Ajoutplanification({ nom: resultat })
                }
            }
        })
    }
    if (_target = event.target.closest('.bt_renommer_planification')) {
        var el = _target
        jeeDialog.prompt({
            title: "Veuillez inserer le nouveau nom pour la planification:" + _target.closest('.planification').querySelector("span.nom_planification").html() + ".",
            buttons: {
                confirm: { label: 'Modifier', className: 'success' },
                cancel: { label: 'Annuler', className: 'danger' }
            },
            callback: function(resultat) {
                if (resultat !== null && resultat != '') {
                    modifyWithoutSave = true;
                    el.closest('.planification').querySelector("span.nom_planification").innerHTML = resultat

                }
            }
        })
    }
    if (_target = event.target.closest('.bt_dupliquer_planification')) {
        var planification = _target.closest('.planification').cloneNode(true)
        jeeDialog.prompt({
            title: "Veuillez inserer le nom pour la planification dupliquée.",
            buttons: {
                confirm: { label: 'Dupliquer', className: 'success' },
                cancel: { label: 'Annuler', className: 'danger' }
            },
            callback: function(resultat) {
                if (resultat !== null && resultat != '') {
                    modifyWithoutSave = true;
                    planification.querySelector('.nom_planification').innerHTML = resultat
                    planification.setAttribute('id', jeedomUtils.uniqId())
                    document.querySelector('#div_planifications').append(planification)

                }
            }
        })

    }
    if (_target = event.target.closest('.bt_appliquer_planification')) {
        planification = _target.closest('.planification')
        programName = planification.querySelector('.nom_planification').html()
        jeeDialog.confirm({
            message: "Voulez vous vraiment appliquer la planification " + programName + " maintenant ?",
            buttons: {
                confirm: {
                    label: 'Oui',
                    className: 'success'
                },
                cancel: {
                    label: 'Non',
                    className: 'danger'
                }
            },
            callback: function(result) {
                if (result === true) {
                    jeedom.cmd.execute({ id: set_planification_Id, value: { select: programName, Id_planification: planification.getAttribute("Id") } })
                }
            }
        })
    }
    if (_target = event.target.closest('.bt_supprimer_planification')) {
        Ce_progamme = _target.closest('.planification')
        jeeDialog.confirm({
            message: "Voulez vous vraiment supprimer cette planification ?",
            buttons: {
                confirm: {
                    label: 'Oui',
                    className: 'success'
                },
                cancel: {
                    label: 'Non',
                    className: 'danger'
                }
            },
            callback: function(result) {
                if (result === true) {
                    modifyWithoutSave = true;
                    Ce_progamme.remove()
                }
            }
        })
    }
    if (_target = event.target.closest('.planification_collapsible')) {
        _target.classList.toggle("active");
        var DivPlanification = _target.closest(".planification").querySelector(".planification-body")

        if (DivPlanification.style.overflow == "visible") {
            DivPlanification.style.maxHeight = "0px"
            DivPlanification.style.overflow = "hidden"
        } else {
            DivPlanification.style.overflow = "visible"
            DivPlanification.style.maxHeight = "fit-content"
        }
        var DivProgrammation = _target.closest(".planification").querySelector(".planification-body .div_programDays")
        if (DivProgrammation.style.overflow == "visible") {
            DivProgrammation.style.maxHeight = "0px"
            DivProgrammation.style.overflow = "hidden"
        } else {
            DivProgrammation.style.overflow = "visible"
            DivProgrammation.style.maxHeight = "fit-content"
        }
        var DivgraphJours = _target.closest(".planification").querySelector(".planification-body .graphJours")
        if (DivgraphJours.style.overflow == "visible") {
            DivgraphJours.style.maxHeight = "0px"
            DivgraphJours.style.overflow = "hidden"
        } else {
            DivgraphJours.style.overflow = "visible"
            DivgraphJours.style.maxHeight = "fit-content"
        }
    }
    if (_target = event.target.closest('.select-selected')) {
        modifyWithoutSave = true;
        event.stopPropagation();
        closeAllSelect(this);
        _target.nextSibling.classList.toggle("select-hide");
        _target.classList.toggle("select-arrow-active");
    }
    if (_target = event.target.closest('.select-items div')) {
        modifyWithoutSave = true;
        select = _target.parentNode.previousSibling;
        select.innerHTML = _target.innerHTML;
        select.classList.remove(recup_class_couleur(select.classList))
        select.classList.add(recup_class_couleur(_target.classList))
        select.setAttribute("Id", _target.getAttribute("Id"))
        y = _target.parentNode.getElementsByClassName("same-as-selected");
        for (k = 0; k < y.length; k++) {
            y[k].classList.remove("same-as-selected")
        }
        _target.classList.add("same-as-selected")

        MAJ_Graphique_jour(_target.closest('.JourSemaine'))
        select.click();
    }
    if (_target = event.target.closest('.checkbox_lever_coucher')) {
        var Divjour = _target.closest('.JourSemaine')
        var Periode = _target.closest('.Periode_jour')
        var numero_cette_periode = 0
        var numero_autre_periode = 0
        var valeur_select_lever_coucher = ""
        var autre_valeur_select_lever_coucher = ""

        var nb_checked = 0
        Divjour.querySelectorAll('.checkbox_lever_coucher').forEach(function(lever_coucher) {

            if (lever_coucher.getAttribute("checked") == 'true') {
                nb_checked += 1
            }
        })

        if (nb_checked > 2) {
            _target.setAttribute("checked", false)
            _target.checked = false
            return
        } else {
            _target.setAttribute("checked", _target.checked)
        }
        modifyWithoutSave = true;
        Periode.classList.forEach(function(classe) {
            if (classe.includes("periode")) {
                numero_cette_periode = classe.substr(7, classe.length - 7)
            }
        })

        valeur_select_lever_coucher = Periode.querySelector('.select_lever_coucher').value

        var time = '00:00'
        var tab_gestion = document.querySelector('#tab_gestion')
        if (_target.checked) {
            if (nb_checked == 1) {
                Divjour.querySelectorAll('.checkbox_lever_coucher').forEach(function(_periode) {
                    if (_periode.getAttribute("checked")) {
                        var cette_periode = _periode.closest('.Periode_jour')
                        cette_periode.classList.forEach(function(classe) {
                            if (classe.includes("periode")) {
                                if (classe.substr(7, classe.length - 7) != numero_cette_periode) {
                                    autre_valeur_select_lever_coucher = cette_periode.querySelector('.select_lever_coucher').value
                                    numero_autre_periode = classe.substr(7, classe.length - 7)
                                }
                            }
                        })
                    }
                })
                if ((numero_cette_periode > numero_autre_periode && autre_valeur_select_lever_coucher == "coucher") || (numero_cette_periode < numero_autre_periode && autre_valeur_select_lever_coucher == "lever")) {
                    return
                }

                if (autre_valeur_select_lever_coucher == "coucher") {
                    Periode.querySelector('.select_lever_coucher').setAttribute("selectedIndex", 0)
                    Periode.querySelector('.select_lever_coucher').value = 'lever'
                    if (Divjour.hasClass("Lundi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Lundi').innerText
                    } else if (Divjour.hasClass("Mardi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Mardi').innerText
                    } else if (Divjour.hasClass("Mercredi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Mercredi').innerText
                    } else if (Divjour.hasClass("Jeudi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Jeudi').innerText
                    } else if (Divjour.hasClass("Vendredi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Vendredi').innerText
                    } else if (Divjour.hasClass("Samedi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Samedi').innerText
                    } else if (Divjour.hasClass("Dimanche")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Dimanche').innerText
                    }
                } else if (autre_valeur_select_lever_coucher == "lever") {
                    Periode.querySelector('.select_lever_coucher').setAttribute("selectedIndex", 1)
                    Periode.querySelector('.select_lever_coucher').value = 'coucher';
                    if (Divjour.hasClass("Lundi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Lundi').innerText
                    }
                    if (Divjour.hasClass("Mardi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Mardi').innerText
                    }
                    if (Divjour.hasClass("Mercredi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Mercredi').innerText
                    }
                    if (Divjour.hasClass("Jeudi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Jeudi').innerText
                    }
                    if (Divjour.hasClass("Vendredi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Vendredi').innerText
                    }
                    if (Divjour.hasClass("Samedi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Samedi').innerText
                    }
                    if (Divjour.hasClass("Dimanche")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Dimanche').innerText
                    }
                }
                Periode.querySelector('.in_timepicker').setAttribute("oldvalue", Periode.querySelector('.in_timepicker').getAttribute("value"))
                Periode.querySelector('.in_timepicker').setAttribute("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
                Periode.querySelector('.in_timepicker').setAttribute("value", time)
                Periode.querySelector('.in_timepicker').unseen()
                Periode.querySelector('.select_lever_coucher').seen()

            } else {
                Periode.querySelector('.in_timepicker').unseen()
                Periode.querySelector('.select_lever_coucher').seen()

                if (valeur_select_lever_coucher == "lever") {

                    if (Divjour.hasClass("Lundi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Lundi').innerText
                    } else if (Divjour.hasClass("Mardi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Mardi').innerText
                    } else if (Divjour.hasClass("Mercredi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Mercredi').innerText
                    } else if (Divjour.hasClass("Jeudi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Jeudi').innerText
                    } else if (Divjour.hasClass("Vendredi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Vendredi').innerText
                    } else if (Divjour.hasClass("Samedi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Samedi').innerText
                    } else if (Divjour.hasClass("Dimanche")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_lever_Dimanche').innerText
                    }

                } else {
                    if (Divjour.hasClass("Lundi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Lundi').innerText
                    } else if (Divjour.hasClass("Mardi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Mardi').innerText
                    } else if (Divjour.hasClass("Mercredi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Mercredi')[0].innerText
                    } else if (Divjour.hasClass("Jeudi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Jeudi')[0].innerText
                    } else if (Divjour.hasClass("Vendredi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Vendredi')[0].innerText
                    } else if (Divjour.hasClass("Samedi")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Samedi')[0].innerText
                    } else if (Divjour.hasClass("Dimanche")) {
                        time = tab_gestion.querySelector('.Heure_action_suivante_coucher_Dimanche')[0].innerText
                    }
                }
                Periode.querySelector('.in_timepicker').setAttribute("oldvalue", Periode.querySelector('.in_timepicker').getAttribute("value"))
                Periode.querySelector('.in_timepicker').setAttribute("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
                Periode.querySelector('.in_timepicker').setAttribute("value", time)
            }
        } else {
            time = Periode.querySelector('.in_timepicker').getAttribute("oldvalue")
            if (time != null) {
                Periode.querySelector('.in_timepicker').setAttribute("value", time)
                Periode.querySelector('.in_timepicker').setAttribute("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
                Periode.querySelector('.in_timepicker').removeAttribute('oldvalue')
            }
            Periode.querySelector('.select_lever_coucher').unseen()
            Periode.querySelector('.in_timepicker').seen()
        }
        triage_jour(Divjour)
        MAJ_Graphique_jour(Divjour)
    }
    if (_target = event.target.closest('.bt_supprimer_perdiode')) {
        Divjour = _target.closest('.JourSemaine')
        _target.closest('.Periode_jour').remove()
        modifyWithoutSave = true;
        MAJ_Graphique_jour(Divjour)
    }
    if (_target = event.target.closest('.bt_ajout_periode')) {
        modifyWithoutSave = true;
        _target.closest("th").querySelector(".collapsible").classList.add("active")
        _target.closest("th").querySelector(".collapsible").classList.add("cursor")
        _target.closest("th").querySelector(".collapsible").classList.remove("no-arrow")
        Divjour = _target.closest('th').querySelector('.JourSemaine')


        var SELECT_LIST = Recup_select("planifications")
        var CMD_LIST = Recup_liste_commandes_planification()
        Couleur = "erreur"
        Nom = ""
        Couleur = "couleur-" + CMD_LIST[0].couleur
        Nom = CMD_LIST[0].Nom
        Id = CMD_LIST[0].Id
        var element = SELECT_LIST.replace("#COULEUR#", Couleur);
        element = element.replace("#VALUE#", Nom)
        element = element.replace("#ID#", Id)
        Ajout_Periode(element, Divjour)

        MAJ_Graphique_jour(Divjour)
        Divjour.style.maxHeight = "fit-content"
        Divjour.style.overflow = "visible"
        DivprogramDays = _target.closest('.div_programDays')
        DivprogramDays.style.overflow = "visible"
        DivprogramDays.style.maxHeight = "fit-content"
        Divplanification = _target.closest('.planification-body')
        Divplanification.style.overflow = "visible"
        Divplanification.style.maxHeight = "fit-content"
    }
    if (_target = event.target.closest('.bt_copier_jour')) {
        var jour = _target.closest('th').querySelector('.JourSemaine')
        JSONCLIPBOARD = { data: [] }
        jour.querySelectorAll('.Periode_jour').forEach(function(_jour) {
            if (_jour.querySelector('.checkbox_lever_coucher').getAttribute("checked")) {
                type_periode = _jour.querySelector('.select_lever_coucher').jeeValue()
            } else {
                type_periode = "heure_fixe"
            }


            debut_periode = _jour.querySelector('.in_timepicker').jeeValue()
            Id = _jour.querySelector('.select-selected').getAttribute("id")
            Nom = _jour.querySelector('.select-selected span').innerHTML
            Couleur = recup_class_couleur(_jour.querySelector('.select-selected').classList)
            JSONCLIPBOARD.data.push({ type_periode, debut_periode, Id, Nom, Couleur })
        })
    }
    if (_target = event.target.closest('.bt_coller_jour')) {
        if (JSONCLIPBOARD == null) return
        modifyWithoutSave = true;
        Divjour = _target.closest('th').querySelector('.JourSemaine')
        Divjour.querySelectorAll('.Periode_jour').forEach(function(_periode) {
            _periode.remove()
        })
        var SELECT_LIST = Recup_select("planifications")
        JSONCLIPBOARD.data.forEach(function(periode) {

            Type_periode = periode["type_periode"]

            Couleur = periode["Couleur"]
            Nom = periode["Nom"]
            Id = periode["Id"]
            var element = SELECT_LIST.replace("#COULEUR#", Couleur);
            element = element.replace("#VALUE#", Nom)
            element = element.replace("#ID#", Id)
            Ajout_Periode(element, Divjour, periode.debut_periode, null, Type_periode)

        })
        Divjour.style.overflow = "visible"
        Divjour.style.maxHeight = "fit-content"
        _target.closest("th").querySelector(".collapsible").classList.add("active")
        _target.closest("th").querySelector(".collapsible").classList.add("cursor")
        _target.closest("th").querySelector(".collapsible").classList.remove("no-arrow")
        MAJ_Graphique_jour(Divjour)
    }
    if (_target = event.target.closest('.bt_vider_jour')) {
        modifyWithoutSave = true;
        _target.closest("th").querySelector(".collapsible").classList.remove("active")
        _target.closest("th").querySelector(".collapsible").classList.remove("cursor")
        _target.closest("th").querySelector(".collapsible").classList.add("no-arrow")
        Divjour = _target.closest('th').querySelector('.JourSemaine')
        Divjour.style.overflow = "hidden"
        Divjour.style.maxHeight = 0
        Divjour.querySelectorAll('.Periode_jour').forEach(function(_periode) {
            _periode.remove()
        })
        MAJ_Graphique_jour(Divjour)
    }
    if (_target = event.target.closest('.collapsible')) {

        _target.classList.toggle("active");
        var Divjour = _target.closest("th").querySelector(".JourSemaine")
        if (Divjour.style.overflow == "visible") {
            Divjour.style.maxHeight = "0px"
            Divjour.style.overflow = "hidden"
        } else {
            Divjour.style.overflow = "visible"
            Divjour.style.maxHeight = "fit-content"
        }
    }

});
document.getElementById('tab_planifications').addEventListener('focusout', function(event) {
    var _target = null
    if (_target = event.target.closest('.in_timepicker')) {
        time = _target.jeeValue()
        time_old = _target.getAttribute("value")
        if (time != time_old) {
            modifyWithoutSave = true;
            _target.setAttribute("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
            _target.setAttribute("value", time)
            Divjour = _target.closest('.JourSemaine')
            triage_jour(Divjour.closest('.JourSemaine'))
            MAJ_Graphique_jour(Divjour.closest('.JourSemaine'));
        }

    }
});
document.getElementById('tab_planifications').addEventListener('change', function(event) {
        if (_target = event.target.closest('.select_lever_coucher')) {
            modifyWithoutSave = true;
            var Divjour = _target.closest('.JourSemaine')
            var Periode = _target.closest('.Periode_jour')
            var numero_cette_periode = 0
            var autre_valeur_select_lever_coucher = ""
            Periode.classList.forEach(function(classe) {
                if (classe.includes("periode")) {
                    numero_cette_periode = classe.substr(7, classe.length - 7)
                }
            })

            Divjour.querySelectorAll('.checkbox_lever_coucher').forEach(function(checkbox) {
                if (checkbox.getAttribute("checked") == 'true') {
                    var cette_periode = checkbox.closest('.Periode_jour')
                    cette_periode.classList.forEach(function(classe) {
                        if (classe.includes("periode")) {
                            if (classe.substr(7, classe.length - 7) != numero_cette_periode) {
                                autre_valeur_select_lever_coucher = cette_periode.querySelector('.select_lever_coucher').value()
                            }
                        }
                    })
                }
            })

            if (_target.value == "lever" && autre_valeur_select_lever_coucher == "lever") {
                modifyWithoutSave = false;
                Periode.querySelector('.select_lever_coucher').setAttribute("selectedIndex", 1)
                Periode.querySelector('.select_lever_coucher').value = 'coucher';
            }
            if (_target.value == "coucher" && autre_valeur_select_lever_coucher == "coucher") {
                modifyWithoutSave = false;
                Periode.querySelector('.select_lever_coucher').setAttribute("selectedIndex", 0)
                Periode.querySelector('.select_lever_coucher').value = 'lever';
            }
            if (Periode.querySelector('.select_lever_coucher').value == 'lever') {
                Periode.querySelector('.select_lever_coucher').setAttribute("selectedIndex", 0)
                if (Divjour.hasClass("Lundi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Lundi').innerText
                } else if (Divjour.hasClass("Mardi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Mardi').innerText
                } else if (Divjour.hasClass("Mercredi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Mercredi').innerText
                } else if (Divjour.hasClass("Jeudi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Jeudi').innerText
                } else if (Divjour.hasClass("Vendredi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Vendredi').innerText
                } else if (Divjour.hasClass("Samedi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Samedi').innerText
                } else if (Divjour.hasClass("Dimanche")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Dimanche').innerText
                }
            } else {
                Periode.querySelector('.select_lever_coucher').setAttribute("selectedIndex", 1)
                if (Divjour.hasClass("Lundi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Lundi').innerText
                } else if (Divjour.hasClass("Mardi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Mardi').innerText
                } else if (Divjour.hasClass("Mercredi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Mercredi').innerText
                } else if (Divjour.hasClass("Jeudi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Jeudi').innerText
                } else if (Divjour.hasClass("Vendredi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Vendredi').innerText
                } else if (Divjour.hasClass("Samedi")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Samedi').innerText
                } else if (Divjour.hasClass("Dimanche")) {
                    time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Dimanche').innerText
                }
            }
            Periode.querySelector('.in_timepicker').setAttribute("oldvalue", Periode.querySelector('.in_timepicker').getAttribute("value"))
            Periode.querySelector('.in_timepicker').setAttribute("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
            Periode.querySelector('.in_timepicker').setAttribute("value", time)
            triage_jour(Divjour)
            MAJ_Graphique_jour(Divjour)
        }
    })
    //gestion lever coucher de soleil

document.getElementById('tab_gestion').addEventListener('change', function(event) {
    var _target = null
    if (_target = event.target.closest('.selection_jour')) {
        var tab_gestion = document.getElementById('tab_gestion')
        tab_gestion.querySelector('.Lundi').style.display = "none"
        tab_gestion.querySelector('.Mardi').style.display = "none"
        tab_gestion.querySelector('.Mercredi').style.display = "none"
        tab_gestion.querySelector('.Jeudi').style.display = "none"
        tab_gestion.querySelector('.Vendredi').style.display = "none"
        tab_gestion.querySelector('.Samedi').style.display = "none"
        tab_gestion.querySelector('.Dimanche').style.display = "none"
        tab_gestion.querySelector('.bt_copier_lever_coucher').style.display = "inline-block"
        switch (_target.jeeValue()) {
            case 'Lundi':
                tab_gestion.querySelector('.Lundi').style.display = "block"
                break
            case 'Mardi':
                tab_gestion.querySelector('.Mardi').style.display = "block"
                break
            case 'Mercredi':
                tab_gestion.querySelector('.Mercredi').style.display = "block"
                break
            case 'Jeudi':
                tab_gestion.querySelector('.Jeudi').style.display = "block"
                break
            case 'Vendredi':
                tab_gestion.querySelector('.Vendredi').style.display = "block"
                break
            case 'Samedi':
                tab_gestion.querySelector('.Samedi').style.display = "block"
                break
            case 'Dimanche':
                tab_gestion.querySelector('.Dimanche').style.display = "block"
                tab_gestion.querySelector('.bt_copier_lever_coucher').style.display = "none"
                break
        }
    }
});
document.getElementById('tab_gestion').addEventListener('click', function(event) {
    var _target = null
    if (_target = event.target.closest('.bt_copier_lever_coucher')) {
        var tab_gestion = document.getElementById('tab_gestion')
        if (tab_gestion.querySelector('.Lundi').style.display == "block") {
            var Ce_jour = "Lundi"
            tab_gestion.querySelectorAll('.in_timepicker').forEach(function(input) {
                if (!input.classList[1].includes("Lundi")) {
                    if (input.classList[1].includes("Mardi")) jour = 'Mardi'
                    if (input.classList[1].includes("Mercredi")) jour = 'Mercredi'
                    if (input.classList[1].includes("Jeudi")) jour = 'Jeudi'
                    if (input.classList[1].includes("Vendredi")) jour = 'Vendredi'
                    if (input.classList[1].includes("Samedi")) jour = 'Samedi'
                    if (input.classList[1].includes("Dimanche")) jour = 'Dimanche'
                    tab_gestion.querySelector('.HeureLeverMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureLeverMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMax_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMax_' + Ce_jour).jeeValue())
                }
            })
        }
        if (tab_gestion.querySelector('.Mardi').style.display == "block") {
            var Ce_jour = "Mardi"
            tab_gestion.querySelectorAll('.in_timepicker').forEach(function(input) {
                if (!input.classList[1].includes("Lundi") && !input.classList[1].includes("Mardi")) {
                    if (input.classList[1].includes("Mercredi")) jour = 'Mercredi'
                    if (input.classList[1].includes("Jeudi")) jour = 'Jeudi'
                    if (input.classList[1].includes("Vendredi")) jour = 'Vendredi'
                    if (input.classList[1].includes("Samedi")) jour = 'Samedi'
                    if (input.classList[1].includes("Dimanche")) jour = 'Dimanche'
                    tab_gestion.querySelector('.HeureLeverMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureLeverMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMax_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMax_' + Ce_jour).jeeValue())
                }
            })

        }
        if (tab_gestion.querySelector('.Mercredi').style.display == "block") {
            var Ce_jour = "Mercredi"
            tab_gestion.querySelectorAll('.in_timepicker').forEach(function(input) {
                if (!input.classList[1].includes("Lundi") && !input.classList[1].includes("Mardi") && !input.classList[1].includes("Mercredi")) {
                    if (input.classList[1].includes("Jeudi")) jour = 'Jeudi'
                    if (input.classList[1].includes("Vendredi")) jour = 'Vendredi'
                    if (input.classList[1].includes("Samedi")) jour = 'Samedi'
                    if (input.classList[1].includes("Dimanche")) jour = 'Dimanche'
                    tab_gestion.querySelector('.HeureLeverMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureLeverMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMax_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMax_' + Ce_jour).jeeValue())
                }
            })
        }
        if (tab_gestion.querySelector('.Jeudi').style.display == "block") {
            var Ce_jour = "Jeudi"
            tab_gestion.querySelectorAll('.in_timepicker').forEach(function(input) {
                if (!input.classList[1].includes("Lundi") && !input.classList[1].includes("Mardi") && !input.classList[1].includes("Mercredi") && !input.classList[1].includes("Jeudi")) {
                    if (input.classList[1].includes("Vendredi")) jour = 'Vendredi'
                    if (input.classList[1].includes("Samedi")) jour = 'Samedi'
                    if (input.classList[1].includes("Dimanche")) jour = 'Dimanche'
                    tab_gestion.querySelector('.HeureLeverMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureLeverMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMax_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMax_' + Ce_jour).jeeValue())
                }
            })
        }
        if (tab_gestion.querySelector('.Vendredi').style.display == "block") {
            var Ce_jour = "Vendredi"
            tab_gestion.querySelectorAll('.in_timepicker').forEach(function(input) {
                if (!input.classList[1].includes("Lundi") && !input.classList[1].includes("Mardi") && !input.classList[1].includes("Mercredi") && !input.classList[1].includes("Jeudi") && !input.classList[1].includes("Vendredi")) {
                    if (input.classList[1].includes("Samedi")) jour = 'Samedi'
                    if (input.classList[1].includes("Dimanche")) jour = 'Dimanche'
                    tab_gestion.querySelector('.HeureLeverMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureLeverMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMax_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMax_' + Ce_jour).jeeValue())
                }
            })
        }
        if (tab_gestion.querySelector('.Samedi').style.display == "block") {
            var Ce_jour = "Samedi"
            tab_gestion.querySelectorAll('.in_timepicker').forEach(function(input) {
                if (!input.classList[1].includes("Lundi") && !input.classList[1].includes("Mardi") && !input.classList[1].includes("Mercredi") && !input.classList[1].includes("Jeudi") && !input.classList[1].includes("Vendredi") && !input.classList[1].includes("Samedi")) {
                    if (input.classList[1].includes("Dimanche")) jour = 'Dimanche'
                    tab_gestion.querySelector('.HeureLeverMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureLeverMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureLeverMax_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMin_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMin_' + Ce_jour).jeeValue())
                    tab_gestion.querySelector('.HeureCoucherMax_' + jour).jeeValue(tab_gestion.querySelector('.HeureCoucherMax_' + Ce_jour).jeeValue())
                }
            })
        }
    }
})

//fonctions*/
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

function recup_class_couleur(classes) {
    var class_color = "erreur"
    try {
        for (classe in classes) {
            if (classes[classe].includes("couleur")) {
                class_color = classes[classe]
                break
            }
        }
    } catch (err) {}

    return class_color
}

function Ajoutplanification(_planification) {
    var JOURS = ['{{Lundi}}', '{{Mardi}}', '{{Mercredi}}', '{{Jeudi}}', '{{Vendredi}}', '{{Samedi}}', '{{Dimanche}}']
    if (init(_planification.nom) == '') return
    if (init(_planification.Id) == '') { _planification.Id = jeedomUtils.uniqId(); }
    var random = Math.floor((Math.random() * 1000000) + 1)
    var div = '<div class="planification panel panel-default" Id=' + _planification.Id + '>'
    div += '<div class="panel-heading">'
    div += '<h3 class="panel-title" style="padding-bottom: 4px;">'
    div += '<div class="planification_collapsible cursor" style="height:32px;padding-top: 10px;width: calc(100% - 345px)">'
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
        div += '<div class="collapsible no-arrow">'
        div += jour
        div += '</div>'
        div += '<div class="input-group" style="display:inline-flex">'
        div += '<span>'
        div += '<span><i class="fa fa-plus-circle cursor bt_ajout_periode" title="{{Ajouter une période}}"></i> </span>'
        div += '<span><i class="fas fa-sign-out-alt cursor bt_copier_jour" title="{{Copier le jour}}"></i> </span>'
        div += '<span><i class="fas fa-sign-in-alt cursor bt_coller_jour" title="{{Coller le jour}}"></i> </span>'
        div += '<span><i class="fa fa-minus-circle cursor bt_vider_jour" title="{{Vider le jour}}"></i> </span>'
        div += '</span>'
        div += '</div>'
        div += '<br></br>'

        div += '<div class="JourSemaine ' + jour + '" style="width:100%; float:left">'

        div += '</div>'
        div += '</td>'
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
        div += '<div class="graphique_jour_' + jour + '" style="width:calc(100% - 80px); display:inline-block;">'
        div += '</div>'
    })
    div += '</div>'
    div += '</div>'
    div += '</form>'
    div += '</div>'
    div += '</div>'

    document.getElementById('div_planifications').append(domUtils.DOMparseHTML(div))

}

function Ajout_Periode(PROGRAM_MODE_LIST, Div_jour, time = null, Mode_periode = null, Type_periode = false) {
    Periode_jours = Div_jour.querySelectorAll('.Periode_jour')
    prochain_debut = "00:00"
    if (Periode_jours.length > 0) {
        periode_precedente = Periode_jours[Periode_jours.length - 1]
        dernier_debut = periode_precedente.querySelector('.in_timepicker').value

        prochain_debut_int = parseInt(dernier_debut.split(':')[0]) * 60 + parseInt(dernier_debut.split(':')[1]) + 1
        heures = Math.trunc(prochain_debut_int / 60)
        heures_str = "0" + heures
        heures_str = heures_str.substr(heures_str.length - 2)
        minutes_str = "0" + (prochain_debut_int - (heures * 60))
        minutes_str = minutes_str.substr(minutes_str.length - 2)
        prochain_debut = heures_str + ":" + minutes_str

        if (time == null) {
            time_int = parseInt(parseInt(dernier_debut.split(':')[0] * 60) + parseInt(dernier_debut.split(':')[1]))

            if (time_int == 1439) {
                time = ""
            } else if (time_int >= 1425) {
                time = 23 + ':' + 59
            } else if (dernier_debut == "") {
                time = ""
            } else {
                time = prochain_debut
            }

        } else if (Mode_periode == null) {
            last_timeStart = (parseInt(dernier_debut.split(':')[0]) * 60) + parseInt(dernier_debut.split(':')[1])
            heure_debut = (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1])
            if (heure_debut <= last_timeStart) {
                time = prochain_debut
            }
        }
    }
    if (time == "" && Type_periode == "lever") {
        if (Div_jour.hasClass("Lundi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Lundi').innerText
        } else if (Div_jour.hasClass("Mardi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Mardi').innerText
        } else if (Div_jour.hasClass("Mercredi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Mercredi').innerText
        } else if (Div_jour.hasClass("Jeudi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Jeudi').innerText
        } else if (Div_jour.hasClass("Vendredi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Vendredi').innerText
        } else if (Div_jour.hasClass("Samedi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Samedi').innerText
        } else if (Div_jour.hasClass("Dimanche")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_lever_Dimanche').innerText
        }
    } else if (time == "" && Type_periode == "coucher") {

        if (Div_jour.hasClass("Lundi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Lundi').innerText
        } else if (Div_jour.hasClass("Mardi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Mardi').innerText
        } else if (Div_jour.hasClass("Mercredi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Mercredi').innerText
        } else if (Div_jour.hasClass("Jeudi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Jeudi').innerText
        } else if (Div_jour.hasClass("Vendredi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Vendredi').innerText
        } else if (Div_jour.hasClass("Samedi")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Samedi').innerText
        } else if (Div_jour.hasClass("Dimanche")) {
            time = document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Dimanche').innerText
        }
    } else if (time == null) {
        time = '00:00'
    }

    var time_int = (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1])
    div = '<div class="Periode_jour periode' + (Periode_jours.length + 1) + ' input-group" style="width:100% !important; line-height:1.4px !important;display: inline-grid">'
    div += '<div>'
    div += '<input style="width: 28px !important;font-size: 20px!important;vertical-align: middle;padding: 5px;" title="activer/désactiver heure lever/coucher de soleil" class="checkbox_lever_coucher checkbox form-control input-sm cursor" type="checkbox">'
    div += '<select class="select_lever_coucher select form-control input-sm" style="background-color: var(--btn-default-color) !important;width: calc(100% - 65px)!important;;display: none;" title="Type planification">'
    div += '<option value="lever" selected>Lever de soleil</option>'
    div += '<option value="coucher">Coucher de soleil</option>'
    div += '</select>'
    div += '<input class="in_timepicker form-control input-sm "  time_int="' + time_int + '"  value="' + time + '" style="left:-10px;padding:0px!important;text-align:center;width:calc(100% - 80px)!important;display:inline-block;position: relative">'
    div += '<a class="btn btn-default bt_afficher_timepicker_planification btn-sm" style="background-color: var(--form-bg-color) !important;position: absolute;right: 26px;display: inline-block"><i class="icon far fa-clock"></i></a>'
    div += '</input>'
    div += '<a class="btn btn-default bt_supprimer_perdiode btn-sm" style="position: absolute;right: 0px;display: inline-block" title="Supprimer cette période"><i class="fa fa-minus-circle"></i></a>'
    div += '</div>'
    div += '<div class="custom-select">'
    div += PROGRAM_MODE_LIST
    div += '</div>'
    div += '</div>'
    Div_jour.insertAdjacentHTML('beforeend', div)
    nouvelle_periode = Div_jour.querySelectorAll(".Periode_jour")[Div_jour.querySelectorAll(".Periode_jour").length - 1]





    if (Mode_periode != null) {
        for (var i = 0; i < nouvelle_periode.querySelectorAll('.select-items div').length; i++) {
            if (nouvelle_periode.querySelectorAll('.select-items div')[i].id == nouvelle_periode.querySelector('.select-selected').getAttribute("id")) {
                nouvelle_periode.querySelectorAll('.select-items div')[i].classList.add('same-as-selected')
            }
        }
    } else {
        nouvelle_periode.querySelector('.select-items div').classList.add('same-as-selected')
    }
    if (document.querySelector('#tab_gestion .Heure_action_suivante_lever_Lundi').innerText == "") {
        nouvelle_periode.querySelector('.checkbox_lever_coucher').style.display = 'none'
        nouvelle_periode.querySelector('.in_timepicker').style.width = 'calc(100% - 28px)'
    }
    if (Type_periode == "lever") {
        nouvelle_periode.querySelector('.checkbox_lever_coucher').setAttribute('checked', true)
        nouvelle_periode.querySelector('.in_timepicker').unseen()
        nouvelle_periode.querySelector('.select_lever_coucher').setAttribute("selectedIndex", 0)
        nouvelle_periode.querySelector('.select_lever_coucher').seen()
    } else if (Type_periode == "coucher") {
        nouvelle_periode.querySelector('.checkbox_lever_coucher').setAttribute('checked', true)
        nouvelle_periode.querySelector('.in_timepicker').unseen()
        nouvelle_periode.querySelector('.select_lever_coucher').setAttribute("selectedIndex", 1)
        nouvelle_periode.querySelector('.select_lever_coucher').seen()
    }
    Div_jour.closest("th").querySelector(".collapsible").classList.remove("no-arrow")
    Div_jour.closest("th").querySelector(".collapsible").classList.add("cursor")
}

function triage_jour(Div_jour) {
    var div = ""
    Array.prototype.map.call(Div_jour.querySelectorAll(".in_timepicker"), function(période) {
        return { val: période.getAttribute("time_int"), el: période.closest(".Periode_jour ") };
    }).sort(function(a, b) {
        return a.val - b.val;
    }).map(function(map) {
        div += map.el.outerHTML
    })
    Div_jour.html(div)
    Div_jour.querySelectorAll('.checkbox_lever_coucher').forEach(function(checkbox) {

        if (checkbox.getAttribute("checked") == 'true') {
            checkbox.checked = true
        } else {
            checkbox.checked = false
        }
    })
    Div_jour.querySelectorAll('.select_lever_coucher').forEach(function(lever_coucher) {
        if (lever_coucher.getAttribute("selectedIndex") == '0') {
            lever_coucher.value = 'lever'
        } else if (lever_coucher.getAttribute("selectedIndex") == '1') {
            lever_coucher.value = 'coucher'
        }
    })
}

function MAJ_Graphique_jour(Div_jour) {

    graphDiv = Div_jour.closest('.planification-body').querySelector('.graphique_jour_' + Div_jour.getAttribute("class").split(' ')[1])

    graphDiv.empty()
    Periode_jour = Div_jour.querySelectorAll('.Periode_jour')
    for (var i = 0; i < Periode_jour.length; i++) {
        var isFirst = (i == 0) ? true : false
        var isLast = (i == Periode_jour.length - 1) ? true : false
        var periode = Periode_jour[i]
        var debut_periode = periode.querySelector('.in_timepicker').getAttribute("value")
        var heure_debut = (parseInt(debut_periode.split(':')[0]) * 60) + parseInt(debut_periode.split(':')[1])
        var delta, class_periode, mode, nouveau_graph, heure_fin, width, fin_periode, fin_periode
        if (isFirst && heure_debut != 0) {
            heure_fin = heure_debut
            delta = heure_fin
            width = (delta * 100) / 1440
            class_periode = ""
            mode = "Aucun"
            nouveau_graph = '<div class="graph ' + class_periode + '" style="width:' + width + '%; height:20px; display:inline-block;">'
            nouveau_graph += '<span class="tooltiptext  ' + class_periode + '">' + debut_periode + " - 23:59<br>" + mode + '</span>'
            nouveau_graph += '</div>'
            graphDiv.append(domUtils.DOMparseHTML(nouveau_graph))
        }
        if (isLast) {
            heure_fin = 1439
            fin_periode = "23:59"
        } else {

            fin_periode = Periode_jour[i + 1].querySelector('.in_timepicker').getAttribute("value")
            heure_fin = (parseInt(fin_periode.split(':')[0]) * 60) + parseInt(fin_periode.split(':')[1])
        }
        delta = heure_fin - heure_debut
        width = (delta * 100) / 1440
        class_periode = recup_class_couleur(periode.querySelector('.select-selected').getAttribute('class').split(' '))
        mode = periode.querySelector('.select-selected').jeeValue()
        nouveau_graph = '<div class="graph ' + class_periode + '" style="width:' + width + '%; height:20px; display:inline-block;">'
        nouveau_graph += '<span class="tooltiptext  ' + class_periode + '">' + debut_periode + " - " + fin_periode + "<br>" + mode + '</span>'
        nouveau_graph += '</div>'
        graphDiv.append(domUtils.DOMparseHTML(nouveau_graph))
    }
}

function Recup_select(type_) {
    var SELECT = ""
    domUtils.ajax({
        type: "POST",
        url: "plugins/planification/core/ajax/planification.ajax.php",
        data: {
            action: "Recup_select",
            eqLogic_id: document.querySelector('.eqLogicAttr[data-l1key=id]').jeeValue(),
            type: type_
        },
        global: true,
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                jeedomUtils.showAlert({ message: data, level: 'danger' });
                SELECT = "";
            }
            SELECT = data.result;
        }
    });
    return SELECT;

}

function Recup_liste_commandes_planification() {
    var COMMANDE_LIST = []
    domUtils.ajax({
        type: "POST",
        url: "plugins/planification/core/ajax/planification.ajax.php",
        data: {
            action: "Recup_liste_commandes_planification",
            eqLogic_id: document.querySelector('.eqLogicAttr[data-l1key=id]').jeeValue(),
        },
        global: true,
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                jeedomUtils.showAlert({ message: data, level: 'danger' });
                COMMANDE_LIST = "";
            }
            COMMANDE_LIST = data.result;

        }
    });
    return COMMANDE_LIST;

}

function printEqLogic(_eqLogic) {
    document.getElementById("div_planifications").empty();
    if (_eqLogic.configuration.etat_id != "" && typeof(_eqLogic.configuration.etat_id) != "undefined") {
        if (document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .alias') != null) {
            document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .alias').seen()
        }
    } else {
        if (document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .alias') != null) {
            document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .alias').unseen()
        }
    }

    if (_eqLogic.configuration.Type_équipement == 'Poele') {
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_id]').jeeValue(_eqLogic.configuration.temperature_id)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').jeeValue(_eqLogic.configuration.etat_allume_id)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_boost_id]').jeeValue(_eqLogic.configuration.etat_boost_id)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_consigne_par_defaut]').jeeValue(_eqLogic.configuration.temperature_consigne_par_defaut);
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').jeeValue(_eqLogic.configuration.Duree_mode_manuel_par_defaut)
    }
    if (_eqLogic.configuration.Type_équipement == 'PAC') {
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_id]').jeeValue(_eqLogic.configuration.temperature_id)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').jeeValue(_eqLogic.configuration.Duree_mode_manuel_par_defaut)

    }
    if (_eqLogic.configuration.Type_équipement == 'Volet') {
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').jeeValue(_eqLogic.configuration.etat_id)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Ouvert]').jeeValue(_eqLogic.configuration.Alias_Ouvert)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Ferme]').jeeValue(_eqLogic.configuration.Alias_Ferme)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_My]').jeeValue(_eqLogic.configuration.Alias_My)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Niveau_batterie_gauche_id]').jeeValue(_eqLogic.configuration.Niveau_batterie_gauche_id)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Niveau_batterie_droite_id]').jeeValue(_eqLogic.configuration.Niveau_batterie_droite_id)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Etat_fenêtre_gauche_id]').jeeValue(_eqLogic.configuration.Etat_fenêtre_gauche_id)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Etat_fenêtre_droite_id]').jeeValue(_eqLogic.configuration.Etat_fenêtre_droite_id)
        
        if (_eqLogic.configuration.Type_fenêtre == 'baie'){
            document.getElementById('baie').checked = true
            document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .sens_ouverture').seen()
            document.querySelector('#tab_eqlogic .Volet fieldset legend').innerHTML="Détecteur d'ouverture gauche"
      
        }else{
            document.getElementById('fenêtre').checked = true
            document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .sens_ouverture').unseen()
            document.querySelector('#tab_eqlogic .Volet fieldset legend').innerHTML="Détecteur d'ouverture"
       
        }
        if (_eqLogic.configuration.Sens_ouverture == 'droite'){
            document.getElementById('droite').checked = true
            document.querySelector('#tab_eqlogic .Volet .ouverture_droite').seen()
            document.querySelector('#tab_eqlogic .Volet .ouverture_gauche').unseen()
        }else if (_eqLogic.configuration.Sens_ouverture == 'gauche-droite'){
            document.getElementById('gauche-droite').checked = true
            document.querySelector('#tab_eqlogic .Volet .ouverture_droite').seen()
            document.querySelector('#tab_eqlogic .Volet .ouverture_gauche').seen()
        }else{
            document.getElementById('gauche').checked = true
            document.querySelector('#tab_eqlogic .Volet .ouverture_droite').unseen()
            document.querySelector('#tab_eqlogic .Volet .ouverture_gauche').seen()
           
        }       
    }
    if (_eqLogic.configuration.Type_équipement == 'Prise') {
        if (_eqLogic.configuration.etat_id != "" && _eqLogic.configuration.etat_id != undefined) {
            document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .alias').seen()

        } else {
            document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .alias').unseen()
        }
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').jeeValue(_eqLogic.configuration.etat_id)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_On]').jeeValue(_eqLogic.configuration.Alias_On)
    }
    if (_eqLogic.configuration.Type_équipement == 'Chauffage') {
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').jeeValue(_eqLogic.configuration.etat_id)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Confort]').jeeValue(_eqLogic.configuration.Alias_Confort)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Eco]').jeeValue(_eqLogic.configuration.Alias_Eco)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Hg]').jeeValue(_eqLogic.configuration.Alias_Hg)
        document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Arret]').jeeValue(_eqLogic.configuration.Alias_Arret)

    }
    if (_eqLogic.configuration.Type_équipement == 'Perso') {
        document.querySelector('.eqLogicAttr[data-l2key=chemin_image]').seen()
        document.querySelector('.bt_modifier_image').seen()
        document.querySelector('.eqLogicAttr[data-l2key=chemin_image]').jeeValue(_eqLogic.configuration.Chemin_image)

    }
    domUtils.ajax({
        type: "POST",
        url: "plugins/planification/core/ajax/planification.ajax.php",
        data: {
            action: "Recup_infos_lever_coucher_soleil",
            id: _eqLogic["id"]
        },
        dataType: 'json',
        global: false,
        async: false,
        error: function(request, status, error) { handleAjaxError(request, status, error) },
        success: function(data) {
            if (data.state != 'ok') {
                jeedomUtils.showAlert({
                    message: data.result,
                    level: 'danger'
                })
                return
            }

            if (data.result == false) {
                jeedomUtils.showAlert({
                    message: "Pour utiliser la fonction lever/coucher de soleil, veuillez enregistrer les coordonnées GPS (latitude et longitude) dans la configuration de jeedom.",
                    level: 'warning'
                })
                return
            }

            document.querySelector('#tab_gestion .HeureLever_Lundi').innerText = data.result["Lever_soleil"]
            document.querySelector('#tab_gestion .HeureLever_Mardi').innerText = data.result["Lever_soleil"]
            document.querySelector('#tab_gestion .HeureLever_Mercredi').innerText = data.result["Lever_soleil"]
            document.querySelector('#tab_gestion .HeureLever_Jeudi').innerText = data.result["Lever_soleil"]
            document.querySelector('#tab_gestion .HeureLever_Vendredi').innerText = data.result["Lever_soleil"]
            document.querySelector('#tab_gestion .HeureLever_Samedi').innerText = data.result["Lever_soleil"]
            document.querySelector('#tab_gestion .HeureLever_Dimanche').innerText = data.result["Lever_soleil"]
            document.querySelector('#tab_gestion .HeureCoucher_Lundi').innerText = data.result["Coucher_soleil"]
            document.querySelector('#tab_gestion .HeureCoucher_Mardi').innerText = data.result["Coucher_soleil"]
            document.querySelector('#tab_gestion .HeureCoucher_Mercredi').innerText = data.result["Coucher_soleil"]
            document.querySelector('#tab_gestion .HeureCoucher_Jeudi').innerText = data.result["Coucher_soleil"]
            document.querySelector('#tab_gestion .HeureCoucher_Vendredi').innerText = data.result["Coucher_soleil"]
            document.querySelector('#tab_gestion .HeureCoucher_Samedi').innerText = data.result["Coucher_soleil"]
            document.querySelector('#tab_gestion .HeureCoucher_Dimanche').innerText = data.result["Coucher_soleil"]
            document.querySelector('#tab_gestion .Heure_action_suivante_lever_Lundi').innerText = data.result["Heure_action_suivante_lever_lundi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_lever_Mardi').innerText = data.result["Heure_action_suivante_lever_mardi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_lever_Mercredi').innerText = data.result["Heure_action_suivante_lever_mercredi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_lever_Jeudi').innerText = data.result["Heure_action_suivante_lever_jeudi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_lever_Vendredi').innerText = data.result["Heure_action_suivante_lever_vendredi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_lever_Samedi').innerText = data.result["Heure_action_suivante_lever_samedi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_lever_Dimanche').innerText = data.result["Heure_action_suivante_lever_dimanche"]
            document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Lundi').innerText = data.result["Heure_action_suivante_coucher_lundi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Mardi').innerText = data.result["Heure_action_suivante_coucher_mardi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Mercredi').innerText = data.result["Heure_action_suivante_coucher_mercredi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Jeudi').innerText = data.result["Heure_action_suivante_coucher_jeudi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Vendredi').innerText = data.result["Heure_action_suivante_coucher_vendredi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Samedi').innerText = data.result["Heure_action_suivante_coucher_samedi"]
            document.querySelector('#tab_gestion .Heure_action_suivante_coucher_Dimanche').innerText = data.result["Heure_action_suivante_coucher_dimanche"]
        }

    })
    nom_planification_erreur = []



    var SELECT_LIST = Recup_select("planifications")
    var CMD_LIST = Recup_liste_commandes_planification()

    domUtils.ajax({
        type: "POST",
        url: "plugins/planification/core/ajax/planification.ajax.php",
        data: {
            action: "Recup_planification",
            eqLogic_id: _eqLogic["id"]
        },
        //dataType: 'json',
        global: false,
        async: false,
        error: function(request, status, error) { handleAjaxError(request, status, error) },
        success: function(data) {
            if (data.state != 'ok') {
                jeedomUtils.showAlert({
                    message: data.result,
                    level: 'danger'
                })
                return
            }
            if (data.result == false) {
                return
            }
            var array = JSON.parse("[" + data.result + "]");
            if (array[0].length == 0) { return; }
            var numéro_planification = 0
                //console.log(data.result)

            array[0].forEach(function(planifications) {
                while (isset(planifications[numéro_planification])) {
                    var nom_planification = ""
                    var id_planification = ""
                    var périodes = []

                    planifications[numéro_planification].forEach(function(planification) {
                        if (isset(planification.Nom)) { nom_planification = planification.Nom }
                        if (isset(planification.Id)) { id_planification = planification.Id }
                        if (isset(planification.Lundi)) { périodes['Lundi'] = planification.Lundi }
                        if (isset(planification.Mardi)) { périodes['Mardi'] = planification.Mardi }
                        if (isset(planification.Mercredi)) { périodes['Mercredi'] = planification.Mercredi }
                        if (isset(planification.Jeudi)) { périodes['Jeudi'] = planification.Jeudi }
                        if (isset(planification.Vendredi)) { périodes['Vendredi'] = planification.Vendredi }
                        if (isset(planification.Samedi)) { périodes['Samedi'] = planification.Samedi }
                        if (isset(planification.Dimanche)) { périodes['Dimanche'] = planification.Dimanche }

                    })

                    Ajoutplanification({ nom: nom_planification, Id: id_planification, nouveau: false })

                    document.querySelectorAll('#div_planifications .planification')[numéro_planification].querySelectorAll('.JourSemaine').forEach(function(div_jour) {
                        périodes[div_jour.classList[1]].forEach(function(periode) {
                            if (!isset(periode.Type)) { return }
                            Couleur = "erreur"
                            Nom = ""
                            Id = ""
                            CMD_LIST.forEach(function(cmd) {
                                if (periode.Id == cmd.Id || periode.Id == cmd.Nom) {
                                    Couleur = "couleur-" + cmd.couleur
                                    Nom = cmd.Nom
                                    Id = cmd.Id
                                }
                            });
                            var element = SELECT_LIST.replace("#COULEUR#", Couleur);
                            element = element.replace("#VALUE#", Nom)
                            element = element.replace("#ID#", Id)
                            Ajout_Periode(element, div_jour, periode.Début, periode.Id, periode.Type)
                        })
                        triage_jour(div_jour)
                        MAJ_Graphique_jour(div_jour)
                    })
                    numéro_planification += 1
                }
            })
        }
    })

    var img = "plugins/planification/core/img/autre.png"
    img = _eqLogic.configuration.Chemin_image
    document.querySelector(".bt_image_défaut").unseen();
    document.querySelector(".Poele").unseen();
    document.querySelector(".Volet").unseen();
    document.querySelector(".Chauffage").unseen();
    document.querySelector(".Prise").unseen();
    document.querySelector(".PAC").unseen();
    // document.querySelector(".Perso").unseen();

    if (_eqLogic.configuration.Type_équipement == "PAC") {
        document.querySelector(".PAC").seen()
        if (img == "" || img == undefined) {
            img = 'plugins/planification/core/img/pac.png'
            document.querySelector(".bt_image_défaut").unseen();
        } else if (img != 'plugins/planification/core/img/pac.png') {
            document.querySelector(".bt_image_défaut").seen();
        }

    } else if (_eqLogic.configuration.Type_équipement == "Volet") {
        document.querySelector(".Volet").seen()
        if (img == "" || img == undefined) {
            img = "plugins/planification/core/img/volet.png"
            document.querySelector(".bt_image_défaut").unseen();
        } else if (img != 'plugins/planification/core/img/volet.png') {
            document.querySelector(".bt_image_défaut").seen();
        }
    } else if (_eqLogic.configuration.Type_équipement == "Chauffage") {
        document.querySelector(".Chauffage").seen()
        if (img == "" || img == undefined) {
            img = "plugins/planification/core/img/chauffage.png"
            document.querySelector(".bt_image_défaut").unseen();
        } else if (img != 'plugins/planification/core/img/chauffage.png') {
            document.querySelector(".bt_image_défaut").seen();
        }
    } else if (_eqLogic.configuration.Type_équipement == "Poele") {
        document.querySelector(".Poele").seen()
        if (img == "" || img == undefined) {
            img = "plugins/planification/core/img/poele.png"
            document.querySelector(".bt_image_défaut").unseen();
        } else if (img != 'plugins/planification/core/img/poele.png') {
            document.querySelector(".bt_image_défaut").seen();
        }
    } else if (_eqLogic.configuration.Type_équipement == "Prise") {
        document.querySelector(".Prise").seen()
        if (img == "" || img == undefined) {
            img = "plugins/planification/core/img/prise.png"
            document.querySelector(".bt_image_défaut").unseen();
        } else if (img != 'plugins/planification/core/img/prise.png') {
            document.querySelector(".bt_image_défaut").seen();
        }
    } else if (_eqLogic.configuration.Type_équipement == "Perso") {
        //document.querySelector(".Perso").seen()
        document.querySelector(".bt_ajouter_commande").seen()
        if (img == "" || img == undefined) {
            img = "plugins/planification/core/img/perso.png"
            document.querySelector(".bt_image_défaut").unseen();
        } else if (img != 'plugins/planification/core/img/perso.png') {
            document.querySelector(".bt_image_défaut").seen();
        }

    }
    var http = new XMLHttpRequest();
    http.open('HEAD', img, false);
    http.send();
    if (http.status != 200) {
        jeedomUtils.showAlert({
            message: "L'image " + img + " n'existe pas.",
            level: 'danger'
        })

        img = "plugins/planification/plugin_info/planification_icon.png"
    }


    document.querySelector('#img_planificationModel').setAttribute('src', img)
    document.querySelector('.eqLogicAttr[data-l2key=Chemin_image]').jeeValue(img)
    document.querySelectorAll('.li_eqLogic').forEach(li_eqLogic => {
        li_eqLogic.removeClass('active');
        if (li_eqLogic.getAttribute("data-eqlogic_id") == _eqLogic.id) {
            li_eqLogic.addClass('active');
        }
    });
}

function saveEqLogic(_eqLogic) {
    if (!isset(_eqLogic.configuration)) {
        _eqLogic.configuration = {}
    }
    var planifications = '[{'

    var erreur = false
    var numéro_planification = 0
    document.querySelectorAll('#div_planifications .planification').forEach(Planification => {
        if (numéro_planification != 0) {
            planifications += ','
        }
        planifications += '"' + numéro_planification + '":'
        planifications += '['
        planifications += '{"Nom":"' + Planification.querySelector('.nom_planification').html() + '",'
        planifications += '"Id":"' + Planification.getAttribute("Id") + '",'
        Planification.querySelectorAll('th .JourSemaine').forEach(Jour => {
            if (Jour.getAttribute("class").split(' ')[1] != "Lundi") {
                planifications += ','
            }
            planifications += '"' + Jour.getAttribute("class").split(' ')[1] + '":[{'
            nb_période = 0
            Jour.querySelectorAll('.Periode_jour').forEach(Période => {
                if (nb_période > 0) {
                    planifications += '},{'
                }
                var type_periode = ""
                type_periode = "heure_fixe"
                if (Période.querySelector('.checkbox_lever_coucher').getAttribute("checked") == 'true') {
                    type_periode = Période.querySelector('.select_lever_coucher').value
                    debut_periode = ""
                } else {
                    debut_periode = Période.querySelector('.in_timepicker').value
                }

                Id = Période.querySelector('.select-selected').getAttribute('id')
                if (Période.querySelector('.select-selected').hasClass("erreur")) {
                    erreur = true
                }
                if (typeof(Id) != 'string') {
                    erreur = true
                    Période.querySelector('.select-selected').classList.add("erreur")
                }

                if (type_periode == "heure_fixe" && debut_periode == "") {
                    erreur = true
                    Période.querySelector('.select-selected').classList.add("erreur")
                }
                planifications += '"Type":"' + type_periode + '", "Début":"' + debut_periode + '", "Id":"' + Id + '"'
                nb_période++
            })
            planifications += '}]'

        })
        planifications += '}]'

        numéro_planification++
    })

    planifications += '}]'
    if (planifications == '[{}]') {
        jeedom.cmd.execute({ id: set_planification_Id, value: { select: '', Id_planification: '' } })
    }
    //console.log(planifications)
    //return
    if (erreur) {

        jeedomUtils.showAlert({
            message: "Impossible d'enregistrer la planification. Celle-ci comporte des erreurs.",
            level: 'danger'
        })

        return;
    }
    domUtils.ajax({
        type: "POST",
        url: "plugins/planification/core/ajax/planification.ajax.php",
        data: {
            action: "Enregistrer_planifications",
            id: _eqLogic["id"],
            planifications: JSON.stringify(JSON.parse(planifications), null, " ")
        },
        global: false,
        error: function(request, status, error) { handleAjaxError(request, status, error) },
        success: function(data) {
            if (data.state != 'ok') {
                jeedomUtils.showAlert({
                    message: data.result,
                    level: 'danger'
                })
                return
            }
        }
    })

    _eqLogic.configuration.Chemin_image = document.querySelector('.eqLogicAttr[data-l2key=Chemin_image]').jeeValue();

    if (_eqLogic.configuration.Type_équipement == 'Poele') {
        _eqLogic.configuration.temperature_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_id]').jeeValue();
        _eqLogic.configuration.etat_allume_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').jeeValue();
        _eqLogic.configuration.etat_boost_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_boost_id]').jeeValue();
        _eqLogic.configuration.temperature_consigne_par_defaut = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_consigne_par_defaut]').jeeValue();
        _eqLogic.configuration.Duree_mode_manuel_par_defaut = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').jeeValue();
    }
    if (_eqLogic.configuration.Type_équipement == 'PAC') {
        _eqLogic.configuration.temperature_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_id]').jeeValue();
        _eqLogic.configuration.Duree_mode_manuel_par_defaut = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').jeeValue();

    }
    if (_eqLogic.configuration.Type_équipement == 'Volet') {
        _eqLogic.configuration.etat_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').jeeValue();
        _eqLogic.configuration.Alias_Ouvert = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Ouvert]').jeeValue();
        _eqLogic.configuration.Alias_Ferme = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Ferme]').jeeValue();
        _eqLogic.configuration.Alias_My = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_My]').jeeValue();
        var type_fenêtre="fenêtre"
        document.querySelectorAll('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=type_fenêtre]').forEach(_el => {
           if (_el.jeeValue() == 1){
                type_fenêtre=_el.id
            }
        })
        _eqLogic.configuration.Type_fenêtre= type_fenêtre;
        var sens_ouverture="gauche"
        document.querySelectorAll('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=sens_ouveture_fenêtre]').forEach(_el => {
           if (_el.jeeValue() == 1){
            sens_ouverture=_el.id
            }
        })
        _eqLogic.configuration.Sens_ouverture= sens_ouverture;

        _eqLogic.configuration.Etat_fenêtre_gauche_id = "";
        _eqLogic.configuration.Niveau_batterie_gauche_id = "";
        _eqLogic.configuration.Etat_fenêtre_droite_id = "";
        _eqLogic.configuration.Niveau_batterie_droite_id = "";
        
        if(sens_ouverture == "droite"){
            _eqLogic.configuration.Etat_fenêtre_droite_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Etat_fenêtre_droite_id]').jeeValue();
            _eqLogic.configuration.Niveau_batterie_droite_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Niveau_batterie_droite_id]').jeeValue();
        }
        if(sens_ouverture == "gauche"){
            _eqLogic.configuration.Etat_fenêtre_gauche_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Etat_fenêtre_gauche_id]').jeeValue();
            _eqLogic.configuration.Niveau_batterie_gauche_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Niveau_batterie_gauche_id]').jeeValue();
        }
        if(sens_ouverture == "gauche-droite"){
            _eqLogic.configuration.Etat_fenêtre_gauche_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Etat_fenêtre_gauche_id]').jeeValue();
            _eqLogic.configuration.Niveau_batterie_gauche_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Niveau_batterie_gauche_id]').jeeValue();
            _eqLogic.configuration.Etat_fenêtre_droite_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Etat_fenêtre_droite_id]').jeeValue();
            _eqLogic.configuration.Niveau_batterie_droite_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Niveau_batterie_droite_id]').jeeValue();
        }
    }
    if (_eqLogic.configuration.Type_équipement == 'Prise') {
        _eqLogic.configuration.etat_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').jeeValue();
        _eqLogic.configuration.Alias_On = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_On]').jeeValue();
        _eqLogic.configuration.Alias_Off = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Off]').jeeValue();
    }
    if (_eqLogic.configuration.Type_équipement == 'Chauffage') {
        _eqLogic.configuration.etat_id = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').jeeValue();
        _eqLogic.configuration.Alias_Confort = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Confort]').jeeValue();
        _eqLogic.configuration.Alias_Eco = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Eco]').jeeValue();
        _eqLogic.configuration.Alias_Hg = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Hg]').jeeValue();
        _eqLogic.configuration.Alias_Arret = document.querySelector('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Arret]').jeeValue();

    }
    if (_eqLogic.configuration.Type_équipement == 'Autre') {

    }

    _eqLogic.cmd.forEach(function(_cmd) {

        document.querySelectorAll('#table_actions tbody tr').forEach(_el => {
            if (!isset(_cmd.configuration)) {
                _cmd.configuration = {}
            }
            if (_cmd.id == '') {
                _cmd.Type = 'action'
                _cmd.subType = 'other'
                _cmd.configuration.Type = 'Planification_perso'

            }

            if ((_el.querySelector('.cmdAttr').jeeValue() == _cmd.id)) {


                if (isset(_el.querySelector('.expressionAttr'))) {
                    var options = {}
                    _el.querySelectorAll('.expressionAttr').forEach(_el => {
                        var aaa = _el.getAttribute('data-l2key')
                        options.aaa = _el.jeeValue()
                        options[aaa] = options.aaa;
                        delete options.aaa;
                    })
                    _cmd.configuration.options = options
                } else {
                    _cmd.configuration.options = ''
                }
            }


        })
    });
    //return false
    return _eqLogic
}

function addCmdToTable(_cmd) {
    if (_cmd.logicalId == "set_heure_fin" || _cmd.logicalId == "set_consigne_temperature" || _cmd.logicalId == "set_action_en_cours" || _cmd.logicalId == "manuel" || _cmd.logicalId == "refresh" || _cmd.logicalId == "boost_on" || _cmd.logicalId == "boost_off") {
        return
    }
    var type_eqlogic = document.querySelector('#tab_eqlogic .eqLogicAttr[data-l2key=Type_équipement]').jeeValue()

    if (_cmd.logicalId == 'set_planification') {
        //set_planification_Id = _cmd.id
        return
    }
    if (!isset(_cmd)) var _cmd = { configuration: {} }
    if (!isset(_cmd.configuration)) _cmd.configuration = {}




    if (_cmd.type == 'info') {
        var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
        tr += '<td>'
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none" >'
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none">'
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none">'
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" disabled placeholder="{{Nom}} "</td>'
        tr += '</td>'
        if (typeof jeeFrontEnd !== 'undefined' && jeeFrontEnd.jeedomVersion !== 'undefined') {
            tr += '<td>';
            tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>';
            tr += '</td>';
        }
        tr += '<td>'

        tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized"/>{{Historiser}}</label></span> '

        tr += '</td>'

        tr += '<td>'
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> '
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>'
        }
        tr += '</td>'
        tr += '</tr>'

        document.getElementById('table_infos').insertAdjacentHTML('beforeend', tr)
        const _tr = document.getElementById('table_infos').lastChild
        _tr.setJeeValues(_cmd, '.cmdAttr');
        jeedom.cmd.changeType(_tr, init(_cmd.subType));
        _tr.querySelector('.cmdAttr[data-l1key=type],.cmdAttr[data-l1key=subType]').setAttribute("disabled", true);
    } else if (_cmd.type == 'action') {

        var SELECT_LIST = Recup_select("commandes");
        var tr = ''
        tr += '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">'
        tr += '<td>'
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none" >'
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none">'
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none">'
        if (_cmd.configuration.Type == "Planification" || _cmd.logicalId == 'auto') {
            tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" disabled  placeholder="{{Nom}} "</td>'
        } else {
            tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" disabled placeholder="{{Nom}} "</td>'
        }

        tr += '</td>'

        tr += '<td>'
        if (isset(_cmd.configuration.Type)) {

            if (_cmd.configuration.Type == "Planification" || _cmd.configuration.Type == "Planification_perso") {
                tr += '<div class="input-group" style=" width:100%;">'
                tr += '<input class="cmdAttr form-control input-sm cmdAction" data-l1key="configuration" data-l2key="commande"/>'
                tr += '<span class="input-group-btn">';
                tr += '<a class="btn btn-success btn-sm listAction"><i class="fa fa-list-alt"></i></a>'
                tr += '<a class="btn btn-success btn-sm listCmdAction"><i class="fa fa-tasks"></i></a>'
                tr += '</span>'
                tr += '</div>'
                tr += '<div class="actionOptions">'
                tr += '</div>'
                tr += '</td>'
                tr += '<td>'
                tr += '<div class="custom-select">'
                tr += SELECT_LIST
                tr += '</div>'
                tr += '</td>'
            }
        }
        tr += '<td>'
        if (_cmd.logicalId == 'auto' || _cmd.logicalId == 'absent') {
            tr += '</td>'
            tr += '<td>'

        }
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a>'
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>'
            if (_cmd.configuration.Type == "Planification_perso") {
                tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>'
            }
        }
        tr += '</td>'
        tr += '</tr>'


        document.getElementById('table_actions').insertAdjacentHTML('beforeend', tr)
        const _tr = document.getElementById('table_actions').lastChild
        _tr.setJeeValues(_cmd, '.cmdAttr');
        //jeedom.cmd.changeType(_tr, init(_cmd.subType));

        if (isset(_tr.querySelector(".actionOptions"))) {
            jeedom.cmd.displayActionOption(_cmd.configuration.commande, init(_cmd.configuration.options),
                function(html) {
                    _tr.querySelector('.actionOptions').html(html);
                    if (_cmd.configuration.options.hasOwnProperty('theme')) {
                        _tr.querySelector('select[data-l2key=theme]').value = _cmd.configuration.options.theme
                    }
                });


            
        }
        if (isset(_cmd.configuration.Type)) {
            if (_cmd.configuration.Type == "Planification" || _cmd.configuration.Type == "Planification_perso") {
                if (isset(_cmd.configuration.Couleur)) {
                    couleur = _cmd.configuration.Couleur
                    if (_cmd.configuration.Couleur == "<span>#VALUE#<\/span>") {
                        couleur = "orange"
                    }
                } else {
                    couleur = "orange"
                }
                _tr.querySelector(".select-selected").classList.replace("#COULEUR#", "couleur-" + couleur)
                _tr.querySelector(".select-items ." + "couleur-" + couleur).classList.add("same-as-selected")
                _tr.querySelector(".select-selected").innerHTML = couleur
            }
        }
    }
    
}
