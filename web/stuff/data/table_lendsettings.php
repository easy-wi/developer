<?php

/**
 * File: table_lendsettings.php.
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

$defined['lendsettings'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'activeGS' => array("Type"=>"enum('A','R','B','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'activeVS' => array("Type"=>"enum('A','R','B','N')","Null"=>"NO","Key"=>"","Default"=>"N","Extra"=>""),
    'userGame' => array("Type"=>"enum('A','B','R')","Null"=>"NO","Key"=>"","Default"=>"B","Extra"=>""),
    'gameVoice' => array("Type"=>"enum('A','B','R')","Null"=>"NO","Key"=>"","Default"=>"B","Extra"=>""),
    'mintime' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'maxtime' => array("Type"=>"smallint(4)","Null"=>"NO","Key"=>"","Default"=>"120","Extra"=>""),
    'timesteps' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'minplayer' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'maxplayer' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"12","Extra"=>""),
    'playersteps' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'mintimeRegistered' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'maxtimeRegistered' => array("Type"=>"smallint(4)","Null"=>"NO","Key"=>"","Default"=>"120","Extra"=>""),
    'timestepsRegistered' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'minplayerRegistered' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'maxplayerRegistered' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"12","Extra"=>""),
    'playerstepsRegistered' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'vomintime' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'vomaxtime' => array("Type"=>"smallint(4) unsigned","Null"=>"NO","Key"=>"","Default"=>"120","Extra"=>""),
    'votimesteps' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'vominplayer' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'vomaxplayer' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"12","Extra"=>""),
    'voplayersteps' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'vomintimeRegistered' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'vomaxtimeRegistered' => array("Type"=>"smallint(4) unsigned","Null"=>"NO","Key"=>"","Default"=>"120","Extra"=>""),
    'votimestepsRegistered' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"20","Extra"=>""),
    'vominplayerRegistered' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'vomaxplayerRegistered' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"12","Extra"=>""),
    'voplayerstepsRegistered' => array("Type"=>"smallint(3) unsigned","Null"=>"NO","Key"=>"","Default"=>"2","Extra"=>""),
    'shutdownempty' => array("Type"=>"enum('Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'shutdownemptytime' => array("Type"=>"smallint(3)","Null"=>"NO","Key"=>"","Default"=>"5","Extra"=>""),
    'ftpupload' => array("Type"=>"enum('A','R','Y','N')","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'ftpuploadpath' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'lendaccess' => array("Type"=>"smallint(1)","Null"=>"NO","Key"=>"","Default"=>"1","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"MUL","Default"=>"0","Extra"=>""),
    'lastcheck' => array("Type"=>"timestamp","Null"=>"NO","Key"=>"","Default"=>"CURRENT_TIMESTAMP","Extra"=>""),
    'oldcheck' => array("Type"=>"timestamp","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>"")
);