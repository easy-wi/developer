<?php

/**
 * File: ajax.php.
 * Author: Ulrich Block
 * Date: 03.10.12
 * Time: 17:09
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

define('EASYWIDIR', dirname(__FILE__));

define('AJAXINCLUDED', true);

if (is_dir(EASYWIDIR . '/install')) {
    die('Please remove the "install" folder');
}

include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/config.php');
include(EASYWIDIR . '/stuff/settings.php');

if (!isset($admin_id) and !isset($user_id)) {
    redirect('login.php');
} else if (isset($admin_id)) {
    $pa = User_Permissions($admin_id);
} else if (isset($user_id)) {
    $pa = User_Permissions($user_id);
}

if ($ui->smallletters('w', 9, 'get') == 'datatable') {

    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 1 Jan 1900 00:00:00 GMT');
    header('Content-type: application/json');

    $array = array('iTotalRecords' => 0, 'iTotalDisplayRecords' => 0, 'aaData' => array());

    $iDisplayStart = ($ui->isinteger('iDisplayStart', 'get')) ? $ui->isinteger('iDisplayStart', 'get') : 0;
    $iDisplayLength = ($ui->isinteger('iDisplayLength', 'get')) ? $ui->isinteger('iDisplayLength', 'get') : 10;
    $iSortCol = ($ui->isinteger('iSortCol_0', 'get')) ? $ui->isinteger('iSortCol_0', 'get') : 0;
    $sSortDir = ($ui->smallletters('sSortDir_0', 4, 'get') == 'desc') ? 'DESC' : 'ASC';
    $sSearch = (strlen($ui->escaped('sSearch', 'get')) > 0) ? $ui->escaped('sSearch', 'get') : false;

    if ($ui->smallletters('d', 7, 'get') == 'userlog' and isset($user_id) and $pa['log']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_userlog.php');

    // App server
    } else if ($ui->smallletters('d', 9, 'get') =='appserver' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['roots']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_appserver.php');

    // App (GS + Tools) masterserver
    } else if ($ui->smallletters('d', 15, 'get') =='appmasterserver' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['masterServer']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_appmasterserver.php');

        // GS images
    } else if ($ui->smallletters('d', 10, 'get') =='gameimages' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['gimages']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_gameimages.php');

        // GS addons
    } else if ($ui->smallletters('d', 16, 'get') =='gameserveraddons' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['addons']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_gameaddons.php');

    // Code wise it seems odd, but this way we can get plausible userIDs for following queries up front
    } else {

        // When searching a table combination that should provide a server and user´s loginname, firstname, lastname. First do a search at usertable and get IDs.
        // This IDs should be used for doing a `ID` IN (implode(',', $foundIDs)) as it will be faster.
    }


    die(json_encode($array));

// App master server updates. Triggered asyncronous with ajax to avoid 5xx errors
} else if ($ui->smallletters('d', 21, 'get') =='masterappserverupdate' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['masterServer']) {

    require_once(EASYWIDIR . '/stuff/ajax/app_master_update.php');
    die;

} else if (isset($admin_id) and $pa['dedicatedServer'] and $ui->smallletters('d', 7, 'get') == 'freeips' and $reseller_id == 0) {

    if ($ui->id('userID', 10, 'get')) {

        $query = $sql->prepare("SELECT `resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($ui->id('userID', 10, 'get')));

        $ipsAvailable = freeips(($query->fetchColumn()));

    } else {
        $ipsAvailable = array();
    }

    require_once IncludeTemplate($template_to_use, 'ajax_admin_roots_ips.tpl', 'ajax');

    die;

} else if (isset($admin_id) and $pa['fastdl'] and $ui->smallletters('d', 8, 'get') == 'webmaster' and $ui->id('id', 10, 'get')) {

    $sprache = getlanguagefile('web', $user_language, $resellerLockupID);

    $maxVhost = 0;
    $maxHDD = 0;
    $webVhosts = 0;
    $leftHDD = 0;
    $totalHDD = 0;
    $totalVhosts = 0;
    $quotaActive = 'N';
    $dns = '';

    $query = $sql->prepare("SELECT m.`vhostTemplate`,m.`maxVhost`,m.`maxHDD`,m.`quotaActive`,m.`defaultdns`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`) AS `totalVhosts`,(SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`) AS `totalHDD` FROM `webMaster` AS m WHERE m.`webMasterID`=? AND m.`resellerID`=? LIMIT 1");
    $query->execute(array($ui->id('id', 10, 'get'), $resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $vhostTemplate = $row['vhostTemplate'];
        $maxVhost = (int) $row['maxVhost'];
        $maxHDD = (int) $row['maxHDD'];
        $totalVhosts = (int) $row['totalVhosts'];
        $leftHDD = (int) $row['maxHDD'] - $row['totalHDD'];
        $quotaActive = $row['quotaActive'];
        $dns = $row['defaultdns'];
    }

    require_once IncludeTemplate($template_to_use, 'ajax_admin_web_master.tpl', 'ajax');

    die;

} else if (isset($admin_id) and $pa['voiceserverStats'] and $ui->smallletters('d', 15, 'get') == 'adminvoicestats' and $ui->st('w', 'get')) {

    $data = array();

    if ($ui->st('w', 'get') == 'us') {
        $query = $sql->prepare("SELECT u.`id`,u.`cname`,u.`vname`,u.`name` FROM `userdata` u INNER JOIN `voice_server` v ON u.`id`=v.`userid` AND v.`active`='Y' WHERE u.`resellerid`=? GROUP BY u.`id`");
        $query->execute(array($resellerLockupID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $data[] = '<option value=' . $row['id'] . '>' . trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']) . '</option>';
        }

    } else if ($ui->st('w', 'get') == 'se') {

        $query = $sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,v.`dns`,m.`usedns` FROM `voice_server` v INNER JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`resellerid`=? ORDER BY v.`ip`,v.`port`");
        $query->execute(array($resellerLockupID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $data[] = '<option value=' . $row['id'] . '>' . $row['ip'] . ':' . $row['port'] . '</option>';
        }

    } else if ($ui->st('w', 'get') == 'ma') {

        $query = $sql->prepare("SELECT `id`,`ssh2ip` FROM `voice_masterserver` WHERE `resellerid`=? AND `active`='Y' ORDER BY `ssh2ip`");
        $query->execute(array($resellerLockupID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $data[] = '<option value=' . $row['id'] . '>' . $row['ssh2ip'] . '</option>';
        }

    }

    require_once IncludeTemplate($template_to_use, 'ajax_admin_voice_stats.tpl', 'ajax');

    die;

} else if (isset($user_id) and $pa['voiceserverStats'] and $ui->smallletters('d', 14, 'get') == 'uservoicestats' and $ui->st('w', 'get')) {

    require_once(EASYWIDIR . '/stuff/ajax/userpanel_voice_stats.php');
    die;

} else if (isset($user_id) and $pa['usertickets'] and $ui->w('d', 20, 'get') == 'userTicketCategories' and $ui->id('topicName', 10, 'get')) {

    require_once(EASYWIDIR . '/stuff/ajax/userpanel_ticket_category.php');
    die;

} else if (isset($user_id) and $pa['voiceserverStats'] and $ui->w('d', 14, 'get') == 'voiceUserStats') {

    require_once(EASYWIDIR . '/stuff/ajax/stats_voicestats.php');
    die;

} else if (isset($user_id) and ($pa['gserver'] or $pa['restart']) and $ui->username('mapgroup', 50, 'get')) {

    require_once(EASYWIDIR . '/stuff/ajax/userpanel_mapgroup.php');
    die;
}

die('No Access:' . $ui->smallletters('d', 200, 'get'));