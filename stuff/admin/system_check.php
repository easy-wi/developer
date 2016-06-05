<?php

/**
 * File: eac.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and (!isset($reseller_id) or $reseller_id != 0)) or !$pa['settings']) {
    header('Location: admin.php');
    die('No Access');
}

$sprache = getlanguagefile('system_check', $user_language, $reseller_id);

$systemCheckOk = array();
$systemCheckError = array();

if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
    $systemCheckOk['php'] = $sprache->system_ok_php_version . PHP_VERSION;
} else {
    $systemCheckError['php'] = $sprache->error_system_php_version . PHP_VERSION;
}

if (extension_loaded('openssl')) {
    $systemCheckOk['openssl'] = $sprache->system_ok_openssl;
} else {
    $systemCheckError['openssl'] = $sprache->error_system_openssl;
}

if (extension_loaded('json')) {
    $systemCheckOk['json'] = $sprache->system_ok_json;
} else {
    $systemCheckError['json'] = $sprache->error_system_json;
}

if (extension_loaded('hash')) {
    $systemCheckOk['hash'] = $sprache->system_ok_hash;
} else {
    $systemCheckError['hash'] = $sprache->error_system_hash;
}

if (extension_loaded('ftp')) {
    $systemCheckOk['ftp'] = $sprache->system_ok_ftp;
} else {
    $systemCheckError['ftp'] = $sprache->error_system_ftp;
}

if (extension_loaded('SimpleXML')) {
    $systemCheckOk['SimpleXML'] = $sprache->system_ok_SimpleXML;
} else {
    $systemCheckError['SimpleXML'] = $sprache->error_system_SimpleXML;
}

if (extension_loaded('curl')) {
    $systemCheckOk['curl'] = $sprache->system_ok_curl;
} else {
    $systemCheckError['curl'] = $sprache->error_system_curl;
}

if (extension_loaded('gd')) {
    $systemCheckOk['gd'] = $sprache->system_ok_gd;
} else {
    $systemCheckError['gd'] = $sprache->error_system_gd;
}

if (extension_loaded('PDO')) {
    $systemCheckOk['PDO'] = $sprache->system_ok_PDO;
} else {
    $systemCheckError['PDO'] = $sprache->error_system_PDO;
}

if (extension_loaded('pdo_mysql')) {
    $systemCheckOk['pdo_mysql'] = $sprache->system_ok_pdo_mysql;
} else {
    $systemCheckError['pdo_mysql'] = $sprache->error_system_pdo_mysql;
}

if (function_exists('fopen')) {
    $systemCheckOk['fopen'] = $sprache->system_ok_fopen;
} else {
    $systemCheckError['fopen'] = $sprache->error_system_fopen;
}

$folderArray = array(
    'css/',
    'css/default/',
    'images/',
    'images/flags',
    'images/games/',
    'images/games/icons/',
    'js/',
    'js/default/',
    'keys/',
    'languages/',
    'languages/default/',
    'languages/default/de/',
    'languages/default/dk/',
    'languages/default/uk',
    'stuff/',
    'stuff/admin/',
    'stuff/api/',
    'stuff/cms/',
    'stuff/custom_modules/',
    'stuff/jobs/',
    'stuff/methods/',
    'stuff/user/',
    'template/',
    'template/default/',
    'third_party/',
    'tmp/'
);

foreach ($folderArray as $folder) {
    if (is_dir(EASYWIDIR . "/${folder}")) {
        $handle = @fopen(EASYWIDIR . "/${folder}test.txt", "w+");

        if ($handle) {
            fclose($handle);
            unlink(EASYWIDIR . "/${folder}test.txt");
            $systemCheckOk['folders'][] = "Folder exists and can write to: ${folder}";

        } else {
            $systemCheckError['folders'][] = "Folder exists but cannot edit files: ${folder}";
        }
    } else {
        $systemCheckError['folders'][] = "Folder does not exist or cannot access: ${folder}";
    }
}

$displayPHPUser = ($ui->escaped('USER', 'server')) ? $ui->escaped('USER', 'server') : 'changeToPHPUser';

$query = $sql->prepare("SELECT `pageurl` FROM `page_settings` WHERE `id`=1 LIMIT 1");
$query->execute();
$pageUrl = $query->fetchColumn();

$template_file = 'admin_system_check.tpl';