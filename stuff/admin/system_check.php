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

$modules = array('openssl', 'json', 'hash', 'ftp', 'SimpleXML', 'curl', 'gd', 'PDO', 'pdo_mysql', 'zip');

foreach ($modules as $module) {
    if (extension_loaded($module)) {
        $okField = "system_ok_$module";
        $systemCheckOk[$module] = $sprache->$okField;
    } else {
        $errorField = "error_system_$module";
        $systemCheckError[$module] = $sprache->$errorField;
    }
}

$functions = array('fopen');

foreach ($functions as $function) {
    if (function_exists($function)) {
        $okField = "system_ok_$function";
        $systemCheckOk[$function] = $function->$okField;
    } else {
        $errorField = "error_system_$function";
        $systemCheckError[$function] = $sprache->$errorField;
    }
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