<?php
/**
 * File: ssh_exec.php.
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
if (!function_exists('exec_server')) {
    function exec_server($ssh2ip,$ssh2port,$ssh2user,$ssh2pass,$ssh2cmd,$sql) {
        if (strpos(strtolower($ssh2cmd), "control.sh") !== false) {
            $pselect=$sql->prepare("SELECT `publickey`,`keyname`,`notified`,`resellerid` FROM `virtualhosts` WHERE `ip`=? LIMIT 1");
            $pselect->execute(array($ssh2ip));
            foreach ($pselect->fetchAll() as $row) {
                $publickey=$row['publickey'];
                $keyname=$row['keyname'];
                $notified=$row['notified'];
                $resellerid=$row['resellerid'];
            }
        } else {
            $pselect=$sql->prepare("SELECT `publickey`,`keyname`,`notified`,`resellerid` FROM `rserverdata` WHERE `ip`=? LIMIT 1");
            $pselect->execute(array($ssh2ip));
            foreach ($pselect->fetchAll() as $row) {
                $publickey=$row['publickey'];
                $keyname=$row['keyname'];
                $notified=$row['notified'];
                $resellerid=$row['resellerid'];
            }
        }
        if (!isset($notified)) $notified=0;
        if (!isset($resellerid)) $resellerid=0;
        $pselect=$sql->prepare("SELECT `down_checks` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $pselect->execute(array($resellerid));
        foreach ($pselect->fetchAll() as $row) {
            $down_checks=$row['down_checks'];
        }
        if (isset($publickey) and $publickey=="Y") {
            $pubkey="keys/".$keyname.".pub";
            $key="keys/".$keyname;
            $ssh2= @ssh2_connect($ssh2ip,$ssh2port,array('hostkey'=>'ssh-rsa'));
        } else {
            $ssh2= @ssh2_connect($ssh2ip,$ssh2port);
        }
        if ($ssh2==true) {
            if (isset($publickey) and $publickey=="Y") {
                $connect_ssh2= @ssh2_auth_pubkey_file($ssh2, $ssh2user, $pubkey, $key);
            } else {
                $connect_ssh2= @ssh2_auth_password($ssh2, $ssh2user, $ssh2pass);
            }
            if ($connect_ssh2==true) {
                $shell = ssh2_exec($ssh2, $ssh2cmd);
                stream_set_blocking($shell,true);
                $data = '';
                while($buffer = fread($shell,4096)){
                    $data .= $buffer;
                }
                if (strpos(strtolower($ssh2cmd), "control.sh") !== false and $notified>0) {
                    $pupdate2=$sql->prepare("UPDATE `virtualhosts` SET `notified`='0' WHERE `ip`=? LIMIT 1");
                    $pupdate2->execute(array($ssh2ip));
                } else if ($notified>0) {
                    $pupdate2=$sql->prepare("UPDATE `rserverdata` SET `notified`='0' WHERE `ip`=? LIMIT 1");
                    $pupdate2->execute(array($ssh2ip));
                }
                ssh2_exec($ssh2,'exit');
                $connect_ssh2=null;
                return $data;
            } else {
                $bad="The login data does not work";
            }
        } else {
            $bad="Could not connect to Server";
        }
        if (isset($bad)) {
            $notified++;
            if (strpos(strtolower($ssh2cmd), "control.sh") !== false) {
                $pupdate2=$sql->prepare("UPDATE `virtualhosts` SET `notified`=? WHERE `ip`=? LIMIT 1");
                $pupdate2->execute(array($notified,$ssh2ip));
            } else {
                $pupdate2=$sql->prepare("UPDATE `rserverdata` SET `notified`=? WHERE `ip`=? LIMIT 1");
                $pupdate2->execute(array($notified,$ssh2ip));
            }
        }
        if (isset($bad) and ($bad=="Could not connect to Server" or $bad=="The login data does not work") and $notified==$down_checks) {
            if ($resellerid==0) {
                $query2=$sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `resellerid`='0' AND `accounttype`='a'");
                $query2->execute();
            } else {
                $query2=$sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE (`id`=? AND `id`=`resellerid`) OR `resellerid`='0' AND `accounttype`='a'");
                $query2->execute(array($resellerid));
            }
            foreach ($query2->fetchAll() as $row2) {
                if ($row2['mail_serverdown']=='Y') {
                    sendmail('emaildown',$row2['id'],$ssh2ip,'',$sql);
                }
            }
        }
        if (isset($bad)) {
            return $bad;
        }
    }
    function ssh2exec($serverid,$type,$aeskey,$ssh2cmd,$sql) {
        $serverdata=serverdata($type,$serverid,$aeskey,$sql);
        $notified=$serverdata['notified'];
        $resellerid=$serverdata['resellerid'];
        $ssh2ip=$serverdata['ip'];
        global $rSA;
        if ($serverdata['publickey']=="Y") {
            $pubkey=EASYWIDIR."keys/".$serverdata['keyname'].".pub";
            $key=EASYWIDIR."keys/".$serverdata['keyname'];
            if (file_exists($pubkey) and file_exists($key)) $ssh2=@ssh2_connect($ssh2ip,$serverdata['port'],array('hostkey'=>'ssh-rsa'));
        } else {
            $ssh2=@ssh2_connect($ssh2ip,$serverdata['port']);
        }
        if (isset($ssh2) and $ssh2==true) {
            if ($serverdata['publickey']=="Y") {
                $connect_ssh2= @ssh2_auth_pubkey_file($ssh2, $serverdata['user'], $pubkey, $key);
            } else {
                $connect_ssh2= @ssh2_auth_password($ssh2, $serverdata['user'], $serverdata['pass']);
            }
            if ($connect_ssh2==true) {
                $shell = ssh2_exec($ssh2, $ssh2cmd);
                stream_set_blocking($shell, true);
                $data = '';
                while($buffer = fread($shell,4096)){
                    $data .= $buffer;
                }
                fclose($shell);
                ssh2_exec($ssh2,'exit');
                if ($notified>0) {
                    if ($type=="root") {
                        $query=$sql->prepare("UPDATE `rserverdata` SET `notified`='0' WHERE `id`=? LIMIT 1");
                    } else if ($type=="virtualhost") {
                        $query=$sql->prepare("UPDATE `virtualhosts` SET `notified`='0' WHERE `id`=? LIMIT 1");
                    } else if ($type=="dhcp") {
                        $query=$sql->prepare("UPDATE `dhcpdata` SET `notified`='0' WHERE `id`=? LIMIT 1");
                    } else if ($type=="eac") {
                        $query=$sql->prepare("UPDATE `eac` SET `notified`='0' WHERE `resellerid`=? LIMIT 1");
                    }
                    $query->execute(array($serverid));
                }
                return $data;
            } else {
                $bad="The login data does not work";
            }
        } else {
            $bad="Could not connect to Server";
        }
        if (isset($bad)) {
            $notified++;
            if ($type=="root") {
                $query=$sql->prepare("UPDATE `rserverdata` SET `notified`=? WHERE `id`=? LIMIT 1");
            } else if ($type=="virtualhost") {
                $query=$sql->prepare("UPDATE `virtualhosts` SET `notified`=? WHERE `id`=? LIMIT 1");
            } else if ($type=="dhcp") {
                $query=$sql->prepare("UPDATE `dhcpdata` SET `notified`=? WHERE `id`=? LIMIT 1");
            } else if ($type=="eac") {
                $query=$sql->prepare("UPDATE `eac` SET `notified`=? WHERE `resellerid`=? LIMIT 1");
            }
            $query->execute(array($notified,$serverid));
        }
        if (isset($bad) and ($bad=="Could not connect to Server" or $bad=="The login data does not work") and $notified==$rSA['down_checks']) {
            if ($resellerid==0) {
                $query=$sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `resellerid`='0' AND `accounttype`='a'");
                $query->execute();
            } else {
                $query=$sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE (`id`=? AND `id`=`resellerid`) OR (`resellerid`='0' AND `accounttype`='a')");
                $query->execute(array($resellerid));
            }
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if ($row['mail_serverdown']=='Y') {
                    sendmail('emaildown',$row['id'],$ssh2ip,'',$sql);
                }
            }
        }
        if (isset($bad)) return $bad;
    }
    function shell_server($ssh2ip,$ssh2port,$ssh2user,$ssh2pass,$suuser,$supass,$sucmd,$sql) {
        $command=array();
        $command[]="su -c '".$sucmd."' ".$suuser;
        $command[]=$supass;
        $command[]='exit';
        $pselect=$sql->prepare("SELECT `id`,`publickey`,`keyname`,`notified`,`resellerid` FROM `rserverdata` WHERE `ip`=? LIMIT 1");
        $pselect->execute(array($ssh2ip));
        foreach ($pselect->fetchAll() as $row) {
            $publickey=$row['publickey'];
            $keyname=$row['keyname'];
            $notified=$row['notified'];
            $resellerid=$row['resellerid'];
            $rootID=$row['id'];
        }
        $pselect=$sql->prepare("SELECT `down_checks` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $pselect->execute(array($resellerid));
        foreach ($pselect->fetchAll() as $row) {
            $down_checks=$row['down_checks'];
        }
        if ($publickey=="Y") {
            $pubkey="keys/".$keyname.".pub";
            $key="keys/".$keyname;
            $ssh2= @ssh2_connect($ssh2ip,$ssh2port,array('hostkey'=>'ssh-rsa'));
        } else {
            $ssh2= @ssh2_connect($ssh2ip,$ssh2port);
        }
        if ($ssh2==true) {
            if ($publickey=="Y") {
                $connect_ssh2= @ssh2_auth_pubkey_file($ssh2, $ssh2user, $pubkey, $key);
            } else {
                $connect_ssh2= @ssh2_auth_password($ssh2, $ssh2user, $ssh2pass);
            }
            if ($connect_ssh2==true) {
                $data = '';
                $shell = ssh2_shell($ssh2);
                for($i=0; $i<count($command); $i++) {
                    fwrite($shell, $command[$i] . PHP_EOL);
                    #usleep(500000);
                    sleep(1);
                    while($buffer = fgets($shell)) {
                        flush();
                        $data .= $buffer;
                    }
                }
                #usleep(500000);
                sleep(1);
                fclose($shell);
                if ($notified>0) {
                    $pupdate2=$sql->prepare("UPDATE `rserverdata` SET `notified`='0' WHERE `ip`=? LIMIT 1");
                    $pupdate2->execute(array($ssh2ip));
                }
                return '';
            } else {
                $bad="The login data does not work";
            }
        } else {
            $bad="Could not connect to Server";
        }
        if (isset($bad)) {
            $notified++;
            $pupdate2=$sql->prepare("UPDATE `rserverdata` SET `notified`=? WHERE `id`=? LIMIT 1");
            $pupdate2->execute(array($notified,$rootID));
        }
        if (isset($bad) and ($bad=="Could not connect to Server" or $bad=="The login data does not work") and $down_checks==$notified) {
            if ($resellerid==0) {
                $query2=$sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `resellerid`='0' AND `accounttype`='a'");
                $query2->execute();
            } else {
                $query2=$sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE (`id`=? AND `id`=`resellerid`) OR (`resellerid`='0' AND `accounttype`='a')");
                $query2->execute(array($resellerid));
            }
            foreach ($query2->fetchAll() as $row2) {
                if ($row2['mail_serverdown']=='Y') {
                    sendmail('emaildown',$row2['id'],$ssh2ip,'',$sql);
                }
            }
        }
        if (isset($bad)) {
            return $bad;
        }
    }
}