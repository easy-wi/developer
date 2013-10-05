<?php

/**
 * File: userpanel_backup.php.
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
if ((!isset($user_id) or $main!=1) or (isset($user_id) and !$pa['ftpbackup']) or !$ui->id('id', 10, 'get')) {
    header('Location: userpanel.php');
    die;
}
$sprache = getlanguagefile('gserver',$user_language,$reseller_id);
if (isset($admin_id) and $reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id=$admin_id;
}
$customer=getusername($user_id);
include(EASYWIDIR . '/stuff/keyphrasefile.php');
if ($ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'),$substituteAccess['gs']))) {
    $id=(int)$ui->id('id', 10, 'get');
    $query = $sql->prepare("SELECT g.`serverip`,g.`port`,g.`rootID`,g.`newlayout`,s.`map`,t.`shorten`,AES_DECRYPT(g.`ftppassword`,?) AS `dftppassword`,u.`cname`,AES_DECRYPT(u.`ftpbackup`,?) AS `ftp` FROM `gsswitch` g LEFT JOIN `serverlist` s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` LEFT JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($aeskey,$aeskey,$id,$user_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $serverip=$row['serverip'];
        $port=$row['port'];
        $gsfolder=$serverip . '_' . $port;
        $map=$row['map'];
        $gsswitch=$row['shorten'];
        $rootid=$row['rootID'];
        $ftppass=$row['dftppassword'];
        $customer=$row['cname'];
        if ($row['newlayout'] == 'Y') $customer=$customer . '-' . $id;
        $ftpbackup=$row['ftp'];
    }
    if ($query->rowCount()==0) redirect('userpanel.php');
    if (!$ui->w('action',3,'post')) {
        $template_file = "userpanel_gserver_backup.tpl";
    } else if ($ui->w('action',3,'post') == 'mb'){
        include(EASYWIDIR . '/stuff/ssh_exec.php');
        $rdata=serverdata('root',$rootid,$aeskey);
        $sship=$rdata['ip'];
        $sshport=$rdata['port'];
        $sshuser=$rdata['user'];
        $sshpass=$rdata['pass'];
        $query = $sql->prepare("SELECT DISTINCT(t.`shorten`) FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=?");
        $query->execute(array($id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (isset($shortens)) $shortens .= ' ' . $row['shorten'];
            else $shortens=$row['shorten'];
        }
        $webhostdomain=webhostdomain($reseller_id);
        $template_file = (ssh2_execute('gs',$rootid,"sudo -u $customer ./control.sh backup $gsfolder \"$shortens\" \"$webhostdomain\" \"$ftpbackup\"")===false) ? "Error: ".$ssh_reply: $template_file = $gsprache->backup . '  ' . $sprache->create;
    } else if ($ui->w('action',3,'post') == 'md'){
        $template_file = "userpanel_gserver_backup_md.tpl";
    } else if ($ui->w('action',3,'post') == 'md2') {
        $query = $sql->prepare("UPDATE `userdata` SET `ftpbackup`=AES_ENCRYPT(?,?) WHERE `id`=? LIMIT 1");
        $query->execute(array($ui->url('ftpbackup','post'),$aeskey,$user_id));
        $template_file = $spracheResponse->table_add;
    } else if ($ui->w('action',3,'post') == 'rb'){
        $shortens = array();
        $query = $sql->prepare("SELECT DISTINCT(t.`shorten`) FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=?");
        $query->execute(array($id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $shortens[] = $row['shorten'];
            $shortens[] = $row['shorten'].'-2';
            $shortens[] = $row['shorten'].'-3';
        }
        $template_file = "userpanel_gserver_backup_rb.tpl";
    } else if ($ui->w('action',3,'post') == 'rb2' and $ui->gamestring('template','post')){
        include(EASYWIDIR . '/stuff/ssh_exec.php');
        $rdata=serverdata('root',$rootid,$aeskey);
        $sship=$rdata['ip'];
        $sshport=$rdata['port'];
        $sshuser=$rdata['user'];
        $sshpass=$rdata['pass'];
        $folders=explode("/",$ui->server['SCRIPT_NAME']);
        $amount=count($folders)-1;
        $i = 0;
        $path = '';
        while ($i<$amount) {
            $path .=$folders[$i]."/";
            $i++;
        }
        $webhostdomain=(isset($ui->server['HTTPS'])) ? "https://".$ui->server['HTTP_HOST'].$path : $webhostdomain="http://".$ui->server['HTTP_HOST'].$path;
        $template_file = (ssh2_execute('gs',$rootid,"sudo -u $customer ./control.sh restore $gsfolder \"".$ui->gamestring('template','post')."\" \"$webhostdomain\" \"$ftpbackup\"")===false) ? "Error: ".$ssh_reply: $template_file = $gsprache->backup . '  ' . $sprache->recover;
    } else {
        $template_file = 'userpanel_404.tpl';
    }
} else {
    redirect('userpanel.php');
}