<?php
/**
 * File: userpanel_protectionmode.php.
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
if ((!isset($user_id) or $main!=1) or (isset($user_id) and !$pa['restart']) or !$ui->id('id',10,'get')) redirect('userpanel.php');
$sprache=getlanguagefile('gserver',$user_language,$reseller_id,$sql);
$loguserid=$user_id;
$logusername=getusername($user_id,$sql);
$logusertype="user";
$logreseller=0;
if (isset($admin_id)) {
	$logsubuser=$admin_id;
} else if (isset($subuser_id)) {
	$logsubuser=$subuser_id;
} else {
	$logsubuser=0;
}
if (isset($admin_id) and $reseller_id!=0 and $admin_id!=$reseller_id) $reseller_id=$admin_id;
$aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
$aeskey=$aesfilecvar['aeskey'];
$files=array();
$query=$sql->prepare("SELECT g.*,AES_DECRYPT(g.`ftppassword`,?) AS `dftppassword`,AES_DECRYPT(g.`ppassword`,?) AS `dpftppassword`,t.`protected` AS `tpallowed`,t.`shorten`,t.`protectedSaveCFGs`,t.`gamebinary`,t.`binarydir`,t.`modfolder`,u.`cname`,s.`servertemplate` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`userid`=? AND s.`resellerid`=? LIMIT 1");
$query->execute(array($aeskey,$aeskey,$ui->id('id',10,'get'),$user_id,$reseller_id));
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $currentID=$row['serverid'];
	$serverip=$row['serverip'];
	$port=$row['port'];
    $gsfolder=$serverip.'_'.$port;
    $gamestring='1_'.$row['shorten'];
	$protected=$row['protected'];
    $pallowed=($row['pallowed']=='Y' and $row['tpallowed']=='Y') ? 'Y' : 'N';
	$rootid=$row['rootID'];
    $customer=($row['newlayout']=='Y') ? $row['cname'].'-'.$ui->id('id',10,'get') : $row['cname'];
    $customerp=$customer.'-p';
    $ftppass=$row['dftppassword'];
    $ftppassProtected=$row['dpftppassword'];
    foreach (explode("\r\n",$row['protectedSaveCFGs']) as $cfg) if ($cfg!='') $files[]=$cfg;
    $shorten=$row['shorten'];
    $serverTemplate=($row['servertemplate']!=1) ? $row['shorten'].'-'.$row['servertemplate'] : $row['shorten'];
    if($row['gamebinary']=="srcds_run") $gamePath="${row['binarydir']}/${row['modfolder']}";
    else if($row['gamebinary']=="hlds_run") $gamePath="${row['modfolder']}";
    else $gamePath='';
    $gamePath=str_replace(array('//','///','////'),'/',$gamePath);
}

if ($query->rowCount()==0 or (isset($pallowed) and $pallowed=='N') or (isset($_SESSION['sID']) and !in_array($ui->id('id',10,'get'),$substituteAccess['gs']))) {
	redirect('userpanel.php');
} else if (isset($rootid)) {
    include(EASYWIDIR.'/stuff/ssh_exec.php');
    function cfgTransfer ($pserverRead,$customerRead,$ftppassRead,$readFTPShorten,$pserverWrite,$customerWrite,$ftppassWrite,$writeFTPShorten) {
        global $sship,$ftpport,$gsfolder,$gamePath,$files;
        foreach ($files as $cfg) {
            $fp=@fopen("ftp://${customerRead}:${ftppassRead}@${sship}:${ftpport}/${pserverRead}${gsfolder}/${readFTPShorten}/${gamePath}/${cfg}",'r');
            if ($fp) {
                $temp = tmpfile();
                stream_set_timeout($fp,5);
                while (!feof($fp)) fwrite($temp,fread($fp,1024));
                fclose($fp);
                fseek($temp,0);
                fseek($temp,0);
                $ftp_connect=($ftpport==21 or $ftpport=="" or $ftpport==null) ? @ftp_connect($sship) : @ftp_connect($sship,$ftpport);
                if ($ftp_connect) {
                    $ftp_login=ftp_login($ftp_connect,$customerWrite,$ftppassWrite);
                    if ($ftp_login) {
                        $split_config=preg_split('/\//',str_replace(array('//','///','////'),'/',$cfg),-1,PREG_SPLIT_NO_EMPTY);
                        $folderFileCount=count($split_config)-1;
                        $i=0;
                        $folders="${pserverWrite}/${gsfolder}/${writeFTPShorten}/${gamePath}/";
                        while ($i<$folderFileCount) {
                            $folders.='/'.$split_config[$i];
                            $i++;
                        }
                        foreach (preg_split('/\//',str_replace(array('//','///','////'),'/',$folders),-1,PREG_SPLIT_NO_EMPTY) as $dir) {
                            if(!@ftp_chdir($ftp_connect,$dir)) {
                                @ftp_mkdir($ftp_connect,$dir);
                                @ftp_chdir($ftp_connect,$dir);
                            }
                        }
                        $uploadfile=str_replace(array('//','///','////'),'/',$split_config[$i]);
                        @ftp_fput($ftp_connect,$uploadfile,$temp,FTP_ASCII);
                    }
                    ftp_close($ftp_connect);
                }
                fclose($temp);
            }
        }
    }
    $rdata=serverdata('root',$rootid,$aeskey,$sql);
    $sship=$rdata['ip'];
    $sshport=$rdata['port'];
    $sshuser=$rdata['user'];
    $sshpass=$rdata['pass'];
    $ftpport=$rdata['ftpport'];
    if (isset($protected,$serverip,$port) and $protected=='Y') {
        $query=$sql->prepare("UPDATE `serverlist` SET `anticheat`='1' WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($currentID,$reseller_id));
        $query=$sql->prepare("UPDATE `gsswitch` SET `protected`='N' WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id',10,'get'),$reseller_id));
        cfgTransfer('',$customerp,$ftppassProtected,$shorten,'server/',$customer,$ftppass,$serverTemplate);
        gsrestart($ui->id('id',10,'get'),'re',$aeskey,$sprache,$reseller_id,$sql);
        $loguseraction="%stop% %pmode% $serverip:$port";
        $insertlog->execute();
        $template_file=$sprache->protect.' off';
    } else if (isset($protected,$serverip,$port,$rootid,$customer,$ftppass) and $protected=='N') {
        gsrestart($ui->id('id',10,'get'),'sp',$aeskey,$sprache,$reseller_id,$sql);
        $randompass=passwordgenerate(10);
        exec_server($sship,$sshport,$sshuser,$sshpass,'./control.sh mod '.$customer.' '.$ftppass.' '.$randompass,$sql);
        shell_server($sship,$sshport,$sshuser,$sshpass,$customer.'-p',$randompass,'./control.sh reinstserver '.$customer.'-p '.$gamestring.' '.$gsfolder.' protected',$sql);
        $query=$sql->prepare("UPDATE `gsswitch` SET `ppassword`=AES_ENCRYPT(?,?),`protected`='Y',`psince`=NOW() WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($randompass,$aeskey,$ui->id('id',10,'get'),$reseller_id));
        cfgTransfer('server/',$customer,$ftppass,$serverTemplate,'',$customerp,$randompass,$shorten);
        #gsrestart($ui->id('id',10,'get'),'re',$aeskey,$sprache,$reseller_id,$sql);
        $loguseraction="%restart% %pmode% $serverip:$port";
        $insertlog->execute();
        $template_file=$sprache->protect.' on';
    }
}