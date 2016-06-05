<?php

/**
 * File: table_traffic_settings.php.
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

$defined['traffic_settings'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'type' => array("Type"=>"varchar(30)","Null"=>"NO","Key"=>"","Default"=>"mysql","Extra"=>""),
    'statip' => array("Type"=>"varchar(50)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dbname' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dbuser' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dbpassword' => array("Type"=>"blob","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'multiplier' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"","Default"=>"10","Extra"=>""),
    'table_name' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'column_sourceip' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'column_destip' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'column_byte' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'column_date' => array("Type"=>"varchar(30)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'text_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'text_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'text_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'barin_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'barin_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"206","Extra"=>""),
    'barin_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"209","Extra"=>""),
    'barout_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"0","Extra"=>""),
    'barout_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"191","Extra"=>""),
    'barout_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"255","Extra"=>""),
    'bartotal_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"30","Extra"=>""),
    'bartotal_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"144","Extra"=>""),
    'bartotal_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"255","Extra"=>""),
    'bg_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"240","Extra"=>""),
    'bg_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"240","Extra"=>""),
    'bg_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"255","Extra"=>""),
    'border_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'border_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'border_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'line_colour_1' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"220","Extra"=>""),
    'line_colour_2' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"220","Extra"=>""),
    'line_colour_3' => array("Type"=>"smallint(3) unsigned","Null"=>"YES","Key"=>"","Default"=>"220","Extra"=>"")
);