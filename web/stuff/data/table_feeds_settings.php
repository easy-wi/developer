<?php

/**
 * File: table_feeds_settings.php.
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
    'lastUpdate' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"CURRENT_TIMESTAMP","Extra"=>""),
    'maxKeep' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'resellerID' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'oauth_access_token' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'oauth_access_token_secret' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'consumer_key' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'consumer_secret' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>"")
);