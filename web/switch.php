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
include(EASYWIDIR . '/stuff/functions.php');
include(EASYWIDIR . '/stuff/class_validator.php');
include(EASYWIDIR . '/stuff/vorlage.php');
include(EASYWIDIR . '/stuff/settings.php');
if (!isset($admin_id) or !isset($reseller_id)) {
    die('No access');
}
$pa = User_Permissions($admin_id);
if (!$pa['user'] and !$pa['gserver'] and !$pa['root']) {
	die('No access');
}
if ($reseller_id != 0 and isset($admin_id) and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}
if ($ui->id('id', 19, 'get')) {
	$referrer = explode('/', str_replace(array('http://','https://'), '', strtolower($ui->escaped('HTTP_REFERER','server'))));
    $refstring = explode('?',$referrer[1]);
    if (isset($refstring[1])) {
        $from = explode('&',$refstring[1]);
    }
    $query = $sql->prepare("SELECT `resellerid`,`accounttype` FROM `userdata` WHERE `id`=? LIMIT 1");
    $query->execute(array($ui->id('id', 19, 'get')));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $resellerid = $row['resellerid'];
        $accounttype = $row['accounttype'];
    }
    $sql=null;
    if (!isset($resellerid) or ($reseller_id != 0 and $resellerid != $reseller_id)) {
        header('Location: login.php');
        die('Please allow redirection');
    }
    if (isset($accounttype) and $accounttype == 'u') {
        $_SESSION['userid'] = $ui->id('id', 19, 'get');
        if (isset($from) and $from[0] == "w=gs") {
            header('Location: userpanel.php?w=gs');
            die('Please allow redirection');
        } else if (isset($from) and $from[0] == "w=vo") {
            header('Location: userpanel.php?w=vo');
            die('Please allow redirection');
        } else if (isset($from) and $from[0] == "w=my") {
            header('Location: userpanel.php?w=my');
            die('Please allow redirection');
        } else {
            header('Location: userpanel.php');
            die('Please allow redirection');
        }
    } else if (isset($accounttype) and $accounttype == 'r' and isset($resellerid)) {
        $_SESSION['oldid'] = $admin_id;
        $_SESSION['oldresellerid'] = $reseller_id;
        $_SESSION['adminid'] = $ui->id('id', 19, 'get');
        $_SESSION['resellerid'] = $resellerid;
        if ($reseller_id == 0) {
            $_SESSION['oldadminid'] = $admin_id;
        }
        header('Location: admin.php');
        die('Please allow redirection');
    } else {
        header('Location: login.php');
        die('Please allow redirection');
    }
} else {
    $sql=null;
	header('Location: login.php');
	die('Please allow redirection');
}