<?php

/**
 * File: table_page_settings.php.
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

$defined['page_settings'] = array(
    'id' => array("Type"=>"int(10) unsigned","Null"=>"NO","Key"=>"PRI","Default"=>"","Extra"=>"auto_increment"),
    'seo' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'rssfeed' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'rssfeed_fulltext' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'rssfeed_textlength' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'maxnews' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"10","Extra"=>""),
    'maxnews_sidebar' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"3","Extra"=>""),
    'newssidebar_textlength' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"200","Extra"=>""),
    'defaultpage' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"home","Extra"=>""),
    'pageurl' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'commentMinLength' => array("Type"=>"int(11) unsigned","Null"=>"YES","Key"=>"","Default"=>"10","Extra"=>""),
    'protectioncheck' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'spamFilter' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'mailRequired' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'languageFilter' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'blockLinks' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'blockWords' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'honeyPotKey' => array("Type"=>"varchar(255)","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'dnsbl' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'commentsModerated' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'registration' => array("Type"=>"enum('N','A','M','D')","Null"=>"YES","Key"=>"","Default"=>"N","Extra"=>""),
    'registrationQuestion' => array("Type"=>"enum('Y','N')","Null"=>"YES","Key"=>"","Default"=>"Y","Extra"=>""),
    'registrationBadEmail' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'registrationBadIP' => array("Type"=>"text","Null"=>"YES","Key"=>"","Default"=>"","Extra"=>""),
    'resellerid' => array("Type"=>"int(10) unsigned","Null"=>"YES","Key"=>"MUL","Default"=>"0","Extra"=>"")
);