<?php
/**
 * File: voice_usage.php.
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

if(!isset($admin_id) or $main!=1 or (isset($admin_id) and !$pa['voiceserver'] and !$pa['voiceserverSettings'] and !$pa['voiceserverStats'])) {
    header('Location: admin.php');
    die;
}
$sprache = getlanguagefile('traffic',$user_language,$reseller_id);
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($d== 'se' and $pa['voiceserverSettings']) {
	if (isset($ui->post['text_colour_1']) and $ui->w('action', 4, 'post') == 'md') {
		$error = 0;
		if (!validate_int($ui->post['text_colour_1'], 0 , 255) and $ui->post['text_colour_1'] != 0) $error = 1;
		if (!validate_int($ui->post['text_colour_2'], 0 , 255) and $ui->post['text_colour_2'] != 0) $error = 1;
		if (!validate_int($ui->post['text_colour_3'], 0 , 255) and $ui->post['text_colour_3'] != 0) $error = 1;
		if (!validate_int($ui->post['barin_colour_1'], 0 , 255) and $ui->post['barin_colour_1'] != 0) $error = 1;
		if (!validate_int($ui->post['barin_colour_2'], 0 , 255) and $ui->post['barin_colour_2'] != 0) $error = 1;
		if (!validate_int($ui->post['barin_colour_3'], 0 , 255) and $ui->post['barin_colour_3'] != 0) $error = 1;
		if (!validate_int($ui->post['barout_colour_1'], 0 , 255) and $ui->post['barout_colour_1'] != 0) $error = 1;
		if (!validate_int($ui->post['barout_colour_2'], 0 , 255) and $ui->post['barout_colour_2'] != 0) $error = 1;
		if (!validate_int($ui->post['barout_colour_3'], 0 , 255) and $ui->post['barout_colour_3'] != 0) $error = 1;
		if (!validate_int($ui->post['bg_colour_1'], 0 , 255) and $ui->post['bg_colour_1'] != 0) $error = 1;
		if (!validate_int($ui->post['bg_colour_2'], 0 , 255) and $ui->post['bg_colour_2'] != 0) $error = 1;
		if (!validate_int($ui->post['bg_colour_3'], 0 , 255) and $ui->post['bg_colour_3'] != 0) $error = 1;
		if (!validate_int($ui->post['border_colour_1'], 0 , 255) and $ui->post['border_colour_1'] != 0) $error = 1;
		if (!validate_int($ui->post['border_colour_2'], 0 , 255) and $ui->post['border_colour_2'] != 0) $error = 1;
		if (!validate_int($ui->post['border_colour_3'], 0 , 255) and $ui->post['border_colour_3'] != 0) $error = 1;
		if (!validate_int($ui->post['line_colour_1'], 0 , 255) and $ui->post['line_colour_1'] != 0) $error = 1;
		if (!validate_int($ui->post['line_colour_2'], 0 , 255) and $ui->post['line_colour_2'] != 0) $error = 1;
		if (!validate_int($ui->post['line_colour_3'], 0 , 255) and $ui->post['line_colour_3'] != 0) $error = 1;
		if ($error==0) {
            $query = $sql->prepare("UPDATE `voice_stats_settings` SET `text_colour_1`=?,`text_colour_2`=?,`text_colour_3`=?,`barin_colour_1`=?,`barin_colour_2`=?,`barin_colour_3`=?,`barout_colour_1`=?,`barout_colour_2`=?,`barout_colour_3`=?,`bg_colour_1`=?,`bg_colour_2`=?,`bg_colour_3`=?,`border_colour_1`=?,`border_colour_2`=?,`border_colour_3`=?,`line_colour_1`=?,`line_colour_2`=?,`line_colour_3`=? WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($ui->post['text_colour_1'],$ui->post['text_colour_2'],$ui->post['text_colour_3'],$ui->post['barin_colour_1'],$ui->post['barin_colour_2'],$ui->post['barin_colour_3'],$ui->post['barout_colour_1'],$ui->post['barout_colour_2'],$ui->post['barout_colour_3'],$ui->post['bg_colour_1'],$ui->post['bg_colour_2'],$ui->post['bg_colour_3'],$ui->post['border_colour_1'],$ui->post['border_colour_2'],$ui->post['border_colour_3'],$ui->post['line_colour_1'],$ui->post['line_colour_2'],$ui->post['line_colour_3'],$reseller_id));
			$template_file = $spracheResponse->table_add;
		} else {
			$template_file = 'Error';
		}
	} else {
		$query = $sql->prepare("SELECT * FROM `voice_stats_settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
		foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
			$text_colour_1=$row['text_colour_1'];
			$text_colour_2=$row['text_colour_2'];
			$text_colour_3=$row['text_colour_3'];
			$barin_colour_1=$row['barin_colour_1'];
			$barin_colour_2=$row['barin_colour_2'];
			$barin_colour_3=$row['barin_colour_3'];
			$barout_colour_1=$row['barout_colour_1'];
			$barout_colour_2=$row['barout_colour_2'];
			$barout_colour_3=$row['barout_colour_3'];
			$bg_colour_1=$row['bg_colour_1'];
			$bg_colour_2=$row['bg_colour_2'];
			$bg_colour_3=$row['bg_colour_3'];
			$border_colour_1=$row['border_colour_1'];
			$border_colour_2=$row['border_colour_2'];
			$border_colour_3=$row['border_colour_3'];
			$line_colour_1=$row['line_colour_1'];
			$line_colour_2=$row['line_colour_2'];
			$line_colour_3=$row['line_colour_3'];
		}
		$template_file = 'admin_voice_stats_settings.tpl';
	}
} else if ($pa['voiceserverStats']) {
	$display=$sprache->total;
	$data = array();
	if (!$ui->st('kind','post') or $ui->st('kind','post') == 'al') {
		$kind='al';
		$whichdata = '';
	} else if (isid($ui->post['what'],30) and $ui->st('kind','post') == 'us') {
		$kind='us';
		$whichdata="&amp;distro=".$ui->post['what'];
		$extra=$sprache->user;
		$pselect=$sql->prepare("SELECT u.`id`,u.`cname`,u.`vname`,u.`name` FROM `userdata` u INNER JOIN `voice_server` v ON u.`id`=v.`userid` AND v.`active`='Y' WHERE u.`resellerid`=? GROUP BY u.`id`");
		$pselect->execute(array($reseller_id));
		foreach ($pselect->fetchall(PDO::FETCH_ASSOC) as $row) {
			if ($ui->post['what'] == $row['id']) {
				$data[] = '<option value='.$row['id'].' selected="selected">'.trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']).'</option>';
			} else {
				$data[] = '<option value='.$row['id'].'>'.trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']).'</option>';
			}
		}
		$pselect=$sql->prepare("SELECT `cname` FROM `userdata` WHERE `accounttype`='r' AND `id`=? AND `resellerid`=? LIMIT 1");
		$pselect->execute(array($ui->post['what'],$reseller_id));
		foreach ($pselect->fetchall(PDO::FETCH_ASSOC) as $row) {
			$display=$extra . '  ' . $row['cname'];
		}
	} else if (isid($ui->post['what'], '30') and $ui->st('kind','post')=="se") {
		$kind='se';
		$whichdata="&amp;shorten=".$ui->post['what'];
		$pselect=$sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,v.`dns`,m.`usedns` FROM `voice_server` v INNER JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`id`=? AND v.`resellerid`=? LIMIT 1");
		$pselect->execute(array($ui->post['what'],$reseller_id));
		foreach ($pselect->fetchall(PDO::FETCH_ASSOC) as $row) {
			$display=$sprache->server . '  ' . $row['ip'] . ':' . $row['port'];
		}
		$pselect=$sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,v.`dns`,m.`usedns` FROM `voice_server` v INNER JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`resellerid`=? ORDER BY v.`ip`,v.`port`");
		$pselect->execute(array($reseller_id));
		foreach ($pselect->fetchall(PDO::FETCH_ASSOC) as $row) {
            $server=$row['ip'] . ':' . $row['port'];
			if ($ui->post['what'] == $row['id']) {
				$data[] = '<option value='.$row['id'].' selected="selected">'.$server.'</option>';
			} else {
				$data[] = '<option value='.$row['id'].'>'.$server.'</option>';
			}
		}
	} else if (isid($ui->post['what'], '30') and $ui->st('kind','post') == 'ma') {
		$kind='ma';
		$whichdata="&amp;short=".$ui->post['what'];
		$pselect=$sql->prepare("SELECT `ssh2ip` FROM `voice_masterserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
		$pselect->execute(array($ui->post['what'],$reseller_id));
		foreach ($pselect->fetchall(PDO::FETCH_ASSOC) as $row) {
			$display=$sprache->server . '  ' . $row['ssh2ip'];
		}
		$pselect=$sql->prepare("SELECT `id`,`ssh2ip` FROM `voice_masterserver` WHERE `resellerid`=? ORDER BY `ssh2ip`");
		$pselect->execute(array($reseller_id));
		foreach ($pselect->fetchall(PDO::FETCH_ASSOC) as $row) {
			if ($ui->post['what'] == $row['id']) {
				$data[] = '<option value='.$row['id'].' selected="selected">'.$row['ssh2ip'].'</option>';
			} else {
				$data[] = '<option value='.$row['id'].'>'.$row['ssh2ip'].'</option>';
			}
		}
	}
	if (!isset($ui->post['dmy'])) {
		$dmy='da';
		$year=date('Y',strtotime("-6 days"));
		$month=date('m',strtotime("-6 days"));
		$day=date('d',strtotime("-6 days"));
		$yearstop=date('Y');
		$monthstop=date('m');
		$daystop=date('d');
		$amount=7;
	} else if ($ui->post['dmy'] == 'to') {
		$dmy='to';
		if (validate_int($ui->post['daystart'],1,31)) {
			$day=$ui->post['daystart'];
		} else {
			$day=date('d');
		}
		if (validate_int($ui->post['daystop'],1,31)) {
			$daystop=$ui->post['daystop'];
		} else {
			$day=date('d');
		}
		if (validate_int($ui->post['monthstart'],1,12)) {
			$month=$ui->post['monthstart'];
		} else {
			$month=date('m');
		}
		if (validate_int($ui->post['monthstop'],1,12)) {
			$monthstop=$ui->post['monthstop'];
		} else {
			$monthstop=date('m');
		}
		if (validate_int($ui->post['yearstart'],2000,date('Y'))) {
			$year=$ui->post['yearstart'];
		} else {
			$year=date('Y');
		}
		if (validate_int($ui->post['yearstop'],2000,date('Y'))) {
			$yearstop=$ui->post['yearstop'];
		} else {
			$yearstop=date('Y');
		}
		$now=date('Y-m-d');
		$date1=strtotime("$year-$month-$day");
		$date2=strtotime("$yearstop-$monthstop-$daystop");
		$amount=intval(($date2-$date1)/86400)+1;
		if ($amount<0 and "$yearstop-$monthstop-$daystop">$now){
			$yearstop=date('Y');
			$monthstop=date('m');
			$daystop=date('d');
			$day=date('d');
			$month=date('m');
			$year=date('Y');
			$amount = 1;
		}
	} else if ($ui->post['dmy'] == 'da') {
		$dmy='da';
		if (validate_int($ui->post['daystart'],1,31)) {
			$day=$ui->post['daystart'];
		} else {
			$day=date('d',strtotime("-6 days"));
		}
		if (validate_int($ui->post['daystop'],1,31)) {
			$daystop=$ui->post['daystop'];
		} else {
			$day=date('d');
		}
		if (validate_int($ui->post['monthstart'],1,12)) {
			$month=$ui->post['monthstart'];
		} else {
			$month=date('m',strtotime("-6 days"));
		}
		if (validate_int($ui->post['monthstop'],1,12)) {
			$monthstop=$ui->post['monthstop'];
		} else {
			$monthstop=date('m');
		}
		if (validate_int($ui->post['yearstart'],2000,date('Y'))) {
			$year=$ui->post['yearstart'];
		} else {
			$year=date('Y',strtotime("-6 days"));
		}
		if (validate_int($ui->post['yearstop'],2000,date('Y'))) {
			$yearstop=$ui->post['yearstop'];
		} else {
			$yearstop=date('Y');
		}
		$now=date('Y-m-d');
		$date1=strtotime("$year-$month-$day");
		$date2=strtotime("$yearstop-$monthstop-$daystop");
		$amount=intval(($date2-$date1)/86400)+1;
		if ($amount<0 and "$yearstop-$monthstop-$daystop">$now){
			$yearstop=date('Y');
			$monthstop=date('m');
			$daystop=date('d');
			$day=date('d',strtotime("-6 days"));
			$month=date('m',strtotime("-6 days"));
			$year=date('Y',strtotime("-6 days"));
			$amount=7;
		}
	} else if ($ui->post['dmy'] == 'mo') {
		$dmy='mo';
		$day = 1;
		if (validate_int($ui->post['monthstart'],1,12)) {
			$month=$ui->post['monthstart'];
		} else {
			$month=date('m',strtotime("-6 days"));
		}
		if (validate_int($ui->post['yearstart'],2000,date('Y'))) {
			$year=$ui->post['yearstart'];
		} else {
			$year=date('Y',strtotime("-6 days"));
		}
		if (validate_int($ui->post['yearstop'],2000,date('Y'))) {
			$yearstop=$ui->post['yearstop'];
		} else {
			$yearstop=date('Y');
		}
		if (validate_int($ui->post['monthstop'],1,12)) {
			$monthstop=$ui->post['monthstop'];
		} else {
			$monthstop=date('m');
		}
		$daystop=date('t', strtotime("$yearstop-$monthstop"));
		$now=date('Y-m');
		$date1=strtotime("$year-$month-$day");
		$add=$date1;
		$date2=strtotime("$yearstop-$monthstop-$daystop");
		$i = 0;
		while ($add<=$date2) {
			$newadd=strtotime("+1 months",$add);
			$add=$newadd;
			$i++;
		}
		$amount=$i;
		if ($amount<0 or "$yearstop-$monthstop">$now){
			$yearstop=date('Y');
			$monthstop=date('m');
			$daystop=date('t', strtotime("$yearstop-$monthstop"));
			$day = 1;
			$month=date('m',strtotime("-6 months"));
			$year=date('Y',strtotime("-6 months"));
			$amount=7;
		}
	} else if ($ui->post['dmy'] == 'ye') {
		$dmy='ye';
		$day = 1;
		if (validate_int($ui->post['yearstart'],2000,date('Y'))) {
			$year=$ui->post['yearstart'];
		} else {
			$year=date('Y',strtotime("-6 days"));
		}
		if (validate_int($ui->post['yearstop'],2000,date('Y'))) {
			$yearstop=$ui->post['yearstop'];
		} else {
			$yearstop=date('Y');
		}
		$month = 1;
		$monthstop=12;
		$daystop=31;
		$now=date('Y');
		$date1=strtotime("$year-$month-$day");
		$date2=strtotime("$yearstop-$monthstop-$daystop");
		$add=$date1;
		$i = 0;
		while ($add<=$date2) {
			$newadd=strtotime("+1 year",$add);
			$add=$newadd;
			$i++;
		}
		$amount=$i;
		if ($amount<0 or "$yearstop">$now){
			$yearstop=date('Y');
			$monthstop=12;
			$daystop=31;
			$day = 1;
			$month = 1;
			$year=date('Y',strtotime("-1 year"));
			$amount = 2;
		}
	}
	if ($user_language="de") {
		$startdate="$day.$month.$year";
		$stopdate="$daystop.$monthstop.$yearstop";
	} else {
		$startdate="$year-$month-$day";
		$stopdate="$yearstop-$monthstop-$daystop";
	}
	$getlink="images.php?img=vo&amp;d=$dmy&amp;p=$year&amp;id=$day&amp;po=$month&amp;m=$amount$whichdata";
	$template_file = "admin_voice_stats.tpl";
}