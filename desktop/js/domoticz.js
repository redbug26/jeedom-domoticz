
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }


    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';

    tr += '<td>';
    tr += '<div class="row">';
    tr += '<div class="col-sm-6">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
    tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> Icône</a>';
    tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
    tr += '</div>';
    tr += '<div class="col-sm-6">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name"></td>';
    tr += '</div>';
    tr += '</div>';
    tr += '</td>';

/*
tr += '<td>';
tr += '<div class="col-sm-3">';
tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fa fa-flag"></i> Icône</a>';
tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
tr += '</div>';
tr += '<div class="col-sm-3">';
tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
tr += '<input class="cmdAttr form-control input-sm" data-l1key="name"></td>';
tr += '</div></td>';
*/


//    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
 //   tr += '<input class="cmdAttr form-control input-sm" data-l1key="name"></td>';
    if(typeof _cmd.configuration.commandName !== "undefined") {
        tr += '<td>' + _cmd.configuration.commandName + '</td>';            
    } else {
        tr += '<td></td>';            
    }
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="parameters"></td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" value="action" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" value="message" style="display : none;">';
    // tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/> {{Afficher}}<br/></span>';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" data-size="mini" checked/>{{Afficher}}</label></span>';
    tr += '</td>';
    tr += '<td>';

    if (is_numeric(_cmd.id)) {
      tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    var tr = $('#table_cmd tbody tr:last');
    jeedom.eqLogic.builSelectCmd({
        id: $(".li_eqLogic.active").attr('data-eqLogic_id'),
        filter: {type: 'info'},
        error: function (error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function (result) {
            tr.find('.cmdAttr[data-l1key=value]').append(result);
            tr.setValues(_cmd, '.cmdAttr');
            jeedom.cmd.changeType(tr, init(_cmd.subType));
        }
    });


    // $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
}

$('#bt_syncEqLogic').on('click', function () {
    syncEqLogicWithRazberry();
});

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});


function syncEqLogicWithRazberry() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/domoticz/core/ajax/domoticz.ajax.php", // url du fichier php
        data: {
            action: "syncEqLogicWithRazberry",
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) { // si l'appel a bien fonctionné
        if (data.state != 'ok') {
            $('#div_alert').showAlert({message: data.result, level: 'danger'});
            return;
        }
        window.location.reload();
    }
});
}
