<?php
/**
 * File: root_virtual_hosts.php.
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

if (!isset($admin_id) or $main!=1 or $reseller_id != 0 or !$pa['vserverhost']) {
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('reseller',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id==0) {
    $logreseller = 0;
    $logsubuser = 0;
} else {
    if (isset($_SESSION['oldid'])) {
        $logsubuser=$_SESSION['oldid'];
    } else {
        $logsubuser = 0;
    }
    $logreseller = 0;
}
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('d','get') == 'ad') {
    if (!$ui->smallletters('action',2,'post')) {
        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `accounttype`='r' AND `resellerid`=`id` ORDER BY `id` DESC");
        $query->execute(array());
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[] = '<option value="'.$row['id'].'">'.$row['cname'].'</option>';
        }
        $template_file = "admin_root_virtualhosts_add.tpl";
    } else if ($ui->smallletters('action',2,'post') == 'ad'){
        $fail = 0;
        $template_file = 'admin_404.tpl';
        if (!isid($ui->post['reseller'],10) and $ui->post['reseller'] != 0) {
            $fail = 1;
        }
        if (!isip($ui->post['ip'],"all")) {
            $fail = 1;
            $template_file .="IP <br />";
        }
        if (!isid($ui->post['port'],"5")) {
            $fail = 1;
            $template_file .="Port <br />";
        }
        if (!uname_check($ui->post['user'],"20")) {
            $fail = 1;
            $template_file .="User <br />";
        }
        if (!active_check($ui->post['publickey'])) {
            $fail = 1;
            $template_file .="Key <br />";
        }
        if (!active_check($ui->post['active'])) {
            $fail = 1;
            $template_file .="Active <br />";
        }
        if (!active_check($ui->post['esxi'])) {
            $fail = 1;
            $template_file .="ESXi <br />";
        }
        if (!active_check($ui->post['thin'])) {
            $fail = 1;
        }
        if ($fail!="1") {
            $publickey=$ui->post['publickey'];
            $keyname=startparameter($ui->post['keyname']);
            $active=$ui->post['active'];
            $reseller=$ui->post['reseller'];
            $ip=$ui->post['ip'];
            $thin=$ui->post['thin'];
            $thinquota=isid($ui->post['thinquota'],"2");
            $maxserver=isid($ui->post['maxserver'],"3");
            $port=$ui->post['port'];
            $user=$ui->post['user'];
            $esxi=$ui->post['esxi'];
            $pass=password_check($ui->post['pass'],255);
            $os="linux";
            $description=description($ui->post['description']);
            $cores=isid($ui->post['cores'],"5");
            $cpu=description($ui->post['cpu']);
            $mhz=isid($ui->post['mhz'],"5");
            $ram=isinteger($ui->post['ram']);
            $pinsert=$sql->prepare("INSERT INTO `virtualhosts` (`active`,`esxi`,`ip`,`port`,`user`,`pass`,`os`,`description`,`publickey`,`keyname`,`cpu`,`cores`,`mhz`,`ram`,`maxserver`,`thin`,`thinquota`,`resellerid`) VALUES (:active, :esxi, :ip, AES_ENCRYPT(:port, :aeskey),AES_ENCRYPT(:user, :aeskey),AES_ENCRYPT(:pass, :aeskey),:os, :description, :publickey, :keyname, :cpu, :cores, :mhz, :ram, :maxserver, :thin, :thinquota, :reseller)");
            $pinsert->execute(array(':active'=>$active,':esxi'=>$esxi,':ip'=>$ip,':port'=>$port,':aeskey'=>$aeskey,':user'=>$user,':pass'=>$pass,':os'=>$os,':description'=>$description,':publickey'=>$publickey,':keyname'=>$keyname,  ':cpu'=>$cpu,  ':cores'=>$cores,':mhz'=>$mhz,':ram'=>$ram,':maxserver'=>$maxserver,':thin'=>$thin,':thinquota'=>$thinquota,':reseller'=>$reseller));
            $serverid=$sql->lastInsertId();
            include(EASYWIDIR . '/stuff/ssh_exec.php');
            $uidb=ssh2_execute('vh',$serverid,'cd /vmfs/volumes; S = ''; for U in `ls -la | grep "drwxr-xr-t" | awk \'{print $9}\'`; do C=`vmkfstools -Ph $U 2> /dev/null | grep "Capacity" | awk \'{print $2$3}\'`; S="$S$U:$C;"; done; for U in `ls -la | grep "drwxrwxrwx" | awk \'{print $9}\'`; do C=`vmkfstools -Ph $U 2> /dev/null | grep "Capacity" | awk \'{print $2$3}\'`; S="$S$U:$C;"; done; echo $S');
            if ($uidb != '' and $uidb!==false) {
                $uiddata=explode(";",$uidb);
                $i = 0;
                $count=count($uiddata)-1;
                while ($i<$count) {
                    list($uid,$space)=explode(":", $uiddata[$i]);
                    if(strpos(strtolower($space), strtolower('TB')) === false) {
                        $hddamount=str_replace('GB,', '', $space);
                    } else {
                        $hddamount=str_replace('TB,', '', $space)*1000;
                    }
                    if(isset($hdd)){
                        $hdd .="\r\n".$uid . '  ' . $hddamount;
                    } else {
                        $hdd=$uid . '  ' . $hddamount;
                    }
                    $i++;
                }
                $pupdate=$sql->prepare("UPDATE `virtualhosts` SET `hdd`=? WHERE `id`=?");
                $pupdate->execute(array($hdd,$serverid));
                $loguseraction="%add% %virtual% $ip";
                $insertlog->execute();
                $template_file = $spracheResponse->table_add;
            } else {
                $pdelete=$sql->prepare("DELETE FROM `virtualhosts` WHERE `id`=?");
                $pdelete->execute(array($serverid));
                $template_file = "Error: Could not connect!";
            }
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d','get') == 'dl' and $ui->id('id', 10, 'get')) {
    $id=$ui->id('id', 10, 'get');
    if (!$ui->smallletters('action',2,'post')) {
        $query = $sql->prepare("SELECT `ip`,`description` FROM `virtualhosts` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $description=$row['description'];
            $ip=$row['ip'];
        }
        $template_file = "admin_root_virtualhosts_dl.tpl";
    } else if ($ui->smallletters('action',2,'post') == 'dl'){
        $query = $sql->prepare("SELECT id,ip,userid FROM `virtualcontainer` WHERE `hostid`=?");
        $query2 = $sql->prepare("DELETE FROM `gsswitch` WHERE `serverid`=? AND `resellerid`=?");
        $query4 = $sql->prepare("DELETE FROM `addons_installed` WHERE `serverid`=? AND `resellerid`=?");
        $query5 = $sql->prepare("DELETE FROM `serverlist` WHERE `id`=? AND `resellerid`=?");
        $query->execute(array($id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip=$row['ip'];
            $query2->execute(array($row['id'], $row['userid']));
            $query4->execute(array($row['id'], $row['userid']));
            $query5->execute(array($row['id'], $row['userid']));
        }
        $query = $sql->prepare("SELECT ip FROM `virtualhosts` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        $ip=$query->fetchColumn();
        $query = $sql->prepare("DELETE FROM `virtualhosts` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        $query = $sql->prepare("DELETE FROM `virtualcontainer` WHERE `hostid`=?");
        $query->execute(array($id));
        $template_file = $spracheResponse->table_del;
        $loguseraction="%del% %virtual% $ip";
        $insertlog->execute();
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d','get') == 'md' and $ui->id('id', 10, 'get')) {
    $id=$ui->id('id', 10, 'get');
    if (!$ui->smallletters('action',2,'post')) {
        $query = $sql->prepare("SELECT `active`,`esxi`,`ip`,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,`os`,`description`,`publickey`,`keyname`,`cpu`,`cores`,`mhz`,`hdd`,`ram`,`maxserver`,`thin`,`thinquota`,`resellerid` FROM `virtualhosts` WHERE `id`=:id LIMIT 1");
        $query->execute(array(':id'=>$id,':aeskey'=>$aeskey));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $esxi=$row['esxi'];
            $ip=$row['ip'];
            $port=$row['decryptedport'];
            $user=$row['decrypteduser'];
            $pass=$row['decryptedpass'];
            $os=$row['os'];
            $description=$row['description'];
            $publickey=$row['publickey'];
            $keyname=$row['keyname'];
            $cpu=$row['cpu'];
            $cores=$row['cores'];
            $mhz=$row['mhz'];
            $hdd=$row['hdd'];
            $ram=$row['ram'];
            $maxserver=$row['maxserver'];
            $thin=$row['thin'];
            $thinquota=$row['thinquota'];
            $resellerid=$row['resellerid'];
        }
        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `accounttype`='r' ORDER BY `id` DESC");
        $query->execute(array(':reseller_id'=>$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $table[]=($resellerid==$row['id']) ? '<option value="'.$row['id'].'" selected="selected">'.$row['cname'].'</option>' : '<option value="'.$row['id'].'">'.$row['cname'].'</option>';
        $template_file = "admin_root_virtualhosts_md.tpl";
    } else if ($ui->smallletters('action',2,'post') == 'md'){
        $fail = 0;
        $template_file = 'admin_404.tpl';
        if (!isid($ui->post['reseller'],10) and $ui->post['reseller'] != 0) {
            $fail = 1;
            echo "test".$ui->post['reseller'];
        }
        if (!isip($ui->post['ip'],"all")) {
            $fail = 1;
            $template_file .="IP <br />";
        }
        if (!isid($ui->post['port'],"5")) {
            $fail = 1;
            $template_file .="Port <br />";
        }
        if (!uname_check($ui->post['user'],"20")) {
            $fail = 1;
            $template_file .="User <br />";
        }
        if (!active_check($ui->post['publickey'])) {
            $fail = 1;
            $template_file .="Key <br />";
        }
        if (!active_check($ui->post['active'])) {
            $fail = 1;
            $template_file .="Active <br />";
        }
        if (!active_check($ui->post['esxi'])) {
            $fail = 1;
            $template_file .="ESXi <br />";
        }
        if (!active_check($ui->post['thin'])) {
            $fail = 1;
        }
        if ($fail!="1") {
            $publickey=$ui->post['publickey'];
            $keyname=startparameter($ui->post['keyname']);
            $active=$ui->post['active'];
            $esxi=$ui->post['esxi'];
            $ip=$ui->post['ip'];
            $thin=$ui->post['thin'];
            $thinquota=isid($ui->post['thinquota'],"2");
            $port=$ui->post['port'];
            $reseller=$ui->post['reseller'];
            $user=$ui->post['user'];
            $pass=startparameter($ui->post['pass']);
            $os="linux";
            $description=description($ui->post['description']);
            $cores=isid($ui->post['cores'],"5");
            $cpu=description($ui->post['cpu']);
            $mhz=isid($ui->post['mhz'],"5");
            $ram=isid($ui->post['ram'],"5");
            $hdd=startparameter($ui->post['hdd']);
            $maxserver=isid($ui->post['maxserver'],"3");
            $pinsert=$sql->prepare("UPDATE `virtualhosts` SET `active`=:active,`esxi`=:esxi,`ip`=:ip,`port`=AES_ENCRYPT(:port,:aeskey),`user`=AES_ENCRYPT(:user,:aeskey),`pass`=AES_ENCRYPT(:pass,:aeskey),`os`=:os,`description`=:description,`publickey`=:publickey,`keyname`=:keyname,`cpu`=:cpu,`cores`=:cores,`mhz`=:mhz,`hdd`=:hdd,`ram`=:ram,`maxserver`=:maxserver,`thin`=:thin,`thinquota`=:thinquota,`resellerid`=:reseller WHERE `id`=:id LIMIT 1");
            $pinsert->execute(array(':active'=>$active,':esxi'=>$esxi,':ip'=>$ip,':port'=>$port,':aeskey'=>$aeskey,':user'=>$user,':pass'=>$pass,':os'=>$os,':description'=>$description,':publickey'=>$publickey,':keyname'=>$keyname,  ':cpu'=>$cpu,  ':cores'=>$cores,':mhz'=>$mhz,':hdd'=>$hdd,':ram'=>$ram,':maxserver'=>$maxserver,':id'=>$id,':thin'=>$thin,':thinquota'=>$thinquota,':reseller'=>$reseller));
            $template_file = $spracheResponse->table_add;
            $loguseraction="%mod% %virtual% $ip";
            $insertlog->execute();
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else {
    $table = array();
    $o=$ui->st('o','get');
    if ($ui->st('o','get') == 'ap') {
        $orderby='h.`ip` ASC';
    } else if ($ui->st('o','get') == 'ae') {
        $orderby='h.`description` ASC';
    } else if ($ui->st('o','get') == 'de') {
        $orderby='h.`description` DESC';
    } else if ($ui->st('o','get') == 'as') {
        $orderby='h.`active` ASC, h.`notified` ASC';
    } else if ($ui->st('o','get') == 'ds') {
        $orderby='h.`active` DESC, h.`notified` DESC';
    } else if ($ui->st('o','get') == 'ai') {
        $orderby='h.`id` ASC';
    } else if ($ui->st('o','get') == 'di') {
        $orderby='h.`id` DESC';
    } else {
        $orderby='h.`ip` DESC';
        $o='ap';
    }
    $pselect=$sql->prepare("SELECT h.`active`,h.`id`,h.`ip`,h.`description`,h.`cores`,h.`mhz`,h.`hdd`,h.`ram`,h.`maxserver`,h.`notified` FROM `virtualhosts` h LEFT JOIN `virtualcontainer` v ON v.`hostid`=h.`id` GROUP BY h.`id` ORDER BY $orderby LIMIT $start,$amount");
    $pselect->execute();
    foreach ($pselect->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $id=$row['id'];
        $cores=$row['cores'];
        $mhz=$row['mhz'];
        $hddsize=$row['hdd'];
        $ram=$row['ram'];
        $ramused = 0;
        $mountsize = '';
        $mountunused = '';
        $cpucore = array();
        $hdd = '';
        $hdd_rows=explode("\r\n", $row['hdd']);
        foreach ($hdd_rows as $hddline) {
            $data_explode=explode(" ", $hddline);
            if (isset($data_explode[1])) {
                $mountpoint=$data_explode[0];
                $hdd[]=$mountpoint;
                $mountsize[$mountpoint]=$data_explode[1];
                $mountunused[$mountpoint] = 0;
            }
        }
        $i = 1;
        $cpucores = '';
        while ($i<=$cores) {
            $cpucores[]=$i;
            $cpucore[$i] = 0;
            $i++;
        }
        $pselect2=$sql->prepare("SELECT `cores`,`minmhz`,`maxmhz`,`hddsize`,`mountpoint`,`minram` FROM `virtualcontainer` WHERE `hostid`=?");
        $pselect2->execute(array($id));
        $i2 = 0;
        foreach ($pselect2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $mountpoint=$row2['mountpoint'];
            $addstracthdd=$mountunused[$mountpoint]+$row2['hddsize'];
            $mountunused[$mountpoint]=$addstracthdd;
            $addstractram=$ramused+$row2['minram'];
            $ramused=$addstractram;
            $cpuhz=$row2['cores']*$row2['minmhz'];
            $addcpu=$cpucore[1]+$cpuhz;
            if ($addcpu<=$mhz) {
                $cpucore[1]=$addcpu;
            } else {
                $cpucore[1]=$mhz;
                $nextcore = 2;
                while ($nextcore<=$cores) {
                    $extra=$addcpu-$mhz;
                    $addcpu=$cpucore[$nextcore]+$extra;
                    if ($addcpu<=$mhz and $addcpu>=0) {
                        $cpucore[$nextcore]=$addcpu;
                    } else if ($addcpu>=0) {
                        $cpucore[$nextcore]=$mhz;
                    }
                    $nextcore++;
                }
            }
            $i2++;
        }
        if ($row['notified']>=$downChecks and $row['active'] == 'Y') {
            $imgName='16_error';
            $imgAlt='Offline';
        } else if ($row['active'] == 'Y') {
            $imgName='16_ok';
            $imgAlt='Online';
        } else {
            $imgName='16_bad';
            $imgAlt='Deactivated';
        }
        $installedserver=$i2. '/' . $row['maxserver'];
        $table[]=array('id'=>$id,'img'=>$imgName,'alt'=>$imgAlt,'ip'=>$row['ip'],'active'=>$row['active'],'description'=>$row['description'],'cores'=>$cores,'mhz'=>$mhz,'cpus'=>$cpucore,'hdd'=>$hdd,'ram'=>$ram,'ramused'=>$ramused,'mountsize'=>$mountsize,'mountunused'=>$mountunused,'installedserver'=>$installedserver);
    }
    $next=$start+$amount;
    $countp=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `virtualhosts`");
    $countp->execute(array());
    foreach ($countp->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $colcount=$row['amount'];
    }
    if ($colcount>$next) {
        $vor=$start+$amount;
    } else {
        $vor=$start;
    }
    $back=$start - $amount;
    if ($back>=0){
        $zur=$start - $amount;
    } else {
        $zur=$start;
    }
    $pageamount = ceil($colcount / $amount);
    $link='<a href="admin.php?w=vh&amp;d=md&amp;a=';
    if(!isset($amount)) {
        $link .="20";
    } else {
        $link .=$amount;
    }
    if ($start==0) {
        $link .='&p=0" class="bold">1</a>';
    } else {
        $link .='&p=0">1</a>';
    }
    $pages[]=$link;
    $i = 2;
    while ($i<=$pageamount) {
        $selectpage = ($i - 1) * $amount;
        if ($start==$selectpage) {
            $pages[] = '<a href="admin.php?w=vh&amp;d=md&amp;a='.$amount.'&p='.$selectpage.'" class="bold">'.$i.'</a>';
        } else {
            $pages[] = '<a href="admin.php?w=vh&amp;d=md&amp;a='.$amount.'&p='.$selectpage.'">'.$i.'</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = "admin_root_virtualhosts_list.tpl";
}