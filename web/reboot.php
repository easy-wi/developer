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
    $ip=$_SERVER['REMOTE_ADDR'];
    if (isset($_GET['timeout']) and is_numeric($_GET['timeout'])) {
        $timelimit=$_GET['timeout'];
    } else {
        $timelimit=ini_get('max_execution_time')-10;
    }
} else {
    $timelimit=600;
}
set_time_limit($timelimit);
if (!isset($ip) or $_SERVER['SERVER_ADDR']==$ip) {
    define('EASYWIDIR',dirname(__FILE__));
    echo "Reboot and Updater started\r\n";
    include('stuff/vorlage.php');
    include('stuff/class_validator.php');
    include('stuff/functions.php');
    include('stuff/settings.php');
    include('stuff/ssh_exec.php');
    include('stuff/class_masterserver.php');
    include('stuff/class_voice.php');
    if (version_compare(PHP_VERSION,'5.3.0')>=0){
        $currentTime=new DateTime(date('Y-m-d H:i:s'));
    } else {
        $currentDay=date('j');
        $currentDays=date('t');
        $currentHour=date('G');
    }
    $now=date('Y-m-d',strtotime("now"));
    $aesfilecvar=getconfigcvars("stuff/keyphrasefile.php");
    $aeskey=$aesfilecvar['aeskey'];
    $sprache=getlanguagefile('gserver','uk',0,$sql);
    echo "Fetch version for valves appIDs\r\n";
    $query=$sql->prepare("SELECT t.`appID`,t.`shorten` FROM `servertypes` t LEFT JOIN `rservermasterg` r ON t.`id`=r.`servertypeid` WHERE r.`id` IS NOT NULL AND t.`appID` IS NOT NULL AND t.`steamgame`!='N' GROUP BY t.`appID` ORDER BY t.`appID`");
    $query2=$sql->prepare("UPDATE `servertypes` SET `steamVersion`=? WHERE `appID`=?");
    $query->execute();
    $steamVersion=array();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!in_array($row['appID'],array(null,'',false))) {
            $lookUpAppID=workAroundForValveChaos($row['appID'],$row['shorten']);
            $json=webhostRequest('api.steampowered.com','easy-wi.com','/ISteamApps/UpToDateCheck/v0001/?appid='.$lookUpAppID.'&version=0.0.0.0&format=json');
            $decoded=@json_decode($json);
            if ($decoded and !isset($decoded->response->error) and isset($decoded->response->required_version)) {
                $query2->execute(array($decoded->response->required_version,$row['appID']));
                echo "Version for appID ".$row['appID']." is: ".$decoded->response->required_version."\r\n";
            } else if (isset($decoded->response->error)) {
                echo "Error for appID ".$row['appID']." is: ".$decoded->response->error."\r\n";
            } else {
                echo "Error for appID ".$row['appID']." is: Could not retrieve JSON string\r\n";
            }
        }
    }
    $webhostdomain=webhostdomain(0,$sql);
    $query=$sql->prepare("SELECT `timezone`,`voice_autobackup`,`voice_autobackup_intervall`,`voice_maxbackup`,`down_checks`,`resellerid` FROM `settings`");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $resellerstimezone=$row['timezone'];
        $resellerid=$row['resellerid'];
        $voice_autobackup=$row['voice_autobackup'];
        $voice_autobackup_intervall=$row['voice_autobackup_intervall'];
        $voice_maxbackup=$row['voice_maxbackup'];
        $down_checks=$row['down_checks'];
        $stunde=date('G',strtotime("$resellerstimezone hour"));
        $next=date('Y-m-d',strtotime("-$voice_autobackup_intervall day"));
        $query2=$sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `active`='Y' AND `resellerid`=:reseller_id");
        $query2->execute(array(':aeskey'=>$aeskey,':reseller_id'=>$resellerid));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $ts3masterid=$row2['id'];
            $query3=$sql->prepare("SELECT `id` FROM `voice_server` WHERE `masterserver`=? LIMIT 1");
            $query3->execute(array($ts3masterid));
            if ($query3->rowcount()>0) {
                unset($connect_ssh2,$ssh2,$badLogin);
                $ts3masternotified=$row2['notified'];
                $addedby=$row2['addedby'];
                $queryport=$row2['queryport'];
                $querypassword=$row2['decryptedquerypassword'];
                $resellerid=$row2['resellerid'];
                $autorestart=$row2['autorestart'];
                if ($addedby=='2') {
                    $queryip=$row2['ssh2ip'];
                } else if ($addedby=='1') {
                    $query3=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query3->execute(array($row2['rootid'],$resellerid));
                    $queryip=$query3->fetchColumn();
                }
                $tsdnsExternalActive=false;
                if (isid($row2['tsdnsServerID'],19)) {
                    $query3=$sql->prepare("SELECT `id` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=? LIMIT 1");
                    $query3->execute(array($row2['tsdnsServerID']));
                    if ($query3->rowCount()>0) {
                        $tsdnsExternalActive=true;
                    }
                }
                if ($row2['publickey']=='Y') {
                    $pubkey="keys/".$row2['keyname'].".pub";
                    $key="keys/".$row2['keyname'];
                    if (file_exists($pubkey) and file_exists($key)) {
                        $ssh2= @ssh2_connect($queryip,$row2['decryptedssh2port'],array('hostkey'=>'ssh-rsa'));
                    }
                } else {
                    $ssh2= @ssh2_connect($queryip,$row2['decryptedssh2port']);
                }
                if ($ssh2) {
                    if ($row2['publickey']=='Y') {
                        $connect_ssh2= @ssh2_auth_pubkey_file($ssh2,$row2['decryptedssh2user'],$pubkey,$key);
                    } else {
                        $connect_ssh2= @ssh2_auth_password($ssh2,$row2['decryptedssh2user'],$row2['decryptedssh2password']);
                    }
                    if ($connect_ssh2) {
                        $split_config=preg_split('/\//',$row2['serverdir'], -1, PREG_SPLIT_NO_EMPTY);
                        $folderfilecount=count($split_config)-1;
                        $i=0;
                        if (substr($row2['serverdir'],0,1)=='/') {
                            $folders='cd  /';
                        } else {
                            $folders='cd ';
                        }
                        while ($i<=$folderfilecount) {
                            $folders=$folders.$split_config["$i"]."/";
                            $i++;
                        }
                        if ($folders=='cd ') {
                            $folders='';
                        } else {
                            $folders=$folders.' && ';
                        }
                    } else {
                        $badLogin=true;
                        print "Error: Bad logindata\r\n";
                    }
                } else {
                    $badLogin=true;
                    print "Error: Can not connect via ssh2\r\n";
                }
                if (!isset($badLogin) and $connect_ssh2) {
                    $tsdown=false;
                    $tsdnsdown=false;
                    $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
                    $errorcode=$connection->errorcode;
                    if (strpos($errorcode,'error id=0') === false) {
                        $connection->CloseConnection();
                        unset($connection);
                        sleep(1);
                        $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
                        $errorcode=$connection->errorcode;
                    }
                    if (strpos($errorcode,'error id=0') === false) {
                        $connection->CloseConnection();
                        unset($connection);
                        $tsdown=true;
;                        print "TS3 Query Error: ".$errorcode."\r\n";
                        $restartreturn="TS3";
                    }
                    if ($row2['usedns']=='Y' and $tsdnsExternalActive==false) {
                        $tsdnscheck=@fsockopen ($queryip,41144,$errno,$errstr,5);
                        if (!is_resource($tsdnscheck)) {
                            sleep(1);
                            $tsdnscheck=@fsockopen ($queryip,41144,$errno,$errstr,5);
                        }
                        if (!is_resource($tsdnscheck)) {
                            print "TSDNS Error: ".$errno.' ('.$errstr.")\r\n";
                            $tsdnsdown=true;
                            if (isset($restartreturn)) {
                                $restartreturn .=" and internal TSDNS";
                            } else {
                                $restartreturn="internal TSDNS";
                            }
                        }
                    } else if ($row2['usedns']=='Y' and $tsdnsExternalActive==true) {
                        print "Skip TSDNS since external is used\r\n";
                    }
                    if ($tsdown==true or $tsdnsdown==true) {
                        $ts3masternotified++;
                        if ($ts3masternotified==$down_checks) {
                            if ($resellerid==0) {
                                $query3=$sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `accounttype`='a' AND `resellerid`=0");
                                $query3->execute();
                            } else {
                                $query3=$sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `id`=? LIMIT 1");
                                $query3->execute(array($resellerid));
                            }
                            foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                                if ($row3['mail_serverdown']=='Y') {
                                    sendmail('emaildownrestart',$row3['id'],$queryip.' ('.$restartreturn.')','',$sql);
                                }
                            }
                        }
                        $query3=$sql->prepare("UPDATE `voice_masterserver` SET `notified`=? WHERE `id`=? LIMIT 1");
                        $query3->execute(array($ts3masternotified,$ts3masterid));
                        if ($autorestart=='Y' and $ts3masternotified>=$down_checks) {
                            if ($row2['bitversion']=='32') {
                                $tsbin='ts3server_linux_x86';
                                $tsdnsbin='tsdnsserver_linux_x86';
                            } else {
                                $tsbin='ts3server_linux_amd64';
                                $tsdnsbin='tsdnsserver_linux_amd64';
                            }
                            $ssh2cmd=$folders.'function restart1 () { if [ "`ps fx | grep '.$tsbin.' | grep -v grep`" == "" ]; then ./ts3server_startscript.sh start > /dev/null & else ./ts3server_startscript.sh restart > /dev/null & fi }; restart1& ';
                            if ($tsdown==true) {
                                echo ssh2_exec($ssh2,$ssh2cmd);
                            }
                            if ($row2['usedns']=='Y') {
                                if ($tsdnsdown==true) {
                                    $ssh2cmd2=$folders.'cd tsdns && function restart2 () { if [ "`ps fx | grep '.$tsdnsbin.' | grep -v grep`" == "" ]; then ./'.$tsdnsbin.' > /dev/null & else ./'.$tsdnsbin.' --update > /dev/null & fi }; restart2& ';
                                    echo ssh2_exec($ssh2,$ssh2cmd2);
                                }
                            }
                            print "Restarting: $restartreturn\r\n";
                        }
                    } else {
                        if ($ts3masternotified>0) {
                            $query3=$sql->prepare("UPDATE `voice_masterserver` SET `notified`=0 WHERE `id`=? LIMIT 1");
                            $query3->execute(array($ts3masterid));
                        }
                        $query3=$sql->prepare("SELECT `active`,`backup`,`id`,`ip`,`port`,`slots`,`userid`,`localserverid`,`max_download_total_bandwidth`,`max_upload_total_bandwidth`,`serverCreated` FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=?");
                        $query3->execute(array($ts3masterid,$resellerid));
                        foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                            $ts3id=$row3['id'];
                            $serverCreated=$row3['serverCreated'];
                            $ts3userid=$row3['userid'];
                            $localserverid=$row3['localserverid'];
                            if ($stunde=='00' or $serverCreated==null) {
                                $resetTraffic=false;
                                if (version_compare(PHP_VERSION,'5.3.0')>=0){
                                    $createdTime=new DateTime($serverCreated);
                                    $interval=$createdTime->diff($currentTime);
                                    if ($interval->d==0 and $interval->m>0) {
                                        $resetTraffic=true;
                                    }
                                } else {
                                    $createdDay=date('j',strtotime($serverCreated));
                                    $createdDays=date('t',strtotime($serverCreated));
                                    if (($createdDay==$createdDays and $currentDays==$currentDay) or ($createdDay!=$createdDays and $createdDay==$currentDay)) {
                                        $resetTraffic=true;
                                    }
                                }
                                if ($resetTraffic==true and $serverCreated!=null) {
                                    $query4=$sql->prepare("UPDATE `voice_server` SET `filetraffic`=0,`lastfiletraffic`=0 WHERE `id`=? LIMIT 1");
                                    $query4->execute(array($ts3id));
                                    $connection->ImportModServer($localserverid,$row3['slots'],$row3['ip'],$row3['port'],array('virtualserver_max_download_total_bandwidth'=>$row3['max_download_total_bandwidth'],'virtualserver_max_upload_total_bandwidth'=>$row3['max_upload_total_bandwidth']));
                                } else if ($serverCreated==null) {
                                    $query4=$sql->prepare("UPDATE `voice_server` SET `filetraffic`=0,`lastfiletraffic`=0,`serverCreated`=NOW() WHERE `id`=? LIMIT 1");
                                    $query4->execute(array($ts3id));
                                    $connection->ImportModServer($localserverid,$row3['slots'],$row3['ip'],$row3['port'],array('virtualserver_max_download_total_bandwidth'=>$row3['max_download_total_bandwidth'],'virtualserver_max_upload_total_bandwidth'=>$row3['max_upload_total_bandwidth']));
                                }
                            }
                            if ($voice_autobackup=='Y' and $stunde=='5' and $row3['active']=='Y' and $row3['backup']=='Y') {
                                $name='Autobackup';
                                $backupcount=0;
                                unset($last);
                                $query4=$sql->prepare("SELECT `id`,`date`,`name` FROM `voice_server_backup` WHERE `sid`=? AND `uid`=? AND `resellerid`=? ORDER BY `id` ASC");
                                $query4->execute(array($ts3id,$ts3userid,$resellerid));
                                foreach ($query4->fetchAll(PDO::FETCH_ASSOC) as $row4) {
                                    $backupcount++;
                                    $date=$row4['date'];
                                    if ($row4['name']=='Autobackup') {
                                        $last=date('Y-m-d',strtotime($date));
                                    }
                                }
                                $stunde=date('G',strtotime("$resellerstimezone hour"));
                                if ($backupcount==0 or !isset($last) or (isset($last) and $last<$next)) {
                                    $toomuch=$backupcount+1-$voice_maxbackup;
                                    if ($toomuch>0) {
                                        $query4=$sql->prepare("SELECT `id` FROM `voice_server_backup` WHERE `sid`=? AND `uid`=? AND `resellerid`=? ORDER BY `id` ASC LIMIT $toomuch");
                                        $query4->execute(array($ts3id,$ts3userid,$resellerid));
                                        foreach ($query4->fetchAll(PDO::FETCH_ASSOC) as $row4) {
                                            $query5=$sql->prepare("DELETE FROM `voice_server_backup` WHERE `id`=? AND `uid`=? AND `resellerid`=? LIMIT 1");
                                            $query5->execute(array($row4['id'],$ts3userid,$resellerid));
                                            $backupfolder='backups/virtualserver_'.$localserverid.'/';
                                            $delcmd='cd '.$folders.' && function backup () { nice -n +19 rm -f '.$backupfolder.$row4['id'].'.tar.bz2; }; backup& ';
                                            $shell=ssh2_exec($ssh2,$delcmd);
                                        }
                                    }
                                    $rawsnapshot=$connection->Snapshotcreate($localserverid);
                                    $snapshot=gzcompress($rawsnapshot,9);
                                    $query4=$sql->prepare("INSERT INTO `voice_server_backup` (`sid`,`uid`,`name`,`snapshot`,`date`,`resellerid`) VALUES(?,?,?,?,NOW(),?)");
                                    $query4->execute(array($ts3id,$ts3userid,$name,$snapshot,$resellerid));
                                    $query4=$sql->prepare("SELECT `id` FROM `voice_server_backup` WHERE `sid`=? AND `uid`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
                                    $query4->execute(array($ts3id,$ts3userid,$resellerid));
                                    foreach ($query4->fetchAll(PDO::FETCH_ASSOC) as $row4) {
                                        $filefolder='files/virtualserver_'.$localserverid.'/';
                                        $backupfolder='backups/virtualserver_'.$localserverid.'/';
                                        $createcmd='cd '.$folders.' && function backup () { mkdir -p '.$backupfolder.' && nice -n +19 tar cfj '.$backupfolder.$row4['id'].'.tar.bz2 '.$filefolder.'; }; backup& ';
                                        $shell=ssh2_exec($ssh2,$createcmd);
                                        print "Creating backup for ts3 server: ".$row3['ip'].":".$row3['port']."\r\n";
                                        usleep(500000);
                                    }
                                }
                            }
                        }
                    }
                    $ssh2=null;
                    $connection->CloseConnection();
                    usleep(500000);
                } else {
                    print "Error: Cannot connect to masterserver $queryip\r\n";
                }
            }
        }
        $currenttime=strtolower(date('D',strtotime("$resellerstimezone hour")))."_".date('G',strtotime("$resellerstimezone hour"));
        $query1=$sql->prepare("SELECT `id` FROM `rserverdata` WHERE `active`='Y'");
        $query1->execute();
        foreach($query1->fetchAll(PDO::FETCH_ASSOC) as $row1) {
            $rootID=$row1['id'];
            $cmds=array();
            $query2=$sql->prepare("SELECT *,AES_DECRYPT(`ftppassword`,?) AS `decryptedftppass`,AES_DECRYPT(`ppassword`,?) AS `decryptedppassword` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=? ORDER BY `userid`,`serverid`");
            $query2->execute(array($aeskey,$aeskey,$rootID,$resellerid));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                unset($restart);
                $serverip=$row2['serverip'];
                $gsport=$row2['port'];
                $gsswitchID=$row2['id'];
                $useridID=$row2['userid'];
                $newlayout=$row2['newlayout'];
                $gsfolder=$serverip.'_'.$gsport;
                $ftppass=$row2['decryptedftppass'];
                $decryptedftppass=$row2['decryptedppassword'];
                $protected_old=$row2['protected'];
                if ($row2['serverid']==null) {
                    $query3=$sql->prepare("SELECT `id` FROM `serverlist` WHERE `switchID`=? LIMIT 1");
                    $query3->execute(array($gsswitchID));
                    $runID_old=$query3->fetchColumn();
                    $query3=$sql->prepare("UPDATE `gsswitch` SET `serverid`=? WHERE `id`=? LIMIT 1");
                    $query3->execute(array($runID_old,$gsswitchID));
                } else {
                    $runID_old=$row2['serverid'];
                }
                $server=$serverip.':'.$gsport;
                $query3=$sql->prepare("SELECT * FROM `gserver_restarts` WHERE `switchID`=? AND `restarttime`=? LIMIT 1");
                $query3->execute(array($gsswitchID,$currenttime));
                foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                    $template=$row3['template'];
                    $anticheat=$row3['anticheat'];
                    $shorten=$row3['gsswitch'];
                    $map=$row3['map'];
                    $mapGroup=$row3['mapGroup'];
                    $protected=$row3['protected'];
                    $restart=$row3['restart'];
                    $backup=$row3['backup'];
                    $worldsafe=$row3['worldsafe'];
                    $upload=$row3['upload'];
                }
                $query3=$sql->prepare("SELECT `active`,`cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query3->execute(array($row2['userid'],$resellerid));
                foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                    $user_active=$row3['active'];
                    $SSH2customer=$row3['cname'];
                }
                if ($newlayout=='Y') $SSH2customer=$SSH2customer.'-'.$gsswitchID;
                if (isset($restart) and $user_active=='Y') {
                    $query3=$sql->prepare("SELECT s.`id`,s.`upload`,s.`map`,s.`servertemplate`,s.`mapGroup`,t.`qstat` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE t.`shorten`=? AND s.`switchID`=? LIMIT 1");
                    $query3->execute(array($shorten,$gsswitchID));
                    foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                        $runID=$row3['id'];
                        $qstat=$row3['qstat'];
                        $query3=$sql->prepare("UPDATE `gsswitch` SET `serverid`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                        $query3->execute(array($runID,$gsswitchID,$resellerid));
                        $query3=$sql->prepare("UPDATE `serverlist` SET `anticheat`=?,`map`=?,`mapGroup`=?,`servertemplate`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                        $query3->execute(array($anticheat,$map,$mapGroup,$template,$runID,$resellerid));
                        $shortens='';
                        if ($restart=='Y') {
                            unset($newProtected);
                            if ($protected=='Y' and $protected_old=='N') {
                                $tmp=gsrestart($gsswitchID,'so',$aeskey,$sprache,$resellerid,$sql);
                                if (is_array($tmp)) foreach($tmp as $t) $cmds[]=$t;
                                echo "Stopping unprotected server: $server\r\n";
                                $gamestring='1_'.$shorten;
                                $randompass=passwordgenerate(20);
                                $query3=$sql->prepare("UPDATE `gsswitch` SET `ppassword`=AES_ENCRYPT(?,?) WHERE `id`=? LIMIT 1");
                                $query3->execute(array($randompass,$aeskey,$gsswitchID));
                                $cmds[]='./control.sh mod '.$SSH2customer.' '.$ftppass.' '.$randompass;
                                $SSH2customer=$SSH2customer.'-p';
                                $cmds[]="sudo -u ${SSH2customer} ./control.sh reinstserver ${SSH2customer} ${gamestring} ${gsfolder} protected";
                                echo 'Reinstall protected server: '.$server."\r\n";
                                $newProtected=true;
                            }
                            $query3=$sql->prepare("UPDATE `gsswitch` SET `serverid`=?,`stopped`='N',`protected`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                            $query3->execute(array($runID,$protected,$gsswitchID,$resellerid));
                            if (!isset($newProtected)) {
                                echo 'Restarting server: '.$server."\r\n";
                                $tmp=gsrestart($gsswitchID,'re',$aeskey,$sprache,$resellerid,$sql);
                                if (is_array($tmp)) foreach($tmp as $t) $cmds[]=$t;
                            }
                        } else if ($restart=='N' and $qstat=='minecraft' and $worldsafe=='Y') {
                            $cmds[]="sudo -u ${SSH2customer} ./control.sh mc_ws $gsfolder";
                            echo "Minecraft worlsafe: $server\r\n";
                        } else if ($restart=='N' and $qstat=='a2s' and ($uploadtype=='2' or $uploadtype=='3') and $upload=='Y') {
                            $tmp=gsrestart($gsswitchID,'du',$aeskey,$sprache,$resellerid,$sql);
                            if (is_array($tmp)) foreach($tmp as $t) $cmds[]=$t;
                        }
                        if ($backup=='Y') {
                            $query3=$sql->prepare("SELECT AES_DECRYPT(`ftpbackup`,?) AS `backup` FROM `userdata` WHERE `id`=? LIMIT 1");
                            $query3->execute(array($aeskey,$useridID));
                            $ftpbackup=$query3->fetchColumn();
                            $shortens=array();
                            $query3=$sql->prepare("SELECT DISTINCT(t.`shorten`) FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=?");
                            $query3->execute(array($gsswitchID));
                            foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                                $shortens[]=$row3['shorten'];
                            }
                            $cmds[]="sudo -u ${SSH2customer} ./control.sh backup ${gsfolder} \"".implode(' ',$shortens)."\" \"${webhostdomain}\" \"${ftpbackup}\"";
                            echo "Backup started for $server\r\n";
                        }
                    }
                } else {
                    echo "$currenttime Not the restart time for $server\r\n";
                }
            }
            if (count($cmds)>0) ssh2_execute('gs',$rootID,$cmds);
        }
        echo "Executing Updates if neccessary\r\n";
        $query2=$sql->prepare("SELECT `id` FROM `rserverdata` WHERE `resellerid`=? AND `updates`!=3 AND (`updateMinute`=0 OR `updateMinute` IS NULL)");
        $query2->execute(array($resellerid));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $rootServer=new masterServer($row2['id'],$aeskey);
            $rootServer->collectData();
            if (4==$stunde) {
                $sshcmd=$rootServer->returnCmds('update','all');
            } else {
                $sshcmd=$rootServer->returnCmds();
            }
            if ($rootServer->sshcmd!==null) {
                echo "Starting updates for ".$rootServer->sship."\r\n";
                $update=exec_server($rootServer->sship,$rootServer->sshport,$rootServer->sshuser,$rootServer->sshpass,$rootServer->sshcmd,$sql);
                if ($update!="Could not connect to Server" and $update!="The login data does not work") {
                    $rootServer->setUpdating();
                    echo "Updater started for ".$rootServer->sship."\r\n";
                } else {
                    echo "Updating failed for: ".$rootServer->sship."\r\n";
                }
            }
            unset($rootServer);
        }
    }
    $newsInclude=true;
    $printToConsole=true;
    print "Check for new news feeds\r\n";
    include('stuff/feeds_function.php');
    if (isset($template_file)) print $template_file."\r\n";
    if (isset($argv[1]) and is_numeric($argv[1])) {
        $chunkSize=$argv[1];
    } else {
        $chunkSize=500;
    }
    print "Cleaning Up hourly data. Chunksize is ${chunkSize}\r\n";
    $query=$sql->prepare("DELETE FROM `voice_server_stats` WHERE `date` IS NULL OR `date`='0000-00-00 00:00:00'");
    $query->execute();
    $query=$sql->prepare("DELETE FROM `voice_server_stats_hours` WHERE `date` IS NULL OR `date`='0000-00-00 00:00:00'");
    $query->execute();
    $Count=$sql->prepare("SELECT COUNT(`sid`) AS `amount` FROM `voice_server_stats_hours` WHERE `date` NOT LIKE '%00:00:00'");
    $query=$sql->prepare("SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
    $query2=$sql->prepare("SELECT * FROM `voice_server_stats_hours` WHERE `date` NOT LIKE '%00:00:00' LIMIT $chunkSize");
    $query3=$sql->prepare("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
    $query4=$sql->prepare("DELETE FROM `voice_server_stats_hours` WHERE `date` NOT LIKE '%00:00:00' LIMIT $chunkSize");
    $query5=$sql->prepare("INSERT INTO `voice_server_stats` (`sid`,`mid`,`installed`,`used`,`date`,`uid`,`resellerid`) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `used`=(`used`*(`count`/(`count`+1))+(VALUES(`used`)*(1/(`count`+1)))),`installed`=(`installed`*(`count`/(`count`+1))+(VALUES(`installed`)*(1/(`count`+1)))),`count`=`count`+1");
    function compressVoiceData ($Count,$query2,$query4) {
        $Count->execute();
        $theCount=$Count->fetchColumn();
        global $query,$query3,$query5;
        $pass=1;
        if ($theCount<1) {
            print "Found ${theCount} voice server stat entries; Entries to less to compress data\r\n";
        } else {
            print "Found ${theCount} voice server stat entries; starting to compress data\r\n";
        }
        while ($theCount>1 and $pass<11) {
            $query->execute();
            $query2->execute();
            $query3->execute();
            $currentCount=$theCount;
            $Count->execute();
            $theCount=$Count->fetchColumn();
            print "Run: ${pass}; Found ${currentCount} voice server stat entries; Entries left after Run: ${theCount}\r\n";
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $queryDayAndZeroHour=date('Y-m-d',strtotime($row['date'])).' 00:00:00';
                print "Calc: ${queryDayAndZeroHour}; Table ${row['date']}\r\n";
                $query5->execute(array($row['sid'],$row['mid'],$row['installed'],$row['used'],$queryDayAndZeroHour,$row['uid'],$row['resellerid']));
            }
            $query4->execute();
            $pass++;
        }
    }
    compressVoiceData($Count,$query2,$query4);
    $Count=$sql->prepare("SELECT COUNT(`sid`) AS `amount` FROM `voice_server_stats` WHERE `date` NOT LIKE '%00:00:00'");
    $query2=$sql->prepare("SELECT * FROM `voice_server_stats` WHERE `date` NOT LIKE '%00:00:00' LIMIT $chunkSize");
    $query4=$sql->prepare("DELETE FROM `voice_server_stats` WHERE `date` NOT LIKE '%00:00:00' LIMIT $chunkSize");
    compressVoiceData($Count,$query2,$query4);
    print "Voice server stat entries compression completed\r\n";
    $day=date('Y-m-d');
    $Count=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `traffic_data` WHERE `day` NOT LIKE ?");
    $Count->execute(array($day."%"));
    $theCount=$Count->fetchColumn();
    if ($theCount<=10) {
        print "Found ${theCount} traffic entries; Entries to less to compress data\r\n";
    } else {
        print "Found ${theCount} traffic entries; starting to compress data\r\n";
    }
    $query=$sql->prepare("SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
    $query2=$sql->prepare("SELECT * FROM `traffic_data` WHERE `day` NOT LIKE ? LIMIT $chunkSize");
    $query3=$sql->prepare("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
    $query4=$sql->prepare("DELETE FROM `traffic_data` WHERE `day` NOT LIKE ? LIMIT $chunkSize");
    $query5=$sql->prepare("SELECT `id` FROM `traffic_data_day` WHERE `ip`=? AND `day`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
    $query6=$sql->prepare("UPDATE `traffic_data_day` SET `in`=`in`+?,`out`=`out`+?,`serverid`=? WHERE `id`=? LIMIT 1");
    $query7=$sql->prepare("INSERT INTO `traffic_data_day` (`serverid`,`in`,`out`,`ip`,`day`,`userid`,`resellerid`) VALUES (?,?,?,?,?,?,?)");
    $pass=1;
    while ($theCount>10) {
        $exists=array();
        $query->execute();
        $query2->execute(array($day."%"));
        $trafficData=$query2->fetchAll(PDO::FETCH_ASSOC);
        $query3->execute();
        $query4->execute(array($day."%"));
        $currentCount=$theCount;
        $Count->execute(array($day."%"));
        $theCount=$Count->fetchColumn();
        print "Run: $pass; Found $currentCount traffic entries; Entries left after Run: $theCount\r\n";
        $pass++;
        foreach ($trafficData as $id => $row) {
            unset($trafficData[$id],$serverID);
            list($data_day)=explode(' ',$row['day']);
            print "$data_day: ".$row['ip']." in: ".$row['in']." out: ".$row['out']."\r\n";
            if (array_search($row['ip'].','.$data_day.','.$row['userid'].','.$row['resellerid'],$exists)) {
                $serverID=array_search($row['ip'].','.$data_day.','.$row['userid'].','.$row['resellerid'],$exists);
            } else {
                $query5->execute(array($row['ip'],$data_day,$row['userid'],$row['resellerid']));
                foreach ($query5->fetchall(PDO::FETCH_ASSOC) as $row2) {
                    $serverID=$row2['id'];
                }
            }
            if (isset($serverID)) {
                $query6->execute(array($row['in'],$row['out'],$serverID,$id));
            } else {
                $query7->execute(array($row['serverid'],$row['in'],$row['out'],$row['ip'],$data_day,$row['userid'],$row['resellerid']));
            }
        }
    }
    print "Traffic entries compression completed\r\n";
    print "Cleaning Logs\r\n";
    $query=$sql->prepare("DELETE FROM `userlog` WHERE DATEDIFF(NOW(),`logdate`)>31");
    $query->execute();
    $query=$sql->prepare("DELETE FROM `mail_log` WHERE DATEDIFF(NOW(),`date`)>31");
    $query->execute();
    print "Reparing tables\r\n";
    $query=$sql->prepare("REPAIR TABLE `addons`,`addons_installed`,`api_external_auth`,`api_ips`,`api_settings`,`badips`,`dhcpdata`,`eac`,`easywi_version`,`gserver_restarts`,`gsstatus`,`gsswitch`,`imprints`,`jobs`,`lendedserver`,`lendsettings`,`lendstats`,`mail_log`,`mysql_external_dbs`,`mysql_external_servers`,`page_pages`,`page_pages_text`,`page_settings`,`page_terms`,`page_terms_used`,`qstatshorten`,`resellerdata`,`resellerimages`,`rserverdata`,`rservermasterg`,`serverlist`,`servertypes`,`settings`,`test`,`tickets`,`ticket_topics`,`traffic_data`,`traffic_data_day`,`traffic_settings`,`userdata`,`usergroups`,`userlog`,`userpermissions`,`virtualcontainer`,`virtualhosts`,`voice_masterserver`,`voice_server`,`voice_server_backup`,`voice_server_stats`,`voice_server_stats_hours`,`voice_stats_settings`");
    $query->execute();
    print "Optimizing tables\r\n";
    $query=$sql->prepare("OPTIMIZE TABLE `addons`,`addons_installed`,`api_external_auth`,`api_ips`,`api_settings`,`badips`,`dhcpdata`,`eac`,`easywi_version`,`gserver_restarts`,`gsstatus`,`gsswitch`,`imprints`,`jobs`,`lendedserver`,`lendsettings`,`lendstats`,`mail_log`,`mysql_external_dbs`,`mysql_external_servers`,`page_pages`,`page_pages_text`,`page_settings`,`page_terms`,`page_terms_used`,`qstatshorten`,`resellerdata`,`resellerimages`,`rserverdata`,`rservermasterg`,`serverlist`,`servertypes`,`settings`,`test`,`tickets`,`ticket_topics`,`traffic_data`,`traffic_data_day`,`traffic_settings`,`userdata`,`usergroups`,`userlog`,`userpermissions`,`virtualcontainer`,`virtualhosts`,`voice_masterserver`,`voice_server`,`voice_server_backup`,`voice_server_stats`,`voice_server_stats_hours`,`voice_stats_settings`");
    $query->execute();
    $query=$sql->prepare("UPDATE `settings` SET `lastCronReboot`=UNIX_TIMESTAMP() WHERE `resellerid`=0 LIMIT 1");
    $query->execute();
} else {
    header('Location: login.php');
    die('Statuscheck can only be run via console and or a cronjob');
}