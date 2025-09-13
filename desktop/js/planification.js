JSONCLIPBOARD = null
localStorage.setItem("JSONCLIPBOARD", null);
flatpickr.localize(flatpickr.l10ns.fr)
var typesEquipements = ['chauffages', 'PACs', 'volets', 'prises', 'persos'];
var Json_lever_coucher=''
document.getElementById('div_pageContainer').addEventListener('click', function(event) {
  var _target = null
  closeAllSelect(event.target)


  if (event.target.closest('.ajouter_eqlogic')) {
    const dialog_message = `
                <form class="form-horizontal" onsubmit="return false;">
                    <div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="Type_équipement" id="Volet" value="Volet" checked="checked"> {{Volet}}
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="Type_équipement" id="PAC" value="PAC"> {{Pompe à chaleur}}
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="Type_équipement" id="Prise" value="Prise"> {{Prise}}
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="Type_équipement" id="Chauffage" value="Chauffage"> {{Chauffage avec fil pilote}}
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="Type_équipement" id="Perso" value="Perso"> {{Perso}}
                            </label>
                        </div>
                        <br>
                        <div class="input">
                            <input class="col-sm-8" type="text" placeholder="Nom de l'équipement" name="nom" id="nom">
                        </div>
                        <br>
                    </div>
                </form>`;

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
              const nom = document.querySelector("input[name='nom']").value.trim();
              if (!nom) {
                jeedomUtils.showAlert({
                  message: "Le nom de l'équipement ne peut pas être vide.",
                  level: 'warning',
                  timeout: 2000,
                  emptyBefore: true,
                });
                return;
              }
              const type_equipement = document.querySelector("input[name='Type_équipement']:checked").value;
              domUtils.ajax({
                type: "POST",
                url: "plugins/planification/core/ajax/planification.ajax.php",
                data: {
                  action: "Ajout_equipement",
                  Nom: nom,
                  Type_équipement: type_equipement,
                },
                global: true,
                async: false,
                error: (request, status, error) => handleAjaxError(request, status, error),
                success: (data) => {
                  if (data.state !== 'ok') {
                    jeedomUtils.showAlert({ message: data.result, level: 'danger' });
                  } else {
                    window.location.href = `index.php?v=d&p=planification&m=planification&id=${data.result}`;
                  }
                }
              });
            }
          }
        },
        cancel: {
          label: '{{Annuler}}',
          className: 'warning',
          callback: { click: function() { jeeDialog.get('#mod_ajout_équipement').close(); } }
        }
      },
      onClose: function() {
        jeeDialog.get('#mod_ajout_équipement').destroy();
      }
    });
  }
  if (event.target.closest('.sante')) {
    jeeDialog.dialog({
      id: 'mod_ajout_équipement',
      title: '{{Santé Planification}}',
      width: Math.min(window.innerWidth - 50, window.innerHeight - 150),
      height: window.innerHeight - 150,
      contentUrl: "index.php?v=d&plugin=planification&modal=health",
      buttons: {},
      onClose: function() {
        jeeDialog.get('#mod_ajout_équipement').destroy();
      }
    });
  }

  if (event.target.closest('.restart_demon')) {
    jeedom.plugin.deamonStart({
      id: 'planification',
      forceRestart: 1,
      error: (error) => {
        jeedomUtils.showAlert({
          message: "Problème lors du redémarrage du démon.",
          level: 'danger'
        });
      },
      success: () => {
        jeedomUtils.showAlert({
          message: "Démon redémarré avec succès.",
          level: 'warning'
        });
      }
    });
  }

  if ( event.target.closest('.dupliquer_equipement')) {
    const eqLogicId = document.querySelector('.eqLogicAttr[data-l1key=id]').value;
    const eqLogicName = document.querySelector('.eqLogicAttr[data-l1key=name]').value;

    if (eqLogicId && eqLogicId.trim() !== '') {
      jeeDialog.prompt({
        title: '{{Nom de la copie de l\'équipement ?}}',
        value: `${eqLogicName}_copie`,
        callback: (result) => {
          if (result !== null && result.trim() !== '') {
            const id_source = eqLogicId;

            jeedom.eqLogic.copy({
              id: id_source,
              name: result,
              error: (error) => {
                jeedomUtils.showAlert({ message: error.message, level: 'danger' });
              },
              success: (data) => {
                modifyWithoutSave = false;
                const id_cible = data.id;

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
                  error: (request, status, error) => {
                    handleAjaxError(request, status, error);
                  },
                  success: (data) => {
                    if (data.state !== 'ok') {
                      jeedomUtils.showAlert({
                        message: data.result,
                        level: 'danger'
                      });
                      return;
                    }
                  }
                });

                const vars = getUrlVars();
                let url = 'index.php?';
                Object.keys(vars).forEach((key) => {
                  if (!['id', 'saveSuccessFull', 'removeSuccessFull'].includes(key)) {
                    url += `${key}=${vars[key].replace('#', '')}&`;
                  }
                });
                url += `id=${data.id}&saveSuccessFull=1`;

                jeedomUtils.loadPage(url);
              }
            });
          }
        }
      });
    }
  }   
  if (event.target.closest('.li_eqLogic')) {
    const _target = event.target.closest('.li_eqLogic');
    const activeTabpaneId = document.querySelector(".tab-content .tab-pane.active").id;

    jeedomUtils.hideAlert();

    const type = document.body.dataset.page;
    const eqLogicId = _target.dataset.eqlogic_id;

    if (event.ctrlKey || event.metaKey) {
      window.open(`index.php?v=d&m=${type}&p=${type}&id=${eqLogicId}`).focus();
    } else {
      const eqLogicType = _target.dataset.eqlogic_type;
      jeeFrontEnd.pluginTemplate.displayEqlogic(eqLogicType, eqLogicId);
    }

    document.querySelectorAll('.li_eqLogic').forEach((el) => el.classList.remove('active'));
    _target.classList.add('active');

    setTimeout(() => {
      document.querySelectorAll("li").forEach((li) => {
        if (li.id.includes(activeTabpaneId)) {
          li.querySelector('a').click();
        }
      });
    }, 50);
  }
  if (event.target.closest('.bt_afficher_timepicker') || event.target.closest('.bt_afficher_timepicker_planification')) {
    flatpickr(event.target.closest('div').querySelector('.in_timepicker'), {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      time_24hr: true,
      minuteIncrement: 1,
      allowInput: true,
      clickOpens: false,
      onChange: function(selectedDates, dateStr, instance) {},
      onOpen: function(selectedDates, dateStr, instance) {
        if (instance.element.value !== '') {
          instance.hourElement.value = instance.element.value.substring(0, 2);
          instance.minuteElement.value = instance.element.value.substring(3, 5);
        } else {
          instance.hourElement.value = '00';
          instance.minuteElement.value = '00';
        }
      },
      onClose: function(selectedDates, dateStr, instance) {
        if (event.target.closest('.bt_afficher_timepicker_planification')) {
          const time = instance.element.value;
          const time_old = instance.element.getAttribute("value");

          if (time !== time_old) {
            modifyWithoutSave = true;
            instance.element.setAttribute("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]));
            instance.element.setAttribute("value", time);

            const Divjour = event.target.closest('.JourSemaine');
            triage_jour(Divjour.closest('.JourSemaine'));
            MAJ_Graphique_jour(Divjour.closest('.JourSemaine'));
          }
        }
        instance.destroy();
      },
      onValueUpdate: function(selectedDates, dateStr, instance) {}
    });
    event.target.closest("div").querySelector('.in_timepicker')._flatpickr.open();
  }
})
document.getElementById('tab_eqlogic').addEventListener('click', function(event) {
  const handleCmdInfoSelection = (type, subType = "",show_alias = false) => {
    const el = event.target.closest('div').querySelector('input');
    if (subType !=''){
      jeedom.cmd.getSelectModal({ cmd: { type, subType } }, function(result) {
        el.value = result.human;
      });
    }else{
      jeedom.cmd.getSelectModal({ cmd: { type} }, function(result) {
        el.value = result.human;
        if (show_alias){
        
          if( el.value != ''){
            target.closest('.option').querySelector('.alias').style.display='block'
          }else{
            target.closest('.option').querySelector('.alias').style.display='none'
          }
        }
      });
    }
    
  };

  const toggleImage = (iconPath) => {
    const http = new XMLHttpRequest();
    http.open('HEAD', iconPath, false);
    http.send();
    const exists = http.status === 200;
    return exists ? iconPath : "plugins/planification/plugin_info/planification_icon.png";
  };

  const target = event.target;
  
  if (target.closest('.list_Cmd_info_numeric')) {
    handleCmdInfoSelection('info', 'numeric');
  } else if (target.closest('.list_Cmd_info_binary')) {
    handleCmdInfoSelection('info', 'binary');
  } else if (target.closest('.list_Cmd_info')) {
      handleCmdInfoSelection('info','',true);
      
    
  } else if (target.closest('.bt_modifier_image')) {
    const objectId = document.querySelector('#tab_eqlogic .eqLogicAttr[data-l1key=id]').value;
    const url = `index.php?v=d&plugin=planification&modal=selectIcon&object_id=${objectId}`;

    jeeDialog.dialog({
      id: 'mod_selectIcon',
      title: '{{Choisir une illustration}}',
      width: Math.min(window.innerWidth - 50, window.innerHeight - 150),
      height: window.innerHeight - 150,
      buttons: {
        confirm: {
          label: '{{Appliquer}}',
          className: 'success',
          callback: () => {
            const icon = document.querySelector('#mod_selectIcon .iconSelected .iconSel img')?.getAttribute('src') || '';
            document.querySelector('#tab_eqlogic .eqLogicAttr[data-l1key=configuration][data-l2key="Chemin_image"]').value = icon;
            document.querySelector('#img_planificationModel').setAttribute('src', icon);
            document.querySelector("#tab_eqlogic .bt_image_défaut").style.display = 'block';
            jeeDialog.get('#mod_selectIcon').close();
          }
        },
        cancel: {
          label: '{{Annuler}}',
          className: 'warning',
          callback: () => jeeDialog.get('#mod_selectIcon').close(),
        }
      },
      onClose: () => jeeDialog.get('#mod_selectIcon').destroy(),
      contentUrl: url
    });
  } else if (target.closest('.bt_image_défaut')) {
    const typeEquipement = document.querySelector('.eqLogicAttr[data-l2key=Type_équipement]').value;
    const typeIcons = {
      "PAC": 'plugins/planification/core/img/pac.png',
      "Volet": 'plugins/planification/core/img/volet.png',
      "Chauffage": 'plugins/planification/core/img/chauffage.png',
      "Prise": 'plugins/planification/core/img/prise.png',
      "Perso": 'plugins/planification/core/img/perso.png'
    };

    const defaultIcon = typeIcons[typeEquipement] || "plugins/planification/plugin_info/planification_icon.png";
    const finalIconPath = toggleImage(defaultIcon);

    document.querySelector('#img_planificationModel').setAttribute('src', finalIconPath);
    document.querySelector('input[data-l2key=Chemin_image]').value = finalIconPath;
    document.querySelector("#tab_eqlogic .bt_image_défaut").style.display = 'none';
  }
});
document.getElementById('tab_eqlogic').addEventListener('focusout', function(event) {
  let _target = event.target.closest('.cmdAction');

  if (_target) {
    const div_alias = _target.closest('.option').querySelector(".alias");
    const type_eq = _target.closest(".option").classList[1];
    const etatElement = document.querySelector(`#tab_eqlogic .${type_eq} .eqLogicAttr[data-l2key=etat_id]`);

    if (_target.value.trim() !== "" && etatElement) {
      domUtils.ajax({
        type: "POST",
        url: "core/ajax/cmd.ajax.php",
        data: {
          action: 'byHumanName',
          humanName: etatElement.value
        },
        global: true,
        async: false,
        error: function(request, status, error) {
          return "erreur";
        },
        success: function(data) {
          if (data.state !== "ok") {
            jeedomUtils.showAlert({
              message: "La commande de l'état du chauffage est invalide, veuillez insérer une commande valide.",
              level: 'danger'
            });

            etatElement.value = "";
            div_alias.style.display = 'none';
          } else {
            div_alias.style.display = 'block';
          }
        }
      });
    } else {
      div_alias.style.display = 'none';
    }
  }
});
document.getElementById('tab_planifications').addEventListener('click', function(event) {
  if (event.target.closest('.bt_ajouter_planification')) {
    const _target = event.target.closest('.bt_ajouter_planification');

    jeeDialog.prompt({
      title: "Veuillez insérer le nouveau nom de la planification à ajouter.",
      buttons: {
        confirm: { label: 'Ajouter', className: 'success' },
        cancel: { label: 'Annuler', className: 'danger' }
      },
      callback: (resultat) => {
        if (resultat && resultat.trim() !== '') {
          modifyWithoutSave = true;    
          Ajoutplanification({ nom: resultat });    
          const lastPlanification = document.querySelector('#div_planifications .planification:last-of-type');
          AjoutGestionPlanification({ 
            nom: resultat, 
            Id: lastPlanification?.getAttribute('id') 
          });
        }
      }
    });
  }
  if (event.target.closest('.bt_renommer_planification')) {
    const _target = event.target.closest('.bt_renommer_planification');
    const planificationElement = _target.closest('.planification');
    const planificationName = planificationElement.querySelector("span.nom_planification").innerHTML;

    jeeDialog.prompt({
      title: `Veuillez insérer le nouveau nom pour la planification : ${planificationName}.`,
      buttons: {
        confirm: { label: 'Modifier', className: 'success' },
        cancel: { label: 'Annuler', className: 'danger' }
      },
      callback: (resultat) => {
        if (resultat && resultat.trim() !== '') {
          modifyWithoutSave = true;
          planificationElement.querySelector("span.nom_planification").innerHTML = resultat;
          const gestionPlanificationsElement = document.querySelector(`#div_GestionPlanifications .${planificationElement.getAttribute('id')}`);
          if (gestionPlanificationsElement) {
            gestionPlanificationsElement.querySelector(".Nom_planification").innerHTML = resultat;
          }
          
        }
      }
    });
  }

  if (event.target.closest('.bt_dupliquer_planification')) {
    const _target = event.target.closest('.bt_dupliquer_planification');
    const planification = _target.closest('.planification').cloneNode(true);

    jeeDialog.prompt({
      title: "Veuillez insérer le nom pour la planification dupliquée.",
      buttons: {
        confirm: { label: 'Dupliquer', className: 'success' },
        cancel: { label: 'Annuler', className: 'danger' }
      },
      callback: (resultat) => {
        if (resultat && resultat.trim() !== '') {
          modifyWithoutSave = true;
          planification.querySelector('.nom_planification').innerHTML = resultat;
          planification.setAttribute('id', jeedomUtils.uniqId());
          document.querySelector('#div_planifications').appendChild(planification);
          const lastPlanification = document.querySelector('#div_planifications .planification:last-of-type');
          AjoutGestionPlanification({ 
            nom: resultat, 
            Id: lastPlanification.getAttribute('id') 
          });
        }
      }
    });
  }
  if (event.target.closest('.bt_appliquer_planification')) {
    const _target = event.target.closest('.bt_appliquer_planification');
    const planification = _target.closest('.planification');
    const programName = planification.querySelector('.nom_planification').innerHTML;
    const planificationId = planification.getAttribute("Id");

    jeeDialog.confirm({
      message: `Voulez-vous vraiment appliquer la planification "${programName}" maintenant ?`,
      buttons: {
        confirm: { label: 'Oui', className: 'success' },
        cancel: { label: 'Non', className: 'danger' }
      },
      callback: (result) => {
        if (result) {
          jeedom.cmd.execute({
            id: set_planification_Id,
            value: { select: programName, Id_planification: planificationId }
          });
        }
      }
    });
  }
  if (event.target.closest('.bt_supprimer_planification')) {
    const _target = event.target.closest('.bt_supprimer_planification');
    const planificationElement = _target.closest('.planification');
    const planificationId = planificationElement.getAttribute('id');

    jeeDialog.confirm({
      message: "Voulez-vous vraiment supprimer cette planification ?",
      buttons: {
        confirm: { label: 'Oui', className: 'success' },
        cancel: { label: 'Non', className: 'danger' }
      },
      callback: (result) => {
        if (result) {
          modifyWithoutSave = true;
          document.querySelector(`#tab_Gestion_planifications .${planificationId}`)?.remove();
          planificationElement.remove();
        }
      }
    });
  }
  if (event.target.closest('.planification_collapsible')) {
    const _target = event.target.closest('.planification_collapsible');
    _target.classList.toggle("active");

    const planificationBody = _target.closest(".planification").querySelectorAll(".planification-body, .div_programDays, .graphJours");

    planificationBody.forEach(section => {
      const isVisible = section.style.overflow === "visible";
      section.style.overflow = isVisible ? "hidden" : "visible";
      section.style.maxHeight = isVisible ? "0px" : "fit-content";
    });
  }
  if (event.target.closest('.select-selected')) {
    const _target = event.target.closest('.select-selected');
    modifyWithoutSave = true;

    event.stopPropagation();
    closeAllSelect(_target);
    _target.nextElementSibling.classList.toggle("select-hide");
    _target.classList.toggle("select-arrow-active");
  }
  if (event.target.closest('.select-items div')) {
    const _target = event.target.closest('.select-items div');
    modifyWithoutSave = true;

    const select = _target.parentNode.previousSibling;
    select.innerHTML = _target.innerHTML;
    select.classList.remove(recup_class_couleur(select.classList));
    select.classList.add(recup_class_couleur(_target.classList));
    select.setAttribute("Id", _target.getAttribute("Id"));
    Array.from(_target.parentNode.getElementsByClassName("same-as-selected")).forEach((item) => {
      item.classList.remove("same-as-selected");
    });
    _target.classList.add("same-as-selected");
    const jourSemaine = _target.closest('.JourSemaine');
    if (jourSemaine) {
      MAJ_Graphique_jour(jourSemaine);
    }
    select.click();
  }
  
 
  
  if (event.target.closest('.bt_supprimer_perdiode')) {
    const _target = event.target.closest('.bt_supprimer_perdiode');
    const Divjour = _target.closest('.JourSemaine');

    _target.closest('.Periode_jour').remove();
    modifyWithoutSave = true;
    MAJ_Graphique_jour(Divjour);
  }
  if (event.target.closest('.bt_ajout_periode')) {
    const _target = event.target.closest('.bt_ajout_periode');
    const collapsibleElement = _target.closest("th").querySelector(".collapsible");
    const Divjour = _target.closest('th').querySelector('.JourSemaine');
    const DivprogramDays = _target.closest('.div_programDays');
    const Divplanification = _target.closest('.planification-body');

    collapsibleElement.classList.add("active", "cursor");
    collapsibleElement.classList.remove("no-arrow");

    const SELECT_LIST = Recup_select("planifications");
    const CMD_LIST = Recup_liste_commandes_planification();
    const Couleur = `couleur-${CMD_LIST[0].couleur}`;
    const Nom = CMD_LIST[0].Nom;
    const Id = CMD_LIST[0].Id;

    let element = SELECT_LIST.replace("#COULEUR#", Couleur)
    .replace("#VALUE#", Nom)
    .replace("#ID#", Id);

    Ajout_Periode(element, Divjour, null, null, 'heure_fixe', document.querySelector('.eqLogicAttr[data-l2key=Type_équipement]').value);

    MAJ_Graphique_jour(Divjour);
    Divjour.style.maxHeight = "fit-content";
    Divjour.style.overflow = "visible";
    DivprogramDays.style.overflow = "visible";
    DivprogramDays.style.maxHeight = "fit-content";
    Divplanification.style.overflow = "visible";
    Divplanification.style.maxHeight = "fit-content";
  }
  if (event.target.closest('.bt_copier_jour')) {
    const _target = event.target.closest('.bt_copier_jour');
    const jour = _target.closest('th').querySelector('.JourSemaine');
    const JSONCLIPBOARD = { data: [] };

    jour.querySelectorAll('.Periode_jour').forEach((_jour) => {
      const checkbox = _jour.querySelector('.checkbox_lever_coucher');
      const type_periode = checkbox.checked ? _jour.querySelector('.select_lever_coucher').value : "heure_fixe";

      const debut_periode = _jour.querySelector('.in_timepicker').value;
      const Id = _jour.querySelector('.select-selected').getAttribute("id");
      const Nom = _jour.querySelector('.select-selected span').innerHTML;
      const Couleur = recup_class_couleur(_jour.querySelector('.select-selected').classList);
      JSONCLIPBOARD.data.push({ type_periode, debut_periode, Id, Nom, Couleur });
      localStorage.setItem("JSONCLIPBOARD", JSON.stringify(JSONCLIPBOARD));
   });
  }
  if (event.target.closest('.bt_coller_jour')) {
    const _target = event.target.closest('.bt_coller_jour');
  
   const JSONCLIPBOARD = JSON.parse(localStorage.getItem("JSONCLIPBOARD"));

   
    if (!JSONCLIPBOARD) return;

    modifyWithoutSave = true;

    const Divjour = _target.closest('th').querySelector('.JourSemaine');


    Divjour.querySelectorAll('.Periode_jour').forEach((_periode) => _periode.remove());


    const SELECT_LIST = Recup_select("planifications");
    JSONCLIPBOARD.data.forEach((periode) => {
      const { type_periode, Couleur, Nom, Id, debut_periode } = periode;

      let element = SELECT_LIST.replace("#COULEUR#", Couleur)
      .replace("#VALUE#", Nom)
      .replace("#ID#", Id);

      Ajout_Periode(element, Divjour, debut_periode, null, type_periode, 
                    document.querySelector('.eqLogicAttr[data-l2key=Type_équipement]').value);
    });

    Divjour.style.overflow = "visible";
    Divjour.style.maxHeight = "fit-content";

    const collapsibleElement = _target.closest("th").querySelector(".collapsible");
    collapsibleElement.classList.add("active", "cursor");
    collapsibleElement.classList.remove("no-arrow");

    MAJ_Graphique_jour(Divjour);
  }
  if (event.target.closest('.bt_vider_jour')) {
    const _target = event.target.closest('.bt_vider_jour');
    const collapsibleElement = _target.closest("th").querySelector(".collapsible");
    const Divjour = _target.closest('th').querySelector('.JourSemaine');

    modifyWithoutSave = true;
    collapsibleElement.classList.remove("active", "cursor");
    collapsibleElement.classList.add("no-arrow");

    Divjour.style.overflow = "hidden";
    Divjour.style.maxHeight = "0";

    Divjour.querySelectorAll('.Periode_jour').forEach((_periode) => _periode.remove());

    MAJ_Graphique_jour(Divjour);
  }
  if (event.target.closest('.collapsible')) {
    const _target = event.target.closest('.collapsible');
    const Divjour = _target.closest("th").querySelector(".JourSemaine");

    _target.classList.toggle("active");

    const isVisible = Divjour.style.overflow === "visible";
    Divjour.style.overflow = isVisible ? "hidden" : "visible";
    Divjour.style.maxHeight = isVisible ? "0px" : "fit-content";
  }

});
document.getElementById('tab_planifications').addEventListener('focusout', function(event) {
  const _target = event.target.closest('.in_timepicker');
  if (_target) {
    const time = _target.value;
    const time_old = _target.getAttribute("value");
    if (time !== time_old) {
      modifyWithoutSave = true;
      _target.setAttribute("time_int", (parseInt(time.split(':')[0]) * 60) + parseInt(time.split(':')[1]));
      _target.setAttribute("value", time);

      const Divjour = _target.closest('.JourSemaine');
      triage_jour(Divjour);
      MAJ_Graphique_jour(Divjour);
    }
  }
});

