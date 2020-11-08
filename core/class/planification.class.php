<?php
require_once  '/var/www/html/core/php/core.inc.php';

class planification extends eqLogic {
	function supp_accents( $str, $charset='utf-8' ) {
 
    $str = htmlentities( $str, ENT_NOQUOTES, $charset );
    
    $str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
    $str = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
    $str = preg_replace( '#&[^;]+;#', '', $str );
    
    return $str;
}
    public static $_widgetPossibility = array('custom' => true, 'custom::layout' => false);

    public static function logger($str = '', $level = 'debug') {
        if (is_array($str)) $str = json_encode($str);
        $function_name = debug_backtrace(false, 2)[1]['function'];
        $class_name = debug_backtrace(false, 2)[1]['class'];
        $msg = '['.$class_name.'] <'. $function_name .'> '.$str;
        log::add('planification', $level, $msg);
    }
  	public function Recup_liste_mode_planification($eqLogic_id) {
      	$eqLogic = eqLogic::byId($eqLogic_id);
      	if (!is_object($eqLogic)) {
			throw new Exception(__('Impossible de trouver l\'équipement : ', __FILE__) . $eqLogic_id);
		}
      	$cmds=$eqLogic->getConfiguration("commandes_planification","");
      	$arr =array();
      	foreach ($cmds as $cmd) {
           	array_push($arr, '<option class="Option_Mode_Periode couleur-'.$cmd["couleur"].'" id="'.$cmd["Id"].'" value="'. $cmd["nom"] . '">'.$cmd["nom"] .'</option>');
        }
      
      return $arr;
    }
	public function Verificarion_planification_avant_suppression_commande($eqLogic_id,$cmd_id){
		$eqLogic = eqLogic::byId($eqLogic_id);
      	if (!is_object($eqLogic)) {
			throw new Exception(__('Impossible de trouver l\'équipement : ', __FILE__) . $eqLogic_id);
		}
		$arr=[];
		$planifications=$this->getConfiguration('planifications', "");
		foreach ( $planifications as $planification){
			$existe=false;
			$nom_planification=$planification['nom_planification'];
			foreach ( $planification['semaine'] as $jour){
				
				foreach($jour['periodes'] as $periode){
					
					//var_dump( $periode['Id']);
					if ($periode['Id'] == $cmd_id){
						$existe=true;
						array_push ( $arr, $nom_planification );
						//$arr["Nom_planification"] = $nom_planification;
						break;
						//return true;
					}
					if($existe){break;}
				}
				if($existe){break;}
			}
		}
		return $arr;
	}
	public function importer_eqlogic($_eqLogic_id) {
	
		$eqLogic = eqLogic::byId($_eqLogic_id);
		if (!is_object($eqLogic)) {
			throw new Exception(__('Impossible de trouver l\'équipement : ', __FILE__) . $_eqLogic_id);
		}
		if ($eqLogic->getEqType_name() == 'planification') {
			throw new Exception(__('Vous ne pouvez importer les commandes d\'un équipement planification', __FILE__));
		}
		$cmds_prog=[];
		$arr=$this->getConfiguration('commandes_planification', "");
		for ($i = 0; $i < count($arr); $i++) {
			$cmd_prog["Id"] = $arr[$i]["Id"];
			$cmd_prog["nom"] = $arr[$i]["nom"];
			$cmd_prog["cmd"] = $arr[$i]["cmd"];
			$cmd_prog["couleur"] = $arr[$i]["couleur"];
			array_push ( $cmds_prog, $cmd_prog );				
		}
		$cmd_prog=[];
		
		
		foreach ($eqLogic->getCmd() as $cmd) {
			if ($cmd->getName() != 'Rafraichir' and $cmd->getType() == 'action') {
				$cmd_prog["nom"] = $cmd->getName();
				$cmd_prog["cmd"] = '#' . $cmd->getId() .'#';
				$cmd_prog["couleur"] = "jaune";
				array_push ( $cmds_prog, $cmd_prog );
			}
		}
		
		$this->setConfiguration('commandes_planification', $cmds_prog);
		$this->save();
	} 
    public function preSave() {
     if ($this->getConfiguration('type','') == ''){
		   	$this->setConfiguration('type', 'Autre');
			$this->setIsVisible(1);
           	$this->setIsEnable(1);
              
        }
    }

    public function postSave() {
         
    }

