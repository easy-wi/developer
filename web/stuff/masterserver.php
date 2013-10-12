<?php
/**
 * File: masterserver.php.
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

if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['masterServer'])) {
	header('Location: admin.php');
	die('No acces');
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/class_masterserver.php');

$sprache = getlanguagefile('roots',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id==0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
	$logreseller = 0;
}
if ($reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('d','get') == 'ad') {
    if ($ui->smallletters('action',2,'post') == 'ad'){
        include(EASYWIDIR . '/stuff/ssh_exec.php');
        $serverid=$ui->id('id', 10, 'get');
        $rootServer=new masterServer($serverid,$aeskey);
        if($ui->id('id',19,'post')) {
            $template_file = '';
            $query = $sql->prepare("SELECT `id` FROM `rservermasterg` WHERE `serverid`=? AND `servertypeid`=? AND `resellerid`=?");
            $query2 = $sql->prepare("SELECT * FROM `servertypes` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query3 = $sql->prepare("INSERT INTO rservermasterg (`serverid`,`servertypeid`,`installing`,`installstarted`,`resellerid`) VALUES (?,?,'Y',NOW(),?)");
            foreach($ui->id('id',19,'post') as $id) {
                $query->execute(array($serverid,$id,$reseller_id));
                if ($query->rowcount()==0) {
                    $query2->execute(array($id,$reseller_id));
                    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                        $description=$row2['description'];
                        $shorten=$row2['shorten'];
                    }
                    $query3->execute(array($serverid,$id,$reseller_id));
                    $template_file .="<b>$description</b> ".$sprache->root_masterinstall;
                    $loguseraction="%add% %master% $shorten";
                    $insertlog->execute();
                }
                $rootServer->collectData($id,true);
            }
            $sshcmd=$rootServer->returnCmds('install','all');
            if ($rootServer->sshcmd!==null) ssh2_execute('gs',$serverid,$rootServer->sshcmd);
        } else {
            $template_file = $sprache->error_root_noselect;
        }
    } else {
        $id=$ui->id('id',19,'get');
        $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $ip=$query->fetchColumn();
        $query = $sql->prepare("SELECT `id`,`shorten`,`steamgame`,`description`,`type` FROM `servertypes` WHERE `resellerid`=? ORDER BY `description`");
        $query->execute(array($reseller_id));
        $table = array();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $gameid=$row['id'];
            $shorten=$row['shorten'];
            $description=$row['description'];
            $type=$row['type'];
            $query = $sql->prepare("SELECT r.`id` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`serverid`=? AND r.`resellerid`=? AND s.`shorten`=?");
            $query->execute(array($id,$reseller_id,$shorten));
            if ($query->rowCount()<1) {
                $table[] = array('id' => $gameid,'shorten' => $shorten,'description' => $description);
            }
        }
        $template_file = "admin_master_add.tpl";
    }
} else if ($ui->st('d','get') == 'dl' and $ui->id('id',19,'get')) {
    if ($ui->smallletters('action',2,'post') == 'dl'){
        include(EASYWIDIR . '/stuff/ssh_exec.php');
        $serverid=$ui->id('id',19,'get');
        $rdata=serverdata('root',$serverid,$aeskey);
        $sship=$rdata['ip'];
        $sshport=$rdata['port'];
        $sshuser=$rdata['user'];
        $sshpass=$rdata['pass'];
        if($ui->id('id',30,'post')) {
            $template_file = '';
            $deletestring = '';
            $i = 0;
            foreach($ui->id('id',30,'post') as $id) {
                $query = $sql->prepare("SELECT s.`shorten` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`id`=? AND r.`resellerid`=? LIMIT 1");
                $query->execute(array($id,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $shorten=$row['shorten'];
                    $deletestring .="_".$shorten;
                }
                $query = $sql->prepare("DELETE FROM `rservermasterg` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id,$reseller_id));
                $template_file .=$spracheResponse->table_del.": $shorten<br />";
                $loguseraction="%del% %master% $sship $shorten";
                $insertlog->execute();
                $i++;
            }
            $deletestring=$i.$deletestring;
            if (ssh2_execute('gs',$serverid,"./control.sh delete $deletestring")) {
                $template_file .=$sprache->root_masterdel;
            } else {
                $template_file .=$sprache->error_root_masterdel2;
            }
        } else {
            $template_file = $sprache->error_root_noselect;
        }
    } else {
        $id=$ui->id('id',19,'get');
        $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $ip=$query->fetchColumn();
        $table = array();
        $query = $sql->prepare("SELECT r.`id`,s.`shorten`,s.`description` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`serverid`=? AND r.`resellerid`=? ORDER BY `description`");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[] = array('id' => $row['id'], 'shorten' => $row['shorten'], 'description' => $row['description']);
        }
        if (count($table)>0) {
            $template_file = "admin_master_dl.tpl";
        } else {
            $template_file = "Error: No such ID!";
        }
    }
} else if ($ui->st('d','get') == 'md'){
    include(EASYWIDIR . '/stuff/ssh_exec.php');
    $o = $ui->st('o','get');
    if ($ui->st('o','get') == 'ar') {
        $orderby = '`resellerid` ASC';
    } else if ($ui->st('o','get') == 'dr') {
        $orderby = '`resellerid` DESC';
    } else if ($ui->st('o','get') == 'ap') {
        $orderby = '`ip` ASC';
    } else if ($ui->st('o','get') == 'dp') {
        $orderby = '`ip` DESC';
    } else if ($ui->st('o','get') == 'as') {
        $orderby = '`active` ASC';
    } else if ($ui->st('o','get') == 'ds') {
        $orderby = '`active` DESC';
    } else if ($ui->st('o','get') == 'ad') {
        $orderby = '`description` ASC';
    } else if ($ui->st('o','get') == 'dd') {
        $orderby = '`description` DESC';
    } else if ($ui->st('o','get') == 'di') {
        $orderby = '`id` DESC';
    } else {
        $orderby = '`id` ASC';
        $o = 'ai';
    }
    $query = $sql->prepare("SELECT `id`,`ip`,`os`,`bitversion`,`description`,`active` FROM `rserverdata` WHERE `active`='Y' AND `resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array($reseller_id));
    $table = array();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $id=$row['id'];
        if ($row['active'] == 'Y') {
            $imgName='16_ok';
            $imgAlt='Active';
        } else {
            $imgName='16_bad';
            $imgAlt='Inactive';
        }
        $statusList = array();
        $sshcheck = array();
        $description=$row['description'];
        $pselect2=$sql->prepare("SELECT s.`shorten`,r.`installing`,r.`updating`,r.`installstarted` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`serverid`=? AND r.`resellerid`=? GROUP BY s.`shorten`");
        $pselect2->execute(array($id,$reseller_id));
        foreach ($pselect2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $shorten=$row2['shorten'];
            if ($row['active'] == 'N' or ($row2['installing'] == 'N' and $row2['updating'] == 'N')) {
                $statusList[$row2['shorten']] = '16_ok';
            } else {
                $toolong=date($row2['installstarted'],strtotime("+15 minutes"));
                if (strtotime($logdate)>strtotime($toolong) or $row2['updating'] == 'Y') {
                    $sshcheck[] = $row2['shorten'];
                } else {
                    $statusList[$row2['shorten']] = '16_installing';
                }
            }
        }
        if (count($sshcheck)>0) {
            $serverdata=serverdata('root',$id,$aeskey);
            $ip=$serverdata['ip'];
            $user=$serverdata['user'];
            $port=$serverdata['port'];
            $pass=$serverdata['pass'];
            $check=ssh2_execute('gs',$id,'./control.sh updatestatus "'.implode(' ',$sshcheck).'"');
            if ($check === false) {
                $description="The login data does not work";
            } else if (preg_match('/^[\w\:\-\=]+$/',$check)) {
                $games = array();
                $query2 = $sql->prepare("SELECT r.`id`,s.`steamgame`,s.`updates`,d.`updates` AS `rupdates` FROM `rservermasterg` r INNER JOIN `rserverdata` d ON r.`serverid`=d.`id` INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE s.`shorten`=? AND r.`resellerid`=? AND d.`ip`=? LIMIT 1");
                $query3 = $sql->prepare("UPDATE `rservermasterg` SET `installing`='N',`updating`='N' WHERE `id`=?");
                foreach (preg_split('/\:/',$check,-1,PREG_SPLIT_NO_EMPTY) as $status) {
                    $ex=explode('=',$status);
                    if (isset($ex[1])) {
                        $games[$ex[0]] = $ex[1];
                    }
                }
                foreach ($games as $k=>$v) {
                    if (!in_array($k, array('steamcmd','sync'))) {
                        $query2->execute(array($k,$reseller_id,$ip));
                        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                            if (($v==0 and $row2['rupdates']!=4 and $row2['updates']!=4 and $row2['steamgame'] != 'S') or ($row2['steamgame'] == 'S' and (!isset($games['steamcmd']) or $games['steamcmd']==0)) or (($row2['rupdates']==4 or $row2['updates']==4) and (!isset($games['sync']) or $games['sync']==0))) {
                                $statusList[$k] = '16_ok';
                                $query3->execute(array($row2['id']));
                                unset($sshcheck[array_search($k,$sshcheck)]);
                            }
                        }
                    }
                }
            }
            foreach ($sshcheck as $shorten) {
                $statusList[$shorten] = '16_installing';
            }
        }
        $table[] = array('id' => $row['id'], 'img' => $imgName,'alt' => $imgAlt,'ip' => $row['ip'], 'os' => $row['os'], 'bit' => $row['bitversion'], 'description' => $description,'statusList' => $statusList,'active' => $row['active']);
    }
    $next=$start+$amount;
    $countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `rserverdata` WHERE `resellerid`=?");
    $countp->execute(array($reseller_id));
    foreach ($countp->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $colcount=$row['amount'];
    }
    if ($colcount>$next) {
        $vor=$start+$amount;
    } else {
        $vor=$start;
    }
    $back=$start - $amount;
    if ($back>=0){
        $zur=$start - $amount;
    } else {
        $zur=$start;
    }
    $pageamount = ceil($colcount / $amount);
    $link='<a href="admin.php?w=ma&amp;d=md&amp;a=';
    if(!isset($amount)) {
        $link .="20";
    } else {
        $link .=$amount;
    }
    if ($start==0) {
        $link .='&p=0" class="bold">1</a>';
    } else {
        $link .='&p=0">1</a>';
    }
    $pages[] = $link;
    $i = 2;
    while ($i<=$pageamount) {
        $selectpage = ($i - 1) * $amount;
        if ($start==$selectpage) {
            $pages[] = '<a href="admin.php?w=ma&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>';
        } else {
            $pages[] = '<a href="admin.php?w=ma&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = "admin_master_list.tpl";
} else if ($ui->st('d','get') == 'ud' and $ui->smallletters('action',2,'post') == 'ud'){
    if (is_object($ui->id('id',19,'post')) or is_array($ui->id('id',19,'post'))) {
        foreach($ui->id('id',19,'post') as $id) {
            $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $ips[$id] = $row['ip'];
            }
        }
    }
    if (isset($ips)) {
        $template_file = "admin_master_ud2.tpl";
    } else {
        $template_file = "Error: No server selected or the server(s) are already updating";
    }
} else {
    $query = $sql->prepare("SELECT s.`description`,s.`shorten` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` $where GROUP BY s.`description` ORDER BY s.`description` ASC");
    $query->execute(array(':reseller_id' => $reseller_id));
    $table = array();
    $i = 0;
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[$i]['game'] = array('shorten' => $row['shorten'], 'description' => $row['description']);
        $i++;
    }
    $query = $sql->prepare("SELECT d.`id`,d.`ip` FROM `rservermasterg` r INNER JOIN `rserverdata` d ON r.`serverid`=d.`id` INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` $where GROUP BY d.`id` ASC");
    $query->execute(array(':reseller_id' => $reseller_id));
    $i2 = 0;
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[$i2]['server'] = array('id' => $row['id'], 'ip' => $row['ip']);
        $i2++;
    }
    $query5 = $sql->prepare("SELECT s.`shorten` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`resellerid`=? GROUP BY s.`shorten` ORDER BY s.`shorten` ASC");
    $query5->execute(array($reseller_id));
    $table3 = array();
    foreach ($query5->fetchAll(PDO::FETCH_ASSOC) as $row5) {
        $shorten=$row5['shorten'];
        $table3[] = '<a href="admin.php?w=ma&amp;d=ud&amp;m='.$shorten.'">'.$shorten.'</a>';
    }
    $query6 = $sql->prepare("SELECT s.`qstat`,q.`description` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` INNER JOIN `qstatshorten` q ON s.`qstat`=q.`qstat` WHERE r.`resellerid`=? GROUP BY s.`qstat` ORDER BY s.`qstat` ASC");
    $query6->execute(array($reseller_id));
    $table4 = array();
    foreach ($query6->fetchAll(PDO::FETCH_ASSOC) as $row6) {
        $shorten=$row6['qstat'];
        $type=$row6['description'];
        $table4[] = '<a href="admin.php?w=ma&amp;d=ud&amp;m='.$shorten.'">'.$type.'</a>';
    }
    $template_file = "admin_master_ud.tpl";
}