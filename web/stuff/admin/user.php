<?php

/**
 * File: user.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['user'] and !$pa['user_users'] and !$pa['userPassword'])) {
    header('Location: admin.php');
    die();
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/third_party/password_compat/password.php');

$sprache = getlanguagefile('user', $user_language, $reseller_id);
$rsprache = getlanguagefile('reseller', $user_language, $reseller_id);
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

// Define the ID variable which will be used at the form and SQLs
$id = $ui->id('id', 10, 'get');

// Default variables. Mostly needed for the add operation
$externalID = $ui->escaped('externalID', 'post');
$passwordRepeat = $ui->password('pass2', 255, 'post');
$salutation = $ui->isinteger('salutation', 'post');
$name = $ui->names('name', 255, 'post');
$vname = $ui->names('vname', 255, 'post');
$mail = $ui->ismail('mail', 'post');
$phone = $ui->phone('phone', 'post');
$handy = $ui->phone('handy', 'post');
$city = $ui->names('city', 50, 'post');
$cityn = $ui->id('cityn', 6, 'post');
$street = $ui->names('street', 50, 'post');
$streetn = $ui->streetNumber('streetn', 'post');
$country = $ui->st('country', 'post');
$fax = $ui->phone('fax', 'post');
$fdlpath = $ui->url('fdlpath', 'post');

$active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
$useractive = ($ui->active('useractive', 'post')) ? $ui->active('useractive', 'post') : 'Y';
$accountType = ($ui->smallletters('accounttype', 1, 'post')) ? $ui->smallletters('accounttype', 1, 'post') : '';
$password = ($ui->password('password', 255, 'post')) ? $ui->password('password', 255, 'post') : passwordgenerate(10);
$birthday = date('Y-m-d', strtotime($ui->isDate('birthday', 'post')));
$maxuser = ($ui->id('maxuser', 10, 'post')) ? $ui->id('maxuser', 10, 'post') : 0;
$maxgserver = ($ui->id('maxgserver', 10, 'post')) ? $ui->id('maxgserver', 10, 'post') : 0;
$maxvoserver = ($ui->id('maxvoserver', 10, 'post')) ? $ui->id('maxvoserver', 10, 'post') : 0;
$maxvserver = ($ui->id('maxvserver', 10, 'post') and $easywiModules['ro']) ? $ui->id('maxvserver', 10, 'post') : 0;
$maxdedis = ($ui->id('maxdedis', 10, 'post') and $easywiModules['ro']) ? $ui->id('maxdedis', 10, 'post') : 0;
$maxuserram = ($ui->id('maxuserram', 255, 'post') and $easywiModules['ro']) ? $ui->id('maxuserram', 255, 'post') : 0;
$maxusermhz = ($ui->id('maxusermhz', 255, 'post') and $easywiModules['ro']) ? $ui->id('maxusermhz', 255, 'post') : 0;

$mail_backup = ($ui->active('mail_backup', 'post')) ? $ui->active('mail_backup', 'post') : 'N';
$mail_serverdown = ($ui->active('mail_serverdown', 'post')) ? $ui->active('mail_serverdown', 'post') : 'N';
$mail_ticket = ($ui->active('mail_ticket', 'post')) ? $ui->active('mail_ticket', 'post') : 'N';
$mail_gsupdate = ($ui->active('mail_gsupdate', 'post')) ? $ui->active('mail_gsupdate', 'post') : 'N';
$mail_securitybreach = ($ui->active('mail_securitybreach', 'post')) ? $ui->active('mail_securitybreach', 'post') : 'N';
$mail_vserver = ($ui->active('mail_vserver', 'post')) ? $ui->active('mail_vserver', 'post') : 'N';

if ($accountType == 'a' and $ui->username('acname', 255, 'post')) {
    $cname = $ui->username('acname', 255, 'post');
} else if ($accountType == 'r' and $ui->username('rcname', 255, 'post')) {
    $cname = $ui->username('rcname', 255, 'post');
} else if ($accountType == 'u' and $ui->username('cname', 255, 'post')) {
    $cname = $ui->username('cname', 255, 'post');
} else {
    $cname = $rSA['prefix2'];
}

$bogus = $cname . $mail;

if ($accountType == 'r') {

    $userGroups = (array) $ui->id('groups_r', 10, 'post');

    $mail_gsupdate = ($ui->active('rmail_gsupdate', 'post')) ? $ui->active('rmail_gsupdate', 'post') : 'N';
    $mail_securitybreach = ($ui->active('rmail_securitybreach', 'post')) ? $ui->active('rmail_securitybreach', 'post') : 'N';
    $mail_vserver = ($ui->active('rmail_vserver', 'post')) ? $ui->active('rmail_vserver', 'post') : 'N';

} else if ($accountType == 'a') {
    $userGroups = (array) $ui->id('groups_a', 10, 'post');
} else if ($accountType == 'u') {
    $userGroups = (array) $ui->id('groups_u', 10, 'post');
} else if ($ui->id('groups', 10, 'post')) {
    $userGroups = (array) $ui->id('groups', 10, 'post');
} else {
    $userGroups = array();
}

$query = $sql->prepare("SELECT `accounttype` FROM `userdata` WHERE `id`=? LIMIT 1");
$query->execute(array($admin_id));
$userAccounttype = $query->fetchColumn();

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {

    unset($header, $text);

    $errors = array($spracheResponse->token);

    $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_user_add.tpl' : 'admin_user_md.tpl';

// Add and modify entries. Same validation can be used.
} else if (($ui->st('d', 'get') == 'ad' or ($ui->st('d', 'get') == 'md') and ($id != $admin_id or $reseller_id == 0)) and ($pa['user'] or $pa['user_users']) and (($accountType == 'a' and $pa['user']) or $accountType != 'a' and ($pa['user'] or $pa['user_users']))) {

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    $selectlanguages = getlanguages($template_to_use);

    $groups = array('a' => array(), 'r' => array(), 'u' => array());
    $defaultGroups = array();

    $query = $sql->prepare("SELECT `id`,`grouptype`,`name`,`defaultgroup` FROM `usergroups` WHERE `active`='Y' AND `resellerid`=?");
    $query->execute(array($resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        if ($row['defaultgroup'] == 'Y') {
            $defaultGroups[$row['grouptype']][$row['id']] = $row['name'];
        }

        $groups[$row['grouptype']][$row['id']] = $row['name'];
    }

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $template_file = 'admin_user_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = ($reseller_id == 0) ? $sql->prepare("SELECT * FROM `userdata` WHERE id=? AND (`resellerid`=? OR `id`=resellerid) LIMIT 1") : $sql->prepare("SELECT * FROM `userdata` WHERE id=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $jobPending = $gsprache->no;
                $active = $row['active'];

                if ($row['jobPending'] == 'Y') {
                    $query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='us' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");
                    $query2->execute(array($row['id'], $row['resellerid']));
                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                        if ($row2['action'] == 'ad') {
                            $jobPending = $gsprache->add;
                        } else if ($row2['action'] == 'dl') {
                            $jobPending = $gsprache->del;
                        } else {
                            $jobPending = $gsprache->mod;
                        }

                        $json = @json_decode($row2['extraData']);
                        $active = (is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
                    }
                }

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
                $fdlpath = $row['fdlpath'];
                $accountType = $row['accounttype'];
                $salutation = $row['salutation'];
                $birthday = $row['birthday'];
                $country = $row['country'];
                $fax = $row['fax'];
                $mail_backup = $row['mail_backup'];
                $mail_gsupdate = $row['mail_gsupdate'];
                $mail_securitybreach = $row['mail_securitybreach'];
                $mail_serverdown = $row['mail_serverdown'];
                $mail_ticket = $row['mail_ticket'];
                $mail_vserver = $row['mail_vserver'];
                $creationTime = $row['creationTime'];
                $updateTime = $row['updateTime'];
                $externalID = $row['externalID'];

                if ($user_language == 'de') {
                    $creationTime = date('d-m-Y H:i:s', strtotime($row['creationTime']));
                    $updateTime = date('d-m-Y H:i:s', strtotime($row['updateTime']));
                }
            }

            if ($query->rowCount() > 0) {

                $groupsAssigned = array();

                $query = $sql->prepare("SELECT `groupID` FROM `userdata_groups` WHERE `userID`=?");
                $query->execute(array($id));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $groupsAssigned[] = $row['groupID'];
                }

                if ($accountType == 'r') {

                    $query = $sql->prepare("SELECT * FROM `resellerdata` WHERE `resellerid`=?");
                    $query->execute(array($id));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $useractive = $row['useractive'];
                        $maxuser = $row['maxuser'];
                        $maxgserver = $row['maxgserver'];
                        $maxvoserver = $row['maxvoserver'];
                        $maxdedis = $row['maxdedis'];
                        $maxvserver = $row['maxvserver'];
                        $maxuserram = $row['maxuserram'];
                        $maxusermhz = $row['maxusermhz'];
                    }
                }

                $template_file = 'admin_user_md.tpl';

            } else {
                $template_file =  'admin_404.tpl';
            }

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$active) {
            $errors['active'] = $sprache->active;
        }

        if (!$mail){
            $errors['mail'] = $sprache->error_mail;
        } else {

            $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userdata` WHERE `mail`=? AND `id`!=? LIMIT 1");
            $query->execute(array($mail, $id));

            if ($query->fetchColumn() > 0) {
                $errors['mail'] = $sprache->error_mail_exists;
            }
        }

        if ($ui->st('action', 'post') == 'ad') {

            if (!$password) {
                $errors['password'] = $sprache->error_pass;
            }

            if (!in_array($accountType, array('a', 'r', 'u'))) {
                $errors['accounttype'] = $sprache->accounttype;
            }

            if ($rSA['prefix1'] == 'Y' and $accountType != 'a') {

                $cname = $rSA['prefix2'];

            } else {

                if ($rSA['prefix2'] == $cname) {
                    $errors['cname'] = $sprache->nickname;
                } else {

                    $bogus = $cname;
                    $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? LIMIT 1");
                    $query->execute(array($cname));

                    if ($query->rowCount() > 0) {
                        $errors['cname'] = $sprache->nickname;
                    } else {

                        # https://github.com/easy-wi/developer/issues/2 "Substitutes"
                        $query = $sql->prepare("SELECT 1 FROM `userdata_substitutes` WHERE `loginName`=? LIMIT 1");
                        $query->execute(array($cname));

                        if ($query->rowCount() > 0) {
                            $errors['cname'] = $sprache->nickname;
                        }
                    }
                }
            }
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `userdata` (`creationTime`,`updateTime`,`active`,`salutation`,`birthday`,`country`,`fax`,`cname`,`security`,`name`,`vname`,`mail`,`phone`,`handy`,`city`,`cityn`,`street`,`streetn`,`fdlpath`,`accounttype`,`mail_backup`,`mail_gsupdate`,`mail_securitybreach`,`mail_serverdown`,`mail_ticket`,`mail_vserver`,`externalID`) VALUES (NOW(),NOW(),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $query->execute(array($active, $salutation, $birthday, $country, $fax, $bogus, $password, $name, $vname, $mail, $phone, $handy, $city, $cityn, $street, $streetn, $fdlpath, $accountType, $mail_backup, $mail_gsupdate, $mail_securitybreach, $mail_serverdown, $mail_ticket, $mail_vserver, $externalID));

                $id = $sql->lastInsertId();

                $rowCount = $query->rowCount();

                if ($rSA['prefix1'] == 'Y' and $accountType != 'a') {
                    $cname = $cname . $id;
                }

                $newHash = passwordCreate($cname, $password);

                if (is_array($newHash)) {

                    $query = $sql->prepare("UPDATE `userdata` SET `cname`=?,`security`=?,`salt`=?,`resellerid`=? WHERE `id`=? LIMIT 1");

                    if ($userAccounttype == 'a' and $accountType == 'r') {
                        $query->execute(array($cname, $newHash['hash'], $newHash['salt'], $id, $id));
                    } else if ($userAccounttype == 'r' and $accountType == 'r') {
                        $query->execute(array($cname, $newHash['hash'], $newHash['salt'], $admin_id, $id));
                    } else {
                        $query->execute(array($cname, $newHash['hash'], $newHash['salt'], $resellerLockupID, $id));
                    }

                } else {

                    $query = $sql->prepare("UPDATE `userdata` SET `cname`=?,`security`=?,`resellerid`=? WHERE `id`=? LIMIT 1");

                    if ($userAccounttype == 'a' and $accountType == 'r') {
                        $query->execute(array($cname, $newHash, $id, $id));
                    } else if ($userAccounttype == 'r' and $accountType == 'r') {
                        $query->execute(array($cname, $newHash, $admin_id, $id));
                    } else {
                        $query->execute(array($cname, $newHash, $resellerLockupID, $id));
                    }
                }

                $rowCount += $query->rowCount();

                if ($accountType == 'r') {

                    CopyAdminTable('servertypes', $id, $resellerLockupID, '');
                    CopyAdminTable('settings', $id, $resellerLockupID, 'LIMIT 1');

                    if ($reseller_id > 0 and $reseller_id != $admin_id) {
                        CopyAdminTable('usergroups', $id, $resellerLockupID, '', "AND `active`='Y' AND `name` IS NOT NULL AND `grouptype`='u'");
                    } else {
                        CopyAdminTable('usergroups', $id, $resellerLockupID, '', "AND `active`='Y' AND `name` IS NOT NULL AND `grouptype` IN ('u','r')");
                    }

                    $query = $sql->prepare("INSERT INTO `lendsettings` (`resellerid`) VALUES (?)");
                    $query->execute(array($id));
                    $query = $sql->prepare("INSERT INTO `eac` (`resellerid`) VALUES (?)");
                    $query->execute(array($id));
                    $query = $sql->prepare("INSERT INTO `resellerdata` (`useractive`,`maxuser`,`maxgserver`,`maxvoserver`,`maxdedis`,`maxvserver`,`maxuserram`,`maxusermhz`,`resellerid`,`resellersid`) VALUES (?,?,?,?,?,?,?,?,?,?)");
                    $query->execute(array($useractive, $maxuser, $maxgserver, $maxvoserver, $maxdedis, $maxvserver, $maxuserram, $maxusermhz, $id, ($reseller_id == 0) ? $id : $reseller_id));

                    $query = $sql->prepare("SELECT * FROM `translations` WHERE `type`='em' AND `resellerID`=?");
                    $query2 = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('em',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
                    $query->execute(array($resellerLockupID));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $query2->execute(array($row['transID'], $row['lang'], $row['text'], $id));
                    }
                }

                sendmail('emailuseradd', $id, $cname, $password);

                $loguseraction = '%add% %user% ' . $cname;

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                $jobPending = 'N';
                $rowCount = 0;

                if ($reseller_id == 0){
                    $query = $sql->prepare("SELECT `accounttype`,`active`,`cname`,`resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
                    $query->execute(array($id));
                } else {
                    $query = $sql->prepare("SELECT `accounttype`,`active`,`cname`,`resellerid` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($id, $resellerLockupID));
                }
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $accountType = $row['accounttype'];
                    $cname = $row['cname'];
                    $resellerUpdateId = $row['resellerid'];
                    $oldActive = $row['active'];
                }

                if (isset($oldActive)) {

                    if ($oldActive != $active) {

                        $jobPending = 'Y';

                        $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='us' AND (`status` IS NULL OR `status`='1') AND `userID`=? and `resellerID`=?");
                        $query->execute(array($id, $resellerLockupID));

                        $rowCount += $query->rowCount();

                        $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('U','us',?,?,?,?,NULL,NOW(),'md',?,?)");
                        $query->execute(array($admin_id, $id, $id, $cname, json_encode(array('newActive' => $active)), $resellerLockupID));

                        $rowCount += $query->rowCount();

                        updateJobs($id, $resellerLockupID);
                    }

                    $query = $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`salutation`=?,`birthday`=?,`country`=?,`fax`=?,`name`=?,`vname`=?,`mail`=?,`phone`=?,`handy`=?,`city`=?,`cityn`=?,`street`=?,`streetn`=?,`fdlpath`=?,`mail_backup`=?,`mail_gsupdate`=?,`mail_securitybreach`=?,`mail_serverdown`=?,`mail_ticket`=?,`mail_vserver`=?,`externalID`=?,`jobPending`=? WHERE `id`=? and `resellerid`=? LIMIT 1");
                    $query->execute(array($salutation, $birthday, $country, $fax, $name, $vname, $mail, $phone, $handy, $city, $cityn, $street, $streetn, $fdlpath, $mail_backup, $mail_gsupdate, $mail_securitybreach, $mail_serverdown, $mail_ticket, $mail_vserver, $externalID, $jobPending, $id, $resellerUpdateId));

                    $rowCount += $query->rowCount();
                }

                if ($accountType == 'r' and isset($resellerUpdateId)) {

                    if ($resellerUpdateId == 0) {
                        $resellerUpdateId = $id;
                    }

                    $query = $sql->prepare("SELECT `useractive` FROM `resellerdata` WHERE `resellerid`=? LIMIT 1");
                    $query->execute(array($id));

                    if ($query->fetchColumn() != $useractive) {

                        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `resellerid`=?");
                        $query2 = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='us' AND (`status` IS NULL OR `status`='1') AND `userID`=? and `resellerID`=?");
                        $query3 = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('U','us',?,?,?,?,NULL,NOW(),'md',?,?)");

                        $query->execute(array($id));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                            $query2->execute(array($id, $resellerUpdateId));

                            $query3->execute(array($admin_id, $row['id'], $row['id'], $row['cname'], json_encode(array('newActive' => $useractive)), $id));

                            updateJobs($row['id'], $resellerUpdateId);
                        }
                    }

                    $query = $sql->prepare("UPDATE `resellerdata` SET `useractive`=?,`maxuser`=?,`maxgserver`=?,`maxvoserver`=?,`maxdedis`=?,`maxvserver`=?,`maxuserram`=?,`maxusermhz`=? WHERE `resellerid`=? LIMIT 1");
                    $query->execute(array($useractive, $maxuser, $maxgserver, $maxvoserver, $maxdedis, $maxvserver, $maxuserram, $maxusermhz, $id));
                }

                $loguseraction = '%mod% %user% ' . $cname;
            }

            $query = $sql->prepare("INSERT INTO `easywi_statistics_current` (`userID`) VALUES (?) ON DUPLICATE KEY UPDATE `userID`=VALUES(`userID`)");
            $query->execute(array(($accountType == 'a') ? 0 : $id));

            $rowCount += $query->rowCount();

            customColumns('U', $id, 'save');

            $notIn = (is_array ($userGroups) and count($userGroups) > 0) ? 'AND `groupID` NOT IN ('. implode(',', $userGroups) .')' : '';

            $query = $sql->prepare("DELETE FROM `userdata_groups` WHERE `userID`=? AND `resellerID`=? " . $notIn);
            $query->execute(array($id, $resellerLockupID));

            $rowCount += $query->rowCount();

            $query = $sql->prepare("INSERT INTO `userdata_groups` (`userID`,`groupID`,`resellerID`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `groupID`=VALUES(`groupID`)");

            foreach ($userGroups as $gid) {

                if (isset($groups[$accountType][$gid])) {

                    if ($accountType == 'r' and $reseller_id == 0) {
                        $query->execute(array($id, $gid, $id));
                    } else {
                        $query->execute(array($id, $gid, $resellerLockupID));
                    }

                    $rowCount += $query->rowCount();
                }
            }

            // Check if a row was affected during insert or update
            if (isset($rowCount) and $rowCount > 0) {

                $insertlog->execute();
                $template_file = $spracheResponse->table_add;

                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }

            // An error occurred during validation unset the redirect information and display the form again
        } else {
            unset($header, $text);
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_user_add.tpl' : 'admin_user_md.tpl';
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $id and $id != $admin_id and ($pa['user'] or $pa['user_users'])) {

    unset($cname);

    $whereCase = ($pa['user'] and $reseller_id == 0) ? '' : 'AND `accounttype`!=\'a\'';

    $query = ($reseller_id == 0) ? $sql->prepare("SELECT CONCAT(`vname`,' ',`name`) AS `full_name`,`cname`,`accounttype`,`resellerid` FROM `userdata` WHERE `id`=? {$whereCase} AND (`resellerid`=? OR `id`=`resellerid`) LIMIT 1") : $sql->prepare("SELECT CONCAT(`vname`,' ',`name`) AS `full_name`,`cname`,`accounttype`,`resellerid` FROM `userdata` WHERE `id`=? AND `resellerid`=? {$whereCase} LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $cname = $row['cname'];
        $fullName = $row['full_name'];
        $accountType = $row['accounttype'];
        $resellerId = $row['resellerid'];
    }

    if (isset($cname)) {

        // Nothing submitted yet, display the delete form
        if (!$ui->st('action', 'post')) {

            $template_file = 'admin_user_dl.tpl';

            // User submitted remove the entry
        } else if ($ui->st('action', 'post') == 'dl') {

            // Deactivate all old jobs belonging to this user
            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='us' AND (`status` IS NULL OR `status`='1') AND `userID`=? and `resellerID`=?");
            $query->execute(array($id, $resellerId));

            // Add the removal job
            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('U','us',?,?,?,?,NULL,NOW(),'dl',?)");
            $query->execute(array($admin_id, $id, $id, $cname, $resellerId));

            updateJobs($id, $resellerLockupID);

            // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
            if ($query->rowCount() > 0) {

                $query = $sql->prepare("UPDATE `userdata` SET `jobPending`='Y' WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id, $resellerId));

                $template_file = $spracheResponse->table_del;
                $loguseraction = '%del% %user% ' . $cname;
                $insertlog->execute();

                // Nothing was deleted, display an error
            } else {
                $template_file = $spracheResponse->error_table;
            }
        }

    // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

// Password changes ID given with the GET request
} else if ($ui->st('d', 'get') == 'pw' and $id) {

    unset($cname);

    $whereCase = ($pa['user'] and $reseller_id == 0) ? '' : 'AND `accounttype`!=\'a\'';

    $query = ($reseller_id == 0) ? $sql->prepare("SELECT CONCAT(`vname`,' ',`name`) AS `full_name`,`cname`,`accounttype` FROM `userdata` WHERE `id`=? {$whereCase} AND (`resellerid`=? OR `id`=`resellerid`) LIMIT 1") : $sql->prepare("SELECT CONCAT(`vname`,' ',`name`) AS `full_name`,`cname`,`accounttype` FROM `userdata` WHERE `id`=? AND `resellerid`=? {$whereCase} LIMIT 1");
    $query->execute(array($id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $cname = $row['cname'];
        $fullName = $row['full_name'];
    }

    if (isset($cname)) {

        $errors = array();

        // Nothing submitted yet, display the delete form
        if (!$ui->st('action', 'post')) {

            $template_file = 'admin_user_pass.tpl';

            // User submitted remove the entry
        } else if ($ui->st('action', 'post') == 'pw') {

            if (!$password) {
                $errors[] = $sprache->error_pass;
            }

            if (!$passwordRepeat) {
                $errors[] = $sprache->error_pass;
            }

            if ($password != $passwordRepeat) {
                $errors[] = $sprache->error_passw_succ;
            }

            if (count($errors) > 0) {

                unset($header, $text);

                $template_file = 'admin_user_pass.tpl';

            } else {

                $password = $ui->password('password', 255, 'post');

                $newHash = passwordCreate($cname, $ui->password('password', 255, 'post'));

                if (is_array($newHash)) {
                    $query = ($reseller_id == 0) ? $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`security`=?,`salt`=? WHERE id=? AND (`resellerid`=? OR `id`=`resellerid`) LIMIT 1") : $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`security`=?,`salt`=? WHERE id=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($newHash['hash'], $newHash['salt'], $id, $resellerLockupID));

                } else {
                    $query = ($reseller_id == 0) ? $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`security`=? WHERE id=? AND (`resellerid`=? OR `id`=`resellerid`) LIMIT 1") : $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW(),`security`=? WHERE id=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($newHash, $id, $resellerLockupID));
                }

                // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
                if ($query->rowCount() > 0) {

                    $template_file = $spracheResponse->table_add;
                    $loguseraction = '%psw% %user% ' . $cname;
                    $insertlog->execute();

                    // Nothing was deleted, display an error
                } else {
                    $template_file = $spracheResponse->error_table;
                }
            }
        }

    // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

// List the available entries
} else {

    configureDateTables('-1, -2', '1, "asc"', 'ajax.php?w=datatable&d=user');

    $template_file = 'admin_user_list.tpl';
}