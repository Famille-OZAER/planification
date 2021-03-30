

	function Modifie_temperature(_this,e) {
		var temp_consigne=$(_this).find('.Temperature_consigne').attr("consigne")
		var temperatureMax=$(_this).find('.Temperature_consigne').attr("max")
		var temperatureMin=$(_this).find('.Temperature_consigne').attr("min")
		var lastAngle=$(_this).find('.Temperature_consigne').attr("lastAngle")
		var offset = $(_this).offset();
		var width=$(_this).width();
		var height=$(_this).height();
		var center_x = (offset.left) + (width/2);
		var center_y = (offset.top) + (height/2);
		var mouse_x = e.pageX; 
		var mouse_y = e.pageY;
		var temp_consigne=Math.round(temp_consigne*10)/10;
		var radians = Math.atan2(mouse_x - center_x, mouse_y - center_y);
		
		degree = (radians * (180 / Math.PI) * -1) + 180; 
		if ( degree - lastAngle > 0){
			temp_consigne+=0.2;
		}else{
		   temp_consigne-=0.2;
		}
		
		if (Math.round(temp_consigne) > temperatureMax){
			temp_consigne=temperatureMax
		}
		if (Math.round(temp_consigne) < temperatureMin){
			temp_consigne=temperatureMin
		}
		
		$(_this).find('.Temperature_consigne').attr("consigne",temp_consigne)
		$(_this).find('.Temperature_consigne').attr("lastAngle",degree)
		majWidget($(_this).parents('.Thermostat'))
		 
		 
		 
	};

	function majWidget(_this){
		var couleurs = ['243594','2c358f','373487','44337e','513174','5c306c','6b2f62','792e58','892d4d','9e2b3d','b4292e','c9271f','e0250e'];
		var temperatureConsigne=Math.round($(_this).find('.Temperature_consigne').attr('consigne'))
		var temperatureMax=$(_this).find('.barres').attr('max')
		var temperatureMin=$(_this).find('.barres').attr('min')
		var temperatureActuelle=$(_this).find('.Temperature_actuelle').attr('temp')
		$(_this).find('.Temperature_consigne').html(temperatureConsigne +"°C");
		$(_this).find('.Temperature_actuelle').html(temperatureActuelle + "°C");
		ratio=temperatureMax/(temperatureMax-temperatureMin)-1
		nb=(Math.round(temperatureConsigne)/(temperatureMax-temperatureMin)-ratio)*100
		$(_this).find('.colorBar').removeClass('active').slice(0, Math.round(nb)).addClass('active');
		var couleurFond =""
		if (temperatureConsigne < 16){
			couleurFond = '#' + couleurs[0];
		}
		if (temperatureConsigne > 28){
			couleurFond = '#' + couleurs[11];
		}
		if (temperatureConsigne <= 28 && temperatureConsigne >= 16){
			couleurFond = '#' + couleurs[temperatureConsigne - 16];
		}	
		centerCircle=$(_this).find('.cercle_int')
		//centerCircle.css("background", "-webkit-radial-gradient(top left, ellipse cover, #fcf7f7 10%," + couleurFond + " 60%)"); /* Chrome 10 */
		//centerCircle.css("background", "-moz-radial-gradient(top left, ellipse cover, #fcf7f7 10%," + couleurFond + " 60%)"); /* FF */
		//centerCircle.css("background", "-webkit-gradient(radial, top left, 0px, top left, 100%, color-stop(10%,fff9f9), color-stop(60%,"+ couleurFond +"))"); /* Safari */
		//centerCircle.css("background", "-o-radial-gradient(top left, ellipse cover, #fcf7f7 10%," + couleurFond + " 60%)"); /* Opera 12+ */
		//centerCircle.css("background", "-ms-radial-gradient(top left, ellipse cover, #fcf7f7 10%," + couleurFond + " 60%)"); /* IE10+ */
		//centerCircle.css("background", "radial-gradient(top left, ellipse cover, #fcf7f7 10%," + couleurFond + " 60%)"); /* W3C */
		centerCircle.css("background",  couleurFond ); /* W3C */

	}

	function drag_start(_this,e){
		$(_this).attr('mouseDown',"ok")
	}
	function drag_move(_this,e){
		if ($(_this).attr('mouseDown') == "ok"){
			Modifie_temperature($(_this),e)
		}
	}
	function drag_stop(_this,e){
		if ($(_this).attr('mouseDown') == "ok"){
			$(_this).find(".Temperature_consigne").attr('consigne',Math.round($(_this).find(".Temperature_consigne").attr('consigne')));
			$(_this).removeAttr('mouseDown',"");
		}
	}
    function reset_page(id,uid,page,action_en_cours){		
        if (page == 'page1'){
            $('.eqLogic[data-eqLogic_uid='+uid+'] .page_1').css('display', 'block')
            $('.eqLogic[data-eqLogic_uid='+uid+'] .page_2').css('display', 'none')
            if (action_en_cours=="Arrêt"){
                $('.eqLogic[data-eqLogic_uid='+uid+'] .droite').find(".page_1").css('display', 'none')
            }else if (action_en_cours=="Absent"){
                $('.eqLogic[data-eqLogic_uid='+uid+'] .droite').find(".page_1").css('display', 'none')
            }else if (action_en_cours=="Ventilation"){
                $('.eqLogic[data-eqLogic_uid='+uid+'] .droite').find(".page_1").css('display', 'none')
            }else{
                $('.eqLogic[data-eqLogic_uid='+uid+'] .droite').find(".page_1").css('display', 'block')
            }
        }
        if (page == 'page2'){
            $('.eqLogic[data-eqLogic_uid='+uid+'] .page_2').css('display', 'block')
            $('.eqLogic[data-eqLogic_uid='+uid+'] .page_1').css('display', 'none')
            setTimeout(function(){ 
                $.ajax({
                    type: "POST",
                    url: "plugins/planification/core/ajax/planification.ajax.php",
                    data: {
                        action: "Set_widget_cache",
                        id: id,
                        page :"page1",
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
                reset_page(id,uid,"page1",action_en_cours)

                
            
            },60000);
        }
    }