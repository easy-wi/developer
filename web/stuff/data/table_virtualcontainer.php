<?php

/**
 * File: table_virtualcontainer.php.
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
    'port' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
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