<?php

/**
 * File: functions.php.
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

if (!defined('EASYWIDIR')) {
    define('EASYWIDIR','');
}
if (!extension_loaded('ssh2')) {
    function ssh2_connect ($ip='',$port='',$params='') {
        return null;
    }
}
if (!function_exists('passwordgenerate')) {
    function passwordgenerate ($length) {
        $zeichen = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',1,'2','3','4','5','6','7','8','9');
        $anzahl=count($zeichen)-1;
        $password='';
        for($i=1; $i<=$length; $i++){
            $wuerfeln=mt_rand(0,$anzahl);
            $password .= $zeichen[$wuerfeln];
        }
        return $password;
    }
    function passwordhash($username,$password,$salt=false){
        $passworda = str_split($password,(strlen($password)/2)+1);
        $usernamea = str_split($username,(strlen($username)/2)+1);
        return ($salt==false) ? hash('sha512',sha1($usernamea[0].md5($passworda[0].$usernamea[1]).$passworda[1])): hash('sha512',sha1($usernamea[0].md5($passworda[0].$salt.$usernamea[1]).$passworda[1]));
    }
    function createHash ($name,$pwd,$saltOne,$saltTwo='ZPZw$[pkJF!;SHdl',$iterate=1000) {
        $pwdSplit=str_split($pwd,(strlen($pwd)/2)+1);
        $nameSplit=str_split($name,(strlen($name)/2)+1);
        $hash='';
        if (!isset($nameSplit[1]) and strlen($nameSplit[0])>0) $nameSplit[1]=$nameSplit[0];
        if (!isset($pwdSplit[1]) and strlen($pwdSplit[0])>0) $pwdSplit[1]=$pwdSplit[0];
        if (isset($nameSplit[1]) and isset($pwdSplit[1])) {
            for ($i=0;$i<=$iterate;$i++) $hash=hash('sha512',$nameSplit[0].$saltOne.$pwdSplit[0].$hash.$nameSplit[1].$saltTwo.$pwdSplit[1]);
            return $hash;
        } else if (!isset($pwdSplit[1])) {
            die ('Fatal Error: No or invalid Password!');
        } else {
            die ('Fatal Error: No or invalid Username!');
        }
    }
    function szrp ($value) {
        $szrm=array('ä'=>'ae','ö'=>'oe','ü'=>'ue','Ä'=>'Ae','Ö'=>'Oe','Ü'=>'Ue','ß'=>'ss','á'=>'a','à'=>'a','Á'=>'A','À'=>'A','é'=>'e','è'=>'e','É'=>'E','È'=>'E','ó'=>'o','ò'=>'o','Ó'=>'O','Ò'=>'O','ú'=>'u','ù'=>'u','Ú'=>'U','Ù'=>'U');
        return strtolower(preg_replace('/[^a-zA-Z0-9]{1}/','-',strtr($value,$szrm)));
    }
    function redirect($value, $sendHTTP301 = false) {
        if ($sendHTTP301 == true) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        header ('Location: '.$value);
        die('Please allow redirection settings');
    }
    function listDirs ($dir) {
        $selectLanguages=array();
        if (is_dir($dir)){
            $dirs=scandir($dir);
            foreach ($dirs as $row) if (small_letters_check($row,2)) $selectLanguages[]=$row;
        }
        return $selectLanguages;
    }
    function getlanguages ($value) {
        $selectLanguages=listDirs('languages/'.$value.'/');
        if (count($selectLanguages)<1) $selectLanguages=listDirs('languages/default/');
        if (count($selectLanguages)<1) $selectLanguages=listDirs('languages/');
        return $selectLanguages;
    }

    function cleanFsockOpenRequest ($string,$start,$stop) {
        while(substr($string,0,1)!=$start and strlen($string)>0) $string=substr($string,1);
        while(substr($string,-1)!=$stop and strlen($string)>0) $string=substr($string,0,-1);
        return $string;
    }
    function serverdata($type,$serverID,$aeskey) {
        global $sql;
        $serverdata=array();
        if ($type=="root") {
            $query=$sql->prepare("SELECT `ip`,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,AES_DECRYPT(`steamAccount`,:aeskey) AS `decryptedsteamAccount`,AES_DECRYPT(`steamPassword`,:aeskey) AS `decryptedsteamPassword`,`publickey`,`keyname`,`ftpport`,`notified`,`cores`,`hyperthreading`,`resellerid` FROM `rserverdata` WHERE `id`=:serverID LIMIT 1");
        } else if ($type=="virtualhost") {
            $query=$sql->prepare("SELECT `ip`,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,`publickey`,`keyname`,`notified`,`resellerid` FROM `virtualhosts` WHERE `id`=:serverID LIMIT 1");
        } else if ($type=="dhcp") {
            $query=$sql->prepare("SELECT `ip`,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,`publickey`,`keyname`,`notified`,`resellerid` FROM `dhcpdata` WHERE `id`=:serverID LIMIT 1");
        } else {
            $query=$sql->prepare("SELECT `ip`,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,`publickey`,`keyname`,`cfgdir`,`notified`,`resellerid` FROM `eac` WHERE `resellerid`=:serverID LIMIT 1");
        }
        $query->execute(array(':serverID'=>$serverID,':aeskey'=>$aeskey));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $cores='';
            $hyperthreading='';
            $steamAccount='';
            $steamPassword='';
            if ($type=="root") {
                $ftpport=$row['ftpport'];
                $cores=$row['cores'];
                $hyperthreading=$row['hyperthreading'];
                $steamAccount=$row['decryptedsteamAccount'];
                $steamPassword=$row['decryptedsteamPassword'];
            } else if ($type=="eac") {
                $ftpport=$row['cfgdir'];
            } else {
                $ftpport="";
            }
            $serverdata=array('ip'=>$row['ip'],'port'=>$row['decryptedport'],'user'=>$row['decrypteduser'],'pass'=>$row['decryptedpass'],'publickey'=>$row['publickey'],'keyname'=>$row['keyname'],'notified'=>$row['notified'],'resellerid'=>$row['resellerid'],'hyperthreading'=>$hyperthreading,'cores'=>$cores,'ftpport'=>$ftpport,'steamAccount'=>$steamAccount,'steamPassword'=>$steamPassword);
        }
        return $serverdata;
    }
    function serverAmount($resellerid) {
        global $sql,$user_language;
        $query=$sql->prepare("SELECT `licence` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
        $query->execute();
        $json=@json_decode($query->fetchColumn());
        $query=$sql->prepare("SELECT  COUNT(g.`id`) AS `amount` FROM `gsswitch` g LEFT JOIN `userdata` u ON g.`userid`=u.`id` LEFT JOIN `userdata` r ON g.`resellerid`= r.`id` WHERE g.`active`='Y' AND u.`active`='Y' AND (r.`active`='Y' OR r.`active` IS NULL)");
        $query->execute();
        $gsCount=(int)$query->fetchColumn();
        $query=$sql->prepare("SELECT COUNT(v.`id`) AS `amount` FROM `virtualcontainer` v LEFT JOIN `userdata` u ON v.`userid`=u.`id` LEFT JOIN `userdata` r ON v.`resellerid`= r.`id` WHERE v.`active`='Y' AND u.`active`='Y' AND (r.`active`='Y' OR r.`active` IS NULL)");
        $query->execute();
        $vCount=(int)$query->fetchColumn();
        $query=$sql->prepare("SELECT COUNT(v.`id`) AS `amount` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` LEFT JOIN `userdata` u ON v.`userid`=u.`id` LEFT JOIN `userdata` r ON v.`resellerid`= r.`id` WHERE v.`active`='Y' AND m.`active`='Y' AND u.`active`='Y' AND (r.`active`='Y' OR r.`active` IS NULL)");
        $query->execute();
        $voCount=(int)$query->fetchColumn();
        $query=$sql->prepare("SELECT `dedicatedID` FROM `rootsDedicated` d LEFT JOIN `userdata` u ON d.`userID`=u.`id` LEFT JOIN `userdata` r ON d.`resellerID`= r.`id` WHERE r.`active`!='N' AND u.`active`!='N' AND d.`active`!='N'");
        $query->execute();
        $dCount=(int)$query->fetchColumn();
        $count=$gsCount+$vCount+$voCount+$dCount;
        $sprache=getlanguagefile('licence',$user_language,$resellerid);
        $s=$sprache->unlimited;
        $mG=$s;
        $mVs=$s;
        $mVo=$s;
        $mD=$s;
        $lG=10;
        $lVs=10;
        $lVo=10;
        $lD=10;
        $left=$s;
        if ($resellerid!=0) {
            $query=$sql->prepare("SELECT `maxgserver`,`maxvserver`,`maxvoserver`,`maxdedis` FROM `resellerdata` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($resellerid));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $mG=$row['maxgserver'];
                $mVs=$row['maxvserver'];
                $mVo=$row['maxvoserver'];
                $mD=$row['maxdedis'];
            }
            $query=$sql->prepare("SELECT COUNT(g.`id`) AS `amount` FROM `gsswitch` g LEFT JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`resellerid`=? AND g.`active`='Y' AND u.`active`='Y'");
            $query->execute(array($resellerid));
            $gsCount=$query->fetchColumn();
            $query=$sql->prepare("SELECT COUNT(v.`id`) AS `amount` FROM `virtualcontainer` v LEFT JOIN `userdata` u ON v.`userid`=u.`id` WHERE (v.`userid`=:resellerid OR v.`resellerid`=:resellerid) AND v.`active`='Y' AND u.`active`='Y'");
            $query->execute(array(':resellerid'=>$resellerid));
            $vCount=$query->fetchColumn();
            $query=$sql->prepare("SELECT COUNT(v.`id`) AS `amount` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` LEFT JOIN `userdata` u ON v.`userid`=u.`id` LEFT JOIN `userdata` r ON v.`resellerid`= r.`id` WHERE v.`resellerid`=? AND v.`active`='Y' AND m.`active`='Y' AND u.`active`='Y'");
            $query->execute(array($resellerid));
            $voCount=$query->fetchColumn();
            $query=$sql->prepare("SELECT COUNT(`dedicatedID`) AS `amount` FROM `rootsDedicated` d LEFT JOIN `userdata` u ON d.`userid`=u.`id` WHERE (d.`userID`=:resellerid OR d.`resellerID`=:resellerid) AND d.`active`!='N'");
            $query->execute(array(':resellerid'=>$resellerid));
            $dCount=$query->fetchColumn();
        }
        return array('left'=>$left,'count'=>$count,'gsCount'=>$gsCount,'vCount'=>$vCount,'voCount'=>$voCount,'dCount'=>$dCount,'mG'=>$mG,'mVs'=>$mVs,'mVo'=>$mVo,'mD'=>$mD,'lG'=>$lG,'lVs'=>$lVs,'lVo'=>$lVo,'lD'=>$lD,'p'=>$json->p,'b'=>$json->b,'t'=>$json->t,'u'=>$json->u,'c'=>$json->c,'v'=>$json->v);
    }
    function getusername($userid) {
        global $sql;
        $query=$sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($userid));
        $cname=($query->rowCount()==0) ? 'User deleted' : $query->fetchColumn();
        return $cname;
    }
    function rsellerpermisions($userid) {
        global $sql;
        $query=$sql->prepare("SELECT `userid` FROM `userpermissions` WHERE `userid`=? AND (`addvserver`='Y' OR `modvserver`='Y' OR `delvserver`='Y' OR `vserversettings`='Y' OR `vserverhost`='Y' OR `resellertemplates`='Y' OR `usevserver`='Y' OR `root`='Y' OR `traffic`='Y') LIMIT 1");
        $query->execute(array($userid));
        $colcount=$query->rowCount();
        if ($colcount==0) {
            $u_p_q=$sql->prepare("SELECT g.`id` FROM `userdata_groups` u LEFT JOIN `usergroups` g ON u.`groupID`=g.`id` WHERE u.`userID`=? AND (`addvserver`='Y' OR `modvserver`='Y' OR `delvserver`='Y' OR `vserversettings`='Y' OR `vserverhost`='Y' OR `resellertemplates`='Y' OR `usevserver`='Y' OR `root`='Y' OR `traffic`='Y') LIMIT 1");
            $u_p_q->execute(array($userid));
            $colcount=$u_p_q->fetchAll(PDO::FETCH_ASSOC);
        }
        return $colcount;
    }
    function isanyadmin($userid) {
        global $sql;
        $query=$sql->prepare("SELECT `accounttype` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($userid));
        $accountType=$query->fetchColumn();
        return ($accountType=='a' or $accountType=='r') ?  true : false;
    }
    function isanyuser($userid) {
        global $sql;
        $query=$sql->prepare("SELECT `accounttype` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($userid));
        return ($query->fetchColumn()=='u') ? true : false;
    }
    function language($user_id) {
        global $sql,$ui;
        if (!isset($_SESSION['language'])) {
            $query=$sql->prepare("SELECT `language` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($user_id));
            $language=$query->fetchColumn();
            if ($language=='') {
                $lang_detect=(isset($ui->server['HTTP_ACCEPT_LANGUAGE'])) ? small_letters_check(substr($ui->server['HTTP_ACCEPT_LANGUAGE'],0,2),2) : 'uk';
                if (is_dir(EASYWIDIR."/languages/$lang_detect")) {
                    $language=$lang_detect;
                } else {
                    $query=$sql->prepare("SELECT `language` FROM `settings` LIMIT 1");
                    $query->execute();
                    $language=$query->fetchColumn();
                }
            } else if (!is_dir(EASYWIDIR."/languages/$language")) {
                $query=$sql->prepare("SELECT `language` FROM `settings` LIMIT 1");
                $query->execute();
                $language=$query->fetchColumn();
            }
            $query=$sql->prepare("UPDATE `userdata` SET `language`=? WHERE `id`=? LIMIT 1");
            $query->execute(array($language,$user_id));
            $_SESSION['language']=$language;
        } else {
            $language=$_SESSION['language'];
        }
        return $language;
    }
    function getlanguagefile($filename,$user_language,$reseller_id) {
        global $sql;
        $sprache=new stdClass;
        $query=$sql->prepare("SELECT `language`,`template` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $default_language=$row['language'];
            $template=$row['template'];
            if (file_exists(EASYWIDIR.'/languages/'.$template.'/'.$user_language.'/'.$filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR.'/languages/'.$template.'/'.$user_language.'/'.$filename.'.xml');
            } else if (file_exists(EASYWIDIR.'/languages/'.$template.'/'.$default_language.'/'.$filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR.'/languages/'.$template.'/'.$default_language.'/'.$filename.'.xml');
            } else if (file_exists(EASYWIDIR.'/languages/default/'.$user_language.'/'.$filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR.'/languages/default/'.$user_language.'/'.$filename.'.xml');
            } else if (file_exists(EASYWIDIR.'/languages/default/'.$default_language.'/'.$filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR.'/languages/default/'.$default_language.'/'.$filename.'.xml');
            } else if (file_exists(EASYWIDIR.'/languages/'.$user_language.'/'.$filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR."/languages/$user_language/$filename.xml");
            } else if (file_exists(EASYWIDIR.'/languages/'.$default_language.'/'.$filename.'.xml')) {
                $sprache=simplexml_load_file(EASYWIDIR."/languages/$default_language/$filename.xml");
            }
        }
        return $sprache;
    }
    function ipstoarray($value) {
        $ips_array=array();
        if (isips($value)) {
            foreach (explode("\r\n", $value) as $exip) {
                if (isips($exip)) {
                    $exploded_ip=explode(".", $exip);
                    if (is_numeric($exploded_ip['3'])){
                        $ips_array[]=$exip;
                    } else {
                        $range=explode("/", $exploded_ip['3']);
                        $i=$range[0];
                        while ($i <= $range[1]) {
                            $ips_array[]=$exploded_ip[0].".".$exploded_ip[1].".".$exploded_ip['2'].".".$i;
                            $i++;
                        }
                    }
                }
            }
        }
        natsort($ips_array);
        return $ips_array;
    }
    function freeips($value) {
        global $sql;
        $userips=array();
        $usedips=array();
        if ($value==0) {
            $userips=array();
            $query=$sql->prepare("SELECT `ips` FROM `rootsDHCP` WHERE `active`='Y' AND `resellerid`=0");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                foreach (ipstoarray($row['ips']) as $ip) $userips[]=$ip;
            }
            $query=$sql->prepare("SELECT `ips` FROM `resellerdata`");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                foreach (ipstoarray($row['ips']) as $usedip) $usedips[]=$usedip;
            }
            $query=$sql->prepare("SELECT `ip`,`ips` FROM `virtualcontainer`");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $usedips[]=$row['ip'];
                foreach (ipstoarray($row['ips']) as $usedip) {
                    $usedips[]=$usedip;
                }
            }
            $query=$sql->prepare("SELECT `ip`,`ips` FROM `rootsDedicated`");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $usedips[]=$row['ip'];
                foreach (ipstoarray($row['ips']) as $usedip) {
                    $usedips[]=$usedip;
                }
            }
        } else {
            $query=$sql->prepare("SELECT `resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($value));
            $resellerid=$query->fetchColumn();
            $query=$sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($resellerid));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $userips=ipstoarray($row['ips']);
            }
            $query=$sql->prepare("SELECT `ip`,`ips` FROM `virtualcontainer` WHERE `resellerid`=?");
            $query->execute(array($resellerid));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $usedips[]=$row['ip'];
                foreach (ipstoarray($row['ips']) as $usedip) $usedips[]=$usedip;
            }
            $query=$sql->prepare("SELECT `ip`,`ips` FROM `rootsDedicated` WHERE `resellerid`=?");
            $query->execute(array($resellerid));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $usedips[]=$row['ip'];
                foreach (ipstoarray($row['ips']) as $usedip) $usedips[]=$usedip;
            }
            $query=$sql->prepare("SELECT `id` FROM `userdata` WHERE accounttype='r' AND `resellerid`=:id AND `id`!=:id");
            $query2=$sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array(':id'=>$resellerid));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $query2->execute(array($row['id']));
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    foreach (ipstoarray($row2['ips']) as $usedip) $usedips[]=$usedip;
                }
            }
        }
        $checkedips=array();
        foreach (array_unique($userips) as $userip) {
            if (!in_array($userip, $usedips)) $checkedips[]=$userip;
        }
        $checkedips=array_unique($checkedips);
        natsort($checkedips);
        return $checkedips;
    }
    function getconfigcvars($file) {
        $fp= @fopen($file,'rb');
        if ($fp == true) {
            $vars=array();
            $configfile="";
            while (!feof($fp)){
                $line=fgets($fp);
                if(strpos(strtolower($line), strtolower("<?php")) === false and strpos(strtolower($line), strtolower("?>")) === false) {
                    $configfile .="$line\r\n";
                }
            }
            fclose($fp);
            $lines=explode("\r\n", $configfile);
            foreach ($lines as $line) {
                if(strpos(strtolower($line), strtolower("//")) === false and strpos(strtolower($line), strtolower("=")) == true) {
                    $data=explode("=", $line);
                    $cvar=preg_replace('/\s+/', '', $data[0]);
                    $cvar=str_replace('$', "", $cvar);
                    $data2=explode(";", $data[1]);
                    $stringlenght=strlen($data2[0]);
                    $stop=$stringlenght-2;
                    $value=substr($data2[0],1,$stop);
                    $vars["$cvar"]=$value;
                }
            }
            return $vars;
        } else {
            die("No configdata!");
        }
    }
    function eacchange($what,$serverid,$rcon,$reseller_id) {
        global $sql;
        $query=$sql->prepare("SELECT `active`,`cfgdir` FROM `eac` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $cfgdir=$row['cfgdir'];
            $active=$row['active'];
        }
        $query=$sql->prepare("SELECT g.`serverip`,g.`port`,s.`anticheat`,t.`shorten` FROM `gsswitch` g LEFT JOIN `serverlist` s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`id`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($serverid,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $gsip=$row['serverip'];
            $gsport=$row['port'];
            $anticheat=$row['anticheat'];
            $shorten=$row['shorten'];
        }
        if ($anticheat==3) $parameter='';
        else if ($anticheat==4) $parameter='-2';
        else if ($anticheat==5) $parameter='-1';
        else if ($anticheat==6) $parameter='-3';
        if ($shorten=='cstrike' or $shorten=='czero') $subfolder='hl1';
        else if ($shorten=='css' or $shorten=='tf') $subfolder='hl2';
        else if ($shorten=='cod4') $subfolder='cod4';
        $file="$cfgdir/$subfolder/$gsip-$gsport";
        $file=preg_replace('/\/\//', '/', $file);
        if ($what=='change') $ssh2cmd='echo "'.$gsip.':'.$gsport.'-'.$rcon.$parameter.'" > '.$file;
        else if ($what=="remove") $ssh2cmd='rm -f '.$file;
        if (isset($ssh2cmd) and $active=='Y') {
            if (!function_exists('ssh2_execute')) include(EASYWIDIR.'/stuff/ssh_exec.php');
            if (isset($ssh2cmd)) ssh2_execute('eac',$reseller_id,$ssh2cmd);
        }
    }
    function gsrestart($switchID,$action,$aeskey,$reseller_id) {
        global $sql;
        $tempCmds=array();
        $stopped='Y';
        $query=$sql->prepare("SELECT g.*,g.`id` AS `switchID`,AES_DECRYPT(g.`ppassword`,:aeskey) AS `decryptedppass`,AES_DECRYPT(g.`ftppassword`,:aeskey) AS `decryptedftppass`,s.*,AES_DECRYPT(s.`uploaddir`,:aeskey) AS `decypteduploaddir`,AES_DECRYPT(s.`webapiAuthkey`,:aeskey) AS `dwebapiAuthkey`,g.`pallowed`,t.`modfolder`,t.`gamebinary`,t.`binarydir`,t.`shorten`,t.`qstat`,t.`appID` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`active`='Y' AND g.`id`=:serverid AND g.`resellerid`=:reseller_id  AND t.`resellerid`=:reseller_id LIMIT 1");
        $query->execute(array(':aeskey'=>$aeskey,':serverid'=>$switchID,':reseller_id'=>$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $serverid=$row['serverid'];
            $anticheat=$row['anticheat'];
            $servertemplate=$row['servertemplate'];
            $protected=$row['protected'];
            $qstat=$row['qstat'];
            $upload=$row['upload'];
            $uploaddir=$row['decypteduploaddir'];
            $shorten=$row['shorten'];
            $tvenable=$row['tvenable'];
            $gsip=$row['serverip'];
            $port=$row['port'];
            $port2=$row['port2'];
            $port3=$row['port3'];
            $port4=$row['port4'];
            $port5=$row['port5'];
            $minram=($row['minram']>0) ? $row['minram'] : 512;
            $maxram=($row['maxram']>0) ? $row['maxram'] : 1024;
            $gamebinary=$row['gamebinary'];
            $binarydir=$row['binarydir'];
            $eacallowed=$row['eacallowed'];
            $fps=$row['fps'];
            $slots=$row['slots'];
            $map=$row['map'];
            $mapGroup=$row['mapGroup'];
            $tic=$row['tic'];
            $rootid=$row['rootID'];
            $modfolder=$row['modfolder'];
            $ftppass=$row['decryptedftppass'];
            $decryptedftppass=$row['decryptedppass'];
            $cmd=$row['cmd'];
            $modcmd=$row['modcmd'];
            $pallowed=$row['pallowed'];
            $user_id=$row['userid'];
            $query=$sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($user_id));
            $customer=$query->fetchColumn();
            if ($row['newlayout']=='Y') $customer=$customer.'-'.$row['switchID'];
            $cores=($row['taskset']=='Y') ? $row['cores'] : '';
            $maxcores=count(preg_split("/\,/",$cores,-1,PREG_SPLIT_NO_EMPTY));
            if ($maxcores==0) $maxcores=1;
            $folder=($servertemplate>1 and $protected=='N') ? $shorten."-".$servertemplate : $shorten;
            if ($servertemplate>1 and $protected=='N') {
                $pserver="server/";
                $absolutepath="/home/".$customer."/server/".$gsip."_"."$port/$folder";
            } else if ($protected=='Y') {
                $pserver="";
                $absolutepath="/home/".$customer."/pserver/".$gsip."_"."$port/$folder";
            } else {
                $pserver="server/";
                $absolutepath="/home/".$customer."/server/".$gsip."_"."$port/$folder";
            }
            $bindir=$absolutepath.'/'.$binarydir;
            $cvarprotect=array();
            if ($qstat=='hla2s' and $tvenable=='Y') $slots++;
            $modsCmds=array();
            $cvars=array('%binary%','%tic%','%ip%','%port%','%tvport%','%port2%','%port3%','%port4%','%port5%','%slots%','%map%','%mapgroup%','%fps%','%minram%','%maxram%','%maxcores%','%folder%','%user%','%absolutepath%');
            $query2=$sql->prepare("SELECT `cmd`,`modcmds`,`configedit` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");
            $query2->execute(array($shorten,$reseller_id));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                foreach (explode("\r\n",$row2['configedit']) as $line) {
                    if (preg_match('/^(\[[\w\/\.\-\_]{1,}\]|\[[\w\/\.\-\_]{1,}\] (xml|ini|cfg))$/',$line)) {
                        $ex=preg_split("/\s+/",$line,-1,PREG_SPLIT_NO_EMPTY);
                        $cvartype=(isset($ex[1])) ? $ex[1] : 'cfg';
                        $config=substr($ex[0],1,strlen($ex[0])-2);
                        $cvarprotect[$config]['type']=$cvartype;
                    } else if (isset($config)) {
                        unset($splitline);
                        if ($cvarprotect[$config]['type']=='cfg') {
                            $splitline=preg_split("/\s+/",$line,-1,PREG_SPLIT_NO_EMPTY);
                        } else if($cvarprotect[$config]['type']=='ini') {
                            $splitline=preg_split("/\=/",$line,-1,PREG_SPLIT_NO_EMPTY);
                        } else if($cvarprotect[$config]['type']=='xml') {
                            $ex1=explode(">",$line);
                            if (isset($ex1[1])) {
                                $c=str_replace('<','',$ex1[0]);
                                list($v)=explode("<",$ex1[1]);
                                $splitline=array($c,$v);
                            }
                        }
                        if (isset($splitline[1])) {
                            $replace=array($gamebinary,$tic,$gsip,$port,$port2,$port2,$port3,$port4,$port5,$slots,$map,$mapGroup,$fps,$minram,$maxram,$maxcores,$folder,$customer,$absolutepath);
                            $cvar=str_replace($cvars,$replace,$splitline[1]);
                            foreach (customColumns('G',$switchID) as $cu) $cvar=str_replace("%${cu['name']}%",$cu['value'],$cvar);
                            $cvarprotect[$config]['cvars'][$splitline[0]]=$cvar;
                        }
                    }
                }
                foreach (explode("\r\n",$row2['modcmds']) as $line) {
                    if (preg_match('/^(\[[\w\/\.\-\_\= ]{1,}\])$/',$line)) {
                        $name=trim($line,'[]');
                        $ex=preg_split("/\=/",$name,-1,PREG_SPLIT_NO_EMPTY);
                        if (isset($ex[1]) and trim($ex[1])=='default' and ($modcmd===null or $modcmd==='')) $modcmd=$ex[0];
                        $name=trim($ex[0]);
                        if (!isset($modsCmds[$name])) $modsCmds[$name]=array();
                    } else if (isset($name) and isset ($modsCmds[$name]) and $line!='') {
                        $modsCmds[$name][]=$line;
                    }
                }
                if ($row['owncmd']=='N') $cmd=$row2['cmd'];
            }
            if ($qstat=='a2s' and $tvenable=='N') $cmd .=" -nohltv -tvdisable";
            if (($protected=='N' and ($qstat=='a2s' or $qstat=='hla2s') and ($anticheat==2 or $anticheat==3 or $anticheat==4 or $anticheat==5 or $anticheat==6))) {
                $cmd .=" -insecure";
            } else if (($protected=='Y' and ($anticheat==3 or $anticheat==4 or $anticheat==5 or $anticheat==6)) and ($qstat=='a2s' or $qstat=='hla2s') and $eacallowed=='Y') {
                $cmd .=" -insecure";
            }
            $query2=($protected=='Y') ? $sql->prepare("SELECT `addonid` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `paddon`='Y' AND `resellerid`=?") : $sql->prepare("SELECT `addonid` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `paddon`='N' AND `resellerid`=?");
            $installedaddons=array();
            $rmarray=array();
            $query2->execute(array($user_id,$serverid,$reseller_id));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $query3=$sql->prepare("SELECT `cmd`,`rmcmd`,`addon`,`type` FROM `addons` WHERE `id`=? AND `resellerid`=? AND `active`='Y' LIMIT 1");
                $query3->execute(array($row2['addonid'],$reseller_id));
                foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                    if ($row3['type']=='tool') $installedaddons[]=$row3['addon'];
                    if ($row3['cmd']!=null) $cmd .=" ".$row3['cmd'];
                    if ($row3['rmcmd']!=null) {
                        foreach (preg_split("/\r\n/",$row3['rmcmd'],-1,PREG_SPLIT_NO_EMPTY) as $rm) $rmarray[]=$rm;
                    }
                }
            }
            foreach ($rmarray as $rm) $cmd=str_replace($rm,'',$cmd);
            $query2=$sql->prepare("SELECT `rcon`,`password`,`slots`,AES_DECRYPT(`ftpuploadpath`,?) AS `decyptedftpuploadpath` FROM `lendedserver` WHERE `serverid`=? AND `servertype`='g' AND `resellerid`=? LIMIT 1");
            $query2->execute(array($aeskey,$serverid,$reseller_id));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $slots=$row2['slots'];
                if ($row2['decyptedftpuploadpath']!=null and $row2['decyptedftpuploadpath']!="" and $row2['decyptedftpuploadpath']!="ftp://username:password@1.1.1.1/demos") $ftpupload=$row2['decyptedftpuploadpath'];
                if ($qstat=='a2s') {
                    $cmd .=" +rcon_password ${row2['rcon']} +sv_password ${row2['password']} +tv_enable 1 +tv_autorecord 1";
                } else if ($qstat=='hla2s') {
                    $cmd .=" +rcon_password ${row2['rcon']} +sv_password ${row2['password']}";
                }
            }
            if (isset($modcmd) and isset($modsCmds[$modcmd]) and is_array($modsCmds[$modcmd])) {
                foreach ($modsCmds[$modcmd] as $singleModADD) $cmd .=' '.$singleModADD;
            }
            if (in_array($row['appID'],array(730,740)) and isid($row['workshopCollection'],10) and wpreg_check($row['dwebapiAuthkey'],32) and strlen($row['dwebapiAuthkey'])>0 and $row['workshopCollection']>0) {
                $cmd .=" +host_workshop_collection ${row['workshopCollection']} +workshop_start_map ${map} -authkey ${row['dwebapiAuthkey']}";
                $cmd=preg_replace('/[\s\s+]{1,}\+map[\s\s+]{1,}[\w-_!%]{1,}/','',$cmd);
            }
            $rdata=serverdata('root',$rootid,$aeskey);
            $sship=$rdata['ip'];
            $sshport=$rdata['port'];
            $sshuser=$rdata['user'];
            $sshpass=$rdata['pass'];
            $ftpport=$rdata['ftpport'];
            $serverfolder=$gsip."_"."$port/$folder/$binarydir";
            $replace=array($gamebinary,$tic,$gsip,$port,$port2,$port2,$port3,$port4,$port5,$slots,$map,$mapGroup,$fps,$minram,$maxram,$maxcores,$folder,$customer,$absolutepath);
            $startline=str_replace($cvars,$replace,$cmd);
            foreach (customColumns('G',$switchID) as $cu) $startline=str_replace("%${cu['name']}%",$cu['value'],$startline);
            if ($protected=='Y' and $pallowed=='Y') {
                $customerUnprotected=$customer;
                $ftppassUnprotected=$ftppass;
                $customer=$customer.'-p';
                $ftppass=$decryptedftppass;
            } else if ($protected=='N' and $pallowed=='Y') {
                $customerProtected=$customer.'-p';
                $ftppassProtected=$decryptedftppass;
            }
            if ($action!='du' and $eacallowed=='Y' and ($anticheat==3 or $anticheat==4 or $anticheat==5 or $anticheat==6) and ($qstat=='a2s' or $qstat=='hla2s')) {
                if ($action=='so' or $action=='sp') {
                    $rcon="";
                    eacchange('remove',$serverid,$rcon,$reseller_id);
                } else if ($action=='re') {
                    if($gamebinary=="srcds_run") {
                        $config=$modfolder."/cfg/server.cfg";
                    } else if($gamebinary=="hlds_run") {
                        $config=$modfolder."/server.cfg";
                    } else {
                        $config="main/server.cfg";
                    }
                    $configfile="";
                    $fp= @fopen("ftp://$customer:$ftppass@$sship:$ftpport/$pserver$serverfolder/$config",'r');
                    if ($fp==true) {
                        stream_set_timeout($fp,'5');
                        while (!feof($fp)) $configfile .=fread($fp,1024);
                        $info=stream_get_meta_data($fp);
                        fclose($fp);
                    }
                    if (isset($info['timed_out']) and $info['timed_out']=="") {
                        $configfile=str_replace(array("\0","\b","\r","\Z"),"",$configfile);
                        $lines=explode("\n",$configfile);
                        $lines=preg_replace('/\s+/',' ',$lines);
                        foreach ($lines as $singeline) {
                            if (preg_match("/\w/",substr($singeline,0,1))) {
                                if (preg_match("/\"/",$singeline)) {
                                    $split=explode('"',$singeline);
                                    $cvar=str_replace(" ","",$split[0]);
                                    $value=$split[1];
                                    if ($cvar=="rcon_password") $rcon=$value;
                                } else {
                                    $split=explode(' ',$singeline);
                                    if (isset($split[0])) {
                                        $cvar=$split[0];
                                        $value=(isset($split[1])) ? $split[1] : '';
                                        if ($cvar=="rcon_password") $rcon=$value;
                                    }
                                }
                            }
                        }
                        if (isset($rcon)) eacchange('change',$serverid,$rcon,$reseller_id);
                    }
                }
            } else if ($action!='du' and $eacallowed=='Y' and ($qstat=='a2s' or $qstat=='hla2s') and ($anticheat==1 or $anticheat==2)) {
                $rcon="";
                eacchange('remove',$serverid,$rcon,$reseller_id);
            }
            if ($protected=='N') {
                $protectedString='unprotected';
            } else {
                $protectedString='protected';
            }
            if ($action=='so' or $action=='sp') {
                if ($action=='so') {
                    $tempCmds[]="sudo -u ${customer} ./control.sh gstop $customer \"$serverfolder\" $qstat $protectedString";
                    if ((isset($ftpupload) and $qstat=='a2s')) $tempCmds[]="sudo -u ${customer} ./control.sh demoupload \"$bindir\" \"$ftpupload\" \"$modfolder\"";
                } else {
                    $tempCmds[]="sudo -u ${customer} ./control.sh stopall";
                }
                $stopped='Y';
            } else if ($action=='re') {
                if ($protected=='N' and count($installedaddons)>0) $tempCmds[]="sudo -u ${customer} ./control.sh addonmatch $customer \"$serverfolder\" \"".implode(' ',$installedaddons)."\"";
                $restartCmd="sudo -u ${customer} ./control.sh grestart $customer \"$serverfolder\" \"$startline\" $protectedString $qstat \"$cores\"";
                $stopped='N';
            }
            if (!isset($ftpupload) and $qstat=='a2s' and isurl($uploaddir)) {
                if ($upload==2) {
                    $uploadcmd="./control.sh demoupload \"$bindir\" \"$uploaddir\" \"$modfolder\" manual remove";
                } else if ($upload==3) {
                    $uploadcmd="./control.sh demoupload \"$bindir\" \"$uploaddir\" \"$modfolder\" manual keep";
                } else if ($upload==4) {
                    $uploadcmd="./control.sh demoupload \"$bindir\" \"$uploaddir\" \"$modfolder\" auto remove";
                } else if ($upload==5) {
                    $uploadcmd="./control.sh demoupload \"$bindir\" \"$uploaddir\" \"$modfolder\" auto keep";
                }
                if ($action=='du' and isset($uploadcmd)) {
                    $stopped='N';
                    $sshcmd=array($uploadcmd);
                } else if ($action!='so' and $action!='sp' and isset($uploadcmd)) {
                    $tempCmds[]="sudo -u ${customer} $uploadcmd";
                }
            }
            foreach ($cvarprotect as $config => $values) if (count($values['cvars'])==0) unset($cvarprotect[$config]);
            if (count($cvarprotect)>0 and $action!='du') {
                if ($ftpport==21 or $ftpport=="" or $ftpport==null) {
                    $ftp_connect= ftp_connect($sship);
                } else {
                    $ftp_connect= ftp_connect($sship,$ftpport);
                }
                if ($ftp_connect) {
                    $ftp_login= @ftp_login($ftp_connect,$customer,$ftppass);
                    if ($ftp_login) {
                        foreach ($cvarprotect as $config => $values) {
                            $temp=tmpfile();
                            $temp2=tmpfile();
                            $cfgtype=$values['type'];
                            if($gamebinary=="srcds_run") {
                                $config=$modfolder."/".$config;
                            } else if($gamebinary=="hlds_run") {
                                $config=$modfolder."/".$config;
                            }
                            $split_config=preg_split('/\//',$config,-1,PREG_SPLIT_NO_EMPTY);
                            $folderfilecount=count($split_config)-1;
                            $i=0;
                            $folders="/$pserver$serverfolder";
                            while ($i<$folderfilecount) {
                                $folders .="/".$split_config["$i"];
                                $i++;
                            }
                            $uploadfile=$split_config["$i"];
                            @ftp_chdir($ftp_connect,$folders);
                            if (strlen($uploadfile)>0 and @ftp_fget($ftp_connect,$temp,$uploadfile,FTP_ASCII,0)) {
                                fseek($temp,0);
                                fseek($temp,0);
                                $configfile='';
                                while (!feof($temp)) {
                                    $configfile .=fread($temp,1024);
                                }
                                fclose($temp);
                                $configfile=str_replace(array("\0","\b","\r","\Z"),"",$configfile);
                                $lines=explode("\n",$configfile);
                                $linecount=count($lines)-1;
                                $i=0;
                                foreach ($lines as $singeline) {
                                    $edited=false;
                                    $lline=strtolower($singeline);
                                    foreach ($values['cvars'] as $cvar => $value) {
                                        if ($cfgtype=='cfg' and preg_match("/^(.*)".strtolower($cvar)."\s+(.*)$/",$lline)) {
                                            $splitline=preg_split("/$cvar/",$lline,-1,PREG_SPLIT_NO_EMPTY);
                                            if (isset($splitline[1])) {
                                                fwrite($temp2,$splitline[0].$cvar." ".$value);
                                            } else {
                                                fwrite($temp2,$cvar." ".$value);
                                            }
                                            $edited=true;
                                        } else if ($cfgtype=='ini' and preg_match("/^(.*)".strtolower($cvar)."(\=|\s+\=\s+|\s+\=|\=\s+)(.*)$/",$lline)) {
                                            $splitline=preg_split("/$cvar/",$lline,-1,PREG_SPLIT_NO_EMPTY);
                                            if (isset($splitline[1]) and preg_match("/^(.*)".strtolower($cvar)."\s+\=\s+(.*)$/",$splitline[1])) {
                                                fwrite($temp2,$splitline[0].$cvar." = ".$value);
                                            } else if (preg_match("/^(.*)".strtolower($cvar)."\s+\=\s+(.*)$/",$splitline[0])) {
                                                fwrite($temp2,$cvar." = ".$value);
                                            } else {
                                                fwrite($temp2,$cvar."=".$value);
                                            }
                                            $edited=true;
                                        } else if ($cfgtype=='xml' and preg_match("/^(.*)<".strtolower($cvar).">(.*)<\/".strtolower($cvar).">$/",$lline)) {
                                            $splitline=preg_split("/\<$cvar/",$lline,-1,PREG_SPLIT_NO_EMPTY);
                                            if (isset($splitline[1])) {
                                                fwrite($temp2,$splitline[0]."<".$cvar.">".$value."</".$cvar.">");
                                            } else {
                                                fwrite($temp2,"<".$cvar.">".$value."</".$cvar.">");
                                            }
                                            $edited=true;
                                        }
                                    }
                                    if ($edited==false) {
                                        fwrite($temp2,$singeline);
                                    }
                                    if ($i<$linecount) {
                                        fwrite($temp2,"\r\n");
                                    }
                                    $i++;
                                }
                                fseek($temp2,0);
                                fseek($temp2,0);
                                @ftp_fput($ftp_connect,$uploadfile,$temp2,FTP_ASCII);
                            }
                            fclose($temp2);
                        }
                    }
                    ftp_close($ftp_connect);
                }
            }
            $query=$sql->prepare("UPDATE `gsswitch` SET `stopped`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($stopped,$switchID,$reseller_id));
            $cmds=array();
            if ($pallowed=='Y' and $protected=='Y') $cmds[]="sudo -u ${customerUnprotected} ./control.sh stopall";
            else if ($pallowed=='Y' and $protected=='N') $cmds[]="sudo -u ${customerProtected} ./control.sh stopall";
            if (isset($restartCmd)) $cmds[]=$restartCmd;
            foreach ($tempCmds as $c) $cmds[]=$c;
            return $cmds;
        }
        return false;
    }
    function webhostdomain($resellerid) {
        global $sql;
        $query=$sql->prepare("SELECT `paneldomain` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($resellerid));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $paneldomain=$row['paneldomain'];
        }
        if (!isset($paneldomain) or (isset($paneldomain) and !filter_var($paneldomain,FILTER_VALIDATE_URL))) {
            $query=$sql->prepare("SELECT `paneldomain` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $paneldomain=$row['paneldomain'];
            }
        }
        return $paneldomain;
    }
    function smtpMail ($host,$port,$user,$pass,$to,$from,$subject,$mail,$ssl='N') {
        $user=base64_encode($user);
        $pass=base64_encode($pass);
        $smtpSocket=fsockopen($host,$port,$errno,$errstr,10);
        if ($smtpSocket) {
            stream_set_blocking($smtpSocket,true);
            fputs($smtpSocket,"EHLO ".$host."\r\n");
            $response='EHLO: '.fgets($smtpSocket,1024).'<br>';
            if ($ssl=='T') {
                fputs($smtpSocket,"STARTTLS\r\n");
                $response.='STARTTLS: '.fgets($smtpSocket,1024).'<br>';
                $crypto=stream_socket_enable_crypto($smtpSocket,true,STREAM_CRYPTO_METHOD_TLS_CLIENT);
                fputs($smtpSocket,"EHLO ".$host."\r\n");
                $response.='EHLO: '.fgets($smtpSocket,1024).'<br>';
            }
            if ((isset($crypto) and $crypto!=false) or $ssl=='N') {
                fputs($smtpSocket,"auth login\r\n");
                $response.='auth login: '.fgets($smtpSocket,1024).'<br>';
                fputs($smtpSocket,$user."\r\n");
                $response.='User: '.fgets($smtpSocket,1024).'<br>';
                fputs($smtpSocket,$pass."\r\n");
                $response.='Pass: '.fgets($smtpSocket,256).'<br>';
                fputs($smtpSocket,"MAIL FROM: <${from}>\r\n");
                $response.='MAIL FROM: '.fgets($smtpSocket,1024).'<br>';
                fputs($smtpSocket,"RCPT TO: <${to}>\r\n");
                $response.='RCPT TO: '.fgets($smtpSocket,1024).'<br>';
                fputs($smtpSocket,"DATA\r\n");
                fputs($smtpSocket,"From: ${from}\r\n");
                fputs($smtpSocket,"Subject: ${subject}\r\n");
                fputs($smtpSocket,"To: ${to}\r\n");
                fputs($smtpSocket,"X-Sender: <${from}>\r\n");
                fputs($smtpSocket,"Return-Path: <${from}>\r\n");
                fputs($smtpSocket,"Errors-To: <${from}>\r\n");
                fputs($smtpSocket,"X-Mailer: Easy-Wi.com\r\n");
                fputs($smtpSocket,"MIME-Version: 1.0\r\n");
                fputs($smtpSocket,"Content-type: text/html; charset=UTF-8\r\n");
                $response.='DATA: '.fgets($smtpSocket,1024).'<br>';
                fputs($smtpSocket,iconv(mb_detect_encoding($mail,mb_detect_order(),true),"UTF-8",$mail)."\r\n.\r\n");
                $response.='Content: '.fgets($smtpSocket,strlen("${mail}\r\n.\r\n")).'<br>';
                fputs($smtpSocket,"RSET\r\n");
                $response.= 'RSET: '.fgets($smtpSocket,1024).'<br>';
                fputs($smtpSocket,"QUIT\r\n");
                fclose($smtpSocket);
                return true;
            }
        }
        return false;
    }
    function sendmail($template,$userid,$server,$shorten) {
        global $sql;
        $aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
        $aeskey=$aesfilecvar['aeskey'];
        if ($template=='emailnewticket') {
            $writerid=$shorten[1];
            $shorten=$shorten[0];
        }
        $userLanguage='';
        $resellerLanguage='';
        $query=$sql->prepare("SELECT `mail`,`vname`,`name`,`cname`,`language`,`resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($userid));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $usermail=$row['mail'];
            $username=$row['vname']." ".$row['name'];
            if ($username==' ' or $username=='') {
                $username=$row['cname'];
            }
            $userLanguage=$row['language'];
            $resellerid=$row['resellerid'];
        }
        if ($template=='emailnewticket') {
            $query=$sql->prepare("SELECT `vname`,`name`,`cname` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($writerid));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $username=($row['vname'].' '.$row['name']==' ') ? $row['cname'] : $row['vname'].' '.$row['name'];
            }
        }
        if(!isset($resellerid) or $resellerid==$userid) {
            $resellersid=0;
            if(!isset($resellerid)) $resellerid=0;
        } else {
            $resellersid=$resellerid;
        }
        $query=$sql->prepare("SELECT *,AES_DECRYPT(`email_settings_password`,?) AS `decryptedpassword` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($aeskey,$resellersid));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $emailregards=nl2br($row['emailregards']);
            $emailfooter=nl2br($row['emailfooter']);
            $resellersmail=$row['email'];
            $resellerLanguage=$row['language'];
            $email_settings_type=$row['email_settings_type'];
            if($email_settings_type=='S'){
                $email_settings_host=$row['email_settings_host'];
                $email_settings_port=$row['email_settings_port'];
                $email_settings_user=$row['email_settings_user'];
                $email_settings_password=$row['decryptedpassword'];
                $email_settings_ssl=$row['email_settings_ssl'];
            }
        }
        if (isset($email_settings_type) and $email_settings_type!='N') {
            $query=$sql->prepare("SELECT `email`,`timezone` FROM `settings` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($resellerid));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $resellerstimezone=$row['timezone'];
                $resellermail=$row['email'];
            }
            if ($template!='contact') {
                if($resellerid==$userid) {
                    $resellermail=$resellersmail;
                    $lookupID=$resellersid;
                } else {
                    $lookupID=$resellerid;
                }
                $query=$sql->prepare("SELECT `text` FROM `translations` WHERE `type`='em' AND `lang`=? AND `transID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($userLanguage,$template,$lookupID));
                $sprache=@simplexml_load_string($query->fetchColumn());
                if (!$sprache) {
                    $query->execute(array($resellerLanguage,$template,$lookupID));
                    $sprache=@simplexml_load_string($query->fetchColumn());
                }
                if (!$sprache) {
                    $query=$sql->prepare("SELECT `text` FROM `translations` WHERE `type`='em' AND `transID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($template,$lookupID));
                    $sprache=@simplexml_load_string($query->fetchColumn());
                }
                $query=$sql->prepare("SELECT `$template` FROM `settings` WHERE `resellerid`=? LIMIT 1");
                $query->execute(array($lookupID));
                $mailtext= @gzuncompress($query->fetchColumn());
            }
            $header='MIME-Version: 1.0' . "\n";
            $header .="Content-type: text/html; charset=utf-8"."\n";
            if (!isset($resellerstimezone)) $resellerstimezone=0;
            $maildate=date('Y-m-d H:i:s',strtotime("$resellerstimezone hour"));
            if (isset($sprache) and isset($sprache->topic) and $mailtext!='') {
                $keys=array('%server%','%username%','%date%','%shorten%','%emailregards%','%emailfooter%');
                $replacements=array($server,$username,$maildate,$shorten,$emailregards,$emailfooter);
                $topic=$sprache->topic;
                foreach ($sprache as $key => $value) {
                    if ($key!="server" and $key!="title" and $key!="username" and $key!="shorten" and $key!="date" and $key!="emailregards" and $key!="emailfooter") {
                        if ($template=='emailnewticket' and $key=='topic') {
                            $value=$sprache->topic." #".$shorten;
                            $topic=$value;
                        }
                        $keys[]="%$key%";
                        $replacements[]=htmlentities($value,null,'UTF-8');
                    }
                }
                $mail=str_replace($keys,$replacements,$mailtext);
                if ($usermail!='ts3@import.mail' and ismail($usermail) and isset($mail)) {
                    if ($email_settings_type=='P') {
                        if (isset($debug) and $debug==1) $sended=mail($usermail,$topic,$mail,$header,"-f $resellermail");
                        else $sended=@mail($usermail,$topic,$mail,$header,"-f $resellermail");
                    } else {
                        if (isset($debug) and $debug==1) $sended=smtpMail($email_settings_host,$email_settings_port,$email_settings_user,$email_settings_password,$usermail,$resellermail,$topic,$mail,$email_settings_ssl);
                        else $sended=@smtpMail($email_settings_host,$email_settings_port,$email_settings_user,$email_settings_password,$usermail,$resellermail,$topic,$mail,$email_settings_ssl);
                    }
                    if ($sended==true) {
                        $query=$sql->prepare("INSERT INTO `mail_log` (`uid`,`topic`,`date`,`resellerid`) VALUES (?,?,NOW(),?)");
                        if($resellerid==$userid) {
                            $query->execute(array($userid,$topic,$resellersid));
                        } else {
                            $query->execute(array($userid,$topic,$resellerid));
                        }
                    }
                }
            } else if ($template=='contact') {
                if ($email_settings_type=='P') {
                    if (isset($debug) and $debug==1) mail($resellermail,'You\'ve been contacted by '.$userid .'.',$server,$header,"-f $shorten");
                    else @mail($resellermail,'You\'ve been contacted by '.$userid .'.',$server,$header,"-f $shorten");
                } else {
                    if (isset($debug) and $debug==1) smtpMail($email_settings_host,$email_settings_port,$email_settings_user,$email_settings_password,$resellersmail,$resellermail,'You\'ve been contacted by '.$userid .'.',$server,$email_settings_ssl);
                    else @smtpMail($email_settings_host,$email_settings_port,$email_settings_user,$email_settings_password,$resellersmail,$resellermail,'You\'ve been contacted by '.$userid .'.',$server,$email_settings_ssl);
                }
            }
        }
    }
    function IncludeTemplate($use,$file) {
        if (is_file(EASYWIDIR.'/template/'.$use.'/'.$file) and preg_match('/^(.*)\.[\w]{1,}$/',$file)) {
            return EASYWIDIR.'/template/'.$use.'/'.$file;
        } else if (is_file(EASYWIDIR.'/template/default/'.$file) and preg_match('/^(.*)\.[\w]{1,}$/',$file)) {
            return EASYWIDIR.'/template/default/'.$file;
        } else if (preg_match('/^(.*)\.[\w]{1,}$/',$file)) {
            return EASYWIDIR.'/template/'.$file;
        }
    }
    function User_Permissions($id) {
        global $sql;
        $pa=array();
        $query=$sql->prepare("SELECT `accounttype` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        $accounttype=$query->fetchColumn();
        $query=$sql->prepare("SELECT g.* FROM `userdata_groups` a LEFT JOIN `usergroups` g ON g.`id`=a.`groupID` WHERE a.`userID`=?");
        $query->execute(array($id));
        $array=$query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($array as $row) {
            if (($accounttype=='u' and $row['miniroot']=='Y')) {
                foreach ($row as $key => $value) $pa["$key"]=true;
            } else if (($accounttype!='u' and $row['root']=='Y')) {
                foreach ($row as $key => $value) $pa["$key"]=true;
            } else {
                foreach ($row as $key => $value) {
                    if ((isset($pa["$key"]) and $pa["$key"]===false) or !isset($pa["$key"])) $pa["$key"]=($value=='Y') ? true : false;
                }
            }
        }
        return $pa;
    }
    function array_value_exists($key,$value,$array) {
        if (array_key_exists($key,$array) and $array[$key]==$value) return true;
        return false;
    }
    function updateJobs($localID,$resellerID,$jobPending='Y') {
        global $sql;
        $update=$sql->prepare("UPDATE `gsswitch` SET `jobPending`=? WHERE `userid`=? AND `resellerid`=?");
        $update->execute(array($jobPending,$localID,$resellerID));
        $update=$sql->prepare("UPDATE `mysql_external_dbs` SET `jobPending`=? WHERE `uid`=? AND `resellerid`=?");
        $update->execute(array($jobPending,$localID,$resellerID));
        $update=$sql->prepare("UPDATE `virtualcontainer` SET `jobPending`=? WHERE `userid`=? AND `resellerid`=?");
        $update->execute(array($jobPending,$localID,$resellerID));
        $update=$sql->prepare("UPDATE `voice_server` SET `jobPending`=? WHERE `userid`=? AND `resellerid`=?");
        $update->execute(array($jobPending,$localID,$resellerID));
        $update=$sql->prepare("UPDATE `voice_dns` SET `jobPending`=? WHERE `userID`=? AND `resellerID`=?");
        $update->execute(array($jobPending,$localID,$resellerID));
    }
    function updateStates($sql,$action,$type=null) {
        $typeQuery=($type!=null) ? " AND `type`='${type}" : '';
        $query=$sql->prepare("SELECT `type`,`affectedID` FROM `jobs` WHERE (`status` IS NULL OR `status`=1) AND `action`=? $typeQuery GROUP BY `type`,`affectedID`");
        $query->execute(array($action));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $query2=$sql->prepare("SELECT `jobID` FROM `jobs` WHERE `type`=? AND `affectedID`=? AND `action`=? $typeQuery ORDER BY `jobID` DESC LIMIT 1");
            $query2->execute(array($row['type'],$row['affectedID'],$action));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                if ($type==null) {
                    $update=$sql->prepare("UPDATE `jobs` SET `status`='2' WHERE (`status` IS NULL OR `status`=1) AND `type`=? AND `affectedID`=? AND `jobID`!=?");
                    $update->execute(array($row['type'],$row['affectedID'],$row2['jobID']));
                } else {
                    $update=$sql->prepare("UPDATE `jobs` SET `status`='2' WHERE (`status` IS NULL OR `status`=1) AND `userID`=? AND `jobID`!=?");
                    $update->execute(array($row['affectedID'],$row2['jobID']));
                }
            }
        }
    }
    function dataExist ($value,$array) {
        if (isset($array[$value]) and isset($array[$array[$value]]) and !in_array($array[$array[$value]],array(false,null,''))) return true;
        return false;
    }
    function webhostRequest ($domain,$useragent,$file,$postParams='',$port=80) {
        $domain=str_replace(array('https://','http://'),'',$domain);
        if (isdomain($domain)) $fp=@fsockopen($domain,$port,$errno,$errstr,10);
        else $errstr=$domain.' is no domain';
        if(isset($fp) and $fp) {
            if(is_array($postParams) and count($postParams)>0) {
                $postData='';
                $i=0;
                foreach ($postParams as $key=>$value) {
                    if ($i==0){
                        $postData .=$key.'='.$value;
                    } else {
                        $postData .='&'.$key.'='.$value;
                    }
                    $i++;
                }
                $send = "POST /".$file." HTTP/1.1\r\n";
            } else {
                if (strlen($file)==0) $file='/';
                $send="GET $file HTTP/1.1\r\n";
            }
            $send .= "Host: ".$domain."\r\n";
            $send .="User-Agent: $useragent\r\n";
            $send .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
            if(is_array($postParams) and count($postParams)>0) {
                $send .= "Content-Length: ".strlen($postData)."\r\n";
            }
            $send .= "Connection: Close\r\n\r\n";
            if(is_array($postParams) and count($postParams)>0) {
                $send .= $postData;
            }
            fwrite($fp,$send);
            $buffer='';
            while (!feof($fp)) {
                $buffer.=fgets($fp,4096);
            }
            fclose($fp);
            $ex=explode("\r\n\r\n",$buffer);
            if (strpos($ex[0],'404')!==false) {
                return 'file not found: '.$domain.'/'.$file;
            } else if (isset($ex[1])) {
                return $ex[1];
            } else {
                $errstr='Error: no response. Header is: '.$ex[0];
            }
        }
        return 'Error: Could not connect to host '.$domain.' and port '.$port.' ('.$errstr.')';
    }
    function checkPorts ($send,$used) {
        foreach ($send as $port) if (!port($port) or in_array($port,$used)) return false;
        return true;
    }
    function usedPorts ($ips) {
        global $sql;
        $portsArray=array();
        foreach ($ips as $serverIP) {
            $ports=array();
            $query=$sql->prepare("SELECT `port`,`port2`,`port3`,`port4`,`port5` FROM `gsswitch` WHERE `serverip`=? ORDER BY `port`");
            $query->execute(array($serverIP));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if (port($row['port'])){
                    $ports[]=$row['port'];
                }
                if (port($row['port2'])){
                    $ports[]=$row['port2'];
                }
                if (port($row['port3'])){
                    $ports[]=$row['port3'];
                }
                if (port($row['port4'])){
                    $ports[]=$row['port4'];
                }
                if (port($row['port5'])){
                    $ports[]=$row['port5'];
                }
            }
            $query=$sql->prepare("SELECT `port` FROM `voice_server` WHERE `ip`=?");
            $query->execute(array($serverIP));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if (port($row['port'])){
                    $ports[]=$row['port'];
                }
            }
            $ports=array_unique($ports);
            asort($ports);
            $portsArray[count($ports)]=array('ip'=>$serverIP,'ports'=>$ports);
        }
        $bestIP=current($portsArray);
        return array('ip'=>$bestIP['ip'],'ports'=>$bestIP['ports']);
    }
    function array2xml($array,$xml){
        foreach($array as $key => $value){
            if (is_numeric($key)) {
                $key='key'.$key;
            }
            if(is_array($value)){
                array2xml($value,$xml->addChild($key));
            } else {
                $xml->$key=$value;
            }
        }
        return $xml->asXML();
    }
    function yesNo ($check) {
        global $ui;
        if($ui->active($check,'post')=='Y') {
            return 'Y';
        }
        return 'N';
    }
    function returnPlainArray ($arr,$key) {
        $return=array();
        if (is_array($arr) and !is_array($key)) {
            foreach ($arr as $v) {
                $return[]=$v[$key];
            }
        }
        return $return;
    }
    function licenceRequest($return=false) {
        global $sql,$ui;
        $licencecode=webhostRequest('l.easy-wi.com',$ui->server['HTTP_HOST'],'/licence.php',null,80);
        $licencecode=cleanFsockOpenRequest($licencecode,'{','}');
        $json=@json_decode($licencecode);
        if ($json and isset($json->v)) {
            $licencecode=array();
            foreach($json as $k=>$v) $licencecode[$k]=$v;
            $time=time();
            $licencecode['lt']="${time}";
            $licencecode=json_encode($licencecode);
            $query2=$sql->prepare("UPDATE `settings` SET `licence`=?,`version`=?,`releasenotesDE`=?,`releasenotesEN`=? WHERE `resellerid`=0 LIMIT 1");
            $query2->execute(array($licencecode,$json->v,$json->de,$json->en));
        }
        if ($return==true) return $licencecode;
    }
    function token ($check=false) {
        global $ui,$_SESSION;
        if ($check==false) {
            $token=md5(mt_rand());
            if ($ui->id('id',10,'get') and $ui->smallletters('d',10,'get')) $_SESSION[$ui->smallletters('w',10,'get')][$ui->smallletters('d',10,'get')][$ui->id('id',10,'get')]=array('t'=>$token,'d'=>strtotime("+20 minutes"));
            else if (!$ui->id('id',10,'get') and $ui->smallletters('d',10,'get')) $_SESSION[$ui->smallletters('w',10,'get')][$ui->smallletters('d',10,'get')]=array('t'=>$token,'d'=>strtotime("+20 minutes"));
            else if ($ui->id('id',10,'get') and !$ui->smallletters('d',10,'get')) $_SESSION[$ui->smallletters('w',10,'get')][$ui->id('id',10,'get')]=array('t'=>$token,'d'=>strtotime("+20 minutes"));
            else $_SESSION[$ui->smallletters('w',10,'get')]=array('t'=>$token,'d'=>strtotime("+15 minutes"));
            return $token;
        } else {
            if (isset($_SESSION[$ui->smallletters('w',10,'get')][$ui->smallletters('d',10,'get')][$ui->id('id',10,'get')]['t']) and $_SESSION[$ui->smallletters('w',10,'get')][$ui->smallletters('d',10,'get')][$ui->id('id',10,'get')]['t']==$ui->w('token',32,'post') and $_SESSION[$ui->smallletters('w',10,'get')][$ui->smallletters('d',10,'get')][$ui->id('id',10,'get')]['d']>=strtotime("now")) {
                deleteOldToken($ui->smallletters('w',10,'get'),$ui->smallletters('d',10,'get'),$ui->id('id',10,'get'));
                return true;
            } else if (isset($_SESSION[$ui->smallletters('w',10,'get')][$ui->smallletters('d',10,'get')]['t']) and $_SESSION[$ui->smallletters('w',10,'get')][$ui->smallletters('d',10,'get')]['t']==$ui->w('token',32,'post') and $_SESSION[$ui->smallletters('w',10,'get')][$ui->smallletters('d',10,'get')]['d']>=strtotime("now")) {
                deleteOldToken($ui->smallletters('w',10,'get'),$ui->smallletters('d',10,'get'));
                return true;
            } else if (isset($_SESSION[$ui->smallletters('w',10,'get')][$ui->id('id',10,'get')]['t']) and $_SESSION[$ui->smallletters('w',10,'get')][$ui->id('id',10,'get')]['t']==$ui->w('token',32,'post') and $_SESSION[$ui->smallletters('w',10,'get')][$ui->id('id',10,'get')]['d']>=strtotime("now")) {
                deleteOldToken($ui->smallletters('w',10,'get'),'',$ui->id('id',10,'get'));
                return true;
            } else if (isset($_SESSION[$ui->smallletters('w',10,'get')]['t']) and $_SESSION[$ui->smallletters('w',10,'get')]['t']==$ui->w('token',32,'post') and $_SESSION[$ui->smallletters('w',10,'get')]['d']>=strtotime("now")) {
                deleteOldToken($ui->smallletters('w',10,'get'));
                return true;
            } else {
                deleteOldToken();
                return false;
            }
        }
    }
    function deleteOldToken ($w='',$d='',$id='') {
        global $_SESSION;
        if ($id!='' and $d!='') unset($_SESSION[$w][$d][$id]);
        else if ($id=='' and $d!='') unset($_SESSION[$w][$d]);
        else if ($id!='' and $d=='') unset($_SESSION[$w][$id]);
        else if ($id=='' and $d=='') unset($_SESSION[$w]);
        foreach ($_SESSION as $k=>$v) {
            if (wpreg_check($k,4) and ((isset($_SESSION[$k]['t']) and $_SESSION[$k]['d']<strtotime("now")) or (is_array($_SESSION[$k]) and count($_SESSION[$k])==0))) unset($_SESSION[$k]);
            else if (wpreg_check($k,4) and is_array($_SESSION[$k]) and count($_SESSION[$k])>0) {
                foreach ($_SESSION[$k] as $k2=>$v2) {
                    if (wpreg_check($k2,4) and ((isset($_SESSION[$k][$k2]['t']) and $_SESSION[$k][$k2]['d']<strtotime("now")) or (is_array($_SESSION[$k][$k2]) and count($_SESSION[$k][$k2])==0))) unset($_SESSION[$k][$k2]);
                    else if (wpreg_check($k2,4) and is_array($_SESSION[$k][$k2]) and count($_SESSION[$k][$k2])>0) {
                        foreach ($_SESSION[$k][$k2] as $k3=>$v3) {
                            if (isid($k3,4) and ((isset($_SESSION[$k][$k2][$k3]['t']) and $_SESSION[$k][$k2][$k3]['d']<strtotime("now")) or (is_array($_SESSION[$k][$k2][$k3]) and count($_SESSION[$k][$k2][$k3])==0))) unset($_SESSION[$k][$k2][$k3]);
                        }
                    }
                }
            }
        }
    }
    function customColumns($item,$id=0,$action=false,$api=false) {
        global $sql,$user_language,$default_language;
        $return=array();
        $query=$sql->prepare("SELECT * FROM `custom_columns_settings` WHERE `item`=? AND `active`='Y'");
        $query->execute(array($item));
        if ($action==false) {
            $query2=$sql->prepare("SELECT `text` FROM `translations` WHERE `type`='cc' AND `transID`=? AND `lang`=? LIMIT 1");
            $query3=$sql->prepare("SELECT `var` FROM `custom_columns` WHERE `customID`=? AND `itemID`=? LIMIT 1");
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $text='';
                $query2->execute(array($row['customID'],$user_language));
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    $text=$row2['text'];
                }
                if (empty($text)) {
                    $query2->execute(array($row['customID'],$default_language));
                    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                        $text=$row2['text'];
                    }
                }
                $type=($row['type']=='I') ? 'number' : 'text';
                $query3->execute(array($row['customID'],$id));
                $value=$id==0 ? '' : $query3->fetchColumn();
                $return[]=array('customID'=>$row['customID'],'menu'=>$text,'name'=>$row['name'],'length'=>$row['length'],'type'=>$row['type'],'input'=>"<input id='inputCustom-${row['customID']}' type='${type}' name='${row['name']}' maxlength='${row['length']}' value='${value}' >",'value'=>$value);
            }
        } else if ($action=='save') {
            $query2=$sql->prepare("INSERT INTO `custom_columns` (`customID`,`itemID`,`var`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `var`=VALUES(`var`)");
            if ($api==false) {
                global $ui;
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if ($row['type']=='I' and $ui->id($row['name'],$row['length'],'post')) {
                        $var=$ui->id($row['name'],$row['length'],'post');
                    } else if ($ui->names($row['name'],$row['length'],'post')) {
                        $var=$ui->names($row['name'],$row['length'],'post');
                    } else {
                        $var='';
                    }
                    $query2->execute(array($row['customID'],$id,$var));
                }
            } else {
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if ($row['type']=='I') {
                        $var=isid($api["${row['name']}"],$row['length']);
                    } else if (names($api["${row['name']}"],$row['length'])) {
                        $var=names($api["${row['name']}"],$row['length']);
                    } else {
                        $var='';
                    }
                    $query2->execute(array($row['customID'],$id,$var));
                }
            }
        } else if ($action=='del') {
            $query2=$sql->prepare("DELETE FROM `custom_columns` WHERE `customID`=? AND `itemID`=? LIMIT 1");
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $query2->execute(array($row['customID'],$id));
            }
        }
        return $return;
    }
}
function workAroundForValveChaos ($appID,$shorten,$toApi=true) {
    if ($toApi==true) {
        if ($appID==90 and $shorten=='cstrike') {
            return 10;
        } else if ($appID==90 and $shorten=='czero') {
            return 80;
        } else if ($appID==90 and $shorten=='dmc') {
            return 40;
        } else if ($appID==90 and $shorten=='dod') {
            return 30;
        } else if ($appID==90 and $shorten=='gearbox') {
            return 50;
        } else if ($appID==90 and $shorten=='ricochet') {
            return 60;
        } else if ($appID==90 and $shorten=='tfc') {
            return 20;
        } else if ($appID==740) {
            return 730;
        } else if ($appID==215360 or $appID==215350) {
            return 1250;
        } else if ($appID==229830) {
            return 440;
        } else if ($appID==232290) {
            return 300;
        } else if ($appID==232330) {
            return 240;
        } else if ($appID==232370) {
            return 320;
        }
    } else {
        if (in_array($appID,array(10,20,30,40,50,60,80))) {
            return 90;
        } else if ($appID==240) {
            return 232330;
        } else if ($appID==300) {
            return 232290;
        } else if ($appID==320) {
            return 232370;
        } else if ($appID==440) {
            return 229830;
        } else if ($appID==730) {
            return 740;
        } else if ($appID==1250 or $appID==215350) {
            return 215360;
        }
    }
    return $appID;
}