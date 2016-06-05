<?php

/**
 * File: table_easywi_statistics_current.php.
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
    'webMasterCrashed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webMasterSpaceAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webMasterVhostAvailable' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceInstalled' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceSpaceGiven' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceSpaceGivenActive' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'webspaceSpaceUsed' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"0","Extra"=>""),
    'statDate' => array("Type"=>"timestamp","Null"=>"NO","Key"=>"","Default"=>"CURRENT_TIMESTAMP","Extra"=>"ON UPDATE CURRENT_TIMESTAMP")
);