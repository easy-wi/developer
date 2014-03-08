<?php

/**
 * File: init_admin.php.
 * Author: Ulrich Block
 * Date: 30.01.13
 * Time: 11:12
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

if (!isset($admin_id) or !isset($reseller_id)) {
    header('Location: login.php');
    die;
}

$adminInclude = true;

$pa = User_Permissions($admin_id);

if (!isanyadmin($admin_id) and count($pa) == 0) {
    redirect('login.php');
}

$licenceDetails = serverAmount($reseller_id);
$gserver_module = (is_numeric($licenceDetails['mG']) and $licenceDetails['mG'] == 0) ? false : true;
$vserver_module = (is_numeric($licenceDetails['mVs']) and $licenceDetails['mVs'] == 0) ? false : true;
$voserver_module = (is_numeric($licenceDetails['mVo']) and $licenceDetails['mVo'] == 0) ? false : true;
$dediserver_module = (is_numeric($licenceDetails['mD']) and $licenceDetails['mD'] == 0) ? false : true;

$ewVersions['files'] = '4.30';

$vcsprache = getlanguagefile('versioncheck', $user_language, $reseller_id);
$query = $sql->prepare("SELECT `version` FROM `easywi_version` ORDER BY `id` DESC LIMIT 1");
$query->execute();
$ewVersions['cVersion'] = $query->fetchColumn();

$query = $sql->prepare("SELECT `version`,`releasenotesDE`,`releasenotesEN` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
$query->execute();
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $ewVersions['version'] = $row['version'];
    $ewVersions['releasenotesDE'] = $row['releasenotesDE'];
    $ewVersions['releasenotesEN'] = $row['releasenotesEN'];
}

if ($reseller_id == 0 and $ui->st('w', 'get') != 'vc' and ($ewVersions['cVersion'] < $ewVersions['version'] or $ewVersions['files'] < $ewVersions['version'])) {
    $toooldversion = $vcsprache->newversion.$ewVersions['version'];
}

$query = $sql->prepare("SELECT `cname`,`name`,`vname`,`lastlogin` FROM `userdata` WHERE `id`=? LIMIT 1");
$query->execute(array($admin_id));
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $great_name = $row['name'];
    $great_vname = $row['vname'];

    $great_user = ($row['name'] != '' or $row['vname'] != '') ? trim ($row['vname'] . ' ' . $row['name']) : $row['cname'];

    if ($row['lastlogin'] != null and $row['lastlogin'] != '0000-00-00 00:00:00') {
        $great_last = ($user_language == 'de') ? date('d.m.Y H:m:s', strtotime($row['lastlogin'])) : $row['lastlogin'];
    } else {
        $great_last = ($user_language == 'de') ? 'Niemals' : 'Never';
    }
}

# https://github.com/easy-wi/developer/issues/61 modules management
$what_to_be_included_array = array(
    'fe' => 'feeds.php', 'fn' => 'feeds_entries.php',
    'ap' => 'api_settings.php', 'aa' => 'api_external_auth.php', 'ui' => 'api_import_users.php', 'jb' => 'jobs_list.php', 'bu' => 'mysql_root.php',
    'vc' => 'versioncheck.php', 'ib' => 'ip_bans.php', 'se' => 'panel_settings.php', 'cc' => 'panel_settings_columns.php', 'sm' => 'panel_settings_email.php', 'lo' => 'logdata.php', 'ml' => 'maillog.php', 'sr' => 'admin_search.php',
    'us' => 'user.php', 'ug' => 'user_groups.php',
    'su' => 'global_userdata.php'
);

$easywiModules = array('fd' => true, 'gs' => true, 'ip' => true, 'ea' => true, 'my' => true, 'pn' => true, 'ro' => true, 'ti' => true, 'le' => true, 'vo' => true);
$customModules = array('fd' => array(), 'gs' => array(), 'mo' => array(), 'my' => array(), 'ro' => array(), 'ti' => array(), 'us' => array(), 'vo' => array(), 'pa' => array());
$customFiles = array();

$query = $sql->prepare("SELECT * FROM `modules` WHERE `type` IN ('A','C')");
$query2 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='mo' AND `transID`=? AND `lang`=? LIMIT 1");
$query->execute();
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    if ($row['active'] == 'Y' and $row['type'] == 'A' and is_file(EASYWIDIR . '/stuff/custom_modules/' . $row['file'])) {
        $query2->execute(array($row['id'], $user_language));
        $name = $query2->fetchColumn();

        if (strlen($name) == 0) {
            $query2->execute(array($row['id'], $rSA['language']));
            $name = $query2->fetchColumn();
        }
        if (strlen($name) == 0) {
            $name = $row['file'];
        }

        $customModules[$row['sub']][$row['get']] = $name;
        $customFiles[$row['get']] = $row['file'];

    } else if ($row['type'] == 'C' and $row['active'] == 'N') {
        $easywiModules[$row['get']] = false;
    }
}

if ($reseller_id == 0) {
    $what_to_be_included_array['mo'] = 'admin_modules.php';
    $what_to_be_included_array['up'] = 'admin_social_provider.php';

    if ($easywiModules['pn'] === true) {
        $what_to_be_included_array['ps'] = 'page_settings.php';
        $what_to_be_included_array['pp'] = 'page_pages.php';
        $what_to_be_included_array['pn'] = 'page_news_edit.php';
        $what_to_be_included_array['pc'] = 'page_comments.php';
        $what_to_be_included_array['pd'] = 'page_downloads.php';
    }
}

if ($easywiModules['gs'] === true) {
    $what_to_be_included_array['ro'] = 'roots.php';
    $what_to_be_included_array['ma'] = 'masterserver.php';
    $what_to_be_included_array['gs'] = 'gserver.php';
    $what_to_be_included_array['gt'] = 'global_gserver_file_templates.php';
    $what_to_be_included_array['ad'] = 'addons.php';
    $what_to_be_included_array['im'] = 'images.php';
}

if ($easywiModules['ea'] === true) {
    $what_to_be_included_array['ea'] = 'eac.php';
}

if ($easywiModules['fd'] === true) {
    $what_to_be_included_array['fm'] = 'fastdl_master.php';
    $what_to_be_included_array['fv'] = 'fastdl_vhost.php';
}

if ($easywiModules['my'] === true) {
    $what_to_be_included_array['my'] = 'mysql_server.php';
}

if ($easywiModules['ro'] === true) {
    $what_to_be_included_array['rh'] = 'root_dedicated.php';
    $what_to_be_included_array['vs'] = 'root_virtual_server.php';
    $what_to_be_included_array['tf'] = 'traffic.php';

    if ($reseller_id == 0) {
        $what_to_be_included_array['rd'] = 'root_dhcp.php';
        $what_to_be_included_array['rp'] = 'root_pxe.php';
        $what_to_be_included_array['ot'] = 'roots_os_templates.php';
        $what_to_be_included_array['vh'] = 'root_virtual_hosts.php';
        $what_to_be_included_array['sn'] = 'roots_subnets.php';
    }
}

if ($easywiModules['ti'] === true) {
    $what_to_be_included_array['ti'] = 'tickets.php';
    $what_to_be_included_array['tr'] = 'tickets_reseller.php';
}

if ($easywiModules['le'] === true) {
    $what_to_be_included_array['le'] = 'lendserver.php';
}

if ($easywiModules['vo'] === true) {
    $what_to_be_included_array['vu'] = 'voice_usage.php';
    $what_to_be_included_array['vo'] = 'voice.php';
    $what_to_be_included_array['vd'] = 'voice_tsdns.php';
    $what_to_be_included_array['vr'] = 'voice_tsdnsrecords.php';
    $what_to_be_included_array['vm'] = 'voice_master.php';
}

if ($easywiModules['ip'] === true) {
    $what_to_be_included_array['ip'] = 'imprint.php';
    $what_to_be_included_array['si'] = 'panel_settings_imprint.php';
}