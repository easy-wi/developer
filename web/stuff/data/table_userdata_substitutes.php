<?php

/**
 * File: table_userdata_substitutes.php.
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

#https://github.com/easy-wi/developer/issues/2
$defined['userdata_substitutes'] = array(
    'sID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'userID' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'active' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'loginName' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"MUL","Default"=>"","Extra"=>""),
    'name' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'vname' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'passwordHashed' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'salt' => array("Type"=>"varchar(32)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'language' => array("Type"=>"varchar(2)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'show_help_text' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'lastlogin' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'logintime' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"CURRENT_TIMESTAMP","Extra"=>""),
    'externalID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'sourceSystemID' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);