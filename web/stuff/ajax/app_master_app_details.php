<?php

/**
 * File: app_master_app_details.php.
 * Author: Ulrich Block
 * Date: 27.09.14
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

$sprache = getlanguagefile('gserver', $user_language, $resellerLockupID);

$table = array();

$ids = (array) $ui->id('id', 10, 'get');
$query = $sql->prepare("SELECT `id`,`shorten`,`description`,`steamgame`,`fps`,`map`,`mapGroup`,`cmd`,`tic`,`gamebinary` FROM `servertypes` WHERE `id`=? AND `resellerid`=? LIMIT 1");
$query2 = $sql->prepare("SELECT `fps`,`map`,`mapGroup`,`cmd`,`owncmd`,`tic`,`userfps`,`usertick`,`usermap`,`user_uploaddir`,`upload`,AES_DECRYPT(`uploaddir`,?) AS `upload_dir` FROM `serverlist` WHERE `switchID`=? AND `servertype`=? AND `resellerid`=? LIMIT 1");

foreach ($ids as $id) {

    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        if ($ui->id('gameServerID', 10, 'get')) {

            unset($fps, $map, $mapGroup, $cmd, $ownCmd, $tic, $userFps, $userTick, $userMap, $userUploaddir, $upload, $uploadDir);

            $query2->execute(array($aeskey, $ui->id('gameServerID', 10, 'get'), $id, $resellerLockupID));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                $fps = $row2['fps'];
                $map = $row2['map'];
                $mapGroup = $row2['mapGroup'];
                $cmd = stripslashes($row2['cmd']);
                $ownCmd = $row2['owncmd'];
                $tic = $row2['tic'];
                $userFps = $row2['userfps'];
                $userTick = $row2['usertick'];
                $userMap = $row2['usermap'];
                $userUploadDir = $row2['user_uploaddir'];
                $upload = $row2['upload'];
                $uploadDir = $row2['upload_dir'];
            }
        }

        $uploadType = ($row['gamebinary'] == 'srcds_run') ? 1 : 0;

        $table[] = array(
            'id' => $row['id'],
            'shorten' => $row['shorten'],
            'description' => $row['description'],
            'steamgame' => $row['steamgame'],
            'fps' => (isset($fps))? $fps : $row['fps'],
            'userFps' => (isset($userFps))? $userFps : 'N',
            'map' => (isset($map)) ? $map : $row['map'],
            'mapGroup' => (isset($mapGroup)) ? $mapGroup : $row['mapGroup'],
            'userMap' => (isset($userMap))? $userMap : 'Y',
            'cmd' => (isset($cmd)) ? $cmd : stripslashes($row['cmd']),
            'ownCmd' => (isset($ownCmd))? $ownCmd : 'N',
            'tic' => (isset($tic)) ? $tic : $row['tic'],
            'userTick' => (isset($userTick))? $userTick : 'N',
            'upload' => (isset($upload)) ? $upload : $uploadType,
            'uploadDir' => (isset($uploadDir)) ? $uploadDir : '',
            'userUploadDir' => (isset($userUploadDir)) ? $userUploadDir : 'N'
        );
    }
}

require_once IncludeTemplate($template_to_use, 'ajax_admin_app_details.tpl', 'ajax');