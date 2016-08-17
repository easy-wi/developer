<?php

/**
 * File: table_settings.php.
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

$defined['settings'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'version' => array("Type"=>"varchar(10)","Null"=>"YES","Key"=>"","Default"=>"5.30","Extra"=>""),
    'favicon' => array("Type"=>"varchar(100)","Null"=>"YES","Key"=>"","Default"=>"images/favicon.ico","Extra"=>""),
    'header_icon' => array("Type"=>"varchar(100)","Null"=>"YES","Key"=>"","Default"=>"logo_180px.png","Extra"=>""),
    'header_text' => array("Type"=>"varchar(100)","Null"=>"YES","Key"=>"","Default"=>"Easy-Wi","Extra"=>""),
    'header_href' => array("Type"=>"varchar(100)","Null"=>"YES","Key"=>"","Default"=>"https://easy-wi.com","Extra"=>""),
    'language' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'template' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"default","Extra"=>""),
    'templateColor' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"blue","Extra"=>""),
    'imageserver' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cronjob_ips' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'master' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'voice_autobackup' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'voice_autobackup_intervall' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"5","Extra"=>""),
    'voice_maxbackup' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"5","Extra"=>""),
    'prefix1' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'prefix2' => array("Type"=>"varchar(15)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'faillogins' => array("Type"=>"smallint(2) unsigned","Null"=>"NO","Key"=>"","Default"=>"5","Extra"=>""),
    'brandname' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'timezone' => array("Type"=>"varchar(3)","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'supportnumber' => array("Type"=>"varchar(100)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'noservertag' => array("Type"=>"smallint(1) unsigned","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'nopassword' => array("Type"=>"smallint(1) unsigned","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'tohighslots' => array("Type"=>"smallint(1) unsigned","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'paneldomain' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'down_checks' => array("Type"=>"smallint(5) unsigned","Null"=>"YES","Key"=>"","Default"=>"2","Extra"=>""),
    'developer' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
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
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'login_header_text' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"Easy-Wi","Extra"=>"")
);