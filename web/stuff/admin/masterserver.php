<?php

/**
 * File: masterserver.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['masterServer'])) {
	header('Location: admin.php');
	die('No acces');
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_masterserver.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

$sprache = getlanguagefile('roots',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

if ($reseller_id == 0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
	$logreseller = 0;
}

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->st('d', 'get') == 'ad') {

    $id = $ui->id('id', 10, 'get');

    if ($ui->smallletters('action',2, 'post') == 'ad') {

        $rootServer = new masterServer($id, $aeskey);

        if ($ui->id('id', 10, 'post')) {

            $template_file = '';

            $query = $sql->prepare("SELECT `id` FROM `rservermasterg` WHERE `serverid`=? AND `servertypeid`=? AND `resellerid`=?");
            $query2 = $sql->prepare("SELECT * FROM `servertypes` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query3 = $sql->prepare("INSERT INTO rservermasterg (`serverid`,`servertypeid`,`installing`,`installstarted`,`resellerid`) VALUES (?,?,'Y',NOW(),?)");

            foreach($ui->id('id', 10, 'post') as $masterID) {

                $query->execute(array($id, $masterID ,$resellerLockupID));

                if ($query->rowcount() == 0) {

                    $query2->execute(array($masterID, $resellerLockupID));
                    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

                        $description = $row2['description'];
                        $shorten = $row2['shorten'];

                        $template_file .= '<b>' . $description . '</b> ' . $sprache->root_masterinstall;
                        $loguseraction = '%add% %master% ' . $shorten;
                        $insertlog->execute();
                    }

                    $query3->execute(array($id, $masterID, $resellerLockupID));
                }

                $rootServer->collectData($masterID, true);
            }

            $sshcmd = $rootServer->returnCmds('install','all');

            if ($rootServer->sshcmd !== null) {
                ssh2_execute('gs', $id, $rootServer->sshcmd);
            }

        } else {
            $template_file = $sprache->error_root_noselect;
        }

    } else {

        $table = array();

        $query = $sql->prepare("SELECT `ip`,`os`,`description` FROM `rserverdata` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip = $row['ip'];
            $os = $row['os'];
            $description = $row['description'];
        }

        if (isset($ip) and isset($os)) {

            $query = $sql->prepare("SELECT `id`,`shorten`,`steamgame`,`description` FROM `servertypes` AS t WHERE `resellerid`=? AND (`os`='B' OR `os`=?) AND NOT EXISTS (SELECT 1 FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`serverid`=? AND s.`shorten`=t.`shorten`) ORDER BY `description`");
            $query->execute(array($resellerLockupID, $os, $id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $table[] = array('id' => $row['id'], 'shorten' => $row['shorten'], 'description' => $row['description']);
            }

            $template_file = 'admin_master_add.tpl';

        } else {
            $template_file = 'admin_404.tpl';
        }
    }

} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id',19, 'get')) {

    if ($ui->smallletters('action', 2, 'post') == 'dl') {

        $serverid = $ui->id('id', 19, 'get');

        $rdata = serverdata('root', $serverid, $aeskey);
        $sship = $rdata['ip'];

        if ($ui->id('id', 10, 'post')) {

            $template_file = '';
            $deletestring = '';
            $i = 0;

            foreach($ui->id('id',30, 'post') as $id) {

                $query = $sql->prepare("SELECT s.`shorten` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`id`=? AND r.`resellerid`=? LIMIT 1");
                $query->execute(array($id, $resellerLockupID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $shorten = $row['shorten'];
                    $deletestring .= '_' . $shorten;
                }

                $query = $sql->prepare("DELETE FROM `rservermasterg` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id, $resellerLockupID));

                $template_file .= $spracheResponse->table_del . ": $shorten<br />";
                $loguseraction = "%del% %master% $sship $shorten";
                $insertlog->execute();
                $i++;
            }

            $deletestring = $i . $deletestring;

            $template_file .= (ssh2_execute('gs', $serverid, "./control.sh delete $deletestring")) ? $sprache->root_masterdel : $sprache->error_root_masterdel2;

        } else {
            $template_file = $sprache->error_root_noselect;
        }

    } else {

        $table = array();

        $id = $ui->id('id',19, 'get');

        $query = $sql->prepare("SELECT `ip`,`description` FROM `rserverdata` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $resellerLockupID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip = $row['ip'];
            $description = $row['description'];
        }

        $query = $sql->prepare("SELECT r.`id`,s.`shorten`,s.`description` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`serverid`=? AND r.`resellerid`=? ORDER BY `description`");
        $query->execute(array($id,$resellerLockupID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[] = array('id' => $row['id'], 'shorten' => $row['shorten'], 'description' => $row['description']);
        }

        $template_file = (isset($ip) and strlen($ip) > 0) ? 'admin_master_dl.tpl' : 'Error: No such ID!';
 
    }

} else if ($ui->st('d', 'get') == 'md') {

    configureDateTables('-1', '1, "asc"', 'ajax.php?w=datatable&d=appmasterserver');

    $template_file = 'admin_master_list.tpl';

} else if ($ui->st('d', 'get') == 'ud' and $ui->smallletters('action', 2, 'post') == 'ud') {

    if (is_object($ui->id('id', 10, 'post')) or is_array($ui->id('id', 10, 'post'))) {
        foreach($ui->id('id',19, 'post') as $id) {
            $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $ips[$id] = $row['ip'];
            }
        }
    }

    if ((isset($ips))) {

        $query = $sql->prepare("SELECT s.`shorten` FROM `rservermasterg` r LEFT JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE s.`description`=? AND r.`serverid`=? AND r.`installing`='N' AND r.`resellerid`=?");
        $ajax = '<script type="text/javascript">window.onload = function() {';
        foreach($ui->id('id',19, 'post') as $id) {

            $i = 0;
            $gamestring_buf = '';

            foreach($ui->description('description', 'post') as $description) {

                $query->execute(array($description, $id, ($reseller_id == 0) ? 0 : $admin_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $gamestring_buf .= '_' . $row['shorten'];
                    $i++;
                }
            }

            if ($i > 0) {
                $posted_gamestring = $i . $gamestring_buf;
                $ajax .= "onloaddata('serverallocation.php?gamestring=$posted_gamestring&id=','$id','$id');";
            }
        }

        $ajax .= '}</script>';

        $htmlExtraInformation['js'][] = $ajax;

        $template_file = 'admin_master_ud2.tpl';

    } else {
        $template_file ='Error: No server selected or the server(s) are already updating';
    }

} else {

    $table = array();
    $table3 = array();

    $i = 0;

    $query = $sql->prepare("SELECT s.`description`,s.`shorten` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` " . $where . " GROUP BY s.`description` ORDER BY s.`description` ASC");
    $query->execute(array(':reseller_id' => $resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[$i]['game'] = array('shorten' => $row['shorten'], 'description' => $row['description']);
        $i++;
    }

    $i2 = 0;

    $query = $sql->prepare("SELECT d.`id`,d.`ip` FROM `rservermasterg` r INNER JOIN `rserverdata` d ON r.`serverid`=d.`id` INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` " . $where . " GROUP BY d.`id` ASC");
    $query->execute(array(':reseller_id' => $resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[$i2]['server'] = array('id' => $row['id'], 'ip' => $row['ip']);
        $i2++;
    }

    $query = $sql->prepare("SELECT s.`shorten` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`resellerid`=? GROUP BY s.`shorten` ORDER BY s.`shorten` ASC");
    $query->execute(array($resellerLockupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $shorten = $row['shorten'];
        $table3[] = '<a href="admin.php?w=ma&amp;d=ud&amp;m=' . $shorten . '">' . $shorten . '</a>';
    }

    $template_file = 'admin_master_ud.tpl';

}