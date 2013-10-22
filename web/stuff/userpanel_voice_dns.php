<?php
/**
 * File: userpanel_voice_dns.php.
 * Author: Ulrich Block
 * Date: 23.09.12
 * Time: 23:10
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
$sprache = getlanguagefile('voice',$user_language,$reseller_id);
$loguserid = $user_id;
$logusername=getusername($user_id);
$logusertype='user';
$logreseller = 0;
if (isset($admin_id)) {
    $logsubuser = $admin_id;
} else if (isset($subuser_id)) {
    $logsubuser = $subuser_id;
} else {
    $logsubuser = 0;
}
include(EASYWIDIR . '/stuff/class_voice.php');
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('d', 'get') == 'md' and $ui->id('id',19, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'),$substituteAccess['vd']))) {
    $id = $ui->id('id',19, 'get');
    if (!$ui->smallletters('action',2, 'post')) {
        $query = $sql->prepare("SELECT d.`dnsID`,d.`dns`,d.`ip`,d.`port`,t.`defaultdns` FROM `voice_dns` d LEFT JOIN `voice_tsdns` t ON d.`tsdnsID`=t.`id` WHERE d.`active`='Y' AND d.`dnsID`=? AND d.`resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $dns = $row['dns'];
            $ip = $row['ip'];
            $port = $row['port'];
            $defaultdns=strtolower($row['dnsID'] . '-' . getusername($user_id).$row['defaultdns']);
        }
        if (isset($dns)) {
            $template_file = 'userpanel_voiceserver_dns_md.tpl';
        } else {
            $template_file = 'userpanel_404.tpl';
        }
    } else if ($ui->smallletters('action',2, 'post') == 'md') {
        $query = $sql->prepare("SELECT d.`tsdnsID`,d.`dnsID`,d.`dns`,d.`ip`,d.`port`,t.`defaultdns` FROM `voice_dns` d LEFT JOIN `voice_tsdns` t ON d.`tsdnsID`=t.`id` WHERE d.`active`='Y' AND d.`dnsID`=? AND d.`resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $tsdnsID = $row['tsdnsID'];
            $olddns = $row['dns'];
            $oldip = $row['ip'];
            $oldport = $row['port'];
        }
        if (isset($olddns)) {
            $error = array();
            if ($ui->ip('ip', 'post')) {
                $ip = $ui->ip('ip', 'post');
            } else {
                $error[]="IP";
            }
            if ($ui->port('port', 'post')) {
                $port = $ui->port('port', 'post');
            } else {
                $error[]="Port";
            }
            if ($ui->domain('dns', 'post')) {
                $dns=strtolower($ui->domain('dns', 'post'));
            } else {
                $error[]="DNS";
            }
            if (count($error)==0 and $ip==$oldip and $dns==$olddns and $port==$oldport) {
                $error[] = $spracheResponse->error_table;
            } else if (count($error)==0 and checkDNS($dns,$id,$user_id,$type='dns') === false) {
                $error[]="DNS";
            }
            if (count($error)>0) {
                $template_file = 'Error: '.implode('<br />',$error);
            } else {
				include(EASYWIDIR . '/stuff/keyphrasefile.php');
                $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':aeskey' => $aeskey,':id' => $tsdnsID,':reseller_id' => $reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $publickey = $row['publickey'];
                    $queryip = $row['ssh2ip'];
                    $ssh2port = $row['decryptedssh2port'];
                    $ssh2user = $row['decryptedssh2user'];
                    $ssh2password = $row['decryptedssh2password'];
                    $serverdir = $row['serverdir'];
                    $keyname = $row['keyname'];
                    $bitversion = $row['bitversion'];
                }
                if (isset($publickey)) {
                    $template_file = tsdns('md',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,0,$serverdir,$bitversion, array($ip,$oldip), array($port,$oldport), array($dns,$olddns),$reseller_id);
                    $query = $sql->prepare("UPDATE `voice_dns` SET `dns`=?,`ip`=?,`port`=? WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($dns,$ip,$port,$id,$reseller_id));
                } else {
                    $template_file = 'userpanel_404.tpl';
                }
            }
        } else {
            $template_file = 'userpanel_404.tpl';
        }
    }
} else {
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'dd') {
        $orderby = '`dns` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = '`dns` ASC';
    } else if ($ui->st('o', 'get') == 'db') {
        $orderby = '`ip` DESC,`port` DESC';
    } else if ($ui->st('o', 'get') == 'ab') {
        $orderby = '`ip` ASC,`port` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = '`dnsID` DESC';
    } else {
        $orderby = '`dnsID` ASC';
        $o = 'ai';
    }
    $table = array();
    $query = $sql->prepare("SELECT `dnsID`,`dns`,`ip`,`port` FROM `voice_dns` WHERE `active`='Y' AND `userID`=? AND `resellerID`=? ORDER BY $orderby");
    $query->execute(array($user_id,$reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        if (!isset($_SESSION['sID']) or in_array($row['dnsID'],$substituteAccess['vd'])) $table[] = array('id' => $row['dnsID'], 'dns' => $row['dns'], 'address' => $row['ip'] . ':' . $row['port']);
    }
    $template_file = 'userpanel_voiceserver_dns_list.tpl';
}