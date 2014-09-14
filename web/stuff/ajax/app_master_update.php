<?php

/**
 * File: app_master_update.php.
 * Author: Ulrich Block
 * Date: 14.09.14
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

if (!defined('AJAXINCLUDED')) {
    die('Do not access directly!');
}

include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/methods/class_masterserver.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

$gamelist = array();

$sprache = getlanguagefile('roots', $user_language, $resellerLockupID);

$rootServer = new masterServer($ui->id('serverID', 10, 'get'), $aeskey);

$query = $sql->prepare("SELECT `shorten` FROM `servertypes` WHERE `id`=? AND `resellerid`=? LIMIT 1");
foreach($ui->id('masterIDs', 10, 'get') as $masterID) {

    $query->execute(array($masterID, $resellerLockupID));

    $gameShorten = $query->fetchColumn();

    if (strlen($gameShorten) > 0) {

        $gamelist[] = $gameShorten;

        $rootServer->collectData($masterID, true);
    }
}

$sshcmd = $rootServer->returnCmds('install', 'all');

if ($rootServer->sshcmd === null) {

    echo 'Nothing to update/sync!';

} else {

    if (ssh2_execute('gs', $ui->id('serverID', 10, 'get'), $rootServer->sshcmd) === false) {
        echo $sprache->error_root_updatemaster . ' ( ' . implode(', ', $gamelist) . ' )';
    } else {
        $rootServer->setUpdating();
        echo $sprache->root_updatemaster . ' ( ' . implode(', ', $gamelist) . ' )';
    }

    if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
        echo '<br>' . implode('<br>', $rootServer->sshcmd);
    }
}