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

if (is_dir(EASYWIDIR . '/install')) {
    die('Please remove the "install" folder');
}

$logininclude = true;

include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/settings.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

if ($ui->ip4('REMOTE_ADDR', 'server') and $ui->names('user', 255, 'post')) {

    $query = $sql->prepare("SELECT `ip`,`active`,`pwd`,`salt`,`user`,i.`resellerID` FROM `api_ips` i INNER JOIN `api_settings` s ON s.`resellerID`=i.`resellerID` WHERE `ip`=?");
    $query->execute(array($ui->ip4('REMOTE_ADDR', 'server')));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        if ($row['active'] == 'Y' and passwordhash($ui->password('pwd', 255, 'post'), $row['salt']) == $row['pwd'] and $ui->names('user', 255, 'post') == $row['user']) {
            $apiIP = $row['ip'];
            $resellerIDs[] = $row['resellerID'];
        }
    }

} else {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden: No valid access data');
}

if (in_array($ui->smallletters('type', 10, 'post'), array('gserver', 'list', 'tsdns', 'mysql', 'user', 'voice', 'web'))) {
    $type = $ui->smallletters('type', 10, 'post');
}

if (isset($resellerIDs) and count($resellerIDs) == 1 and isset($type)) {

    $data = array();
    $resellerID = $resellerIDs[0];
    $licenceDetails = serverAmount($resellerID);

    if (is_numeric($licenceDetails['left']) and (0 > $licenceDetails['left'] or 0 > $licenceDetails['lG'] or 0 > $licenceDetails['lVo'] or 0 > $licenceDetails['lVs'] or 0 > $licenceDetails['lD'])) {
        header('HTTP/1.1 403 Forbidden');
        die('403 Forbidden: More servers are stored than allowed!');
    }

    if ($ui->escaped('json', 'post')) {

        $apiType = 'json';
        $data = @json_decode(urldecode(base64_decode($ui->escaped('json', 'post'))));

        if (!$data) {
            header('HTTP/1.1 403 Forbidden');
            die('403 Forbidden: JSON not vaild');
        }

    } else if ($ui->escaped('xml', 'post')) {

        $apiType = 'xml';
        $data = @simplexml_load_string(urldecode(base64_decode($ui->escaped('xml', 'post'))));
        if (!$data) {
            header('HTTP/1.1 403 Forbidden');
            die('403 Forbidden: XML not valid');
        }

    } else {
        header('HTTP/1.1 403 Forbidden');
        die('403 Forbidden: Neither POST value xml, nor JSON has been send!');
    }

    $tempArray = array();
    $bad = array(false, null, '');
    $data = (array)$data;

    foreach ($data as $key => $value) {
        $tempArray[$key] = (is_object($value)) ? null : $value;
    }

    $data = $tempArray;
    unset($tempArray);

    $licenceDetails = serverAmount($resellerID);

    if (is_numeric($licenceDetails['left']) and (0 > $licenceDetails['left'] or 0 > $licenceDetails['lG'] or 0 > $licenceDetails['lVo'] or 0 > $licenceDetails['lVs'] or 0 > $licenceDetails['lD'])) {
        header('HTTP/1.1 403 Forbidden');
        die('403 Forbidden: Server amount already exceeds licence limits!');
    }

    $gsModule = (is_numeric($licenceDetails['mG']) and $licenceDetails['mG'] == 0) ? false : true;
    $vModule = (is_numeric($licenceDetails['mVs']) and $licenceDetails['mVs'] == 0) ? false : true;
    $voModule = (is_numeric($licenceDetails['mVo']) and $licenceDetails['mVo'] == 0) ? false : true;
    $dModule = (is_numeric($licenceDetails['mD']) and $licenceDetails['mD'] == 0) ? false : true;

    if ($type == 'list') {

        include(EASYWIDIR . '/stuff/api/api_list.php');

    } else if ($type == 'user') {

        include(EASYWIDIR . '/stuff/api/api_users.php');

    } else if ($type == 'web') {

        include(EASYWIDIR . '/stuff/api/api_web.php');

    } else if ($type == 'voice') {

        if ($voModule == true) {

            include(EASYWIDIR . '/stuff/api/api_voice.php');

        } else {

            header('HTTP/1.1 403 Forbidden');
            die('403 Forbidden: Voice module is inactive');

        }

    } else if ($type == 'tsdns') {

        if ($voModule == true) {

            include(EASYWIDIR . '/stuff/api/api_tsdns.php');

        } else {

            header('HTTP/1.1 403 Forbidden');
            die('403 Forbidden: Voice module is inactive');

        }

    } else if ($type == 'mysql') {

        include(EASYWIDIR . '/stuff/api/api_mysql.php');

    } else if ($type == 'gserver') {

        if ($gsModule == true) {

            include(EASYWIDIR . '/stuff/api/api_gserver.php');

        } else {

            header('HTTP/1.1 403 Forbidden');
            die('403 Forbidden: Gameserver module is inactive');

        }

    }

} else if (isset($resellerIDs) and count($resellerIDs) == 1 and $ui->smallletters('type', 10, 'post')) {

    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden: Type ' . $ui->smallletters('type', 10, 'post') . 'is not known');

} else if (isset($resellerIDs) and count($resellerIDs) == 1 and !isset($type)) {

    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden: Type is not defined');

} else {

    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden: No valid api data');

}