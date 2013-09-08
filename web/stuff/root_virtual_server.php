<?php
/**
 * File: root_virtual_server.php.
 * Author: Ulrich Block
 * Date: 29.04.12
 * Time: 11:57
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
if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['addvserver'] and !$pa['modvserver'] and !$pa['delvserver'] and !$pa['usevserver'])) {
    header('Location: admin.php');
    die;
}
$aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
$aeskey=$aesfilecvar['aeskey'];
$sprache=getlanguagefile('reseller',$user_language,$reseller_id);
$loguserid=$admin_id;
$logusername=getusername($admin_id);
$logusertype="admin";
$logreseller=0;
$logsubuser=0;
if ($reseller_id!=0) {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller=0;
}
if ($ui->st('d','get')=='ad' and is_numeric($licenceDetails['lVs']) and $licenceDetails['lVs']>0 and $licenceDetails['left']>0 and !is_numeric($licenceDetails['left'])) {
    $template_file=$gsprache->licence;
} else if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->st('d','get')=='ad' and ($reseller_id==0 or $admin_id==$reseller_id) and $pa['addvserver'] and (!is_numeric($licenceDetails['lVs']) or $licenceDetails['lVs']>0) and ($licenceDetails['left']>0 or !is_numeric($licenceDetails['left']))) {
    if (!$ui->smallletters('action',2,'post')) {
        $table=array();
        $table2=array();
        $besthostcpu='';
        $query=$sql->prepare("SELECT h.`id`, h.`ip`, h.`description`, h.`cores`, h.`mhz`, h.`hdd`, h.`ram`, h.`maxserver`, h.`maxserver`-COUNT(DISTINCT v.`id`) AS `freeserver`, h.`ram`-SUM(v.`minram`) AS `freeram`, h.`cores`*h.`mhz`-SUM(v.`cores`*v.`minmhz`) AS `freecpu`, h.`active` AS `active`, h.`resellerid` AS `resellerid`,h.`thin`,h.`thinquota` FROM `virtualhosts` h LEFT JOIN `virtualcontainer` v ON v.`hostid`=h.`id` GROUP BY h.`id` HAVING ((`freeserver` > 0 OR `freeserver` IS NULL) AND (`freecpu` > 0 OR `freecpu` IS NULL) AND (`freeram` > 0 OR `freeram` IS NULL) AND `active`='Y' AND (`resellerid`=? OR `resellerid`='0')) ORDER BY `freeram` DESC,`freecpu` DESC,`freeserver` DESC");
        $query->execute(array($reseller_id));
        if ($query->rowCount()>0) {
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                unset($ramused);
                unset($cpucore);
                unset($mountunused);
                unset($hdd);
                $id=$row['id'];
                $cores=$row['cores'];
                $mhz=$row['mhz'];
                $hddsize=$row['hdd'];
                $ram=$row['ram'];
                $maxserver=$row['maxserver'];
                $resellerid=$row['resellerid'];
                $ramused=0;
                $percent=($row['thin']=='Y') ? $row['thinquota'] : 100;
                $mountsize='';
                $mountunused='';
                $core='';
                $cpucore=array();
                $hdd='';
                $hdd_rows=explode("\r\n", $row['hdd']);
                foreach ($hdd_rows as $hddline) {
                    $data_explode=explode(" ", $hddline);
                    if (isset($data_explode[1])) {
                        $mountpoint=$data_explode[0];
                        $hdd[]=$mountpoint;
                        $mountsize[$mountpoint]=$data_explode[1];
                        $mountunused[$mountpoint]=0;
                    }
                }
                $i=1;
                $cpucores='';
                while ($i<=$cores) {
                    $cpucores[]=$i;
                    $cpucore[$i]=0;
                    $i++;
                }
                $query2=$sql->prepare("SELECT `cores`,`minmhz`,`maxmhz`,`hddsize`,`mountpoint`,`minram` FROM `virtualcontainer` WHERE `hostid`=:id");
                $query2->execute(array(':id'=>$id));
                $i2=0;
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    $mountpoint=$row2['mountpoint'];
                    $addstracthdd=$mountunused[$mountpoint]+($row2['hddsize']*($percent/100));
                    $mountunused[$mountpoint]=$addstracthdd;
                    $addstractram=$ramused+$row2['minram'];
                    $ramused=$addstractram;
                    $cpuhz=$row2['cores']*$row2['minmhz'];
                    $addcpu=$cpucore[1]+$cpuhz;
                    if ($addcpu<=$mhz) {
                        $cpucore[1]=$addcpu;
                    } else {
                        $cpucore[1]=$mhz;
                        $nextcore="2";
                        while ($nextcore<=$cores) {
                            $extra=$addcpu-$mhz;
                            $addcpu=$cpucore[$nextcore]+$extra;
                            if ($addcpu<=$mhz and $addcpu>=0) $cpucore[$nextcore]=$addcpu;
                            else if ($addcpu>=0) $cpucore[$nextcore]=$mhz;
                            $nextcore++;
                        }
                    }
                    $i2++;
                }
                $i=1;
                unset($freespace);
                unset($percentusedcpu);
                unset($percentusedram);
                unset($percentserver);
                unset($percentusedhdd);
                while ($i<=$cores) {
                    if($cpucore[$i]==0) {
                        $percentusedcpu[$i]=0;
                    } else {
                        $percentusedcpu[$i]=$cpucore[$i]/($mhz/100);
                    }
                    $i++;
                }
                $percentusedram=$ramused/($ram/100);
                $percentserver=$i2/($maxserver/100);
                unset($freespace);
                foreach ($hdd as $hdd_row) {
                    $percentusedhdd[$hdd_row]=($mountunused[$hdd_row]==0) ? 0 : $mountunused[$hdd_row]/($mountsize[$hdd_row]/100);
                    $freespace[$hdd_row]=$mountsize[$hdd_row]-($mountunused[$hdd_row]*($percent/100));
                }
                natsort($freespace);
                $freespace=array_reverse($freespace);
                $serverused[$id]=array('ram'=>$ramused,'cpu'=>$cpucore,'server'=>$i2,'hdd'=>$mountunused,'freespace'=>$freespace);
                if ($resellerid==$reseller_id) {
                    $serverusage[$id]=array('ram'=>$percentusedram,'cpu'=>$percentusedcpu,'server'=>$percentserver,'hdd'=>$percentusedhdd,'freespace'=>$freespace);
                    $table[]=array('id'=>$id,'ip'=>$row['ip']);
                } else {
                    $serverusage2[$id]=array('ram'=>$percentusedram,'cpu'=>$percentusedcpu,'server'=>$percentserver,'hdd'=>$percentusedhdd,'freespace'=>$freespace);
                }
            }
            if (isset($serverusage) and is_array($serverusage)) {
                asort($serverusage);
                $bestserver=key($serverusage);
                $query=$sql->prepare("SELECT `cores`,`cpu`,`esxi`,`mhz`,`hdd`,`ram`,`maxserver` FROM `virtualhosts` WHERE `id`=:bestserver LIMIT 1");
                $query->execute(array(':bestserver'=>$bestserver));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $mhz=$row['mhz'];
                    $ram=$row['ram'];
                    $besthostcpu=$row['cpu']." ".$row['cores']."x".$row['mhz']." MHz";
                    $maxserver=$row['maxserver'];
                    $esxi=$row['esxi'];
                    $core='';
                    $hdd='';
                    $i=1;
                    while ($i<=$row['cores']) {
                        $core[]=$i;
                        $i++;
                    }
                    $i=1;
                    if ($esxi=='Y') {
                        $maxcore=8;
                    } else {
                        $maxcore=$row['cores'];
                    }
                    while ($i<=$cores and $i<=$maxcore) {
                        $add_core[]=$i;
                        $i++;
                    }
                    $hdd_rows=explode("\r\n", $row['hdd']);
                    foreach ($hdd_rows as $hddline) {
                        $data_explode=explode(" ", $hddline);
                        if (isset($data_explode[1])) {
                            $mountpoint=$data_explode[0];
                            $mountsize[$mountpoint]=$data_explode[1];
                        }
                    }
                    foreach ($serverused[$bestserver]['freespace'] as $mountpoint => $free) {
                        $hdd[]=$mountpoint;
                        if (!isset($firstfreespace)) {
                            $firstfreespace=$free;
                        }
                    }
                    $firstpoint=$hdd[0];
                }
            }
            if ($reseller_id!=0 and (!isset($bestserver) or !isid($bestserver,10))) {
                asort($serverusage2);
                $bestserver=key($serverusage2);
                $query=$sql->prepare("SELECT `esxi`,`cpu`,`ip`,`cores`,`mhz`,`hdd`,`ram`,`maxserver` FROM `virtualhosts` WHERE `id`=? LIMIT 1");
                $query->execute(array($bestserver));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $esxi=$row['esxi'];
                    $notexclusive=1;
                    $mhz=$row['mhz'];
                    $ram=$row['ram'];
                    $besthostcpu=$row['cpu']." ".$row['cores']."x".$row['mhz']." MHz";
                    $maxserver=$row['maxserver'];
                    $core='';
                    $hdd='';
                    $i=1;
                    while ($i<=$row['cores']) {
                        $core[]=$i;
                        $i++;
                    }
                    $i=1;
                    if ($esxi=='Y') {
                        $maxcore="8";
                    } else {
                        $maxcore=$row['cores'];
                    }
                    while ($i<=$cores and $i<=$maxcore) {
                        $add_core[]=$i;
                        $i++;
                    }
                    $hdd_rows=explode("\r\n", $row['hdd']);
                    foreach ($hdd_rows as $hddline) {
                        $data_explode=explode(" ", $hddline);
                        if (isset($data_explode[1])) {
                            $mountpoint=$data_explode[0];
                            $mountsize[$mountpoint]=$data_explode[1];
                        }
                    }
                    foreach ($serverused[$bestserver]['freespace'] as $mountpoint => $free) {
                        $hdd[]=$mountpoint;
                        if (!isset($firstfreespace)) {
                            $firstfreespace=$free;
                        }
                    }
                    $firstpoint=$hdd[0];
                    $table[]=array('id'=>$bestserver,'ip'=>$row['ip']);
                }
            }
        } else {
            $mhz='';
            $ram='';
            $maxserver='';
            $core=array();
            $hdd=array();
            $serverused[1]=array('ram'=>"",'cpu'=>"",'server'=>"",'hdd'=>"");
            $bestserver=1;
        }
        $reseller=array();
        if ($reseller_id!=0) {
            $query=$sql->prepare("SELECT `maxvserver`, `maxuserram`, `maxusermhz` FROM `resellerdata` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $maxvserver=$row['maxvserver'];
                $maxuserram=$row['maxuserram'];
                $maxusermhz=$row['maxusermhz'];
            }
            $query=$sql->prepare("SELECT COUNT( DISTINCT `id` ) AS `usedservers`, SUM( `minram` ) AS `usedram`, SUM( `cores` * `minmhz` ) AS `usedcpu` FROM `virtualcontainer` WHERE `resellerid`=? LIMIT 1 ");
            $query->execute(array($reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $usedservers=$row['usedservers'];
                if ($row['usedram']!=null){
                    $useduserram=$row['usedram'];
                } else {
                    $useduserram=0;
                }
                if ($row['usedcpu']!=null){
                    $usedusercpu=$row['usedcpu'];
                } else {
                    $usedusercpu=0;
                }
            }
        } else {
        }
        $checkedips=array();
        $query=($reseller_id==0) ? $sql->prepare("SELECT `id`,`cname`,`vname`,`name`,`accounttype` FROM `userdata` WHERE (`id`=`resellerid` OR `resellerid`=?) AND `accounttype` IN ('r','u') ORDER BY `id` DESC") : $sql->prepare("SELECT `id`,`cname`,`vname`,`name`,`accounttype` FROM `userdata` WHERE `resellerid`=? AND `accounttype` IN ('r','u') ORDER BY `id` DESC");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!isset($firstresellerip)) {
                if ($row['accounttype']=='u') $checkedips=freeips($reseller_id);
                else $checkedips=freeips($row['id']);
                $firstresellerip=current($checkedips);
            }
            $type=($row['accounttype']=='u') ? $gsprache->user : $gsprache->reseller;
            $reseller[$row['id']]=$type.' '.trim($row['cname'].' '.$row['vname'].' '.$row['name']);
        }
        if (!isset($firstresellerip) or !isip($firstresellerip,'all')) {
            $checkedips=array();
            $firstresellerip=current(freeips($reseller_id));
        }
        $templates=array();
        $query=$sql->prepare("SELECT `id`,`description`,`bitversion` FROM `resellerimages` ORDER BY `distro`,`bitversion`,`description`");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $templates[]=array('id'=>$row['id'],'description'=>$row['description']." ".$row['bitversion']." Bit");
        }
        $template_file="admin_root_vserver_add.tpl";
    } else if ($ui->smallletters('action',2,'post')=='ad'){
        $template_file="Error: ";
        $fail=0;
        if (!isid($ui->post['hostid'],10)) {
            $fail=1;
        } else {
            $hostid=$ui->post['hostid'];
        }
        if (isid($ui->post['userid'],10)) {
            $userid=$ui->post['userid'];
            $query=$sql->prepare("SELECT `resellerid` FROM `userdata` WHERE `id`=? LIMIT 1");
            $query->execute(array($userid));
            $resellerid=$query->fetchColumn();
        } else {
            $fail=1;
        }
        if (!startparameter($ui->post['mount'])) {
            $fail=1;
        } else {
            $mountpoint=$ui->post['mount'];
        }
        if (!isid($ui->post['cores'],1) or (!isid($ui->post['minmhz'],"5") and $ui->post['minmhz']!=0)) {
            $fail=1;
            $template_file .="<br/ >MinMHZ";
        } else {
            $cores=$ui->post['cores'];
            $minmhz=$ui->post['minmhz'];
            $cpu_usage=$cores*$minmhz;
        }
        if (!isid($ui->post['maxmhz'],"5")) {
            $fail=1;
            $template_file .="<br/ >MAXMHZ";
        } else {
            $maxmhz=$ui->post['maxmhz'];
        }
        if (!isid($ui->post['hddsize'],4)) {
            $fail=1;
            $template_file .="<br/ >HDDsize";
        } else {
            $hddsize=$ui->post['hddsize'];
        }
        if (!isinteger($ui->post['ram'])) {
            $fail=1;
            $template_file .="<br/ >Ram";
        } else {
            $ram=$value=str_replace(',', '.', $ui->post['ram']);
        }
        if (!isinteger($ui->post['minram'])) {
            $fail=1;
            $template_file .="<br/ >MinRam";
        } else {
            $minram=$ui->post['minram'];
        }
        if (!isinteger($ui->post['maxram'])) {
            $fail=1;
            $template_file .="<br/ >MaxRam";
        } else {
            $maxram=$ui->post['maxram'];
        }
        if (isips($ui->post['ips']) or empty($ui->post['ips'])) {
            $freeips=($reseller_id==0) ? freeips($reseller_id) : freeips($userid);
            if (isips($ui->post['ips'])) {
                $posted_ip=ipstoarray($ui->post['ips']);
                foreach ($posted_ip as $ip_row) {
                    if (in_array($ip_row, $freeips)) {
                        if (!isset($ip)) {
                            $ip=$ip_row;
                        } else {
                            if (!isset($ips)) $ips=$ip_row;
                            else $ips .="\r\n".$ip_row;
                        }
                    }
                }
            }
            if (!isset($ip)) {
                if (isip(current($freeips), 'all')) {
                    $ip=current($freeips);
                } else {
                    $freeips=freeips('0',$sql);
                    $ip=current($freeips);
                    $query=$sql->prepare("SELECT `ips` FROM `resellerdata` WHERE `resellerid`=? LIMIT 1");
                    $query2=$sql->prepare("UPDATE `resellerdata` SET `ips`=? WHERE `resellerid`=?");
                    $query->execute(array($userid));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $query2->execute(array($row['ips']."\r\n".$ip,$userid));
                    }
                }
            }
            if (!isset($ips)) {
                $ips='';
            }
        } else {
            $fail=1;
            $template_file .="IPs";
        }
        if ($reseller_id!=0) {
            $query=$sql->prepare("SELECT `maxvserver`, `maxuserram`, `maxusermhz` FROM `resellerdata` WHERE `resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':reseller_id'=>$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $maxvserver=$row['maxvserver'];
                $maxuserram=$row['maxuserram'];
                $maxusermhz=$row['maxusermhz'];
            }
            $query=$sql->prepare("SELECT COUNT( DISTINCT `id` ) AS `usedservers`, SUM( `minram` ) AS `usedram`, SUM( `cores` * `minmhz` ) AS `usedcpu` FROM `virtualcontainer` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $usedservers=$row['usedservers'];
                if ($row['usedram']!=null){
                    $useduserram=$row['usedram']+$ram;
                } else {
                    $useduserram=0+$ram;
                }
                if ($row['usedcpu']!=null){
                    $usedusercpu=$row['usedcpu']+$cpu_usage;
                } else {
                    $usedusercpu=0+$cpu_usage;
                }
            }
            if ($usedservers>=$maxvserver or $useduserram>=$maxuserram or $usedusercpu>=$maxusermhz) {
                $fail=1;
                $template_file .="Reseller Limits";
            }
        }
        $query=$sql->prepare("SELECT h.`id`, h.`cores`, h.`mhz`, h.`hdd`, h.`ram`, h.`maxserver`, h.`maxserver`-COUNT(DISTINCT v.`id`) AS `freeserver`, h.`ram`-SUM(v.`minram`) AS `freeram`, h.`cores`*h.`mhz`-SUM(v.`cores`*v.`minmhz`) AS `freecpu`, h.`active` AS `active`,h.`thin`,h.`thinquota`, h.`resellerid` AS `resellerid` FROM `virtualhosts` h LEFT JOIN `virtualcontainer` v ON v.`hostid`=h.`id` GROUP BY h.`id` HAVING ((`freeserver` > 0 OR `freeserver` IS NULL) AND (`freecpu` > 0 OR `freecpu` IS NULL) AND (`freeram` > 0 OR `freeram` IS NULL) AND `active`='Y' AND (`resellerid`=? OR `resellerid`='0') AND h.`id`=?)");
        $query2=$sql->prepare("SELECT `hddsize` FROM `virtualcontainer` WHERE `hostid`=? AND `mountpoint`=?");
        $query->execute(array($reseller_id,$hostid));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $freeram=($row['freeram']==false) ? $row['ram'] : $row['freeram'];
            $freecpu=($row['freecpu']==false) ? $row['cores']*$row['mhz'] : $row['freecpu'];
            if ($freeram<$ram) {
                $fail=1;
                $template_file .="<br/ >Hostlimits: Ram";
            }
            if ($freecpu<$cpu_usage) {
                $fail=1;
                $template_file .="<br/ >Hostlimits: CPU";
            }
            if (0>=$row['freeserver']) {
                $fail=1;
                $template_file .="<br/ >Hostlimits: Max Servers";
            }
            if ($row['thin']=='Y') {
                $percent=$row['thinquota'];
            } else {
                $percent="100";
            }
            $mountspace=0;
            $hdd_rows=explode("\r\n", $row['hdd']);
            foreach ($hdd_rows as $hddline) {
                $data_explode=explode(" ", $hddline);
                if (isset($data_explode[1]) and $data_explode[0]==$mountpoint) {
                    $mountspace=$data_explode[1];
                }
            }
            $query2->execute(array($hostid,$mountpoint));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $substracthdd=$mountspace-($row2['hddsize']*($percent/100));
                $mountspace=$substracthdd;
            }
            if ($hddsize>$mountspace) {
                $fail=1;
                $template_file .="<br/ >Hostlimits: HDD";
            }
        }
        if ($query->rowCount()<1) {
            $fail=1;
            $template_file .="<br/ >Hostlimits reached";
        }
        if ($fail!=1) {
            $pass=passwordgenerate(10);
            $last_mac=$sql->prepare("SELECT mac FROM `virtualcontainer` ORDER BY id DESC LIMIT 1");
            $last_mac->execute();
            foreach ($last_mac->fetchAll(PDO::FETCH_ASSOC) as $last_mac_row) {
                $mac=$last_mac_row['mac'];
            }
            if (!isset($mac) or $mac=="00:50:56:3f:ff:ff") {
                $mac="00:50:56:00:00:00";
            }
            $row=1;
            while ($row==1) {
                $ex_mac=explode(":", $mac);
                $ex_mac1=hexdec($ex_mac[3]);
                $ex_mac2=hexdec($ex_mac[4]);
                $ex_mac3=hexdec($ex_mac[5]);
                if (hexdec($ex_mac[5])=="255") {
                    $ex_mac3="00";
                    if (hexdec($ex_mac[4])=="255") {
                        $ex_mac2="00";
                        $ex_mac1=hexdec($ex_mac[3])+1;
                        if ($ex_mac1<="15") {
                            $ex_mac1="0".strtolower(dechex($ex_mac1));
                        } else {
                            $ex_mac1=strtolower(dechex($ex_mac1));
                        }
                    } else {
                        $ex_mac2=hexdec($ex_mac[4])+1;
                        if ($ex_mac2<="15") {
                            $ex_mac2="0".strtolower(dechex($ex_mac2));
                        } else {
                            $ex_mac2=strtolower(dechex($ex_mac2));
                        }
                        $ex_mac1=$ex_mac[3];
                    }
                } else {
                    $ex_mac3=hexdec($ex_mac[5])+1;
                    if ($ex_mac3<="15") {
                        $ex_mac3="0".strtolower(dechex($ex_mac3));
                    } else {
                        $ex_mac3=strtolower(dechex($ex_mac3));
                    }
                    $ex_mac1=$ex_mac[3];
                    $ex_mac2=$ex_mac[4];
                }
                $mac="00:50:56:$ex_mac1:$ex_mac2:$ex_mac3";
                $query=$sql->prepare("SELECT `id` FROM `virtualcontainer` WHERE `mac`=? LIMIT 1");
                $query->execute(array($mac));
                $row=$query->rowCount();
            }
            $query=$sql->prepare("INSERT INTO `virtualcontainer` (`userid`,`hostid`,`ip`,`ips`,`mac`,`port`,`pass`,`cores`,`minmhz`,`maxmhz`,`hddsize`,`mountpoint`,`ram`,`minram`,`maxram`,`status`,`resellerid`) VALUES (:userid, :hostid, :ip, :ips, :mac, :port, AES_ENCRYPT(:pass, :aeskey), :cores, :minmhz, :maxmhz, :hddsize, :mountpoint, :ram, :minram, :maxram, :status, :resellerid)");
            $query->execute(array(':userid'=>$userid,':hostid'=>$hostid,':ip'=>$ip,':ips'=>$ips,':mac'=>$mac,':port' => '21',':pass'=>$pass,':aeskey'=>$aeskey,':cores'=>$cores,':minmhz'=>$minmhz,':maxmhz'=>$maxmhz,':hddsize'=>$hddsize,':mountpoint'=>$mountpoint,':ram'=>$ram,':minram'=>$minram,':maxram'=>$maxram,':status' => 0,':resellerid'=>$resellerid));
            if ($query->rowCount()>0) {
                $loguseraction="%add% %vserver% $ip Ram: $ram; MinRam: $minram; MaxRam: $maxram; Cores: $cores; MinMhz: $minmhz; MaxMhz: $maxmhz; HDD: $hddsize";
                $insertlog->execute();
                $template_file=$spracheResponse->table_add;
            } else {
                $template_file=$spracheResponse->error_table;
            }
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else if ($ui->st('d','get')=='dl' and $ui->id('id',10,'get') and $pa['delvserver']) {
    $id=$ui->id('id',10,'get');
    if ($reseller_id==0) {
        $query=$sql->prepare("SELECT c.`ip`,c.`hostid`,c.`userid`,r.`description`,r.`bitversion` FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON c.`imageid`=r.`id` WHERE c.`id`=? LIMIT 1");
        $query->execute(array($id));
    } else {
        $query=$sql->prepare("SELECT c.`ip`,c.`hostid`,c.`userid`,r.`description`,r.`bitversion` FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON c.`imageid`=r.id WHERE c.`id`=? AND c.`resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
    }
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $ip=$row['ip'];
        $hostID=$row['hostid'];
        $userID=$row['userid'];
        $description=$row['description'];
        $bitversion=$row['bitversion'];
    }
    if (!$ui->smallletters('action',2,'post')) {
        $template_file="admin_root_vserver_dl.tpl";
    } else if ($ui->smallletters('action',2,'post')=='dl'){
        if (isset($ip)) {
            $query=$sql->prepare("UPDATE `jobs` SET `status`=2 WHERE `affectedID`=? AND `type`='vs' AND (`status`IS NULL OR `status`=1)");
            $query->execute(array($id));
            $query=$sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('D','vs',?,?,?,?,?,NULL,NOW(),'dl',?)");
            $query->execute(array($hostID,$admin_id,$id,$userID,$ip,$reseller_id));
            if ($query->rowCount()>0) {
                $query=$sql->prepare("UPDATE `virtualcontainer` SET `jobPending`='Y' WHERE `id`=? AND `resellerid`=?");
                $query->execute(array($id,$reseller_id));
                $loguseraction="%del% %vserver% $ip";
                $insertlog->execute();
                $template_file=$spracheResponse->table_add;
            } else {
                $template_file=$spracheResponse->error_table;
            }
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else if ($ui->st('d','get')=='md' and $ui->id('id',10,'get') and $pa['modvserver']) {
    $id=$ui->id('id',10,'get');
    if (!$ui->smallletters('action',2,'post')) {
        if ($reseller_id==0) {
            $query=$sql->prepare("SELECT c.`active`,c.`ip`,c.`ips`,c.`mac`,c.`cores`,c.`minmhz`,c.`maxmhz`,c.`hddsize`,c.`mountpoint`,c.`ram`,c.`minram`,c.`maxram`,AES_DECRYPT(c.`pass`, :aeskey) AS `decryptedpass`,r.`description`,r.`bitversion`,u.`cname`,h.`cores` AS `hcore`,h.`esxi`,u.`id` AS `userid` FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON c.`imageid`=r.`id` LEFT JOIN `userdata` u ON c.`userid`=u.`id` LEFT JOIN `virtualhosts` h ON c.`hostid`=h.`id` WHERE c.`id`=:id LIMIT 1");
            $query->execute(array(':id'=>$id,':aeskey'=>$aeskey));
        } else if ($reseller_id==$admin_id) {
            $query=$sql->prepare("SELECT c.`active`,c.`ip`,c.`ips`,c.`mac`,c.`cores`,c.`minmhz`,c.`maxmhz`,c.`hddsize`,c.`mountpoint`,c.`ram`,c.`minram`,c.`maxram`,AES_DECRYPT(c.`pass`, :aeskey) AS `decryptedpass`,r.`description`,r.`bitversion`,u.`cname`,h.`cores` AS `hcore`,h.`esxi`,u.`id` AS `userid` FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON c.`imageid`=r.`id` LEFT JOIN `userdata` u ON c.`userid`=u.`id` LEFT JOIN `virtualhosts` h ON c.`hostid`=h.`id` WHERE c.`id`=:id AND c.`resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':id'=>$id,':aeskey'=>$aeskey,':reseller_id'=>$reseller_id));
        } else {
            $query=$sql->prepare("SELECT c.`active`,c.`ip`,c.`ips`,c.`mac`,c.`cores`,c.`minmhz`,c.`maxmhz`,c.`hddsize`,c.`mountpoint`,c.`ram`,c.`minram`,c.`maxram`,AES_DECRYPT(c.`pass`, :aeskey) AS `decryptedpass`,r.`description`,r.`bitversion`,u.`cname`,h.`cores` AS `hcore`,h.`esxi`,u.`id` AS `userid` FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON c.`imageid`=r.`id` LEFT JOIN `userdata` u ON c.`userid`=u.`id` LEFT JOIN `virtualhosts` h ON c.`hostid`=h.`id` WHERE c.`id`=:id AND c.`userid`=:admin_id AND c.`resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':id'=>$id,':aeskey'=>$aeskey,':admin_id'=>$admin_id,':reseller_id'=>$reseller_id));
        }
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $description=$row['description'];
            $bitversion=$row['bitversion'];
            $ip=$row['ip'];
            $ips=$row['ips'];
            $mac=$row['mac'];
            $cores=$row['cores'];
            $minmhz=$row['minmhz'];
            $maxmhz=$row['maxmhz'];
            $hddsize=$row['hddsize'];
            $mountpoint=$row['mountpoint'];
            $ram=$row['ram'];
            $minram=$row['minram'];
            $maxram=$row['maxram'];
            $cname=$row['cname'];
            $userid=$row['userid'];
            $pass=$row['decryptedpass'];
            $esxi=$row['esxi'];
            $i=1;
            $cpucores='';
            if ($esxi=='Y') {
                $maxcore="8";
            } else {
                $maxcore=$row['hcore'];
            }
            while ($i<=$maxcore) {
                $cpucores[]=$i;
                $i++;
            }
        }
        if (isset($userid)) {
            $checkedips=($reseller_id==0) ? freeips($reseller_id) : freeips($userid);
            $template_file="admin_root_vserver_md.tpl";
        } else {
            $template_file="admin_404.tpl";
        }
    } else if ($ui->smallletters('action',2,'post')=='md'){
        $template_file="Error: ";
        $fail=0;
        if (!isid($ui->post['cores'],1)) {
            $fail=1;
        } else {
            $cores=$ui->post['cores'];
        }
        if (!isid($ui->post['minmhz'],"5") and $ui->post['minmhz']!=0) {
            $fail=1;
            $template_file .="MinMHZ";
        } else {
            $minmhz=$ui->post['minmhz'];
        }
        if (!isid($ui->post['maxmhz'],"5")) {
            $fail=1;
            $template_file .="MAXMHZ";
        }
        if (!isid($ui->post['hddsize'],4)) {
            $fail=1;
            $template_file .="HDDsize";
        }
        if (!isinteger($ui->post['ram'])) {
            $fail=1;
            $template_file .="Ram";
        }
        if (!isinteger($ui->post['minram'])) {
            $fail=1;
            $template_file .="MinRam";
        } else {
            $minram=$ui->post['minram'];
        }
        if (!isinteger($ui->post['maxram'])) {
            $fail=1;
            $template_file .="MaxRam";
        }
        if (!ismac($ui->post['mac'])) {
            $fail=1;
            $template_file .="MAC";
        }
        if (!active_check($ui->post['active'])) {
            $fail=1;
            $template_file .="Active";
        }
        if ($reseller_id==0) {
            $query=$sql->prepare("SELECT * FROM `virtualcontainer` WHERE `id`=? LIMIT 1");
            $query->execute(array($id));
        } else {
            $query=$sql->prepare("SELECT * FROM `virtualcontainer` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id,$reseller_id));
        }
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $oldactive=$row['active'];
            $hostid=$row['hostid'];
            $oldip=$row['ip'];
            $oldmac=$row['mac'];
            $oldcores=$row['cores'];
            $oldminmhz=$row['minmhz'];
            $oldmaxmhz=$row['maxmhz'];
            $oldhddsize=$row['hddsize'];
            $oldram=$row['ram'];
            $oldminram=$row['minram'];
            $oldmaxram=$row['maxram'];
            $userid=$row['maxram'];
        }
        if ($reseller_id!=0) {
            $query=$sql->prepare("SELECT r.`maxvserver`,r.`maxuserram`,r.`maxusermhz`, COUNT( DISTINCT v.`id`) AS `usedservers`, SUM(v.`minram`) AS `usedram`, SUM(v.`cores` * v.`minmhz`) AS `usedcpu` FROM `resellerdata` r LEFT JOIN `virtualcontainer` v ON v.`userid`=r.`resellerid` WHERE r.`resellerid`=? GROUP BY v.`id` LIMIT 1");
            $query->execute(array($reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $maxvserver=$row['maxvserver'];
                $maxuserram=$row['maxuserram'];
                $maxusermhz=$row['maxusermhz'];
                $usedservers=$row['usedservers'];
                if ($usedservers>'0'){
                    $useduserram=$row['usedram']-$oldminram+$minram;
                    $usedusercpu=$row['usedcpu']-($oldminmhz*$oldcores)+($minmhz*$cores);
                } else {
                    $useduserram=0-$oldminram+$minram;
                    $usedusercpu=0-($oldminmhz*$oldcores)+($minmhz*$cores);
                }
            }
            if ($useduserram>=$maxuserram or $usedusercpu>=$maxusermhz) {
                $fail=1;
                $template_file .="Reseller Limits";
            }
        }
        if (isip($ui->post['ip'],'all') and isset($oldip)) {
            $freeips=($reseller_id==0) ? freeips($reseller_id) : freeips($userid);
            $checked_ips=array();
            if (isips($ui->post['ips'])) {
                $postedips=ipstoarray($ui->post['ips']);
                if (is_array($postedips)) {
                    foreach ($postedips as $postedip) {
                        if (in_array($postedip, $freeips)) {
                            $checked_ips[]=$postedip;
                        }
                    }
                }
                foreach ($checked_ips as $checked_ip) {
                    if (isset($ips)) {
                        $ips .="\r\n".$checked_ip;
                    } else {
                        $ips=$checked_ip;
                    }
                }
            }
            if ($ui->post['ip']==$oldip or in_array($ui->post['ip'],$freeips)) {
                $ip=$ui->post['ip'];
            } else if(isset($checked_ips[0]) and isip($checked_ips[0],'all')) {
                $ip=$checked_ips[0];
            }
            if (!isset($ips)) {
                $ips='';
            }
            if (!isset($ip)) {
                $fail=1;
                $template_file .="IP";
            }
        } else {
            $fail=1;
            $template_file .="No/Bad IP";
        }
        if ($fail!=1 and isset($oldmac)) {
            $cores=$ui->post['cores'];
            $minmhz=$ui->post['minmhz'];
            $maxmhz=$ui->post['maxmhz'];
            $hddsize=$ui->post['hddsize'];
            $ram=str_replace(',','.',$ui->post['ram']);
            $minram=$ui->post['minram'];
            $maxram=$ui->post['maxram'];
            $active=$ui->post['active'];
            $mac=$ui->post['mac'];
            if ($reseller_id==0) {
                $query=$sql->prepare("UPDATE `virtualcontainer` SET `active`=:active, `ip`=:ip, `ips`=:ips,`mac`=:mac,`cores`=:cores, minmhz=:minmhz, maxmhz=:maxmhz, hddsize=:hddsize, ram=:ram, minram=:minram, maxram=:maxram WHERE `id`=:id LIMIT 1");
                $query->execute(array(':active'=>$active,':ip'=>$ip,':ips'=>$ips,':mac'=>$mac,':cores'=>$cores,':minmhz'=>$minmhz,':maxmhz'=>$maxmhz,':hddsize'=>$hddsize,':ram'=>$ram,':minram'=>$minram,':maxram'=>$maxram,':id'=>$id));
            } else if ($reseller_id==$admin_id) {
                $query=$sql->prepare("UPDATE `virtualcontainer` SET `active`=:active, `ip`=:ip, `ips`=:ips,`mac`=:mac,`cores`=:cores, minmhz=:minmhz, maxmhz=:maxmhz, hddsize=:hddsize, ram=:ram, minram=:minram, maxram=:maxram WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':active'=>$active,':ip'=>$ip,':ips'=>$ips,':mac'=>$mac,':cores'=>$cores,':minmhz'=>$minmhz,':maxmhz'=>$maxmhz,':hddsize'=>$hddsize,':ram'=>$ram,':minram'=>$minram,':maxram'=>$maxram,':id'=>$id,':reseller_id'=>$reseller_id));
            } else {
                $query=$sql->prepare("UPDATE `virtualcontainer` SET `active`=:active, `ip`=:ip, `ips`=:ips,`mac`=:mac,`cores`=:cores, minmhz=:minmhz, maxmhz=:maxmhz, hddsize=:hddsize, ram=:ram, minram=:minram, maxram=:maxram WHERE `id`=:id AND `userid`=:userid AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':active'=>$active,':ip'=>$ip,':ips'=>$ips,':mac'=>$mac,':cores'=>$cores,':minmhz'=>$minmhz,':maxmhz'=>$maxmhz,':hddsize'=>$hddsize,':ram'=>$ram,':minram'=>$minram,':maxram'=>$maxram,':id'=>$id,':userid'=>$userid,':reseller_id'=>$reseller_id));
            }
            if ($oldmac!=$mac or $oldcores!=$cores or $oldminmhz!=$minmhz or $oldmaxmhz!=$maxmhz or $oldhddsize!=$hddsize or $oldram!=$ram or $oldminram!=$minram or $oldmaxram!=$maxram or $oldactive!=$active) {
                $query=$sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('D','vs',NULL,?,?,?,?,NULL,NOW(),'md',?,?)");
                $query->execute(array($admin_id,$id,$userid,$ip,json_encode(array('oldactive'=>$row['active'],'oldip'=>$row['ip'],'oldmac'=>$row['mac'])),$reseller_id));
            }
            if ($query->rowCount()>0) {
                $loguseraction="%mod% %vserver% $ip Ram: $ram; MinRam: $minram; MaxRam: $maxram; Cores: $cores; MinMhz: $minmhz; MaxMhz: $maxmhz; HDD: $hddsize";
                $insertlog->execute();
                $template_file=$spracheResponse->table_add;
            } else {
                $template_file=$spracheResponse->error_table;
            }
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else if ($ui->st('d','get')=='va' and $ui->id('id',10,'get') and $pa['usevserver']) {
    $id=$ui->id('id',10,'get');
    if (!$ui->smallletters('action',2,'post')) {
        $option=array();
        if ($reseller_id==0) {
            $query=$sql->prepare("SELECT c.ip,c.status,AES_DECRYPT(c.pass, :aeskey) AS decryptedpass,r.description,r.bitversion FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON c.imageid=r.id WHERE c.id=:id LIMIT 1");
            $query->execute(array(':id'=>$id,':aeskey'=>$aeskey));
        } else if ($reseller_id==$admin_id) {
            $query=$sql->prepare("SELECT c.ip,c.status,AES_DECRYPT(c.pass, :aeskey) AS decryptedpass,r.description,r.bitversion FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON c.imageid=r.id WHERE c.id=:id AND c.resellerid=:reseller_id LIMIT 1");
            $query->execute(array(':id'=>$id,':aeskey'=>$aeskey,':reseller_id'=>$reseller_id));
        } else {
            $query=$sql->prepare("SELECT c.ip,c.status,AES_DECRYPT(c.pass, :aeskey) AS decryptedpass,r.description,r.bitversion FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON c.imageid=r.id WHERE c.id=:id AND c.userid=:userid AND c.resellerid=:reseller_id LIMIT 1");
            $query->execute(array(':id'=>$id,':aeskey'=>$aeskey,':userid'=>$admin_id,':reseller_id'=>$reseller_id));
        }
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip=$row['ip'];
            if ($row['status']==null or $row['status']==2) {
                $option[]='<option value="rc">'.$sprache->rescue_start.'</option>';
                $option[]='<option value="ri">'.$sprache->reinstall.'</option>';
            } else if ($row['status']==0) {
                $option[]='<option value="rs">'.$sprache->restart.'</option>';
                $option[]='<option value="st">'.$sprache->stop.'</option>';
                $option[]='<option value="rc">'.$sprache->rescue_start.'</option>';
                $option[]='<option value="ri">'.$sprache->reinstall.'</option>';
            } else if ($row['status']==1) {
                $option[]='<option value="rs">'.$sprache->restart.'</option>';
                $option[]='<option value="rc">'.$sprache->rescue_start.'</option>';
                $option[]='<option value="ri">'.$sprache->reinstall.'</option>';
            } else if ($row['status']==3) {
                $option[]='<option value="rt">'.$sprache->rescue_stop.'</option>';
                $option[]='<option value="ri">'.$sprache->reinstall.'</option>';
            }
            $description=$row['description'];
            $bitversion=$row['bitversion'];
            $pass=$row['decryptedpass'];
        }
        $templates=array();
        $query=$sql->prepare("SELECT `id`,`description`,`bitversion` FROM `resellerimages` ORDER BY `distro`,`bitversion`,`description`");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if ($row['description']!='Rescue 32bit' and $row['description']!='Rescue 64bit') $templates[]=array('id'=>$row['id'],'description'=>$row['description']);
        }
        $template_file="admin_root_vserver_re.tpl";
    } else if (in_array($ui->st('action','post'),array('ri','rc','rs','st'))) {
        $query=$sql->prepare("SELECT v.`ip`,v.`userid`,v.`hostid`,i.`bitversion` FROM `virtualcontainer` v LEFT JOIN `resellerimages` i ON v.`imageid`=i.`id` WHERE v.`id`=? LIMIT 1");
        $query->execute(array($id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip=$row['ip'];
            $userID=$row['userid'];
            $bitversion=$row['bitversion'];
            $hostid=$row['hostid'];
        }
        if (!isset($bitversion)) $bitversion=64;
        if (isset($ip)) {
            $extraData=array();
            if ($ui->st('action','post')=='ri') {
                $extraData['imageID']=$ui->id('imageid',10,'post');
            } else if ($ui->st('action','post')=='rc') {
                $query=$sql->prepare("SELECT `id` FROM `resellerimages` WHERE `bitversion`=? AND `active`='Y' AND `distro`='other' AND `description` LIKE 'Rescue %' LIMIT 1");
                $query->execute(array($bitversion));
                $extraData['imageID']=$query->fetchColumn();
            }
            $query=$sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('D','vs',?,?,?,?,?,NULL,NOW(),?,?,?)");
            $query->execute(array($hostid,$admin_id,$id,$userID,$ip,$ui->st('action','post'),json_encode($extraData),$reseller_id));
            $query=$sql->prepare("UPDATE `virtualcontainer` SET `jobPending`='Y' WHERE `id`=? AND `resellerid`=?");
            $query->execute(array($id,$reseller_id));
            $template_file=$spracheResponse->table_add;
        } else {
            $template_file='admin_404.tpl';
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else {
    $o=$ui->st('o','get');
    if ($ui->st('o','get')=='da') {
        $orderby='u.`cname` DESC';
    } else if ($ui->st('o','get')=='aa') {
        $orderby='u.`cname` ASC';
    } else if ($ui->st('o','get')=='ds') {
        $orderby='c.`status` DESC';
    } else if ($ui->st('o','get')=='as') {
        $orderby='c.`status` ASC';
    } else if ($ui->st('o','get')=='dp') {
        $orderby='c.`ip` DESC';
    } else if ($ui->st('o','get')=='ap') {
        $orderby='c.`ip` ASC';
    } else if ($ui->st('o','get')=='dh') {
        $orderby='h.`id` DESC';
    } else if ($ui->st('o','get')=='ah') {
        $orderby='h.`id` ASC';
    } else if ($ui->st('o','get')=='de') {
        $orderby='u.`cname` DESC, c.`id` DESC';
    } else if ($ui->st('o','get')=='ae') {
        $orderby='u.`cname` ASC, c.`id` ASC';
    } else if ($ui->st('o','get')=='di') {
        $orderby='c.`id` DESC';
    } else {
        $orderby='c.`id` ASC';
        $o='ai';
    }
    $table = array();
    if ($reseller_id==0) {
        $query=$sql->prepare("SELECT c.*,r.`description` AS `idescription`,r.`bitversion`,h.`ip` AS `hip`,h.`id` AS `hid`,h.`description` AS `hdescription`,u.`cname` FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON  c.`imageid`=r.`id` LEFT JOIN `virtualhosts` h ON c.`hostid`=h.`id` LEFT JOIN `userdata` u ON c.`userid`=u.`id` ORDER BY $orderby LIMIT $start,$amount");
        $query->execute();
    } else if ($reseller_id==$admin_id) {
        $query=$sql->prepare("SELECT c.*,r.`description` AS `idescription`,r.`bitversion`,h.`ip` AS `hip`,h.`id` AS `hid`,h.`description` AS `hdescription`,u.`cname` FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON  c.`imageid`=r.`id` LEFT JOIN `virtualhosts` h ON c.`hostid`=h.`id` LEFT JOIN `userdata` u ON c.`userid`=u.`id` WHERE c.`resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
        $query->execute(array($reseller_id));
    } else {
        $query=$sql->prepare("SELECT c.*,r.`description` AS `idescription`,r.`bitversion`,h.`ip` AS `hip`,h.`id` AS `hid`,h.`description` AS `hdescription`,u.`cname` FROM `virtualcontainer` c LEFT JOIN `resellerimages` r ON  c.`imageid`=r.`id` LEFT JOIN `virtualhosts` h ON c.`hostid`=h.`id` LEFT JOIN `userdata` u ON c.`userid`=u.`id` WHERE c.`userid`=? AND c.`resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
        $query->execute(array($admin_id,$reseller_id));
    } 
    $query2=$sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `type`='vs' AND (`status` IS NULL OR `status`=1 OR `status`=4) ORDER BY `jobID` DESC LIMIT 1");
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $jobPending=$gsprache->no;
        if ($row['jobPending']=='Y') {
            $query2->execute(array($row['id']));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                if ($row2['action']=='ad') $jobPending=$gsprache->add;
                else if ($row2['action']=='dl') $jobPending=$gsprache->del;
                else if ($row2['action']=='ri') $jobPending=$sprache->reinstall;
                else if ($row2['action']=='rc') $jobPending=$sprache->rescue_start;
                else if ($row2['action']=='rs') $jobPending=$sprache->restart;
                else if ($row2['action']=='st') $jobPending=$sprache->stop;
                else $jobPending=$gsprache->mod;
                $json=@json_decode($row2['extraData']);
                $tobeActive=(is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
            }
        }
        if ($row['status'] == 1) {
            $status=$sprache->stopped;
        } else if ($row['status'] == 2) {
            $status=$sprache->installing;
        } else if ($row['status'] == 3) {
            $status=$sprache->rescue;
        } else {
            $status=$sprache->ok;
        }
        $active='Y';
        if (($row['active']=='Y' and $row['jobPending']=='N') or ($row['jobPending']=='Y') and isset($tobeActive) and $tobeActive=='Y') {
            $active='Y';
        } else if ($row['active']=='N') {
            $active='N';
        }
        $table[]=array('id'=>$row['id'],'active'=>$active,'cip'=>$row['ip'],'cores'=>$row['cores'],'minmhz'=>$row['minmhz'],'maxmhz'=>$row['maxmhz'],'hddsize'=>$row['hddsize'],'ram'=>$row['ram'],'minram'=>$row['minram'],'maxram'=>$row['maxram'],'status'=>$status,'idescription'=>$row['idescription'],'bitversion'=>$row['bitversion'],'hip'=>$row['hip'],'hid'=>$row['hid'],'hdescription'=>$row['hdescription'],'cname'=>$row['cname'],'userid'=>$row['userid'],'jobPending'=>$jobPending);
    }
    $next=$start+$amount;
    if ($reseller_id==0) {
        $countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `virtualcontainer`");
        $countp->execute();
    } else if ($reseller_id==$admin_id) {
        $countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `virtualcontainer` WHERE `resellerid`=?");
        $countp->execute(array($reseller_id));
    } else {
        $countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `virtualcontainer` WHERE `userid`=? AND `resellerid`=?");
        $countp->execute(array($admin_id,$reseller_id));
    }
    foreach ($countp->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $colcount=$row['amount'];
    }
    if ($colcount>$next) {
        $vor=$start+$amount;
    } else {
        $vor=$start;
    }
    $back=$start-$amount;
    if ($back>=0){
        $zur=$start-$amount;
    } else {
        $zur=$start;
    }
    $pageamount=ceil($colcount/$amount);
    $link='<a href="admin.php?w=vs&amp;d=md&amp;shorten='.$o.'&amp;a=';
    if(!isset($amount)) {
        $link .=20;
    } else {
        $link .=$amount;
    }
    if ($start==0) {
        $link .='&p=0" class="bold">1</a>';
    } else {
        $link .='&p=0">1</a>';
    }
    $pages[]=$link;
    $i=2;
    while ($i<=$pageamount) {
        $selectpage=($i-1)*$amount;
        if ($start==$selectpage) {
            $pages[]='<a href="admin.php?w=vs&amp;d=md&amp;shorten='.$o.'&amp;a='.$amount.'&p='.$selectpage.'" class="bold">'.$i.'</a>';
        } else {
            $pages[]='<a href="admin.php?w=vs&amp;d=md&amp;shorten='.$o.'&amp;a='.$amount.'&p='.$selectpage.'">'.$i.'</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file="admin_root_vserver_list.tpl";
}