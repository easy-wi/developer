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

include(EASYWIDIR . '/stuff/config.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/methods/class_ts3.php');
include(EASYWIDIR . '/stuff/methods/functions_ts3.php');
include(EASYWIDIR . '/stuff/methods/vorlage.php');
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

    // Userlog
    if (($ui->smallletters('d', 7, 'get') == 'userlog' and isset($user_id)) or ($ui->smallletters('d', 12, 'get') == 'adminuserlog' and isset($admin_id)) and $pa['log']) {

        $adminLookup = ($ui->smallletters('d', 12, 'get') == 'adminuserlog' and isset($admin_id)) ? true : false;

        require_once(EASYWIDIR . '/stuff/ajax/datatable_userlog.php');

        // Mail log
    } else if ($ui->smallletters('d', 7, 'get') == 'maillog' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['jobs']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_maillog.php');

        // Job log
    } else if ($ui->smallletters('d', 6, 'get') == 'joblog' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['jobs']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_joblog.php');

        // IP bans
    } else if ($ui->smallletters('d', 6, 'get') == 'ipbans' and isset($admin_id) and isset($reseller_id) and $reseller_id == 0 and $pa['ipBans']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_ipbans.php');

        // custom module
    } else if ($ui->smallletters('d', 12, 'get') == 'custommodule' and isset($admin_id) and isset($reseller_id) and $reseller_id == 0 and $pa['root']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_custommodule.php');

        // List of imported news feeds
    } else if ($ui->smallletters('d', 16, 'get') == 'feedsnewsentries' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['addons']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_feedsnewsentries.php');

        // List of feeds
    } else if ($ui->smallletters('d', 5, 'get') == 'feeds' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['addons']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_feeds.php');

        // App (GS + Tools) masterserver
    } else if ($ui->smallletters('d', 15, 'get') == 'appmasterserver' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['masterServer']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_appmasterserver.php');

        // GS images
    } else if ($ui->smallletters('d', 10, 'get') == 'gameimages' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['gimages']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_gameimages.php');

        // GS addons
    } else if ($ui->smallletters('d', 16, 'get') == 'gameserveraddons' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['addons']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_gameaddons.php');

        // App server
    } else if ($ui->smallletters('d', 9, 'get') == 'appserver' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['roots']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_appserver.php');

        // Admins, reseller and user
    } else if ($ui->smallletters('d', 4, 'get') == 'user' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and ($pa['user'] or $pa['user_users'] or $pa['userPassword'])) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_user.php');

        // Voice master
    } else if ($ui->smallletters('d', 17, 'get') == 'voicemasterserver' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['voicemasterserver']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_voicemasterserver.php');

        // TSDNS master
    } else if ($ui->smallletters('d', 17, 'get') == 'tsdnsmasterserver' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['voicemasterserver']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_tsdnsmasterserver.php');

        // MySQL server
    } else if ($ui->smallletters('d', 11, 'get') == 'mysqlserver' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['mysql_settings']) {

        require_once(EASYWIDIR . '/stuff/ajax/datatable_mysqlserver.php');

        // Code wise it seems odd, but this way we can get plausible userIDs for following queries up front without having to repeat ourselves
    } else {

        // When searching a table combination that should provide a server and user´s loginname, firstname, lastname. First do a search at usertable and get IDs.
        // This IDs should be used for doing a `ID` IN (implode(',', $foundIDs)) as it will be faster.
        if ($sSearch) {

            $userIDs = array();

            $query = $sql->prepare("SELECT `id`,`cname`,CONCAT(`vname`,' ',`name`) AS `full_name` FROM `userdata` WHERE `resellerid`=:reseller_id HAVING (`cname` LIKE :search OR `full_name` LIKE :search)");
            $query->execute(array(':search' => '%' . $sSearch . '%',':reseller_id' => $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $userIDs[] = $row['id'];
            }
        }

        if ($ui->smallletters('d', 10, 'get') == 'gameserver' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['gserver']) {

            require_once(EASYWIDIR . '/stuff/ajax/datatable_gameserver.php');

        } else if ($ui->smallletters('d', 11, 'get') == 'voiceserver' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['voiceserver']) {

            require_once(EASYWIDIR . '/stuff/ajax/datatable_voiceserver.php');

        } else if ($ui->smallletters('d', 5, 'get') == 'tsdns' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['voiceserver']) {

            require_once(EASYWIDIR . '/stuff/ajax/datatable_tsdns.php');

        } else if ($ui->smallletters('d', 7, 'get') == 'mysqldb' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['mysql']) {

            require_once(EASYWIDIR . '/stuff/ajax/datatable_mysqldb.php');

        }
    }


    die(json_encode($array));

// App master server updates. Triggered asyncronous with ajax to avoid 5xx errors
} else if ($ui->smallletters('d', 21, 'get') == 'masterappserverupdate' and isset($admin_id) and isset($reseller_id) and isset($resellerLockupID) and $pa['masterServer']) {

    require_once(EASYWIDIR . '/stuff/ajax/app_master_update.php');
    die;

} else if (isset($admin_id) and $pa['gserver'] and $ui->smallletters('d', 14, 'get') == 'appmasterusage') {

    if ($ui->id('id', 10, 'get')) {
        require_once(EASYWIDIR . '/stuff/ajax/app_master_usage.php');
    }
    die;

} else if ($ui->smallletters('d', 18, 'get') == 'appmasterportusage' and isset($admin_id) and $pa['gserver']) {

    if ($ui->ip4('ip', 'get')) {
        require_once(EASYWIDIR . '/stuff/ajax/app_master_port_usage.php');
    }
    die;

} else if ($ui->smallletters('d', 17, 'get') == 'appmasterportbest' and isset($admin_id) and $pa['gserver']) {

    require_once(EASYWIDIR . '/stuff/ajax/app_master_port_best.php');
    die;

} else if ($ui->smallletters('d', 19, 'get') == 'appmasterappdetails' and isset($admin_id) and $pa['gserver']) {

    require_once(EASYWIDIR . '/stuff/ajax/app_master_app_details.php');
    die;

} else if (isset($admin_id) and $pa['voiceserver'] and $ui->smallletters('d', 16, 'get') == 'voicemasterusage') {

    if ($ui->id('id', 10, 'get')) {
        require_once(EASYWIDIR . '/stuff/ajax/voice_master_usage.php');
    }
    die;

} else if ($ui->smallletters('d', 20, 'get') == 'voicemasterportusage' and isset($admin_id) and $pa['voiceserver']) {

    if ($ui->ip4('ip', 'get')) {
        require_once(EASYWIDIR . '/stuff/ajax/voice_master_port_usage.php');
    }
    die;

} else if ($ui->smallletters('d', 16, 'get') == 'tsdnsmasterusage' and isset($admin_id) and $pa['voiceserver']) {

    require_once(EASYWIDIR . '/stuff/ajax/tsdns_master_usage.php');
    die;

} else if (isset($admin_id) and $ui->smallletters('d', 16, 'get') == 'mysqlmasterusage' and $pa['mysql']) {

    if ($ui->id('id', 10, 'get')) {
        require_once(EASYWIDIR . '/stuff/ajax/mysql_master_usage.php');
    }
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
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
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

} else if (isset($user_id) and $pa['usertickets'] and $ui->w('d', 20, 'get') == 'userTicketCategories' and $ui->id('topicName', 10, 'get')) {

    require_once(EASYWIDIR . '/stuff/ajax/userpanel_ticket_category.php');
    die;

} else if (isset($admin_id) and $pa['voiceserverStats'] and $ui->w('d', 15, 'get') == 'adminVoiceStats' and $ui->st('w', 'get')) {

    require_once(EASYWIDIR . '/stuff/ajax/admin_voice_stats.php');
    die;

} else if (isset($user_id) and $pa['voiceserverStats'] and $ui->smallletters('d', 14, 'get') == 'uservoicestats' and $ui->st('w', 'get')) {

    require_once(EASYWIDIR . '/stuff/ajax/userpanel_voice_stats.php');
    die;

} else if ($pa['voiceserverStats'] and ((isset($user_id) and $ui->w('d', 14, 'get') == 'voiceUserStats') or (isset($admin_id) and $ui->w('d', 15, 'get') == 'voiceAdminStats'))) {

    require_once(EASYWIDIR . '/stuff/ajax/stats_voicestats.php');
    die;

} else if (isset($user_id) and ($pa['gserver'] or $pa['restart']) and $ui->username('mapgroup', 50, 'get')) {

    require_once(EASYWIDIR . '/stuff/ajax/userpanel_mapgroup.php');
    die;
}

die('No Access:' . $ui->smallletters('d', 200, 'get'));