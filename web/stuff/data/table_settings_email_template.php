<?php
/**
 * File: table_settings_email_template.php
 * Author: Daniel Rodriguez Baumann
 * Date: 15.04.2016
 * Contact: <daniel@triopsi.com>
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
$defined['settings_email_template'] = array(
    'id' => array("Type"=>"int(11) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'reseller_id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"","Default"=>"0","Extra"=>""),
    'language' => array("Type"=>"varchar(2)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'active' => array("Type"=>"varchar(1)","Null"=>"NO","Key"=>"","Default"=>"Y","Extra"=>""),
    'category' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'email_setting_name' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'subject' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'email_body' => array("Type"=>"text","Null"=>"No","Key"=>"","Default"=>"","Extra"=>""),
    'ccmailing' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'bccmailing' => array("Type"=>"varchar(255)","Null"=>"NO","Key"=>"","Default"=>"","Extra"=>""),
    'attachments' => array("Type"=>"text","Null"=>"No","Key"=>"","Default"=>"","Extra"=>"")
);