<?php
/**
 * File: update_302-303.php.
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
$insert_easywi_version=$sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('3.03','<div align=\"right\">06.08.2012</div>
<b>Neuerungen und &Auml;nderungen:</b><br/>
<ul>
<li>Error Reporting der Voice API ist detailierter.</li>
<li>Bei der TS3 API können nun mehr Parameter und die MasterserverID mitgesendet werden.</li>
<li>Traffic Statistiken bei TS3 Servern werden nun monatlich nach dem Erstellen zurück gesetzt und nicht mehr am ersten jedes Monats.</li>
<li>Gameservermanagement zur API hinzugefügt.</li>
<li>12 neue Berechtigungen hinzugef&uuml;gt, um den Userzugriff genauer gestaltet zu k&ouml;nnen.</li>
<li>Sortierung der eingelesenen Feeds in der Adminübersicht ist nun nach neuestem Datum zuerst vorsortiert.</li>
<li>Performance des Trafficcountings im Vserver Modul stark verbessert.</li>
<li>Gameserverbereich komplett überarbeitet:</li>
<li>
<ul>
<li>Bis zu 5 Ports im Template definierbar.</li>
<li>Ports werden nun in der definierten Menge und Schritt beim Anlegen von Gameservern ausgefüllt, an Stelle +100 zum höchsten Port hinzuzufügen.</li>
<li>5 Optionale (opt1-5) Parameter können beim Anlegen eines Gameservers für den Startbefehl definiert werden.</li>
<li>Jeder Gameserver hat nun sein eigenes Passwort.</li>
</ul>
</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Root hinzufügen wird bei Reseller Usern ausgeblendet.</li>
<li>Traffic wird bei TS3 Servern beim Reset wieder freigegeben.</li>
<li>Mehrere zusätzliche isset() Abfragen, um Notice Meldungen im Debug Modus zu verhindern.</li>
<li>Detailierte Beschreibung kann bei Addons hinzugefügt werden.</li>
</ul>','<div align=\"right\">08.06.2012</div>
<b>Changes and new functions:</b><br/>
<ul>
<li>Error reporting regarding Voice API is more detailed.</li>
<li>At is possible to send more parameters and a masterserverID at the TS3 API</li>
<li>Traffic limitation for ts3 servers will be reset each month after adding the server instead of at the first day of the month.</li>
<li>Added gameservers management to the API.</li>
<li>List of already imported news feeds is ordered by data as default.</li>
<li>Improved performance of the traffic counting script for the vserver module.</li>
<li>Total rework of the gameserver part:</li>
<li>
<ul>
<li>Up to 5 ports can be defined at the gameserver template.</li>
<li>At gameserver creation ports will be filled up instead of adding +100 to the highest port.</li>
<li>5 optional (opt1-5) values can be added at the startcommand.</li>
<li>Each Gameserver has its own password now.</li>
</ul>
</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Add root is no longer shown for Reseller User.</li>
<li>Traffic speed will be increased properly when traffic limit is reset.</li>
<li>Added multiple isset() in order to avoid notice messages with active debugger.</li>
<li>Detailed description is available again at gameserver addons.</li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$error=$insert_easywi_version->errorinfo();
$insert_easywi_version->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

// SteamAppIDs nachtragen
$alter_servertypes=$sql->prepare("ALTER TABLE `servertypes` ADD COLUMN `appID` smallint(5) unsigned AFTER `steamgame`");
$alter_servertypes->execute();
$response->add('Action: alter_servertypes done: ');
$error=$alter_servertypes->errorinfo();
$alter_servertypes->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

foreach (array('cstrike'=>10,'tfc'=>20,'dod'=>30,'czero'=>80,'css'=>240,'dods'=>300,'hl2mp'=>360,'tf'=>440,'left4dead'=>500,'left4dead'=>550,'dota2'=>570,'csgo'=>730,'ageofchivalry'=>17510,'insurgency'=>17700,'zps'=>17500) as $key => $value) {
	$query = $sql->prepare("UPDATE `servertypes` SET `appID`=? WHERE `shorten`=?");
	$query->execute(array($value,$key));
}

// Add switchID column to serverlist table
$alter_serverlist=$sql->prepare("ALTER TABLE `serverlist` ADD COLUMN `switchID` bigint(19) unsigned AFTER `id`");
$alter_serverlist->execute();
$response->add('Action: alter_serverlist done: ');
$error=$alter_serverlist->errorinfo();
$alter_serverlist->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

// Add serverID column to restart table
$alter_gserver_restarts=$sql->prepare("ALTER TABLE `gserver_restarts` ADD COLUMN `switchID` bigint(19) unsigned AFTER `gsswitch`");
$alter_gserver_restarts->execute();
$response->add('Action: alter_gserver_restarts done: ');
$error=$alter_gserver_restarts->errorinfo();
$alter_gserver_restarts->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

// Alter gsswitch table
$alter_gsswitch=$sql->prepare("ALTER TABLE `gsswitch`
ADD COLUMN `active` enum('Y','N') NOT NULL DEFAULT 'Y' after `id`,
ADD COLUMN `rootID` bigint(19) unsigned NOT NULL after `userid`,
ADD COLUMN `running` enum('Y','N') NOT NULL DEFAULT 'Y' after `stopped`,
ADD COLUMN `pallowed` enum('Y','N') NOT NULL DEFAULT 'N' after `running`,
ADD COLUMN `eacallowed` enum('Y','N') NOT NULL DEFAULT 'N' after `pallowed`,
ADD COLUMN `protected` enum('Y','N') NOT NULL DEFAULT 'N' after `eacallowed`,
ADD COLUMN `brandname` enum('Y','N') DEFAULT 'N' after `protected`,
ADD COLUMN `tvenable` enum('Y','N') NOT NULL DEFAULT 'N' after `brandname`,
ADD COLUMN `war` enum('Y','N') NOT NULL DEFAULT 'Y' after `tvenable`,
ADD COLUMN `ftppassword` blob after `war`,
ADD COLUMN `ppassword` blob after `ftppassword`,
ADD COLUMN `psince` datetime DEFAULT NULL after `ppassword`,
ADD COLUMN `serverip` varchar(15) NOT NULL after `psince`,
ADD COLUMN `port` smallint(5) unsigned DEFAULT NULL after `serverip`,
ADD COLUMN `port2` smallint(5) unsigned DEFAULT NULL after `port`,
ADD COLUMN `port3` smallint(5) unsigned DEFAULT NULL after `port2`,
ADD COLUMN `port4` smallint(5) unsigned DEFAULT NULL after `port3`,
ADD COLUMN `minram` int(10) unsigned DEFAULT NULL after `port4`,
ADD COLUMN `maxram` int(10) unsigned DEFAULT NULL after `minram`,
ADD COLUMN `slots` smallint(4) unsigned DEFAULT NULL after `maxram`,
ADD COLUMN `masterfdl` enum('Y','N') NOT NULL DEFAULT 'Y' after `slots`,
ADD COLUMN `mfdldata` varchar(255) DEFAULT NULL after `masterfdl`,
ADD COLUMN `taskset` enum('Y','N') DEFAULT 'N' after `mfdldata`,
ADD COLUMN `cores` varchar(255) DEFAULT NULL after `taskset`
");
$alter_gsswitch->execute();
$response->add('Action: alter_gsswitch done: ');
$error=$alter_gsswitch->errorinfo();
$alter_gsswitch->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) {
	$response->add($error[2].'<br />');
} else {
	$response->add('OK<br />');
	// First get users and their password than get his servers and update tables
	$query = $sql->prepare("SELECT `id`,AES_DECRYPT(`ftppass`,?) AS `pwd` FROM `userdata` WHERE `accounttype`='u'");
	$query->execute(array($aeskey));
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$userID=$row['id'];
		$ftpPWD=$row['pwd'];
		
		// get the servers
		$query2 = $sql->prepare("SELECT `id`,`server`,`shorten` FROM `gsswitch` WHERE `userid`=?");
		$query2->execute(array($userID));
		foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
			$address=$row2['server'];
			$gsID=$row2['id'];
			list($gsIP,$gsPort)=explode(':',$address);
			
			// update list and restarts
			$query3 = $sql->prepare("UPDATE `serverlist` SET `switchID`=? WHERE `serverip`=? and `port`=?");
			$query3->execute(array($gsID,$gsIP,$gsPort));
			$query3 = $sql->prepare("UPDATE `gserver_restarts` SET `switchID`=? WHERE `address`=?");
			$query3->execute(array($gsID,$address));
			
			// get serverlist and update gsswitch
			$query3 = $sql->prepare("SELECT s.*,AES_DECRYPT(`ppassword`,?) AS `ppwd`,s.`serverid` AS `rootID` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND t.`shorten`=? LIMIT 1");
			$query3->execute(array($aeskey,$gsID, $row2['shorten']));
			foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
				$query3 = $sql->prepare("UPDATE `gsswitch` SET `active`=?,`rootID`=?,`running`=?,`pallowed`=?,`eacallowed`=?,`protected`=?,`brandname`=?,`tvenable`=?,`war`=?,`ftppassword`=AES_ENCRYPT(?,?),`ppassword`=AES_ENCRYPT(?,?),`psince`=?,`serverip`=?,`port`=?,`port2`=?,`port3`=?,`port4`=?,`minram`=?,`maxram`=?,`slots`=?,`masterfdl`=?,`mfdldata`=?,`taskset`=?,`cores`=? WHERE `id`=? LIMIT 1");
				$query3->execute(array($row3['active'], $row3['rootID'], $row3['running'], $row3['pallowed'], $row3['eacallowed'], $row3['protected'], $row3['brandname'], $row3['tvenable'], $row3['war'],$ftpPWD,$aeskey, $row3['ppwd'],$aeskey, $row3['psince'], $row3['serverip'], $row3['port'], $row3['tvport'], $row3['port3'], $row3['port4'], $row3['minram'], $row3['maxram'], $row3['slots'], $row3['masterfdl'], $row3['mfdldata'], $row3['taskset'], $row3['cores'],$gsID));
				$response->add('Action: Update gameserver: '.$address.'<br />');
			}
		}
	}
	$query = $sql->prepare("DELETE FROM `gserver_restarts` WHERE `switchID` IS NULL");
	$query->execute();
	$query = $sql->prepare("DELETE FROM `serverlist` WHERE `switchID` IS NULL");
	$query->execute();
	$query2 = $sql->prepare("SELECT COUNT(*) AS `amount` FROM `serverlist` WHERE `switchID`=?");
	$query3 = $sql->prepare("DELETE FROM `gsswitch` WHERE `id`=? LIMIT 1");
	$query = $sql->prepare("SELECT `id` FROM `gsswitch`");
	$query->execute(array($userID));
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$query2->execute(array($row['id']));
		if ($query2->fetchColumn()==0) {
			$query3->execute(array($row['id']));
		}
	}
}
} else {
	echo "Error: this file needs to be included by the updater!<br />";
}


// Drop gsstatus table
$drop_gsstatus=$sql->prepare("DROP TABLE `gsstatus`");
$drop_gsstatus->execute();
$response->add('Action: drop_gsstatus done: ');
$error=$drop_gsstatus->errorinfo();
$drop_gsstatus->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');