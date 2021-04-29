var timeoutId

function SetWidget_Thermostat(_this, set_consigne_temperature_id, consigne_min, consigne_max, consigne, temperature) {
    var div = ""
    div += "<div class='cercle_ext'>"
    div += "<div class='cercle_int'>"
    div += "<div class='barres' min=" + consigne_min + " max=" + consigne_max + ">"
    div += "<div class='Nom_Temperature_consigne'>Consigne</div>"
    div += "<div class='Temperature_consigne' consigne=" + consigne + " ></div>"
    div += "<div class='Nom_Temperature_actuelle'>Température actuelle</div>"
    div += "<div class='Temperature_actuelle' temp=" + temperature + " ></div>"
    div += "<div class='monter_temperature cursor'></div>"
    div += "<div class='descendre_temperature cursor'></div>"
    div += "</div>"
    div += "</div>"
    div += "</div>"
    $('.eqLogic[data-eqLogic_uid=' + $(_this).attr("data-eqlogic_uid") + '] .Thermostat').append(div)
    var rad2deg = 180 / Math.PI;
    var deg = 0;
    for (var i = -20; i < 81; i++) {
        deg = i * 3;
        mytop = (-Math.sin(deg / rad2deg) * 95 + 100);
        myleft = Math.cos((180 - deg) / rad2deg) * 95 + 100;
        $('<div class="colorBar" style="-webkit-transform: rotate(' + deg + 'deg) scale(1.25, 0.5); -moz-transform: rotate(' + deg + 'deg) scale(1.25, 0.5); -ms-transform: rotate(' + deg + 'deg) scale(1.25, 0.5);transform: rotate(' + deg + 'deg) scale(1.25, 0.5);top: ' + mytop + 'px; left: ' + myleft + 'px" >')
            .appendTo($('.eqLogic[data-eqLogic_uid=' + $(_this).attr("data-eqlogic_uid") + '] .barres'));
    }

    majWidget($('.eqLogic[data-eqLogic_uid=' + $(_this).attr("data-eqlogic_uid") + '] .Thermostat'), false)

    $('.eqLogic[data-eqLogic_uid=' + $(_this).attr("data-eqlogic_uid") + '] .Thermostat').appendTo
    $('.eqLogic[data-eqLogic_uid=' + $(_this).attr("data-eqlogic_uid") + '] .monter_temperature').click(function() {
        monter_temperature($('.eqLogic[data-eqLogic_uid=' + $(_this).attr("data-eqlogic_uid") + '] .cercle_int'), set_consigne_temperature_id)
    });
    //$( '.eqLogic[data-eqLogic_uid='+$(_this).attr("data-eqlogic_uid")+'] .monter_temperature' ).touchstart( function() {
    //	monter_temperature($( '.eqLogic[data-eqLogic_uid='+$(_this).attr("data-eqlogic_uid")+'] .cercle_int' ),set_consigne_temperature_id)
    //});
    $('.eqLogic[data-eqLogic_uid=' + $(_this).attr("data-eqlogic_uid") + '] .descendre_temperature').click(function() {
        descendre_temperature($('.eqLogic[data-eqLogic_uid=' + $(_this).attr("data-eqlogic_uid") + '] .cercle_int'), set_consigne_temperature_id)
    });
    //$( '.eqLogic[data-eqLogic_uid='+$(_this).attr("data-eqlogic_uid")+'] .descendre_temperature' ).touchend( function() {
    //	descendre_temperature($( '.eqLogic[data-eqLogic_uid='+$(_this).attr("data-eqlogic_uid")+'] .cercle_int' ),set_consigne_temperature_id)
    //});
}

function monter_temperature(_this, set_consigne_temperature_id) {
    var temp_consigne = parseInt($(_this).find('.Temperature_consigne').attr("consigne"))
    var temperatureMax = parseInt($(_this).find('.barres').attr("max"))
    var temperatureMin = parseInt($(_this).find('.barres').attr("min"))
    temp_consigne = temp_consigne + 1
    if (Math.round(temp_consigne) > temperatureMax) {
        temp_consigne = temperatureMax
    }
    if (Math.round(temp_consigne) < temperatureMin) {
        temp_consigne = temperatureMin
    }
    $(_this).find('.Temperature_consigne').attr("consigne", temp_consigne)
    majWidget($(_this).parents('.Thermostat'), true)
    clearTimeout(timeoutId);
    timeoutId = setTimeout(function() { set_temp(_this, set_consigne_temperature_id) }, 2000);


}

