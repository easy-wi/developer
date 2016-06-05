<?php

/**
 * File: table_userdata.php.
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

$defined['userdata'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'creationTime' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'updateTime' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"CURRENT_TIMESTAMP","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N','R')","Null"=>"NO","Key"=>"MUL","Default"=>"Y","Extra"=>""),
    'salutation' => array("Type"=>"int(1)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'cname' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'security' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'salt' => array("Type"=>"varchar(32)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'token' => array("Type"=>"varchar(32)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'name' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'vname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'birthday' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
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
    'lastlogin' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'logintime' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
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