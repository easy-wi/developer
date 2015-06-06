<?php

/**
 * File: reboot.php.
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
    $timelimit = 600;
}

set_time_limit($timelimit);

define('EASYWIDIR', dirname(__FILE__));
include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/settings.php');
include(EASYWIDIR . '/stuff/methods/functions_gs.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/methods/class_app.php');
include(EASYWIDIR . '/stuff/methods/class_masterserver.php');
include(EASYWIDIR . '/stuff/methods/functions_ts3.php');
include(EASYWIDIR . '/stuff/methods/class_ts3.php');
include(EASYWIDIR . '/stuff/methods/queries_updates.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

if (!isset($ip) or $ui->escaped('SERVER_ADDR', 'server') == $ip or in_array($ip, ipstoarray($rSA['cronjob_ips']))) {
    
    echo "Reboot and Updater started\r\n";    

    if (version_compare(PHP_VERSION,'5.3.0') >= 0){
        $currentTime = new DateTime(date('Y-m-d H:i:s'));
    } else {
        $currentDay = date('j');
        $currentDays = date('t');
        $currentHour = date('G');
    }
    $now = date('Y-m-d', strtotime('now'));
    $sprache = getlanguagefile('gserver', 'uk', 0);

    echo "Fetch version for Teamspeak 3 Server\r\n";

    $query = $sql->prepare("UPDATE `voice_masterserver` SET `latest_version`=? WHERE `bitversion`=?");

    $ts3MasterVersion32 = getTS3Version('server', 'linux', 32);
    $query->execute(array($ts3MasterVersion32['version'], '32'));

    $ts3MasterVersion64 = getTS3Version('server', 'linux', 64);
    $query->execute(array($ts3MasterVersion64['version'], '64'));

    echo "Current versions for Teamspeak 3 Server are {$ts3MasterVersion32['version']} (32bit) and {$ts3MasterVersion64['version']} (64bit)\r\n";

    echo "Fetch version for Minecraft and Bukkit Server\r\n";

    $query = $sql->prepare("SELECT t.`shorten` FROM `servertypes` t LEFT JOIN `rservermasterg` r ON t.`id`=r.`servertypeid` WHERE r.`id` IS NOT NULL AND t.`gameq`='minecraft' GROUP BY t.`shorten` ORDER BY t.`shorten`");
    $query2 = $sql->prepare("UPDATE `servertypes` SET `steamVersion`=?,`downloadPath`=? WHERE `shorten`=?");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        echo 'Retrieving Version for ' . $row['shorten'] . "\r\n";

        $reply = ($row['shorten'] == 'bukkit') ? getCraftBukkitVersion () : getMinecraftVersion();

        if (is_array($reply)) {

            echo 'Version for ' . $row['shorten'] . ' is: ' . $reply['version'] . "\r\n";

            if (strlen($reply['version']) > 1) {
                $query2->execute(array($reply['version'], $reply['downloadPath'], $row['shorten']));
            }
        }
    }

    echo "Fetch version for valves appIDs\r\n";

    $steamVersion = array();

    $query2 = $sql->prepare("UPDATE `servertypes` SET `steamVersion`=? WHERE `appID`=?");

    $query = $sql->prepare("SELECT t.`appID`,t.`shorten` FROM `servertypes` t INNER JOIN `rservermasterg` r ON t.`id`=r.`servertypeid` WHERE t.`appID` IS NOT NULL AND t.`steamgame`!='N' GROUP BY t.`appID` ORDER BY t.`appID`");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        
        if (!in_array($row['appID'], array(null,'', false))) {
            
            $lookUpAppID = workAroundForValveChaos($row['appID'], $row['shorten']);
            $json = webhostRequest('api.steampowered.com', 'easy-wi.com', '/ISteamApps/UpToDateCheck/v0001/?appid=' . $lookUpAppID . '&version=0.0.0.0&format=json');
            $decoded = @json_decode($json);
            
            if ($decoded and !isset($decoded->response->error) and isset($decoded->response->required_version)) {
                $query2->execute(array($decoded->response->required_version, $row['appID']));
                echo 'Version for appID ' . $row['appID'] . ' is: ' . $decoded->response->required_version . "\r\n";
                
            } else if (isset($decoded->response->error)) {
                echo 'Error for appID ' . $row['appID'] . ' is: ' . $decoded->response->error . "\r\n";
                
            } else {
                echo 'Error for appID ' . $row['appID'] . ' is: Could not retrieve JSON string' . "\r\n";
            }
        }
    }

    $webhostdomain = webhostdomain(0);

    $query = $sql->prepare("SELECT `timezone`,`voice_autobackup`,`voice_autobackup_intervall`,`voice_maxbackup`,`down_checks`,`resellerid` FROM `settings`");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $resellerstimezone = $row['timezone'];
        $resellerid = $row['resellerid'];
        $resellerLockupID = $row['resellerid'];
        $voice_autobackup = $row['voice_autobackup'];
        $voice_autobackup_intervall = $row['voice_autobackup_intervall'];
        $voice_maxbackup = $row['voice_maxbackup'];
        $down_checks = $row['down_checks'];
        $stunde = date('G', strtotime($resellerstimezone . ' hour'));
        $next = date('Y-m-d', strtotime('-' . $voice_autobackup_intervall .' day'));
        
        $query2 = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `active`='Y' AND `resellerid`=:reseller_id");
        $query2->execute(array(':aeskey' => $aeskey, ':reseller_id' => $resellerid));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
            $ts3masterid = $row2['id'];
            
            $query3 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `masterserver`=? LIMIT 1");
            $query3->execute(array($ts3masterid));
            
            if ($query3->rowCount() > 0) {
                unset($connect_ssh2, $ssh2, $badLogin);
                $ts3masternotified = $row2['notified'];
                $addedby = $row2['addedby'];
                $queryport = $row2['queryport'];
                $querypassword = $row2['decryptedquerypassword'];
                $resellerid = $row2['resellerid'];
                $autorestart = $row2['autorestart'];
                
                if ($addedby == 2) {
                    $queryip = $row2['ssh2ip'];
                    
                } else if ($addedby == 1) {
                    $query3 = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query3->execute(array($row2['rootid'], $resellerid));
                    $queryip = $query3->fetchColumn();
                }
                
                $tsdnsExternalActive = false;
                
                if (isid($row2['tsdnsServerID'],19)) {
                    $query3 = $sql->prepare("SELECT `id` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=? LIMIT 1");
                    $query3->execute(array($row2['tsdnsServerID']));
                    if ($query3->rowCount() > 0) {
                        $tsdnsExternalActive = true;
                    }
                }

                $split_config=preg_split('/\//', $row2['serverdir'], -1, PREG_SPLIT_NO_EMPTY);
                $folderfilecount=count($split_config)-1;
                $i = 0;
                $folders= (substr($row2['serverdir'],0,1) == '/') ? 'cd  /' : 'cd ';

                while ($i<=$folderfilecount) {
                    $folders = $folders.$split_config[$i] . '/';
                    $i++;
                }

                $folders =  ($folders == 'cd ') ? '' : $folders . ' && ';


                $tsdown = false;
                $tsdnsdown = false;
                $connection = new TS3($queryip, $queryport, 'serveradmin', $querypassword);
                $errorcode = $connection->errorcode;

                if (strpos($errorcode,'error id=0') === false) {
                    $connection->CloseConnection();
                    unset($connection);
                    sleep(1);
                    $connection=new TS3($queryip, $queryport, 'serveradmin', $querypassword);
                    $errorcode = $connection->errorcode;
                }

                if (strpos($errorcode,'error id=0') === false) {
                    $connection->CloseConnection();
                    unset($connection);
                    $tsdown = true;
                    print "TS3 Query Error: ".$errorcode."\r\n";
                    $restartreturn="TS3";
                }

                if ($row2['usedns'] == 'Y' and $tsdnsExternalActive == false) {
                    $tsdnscheck = @fsockopen ($queryip,41144, $errno, $errstr,5);

                    if (!is_resource($tsdnscheck)) {
                        sleep(1);
                        $tsdnscheck = @fsockopen ($queryip,41144, $errno, $errstr,5);
                    }

                    if (!is_resource($tsdnscheck)) {
                        print "TSDNS Error: ".$errno.' ('.$errstr.")\r\n";
                        $tsdnsdown = true;

                        if (isset($restartreturn)) {
                            $restartreturn .=" and internal TSDNS";
                        } else {
                            $restartreturn="internal TSDNS";
                        }
                    }

                } else if ($row2['usedns'] == 'Y' and $tsdnsExternalActive == true) {
                    print "Skip TSDNS since external is used\r\n";
                }

                $cmds = array();

                if ($tsdown == true or $tsdnsdown == true) {

                    $ts3masternotified++;

                    if ($ts3masternotified==$down_checks) {

                        if ($resellerid==0) {
                            $query3 = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `accounttype`='a' AND `resellerid`=0");
                            $query3->execute();

                        } else {
                            $query3 = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `id`=? LIMIT 1");
                            $query3->execute(array($resellerid));
                        }

                        while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
                            if ($row3['mail_serverdown'] == 'Y') {
                                sendmail('emaildownrestart', $row3['id'], $queryip.' ('.$restartreturn.')','');
                            }
                        }
                    }

                    $query3 = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=? WHERE `id`=? LIMIT 1");
                    $query3->execute(array($ts3masternotified, $ts3masterid));

                    if ($autorestart == 'Y' and $ts3masternotified >= $down_checks) {

                        if ($row2['bitversion'] == '32') {
                            $tsbin='ts3server_linux_x86';
                            $tsdnsbin='tsdnsserver_linux_x86';

                        } else {
                            $tsbin='ts3server_linux_amd64';
                            $tsdnsbin='tsdnsserver_linux_amd64';
                        }


                        if ($tsdown == true) {
                            $cmds[] = $folders . 'function restart1 () { if [ "`ps fx | grep '.$tsbin.' | grep -v grep`" == "" ]; then ./ts3server_startscript.sh start > /dev/null & else ./ts3server_startscript.sh restart > /dev/null & fi }; restart1& ';
                        }

                        if ($row2['usedns'] == 'Y' and $tsdnsdown == true) {
                            $cmds[] = $folders.'cd tsdns && function restart2 () { if [ "`ps fx | grep '.$tsdnsbin.' | grep -v grep`" == "" ]; then ./'.$tsdnsbin.' > /dev/null & else ./'.$tsdnsbin.' --update > /dev/null & fi }; restart2& ';
                        }

                    } else if ($autorestart == 'Y') {
                        print "Do not restart TS3/TSDNS ${queryip} since failcount is only ${ts3masternotified} and ${down_checks} is required for restart \r\n";
                    }
                }



                if ($ts3masternotified > 0 and $tsdown !== true and $tsdnsdown !== true) {
                    $query3 = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=0 WHERE `id`=? LIMIT 1");
                    $query3->execute(array($ts3masterid));
                }

                $query3 = $sql->prepare("SELECT * FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=?");
                $query3->execute(array($ts3masterid, $resellerid));
                while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {

                    $ts3id = $row3['id'];
                    $serverCreated = $row3['serverCreated'];
                    $ts3userid = $row3['userid'];
                    $localserverid = $row3['localserverid'];

                    if ($stunde == '00' or $serverCreated == null) {

                        $resetTraffic = false;

                        $createdTime = new DateTime($serverCreated);
                        $interval = $createdTime->diff($currentTime);

                        if ($interval->d == 0 and $interval->m>0) {
                            $resetTraffic = true;
                        }

                        if ($resetTraffic == true and $serverCreated != null) {
                            $query4 = $sql->prepare("UPDATE `voice_server` SET `filetraffic`=0,`lastfiletraffic`=0 WHERE `id`=? LIMIT 1");
                            $query4->execute(array($ts3id));
                            $connection->ImportModServer($localserverid, $row3['slots'], $row3['ip'], $row3['port'], array('virtualserver_max_download_total_bandwidth' => $row3['max_download_total_bandwidth'], 'virtualserver_max_upload_total_bandwidth' => $row3['max_upload_total_bandwidth']));
                        } else if ($serverCreated==null) {
                            $query4 = $sql->prepare("UPDATE `voice_server` SET `filetraffic`=0,`lastfiletraffic`=0,`serverCreated`=NOW() WHERE `id`=? LIMIT 1");
                            $query4->execute(array($ts3id));
                            $connection->ImportModServer($localserverid, $row3['slots'], $row3['ip'], $row3['port'], array('virtualserver_max_download_total_bandwidth' => $row3['max_download_total_bandwidth'], 'virtualserver_max_upload_total_bandwidth' => $row3['max_upload_total_bandwidth']));
                        }
                    }

                    if ($voice_autobackup == 'Y' and $stunde == 5 and $row3['active'] == 'Y' and $row3['backup'] == 'Y') {

                        $name = 'Autobackup';
                        $backupcount = 0;

                        unset($last);

                        $query4 = $sql->prepare("SELECT `id`,`date`,`name` FROM `voice_server_backup` WHERE `sid`=? AND `uid`=? AND `resellerid`=? ORDER BY `id` ASC");
                        $query4->execute(array($ts3id, $ts3userid, $resellerid));
                        while ($row4 = $query4->fetch(PDO::FETCH_ASSOC)) {

                            $backupcount++;
                            $date = $row4['date'];

                            if ($row4['name'] == 'Autobackup') {
                                $last=date('Y-m-d', strtotime($date));
                            }
                        }

                        $stunde = date('G', strtotime("$resellerstimezone hour"));

                        if ($backupcount == 0 or !isset($last) or (isset($last) and $last < $next)) {

                            $toomuch = $backupcount + 1 - $voice_maxbackup;

                            if ($toomuch > 0) {

                                $query4 = $sql->prepare("SELECT `id` FROM `voice_server_backup` WHERE `sid`=? AND `uid`=? AND `resellerid`=? ORDER BY `id` ASC LIMIT $toomuch");
                                $query4->execute(array($ts3id, $ts3userid, $resellerid));
                                while ($row4 = $query4->fetch(PDO::FETCH_ASSOC)) {
                                    $query5 = $sql->prepare("DELETE FROM `voice_server_backup` WHERE `id`=? AND `uid`=? AND `resellerid`=? LIMIT 1");
                                    $query5->execute(array($row4['id'], $ts3userid, $resellerid));
                                    $backupfolder='backups/virtualserver_'.$localserverid . '/';
                                    $cmds[] = 'cd '.$folders.' && function backup () { nice -n +19 rm -f '.$backupfolder.$row4['id'].'.tar.bz2; }; backup& ';
                                }

                            }

                            $rawsnapshot = $connection->Snapshotcreate($localserverid);
                            $snapshot = gzcompress($rawsnapshot, 9);

                            $query4 = $sql->prepare("INSERT INTO `voice_server_backup` (`sid`,`uid`,`name`,`snapshot`,`date`,`resellerid`) VALUES(?,?,?,?,NOW(),?)");
                            $query4->execute(array($ts3id, $ts3userid, $name, $snapshot, $resellerid));

                            $query4 = $sql->prepare("SELECT `id` FROM `voice_server_backup` WHERE `sid`=? AND `uid`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
                            $query4->execute(array($ts3id, $ts3userid, $resellerid));
                            while ($row4 = $query4->fetch(PDO::FETCH_ASSOC)) {
                                $filefolder = 'files/virtualserver_' . $localserverid . '/';
                                $backupfolder = 'backups/virtualserver_' . $localserverid . '/';

                                $cmds[] ='cd '.$folders.' && function backup () { mkdir -p ' . $backupfolder . ' && nice -n +19 tar cfj ' . $backupfolder . $row4['id'] . '.tar.bz2 ' . $filefolder . '; }; backup& ';

                                print "Creating backup for ts3 server: " . $row3['ip'] . ':' . $row3['port'] . "\r\n";
                            }

                        }
                    }
                }

                if (count($cmds) > 0) {

                    if (ssh2_execute('vm', $ts3masterid, $cmds)) {
                        print "Restarting: $restartreturn\r\n";
                    } else {
                        print "Failed restarting: $restartreturn\r\n";
                    }

                    if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                        print_r($cmds);
                    }
                }

                if (isset($connection) and is_object($connection)) {
                    $connection->CloseConnection();
                }

                usleep(500000);

            }
        }

        $currenttime = strtolower(date('D', strtotime("$resellerstimezone hour"))) . '_' . date('G',strtotime("$resellerstimezone hour"));

        $query2 = $sql->prepare("SELECT g.`id`,CONCAT(g.`serverip`,':',g.`port`) AS `server`,g.`protected`,AES_DECRYPT(`ftpbackup`,?) AS `backup` FROM `gsswitch` AS g INNER JOIN `userdata` AS u ON u.`id`=g.`userid` WHERE g.`rootID`=? AND g.`active`='Y' AND g.`lendserver`='N' AND g.`stopped`='N' AND u.`active`='Y'");
        $query3 = $sql->prepare("SELECT r.*,s.`id` AS `server_id` FROM `gserver_restarts` AS r INNER JOIN `servertypes` AS t ON t.`shorten`=r.`gsswitch` AND t.`resellerid`=r.`resellerid` INNER JOIN `serverlist` AS s ON s.`servertype`=t.`id` AND s.`switchID`=r.`switchID` WHERE r.`switchID`=? AND r.`restarttime`=? LIMIT 1");
        $query4 = $sql->prepare("UPDATE `gsswitch` SET `serverid`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query5 = $sql->prepare("UPDATE `serverlist` SET `anticheat`=?,`map`=?,`mapGroup`=?,`servertemplate`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query6 = $sql->prepare("UPDATE `gsswitch` SET `ppassword`=AES_ENCRYPT(?,?) WHERE `id`=? LIMIT 1");

        $query = $sql->prepare("SELECT `id`,`resellerid` FROM `rserverdata` WHERE `active`='Y'");
        $query->execute();
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $resellerLockupID = $row['resellerid'];

            $appServer = new AppServer($row['id']);

            $query2->execute(array($aeskey, $row['id']));
            while($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                $query3->execute(array($row2['id'], $currenttime));
                while($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {

                    $query4->execute(array($row3['server_id'], $row3['switchID'], $resellerLockupID));
                    $query5->execute(array($row3['anticheat'], $row3['map'], $row3['mapGroup'], $row3['template'], $row3['server_id'], $resellerLockupID));

                    $appServer->getAppServerDetails($row3['switchID']);

                    if ($row3['restart'] == 'N' and $row3['worldsafe'] == 'Y') {

                        $appServer->mcWorldSave();

                        echo 'MC worldsave: ' . $row2['server'] . "\r\n";
                    }

                    if ($row3['restart'] == 'N' and $row3['upload'] == 'Y') {

                        $appServer->demoUpload();

                        echo 'Demo upload for: ' . $row2['server'] . "\r\n";
                    }

                    if ($row3['restart'] == 'Y') {

                        if ($row3['protected'] == 'Y' and $row2['protected'] == 'N') {

                            $appServer->stopAppHard();

                            $query6->execute(array(passwordgenerate(20), $aeskey, $row3['switchID']));

                            $appServer->getAppServerDetails($row3['switchID']);
                            $appServer->removeApp($row3['template']);
                            $appServer->addApp($row3['template']);
                        }

                        $appServer->startApp();

                        echo 'Restarting server: ' . $row2['server'] . "\r\n";
                    }

                    if ($row3['backup'] == 'Y') {

                        $appServer->backupCreate($row2['backup']);

                        echo 'Backup server: ' . $row2['server'] . "\r\n";
                    }
                }
            }

            $appServer->execute();

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                print implode("\r\n", $appServer->debug()) . "\r\n";
            }
        }
    }
    
    $newsInclude = true;
    $printToConsole = true;
    print "Check for new news feeds\r\n";
    include(EASYWIDIR . '/stuff/methods/feeds_function.php');
    if (isset($template_file)) {
        print $template_file."\r\n";
    }
    
    print "Cleaning Logs\r\n";
    $query = $sql->prepare("DELETE FROM `userlog` WHERE DATEDIFF(NOW(),`logdate`)>31");
    $query->execute();
    $query = $sql->prepare("DELETE FROM `mail_log` WHERE DATEDIFF(NOW(),`date`)>31");
    $query->execute();

    if (date('G') == 5) {

        $query = $sql->prepare("SELECT `ssh2ip`,`description`,`resellerid` FROM `voice_masterserver` WHERE `active`='Y' AND `latest_version`!=`local_version`");
        $query->execute();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $serverName = (strlen($row['description']) == 0) ? $row['ssh2ip'] : $row['ssh2ip'] . ' ' . $row['description'];

            print "Sending TS3 update information for server $serverName\r\n";

            $query2 = $sql->prepare("SELECT `id` FROM `userdata` WHERE ((`resellerid`=? AND `accounttype`='a') OR (`id`=? AND `accounttype`='r')) AND `mail_gsupdate`='Y'");
            $query2->execute(array($row['resellerid'], $row['resellerid']));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                sendmail('emailvoicemasterold', $row2['id'], $serverName, '');
            }
        }

        print "Reparing tables\r\n";
        $query = $sql->prepare("REPAIR TABLE `addons`,`addons_installed`,`api_external_auth`,`api_ips`,`api_settings`,`badips`,`dhcpdata`,`eac`,`easywi_version`,`gserver_restarts`,`gsstatus`,`gsswitch`,`imprints`,`jobs`,`lendedserver`,`lendsettings`,`lendstats`,`mail_log`,`mysql_external_dbs`,`mysql_external_servers`,`page_pages`,`page_pages_text`,`page_settings`,`page_terms`,`page_terms_used`,`resellerdata`,`resellerimages`,`rserverdata`,`rservermasterg`,`serverlist`,`servertypes`,`settings`,`test`,`tickets`,`ticket_topics`,`traffic_data`,`traffic_data_day`,`traffic_settings`,`userdata`,`usergroups`,`userlog`,`userpermissions`,`virtualcontainer`,`virtualhosts`,`voice_masterserver`,`voice_server`,`voice_server_backup`,`voice_server_stats`,`voice_stats_settings`");
        $query->execute();

        print "Optimizing tables\r\n";
        $query = $sql->prepare("OPTIMIZE TABLE `addons`,`addons_installed`,`api_external_auth`,`api_ips`,`api_settings`,`badips`,`dhcpdata`,`eac`,`easywi_version`,`gserver_restarts`,`gsstatus`,`gsswitch`,`imprints`,`jobs`,`lendedserver`,`lendsettings`,`lendstats`,`mail_log`,`mysql_external_dbs`,`mysql_external_servers`,`page_pages`,`page_pages_text`,`page_settings`,`page_terms`,`page_terms_used`,`resellerdata`,`resellerimages`,`rserverdata`,`rservermasterg`,`serverlist`,`servertypes`,`settings`,`test`,`tickets`,`ticket_topics`,`traffic_data`,`traffic_data_day`,`traffic_settings`,`userdata`,`usergroups`,`userlog`,`userpermissions`,`virtualcontainer`,`virtualhosts`,`voice_masterserver`,`voice_server`,`voice_server_backup`,`voice_server_stats`,`voice_stats_settings`");
        $query->execute();
    }

    $query = $sql->prepare("UPDATE `settings` SET `lastCronReboot`=UNIX_TIMESTAMP()");
    $query->execute();
    
} else {
    header('Location: login.php');
    die('Statuscheck can only be run via console and or a cronjob');
}