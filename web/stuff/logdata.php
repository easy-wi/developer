<?php
/**
 * File: logdata.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['log'])) {
	header('Location: admin.php');
	die('No acces');
}
$sprache = getlanguagefile('logs',$user_language,$reseller_id);
$gssprache = getlanguagefile('gserver',$user_language,$reseller_id);
$table = array();
if ($reseller_id==0) {
	$pselect=$sql->prepare("SELECT `userid`,`subuser`,`reseller`,`username`,`usertype`,`useraction`,`ip`,`hostname`,`logdate` FROM `userlog` WHERE `usertype` LIKE :usertype AND (`resellerid`=:reseller_id OR `resellerid`=`userid`) ORDER BY `id` DESC LIMIT $start,$amount");
} else if ($reseller_id != 0 and $admin_id != $reseller_id) {
	$pselect=$sql->prepare("SELECT `userid`,`subuser`,`reseller`,`username`,`usertype`,`useraction`,`ip`,`hostname`,`logdate` FROM `userlog` WHERE `usertype` LIKE :usertype AND `resellerid`=:reseller_id GROUP BY `userid`,`subuser`,`reseller`,`username`,`usertype`,`useraction`,`ip`,`logdate` ORDER BY `id` DESC LIMIT $start,$amount");
} else {
	$pselect=$sql->prepare("SELECT l.`userid`,l.`subuser`,l.`reseller`,l.`username`,l.`usertype`,l.`useraction`,l.`ip`,l.`hostname`,l.`logdate` FROM `userdata` u LEFT JOIN `userlog` l ON u.`id`=l.`resellerid` OR u.`resellerid`=l.`resellerid` WHERE l.`usertype` LIKE :usertype AND u.`resellerid`=:reseller_id GROUP BY l.`userid`,l.`subuser`,l.`reseller`,l.`username`,l.`usertype`,l.`useraction`,l.`ip`,l.`logdate` ORDER BY l.`id` DESC LIMIT $start,$amount");
}
if ($reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}
if (empty($where)) {
	$pselect->execute(array(':usertype'=> "%", ':reseller_id'=> $reseller_id));
} else {
	$pselect->execute(array(':usertype'=> $where, ':reseller_id'=> $reseller_id));
}
foreach ($pselect->fetchall() as $row) {
	$usertype=$row['usertype'];
	$userid=$row['userid'];
	$subuser=$row['subuser'];
	$reseller=$row['reseller'];
	if ($usertype=="admin") {
		if ($subuser==0) {
			$username=$row['username'];
		} else {
            $pselect2=$sql->prepare("SELECT `cname`,`resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
			$pselect2->execute(array($subuser));
			foreach ($pselect2->fetchall() as $row2) {
                $username=$row['username']." <img src=\"images/16_switch.png\" alt=\"switch\" />".$row2['cname'];
			}
		}
		if ($reseller_id==0) {
			$ip=$row['ip'];
            $hostname=$row['hostname'];
		} else if ($reseller_id==$admin_id) {
			if ($subuser==0) {
				$ip=$row['ip'];
                $hostname=$row['hostname'];
			} else {
				$ip = '';
                $hostname = '';
			}
		} else if ($reseller_id != 0 and $reseller_id != $admin_id and $subuser==0) {
			$ip=$row['ip'];
            $hostname=$row['hostname'];
		} else {
			$ip = '';
            $hostname = '';
		}
	} else {
		if ($subuser==0) {
			$username=$row['username'];
		} else {
			$pselect2=$sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? LIMIT 1");
			$pselect2->execute(array($subuser));
			foreach ($pselect2->fetchall() as $row2) {
				$username=$row['username']." <img src=\"images/16_switch\" alt=\"switch\" />".$row2['cname'];
			}
		}
		$ip=$row['ip'];
        $hostname=$row['hostname'];
	}
	$useraction=$row['useraction'];
    $placeholders=array('%%', '%add%', '%dl%', '%del%', '%mod%', '%start%', '%restart%', '%stop%', '%upd%', '%fail%', '%ok%', '%psw%', '%cfg%', '%import%', '%reinstall%', '%backup%', '%use%');
    $replace=array('',$gsprache->add.': ',$gsprache->del.': ',$gsprache->del.': ',$gsprache->mod.': ',$gsprache->start.': ',$gsprache->start.': ',$gsprache->stop.': ',$gsprache->update.': ','','',$gssprache->password.': ',$gssprache->config.': ',$gsprache->import.': ',$gssprache->reinstall.': ',$gsprache->backup,$gsprache->use.': ');
	$replacedpics=str_replace($placeholders,$replace,$useraction);
	$placeholders=array('%modules%', '%voserver%', '%gserver%', '%user%', '%group%', '%fastdl%', '%master%', '%user%', '%root%', '%addon%', '%settings%', '%vserver%', '%ticket_subject%', '%reseller%', '%virtual%', '%eac%', '%resync%', '%virtualimage%', '%template%', '%voserver%', '%emailsettings%', '%dns%', '%tsdns%', '%pmode%', '%comment%');
	$replace=array($gsprache->modules,$gsprache->voiceserver,$gsprache->gameserver,$gsprache->user,$gsprache->groups,$gsprache->fastdownload,$gsprache->master,$gsprache->user,$gsprache->root,$gsprache->addon2,$gsprache->settings,$gsprache->virtual,$gsprache->support,$gsprache->reseller,$gsprache->hostsystem,'Easy Anti Cheat',$gssprache->resync,$gsprache->virtual . ' ' . $gsprache->template,$gsprache->template,$gsprache->voiceserver,'E-Mail '.$gsprache->settings,'TSDNS','TSDNS',$gssprache->protect,$gsprache->comments);
	$replacedwords=str_replace($placeholders,$replace,$replacedpics);
	$logdate=explode(' ', $row['logdate']);
	$table[]=array('logday' => $logdate[0],'loghour' => $logdate[1],'ip' => $ip,'hostname' => $hostname,'username' => $username,'useraction' => $replacedwords);
}
$next=$start+$amount;
if ($reseller_id==0) {
	$countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userlog` WHERE `usertype` LIKE :usertype AND (`resellerid`=:reseller_id OR `resellerid`=`userid`)");
	$requestid=$reseller_id;
} else if ($reseller_id != 0 and $admin_id != $reseller_id) {
	$countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userlog` WHERE `usertype` LIKE :usertype AND `resellerid`=:reseller_id");
	$requestid=$admin_id;
} else {
	$countp=$sql->prepare("SELECT COUNT(l.`id`) AS `amount` FROM `userdata` u LEFT JOIN `userlog` l ON u.`id`=l.`resellerid` OR u.`resellerid`=l.`resellerid` WHERE l.`usertype` LIKE :usertype AND u.`resellerid`=:reseller_id GROUP BY l.`userid`,l.`subuser`,l.`reseller`,l.`username`,l.`usertype`,l.`useraction`,l.`ip`,l.`logdate`");
	$requestid=$reseller_id;
}
if (empty($where)) {
	$countp->execute(array(':usertype'=> "%", ':reseller_id'=> $requestid));
} else {
	$countp->execute(array(':usertype'=> $where, ':reseller_id'=> $requestid));
}
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
$link='<a href="admin.php?w=lo&amp;a=';
if(!isset($amount)) {
    $link .="20";
} else {
    $link .=$amount;
}
if ($start==0) {
    $link .='&amp;p=0" class="bold">1</a>';
} else {
    $link .='&amp;p=0">1</a>';
}
$pages[] = $link;
$i = 2;
while ($i<=$pageamount) {
    $selectpage = ($i - 1) * $amount;
    if ($start==$selectpage) {
        $pages[] = '<a href="admin.php?w=lo&amp;a='.$amount.'&p='.$selectpage.'" class="bold">'.$i.'</a>';
    } else {
        $pages[] = '<a href="admin.php?w=lo&amp;a='.$amount.'&p='.$selectpage.'">'.$i.'</a>';
    }
    $i++;
}
$pages=implode(',',$pages);
$template_file = "admin_logs.tpl";
?>