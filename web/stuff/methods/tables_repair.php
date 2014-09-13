<?php

/**
 * File: tables_repair.php.
 * Author: Ulrich Block
 * Date: 21.04.12
 * Time: 09:51
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

if (!isset($displayToUser) and (!isset($admin_id) or $main != 1 or $reseller_id != 0)) {
    header('Location: admin.php');
    die('No acces');
}

$defined['addons'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'paddon' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'addon' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'type' => array("Type"=>"enum('tool','map')","Null"=>"YES","Key"=>"","Default"=>"tool","Extra"=>""),
    'folder' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'menudescription' => array("Type"=>"varchar(30)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'configs' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cmd' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'rmcmd' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'depending' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['addons_allowed'] = array(
    'addon_id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'servertype_id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'reseller_id' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['addons_installed'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'userid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'addonid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'serverid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'servertemplate' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"1","Extra"=>""),
    'paddon' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['api_ips'] = array(
    'ip' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"0","Extra"=>"")
);

$defined['api_settings'] = array(
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'user' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'salt' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'pwd' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>"")
);

$defined['api_external_auth'] = array(
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'ssl' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'user' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'pwd' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'domain' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'file' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['api_import'] = array(
    'importID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'fetchUpdates' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'token' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'chunkSize' => array("Type"=>"int(11) unsigned","Null"=>"NO","Key"=>"","Default"=>"100","Extra"=>""),
    'ssl' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'domain' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'file' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'groupID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'lastID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lastCheck' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['badips'] = array(
    'id' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'badip' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'bantime' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'failcount' => array("Type"=>"smallint(2) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'reason' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>"")
);

$defined['custom_columns'] = array(
    'customID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'itemID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'var' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>"")
);

$defined['custom_columns_settings'] = array(
    'customID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'item' => array("Type"=>"enum('D','G','S','T','U','V')","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'type' => array("Type"=>"enum('I','V')","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'length' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'name' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>"")
);

$defined['rootsDedicated'] = array(
    'dedicatedID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'status' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'imageID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'pxeID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'resellerImageID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ips' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'initialPass' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'restart' => array("Type"=>"enum('N','A','T')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'useDHCP' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'usePXE' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'apiRequestType' => array("Type"=>"enum('P','G')","Null"=>"YES","Key"=>"","Default"=>"G","Extra"=>""),
    'apiRequestRestart' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'apiRequestStop' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'apiURL' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'https' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'mac' => array("Type"=>"varchar(17)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'jobPending' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['rootsDHCP'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'user' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'pass' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'publickey' => array("Type"=>"enum('B','Y','N')","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'keyname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ips' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'netmask' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"255.255.255.0","Extra"=>""),
    'startCmd' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dhcpFile' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['rootsIP4'] = array(
    'subnetID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'ownerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['rootsPXE'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'user' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'pass' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'publickey' => array("Type"=>"enum('B','Y','N')","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'keyname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'startCmd' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'PXEFolder' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['rootsSubnets'] = array(
    'subnetID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'dhcpServer' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'subnet' => array("Type"=>"varchar(11)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'subnetStart' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'subnetStop' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"254","Extra"=>""),
    'subnetOptions' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'netmask' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'vlan' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'vlanName' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['eac'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'user' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'pass' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'publickey' => array("Type"=>"enum('B','Y','N')","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'keyname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cfgdir' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'normal_3' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'normal_4' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'hlds_3' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'hlds_4' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'hlds_5' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'hlds_6' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'type' => array("Type"=>"enum('M','S')","Null"=>"YES","Key"=>"","Default"=>"S","Extra"=>""),
    'mysql_server' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mysql_port' => array("Type"=>"int(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mysql_db' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mysql_table' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mysql_user' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mysql_password' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['easywi_statistics'] = array(
    'gameMasterInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameMasterActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameMasterServerAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameMasterSlotsAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameMasterCrashed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverSlotsInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverSlotsActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverSlotsUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverNoPassword' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverNoTag' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverNotRunning' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlMasterInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlMasterActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlMasterDBAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlMasterCrashed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlDBInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlDBActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlDBSpaceUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'ticketsCompleted' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'ticketsInProcess' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'ticketsNew' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'userAmount' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'userAmountActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'virtualMasterInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'virtualMasterActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'virtualMasterVserverAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'virtualInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'virtualActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceMasterInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceMasterActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceMasterServerAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceMasterSlotsAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceMasterCrashed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverSlotsInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverSlotsActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverSlotsUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverTrafficAllowed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverTrafficUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverCrashed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webMasterInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webMasterActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webMasterSpaceAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webMasterVhostAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceSpaceGiven' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceSpaceGivenActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceSpaceUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"0","Extra"=>""),
    'statDate' => array("Type"=>"timestamp","Null"=>"NO","Key"=>"PRI","Default"=>"CURRENT_TIMESTAMP","Extra"=>""),
    'countUpdates' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"","Default"=>"0","Extra"=>"")
);

$defined['easywi_statistics_current'] = array(
    'gameMasterInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameMasterActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameMasterServerAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameMasterSlotsAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameMasterCrashed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverSlotsInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverSlotsActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverSlotsUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverNoPassword' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverNoTag' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'gameserverNotRunning' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlMasterInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlMasterActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlMasterDBAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlMasterCrashed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlDBInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlDBActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'mysqlDBSpaceUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'ticketsCompleted' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'ticketsInProcess' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'ticketsNew' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'userAmount' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'userAmountActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'virtualMasterInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'virtualMasterActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'virtualMasterVserverAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'virtualInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'virtualActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceMasterInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceMasterActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceMasterServerAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceMasterSlotsAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceMasterCrashed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverSlotsInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverSlotsActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverSlotsUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverTrafficAllowed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverTrafficUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'voiceserverCrashed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webMasterInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webMasterActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webMasterSpaceAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webMasterVhostAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceSpaceGiven' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceSpaceGivenActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceSpaceUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"0","Extra"=>""),
    'statDate' => array("Type"=>"timestamp","Null"=>"NO","Key"=>"","Default"=>"CURRENT_TIMESTAMP","Extra"=>"on update CURRENT_TIMESTAMP")
);

$defined['easywi_version'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'version' => array("Type"=>"decimal(4,2)","Null"=>"NO","Key"=>"","Default"=>"3.30","Extra"=>""),
    'de' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'en' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>"")
);

$defined['webMaster'] = array(
    'webMasterID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'usageType' => array("Type"=>"enum('F','W')","Null"=>"YES","Key"=>"","Default"=>"F","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"int(5)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'user' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'pass' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'publickey' => array("Type"=>"enum('B','Y','N')","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'keyname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
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

$defined['webVhost'] = array(
    'webVhostID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'webMasterID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'jobPending' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'hdd' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'hddUsage' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'dns' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ftpUser' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ftpPassword' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ownVhost' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'vhostTemplate' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['feeds_news'] = array(
    'newsID' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'feedID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'title' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'link' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'pubDate' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'content' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'author' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['feeds_settings'] = array(
    'settingsID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'merge' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'displayContent' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'orderBy' => array("Type"=>"enum('I','D')","Null"=>"NO","Key"=>"","Default"=>"D","Extra"=>""),
    'limitDisplay' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'useLocal' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'steamFeeds' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'maxChars' => array("Type"=>"int(6) unsigned","Null"=>"NO","Key"=>"","Default"=>"300","Extra"=>""),
    'newsAmount' => array("Type"=>"smallint(6) unsigned","Null"=>"NO","Key"=>"","Default"=>"4","Extra"=>""),
    'updateMinutes' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"","Default"=>"15","Extra"=>""),
    'lastUpdate' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxKeep' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['feeds_url'] = array(
    'feedID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'twitter' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'feedUrl' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'loginName' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'modified' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['gserver_file_templates'] = array(
    'templateID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'servertype' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'name' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'content' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['gserver_restarts'] = array(
    'id' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'template' => array("Type"=>"smallint(1) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'anticheat' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"1","Extra"=>""),
    'protected' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'backup' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'upload' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'worldsafe' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'restart' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'restarttime' => array("Type"=>"varchar(6)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'switchID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'gsswitch' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'map' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mapGroup' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'userid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['gsswitch'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'autoRestart' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'userid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'rootID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'serverid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'lendserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'backup' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'stopped' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'running' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'pallowed' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'eacallowed' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'protected' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'brandname' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'tvenable' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'war' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'ftppassword' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ppassword' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'psince' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'serverip' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port2' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port3' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port4' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port5' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'minram' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxram' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'slots' => array("Type"=>"smallint(4) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'masterfdl' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'mfdldata' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'taskset' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cores' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'secnotified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'newlayout' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'queryName' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'queryNumplayers' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'queryMaxplayers' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'queryMap' => array("Type"=>"varchar(40)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'queryPassword' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'queryUpdatetime' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'sourceSystemID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'jobPending' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['imprints'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'language' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'imprint' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['jobs'] = array(
    'jobID' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'hostID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'affectedID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'invoicedByID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'api' => array("Type"=>"enum('A','U','S')","Null"=>"NO","Key"=>"","Default"=>"A","Extra"=>""),
    'type' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'name' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'status' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'action' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'date' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'extraData' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['lendedserver'] = array(
    'id' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'serverid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'servertype' => array("Type"=>"varchar(1)","Null"=>"NO","Key"=>"","Default"=>"g","Extra"=>""),
    'rcon' => array("Type"=>"varchar(60)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'password' => array("Type"=>"varchar(20)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'slots' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'started' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'lendtime' => array("Type"=>"smallint(4) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'lenderip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ftpuploadpath' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['lendsettings'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'activeGS' => array("Type"=>"enum('A','R','B','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'activeVS' => array("Type"=>"enum('A','R','B','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'userGame' => array("Type"=>"enum('A','B','R')","Null"=>"NO","Key"=>"","Default"=>"B","Extra"=>""),
    'gameVoice' => array("Type"=>"enum('A','B','R')","Null"=>"NO","Key"=>"","Default"=>"B","Extra"=>""),
    'mintime' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'maxtime' => array("Type"=>"smallint(4)","Null"=>"NO","Key"=>"","Default"=>"120","Extra"=>""),
    'timesteps' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'minplayer' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'maxplayer' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"12","Extra"=>""),
    'playersteps' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'mintimeRegistered' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'maxtimeRegistered' => array("Type"=>"smallint(4)","Null"=>"NO","Key"=>"","Default"=>"120","Extra"=>""),
    'timestepsRegistered' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'minplayerRegistered' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'maxplayerRegistered' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"12","Extra"=>""),
    'playerstepsRegistered' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'vomintime' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'vomaxtime' => array("Type"=>"smallint(4) unsigned","Null"=>"NO","Key"=>"","Default"=>"120","Extra"=>""),
    'votimesteps' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'vominplayer' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'vomaxplayer' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"12","Extra"=>""),
    'voplayersteps' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'vomintimeRegistered' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'vomaxtimeRegistered' => array("Type"=>"smallint(4) unsigned","Null"=>"NO","Key"=>"","Default"=>"120","Extra"=>""),
    'votimestepsRegistered' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'vominplayerRegistered' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'vomaxplayerRegistered' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"12","Extra"=>""),
    'voplayerstepsRegistered' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'shutdownempty' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'shutdownemptytime' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"5","Extra"=>""),
    'ftpupload' => array("Type"=>"enum('A','R','Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'ftpuploadpath' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lendaccess' => array("Type"=>"smallint(1)","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'lastcheck' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'oldcheck' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>"")
);

$defined['lendstats'] = array(
    'lendDate' => array("Type"=>"datetime","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'serverID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'serverType' => array("Type"=>"enum('v','g')","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'lendtime' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'slots' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>"")
);

$defined['mail_log'] = array(
    'id' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'uid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'topic' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'date' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

#https://github.com/easy-wi/developer/issues/61 add module management
$defined['modules'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'get' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'file' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'sub' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'type' => array("Type"=>"enum('A','C','P','U')","Null"=>"YES","Key"=>"","Default"=>"A","Extra"=>"")
);

#https://github.com/easy-wi/developer/issues/42 column description added
$defined['mysql_external_dbs'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'sid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'uid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'dbname' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'password' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ips' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'manage_host_table' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'max_databases' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"100","Extra"=>""),
    'max_queries_per_hour' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'max_updates_per_hour' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'max_connections_per_hour' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'max_userconnections_per_hour' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'dbSize' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'jobPending' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['mysql_external_servers'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"3306","Extra"=>""),
    'user' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'password' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'max_databases' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"100","Extra"=>""),
    'interface' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'max_queries_per_hour' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'max_updates_per_hour' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'max_connections_per_hour' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'max_userconnections_per_hour' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['page_comments'] = array(
    'commentID' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'pageTextID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'replyTo' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'date' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'comment' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'authorname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'email' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'homepage' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dns' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'markedSpam' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'spamReason' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'moderateAccepted' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['page_downloads'] = array(
    'fileID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'show' => array("Type"=>"enum('A','R','N','E')","Null"=>"YES","Key"=>"","Default"=>"E","Extra"=>""),
    'order' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'count' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'fileExtension' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'fileName' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'date' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['page_downloads_log'] = array(
    'fileID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'date' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'hostname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>"")
);

$defined['page_pages'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'subpage' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'released' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"1","Extra"=>""),
    'sort' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'authorid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'authorname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'date' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'type' => array("Type"=>"varchar(10)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'img' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'comments' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'naviDisplay' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['page_pages_text'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'pageid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'language' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'title' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'shortlink' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'text' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['page_register_questions'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'question' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'answer' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>"")
);

$defined['page_settings'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'seo' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'rssfeed' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'rssfeed_fulltext' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'rssfeed_textlength' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'maxnews' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"10","Extra"=>""),
    'maxnews_sidebar' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"3","Extra"=>""),
    'newssidebar_textlength' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'defaultpage' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"home","Extra"=>""),
    'pageurl' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'commentMinLength' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"10","Extra"=>""),
    'protectioncheck' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'spamFilter' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'mailRequired' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'languageFilter' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'blockLinks' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'blockWords' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'honeyPotKey' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dnsbl' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'commentsModerated' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'registration' => array("Type"=>"enum('N','A','M','D')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'registrationQuestion' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'registrationBadEmail' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'registrationBadIP' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['page_terms'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'language' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'name' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'search_name' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'type' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'sub' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'count' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['page_terms_used'] = array(
    'page_id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"0","Extra"=>""),
    'term_id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"0","Extra"=>""),
    'language_id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"0","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['resellerdata'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'useractive' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'ips' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxuser' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxgserver' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxvoserver' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxdedis' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxvserver' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxuserram' => array("Type"=>"int(10)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxusermhz' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'resellersid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>"")
);

$defined['resellerimages'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'distro' => array("Type"=>"varchar(50)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'bitversion' => array("Type"=>"smallint(2) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'pxelinux' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>"")
);

$defined['rserverdata'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'hyperthreading' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cores' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"4","Extra"=>""),
    'hostid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'altips' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"blob","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'user' => array("Type"=>"blob","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'pass' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'steamAccount' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'steamPassword' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'os' => array("Type"=>"enum('W','L')","Null"=>"YES","Key"=>"L","Default"=>"","Extra"=>""),
    'bitversion' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'ram' => array("Type"=>"int(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ftpport' => array("Type"=>"smallint(5) unsigned","Null"=>"NO","Key"=>"","Default"=>"21","Extra"=>""),
    'publickey' => array("Type"=>"enum('B','Y','N')","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'keyname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxslots' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxserver' => array("Type"=>"smallint(4) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'updates' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"1","Extra"=>""),
    'updateMinute' => array("Type"=>"smallint(2) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'alreadyStartedAt' => array("Type"=>"smallint(2) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'sourceSystemID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['rservermasterg'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'serverid' => array("Type"=>"varchar(11)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'servertypeid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'localVersion' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'installing' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'updating' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'installstarted' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['serverlist'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'switchID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'servertype' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'anticheat' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"1","Extra"=>""),
    'servertemplate' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"1","Extra"=>""),
    'fps' => array("Type"=>"varchar(5)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'map' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mapGroup' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'workShop' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'workshopCollection' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'webapiAuthkey' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cmd' => array("Type"=>"text","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'modcmd' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'tic' => array("Type"=>"varchar(5)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'gamemod' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'gamemod2' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'owncmd' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'userfps' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'usertick' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'usermap' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'userconfig' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'user_uploaddir' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'upload' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'uploaddir' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);
$defined['servertypes'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'steamgame' => array("Type"=>"enum('N','S')","Null"=>"NO","Key"=>"","Default"=>"S","Extra"=>""),
    'appID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'steamVersion' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'updates' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"1","Extra"=>""),
    'shorten' => array("Type"=>"varchar(20)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'type' => array("Type"=>"varchar(12)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'gamebinary' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'gamebinaryWin' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'binarydir' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'modfolder' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'fps' => array("Type"=>"varchar(5)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'slots' => array("Type"=>"int(11) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'map' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mapGroup' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cmd' => array("Type"=>"text","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'modcmds' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'tic' => array("Type"=>"varchar(5)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'gameq' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'gamemod' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'gamemod2' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'configs' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'configedit' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'iptables' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'protectedSaveCFGs' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'portStep' => array("Type"=>"smallint(4) unsigned","Null"=>"NO","Key"=>"","Default"=>"100","Extra"=>""),
    'portMax' => array("Type"=>"smallint(1) unsigned","Null"=>"NO","Key"=>"","Default"=>"4","Extra"=>""),
    'portOne' => array("Type"=>"smallint(5) unsigned","Null"=>"NO","Key"=>"","Default"=>"27015","Extra"=>""),
    'portTwo' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"27016","Extra"=>""),
    'portThree' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"27017","Extra"=>""),
    'portFour' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"27018","Extra"=>""),
    'portFive' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"27019","Extra"=>""),
    'protected' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'ramLimited' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'ftpAccess' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'workShop' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'os' => array("Type"=>"enum('B','L','W')","Null"=>"YES","Key"=>"","Default"=>"L","Extra"=>""),
    'downloadPath' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['settings'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'version' => array("Type"=>"decimal(4,2)","Null"=>"YES","Key"=>"","Default"=>"4.40","Extra"=>""),
    'releasenotesDE' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'releasenotesEN' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'language' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'template' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"default","Extra"=>""),
    'imageserver' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cronjob_ips' => array("Type"=>"text","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'licence' => array("Type"=>"text","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'master' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'voice_autobackup' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'voice_autobackup_intervall' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"5","Extra"=>""),
    'voice_maxbackup' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"5","Extra"=>""),
    'prefix1' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'prefix2' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'faillogins' => array("Type"=>"smallint(2) unsigned","Null"=>"NO","Key"=>"","Default"=>"5","Extra"=>""),
    'brandname' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'timezone' => array("Type"=>"varchar(3)","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'email' => array("Type"=>"varchar(50)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'email_settings_host' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'email_settings_password' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'email_settings_port' => array("Type"=>"int(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"143","Extra"=>""),
    'email_settings_ssl' => array("Type"=>"enum('N','S','T')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'email_settings_type' => array("Type"=>"enum('P','S','D')","Null"=>"YES","Key"=>"","Default"=>"P","Extra"=>""),
    'email_settings_user' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailregards' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailfooter' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailbackup' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailbackuprestore' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emaildown' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emaildownrestart' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailgserverupdate' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailpwrecovery' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailregister' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailsecuritybreach' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailnewticket' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailuseradd' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailvinstall' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'emailvrescue' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'supportnumber' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'noservertag' => array("Type"=>"smallint(1) unsigned","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'nopassword' => array("Type"=>"smallint(1) unsigned","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'tohighslots' => array("Type"=>"smallint(1) unsigned","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'paneldomain' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'down_checks' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"2","Extra"=>""),
    'lastUpdateRun' => array("Type"=>"smallint(2) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lastCronStatus' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lastCronWarnStatus' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'lastCronReboot' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lastCronWarnReboot' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'lastCronUpdates' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lastCronWarnUpdates' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'lastCronJobs' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lastCronWarnJobs' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'lastCronCloud' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lastCronWarnCloud' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['tickets'] = array(
    'id' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'writedate' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'topic' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'userid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'rating' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'comment' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'priority' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'userPriority' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'supporter' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'state' => array("Type"=>"enum('A','C','D','N','P','R')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerid' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>"")
);

$defined['tickets_text'] = array(
    'ticketID' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'writeDate' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'message' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>"")
);

$defined['ticket_topics'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'topic' => array("Type"=>"varchar(30)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'maintopic' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'priority' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['traffic_data'] = array(
    'id' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'serverid' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'in' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'out' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'day' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'userid' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['traffic_data_day'] = array(
    'id' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'serverid' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'in' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'out' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'day' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'userid' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['traffic_settings'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'type' => array("Type"=>"varchar(30)","Null"=>"NO","Key"=>"","Default"=>"mysql","Extra"=>""),
    'statip' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dbname' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dbuser' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dbpassword' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'multiplier' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"10","Extra"=>""),
    'table_name' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'column_sourceip' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'column_destip' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'column_byte' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'column_date' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'text_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'text_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'text_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'barin_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'barin_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"206","Extra"=>""),
    'barin_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"209","Extra"=>""),
    'barout_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'barout_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"191","Extra"=>""),
    'barout_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"255","Extra"=>""),
    'bartotal_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"30","Extra"=>""),
    'bartotal_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"144","Extra"=>""),
    'bartotal_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"255","Extra"=>""),
    'bg_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"240","Extra"=>""),
    'bg_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"240","Extra"=>""),
    'bg_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"255","Extra"=>""),
    'border_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'border_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'border_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'line_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"220","Extra"=>""),
    'line_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"220","Extra"=>""),
    'line_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"220","Extra"=>"")
);

$defined['translations'] = array(
    'type' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'lang' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'transID' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"0","Extra"=>""),
    'text' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>"")
);

$defined['userdata'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'creationTime' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'updateTime' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N','R')","Null"=>"NO","Key"=>"MUL","Default"=>"Y","Extra"=>""),
    'salutation' => array("Type"=>"int(1)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cname' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'security' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'salt' => array("Type"=>"varchar(32)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'token' => array("Type"=>"varchar(32)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'name' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'vname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'birthday' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mail' => array("Type"=>"varchar(50)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'phone' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'fax' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'handy' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'country' => array("Type"=>"varchar(2)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'city' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cityn' => array("Type"=>"varchar(6)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'street' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'streetn' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'fdlpath' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ftpbackup' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'language' => array("Type"=>"varchar(2)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lastlogin' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'logintime' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'accounttype' => array("Type"=>"varchar(1)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mail_backup' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'mail_gsupdate' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'mail_securitybreach' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'mail_serverdown' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'mail_ticket' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'mail_vserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'show_help_text' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'jobPending' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'sourceSystemID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

#https://github.com/easy-wi/developer/issues/1
$defined['userdata_social_identities'] = array(
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'serviceProviderID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'serviceUserID' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

#https://github.com/easy-wi/developer/issues/1
$defined['userdata_social_identities_substitutes'] = array(
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'serviceProviderID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'serviceUserID' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['userdata_social_providers'] = array(
    'serviceProviderID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'identifier' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'token' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'filename' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

#https://github.com/easy-wi/developer/issues/2
$defined['userdata_substitutes'] = array(
    'sID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'loginName' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'name' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'vname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'passwordHashed' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'salt' => array("Type"=>"varchar(32)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'language' => array("Type"=>"varchar(2)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'show_help_text' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'lastlogin' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'logintime' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'sourceSystemID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['userdata_substitutes_servers'] = array(
    'sID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'oType' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'oID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['userdata_groups'] = array(
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'groupID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

#https://github.com/easy-wi/developer/issues/5
$defined['userdata_value_log'] = array(
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'date' => array("Type"=>"datetime","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'json' => array("Type"=>"text","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['usergroups'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'defaultgroup' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'name' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'grouptype' => array("Type"=>"enum('a','r','u')","Null"=>"YES","Key"=>"","Default"=>"u","Extra"=>""),
    'root' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'miniroot' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'settings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'log' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'ipBans' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'updateEW' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'feeds' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'jobs' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'apiSettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cms_settings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cms_pages' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cms_news' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cms_comments' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'mysql_settings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'mysql' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'user' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'user_users' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'userGroups' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'userPassword' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'roots' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'masterServer' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'gserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'eac' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'gimages' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'addons' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'restart' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'gsResetting' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'modfastdl' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'fastdl' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'useraddons' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'usersettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'ftpaccess' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'tickets' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'usertickets' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'webmaster' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'webvhost' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'addvserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'modvserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'delvserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'usevserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'vserversettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'dhcpServer' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'pxeServer' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'dedicatedServer' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellertemplates' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'vserverhost' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'lendserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'lendserverSettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'voicemasterserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'voiceserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'voiceserverStats' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'voiceserverSettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'ftpbackup' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'traffic' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'trafficsettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['userlog'] = array(
    'id' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'userid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'subuser' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'reseller' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'username' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'usertype' => array("Type"=>"varchar(12)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'useraction' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'hostname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'logdate' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>"")
);

$defined['userpermissions'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'userid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'root' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'miniroot' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'settings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'log' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'ipBans' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'updateEW' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'jobs' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'apiSettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cms_settings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cms_pages' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cms_news' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cms_comments' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'mysql_settings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'mysql' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'user' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'user_users' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'userGroups' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'roots' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'masterServer' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'gserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'eac' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'gimages' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'addons' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'feeds' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'restart' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'gsResetting' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'modfastdl' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'fastdl' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'useraddons' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'usersettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'ftpaccess' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'tickets' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'usertickets' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'addvserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'modvserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'delvserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'usevserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'vserversettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'dhcpServer' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'pxeServer' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellertemplates' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'vserverhost' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'lendserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'lendserverSettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'voicemasterserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'voiceserver' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'voiceserverStats' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'voiceserverSettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'ftpbackup' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'traffic' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'trafficsettings' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['virtualcontainer'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'imageid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'userid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'hostid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'pxeID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'ips' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mac' => array("Type"=>"varchar(17)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"blob","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'pass' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cores' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'minmhz' => array("Type"=>"smallint(5) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'maxmhz' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'hddsize' => array("Type"=>"smallint(4) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'mountpoint' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'ram' => array("Type"=>"varchar(5)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'minram' => array("Type"=>"varchar(6)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxram' => array("Type"=>"varchar(6)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'status' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'initialInstallPending' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'jobPending' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['virtualhosts'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'esxi' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"blob","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'user' => array("Type"=>"blob","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'pass' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'os' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'publickey' => array("Type"=>"enum('B','Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'keyname' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cpu' => array("Type"=>"varchar(30)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'cores' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'mhz' => array("Type"=>"smallint(5) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'hdd' => array("Type"=>"text","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'ram' => array("Type"=>"varchar(5)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'maxserver' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'thin' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'thinquota' => array("Type"=>"smallint(2) unsigned","Null"=>"YES","Key"=>"","Default"=>"50","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['voice_dns'] = array(
    'dnsID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'dns' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'tsdnsID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'jobPending' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

// https://github.com/easy-wi/developer/issues/36 managedServer,managedForID added
$defined['voice_masterserver'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'description' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'type' => array("Type"=>"varchar(30)","Null"=>"NO","Key"=>"","Default"=>"ts3","Extra"=>""),
    'usedns' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'tsdnsServerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'externalDefaultDNS' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'defaultdns' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'defaultname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'defaultwelcome' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'defaulthostbanner_url' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'defaulthostbanner_gfx_url' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'defaulthostbutton_tooltip' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'defaulthostbutton_url' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'defaulthostbutton_gfx_url' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'defaultFlexSlotsFree' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"5","Extra"=>""),
    'defaultFlexSlotsPercent' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"80","Extra"=>""),
    'queryport' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'querypassword' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'filetransferport' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxserver' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxslots' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'rootid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'addedby' => array("Type"=>"smallint(1) unsigned","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'publickey' => array("Type"=>"enum('B','Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'ssh2ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"64","Extra"=>""),
    'ips' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ssh2port' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ssh2user' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ssh2password' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'bitversion' => array("Type"=>"smallint(2) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'serverdir' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'keyname' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'autorestart' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'sourceSystemID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'managedServer' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'managedForID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['voice_server'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'autoRestart' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'backup' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'lendserver' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'userid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'masterserver' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'ip' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'slots' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"","Default"=>"50","Extra"=>""),
    'initialpassword' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'password' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'forcebanner' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'forcebutton' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'forceservertag' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'forcewelcome' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'flexSlots' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'flexSlotsFree' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"10","Extra"=>""),
    'flexSlotsPercent' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"80","Extra"=>""),
    'flexSlotsCurrent' => array("Type"=>"int(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'max_download_total_bandwidth' => array("Type"=>"bigint(19)","Null"=>"YES","Key"=>"","Default"=>"65536","Extra"=>""),
    'max_upload_total_bandwidth' => array("Type"=>"bigint(19)","Null"=>"YES","Key"=>"","Default"=>"65536","Extra"=>""),
    'localserverid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'dns' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'usedslots' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'uptime' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxtraffic' => array("Type"=>"bigint(19)","Null"=>"YES","Key"=>"","Default"=>"2048","Extra"=>""),
    'filetraffic' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lastfiletraffic' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'serverCreated' => array("Type"=>"date","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'queryName' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'queryNumplayers' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'queryMaxplayers' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'queryPassword' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'queryUpdatetime' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'sourceSystemID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'jobPending' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['voice_server_backup'] = array(
    'id' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'sid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'uid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'name' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'snapshot' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'channels' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'date' => array("Type"=>"datetime","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['voice_server_stats'] = array(
    'sid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'date' => array("Type"=>"date","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>""),
    'mid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'installed' => array("Type"=>"decimal(6,2) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'used' => array("Type"=>"decimal(6,2) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'traffic' => array("Type"=>"bigint(19) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'uid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'count' => array("Type"=>"bigint(19) unsigned","Null"=>"YES","Key"=>"","Default"=>"1","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['voice_stats_settings'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'text_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'text_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'text_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'barin_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'barin_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"206","Extra"=>""),
    'barin_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"209","Extra"=>""),
    'barout_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'barout_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"191","Extra"=>""),
    'barout_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"255","Extra"=>""),
    'bartraffic_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'bartraffic_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'bartraffic_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'bg_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"240","Extra"=>""),
    'bg_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"240","Extra"=>""),
    'bg_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"255","Extra"=>""),
    'border_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'border_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'border_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'line_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"220","Extra"=>""),
    'line_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"220","Extra"=>""),
    'line_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"220","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

$defined['voice_tsdns'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'max_dns' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'defaultdns' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'rootid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'publickey' => array("Type"=>"enum('B','Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'ssh2ip' => array("Type"=>"varchar(15)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ssh2port' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ssh2user' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ssh2password' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'bitversion' => array("Type"=>"smallint(2) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'serverdir' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'keyname' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'autorestart' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'description' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);

foreach ($defined as $table => $t_p) {

    $query = $sql->prepare("SHOW TABLE STATUS LIKE '${table}'");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        if ($row['Engine'] == 'MyISAM') {

            $sqlStatement = "ALTER TABLE `${table}` ENGINE = InnoDB";
            $query2 = $sql->prepare($sqlStatement);
            $query2->execute();

            $response->add($sqlStatement.'<br />');
        }

        if ($row['Collation'] != 'utf8_general_ci') {

            $sqlStatement = "ALTER TABLE `${table}` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
            $query2 = $sql->prepare($sqlStatement);
            $query2->execute();

            $response->add($sqlStatement.'<br />');
        }
    }

    if ($query->rowCount() == 0) {

        $response->add('<b>Error: no such table: ' . $table . '</b><br />');

    } else {

        $key_differ = array();
        $drop_key = array();
        $add_keys = array();
        $change = array();
        $addIndex = array();
        $removeIndex = array();

        $keys_should_exist = array_keys($t_p);

        $query = $sql->prepare("SHOW COLUMNS FROM `${table}`");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $array = '';
            $Field = $row['Field'];

            $unset = array_search($Field, $keys_should_exist);
            if ($unset !== false) {
                unset($keys_should_exist[$unset]);
            }

            if (isset($t_p[$Field])) {

                $properties = $t_p[$Field];

                foreach ($row as $key => $value) {
                    if ($key != 'Field' and $key != 'Key' and !in_array($Field, $key_differ) and $properties[$key] != $value) {
                        $key_differ[] = $Field;
                    } else if ($key == 'Key' and $value == '' and $properties['Key'] == 'MUL') {
                        $addIndex[] = $Field;
                    } else if ($key == 'Key' and $value == 'MUL' and $properties['Key'] == '') {
                        $removeIndex[] = $Field;
                    }
                }

            } else {
                $drop_key[] = 'DROP `' . $Field . '`';
            }
        }

        foreach ($key_differ as $key) {

            $NULL = ($t_p[$key]['Null'] == 'NO') ? 'NOT NULL' : 'NULL';
            $AUTO_INCREMENT = ($t_p[$key]['Extra'] == '') ? '' : ' AUTO_INCREMENT';

            if ($t_p[$key]['Default'] == 'CURRENT_TIMESTAMP') {
                $DEFAULT = "DEFAULT CURRENT_TIMESTAMP";
            } else if ($t_p[$key]['Default'] != '') {
                $DEFAULT = "DEFAULT '" . $t_p[$key]['Default'] . "'";
            } else {
                $DEFAULT = '';
            }

            $change[] = 'CHANGE `' . $key . '` `' . $key . '` ' . $t_p[$key]['Type'] . ' ' . $NULL . ' ' . $DEFAULT . $AUTO_INCREMENT;
        }

        if (count($change) > 0) {

            $alter_query = 'ALTER TABLE `' . $table . '` ' . implode(', ', $change);
            $alter = $sql->prepare($alter_query);
            $alter->execute();

            $response->add('CHANGE: ' . $alter_query . '<br />');
        }

        if (count($drop_key) > 0) {

            $drop_query='ALTER TABLE `' . $table . '` ' . implode(', ', $drop_key);
            $drop = $sql->prepare($drop_query);
            $drop->execute();

            $response->add('DROP: ' . $drop_query . '<br />');
        }

        foreach ($keys_should_exist as $key) {

            $i = 0;
            $current = current($t_p);
            $current_key = array_search($current, $t_p);

            while ($current !== false and $current_key != $key) {
                $prev = $current_key;
                $current = next($t_p);
                $current_key = array_search($current, $t_p);
            }

            $AFTER = (isset($prev)) ? ' AFTER `' . $prev . '`' : '';

            $next = array_search(next($t_p), $t_p);
            $before = array_search(prev($t_p), $t_p);

            $NULL = ($t_p[$key]['Null'] == 'NO') ? 'NOT NULL' : 'NULL';
            $DEFAULT = ($t_p[$key]['Default'] == '') ? '' : "DEFAULT '" . $t_p[$key]['Default'] . "'";

            $add_keys[] = 'ADD COLUMN `' . $key . '` ' . $t_p[$key]['Type'] . ' ' . $NULL . ' ' . $DEFAULT . $AFTER;
        }

        if (count($add_keys) > 0) {

            $add_query = 'ALTER TABLE `' . $table . '` ' . implode(', ', $add_keys);
            $add = $sql->prepare($add_query);
            $add->execute();

            $response->add('ADD: ' . $add_query . '<br />');
        }

        if (count($addIndex) > 0) {

            $add_query = 'ALTER TABLE `' . $table . '` ADD INDEX(`' . implode('`),ADD INDEX(`', $addIndex) . '`)';
            $add = $sql->prepare($add_query);
            $add->execute();

            $response->add('ADD: ' . $add_query . '<br />');
        }

        if (count($removeIndex) > 0) {

            $remove_query = 'ALTER TABLE `' . $table . '` DROP INDEX `' . implode('`,DROP INDEX `', $removeIndex) . '`';
            $remove = $sql->prepare($remove_query);
            $remove->execute();

            $response->add('ADD: ' . $remove_query . '<br />');
        }
    }
}