function descendre_temperature(_this, set_consigne_temperature_id) {
    var temperatureConsigne = parseInt($(_this).find('.Temperature_consigne').attr("consigne"))
    var temperatureMax = parseInt($(_this).find('.barres').attr("max"))
    var temperatureMin = parseInt($(_this).find('.barres').attr("min"))
    temperatureConsigne = temperatureConsigne - 1

    if (Math.round(temperatureConsigne) > temperatureMax) {
        temperatureConsigne = temperatureMax
    }
    if (Math.round(temperatureConsigne) < temperatureMin) {
        temperatureConsigne = temperatureMin
    }
    $(_this).find('.Temperature_consigne').attr("consigne", temperatureConsigne)
    majWidget($(_this).parents('.Thermostat'), true)
    clearTimeout(timeoutId);
    timeoutId = setTimeout(function() { set_temp(_this, set_consigne_temperature_id) }, 2000);


}

function set_temp(_this, set_consigne_temperature_id) {
    majWidget($(_this).parents('.Thermostat'), false)
    var temp = $(_this).parents('.Thermostat').find('.Temperature_consigne').attr("consigne")
    jeedom.cmd.execute({ id: set_consigne_temperature_id, value: { slider: temp } });
}

function majWidget(_this, click) {


    var couleurs = ['243594', '2c358f', '373487', '44337e', '513174', '5c306c', '6b2f62', '792e58', '892d4d', '9e2b3d', 'b4292e', 'c9271f', 'e0250e'];
    var temperatureConsigne = Math.round($(_this).find('.Temperature_consigne').attr('consigne'))
    var temperatureMax = parseInt($(_this).find('.barres').attr("max"))
    var temperatureMin = parseInt($(_this).find('.barres').attr("min"))
    if (Math.round(temperatureConsigne) > temperatureMax) {
        temperatureConsigne = temperatureMax
    }
    if (Math.round(temperatureConsigne) < temperatureMin) {
        temperatureConsigne = temperatureMin
    }
    var temperatureMax = $(_this).find('.barres').attr('max')
    var temperatureMin = $(_this).find('.barres').attr('min')
    var temperatureActuelle = $(_this).find('.Temperature_actuelle').attr('temp')
    $(_this).find('.Temperature_consigne').html(temperatureConsigne + "°C");
    $(_this).find('.Temperature_actuelle').html(temperatureActuelle + "°C");
    ratio = temperatureMax / (temperatureMax - temperatureMin) - 1
    nb = (Math.round(temperatureConsigne) / (temperatureMax - temperatureMin) - ratio) * 100

    if (click) {
        $(_this).find('.colorBar').removeClass('active').slice(0, Math.round(nb)).addClass('active');
    } else {
        $(_this).find('.colorBar').removeClass('active');
    }

    var couleurFond = ""
    if (temperatureConsigne < 16) {
        couleurFond = '#' + couleurs[0];
    }
    if (temperatureConsigne > 28) {
        couleurFond = '#' + couleurs[11];
    }
    if (temperatureConsigne <= 28 && temperatureConsigne >= 16) {
        couleurFond = '#' + couleurs[temperatureConsigne - 16];
    }
    centerCircle = $(_this).find('.cercle_int')
    centerCircle.css("background", couleurFond);

}

