JSONCLIPBOARD = null
document.addEventListener("click", closeAllSelect);
if (document.querySelectorAll("div .chauffages .eqLogicDisplayCard").length != 0){
    document.querySelector(".eqLogicThumbnailContainer.chauffages").style.display = 'block'
    document.querySelector(".bs-sidenav.chauffages").style.display = 'block'
}
if (document.querySelectorAll("div .PACs .eqLogicDisplayCard").length != 0){
    document.querySelector(".eqLogicThumbnailContainer.PACs").style.display = 'block'
    document.querySelector(".bs-sidenav.PACs").style.display = 'block'
}
if (document.querySelectorAll("div .poeles .eqLogicDisplayCard").length != 0){
	document.querySelector(".eqLogicThumbnailContainer.poeles").style.display = 'block'
    document.querySelector(".bs-sidenav.poeles").style.display = 'block'
}
if (document.querySelectorAll("div .volets .eqLogicDisplayCard").length != 0){
    document.querySelector(".eqLogicThumbnailContainer.volets").style.display = 'block'
    document.querySelector(".bs-sidenav.volets").style.display = 'block';
}
if (document.querySelectorAll("div .prises .eqLogicDisplayCard").length != 0){
    document.querySelector(".eqLogicThumbnailContainer.prises").style.display = 'block'
    document.querySelector(".bs-sidenav.prises").style.display = 'block'
}
if (document.querySelectorAll("div .persos .eqLogicDisplayCard").length != 0){
    document.querySelector(".eqLogicThumbnailContainer.persos").style.display = 'block'
    document.querySelector(".bs-sidenav.persos").style.display = 'block'  
}
$(".li_eqLogic").on('click', function(event) {
    $.hideAlert()
    if (event.ctrlKey) {
        var type = $('body').attr('data-page')
        var url = 'index.php?v=d&m=' + type + '&p=' + type + '&id=' + $(this).attr('data-eqlogic_id')
        window.open(url).focus()
    } else {
        jeedom.eqLogic.cache.getCmd = Array()
        if ($('.eqLogicThumbnailDisplay').html() != undefined) {
            $('.eqLogicThumbnailDisplay').hide()
        }
        $('.eqLogic').hide()
        if ('function' == typeof(prePrintEqLogic)) {
            prePrintEqLogic($(this).attr('data-eqLogic_id'))
        }
        if (isset($(this).attr('data-eqLogic_type')) && isset($('.' + $(this).attr('data-eqLogic_type')))) {
            $('.' + $(this).attr('data-eqLogic_type')).show()
        } else {
            $('.eqLogic').show()
        }
        if ($('.li_eqLogic').length != 0) {
            $('.li_eqLogic').removeClass('active');
        }
        if ($('.li_eqLogic[data-eqLogic_id=' + $(this).attr('data-eqLogic_id') + ']').html() != undefined) {
            $('.li_eqLogic[data-eqLogic_id=' + $(this).attr('data-eqLogic_id') + ']').addClass('active');
        }
        $(this).addClass('active')
        $('.nav-tabs a:not(.eqLogicAction)').first().click()
        $.showLoading()
        jeedom.eqLogic.print({
            type: isset($(this).attr('data-eqLogic_type')) ? $(this).attr('data-eqLogic_type') : eqType,
            id: $(this).attr('data-eqLogic_id'),
            status: 1,
            getCmdState: 1,
            error: function(error) {
                $.hideLoading()
                $.fn.showAlert({
                    message: error.message,
                    level: 'danger'
                })
            },
            success: function(data) {
                $('body .eqLogicAttr').value('')
                if (isset(data) && isset(data.timeout) && data.timeout == 0) {
                    data.timeout = ''
                }
                $('body').setValues(data, '.eqLogicAttr')
                if (!isset(data.category.opening)) $('input[data-l2key="opening"]').prop('checked', false)

                if ('function' == typeof(printEqLogic)) {
                    printEqLogic(data)
                }
                $('.cmd').remove()
                for (var i in data.cmd) {
                    if (data.cmd[i].type == 'info') {
                        data.cmd[i].state = String(data.cmd[i].state).replace(/<[^>]*>?/gm, '');
                        data.cmd[i]['htmlstate'] = '<span class="cmdTableState"';
                        data.cmd[i]['htmlstate'] += 'data-cmd_id="' + data.cmd[i].id + '"';
                        data.cmd[i]['htmlstate'] += 'title="{{Date de valeur}} : ' + data.cmd[i].valueDate + '<br/>{{Date de collecte}} : ' + data.cmd[i].collectDate;
                        if (data.cmd[i].state.length > 50) {
                            data.cmd[i]['htmlstate'] += '<br/>' + data.cmd[i].state.replaceAll('"', '&quot;');
                        }
                        data.cmd[i]['htmlstate'] += '" >';
                        data.cmd[i]['htmlstate'] += data.cmd[i].state.substring(0, 50) + ' ' + data.cmd[i].unite;
                        data.cmd[i]['htmlstate'] += '<span>';
                    } else {
                        data.cmd[i]['htmlstate'] = '';
                    }
                    if (typeof addCmdToTable == 'function') {
                        addCmdToTable(data.cmd[i])
                    } else {
                        addCmdToTableDefault(data.cmd[i]);
                    }
                }
                $('.cmdTableState').each(function() {
                    jeedom.cmd.addUpdateFunction($(this).attr('data-cmd_id'), function(_options) {
                        _options.value = String(_options.value).replace(/<[^>]*>?/gm, '');
                        let cmd = $('.cmdTableState[data-cmd_id=' + _options.cmd_id + ']')
                        let title = '{{Date de collecte}} : ' + _options.collectDate + ' - {{Date de valeur}} ' + _options.valueDate;
                        if (_options.value.length > 50) {
                            title += ' - ' + _options.value;
                        }
                        cmd.attr('title', title)
                        cmd.empty().append(_options.value.substring(0, 50) + ' ' + _options.unit);
                        cmd.css('color', 'var(--logo-primary-color)');
                        setTimeout(function() {
                            cmd.css('color', '');
                        }, 1000);
                    });
                })
                $('#div_pageContainer').on({
                    'change': function(event) {
                        jeedom.cmd.changeType($(this).closest('.cmd'))
                    }
                }, '.cmd .cmdAttr[data-l1key=type]')

                $('#div_pageContainer').on({
                    'change': function(event) {
                        jeedom.cmd.changeSubType($(this).closest('.cmd'))
                    }
                }, '.cmd .cmdAttr[data-l1key=subType]')

                jeedomUtils.addOrUpdateUrl('id', data.id)
                $.hideLoading()
                modifyWithoutSave = false
                setTimeout(function() {
                    modifyWithoutSave = false
                }, 1000)
            }
        })
    }
    return false
})

