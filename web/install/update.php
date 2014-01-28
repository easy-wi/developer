<?php

/**
 * File: update.php.
 * Author: Ulrich Block
 * Date: 03.08.12
 * Time: 17:09
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


if (!isset($updateinclude) or $updateinclude == false) {

    ini_set('display_errors',1);
    error_reporting(E_ALL|E_STRICT);

    define('EASYWIDIR', dirname(dirname(__FILE__)));

    function isinteger($value) {
        return (preg_match("/^[\d+(.\d+|$)]+$/", $value) or $value == 0) ? true : false;
    }

    class UpdateResponse {
        public $response = '';
        function __construct() {
            $this->response = '';
        }
        function add ($newtext) {
            $this->response .= $newtext;
        }
        function printresponse () {
            return $this->response;
        }
        function __destruct() {
            unset($this->response);
        }
    }


    $response = new UpdateResponse();

} else if (!defined('EASYWIDIR')) {
    define('EASYWIDIR', dirname(dirname(__FILE__)));
}

if (!isset($sql)) {

    include(EASYWIDIR . '/stuff/config.php');

    try {
        $sql = new PDO("$databanktype:host=$host;dbname=$db", $user, $pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }
    catch(PDOException $error) {
        echo $error->getMessage();
        die();
    }
}

function versioncheck ($current, $new ,$file ,$response) {

    global $sql;

    $include = true;

    if ($current < $new) {
        $response->add("Upgrading Database from $current to $new<br />");
        if (is_file(EASYWIDIR . '/' . $file)) {
            $response->add('Found updaterfile ' . $file . '. Executing it now<br>');
            include(EASYWIDIR . '/' . $file);
        } else if (is_file(EASYWIDIR . '/install/' . $file)) {
            $response->add('Found updaterfile ' . EASYWIDIR . 'install/' . $file . '. Executing it now<br>');
            include(EASYWIDIR . '/install/' . $file);
        } else {
            die("File $file is missing<br />");
        }

        if ($new < '2.08' and isset($sql)) {
            $update_easywiversion = $sql->prepare("UPDATE `easywi_version` SET `version`=?");
            $update_easywiversion->execute(array($new));
            $response->add('<br />Action: update_easywiversion done: ');
            $error = $update_easywiversion->errorinfo();
            $update_easywiversion->closecursor();
            if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
            else $response->add('OK<br />');
        }

        return true;
    } else {
        return false;
    }
}

$query = $sql->prepare("SELECT `version` FROM `easywi_version` ORDER BY `id` DESC LIMIT 1");
$query->execute();
$version = $query->fetchColumn();
$admin_id = 1;
$main = 1;
$reseller_id = 0;
$error = $query->errorinfo();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) {
    $response->add("Current database version: 1.9<br />");
    $version = '1.9';
} else {
    $response->add("Current database version: $version<br />");
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

if (versioncheck ($version, '2.00', 'update_1x-20.php', $response)) {
    $version = '2.00';
}
if (versioncheck ($version, '2.01', 'update_200-201.php', $response)) {
    $version = '2.01';
}
if (versioncheck ($version, '2.02', 'update_201-202.php', $response)) {
    $version = '2.02';
}
if (versioncheck ($version, '2.03', 'update_202-203.php', $response)) {
    $version = '2.03';
}
if (versioncheck ($version, '2.04', 'update_203-204.php', $response)) {
    $version = '2.04';
}
if (versioncheck ($version, '2.05', 'update_204-205.php', $response)) {
    $version = '2.05';
}
if (versioncheck ($version, '2.06', 'update_205-206.php', $response)) {
    $version = '2.06';
}
if (versioncheck ($version, '2.07', 'update_206-207.php', $response)) {
    $version = '2.07';
}
if (versioncheck ($version, '2.08', 'update_207-208.php', $response)) {
    $version = '2.08';
}
if (versioncheck ($version, '2.09', 'update_208-209.php', $response)) {
    $version = '2.09';
}
if (versioncheck ($version, '2.10', 'update_209-210.php', $response)) {
    $version = '2.10';
}

include(EASYWIDIR . '/stuff/tables_add.php');

if (versioncheck ($version, '2.11', 'update_210-211.php', $response)) {
    $version = '2.11';
}
if (versioncheck ($version, '3.00', 'update_211-300.php', $response)) {
    $version = '3.00';
}
if (versioncheck ($version, '3.01', 'update_300-301.php', $response)) {
    $version = '3.01';
}
if (versioncheck ($version, '3.02', 'update_301-302.php', $response)) {
    $version = '3.02';
}
if (versioncheck ($version, '3.03', 'update_302-303.php', $response)) {
    $version = '3.03';
}
if (versioncheck ($version, '3.04', 'update_303-304.php', $response)) {
    $version = '3.04';
}
if (versioncheck ($version, '3.05', 'update_304-305.php', $response)) {
    $version = '3.05';
}
if (versioncheck ($version, '3.06', 'update_305-306.php', $response)) {
    $version = '3.06';
}
if (versioncheck ($version, '3.07', 'update_306-307.php', $response)) {
    $version = '3.07';
}
if (versioncheck ($version, '3.08', 'update_307-308.php', $response)) {
    $version = '3.08';
}
if (versioncheck ($version, '3.09', 'update_308-309.php', $response)) {
    $version = '3.09';
}
if (versioncheck ($version, '3.10', 'update_309-310.php', $response)) {
    $version = '3.10';
}
if (versioncheck ($version, '3.20', 'update_310-320.php', $response)) {
    $version = '3.20';
}
if (versioncheck ($version, '3.30', 'update_320-330.php', $response)) {
    $version = '3.30';
}
if (versioncheck ($version, '3.40', 'update_330-340.php', $response)) {
    $version = '3.40';
}
if (versioncheck ($version, '3.60', 'update_340-360.php', $response)) {
    $version = '3.60';
}
if (versioncheck ($version, '3.70', 'update_360-370.php', $response)) {
    $version = '3.70';
}
if (versioncheck ($version, '4.00', 'update_370-400.php', $response)) {
    $version = '4.00';
}
if (versioncheck ($version, '4.10', 'update_400-410.php', $response)) {
    $version = '4.10';
}
if (versioncheck ($version, '4.11', 'update_410-411.php', $response)) {
    $version = '4.11';
}
if (versioncheck ($version, '4.20', 'update_411-420.php', $response)) {
    $version = '4.20';
}
if (versioncheck ($version, '4.30', 'update_420-430.php', $response)) {
    $version = '4.30';
}

$response->add('Repairing tables if needed.');

include(EASYWIDIR . '/stuff/tables_repair.php');

include(EASYWIDIR . '/stuff/tables_entries_repair.php');

# Ende
if (!isset($updateinclude) or $updateinclude == false) {
    $response->add("<br />Database successfully updated!<br /> <b> Please remove the \"install/\" folder and all of it´s content.</b>");
    echo $response->printresponse();
    $sql = null;
}