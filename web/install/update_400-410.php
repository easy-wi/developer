<?php

/**
 * File: update_400-410.php.
 * Author: Ulrich Block
 * Date: 03.10.13
 * Time: 12:25
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
    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('4.10','<div align=\"right\">16.10.2013</div>
<b>Änderungen:</b><br/>
<ul>
</ul>

','<div align=\"right\">10.16.2013</div>
<b>Changes:</b><br/>
<ul>
</ul>

')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

    $query="CREATE TABLE IF NOT EXISTS `addons_allowed` (
  `addon_id` int(10) unsigned NOT NULL,
  `servertype_id` int(10) unsigned NOT NULL,
  `reseller_id` int(10) unsigned NULL DEFAULT 0,
  PRIMARY KEY (`addon_id`,`servertype_id`),KEY(`reseller_id`)
) ENGINE=InnoDB";
    $add = $sql->prepare($query);
    $add->execute();

    $query = $sql->prepare("SELECT s.`id` AS `servertype_id`,s.`resellerid`,a.`id` AS `addon_id` FROM `servertypes` AS s LEFT JOIN `addons` AS a ON s.`shorten`=a.`shorten` OR s.`qstat`=a.`shorten` WHERE a.`id` IS NOT NULL");
    $query2 = $sql->prepare("INSERT INTO `addons_allowed` (`addon_id`,`servertype_id`,`reseller_id`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `addon_id`=`addon_id`");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $query2->execute(array($row['addon_id'],$row['servertype_id'],$row['resellerid']));
    }

    $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='samp' AND `resellerid`=0 LIMIT 1");
    $query->execute();
    if ($query->rowCount() == 0) {
        $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`qstat`,`gamemod`,`gamemod2`,`configs`,`configedit`,`qstatpassparam`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`resellerid`,`mapGroup`) VALUES ('N',NULL,1,'samp','San Andreas Multiplayer','gserver','samp03svr',NULL,NULL,NULL,0,NULL,'./%binary%',NULL,NULL,'gtasamp','N','','server.cfg','[server.cfg] cfg\r\nmaxplayers %slots%\r\nport %port%','',10,1,7777,NULL,NULL,NULL,NULL,0,NULL)");
        $query->execute();
    }

    $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='mtasa' AND `resellerid`=0 LIMIT 1");
    $query->execute();
    if ($query->rowCount() == 0) {
        $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`qstat`,`gamemod`,`gamemod2`,`configs`,`configedit`,`qstatpassparam`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`resellerid`,`mapGroup`) VALUES ('N',NULL,1,'mtasa','Multi Theft Auto San Andreas','gserver','mta-server',NULL,NULL,NULL,0,NULL,'./%binary%',NULL,NULL,'mtasa','N','','[mods/deathmatch/mtaserver.conf] xml\r\n<serverip>%ip%</serverip>\r\n<serverport>%port%</serverport> \r\n<httpport>%port2%</httpport>\r\n<maxplayers>%slots%</maxplayers>\r\n<httpserver>0</httpserver>','',10,3,22003,22005,22126,NULL,NULL,0,NULL)");
        $query->execute();
    }

    $query = $sql->prepare("INSERT INTO `qstatshorten` (`qstat`,`description`) VALUES ('teeworlds', 'Teeworlds'),('mtasa', 'Multi Theft Auto San Andreas')");
    $query->execute();

    $query = $sql->prepare("UPDATE `qstatshorten` SET `description`='San Andreas Multiplayer' WHERE `qstat`='gtasamp'");
    $query->execute();

    if ($query->rowCount() == 0) {
        $query = $sql->prepare("INSERT INTO `servertypes` (`steamgame`,`appID`,`updates`,`shorten`,`description`,`type`,`gamebinary`,`binarydir`,`modfolder`,`fps`,`slots`,`map`,`cmd`,`modcmds`,`tic`,`qstat`,`gamemod`,`gamemod2`,`configs`,`configedit`,`qstatpassparam`,`portStep`,`portMax`,`portOne`,`portTwo`,`portThree`,`portFour`,`portFive`,`resellerid`,`mapGroup`) VALUES ('N',NULL,1,'teeworlds','Teeworlds','gserver','teeworlds_srv',NULL,NULL,NULL,0,NULL,'./%binary%','[Capture the Flag = default]\r\n-f config_ctf.cfg\r\n\r\n[Deathmatch]\r\n-f config_dm.cfg\r\n\r\n[Team Deathmatch]\r\n-f config_tdm.cfg',NULL,'teeworlds','N','','config_ctf.cfg\r\nconfig_dm.cfg\r\nconfig_tdm.cfg', '[autoexec.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_ctf.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_dm.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%\r\n\r\n[config_tdm.cfg] cfg\r\nsv_max_clients %slots%\r\nsv_bindaddr %ip%\r\nsv_port %port%','',10,1,8303,NULL,NULL,NULL,NULL,0,NULL)");
        $query->execute();
    }

    $query = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `shorten`='teeworlds' AND `resellerid`=0 LIMIT 1");
    $query->execute();

    $query = $sql->prepare("UPDATE `servertypes` SET `cmd`='java -Xincgc -Xmx%maxram%M -Xms%minram%M -jar %binary% -o true -h %ip% -p %port% -s %slots% --log-append false --log-limit 50000' WHERE `shorten`='bukkit'");
    $query->execute();



} else {
    echo "Error: this file needs to be included by the updater!<br />";
}