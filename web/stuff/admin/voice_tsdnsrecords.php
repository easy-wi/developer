<?php
/**
 * File: voice_tsdnsrecords.php.
 * Author: Ulrich Block
 * Date: 23.09.12
 * Time: 14:43
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['voiceserver'])) {
	header('Location: admin.php');
	die;
}

include(EASYWIDIR . '/stuff/methods/functions_ts3.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('voice',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id == 0) {
    $logreseller = 0;
    $logsubuser = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller = 0;
}
if ($reseller_id != 0 and $admin_id != $reseller_id) $reseller_id = $admin_id;
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    if ($ui->st('d', 'get') == 'ad' and !$ui->w('action',3, 'post')) {

        $table = array();
        $table2 = array();

        $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
        }

        $query = $sql->prepare("SELECT m.`id`,m.`ssh2ip`,m.`description`, COUNT(d.`dnsID`)/(m.`max_dns`/100) AS `usedpercent` FROM `voice_tsdns` AS m LEFT JOIN `voice_dns` AS d ON d.`tsdnsID`=m.`id` WHERE m.`resellerid`=? AND m.`active`='Y' GROUP BY m.`id` HAVING `usedpercent`<100 ORDER BY `usedpercent` ASC");
        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $table2[$row['id']] = trim($row['ssh2ip'] . ' ' . $row['description']);
        }

        $template_file = 'admin_voice_dns_add.tpl';

    } else if ($ui->st('d', 'get') == 'ad' and $ui->smallletters('action',2, 'post') == 'ad') {

        $error = array();

        if (!$ui->id('userID',19, 'post')) {
            $error[] = 'UserID';
        } else {
            $userID = $ui->id('userID',19, 'post');
            $query = $sql->prepare("SELECT `cname`,`vname`,`name` FROM `userdata` WHERE `id`=? AND `resellerid`=? AND `accounttype`='u' LIMIT 1");
            $query->execute(array($userID,$reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $user = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
                $cname = trim($row['cname']);
            }
            if (!isset($user)) {
                $error[] = 'UserID';
            }
        }

        if (!$ui->id('tsdnsID',19, 'post')) {
            $error[] = 'tsdnsID';
        } else {
            $tsdnsID = $ui->id('tsdnsID',19, 'post');
            $query = $sql->prepare("SELECT `dnsID` FROM `voice_dns` WHERE `resellerid`=? ORDER BY `dnsID` DESC LIMIT 1");
            $query->execute(array($reseller_id));
            $lastID = 1;
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $lastID = $row['dnsID'] + 1;
            }
            $query = $sql->prepare("SELECT `ssh2ip`,`description`,`defaultdns` FROM `voice_tsdns` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($tsdnsID,$reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $tsdns = trim($row['ssh2ip'] . ' ' . $row['description']);
                $dns = strtolower(trim($lastID . '-' . $cname . '.' . $row['defaultdns']));
            }
            if (!isset($tsdns)) {
                $error[] = 'tsdnsID';
            }
        }
        if (count($error)>0) {
            $template_file = 'Error: '.implode('<br />',$error);
        } else {
            $template_file = 'admin_voice_dns_add2.tpl';
        }
    } else if ($ui->st('d', 'get') == 'md' and !$ui->smallletters('action',2, 'post') and $ui->id('id', 10, 'get')) {
        $id = $ui->id('id', 10, 'get');
        $query = $sql->prepare("SELECT d.*,t.`ssh2ip`,t.`description`,u.`cname`,u.`vname`,u.`name` FROM `voice_dns` d INNER JOIN `voice_tsdns` t ON d.`tsdnsID`=t.`id` INNER JOIN `userdata` u ON d.`userID`=u.`id` WHERE d.`dnsID`=? AND d.`resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $jobPending=($row['jobPending'] == 'Y') ? $gsprache->yes : $gsprache->no;
            if ($row['jobPending'] == 'Y') {
                $query2 = $sql->prepare("SELECT `extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='us' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");
                $query2->execute(array($row['id'], $row['resellerid']));
                $json=@json_decode($query2->fetchColumn());
                $active=(is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
            } else {
                $active = $row['active'];
            }
            $dns = $row['dns'];
            $ip = $row['ip'];
            $port = $row['port'];
            $externalID = $row['externalID'];
            $tsdns = trim($row['ssh2ip'] . ' ' . $row['description']);
            $user = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
        }
        if (isset($active)) {
            $template_file = 'admin_voice_dns_md.tpl';
        } else {
            $template_file = 'admin_404.tpl'; 
        }
    } else if ($ui->w('action',3, 'post') == 'ad2' or $ui->smallletters('action',2, 'post') == 'md') {
        $error = array();

        $externalID = $ui->externalID('externalID', 'post');

        if ($ui->active('active', 'post')) {
            $active = $ui->active('active', 'post');
        } else {
            $error[] = 'Active';
        }
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

        if ($ui->w('action',3, 'post') == 'ad2') {
            if ($ui->id('userID',19, 'post')) {
                $userID = $ui->id('userID',19, 'post');
                $query = $sql->prepare("SELECT `cname`,`vname`,`name` FROM `userdata` WHERE `id`=? AND `resellerid`=? AND `accounttype`='u' LIMIT 1");
                $query->execute(array($userID,$reseller_id));
                if ($query->rowCount()==0) {
                    $error[] = 'UserID does not exist';
                }
            } else {
                $error[] = 'UserID';
            }
            if ($ui->id('tsdnsID',19, 'post')) {
                $tsdnsID = $ui->id('tsdnsID',19, 'post');
                $query = $sql->prepare("SELECT `id` FROM `voice_tsdns` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($tsdnsID,$reseller_id));
                if ($query->rowCount()==0) {
                    $error[] = 'tsdnsID does not exist';
                }
            } else {
                $error[] = 'tsdnsID';
            }
        } else {
            $id = $ui->id('id', 10, 'get');
            $query = $sql->prepare("SELECT `active`,`dns`,`ip`,`port`,`tsdnsID` FROM `voice_dns` WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($id,$reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $oldactive = $row['active'];
                $olddns = $row['dns'];
                $oldip = $row['ip'];
                $oldport = $row['port'];
                $tsdnsID = $row['tsdnsID'];
            }
            if (!isset($tsdnsID)) {
                $error[] = 'No such ID';
            }
        }
        if (count($error)>0) {
            $template_file = 'Error: '.implode('<br />',$error);

        } else {

            $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':aeskey' => $aeskey,':id' => $tsdnsID,':reseller_id' => $reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $publickey = $row['publickey'];
                $queryip = $row['ssh2ip'];
                $ssh2port = $row['decryptedssh2port'];
                $ssh2user = $row['decryptedssh2user'];
                $ssh2password = $row['decryptedssh2password'];
                $serverdir = $row['serverdir'];
                $keyname = $row['keyname'];
                $bitversion = $row['bitversion'];
            }

            if (isset($queryip) and $ui->w('action',3, 'post') == 'ad2') {
                $log='add';
                $query = $sql->prepare("SELECT COUNT(`dnsID`) AS `amount` FROM `voice_dns` WHERE `dns`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($dns,$reseller_id));
                $query2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `dns`=? AND `resellerid`=? LIMIT 1");
                $query2->execute(array($dns,$reseller_id));
                if ($query->fetchColumn()==0 and $query2->fetchColumn()==0) {
                    if ($active == 'Y') {
                        $template_file = tsdns('md',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,0,$serverdir,$bitversion, array($ip), array($port), array($dns),$reseller_id);
                    }
                    $query = $sql->prepare("INSERT INTO `voice_dns` (`active`,`dns`,`ip`,`port`,`tsdnsID`,`userID`,`externalID`,`resellerID`) VALUES (?,?,?,?,?,?,?,?)");
                    $query->execute(array($active,$dns,$ip,$port,$tsdnsID,$userID,$externalID,$reseller_id));
                } else {
                    $insterfail = true;
                }
            } else if (isset($queryip) and $ui->smallletters('action', 2, 'post') == 'md') {
                if ($dns != $olddns) {
                    $query = $sql->prepare("SELECT COUNT(`dnsID`) AS `amount` FROM `voice_dns` WHERE `dnsID`!=? AND `dns`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($id,$dns,$reseller_id));
                    $query2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `dns`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($dns,$reseller_id));
                    if ($query->fetchColumn() != 0 and $query2->fetchColumn() != 0) {
                        $insterfail = true;
                    }
                }
                if ($active != $oldactive and $active == 'N') {
                    $dnsAction='dl';
                    $ipArray=array($oldip);
                    $portArray=array($oldport);
                    $dnsArray=array($olddns);
                } else if ($active != $oldactive and $active == 'Y') {
                    $dnsAction='md';
                    $ipArray=array($ip);
                    $portArray=array($port);
                    $dnsArray=array($dns);
                } else if ($active == 'Y' and ($ip != $oldip or $port != $oldport or $dns != $olddns)) {
                    $dnsAction='md';
                    $ipArray=array($ip,$oldip);
                    $portArray=array($port,$oldport);
                    $dnsArray=array($dns,$olddns);
                }
                if (isset($dnsAction)) {
                    $template_file = tsdns($dnsAction,$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,0,$serverdir,$bitversion,$ipArray,$portArray,$dnsArray,$reseller_id);
                }
                $log='mod';
                $query = $sql->prepare("UPDATE `voice_dns` SET `active`=?,`dns`=?,`ip`=?,`port`=?,`externalID`=? WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($active,$dns,$ip,$port,$externalID,$id,$reseller_id));
            }

            if (isset($queryip) and !isset($insterfail) and $query->rowCount() > 0) {
                $template_file = $spracheResponse->table_add;
                $loguseraction="%$log% %voserver% %dns% $ip";
                $insertlog->execute();
            } else if (isset($queryip) and isset($insterfail)) {
                $template_file = 'Error: DNS already exists';
            } else {
                $template_file = $spracheResponse->error_table;
            }
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {
    $id = $ui->id('id', 10, 'get');
    if (!$ui->smallletters('action',2, 'post')) {
        $query = $sql->prepare("SELECT `dns`,`ip`,`port` FROM `voice_dns` WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $ip = $row['ip'];
            $port = $row['port'];
            $dns = $row['dns'];
        }
        if (isset($ip)) {
            $template_file = 'admin_voice_dns_dl.tpl';
        } else {
            $template_file = 'admin_404.tpl'; 
        }
    } else if ($ui->smallletters('action',2, 'post') == 'dl'){
        $query = $sql->prepare("SELECT `dns`,`ip`,`port`,`tsdnsID` FROM `voice_dns` WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $ip = $row['ip'];
            $port = $row['port'];
            $dns = $row['dns'];
            $tsdnsID = $row['tsdnsID'];
            $deleteDNS = $row['ip'] . ' ' . $row['port'] . ' ' . $row['dns'];
        }
        $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
        $query->execute(array(':aeskey' => $aeskey,':id' => $tsdnsID,':reseller_id' => $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $publickey = $row['publickey'];
            $queryip = $row['ssh2ip'];
            $ssh2port = $row['decryptedssh2port'];
            $ssh2user = $row['decryptedssh2user'];
            $ssh2password = $row['decryptedssh2password'];
            $serverdir = $row['serverdir'];
            $keyname = $row['keyname'];
            $bitversion = $row['bitversion'];
        }
        if ($query->rowCount() > 0) {
            $query = $sql->prepare("DELETE FROM `voice_dns` WHERE `dnsID`=? AND `resellerid`=?");
            $query->execute(array($id,$reseller_id));
            if ($query->rowCount() > 0) {
                $template_file = tsdns('dl',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,0,$serverdir,$bitversion, array($ip), array($port), array($dns),$reseller_id);
                $loguseraction="%del% %voserver% %dns% $deleteDNS";
                $insertlog->execute();
                $template_file = $spracheResponse->table_del;
            } else {
                $template_file = $spracheResponse->error_table;
            }
        } else {
            $template_file = 'admin_404.tpl'; 
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else {
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'da') {
        $orderby = 'd.`active` DESC';
    } else if ($ui->st('o', 'get') == 'aa') {
        $orderby = 'd.`active` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = 't.`ssh2ip` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = 't.`ssh2ip` ASC';
    } else if ($ui->st('o', 'get') == 'dd') {
        $orderby = 'd.`dns` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = 'd.`dns` ASC';
    } else if ($ui->st('o', 'get') == 'db') {
        $orderby = 'd.`ip` DESC,d.`port` DESC';
    } else if ($ui->st('o', 'get') == 'ab') {
        $orderby = 'd.`ip` ASC,d.`port` ASC';
    } else if ($ui->st('o', 'get') == 'du') {
        $orderby = 'u.`cname` DESC';
    } else if ($ui->st('o', 'get') == 'au') {
        $orderby = 'u.`cname` ASC';
    } else if ($ui->st('o', 'get') == 'dn') {
        $orderby = 'u.`name` DESC,u.`vname` DESC';
    } else if ($ui->st('o', 'get') == 'an') {
        $orderby = 'u.`name` ASC,u.`vname` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = 'd.`dnsID` DESC';
    } else {
        $orderby = 'd.`dnsID` ASC';
        $o = 'ai';
    }
    $query = $sql->prepare("SELECT COUNT(`dnsID`) AS `amount` FROM `voice_dns` WHERE `resellerid`=?");
    $query->execute(array($reseller_id));
    $colcount = $query->fetchColumn();
    if ($start>$colcount) {
        $start = $colcount-$amount;
        if ($start<1) {
            $start = 0;
        }
    }
    $table = array();
    $query = $sql->prepare("SELECT d.*,u.`cname`,u.`name`,u.`vname`,t.`ssh2ip`,t.`description` FROM `voice_dns` d INNER JOIN `userdata` u ON d.`userID`=u.`id` INNER JOIN `voice_tsdns` t ON d.`tsdnsID`=t.`id` WHERE d.`resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array($reseller_id));
    $query2 = $sql->prepare("SELECT `extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='ds' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        if ($row['jobPending'] == 'Y') {
            $jobPending = $gsprache->yes;
            $query2->execute(array($row['dnsID'], $row['resellerID']));
            $json=@json_decode($query2->fetchColumn());
            $tobeActive=(is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
        } else {
            $jobPending = $gsprache->no;
        }
        if (($row['active'] == 'Y' and $row['jobPending'] == 'N') or ($row['jobPending'] == 'Y') and isset($tobeActive) and $tobeActive == 'Y') {
            $imgName = '16_ok';
            $imgAlt='online';
        } else {
            $imgName = '16_bad';
            $imgAlt='inactive';
        }
        $table[] = array('id' => $row['dnsID'], 'active' => $row['active'], 'img' => $imgName,'alt' => $imgAlt,'dns' => $row['dns'], 'address' => $row['ip'] . ':' . $row['port'], 'masterip' => trim($row['ssh2ip'] . ' ' . $row['description']),'cname' => $row['cname'], 'names' => trim($row['name'] . ' ' . $row['vname']),'userid' => $row['userID'], 'jobPending' => $jobPending);
    }
    $next = $start+$amount;
    if ($colcount>$next) {
        $vor = $start+$amount;
    } else {
        $vor = $start;
    }
    $back = $start - $amount;
    if ($back>=0){
        $zur = $start - $amount;
    } else {
        $zur = $start;
    }
    $pageamount = ceil($colcount / $amount);
    $link='<a href="admin.php?w=vr&amp;o='.$o.'&amp;a=';
    if (!isset($amount)) {
        $link .="20";
    } else {
        $link .= $amount;
    }
    if ($start==0) {
        $link .= '&p=0" class="bold">1</a>';
    } else {
        $link .= '&p=0">1</a>';
    }
    $pages[] = $link;
    $i = 2;
    while ($i<=$pageamount) {
        $selectpage = ($i - 1) * $amount;
        if ($start==$selectpage) {
            $pages[] = '<a href="admin.php?w=vr&amp;o='.$o.'&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>';
        } else {
            $pages[] = '<a href="admin.php?w=vr&amp;o='.$o.'&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = 'admin_voice_dns_list.tpl';
}