<?php

/**
 * File: table_gsswitch.php.
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

$defined['gsswitch'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'description' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'type' => array("Type"=>"enum('A','G')","Null"=>"NO","Key"=>"","Default"=>"G","Extra"=>""),
    'autoRestart' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'updateRestart' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'userid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'rootID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'homeLabel' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"home","Extra"=>""),
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
    'psince' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'serverip' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port2' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port3' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port4' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port5' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'minram' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'maxram' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'slots' => array("Type"=>"smallint(4) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'masterfdl' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'mfdldata' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'taskset' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cores' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'secnotified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'newlayout' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'queryName' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'queryNumplayers' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"0","Extra"=>""),
    'queryMaxplayers' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"0","Extra"=>""),
    'queryMap' => array("Type"=>"varchar(40)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'queryPassword' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'queryUpdatetime' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"CURRENT_TIMESTAMP","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'sourceSystemID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'jobPending' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'hdd' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'hddUsage' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);