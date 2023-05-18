<?php

/**
 * File: voice_tsdns.php.
 * Author: Ulrich Block
 * Date: 22.09.12
 * Time: 21:53
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['voicemasterserver'])) {
	header('Location: admin.php');
	die;
}
include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/functions_ts3.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/third_party/password_compat/password.php');

$sprache = getlanguagefile('voice', $user_language, $reseller_id);
$usprache = getlanguagefile('user', $user_language, $reseller_id);
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
if ($reseller_id != 0 and $admin_id != $reseller_id) {
    $reseller_id = $admin_id;
}

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {

	unset($header, $text);

    $errors = array($spracheResponse->token);

    $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_voice_tsdns_add.tpl' : 'admin_voice_tsdns_md.tpl';

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();

    // At this point all variables are defined that can come from the user
    $id = $ui->id('id', 10, 'get');
    $externalID = $ui->externalID('externalID', 'post');
    $active = $ui->active('active', 'post');
    $description = $ui->escaped('description', 'post');
    $autorestart = $ui->active('autorestart', 'post');
    $defaultdns = strtolower($ui->domain('defaultdns', 'post'));
    $ssh2ip = $ui->ip('ip', 'post');
    $ssh2port = $ui->port('port', 'post');
    $ssh2user = $ui->username('user', 255, 'post');
    $ssh2password = $ui->password('pass', 255, 'post');
    $serverdir = $ui->folder('serverdir', 'post');
    $keyname = $ui->startparameter('keyname', 'post');
    $bit = $ui->id('bit',2, 'post');
    $connectIpOnly = ($ui->active('connectIpOnly', 'post')) ? $ui->active('connectIpOnly', 'post') : 'Y';
    $externalIp = $ui->ip('externalIp', 'post');
    $maxDns = ($ui->id('maxDns', 10, 'post')) ? $ui->id('maxDns', 10, 'post') : 500;

    // Default variables. Mostly needed for the add operation
    $publickey = ($ui->w('publickey', 1, 'post')) ? $ui->w('publickey', 1, 'post') : 'N';

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {
            $template_file = 'admin_voice_tsdns_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':aeskey' => $aeskey,':id' => $id,':reseller_id' => $reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $active = $row['active'];
                $externalID = $row['externalID'];
                $description = $row['description'];
                $autorestart = $row['autorestart'];
                $defaultdns = $row['defaultdns'];
                $publickey = $row['publickey'];
                $ssh2ip = $row['ssh2ip'];
                $ssh2port = $row['decryptedssh2port'];
                $ssh2user = $row['decryptedssh2user'];
                $ssh2password = $row['decryptedssh2password'];
                $serverdir = $row['serverdir'];
                $keyname = $row['keyname'];
                $bit = $row['bitversion'];
                $connectIpOnly = $row['connect_ip_only'];
                $externalIp = $row['external_ip'];
                $maxDns = $row['max_dns'];
            }

            // Check if database entry exists and if not display 404 page
            $template_file =  ($query->rowCount() > 0) ? 'admin_voice_tsdns_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$active) {
            $errors['active'] = $sprache->active;
        }
        if (!$autorestart) {
            $errors['autorestart'] = $sprache->autorestart;
        }
        if (!$publickey) {
            $errors['publickey'] = $sprache->keyuse;
        }
        if (!$ssh2ip) {
            $errors['ip'] = $sprache->ssh_ip;
        }
        if (!$ssh2port) {
            $errors['port'] = $sprache->ssh_port;
        }
        if (!$ssh2user) {
            $errors['user'] = $sprache->ssh_user;
        }
        if (!$bit) {
            $errors['active'] = $sprache->active;
        }

        if ($publickey != 'N' and !is_file(EASYWIDIR . '/keys/' . $keyname)) {
            $errors['keyname'] = $sprache->keyname;
        }

        $ssh2Check = (count($errors) == 0 and $active == 'Y') ? ssh_check($ssh2ip, $ssh2port, $ssh2user, $publickey, $keyname, $ssh2password) : true;

        if ($ssh2Check !== true) {

            if ($ssh2Check == 'ipport') {
                $errors['ip'] = $sprache->ssh_ip;
                $errors['port'] = $sprache->ssh_port;

            } else {
                $errors['user'] = $sprache->ssh_user;
                $errors['publickey'] = $sprache->keyuse;

                if ($publickey == 'N') {
                    $errors['pass'] = $sprache->ssh_pass;

                } else if (!$ui->active('publickey', 'post') == 'B') {
                    $errors['pass'] = $sprache->ssh_pass;
                    $errors['keyname'] = $sprache->keyname;

                } else {
                    $errors['keyname'] = $sprache->keyname;
                }
            }
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `voice_tsdns` (`externalID`,`max_dns`,`active`,`bitversion`,`defaultdns`,`publickey`,`ssh2ip`,`ssh2port`,`ssh2user`,`ssh2password`,`serverdir`,`keyname`,`autorestart`,`description`,`connect_ip_only`,`external_ip`,`resellerid`) VALUES (:externalID,:maxDns,:active,:bit,:defaultdns,:publickey,:ssh2ip,AES_ENCRYPT(:ssh2port,:aeskey),AES_ENCRYPT(:ssh2user,:aeskey),AES_ENCRYPT(:ssh2password,:aeskey),:serverdir,:keyname,:autorestart,:description,:connect_ip_only,:external_ip,:reseller_id)");
                $query->execute(array(':externalID' => $externalID,':maxDns' => $maxDns,':aeskey' => $aeskey,':active' => $active,':bit' => $bit,':defaultdns' => $defaultdns,':publickey' => $publickey,':ssh2ip' => $ssh2ip,':ssh2port' => $ssh2port,':ssh2user' => $ssh2user,':ssh2password' => $ssh2password,':serverdir' => $serverdir,':keyname' => $keyname,':autorestart' => $autorestart,':description' => $description,':connect_ip_only' => $connectIpOnly,':external_ip' => $externalIp,':reseller_id' => $reseller_id));

                $rowCount = $query->rowCount();

                $id = $sql->lastInsertId();

                $loguseraction = '%add% %voserver% %tsdns% ' . $ssh2ip;

            } else if ($ui->st('action', 'post') == 'md') {

                $query = $sql->prepare("UPDATE `voice_tsdns` SET `externalID`=:externalID,`max_dns`=:maxDns,`active`=:active,`bitversion`=:bit,`defaultdns`=:defaultdns,`publickey`=:publickey,`ssh2ip`=:ssh2ip,`ssh2port`=AES_ENCRYPT(:ssh2port,:aeskey),`ssh2user`=AES_ENCRYPT(:ssh2user,:aeskey),`ssh2password`=AES_ENCRYPT(:ssh2password,:aeskey),`serverdir`=:serverdir,`keyname`=:keyname,`autorestart`=:autorestart,`description`=:description,`connect_ip_only`=:connect_ip_only,`external_ip`=:external_ip WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':externalID' => $externalID,':maxDns' => $maxDns,':aeskey' => $aeskey,':active' => $active,':bit' => $bit,':defaultdns' => $defaultdns,':publickey' => $publickey,':ssh2ip' => $ssh2ip,':ssh2port' => $ssh2port,':ssh2user' => $ssh2user,':ssh2password' => $ssh2password,':serverdir' => $serverdir,':keyname' => $keyname,':autorestart' => $autorestart,':description' => $description,':connect_ip_only' => $connectIpOnly,':external_ip' => $externalIp,':id' => $id,':reseller_id' => $reseller_id));

                $rowCount = $query->rowCount();
                $loguseraction = '%mod% %voserver% %tsdns% ' . $ssh2ip;
            }

            // Check if a row was affected during insert or update
            if (isset($rowCount) and $rowCount > 0) {
                $insertlog->execute();

                if ($ui->st('action', 'post') == 'md') {
                    $template_file = $spracheResponse->table_add;

                } else {

                    unset($header, $text);

                    $newArray = array();
                    $table = array();
                    $newuser = ($rSA['prefix1'] == 'Y') ? 1 : 2;
                    $maxPost = @ini_get('suhosin.post.max_vars');
                    $maxRequests = @ini_get('suhosin.request.max_vars');
                    $i = 0;
                    $max = ($maxRequests and $maxPost and $maxPost<$maxRequests) ? $maxPost : $maxRequests;
                    $max = (empty($max)) ? count($dnsarray) : ($max-10)/7;

                    $dnsarray = tsdns('li', $ssh2ip, $ssh2port, $ssh2user, $publickey, $keyname, $ssh2password, 'N', $serverdir, $bit, array(''), array(''), array(''), $reseller_id);

                    if (is_array($dnsarray)) {

                        $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
                        $query->execute(array($reseller_id));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
                        }

                        $query = $sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dns`=? AND `resellerID`=? LIMIT 1");
                        $query2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `dns`=? AND `resellerid`=? LIMIT 1");
                        foreach ($dnsarray as $k => $v) {
                            $query->execute(array($v, $reseller_id));
                            $query2->execute(array($v, $reseller_id));
                            $ex = explode(':', $k);

                            if ($query->rowCount() == 0 and $query2->rowCount() == 0 and $i <= $max and isset($ex[1]) and port($ex[1])) {
                                $newArray[$k] = $v;
                                $i++;
                            }
                        }
                    }
                    $template_file = 'admin_voice_tsdns_import.tpl';
                }


                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }

            // An error occurred during validation unset the redirect information and display the form again
        } else {
            unset($header, $text);
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_voice_tsdns_add.tpl' : 'admin_voice_tsdns_md.tpl';
        }
    }
} else if ($ui->st('d', 'get') == 'ip' and $ui->id('id',19, 'get')) {

    $id = $ui->id('id',19, 'get');

    if (!$ui->smallletters('action', 2, 'post')) {
        $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
        $query->execute(array(':aeskey' => $aeskey,':id' => $id,':reseller_id' => $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publickey = $row['publickey'];
            $ssh2ip = $row['ssh2ip'];
            $ssh2port = $row['decryptedssh2port'];
            $ssh2user = $row['decryptedssh2user'];
            $ssh2password = $row['decryptedssh2password'];
            $serverdir = $row['serverdir'];
            $keyname = $row['keyname'];
            $bit = $row['bitversion'];
        }

        if ($query->rowCount() > 0) {

            $dnsarray = tsdns('li', $ssh2ip, $ssh2port, $ssh2user, $publickey, $keyname, $ssh2password, 'N', $serverdir, $bit, array(''), array(''), array(''), $reseller_id);

            if (is_array($dnsarray)) {

                $newArray = array();
                $table = array();
                $newuser = ($rSA['prefix1'] == 'Y') ? 1 : 2;
                $maxPost = @ini_get('suhosin.post.max_vars');
                $maxRequests = @ini_get('suhosin.request.max_vars');
                $i = 0;
                $max = ($maxRequests and $maxPost and $maxPost<$maxRequests) ? $maxPost : $maxRequests;
                $max = (empty($max)) ? count($dnsarray) : ($max-10)/7;

                $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
                $query->execute(array($reseller_id));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
                }

                $query = $sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dns`=? AND `resellerID`=? LIMIT 1");
                $query2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `dns`=? AND `resellerid`=? LIMIT 1");
                foreach ($dnsarray as $k => $v) {
                    $query->execute(array($v, $reseller_id));
                    $query2->execute(array($v, $reseller_id));
                    $ex = explode(':', $k);

                    if ($query->rowCount() == 0 and $query2->rowCount() == 0 and $i <= $max and isset($ex[1]) and port($ex[1])) {
                        $newArray[$k] = $v;
                        $i++;
                    }
                }
            }

            $template_file = 'admin_voice_tsdns_import.tpl';

        } else {
            $template_file = 'admin_404.tpl';
        }

    } else if ($ui->smallletters('action', 2, 'post') == 'ip') {

        $added = '';
        $prefix = $rSA['prefix2'];
        $dnsList = (array) $ui->domain('dns', 'post');

        foreach ($dnsList as $dns) {

            $lookUp = str_replace('.', '_', $dns);
            $ex = explode(':', $ui->ipport("${lookUp}-address", 'post'));

            if ($ui->active("${lookUp}-import", 'post') == 'Y'  and isset($ex[1]) and port($ex[1])) {

                $ip = $ex[0];
                $port = $ex[1];
                $customer = $ui->id("${lookUp}-customer", 19, 'post');

                if ($customer == 0 or $customer == false or $customer == null) {

                    $usernew = true;

                    if ($ui->username("${lookUp}-username",50, 'post') and $ui->ismail("${lookUp}-email", 'post')) {
                        $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`=? AND `resellerid`=? LIMIT 1");
                        $query->execute(array($ui->username("${lookUp}-username",50, 'post'), $ui->ismail("${lookUp}-email", 'post'), $reseller_id));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            $usernew = false;
                            $customer = $row['id'];
                            $cnamenew = $ui->username("${lookUp}-username",50, 'post');
                        }
                        if ($usernew == true) {

                            $newHash = passwordCreate($ui->username("${lookUp}-username",50, 'post'), passwordgenerate(10));

                            if (is_array($newHash)) {
                                $query = $sql->prepare("INSERT INTO `userdata` (`cname`,`security`,`salt`,`mail`,`accounttype`,`resellerid`) VALUES (?,?,?,?,'u',?)");
                                $query->execute(array($ui->username("${lookUp}-username",50, 'post'), $newHash['hash'], $newHash['salt'], $ui->ismail("${lookUp}-email", 'post'), $reseller_id));
                            } else {
                                $query = $sql->prepare("INSERT INTO `userdata` (`cname`,`security`,`mail`,`accounttype`,`resellerid`) VALUES (?,?,?,'u',?)");
                                $query->execute(array($ui->username("${lookUp}-username",50, 'post'), $newHash, $ui->ismail("${lookUp}-email", 'post'), $reseller_id));
                            }

                            $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
                            $query->execute(array($ui->username("${lookUp}-username",50, 'post'), $ui->ismail("${lookUp}-email", 'post'), $reseller_id));
                            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                $customer = $row['id'];
                                $cnamenew = $ui->username("${lookUp}-username",50, 'post');
                                sendmail('emailuseradd', $customer, $cnamenew, $initialpassword);
                            }
                        }

                    } else {
                        $cldbid = rand(1,100) . '.' . rand(1,100);
                        $cnamenew = $prefix . $cldbid;
                        $query = $sql->prepare("INSERT INTO `userdata` (`cname`,`security`,`mail`,`accounttype`,`resellerid`) VALUES (?,?,?,'u',?)");
                        $query->execute(array($cnamenew,passwordgenerate(10),'ts3@import.mail', $reseller_id));
                        $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`='ts3@import.mail' ORDER BY `id` DESC LIMIT 1");
                        $query->execute(array($cnamenew));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            $customer = $row['id'];
                            $cnamenew = $prefix . $customer;
                        }
                        $query = $sql->prepare("UPDATE `userdata` SET `cname`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                        $query->execute(array($cnamenew, $customer, $reseller_id));
                    }

                    if ($usernew == true) {
                        $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `active`='Y' AND `defaultgroup`='Y' AND `grouptype`='u' AND `resellerid`=? LIMIT 1");
                        $query->execute(array($reseller_id));
                        $groupID = $query->fetchColumn();
                        $query = $sql->prepare("UPDATE `userdata` SET `usergroup`=? WHERE id=? AND `resellerid`=? LIMIT 1");
                        $query->execute(array($groupID, $customer, $reseller_id));
                    }

                    $added .= 'User ' . $cnamenew . ' ';

                } else {
                    $query = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($customer, $reseller_id));
                    $cnamenew = $query->fetchColumn();
                }

                $added .= 'Server ' . $ip . ':' . $port . ':' . $dns.'<br />';
                $query = $sql->prepare("INSERT INTO `voice_dns` (`active`,`dns`,`ip`,`port`,`tsdnsID`,`userID`,`externalID`,`resellerID`) VALUES (?,?,?,?,?,?,?,?)");
                $query->execute(array('Y', $dns, $ip, $port, $id, $customer, '', $reseller_id));
            }
        }
        $template_file = $added;
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {

    // Define the ID variable which will be used at the form and SQLs
    $id = $ui->id('id', 10, 'get');

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        $query = $sql->prepare("SELECT `ssh2ip`,`description` FROM `voice_tsdns` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $ip = $row['ssh2ip'];
            $description = $row['description'];
        }

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_voice_tsdns_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl') {

        $query = $sql->prepare("SELECT `ssh2ip` FROM `voice_tsdns` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        $ip = $query->fetchColumn();

        if ($query->rowCount() > 0) {
            $query = $sql->prepare("UPDATE `voice_masterserver` SET `tsdnsServerID`=NULL WHERE `tsdnsServerID`=? AND `resellerid`=?");
            $query->execute(array($id, $reseller_id));

            $query = $sql->prepare("DELETE FROM `voice_tsdns` WHERE `id`=? AND `resellerid`=?");
            $query->execute(array($id, $reseller_id));
        }

        // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
        if ($query->rowCount() > 0) {

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% %voserver% %tsdns% ' . $ip;
            $insertlog->execute();

            // Nothing was deleted, display an error
        } else {
            $template_file = $spracheResponse->error_table;
        }

        // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

// List the available entries
} else {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=tsdnsmasterserver');

    $template_file = 'admin_voice_tsdns_list.tpl';
}
