<?php

/**
 * File: voice_master.php.
 * Author: Ulrich Block
 * Date: 23.09.12
 * Time: 11:16
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
include(EASYWIDIR . '/stuff/methods/class_ts3.php');
include(EASYWIDIR . '/stuff/methods/functions_ts3.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/third_party/password_compat/password.php');

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['voicemasterserver'])) {
    header('Location: admin.php');
    die;
}
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


if ($ui->w('action',4, 'post') and !token(true)) {

	unset($header, $text);

    $errors = array($spracheResponse->token);

    $template_file = ($ui->st('d', 'get') == 'md') ? 'admin_voicemasterserver_md.tpl' : 'admin_voicemasterserver_add.tpl';

} else if ($ui->st('d', 'get') == 'ad' or (($ui->st('d', 'get') == 'ri' or $ui->st('d', 'get') == 'md') and $ui->id('id', 10, 'get'))) {

    $oldactive = 'Y';
    $resellerToBeWritten = null;
    $externalDNS = array();
    $dnsarray = array();
    $errors = array();

    $id = $ui->id('id', 10, 'get');
    $autorestart = $ui->active('autorestart', 'post');
    $externalDefaultDNS = $ui->active('externalDefaultDNS', 'post');
    $ip = $ui->ip('ip', 'post');
    $connectIpOnly = $ui->active('connectIpOnly', 'post');
    $user = $ui->username('user', 50, 'post');
    $externalID = $ui->escaped('externalID', 'post');
    $ips = $ui->ips('ips', 'post');
    $addtype = 2;
    $addedby = 2;
    $keyname = $ui->startparameter('keyname', 'post');
    $pass = $ui->startparameter('pass', 'post');
    $serverdir = $ui->folder('serverdir', 'post');
    $rootid = ($ui->id('rootid', 2, 'post')) ? $ui->id('rootid', 2, 'post') : null;
    $usedns = $ui->active('usedns', 'post');
    $querypassword = $ui->password('querypassword', 50, 'post');
    $type = 'ts3';
    $defaultdns = strtolower($ui->domain('defaultdns', 'post'));
    $defaultwelcome = $ui->description('defaultwelcome', 'post');
    $defaulthostbanner_url = $ui->url('defaulthostbanner_url', 'post');
    $defaulthostbanner_gfx_url = $ui->url('defaulthostbanner_gfx_url', 'post');
    $defaulthostbutton_tooltip = $ui->description('defaulthostbutton_tooltip', 'post');
    $defaulthostbutton_url = $ui->url('defaulthostbutton_url', 'post');
    $defaulthostbutton_gfx_url = $ui->url('defaulthostbutton_gfx_url', 'post');
    $defaultFlexSlotsFree = $ui->id('defaultFlexSlotsFree', 11, 'post');
    $defaultFlexSlotsPercent = $ui->id('defaultFlexSlotsPercent', 3, 'post');
    $tsdnsServerID = $ui->id('tsdnsServerID', 19, 'post');
    $description = $ui->description('description', 'post');

    $queryport = ($ui->port('queryport', 'post')) ? $ui->port('queryport', 'post') : 10011;
    $filetransferport = ($ui->port('filetransferport', 'post')) ? $ui->port('filetransferport', 'post') : 30033;
    $defaultname = ($ui->startparameter('defaultname', 'post')) ? $ui->startparameter('defaultname', 'post') : $rSA['brandname'];
    $active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
    $publickey = ($ui->w('publickey', 1, 'post')) ? $ui->w('publickey', 1, 'post') : 'N';
    $maxserver = ($ui->id('maxserver', 30, 'post')) ? $ui->id('maxserver', 30, 'post') : 10;
    $maxslots = ($ui->id('maxslots', 30, 'post')) ? $ui->id('maxslots', 30, 'post') : 512;
    $port = ($ui->port('port', 'post')) ? $ui->port('port', 'post') : 22;
    $bit = ($ui->id('bit', 2, 'post')) ? $ui->id('bit', 2, 'post') : 64;
    $managedServer = ($ui->active('managedServer', 'post')) ? $ui->active('managedServer', 'post') : 'N';
    $managedForID = $ui->id('managedForID', 10, 'post');

    // https://github.com/easy-wi/developer/issues/36 managedServer,managedForID added
    if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

        $resellerIDs = array();

        $or = ($reseller_id == 0) ? 'OR `resellerid`=`id`' : '';
        $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE (`resellerid`=? $or) AND `accounttype`='r' ORDER BY `id` DESC");
        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $resellerIDs[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
        }
    }

    if ($ui->w('action', 3, 'post') and ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') and $ui->active('managedServer', 'post') == 'Y') {

        if ($reseller_id == 0) {
            $query = $sql->prepare("SELECT 1 FROM `userdata` WHERE `resellerid`=? AND `accounttype`='r' LIMIT 1");
            $query->execute(array($ui->id('managedForID', 10, 'post')));

        } else {
            $query = $sql->prepare("SELECT 1 FROM `userdata` WHERE `id`=? AND `resellerid`=? AND `accounttype`='r' LIMIT 1");
            $query->execute(array($ui->id('managedForID', 10, 'post'), $reseller_id));
        }

        $resellerToBeWritten = ($query->rowCount() > 0) ? $ui->id('managedForID', 10, 'post') : null;

    }

    $query = $sql->prepare("SELECT `id`,`ssh2ip`,`description` FROM `voice_tsdns` WHERE `active`='Y' AND `resellerid`=?");
    $query->execute(array($reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $externalDNS[$row['id']] = ($row['description'] != '' and $row['description'] != null) ? $row['ssh2ip'] . ': ' . $row['description'] : $row['ssh2ip'];
    }

    if ($ui->st('d', 'get') == 'ad') {

        $roots = array();

        $query = $sql->prepare("SELECT `id`,`ip` FROM `rserverdata` WHERE `active`='Y' AND `resellerid`=?");
        $query2 = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_masterserver` WHERE `rootid`=? AND `resellerid`=?");
        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $query2->execute(array($row['id'], $reseller_id));
            $colcount = $query2->fetchColumn();

            if ($colcount == 0) {
                $roots[] = '<option value="' . $row['id'] . '">' . $row['ip'] . '</option>';
            }
        }
    }
    if (!$ui->w('action', 3, 'post') and $ui->st('d', 'get') != 'ri') {

        if ($ui->st('d', 'get') == 'ad') {

            $template_file = 'admin_voicemasterserver_add.tpl';

        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':aeskey' => $aeskey,':id' => $id,':reseller_id' => $reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $active = $row['active'];
                $defaultname = $row['defaultname'];
                $description = $row['description'];
                $addedby = $row['addedby'];
                $autorestart = $row['autorestart'];
                $externalID = $row['externalID'];
                $tsdnsServerID = $row['tsdnsServerID'];
                $externalDefaultDNS = $row['externalDefaultDNS'];
                $defaultFlexSlotsFree = $row['defaultFlexSlotsFree'];
                $defaultFlexSlotsPercent = $row['defaultFlexSlotsPercent'];
                $publickey = $row['publickey'];
                $ip = $row['ssh2ip'];
                $connectIpOnly = $row['connect_ip_only'];
                $ips = $row['ips'];
                $port = $row['decryptedssh2port'];
                $user = $row['decryptedssh2user'];
                $pass = $row['decryptedssh2password'];
                $serverdir = $row['serverdir'];
                $keyname = $row['keyname'];
                $bit = $row['bitversion'];

                // https://github.com/easy-wi/developer/issues/36 managedServer,managedForID added
                $managedServer = $row['managedServer'];
                $managedForID = $row['managedForID'];

                if ($row['type'] == 'ts3') {
                    $type = 'ts3';
                    $usedns = $row['usedns'];
                    $defaultdns = $row['defaultdns'];
                    $defaultwelcome = $row['defaultwelcome'];
                    $defaulthostbanner_url = $row['defaulthostbanner_url'];
                    $defaulthostbanner_gfx_url = $row['defaulthostbanner_gfx_url'];
                    $defaulthostbutton_tooltip = $row['defaulthostbutton_tooltip'];
                    $defaulthostbutton_url = $row['defaulthostbutton_url'];
                    $defaulthostbutton_gfx_url = $row['defaulthostbutton_gfx_url'];
                    $queryport = $row['queryport'];
                    $querypassword = $row['decryptedquerypassword'];
                    $filetransferport = $row['filetransferport'];
                    $maxserver = $row['maxserver'];
                    $maxslots = $row['maxslots'];
                }

                if ($addedby == 1) {
                    $query2 = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($row['rootid'], $reseller_id));
                    $root = $query2->fetchColumn();
                }
            }

            $template_file =  ($query->rowCount() > 0) ? 'admin_voicemasterserver_md.tpl' : 'admin_404.tpl';

        } else {
            $template_file = 'admin_404.tpl';
        }

    } else if ($ui->w('action', 3, 'post') == 'ad' or ((in_array($ui->w('action', 3, 'post'), array('md', 'ad2')) or $ui->st('d', 'get') == 'ri') and $id)) {

        if (!$ui->active('active', 'post')) {
            $errors['active'] = $sprache->active;
        }

        if (!$ui->active('autorestart', 'post')) {
            $errors['autorestart'] = $sprache->autorestart;
        }

        if (!$ui->id('maxserver', 30, 'post')) {
            $errors['maxserver'] = $sprache->maxserver;
        }

        if (!$ui->id('maxslots', 30, 'post')) {
            $errors['maxslots'] = $sprache->maxslots;
        }
        if (!$ui->w('type', 3, 'post') == 'ts3') {

            if (!$ui->active('externalDefaultDNS', 'post')) {
                $errors['externalDefaultDNS'] = $sprache->externalDefaultDNS;
            }

            if (!$ui->active('usedns', 'post')) {
                $errors['usedns'] = $sprache->usedns;
            }

            if (!$ui->password('querypassword', 50, 'post')) {
                $errors['querypassword'] = $sprache->querypassword;
            }

            if (!$ui->port('queryport', 'post')) {
                $errors['queryport'] = $sprache->queryport;
            }

            if (!$ui->port('filetransferport', 'post')) {
                $errors['filetransferport'] = $sprache->filetransferport;
            }
        }

        if (($ui->w('action', 3, 'post') == 'ad' and $ui->id('addtype', 1, 'post') == 2) or $ui->w('action', 3, 'post') == 'md') {

            if (!$ui->w('publickey', 1, 'post')) {
                $errors['publickey'] = $sprache->keyuse;
            }

            if (!$ui->ip('ip', 'post')) {
                $errors['ip'] = $sprache->ssh_ip;
            }

            if (!$ui->port('port', 'post')) {
                $errors['port'] = $sprache->ssh_port;
            }

            if (!$ui->username('user', 50, 'post')) {
                $errors['user'] = $sprache->ssh_user;
            }

            if (!$ui->id('bit', 2, 'post')) {
                $errors['bit'] = $sprache->os_bit;
            }

            if (count($errors) == 0 and $ui->st('d', 'get') != 'ri') {

                if ($ui->w('action', 3, 'post') == 'md') {
                    $query = $sql->prepare("SELECT `active` FROM `voice_masterserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($id, $reseller_id));
                    $oldactive = $query->fetchColumn();
                }

                if ($oldactive != 'N' and $active == 'Y') {
                    $connection = new TS3($ip, $queryport, 'serveradmin', $querypassword);

                    if ($connection->socketConnected !== true) {
                        $errors['ip'] = $sprache->ssh_ip;
                        $errors['queryport'] = $sprache->queryport;

                    } else if (strpos($connection->errorcode,'error id=0') === false) {
                        $errors['querypassword'] = $sprache->querypassword;

                    }
                }
            }

            if ($publickey != 'N' and !is_file(EASYWIDIR . '/keys/' . $keyname)) {
                $errors['keyname'] = $sprache->keyname;
            }

            $ssh2Check = (count($errors) == 0 and $ui->st('d', 'get') != 'ri' and $active == 'Y') ? ssh_check($ui->ip('ip', 'post'), $ui->port('port', 'post'), $ui->username('user', 20, 'post'), $ui->active('publickey', 'post'), $ui->startparameter('keyname', 'post'), $ui->password('pass', 255, 'post')) : true;

            if ($ssh2Check !== true) {

                if ($ssh2Check == 'ipport') {

                    $errors['ip'] = $sprache->ssh_ip;
                    $errors['port'] = $sprache->ssh_port;

                } else {

                    $errors['user'] = $sprache->ssh_user;

                    if (!$ui->active('publickey', 'post') == 'N') {

                        $errors['pass'] = $sprache->ssh_pass;

                    } else if (!$ui->active('publickey', 'post') == 'B') {

                        $errors['pass'] = $sprache->ssh_pass;
                        $errors['publickey'] = $sprache->keyuse;
                        $errors['keyname'] = $sprache->keyname;

                    } else {
                        $errors['publickey'] = $sprache->keyuse;
                        $errors['keyname'] = $sprache->keyname;
                    }
                }
            }

        } else {
            $addtype = 1;
        }
        
        if ($ui->w('action', 3, 'post') != 'ad2' and ((count($errors) == 0 or ($ui->st('d', 'get') == 'ri' and $id)))) {

            if ($ui->w('action', 3, 'post') == 'ad' or ($ui->st('d', 'get') == 'ri' and !$ui->w('action', 3, 'post') and $id)) {

                if ($ui->st('d', 'get') == 'ri') {

                    $masterid = $id;

                    $query = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                    $query->execute(array(':aeskey' => $aeskey,':id' => $masterid,':reseller_id' => $reseller_id));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                        $active = $row['active'];
                        $defaultname = $row['defaultname'];
                        $addtype = $row['addedby'];
                        $rootid = $row['rootid'];
                        $tsdnsServerID = $row['tsdnsServerID'];
                        $externalDefaultDNS = $row['externalDefaultDNS'];
                        $defaultFlexSlotsFree = $row['defaultFlexSlotsFree'];
                        $defaultFlexSlotsPercent = $row['defaultFlexSlotsPercent'];
                        $description = $row['description'];

                        if ($row['type'] == 'ts3') {
                            $type = $sprache->ts3;
                            $usedns = $row['usedns'];
                            $defaultdns = $row['defaultdns'];
                            $defaultwelcome = $row['defaultwelcome'];
                            $defaulthostbanner_url = $row['defaulthostbanner_url'];
                            $defaulthostbanner_gfx_url = $row['defaulthostbanner_gfx_url'];
                            $defaulthostbutton_tooltip = $row['defaulthostbutton_tooltip'];
                            $defaulthostbutton_url = $row['defaulthostbutton_url'];
                            $defaulthostbutton_gfx_url = $row['defaulthostbutton_gfx_url'];
                            $queryport = $row['queryport'];
                            $querypassword = $row['decryptedquerypassword'];
                            $filetransferport = $row['filetransferport'];
                            $maxserver = $row['maxserver'];
                            $maxslots = $row['maxslots'];
                        }

                        if ($addtype == 2) {
                            $publickey = $row['publickey'];
                            $ip = $row['ssh2ip'];
                            $ips = $row['ips'];
                            $port = $row['decryptedssh2port'];
                            $user = $row['decryptedssh2user'];
                            $pass = $row['decryptedssh2password'];
                            $serverdir = $row['serverdir'];
                            $keyname = $row['keyname'];
                            $bit = $row['bitversion'];

                        } else if ($addtype == 1) {
                            $query2 = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                            $query2->execute(array($rootid, $reseller_id));
                            $ip = $query2->fetchColumn();
                        }
                    }
                }

                if ($addtype == 2) {

                    $table = array();

                    $query = $sql->prepare("SELECT `id`,`cname`,`name`,`vname` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
                    $query->execute(array($reseller_id));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $table[$row['id']]=($row['vname'] != '' or $row['name'] != '') ? $row['cname'] . ' (' . $row['vname'] . ' ' . $row['name'] . ')': $row['cname'];
                    }

                    if ($usedns == 'Y') {

                        if (isset($tsdnsServerID) and isid($tsdnsServerID, 19)) {
                            $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                            $query->execute(array(':aeskey' => $aeskey,':id' => $tsdnsServerID,':reseller_id' => $reseller_id));
                            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                $publickey = $row['publickey'];
                                $ip = $row['ssh2ip'];
                                $port = $row['decryptedssh2port'];
                                $user = $row['decryptedssh2user'];
                                $pass = $row['decryptedssh2password'];
                                $serverdir = $row['serverdir'];
                                $keyname = $row['keyname'];
                                $bit = $row['bitversion'];

                                if ($externalDefaultDNS == 'Y') {
                                    $defaultdns = $row['defaultdns'];
                                }
                            }
                        }

                        $dnsarray = tsdns('li', $ip, $port, $user, $publickey, $keyname, $pass,'N', $serverdir, $bit, array(''), array(''), array(''), $reseller_id);

                    }
                    if ($ui->st('d', 'get') == 'ri') {
                        $connection = new TS3($ip, $queryport, 'serveradmin', $querypassword);
                    }

                    if ($connection->socketConnected === true and strpos($connection->errorcode,'error id=0') !== false) {

                        $i = 1;
                        $servers = $connection->ImportData($dnsarray);

                        $query = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `localserverid`=? AND `ip`=? AND `resellerid`=? LIMIT 1");
                        foreach ($servers as $virtualserver_id => $values) {

                            $query->execute(array($virtualserver_id, $values['virtualserver_ip'], $reseller_id));
                            $colcount = $query->rowCount();

                            if ($colcount == 1 or $i > 25) {
                                unset($servers[$virtualserver_id]);
                            } else {
                                $i++;
                            }
                        }

                        $connection->CloseConnection();
                    } else {
                        $servers = array();
                    }
                }

                $query = $sql->prepare("SELECT `id` FROM `voice_masterserver` WHERE `ssh2ip`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($ip, $reseller_id));

                if (!isid($query->fetchColumn(), 10)) {


                    if ($ui->st('d', 'get') != 'ri') {
                        // https://github.com/easy-wi/developer/issues/36 managedServer,managedForID added
                        $query = $sql->prepare("INSERT INTO `voice_masterserver` (`active`,`connect_ip_only`,`type`,`description`,`defaultname`,`bitversion`,`queryport`,`querypassword`,`filetransferport`,`maxserver`,`maxslots`,`rootid`,`addedby`,`usedns`,`defaultdns`,`defaultwelcome`,`defaulthostbanner_url`,`defaulthostbanner_gfx_url`,`defaulthostbutton_tooltip`,`defaulthostbutton_url`,`defaulthostbutton_gfx_url`,`defaultFlexSlotsFree`,`defaultFlexSlotsPercent`,`publickey`,`ssh2ip`,`ssh2port`,`ssh2user`,`ssh2password`,`ips`,`serverdir`,`keyname`,`autorestart`,`externalID`,`tsdnsServerID`,`externalDefaultDNS`,`managedServer`,`managedForID`,`resellerid`) VALUES (:active,:connect_ip_only,:type,:description,:defaultname,:bit,:queryport,AES_ENCRYPT(:querypassword,:aeskey),:filetransferport,:maxserver,:maxslots,:rootid,:addedby,:usedns,:defaultdns,:defaultwelcome,:defaulthostbanner_url,:defaulthostbanner_gfx_url,:defaulthostbutton_tooltip,:defaulthostbutton_url,:defaulthostbutton_gfx_url,:defaultFlexSlotsFree,:defaultFlexSlotsPercent,:publickey,:ssh2ip,AES_ENCRYPT(:ssh2port,:aeskey),AES_ENCRYPT(:ssh2user,:aeskey),AES_ENCRYPT(:ssh2password,:aeskey),:ips,:serverdir,:keyname,:autorestart,:externalID,:tsdnsServerID,:externalDefaultDNS,:managedServer,:managedForID,:reseller_id)");
                        $query->execute(array(':aeskey' => $aeskey, ':active' => $active, ':connect_ip_only' => $connectIpOnly, ':type' => $type, ':description' => $description, ':defaultname' => $defaultname, ':bit' => $bit, ':queryport' => $queryport, ':querypassword' => $querypassword, ':filetransferport' => $filetransferport, ':maxserver' => $maxserver, ':maxslots' => $maxslots, ':rootid' => $rootid, ':addedby' => $addtype, ':usedns' => $usedns, ':defaultdns' => $defaultdns, ':defaultwelcome' => $defaultwelcome, ':defaulthostbanner_url' => $defaulthostbanner_url, ':defaulthostbanner_gfx_url' => $defaulthostbanner_gfx_url, ':defaulthostbutton_tooltip' => $defaulthostbutton_tooltip, ':defaulthostbutton_url' => $defaulthostbutton_url, ':defaulthostbutton_gfx_url' => $defaulthostbutton_gfx_url, ':defaultFlexSlotsFree' => $defaultFlexSlotsFree, ':defaultFlexSlotsPercent' => $defaultFlexSlotsPercent, ':publickey' => $publickey, ':ssh2ip' => $ip, ':ssh2port' => $port, ':ssh2user' => $user, ':ssh2password' => $pass, ':ips' => $ips, ':serverdir' => $serverdir, ':keyname' => $keyname, ':autorestart' => $autorestart, ':externalID' => $externalID, ':tsdnsServerID' => $tsdnsServerID, ':externalDefaultDNS' => $externalDefaultDNS, ':managedServer' => $managedServer , ':managedForID' => $resellerToBeWritten, ':reseller_id' => $reseller_id));

                        $rowCount = $query->rowCount();

                        $masterid = $sql->lastInsertId();
                    }

                    $loguseraction = '%add% %voserver% %master% ' . $ip;
                    $insertlog->execute();

                }

                $template_file = 'admin_voicemasterserver_add2.tpl';

            } else if ($ui->w('action', 3, 'post') == 'md') {

                if (($oldactive == 'Y' and $active == 'N') or ($oldactive == 'N' and $active == 'Y')) {


                    $split_config = preg_split('/\//', $serverdir, -1, PREG_SPLIT_NO_EMPTY);
                    $folderfilecount = count($split_config)-1;

                    $i = 0;
                    while ($i <= $folderfilecount) {
                        if (isset($commandFolders)) {
                            $commandFolders .= $split_config[$i] . '/';
                        } else {
                            $commandFolders = 'cd ' . $split_config[$i] . '/';
                        }
                        $i++;
                    }

                    if (isset($commandFolders)) {
                        $commandFolders .= ' && ';
                    } else {
                        $commandFolders = '';
                    }

                    if ($bit == '32') {
                        $tsbin = 'ts3server_linux_x86';
                        $tsdnsbin = 'tsdnsserver_linux_x86';

                    } else {
                        $tsbin = 'ts3server_linux_amd64';
                        $tsdnsbin = 'tsdnsserver_linux_amd64';
                    }

                    if ($active == 'N') {
                        $ssh2cmd = "ps fx | grep '$tsbin' | grep -v 'grep' | awk '{print $1}' | while read pid; do kill ".'$pid'."; done";
                        $ssh2cmd2 = "ps fx | grep '$tsdnsbin' | grep -v 'grep' | awk '{print $1}' | while read pid; do kill ".'$pid'."; done";

                    } else if ($active == 'Y') {
                        $ssh2cmd = $commandFolders . 'function restart1 () { if [ "`ps fx | grep ' . $tsbin . ' | grep -v grep`" == "" ]; then ./ts3server_startscript.sh start > /dev/null & else ./ts3server_startscript.sh restart > /dev/null & fi }; restart1& ';
                        $ssh2cmd2 = $commandFolders . ' cd tsdns && function restart2 () { if [ "`ps fx | grep '.$tsdnsbin.' | grep -v grep`" == "" ]; then ./'.$tsdnsbin.' > /dev/null & else ./'.$tsdnsbin.' --update > /dev/null & fi }; restart2& ';
                    }

                    if ($usedns == 'Y') {
                        $cmds = array($ssh2cmd, $ssh2cmd2);
                    } else {
                        $cmds = array($ssh2cmd);
                    }

                    ssh2_execute('vm', $id, $cmds);
                }

                // https://github.com/easy-wi/developer/issues/36 managedServer,managedForID added
                $query = $sql->prepare("UPDATE `voice_masterserver` SET `active`=:active,`connect_ip_only`=:connect_ip_only,`description`=:description,`managedServer`=:managedServer,`managedForID`=:managedForID,`externalID`=:externalID,`defaultname`=:defaultname,`bitversion`=:bit,`queryport`=:queryport,`querypassword`=AES_ENCRYPT(:querypassword,:aeskey),`filetransferport`=:filetransferport,`maxserver`=:maxserver,`maxslots`=:maxslots,`usedns`=:usedns,`defaultdns`=:defaultdns,`defaultwelcome`=:defaultwelcome,`defaulthostbanner_url`=:defaulthostbanner_url,`defaulthostbanner_gfx_url`=:defaulthostbanner_gfx_url,`defaulthostbutton_tooltip`=:defaulthostbutton_tooltip,`defaulthostbutton_url`=:defaulthostbutton_url,`defaulthostbutton_gfx_url`=:defaulthostbutton_gfx_url,`defaultFlexSlotsFree`=:defaultFlexSlotsFree,`defaultFlexSlotsPercent`=:defaultFlexSlotsPercent,`publickey`=:publickey,`ssh2ip`=:ssh2ip,`ssh2port`=AES_ENCRYPT(:ssh2port,:aeskey),`ssh2user`=AES_ENCRYPT(:ssh2user,:aeskey),`ssh2password`=AES_ENCRYPT(:ssh2password,:aeskey),`ips`=:ips,`serverdir`=:serverdir,`keyname`=:keyname,`autorestart`=:autorestart,`tsdnsServerID`=:tsdnsServerID,`externalDefaultDNS`=:externalDefaultDNS WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':aeskey' => $aeskey,':active' => $active, ':connect_ip_only' => $connectIpOnly,':description' => $description,':managedServer' => $managedServer,':managedForID' => $resellerToBeWritten,':externalID' => $externalID,':defaultname' => $defaultname,':bit' => $bit,':queryport' => $queryport,':querypassword' => $querypassword,':filetransferport' => $filetransferport,':maxserver' => $maxserver,':maxslots' => $maxslots,':usedns' => $usedns,':defaultdns' => $defaultdns,':defaultwelcome' => $defaultwelcome,':defaulthostbanner_url' => $defaulthostbanner_url,':defaulthostbanner_gfx_url' => $defaulthostbanner_gfx_url,':defaulthostbutton_tooltip' => $defaulthostbutton_tooltip,':defaulthostbutton_url' => $defaulthostbutton_url,':defaulthostbutton_gfx_url' => $defaulthostbutton_gfx_url,':defaultFlexSlotsFree' => $defaultFlexSlotsFree,':defaultFlexSlotsPercent' => $defaultFlexSlotsPercent,':publickey' => $publickey,':ssh2ip' => $ip,':ssh2port' => $port,':ssh2user' => $user,':ssh2password' => $pass,':ips' => $ips,':serverdir' => $serverdir,':keyname' => $keyname,':autorestart' => $autorestart,':tsdnsServerID' => $tsdnsServerID,':externalDefaultDNS' => $externalDefaultDNS,':id' => $id,':reseller_id' => $reseller_id));

                $rowCount = $query->rowCount();
                $template_file = $spracheResponse->table_add;
                $loguseraction = '%mod% %voserver% %master% ' . $ip;
            }

        } else if ($ui->w('action', 3, 'post') == 'ad2' and $ui->id('id', 10, 'get')) {

            # get masterserver data to be able to import existing voice servers
            $masterid = $ui->id('id', 10, 'get');

            # Suhoshin has a POST Limit. So we need to limit as well.
            $prefix = $rSA['prefix2'];
            $i = 0;
            $toomuch = 0;
            $added = '<br />Added:';
            $ssh2ip = '';

            $query = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':aeskey' => $aeskey,':id' => $masterid,':reseller_id' => $reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $defaultdns = $row['defaultdns'];
                $serverdir = $row['serverdir'];
                $addedby = $row['addedby'];
                $usedns = $row['usedns'];
                $defaultdns = $row['defaultdns'];
                $queryport = $row['queryport'];
                $querypassword = $row['decryptedquerypassword'];
                $mnotified = $row['notified'];
                $defaultwelcome = $row['defaultwelcome'];
                $defaulthostbanner_url = $row['defaulthostbanner_url'];
                $defaulthostbanner_gfx_url = $row['defaulthostbanner_gfx_url'];
                $defaulthostbutton_tooltip = $row['defaulthostbutton_tooltip'];
                $defaulthostbutton_url = $row['defaulthostbutton_url'];
                $defaulthostbutton_gfx_url = $row['defaulthostbutton_gfx_url'];
                $tsdnsServerID = $row['tsdnsServerID'];
                $externalDefaultDNS = $row['externalDefaultDNS'];
                $publickey = $row['publickey'];
                $queryip = $row['ssh2ip'];
                $ssh2ip = $row['ssh2ip'];
                $ssh2port = $row['decryptedssh2port'];
                $ssh2user = $row['decryptedssh2user'];
                $ssh2password = $row['decryptedssh2password'];
                $keyname = $row['keyname'];
                $bitversion = $row['bitversion'];

                 if ($addedby==1) {
                    $query2 = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($row['rootid'], $reseller_id));
                    $queryip = $query2->fetchColumn();
                }

                $TSDNSSsh2ip = ($row['connect_ip_only'] == 'Y') ? $row['ips'] : $ssh2ip;
            }

            # will only be an ID in case a master DNS has been chosen
            if (isid($tsdnsServerID,19)) {
                $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':aeskey' => $aeskey,':id' => $tsdnsServerID,':reseller_id' => $reseller_id));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $publickey = $row['publickey'];
                    $ssh2port = $row['decryptedssh2port'];
                    $ssh2user = $row['decryptedssh2user'];
                    $ssh2password = $row['decryptedssh2password'];
                    $serverdir = $row['serverdir'];
                    $keyname = $row['keyname'];
                    $bitversion = $row['bitversion'];

                    if ($externalDefaultDNS== 'Y') {
                        $defaultdns = $row['defaultdns'];
                    }

                    $TSDNSSsh2ip =  ($row['connect_ip_only'] == 'Y') ? $row['external_ip'] : $row['ssh2ip'];
                }
            }

            // Connect to TS3 Server. Only if successfull do the import

            $connection = new TS3($ssh2ip, $queryport, 'serveradmin', $querypassword);

            if ($connection->socketConnected === true and strpos($connection->errorcode,'error id=0') !== false) {

                foreach ($ui->id('virtualserver_id', 19, 'post') as $virtualserver_id) {

                    if ($ui->active("$virtualserver_id-import", 'post') == 'Y') {

                        $customerID = $ui->id("$virtualserver_id-customer", 10, 'post');

                        if ($customerID == 0 or $customerID == false or $customerID == null) {

                            $usernew = true;

                            if ($ui->username("$virtualserver_id-username", 50, 'post') and $ui->ismail("$virtualserver_id-email", 'post')) {

                                $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`=? AND `resellerid`=? LIMIT 1");
                                $query->execute(array($ui->username("$virtualserver_id-username", 50, 'post'), $ui->ismail("$virtualserver_id-email", 'post'), $reseller_id));
                                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                    $usernew = false;
                                    $customerID = $row['id'];
                                    $cnamenew = $ui->username("$virtualserver_id-username", 50, 'post');
                                }

                                if ($usernew == true) {

                                    $initialpassword = passwordgenerate(10);

                                    $newHash = passwordCreate($ui->username("$virtualserver_id-username", 50, 'post'), $initialpassword);

                                    if (is_array($newHash)) {
                                        $query = $sql->prepare("INSERT INTO `userdata` (`cname`,`security`,`salt`,`mail`,`accounttype`,`resellerid`) VALUES (?,?,?,?,'u',?)");
                                        $query->execute(array($ui->username("$virtualserver_id-username", 50, 'post'), $newHash['hash'], $newHash['salt'], $ui->ismail("$virtualserver_id-email", 'post'), $reseller_id));
                                    } else {
                                        $query = $sql->prepare("INSERT INTO `userdata` (`cname`,`security`,`mail`,`accounttype`,`resellerid`) VALUES (?,?,?,'u',?)");
                                        $query->execute(array($ui->username("$virtualserver_id-username", 50, 'post'), $newHash, $ui->ismail("$virtualserver_id-email", 'post'), $reseller_id));
                                    }

                                    $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
                                    $query->execute(array($ui->username("$virtualserver_id-username", 50, 'post'), $ui->ismail("$virtualserver_id-email", 'post'), $reseller_id));
                                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                        $customerID = $row['id'];
                                        $cnamenew = $ui->username("$virtualserver_id-username", 50, 'post');
                                        sendmail('emailuseradd', $customerID, $cnamenew, $initialpassword);
                                    }
                                }

                            } else {

                                $userlist = '';
                                $cldbid = rand(1, 100) . '.' . rand(1, 100);

                                $adminList = $connection->AdminList($virtualserver_id);

                                if (is_array($adminList)) {
                                    foreach ($adminList as $cldbid => $client_unique_identifier) {
                                        $userlist .= $cldbid . ':' . $client_unique_identifier . '|';
                                    }
                                }

                                $cnamenew = $prefix . $cldbid;

                                $query = $sql->prepare("INSERT INTO `userdata` (`cname`,`security`,`mail`,`accounttype`,`resellerid`) VALUES (?,?,?,'u',?)");
                                $query->execute(array($cnamenew, $userlist,'ts3@import.mail', $reseller_id));

                                $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`='ts3@import.mail' ORDER BY `id` DESC LIMIT 1");
                                $query->execute(array($cnamenew));
                                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                    $customerID = $row['id'];
                                    $cnamenew = $prefix . $customerID;
                                }

                                $query = $sql->prepare("UPDATE `userdata` SET `cname`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                                $query->execute(array($cnamenew, $customerID, $reseller_id));
                            }

                            if ($usernew == true) {
                                $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `active`='Y' AND `defaultgroup`='Y' AND `grouptype`='u' AND `resellerid`=? LIMIT 1");
                                $query->execute(array($reseller_id));
                                $groupID = $query->fetchColumn();

                                $query = $sql->prepare("INSERT INTO `userdata_groups` (`userID`,`groupID`,`resellerID`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
                                $query->execute(array($customerID, $groupID, $reseller_id));
                            }

                            $added .= 'User ' . $cnamenew . ' ';

                        } else {
                            $query = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                            $query->execute(array($customerID, $reseller_id));
                            $cnamenew = $query->fetchColumn();
                        }

                        $slots = $ui->id("$virtualserver_id-virtualserver_maxclients", 30, 'post');
                        $port = $ui->port("$virtualserver_id-virtualserver_port", 'post');
                        $forcewelcome = $ui->active("$virtualserver_id-forcewelcome", 'post');
                        $forcebanner = $ui->active("$virtualserver_id-forcebanner", 'post');
                        $forcebutton = $ui->active("$virtualserver_id-forcebutton", 'post');
                        $forceservertag = $ui->active("$virtualserver_id-forceservertag", 'post');
                        $flexSlots = $ui->active("$virtualserver_id-flexSlots", 'post');
                        $flexSlotsFree = $ui->id("$virtualserver_id-flexSlotsFree", 11, 'post');
                        $flexSlotsPercent = $ui->id("$virtualserver_id-flexSlotsPercent", 3, 'post');
                        $password = ($ui->id("$virtualserver_id-password", 1, 'post') == 1) ? 'Y' : 'N';
                        $serverdns = ($ui->domain("$virtualserver_id-virtualserver_dns", 'post') == '') ? $cnamenew . '-' . $virtualserver_id . '.' . $defaultdns : $ui->domain("$virtualserver_id-virtualserver_dns", 'post');

                        if ($port != null) {
                            $serverdns = strtolower($serverdns);
                            unset($addlist);
                            $addlist = array();
                            unset($removelist);
                            $removelist = array();
                            unset($settings);
                            $settings = array();
                            $settings['virtualserver_max_download_total_bandwidth'] = 65536;
                            $settings['virtualserver_max_upload_total_bandwidth'] = 65536;

                            if ($forcebanner== 'Y') {
                                $removelist[] = 'b_virtualserver_modify_hostbanner';
                                $removelist[] = 'i_needed_modify_power_virtualserver_modify_hostbanner';
                                $settings['virtualserver_hostbanner_url'] = $defaulthostbanner_url;
                                $settings['virtualserver_hostbanner_gfx_url'] = $defaulthostbanner_gfx_url;

                            } else if ($forcebanner== 'N') {
                                $addlist[] = 'b_virtualserver_modify_hostbanner';
                                $addlist[] = 'i_needed_modify_power_virtualserver_modify_hostbanner';
                            }

                            if ($forcebutton == 'Y') {
                                $removelist[] = 'b_virtualserver_modify_hostbutton';
                                $removelist[] = 'i_needed_modify_power_virtualserver_modify_hostbutton';
                                $settings['virtualserver_hostbutton_url'] = $defaulthostbutton_url;
                                $settings['virtualserver_hostbutton_gfx_url'] = $defaulthostbutton_gfx_url;
                                $settings['virtualserver_hostbutton_tooltip'] = $defaulthostbutton_tooltip;

                            } else if ($forcebutton == 'N') {
                                $addlist[] = 'b_virtualserver_modify_hostbutton';
                                $addlist[] = 'i_needed_modify_power_virtualserver_modify_hostbutton';
                            }

                            if ($forcewelcome == 'Y') {
                                $removelist[] = 'b_virtualserver_modify_welcomemessage';
                                $removelist[] = 'i_needed_modify_power_virtualserver_modify_welcomemessage';
                                $settings['virtualserver_welcomemessage'] = $defaultwelcome;

                            } else if ($forcewelcome == 'N') {
                                $addlist[] = 'b_virtualserver_modify_welcomemessage';
                                $addlist[] = 'i_needed_modify_power_virtualserver_modify_welcomemessage';
                            }

                            if (isset($addlist)) {
                                $connection->AdminPermissions($virtualserver_id,'add', $addlist);
                            }

                            if (isset($removelist)) {
                                $connection->AdminPermissions($virtualserver_id,'del', $removelist);
                            }

                            $connection->ImportModServer($virtualserver_id, $slots, $TSDNSSsh2ip, $port, $settings);

                            $added .= 'Server '.$ssh2ip . ':' . $port . '<br />';

                            $query = $sql->prepare("INSERT INTO `voice_server` (`userid`,`masterserver`,`ip`,`port`,`slots`,`password`,`forcebanner`,`forcebutton`,`forceservertag`,`forcewelcome`,`dns`,`flexSlots`,`flexSlotsFree`,`flexSlotsPercent`,`localserverid`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                            $query->execute(array($customerID, $masterid, $TSDNSSsh2ip, $port, $slots, $password, $forcebanner, $forcebutton, $forceservertag, $forcewelcome, $serverdns, $flexSlots, $flexSlotsFree, $flexSlotsPercent, $virtualserver_id, $reseller_id));
                        }

                        $i++;
                    }
                }

                $not = '';
                $connection->CloseConnection();

                if ($usedns == 'Y') {

                    $dns = array();

                    if (isid($tsdnsServerID, 19)) {

                        $query = $sql->prepare("SELECT `id` FROM `voice_masterserver` WHERE `tsdnsServerID`=? AND `resellerid`=?");
                        $query2 = $sql->prepare("SELECT `ip`,`port`,`dns` FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=?");

                        $query->execute(array($tsdnsServerID, $reseller_id));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                            $query2->execute(array($row['id'], $reseller_id));
                            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                                $dns[] = $row2['dns'].'='.$row2['ip'] . ':' . $row2['port'];
                            }
                        }

                    } else {

                        $query = $sql->prepare("SELECT `ip`,`port`,`dns` FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=?");
                        $query->execute(array($masterid, $reseller_id));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            $dns[] = $row['dns'].'='.$row['ip'] . ':' . $row['port'];
                        }
                    }

                    $dns = array_unique($dns);

                    if ($i > 0) {
                        $template_file = tsdns('mw', $TSDNSSsh2ip, $ssh2port, $ssh2user, $publickey, $keyname, $ssh2password, $mnotified, $serverdir, $bitversion, $dns,'','', $reseller_id);
                    }
                }
            }

            if ($added == '<br />Added:') {
                $template_file = $spracheResponse->error_table;
            } else {
                $template_file = $spracheResponse->table_add.$not.$added;
            }

        } else {
            unset($header, $text);
            $template_file = ($ui->st('d', 'get') == 'md') ? 'admin_voicemasterserver_md.tpl' : 'admin_voicemasterserver_add.tpl';
        }
    } else {
        $template_file = 'admin_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->w('action', 3, 'post')) {

        $query = $sql->prepare("SELECT `ssh2ip`,`description`,`rootid`,`type` FROM `voice_masterserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $ip = $row['ssh2ip'];
            $description = $row['description'];
            $type = $row['type'];

            if ($ip==null) {
                $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($row['rootid'], $reseller_id));
                $ip = $query->fetchColumn();
            }
        }

        $template_file = ($query->rowCount() > 0) ? 'admin_voicemasterserver_dl.tpl' : 'admin_404.tpl';

    } else if ($ui->w('action', 3, 'post') == 'dl') {

        $query = $sql->prepare("SELECT `ssh2ip`,`rootid`,`type` FROM `voice_masterserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $ip = $row['ssh2ip'];
            $type = $row['type'];

            if ($row['ssh2ip'] == null) {
                $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($row['rootid'], $reseller_id));
                $ip = $query->fetchColumn();
            }
        }
        if ($query->rowCount() > 0) {

            $query = $sql->prepare("DELETE FROM `voice_masterserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $reseller_id));
            $query = $sql->prepare("DELETE FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=?");
            $query->execute(array($id, $reseller_id));

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% %voserver% %master% ' . $ip . ' ' . $type;
            $insertlog->execute();

        } else {
            $spracheResponse->error_table;
        }
    } else {
        $template_file = 'admin_404.tpl';
    }

} else {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=voicemasterserver');

    $template_file = 'admin_voicemasterserver_list.tpl';
}