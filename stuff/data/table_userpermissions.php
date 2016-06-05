<?php

/**
 * File: table_userpermissions.php.
 * Author: Ulrich Block
 * Date: 17.10.15
 * Time: 15:49
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

$defined['userpermissions'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'userid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>""),
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