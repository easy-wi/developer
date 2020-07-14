<?php

/**
 * File: config.php.
 * Author: Ulrich Block
 * Date: 05.03.16
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

$templateColors = array(
    'default' => 'blue',
    'colors' => array(
        'black',
        'black-light',
        'blue',
        'blue-light',
        'green',
        'green-light',
        'purple',
        'purple-light',
        'red',
        'red-light',
        'yellow',
        'yellow-light'
    )
);

$mailkontakt = ""; // Email displayed for Users  when NO Email is written here .. nothing will be displayed! 
$pingcfg = '0'; // If you want to use the Ping Function use '1' if you have more then 5 Servers for each user we recommend to turn the function off with '0'!
$location = '1'; //If you dont want to display the Location of your server set this to '0' otherwise to '1'.
$hideinfo = '0'; //Do you want to Hide Server informations? (e.g. FTP User / Map / Players ...) and make them accessable with a button? (Recommended for Large Server lists!) 1=ON 0=OFF  3=everything below is hidden
$gsnavigation = '1'; // 1 = Gameserver Menu above the Informations (Vanilla) | 2 = Gameserver Menu below the Informations  | 3 = Pictures as Buttons provided by n0miy0 PeekSv.eu#1497
 

?>      
