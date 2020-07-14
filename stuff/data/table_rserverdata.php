<?php

/**
 * File: table_rserverdata.php.
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

$defined['rserverdata'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'hyperthreading' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'cores' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"4","Extra"=>""),
    'hostid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'connect_ip_only' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'ip' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'altips' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'port' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'user' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'pass' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'steamAccount' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'steamPassword' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'os' => array("Type"=>"enum('W','L')","Null"=>"YES","Key"=>"L","Default"=>"","Extra"=>""),
    'bitversion' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'ram' => array("Type"=>"int(7) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'description' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'ftpport' => array("Type"=>"smallint(5) unsigned","Null"=>"NO","Key"=>"","Default"=>"21","Extra"=>""),
    'publickey' => array("Type"=>"enum('B','Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'keyname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxslots' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'maxserver' => array("Type"=>"smallint(4) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'install_paths' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'quota_active' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'quota_cmd' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'repquota_cmd' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'blocksize' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"4096","Extra"=>""),
    'inode_block_ratio' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"4","Extra"=>""),
    'config_log_time' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"7","Extra"=>""),
    'config_demo_time' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"365","Extra"=>""),
    'config_ztmp_time' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'config_bad_time' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'config_user_id' => array("Type"=>"smallint(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"1000","Extra"=>""),
    'config_ionice' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'config_binaries' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"srcds_run,srcds_linux,hlds_run,hlds_amd,hlds_i686,ucc-bin,ucc-bin-real","Extra"=>""),
    'config_files' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"*/cfg/valve.rc","Extra"=>""),
    'config_bad_files' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"zip,rar,7zip,bz2","Extra"=>""),
    'updates' => array("Type"=>"smallint(1) unsigned","Null"=>"YES","Key"=>"","Default"=>"1","Extra"=>""),
    'updateMinute' => array("Type"=>"smallint(2) unsigned","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'alreadyStartedAt' => array("Type"=>"smallint(2) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'notified' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"MUL","Default"=>"","Extra"=>""),
    'sourceSystemID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);