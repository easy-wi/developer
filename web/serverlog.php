<?php

/**
 * File: serverlog.php.
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

define('EASYWIDIR', dirname(__FILE__));
include(EASYWIDIR . '/stuff/functions.php');
include(EASYWIDIR . '/stuff/class_validator.php');
include(EASYWIDIR . '/stuff/vorlage.php');
include(EASYWIDIR . '/stuff/settings.php');
if (!isset($user_id) and !isset($admin_id)) {
	header('Location: login.php');
	die('Please allow redirection');
} 
if ($ui->id('id', 19, 'get')) {
	include(EASYWIDIR . '/stuff/keyphrasefile.php');
	if ($reseller_id!="0" and $admin_id!=$reseller_id) {
		$reseller_id=$admin_id;
	}
	if(isset($admin_id)) {
        $query = $sql->prepare("SELECT u.`id`,u.`cname` FROM `gsswitch` g LEFT JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id', 19, 'get'),$reseller_id));
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$username=$row['cname'];
            $user_id=$row['id'];
		}
	} else {
		$username=getusername($user_id);
	}
    $query = $sql->prepare("SELECT g.`id`,g.`newlayout`,g.`rootID`,g.`serverip`,g.`port`,g.`protected`,AES_DECRYPT(g.`ftppassword`,?) AS `dftppass`,AES_DECRYPT(g.`ppassword`,?) AS `decryptedftppass`,s.`servertemplate`,t.`binarydir`,t.`shorten` FROM `gsswitch` g LEFT JOIN `serverlist` s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($aeskey,$aeskey,$server_id,$user_id,$reseller_id));
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$protected=$row['protected'];
		$servertemplate=$row['servertemplate'];
		$serverid=$row['rootID'];
		$serverip=$row['serverip'];
		$port=$row['port'];
        if ($row['newlayout']=='Y') $username=$username . '-' . $row['id'];
		$shorten=$row['shorten'];
		$binarydir=$row['binarydir'];
        $ftppass=$row['dftppass'];
		if ($protected=='N' and $servertemplate>1) {
			$shorten=$row['shorten']."-".$servertemplate;
			$pserver="server/";
		} else if ($protected=='Y') {
			$shorten=$row['shorten'];
			$username=$username.'-p';
			$ftppass=$row['decryptedftppass'];
			$pserver = '';
		} else {
			$shorten=$row['shorten'];
			$pserver='server/';
		}
	}
    $query = $sql->prepare("SELECT `ip`,`ftpport` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($serverid,$reseller_id));
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$ftpport=$row['ftpport'];
		$ip=$row['ip'];
	}
    if (isset($username,$ftpport)) {
        ini_set('default_socket_timeout',5);
        #echo "ftp://$username:$ftppass@$ip:$ftpport/$pserver".$serverip."_"."$port/$shorten/$binarydir/screenlog.0";
        $fp=@fopen("ftp://$username:$ftppass@$ip:$ftpport/$pserver".$serverip."_"."$port/$shorten/$binarydir/screenlog.0",'r');
        $screenlog = '';
        if ($fp == false) {
            echo "No Logdata!";
        } else {
            stream_set_timeout($fp,5);
            $i = 0;
            while ($i < 500) {
                $screenlog.=nl2br(fread($fp,128));
                $i++;
            }
            $info=stream_get_meta_data($fp);
            fclose($fp);
            echo ($info['timed_out']) ? 'Connection timed out!' : '<html><head><link href="main.css" rel="stylesheet" type="text/css"></head><body><div id="screenlog">'.$screenlog.'</div></body></html>';
        }
    } else {
        echo 'Error: ID';
    }
}
$sql=null;