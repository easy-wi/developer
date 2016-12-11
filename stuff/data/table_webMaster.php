<?php

/**
 * File: table_webMaster.php.
 * Author: Ulrich Block
 * Date: 17.10.15
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

$defined['webMaster'] = array(
    'webMasterID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'usageType' => array("Type"=>"enum('F','W')","Null"=>"YES","Key"=>"","Default"=>"F","Extra"=>""),
    'phpConfiguration' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"int(5)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'user' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'pass' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'publickey' => array("Type"=>"enum('B','Y','N')","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'keyname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'connect_ip_only' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'ftpIP' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ftpPort' => array("Type"=>"int(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxVhost' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'maxHDD' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'hddOverbook' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'overbookPercent' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"50","Extra"=>""),
    'defaultdns' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'serverType' => array("Type"=>"enum('A','H','L','N','O')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'httpdCmd' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'userGroup' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'vhostStoragePath' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'vhostConfigPath' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'vhostTemplate' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'fpmConfigPath' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'fpmTemplate' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'fpmCmd' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dirHttpd' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dirLogs' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'quotaActive' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'quotaCmd' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'repquotaCmd' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'blocksize' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"4096","Extra"=>""),
    'inodeBlockRatio' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"4","Extra"=>""),
    'userAddCmd' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'userModCmd' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'userDelCmd' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);