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

if (!isset($admin_id) or !isset($reseller_id)) redirect('login.php');
$pa=User_Permissions($admin_id);
if (!isanyadmin($admin_id) and count($pa)==0) redirect('login.php');
$licenceDetails=serverAmount($reseller_id);
$gserver_module=(is_numeric($licenceDetails['mG']) and $licenceDetails['mG']==0) ? false : true;
$vserver_module=(is_numeric($licenceDetails['mVs']) and $licenceDetails['mVs']==0) ? false : true;
$voserver_module=(is_numeric($licenceDetails['mVo']) and $licenceDetails['mVo']==0) ? false : true;
$dediserver_module=(is_numeric($licenceDetails['mD']) and $licenceDetails['mD']==0) ? false : true;
$ewVersions['files']='4.00';
$vcsprache=getlanguagefile('versioncheck',$user_language,$reseller_id,$sql);
$query=$sql->prepare("SELECT `version` FROM `easywi_version` ORDER BY `id` DESC LIMIT 1");
$query->execute();
$ewVersions['cVersion']=$query->fetchColumn();
$query=$sql->prepare("SELECT `version`,`releasenotesDE`,`releasenotesEN` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
$query->execute();
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $ewVersions['version']=$row['version'];
    $ewVersions['releasenotesDE']=$row['releasenotesDE'];
    $ewVersions['releasenotesEN']=$row['releasenotesEN'];
}
if ($reseller_id==0 and $ui->st('w','get')!='vc' and ($ewVersions['cVersion']<$ewVersions['version'] or $ewVersions['files']<$ewVersions['version'])) $toooldversion=$vcsprache->newversion.$ewVersions['version'];
$query=$sql->prepare("SELECT `name`,`vname`,`lastlogin` FROM `userdata` WHERE `id`=? LIMIT 1");
$query->execute(array($admin_id));
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $great_name=$row['name'];
    $great_vname=$row['vname'];
    if ($row['lastlogin']!=null and $row['lastlogin']!='0000-00-00 00:00:00' and $user_language=='de') $great_last=date('d.m.Y H:i:s',strtotime($row['lastlogin']));
    else if ($row['lastlogin']!=null and $row['lastlogin']!='0000-00-00 00:00:00') $great_last=$row['lastlogin'];
    else if ($user_language=='de') $great_last='Niemals';
    else $great_last='Never';
}
$what_to_be_included_array=array('ro'=>'roots.php',
    'fe'=>'feeds.php','fn'=>'feeds_entries.php',
    'ap'=>'api_settings.php','aa'=>'api_external_auth.php','ui'=>'api_import_users.php','jb'=>'jobs_list.php','bu'=>'mysql_root.php',
    'ma'=>'masterserver.php','gs'=>'gserver.php','ad'=>'addons.php','im'=>'images.php','ea'=>'eac.php',
    'vc'=>'versioncheck.php','ib'=>'ip_bans.php','se'=>'panel_settings.php','cc'=>'panel_settings_columns.php','sm'=>'panel_settings_email.php','lo'=>'logdata.php','ml'=>'maillog.php','sr'=>'admin_search.php',
    'us'=>'user.php','ug'=>'user_groups.php',
    'vu'=>'voice_usage.php','vo'=>'voice.php','vd'=>'voice_tsdns.php','vr'=>'voice_tsdnsrecords.php','vm'=>'voice_master.php',
    'le'=>'lendserver.php',
    'ps'=>'page_settings.php','pp'=>'page_pages.php','pn'=>'page_news_edit.php','pc'=>'page_comments.php','pd'=>'page_downloads.php',
    'ip'=>'imprint.php','su'=>'global_userdata.php',
    'rh'=>'root_dedicated.php','rd'=>'root_dhcp.php','rp'=>'root_pxe.php','vh'=>'root_virtual_hosts.php','vs'=>'root_virtual_server.php','ot'=>'roots_os_templates.php','tf'=>'traffic.php',
    'my'=>'mysql_server.php',
    'ti'=>'tickets.php','tr'=>'tickets_reseller.php'
);

# hier dann w = 4 buchstaben custom