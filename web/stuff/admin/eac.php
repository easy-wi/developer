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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['eac'])) {
	header('Location: admin.php');
	die('No Access');
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('roots', $user_language, $reseller_id);
$gssprache = getlanguagefile('gserver', $user_language, $reseller_id);
$mysprache = getlanguagefile('mysql', $user_language, $reseller_id);

$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

if ($reseller_id == 0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
	if (isset($_SESSION['oldid'])) {
		$logsubuser = $_SESSION['oldid'];
	} else {
		$logsubuser = 0;
	}
	$logreseller = 0;
}

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if (!$ui->w('action', 4, 'post')) {

	$query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass` FROM `eac` WHERE resellerid=:reseller_id LIMIT 1");
    $query->execute(array(':aeskey' => $aeskey,':reseller_id' => $reseller_id));
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		$eac_active = $row['active'];
		$eac_ip = $row['ip'];
		$eac_port = $row['dport'];
		$eac_user = $row['duser'];
		$eac_pass = $row['dpass'];
		$normal_3 = $row['normal_3'];
		$normal_4 = $row['normal_4'];
		$hlds_3 = $row['hlds_3'];
		$hlds_4 = $row['hlds_4'];
		$hlds_5 = $row['hlds_5'];
		$hlds_6 = $row['hlds_6'];
		$eac_publickey = $row['publickey'];
		$eac_keyname = $row['keyname'];
		$eac_cfgdir = $row['cfgdir'];
        $type = $row['type'];
        $mysql_server = $row['mysql_server'];
        $mysql_port = $row['mysql_port'];
        $mysql_db = $row['mysql_db'];
        $mysql_table = $row['mysql_table'];
        $mysql_user = $row['mysql_user'];
        $mysql_password = $row['mysql_password'];
	}

	$template_file = 'admin_eac.tpl';

} else if ($ui->w('action', 4, 'post') == 'md') {

    $normal_3 = ($ui->active('normal_3', 'post')) ? $ui->active('normal_3', 'post') : 'N';
    $normal_4 = ($ui->active('normal_4', 'post')) ? $ui->active('normal_4', 'post') : 'N';
    $hlds_3 = ($ui->active('hlds_3', 'post')) ? $ui->active('hlds_3', 'post') : 'N';
    $hlds_4 = ($ui->active('hlds_4', 'post')) ? $ui->active('hlds_4', 'post') : 'N';
    $hlds_5 = ($ui->active('hlds_5', 'post')) ? $ui->active('hlds_5', 'post') : 'N';
    $hlds_6 = ($ui->active('hlds_6', 'post')) ? $ui->active('hlds_6', 'post') : 'N';

    $query = $sql->prepare("UPDATE `eac` SET `active`=:active,`ip`=:ip,`port`=AES_ENCRYPT(:port, :aeskey),`user`=AES_ENCRYPT(:user, :aeskey),`pass`=AES_ENCRYPT(:pass, :aeskey),`publickey`=:publickey,`keyname`=:keyname,`cfgdir`=:cfgdir,`normal_3`=:normal_3,`normal_4`=:normal_4,`hlds_3`=:hlds_3,`hlds_4`=:hlds_4,`hlds_5`=:hlds_5,`hlds_6`=:hlds_6,`type`=:type,`mysql_server`=:mysql_server,`mysql_port`=:mysql_port,`mysql_db`=:mysql_db,`mysql_table`=:mysql_table,`mysql_user`=:mysql_user,`mysql_password`=:mysql_password WHERE resellerid=:reseller_id");
    $query->execute(array(':active' => $ui->active('active', 'post'), ':ip' => $ui->ip4('ip', 'post'), ':port' => $ui->port('port', 'post'), ':aeskey' => $aeskey, ':user' => $ui->pregw('user', 255, 'post'), ':pass' => $ui->password('pass', 255, 'post') ,':publickey' => $ui->active('publickey', 'post'), ':keyname' => $ui->startparameter('keyname', 'post'), ':cfgdir' => $ui->folder('cfgdir','post'), ':normal_3' => $normal_3, ':normal_4' => $normal_4, ':hlds_3' => $hlds_3, ':hlds_4' => $hlds_4, ':hlds_5' => $hlds_5, ':hlds_6' => $hlds_6, ':type' => $ui->w('type', 1, 'post'), ':mysql_server' => $ui->w('mysql_server', 255, 'post'), ':mysql_port' => $ui->port('mysql_port', 'post'), ':mysql_db' => $ui->w('mysql_db', 255, 'post'), ':mysql_table' => $ui->w('mysql_table', 255, 'post'), ':mysql_user' => $ui->startparameter('mysql_user', 'post'), ':mysql_password' => $ui->password('mysql_password', 255, 'post'), ':reseller_id' => $reseller_id));

    if ($query->rowCount() > 0) {
        $template_file = $spracheResponse->table_add;
        $loguseraction="%mod% %eac%";
        $insertlog->execute();
    } else {
        $template_file = $spracheResponse->error_table;
    }
}