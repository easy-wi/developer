<?php

/**
 * File: userpanel_logdata.php.
 * Author: Ulrich Block
 * Contact: <ulrich.block@easy-wi.com>
 *
 * This file is part of Easy-WI.
 *
 * Easy-WI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Easy-WI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy-WI.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Diese Datei ist Teil von Easy-WI.
 *
 * Easy-WI ist Freie Software: Sie koennen es unter den Bedingungen
 * der GNU General Public License, wie von der Free Software Foundation,
 * Version 3 der Lizenz oder (nach Ihrer Wahl) jeder spaeteren
 * veroeffentlichten Version, weiterverbreiten und/oder modifizieren.
 *
 * Easy-WI wird in der Hoffnung, dass es nuetzlich sein wird, aber
 * OHNE JEDE GEWAEHELEISTUNG, bereitgestellt; sogar ohne die implizite
 * Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
 * Siehe die GNU General Public License fuer weitere Details.
 *
 * Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
 * Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
 */
if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['log'])) {
    header('Location: userpanel.php');
    die;
}

$sprache = getlanguagefile('logs',$user_language,$reseller_id);

$htmlExtraInformation['css'][] = '<link href="css/adminlte/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css">';
$htmlExtraInformation['js'][] = '<script src="js/adminlte/plugins/datatables/jquery.datatables.js" type="text/javascript"></script>';
$htmlExtraInformation['js'][] = '<script src="js/adminlte/plugins/datatables/datatables.bootstrap.js" type="text/javascript"></script>';
$htmlExtraInformation['js'][] = '<script type="text/javascript">
$(function() {
    $(\'#dataTable\').dataTable({
        "bPaginate" : true,
        "bLengthChange" : true,
        "bFilter" : true,
        "bSort" : true,
        "bInfo" : true,
        "bAutoWidth" : false,
        "bServerSide" : true,
        "iDisplayLength" : 10,
        "aaSorting": [[0,\'desc\']],
        "sAjaxSource": "ajax.php?w=datatable&d=userlog",
        "oLanguage": {
            "oPaginate": {
                "sFirst": "' . $gsprache->dataTablesFirst . '",
                "sLast": "' . $gsprache->dataTablesLast . '",
                "sNext": "' . $gsprache->dataTablesNext . '",
                "sPrevious": "' . $gsprache->dataTablesPrevious . '"
            },
            "sEmptyTable": "' . $gsprache->dataTablesEmptyTable . '",
            "sInfo": "' . $gsprache->dataTablesInfo . '",
            "sInfoEmpty": "' . $gsprache->dataTablesEmpty . '",
            "sInfoFiltered": "' . $gsprache->dataTablesFiltered . '",
            "sLengthMenu": "' . $gsprache->dataTablesMenu . '",
            "sSearch": "' . $gsprache->dataTablesSearch . '",
            "sZeroRecords": "' . $gsprache->dataTablesNoRecords . '"
        }
    });
});
</script>';
$template_file = 'userpanel_logs.tpl';