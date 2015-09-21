
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



/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.rss
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;"><input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 90%;margin-left:auto;margin-right:auto;" placeholder="{{Nom}}" /></td>';
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="lien_rss" style="width :90%;margin-left:auto;margin-right:auto;" placeholder="{{lien rss}}" /></td>';
    tr += '<td><select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="nbr_article" style="width :140px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></td>';
    tr += '<td><span><input type="checkbox" data-size="mini" data-label-text="{{Activer}}" class="cmdAttr bootstrapSwitch" data-l1key="isVisible" checked/></span><i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" value="info" style="display : none;" />';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="subtype" value="string" style="display : none;" />';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}