document.getElementById('tab_planifications').addEventListener('change', function(event) {
  if (event.target.closest('.select_lever_coucher')) {
    const selectElement = event.target.closest('.select_lever_coucher');
    if (selectElement) {
      modifyWithoutSave = true;

      const jourDiv = selectElement.closest('.JourSemaine');
      const periodeDiv = selectElement.closest('.Periode_jour');

      let existeLever = false;
      let existeCoucher = false;

      jourDiv.querySelectorAll('.checkbox_lever_coucher').forEach((checkbox) => {
        if (checkbox.checked) {
          const autrePeriode = checkbox.closest('.Periode_jour');
          if (autrePeriode === periodeDiv) return;

          const autreSelect = autrePeriode.querySelector('.select_lever_coucher');
          if (autreSelect?.value === "lever") existeLever = true;
          if (autreSelect?.value === "coucher") existeCoucher = true;
        }
      });

      if (selectElement.value === "lever" && existeLever) {
        selectElement.value = "coucher";
      }

      if (selectElement.value === "coucher" && existeCoucher) {
        selectElement.value = "lever";
      }

      modifHeure(jourDiv,selectElement);

      triage_jour(jourDiv);
      MAJ_Graphique_jour(jourDiv);
    }

  }
  if (event.target.closest('.checkbox_lever_coucher')) {
    const checkbox = event.target.closest('.checkbox_lever_coucher');
    if (!checkbox) return;

    const jourDiv = checkbox.closest('.JourSemaine');
    const checkedBoxes = Array.from(jourDiv.querySelectorAll('.checkbox_lever_coucher')).filter(cb => cb.checked);

    if ( checkedBoxes.length > 2) {
      event.preventDefault();
      checkbox.checked = false;
      return;
    }

    setTimeout(() => {      
      if (checkbox.checked) {
        const periodeDiv = checkbox.closest('.Periode_jour');
        const select = periodeDiv.querySelector('.select_lever_coucher');
        if (!select) return;

        const jourDiv = checkbox.closest('.JourSemaine');
        let existeLever = false;
        let existeCoucher = false;

        checkedBoxes.forEach(cb => {
          const autrePeriode = cb.closest('.Periode_jour');
          if (autrePeriode === periodeDiv) return;

          const autreSelect = autrePeriode.querySelector('.select_lever_coucher');
          if (autreSelect?.value === "lever") existeLever = true;
          if (autreSelect?.value === "coucher") existeCoucher = true;
        });

        if (!existeLever) {
          select.value = "lever";
        } else if (!existeCoucher) {
          select.value = "coucher";
        }
      }

      jourDiv.querySelectorAll('.checkbox_lever_coucher').forEach(updateDisplay);
      
      modifHeure(jourDiv,checkbox.closest("div").querySelector(".select_lever_coucher"));
      triage_jour(jourDiv);
      MAJ_Graphique_jour(jourDiv);
    }, 10);

  }
});



