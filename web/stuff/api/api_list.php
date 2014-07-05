<?php

/**
 * File: api_list.php.
 * Author: Ulrich Block
 * Date: 07.06.14
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

if (array_value_exists('action', 'ls', $data)) {


    // Game listing
    if ($apiType == 'json' or $apiType == 'xml') {

        if ($apiType == 'xml') {

            // Create XML DOM and send header
            $responsexml = new DOMDocument('1.0','utf-8');
            $element = $responsexml->createElement('server');

            $key = $responsexml->createElement('server');

            header("Content-type: text/xml; charset=UTF-8");

        } else if ($apiType == 'json') {

            header("Content-type: application/json; charset=UTF-8");
        }

        $query = $sql->prepare("SELECT r.`id`,r.`ip`,r.`description`,r.`altips`,r.`maxslots`,r.`maxserver`,r.`maxserver`-COUNT(g.`id`) AS `freeserver`,COUNT(g.`id`) AS `installedserver`,r.`active` AS `hostactive`,r.`resellerid` AS `resellerid`,(r.`maxslots`-SUM(g.`slots`)) AS `leftslots`,SUM(g.`slots`) AS `installedslots` FROM `rserverdata` r LEFT JOIN `gsswitch` g ON g.`rootID`=r.`id` GROUP BY r.`id` HAVING ((`freeserver` > 0 OR `freeserver` IS NULL) AND (`leftslots`>0 OR `leftslots` IS NULL) AND `hostactive`='Y' AND `resellerid`=?) ORDER BY `freeserver` DESC");
        $query2 = $sql->prepare("SELECT t.`shorten`,t.`description` FROM `rservermasterg` AS r INNER JOIN `servertypes` AS t ON r.`servertypeid` = t.`id` WHERE r.`serverid`=?");
        $query->execute(array($resellerID));

        if ($apiType == 'xml' and isset($key) and isset($element)) {

            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                $listRootServerXML = $responsexml->createElement('gameServer');

                $listServerXML = $responsexml->createElement('id', $row['id']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('ip', $row['ip']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('description', $row['description']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('altips', $row['altips']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('maxslots', $row['maxslots']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('maxserver', $row['maxserver']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('freeserver', $row['freeserver']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('installedserver', $row['installedserver']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('leftslots', $row['leftslots']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('installedslots', $row['installedslots']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('gamesavailable');

                $query2->execute(array($row['id']));
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    $listShortenXML = $responsexml->createElement($row2['shorten'], $row2['description']);
                    $listServerXML->appendChild($listShortenXML);
                }

                $listRootServerXML->appendChild($listServerXML);

                $key->appendChild($listRootServerXML);
            }

            $element->appendChild($key);

        }

        // MySQL

        $query = $sql->prepare("SELECT s.`id`,s.`externalID`,s.`ip`,s.`interface` AS `description`,s.`max_databases`, COUNT(d.`id`) AS `installed` FROM `mysql_external_servers` s LEFT JOIN `mysql_external_dbs` d ON s.`id`=d.`sid` WHERE s.`active`='Y' AND s.`resellerid`=? GROUP BY s.`ip`");
        $query->execute(array($resellerID));

        if ($apiType == 'xml' and isset($key) and isset($element)) {

            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                $listRootServerXML = $responsexml->createElement('mysqlServer');

                $listServerXML = $responsexml->createElement('id', $row['id']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('externalID', $row['externalID']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('ssh2ip', $row['ip']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('description', $row['description']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('maxDBs', $row['max_databases']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('dbsInstalled', $row['installed']);
                $listRootServerXML->appendChild($listServerXML);

                $key->appendChild($listRootServerXML);
            }

            $element->appendChild($key);

            $responsexml->appendChild($element);
        }

        // TSDNS

        $query = $sql->prepare("SELECT m.`id`,m.`externalID`,m.`ssh2ip`,m.`description`,m.`defaultdns`,m.`max_dns`,COUNT(d.`dnsID`) AS `installedDNS` FROM `voice_tsdns` AS m LEFT JOIN `voice_dns` AS d ON d.`tsdnsID`=m.`id` WHERE m.`resellerid`=? AND m.`active`='Y' GROUP BY m.`id`");
        $query->execute(array($resellerID));

        if ($apiType == 'xml' and isset($key) and isset($element)) {

            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                $listRootServerXML = $responsexml->createElement('tsdnsServer');

                $listServerXML = $responsexml->createElement('id', $row['id']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('externalID', $row['externalID']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('ssh2ip', $row['ssh2ip']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('description', $row['description']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('maxDNS', $row['max_dns']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('installedDNS', $row['installedDNS']);
                $listRootServerXML->appendChild($listServerXML);

                $key->appendChild($listRootServerXML);
            }

            $element->appendChild($key);

            $responsexml->appendChild($element);
        }


        // Voice server

        $query = $sql->prepare("SELECT m.`id`,m.`usedns`,m.`ssh2ip`,m.`description`,m.`defaultname`,m.`defaultwelcome`,m.`defaulthostbanner_url`,m.`defaulthostbanner_gfx_url`,m.`defaulthostbutton_tooltip`,m.`defaulthostbutton_url`,m.`defaulthostbutton_gfx_url`,m.`maxserver`,m.`maxslots`,COUNT(v.`id`)*(100/m.`maxserver`) AS `serverpercent`,SUM(v.`slots`)*(100/m.`maxslots`) AS `slotpercent`,COUNT(v.`id`) AS `installedserver`,SUM(v.`slots`) AS `installedslots`,SUM(v.`usedslots`) AS `uslots` FROM `voice_masterserver` m LEFT JOIN `rserverdata` r ON m.`rootid`=r.`id` LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` WHERE m.`active`='Y' AND m.`resellerid`=? GROUP BY m.`id`");
        $query->execute(array($resellerID));

        if ($apiType == 'xml' and isset($key) and isset($element)) {

            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                $listRootServerXML = $responsexml->createElement('voiceServer');

                $listServerXML = $responsexml->createElement('id', $row['id']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('usedns', $row['usedns']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('ssh2ip', $row['ssh2ip']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('description', $row['description']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('maxserver', $row['maxserver']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('installedserver', $row['installedserver']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('maxslots', $row['maxslots']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('installedslots', $row['installedslots']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('uslots', $row['uslots']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('defaultname', $row['defaultname']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('defaultwelcome', $row['defaultwelcome']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('defaulthostbanner_url', $row['defaulthostbanner_url']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('defaulthostbanner_gfx_url', $row['defaulthostbanner_gfx_url']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('defaulthostbutton_tooltip', $row['defaulthostbutton_tooltip']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('defaulthostbutton_url', $row['defaulthostbutton_url']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('defaulthostbutton_gfx_url', $row['defaulthostbutton_gfx_url']);
                $listRootServerXML->appendChild($listServerXML);

                $key->appendChild($listRootServerXML);
            }

            $element->appendChild($key);

            $responsexml->appendChild($element);
        }

        // Webspace

        $query = $sql->prepare("SELECT m.`webMasterID`,m.`externalID`,m.`description`,m.`ip`,m.`defaultdns`,m.`maxVhost`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID` = m.`webMasterID`) AS `installedVhosts`,m.`maxHDD`,m.`hddOverbook`,(SELECT SUM( v.`hdd` ) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID` = m.`webMasterID`) AS `hddUsage` FROM `webMaster` AS m WHERE m.`active`='Y' AND m.`resellerID`=?");
        $query->execute(array($resellerID));

        if ($apiType == 'xml' and isset($key) and isset($element)) {

            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                $listRootServerXML = $responsexml->createElement('webspaceServer');

                $listServerXML = $responsexml->createElement('id', $row['webMasterID']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('externalID', $row['externalID']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('ssh2ip', $row['ip']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('description', $row['description']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('defaultdns', $row['defaultdns']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('maxVhost', $row['maxVhost']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('installedVhosts', $row['installedVhosts']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('maxHDD', $row['maxHDD']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('hddOverbook', $row['hddOverbook']);
                $listRootServerXML->appendChild($listServerXML);

                $listServerXML = $responsexml->createElement('hddUsage', $row['hddUsage']);
                $listRootServerXML->appendChild($listServerXML);

                $key->appendChild($listRootServerXML);
            }

            $element->appendChild($key);

            $responsexml->appendChild($element);
        }

        if ($apiType == 'xml') {

            $responsexml->formatOutput = true;

            echo $responsexml->saveXML();

        }

    } else {
        header('HTTP/1.1 403 Forbidden');
        die('403 Forbidden');
    }


}