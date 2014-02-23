<?php

/**
 * File: switch.php.
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

define('EASYWIDIR', dirname(__FILE__));
include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/settings.php');

if (!isset($admin_id) or !isset($reseller_id)) {
    redirect('login.php');
}

$pa = User_Permissions($admin_id);

if (!$pa['user'] and !$pa['gserver'] and !$pa['root']) {
	die('No access');
}

if ($reseller_id != 0 and isset($admin_id) and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}

if ($ui->id('id', 19, 'get')) {
	$referrer = explode('/', str_replace(array('http://','https://'), '', strtolower($ui->escaped('HTTP_REFERER', 'server'))));
    $refstring = explode('?', $referrer[1]);

    if (isset($refstring[1])) {
        $from = explode('&', $refstring[1]);
    }

    $query = $sql->prepare("SELECT `resellerid`,`accounttype` FROM `userdata` WHERE `id`=? LIMIT 1");
    $query->execute(array($ui->id('id', 19, 'get')));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $sql = null;

        if ($row['accounttype'] == 'u') {

            $_SESSION['userid'] = $ui->id('id', 19, 'get');

            if (isset($from) and $from[0] == "w=gs") {
                redirect('userpanel.php?w=gs');
            } else if (isset($from) and $from[0] == "w=vo") {
                redirect('userpanel.php?w=vo');
            } else if (isset($from) and $from[0] == "w=my") {
                redirect('userpanel.php?w=my');
            } else {
                redirect('userpanel.php');
            }

        } else if ($row['accounttype'] == 'r' and $row['resellerid'] > 0) {
            $_SESSION['oldid'] = $admin_id;
            $_SESSION['oldresellerid'] = $reseller_id;
            $_SESSION['adminid'] = $ui->id('id', 19, 'get');
            $_SESSION['resellerid'] = $row['resellerid'];

            if ($reseller_id == 0) {
                $_SESSION['oldadminid'] = $admin_id;
            }

            redirect('admin.php');

        } else {
            redirect('login.php');
        }

    }
}
$sql = null;
redirect('login.php');