<?php

/**
 * File: serverallocation.php.
 * Author: Ulrich Block
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
include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/config.php');
include(EASYWIDIR . '/stuff/settings.php');

$die = false;
if (!isset($admin_id) and !isset($user_id)) {
    redirect('login.php');
} else if (isset($admin_id)) {
    $pa = User_Permissions($admin_id);
} else if (isset($user_id)) {
    $pa = User_Permissions($user_id);
} else {
    $die = true;
}

if (!isset($pa) or count($pa)==0 or ((!isset($admin_id) and !isset($user_id)) or (((!$pa['gserver']) and !$pa['voiceserver'] and !$pa['voicemasterserver'] and !$pa['traffic'] and !$pa['user'] and !rsellerpermisions($admin_id) and !$pa['usertickets']) and (!$pa['restart'] and !$pa['usertickets'])))) {
    $die = true;
}

if ($die == true) {

    redirect('login.php');

} else if ($ui->id('id',19, 'get') and $ui->st('d', 'get')=="vs" and ($pa['addvserver'] or $pa['root'])) {
	$sprache = getlanguagefile('reseller', $user_language, $reseller_id);
	if ($reseller_id != 0 and $admin_id != $reseller_id) {
        $reseller_id = $admin_id;
        $notexclusive = true;
    }
	$query = $sql->prepare("SELECT `id`,`cpu`,`active`,`ip`,`esxi`,`description`,`cores`,`mhz`,`hdd`,`ram`,`maxserver`,`thin`,`thinquota` FROM `virtualhosts` WHERE `id`=?");
    $query2 = $sql->prepare("SELECT `cores`,`minmhz`,`hddsize`,`mountpoint`,`minram` FROM `virtualcontainer` WHERE hostid=?");
    $query->execute(array($ui->id('id',19, 'get')));
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		$id = $row['id'];
		$cores = $row['cores'];
		$mhz = $row['mhz'];
		$hddsize = $row['hdd'];
		$ram = $row['ram'];
		$maxserver = $row['maxserver'];
		$esxi = $row['esxi'];
		$besthostcpu = $row['cpu'] . '  ' . $cores."x".$mhz." MHz";
		if ($row['thin'] == 'Y') {
			$percent = $row['thinquota'];
		} else {
			$percent="100";
		}
		$ramused = 0;
		$hdd_rows=explode("\r\n", $row['hdd']);
		foreach ($hdd_rows as $hddline) {
			$data_explode=explode(" ", $hddline);
			if (isset($data_explode[1])) {
				$mountpoint = $data_explode[0];
				$mountsize[$mountpoint] = $data_explode[1];
				$mountunused[$mountpoint] = 0;
				$hdd[] = $mountpoint;
			}
		}
		$i = 1;
		while ($i<=$cores) {
			$core[] = $i;
			$cpucore[$i] = 0;
			$i++;
		}
		$i = 1;
		if ($esxi == 'Y') {
			$maxcore="8";
		} else {
			$maxcore = $cores;
		}
		while ($i<=$cores and $i<=$maxcore) {
			$add_core[] = $i;
			$i++;
		}
        $query2->execute(array($id));
		$i2 = 0;
		while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
			$mountpoint = $row2['mountpoint'];
			$addstracthdd = $mountunused[$mountpoint]+($row2['hddsize']*($percent/100));
			$mountunused[$mountpoint] = $addstracthdd;
			$addstractram = $ramused+$row2['minram'];
			$ramused = $addstractram;
			$cpuhz = $row2['cores']*$row2['minmhz'];
			$addcpu = $cpucore[1]+$cpuhz;
			if ($addcpu<=$mhz) {
				$cpucore[1] = $addcpu;
			} else {
				$cpucore[1] = $mhz;
				$nextcore = 2;
				while ($nextcore<=$cores) {
					$extra = $addcpu-$mhz;
					$addcpu = $cpucore[$nextcore]+$extra;
					if ($addcpu<=$mhz and $addcpu>=0) {
						$cpucore[$nextcore] = $addcpu;
					} else if ($addcpu>=0) {
						$cpucore[$nextcore] = $mhz;
					}
					$nextcore++;
				}
			}
			$i2++;
		}
		foreach ($hdd as $mountpoint) {
			$freespace[$mountpoint] = $mountsize[$mountpoint]-($mountunused[$mountpoint]*($percent/100));
		}
		natsort($freespace);
		$freespace=array_reverse($freespace);
		foreach ($freespace as $mountpoint => $free) {
			$best_hdd[] = $mountpoint;
		}
	}
    require_once IncludeTemplate($template_to_use,'ajax_admin_vserver_allocation.tpl', 'ajax');

} else if ($ui->st('d', 'get')=="ui" and $ui->id('id',19, 'get')) {
	foreach (freeips($ui->id('id',19, 'get')) as $ip) echo $ip."<br />";

} else if ($ui->st('d', 'get')=="my" and $ui->id('id',19, 'get')) {
	$query = $sql->prepare("SELECT s.`ip`,s.`max_databases`,COUNT(d.`id`) AS `installed` FROM `mysql_external_servers` s LEFT JOIN `mysql_external_dbs` d ON s.`id`=d.`sid` WHERE s.`id`=? AND s.`active`='Y' AND s.`resellerid`=? LIMIT 1");
	$query->execute(array($ui->id('id',19, 'get'), $reseller_id));
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		$installed = $row['installed'];
		$max_databases = $row['max_databases'];
	}
	if (!isset($installed)) {
		$installed = 0;
		$max_databases = 0;
	}
    require_once IncludeTemplate($template_to_use,'ajax_admin_mysql_server.tpl', 'ajax');

} else if ($ui->st('d', 'get')=="tr" and $ui->st('w', 'get')) {
	if ($ui->st('w', 'get')=="su") {
		if ($reseller_id == 0) {
            $query = $sql->prepare("SELECT `ips` FROM `resellerdata`");
            $query->execute();
		} else if ($reseller_id==$admin_id) {
            $query = $sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellersid`=?");
            $query->execute(array($reseller_id));
		} else {
            $query = $sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellerid`=? AND c.`resellersid`=?");
            $query->execute(array($admin_id, $reseller_id));
		}		
		$ips = array();
		$userips = array();
		foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
			unset($userips);
			$userips=ipstoarray($row['ips']);
			foreach ($userips as $ip) {
				$ip_ex=explode(".", $ip);
				$ips[] = $ip_ex[0] . '.' . $ip_ex[1] . '.' . $ip_ex[2].".";
			}
		}
		$subnets=array_unique($ips);
		natsort($subnets);
		foreach ($subnets as $subnet) {
			$data[] = '<option>'.$subnet.'</option>';
		}
	} else if ($ui->st('w', 'get')=="rs") {
		if ($reseller_id == 0) {
            $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `accounttype`='r' AND `id`=`resellerid`");
            $query->execute();
		}
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $data[] = '<option value='.$row['id'].'>'.$row['cname'].'</option>';
	} else if ($ui->st('w', 'get')=="us") {
		if ($reseller_id == 0) {
            $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `accounttype`='r'");
            $query->execute();
		} else if ($reseller_id==$admin_id) {
            $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `accounttype`='r' AND `resellerid`=?");
            $query->execute(array($reseller_id));
		}
		foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$data[] = '<option value='.$row['id'].'>'.$row['cname'].'</option>';
		}
	} else if ($ui->st('w', 'get')=="se") {
		if ($reseller_id == 0) {
            $query = $sql->prepare("SELECT c.`id`,u.`cname` FROM `virtualcontainer` c LEFT JOIN `userdata` u ON c.`userid`=u.`id` ORDER BY u.`id`,c.`id`");
            $query->execute();
		} else if ($reseller_id==$admin_id){
            $query = $sql->prepare("SELECT c.`id`,u.`cname` FROM `virtualcontainer` c LEFT JOIN `userdata` u ON c.`userid`=u.`id` WHERE c.`resellerid`=? ORDER BY u.`id`,c.`id`");
            $query->execute(array($reseller_id));
		} else {
            $query = $sql->prepare("SELECT c.`id`,u.`cname` FROM `virtualcontainer` c LEFT JOIN `userdata` u ON c.`userid`=u.`id` WHERE c.`userid`=? AND c.`resellerid`=? ORDER BY u.`id`,c.`id`");
            $query->execute(array($admin_id, $reseller_id));
		}
		foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) $data[] = '<option value='.$row['id'].'>'.$row['cname'] . '-' . $row['id'].'</option>';
	} else if ($ui->st('w', 'get')=="ip") {
		$userips = array();
		if ($reseller_id == 0) {
            $query = $sql->prepare("SELECT `ips` FROM `resellerdata`");
            $query->execute();
		} else if ($reseller_id==$admin_id) {
            $query = $sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellersid`=?");
            $query->execute(array($reseller_id));
		} else {
            $query = $sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellerid`=? AND c.`resellersid`=?");
            $query->execute(array($admin_id, $reseller_id));
		}
		$ips = array();
		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			foreach (ipstoarray($row['ips']) as $userip) $userips[] = $userip;
		}
		$ips=array_unique($userips);
		natsort($ips);
		foreach ($ips as $ip) $data[] = '<option>'.$ip.'</option>';
	}
    require_once IncludeTemplate($template_to_use,'ajax_admin_traffic.tpl', 'ajax');

} else if ($ui->username('distro', 50, 'get') and $ui->id('id',19, 'get') and ($pa['vserversettings'] or $pa['root']) and $reseller_id == 0) {
	$pselect = $sql->prepare("SELECT `pxeautorun` FROM `resellerimages` WHERE `bitversion`=? AND `distro`=?");
	$pselect->execute(array($ui->id('id',19, 'get'), $ui->username('distro', 50, 'get')));
	$usedpxeautorun = array();
	foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$usedpxeautorun[] = $row['pxeautorun'];
	}
	$i = 0;
	while ($i<="9") {
		if (!in_array($i, $usedpxeautorun)){
			$pxeautorun[] = $i;
		}
		$i++;
	}
?>
<select name="<?php echo "pxeautorun".$ui->id('id',19, 'get');?>">
<?php
	foreach($pxeautorun as $pxe) {
?>
		<option><?php echo $pxe;?></option>
<?php 
	}
?>
</select>
<?php 
} else if (($ui->username('short', 50, 'get') or $ui->username('shorten', 50, 'get')) and $pa['restart']) {
	$sprache = getlanguagefile('gserver', $user_language, $reseller_id);
	if ($reseller_id != 0 and $admin_id != $reseller_id) {
		$reseller_id = $admin_id;
	}
    $get_shorten = $ui->username('shorten', 50, 'get');
	if ($ui->username('short', 50, 'get')) {
		$get_shorten = $get_short;
	}
    $query = $sql->prepare("SELECT `id` FROM `eac` WHERE `active`='Y' AND `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));
    $count = $query->rowCount();
    $query2 = $sql->prepare("SELECT `gamebinary` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");
    $query2->execute(array($get_shorten, $reseller_id));
	while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
		if ($row['gamebinary'] == 'srcds_run' or $row['gamebinary'] == 'hlds_run') {
			$anticheatsoft="Valve Anti Cheat";
		} else if ($row2['gamebinary'] == 'cod4_lnxded') {
			$anticheatsoft="Punkbuster";
		} else {
			$anticheatsoft = '';
		}
		if ($count>0 and ($get_shorten=="css" or $get_shorten=="cod4" or $get_shorten=="cstrike" or $get_shorten=="czero" or $get_shorten=="tf")) {
			$eac='<option value="3">Easy Anti Cheat</option>';
		} else {
			$eac = '';
		}
	}
	if (!isset($anticheatsoft)) {
		$anticheatsoft = '';
	}
	if (!isset($anticheat)) {
		$anticheat = '';
	}
?>
<select name="anticheat">
	<option value="1"><?php echo $anticheatsoft . '  ' . $sprache->on;?></option>
	<?php if (!$ui->username('short', 50, 'get')){ ?><option value="2" <?php if ($anticheat=="2") echo 'selected="selected"';?>><?php echo $anticheatsoft . '  ' . $sprache->off2;?></option><?php } ?>
	<?php echo $eac;?>
</select>
<?php

} else if (($pa['voiceserver'] or $pa['voiceserver']) and $ui->st('d', 'get')=="vo" and $ui->id('id',19, 'get')) {

	$sprache = getlanguagefile('voice', $user_language, $reseller_id);
	$query = $sql->prepare("SELECT m.`maxserver`,COUNT(v.`id`) AS `installedserver`,m.`maxslots`,SUM(v.`slots`) AS `installedslots`,SUM(v.`usedslots`) AS `uslots` FROM `voice_masterserver` m LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` WHERE m.`id`=? AND m.`resellerid`=? LIMIT 1");
	$query->execute(array($ui->id('id',19, 'get'), $reseller_id));
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if ($row['installedserver'] == null) {
			$installedserver = 0;
		} else {
			$installedserver = $row['installedserver'];
		}
		if ($row['installedslots'] == null) {
			$installedslots = 0;
		} else {
			$installedslots = $row['installedslots'];
		}
		if ($row['uslots'] == null) {
			$uslots = 0;
		} else {
			$uslots = $row['uslots'];
		}
        require_once IncludeTemplate($template_to_use,'ajax_admin_voiceserver_usage.tpl', 'ajax');
	}

} else if (($pa['usertickets'] or $pa['usertickets']) and $ui->port('po', 'get') and ($ui->st('d', 'get') == 'ut' or $ui->st('d', 'get') == 'rt')) {
	if ($reseller_id != 0 and $admin_id==$reseller_id and $ui->st('d', 'get') == 'rt') {
		$resellerid = 0;
	} else if ($reseller_id != 0 and $admin_id != $reseller_id and $ui->st('d', 'get') == 'rt') {
		$resellerid = $admin_id;
	} else if ($ui->st('d', 'get') == 'ut' or $ui->st('d', 'get') == 'rt') {
		$resellerid = $reseller_id;
    }
    $table = array();
    if (isset($resellerid)) {
        $query = $sql->prepare("SELECT `language` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($resellerid));
        $default_language = $query->fetchColumn();
        $query = $sql->prepare("SELECT * FROM `ticket_topics` WHERE `maintopic`=? AND `maintopic`!=`id` AND `resellerid`=? ORDER BY `id`");
        $query->execute(array($ui->port('po', 'get'), $resellerid));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $topic = '';
            $pselect3 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ti' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
            $pselect3->execute(array($user_language, $row['id'], $resellerid));
            $topic = $pselect3->fetchColumn();
            if (empty($topic)) {
                $pselect3->execute(array($default_language, $row['id'], $resellerid));
                $topic = $pselect3->fetchColumn();
            }
            if (empty($topic)) $topic = $row['topic'];
            $table[] = array('id' => $row['id'], 'topic' => $topic);
        }
        $ticketTemplate=($ui->id('r',1, 'get') != 1) ? 'ajax_userpanel_ticket_category.tpl' : 'ajax_admin_reseller_ticket_category.tpl';
        require_once IncludeTemplate($template_to_use, $ticketTemplate, 'ajax');
    }
}