$('.ajouter_eqlogic').on('click', function() {
    var dialog_title = '{{Choisissez le type d\'équipement que souhaitez ajouter}}';

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
        '<input class="col-sm-8" type="text" placeholder="Nom de l\'équipement" name="nom" id="nom" >  ' +

        '</div> <br>' +
        '</div>' +
        '</form>';
    bootbox.dialog({
        title: dialog_title,
        message: dialog_message,
        buttons: {
            "{{Annuler}}": {
                className: "btn-danger",
                callback: function() {}
            },
            success: {
                label: "{{Valider}}",
                className: "btn-success",

                callback: function() {
                    if ($("input[name='nom']").val() == "") {
                        $('#div_alert').showAlert({ message: "Le nom de l'équipement ne peut pas être vide.", level: 'danger' });
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: "plugins/planification/core/ajax/planification.ajax.php",
                        data: {
                            action: "Ajout_equipement",
                            nom: $("input[name='nom']").val(),
                            type: $("input[name='Type_équipement']:checked").val()
                        },
                        global: true,
                        async: false,
                        error: function(request, status, error) {
                            handleAjaxError(request, status, error);
                        },
                        success: function(data) {
                            if (data.state != 'ok') {
                                $('#div_alert').showAlert({ message: data.result, level: 'danger' });

                            }
                            window.location.href = 'index.php?v=d&p=planification&m=planification&id=' + data.result;

                        }
                    });
                }
            }
        },
    })
})
$('.sante').on('click', function() {
    $('#md_modal').dialog({ title: "{{Santé Planification}}" });
    $('#md_modal').load('index.php?v=d&plugin=planification&modal=health').dialog('open');
});
$('.dupliquer_equipement').off('click').on('click', function() {
        if ($('.eqLogicAttr[data-l1key=id]').value() != undefined && $('.eqLogicAttr[data-l1key=id]').value() != '') {
            bootbox.prompt({
                size: 'small',
                value: $('.eqLogicAttr[data-l1key=name]').value() + "_copie",
                title: '{{Nom de la copie de l\'équipement ?}}',
                callback: function(result) {
                    if (result !== null) {
                        var id_source = $('.eqLogicAttr[data-l1key=id]').value()
                        jeedom.eqLogic.copy({
                            id: id_source,
                            name: result,
                            error: function(error) {
                                $('#div_alert').showAlert({ message: error.message, level: 'danger' });
                            },
                            success: function(data) {
                                modifyWithoutSave = false
                                var id_cible = data.id
                                $.ajax({
                                    type: "POST",
                                    url: "plugins/planification/core/ajax/planification.ajax.php",
                                    data: {
                                        action: "Copy_JSON",
                                        id_source: id_source,
                                        id_cible: id_cible
                                    },
                                    global: false,
                                    error: function(request, status, error) { handleAjaxError(request, status, error) },
                                    success: function(data) {
                                        if (data.state != 'ok') {
                                            bootbox.hideAll()
                                            $('#div_alert').showAlert({
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
                                loadPage(url)
                                bootbox.hideAll()
                            }
                        })
                        return false
                    }
                }
            })
        }
    })
    //équipement
$('#tab_eqlogic').on('click', '.list_Cmd_info_numeric', function() {
    //var el = $(this).closest('div').find('.eqLogicAttr[data-l2key=temperature_id]');
    var el = $(this).closest('div').find('input')
    jeedom.cmd.getSelectModal({ cmd: { type: 'info', subType: "numeric" } }, function(result) {
        el.value(result.human);
    });
});
$('#tab_eqlogic').on('click', '.list_Cmd_info_binary', function() {
    //var el = $(this).closest('div').find('.eqLogicAttr[data-l2key=etat_allume_id]');
    var el = $(this).closest('div').find('input')
    jeedom.cmd.getSelectModal({ cmd: { type: 'info', subType: "binary" } }, function(result) {
        el.value(result.human);
    });
});

$('#tab_eqlogic').on('click', '.list_Cmd_info', function() {
    var div_alias = $(this).closest('.option').find(".alias")
    var el = $(this).closest('div').find('input')

    jeedom.cmd.getSelectModal({ cmd: { type: 'info', } }, function(result) {
        el.value(result.human);
        div_alias.show()
    });

});
$('#tab_eqlogic').on('focusout', '.cmdAction', function() {
    var div_alias = $(this).closest('.option').find(".alias")
    var type_eq = $(this).closest(".option")[0].classList[1]
    if ($(this).value() != "") {
        $.ajax({
            type: "POST",
            url: "core/ajax/cmd.ajax.php",
            data: {
                action: 'byHumanName',
                humanName: $('#tab_eqlogic .' + type_eq + ' .eqLogicAttr[data-l2key=etat_id]').val()
            },
            global: true,
            async: false,
            error: function(request, status, error) {

                return "erreur"
            },
            success: function(data) {
                if (data.state != "ok") {
                    $('#div_alert').showAlert({
                        message: "La commande de l 'état du chauffage est invalide, veuillez insérer une commande valide.",
                        level: 'danger'
                    })

                    $('#tab_eqlogic .' + type_eq + ' .eqLogicAttr[data-l2key=etat_id]').value("")
                    div_alias.hide()
                }
                div_alias.show()
            }
        });

    } else {
        div_alias.hide()
    }
});
$("#tab_eqlogic .bt_modifier_image").on('click', function() {


    if ($("#mod_selectIcon").length == 0) {
        $('#div_pageContainer').append('<div id="mod_selectIcon"></div>')
        $("#mod_selectIcon").dialog({
            title: '{{Choisissez une icône perso}}',
            closeText: '',
            autoOpen: false,
            modal: true,
            height: (jQuery(window).height() - 150),
            width: 1500,
            open: function() {
                if ((jQuery(window).width() - 50) < 1500) {
                    $('#mod_selectIcon').dialog({ width: jQuery(window).width() - 50 })
                }
                $('body').css({ overflow: 'hidden' });
                setTimeout(function() { initTooltips($("#mod_selectIcon")) }, 500)
            },
            beforeClose: function(event, ui) {
                $('body').css({ overflow: 'inherit' })
            }
        });
    }
    var url = 'index.php?v=d&plugin=planification&modal=selectIcon&show_img=1&show_icon=0&tab_img=1&selectIcon=' + $('#tab_eqlogic .eqLogicAttr[data-l1key=configuration][data-l2key="Chemin_image"]').value()
    console.log(url)


    $('#mod_selectIcon').empty().load(url, function() {
        $("#mod_selectIcon").dialog('option', 'buttons', {
            "Annuler": function() {
                $(this).dialog("close")
            },
            "Valider": function() {
                var icon = $('.iconSelected .iconSel .img-responsive').attr('src')
                if (icon == undefined) {
                    icon = ''
                }
                $('#tab_eqlogic .eqLogicAttr[data-l1key=configuration][data-l2key="chemin_image"]').val(icon)
                $('#img_planificationModel').attr('src', icon)
                $("#tab_eqlogic .bt_image_défaut").show()
                modifyWithoutSave = true
                $(this).dialog("close")
            }
        });
        $('#mod_selectIcon').dialog('open')
    });

});
$("#tab_eqlogic .bt_image_défaut").on('click', function() {
    modifyWithoutSave = true
    if ($('.eqLogicAttr[data-l2key=Type_équipement]').value() == "PAC") {
        img = 'plugins/planification/core/img/pac.png'
    } else if ($('.eqLogicAttr[data-l2key=Type_équipement]').value() == "Volet") {
        img = "plugins/planification/core/img/volet.png"
    } else if ($('.eqLogicAttr[data-l2key=Type_équipement]').value() == "Chauffage") {
        img = "plugins/planification/core/img/chauffage.png"
    } else if ($('.eqLogicAttr[data-l2key=Type_équipement]').value() == "Poele") {
        img = "plugins/planification/core/img/poele.png"
    } else if ($('.eqLogicAttr[data-l2key=Type_équipement]').value() == "Prise") {
        img = "plugins/planification/core/img/prise.png"
    } else if ($('.eqLogicAttr[data-l2key=Type_équipement]').value() == "Perso") {
        img = "plugins/planification/core/img/perso.png"
    }
    var http = new XMLHttpRequest();
    http.open('HEAD', img, false);
    http.send();
    if (http.status != 200) {
        $('#div_alert').showAlert({
            message: "L'image " + img + " n'existe pas.",
            level: 'danger'
        })

        img = "plugins/planification/plugin_info/planification_icon.png"
    }


    $('#img_planificationModel').attr('src', img)
    $('.image_perso .eqLogicAttr[data-l2key=chemin_image]').value(img)
    $("#tab_eqlogic .bt_image_défaut").hide()
});
//commandes
$('#tab_commandes').on('click', '.select-selected', function(e) {
    modifyWithoutSave = false;
    e.stopPropagation();
    closeAllSelect(this);
    this.nextSibling.classList.toggle("select-hide");
    this.classList.toggle("select-arrow-active");
});
$('#tab_commandes').on('click', '.select-items div', function() {
    modifyWithoutSave = true;
    select = this.parentNode.previousSibling;
    select.innerHTML = this.innerHTML;
    select.classList.remove(recup_class_couleur(select.classList))
    select.classList.add(recup_class_couleur(this.classList))
    select.setAttribute("Id", this.getAttribute("Id"))
    y = this.parentNode.getElementsByClassName("same-as-selected");
    for (k = 0; k < y.length; k++) {
        y[k].classList.remove("same-as-selected")
    }
    this.classList.add("same-as-selected")
    select.click();
});
$('#tab_commandes').on('click', '.listCmdAction', function() {
    //$("body").delegate(".listCmdAction", 'click', function() {
    var el = $(this).closest('div div').find('.cmdAttr[data-l2key=commande]');
    jeedom.cmd.getSelectModal({ cmd: { type: 'action' } }, function(result) {
        el.value(result.human);
        jeedom.cmd.displayActionOption(el.value(), '', function(html) {
            el.closest('div td').find('.actionOptions').html(html);
        });
    });
});
$('#tab_commandes').on('click', '.listAction', function() {
    var el = $(this).closest('div div').find('.cmdAttr[data-l2key=commande]');
    jeedom.getSelectActionModal({}, function(result) {
        el.value(result.human);
        jeedom.cmd.displayActionOption(el.value(), '', function(html) {
            el.closest('div td').find('.actionOptions').html(html);
            taAutosize();
        });
    });
});
$('#tab_commandes').on('focusout', '.cmdAction', function() {
    var el = $(this);

    var expression = el.closest('td').getValues('.expressionAttr');
    jeedom.cmd.displayActionOption(el.value(), expression[0].options, function(html) {
        el.closest('div td').find('.actionOptions').html(html);
        taAutosize();
    });
});
$('#tab_commandes').on('click', '.bt_ajouter_commande', function(e) {
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


});
$("#tab_commandes #table_actions").sortable({ axis: "y", cursor: "move", items: ".cmd", distance: 30, placeholder: "highlight", tolerance: "intersect", forcePlaceholderSize: true });
$("#tab_commandes #table_infos").sortable({ axis: "y", cursor: "move", items: ".cmd", distance: 30, placeholder: "highlight", tolerance: "intersect", forcePlaceholderSize: true });



//planifications:
$("#tab_planifications #div_planifications").sortable({
    axis: "y",
    cursor: "move",
    items: ".planification",
    handle: ".panel-heading",
    placeholder: "ui-state-highlight",
    tolerance: "intersect",
    forcePlaceholderSize: true
});
$("#tab_planifications").on('click', '.bt_ajouter_planification', function() {
    bootbox.prompt({
        title: "Veuillez inserer le nouveau nom de la planification à ajouter.",
        buttons: {
            confirm: { label: 'Ajouter', className: 'btn-success' },
            cancel: { label: 'Annuler', className: 'btn-danger' }
        },
        callback: function(resultat) {
            if (resultat !== null && resultat != '') {
                modifyWithoutSave = true;
                Ajoutplanification({ nom: resultat })
            }
        }
    })
});
$("#tab_planifications").on('click', '.bt_supprimer_planification', function() {
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
        callback: function(result) {
            if (result === true) {
                modifyWithoutSave = true;
                Ce_progamme.remove()
            }
        }
    })
});
$('#tab_planifications').on('click', '.bt_dupliquer_planification', function() {
    var planification = $(this).closest('.planification').clone()

    bootbox.prompt({
        title: "Veuillez inserer le nom pour la planification dupliquée.",
        buttons: {
            confirm: { label: 'Dupliquer', className: 'btn-success' },
            cancel: { label: 'Annuler', className: 'btn-danger' }
        },
        callback: function(resultat) {
            if (resultat !== null && resultat != '') {
                modifyWithoutSave = true;
                var random = Math.floor((Math.random() * 1000000) + 1)
                planification.find('a[data-toggle=collapse]').attr('href', '#collapse' + random)
                planification.find('.panel-collapse.collapse').attr('id', 'collapse' + random)
                planification.find('.nom_planification').html(resultat)
                $(planification).attr('id', uniqId())
                $('#div_planifications').append(planification)
                $('.collapse').collapse()
            }
        }
    })
});
$('#tab_planifications').on('click', '.bt_appliquer_planification', function() {
    planification = $(this).closest('.planification')
    programName = planification.find('.nom_planification').html()
    bootbox.confirm({
        message: "Voulez vous vraiment appliquer la planification " + programName + " maintenant ?",
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
        callback: function(result) {
            if (result === true) {
                jeedom.cmd.execute({ id: set_planification_Id, value: { select: programName, Id_planification: planification.attr("Id") } })
            }
        }
    })
});
$('#tab_planifications').on('click', '.bt_renommer_planification', function() {
    var el = $(this)
    bootbox.prompt({
        title: "Veuillez inserer le nouveau nom pour la planification:" + $(this).closest('.planification').find('.nom_planification').html() + ".",
        buttons: {
            confirm: { label: 'Modifier', className: 'btn-success' },
            cancel: { label: 'Annuler', className: 'btn-danger' }
        },
        callback: function(resultat) {
            if (resultat !== null && resultat != '') {
                modifyWithoutSave = true;
                el.closest('.panel.panel-default').find('span.nom_planification').text(resultat)
            }
        }
    })
});
$('#tab_planifications').on('click', '.bt_supprimer_perdiode', function() {
    Divjour = $(this).closest('.JourSemaine')
    $(this).closest('.Periode_jour').remove()
    modifyWithoutSave = true;
    MAJ_Graphique_jour(Divjour)
});
$('#tab_planifications').on('click', '.bt_ajout_periode', function() {
    modifyWithoutSave = true;
    $(this).closest("th").find(".collapsible")[0].classList.add("active")
    $(this).closest("th").find(".collapsible")[0].classList.add("cursor")
    $(this).closest("th").find(".collapsible")[0].classList.remove("no-arrow")
    Divjour = $(this).closest('th').find('.JourSemaine')


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
    Divjour.css("max-height", "fit-content")
    Divjour.css("overflow", "visible")
    DivprogramDays = $(this).closest('.div_programDays')
    DivprogramDays.css("overflow", "visible")
    DivprogramDays.css("max-height", "fit-content")
    Divplanification = $(this).closest('.planification-body')
    Divplanification.css("overflow", "visible")
    Divplanification.css("max-height", "fit-content")
});
$('#tab_planifications').on('click', '.bt_copier_jour', function() {
    var jour = $(this).closest('th').find('.JourSemaine')
    JSONCLIPBOARD = { data: [] }
    jour.find('.Periode_jour').each(function() {
        if ($(this).find('.checkbox_lever_coucher').prop("checked")) {
            type_periode = $(this).find('.select_lever_coucher').val()
        } else {
            type_periode = "heure_fixe"
        }


        debut_periode = $(this).find('.clock-timepicker').val()
        Id = $(this).find('.select-selected').attr("id")
        Nom = $(this).find('.select-selected span')[0].innerHTML
        Couleur = recup_class_couleur($(this).find('.select-selected')[0].classList)
        JSONCLIPBOARD.data.push({ type_periode, debut_periode, Id, Nom, Couleur })
    })
});
$('#tab_planifications').on('click', '.bt_coller_jour', function() {
    if (JSONCLIPBOARD == null) return
    modifyWithoutSave = true;
    Divjour = $(this).closest('th').find('.JourSemaine')
    Divjour.find('.Periode_jour').each(function() {
        $(this).remove()
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
    Divjour.css("overflow", "visible")
    Divjour.css("max-height", "fit-content")
    $(this).closest("th").find(".collapsible")[0].classList.add("active")
    $(this).closest("th").find(".collapsible")[0].classList.add("cursor")
    $(this).closest("th").find(".collapsible")[0].classList.remove("no-arrow")
    MAJ_Graphique_jour(Divjour)
});
$('#tab_planifications').on('click', '.bt_vider_jour', function() {
    modifyWithoutSave = true;
    $(this).closest("th").find(".collapsible")[0].classList.remove("active")
    $(this).closest("th").find(".collapsible")[0].classList.remove("cursor")
    $(this).closest("th").find(".collapsible")[0].classList.add("no-arrow")
    Divjour = $(this).closest('th').find('.JourSemaine')
    Divjour.css("overflow", "hidden")
    Divjour.css("max-height", 0)
    Divjour.find('.Periode_jour').each(function() {
        $(this).remove()
    })
    MAJ_Graphique_jour(Divjour)
});
$('#tab_planifications').on('click', '.collapsible', function() {
    this.classList.toggle("active");
    var Divjour = $(this).closest("th").find(".JourSemaine")

    if (Divjour.css("overflow") == "visible") {
        Divjour.css("max-height", "0px")
        Divjour.css("overflow", "hidden")
    } else {
        Divjour.css("overflow", "visible")
        Divjour.css("max-height", "fit-content")
    }
});
$('#tab_planifications').on('click', '.planification_collapsible', function() {
    this.classList.toggle("active");

    var DivPlanification = $(this).closest(".planification").find(".planification-body")


    if (DivPlanification.css("overflow") == "visible") {
        DivPlanification.css("max-height", "0px")
        DivPlanification.css("overflow", "hidden")
    } else {
        DivPlanification.css("overflow", "visible")
        DivPlanification.css("max-height", "fit-content")
    }
    var DivProgrammation = $(this).closest(".planification").find(".planification-body").find(".div_programDays")
    if (DivProgrammation.css("overflow") == "visible") {
        DivProgrammation.css("max-height", "0px")
        DivProgrammation.css("overflow", "hidden")
    } else {
        DivProgrammation.css("overflow", "visible")
        DivProgrammation.css("max-height", "fit-content")
    }
    var DivgraphJours = $(this).closest(".planification").find(".planification-body").find(".graphJours")
    if (DivgraphJours.css("overflow") == "visible") {
        DivgraphJours.css("max-height", "0px")
        DivgraphJours.css("overflow", "hidden")
    } else {
        DivgraphJours.css("overflow", "visible")
        DivgraphJours.css("max-height", "fit-content")
    }

});
$('#tab_planifications').on('click', '.select-selected', function(e) {
    /* When the select box is clicked, close any other select boxes,
    and open/close the current select box: */
    modifyWithoutSave = true;
    e.stopPropagation();
    closeAllSelect(this);
    this.nextSibling.classList.toggle("select-hide");
    this.classList.toggle("select-arrow-active");
});
$('#tab_planifications').on('click', '.select-items div', function() {
    modifyWithoutSave = true;
    select = this.parentNode.previousSibling;
    select.innerHTML = this.innerHTML;
    select.classList.remove(recup_class_couleur(select.classList))
    select.classList.add(recup_class_couleur(this.classList))
    select.setAttribute("Id", this.getAttribute("Id"))
    y = this.parentNode.getElementsByClassName("same-as-selected");
    for (k = 0; k < y.length; k++) {
        y[k].classList.remove("same-as-selected")
    }
    this.classList.add("same-as-selected")

    MAJ_Graphique_jour($(this).closest('.JourSemaine'))
    select.click();
});
$('#tab_planifications').on('change', '.select_lever_coucher', function() {
    //$("body").delegate( '.select_lever_coucher',"change" ,function () {
    modifyWithoutSave = true;
    var Divjour = $(this).closest('.JourSemaine')
    var Periode = $(this).closest('.Periode_jour')
    var numero_cette_periode = 0
    var autre_valeur_select_lever_coucher = ""
    Periode.prop("classList").forEach(function(classe) {
        if (classe.includes("periode")) {
            numero_cette_periode = classe.substr(7, classe.length - 7)
        }
    })

    Divjour.find('.checkbox_lever_coucher').each(function(checkbox) {
        if ($(this).is(':checked')) {
            var cette_periode = $(this).closest('.Periode_jour')
            cette_periode.prop("classList").forEach(function(classe) {
                if (classe.includes("periode")) {
                    if (classe.substr(7, classe.length - 7) != numero_cette_periode) {
                        autre_valeur_select_lever_coucher = cette_periode.find('.select_lever_coucher').value()
                    }
                }
            })
        }
    })

    if (this.value == "lever" && autre_valeur_select_lever_coucher == "lever") {
        modifyWithoutSave = false;
        Periode.find('.select_lever_coucher').prop('selectedIndex', 1)
    }
    if (this.value == "coucher" && autre_valeur_select_lever_coucher == "coucher") {
        modifyWithoutSave = false;
        Periode.find('.select_lever_coucher').prop('selectedIndex', 0)
    }
    if (Periode.find('.select_lever_coucher').prop('selectedIndex') == 0) {
        if ($(Divjour).hasClass("Lundi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Lundi')[0].innerText
        } else if ($(Divjour).hasClass("Mardi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Mardi')[0].innerText
        } else if ($(Divjour).hasClass("Mercredi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Mercredi')[0].innerText
        } else if ($(Divjour).hasClass("Jeudi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Jeudi')[0].innerText
        } else if ($(Divjour).hasClass("Vendredi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Vendredi')[0].innerText
        } else if ($(Divjour).hasClass("Samedi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Samedi')[0].innerText
        } else if ($(Divjour).hasClass("Dimanche")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Dimanche')[0].innerText
        }
    } else {
        if ($(Divjour).hasClass("Lundi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Lundi')[0].innerText
        } else if ($(Divjour).hasClass("Mardi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mardi')[0].innerText
        } else if ($(Divjour).hasClass("Mercredi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mercredi')[0].innerText
        } else if ($(Divjour).hasClass("Jeudi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Jeudi')[0].innerText
        } else if ($(Divjour).hasClass("Vendredi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Vendredi')[0].innerText
        } else if ($(Divjour).hasClass("Samedi")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Samedi')[0].innerText
        } else if ($(Divjour).hasClass("Dimanche")) {
            time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Dimanche')[0].innerText
        }
    }
    Periode.find('.clock-timepicker').attr("oldvalue", Periode.find('.clock-timepicker').attr("value"))
    Periode.find('.clock-timepicker').attr("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
    Periode.find('.clock-timepicker').attr("value", time)
    triage_jour(Divjour)
    MAJ_Graphique_jour(Divjour)
});
$('#tab_planifications').on('click', '.checkbox_lever_coucher', function() {
    //$("body").delegate( '.checkbox_lever_coucher',"change" ,function () {

    var Divjour = $(this).closest('.JourSemaine ')
    var Periode = $(this).closest('.Periode_jour')
    var numero_cette_periode = 0
    var numero_autre_periode = 0
    var valeur_select_lever_coucher = ""
    var autre_valeur_select_lever_coucher = ""

    var nb_checked = 0
    Divjour.find('.checkbox_lever_coucher').each(function() { if ($(this).prop("checked")) { nb_checked += 1 } })

    if (nb_checked > 2) {
        $(this).prop("checked", false)
        return
    }
    modifyWithoutSave = true;
    Periode.prop("classList").forEach(function(classe) {
        if (classe.includes("periode")) {
            numero_cette_periode = classe.substr(7, classe.length - 7)
        }
    })

    valeur_select_lever_coucher = Periode.find('.select_lever_coucher').value()

    var time = '00:00'
    if ($(this).is(':checked')) {
        if (nb_checked == 2) {
            Divjour.find('.checkbox_lever_coucher').each(function() {
                if ($(this).is(':checked')) {
                    var cette_periode = $(this).closest('.Periode_jour')
                    cette_periode.prop("classList").forEach(function(classe) {
                        if (classe.includes("periode")) {
                            if (classe.substr(7, classe.length - 7) != numero_cette_periode) {
                                autre_valeur_select_lever_coucher = cette_periode.find('.select_lever_coucher').value()
                                numero_autre_periode = classe.substr(7, classe.length - 7)
                            }
                        }
                    })
                }
            })
            if ((numero_cette_periode > numero_autre_periode && autre_valeur_select_lever_coucher == "coucher") || (numero_cette_periode < numero_autre_periode && autre_valeur_select_lever_coucher == "lever")) {
                $(this).prop("checked", false)
                return
            }
            if (autre_valeur_select_lever_coucher == "coucher") {
                Periode.find('.select_lever_coucher').prop("selectedIndex", 0)
                if ($(Divjour).hasClass("Lundi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Lundi')[0].innerText
                } else if ($(Divjour).hasClass("Mardi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Mardi')[0].innerText
                } else if ($(Divjour).hasClass("Mercredi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Mercredi')[0].innerText
                } else if ($(Divjour).hasClass("Jeudi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Jeudi')[0].innerText
                } else if ($(Divjour).hasClass("Vendredi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Vendredi')[0].innerText
                } else if ($(Divjour).hasClass("Samedi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Samedi')[0].innerText
                } else if ($(Divjour).hasClass("Dimanche")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Dimanche')[0].innerText
                }
            } else if (autre_valeur_select_lever_coucher == "lever") {
                Periode.find('.select_lever_coucher').prop("selectedIndex", 1)
                if ($(Divjour).hasClass("Lundi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Lundi')[0].innerText
                } else if ($(Divjour).hasClass("Mardi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mardi')[0].innerText
                } else if ($(Divjour).hasClass("Mercredi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mercredi')[0].innerText
                } else if ($(Divjour).hasClass("Jeudi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Jeudi')[0].innerText
                } else if ($(Divjour).hasClass("Vendredi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Vendredi')[0].innerText
                } else if ($(Divjour).hasClass("Samedi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Samedi')[0].innerText
                } else if ($(Divjour).hasClass("Dimanche")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Dimanche')[0].innerText
                }
            }
            Periode.find('.clock-timepicker').attr("oldvalue", Periode.find('.clock-timepicker').attr("value"))
            Periode.find('.clock-timepicker').attr("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
            Periode.find('.clock-timepicker').attr("value", time)
            Periode.find('.clock-timepicker').hide()
            Periode.find('.select_lever_coucher').show()

        } else {
            Periode.find('.clock-timepicker').hide()
            Periode.find('.select_lever_coucher').show()

            if (valeur_select_lever_coucher == "lever") {

                if ($(Divjour).hasClass("Lundi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Lundi')[0].innerText
                } else if ($(Divjour).hasClass("Mardi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Mardi')[0].innerText
                } else if ($(Divjour).hasClass("Mercredi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Mercredi')[0].innerText
                } else if ($(Divjour).hasClass("Jeudi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Jeudi')[0].innerText
                } else if ($(Divjour).hasClass("Vendredi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Vendredi')[0].innerText
                } else if ($(Divjour).hasClass("Samedi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Samedi')[0].innerText
                } else if ($(Divjour).hasClass("Dimanche")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_lever_Dimanche')[0].innerText
                }

            } else {
                if ($(Divjour).hasClass("Lundi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Lundi')[0].innerText
                } else if ($(Divjour).hasClass("Mardi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mardi')[0].innerText
                } else if ($(Divjour).hasClass("Mercredi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Mercredi')[0].innerText
                } else if ($(Divjour).hasClass("Jeudi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Jeudi')[0].innerText
                } else if ($(Divjour).hasClass("Vendredi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Vendredi')[0].innerText
                } else if ($(Divjour).hasClass("Samedi")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Samedi')[0].innerText
                } else if ($(Divjour).hasClass("Dimanche")) {
                    time = $('#tab_gestion ').find('.Heure_prochaine_action_coucher_Dimanche')[0].innerText
                }
            }
            Periode.find('.clock-timepicker').attr("oldvalue", Periode.find('.clock-timepicker').attr("value"))
            Periode.find('.clock-timepicker').attr("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
            Periode.find('.clock-timepicker').attr("value", time)
        }
    } else {
        time = Periode.find('.clock-timepicker').attr("oldvalue")

        if (typeof(time) != "undefined") {
            Periode.find('.clock-timepicker').attr("value", time)
            Periode.find('.clock-timepicker').attr("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
            Periode.find('.clock-timepicker').removeAttr('oldvalue')
        }
        Periode.find('.select_lever_coucher').hide()
        Periode.find('.clock-timepicker').show()
    }
    triage_jour(Divjour)
    MAJ_Graphique_jour(Divjour)
});
$('#tab_planifications').on('click', '.clock-timepicker', function() {
    Divjour = $(this).closest('.JourSemaine ')
    $(this).datetimepicker({
        step: 5,
        theme: 'dark',
        datepicker: false,
        format: 'H:i',
        onClose: function(dp, $input) {
            $('.clock-timepicker').datetimepicker('destroy')
        },
        onSelectTime: function(dp, $input) {
            modifyWithoutSave = true;
            time = $input.val()
            $($input).attr("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
            $($input).attr("value", time)
            triage_jour($(Divjour))
            MAJ_Graphique_jour($(Divjour).closest('.JourSemaine'));
        }
    });
    $(this).datetimepicker('show');
});
$('#tab_planifications').on("blur", ".clock-timepicker", function() {
    modifyWithoutSave = true;
    time = $(this).val()
    $(this).attr("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]))
    $(this).attr("value", time)
    Divjour = $(this).closest('.JourSemaine ')
    triage_jour($(Divjour))
    MAJ_Graphique_jour($(Divjour).closest('.JourSemaine'));
});
$('#tab_planifications').on("keydown", ".clock-timepicker", function() {
    modifyWithoutSave = true;
    $('.clock-timepicker').datetimepicker('destroy')
});
//gestion lever coucher de soleil
$('#tab_gestion').on("change", ".selection_jour", function() {
    $('#tab_gestion ').find('.Lundi').css("display", "none")
    $('#tab_gestion ').find('.Mardi').css("display", "none")
    $('#tab_gestion ').find('.Mercredi').css("display", "none")
    $('#tab_gestion ').find('.Jeudi').css("display", "none")
    $('#tab_gestion ').find('.Vendredi').css("display", "none")
    $('#tab_gestion ').find('.Samedi').css("display", "none")
    $('#tab_gestion ').find('.Dimanche').css("display", "none")
    $('#tab_gestion ').find('.bt_copier_lever_coucher').css("display", "inline-block")
    switch ($(this).val()) {
        case 'Lundi':
            $('#tab_gestion ').find('.Lundi').css("display", "block")
            break
        case 'Mardi':
            $('#tab_gestion ').find('.Mardi').css("display", "block")
            break
        case 'Mercredi':
            $('#tab_gestion ').find('.Mercredi').css("display", "block")
            break
        case 'Jeudi':
            $('#tab_gestion ').find('.Jeudi').css("display", "block")
            break
        case 'Vendredi':
            $('#tab_gestion ').find('.Vendredi').css("display", "block")
            break
        case 'Samedi':
            $('#tab_gestion ').find('.Samedi').css("display", "block")
            break
        case 'Dimanche':
            $('#tab_gestion ').find('.Dimanche').css("display", "block")
            $('#tab_gestion ').find('.bt_copier_lever_coucher').css("display", "none")
            break
    }


});
$('#tab_gestion').on("click", ".bt_copier_lever_coucher", function() {

        if ($('#tab_gestion .Lundi').style("display") == "block") {
            jour = "Lundi"
            $('#tab_gestion .HeureLeverMin_Mardi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Mardi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Mardi').val($('#tab_gestion .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Mardi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Mercredi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Mercredi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Mercredi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Mercredi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Jeudi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Jeudi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Jeudi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Jeudi').val($('#tab_gestion  .HeureCoucherMax' + jour).val())
            $('#tab_gestion .HeureLeverMin_Vendredi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Vendredi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Vendredi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Vendredi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Samedi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Samedi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Samedi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Samedi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Dimanche').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Dimanche').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Dimanche').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Dimanche').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())

        }
        if ($('#tab_gestion .Mardi').style("display") == "block") {
            jour = "Mardi"
            $('#tab_gestion .HeureLeverMin_Mercredi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Mercredi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Mercredi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Mercredi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Jeudi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Jeudi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Jeudi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Jeudi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Vendredi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Vendredi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Vendredi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Vendredi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Samedi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Samedi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Samedi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Samedi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Dimanche').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Dimanche').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Dimanche').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Dimanche').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())

        }
        if ($('#tab_gestion .Mercredi').style("display") == "block") {
            jour = "Mercredi"
            $('#tab_gestion .HeureLeverMin_Jeudi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Jeudi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Jeudi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Jeudi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Vendredi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Vendredi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Vendredi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Vendredi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Samedi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Samedi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Samedi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Samedi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Dimanche').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Dimanche').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Dimanche').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Dimanche').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
        }
        if ($('#tab_gestion .Jeudi').style("display") == "block") {
            jour = "Jeudi"
            $('#tab_gestion .HeureLeverMin_Vendredi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Vendredi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Vendredi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Vendredi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Samedi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Samedi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Samedi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Samedi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Dimanche').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Dimanche').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Dimanche').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Dimanche').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())

        }
        if ($('#tab_gestion .Vendredi').style("display") == "block") {
            jour = "Vendredi"
            $('#tab_gestion .HeureLeverMin_Samedi').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Samedi').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Samedi').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Samedi').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())
            $('#tab_gestion .HeureLeverMin_Dimanche').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Dimanche').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Dimanche').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Dimanche').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())

        }
        if ($('#tab_gestion .Samedi').style("display") == "block") {
            jour = "Samedi"
            $('#tab_gestion .HeureLeverMin_Dimanche').val($('#tab_gestion  .HeureLeverMin_' + jour).val())
            $('#tab_gestion .HeureLeverMax_Dimanche').val($('#tab_gestion  .HeureLeverMax_' + jour).val())
            $('#tab_gestion .HeureCoucherMin_Dimanche').val($('#tab_gestion  .HeureCoucherMin_' + jour).val())
            $('#tab_gestion .HeureCoucherMax_Dimanche').val($('#tab_gestion  .HeureCoucherMax_' + jour).val())

        }
        console.log(jour)
    })
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
        mode = periode.querySelector('.select-selected').value
        nouveau_graph = '<div class="graph ' + class_periode + '" style="width:' + width + '%; height:20px; display:inline-block;">'
        nouveau_graph += '<span class="tooltiptext  ' + class_periode + '">' + debut_periode + " - " + fin_periode + "<br>" + mode + '</span>'
        nouveau_graph += '</div>'
        graphDiv.append(domUtils.DOMparseHTML(nouveau_graph))
    }
}
function Recup_select(type_) {
    var SELECT = ""
    $.ajax({
        type: "POST",
        url: "plugins/planification/core/ajax/planification.ajax.php",
        data: {
            action: "Recup_select",
            eqLogic_id: document.querySelector('.eqLogicAttr[data-l1key=id]').value,
            type: type_
        },
        global: true,
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $.showAlert({ message: data, level: 'danger' });
                SELECT = "";
            }
            SELECT = data.result;
        }
    });
    return SELECT;

}
function Recup_liste_commandes_planification() {
    var COMMANDE_LIST = []
    $.ajax({
        type: "POST",
        url: "plugins/planification/core/ajax/planification.ajax.php",
        data: {
            action: "Recup_liste_commandes_planification",
            eqLogic_id: document.querySelector('.eqLogicAttr[data-l1key=id]').value,
        },
        global: true,
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $.showAlert({ message: data, level: 'danger' });
                COMMANDE_LIST = "";
            }
            COMMANDE_LIST = data.result;

        }
    });
    return COMMANDE_LIST;

}
function printEqLogic(_eqLogic) {
    $('#div_planifications').empty()
    $('#table_cmd_planification tbody').empty()
    if (_eqLogic.configuration.etat_id != "" && typeof(_eqLogic.configuration.etat_id) != "undefined") {
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .alias').show()
    } else {
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .alias').hide()
    }

    if (_eqLogic.configuration.Type_équipement == 'Poele') {
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_id]').val(_eqLogic.configuration.temperature_id)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').val(_eqLogic.configuration.etat_allume_id)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_boost_id]').val(_eqLogic.configuration.etat_boost_id)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_consigne_par_defaut]').val(_eqLogic.configuration.temperature_consigne_par_defaut);
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').val(_eqLogic.configuration.Duree_mode_manuel_par_defaut)
    }
    if (_eqLogic.configuration.Type_équipement == 'PAC') {
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_id]').val(_eqLogic.configuration.temperature_id)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').val(_eqLogic.configuration.Duree_mode_manuel_par_defaut)

    }
    if (_eqLogic.configuration.Type_équipement == 'Volet') {
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').val(_eqLogic.configuration.etat_id)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Ouvert]').val(_eqLogic.configuration.Alias_Ouvert)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Ferme]').val(_eqLogic.configuration.Alias_Ferme)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_My]').val(_eqLogic.configuration.Alias_My)
    }
    if (_eqLogic.configuration.Type_équipement == 'Prise') {
        if (_eqLogic.configuration.etat_id != "" && _eqLogic.configuration.etat_id != undefined) {
            $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .alias').show()

        } else {
            $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .alias').hide()
        }
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').val(_eqLogic.configuration.etat_id)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_On]').val(_eqLogic.configuration.Alias_On)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Off]').val(_eqLogic.configuration.Alias_Off)

    }
    if (_eqLogic.configuration.Type_équipement == 'Chauffage') {
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').val(_eqLogic.configuration.etat_id)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Confort]').val(_eqLogic.configuration.Alias_Confort)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Eco]').val(_eqLogic.configuration.Alias_Eco)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Hg]').val(_eqLogic.configuration.Alias_Hg)
        $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Arret]').val(_eqLogic.configuration.Alias_Arret)

    }
    if (_eqLogic.configuration.Type_équipement == 'Perso') {
        $('.eqLogicAttr[data-l2key=chemin_image]').show()
        $('.bt_modifier_image').show()
        $('.eqLogicAttr[data-l2key=chemin_image]').val(_eqLogic.configuration.Chemin_image)

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
        async: false,
        error: function(request, status, error) { handleAjaxError(request, status, error) },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({
                    message: data.result,
                    level: 'danger'
                })
                return
            }

            if (data.result == false) {
                $('#div_alert').showAlert({
                    message: "Pour utiliser la fonction lever/coucher de soleil, veuillez enregistrer les coordonnées GPS (latitude et longitude) dans la configuration de jeedom.",
                    level: 'warning'
                })
                return
            }
            $('#tab_gestion ').find('.HeureLever_Lundi')[0].innerText = data.result["Lever_soleil"]
            $('#tab_gestion ').find('.HeureLever_Mardi')[0].innerText = data.result["Lever_soleil"]
            $('#tab_gestion ').find('.HeureLever_Mercredi')[0].innerText = data.result["Lever_soleil"]
            $('#tab_gestion ').find('.HeureLever_Jeudi')[0].innerText = data.result["Lever_soleil"]
            $('#tab_gestion ').find('.HeureLever_Vendredi')[0].innerText = data.result["Lever_soleil"]
            $('#tab_gestion ').find('.HeureLever_Samedi')[0].innerText = data.result["Lever_soleil"]
            $('#tab_gestion ').find('.HeureLever_Dimanche')[0].innerText = data.result["Lever_soleil"]
            $('#tab_gestion ').find('.HeureCoucher_Lundi')[0].innerText = data.result["Coucher_soleil"]
            $('#tab_gestion ').find('.HeureCoucher_Mardi')[0].innerText = data.result["Coucher_soleil"]
            $('#tab_gestion ').find('.HeureCoucher_Mercredi')[0].innerText = data.result["Coucher_soleil"]
            $('#tab_gestion ').find('.HeureCoucher_Jeudi')[0].innerText = data.result["Coucher_soleil"]
            $('#tab_gestion ').find('.HeureCoucher_Vendredi')[0].innerText = data.result["Coucher_soleil"]
            $('#tab_gestion ').find('.HeureCoucher_Samedi')[0].innerText = data.result["Coucher_soleil"]
            $('#tab_gestion ').find('.HeureCoucher_Dimanche')[0].innerText = data.result["Coucher_soleil"]
            $('#tab_gestion ').find('.Heure_action_suivante_lever_Lundi')[0].innerText = data.result["Heure_prochaine_action_lever_lundi"]
            $('#tab_gestion ').find('.Heure_action_suivante_lever_Mardi')[0].innerText = data.result["Heure_prochaine_action_lever_mardi"]
            $('#tab_gestion ').find('.Heure_action_suivante_lever_Mercredi')[0].innerText = data.result["Heure_prochaine_action_lever_mercredi"]
            $('#tab_gestion ').find('.Heure_action_suivante_lever_Jeudi')[0].innerText = data.result["Heure_prochaine_action_lever_jeudi"]
            $('#tab_gestion ').find('.Heure_action_suivante_lever_Vendredi')[0].innerText = data.result["Heure_prochaine_action_lever_vendredi"]
            $('#tab_gestion ').find('.Heure_action_suivante_lever_Samedi')[0].innerText = data.result["Heure_prochaine_action_lever_samedi"]
            $('#tab_gestion ').find('.Heure_action_suivante_lever_Dimanche')[0].innerText = data.result["Heure_prochaine_action_lever_dimanche"]
            $('#tab_gestion ').find('.Heure_action_suivante_coucher_Lundi')[0].innerText = data.result["Heure_prochaine_action_coucher_lundi"]
            $('#tab_gestion ').find('.Heure_action_suivante_coucher_Mardi')[0].innerText = data.result["Heure_prochaine_action_coucher_mardi"]
            $('#tab_gestion ').find('.Heure_action_suivante_coucher_Mercredi')[0].innerText = data.result["Heure_prochaine_action_coucher_mercredi"]
            $('#tab_gestion ').find('.Heure_action_suivante_coucher_Jeudi')[0].innerText = data.result["Heure_prochaine_action_coucher_jeudi"]
            $('#tab_gestion ').find('.Heure_action_suivante_coucher_Vendredi')[0].innerText = data.result["Heure_prochaine_action_coucher_vendredi"]
            $('#tab_gestion ').find('.Heure_action_suivante_coucher_Samedi')[0].innerText = data.result["Heure_prochaine_action_coucher_samedi"]
            $('#tab_gestion ').find('.Heure_action_suivante_coucher_Dimanche')[0].innerText = data.result["Heure_prochaine_action_coucher_dimanche"]
        }

    })
    nom_planification_erreur = []

    var SELECT_LIST = Recup_select("planifications")
    var CMD_LIST = Recup_liste_commandes_planification()
    $.ajax({
        type: "POST",
        url: "plugins/planification/core/ajax/planification.ajax.php",
        data: {
            action: "Recup_planification",
            eqLogic_id: _eqLogic["id"]
        },
        global: false,
        async: false,
        error: function(request, status, error) { handleAjaxError(request, status, error) },
        success: function(data) {
            console.log(data)
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
    $(".bt_image_défaut").hide();
    if (img != undefined) {
        if (img.indexOf("/img/") !== 0) {
            $(".bt_image_défaut").show();
        }
    }

    $(".Poele").hide();
    $(".Volet").hide();
    $(".Chauffage").hide();
    $(".Prise").hide();
    $(".PAC").hide();
    $(".Perso").hide();

    if (_eqLogic.configuration.Type_équipement == "PAC") {
        $(".PAC").show()
        img = _eqLogic.configuration.chemin_image
        if (img == "" || img == undefined) {
            img = 'plugins/planification/core/img/pac.png'
        }

    } else if (_eqLogic.configuration.Type_équipement == "Volet") {
        $(".Volet").show()
        if (img == "" || img == undefined) {
            img = "plugins/planification/core/img/volet.png"
        }
    } else if (_eqLogic.configuration.Type_équipement == "Chauffage") {
        $(".Chauffage").show()
        if (img == "" || img == undefined) {
            img = "plugins/planification/core/img/chauffage.png"
        }
    } else if (_eqLogic.configuration.Type_équipement == "Poele") {
        $(".Poele").show()
        if (img == "" || img == undefined) {
            img = "plugins/planification/core/img/poele.png"
        }
    } else if (_eqLogic.configuration.Type_équipement == "Prise") {
        $(".Prise").show()
        if (img == "" || img == undefined) {
            img = "plugins/planification/core/img/prise.png"
        }
    } else if (_eqLogic.configuration.Type_équipement == "Perso") {
        $(".Perso").show()
        $(".bt_ajouter_commande").show()
        if (img == "" || img == undefined) {
            img = "plugins/planification/core/img/perso.png"
        }

    }
    var http = new XMLHttpRequest();
    http.open('HEAD', img, false);
    http.send();
    if (http.status != 200) {
        $('#div_alert').showAlert({
            message: "L'image " + img + " n'existe pas.",
            level: 'danger'
        })

        img = "plugins/planification/plugin_info/planification_icon.png"
    }


    $('#img_planificationModel').attr('src', img)
    $('.image_perso .eqLogicAttr[data-l2key=chemin_image]').value(img)
}

function saveEqLogic(_eqLogic) {
    if (!isset(_eqLogic.configuration)) {
        _eqLogic.configuration = {}
    }
    var planifications = [];
    var erreur = false

    $('#div_planifications .planification').each(function() {
        var _Cette_planification = {}
        _Cette_planification.nom_planification = $(this).find('.nom_planification').html()
        _Cette_planification.Id = $(this).attr("Id")
        var semaine = []
        $(this).find('th').find(".JourSemaine").each(function() {
            var jour = {}
            jour.jour = $(this).attr("class").split(' ')[1]
            var periodes = []
            $(this).find('.Periode_jour').each(function() {
                var type_periode = ""
                type_periode = "heure_fixe"
                if ($(this).find('.checkbox_lever_coucher').prop("checked")) {
                    type_periode = $(this).find('.select_lever_coucher').value()
                    debut_periode = ""
                } else {
                    debut_periode = $(this).find('.clock-timepicker').val()
                }

                Id = $(this).find('.select-selected')[0].getAttribute('id')
                if (typeof(Id) != 'string') {
                    erreur = true
                    $(this).find('.select-selected')[0].classList.add("erreur")
                }

                if (type_periode == "heure_fixe" && debut_periode == "") {
                    erreur = true
                    $(this).find('.select-selected')[0].classList.add("erreur")
                }
                periodes.push({ 'Type_periode': type_periode, 'Debut_periode': debut_periode, 'Id': Id })
            })
            jour.periodes = periodes
            semaine.push(jour)

        })
        _Cette_planification.semaine = semaine
        planifications.push(_Cette_planification)
    })
    if (erreur) {
        $('#div_alert').showAlert({
            message: "Impossible d'enregistrer la planification. Celle-ci comporte des erreurs.",
            level: 'danger'
        })

        return false;
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
        error: function(request, status, error) { handleAjaxError(request, status, error) },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({
                    message: data.result,
                    level: 'danger'
                })
                return
            }
        }
    })


    _eqLogic.configuration.Chemin_image = $('.eqLogicAttr[data-l2key=chemin_image]').val();
    console.log($('.eqLogicAttr[data-l2key=chemin_image]').val())
    if (_eqLogic.configuration.Type_équipement == 'Poele') {
        _eqLogic.configuration.temperature_id = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_id]').val();
        _eqLogic.configuration.etat_allume_id = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').val();
        _eqLogic.configuration.etat_boost_id = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_boost_id]').val();
        _eqLogic.configuration.temperature_consigne_par_defaut = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_consigne_par_defaut]').val();
        _eqLogic.configuration.Duree_mode_manuel_par_defaut = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').val();
    }
    if (_eqLogic.configuration.Type_équipement == 'PAC') {
        _eqLogic.configuration.temperature_id = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=temperature_id]').val();
        _eqLogic.configuration.Duree_mode_manuel_par_defaut = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').val();

    }
    if (_eqLogic.configuration.Type_équipement == 'Volet') {
        _eqLogic.configuration.etat_id = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').val();
        _eqLogic.configuration.Alias_Ouvert = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Ouvert]').val();
        _eqLogic.configuration.Alias_Ferme = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Ferme]').val();
        _eqLogic.configuration.Alias_My = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_My]').val();
        _eqLogic.configuration.Alias_My = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_My]').val();
        _eqLogic.configuration.Alias_Ferme = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Ferme]').val();

    }
    if (_eqLogic.configuration.Type_équipement == 'Prise') {
        _eqLogic.configuration.etat_id = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').val();
        _eqLogic.configuration.Alias_On = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_On]').val();
        _eqLogic.configuration.Alias_Off = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Off]').val();
    }
    if (_eqLogic.configuration.Type_équipement == 'Chauffage') {
        _eqLogic.configuration.etat_id = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=etat_id]').val();
        _eqLogic.configuration.Alias_Confort = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Confort]').val();
        _eqLogic.configuration.Alias_Eco = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Eco]').val();
        _eqLogic.configuration.Alias_Hg = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Hg]').val();
        _eqLogic.configuration.Alias_Arret = $('#tab_eqlogic .' + _eqLogic.configuration.Type_équipement + ' .eqLogicAttr[data-l2key=Alias_Arret]').val();

    }
    if (_eqLogic.configuration.Type_équipement == 'Autre') {

    }


    _eqLogic.cmd.forEach(function(_cmd) {

        $('#table_actions tbody tr').each(function() {
            if (!isset(_cmd.configuration)) {
                _cmd.configuration = {}
            }
            if (_cmd.id == '') {
                _cmd.Type = 'action'
                _cmd.subType = 'other'
                _cmd.configuration.Type = 'Planification_perso'

            }
            if (($(this).getValues('.cmdAttr')[0].id == _cmd.id)) {

                if (typeof($(this).getValues('.expressionAttr')[0].options) != "undefined") {
                    _cmd.configuration.options = ($(this).getValues('.expressionAttr')[0].options)
                } else {
                    _cmd.configuration.options = ''
                }
            }


        })
    });
    return _eqLogic
}

function addCmdToTable(_cmd) {
    if (_cmd.logicalId == "set_heure_fin" || _cmd.logicalId == "set_consigne_temperature" || _cmd.logicalId == "set_action_en_cours" || _cmd.logicalId == "manuel" || _cmd.logicalId == "refresh" || _cmd.logicalId == "boost_on" || _cmd.logicalId == "boost_off") {
        return
    }
    var type_eqlogic = $('#tab_eqlogic .eqLogicAttr[data-l2key=Type_équipement]').value()
    if (_cmd.logicalId == 'set_planification') {
        set_planification_Id = _cmd.id
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
        $('#table_infos tbody').append(tr)
        const $tr = $('#table_infos tbody tr:last');
        $tr.setValues(_cmd, '.cmdAttr');
        jeedom.cmd.changeType($tr, init(_cmd.subType));
        $tr.find('.cmdAttr[data-l1key=type],.cmdAttr[data-l1key=subType]').prop("disabled", true);
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
            tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom}} "</td>'
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



        $('#table_actions tbody').append(tr)
        $('#table_actions tbody tr:last').setValues(_cmd, '.cmdAttr')
        if (isset(_cmd.type)) $('#table_actions tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type))
        jeedom.cmd.changeType($('#table_actions tbody tr:last'), init(_cmd.subType))
        $('#table_actions tbody tr:last').find(".actionOptions").append(jeedom.cmd.displayActionOption(_cmd.configuration.commande, init(_cmd.configuration.options)))
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
                $('#table_actions tbody tr:last').find(".select-selected")[0].classList.replace("#COULEUR#", "couleur-" + couleur)
                $('#table_actions tbody tr:last .select-items ').find("." + "couleur-" + couleur)[0].classList.add("same-as-selected")
                $('#table_actions tbody tr:last').find(".select-selected")[0].innerHTML = couleur
            }
        }
    }
}