    public function toHtml($_version = 'dashboard') {
        /*$version = jeedom::versionAlias($_version);
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }

        // only custom template for thermostat dashboard:
        $_thisType = $this->getConfiguration('type');
        $replace['#category#'] = $this->getPrimaryCategory();

        if ($_thisType == 'Thermostat')
        {
            $refresh = $this->getCmd(null, 'refresh');
            $replace['#refresh_id#'] = $refresh->getId();

            $replace['#temperature_name#'] = __('Température', __FILE__);
            $replace['#humidity_name#'] = __('Humidité', __FILE__);
            $replace['#presence_name#'] = __('Presence', __FILE__);

            $cmd = $this->getCmd(null, 'temperature_order');
            $tmpConsigne = $cmd->execCmd();
            $replace['#temperature_order#'] = $tmpConsigne;
            $replace['#temperature_order_id#'] = $cmd->getId();
            $replace['#temperature_order_collectDate#'] = __('Date de valeur', __FILE__).' : '.$cmd->getValueDate().'<br>'.__('Date de collecte', __FILE__).' : '.$cmd->getCollectDate();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#temperature_order_history#'] = 'history cursor';
            }
            $cmd = $this->getCmd(null, 'set_plus_one');
            $replace['#set_plus_one_id#'] = $cmd->getId();
            $cmd = $this->getCmd(null, 'set_minus_one');
            $replace['#set_minus_one_id#'] = $cmd->getId();
            $cmd = $this->getCmd(null, 'cancel_time_order');
            $replace['#cancel_id#'] = $cmd->getId();

            $cmd = $this->getCmd(null, 'temperature');
            $tmpRoom = $cmd->execCmd();
            $replace['#temperature#'] = $tmpRoom;
            $replace['#temperature_id#'] = $cmd->getId();
            $replace['#temperature_collectDate#'] = __('Date de valeur', __FILE__).' : '.$cmd->getValueDate().'<br>'.__('Date de collecte', __FILE__).' : '.$cmd->getCollectDate();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#temperature_history#'] = 'history cursor';
            }

            $cmd = $this->getCmd(null, 'humidity');
            $replace['#humidity#'] = $cmd->execCmd();
            $replace['#humidity_id#'] = $cmd->getId();
            $replace['#humidity_collectDate#'] = __('Date de valeur', __FILE__).' : '.$cmd->getValueDate().'<br>'.__('Date de collecte', __FILE__).' : '.$cmd->getCollectDate();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#humidity_history#'] = 'history cursor';
            }

            $cmd = $this->getCmd(null, 'presence');
            $pres = $cmd->execCmd();
            $replace['#presence_id#'] = $cmd->getId();
            $replace['#presence_collectDate#'] = __('Date de valeur', __FILE__).' : '.$cmd->getValueDate().'<br>'.__('Date de collecte', __FILE__).' : '.$cmd->getCollectDate();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#presence_history#'] = 'history cursor';
            }
            $replace['#pres_class#'] = 'fa fa-check';
            if ($pres == 0) {
                $replace['#pres_class#'] = 'fa fa-times';
            }

            $cmd = $this->getCmd(null, 'last_presence');
            $replace['#lastpres#'] = $cmd->execCmd();
            $replace['#lastpres_id#'] = $cmd->getId();
            $replace['#lastpres_collectDate#'] = __('Date de valeur', __FILE__).' : '.$cmd->getValueDate().'<br>'.__('Date de collecte', __FILE__).' : '.$cmd->getCollectDate();

            $cmd = $this->getCmd(null, 'heating');
            $heating = $cmd->execCmd();
            $replace['#heating_id#'] = $cmd->getId();
            $replace['#heating_collectDate#'] = __('Date de valeur', __FILE__).' : '.$cmd->getValueDate().'<br>'.__('Date de collecte', __FILE__).' : '.$cmd->getCollectDate();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#heating_history#'] = 'history cursor';
            }

            if ($heating > 0) $replace['#imgheating#'] = '/plugins/planification/core/img/heating_on.png';
            else $replace['#imgheating#'] = '/plugins/planification/core/img/heating_off.png';

            $html = template_replace($replace, getTemplate('core', $version, 'thermostat', 'planification'));
        }

        if ($_thisType == 'Module Chauffage')
        {
            // infos
            $cmd = $this->getCmd(null, 'module_order');
            $order = $cmd->execCmd();
            $replace['#order#'] = $order;
            $replace['#order_id#'] = $cmd->getId();
            $replace['#order_collectDate#'] = __('Date de valeur', __FILE__).' : '.$cmd->getValueDate().'<br>'.__('Date de collecte', __FILE__).' : '.$cmd->getCollectDate();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#order_history#'] = 'history cursor';
            }

            $cmd = $this->getCmd(null, 'last_communication');
            $replace['#last_communication#'] = $cmd->execCmd();
            $replace['#last_communication_id#'] = $cmd->getId();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#last_communication_history#'] = 'history cursor';
            }

            // actions:
            if ($order != 'monozone')
            {
                $cmd = $this->getCmd(null, 'set_order');
                $replace['#set_order_id#'] = $cmd->getId();
                $modes = $cmd->getConfiguration('listValue');
                $modes = explode(';', $modes);
                $options = '';
                foreach ($modes as $mode)
                {
                    $value = explode('|', $mode)[0];
                    $display = explode('|', $mode)[1];
                    if ($order == $display) $options .= '<option value="'.$value.'" selected>'.$display.'</option>';
                    else $options .= '<option value="'.$value.'">'.$display.'</option>';
                }
                $replace['#set_order_listValue#'] = $options;
            }
            $cmd = $this->getCmd(null, 'current_program');
            $current_program = $cmd->execCmd();
            $replace['#program_collectDate#'] = __('Date de valeur', __FILE__).' : '.$cmd->getValueDate().'<br>'.__('Date de collecte', __FILE__).' : '.$cmd->getCollectDate();

            $cmd = $this->getCmd(null, 'set_program');
            $replace['#set_program_id#'] = $cmd->getId();
            $programs = $cmd->getConfiguration('listValue');
            $programs = explode(';', $programs);
            $options = '';
            foreach ($programs as $program)
            {
                $value = explode('|', $program)[0];
                $display = explode('|', $program)[1];
                if ($current_program == $display) $options .= '<option value="'.$value.'" selected>'.$display.'</option>';
                else $options .= '<option value="'.$value.'">'.$display.'</option>';
            }
            $replace['#set_program_listValue#'] = $options;

            if ($order == 'monozone') $html = template_replace($replace, getTemplate('core', $version, 'module-t', 'planification'));
            else $html = template_replace($replace, getTemplate('core', $version, 'module', 'planification'));
        }

        if ($_thisType == 'Passerelle')
        {
            $cmd = $this->getCmd(null, 'last_communication');
            $replace['#last_communication#'] = $cmd->execCmd();
            $replace['#last_communication_id#'] = $cmd->getId();
            $replace['#last_communication_collectDate#'] = __('Date de valeur', __FILE__).' : '.$cmd->getValueDate().'<br>'.__('Date de collecte', __FILE__).' : '.$cmd->getCollectDate();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#last_communication_history#'] = 'history cursor';
            }


            $cmd = $this->getCmd(null, 'firmware_version');
            $replace['#firmware_version#'] = $cmd->execCmd();
            $replace['#firmware_version_id#'] = $cmd->getId();
            $replace['#firmware_version_collectDate#'] = __('Date de valeur', __FILE__).' : '.$cmd->getValueDate().'<br>'.__('Date de collecte', __FILE__).' : '.$cmd->getCollectDate();
            if ($cmd->getIsHistorized() == 1) {
                $replace['#firmware_version_history#'] = 'history cursor';
            }

            $html = template_replace($replace, getTemplate('core', $version, 'gateway', 'planification'));
        }

        return $html;*/
    }

