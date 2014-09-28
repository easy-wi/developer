<?php

/**
 * File: app_master_port_best.php.
 * Author: Ulrich Block
 * Date: 27.09.14
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

if (!defined('AJAXINCLUDED')) {
    die('Do not access directly!');
}

$sprache = getlanguagefile('gserver', $user_language, $resellerLockupID);

$used = usedPorts(array($ui->ip4('ip', 'get')));
$ports = $used['ports'];
$port = '';
$port2 = '';
$port3 = '';
$port4 = '';
$port5 = '';
$portStep = false;
$portMax = false;

$query = $sql->prepare("SELECT * FROM `servertypes` WHERE `id`=? AND `resellerid`=? LIMIT 1");
$query->execute(array($ui->id('id', 10, 'get'), $resellerLockupID));
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $portStep = $row['portStep'];
    $portMax = $row['portMax'];
    $port = $row['portOne'];
    $port2 = $row['portTwo'];
    $port3 = $row['portThree'];
    $port4 = $row['portFour'];
    $port5 = $row['portFive'];
}

if ($portMax > 0) {
    while (in_array($port, $ports)) {
        $port += $portStep;
    }
} else {
    $port2 = '';
    $port3 = '';
    $port4 = '';
    $port5 = '';
}

if ($portMax > 1) {
    while (in_array($port2, $ports)) {
        $port2 += $portStep;
    }
} else {
    $port3 = '';
    $port4 = '';
    $port5 = '';
}

if ($portMax > 2) {
    while (in_array($port3, $ports)) {
        $port3 += $portStep;
    }
} else {
    $port4 = '';
    $port5 = '';
}

if ($portMax > 3) {
    while (in_array($port4, $ports)) {
        $port4 += $portStep;
    }
} else {
    $port5 = '';
}

if ($portMax > 4) {
    while (in_array($port5, $ports)) {
        $port5 += $portStep;
    }
}

require_once IncludeTemplate($template_to_use, 'ajax_admin_appmaster_ports_best.tpl', 'ajax');