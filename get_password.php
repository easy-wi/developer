<?php

/**
 * File: get_password.php.
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

include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/config.php');
include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/settings.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

$ip = $ui->ip('REMOTE_ADDR', 'server');

if ($ui->st('w', 'get') == 'ms' and $ui->username('shorten', 50, 'get')) {

	$query = $sql->prepare("SELECT r.`id`,r.`serverid`,r.`resellerid`,r.`installing`,r.`updating`,r.`servertypeid`,d.`resellerid` AS `userid`,s.`steamVersion`,r.`localVersion` FROM `rservermasterg` r INNER JOIN `rserverdata` d ON r.`serverid`=d.`id` INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE s.`shorten`=? AND (d.`ip`=? OR d.`altips` LIKE ?) LIMIT 1");
	$query->execute(array($ui->username('shorten', 50, 'get'), $ip, '%' . $ip . '%'));

	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        if ($row['installing'] != 'N' or $row['updating'] != 'N') {

            $query2 = $sql->prepare("UPDATE `rservermasterg` SET `installing`='N',`updating`='N' WHERE `id`=? LIMIT 1");
            $query2->execute(array($row['id']));

            $query2 = $sql->prepare("UPDATE `rservermasterg` SET `localVersion`=? WHERE `id`=? LIMIT 1");
            $query2->execute(array($row['steamVersion'], $row['id']));

            if ($query2->rowCount() > 0) {

                $query2 = $sql->prepare("SELECT g.`id`,g.`userid`,CONCAT(g.`serverip`,':',g.`port`) AS `name` FROM `gsswitch` AS g INNER JOIN `serverlist` AS s ON g.`serverid`=s.`id` WHERE g.`rootID`=? AND s.`servertype`=? AND g.`active`='Y' AND g.`stopped`='N' AND g.`updateRestart`='Y'");
                $query2->execute(array($row['serverid'], $row['servertypeid']));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                    $query3 = $sql->prepare("UPDATE `gsswitch` SET `jobPending`='Y' WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query3->execute(array($row2['id'], $row['resellerid']));

                    $query3 = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='gs' AND (`status` IS NULL OR `status`='1') AND (`action`='re' OR `action`='st') AND `affectedID`=? and `resellerID`=?");
                    $query3->execute(array($row2['id'], $row['resellerid']));

                    $query3 = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('R','gs',?,?,?,?,?,NULL,NOW(),'re',?)");
                    $query3->execute(array($row['serverid'], $row['resellerid'], $row2['id'], $row2['userid'], $row2['name'], $row['resellerid']));
                }

                $query2 = $sql->prepare("SELECT `id` FROM `userdata` WHERE ((`resellerid`=? AND `accounttype`='a') OR (`id`=? AND `accounttype`='r')) AND `mail_gsupdate`='Y'");
                $query2->execute(array($row['resellerid'], $row['resellerid']));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    sendmail('emailgserverupdate', $row2['id'], $ip, $ui->username('shorten', 50, 'get'));
                }
            }
        }
	}

	echo 'ok';

} else if ($ui->st('w', 'get') == 'bu' and $ui->id('id',19, 'get') and $ui->username('shorten', 50, 'get') and $ui->ip('ip', 'get')) {

    $query = $sql->prepare("SELECT g.`id`,CONCAT(g.`serverip`,':',g.`port`) AS `server`,g.`userid` FROM `gsswitch` g INNER JOIN `rserverdata` r ON g.`rootID`=r.`id` WHERE r.`ip`=? AND s.`customer`=? AND g.`port`=? AND g.`serverip`=? LIMIT 1");
    $query2 = $sql->prepare("SELECT 1 FROM `userdata` WHERE `mail_backup`='Y' AND `id`=? LIMIT 1");

    $query->execute(array($ip,$ui->username('shorten', 50, 'get'),$ui->id('id',19, 'get'),$ui->ip('ip', 'get')));
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $query2->execute(array($row['userid']));

		if ($query2->rowCount() > 0) {
			sendmail('emailbackup', $row['userid'], $row['server'], '');
		}
	}

    echo 'ok';

} else if ($ui->st('w', 'get') == 'rb' and $ui->id('id',19, 'get') and $ui->username('shorten', 50, 'get') and $ui->ip('ip', 'get')) {

    $query = $sql->prepare("SELECT g.`id`,CONCAT(g.`serverip`,':',g.`port`) AS `server`,g.`userid` FROM `gsswitch` g INNER JOIN `rserverdata` r ON g.`rootID`=r.`id` INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE r.`ip`=? AND u.`cname`=? AND g.`port`=? AND g.`serverip`=? LIMIT 1");
    $query2 = $sql->prepare("SELECT `mail_backup` FROM `userdata` WHERE `mail_backup`='Y' AND `id`=? LIMIT 1");

    $query->execute(array($ip,$ui->username('shorten', 50, 'get'),$ui->id('id',19, 'get'),$ui->ip('ip', 'get')));
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $query2->execute(array($row['userid']));
		while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
			sendmail('emailbackuprestore', $row['userid'], $row['server'], '');
		}
	}

    echo 'ok';

} else {

    $query = $sql->prepare("SELECT AES_DECRYPT(`pass`,?) AS `decryptedpass`,`ip`,`userid` FROM `virtualcontainer` WHERE `ip`=? AND `status`='2' LIMIT 1");
    $query2 = $sql->prepare("UPDATE `virtualcontainer` SET `status`='0' WHERE `ip`=? LIMIT 1");
    $query3 = $sql->prepare("SELECT `mail_vserver` FROM `userdata` WHERE `id`=? LIMIT 1");

    $query->execute(array($aeskey, $ip));
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

		$pass = $row['decryptedpass'];
		$userid = $row['userid'];
		$ip = $row['ip'];
        $query2->execute(array($ip));
        $query3->execute(array($userid));
		while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
			if ($row3['mail_vserver'] == 'Y') {
				sendmail('emailvinstall', $userid, $ip, $pass);
			}
		}
	}

    echo (isset($pass)) ? $pass : 'old';
}

$sql = null;