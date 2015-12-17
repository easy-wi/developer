<?php

/**
 * File: table_servertypes.php.
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

$defined['servertypes'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'steamgame' => array("Type"=>"enum('N','S')","Null"=>"NO","Key"=>"","Default"=>"S","Extra"=>""),
    'steam_account' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'steam_password' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
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
    'slots' => array("Type"=>"int(11) unsigned","Null"=>"NO","Key"=>"","Default"=>"0","Extra"=>""),
    'map' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'mapGroup' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cmd' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
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
    'useQueryPort' => array("Type"=>"smallint(1) unsigned","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'protected' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'ramLimited' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'ftpAccess' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'workShop' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'copyStartBinary' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'os' => array("Type"=>"enum('B','L','W')","Null"=>"YES","Key"=>"","Default"=>"L","Extra"=>""),
    'downloadPath' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'liveConsole' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'steamGameserverToken' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);