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
$sprache = getlanguagefile('logs', $user_language,$reseller_id);
$gssprache = getlanguagefile('gserver', $user_language,$reseller_id);

$table = array();
if (!isset($amount)) {
    $amount = 20;
}
if (!isset($start)) {
    $start = 0;
}

if ($reseller_id==0) {
	$query = $sql->prepare("SELECT `userid`,`subuser`,`reseller`,`username`,`usertype`,`useraction`,`ip`,`hostname`,`logdate` FROM `userlog` WHERE `usertype` LIKE :usertype AND (`resellerid`=:reseller_id OR `resellerid`=`userid`) ORDER BY `id` DESC LIMIT $start,$amount");
} else if ($reseller_id != 0 and $admin_id != $reseller_id) {
    $query = $sql->prepare("SELECT `userid`,`subuser`,`reseller`,`username`,`usertype`,`useraction`,`ip`,`hostname`,`logdate` FROM `userlog` WHERE `usertype` LIKE :usertype AND `resellerid`=:reseller_id GROUP BY `userid`,`subuser`,`reseller`,`username`,`usertype`,`useraction`,`ip`,`logdate` ORDER BY `id` DESC LIMIT $start,$amount");
} else {
    $query = $sql->prepare("SELECT l.`userid`,l.`subuser`,l.`reseller`,l.`username`,l.`usertype`,l.`useraction`,l.`ip`,l.`hostname`,l.`logdate` FROM `userdata` u LEFT JOIN `userlog` l ON u.`id`=l.`resellerid` OR u.`resellerid`=l.`resellerid` WHERE l.`usertype` LIKE :usertype AND u.`resellerid`=:reseller_id GROUP BY l.`userid`,l.`subuser`,l.`reseller`,l.`username`,l.`usertype`,l.`useraction`,l.`ip`,l.`logdate` ORDER BY l.`id` DESC LIMIT $start,$amount");
}
$query2 = $sql->prepare("SELECT `cname`,`resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");

if ($reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}

if (empty($where)) {
    $query->execute(array(':usertype' => "%", ':reseller_id' => $reseller_id));
} else {
    $query->execute(array(':usertype' => $where, ':reseller_id' => $reseller_id));
}

foreach ($query->fetchall() as $row) {
	$usertype = $row['usertype'];
	$userid = $row['userid'];
	$subuser = $row['subuser'];
	$reseller = $row['reseller'];

	if ($usertype == 'admin') {

		if ($subuser==0) {
			$username = $row['username'];

		} else {
            $query2->execute(array($subuser));
            $username = $row['username'] . ' &harr; ' . $query2->fetchColumn();
		}

		if ($reseller_id==0) {
			$ip = $row['ip'];
            $hostname = $row['hostname'];

		} else if ($reseller_id==$admin_id) {

			if ($subuser==0) {
				$ip = $row['ip'];
                $hostname = $row['hostname'];

			} else {
				$ip = '';
                $hostname = '';
			}

		} else if ($reseller_id != 0 and $reseller_id != $admin_id and $subuser==0) {
			$ip = $row['ip'];
            $hostname = $row['hostname'];

		} else {
			$ip = '';
            $hostname = '';
		}

	} else {
        $ip = $row['ip'];
        $hostname = $row['hostname'];

		if ($subuser == 0) {
			$username = $row['username'];

		} else {
            $query2->execute(array($subuser));
            $username = $row['username'] . ' &harr; ' . $query2->fetchColumn();
		}
	}

	$useraction=$row['useraction'];
    $placeholders=array('%%', '%add%', '%dl%', '%del%', '%mod%', '%start%', '%restart%', '%stop%', '%upd%', '%fail%', '%ok%', '%psw%', '%cfg%', '%import%', '%reinstall%', '%backup%', '%use%');
    $replace=array('', $gsprache->add.': ', $gsprache->del.': ', $gsprache->del.': ', $gsprache->mod.': ', $gsprache->start.': ', $gsprache->start.': ', $gsprache->stop.': ', $gsprache->update.': ','','', $gssprache->password.': ', $gssprache->config.': ', $gsprache->import.': ', $gssprache->reinstall.': ', $gsprache->backup, $gsprache->use.': ');
	$replacedpics = str_replace($placeholders,$replace,$useraction);

	$placeholders=array('%modules%', '%voserver%', '%gserver%', '%user%', '%group%', '%fastdl%', '%master%', '%user%', '%root%', '%addon%', '%settings%', '%vserver%', '%ticket_subject%', '%reseller%', '%virtual%', '%eac%', '%resync%', '%virtualimage%', '%template%', '%voserver%', '%emailsettings%', '%dns%', '%tsdns%', '%pmode%', '%comment%');
	$replace=array($gsprache->modules, $gsprache->voiceserver, $gsprache->gameserver, $gsprache->user, $gsprache->groups, $gsprache->fastdownload, $gsprache->master, $gsprache->user, $gsprache->root, $gsprache->addon2, $gsprache->settings, $gsprache->virtual, $gsprache->support, $gsprache->reseller, $gsprache->hostsystem, 'Easy Anti Cheat', $gssprache->resync, $gsprache->virtual . ' ' . $gsprache->template, $gsprache->template, $gsprache->voiceserver, 'E-Mail '.$gsprache->settings, 'TSDNS', 'TSDNS', $gssprache->protect, $gsprache->comments);
	$replacedwords=str_replace($placeholders,$replace,$replacedpics);

	$logdate=explode(' ', $row['logdate']);
	$table[] = array('logday' => $logdate[0], 'loghour' => $logdate[1], 'ip' => $ip,'hostname' => $hostname, 'username' => $username, 'useraction' => $replacedwords);
}
$next = $start + $amount;

if ($reseller_id==0) {
	$query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userlog` WHERE `usertype` LIKE :usertype AND (`resellerid`=:reseller_id OR `resellerid`=`userid`)");
	$requestid = $reseller_id;
} else if ($reseller_id != 0 and $admin_id != $reseller_id) {
    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userlog` WHERE `usertype` LIKE :usertype AND `resellerid`=:reseller_id");
	$requestid = $admin_id;
} else {
    $query = $sql->prepare("SELECT COUNT(l.`id`) AS `amount` FROM `userdata` u LEFT JOIN `userlog` l ON u.`id`=l.`resellerid` OR u.`resellerid`=l.`resellerid` WHERE l.`usertype` LIKE :usertype AND u.`resellerid`=:reseller_id GROUP BY l.`userid`,l.`subuser`,l.`reseller`,l.`username`,l.`usertype`,l.`useraction`,l.`ip`,l.`logdate`");
	$requestid = $reseller_id;
}

if (empty($where)) {
    $query->execute(array(':usertype' => "%", ':reseller_id' => $requestid));
} else {
    $query->execute(array(':usertype' => $where, ':reseller_id' => $requestid));
}

$colcount = $query->fetchColumn();

$vor = ($colcount>$next) ? $start + $amount : $start;
$back = $start - $amount;
$zur =  ($back >= 0) ? $start - $amount : $start;
$pageamount = ceil($colcount / $amount);

$link = '<a href="admin.php?w=lo&amp;a=' . $amount;
$link .= ($start == 0) ? '&amp;p=0" class="bold">1</a>' : '&amp;p=0">1</a>';

$pages[] = $link;
$i = 2;
while ($i <= $pageamount) {
    $selectpage = ($i - 1) * $amount;
    $pages[] = ($start == $selectpage) ? '<a href="admin.php?w=lo&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=lo&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
    $i++;
}
$pages = implode(',', $pages);
$template_file = 'admin_logs.tpl';