<?php
/**
 * File: update_360-370.php.
 * Author: Ulrich Block
 * Date: 03.08.13
 * Time: 23:05
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
    if (@is_file('../stuff/keyphrasefile.php')){
        $aesfilecvar=getconfigcvars('../stuff/keyphrasefile.php');
        $aeskey=$aesfilecvar['aeskey'];
    } else if (@is_file(EASYWIDIR.'/stuff/keyphrasefile.php')){
        $aesfilecvar=getconfigcvars(EASYWIDIR.'/stuff/keyphrasefile.php');
        $aeskey=$aesfilecvar['aeskey'];
    } else if (@is_file(EASYWIDIR.'keyphrasefile.php')){
        $aesfilecvar=getconfigcvars(EASYWIDIR.'keyphrasefile.php');
        $aeskey=$aesfilecvar['aeskey'];
    }
    $insert_easywi_version=$sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('3.70','<div align=\"right\">18.08.2013</div>
<b>Änderungen:</b><br/>
<ul>
<li>Generell:
<ul>
<li>Hinzugefügt: Modal Boxen bei den Masterserver Übersichten, die eine Liste der installierten Server anzeigen.</li>
</ul></li>
<li>API:
<ul>
<li>Hinzugefügt: Die Leiherver API kann alle Leihserver auflisten.</li>
</ul></li>
<li>Benutzer:
<ul>
<li>Hinzugefügt: Registration.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Geändert: Beschriebung auf den Buttons an Stelle vom Hover im Userteil.</li>
<li>Hinzugefügt: Wenn gesetzt, werden Ram und gebundene CPU Kerne angezeigt im Userteil.</li>
<li>Hinzugefügt: Dateien können angegeben werden, die kopiert werden, wenn der Protection Mode an/aus gemacht wird. FTP Regeln müssen nachinstalliert werden!</li>
<li>Hinzugefügt: Etwaige Abhängigkeiten werden bei Gameserveraddons im Userteil angezeigt.</li>
</ul></li>
<li>Leihserver:
<ul>
<li>Hinzugefügt: Es kann zwischen anonymen und registrierten Benutzern im Zugang und den Einstellungen unterschieden werden.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Hinzugefügt: Parameter tsDebug:1 für die statuscheck.php</li>
<li>Geändert: Beschreibung auf den Buttons an Stelle vom Hover im Userteil.</li>
<li>Geändert: Antworttexte nach Useraktionen.</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br />
<ul>
<li>Config Edit im Protection Mode greift wieder auf den Protected Server zu.</li>
<li>CSS Server können wieder umgezogen werden.</li>
<li>Import von TS3 Servern beim Anlegen des Masterservers gibt keinen Token Fehler mehr.</li>
<li>Admin Home Seite listet Gameserver mit entfernten Servertag.</li>
<li>statuscheck.php und voiceserver mit PHP 5.4.4-14+deb7u2 funktioniert.</li>
<li>TS3 Server können gelöscht werden, wenn der Masterserver bereits entfernt wurde.</li>
</ul>
','<div align=\"right\">08.18.2013</div>
<b>Changes:</b><br/>
<ul>
<li>General:
<ul>
<li>Added: Modal boxes with installed server list at master server overviews.</li>
</ul></li>
<li>API:
<ul>
<li>Added: Lendserver API can list all lendservers.</li>
</ul></li>
<li>Gameserver:
<ul>
<li>Changed: Description on buttons instead of tooltip at the userpart.</li>
<li>Added: If set, Ram and CPU bound cores are displayed at the userpart.</li>
<li>Added: Files can be defined that will be copied in case protection mode is started/stopped. FTP rules need to be adjusted!</li>
<li>Added: Depencies are shown at the userpanels gameserver addon page.</li>
</ul></li>
<li>Lendserver:
<ul>
<li>Added: Sepeartion between anonymous and registered users in settings and access.</li>
</ul></li>
<li>User:
<ul>
<li>Added: Registration.</li>
</ul></li>
<li>Voiceserver:
<ul>
<li>Added: Parameter tsDebug:1 for statuscheck.php</li>
<li>Changed: Description on buttons instead of tooltip at the userpart.</li>
<li>Changed: response text after user actions userpart.</li>
</ul></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br />
<ul>
<li>Config edit in protection mode edits protected server again.</li>
<li>CSS server can be moved/imported again.</li>
<li>Importing TS3 servern at add masterserver step 2 does not produce token error.</li>
<li>Admin home lists gameserver with removed branding.</li>
<li>statuscheck.php and voiceserver and PHP 5.4.4-14+deb7u2 are working together.</li>
<li>TS3 server can be deleted in case the masterserver is already removed.</li>
</ul>
')");

    $insert_easywi_version->execute();
    $response->add('Action: insert_easywi_version done: ');
    $insert_easywi_version->closecursor();
    $query=$sql->prepare("ALTER TABLE `settings` ADD COLUMN `emailregister` blob NULL");
    $query->execute();

    $query=$sql->prepare("UPDATE `settings` SET `emailregister`=0x78dae5554d8fd330103d77a5fd0fc628b75649535a8a7022ad4a7b854339704293649258ebd8c1710be5d733f95ab6d54a5be08010b978de7c3cdba337b178f1eefd66ffe9c39695ae5231bbbd11254216dfde4c84934e61ec3953cbd4137e0fdb40850e28dfd533fc7290c7886f8c76a8dd6c7faa91b3b4471177f8cdf92def5b9696601b24dfc7fd6eb6e64423fc61239198ecc4922235cad888bfdc751f670a7357812da48e78c0191de311eacdaf327325e1687494288bd275194aea7b620b97abe55dc8d9f102c38037dd47f4745682017d8b05efef0f894296189ba1ed2887ede641e0d12d51a91ab24ceaa20bb6b8a9211d70cb3061c2d9ce98303233361e2f7c4446543fefbe99afeec21d8fa9dbd933856fd66795dbe5abe53cbcaaf2992d1959f6dca2b5edc5af7665fe076d097eb72febf34a9252b05d7582cb49972c8714230e56829a96a88ee8640ad323da0c344c1d94a602ce1af99db24807a271d6e8629c02e635682999c6610808bfa58d4562fd4ecbdde235a00e0e9c349a4a0e54a3a1426f7a91d56a6eee8d4e1a298b79c4bda634960688aee148d4edd07c4e14e87b1e3f84840ff11364a1f7847371e9c40aa4b25880cd1aefc13d46b5b158abd345d1b0f48dbf4262c1951afba795b57a1dd0be7d3f73635ca78bbfde9bffe65f24fcf6e5e89f92f6f1fa01c9a9e3c7");
    $query->execute();

    $query=$sql->prepare("INSERT INTO `translations` (`type`, `lang`, `transID`, `text`, `resellerID`) VALUES
    ('em', 'de', 'emailregister', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Account Aktivierung</topic>\r\n	<salutation>Sehr geehrte(r)</salutation>\r\n	<text1>bitte aktivieren Sie Ihren Account indem sie auf folgenden Link klicken:</text1>\r\n	<text2>Bei Fragen nutzen Sie bitte das Ticketsystem, oder nehmen telefonisch Kontakt auf.</text2>\r\n	<text3>Die entsprechende Nummer finden Sie im Panel.</text3>\r\n	<noreply>(Dies ist eine automatisch versendete E-Mail. Bitte antworten Sie nicht darauf, weil dieses E-Mail Konto nicht in der Lage ist, E-Mails zu empfangen.)</noreply>\r\n</sprache>', 0),
    ('em', 'uk', 'emailregister', '<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<sprache>\r\n	<topic>Account Activation</topic>\r\n	<salutation>Dear</salutation>\r\n	<text1>please activate your account by clicking following link:</text1>\r\n	<text2>If you have any questions feel free to use our supportsystem or give us a call.</text2>\r\n	<text3>You will find the phonenumber in our panel.</text3>\r\n	<noreply>(This is an automated mail. Please do not reply to it since the account is configured to send only.)</noreply>\r\n</sprache>', 0)");
    $query->execute();


    $query=$sql->prepare("ALTER TABLE `servertypes` ADD COLUMN `protectedSaveCFGs` text NULL");
    $query->execute();
    $query=$sql->prepare("UPDATE `servertypes` SET `protectedSaveCFGs`='cfg/server.cfg'");
    $query->execute();

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}