document.getElementById('tab_gestion_heures_lever_coucher').addEventListener('change', function(event) {
  const adjustTimeForLeverCoucher = (_target, selector) => {
    _target=_target.closest(".well")

    adjustNextActionTime(
      _target.querySelector(`.Heure${selector}`).innerText,
      _target.querySelector(`.Heure${selector}Min`).value,
      _target.querySelector(`.Heure${selector}Max`).value,
      _target.querySelector(`.Heure_action_suivante_${selector}`),
    );
  };

  const toggleDaysDisplay = (selectedDay) => {
    const days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    const tabGestion = document.getElementById('tab_gestion_heures_lever_coucher');

    days.forEach(day => {
      const displayStyle = day === selectedDay ? "block" : "none";
      tabGestion.querySelector(`.${day}`).style.display = displayStyle;
    });

    tabGestion.querySelector('.bt_copier_lever_coucher').style.display = selectedDay === 'Dimanche' ? "none" : "inline-block";
  };

  const _target = event.target;

  if (_target.closest('.HeureLeverMin') || _target.closest('.HeureLeverMax')) {
    adjustTimeForLeverCoucher(_target, 'Lever');
  }

  if (_target.closest('.HeureCoucherMin') || _target.closest('.HeureCoucherMax')) {
    adjustTimeForLeverCoucher(_target, 'Coucher');
  }

  if (_target.closest('.selection_jour')) {
    toggleDaysDisplay(_target.value);
  }
});
document.getElementById('tab_gestion_heures_lever_coucher').addEventListener('click', function(event) {
  const _target = event.target.closest('.bt_copier_lever_coucher');
  if (!_target) return;

  const jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
  const tabGestion = document.getElementById('tab_gestion_heures_lever_coucher');
  let Ce_jour, jour_min;

  jours.forEach((jour, index) => {
    if (tabGestion.querySelector(`.${jour}`).style.display === "block") {
      Ce_jour = jour;
      jour_min = jours[index + 1] || null; 
    }
  });

  if (!Ce_jour || !jour_min) return; 

  let jour_trouvé = false;
  tabGestion.querySelectorAll('.in_timepicker').forEach((input) => {
    const jour_courant = input.classList[2];
    if (jour_courant === jour_min || jour_trouvé) {
      jour_trouvé = true;
      ['HeureLeverMin', 'HeureLeverMax', 'HeureCoucherMin', 'HeureCoucherMax'].forEach((classe) => {
        tabGestion.querySelector(`.${classe}.${jour_courant}`).value = 
          tabGestion.querySelector(`.${classe}.${Ce_jour}`).value;
      });
    }
  });
});

document.getElementById('tab_gestion_heures_lever_coucher').addEventListener('keydown', function(e) {
  

    if (e.target.closest('.in_timepicker')) {
      const adjustTimeForLeverCoucher = (_target, selector) => {
        _target=_target.closest(".well")
        adjustNextActionTime(
          _target.querySelector(`.Heure${selector}`).innerText,
          _target.querySelector(`.Heure${selector}Min`).value,
          _target.querySelector(`.Heure${selector}Max`).value,
          _target.querySelector(`.Heure_action_suivante_${selector}`),
        );
      };
      

       
    
      let value = e.target.value;
      let cursorPos = e.target.selectionStart; // Position actuelle du curseur
      let char = e.key;

      // Autoriser touches de contrôle (Backspace, Delete, Tab, Flèches)
      if (['Tab', 'ArrowLeft', 'ArrowRight', 'Control', 'F5'].includes(char)) {
            return;
      }
      if (['Backspace', 'Delete'].includes(char)) {
          e.target.value=''

          return;
      }
      switch (cursorPos) {
        case 0: // 🚫 Bloquer toute modification du premier chiffre si le deuxième est déjà >3
          if (value.length >= 1 && value[0] > '3' &&!/[0-1]/.test(char)) {
              e.preventDefault();
          } else if (!/[0-2]/.test(char)) { // Bloquer les valeurs supérieures à 2 en première position
              e.preventDefault();
          }
          break;
        case 1: // 🚫 Empêcher les heures supérieures à 23
          if (value[0] === '2' && !/[0-3]/.test(char)) {
            e.preventDefault(); // Bloque >23
          } else if ((value[0] === '1' || value[0] === '0') && !/[0-9]/.test(char)) {
            e.preventDefault(); // Bloque tout ce qui n’est pas 0–9
          } else {
            // 👍 L’heure semble correcte, on ajoute le caractère et le ":"
            e.target.value = value + char + ':';
            e.preventDefault();
          }


          break;
        case 3: // 🚫 Bloquer minutes hors plage 0-5
            if (!/[0-5]/.test(char)) e.preventDefault();
            break;
        case 4: // 🚫 Bloquer minutes hors plage 0-9
          if (!/[0-9]/.test(char)) e.preventDefault();
          
            
          e.target.value += char ;
          e.preventDefault();
          var jour=this.querySelector(".selection_jour").value
          console.log("vérification")
          //vérification heures min et max 
          if (e.target.classList.contains('HeureCoucherMin') && e.target.value > document.querySelector('.HeureCoucherMax.' + jour).value) {
            alert("L'heure minimale ne peut pas être supérieure à l'heure maximale !");
            e.target.value =Json_lever_coucher["Heure_coucher_min_" + jour.toLowerCase()]
          }
          if (e.target.classList.contains('HeureCoucherMax') && e.target.value < document.querySelector('.HeureCoucherMin.' + jour).value) {
            alert("L'heure maximale ne peut pas être inférieure à l'heure minimale !");
            e.target.value =Json_lever_coucher["Heure_coucher_max_" + jour.toLowerCase()]
          }
          if (e.target.classList.contains('HeureLeverMin')  && e.target.value >  document.querySelector('.HeureLeverMax.' + jour).value) {
            alert("L'heure minimale ne peut pas être supérieure à l'heure maximale !");
            e.target.value =Json_lever_coucher["Heure_lever_min_" + jour.toLowerCase()]
          }
          if (e.target.classList.contains('HeureCoucherMax')  && e.target.value <  document.querySelector('.HeureLeverMin.' + jour).value) {
            alert("L'heure maximale ne peut pas être inférieure à l'heure minimale !");
            e.target.value =Json_lever_coucher["Heure_lever_max_" + jour.toLowerCase()]
          }
          //mise à jour de l'heure de prochine action
          if (e.target.closest('.HeureLeverMin') || e.target.closest('.HeureLeverMax')) {
            adjustTimeForLeverCoucher(e.target, 'Lever');
          }  
          if (e.target.closest('.HeureCoucherMin') || e.target.closest('.HeureCoucherMax')) {
            adjustTimeForLeverCoucher(e.target, 'Coucher');
          } 
          break;
      
        default: // 🚫 Bloquer toute saisie supplémentaire           
        e.preventDefault()
            
      }
       
    }
});


document.getElementById('tab_commandes').addEventListener('click', function(event) {
  let _target;

  if (_target = event.target.closest('.select-selected')) {
    modifyWithoutSave = false;
    event.stopPropagation();
    closeAllSelect(_target);
    _target.nextSibling.classList.toggle("select-hide");
    _target.classList.toggle("select-arrow-active");
  } else if (_target = event.target.closest('.select-items div')) {
    modifyWithoutSave = true;
    const select = _target.parentNode.previousSibling;
    select.innerHTML = _target.innerHTML;
    select.classList.remove(recup_class_couleur(select.classList));
    select.classList.add(recup_class_couleur(_target.classList));
    select.setAttribute("Id", _target.getAttribute("Id"));

    const y = _target.parentNode.getElementsByClassName("same-as-selected");
    Array.from(y).forEach(item => item.classList.remove("same-as-selected"));

    _target.classList.add("same-as-selected");
    select.click();
  } else if (_target = event.target.closest('.listCmdAction')) {
    const el = _target.closest('div div').querySelector('.cmdAttr[data-l2key=commande]');
    jeedom.cmd.getSelectModal({ cmd: { type: 'action' } }, function(result) {
      el.value = result.human;
      jeedom.cmd.displayActionOption(el.value, '', function(html) {
        el.closest('div td').querySelector('.actionOptions').innerHTML = html;
      });
    });
  } else if (_target = event.target.closest('.listAction')) {
    const el = _target.closest('div div').querySelector('.cmdAttr[data-l2key=commande]');
    jeedom.getSelectActionModal({}, function(result) {
      el.value = result.human;
      jeedom.cmd.displayActionOption(el.value, '', function(html) {
        el.closest('div td').querySelector('.actionOptions').innerHTML = html;
      });
    });
  } else if (_target = event.target.closest('.tester')) {
    jeedom.cmd.execute({ id: _target.closest('.cmd').getAttribute('data-cmd_id') });
  }
});
document.getElementById('tab_commandes').addEventListener('focusout', function(event) {
  const _target = event.target.closest('.cmdAction');
  if (_target) {
    jeedom.cmd.displayActionOption(_target.value, _target.value.options, function(html) {
      _target.closest('div td').querySelector('.actionOptions').innerHTML = html;
    });
  }
});
document.getElementById('menu_tab_Gestion_planifications').addEventListener('click', function(event) {
  const textareas = document.querySelectorAll('textarea');

  textareas.forEach((textarea) => {
    textarea.style.height = `${textarea.scrollHeight}px`;
  });
});
document.getElementById('tab_Gestion_planifications').addEventListener('click', function(event) {
  let _target;

  if (_target = event.target.closest('.listCmdInfoWindow')) {
    const textarea = _target.closest('div').querySelector('textarea');

    jeedom.cmd.getSelectModal({ cmd: { type: 'info' } }, function(result) {
      textarea.value += result.human;
      textarea.style.height = `${textarea.scrollHeight}px`;

      const evaluation = textarea.closest('.GestionPlanification').querySelector('.Evaluation');
      const resultEvaluation = textarea.closest('.GestionPlanification').querySelector('.RésultatEvaluation');

      evaluation.classList.add("alert-info");
      resultEvaluation.classList.add("alert-info");

      jeedom.scenario.testExpression({
        expression: textarea.value,
        error: function(error) {
          jeedomUtils.showAlert({
            message: error.message,
            level: 'danger'
          });
        },
        success: function(data) {
          if (data.correct === 'nok') {
            evaluation.innerHTML = "Attention : il doit y avoir un souci avec l'expression";
            resultEvaluation.innerHTML = "";
            evaluation.classList.add("alert-danger");
            textarea.closest('.GestionPlanification').querySelector('.ConditionId').innerHTML = '';
            textarea.closest('.GestionPlanification').querySelector('.ConditionId').classList.remove("alert-info");
          } else {
            domUtils.ajax({
              type: "POST",
              url: "plugins/planification/core/ajax/planification.ajax.php",
              data: {
                action: "fromHumanReadable",
                expression: textarea.value
              },
              global: false,
              async: false,
              error: function(request, status, error) {
                handleAjaxError(request, status, error);
              },
              success: function(data) {
                if (data.state !== 'ok') {
                  jeedomUtils.showAlert({
                    message: data.result,
                    level: 'danger'
                  });
                  return;
                }
                const conditionId = textarea.closest('.GestionPlanification').querySelector('.ConditionId');
                conditionId.innerHTML = data.result;
                if (evaluation.innerHTML !== '') {
                  conditionId.classList.add("alert-info");
                }
              }
            });

            evaluation.innerHTML = data.evaluate;
            resultEvaluation.innerHTML = data.result;
            evaluation.classList.add("alert-info");
            evaluation.classList.remove("alert-danger");

            if (data.result) {
              resultEvaluation.classList.remove("alert-danger");
              resultEvaluation.classList.add("alert-success");
            } else {
              resultEvaluation.classList.add("alert-danger");
              resultEvaluation.classList.remove("alert-success");
            }
          }
        }
      });
    });
  }

  if (_target = event.target.closest('.bt_vider_textarea')) {
    modifyWithoutSave = true;
    let el = _target.closest('div').querySelector('input') || _target.closest('div').querySelector('textarea');
    el.value = '';

    const gestionPlanification = _target.closest('.GestionPlanification');
    gestionPlanification.querySelector('.ConditionId').innerHTML = '';
    gestionPlanification.querySelector('.Evaluation').innerHTML = '';
    gestionPlanification.querySelector('.RésultatEvaluation').innerHTML = '';

    const textareas = document.querySelectorAll('textarea');
    textareas.forEach((textarea) => {
      textarea.style.height = `${textarea.scrollHeight}px`;
    });
  }
});
document.getElementById('tab_Gestion_planifications').addEventListener('keypress', function(event) {
  const _target = event.target.closest('textarea');
  if (_target) {
    modifyWithoutSave = true;
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach((textarea) => {
      textarea.style.height = `${textarea.scrollHeight}px`;
    });
  }
});
document.getElementById('tab_Gestion_planifications').addEventListener('change', function(event) {
  const textareas = document.querySelectorAll('textarea');

  textareas.forEach((textarea) => {
    textarea.style.height = `${textarea.scrollHeight}px`;
  });
});
document.getElementById('tab_Ouvrants').addEventListener('click', function(event) {
  let _target;

  if (_target = event.target.closest('.ajoutOuvrant')) {
    ajoutOuvrant();
  }

  if (_target = event.target.closest('.bt_removeAction')) {
    const type = _target.getAttribute('data-type');
    const closestElement = _target.closest(`.${type}`);
    if (closestElement) {
      closestElement.remove();
    }
  }

  if (_target = event.target.closest('.listCmdInfoWindow')) {
    let el = _target.closest('div').querySelector('input') || _target.closest('div').querySelector('textarea');

    jeedom.cmd.getSelectModal({ cmd: { type: 'info' } }, function(result) {
      if (el.tagName.toLowerCase() === 'textarea') {
        el.value += result.human;
      } else {
        el.value = result.human;
      }
    });
  }
});
document.getElementById('tab_Paramètres').addEventListener('click', function(event) {
  let _target;

  if (_target = event.target.closest('.expressionAttr[data-l2key="Type_équipement_pilote"]')) {
    modifyWithoutSave = true;
    if (_target.value !== '') {
     
      if (_target.options[_target.selectedIndex].text !== 'Aucun') {
        document.querySelector('#tab_Paramètres .options_type_équipement_pilote').style.display = 'block';

        let options = '';
        jeedom.eqLogic.byType({
          type: _target.options[_target.selectedIndex].text,
          error: function(error) {
            jeedomUtils.showAlert({ message: error.message, level: 'danger' });
          },
          success: function(eqLogics) {
            modifyWithoutSave = false;

            eqLogics.forEach(function(eqLogic) {
              options += `<option id="#${eqLogic.id}#">{{${eqLogic.name}}}</option>`;
            });
            document.querySelector('#tab_Paramètres .expressionAttr[data-l2key="Equipement_pilote"]').innerHTML = options;
          }
        });
      } else {
        document.querySelector('#tab_Paramètres .options_type_équipement_pilote').style.display = 'none';
        document.querySelector('#tab_Paramètres .expressionAttr[data-l2key="Equipement_pilote"]').innerHTML = '';
      }
    } else {
      if (_target.options[_target.selectedIndex] === undefined) {
        document.querySelector('#tab_Paramètres .options_type_équipement_pilote').style.display = 'none';
      } else if (_target.options[_target.selectedIndex].text !== 'Aucun') {
        document.querySelector('#tab_Paramètres .options_type_équipement_pilote').style.display = 'block';
      } else {
        document.querySelector('#tab_Paramètres .options_type_équipement_pilote').style.display = 'none';
      }
    }
  }

  if (_target = event.target.closest('.btn_numeric')) {
    const el = _target.closest('.numeric-updown').querySelector('input');
    if (_target.classList.contains('up') && parseInt(el.value) < parseInt(el.max)) {
      modifyWithoutSave = true;
      el.value = parseInt(el.value) + 1;
      el.setAttribute("value", el.value);
    }
    if (_target.classList.contains('down') && parseInt(el.value) > parseInt(el.min)) {
      modifyWithoutSave = true;
      el.value = parseInt(el.value) - 1;
      el.setAttribute("value", el.value);
    }
  }
});
document.getElementById('tab_commandes').addEventListener('dblclick', function(event) {
  event.preventDefault();
  event.stopPropagation();
});
document.getElementById('tab_Paramètres').addEventListener('dblclick', function(event) {
  event.preventDefault();
  event.stopPropagation();
});

