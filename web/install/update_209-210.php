<?php
/**
 * File: update_209-210.php.
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

if (isset($include) and $include == true) {
$insert_easywi_version = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('2.10','<div align=\"right\">11.01.2012</div>
<b>Neuerungen und &Auml;nderungen:</b><br/>
<ul>
<li>Bei neuen Gameservern wird nun je Gameserver ein eigener User hinzugef&uumlgt.</li>
<li>Updateverzeichnis vom temporären install/ auf permanentes tmp/ geändert.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Probleme in der admin_header.tpl und userpanel_header.tpl behoben.</li>
<li>Die statuscheck.php ber&uuml;cksichtigt nun auch den Status \"Timeout\".</li>
</ul>','<div align=\"right\">01.11.2012</div>
<b>Changes and new functions:</b><br/>
<ul>
<li>Each new gameserver will have it´s own user.</li>
<li>Updatefolder changed from temporary install/ to tmp/ folder.</li>
</ul>
<br />
<b>Bugfixes:</b><br/>
<ul>
<li>Solved issues in the admin_header.tpl and userpanel_header.tpl</li>
<li>The statuscheck.php can handle the serverstatus \"Timeout\" now.</li>
</ul>')");
$insert_easywi_version->execute();
$response->add('Action: insert_easywi_version done: ');
$error = $insert_easywi_version->errorinfo();
$insert_easywi_version->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$alter_gsswitch = $sql->prepare("ALTER TABLE `gsswitch` ADD COLUMN `newlayout` ENUM('Y','N') DEFAULT 'Y' AFTER `secnotified`");
$alter_gsswitch->execute();
$response->add('Action: alter_gsswitch done: ');
$error = $alter_gsswitch->errorinfo();
$alter_gsswitch->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

$update_gsswitch = $sql->prepare("UPDATE `gsswitch` SET `newlayout`='N'");
$update_gsswitch->execute();
$response->add('Action: update_gsswitch done: ');
$error = $update_gsswitch->errorinfo();
$update_gsswitch->closecursor();
if (isset($error[2]) and $error[2] != '' and $error[2] != null and !isinteger($error[2])) $response->add($error[2].'<br />');
else $response->add('OK<br />');

} else {
	echo "Error: this file needs to be included by the updater!<br />";
}
?>