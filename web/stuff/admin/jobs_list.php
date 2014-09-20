<?php

/**
 * File: jobs_list.php.
 * Author: Ulrich Block
 * Date: 20.05.12
 * Time: 19:44
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['jobs'])) {
    header('Location: admin.php');
    die('No acces');
}
$sprache = getlanguagefile('api', $user_language, $reseller_id);

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->w('action', 4, 'post') == 'dl' and !$ui->id('id', 19, 'get')) {

    $i = 0;

    if ($ui->id('id', 19, 'post')) {

        foreach ($ui->id('id', 19, 'post') as $id) {

            if ($reseller_id == 0) {
                $delete = $sql->prepare("DELETE FROM `jobs` WHERE `jobID`=? LIMIT 1");
                $delete->execute(array($id));
            } else {
                $delete = $sql->prepare("DELETE FROM `jobs` WHERE `jobID`=? AND `resellerID`=? LIMIT 1");
                $delete->execute(array($id, $reseller_id));
            }

            $i++;
        }
    }

    $template_file = $i . ' ' . $gsprache->jobs . ' deleted';

} else {

    configureDateTables('-1', '0, "desc"', 'ajax.php?w=datatable&d=joblog');

    $template_file = 'admin_jobs_list.tpl';
}