set_sortable(document.getElementById('div_planifications'),'.planification','.planification-body')
set_sortable(document.getElementById('div_GestionPlanifications'),'.GestionPlanification','.input-group')
set_sortable(document.getElementById('table_infos'),'tbody','')
set_sortable(document.getElementById('table_actions'),'tbody','.input-group,custom-select')
afficherSectionsParType(typesEquipements);

function afficherSectionsParType(types) {
  types.forEach(type => {
    const container = document.querySelector(`.eqLogicThumbnailContainer.${type}`);
    const sidenav = document.querySelector(`.bs-sidenav.${type}`);
    const cards = document.querySelectorAll(`div .${type} .eqLogicDisplayCard`);
    if (cards.length !== 0) {
      if (container) container.style.display = 'block';
      if (sidenav) sidenav.style.display = 'block';
    }
  });
}
function updateDisplay(checkbox) {
  const periodeDiv = checkbox.closest('.Periode_jour');
  const select = periodeDiv.querySelector('.select_lever_coucher');
  const timepicker = periodeDiv.querySelector('.in_timepicker');
  const boutonTimepicker = periodeDiv.querySelector('.bt_afficher_timepicker_planification');

  if (checkbox.checked) {
    select.style.display = 'block';
    timepicker.style.display = 'none';
    boutonTimepicker.style.display = 'none';
  } else {
    select.style.display = 'none';
    timepicker.style.display = 'block';
    boutonTimepicker.style.display = 'block';
  }

}
function modifHeure(jourDiv,selectElement){
  const periodeDiv = selectElement.closest('.Periode_jour');
  const timepicker = periodeDiv.querySelector('.in_timepicker');
  const joursMapLever = {
    "Lundi": ".Heure_action_suivante_Lever.Lundi",
    "Mardi": ".Heure_action_suivante_Lever.Mardi",
    "Mercredi": ".Heure_action_suivante_Lever.Mercredi",
    "Jeudi": ".Heure_action_suivante_Lever.Jeudi",
    "Vendredi": ".Heure_action_suivante_Lever.Vendredi",
    "Samedi": ".Heure_action_suivante_Lever.Samedi",
    "Dimanche": ".Heure_action_suivante_Lever.Dimanche"
  };

  const joursMapCoucher = {
    "Lundi": ".Heure_action_suivante_Coucher.Lundi",
    "Mardi": ".Heure_action_suivante_Coucher.Mardi",
    "Mercredi": ".Heure_action_suivante_Coucher.Mercredi",
    "Jeudi": ".Heure_action_suivante_Coucher.Jeudi",
    "Vendredi": ".Heure_action_suivante_Coucher.Vendredi",
    "Samedi": ".Heure_action_suivante_Coucher.Samedi",
    "Dimanche": ".Heure_action_suivante_Coucher.Dimanche"
  };

  const joursMap = selectElement.value === "lever" ? joursMapLever : joursMapCoucher;

  let heureSuivante = "";
  for (const jour in joursMap) {
    if (jourDiv.classList.contains(jour)) {
      const heureElement = document.querySelector(`#tab_gestion_heures_lever_coucher ${joursMap[jour]}`);
      heureSuivante = heureElement?.innerText || "";
      break;
    }
  }

  if (selectElement.value !== 'lever') {
    timepicker.selectedIndex = 1;
  }

  timepicker.setAttribute("oldvalue", timepicker.getAttribute("value"));
  const [heures, minutes] = heureSuivante.split(':').map(Number);
  timepicker.setAttribute("time_int", (heures * 60) + minutes);
  timepicker.setAttribute("value", heureSuivante);
}
function set_sortable(Element_id,draggable,filter){
  new Sortable(Element_id, {
    delay: 500,
    draggable: draggable,
    direction: 'vertical',
    filter: filter,
    preventOnFilter: false,
    chosenClass: 'dragSelected',
    animation: 150,
    ghostClass: 'blue-background-class',
    onUpdate: function(evt) {
      jeeFrontEnd.modifyWithoutSave = true
    }
  })
}
function adjustNextActionTime(Heure_lever_coucher,nouvelle_heure_début,nouvelle_heure_fin,nextActionTimeElement) { 
  const date_heure_début = new Date('1970-01-01T' + nouvelle_heure_début);
  const date_heure_fin = new Date('1970-01-01T' + nouvelle_heure_fin);
  const Date_heure_lever_coucher = new Date('1970-01-01T' + Heure_lever_coucher);
  nextActionTimeElement.innerText = Heure_lever_coucher; 
  if (date_heure_début > Date_heure_lever_coucher) { 
    nextActionTimeElement.innerText = nouvelle_heure_début; 
  } 

  if (date_heure_fin < Date_heure_lever_coucher) { 
    nextActionTimeElement.innerText = nouvelle_heure_fin; 
  } 

}
function convertTimeToInt(time) {
  const [hours, minutes] = time.split(':').map(Number);
  return (hours * 60) + minutes;
}
function closeAllSelect(elmnt) {
  const x = document.getElementsByClassName("select-items");
  const y = document.getElementsByClassName("select-selected");
  const arrNo = [];

  for (let i = 0; i < y.length; i++) {
    if (elmnt === y[i]) {
      arrNo.push(i);
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }

  for (let i = 0; i < x.length; i++) {
    if (!arrNo.includes(i)) {
      x[i].classList.add("select-hide");
    }
  }
}
function recup_class_couleur(classes) {
  let class_color = "erreur";
  try {
    for (let classe in classes) {
      if (classes[classe].includes("couleur")) {
        class_color = classes[classe];
        break;
      }
    }
  } catch (err) {
    // Ignorer l'erreur
  }

  return class_color;
}
function ajoutOuvrant() {
  const div = `
            <div class="Ouvrant">
                <div class="form-group">
                    <label class="col-sm-1 control-label">{{Ouvrant}}</label>
                    <div class="col-sm-11">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <a class="btn btn-default bt_removeAction roundedLeft" data-type="Ouvrant">
                                    <i class="fas fa-minus-circle bt_removeAction" data-type="Ouvrant"></i>
                                </a>
                            </span>
                            <input class="expressionAttr form-control cmdInfo" data-l1key="Ouvrants" data-l2key="Commande" />
                            <span class="input-group-btn">
                                <a class="btn btn-default listCmdInfoWindow roundedRight">
                                    <i class="fas fa-list-alt"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">{{Éteindre si ouvert plus de}} <sub>(min.)</sub></label>
                    <div class="col-sm-1">
                        <input class="expressionAttr form-control cmdInfo" data-l1key="Ouvrants" placeholder=1 data-l2key="Délai_ouverture" />
                    </div>
                    <label class="col-sm-2 control-label">{{Rallumer si fermé depuis}} <sub>(min.)</sub></label>
                    <div class="col-sm-1">
                        <input class="expressionAttr form-control cmdInfo" data-l1key="Ouvrants" placeholder=1 data-l2key="Délai_fermeture" />
                    </div>
                    <label class="col-sm-2 control-label">{{Envoyer une alerte}}</label>
                    <div class="col-sm-1">
                        <input type="checkbox" class="expressionAttr" data-l1key="Ouvrants" data-l2key="Alerte" />
                    </div>
                </div>
            </div>
        `;
  document.querySelector('#div_ouvrants').insertAdjacentHTML('beforeend', div);
}
function AjoutGestionPlanification(planification) {
  if (init(planification.nom) === '') return;
  if (init(planification.Id) === '') return;

  const div = `
            <div class="GestionPlanification ${planification.Id}" style="padding: 20px; margin-bottom: 10px; background-color: rgb(var(--bg-color));">
                <div class="form-group">
                    <div class="input-group">
                        <label class="col-sm-12 Nom_planification">${planification.nom}</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-btn" style="vertical-align: top !important;">
                            <a class="btn btn-default bt_vider_textarea roundedLeft" data-type="GestionPlanification">
                                <i class="fas fa-trash bt_vider_textarea" data-type="GestionPlanification"></i>
                            </a>
                        </span>
                        <textarea class="expressionAttr form-control cmdInfo" data-l1key="Gestion_planifications" data-l2key="Conditions" 
                            PlanificationId="${planification.Id}" NomPlanification="${planification.nom}" 
                            style="height: 32px !important; overflow: hidden !important; resize: none !important;"></textarea>
                        <span class="input-group-btn" style="vertical-align: top !important;">
                            <a class="btn btn-default listCmdInfoWindow roundedRight">
                                <i class="fas fa-list-alt"></i>
                            </a>
                        </span>
                    </div>
                    <div class="input-group" style="padding-left: 32px;">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="expressionAttr" data-l1key="Gestion_planifications" data-l2key="Stop" />
                            Arrêter si les conditions sont remplies
                        </label>
                    </div>
                    <div class="input-group" style="padding-left: 32px;">
                        <label>Test de l'expression:<label class='ConditionId' style="margin: 0px !important; width: auto; padding: 0px !important;"></label></label>
                    </div>
                    <div class="input-group" style="padding-left: 32px;">
                        <label>Evaluation de l'expression:<label class='Evaluation' style="margin: 0px !important; width: auto; padding: 0px !important;"></label></label>
                    </div>
                    <div class="input-group" style="padding-left: 32px;">
                        <label>Resultat de l'expression:<label class='RésultatEvaluation'></label></label>
                    </div>
                </div>
            </div>
        `;

  document.querySelector('#div_GestionPlanifications').insertAdjacentHTML('beforeend', div);
  const textarea = document.querySelector('#div_GestionPlanifications').lastElementChild.querySelector('textarea');
  textarea.addEventListener('input', function() {
    textarea.style.height = `${textarea.scrollHeight}px`;
    const evaluation = textarea.closest('.GestionPlanification').querySelector('.Evaluation');
    const resultEvaluation = textarea.closest('.GestionPlanification').querySelector('.RésultatEvaluation');
    const conditionId = textarea.closest('.GestionPlanification').querySelector('.ConditionId');

    if (textarea.value !== '') {
      evaluation.classList.add("alert-info");
      resultEvaluation.classList.add("alert-info");

      jeedom.scenario.testExpression({
        expression: textarea.value,
        error: function(error) {
          jeedomUtils.showAlert({
            message: error.message,
            level: 'danger'
          });
        },
        success: function(data) {
          if (data.correct === 'nok') {
            evaluation.innerHTML = "Attention : il doit y avoir un souci avec l'expression";
            resultEvaluation.innerHTML = "";
            evaluation.classList.add("alert-danger");
            conditionId.innerHTML = '';
            conditionId.classList.remove("alert-info");
          } else {
            domUtils.ajax({
              type: "POST",
              url: "plugins/planification/core/ajax/planification.ajax.php",
              data: {
                action: "fromHumanReadable",
                expression: textarea.value
              },
              global: false,
              async: false,
              error: function(request, status, error) {
                handleAjaxError(request, status, error);
              },
              success: function(data) {
                if (data.state !== 'ok') {
                  jeedomUtils.showAlert({
                    message: data.result,
                    level: 'danger'
                  });
                  return;
                }
                conditionId.innerHTML = data.result;
                if (evaluation.innerHTML !== '') {
                  conditionId.classList.add("alert-info");
                }
              }
            });
            evaluation.innerHTML = data.evaluate;
            resultEvaluation.innerHTML = data.result;
            evaluation.classList.add("alert-info");
            evaluation.classList.remove("alert-danger");
            resultEvaluation.classList.toggle("alert-success", data.result);
            resultEvaluation.classList.toggle("alert-danger", !data.result);
          }
        }
      });
    } else {
      evaluation.innerHTML = "";
      resultEvaluation.innerHTML = "";
      conditionId.innerHTML = '';
      evaluation.classList.remove("alert-danger", "alert-info");
      resultEvaluation.classList.remove("alert-danger", "alert-info");
      conditionId.classList.remove("alert-info");
    }
  });

  textarea.addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
    }
  });
}
function Ajoutplanification(_planification) {
  const JOURS = ['{{Lundi}}', '{{Mardi}}', '{{Mercredi}}', '{{Jeudi}}', '{{Vendredi}}', '{{Samedi}}', '{{Dimanche}}'];
  if (init(_planification.nom) === '') return;
  if (init(_planification.Id) === '') { _planification.Id = jeedomUtils.uniqId(); }
  const random = Math.floor((Math.random() * 1000000) + 1);

  let div = `
            <div class="planification panel panel-default" Id="${_planification.Id}" style="border-color: var(--logo-primary-color) !important;">
                <div class="panel-heading">
                    <h3 class="panel-title" style="padding-bottom: 4px;">
                        <div class="planification_collapsible cursor" style="height:32px; padding-top: 10px; width: calc(100% - 345px);">
                            <span class="nom_planification">${_planification.nom}</span>
                            <span class="input-group-btn pull-right" style="top: -5px!important;">
                                <a class="btn btn-sm bt_renommer_planification btn-warning roundedLeft"><i class="fas fa-copy"></i> {{Renommer}}</a>
                                <a class="btn btn-sm bt_dupliquer_planification btn-primary roundedLeft"><i class="fas fa-copy"></i> {{Dupliquer}}</a>
                                <a class="btn btn-sm bt_appliquer_planification btn-success" title="Appliquez la planification maintenant"><i class="fas fa-check-circle"></i> {{Appliquer}}</a>
                                <a class="btn btn-sm bt_supprimer_planification btn-danger roundedRight"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
                            </span>
                        </div>
                    </h3>
                </div>
                <div class="planification-body" style="background-color: rgb(var(--defaultBkg-color)) !important;">
                    <form class="form-horizontal" role="form">
                        <div class="div_programDays" style="width:100%;">
                            <table style="width:100%;">
                                <tr>
        `;

  JOURS.forEach(jour => {
    div += `
                <th style="width:14%; text-align: center; vertical-align: top;">
                    <div class="collapsible no-arrow">${jour}</div>
                    <div class="input-group" style="display:inline-flex;">
                        <span>
                            <i class="fa fa-plus-circle cursor bt_ajout_periode" title="{{Ajouter une période}}"></i>
                            <i class="fas fa-sign-out-alt cursor bt_copier_jour" title="{{Copier le jour}}"></i>
                            <i class="fas fa-sign-in-alt cursor bt_coller_jour" title="{{Coller le jour}}"></i>
                            <i class="fa fa-minus-circle cursor bt_vider_jour" title="{{Vider le jour}}"></i>
                        </span>
                    </div>
                    <br>
                    <div class="JourSemaine ${jour}" style="width:100%; float:left;"></div>
                </th>
            `;
  });

  div += `
                                </tr>
                            </table>
                        </div>
                        <div class="graphJours" style="width:100%; clear:left;">
                            <br>

                            <div style="width:calc(100% - 20px); display:inline-flex;">
                                <div class="nom_graphique" style="width:80px; display:inline-block;"></div>
                                <div style="width: 25%; height:18px; display:inline-block;">00:00</div>
                                <div style="width: 25%; height:18px; display:inline-block; position:inherit;">06:00</div>
                                <div style="width: 25%; height:18px; display:inline-block; position:inherit;">12:00</div>
                                <div style="width: 25%; height:18px; display:inline-block;">18:00</div>
                            </div>
        `;

  JOURS.forEach(jour => {
    div += `
                            <div style="width:calc(100% - 20px);display:inline-flex;margin-top: 1px;"">
                                <div class="nom_graphique" style="width:80px; display:inline-block;">${jour}</div>
                                <div class="graphique_jour_${jour}" style="width:calc(100% - 20px);height: 22px;margin: 1px;display: inline-flex;"></div>
                            </div>
            `;
  });

  div += `
                        </div>
                    </form>
                </div>
            </div>
        `;

  document.getElementById('div_planifications').insertAdjacentHTML('beforeend', div);
}
function Ajout_Periode(PROGRAM_MODE_LIST, Div_jour, time = null, Mode_periode = null, Type_periode = false, type_eqlogic) {
  const Periode_jours = Div_jour.querySelectorAll('.Periode_jour');
  let prochain_debut = "00:00";

  if (Periode_jours.length > 0) {
    const periode_precedente = Periode_jours[Periode_jours.length - 1];
    const dernier_debut = periode_precedente.querySelector('.in_timepicker').value;

    let prochain_debut_int = parseInt(dernier_debut.split(':')[0]) * 60 + parseInt(dernier_debut.split(':')[1]) + 1;
    let heures_str = ("0" + Math.trunc(prochain_debut_int / 60)).slice(-2);
    let minutes_str = ("0" + (prochain_debut_int % 60)).slice(-2);
    prochain_debut = `${heures_str}:${minutes_str}`;

    if (time === null) {
      const time_int = parseInt(dernier_debut.split(':')[0]) * 60 + parseInt(dernier_debut.split(':')[1]);

      if (time_int === 1439) {
        time = "";
      } else if (time_int >= 1425) {
        time = "23:59";
      } else if (dernier_debut === "") {
        time = "";
      } else {
        time = prochain_debut;
      }
    } else if (Mode_periode === null) {
      const last_timeStart = parseInt(dernier_debut.split(':')[0]) * 60 + parseInt(dernier_debut.split(':')[1]);
      const heure_debut = parseInt(time.split(':')[0]) * 60 + parseInt(time.split(':')[1]);
      if (heure_debut <= last_timeStart) {
        time = prochain_debut;
      }
    }
  }

  if (time === "" && Type_periode === "lever") {
    time = document.querySelector(`#tab_gestion_heures_lever_coucher .Heure_action_suivante_Lever.${Div_jour.classList[1]}`).innerText;
  } else if (time === "" && Type_periode === "coucher") {
    time = document.querySelector(`#tab_gestion_heures_lever_coucher .Heure_action_suivante_Coucher.${Div_jour.classList[1]}`).innerText;
  } else if (time === null) {
    time = "00:00";
  }

  const time_int = parseInt(time.split(':')[0]) * 60 + parseInt(time.split(':')[1]);
  let div = `
            <div class="Periode_jour periode${Periode_jours.length + 1} input-group" style="width:100% !important; line-height:1.4px !important; display: inline-grid;">
                <div style="display:flex;">
                    <input style="width:28px !important; font-size:20px!important; vertical-align:middle; padding:5px; margin:0px;" 
                        title="activer/désactiver heure lever/coucher de soleil" class="checkbox_lever_coucher checkbox form-control input-sm cursor" type="checkbox">
                    <select class="select_lever_coucher select form-control input-sm" 
                            style="background-color: var(--form-bg-color) !important; width:80%!important; display:none; text-align:center;">
        `;

  if (Type_periode === "coucher") {
    div += `<option value="lever">Lever de soleil</option><option value="coucher" selected>Coucher de soleil</option>`;
  } else if (Type_periode === "lever") {
    div += `<option value="lever" selected>Lever de soleil</option><option value="coucher">Coucher de soleil</option>`;
  } else {
    div += `<option value="lever">Lever de soleil</option><option value="coucher">Coucher de soleil</option>`;
  }

  div += `
                    </select>
                    <input class="in_timepicker form-control input-sm" time_int="${time_int}" value="${time}" 
                        style="padding:0px!important; text-align:center; width:80%!important; display:inline-block; position:relative;">
                    <a class="btn btn-default bt_afficher_timepicker_planification btn-sm" 
                    style="background-color: var(--form-bg-color) !important; padding:5px;"><i class="icon far fa-clock"></i></a>
                    <a class="btn btn-default bt_supprimer_perdiode btn-sm" style="padding:5px;" title="Supprimer cette période">
                        <i class="fa fa-minus-circle"></i>
                    </a>
                </div>
                <div class="custom-select">
                    ${PROGRAM_MODE_LIST}
                </div>
            </div>
        `;

  Div_jour.insertAdjacentHTML('beforeend', div);
  const nouvelle_periode = Div_jour.querySelectorAll(".Periode_jour")[Periode_jours.length];

  if (Mode_periode !== null) {
    nouvelle_periode.querySelectorAll('.select-items div').forEach((item) => {
      if (item.id === nouvelle_periode.querySelector('.select-selected').getAttribute("id")) {
        item.classList.add('same-as-selected');
      }
    });
  } else {
    nouvelle_periode.querySelector('.select-items div').classList.add('same-as-selected');
  }

  if (type_eqlogic !== "Volet") {
    nouvelle_periode.querySelector('.checkbox_lever_coucher').style.display = 'none';
    nouvelle_periode.querySelector('.in_timepicker').style.width = 'calc(100% - 28px)!important';
  }

  if (Type_periode !== 'heure_fixe') {
    nouvelle_periode.querySelector('.checkbox_lever_coucher').setAttribute('checked', true);
    nouvelle_periode.querySelector('.in_timepicker').style.display = 'none';
    nouvelle_periode.querySelector('.bt_afficher_timepicker_planification').style.display = 'none';
    nouvelle_periode.querySelector('.select_lever_coucher').style.display = 'block';
  }

  Div_jour.closest("th").querySelector(".collapsible").classList.remove("no-arrow");
  Div_jour.closest("th").querySelector(".collapsible").classList.add("cursor");
}
function triage_jour(Div_jour) {
  let div = "";

  // Ajout automatique d’un identifiant unique si absent
  let index = 0;
  Div_jour.querySelectorAll('.Periode_jour').forEach(p => {
    if (!p.hasAttribute('data-id')) {
      p.setAttribute('data-id', `periode_${Date.now()}_${index++}`);
    }
  });

  // Sauvegarde des états
  const états = Array.from(Div_jour.querySelectorAll('.Periode_jour')).map(p => ({
    id: p.getAttribute('data-id'),
    checked: p.querySelector('.checkbox_lever_coucher')?.checked,
    select: p.querySelector('.select_lever_coucher')?.value
  }));

  // Tri des périodes
  const triées = Array.from(Div_jour.querySelectorAll(".in_timepicker"))
    .map(période => ({
      val: parseInt(période.getAttribute("time_int"), 10),
      el: période.closest(".Periode_jour")
    }))
    .sort((a, b) => a.val - b.val);

  triées.forEach(map => {
    div += map.el.outerHTML;
  });

  Div_jour.innerHTML = div;

  // Restauration des états
  états.forEach(({ id, checked, select }) => {
    const période = Div_jour.querySelector(`.Periode_jour[data-id="${id}"]`);
    if (période) {
      const cb = période.querySelector('.checkbox_lever_coucher');
      if (cb) cb.checked = checked;

      const sel = période.querySelector('.select_lever_coucher');
      if (sel) sel.value = select;
    }
  });
}
function MAJ_Graphique_jour(Div_jour) {
  const graphDiv = Div_jour.closest('.planification-body').querySelector('.graphique_jour_' + Div_jour.getAttribute("class").split(' ')[1]);

  graphDiv.innerHTML = '';
  const Periode_jour = Div_jour.querySelectorAll('.Periode_jour');

  for (let i = 0; i < Periode_jour.length; i++) {
    const isFirst = (i === 0);
    const isLast = (i === Periode_jour.length - 1);
    const periode = Periode_jour[i];
    const debut_periode = periode.querySelector('.in_timepicker').getAttribute("value");
    const heure_debut = (parseInt(debut_periode.split(':')[0]) * 60) + parseInt(debut_periode.split(':')[1]);

    let heure_fin, delta, width, class_periode, mode, nouveau_graph, fin_periode;

    if (isFirst && heure_debut !== 0) {
      heure_fin = heure_debut;
      delta = heure_fin;
      width = (delta * 100) / 1440;
      class_periode = "";
      mode = "Aucun";
      nouveau_graph = `
                    <div class="graph ${class_periode}" style="width:${width}%; height:20px; display:inline-block;">
                        <span class="tooltiptext ${class_periode}">${debut_periode} - 23:59<br>${mode}</span>
                    </div>`;
      graphDiv.innerHTML += nouveau_graph;
    }

    if (isLast) {
      heure_fin = 1439;
      fin_periode = "23:59";
    } else {
      fin_periode = Periode_jour[i + 1].querySelector('.in_timepicker').getAttribute("value");
      heure_fin = (parseInt(fin_periode.split(':')[0]) * 60) + parseInt(fin_periode.split(':')[1]);
    }

    delta = heure_fin - heure_debut;
    width = (delta * 100) / 1440;
    class_periode = recup_class_couleur(
      periode.querySelector('.select-selected').getAttribute('class').split(' ')
    );
    mode = periode.querySelector('.select-selected').innerHTML;
    nouveau_graph = `
                <div class="graph ${class_periode}" style="width:${width}%; height: calc(100%);; display:inline-block;">
                    <span class="tooltiptext ${class_periode}">${debut_periode} - ${fin_periode}<br>${mode}</span>
                </div>`;
    graphDiv.innerHTML += nouveau_graph;
  }
}
function Recup_select(type_) {
  let SELECT = "";

  domUtils.ajax({
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
      if (data.state !== 'ok') {
        jeedomUtils.showAlert({ message: data, level: 'danger' });
        SELECT = "";
      } else {
        SELECT = data.result;
      }
    }
  });

  return SELECT;
}
function Recup_liste_commandes_planification() {
  let COMMANDE_LIST = [];

  domUtils.ajax({
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
      if (data.state !== 'ok') {
        jeedomUtils.showAlert({ message: data, level: 'danger' });
        COMMANDE_LIST = [];
      } else {
        COMMANDE_LIST = data.result;
      }
    }
  });

  return COMMANDE_LIST;
}
function printEqLogic(_eqLogic) {
  // Masquer les éléments spécifiques
[".bt_image_défaut", ".Volet", ".Chauffage", ".Prise", ".PAC"].forEach(selector => {
  document.querySelectorAll(selector).forEach(block => {
      block.style.display = (selector === '.' + _eqLogic.configuration.Type_équipement) ? 'block' : 'none';
  });
});
// Réinitialiser le contenu de certains div
["div_planifications", "div_GestionPlanifications", "div_ouvrants"].forEach(id => {
  document.getElementById(id).innerHTML = '';
});
document.getElementById("menu_tab_gestion").classList.add("hidden");

let img = _eqLogic.configuration.Chemin_image || "plugins/planification/core/img/autre.png";

// Initialize variables
const nom_planification_erreur = [];
const SELECT_LIST = Recup_select("planifications");
const CMD_LIST = Recup_liste_commandes_planification();

const eqLogicConfig = _eqLogic.configuration;
document.querySelector('#tab_eqlogic .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').value = eqLogicConfig.Duree_mode_manuel_par_defaut || 0;
document.querySelector('#tab_eqlogic .eqLogicAttr[data-l2key=affichage_heure]').checked = eqLogicConfig.affichage_heure === "1";

if (_eqLogic.configuration.Type_équipement === 'PAC') { 
  const eqConfig = _eqLogic.configuration;
  const imgPath = 'plugins/planification/core/img/pac.png';
  img = eqConfig.Chemin_image || imgPath;

  // Gérer l'affichage de l'image
  document.querySelector(".bt_image_défaut").style.display = (img === imgPath) ? 'none' : 'block';

  // Mettre à jour les champs PAC
 
  document.querySelector('#tab_eqlogic .PAC .eqLogicAttr[data-l2key=Temperature_ambiante_id]').value = eqConfig.Temperature_ambiante_id || '';
  document.querySelector('#tab_eqlogic .PAC .eqLogicAttr[data-l2key=Mode_id]').value = eqConfig.Mode_id || '';

  // Afficher la section alias si Mode_id est défini
  document.querySelector('#tab_eqlogic .alias').style.display = eqConfig.Mode_id ? 'block' : 'none';
}
  

if (_eqLogic.configuration.Type_équipement === 'Volet') {
  const eqConfig = _eqLogic.configuration;

  // AJAX request for sunrise/sunset data
  domUtils.ajax({
      type: "POST",
      url: "plugins/planification/core/ajax/planification.ajax.php",
      data: { action: "Recup_infos_lever_coucher_soleil", id: _eqLogic["id"] },
      dataType: 'json',
      global: false,
      async: false,
      error: handleAjaxError,
      success: (data) => {
          if (data.state !== 'ok') {
              jeedomUtils.showAlert({ message: data.result, level: 'danger' });
              return;
          }
          if (!data.result) {
              jeedomUtils.showAlert({
                  message: "Pour utiliser la fonction lever/coucher de soleil, veuillez enregistrer les coordonnées GPS (latitude et longitude) dans la configuration de jeedom.",
                  level: 'warning'
              });
              return;
          }
          Json_lever_coucher=data.result
          // Update DOM with result values
          const updateElements = (selector, key) => {
              document.querySelectorAll(selector).forEach(element => {
              
                  if (key == "Lever_soleil" ||key == "Coucher_soleil"){
                    element[selector.includes("Min") || selector.includes("Max") ? 'value' : 'innerText'] = data.result[key];

                  }else{
                 
                    element[selector.includes("Min") || selector.includes("Max") ? 'value' : 'innerText'] = data.result[key + element.classList[2].toLowerCase()];
                  
                    
                  }
                
              });
          };         
              updateElements('#tab_gestion_heures_lever_coucher .HeureLever' , "Lever_soleil");
              updateElements('#tab_gestion_heures_lever_coucher .HeureCoucher', "Coucher_soleil");
              updateElements('#tab_gestion_heures_lever_coucher .Heure_action_suivante_Lever' , "Heure_action_suivante_lever_");
              updateElements('#tab_gestion_heures_lever_coucher .Heure_action_suivante_Coucher' , "Heure_action_suivante_coucher_");
              updateElements('#tab_gestion_heures_lever_coucher .HeureLeverMin' , "Heure_lever_min_");
              updateElements('#tab_gestion_heures_lever_coucher .HeureLeverMax', "Heure_lever_max_");
              updateElements('#tab_gestion_heures_lever_coucher .HeureCoucherMin' , "Heure_coucher_min_");
              updateElements('#tab_gestion_heures_lever_coucher .HeureCoucherMax' , "Heure_coucher_max_");
        
          

        
      }
  });

  // Show menu tab
  document.getElementById("menu_tab_gestion").classList.remove("hidden");

  // Image logic
  const imgPath = "plugins/planification/core/img/volet.png";
   img = _eqLogic.configuration.Chemin_image || imgPath;
  document.querySelector(".bt_image_défaut").style.display = (img || imgPath) === imgPath ? 'none' : 'block';

  // Update values based on configuration
  const configKeys = ['Alias_Ouvert', 'Alias_My', 'Alias_Ferme', 'Niveau_batterie_gauche_id', 'Niveau_batterie_droite_id', 'Etat_fenêtre_gauche_id', 'Etat_fenêtre_droite_id'];
  configKeys.forEach(key => {
      document.querySelector(`#tab_eqlogic .${eqConfig.Type_équipement} .eqLogicAttr[data-l2key=${key}]`).value =eqConfig[key] !== undefined ? eqConfig[key] : ''
  });

  if (eqConfig.etat_id) {
      document.querySelector('#tab_eqlogic .Volet .alias').style.display = 'block';
  }

  // Manage window type
  const isBaie = eqConfig.Type_fenêtre === 'baie';
  document.getElementById(isBaie ? 'baie' : 'fenêtre').checked = true;
  document.querySelector(`#tab_eqlogic .${eqConfig.Type_équipement} .sens_ouverture`).style.display = isBaie ? 'block' : 'none';
  document.querySelector('#tab_eqlogic .Volet fieldset legend').innerText = isBaie ? "Détecteur d'ouverture gauche" : "Détecteur d'ouverture";

  // Manage opening direction
  const direction = eqConfig.Sens_ouverture || 'gauche';
  document.getElementById(direction).checked = true;
  document.querySelector('#tab_eqlogic .Volet .ouverture_gauche').style.display = direction === 'droite' ? 'none' : 'block';
  document.querySelector('#tab_eqlogic .Volet .ouverture_droite').style.display = ['droite', 'gauche-droite'].includes(direction) ? 'block' : 'none';
}
  
if (_eqLogic.configuration.Type_équipement === 'Prise') {
  const eqConfig = _eqLogic.configuration;

  // Handle image display
  const imgPath = "plugins/planification/core/img/prise.png";
  img = eqConfig.Chemin_image || imgPath;
  document.querySelector(".bt_image_défaut").style.display = (img === imgPath) ? 'none' : 'block';

  // Toggle alias visibility based on etat_id
  document.querySelector(`#tab_eqlogic .${eqConfig.Type_équipement} .alias`).style.display = eqConfig.etat_id ? 'block' : 'none';
  ['etat_id', 'Alias_On', 'Alias_Off'].forEach(key => {
    
    document.querySelector(`#tab_eqlogic .${eqConfig.Type_équipement} .eqLogicAttr[data-l2key=${key}]`).value = 
    eqConfig[key] !== 'undefined' || undefined ? eqConfig[key] : '';

      
  });
}
  
  if (_eqLogic.configuration.Type_équipement === 'Chauffage') {
    const eqConfig = _eqLogic.configuration;

    // Handle image display logic
    const imgPath = "plugins/planification/core/img/chauffage.png";
    img = eqConfig.Chemin_image || imgPath;
    document.querySelector(".bt_image_défaut").style.display = (img === imgPath) ? 'none' : 'block';
    ['etat_id', 'Alias_Confort', 'Alias_Eco', 'Alias_Hg', 'Alias_Arret'].forEach(key => {
        document.querySelector(`#tab_eqlogic .${eqConfig.Type_équipement} .eqLogicAttr[data-l2key=${key}]`).value = eqConfig[key] !== undefined ? eqConfig[key] : ''
    });
  }
  domUtils.ajax({
    type: "POST",
    url: "plugins/planification/core/ajax/planification.ajax.php",
    data: {
      action: "Recup_Json",
      eqLogic_id: _eqLogic["id"]
    },

    global: true,
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

      array[0].forEach(function(Json) {
        var branche_json=''      

        if (isset(Json["Planifications"])){
          var branche_json=Json["Planifications"][0]
          while (isset(branche_json[numéro_planification])) {

            var nom_planification = ""
            var id_planification = ""
            var périodes = []

            branche_json[numéro_planification].forEach(planification => {
              nom_planification = planification.Nom || nom_planification;
              id_planification = planification.Id || id_planification;
              ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'].forEach(day => {
                  périodes[day] = planification[day] || périodes[day];
              });
            });

            const planificationData = { nom: nom_planification, Id: id_planification };
            Ajoutplanification(planificationData);
            AjoutGestionPlanification(planificationData);
            
            document.querySelectorAll(`#div_planifications .planification`)[numéro_planification].querySelectorAll('.JourSemaine').forEach(div_jour => {
              périodes[div_jour.classList[1]].forEach(periode => {
                if (!periode?.Type) return;

                const cmdMatch = CMD_LIST.find(cmd => periode.Id === cmd.Id || periode.Id === cmd.Nom);
                const Couleur = cmdMatch ? `couleur-${cmdMatch.couleur}` : "erreur";
                const Nom = cmdMatch?.Nom || "";
                const Id = cmdMatch?.Id || "";

                let element = SELECT_LIST.replace("#COULEUR#", Couleur)
                                        .replace("#VALUE#", Nom)
                                        .replace("#ID#", Id);

                Ajout_Periode(element, div_jour, periode.Début, periode.Id, periode.Type, _eqLogic.configuration.Type_équipement);
              });

              triage_jour(div_jour);
              MAJ_Graphique_jour(div_jour);
            });
            numéro_planification += 1
          }


          if (_eqLogic.configuration.etat_id) {
            const aliasElement = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .alias`);
            if (aliasElement) {
                aliasElement.style.display = 'block';
            }
          } else {
            const aliasElement = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .alias`);
            if (aliasElement) {
                aliasElement.style.display = 'none';
            }
          }
        


          document.querySelectorAll('.tab-pane').forEach(function(onglet){

              if (onglet.classList.length === 1 || onglet.classList[1] === 'active') {
                return;
              }


            onglet = onglet.classList[1];

              if (Json?.[onglet]) {
                branche_json = Json[onglet][0];  
                
                if (onglet === 'Ouvrants') {
                    let numéro_ouvrant = 0;
                    while (branche_json[numéro_ouvrant]) {
                        ajoutOuvrant();
                        numéro_ouvrant++;
                    }
                }
              }else{
                return
              }

              if(onglet == 'Paramètres'){

              }
              if (onglet === 'Gestion_planifications') {
                let numéro_planification = 0;
                document.getElementById("div_GestionPlanifications").innerHTML = '';
            
                while (branche_json[numéro_planification]) {
                    const currentPlanification = branche_json[numéro_planification][0];
                    AjoutGestionPlanification({ nom: currentPlanification.Nom, Id: currentPlanification.Id });
            
                    const Gestion_planification = document.querySelector('.GestionPlanification:last-of-type');
                    Gestion_planification.querySelectorAll('.expressionAttr').forEach(element => {
                        const dataKey = element.getAttribute('data-l2key');
                        const value = currentPlanification[dataKey];
            
                        if (dataKey === 'Conditions') {
                            domUtils.ajax({
                                type: "POST",
                                url: "plugins/planification/core/ajax/planification.ajax.php",
                                data: { action: "toHumanReadable", expression: value },
                                global: false,
                                async: false,
                                error: handleAjaxError,
                                success: (data) => {
                                    if (data.state !== 'ok') {
                                        jeedomUtils.showAlert({ message: data.result, level: 'danger' });
                                        return;
                                    }
                                    element.value = data.result;
                                    const conditionIdElement = element.closest('.GestionPlanification').querySelector('.ConditionId');                                 
                                    if (element.value) conditionIdElement.classList.add("alert-info");
                                    conditionIdElement.innerHTML = value;
                                }
                            });
                            const evaluation = Gestion_planification.querySelector('.Evaluation');
                            const resultEvaluation = Gestion_planification.querySelector('.RésultatEvaluation');
            
                            if (value) {
                                jeedom.scenario.testExpression({
                                    expression: value,
                                    error: (error) => {
                                        jeedomUtils.showAlert({ message: error.message, level: 'danger' });
                                    },
                                    success: (data) => {
                                        const isCorrect = data.correct !== 'nok';
                                        evaluation.innerHTML = isCorrect ? data.evaluate : "Attention : il doit y avoir un souci avec l'expression";
                                        evaluation.classList.toggle("alert-danger", !isCorrect);
                                        evaluation.classList.toggle("alert-info", isCorrect);
            
                                        resultEvaluation.innerHTML = isCorrect ? data.result : "";
                                        resultEvaluation.classList.toggle("alert-success", data.result && isCorrect);
                                        resultEvaluation.classList.toggle("alert-danger", !data.result || !isCorrect);
                                    }
                                });
                            }
                        } else if (element.type === "checkbox") {
                            element.checked = !!value;
                        } else if (value !== undefined) {
                            element.value = value;
                        }
                    });
            
                    numéro_planification++;
                    const textarea = Gestion_planification.querySelector('textarea');
                    textarea.style.height = `${textarea.scrollHeight}px`;
                }
              }
            
              document.querySelectorAll(`.expressionAttr[data-l1key="${onglet}"]`).forEach(element => {
                const dataKey = element.getAttribute('data-l2key');
                const value = branche_json[dataKey];
            
                if (dataKey === 'Type_équipement_pilote') {
            
                    Array.from(element).forEach((option, i) => {
                        
                        if (option.value === value) {
                          
                            element.value = value;
                            option.selected = true;
                        }
                    });
            
                    if (value && value !== 'Aucun') {
                        const branche_json1 = branche_json;
                        document.querySelector('#tab_Paramètres .options_type_équipement_pilote').style.display = 'block';
                        
                        let options = '';
                        jeedom.eqLogic.byType({
                            type: value,
                            error: error => {
                                jeedomUtils.showAlert({ message: error.message, level: 'danger' });
                            },
                            success: eqLogics => {
                                modifyWithoutSave = false;
            
                                eqLogics.forEach(eqLogic => {
                                    options += `<option id="#${eqLogic.id}#" ${"#" + eqLogic.id + "#" === branche_json1["Equipement_pilote"] ? 'selected' : ''}>{{${eqLogic.name}}}</option>`;
                                });
                                document.querySelector('#tab_Paramètres .expressionAttr[data-l2key="Equipement_pilote"]').innerHTML = options;
                            }
                        });
                    } else {
                        document.querySelector('#tab_Paramètres .options_type_équipement_pilote').style.display = 'none';
                        document.querySelector('#tab_Paramètres .expressionAttr[data-l2key="Equipement_pilote"]').innerHTML = '';
                    }
                } else if (dataKey === 'Equipement_pilote') {
                    Array.from(element).forEach((option, i) => {
                        if (option.value === value) {
                            option.selected = true;
                        }
                    });
                } else if (onglet === 'Ouvrants') {
                    let numéro_ouvrant = 0;
                    while (branche_json[numéro_ouvrant]) {
                        const ouvrant = document.querySelectorAll('.Ouvrant')[numéro_ouvrant];
                        ouvrant.querySelectorAll('.expressionAttr').forEach(element => {
                            const key = element.getAttribute('data-l2key');
                            const val = branche_json[numéro_ouvrant][0][key];
            
                            if (key === 'Commande') {
                                domUtils.ajax({
                                    type: "POST",
                                    url: "plugins/planification/core/ajax/planification.ajax.php",
                                    data: { action: "toHumanReadable", expression: val },
                                    global: false,
                                    async: false,
                                    error: handleAjaxError,
                                    success: data => {
                                        if (data.state !== 'ok') {
                                            jeedomUtils.showAlert({ message: data.result, level: 'danger' });
                                            return;
                                        }
                                        element.value = data.result;
                                    }
                                });
                            } else if (element.type === "checkbox") {
                                element.checked = !!val;
                            } else if (val !== undefined) {
                                element.value = val;
                            }
                        });
                        numéro_ouvrant++;
                    }
                } else if (onglet === 'Gestion_planifications') {
                    // Add implementation if required
                } else {
                    if (value !== undefined) {
                        element.value = value;
                    }
                }
            });
          })



        }               
      })
    }
  })
  const http = new XMLHttpRequest();
  http.open('HEAD', img, false);
  http.send();
  
  if (http.status !== 200) {
      jeedomUtils.showAlert({
          message: `L'image ${img} n'existe pas.`,
          level: 'danger'
      });
      img = "plugins/planification/plugin_info/planification_icon.png";
  }
  
  // Update image and set its value
  document.querySelector('#img_planificationModel').setAttribute('src', img);
  document.querySelector('.eqLogicAttr[data-l2key=Chemin_image]').value = img;
  
  // Manage active state for eqLogic elements
  document.querySelectorAll('.li_eqLogic').forEach(li_eqLogic => {
      li_eqLogic.classList.toggle('active', li_eqLogic.getAttribute("data-eqlogic_id") === _eqLogic.id);
  });
}

