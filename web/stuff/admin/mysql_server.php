<?php

/**
 * File: mysql_server.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['mysql_settings'] and !$pa['mysql'])) {
    header('Location: login.php');
    die;
}

include(EASYWIDIR . '/stuff/methods/class_mysql.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('mysql', $user_language, $reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

if ($reseller_id == 0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
    $logsubuser = (isset($_SESSION['oldid'])) ? $_SESSION['oldid']: 0;
	$logreseller = 0;
}
if ($reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}
if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->st('d', 'get') == 'ms' and !$ui->id('id', 10, 'get') and $pa['mysql_settings']) {

    $table = array();

    $o = $ui->st('o', 'get');

	if ($ui->st('o', 'get') == 'ap') {
		$orderby = '`ip` ASC';
	} else if ($ui->st('o', 'get') == 'af') {
		$orderby = '`interface` ASC';
	} else if ($ui->st('o', 'get') == 'df') {
        $orderby = '`interface` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = '`id` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = '`id` DESC';
	} else {
		$orderby = '`ip` DESC';
        $o = 'ap';
	}

    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `mysql_external_servers` WHERE `resellerid`=?");
    $query->execute(array($reseller_id));
    $colcount = $query->fetchColumn();

    if ($start > $colcount) {

        $start = $colcount - $amount;

        if ($start < 0) {
            $start = 0;
        }
    }

	$query = $sql->prepare("SELECT * FROM `mysql_external_servers` WHERE `resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
    $query2 = $sql->prepare("SELECT `id`,`active`,`dbname` FROM `mysql_external_dbs` WHERE `sid`=? AND `resellerid`=?");
	$query->execute(array($reseller_id));
	foreach ($query->fetchall(PDO::FETCH_ASSOC)  as $row) {

        $i = 0;
        $ds = array();

        if ($row['active'] == 'Y') {
            $imgName = '16_ok';
            $imgAlt = 'Active';
        } else {
            $imgName = '16_bad';
            $imgAlt = 'Inactive';
        }

        $query2->execute(array($row['id'], $reseller_id));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $ds[] = array('id' => $row2['id'], 'address' => $row2['dbname'], 'status' => ($row2['active'] == 'N') ? 2 : 1);
            $i++;
        }
		$table[] = array('id' => $row['id'], 'img' => $imgName,'alt' => $imgAlt,'max_databases' => $row['max_databases'], 'dbcount' => $i,'ip' => $row['ip'], 'interface' => $row['interface'], 'active' => $row['active'], 'server' => $ds);
	}

	$next = $start + $amount;
    $vor = ($colcount > $next) ? $start + $amount : $start;
    $back = $start - $amount;
    $zur = ($back >= 0) ? $start - $amount : $start;
    $pageamount = ceil($colcount / $amount);

    $bold = ($start==0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';
	$pages[] = '<a href="admin.php?w=my&amp;d=ms&amp;a=' . $amount . $bold;

	$i = 2;
	while ($i <= $pageamount) {
		$selectpage = ($i - 1) * $amount;
        $pages[] = '<a href="admin.php?w=my&amp;d=ms&amp;a=' . $amount . '&p=' . $selectpage . '"' . ($start==$selectpage) ? 'class="bold"' : '' .' >' . $i . '</a>';
		$i++;
	}

	$pages = implode(', ', $pages);
	$template_file = 'admin_mysql_server_list.tpl';

} else if ($ui->st('d', 'get') == 'ds' and $ui->id('id', 10, 'get') and $pa['mysql_settings']) {

    $id = $ui->id('id', 10, 'get');

    if ($ui->st('action', 'post') == 'dl') {

        $query = $sql->prepare("SELECT `ip` FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        $ip = $query->fetchColumn();

        $query = $sql->prepare("DELETE FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        $queryCount = $query->rowCount();

        $query = $sql->prepare("DELETE FROM `mysql_external_dbs` WHERE `sid`=? AND `resellerid`=?");
        $query->execute(array($id, $reseller_id));
        $queryCount += $query->rowCount();

        if ($queryCount > 0) {

            $template_file = $spracheResponse->table_del;
            $loguseraction = '%del% MySQL Server ' . $ip;
            $insertlog->execute();

        } else {
            $template_file = $spracheResponse->error_table;
        }

    } else {

        $query = $sql->prepare("SELECT `ip`,`interface` FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC)  as $row) {
            $interface = $row['interface'];
            $ip = $row['ip'];
        }

        $template_file = 'admin_mysql_server_dl.tpl';
    }

} else if (in_array($ui->st('d', 'get'), array('ms', 'rs')) and $ui->id('id', 10, 'get') and $pa['mysql_settings']) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->st('action', 'post') or $ui->st('d', 'get') == 'rs') {
        $query = $sql->prepare("SELECT `active`,`ip`,`externalID`,`port`,`user`,AES_DECRYPT(`password`,?) AS `decryptedpassword`,`max_databases`,`interface`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour` FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $id, $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC)  as $row) {
            $active = $row['active'];
            $ip = $row['ip'];
            $externalID = $row['externalID'];
            $port = $row['port'];
            $user = $row['user'];
            $password = $row['decryptedpassword'];
            $max_databases = $row['max_databases'];
            $interface = $row['interface'];
            $max_queries_per_hour = $row['max_queries_per_hour'];
            $max_updates_per_hour = $row['max_updates_per_hour'];
            $max_connections_per_hour = $row['max_connections_per_hour'];
            $max_userconnections_per_hour = $row['max_userconnections_per_hour'];
        }

        $rowCount = $query->rowCount();
    }

    if (!$ui->st('action', 'post')) {

        if (isset($rowCount) and $rowCount > 0) {

            if ($ui->st('d', 'get') == 'rs') {

                $installedDbs = array();

                $query = $sql->prepare("SELECT `id`,`dbname` FROM `mysql_external_dbs` WHERE `sid`=? AND `resellerid`=?");
                $query->execute(array($id, $reseller_id));
                foreach($query->fetchall(PDO::FETCH_ASSOC)  as $row) {
                    $installedDbs[$row['id']] = $row['dbname'];
                }

                $template_file = 'admin_mysql_server_ri.tpl';

            } else {
                $template_file = 'admin_mysql_server_md.tpl';
            }

        } else {
            $template_file = 'admin_404.tpl';
        }

    } else if ($ui->st('action', 'post') == 'rs' and isset($rowCount) and $rowCount > 0) {

        $remotesql = new ExternalSQL ($ip, $port, $user, $password);

        if ($remotesql->error == 'ok') {

            $dbIDs = (array) $ui->id('dbID', 10, 'post');

            $query = $sql->prepare("SELECT *,AES_DECRYPT(`password`,?) AS `decryptedpassword` FROM `mysql_external_dbs` WHERE `id`=? AND `sid`=? AND `resellerid`=? LIMIT 1");

            foreach($dbIDs as $dbID) {

                $query->execute(array($aeskey, $dbID, $id, $reseller_id));
                foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {

                    $remotesql->DelDB($row['dbname']);
                    $remotesql->DelUser($row['dbname']);

                    $remotesql->AddDB($row['dbname'], $row['decryptedpassword'], $row['ips'], $row['max_queries_per_hour'], $row['max_connections_per_hour'], $row['max_updates_per_hour'], $row['max_userconnections_per_hour']);

                    $loguseraction = '%ri% MYSQL DB ' . $row['dbname'] . ' (' . $ip . ')';
                    $insertlog->execute();
                }
            }

            $template_file = $spracheResponse->reinstall_success;

        } else {
            $template_file = $remotesql->error;
        }

	} else if ($ui->st('action', 'post') == 'md') {

        $errors = array();

        if (!$ui->ip('ip', 'post')) {
            $errors[] = 'IP';
        }
        if (!$ui->port('port', 'post')) {
            $errors[] = 'Port';
        }
        if (!$ui->username('user',20, 'post')) {
            $errors[] = 'Username';
        }
        if (!$ui->password('password',40, 'post')) {
            $errors[] = 'Password';
        }

        if (count($errors) > 0) {

            $template_file = 'Error(s): ' . implode(', ', $errors);

		} else {
            $externalID = $ui->externalID('externalID', 'post');
			$active = $ui->active('active', 'post');
			$ip = $ui->ip('ip', 'post');
			$port = $ui->port('port', 'post');
			$user = $ui->username('user', 20, 'post');
			$password = $ui->password('password', 40, 'post');
			$interface = $ui->url('interface', 'post');
            $max_databases = ($ui->id('max_databases', 255, 'post')) ? $ui->id('max_databases', 255, 'post'): 100;
            $max_queries_per_hour = ($ui->id('max_queries_per_hour', 255, 'post')) ? $ui->id('max_queries_per_hour', 255, 'post') : 0;
            $max_updates_per_hour = ($ui->id('max_updates_per_hour', 255, 'post')) ? $ui->id('max_updates_per_hour', 255, 'post') : 0;
            $max_connections_per_hour = ($ui->id('max_connections_per_hour', 255, 'post')) ? $ui->id('max_connections_per_hour', 255, 'post') : 0;
            $max_userconnections_per_hour = ($ui->id('max_userconnections_per_hour', 255, 'post')) ? $ui->id('max_userconnections_per_hour', 255, 'post') : 0;

            $query = $sql->prepare("UPDATE `mysql_external_servers` SET `externalID`=?,`active`=?,`ip`=?,`port`=?,`user`=?,`password`=AES_ENCRYPT(?,?),`max_databases`=?,`interface`=?,`max_queries_per_hour`=?,`max_updates_per_hour`=?,`max_connections_per_hour`=?,`max_userconnections_per_hour`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($externalID, $active, $ip, $port, $user, $password, $aeskey, $max_databases, $interface, $max_queries_per_hour, $max_updates_per_hour, $max_connections_per_hour, $max_userconnections_per_hour, $id, $reseller_id));

			if ($query->rowCount() > 0) {
                $template_file = $spracheResponse->table_add;
                $loguseraction = '%mod% MySQL Server ' . $ip;
                $insertlog->execute();
            } else {
                $template_file = $spracheResponse->error_table;
            }
		}
	} else {
        $template_file = $spracheResponse->notFound;
    }

} else if ($ui->st('d', 'get') == 'as' and $pa['mysql']) {

	if (!$ui->st('action', 'post')) {

		$template_file = 'admin_mysql_server_add.tpl';

	} else if ($ui->st('action', 'post') == 'ad'){

        $errors = array();

        if (!$ui->ip('ip', 'post')) {
            $errors[] = 'IP';
        }

        if (!$ui->port('port', 'post')) {
            $errors[] = 'Port';
        }

        if (!$ui->username('user', 20, 'post')) {
            $errors[] = 'Username';
        }

        if (!$ui->password('password',40, 'post')) {
            $errors[] = 'Password';
        }

        if (count($errors)>0) {
            $template_file = 'Error(s): '.implode(', '.$errors);
        } else {

            $externalID = $ui->externalID('externalID', 'post');
            $active = $ui->active('active', 'post');
            $ip = $ui->ip('ip', 'post');
            $port = $ui->port('port', 'post');
            $user = $ui->username('user',20, 'post');
            $password = $ui->password('password',40, 'post');
            $interface = $ui->url('interface', 'post');
            $max_databases = ($ui->id('max_databases', 255, 'post')) ? $ui->id('max_databases', 255, 'post'): 100;
            $max_queries_per_hour = ($ui->id('max_queries_per_hour', 255, 'post')) ? $ui->id('max_queries_per_hour', 255, 'post') : 0;
            $max_updates_per_hour = ($ui->id('max_updates_per_hour', 255, 'post')) ? $ui->id('max_updates_per_hour', 255, 'post') : 0;
            $max_connections_per_hour = ($ui->id('max_connections_per_hour', 255, 'post')) ? $ui->id('max_connections_per_hour', 255, 'post') : 0;
            $max_userconnections_per_hour = ($ui->id('max_userconnections_per_hour', 255, 'post')) ? $ui->id('max_userconnections_per_hour', 255, 'post') : 0;

            $query = $sql->prepare("INSERT INTO `mysql_external_servers` (`externalID`,`active`,`ip`,`port`,`user`,`password`,`max_databases`,`interface`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour`,`resellerid`) VALUES (?,?,?,?,?,AES_ENCRYPT(?,?),?,?,?,?,?,?,?)");
            $query->execute(array($externalID, $active, $ip, $port, $user, $password, $aeskey, $max_databases, $interface, $max_queries_per_hour, $max_updates_per_hour, $max_connections_per_hour, $max_userconnections_per_hour, $reseller_id));

            if ($query->rowCount() > 0) {
                $template_file = $spracheResponse->table_add;
                $loguseraction = '%add% MySQL Server ' . $ip;
                $insertlog->execute();
            } else {
                $template_file = $spracheResponse->error_table;
            }
		}
	}

} else if ($ui->st('d', 'get') == 'ad' and $pa['mysql']) {

	if (!$ui->st('action', 'post')) {

		$table = array();
		$table2 = array();


		$password = passwordgenerate(20);

        $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
        }

        $query = $sql->prepare("SELECT s.`id`,s.`ip`,s.`max_databases`, COUNT(d.`id`) AS `installed`,(s.`max_databases`/100)*COUNT(d.`id`) AS `usedpercent`,s.`max_queries_per_hour`,s.`max_updates_per_hour`,s.`max_connections_per_hour`,s.`max_userconnections_per_hour` FROM `mysql_external_servers` s LEFT JOIN `mysql_external_dbs` d ON s.`id`=d.`sid` WHERE s.`active`='Y' AND s.`resellerid`=? GROUP BY s.`ip` HAVING `usedpercent`<100 ORDER BY `usedpercent` ASC");
        $query->execute(array($reseller_id));
		foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {

			if (!isset($installed)) {
				$installed = $row['installed'];
				$max_databases = $row['max_databases'];
				$max_queries_per_hour = $row['max_queries_per_hour'];
				$max_updates_per_hour = $row['max_updates_per_hour'];
				$max_connections_per_hour = $row['max_connections_per_hour'];
				$max_userconnections_per_hour = $row['max_userconnections_per_hour'];
			}

			$table2[] = array('id' => $row['id'], 'ip' => $row['ip']);
		}

        if (!isset($installed)) {
            $installed = 0;
            $max_databases = 0;
            $max_queries_per_hour = 0;
            $max_updates_per_hour = 0;
            $max_connections_per_hour = 0;
            $max_userconnections_per_hour = 0;
        }

		$template_file = 'admin_mysql_db_add.tpl';

	} else if ($ui->st('action', 'post') == 'ad' and $ui->id('serverid',10, 'post') and $ui->id('userid',10, 'post')) {

        $errors = array();

        if (!$ui->active('active', 'post')) {
            $errors[] = $sprache->active;
        }
        if (!$ui->password('password',40, 'post')) {
            $errors[] = $sprache->password;
        }

        if (count($errors) > 0) {

            $template_file = 'Error: ' . implode('<br>', $errors);

        } else {

            $externalID = $ui->externalID('externalID', 'post');
            $sid = $ui->id('serverid',10, 'post');
            $uid = $ui->id('userid',10, 'post');
            $active = $ui->active('active', 'post');
            $password = $ui->password('password',40, 'post');
            $manage_host_table = ($ui->active('manage_host_table', 'post')) ? $ui->active('manage_host_table', 'post') : 'N';
            $ips = $ui->ips('ips', 'post');
            $max_queries_per_hour = ($ui->id('max_queries_per_hour', 255, 'post')) ? $ui->id('max_queries_per_hour', 255, 'post') : 0;
            $max_updates_per_hour = ($ui->id('max_updates_per_hour', 255, 'post')) ? $ui->id('max_updates_per_hour', 255, 'post') : 0;
            $max_connections_per_hour = ($ui->id('max_connections_per_hour', 255, 'post')) ? $ui->id('max_connections_per_hour', 255, 'post') : 0;
            $max_userconnections_per_hour = ($ui->id('max_userconnections_per_hour', 255, 'post')) ? $ui->id('max_userconnections_per_hour', 255, 'post') : 0;

            #https://github.com/easy-wi/developer/issues/42 column description added
            $query = $sql->prepare("INSERT INTO `mysql_external_dbs` (`externalID`,`active`,`sid`,`uid`,`description`,`password`,`ips`,`manage_host_table`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour`,`resellerid`) VALUES (?,?,?,?,?,AES_ENCRYPT(?,?),?,?,?,?,?,?,?)");
            $query->execute(array($externalID, $active, $sid, $uid, $ui->names('description', 255, 'post'), $password, $aeskey, $ips, $manage_host_table, $max_queries_per_hour, $max_updates_per_hour, $max_connections_per_hour, $max_userconnections_per_hour, $reseller_id));

            $id = $sql->lastInsertId();

            $dbname = 'sql' . $id;

            if ($active == 'N') {
                $password = passwordgenerate(20);
            }

            $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `dbname`=?,`password`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($dbname, $password, $aeskey, $id, $reseller_id));

            $query = $sql->prepare("SELECT `ip`,`port`,`user`,AES_DECRYPT(`password`,?) AS `decryptedpassword` FROM `mysql_external_servers` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($aeskey, $sid, $reseller_id));
            foreach ($query->fetchall(PDO::FETCH_ASSOC)  as $row) {

                $remotesql = new ExternalSQL ($row['ip'], $row['port'], $row['user'], $row['decryptedpassword']);

                if ($remotesql->error == 'ok') {

                    $reply = $remotesql->AddDB($dbname, $password, $ips, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour);

                    customColumns('D', $id, 'save');

                    $template_file = $spracheResponse->table_add;
                    $loguseraction = '%add% MySQL DB ' . $dbname . ' (' . $row['ip'] . ')';
                    $insertlog->execute();

                }
            }

            if (!isset($remotesql) or $remotesql->error != 'ok') {

                $query = $sql->prepare("DELETE FROM `mysql_external_dbs` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id, $reseller_id));

                $template_file = $remotesql->error;
            }
        }

	} else {
		$template_file = 'admin_404.tpl';
	}

} else if (in_array($ui->st('d', 'get') ,array('md','rd')) and $ui->id('id', 10, 'get') and $pa['mysql']) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->st('action', 'post')) {

        $query = $sql->prepare("SELECT e.*,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,s.`ip`,u.`cname` FROM `mysql_external_dbs` e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`id`=? AND e.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $id, $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {

            $externalID = $row['externalID'];
            $ip = $row['ip'];
            $manage_host_table = $row['manage_host_table'];
            $ips = $row['ips'];
            $cname = $row['cname'];
            $active = 'N';
            $description = $row['description'];
            $dbname = $row['dbname'];
            $password = $row['decryptedpassword'];
            $max_queries_per_hour = $row['max_queries_per_hour'];
            $max_updates_per_hour = $row['max_updates_per_hour'];
            $max_connections_per_hour = $row['max_connections_per_hour'];
            $max_userconnections_per_hour = $row['max_userconnections_per_hour'];

            if ($row['jobPending'] == 'Y') {

                $query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='us' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");
                $query2->execute(array($row['id'], $row['resellerid']));
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

                    if ($row2['action'] == 'ad') {
                        $jobPending = $gsprache->add;
                    } else if ($row2['action'] == 'dl') {
                        $jobPending = $gsprache->del;
                    } else {
                        $jobPending = $gsprache->mod;
                    }

                    $json = @json_decode($row2['extraData']);
                    $active = (is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
                }

            } else {
                $jobPending = $gsprache->no;
                $active = $row['active'];
            }

        }

        $modOrReinstall = ($ui->st('d', 'get') == 'md') ? 'admin_mysql_db_md.tpl' : 'admin_mysql_db_ri.tpl';
        $template_file = ($query->rowCount() > 0) ? $modOrReinstall : 'admin_404.tpl';

    } else if ($ui->st('action', 'post') == 'md') {

        $errors = array();

        if (!$ui->active('active', 'post')) {
            $errors[] = $sprache->active;
        }

        if (!$ui->password('password',40, 'post')) {
            $errors[] = $sprache->password;
        }

        if (count($errors) > 0) {

            $template_file = "Error: ".implode('<br>', $errors);

        } else {

            $externalID = $ui->externalID('externalID', 'post');
            $active = $ui->active('active', 'post');
            $password = $ui->password('password',40, 'post');
            $manage_host_table = ($ui->active('manage_host_table', 'post')) ? $ui->active('manage_host_table', 'post') : 'N';
            $ips = $ui->ips('ips', 'post');
            $max_queries_per_hour = ($ui->id('max_queries_per_hour', 255, 'post')) ? $ui->id('max_queries_per_hour', 255, 'post') : 0;
            $max_updates_per_hour = ($ui->id('max_updates_per_hour', 255, 'post')) ? $ui->id('max_updates_per_hour', 255, 'post') : 0;
            $max_connections_per_hour = ($ui->id('max_connections_per_hour', 255, 'post')) ? $ui->id('max_connections_per_hour', 255, 'post') : 0;
            $max_userconnections_per_hour = ($ui->id('max_userconnections_per_hour', 255, 'post')) ? $ui->id('max_userconnections_per_hour', 255, 'post') : 0;

            $query = $sql->prepare("SELECT e.*,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,s.`ip`,AES_DECRYPT(s.`password`,?) AS `decryptedpassword2`,s.`port`,s.`user`,u.`cname` FROM `mysql_external_dbs` e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`id`=? AND e.`resellerid`=? LIMIT 1");
            $query->execute(array($aeskey, $aeskey, $id, $reseller_id));
            foreach ($query->fetchall(PDO::FETCH_ASSOC)  as $row) {

                if ($active != $row['active'] or $manage_host_table != $row['manage_host_table'] or $row['decryptedpassword'] != $password  or $row['ips'] != $ips or $row['max_queries_per_hour'] != $max_queries_per_hour or $row['max_updates_per_hour'] != $max_updates_per_hour or $row['max_connections_per_hour'] != $max_connections_per_hour or $row['max_userconnections_per_hour'] != $max_userconnections_per_hour) {

                    $remotesql = new ExternalSQL ($row['ip'], $row['port'], $row['user'], $row['decryptedpassword2']);

                    if ($remotesql->error == 'ok') {

                        #https://github.com/easy-wi/developer/issues/42 column description added
                        $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `externalID`=?,`active`=?,`ips`=?,`manage_host_table`=?,`description`=?,`password`=AES_ENCRYPT(?,?),`max_queries_per_hour`=?,`max_updates_per_hour`=?,`max_connections_per_hour`=?,`max_userconnections_per_hour`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                        $query->execute(array($externalID, $active, $ips, $manage_host_table, $ui->names('description', 255, 'post'), $password, $aeskey, $max_queries_per_hour, $max_updates_per_hour, $max_connections_per_hour, $max_userconnections_per_hour, $id, $reseller_id));

                        if ($query->rowCount() > 0) {

                            if ($active == 'N' and $row['active'] == 'Y') {
                                $password = passwordgenerate(20);
                            }

                            $remotesql->ModDB($row['dbname'], $password, $ips, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour);

                            $loguseraction = '%mod% MySQL DB ' . $row['dbname'] . ' (' . $row['ip'] . ')';
                            $insertlog->execute();

                            $template_file = $spracheResponse->table_add;

                        } else {
                            $template_file = $spracheResponse->table_error;
                        }

                    } else {
                        $template_file = $remotesql->error;
                    }

                } else {

                    $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `externalID`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($externalID, $id, $reseller_id));

                    customColumns('D', $id, 'save');

                    $template_file = $spracheResponse->table_add;
                }
            }


            if (!isset($template_file)) {
                $template_file = 'admin_404.tpl';
            }
        }

    } else if ($ui->st('action', 'post') == 'rd') {

        $query = $sql->prepare("SELECT e.`dbname`,e.`ips`,e.`max_queries_per_hour`,e.`max_connections_per_hour`,e.`max_updates_per_hour`,e.`max_userconnections_per_hour`,AES_DECRYPT(e.`password`,?) AS `decryptedpassword`,s.`ip`,AES_DECRYPT(s.`password`,?) AS `decryptedpassword2`,s.`port`,s.`user` FROM `mysql_external_dbs` e INNER JOIN `mysql_external_servers` s ON e.`sid`=s.`id` WHERE e.`id`=? AND e.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $aeskey, $id, $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {

            $remotesql = new ExternalSQL ($row['ip'], $row['port'], $row['user'], $row['decryptedpassword2']);

            if ($remotesql->error == 'ok') {

                $remotesql->DelDB($row['dbname']);
                $remotesql->DelUser($row['dbname']);

                $remotesql->AddDB($row['dbname'], $row['decryptedpassword'], $row['ips'], $row['max_queries_per_hour'], $row['max_connections_per_hour'], $row['max_updates_per_hour'], $row['max_userconnections_per_hour']);

                $loguseraction = '%ri% MYSQL DB ' . $row['dbname'] . ' (' . $row['ip'] . ')';
                $insertlog->execute();

                $template_file = $spracheResponse->reinstall_success;

            } else {
                $template_file = $remotesql->error;
            }
        }

        if (!isset($remotesql)) {
            $template_file = 'admin_404.tpl';
        }

    } else {
        $template_file = 'admin_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'dd' and $ui->id('id', 10, 'get') and $pa['mysql']) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->st('action', 'post')) {

        $query = $sql->prepare("SELECT e.`dbname`,s.`ip`,u.`cname` FROM `mysql_external_dbs` e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`id`=? AND e.`resellerid`=? LIMIT 1");
        $query->execute(array($id, $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC)  as $row) {
            $ip = $row['ip'];
            $dbname = $row['dbname'];
            $cname = $row['cname'];
        }

        $template_file = ($query->rowCount() > 0) ? 'admin_mysql_db_dl.tpl' : 'admin_404.tpl';

    } else if ($ui->st('action', 'post') == 'dl') {

        $query = $sql->prepare("SELECT e.`dbname`,s.`ip`,AES_DECRYPT(s.`password`,?) AS `decryptedpassword2`,s.`port`,s.`user` FROM `mysql_external_dbs` e INNER JOIN `mysql_external_servers` s ON e.`sid`=s.`id` WHERE e.`id`=? AND e.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $id, $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC)  as $row) {

            $remotesql = new ExternalSQL ($row['ip'], $row['port'], $row['user'], $row['decryptedpassword2']);

            if ($remotesql->error == 'ok') {

                $remotesql->DelDB($row['dbname']);
                $remotesql->DelUser($row['dbname']);

                $query = $sql->prepare("DELETE FROM `mysql_external_dbs` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id, $reseller_id));

                customColumns('D', $id, 'del');

                $loguseraction = '%del% MySQL DB ' . $row['dbname'] . ' (' . $row['ip'] .')';
                $insertlog->execute();

                $template_file = $spracheResponse->table_del;

            } else {
                $template_file = $remotesql->error;
            }
        }

        if (!isset($remotesql)) {
            $template_file = 'admin_404.tpl';
        }

    } else {
        $template_file = 'admin_404.tpl';
    }

} else if ($pa['mysql']) {

    $table = array();

    $o = $ui->st('o', 'get');

    if ($ui->st('o', 'get') == 'as') {
        $orderby = 'e.`active` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = 'e.`active` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = 'e.`id` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = 'e.`id` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = 'e.`description` ASC';
    } else if ($ui->st('o', 'get') == 'dd') {
        $orderby = 'e.`description` DESC';
    } else if ($ui->st('o', 'get') == 'ap') {
        $orderby = 's.`ip` ASC';
    } else if ($ui->st('o', 'get') == 'dp') {
        $orderby = 's.`ip` DESC';
    } else if ($ui->st('o', 'get') == 'aj') {
        $orderby = '`jobPending` ASC';
    } else if ($ui->st('o', 'get') == 'dj') {
        $orderby = '`jobPending` DESC';
    } else if ($ui->st('o', 'get') == 'au') {
        $orderby = 'u.`cname` ASC';
    } else if ($ui->st('o', 'get') == 'du') {
        $orderby = 'u.`cname` DESC';
    } else if ($ui->st('o', 'get') == 'af') {
        $orderby = 'u.`name` ASC,u.`vname` ASC';
    } else if ($ui->st('o', 'get') == 'df') {
        $orderby = 'u.`name` DESC,u.`vname` DESC';
    } else if ($ui->st('o', 'get') == 'dn') {
        $orderby = 'e.`dbname` DESC';
    } else {
        $orderby = 'e.`dbname` ASC';
        $o = 'an';
    }

    #https://github.com/easy-wi/developer/issues/42 column description added
    $query = $sql->prepare("SELECT e.`id`,e.`uid`,e.`active`,e.`dbname`,e.`description`,e.`jobPending`,e.`dbSize`,e.`resellerid`,s.`ip`,s.`interface`,u.`cname`,u.`name`,u.`vname` FROM `mysql_external_dbs` e LEFT JOIN `mysql_external_servers` s ON e.`sid`=s.`id` LEFT JOIN `userdata` u ON e.`uid`=u.`id` WHERE e.`resellerid`=? ORDER BY $orderby LIMIT $start, $amount");
    $query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='my' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");
    $query->execute(array($reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC)  as $row) {
        if ($row['jobPending'] == 'Y') {
            $query2->execute(array($row['id'], $row['resellerid']));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                if ($row2['action'] == 'ad') $jobPending = $gsprache->add;
                else if ($row2['action'] == 'dl') $jobPending = $gsprache->del;
                else $jobPending = $gsprache->mod;
                $json=@json_decode($row2['extraData']);
                $tobeActive=(is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
            }
        } else {
            $jobPending = $gsprache->no;
        }
        if (($row['active'] == 'Y' and $row['jobPending'] == 'N') or ($row['jobPending'] == 'Y') and isset($tobeActive) and $tobeActive == 'Y') {
            $imgName = '16_ok';
            $imgAlt = 'Active';
        } else {
            $imgName = '16_bad';
            $imgAlt = 'Inactive';
        }
        $dbname = $row['dbname'];
        $jobPending=($row['jobPending'] == 'Y') ? $gsprache->yes: $gsprache->no;
        #https://github.com/easy-wi/developer/issues/42 column description added
        $table[] = array('id' => $row['id'], 'uid' => $row['uid'], 'img' => $imgName, 'description' => $row['description'], 'alt' => $imgAlt, 'dbname' => $dbname ,'dbSize' => $row['dbSize'], 'cname' => $row['cname'], 'names' => trim($row['name'] . ' ' . $row['vname']),'ip' => $row['ip'], 'interface' => $row['interface'], 'jobPending' => $jobPending,'active' => $row['active']);
    }
    $next = $start+$amount;
    $countp = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `mysql_external_dbs` WHERE `resellerid`=?");
    $countp->execute(array($reseller_id));
    foreach ($countp->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $colcount = $row['amount'];
    }
    if ($colcount>$next) {
        $vor = $start+$amount;
    } else {
        $vor = $start;
    }
    $back = $start - $amount;
    if ($back>=0){
        $zur = $start - $amount;
    } else {
        $zur = $start;
    }
    $pageamount = ceil($colcount / $amount);
    $link='<a href="admin.php?w=my&amp;d=md&amp;a=';
    if (!isset($amount)) {
        $link .="20";
    } else {
        $link .= $amount;
    }
    if ($start==0) {
        $link .= '&p=0" class="bold">1</a>';
    } else {
        $link .= '&p=0">1</a>';
    }
    $pages[] = $link;
    $i = 2;
    while ($i<=$pageamount) {
        $selectpage = ($i - 1) * $amount;
        if ($start==$selectpage) {
            $pages[] = '<a href="admin.php?w=my&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>';
        } else {
            $pages[] = '<a href="admin.php?w=my&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        }
        $i++;
    }
    $pages=implode(', ', $pages);
    $template_file = "admin_mysql_db_list.tpl";
}