<?php
/**
 * File: update_1x-20-208.php.
 * Author: Ulrich Block
 *
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


if (isset($include) and $include==true) {
$drop_serverlist_qstat=$sql->prepare("ALTER TABLE `serverlist` DROP `qstat`");
$drop_serverlist_qstat->execute();
$response->add('Action: drop_serverlist_qstat done: ');
$error=$drop_serverlist_qstat->errorinfo();
$drop_serverlist_qstat->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$drop_gsswitch_restarttime=$sql->prepare("ALTER TABLE `gsswitch` DROP `restarttime`");
$drop_gsswitch_restarttime->execute();
$response->add('Action: drop_gsswitch_restarttime done: ');
$error=$drop_gsswitch_restarttime->errorinfo();
$drop_gsswitch_restarttime->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$create_easywiversion=$sql->prepare("CREATE TABLE IF NOT EXISTS `easywi_version` (
  `id` INT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` DECIMAL(4,2) DEFAULT '2.0' NOT NULL,
  PRIMARY KEY (`id`)
 )");
$create_easywiversion->execute();
$response->add('Action: create_easywiversion done: ');
$error=$create_easywiversion->errorinfo();
$create_easywiversion->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$add_rservermasterg_installstarted=$sql->prepare("ALTER TABLE `rservermasterg` ADD COLUMN `installstarted` DATETIME NOT NULL AFTER `installing`");
$add_rservermasterg_installstarted->execute();
$response->add('Action: add_rservermasterg_installstarted done: ');
$error=$add_rservermasterg_installstarted->errorinfo();
$add_rservermasterg_installstarted->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$insert_easywiversion=$sql->prepare("INSERT INTO `easywi_version` (`version`) VALUES ('2.00')");
$insert_easywiversion->execute();
$response->add('Action: insert_easywiversion done: ');
$error=$insert_easywiversion->errorinfo();
$insert_easywiversion->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$create_voice_masterserver=$sql->prepare("CREATE TABLE IF NOT EXISTS `voice_masterserver` (
  `id` INT(30) UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` ENUM('Y','N') DEFAULT 'Y',
  `type` VARCHAR(30) DEFAULT 'ts3' NOT NULL,
  `usedns` ENUM('Y','N') DEFAULT 'Y',
  `defaultdns` VARCHAR(50) DEFAULT NULL NULL,
  `defaultname` VARCHAR(255) DEFAULT NULL NULL,
  `defaultwelcome` VARCHAR(255) DEFAULT NULL NULL,
  `defaulthostbanner_url` VARCHAR(255) DEFAULT NULL NULL,
  `defaulthostbanner_gfx_url` VARCHAR(255) DEFAULT NULL NULL,
  `defaulthostbutton_tooltip` VARCHAR(255) DEFAULT NULL NULL,
  `defaulthostbutton_url` VARCHAR(255) DEFAULT NULL NULL,
  `defaulthostbutton_gfx_url` VARCHAR(255) DEFAULT NULL NULL,
  `queryport` INT(5) UNSIGNED DEFAULT NULL NULL,
  `querypassword` BLOB,
  `filetransferport` INT(5) UNSIGNED DEFAULT NULL NULL,
  `maxserver` INT(30) UNSIGNED DEFAULT NULL NULL,
  `maxslots` INT(255) UNSIGNED DEFAULT NULL NULL,
  `rootid` INT(30) UNSIGNED DEFAULT NULL NULL,
  `addedby` INT(1) UNSIGNED DEFAULT '1' NOT NULL,
  `publickey` ENUM('Y','N') DEFAULT 'Y',
  `ssh2ip` VARCHAR(15) DEFAULT NULL NULL,
  `ips` TEXT DEFAULT NULL NULL,
  `ssh2port` BLOB,
  `ssh2user` BLOB,
  `ssh2password` BLOB,
  `ftpport` BLOB,
  `bitversion` INT(2) UNSIGNED DEFAULT '64',
  `serverdir` VARCHAR(255) DEFAULT NULL,
  `keyname` VARCHAR(50) DEFAULT NULL,
  `notified` ENUM('Y','N') DEFAULT 'N',
  `resellerid` INT(30) UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`)
)");
$create_voice_masterserver->execute();
$response->add('Action: create_voice_masterserver done: ');
$error=$create_voice_masterserver->errorinfo();
$create_voice_masterserver->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$create_voice_server=$sql->prepare("CREATE TABLE IF NOT EXISTS `voice_server` (
  `id` INT(30) UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` ENUM('Y','N') DEFAULT 'Y',
  `lendserver` ENUM('Y','N') NOT NULL DEFAULT 'N',
  `userid` INT(30) UNSIGNED NOT NULL,
  `masterserver` INT(30) UNSIGNED NOT NULL,
  `ip` VARCHAR(15) NOT NULL,
  `port` INT(5) UNSIGNED DEFAULT NULL NULL,
  `slots` INT(30) UNSIGNED DEFAULT '50' NOT NULL,
  `initialpassword` VARCHAR(255) DEFAULT NULL NULL,
  `password` ENUM('Y','N') DEFAULT 'Y',
  `forcebanner` ENUM('Y','N') DEFAULT 'Y',
  `forcebutton` ENUM('Y','N') DEFAULT 'Y',
  `forceservertag` ENUM('Y','N') DEFAULT 'Y',
  `forcewelcome` ENUM('Y','N') DEFAULT 'Y',
  `max_download_total_bandwidth` INT(255) DEFAULT '65536',
  `max_upload_total_bandwidth` INT(255) DEFAULT '65536',
  `localserverid` INT(30) UNSIGNED NOT NULL,
  `dns` VARCHAR(50) DEFAULT NULL NULL,
  `usedslots` INT(30) UNSIGNED,
  `uptime` INT(255) UNSIGNED,
  `file_sent` INT(255) UNSIGNED,
  `file_received` INT(255) UNSIGNED,
  `sent` INT(255) UNSIGNED,
  `received` INT(255) UNSIGNED,
  `notified` ENUM('Y','N') DEFAULT 'N',
  `resellerid` INT(30) UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`)
)");
$create_voice_server->execute();
$response->add('Action: create_voice_server done: ');
$error=$create_voice_server->errorinfo();
$create_voice_server->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');
$create_traffic_settings=$sql->prepare("CREATE TABLE IF NOT EXISTS `traffic_settings` (
  `id` INT(30) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(30) DEFAULT 'mysql' NOT NULL,
  `statip` VARCHAR(50) DEFAULT NULL,
  `dbname` BLOB,
  `dbuser` BLOB,
  `dbpassword` BLOB,
  `multiplier` INT(10) UNSIGNED DEFAULT '512' NULL,
  `table_name` VARCHAR(30) DEFAULT NULL,
  `column_sourceip` VARCHAR(30) DEFAULT NULL,
  `column_destip` VARCHAR(30) DEFAULT NULL,
  `column_byte` VARCHAR(30) DEFAULT NULL,
  `column_date` VARCHAR(30) DEFAULT NULL,
  `text_colour_1` INT(3) UNSIGNED DEFAULT '0' NULL,
  `text_colour_2` INT(3) UNSIGNED DEFAULT '0' NULL,
  `text_colour_3` INT(3) UNSIGNED DEFAULT '0' NULL,
  `barin_colour_1` INT(3) UNSIGNED DEFAULT '0' NULL,
  `barin_colour_2` INT(3) UNSIGNED DEFAULT '206' NULL,
  `barin_colour_3` INT(3) UNSIGNED DEFAULT '209' NULL,
  `barout_colour_1` INT(3) UNSIGNED DEFAULT '0' NULL,
  `barout_colour_2` INT(3) UNSIGNED DEFAULT '191' NULL,
  `barout_colour_3` INT(3) UNSIGNED DEFAULT '255' NULL,
  `bartotal_colour_1` INT(3) UNSIGNED DEFAULT '30' NULL,
  `bartotal_colour_2` INT(3) UNSIGNED DEFAULT '144' NULL,
  `bartotal_colour_3` INT(3) UNSIGNED DEFAULT '255' NULL,
  `bg_colour_1` INT(3) UNSIGNED DEFAULT '240' NULL,
  `bg_colour_2` INT(3) UNSIGNED DEFAULT '240' NULL,
  `bg_colour_3` INT(3) UNSIGNED DEFAULT '255' NULL,
  `border_colour_1` INT(3) UNSIGNED DEFAULT '200' NULL,
  `border_colour_2` INT(3) UNSIGNED DEFAULT '200' NULL,
  `border_colour_3` INT(3) UNSIGNED DEFAULT '200' NULL,
  `line_colour_1` INT(3) UNSIGNED DEFAULT '220' NULL,
  `line_colour_2` INT(3) UNSIGNED DEFAULT '220' NULL,
  `line_colour_3` INT(3) UNSIGNED DEFAULT '220' NULL,
  PRIMARY KEY (`id`)
)");
$create_traffic_settings->execute();
$response->add('Action: create_traffic_settings done: ');
$error=$create_traffic_settings->errorinfo();
$create_traffic_settings->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else {
	$response->add('OK<br />');
	$insert_traffic_settings=$sql->prepare("INSERT INTO `traffic_settings` (`type`) VALUES ('mysql')");
	$insert_traffic_settings->execute();
	$response->add('Action: insert_traffic_settings done: ');
	$error=$insert_traffic_settings->errorinfo();
	$insert_traffic_settings->closecursor();
	if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
	else $response->add('OK<br />');
}

$add_userpermissions=$sql->prepare("ALTER TABLE `userpermissions` 
ADD COLUMN `trafficsettings` ENUM('Y','N') DEFAULT 'N' AFTER `traffic`,
ADD COLUMN `voicemasterserver` ENUM('Y','N') DEFAULT 'N' AFTER `vserverhost`,
ADD COLUMN `voiceserver` ENUM('Y','N') DEFAULT 'N' AFTER `voicemasterserver`");
$add_userpermissions->execute();
$response->add('Action: add_userpermissions done: ');
$error=$add_userpermissions->errorinfo();
$add_userpermissions->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$add_reseller_maxvoserver=$sql->prepare("ALTER TABLE `resellerdata` ADD COLUMN `maxvoserver` INT(10) UNSIGNED DEFAULT '20' AFTER `maxgserver`");
$add_reseller_maxvoserver->execute();
$response->add('Action: add_reseller_maxvoserver done: ');
$error=$add_reseller_maxvoserver->errorinfo();
$add_reseller_maxvoserver->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$add_settings_emailpwrecovery=$sql->prepare("ALTER TABLE `settings` ADD COLUMN `emailpwrecovery` BLOB AFTER `emailgserverupdate`");
$add_settings_emailpwrecovery->execute();
$response->add('Action: add_settings_emailpwrecovery done: ');
$error=$add_settings_emailpwrecovery->errorinfo();
$add_settings_emailpwrecovery->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$update_settings_emailpwrecovery=$sql->prepare("UPDATE `settings` SET `emailpwrecovery`=0x78dae5554d8fd330103d77a5fd0fc628b756f9585a8a7022ad4a7b854339709c24d3c4c2b183e316caaf679ca4da6db5d276e18010be789ec7f366fc3c71c4ab0f1f57db2f9fd6ac768dcad8ed8da811caecf666229c740ab3c0995616810807e81d0d3aa0fdae9de1b7bd3ca47c65b443ed66db638b9c15034ab9c31f2ef4bcef595183ed90d63e6f37b325271a118e89446eca23cbabc2286353fe7ad30fce14ee5c03b6923ae5116754c6233498df65e96ac2e969a14659d5aedfa1a4fe4a6cc97c31bf4f383b5c6018f1aa1f444fb5128c68dcddf1e1fc902b64b9b125da9e724c17475140a744a55a284ba9abdee971d7423162cf3061c2d9de9830324b762a2f794446540f675fc58bfb64c33352bb7c26f0ddf22c723d7f338f93ab229f49c9c8b2e716cd5e8b97aa12ff812cd1efeab23c8fa4568ad68bbee176d4976c0705a61cac0435ad511dd0c902a607b42568983aa84d039c75f227eda23e109db346570f5fc18845e8d93291dbb06fe17e0a3a507b074e1a1db060dfa1d5d06030bdd8e55b2d0e2e43d1521117ab27a736165b757cda3b8871c5b54757defb3f7ddb8bb711e50db001a976c638afe9dfd7e6bf791f44e85ff3e179f73f945f1b1bc32f");
$update_settings_emailpwrecovery->execute();
$response->add('Action: update_settings_emailpwrecovery done: ');
$error=$update_settings_emailpwrecovery->errorinfo();
$update_settings_emailpwrecovery->closecursor();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
else $response->add('OK<br />');

$pselect3=$sql->prepare("SELECT `resellerid` FROM `settings`");
$pselect3->execute();
foreach ($pselect3->fetchAll() as $mess_row) {
	$reseller_id=$mess_row['resellerid'];
	$pselect=$sql->prepare("SELECT `id` FROM `email_languages` WHERE `language`='de' AND `content`='emailpwrecovery' AND `resellerid`=? LIMIT 1");
	$pselect->execute(array($reseller_id));
	$num=$pselect->rowCount();
	if ($num=="1") {
		$update_settings_emailpwrecovery=$sql->prepare("UPDATE `email_languages` SET `xml`=0x78da4d904d4ec3301085d72071875156454a53ba43284da50ad80012a2f400269924239c71648fe9cf79b809176392b6888d65799ebff7e6e5cb5d67e10b7d20c78b649edd24805cba8ab859249bf7c7e96d02cbe2ea320fbd37658b7abdc8c5f55416af2684adf3026f583a45ecf3d971304882b1518c28b55863eba1413d0527fe3a9ffd9b8d34dcc9bcd87480c4c01831c01fdaf021d6ce57e839051f6b645813c2078920d4ce36c895bead30c8cfb75013b90996f8134cacef34cf881e4cd879ecedbe98dc93f229c8e0862a13d7699450b6630b034ec90fd317433683d5e863588630276fa6b215a88c578b14b648162a652af5f80b9e1c8b3bc97423cd0ecfa6c1c1343d69021c2260d7d78675834c3b39e7d3a667e7aa7f01cd578f66 WHERE `language`='de' AND `content`='emailpwrecovery' AND `resellerid`=? LIMIT 1");
		$update_settings_emailpwrecovery->execute(array($reseller_id));
		$response->add('Action: update_settings_emailpwrecovery done: ');
		$error=$update_settings_emailpwrecovery->errorinfo();
		$update_settings_emailpwrecovery->closecursor();
		if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
		else $response->add('OK<br />');
	} else {
		$pupdate2=$sql->prepare("INSERT INTO `email_languages` (`language`,`xml`,`content`,`resellerid`) VALUES ('de',0x78da4d904d4ec3301085d72071875156454a53ba43284da50ad80012a2f400269924239c71648fe9cf79b809176392b6888d65799ebff7e6e5cb5d67e10b7d20c78b649edd24805cba8ab859249bf7c7e96d02cbe2ea320fbd37658b7abdc8c5f55416af2684adf3026f583a45ecf3d971304882b1518c28b55863eba1413d0527fe3a9ffd9b8d34dcc9bcd87480c4c01831c01fdaf021d6ce57e839051f6b645813c2078920d4ce36c895bead30c8cfb75013b90996f8134cacef34cf881e4cd879ecedbe98dc93f229c8e0862a13d7699450b6630b034ec90fd317433683d5e863588630276fa6b215a88c578b14b648162a652af5f80b9e1c8b3bc97423cd0ecfa6c1c1343d69021c2260d7d78675834c3b39e7d3a667e7aa7f01cd578f66,'emailpwrecovery',?)");
		$pupdate2->execute(array($reseller_id));
	}
	$pselect=$sql->prepare("SELECT `id` FROM `email_languages` WHERE `language`='uk' AND `content`='emailpwrecovery' AND `resellerid`=? LIMIT 1");
	$pselect->execute(array($reseller_id));
	$num=$pselect->rowCount();
	if ($num=="1") {
		$update_settings_emailpwrecovery=$sql->prepare("UPDATE `email_languages` SET `xml`=0x78da4d90b16ac3400c86e716fa0ec2533bd46eb6522ece523a67701f409c95e4e85972ef7471fcf6951d0c050984f4ebfb85dce13644b852ca41785fedeab70a88bdf481cffbeabbfb7a7dafe0d03e3dba3c26f417b2f2c1a98cc1b747cc7992d443222f86985d731f2c928cb128aa51db4fc2e49a7f8d154137ddb59dd8f26f09890081698271638e91301314cb93c428931d04e8355c5706c4c03f1f66b86216204ba231ceed737709192c90018bca804a3d0c18620dc73bb417605158f5a002412107f6047ab13bbc97c2ba10bcf0299c4bb27d5365e21e84e35cbfb86673b3c734db67fe001cdc7269 WHERE `language`='uk' AND `content`='emailpwrecovery' AND `resellerid`=? LIMIT 1");
		$update_settings_emailpwrecovery->execute(array($reseller_id));
		$response->add('Action: update_settings_emailpwrecovery done: ');
		$error=$update_settings_emailpwrecovery->errorinfo();
		$update_settings_emailpwrecovery->closecursor();
		if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
		else $response->add('OK<br />');
	} else {
		$pupdate2=$sql->prepare("INSERT INTO `email_languages` (`language`,`xml`,`content`,`resellerid`) VALUES ('uk',0x78da4d90b16ac3400c86e716fa0ec2533bd46eb6522ece523a67701f409c95e4e85972ef7471fcf6951d0c050984f4ebfb85dce13644b852ca41785fedeab70a88bdf481cffbeabbfb7a7dafe0d03e3dba3c26f417b2f2c1a98cc1b747cc7992d443222f86985d731f2c928cb128aa51db4fc2e49a7f8d154137ddb59dd8f26f09890081698271638e91301314cb93c428931d04e8355c5706c4c03f1f66b86216204ba231ceed737709192c90018bca804a3d0c18620dc73bb417605158f5a002412107f6047ab13bbc97c2ba10bcf0299c4bb27d5365e21e84e35cbfb86673b3c734db67fe001cdc7269,'emailpwrecovery',?)");
		$pupdate2->execute(array($reseller_id));
	}
}
}
?>