function saveEqLogic(_eqLogic) {
  if (!isset(_eqLogic.configuration)) {
    _eqLogic.configuration = {};
  }
  _eqLogic.configuration.Chemin_image = document.querySelector('#tab_eqlogic .eqLogicAttr[data-l2key=Chemin_image]').value;
  _eqLogic.configuration.Mode_id = document.querySelector('#tab_eqlogic .eqLogicAttr[data-l2key=Mode_id]').value;
  _eqLogic.configuration.Type_équipement = document.querySelector('#tab_eqlogic .eqLogicAttr[data-l2key=Type_équipement]').value;
  _eqLogic.configuration.Duree_mode_manuel_par_defaut = document.querySelector('#tab_eqlogic .eqLogicAttr[data-l2key=Duree_mode_manuel_par_defaut]').value;
  const type_équipement = document.querySelector('#tab_eqlogic .eqLogicAttr[data-l2key=Type_équipement]').value;

  if (type_équipement === "Volet") {
    _eqLogic.configuration.etat_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=etat_id]`).value;
    _eqLogic.configuration.Alias_Ouvert = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Alias_Ouvert]`).value;
    _eqLogic.configuration.Alias_Ferme = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Alias_Ferme]`).value;
    _eqLogic.configuration.Alias_My = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Alias_My]`).value;

    let type_fenêtre = "fenêtre";
    document.querySelectorAll(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=type_fenêtre]`).forEach(_el => {
      if (_el.checked) {
        type_fenêtre = _el.id;
      }
    });
    _eqLogic.configuration.Type_fenêtre = type_fenêtre;

    let sens_ouverture = "gauche";
    document.querySelectorAll(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=sens_ouveture_fenêtre]`).forEach(_el => {
      if (_el.value == 1) {
        sens_ouverture = _el.id;
      }
    });
    _eqLogic.configuration.Sens_ouverture = sens_ouverture;

    _eqLogic.configuration.Etat_fenêtre_gauche_id = "";
    _eqLogic.configuration.Niveau_batterie_gauche_id = "";
    _eqLogic.configuration.Etat_fenêtre_droite_id = "";
    _eqLogic.configuration.Niveau_batterie_droite_id = "";

    if (sens_ouverture === "droite") {
      _eqLogic.configuration.Etat_fenêtre_droite_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Etat_fenêtre_droite_id]`).value;
      _eqLogic.configuration.Niveau_batterie_droite_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Niveau_batterie_droite_id]`).value;
    }

    if (sens_ouverture === "gauche") {
      _eqLogic.configuration.Etat_fenêtre_gauche_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Etat_fenêtre_gauche_id]`).value;
      _eqLogic.configuration.Niveau_batterie_gauche_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Niveau_batterie_gauche_id]`).value;
    }

    if (sens_ouverture === "gauche-droite") {
      _eqLogic.configuration.Etat_fenêtre_gauche_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Etat_fenêtre_gauche_id]`).value;
      _eqLogic.configuration.Niveau_batterie_gauche_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Niveau_batterie_gauche_id]`).value;
      _eqLogic.configuration.Etat_fenêtre_droite_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Etat_fenêtre_droite_id]`).value;
      _eqLogic.configuration.Niveau_batterie_droite_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Niveau_batterie_droite_id]`).value;
    }

    
  }
  if (type_équipement === 'PAC') {
    _eqLogic.configuration.Temperature_ambiante_id = document.querySelector('#tab_eqlogic .eqLogicAttr[data-l1key="configuration_PAC"][data-l2key=Temperature_ambiante_id]').value;
  }
  if (type_équipement === 'Prise') {
    _eqLogic.configuration.etat_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=etat_id]`).value;
    _eqLogic.configuration.Alias_On = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Alias_On]`).value;
    _eqLogic.configuration.Alias_Off = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Alias_Off]`).value;
  }
  if (type_équipement === 'Chauffage') {
    _eqLogic.configuration.etat_id = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=etat_id]`).value;
    _eqLogic.configuration.Alias_Confort = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Alias_Confort]`).value;
    _eqLogic.configuration.Alias_Eco = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Alias_Eco]`).value;
    _eqLogic.configuration.Alias_Hg = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Alias_Hg]`).value;
    _eqLogic.configuration.Alias_Arret = document.querySelector(`#tab_eqlogic .${_eqLogic.configuration.Type_équipement} .eqLogicAttr[data-l2key=Alias_Arret]`).value;
  }
  if (type_équipement === 'Autre') {
  }
  
  let Json = '';
  let Json1 = []
  let erreur = false;
  let numéro = 0;
 
  document.querySelectorAll('#div_planifications .planification').forEach((Planification) => {
    // Préparation des parties JSON pour chaque planification
    let planificationJson = [
      `"${numéro}":[{"Nom":"${Planification.querySelector('.nom_planification').innerHTML}"`,
      `"Id":"${Planification.getAttribute("Id")}"`
    ];
    // Parcourir les jours
    const jours = Array.from(Planification.querySelectorAll('th .JourSemaine'));
    const joursJson = jours.map((Jour, index) => {
      const jourClass = Jour.getAttribute("class").split(' ')[1];
      let periodesJson = [];
      Jour.querySelectorAll('.Periode_jour').forEach((Période, idx) => {
        let type_periode = "heure_fixe";
        let debut_periode = "";
        const checkbox = Période.querySelector('.checkbox_lever_coucher');
        
        if (checkbox.checked) {
          type_periode = Période.querySelector('.select_lever_coucher').value;
        } else {
          debut_periode = Période.querySelector('.in_timepicker').value;
        }

        const Id = Période.querySelector('.select-selected')?.getAttribute('id');
        if (!Id || typeof Id !== 'string' || Période.querySelector('.select-selected').classList.contains("erreur")) {
          erreur = true;
          Période.querySelector('.select-selected')?.classList.add("erreur");
        }

        if (type_periode === "heure_fixe" && !debut_periode) {
          erreur = true;
          Période.querySelector('.select-selected')?.classList.add("erreur");
        }

        // JSON pour une période
        periodesJson.push(`{"Type":"${type_periode}", "Début":"${debut_periode}", "Id":"${Id}"}`);
      });

      // JSON pour un jour
      return `"${jourClass}":[${periodesJson.join(",")}]`;
    });

    // Finalisation de la planification
    planificationJson.push(joursJson.join());
    Json1.push(planificationJson.join(",") + "}]");
    numéro++;
  });
  Json += `[{"Planifications":[{${Json1.join(",")}}]`;
  if (Json === '[{"Planifications":[{}]') {
    jeedom.cmd.execute({ id: set_planification_Id, value: { select: '', Id_planification: '' } });
  }
  if (erreur) {
    jeedomUtils.showAlert({
      message: "Impossible d'enregistrer la planification. Celle-ci comporte des erreurs.",
      level: 'danger'
    });
    return;
  }
  
  if (type_équipement === "Volet") {
    Json1 = []
    document.querySelectorAll('#tab_gestion_heures_lever_coucher .in_timepicker').forEach(element => {
      if (!element.value) {
        element.value = '00:00';
        if (element.classList[1].includes('Max')) {
          element.value = '23:59';
        }
      }
      Json1.push(`"${element.classList[1]}_${element.classList[2]}":"${element.value}"`);
      
    });
    Json += `,"Lever_coucher":[{${Json1.join(",")}}]`;
  }
  
  
  Json1 = []
  document.querySelectorAll('#tab_Paramètres .' + type_équipement + ' select').forEach((parametre) => {
    
    if(document.querySelector(`.expressionAttr[data-l2key="${parametre.getAttribute('data-l2key')}"]`).options[document.querySelector('#tab_Paramètres .expressionAttr[data-l2key=Type_équipement_pilote]').selectedIndex] == undefined){
      Json1.push(`"${parametre.getAttribute('data-l2key')}":""`)   
    }else{
      Json1.push(`"${parametre.getAttribute('data-l2key')}":"${parametre.options[parametre.selectedIndex].id || parametre.options[parametre.selectedIndex].value}"`)
    }

  })
  if(Json1.length-1 > -1){
    Json += `,"Paramètres":[{${Json1.join(",")}}]`;
  }
  numéro = 0
  Json1 = []
  document.querySelectorAll('.' + type_équipement + ' #div_ouvrants .Ouvrant').forEach((ouvrant) => {
    
    let Commande=''
    domUtils.ajax({
      type: "POST",
      url: "plugins/planification/core/ajax/planification.ajax.php",
      data: { action: "fromHumanReadable", expression: ouvrant.querySelector('[data-l2key=Commande]').value },
      global: true,
      async: false,
      error: (request, status, error) => handleAjaxError(request, status, error),
      success: (data) => {
          if (data.state !== 'ok') {
            jeedomUtils.showAlert({ message: data.result, level: 'danger' });
          } else {            
            Commande= data.result
          }
      }
    });
    Json1.push(`"${numéro}":[{"Commande":"${Commande}", "Délai_ouverture":"${ ouvrant.querySelector('[data-l2key=Délai_ouverture]').value ||  ouvrant.querySelector('[data-l2key=Délai_ouverture]').getAttribute('placeholder')}", "Délai_fermeture":"${ouvrant.querySelector('[data-l2key=Délai_fermeture]').value ||  ouvrant.querySelector('[data-l2key=Délai_fermeture]').getAttribute('placeholder')}", "Alerte":${ouvrant.querySelector('[data-l2key=Alerte]').checked}}]`);
    numéro ++
  })
  if(Json1.length-1 > -1){
    Json += `,"Ouvrants":[{${Json1.join(",")}}]`;
  }



  Json1 = []
  numéro = 0;
  document.querySelectorAll('#div_GestionPlanifications .GestionPlanification').forEach((GestionPlanification) => {
    Conditions=''
    domUtils.ajax({
      type: "POST",
      url: "plugins/planification/core/ajax/planification.ajax.php",
      data: { action: "fromHumanReadable", expression: GestionPlanification.querySelector('[data-l2key=Conditions]').value },
      global: true,
      async: false,
      error: (request, status, error) => handleAjaxError(request, status, error),
      success: (data) => {
          if (data.state !== 'ok') {
            jeedomUtils.showAlert({ message: data.result, level: 'danger' });
          } else {            
             Conditions= data.result
          }
      }
    });
  
    Json1.push(`"${numéro}":[{"Nom":"${GestionPlanification.querySelector('.Nom_planification').innerHTML}", "Id":"${GestionPlanification.classList[1]}", "Conditions":"${Conditions}", "Stop":${GestionPlanification.querySelector('[data-l2key=Stop]').checked}}]`);
    numéro++;
  })
  
 


  Json += `,"Gestion_planifications":[{${Json1.join(",")}}]`;

  Json +='}]'
 
 



  try {
    JSON.stringify(JSON.parse(Json), null, " ");
  } catch (error) {
    jeedomUtils.showAlert({
      message: 'Problème de JSON, vérifiez dans le debug.',
      level: 'danger'
    });
    console.log(Json);
    return;
  }

  domUtils.ajax({
    type: "POST",
    url: "plugins/planification/core/ajax/planification.ajax.php",
    data: {
      action: "Enregistrer_Json",
      id: _eqLogic["id"],
      Json: JSON.stringify(JSON.parse(Json), null, " ")
    },
    global: false,
    error: function(request, status, error) {
      console.log(request);
      handleAjaxError(request, status, error);
    },
    success: function(data) {
      if (data.state !== 'ok') {
        jeedomUtils.showAlert({
          message: data.result,
          level: 'danger'
        });
        return;
      }
    }
  });



  const chauffageRows = Array.from(document.querySelectorAll('#table_chauffage tbody tr'));
  const actionsRows = Array.from(document.querySelectorAll('#table_actions tbody tr'));

  _eqLogic.cmd.forEach(_cmd => {
    chauffageRows.forEach(_el => {
      const cmdAttr = _el.querySelector('.cmdAttr');
      if (cmdAttr && cmdAttr.value == _cmd.id) {
        const numericInput = _el.querySelector('#numericInput');
        if (numericInput) _cmd.value = numericInput.value;
      }
    });

    _cmd.configuration = _cmd.configuration || {};

    if (!_cmd.id) {
      Object.assign(_cmd, {
        Type: 'action',
        subType: 'other',
        configuration: { Type: 'Planification_perso' }
      });
    }
    actionsRows.forEach(_el => {
      const cmdAttr = _el.querySelector('.cmdAttr');
      if (cmdAttr && cmdAttr.value == _cmd.id) {
        const expressionAttrs = Array.from(_el.querySelectorAll('.expressionAttr'));
        if (expressionAttrs.length > 0) {
          const options = expressionAttrs.reduce((acc, attr) => {
            const key = attr.getAttribute('data-l2key');
            if (key) acc[key] = attr.value;
            return acc;
          }, {});
          _cmd.configuration.options = options;
        } else {
          _cmd.configuration.options = '';
        }
      }
    });
  });

  return _eqLogic;
}

