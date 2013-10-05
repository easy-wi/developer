<?php
/**
 * File: maillog.php.
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
if (isset($action) and $action == 'dl' and $ui->id('id',30,'post')) {
	$i = 0;
    if ($ui->id('id',30,'post')) {
        $delete=$sql->prepare("DELETE FROM `mail_log` WHERE `id`=? LIMIT 1");
		foreach ($ui->id('id',30,'post') as $id) {
			$delete->execute(array($id));
			$i++;
		}
	}
	$template_file = $i." logs deleted";
} else {
	$table = array();
    $o = $ui->st('o','get');
	if ($ui->st('o','get') == 'du') {
		$orderby = 'u.`cname` DESC';
	} else if ($ui->st('o','get') == 'au') {
		$orderby = 'u.`cname` ASC';
	} else if ($ui->st('o','get') == 'dt') {
		$orderby = 'l.`topic` DESC';
	} else if ($ui->st('o','get') == 'at') {
		$orderby = 'l.`topic` ASC';
	} else if ($ui->st('o','get') == 'ad') {
		$orderby = 'l.`id` ASC';
	} else {
		$o = 'dd';
		$orderby = 'l.`id` DESC';
	}
	if ($reseller_id==0) {
		$pselect=$sql->prepare("SELECT l.`id`,l.`uid`,l.`topic`,l.`date`,u.`cname`,u.`accounttype`,u.`mail` FROM `mail_log` l LEFT JOIN `userdata` u ON l.`uid`=u.`id` ORDER BY $orderby LIMIT $start,$amount");
	} else if ($reseller_id != 0 and $admin_id != $reseller_id) {
		$pselect=$sql->prepare("SELECT l.`id`,l.`uid`,l.`topic`,l.`date`,u.`cname`,u.`accounttype`,u.`mail` FROM `mail_log` l LEFT JOIN `userdata` u ON l.`uid`=u.`id` WHERE l.`resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
	} else {
		$pselect=$sql->prepare("SELECT l.`id`,l.`uid`,l.`topic`,l.`date`,u.`cname`,u.`accounttype`,u.`mail` FROM `userdata` u LEFT JOIN `mail_log` l ON u.`id`=l.`resellerid` OR u.`resellerid`=l.`resellerid` WHERE u.`resellerid`=? GROUP BY l.`date` ORDER BY $orderby LIMIT $start,$amount");
	}
	if ($reseller_id==0) {
		$pselect->execute();
	} else {
		if ($reseller_id != 0 and $admin_id != $reseller_id) {
			$reseller_id = $admin_id;
		}
		$pselect->execute(array($reseller_id));
	}
	foreach ($pselect->fetchall() as $row) {
		$userid=$row['uid'];
		if ($userid != $admin_id) {
			$username='<a href="switch.php?id='.$userid.'">'.$row['cname'].'</a> ('.$row['mail'].')';
		} else {
			$username=$row['cname'].' ('.$row['mail'].')';
		}
		$logdate=explode(' ', $row['date']);
		if (isset($row['id']) and isid($row['id'],'30') and isset($logdate[1])) {
            $table[]=array('id' => $row['id'],'logday' => $logdate[0],'loghour' => $logdate[1],'username' => $username,'topic' => $row['topic']);
		}
	}
	$next=$start+$amount;
	if ($reseller_id==0) {
		$countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `mail_log`");
		$countp->execute();
	} else {
		if ($reseller_id != 0 and $admin_id != $reseller_id) {
			$countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `mail_log` WHERE `resellerid`=?");
		} else {
			$countp=$sql->prepare("SELECT COUNT(l.`id`) AS `amount` FROM `userdata` u LEFT JOIN `mail_log` l ON u.`id`=l.`resellerid` OR u.`resellerid`=l.`resellerid` WHERE u.`resellerid`=? GROUP BY l.`date`");
		}
		$countp->execute(array($reseller_id));
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
    $link='<a href="admin.php?w=ml&amp;d='.$d.'&amp;a=';
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
            $pages[] = '<a href="admin.php?w=ml&amp;d='.$d.'&amp;a='.$amount.'&amp;p='.$selectpage.'" class="bold">'.$i.'</a>';
        } else {
            $pages[] = '<a href="admin.php?w=ml&amp;d='.$d.'&amp;a='.$amount.'&amp;p='.$selectpage.'">'.$i.'</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
	$template_file = "admin_logs_mail.tpl";
}