    /*public static function deadCmd() {
        planification::logger();
        $return = array();
        $actionsOnError = config::byKey('actionsOnError', 'planification');
        foreach ($actionsOnError as $cmdAr) {
            $options = $cmdAr['options'];
            if ($options['enable'] == 1)
            {
                $cmdId = $cmdAr['cmd'];
                if ($cmdId != '') {
                    if (!cmd::byId($cmdId)) {
                        $return[] = array('detail' => 'Configuration planification', 'help' => 'Action sur erreur', 'who' => $cmdId);
                        planification::logger('deadCmd found: cmdId:'.$cmdId);
                    }
                }
            }
        }
        return $return;
    }*/
}

class planificationCmd extends cmd {
    public static $_widgetPossibility = array('custom' => false);

    public function dontRemoveCmd() {
		if ($this->getConfiguration('planification') == '1') {
			return false;
		}
        return true;
    }

    public function execute($_options = array()) {
		if ($this->getConfiguration("planification",'0') == '1'){
			$cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('infoName')));
			if (is_object($cmd)) {
				$cmd->execCmd();
				return;
			}
		}
		
    }
	public function preSave() {
		
		// uniquement les commande pour la planification
		if ($this->getConfiguration("planification",'0') == '1') {
          	if ($this->getLogicalId() == ''){
			   $this->setLogicalId($this->getName());
		   }
			if ($this->getConfiguration('infoName') == '') {
				throw new Exception(__('Le nom de la commande ne peut etre vide', __FILE__));
			}
			$cmd = cmd::byId(str_replace('#', '', $this->getConfiguration('infoName')));
			if (!is_object($cmd)) {
				throw new Exception(__('La commande ne peut etre vide', __FILE__));
			}
			
		} 
	}
}
?>
