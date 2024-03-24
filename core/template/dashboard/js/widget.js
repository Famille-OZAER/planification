var timeoutId
//flatpickr.localize(flatpickr.l10ns.fr)

function setWidget_Thermostat(_this, set_consigne_temperature_id, consigne_min, consigne_max, consigne, temperature) {
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

    document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .Thermostat').empty().append(domUtils.parseHTML(div));
    var rad2deg = 180 / Math.PI;
    var deg = 0;
    for (var i = -20; i < 81; i++) {
        deg = i * 3;
        mytop = (-Math.sin(deg / rad2deg) * 95 + 100);
        myleft = Math.cos((180 - deg) / rad2deg) * 95 + 100;

        var div = '<div class="colorBar" style="-webkit-transform: rotate(' + deg + 'deg) scale(1.25, 0.5); -moz-transform: rotate(' + deg + 'deg) scale(1.25, 0.5); -ms-transform: rotate(' + deg + 'deg) scale(1.25, 0.5);transform: rotate(' + deg + 'deg) scale(1.25, 0.5);top: ' + mytop + 'px; left: ' + myleft + 'px" >'
        document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .barres').append(domUtils.parseHTML(div));
    }
    majWidget(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .Thermostat'), false)

    document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .monter_temperature').onclick = function() {
        var temperatureConsigne = parseInt(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .cercle_int .Temperature_consigne').getAttribute("consigne"))
        if (temperatureConsigne == "") { temperatureConsigne = 7 }
        var temperatureMax = parseInt(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .cercle_int .barres').getAttribute("max"))
        var temperatureMin = parseInt(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .cercle_int .barres').getAttribute("min"))
        temperatureConsigne = temperatureConsigne + 1
        if (Math.round(temperatureConsigne) > temperatureMax) {
            temperatureConsigne = temperatureMax
        }
        if (Math.round(temperatureConsigne) < temperatureMin) {
            temperatureConsigne = temperatureMin
        }
        document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .cercle_int .Temperature_consigne').setAttribute("consigne", temperatureConsigne)
        majWidget(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .Thermostat'), true)
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {
            majWidget(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .Thermostat'), false)
            var temp = document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .Thermostat').querySelector('.Temperature_consigne').getAttribute("consigne")
            jeedom.cmd.execute({ id: set_consigne_temperature_id, value: { slider: temp } });
        }, 2000);
    };
    document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .descendre_temperature').onclick = function() {
        var temperatureConsigne = parseInt(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .cercle_int .Temperature_consigne').getAttribute("consigne"))
        if (temperatureConsigne == "") { temperatureConsigne = 7 }
        var temperatureMax = parseInt(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .cercle_int .barres').getAttribute("max"))
        var temperatureMin = parseInt(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .cercle_int .barres').getAttribute("min"))
        temperatureConsigne = temperatureConsigne - 1
        if (Math.round(temperatureConsigne) > temperatureMax) {
            temperatureConsigne = temperatureMax
        }
        if (Math.round(temperatureConsigne) < temperatureMin) {
            temperatureConsigne = temperatureMin
        }

        document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .cercle_int .Temperature_consigne').setAttribute("consigne", temperatureConsigne)
        majWidget(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .Thermostat'), true)
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {
            majWidget(document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .Thermostat'), false)
            var temp = document.querySelector('.eqLogic[data-eqLogic_uid=' + _this.getAttribute("data-eqlogic_uid") + '] .Thermostat').querySelector('.Temperature_consigne').getAttribute("consigne")
            jeedom.cmd.execute({ id: set_consigne_temperature_id, value: { slider: temp } });
        }, 2000);
    };


}

function maj_Widget_Thermostat(_this, click) {

    var couleurs = ['243594', '2c358f', '373487', '44337e', '513174', '5c306c', '6b2f62', '792e58', '892d4d', '9e2b3d', 'b4292e', 'c9271f', 'e0250e'];
    var temperatureConsigne = Math.round(_this.querySelector('.Temperature_consigne').getAttribute('consigne'))
    if (temperatureConsigne == 0) { temperatureConsigne = 7 }
    var temperatureMax = parseInt(_this.querySelector('.barres').getAttribute("max"))
    var temperatureMin = parseInt(_this.querySelector('.barres').getAttribute("min"))

    if (Math.round(temperatureConsigne) > temperatureMax) {
        temperatureConsigne = temperatureMax
    }
    if (Math.round(temperatureConsigne) < temperatureMin) {
        temperatureConsigne = temperatureMin
    }
    var temperatureMax = _this.querySelector('.barres').getAttribute('max')
    var temperatureMin = _this.querySelector('.barres').getAttribute('min')
    var temperatureActuelle = _this.querySelector('.Temperature_actuelle').getAttribute('temp')
    _this.querySelector('.Temperature_consigne').html(temperatureConsigne + "°C");
    if (temperatureActuelle != '') {
        _this.querySelector('.Nom_Temperature_actuelle').html("Température actuelle");
        _this.querySelector('.Temperature_actuelle').html(temperatureActuelle + "°C");
    } else {
        _this.querySelector('.Nom_Temperature_actuelle').html("");
        _this.querySelector('.Temperature_actuelle').html("");
    }
    ratio = temperatureMax / (temperatureMax - temperatureMin) - 1
    nb = (Math.round(temperatureConsigne) / (temperatureMax - temperatureMin) - ratio) * 100

    if (click) {
        _this.querySelectorAll('.colorBar').removeClass('active')
        var i = 0
        _this.querySelectorAll('.colorBar').forEach(_el => {
            if (i < nb) {
                _el.addClass("active")
            }
            i++
        })
    } else {
        _this.querySelector('.colorBar').removeClass('active');
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
    centerCircle = _this.querySelector('.cercle_int')
    centerCircle.style.background = couleurFond;

}

function reset_page(id, uid, page, action_en_cours) {
    if (page == 'page1') {
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_1').style.display = 'block'
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_2').style.display = 'none'
        if (document.querySelectorAll('.eqLogic[data-eqLogic_uid=' + uid + '] .droite').length > 1) {
            console.log(action_en_cours)
            if (action_en_cours == "" || action_en_cours == "Arrêt" || action_en_cours == "Absent" || action_en_cours == "Ventilation") {

                document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_1 .droite').style.display = 'none'
            } else {
                document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_1 .droite').style.display = 'block'
                document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .Thermostat').style.display = 'block'
            }
        }

    }
    if (page == 'page2') {
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_2').style.display = 'block'
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_1').style.display = 'none'

        setTimeout(() => {

            domUtils.ajax({
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
                        jeedomUtils.showAlert({
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

function commun_widget(id, uid, info_widget, action_en_cours, set_planification_id, heure_fin_change_id, calendar_selector, page) {

    var taille = parseInt(document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .object_name ').style.height.replace(/px/i, ''))

    document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .tuile').style.height = "calc(100% - " + taille + "px)"
    if (document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .object_name').style.display == "block") {

        if (document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .widget-name').style.display == "block") {
            document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .widget-name').style.display = "none"
            document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .nom_eqLogic').style.display = "block"
        }

        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .tuile').style.backgroundColor = "rgba(0,0,0,0.5)"
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + ']').style.boxShadow = '0px 0px 3px 0.5px rgba(255,255,255,1)'
    }
    if (page == 'page1') {
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_1').style.display = 'block';
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_2').style.display = 'none';
    } else {
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_1').style.display = 'none';
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_2').style.display = 'block';
    }
    if (document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_2').style.display == 'block') {
        reset_page(id, uid, 'page2', action_en_cours)
    } else {
        reset_page(id, uid, 'page1', action_en_cours)
    }
    document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .selectCalendar').onchange = function(select) {
        jeedom.cmd.execute({
            id: set_planification_id,
            value: {
                select: select.target.selectedOptions[0].innerHTML,
                Id_planification: select.target.selectedOptions[0].id
            }
        });
    };
    document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .changecmd').onclick = function() {
        var page = "page1"

        if (document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .page_2').style.display == 'block') {
            page = "page1"

            reset_page(id, uid, page, action_en_cours)
        } else {
            page = "page2"
            reset_page(id, uid, page, action_en_cours)
        }
        domUtils.ajax({
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
                    jeedomUtils.showAlert({
                        message: data.result,
                        level: 'danger'
                    })
                    return
                }

            }
        })

    };
    document.getElementById('div_pageContainer').addEventListener('click', function(event) {

    })


    /*document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').onclick = function() {
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').datetimepicker({
            minDate: '0d',
            step: 15,
            theme: 'dark',
            onClose: function(dp, $input) {
                document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').datetimepicker('destroy')
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
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').datetimepicker('show');
    };*/
    document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .changecmd').style.display = 'block';
    if (document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .selectCalendar').children.length > 1) {
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .selectCalendar').style.display = 'inline';
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .changecmd').style.display = 'block';
    } else {
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .selectCalendar').style.display = 'none';
    }
    if (calendar_selector == "" || calendar_selector == " ") {
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + ']  .selectCalendar').style.display = 'none';
    }
    /*if (document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .datetimepicker').style.display ==  'block') {
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .changecmd').style.display = 'block';
    }*/

    if (info_widget != "" && info_widget != " ") {
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .info_widget').empty().append(info_widget)
        document.querySelector('.eqLogic[data-eqLogic_uid=' + uid + '] .changecmd').style.display = 'block';
    }
}