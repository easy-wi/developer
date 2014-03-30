<?php

/**
 * File: userpanel_fdl.php.
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

if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['fastdl'])) {
	header('Location: userpanel.php');
	die('No acces');
}

require_once(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

$sprache = getlanguagefile('fastdl', $user_language, $reseller_id);
$gameSprache = getlanguagefile('gserver', $user_language, $reseller_id);
$loguserid = $user_id;
$logusername = getusername($user_id);
$logusertype = 'user';
$logreseller = 0;

if (isset($admin_id) and $reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}

if (isset($admin_id)) {
	$logsubuser = $admin_id;
} else if (isset($subuser_id)) {
	$logsubuser = $subuser_id;
} else {
	$logsubuser = 0;
}

if ($ui->st('d', 'get') == 'ud' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['gs']))) {

    $serverid = (int) $ui->id('id', 10, 'get');

    $query = $sql->prepare("SELECT g.`rootID`,g.`masterfdl`,g.`mfdldata`,g.`serverip`,g.`port`,g.`newlayout`,g.`protected`,s.`servertemplate`,t.`modfolder`,t.`shorten`,u.`fdlpath`,u.`cname` FROM `gsswitch` g LEFT JOIN `serverlist` s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` LEFT JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`active`='Y' AND g.`id`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($serverid, $reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $ftpupload = ($row['masterfdl'] == 'Y') ? $row['fdlpath'] : $row['mfdldata'];
        $shorten = ($row['servertemplate'] == 1) ? $row['shorten'] : $row['shorten'] . '-' . $row['servertemplate'];
        $customer = ($row['newlayout'] == 'Y') ? $row['cname'] . '-' . $serverid : $row['cname'];

        if ($row['protected'] == 'Y') {
            $customer .= '-p';
        }

        if ($ftpupload != '') {

            $serverfolder = $row['serverip'] . '_' . $row['port'] . '/' . $shorten;

            if (ssh2_execute('gs', $row['rootID'], 'sudo -u ' . $customer . ' ./control.sh fastdl "' . $customer . '"  "' . $serverfolder . '" "'. $ftpupload . '" "' . $row['modfolder'] . '"') === false) {
                $template_file = $spracheResponse->error_server;
                $actionstatus = 'fail';
            } else {
                $actionstatus = 'ok';
                $template_file = $sprache->fdlstarted;
            }


        } else {
            $template_file = $sprache->fdlfailed;
            $actionstatus = 'fail';
        }

        $loguseraction = '%start% %fastdl% ' . $row['serverip'] . ':' . $row['port'] . ' %' . $actionstatus . '%';
        $insertlog->execute();
    }

} else if ($ui->st('d', 'get') == 'es' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['gs']))) {

    $errors = array();
    $id = (int) $ui->id('id', 10, 'get');
    $masterfdl = $ui->active('masterfdl', 'post');

    if (!$ui->smallletters('action',2, 'post')) {

        $query = $sql->prepare("SELECT `serverip`,`port`,`mfdldata`,`masterfdl` FROM `gsswitch` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $serverip = $row['serverip'];
            $port = $row['port'];
            $masterfdl = $row['masterfdl'];
            $fdlData = ftpStringToData($row['mfdldata']);
            $ftp_adresse = $fdlData['server'];
            $ftp_password = $fdlData['pwd'];
            $ftp_port = $fdlData['port'];
            $ftp_user = $fdlData['user'];
            $ftp_path = $fdlData['path'];
        }

        $template_file = (isset($serverip)) ? 'userpanel_gserver_fdl_es.tpl' : 'userpanel_404.tpl';

    } else if ($ui->smallletters('action',2, 'post') == 'md') {


        $query = $sql->prepare("SELECT `serverip`,`port` FROM `gsswitch` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $serverip = $row['serverip'];
            $port = $row['port'];
        }

        if ($ui->active('masterfdl', 'post')) {

            $fdlConnectString = '';

            if ($ui->active('masterfdl', 'post') == 'N') {

                if ($ui->ip('ftp_adresse', 'post')) {
                    $ftp_adresse = $ui->ip('ftp_adresse', 'post');
                } else {
                    $ftp_adresse = $ui->domain('ftp_adresse', 'post');
                }

                $ftp_password = $ui->password('ftp_password', 20, 'post');
                $ftp_port = $ui->port('ftp_port', 'post');
                $ftp_user = $ui->username('ftp_user', 50, 'post');
                $ftp_path = $ui->path('ftp_path', 'post');

                if (!$ftp_adresse) {
                    $errors['ftp_adresse'] = $gameSprache->ftp_adresse;
                }

                if (!$ftp_port) {
                    $errors['ftp_port'] = $gameSprache->ftp_port;
                }

                if (!$ftp_user) {
                    $errors['ftp_user'] = $gameSprache->ftp_user;
                }

                if (!$ftp_password) {
                    $errors['ftp_password'] = $gameSprache->ftp_password;
                }

                if (count($errors) == 0) {

                    $checkFtpData = checkFtpData($ftp_adresse, $ftp_port, $ftp_user, $ftp_password);

                    if ($checkFtpData !== true and $checkFtpData == 'login') {
                        $errors['ftp_user'] = $gameSprache->ftp_user;
                        $errors['ftp_password'] = $gameSprache->ftp_password;
                    } else if ($checkFtpData !== true and $checkFtpData == 'ipport') {
                        $errors['ftp_adresse'] = $gameSprache->ftp_adresse;
                        $errors['ftp_port'] = $gameSprache->ftp_port;
                    }
                }

                if (substr($ftp_path, 0, 1) != '/') {
                    $ftp_path = '/' . $ftp_path;
                }

                if (substr($ftp_path, -1, 1) != '/') {
                    $ftp_path = $ftp_path . '/';
                }

                $fdlConnectString = 'ftp://' . $ftp_user . ':' . $ftp_password . '@' . $ftp_adresse . ':' . $ftp_port . $ftp_path;
            }


            if (count($errors) == 0) {

                if (isset($serverip) and isset($port)) {
                    $query = $sql->prepare("UPDATE `gsswitch` SET `mfdldata`=?, `masterfdl`=? WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($fdlConnectString, $ui->active('masterfdl', 'post'), $id, $reseller_id));

                    $loguseraction = '%mod% %fastdl% ' . $serverip . ':' . $port;
                    $insertlog->execute();

                    $template_file = ($query->rowCount() > 0) ? $spracheResponse->table_add : $spracheResponse->error_table;

                } else {
                    $template_file = 'userpanel_404.tpl';
                }
            } else {
                unset($header, $text);
                $template_file = 'userpanel_gserver_fdl_es.tpl';
            }

        } else {
            $template_file = 'userpanel_404.tpl';
        }

    } else {
        $template_file = 'userpanel_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'eu' and $pa['modfastdl'] == true) {

    $errors = array();

    if ($ui->ip('ftp_adresse', 'post')) {
        $ftp_adresse = $ui->ip('ftp_adresse', 'post');
    } else {
        $ftp_adresse = $ui->domain('ftp_adresse', 'post');
    }

    $ftp_password = $ui->password('ftp_password', 20, 'post');
    $ftp_port = $ui->port('ftp_port', 'post');
    $ftp_user = $ui->username('ftp_user', 50, 'post');
    $ftp_path = $ui->path('ftp_path', 'post');

    if (!$ui->smallletters('action',2, 'post')) {
        $query = $sql->prepare("SELECT `fdlpath` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($user_id, $reseller_id));
        $fdlData = ftpStringToData($query->fetchColumn());
        $ftp_adresse = $fdlData['server'];
        $ftp_password = $fdlData['pwd'];
        $ftp_port = $fdlData['port'];
        $ftp_user = $fdlData['user'];
        $ftp_path = $fdlData['path'];

        $template_file = ($query->rowCount() > 0) ? 'userpanel_gserver_fdl_eu.tpl' : 'userpanel_404.tpl';

    } else if ($ui->smallletters('action',2, 'post') == 'md') {

        if (!$ftp_adresse) {
            $errors['ftp_adresse'] = $gameSprache->ftp_adresse;
        }

        if (!$ftp_port) {
            $errors['ftp_port'] = $gameSprache->ftp_port;
        }

        if (!$ftp_user) {
            $errors['ftp_user'] = $gameSprache->ftp_user;
        }

        if (!$ftp_password) {
            $errors['ftp_password'] = $gameSprache->ftp_password;
        }

        if (count($errors) == 0) {

            $checkFtpData = checkFtpData($ftp_adresse, $ftp_port, $ftp_user, $ftp_password);

            if ($checkFtpData !== true and $checkFtpData == 'login') {
                $errors['ftp_user'] = $gameSprache->ftp_user;
                $errors['ftp_password'] = $gameSprache->ftp_password;
            } else if ($checkFtpData !== true and $checkFtpData == 'ipport') {
                $errors['ftp_adresse'] = $gameSprache->ftp_adresse;
                $errors['ftp_port'] = $gameSprache->ftp_port;
            }
        }

        if (count($errors) > 0) {
            unset($header, $text);
            $template_file = 'userpanel_gserver_fdl_eu.tpl';
        } else {

            if (substr($ftp_path, 0, 1) != '/') {
                $ftp_path = '/' . $ftp_path;
            }

            if (substr($ftp_path, -1, 1) != '/') {
                $ftp_path = $ftp_path . '/';
            }

            $fdlConnectString = 'ftp://' . $ftp_user . ':' . $ftp_password . '@' . $ftp_adresse . ':' . $ftp_port . $ftp_path;
            $query = $sql->prepare("UPDATE `userdata` SET `fdlpath`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($fdlConnectString, $user_id, $reseller_id));

            if ($query->rowCount() > 0) {

                $template_file = $spracheResponse->table_add;
                $loguseraction = '%mod% %fastdl% %master%';
                $insertlog->execute();

                // No update
            } else {
                $template_file = $spracheResponse->error_table;
            }
        }

    } else {
        $template_file = 'userpanel_404.tpl';
    }
} else {

    $table = array();

    $query = $sql->prepare("SELECT `cname`,`fdlpath` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($user_id, $reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $fdlpath=explode('@', $row['fdlpath']);
        $username = $row['cname'];
    }

    if (!isset($fdlpath[1])) {
        $fdlpath[1] = $sprache->noset;
    }

    $query = $sql->prepare("SELECT `id`,`serverip`,`port` FROM `gsswitch` WHERE `active`='Y' AND `userid`=? AND `resellerid`=?");
    $query->execute(array($user_id, $reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!isset($_SESSION['sID']) or in_array($row['id'], $substituteAccess['gs'])) {
            $table[] = array('id' => $row['id'], 'serverip' => $row['serverip'], 'port' => $row['port']);
        }
    }

    $template_file = 'userpanel_gserver_fdl_list.tpl';
}
