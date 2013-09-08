<?php
/**
 * File: userpanel_logdata.php.
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
if ((!isset($user_id) or $main!=1) or (isset($user_id) and !$pa['usersettings'])) {
    header('Location: userpanel.php');
    die;
}
$sprache=getlanguagefile('logs',$user_language,$reseller_id);
$gssprache=getlanguagefile('gserver',$user_language,$reseller_id);
if (isset($admin_id) and $reseller_id!=0) $reseller_id=$admin_id;
$table=array();
$query=$sql->prepare("SELECT `subuser`,`username`,`useraction`,`ip`,`logdate` FROM `userlog` WHERE `usertype`='user' AND `userid`=? AND `resellerid`=? ORDER BY `logdate` DESC LIMIT $start,$amount");
$query2=$sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? LIMIT 1");
$query->execute(array($user_id,$reseller_id));
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
	$subuser=$row['subuser'];
	if ($subuser==0) {
		$username=$row['username'];
		$ip=$row['ip'];
	} else {
        $query2->execute(array($subuser));
		foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
			if (isanyadmin($subuser) and !isset($admin_id)) {
				$username=$row2['cname'];
				$ip="";
			} else {
				$username=$row2['cname'];
				$ip=$row['ip'];
			}
		}			
	}
	$useraction=$row['useraction'];
    $placeholders=array('%%','%add%','%dl%','%del%','%md%','%mod%','%start%','%restart%','%stop%','%upd%','%fail%','%ok%','%psw%','%cfg%','%import%','%reinstall%','%backup%','%use%');
    $replace=array('',$gsprache->add.': ',$gsprache->del.': ',$gsprache->del.': ',$gsprache->mod.': ',$gsprache->mod.': ',$gsprache->start.': ',$gsprache->start.': ',$gsprache->stop.': ',$gsprache->update.': ','','',$gssprache->password.': ',$gssprache->config.': ',$gsprache->import.': ',$gssprache->reinstall.': ',$gsprache->backup,$gsprache->use.': ');
    $replacedpics=str_replace($placeholders,$replace,$useraction);
    $placeholders=array('%voserver%','%gserver%','%user%','%fastdl%','%master%','%user%','%root%','%addon%','%settings%','%vserver%','%ticket_subject%','%reseller%','%virtual%','%eac%','%resync%','%virtualimage%','%template%','%voserver%','%emailsettings%','%dns%','%tsdns%','%pmode%');
    $replace=array($gsprache->voiceserver,$gsprache->gameserver,$gsprache->user,$gsprache->fastdownload,$gsprache->master,$gsprache->user,$gsprache->root,$gsprache->addon2,$gsprache->settings,$gsprache->virtual,$gsprache->support,$gsprache->reseller,$gsprache->hostsystem,'Easy Anti Cheat',$gssprache->resync,$gsprache->virtual.' '.$gsprache->template,$gsprache->template,$gsprache->voiceserver,'E-Mail '.$gsprache->settings,'TSDNS','TSDNS',$gssprache->protect);
    $replacedwords=str_replace($placeholders,$replace,$replacedpics);
    $logdate=explode(' ', $row['logdate']);
    $table[]=array('logday'=>$logdate[0],'loghour'=>$logdate[1],'ip'=>$ip,'username'=>$username,'useraction'=>$replacedwords);
}
$next=$start+$amount;
$query=$sql->prepare("SELECT `id` FROM `userlog` WHERE `usertype`='user' AND `userid`=? AND `resellerid`=?");
$query->execute(array($user_id,$reseller_id));
$colcount=$query->rowCount();
$vor=($colcount>$next) ? $start+$amount : $start;
$back=$start-$amount;
$zur=($back>=0) ? $start-$amount : $start;
$template_file="userpanel_logs.tpl";