<?php

/**
 * File: global_userdata.php.
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
include(EASYWIDIR . '/third_party/password_compat/password.php');

if ($ui->st('w', 'get') == 'se') {

    if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['usersettings'])) {
        header('Location: userpanel.php');
        die();
    }

    $loguserid = $user_id;
    $logusername = getusername($user_id);
    $logusertype = 'user';
    $logreseller = 0;

    if (isset($admin_id)) {
        $logsubuser = $admin_id;
    } else if (isset($subuser_id)) {
        $logsubuser = $subuser_id;
    } else {
        $logsubuser = 0;
    }

} else {

    if ((!isset($admin_id) or $main != 1)) {
        header('Location: admin.php');
        die();
    }

    $loguserid = $admin_id;
    $logusername = getusername($admin_id);
    $logusertype = 'admin';

    if ($reseller_id == 0) {
        $logreseller = 0;
        $logsubuser = 0;
    } else {
        $logsubuser = (isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
        $logreseller = 0;
    }
}

$sprache = getlanguagefile('user', $user_language, $reseller_id);

$lookUpID = ($ui->st('w', 'get') == 'se') ? $user_id : $admin_id;

if ($ui->st('d', 'get') == 'pw') {

    if (!$ui->smallletters('action',2, 'post')) {
        $template_file = ($logusertype == 'user') ? 'userpanel_pass.tpl' : 'admin_user_own_pass.tpl';

    } else if ($ui->smallletters('action',2, 'post') == 'md'){
        $errors = array();

        if (!$ui->password('password', 255, 'post')) {
            $errors[] = $sprache->error_pass;
        }

        if (!$ui->password('pass2', 255, 'post')) {
            $errors[] = $sprache->error_pas;
        }

        if ($ui->password('password', 255, 'post') != $ui->password('pass2', 255, 'post')) {
            $errors[] = $sprache->error_passw_succ;
        }

        if (!token(true)) {
            $errors[] = $spracheResponse->token;
        }

        if (count($errors)>0) {
            $template_file = implode('<br />', $errors);
        } else {

            $query = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($lookUpID, $reseller_id));
            $cname = $query->fetchColumn();

            $newHash = passwordCreate($cname, $ui->password('password', 255, 'post'));

            if (is_array($newHash)) {
                $query = $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`security`=?,`salt`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($newHash['hash'], $newHash['salt'], $lookUpID, $reseller_id));

            } else {
                $query = $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`security`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($newHash, $lookUpID, $reseller_id));
            }
            if ($query->rowCount() > 0) {
                $template_file = $spracheResponse->table_add;
                $loguseraction="%psw% %user% $cname";
                $insertlog->execute();
            } else {
                $template_file = $spracheResponse->error_table;
            }
        }

    } else {
        $template_file = 'userpanel_404.tpl';
    }

} else if ($ui->escaped('spUser', 'get') and $ui->id('spId', 10, 'get')) {

    $query = $sql->prepare("DELETE FROM `userdata_social_identities` WHERE `userID`=? AND `serviceProviderID`=? AND `serviceUserID`=? AND `resellerID`=? LIMIT 1");
    $query->execute(array($lookUpID, $ui->id('spId', 10, 'get'), $ui->escaped('spUser', 'get'), $reseller_id));

    if ($query->rowCount() > 0) {
        $template_file = $spracheResponse->table_del;
    } else {
        $template_file = $spracheResponse->error_table;
    }

} else {

    $query = $sql->prepare("SELECT * FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($lookUpID, $reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $cname = $row['cname'];
        $name = $row['name'];
        $vname = $row['vname'];
        $mail = $row['mail'];
        $phone = $row['phone'];
        $handy = $row['handy'];
        $city = $row['city'];
        $cityn = $row['cityn'];
        $street = $row['street'];
        $streetn = $row['streetn'];
        $mail_backup = $row['mail_backup'];
        $mail_serverdown = $row['mail_serverdown'];
        $mail_ticket = $row['mail_ticket'];
        $mail_gsupdate = $row['mail_gsupdate'];
        $mail_securitybreach = $row['mail_securitybreach'];
        $mail_vserver = $row['mail_vserver'];

        #https://github.com/easy-wi/developer/issues/5
        $oldValues = array();
        foreach ($row as $k => $v) {
            $oldValues[$k] = $v;
        }
    }

    if ($ui->smallletters('action', 2, 'post') != 'md' and $ui->w('added', 255, 'get')) {

        $template_file = $spracheResponse->table_add;

    } else if ($ui->smallletters('action', 2, 'post') != 'md') {

        $serviceProviders = array();

        $htmlExtraInformation['css'][] = '<link href="css/default/social_buttons.css" rel="stylesheet">';

        $query = $sql->prepare("SELECT `serviceProviderID`,`filename` FROM `userdata_social_providers` WHERE `resellerID`=0 AND `active`='Y'");
        $query2 = $sql->prepare("SELECT `serviceUserID` FROM `userdata_social_identities` WHERE `serviceProviderID`=? AND `userID`=? LIMIT 1");


        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $query2->execute(array($row['serviceProviderID'], $lookUpID));

            $serviceProviders[] = array(
                'spId' => $row['serviceProviderID'],
                'sp' => $row['filename'],
                'spUserId' => urlencode($query2->fetchColumn())
            );
        }
    }

    if ($ui->smallletters('action', 2, 'post') == 'md' and isset($oldValues)) {

        if ($ui->ismail('mail', 'post') and token(true)) {
            $mail_backup = ($ui->active('mail_backup', 'post')) ? $ui->active('mail_backup', 'post') : 'N';
            $mail_serverdown = ($ui->active('mail_serverdown', 'post')) ? $ui->active('mail_serverdown', 'post') : 'N';
            $mail_ticket = ($ui->active('mail_ticket', 'post')) ? $ui->active('mail_ticket', 'post') : 'N';
            $name = $ui->names('name', 30, 'post');
            $vname = $ui->names('vname', 30, 'post');
            $mail = $ui->ismail('mail', 'post');
            $phone = $ui->phone('phone', 30, 'post');
            $handy = $ui->phone('handy', 30, 'post');
            $city = $ui->names('city', 40, 'post');
            $cityn = $ui->isinteger('cityn', 'post');
            $street = $ui->names('street', 40, 'post');
            $streetn = $ui->streetNumber('streetn', 'post');

            if (($ui->st('w', 'get') == 'se')) {

                $query = $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`name`=?,`vname`=?,`mail`=?,`phone`=?,`handy`=?,`city`=?,`cityn`=?,`street`=?,`streetn`=?,`mail_backup`=?,`mail_serverdown`=?,`mail_ticket`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($name, $vname, $mail, $phone, $handy, $city, $cityn, $street, $streetn, $mail_backup, $mail_serverdown, $mail_ticket, $lookUpID, $reseller_id));

            } else {

                $mail_gsupdate = ($ui->active('mail_gsupdate', 'post')) ? $ui->active('mail_gsupdate', 'post') : 'N';
                $mail_securitybreach = ($ui->active('mail_securitybreach', 'post')) ? $ui->active('mail_securitybreach', 'post') : 'N';
                $mail_vserver = ($ui->active('mail_vserver', 'post')) ? $ui->active('mail_vserver', 'post') : 'N';

                $query = $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`name`=?,`vname`=?,`mail`=?,`phone`=?,`handy`=?,`city`=?,`cityn`=?,`street`=?,`streetn`=?,`mail_backup`=?,`mail_serverdown`=?,`mail_ticket`=?,`mail_gsupdate`=?,`mail_securitybreach`=?,`mail_vserver`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($name, $vname, $mail, $phone, $handy, $city, $cityn, $street, $streetn, $mail_backup, $mail_serverdown, $mail_ticket, $mail_gsupdate, $mail_securitybreach, $mail_vserver, $lookUpID, $reseller_id));

            }

            if ($query->rowCount() > 0) {
                #https://github.com/easy-wi/developer/issues/5
                $changed = array();
                foreach ($oldValues as $k => $v) {
                    if (isset($$k) and "{$$k}" != $v) {
                        $changed[$k] = $v;
                    }
                }

                $query = $sql->prepare("INSERT INTO `userdata_value_log` (`userID`,`date`,`json`,`resellerID`) VALUES (?,NOW(),?,?)");
                $query->execute(array($lookUpID, json_encode($changed), $reseller_id));

                $template_file = $spracheResponse->table_add;
                $loguseraction = '%mod% %user% ' . $cname;
                $insertlog->execute();

            } else {
                $template_file = $spracheResponse->error_table;
            }

        } else {
            $template_file = (!token(true)) ? $spracheResponse->token : $sprache->error_mail;
        }

    } else if (!isset($template_file)) {
        $template_file = ($logusertype == 'user') ? 'userpanel_user_md.tpl' : 'admin_user_own_md.tpl';
    }
}