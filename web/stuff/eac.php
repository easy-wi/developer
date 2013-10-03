<?php
/**
 * File: eac.php.
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

if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['eac'])) {
	header('Location: admin.php');
	die('No acces');
}
include(EASYWIDIR . '/stuff/keyphrasefile.php');
$sprache = getlanguagefile('roots',$user_language,$reseller_id);
$gssprache = getlanguagefile('gserver',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id == 0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
	if (isset($_SESSION['oldid'])) {
		$logsubuser=$_SESSION['oldid'];
	} else {
		$logsubuser = 0;
	}
	$logreseller = 0;
}
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if (!$ui->w('action',4,'post')) {
	$pselect=$sql->prepare("SELECT `active`,`ip`,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass`,`publickey`,`keyname`,`cfgdir`,`normal_3`,`normal_4`,`hlds_3`,`hlds_4`,`hlds_5`,`hlds_6` FROM `eac` WHERE resellerid=:reseller_id LIMIT 1");
	$pselect->execute(array(':aeskey'=>$aeskey,':reseller_id'=>$reseller_id));
	foreach ($pselect->fetchAll() as $row) {
		$eac_active=$row['active'];
		$eac_ip=$row['ip'];
		$eac_port=$row['dport'];
		$eac_user=$row['duser'];
		$eac_pass=$row['dpass'];
		$normal_3=$row['normal_3'];
		$normal_4=$row['normal_4'];
		$hlds_3=$row['hlds_3'];
		$hlds_4=$row['hlds_4'];
		$hlds_5=$row['hlds_5'];
		$hlds_6=$row['hlds_6'];
		$eac_publickey=$row['publickey'];
		$eac_keyname=$row['keyname'];
		$eac_cfgdir=$row['cfgdir'];
	}
	$template_file = "admin_eac.tpl";
} else if ($ui->w('action',4,'post')=="md") {
	$fail = 0;
	if (!active_check($ui->post['publickey'])) {
		$fail = 1;
	}
	if (!active_check($ui->post['active'])) {
		$fail = 1;
	}
	if (!isip($ui->post['ip'],"all")) {
		$fail = 1;
	}
	if (!isid($ui->post['port'],"5")) {
		$fail = 1;
	}
	if (!uname_check($ui->post['user'],"20")) {
		$fail = 1;
	}
	if ($fail!="1") {
		if (isset($ui->post['normal_3'])) {
			$normal_3=active_check($ui->post['normal_3']);
		} else {
			$normal_3="N";
		}
		if (isset($ui->post['normal_4'])) {
			$normal_4=active_check($ui->post['normal_4']);
		} else {
			$normal_4="N";
		}
		if (isset($ui->post['hlds_3'])) {
			$hlds_3=active_check($ui->post['hlds_3']);
		} else {
			$hlds_3="N";
		}
		if (isset($ui->post['hlds_4'])) {
			$hlds_4=active_check($ui->post['hlds_4']);
		} else {
			$hlds_4="N";
		}
		if (isset($ui->post['hlds_5'])) {
			$hlds_5=active_check($ui->post['hlds_5']);
		} else {
			$hlds_5="N";
		}
		if (isset($ui->post['hlds_6'])) {
			$hlds_6=active_check($ui->post['hlds_6']);
		} else {
			$hlds_6="N";
		}
		$keyname=startparameter($ui->post['keyname']);
		$publickey=$ui->post['publickey'];
		$active=$ui->post['active'];	
		$ip=$ui->post['ip'];
		$port=$ui->post['port'];
		$user=$ui->post['user'];
		$pass=startparameter($ui->post['pass']);
		$cfgdir=folder($ui->post['cfgdir']);
		$pupdate=$sql->prepare("UPDATE `eac` SET `active`=:active,`ip`=:ip,`port`=AES_ENCRYPT(:port, :aeskey),`user`=AES_ENCRYPT(:user, :aeskey),`pass`=AES_ENCRYPT(:pass, :aeskey),`publickey`=:publickey,`keyname`=:keyname,`cfgdir`=:cfgdir,`normal_3`=:normal_3,`normal_4`=:normal_4,`hlds_3`=:hlds_3,`hlds_4`=:hlds_4,`hlds_5`=:hlds_5,`hlds_6`=:hlds_6 WHERE resellerid=:reseller_id");
		$pupdate->execute(array(':active'=>$active,':ip'=>$ip,':port'=>$port,':aeskey'=>$aeskey,':user'=>$user,':pass'=>$pass,':publickey'=>$publickey,':keyname'=>$keyname,':cfgdir'=>$cfgdir,':normal_3'=>$normal_3,':normal_4'=>$normal_4,':hlds_3'=>$hlds_3,':hlds_4'=>$hlds_4,':hlds_5'=>$hlds_5,':hlds_6'=>$hlds_6,':reseller_id'=>$reseller_id));
		$template_file = $spracheResponse->table_add;
		$loguseraction="%mod% %eac%";
		$insertlog->execute();
	} else {
		$template_file = 'admin_404.tpl';
	}
}