function reset_page(id, uid, page, action_en_cours) {
    if (page == 'page1') {
        $('.eqLogic[data-eqLogic_uid=' + uid + '] .page_1').css('display', 'block')
        $('.eqLogic[data-eqLogic_uid=' + uid + '] .page_2').css('display', 'none')
        if (action_en_cours == "Arrêt") {
            $('.eqLogic[data-eqLogic_uid=' + uid + '] .droite').find(".page_1").css('display', 'none')
        } else if (action_en_cours == "Absent") {
            $('.eqLogic[data-eqLogic_uid=' + uid + '] .droite').find(".page_1").css('display', 'none')
        } else if (action_en_cours == "Ventilation") {
            $('.eqLogic[data-eqLogic_uid=' + uid + '] .droite').find(".page_1").css('display', 'none')
        } else {
            $('.eqLogic[data-eqLogic_uid=' + uid + '] .droite').find(".page_1").css('display', 'block')
        }
    }
    if (page == 'page2') {
        $('.eqLogic[data-eqLogic_uid=' + uid + '] .page_2').css('display', 'block')
        $('.eqLogic[data-eqLogic_uid=' + uid + '] .page_1').css('display', 'none')
        setTimeout(function() {
            $.ajax({
                type: "POST",
                url: "plugins/planification/core/ajax/planification.ajax.php",
                data: {
                    action: "Set_widget_cache",
                    id: id,
                    page: "page1",
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
            reset_page(id, uid, "page1", action_en_cours)



        }, 60000);
    }
}

function Commun_widget(id, uid, info_widget, action_en_cours, set_planification_id, endtime_change_id) {
    var taille = parseInt($('.eqLogic[data-eqLogic_uid=' + uid + '] .object_name ').css('height').replace(/px/i, ''))

    $('.eqLogic[data-eqLogic_uid=' + uid + '] .tuile').css("height", "calc(100% - " + taille + "px)")
    if ($('.eqLogic[data-eqLogic_uid=' + uid + '] .object_name').css("display") == "block") {

        if ($('.eqLogic[data-eqLogic_uid=' + uid + '] .widget-name').css("display") == "block") {
            $('.eqLogic[data-eqLogic_uid=' + uid + '] .widget-name').css("display", "none")
            $('.eqLogic[data-eqLogic_uid=' + uid + '] .nom_eqLogic').css("display", "block")
        }

        $('.eqLogic[data-eqLogic_uid=' + uid + '] .tuile').css("background-color", "rgba(0,0,0,0.5)")
        $('.eqLogic[data-eqLogic_uid=' + uid + ']').css('box-shadow', '0px 0px 3px 0.5px rgba(255,255,255,1)')
    }
    $('.eqLogic[data-eqLogic_uid=' + uid + ']  .changecmd').css('display', 'none');
    if ($('.eqLogic[data-eqLogic_uid=' + uid + '] .selectCalendar option').length > 1) {
        $('.eqLogic[data-eqLogic_uid=' + uid + ']  .selectCalendar').css('display', 'inline');
        $('.eqLogic[data-eqLogic_uid=' + uid + ']  .changecmd').css('display', 'block');
    }
    if ($('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').css('display') == 'block') {
        $('.eqLogic[data-eqLogic_uid=' + uid + ']  .changecmd').css('display', 'block');
    }
    if (info_widget != "") {
        $('.eqLogic[data-eqLogic_uid=' + uid + '] .info_widget').append(info_widget)
        $('.eqLogic[data-eqLogic_uid=' + uid + ']  .changecmd').css('display', 'block');
    }

    if ($('.eqLogic[data-eqLogic_uid=' + uid + '] .page_2').css('display') == 'block') {
        reset_page(id, uid, 'page2 ', action_en_cours)
    } else {
        reset_page(id, uid, 'page1', action_en_cours)
    }
    $('.eqLogic[data-eqLogic_uid=' + uid + '] .selectCalendar').on('change', function() {
        jeedom.cmd.execute({
            id: set_planification_id,
            value: {
                'select': $(this).value(),
                Id_planification: $(this)[0].options[$(this)[0].selectedIndex].id
            }
        });
    });
    $('.eqLogic[data-eqLogic_uid=' + uid + '] .changecmd').on('click', function() {
        var page = "page1"
        if ($('.eqLogic[data-eqLogic_uid=' + uid + '] .page_2').css('display') == 'block') {
            page = "page1"

            reset_page(id, uid, page, action_en_cours)
        } else {
            page = "page2"
            reset_page(id, uid, page, action_en_cours)
        }
        $.ajax({
            type: "POST",
            url: "plugins/planification/core/ajax/planification.ajax.php",
            data: {
                action: "Set_widget_cache",
                id: id,
                page: page,
            },
            global: false,
            error: function(request, status, error) {
                handleAjaxError(request, status, error)
            },
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

    });
    $('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').val('#datetimepicker#');
    jQuery.datetimepicker.setLocale('fr');
    $('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').on('click', function() {
        $('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').datetimepicker({
            minDate: '0d',
            step: 15,
            theme: 'dark',
            onClose: function(dp, $input) {
                console.log("close")
                $('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').datetimepicker('destroy')
            },
            onSelectTime: function(dp, $input) {
                jeedom.cmd.execute({
                    id: endtime_change_id,
                    value: {
                        'message': $input.val()
                    }
                })
            }
        });
        $('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').datetimepicker('show');
    });
}