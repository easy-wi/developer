<?php

/**
 * File: userpanel_backup.php.
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


include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/functions_gs.php');
include(EASYWIDIR . '/stuff/methods/class_app.php');

if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['ftpbackup']) or !$ui->id('id', 10, 'get')) {
    header('Location: userpanel.php');
    die;
}

$sprache = getlanguagefile('gserver', $user_language, $resellerLockupID);

$customer = getusername($user_id);

if ($ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['gs']))) {

    $id = (int) $ui->id('id', 10, 'get');
    $errors = array();

    $query = $sql->prepare("SELECT g.`serverip`,g.`port`,g.`rootID`,AES_DECRYPT(u.`ftpbackup`,?) AS `ftp` FROM `gsswitch` g INNER JOIN `userdata` AS u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($aeskey, $id, $user_id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $serverip = $row['serverip'];
        $port = $row['port'];
        $rootID = $row['rootID'];
        $ftpUploadString = $row['ftp'];
        $fdlData = ftpStringToData($row['ftp']);
        $ftp_adresse = $fdlData['server'];
        $ftp_password = $fdlData['pwd'];
        $ftp_port = $fdlData['port'];
        $ftp_user = $fdlData['user'];
        $ftp_path = $fdlData['path'];
    }

    if ($query->rowCount() == 0) {
        redirect('userpanel.php?w=bu&id=' . $id);
    }

	if ($ui->w('action', 3, 'get') == 'mb' and isset($rootID)) {

        $appServer = new AppServer($rootID);

        $appServer->getAppServerDetails($id);
        $appServer->backupCreate($ftpUploadString);
        $appServer->execute();

        $template_file = $sprache->backup_create;

        if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
            $template_file .= '<br><pre>' . implode("\r\n", $appServer->debug()) . '</pre>';
        }

    } else if ($ui->w('action',3, 'get') == 'md' and isset($rootID)) {

        $template_file = 'userpanel_gserver_backup_md.tpl';

    } else if ($ui->w('action',3, 'post') == 'md2' and isset($rootID)) {

        $ftp_adresse = ($ui->ip('ftp_adresse', 'post')) ? $ui->ip('ftp_adresse', 'post') : $ui->domain('ftp_adresse', 'post');

        $ftp_password = $ui->password('ftp_password', 255, 'post');
        $ftp_port = $ui->port('ftp_port', 'post');
        $ftp_user = $ui->username('ftp_user', 50, 'post');
        $ftp_path = $ui->path('ftp_path', 'post');

        if (!$ftp_adresse) {
            $errors['ftp_adresse'] = $sprache->ftp_adresse;
        }

        if (!$ftp_port) {
            $errors['ftp_port'] = $sprache->ftp_port;
        }

        if (!$ftp_user) {
            $errors['ftp_user'] = $sprache->ftp_user;
        }

        if (!$ftp_password) {
            $errors['ftp_password'] = $sprache->ftp_password;
        }

        if (count($errors) == 0) {

            $checkFtpData = checkFtpData($ftp_adresse, $ftp_port, $ftp_user, $ftp_password);

            if ($checkFtpData !== true and $checkFtpData == 'login') {
                $errors['ftp_user'] = $sprache->ftp_user;
                $errors['ftp_password'] = $sprache->ftp_password;

            } else if ($checkFtpData !== true and $checkFtpData == 'ipport') {
                $errors['ftp_adresse'] = $sprache->ftp_adresse;
                $errors['ftp_port'] = $sprache->ftp_port;
            }
        }

        if (count($errors) == 0) {
            if (substr($ftp_path, 0, 1) != '/') {
                $ftp_path = '/' . $ftp_path;
            }

            if (substr($ftp_path, -1, 1) != '/') {
                $ftp_path = $ftp_path . '/';
            }

            $fdlConnectString = 'ftp://' . $ftp_user . ':' . $ftp_password . '@' . $ftp_adresse . ':' . $ftp_port . $ftp_path;

            $query = $sql->prepare("UPDATE `userdata` SET `ftpbackup`=AES_ENCRYPT(?,?) WHERE `id`=? LIMIT 1");
            $query->execute(array($fdlConnectString, $aeskey, $user_id));
            $template_file = $spracheResponse->table_add;

        } else {
            unset($header, $text);
            $template_file = 'userpanel_gserver_backup_md.tpl';
        }

    } else if ($ui->w('action',3, 'get') == 'rb' and isset($rootID)) {

        $shortens = array();

        $query = $sql->prepare("SELECT DISTINCT(t.`shorten`) FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=?");
        $query->execute(array($id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $shortens[] = $row['shorten'];
            $shortens[] = $row['shorten'] . '-2';
            $shortens[] = $row['shorten'] . '-3';
        }

        $template_file = 'userpanel_gserver_backup_rb.tpl';

    } else if ($ui->w('action',3, 'post') == 'rb2' and $ui->gamestring('template', 'post') and isset($rootID)) {

        $shortens = array();

        $query = $sql->prepare("SELECT DISTINCT(t.`shorten`) FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=?");
        $query->execute(array($id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $shortens[] = $row['shorten'];
            $shortens[] = $row['shorten'] . '-2';
            $shortens[] = $row['shorten'] . '-3';
        }

        if (in_array($ui->gamestring('template', 'post'), $shortens)) {

            $appServer = new AppServer($rootID);

            $appServer->getAppServerDetails($id);
            $appServer->backupDeploy($ui->gamestring('template', 'post'), $ftpUploadString);
            $appServer->execute();

            $template_file = $sprache->backup_recover;

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $template_file .= '<br><pre>' . implode("\r\n", $appServer->debug()) . '</pre>';
            }

        } else {
            $template_file = 'userpanel_404.tpl';
        }

    } else {
        $template_file = 'userpanel_404.tpl';
    }
} else {
    redirect('userpanel.php');
}