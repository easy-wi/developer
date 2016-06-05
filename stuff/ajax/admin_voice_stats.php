<?php

/**
 * File: admin_voice_stats.php.
 * Author: Ulrich Block
 * Date: 17.01.15
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

$data = array();

if ($ui->st('w', 'get') == 'us') {
    $query = $sql->prepare("SELECT u.`id`,u.`cname`,u.`vname`,u.`name` FROM `userdata` u INNER JOIN `voice_server` v ON u.`id`=v.`userid` AND v.`active`='Y' WHERE u.`resellerid`=? GROUP BY u.`id`");
    $query->execute(array($resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $selected = ($ui->id('selectedID', 10, 'get') == $row['id']) ? ' selected="selected"' : '';
        $data[] = '<option value=' . $row['id'] . $selected . '>' . trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']) . '</option>';
    }

} else if ($ui->st('w', 'get') == 'se') {

    $query = $sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,v.`dns`,m.`usedns` FROM `voice_server` v INNER JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`resellerid`=? ORDER BY v.`ip`,v.`port`");
    $query->execute(array($resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $selected = ($ui->id('selectedID', 10, 'get') == $row['id']) ? ' selected="selected"' : '';
        $data[] = '<option value=' . $row['id'] . $selected . '>' . $row['ip'] . ':' . $row['port'] . '</option>';
    }

} else if ($ui->st('w', 'get') == 'ma') {

    $query = $sql->prepare("SELECT `id`,`ssh2ip` FROM `voice_masterserver` WHERE `resellerid`=? AND `active`='Y' ORDER BY `ssh2ip`");
    $query->execute(array($resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $selected = ($ui->id('selectedID', 10, 'get') == $row['id']) ? ' selected="selected"' : '';
        $data[] = '<option value=' . $row['id'] . $selected . '>' . $row['ssh2ip'] . '</option>';
    }

}

require_once IncludeTemplate($template_to_use, 'ajax_admin_voice_stats.tpl', 'ajax');