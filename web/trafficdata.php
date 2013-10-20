<?php

/**
 * File: trafficdata.php.
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

if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $timelimit = (isset($_GET['timeout']) and is_numeric($_GET['timeout'])) ? $_GET['timeout'] : ini_get('max_execution_time') - 10;
} else {
    $maxTime =  (isset($argv[1])) ? $argv[1]: 60;
    $memoryLimit =  (isset($argv[2])) ? $argv[2] : '64M';

    ini_set('max_execution_time', $maxTime);
    ini_set('memory_limit', $memoryLimit);
    set_time_limit($maxTime);

    print 'The time is now: ' . ini_get('max_execution_time') . "\r\n";
    print 'The memory limit is now: ' . ini_get('memory_limit') . "\r\n";
    $timelimit = 600;
}

define('EASYWIDIR', dirname(__FILE__));
include(EASYWIDIR . '/stuff/vorlage.php');
include(EASYWIDIR . '/stuff/class_validator.php');
include(EASYWIDIR . '/stuff/functions.php');
include(EASYWIDIR . '/stuff/settings.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

if (!isset($ip) or $ui->escaped('SERVER_ADDR', 'server') == $ip or in_array($ip, ipstoarray($rSA['cronjob_ips']))) {

	$query = $sql->prepare("SELECT `type`,`statip`,AES_DECRYPT(`dbname`,:aeskey) AS `decpteddbname`,AES_DECRYPT(`dbuser`,:aeskey) AS `decpteddbuser`,AES_DECRYPT(`dbpassword`,:aeskey) AS `decpteddbpassword`,`table_name`,`column_sourceip`,`column_destip`,`column_byte`,`column_date` FROM `traffic_settings` LIMIT 1");
    $query->execute(array(':aeskey' => $aeskey));
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$stats_databanktype = $row['type'];
		$stats_host = $row['statip'];
		$stats_db = $row['decpteddbname'];
		$stats_user = $row['decpteddbuser'];
		$stats_pwd = $row['decpteddbpassword'];
		$table_name = $row['table_name'];
		$column_sourceip = $row['column_sourceip'];
		$column_destip = $row['column_destip'];
		$column_byte = $row['column_byte'];
		$column_date = $row['column_date'];

        try {
            $sql2 = new PDO("$stats_databanktype:host=$stats_host;dbname=$stats_db", $stats_user, $stats_pwd, array(PDO::ATTR_TIMEOUT => 5,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        }

        catch(PDOException $error) {
            print $error->getMessage();
            die("Could not connect to external server\r\n");
        }
	}

    if (!isset($sql2)) {
        die("No external server defined \r\n");
    }

	function searchinnerarray($value,$array) {
		foreach ($array as $key => $ips) {
			if (in_array($value,$ips)) {
				$serverid = $key;
			}
		}
        return (isset($serverid)) ? $serverid : false;
	}

    $query = $sql->prepare("SELECT `ips`,`resellerid`,`resellersid` FROM `resellerdata`");
    $query->execute();
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$ids = $row['resellerid'] . '-' . $row['resellersid'];
		$userips[$ids]=ipstoarray($row['ips']);
	}

    $query = $sql->prepare("SELECT `id`,`ip`,`ips` FROM `virtualcontainer`");
    $query->execute();
	foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
		unset($vserverip);
		$vserverip[] = $row['ip'];
		$vserverid = $row['id'];
		foreach(ipstoarray($row['ips']) as $vip) {
			$vserverip[] = $vip;
		}
		$vserverips[$vserverid] = $vserverip;
	}

    $query = $sql2->prepare("SHOW PROCESSLIST");
    $query->execute();
    print "Killing active locks and threads regarding database $stats_db\r\n";
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        list($host)=explode(':', $row['Host']);
        if ($host == 'localhost' and $row['db'] == $stats_db) {
            $query2 = $sql2->prepare("KILL ?");
            $query2->execute(array($row['Id']));
        }
    }

    $pass = 1;
    $Count = $sql2->prepare("SELECT COUNT(`id`) AS `amount` FROM `$table_name`");
    $Count->execute();
    $theCount = $Count->fetchColumn();

    while ($theCount > 100) {

        $pass++;
        $serverIDs = array();

        $query = $sql2->prepare("SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
        $query->execute();

        $query = $sql2->prepare("SELECT `id`,`$column_sourceip`,`$column_destip`,`$column_byte`,`$column_date` FROM `$table_name` ORDER BY `id` LIMIT 300");
        $query->execute();
        $trafficData = $query->fetchAll(PDO::FETCH_ASSOC);

        $query = $sql2->prepare("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
        $query->execute();

        $query = $sql2->prepare("DELETE FROM `$table_name` ORDER BY `id` LIMIT 300");
        $query->execute();

        $currentCount = $theCount;
        $Count->execute();
        $theCount = $Count->fetchColumn();

        print "Run: $pass; Found $currentCount traffic entries; Entries left after Run: $theCount\r\n";

        foreach ($trafficData as $id => $row) {

            unset($trafficData[$id]);
            unset($serverid);

            $data_id = $row['id'];
            $ip_src = $row[$column_sourceip];
            $ip_dst = $row[$column_destip];
            $bytes = $row[$column_byte];
            $stamp_updated = $row[$column_date];
            $date=explode(' ', $row[$column_date]);
            $hour=explode(':',$date[1]);
            $day = $date[0] . '  ' . $hour[0] . ':00:00';

            if (searchinnerarray($ip_src,$vserverips) or searchinnerarray($ip_dst,$vserverips)) {
                if (searchinnerarray($ip_src,$vserverips)) {
                    $direction = 'out';
                    $serverid = searchinnerarray($ip_src, $vserverips);

                } else if (searchinnerarray($ip_dst, $vserverips)) {
                    $direction = 'in';
                    $serverid = searchinnerarray($ip_dst, $vserverips);
                }

                if (isset($serverid) and isset($serverIDs[$serverid])) {
                    $userid = $serverIDs[$serverid]['userid'];
                    $resellerid = $serverIDs[$serverid]['resellerid'];

                } else if (isset($serverid)) {
                    $query2 = $sql->prepare("SELECT `userid`,`resellerid` FROM `virtualcontainer` WHERE `id`=? LIMIT 1");
                    $query2->execute(array($serverid));
                    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $userid = $row['userid'];
                        $resellerid = $row['resellerid'];
                        $serverIDs[$serverid]['userid'] = $userid;
                        $serverIDs[$serverid]['resellerid'] = $resellerid;
                    }
                }

            } else if (searchinnerarray($ip_src, $userips)) {
                $direction = 'out';
                $serverid = 0;
                $userids = searchinnerarray($ip_src, $userips);
                $uids = explode('-', $userids);
                $userid = $uids[0];
                $resellerid = $uids[1];

            } else if (searchinnerarray($ip_dst, $userips)) {
                $direction = 'in';
                $serverid = 0;
                $userids = searchinnerarray($ip_dst, $userips);
                $uids = explode('-', $userids);
                $userid = $uids[0];
                $resellerid = $uids[1];
            }

            if (isset($serverid)) {
                if ($direction == 'in') {
                    $ip = $ip_dst;
                    $ipcase = 'ip_dst';

                } else {
                    $ip = $ip_src;
                    $ipcase = 'ip_src';
                }

                $query2 = $sql->prepare("SELECT `id` FROM `traffic_data` WHERE `ip`=? AND `day`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
                $query2->execute(array($ip, $day, $userid, $resellerid));
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $id = $row['id'];
                }

                if ($query2->rowCount()==1) {
                    $query2 = $sql->prepare("UPDATE `traffic_data` SET `$direction`=`$direction`+?,`serverid`=? WHERE `id`=? LIMIT 1");
                    $query2->execute(array($bytes, $id, $serverid));
                } else {
                    $query2 = $sql->prepare("INSERT INTO `traffic_data` (`serverid`,`$direction`,`ip`,`day`,`userid`,`resellerid`) VALUES (?,?,?,?,?,?)");
                    $query2->execute(array($serverid, $bytes, $ip, $day, $userid, $resellerid));
                }
            }
        }
    }

    print "Truncating $table_name\r\n";
    $query = $sql2->prepare("TRUNCATE TABLE `$table_name`");
    $query->execute();

    $query = $sql2->prepare("OPTIMIZE TABLE `$table_name`");
    $query->execute();

	$sql = null;
	$sql2 = null;
}