<?php

/**
 * File: server_log.php.
 * Author: Ulrich Block
 * Date: 02.09.15
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

if (!isset($resellerLockupID)) {
    $resellerLockupID = $reseller_id;
}

$array = array('lastLog' => 0, 'log' => '');

$query = $sql->prepare("SELECT r.`ip` AS `ftp_ip`,r.`ftpport`,u.`cname`,g.`id`,g.`newlayout`,g.`rootID`,g.`serverip`,g.`port`,g.`protected`,AES_DECRYPT(g.`ftppassword`,?) AS `dftppass`,AES_DECRYPT(g.`ppassword`,?) AS `decryptedftppass`,s.`servertemplate`,t.`binarydir`,t.`shorten` FROM `gsswitch` AS g INNER JOIN `userdata` AS u ON u.`id`=g.`userid` INNER JOIN `rserverdata` AS r ON r.`id`=g.`rootID` INNER JOIN `serverlist` AS s ON g.`serverid`=s.`id` INNER JOIN `servertypes` AS t ON s.`servertype`=t.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
$query->execute(array($aeskey, $aeskey, $ui->id('id', 10, 'get'), $user_id, $resellerLockupID));
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    if ($ui->escaped('cmd', 'post')) {

        $appServer = new AppServer($row['rootID']);
        $appServer->getAppServerDetails($ui->id('id', 10, 'get'));
        $appServer->shellCommand($ui->escaped('cmd', 'post'));
        $appServer->execute();

    } else {

        $shorten = $row['shorten'];
        $ftppass = $row['dftppass'];
        $username = ($row['newlayout'] == 'Y') ? $row['cname'] . '-' . $row['id'] : $row['cname'];

        if ($row['protected'] == 'N' and $row['servertemplate'] > 1) {
            $shorten .= '-' . $row['servertemplate'];
            $pserver = 'server/';
        } else if ($row['protected'] == 'Y') {
            $username .= '-p';
            $ftppass = $row['decryptedftppass'];
            $pserver = '';
        } else {
            $pserver = 'server/';
        }

        $ftpConnect = new EasyWiFTP($row['ftp_ip'], $row['ftpport'], $username, $ftppass);

        $downloadChrooted = $ftpConnect->removeSlashes($pserver . $row['serverip'] . '_' . $row['port'] . '/' . $shorten . '/' . $row['binarydir'] . '/screenlog.0');

        if ($ftpConnect->ftpConnection) {

            if (!$ftpConnect->downloadToTemp($downloadChrooted, 32768, false, $ui->isinteger('lastLog', 'get'))) {
                $array['error'] = 'Cannot download screenlog from ' . $downloadChrooted;
            } else {
                $array['lastLog'] = $ftpConnect->getLastFileSize();
                $array['log'] = nl2br(htmlentities($ftpConnect->getTempFileContent()));
            }

        } else {
            $array['error'] = 'Cannot connect to FTP Server ' . $row['ftp_ip'] . ':' . $row['ftpport'];
        }
    }
}

if ($query->rowCount() < 1) {
    $array['error'] = 'Error: No rootID';
}

die(json_encode($array));