function addCmdToTable(_cmd) {
  const excludedLogicalIds = [
    "set_heure_fin", "set_consigne_temperature", "set_action_en_cours",
    "manuel", "refresh", "boost_on", "boost_off", "set_info", "mode_planifications"
  ];

  if (excludedLogicalIds.includes(_cmd.logicalId)) {
    return;
  }

  if (_cmd.logicalId === 'set_planification') {
    set_planification_Id = _cmd.id;
    return;
  }

  if (!isset(_cmd)) _cmd = { configuration: {} };
  if (!isset(_cmd.configuration)) _cmd.configuration = {};
  const heatingLogicalIds = [
    "delta_chauffage_eco", "delta_chauffage_boost", "temperature_mini_chauffage_continu",
    "temperature_mini_chauffage", "numéro_semaine_mini_chauffage", "numéro_semaine_max_chauffage",
    "delta_climatisation_boost", "temperature_mini_climatisation"
  ];

  if (heatingLogicalIds.includes(_cmd.logicalId)) {
    let tr = `
        <tr class="cmd" data-cmd_id="${init(_cmd.id)}">
            <td>
                <input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none">
                <input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none">
                <input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none">
                <input class="cmdAttr form-control input-sm" data-l1key="name" disabled="true" placeholder="{{Nom}}">
            </td>
            <td>
                <div class="numeric-updown" style="display:inline-flex!important">
                    <input class="cmdAttr form-control input-sm" type="numeric" 
                        id="numericInput" value="${_cmd.value}" min="${_cmd.configuration.minValue}" max="${_cmd.configuration.maxValue}" step="1" readonly/>
                    <div class="numeric-updown-button">
                        <button class="btn_numeric up"><i class="icon fas fa-chevron-up"></i></button>
                        <button class="btn_numeric down"><i class="icon fas fa-chevron-down"></i></button>
                    </div>
                </div>
            </td>
            <td>
                <a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a>
                <a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>
            </td>
        </tr>`
    document.getElementById('table_chauffage').insertAdjacentHTML('beforeend', tr);
    const _tr = document.getElementById('table_chauffage').lastChild;
    _tr.setJeeValues(_cmd, '.cmdAttr');
    jeedom.cmd.changeType(_tr, init(_cmd.subType));
    _tr.querySelector('.cmdAttr[data-l1key=type],.cmdAttr[data-l1key=subType]').setAttribute("disabled", true);
  } else if (_cmd.type === 'info') {
    let tr = `
      <tr class="cmd" data-cmd_id="${init(_cmd.id)}">
          <td>
              <input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none">
              <input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none">
              <input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none">
              <input class="cmdAttr form-control input-sm" data-l1key="name" disabled="true" placeholder="{{Nom}}">
          </td>
          <td>
          `
    if (typeof jeeFrontEnd !== 'undefined' && jeeFrontEnd.jeedomVersion !== 'undefined') {
      tr += `<span class="cmdAttr" data-l1key="htmlstate"></span>`;
    }
    tr += `
      </td>
      <td>
          <span>
              <label class="checkbox-inline">
                  <input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized"/> {{Historiser}}`
                  if(_cmd.logicalId == "consigne_temperature"){
                     tr += `<div style="margin-top:7px;">
                    <input class="tooltips cmdAttr form-control input-sm tippied" data-l1key="configuration" data-l2key="minValue" placeholder="Min" style="width: 80%; max-width: 80px; margin-right: 2px;" data-title="Min">
                    <input class="tooltips cmdAttr form-control input-sm tippied" data-l1key="configuration" data-l2key="maxValue" placeholder="Max" style="width: 80%; max-width: 80px; margin-right: 2px;" data-title="Max">
                  </div>`
                  }
          tr += `        
              </label>
          </span>
      </td>
      <td>
      `;
    if (is_numeric(_cmd.id)) {
      tr += `
        <a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a>
        <a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>
      `;
    }
    tr += `
        </td>
    </tr>
    `;
    document.getElementById('table_infos').insertAdjacentHTML('beforeend', tr);
    const _tr = document.getElementById('table_infos').lastChild;
    _tr.setJeeValues(_cmd, '.cmdAttr');
    jeedom.cmd.changeType(_tr, init(_cmd.subType));
    _tr.querySelector('.cmdAttr[data-l1key=type],.cmdAttr[data-l1key=subType]').setAttribute("disabled", true);
  } else if (_cmd.type === 'action') {
    const SELECT_LIST = Recup_select("commandes");
    let tr = `
      <tr class="cmd" data-cmd_id="${init(_cmd.id)}">
          <td>
              <input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none">
              <input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none">
              <input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none">
              <input class="cmdAttr form-control input-sm" data-l1key="name" disabled="true" placeholder="{{Nom}}">
          </td>
    `;
    if (_cmd.configuration.Type === "Planification" || _cmd.configuration.Type === "Planification_perso") {
      tr += `
                    <td>
                        <div class="input-group" style="width:100%;">
                            <input class="cmdAttr form-control input-sm cmdAction" data-l1key="configuration" data-l2key="commande"/>
                            <span class="input-group-btn">
                                <a class="btn btn-success btn-sm listAction"><i class="fa fa-list-alt"></i></a>
                                <a class="btn btn-success btn-sm listCmdAction"><i class="fa fa-tasks"></i></a>
                            </span>
                        </div>
                        <div class="actionOptions"></div>
                    </td>
                    <td>
                        <div class="custom-select">${SELECT_LIST}</div>
                    </td>
                `;
    }else{
      tr += `<td></td><td></td>`; 
    }
    tr += `
                    <td>
                        <a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a>
                        <a class="btn btn-default btn-xs cmdAction tester"><i class="fa fa-rss"></i> {{Tester}}</a>
                `;
    if (_cmd.configuration.Type === "Planification_perso") {
      tr += `<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>`;
    }
    tr += `
                    </td>
                </tr>
            `;

    document.getElementById('table_actions').insertAdjacentHTML('beforeend', tr);
    const _tr = document.getElementById('table_actions').lastElementChild;
    _tr.setJeeValues(_cmd, '.cmdAttr');
    const actionOptions = _tr.querySelector(".actionOptions");
    if (actionOptions) {
      jeedom.cmd.displayActionOption(
        _cmd.configuration.commande,
        init(_cmd.configuration.options),
        function (html) {
          actionOptions.innerHTML = html;
        }
      );
    }

    if (isset(_cmd.configuration.Type)) {
      if (_cmd.configuration.Type === "Planification" || _cmd.configuration.Type === "Planification_perso") {
        let couleur = _cmd.configuration.Couleur || "orange"; 
        if (couleur === "<span>#VALUE#</span>") {
          couleur = "orange"; 
        }

        const selectSelected = _tr.querySelector(".select-selected");
        const selectItem = _tr.querySelector(`.select-items .couleur-${couleur}`);

        if (selectSelected && selectItem) {
          selectSelected.classList.replace("#COULEUR#", `couleur-${couleur}`);
          selectItem.classList.add("same-as-selected");
          selectSelected.innerHTML = couleur;
        } 
      }
    }



  }
}