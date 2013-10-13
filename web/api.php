<?php
/**
 * File: api.php.
 * Author: Ulrich Block
 * Date: 20.05.12
 * Time: 13:41
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
if (is_dir(EASYWIDIR . '/install')) die('Please remove the "install" folder');
$logininclude = true;
include(EASYWIDIR . '/stuff/vorlage.php');
include(EASYWIDIR . '/stuff/class_validator.php');
include(EASYWIDIR . '/stuff/functions.php');
include(EASYWIDIR . '/stuff/settings.php');
if ($ui->ip4('REMOTE_ADDR', 'server') and $ui->names('user',255, 'post')) {
    $query = $sql->prepare("SELECT `ip`,`active`,`pwd`,`salt`,`user`,i.`resellerID` FROM `api_ips` i LEFT JOIN `api_settings` s ON i.`resellerID`=s.`resellerID` WHERE `ip`=?");
    $query->execute(array($ui->ip4('REMOTE_ADDR', 'server')));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $apiIP = $row['ip'];
        $pwd = $row['pwd'];
        $salt = $row['salt'];
        if ($row['active'] == 'Y' and passwordhash($ui->password('pwd',255, 'post'),$salt)==$pwd and $ui->names('user',255, 'post')==$row['user']) {
            $resellerIDs[] = $row['resellerID'];
        }
    }
} else {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden: No valid access data');
}
if ($ui->smallletters('type',10, 'post') and ($ui->smallletters('type',4, 'post') == 'user' or $ui->smallletters('type',5, 'post') == 'voice' or $ui->smallletters('type',7, 'post') == 'gserver' or $ui->smallletters('type',5, 'post') == 'mysql')) {
    $type = $ui->smallletters('type',7, 'post');
}
if (isset($resellerIDs) and count($resellerIDs)==1 and passwordhash($ui->password('pwd',255, 'post'),$salt)==$pwd and isset($type)) {
    $resellerID = $resellerIDs[0];
    $licenceDetails=serverAmount($resellerID);
    if (is_numeric($licenceDetails['left']) and (0>$licenceDetails['left'] or 0>$licenceDetails['lG'] or 0>$licenceDetails['lVo'] or 0>$licenceDetails['lVs'] or 0>$licenceDetails['lD'])) {
        header('HTTP/1.1 403 Forbidden');
        die('403 Forbidden: More servers are stored than allowed!');
    }
    $data = array();
    if ($ui->escaped('json', 'post')) {
        $apiType='json';
        $data=@json_decode(urldecode(base64_decode($ui->escaped('json', 'post'))));
        if (!$data) {
            header('HTTP/1.1 403 Forbidden');
            die('403 Forbidden: JSON not vaild');
        }
    } else if ($ui->escaped('xml', 'post')) {
        $apiType='xml';
        $data=@simplexml_load_string(urldecode(base64_decode($ui->escaped('xml', 'post'))));
        if (!$data) {
            header('HTTP/1.1 403 Forbidden');
            die('403 Forbidden: XML not valid');
        }
    } else {
        header('HTTP/1.1 403 Forbidden');
        die('403 Forbidden: Neither POST value xml, nor JSON has been send!');
    }
    $data=(array)$data;
    $tempArray = array();
    foreach ($data as $key => $value) {
        if (is_object($value)) {
            $tempArray[$key] = null;
        } else {
            $tempArray[$key] = $value;
        }
    }
    $data = $tempArray;
    unset($tempArray);
    $bad=array(false, null,'');
    $licenceDetails=serverAmount($resellerID);
    if (is_numeric($licenceDetails['left']) and (0>$licenceDetails['left'] or 0>$licenceDetails['lG'] or 0>$licenceDetails['lVo'] or 0>$licenceDetails['lVs'] or 0>$licenceDetails['lD'])) {
        header('HTTP/1.1 403 Forbidden');
        die('403 Forbidden: Server amount already exceeds licence limits!');
    }
    $gsModule=(is_numeric($licenceDetails['mG']) and $licenceDetails['mG']==0) ? false : true;
    $vModule=(is_numeric($licenceDetails['mVs']) and $licenceDetails['mVs']==0) ? false : true;
    $voModule=(is_numeric($licenceDetails['mVo']) and $licenceDetails['mVo']==0) ? false : true;
    $dModule=(is_numeric($licenceDetails['mD']) and $licenceDetails['mD']==0) ? false : true;
    if ($type == 'user') {
        include(EASYWIDIR . '/stuff/api_users.php');
    } else if ($type == 'voice') {
        if ($voModule == true) {
            include(EASYWIDIR . '/stuff/api_voice.php');
        } else {
            header('HTTP/1.1 403 Forbidden');
            die('403 Forbidden: Voice module is inactive');
        }
    } else if ($type == 'mysql') {
        include(EASYWIDIR . '/stuff/api_mysql.php');
    } else if ($type == 'gserver') {
        if ($gsModule == true) {
            include(EASYWIDIR . '/stuff/api_gserver.php');
        } else {
            header('HTTP/1.1 403 Forbidden');
            die('403 Forbidden: Gameserver module is inactive');
        }
    }
} else if (isset($resellerIDs) and count($resellerIDs)==1 and passwordhash($ui->password('pwd',255, 'post'),$salt)==$pwd and !isset($type)) {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden: Type is not defined');
} else {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden: No valid api data');
}