<?php
/**
 * File: class_rootserver.php.
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

class rootServer {

    // Data
    protected $sql,$aeskey,$httpConnect,$tempID,$netmask,$extraData,$ID=array(),$type=array(),$dhcpData=array(),$PXEData=array(),$startStop=array(),$vmwareHosts = array();

    function __destruct() {
        unset($this->sql,$this->aeskey,$this->netmask,$this->ID,$this->type,$this->httpConnect,$this->dhcpData,$this->startStop,$this->vmwareHosts);
    }

    function __construct($aeskey) {

        // retrieve global vars
        global $sql;

        // define internal vars
        $this->sql=$sql;
        $this->aeskey=$aeskey;

        // check if curl is choosen and available and initiate cURL-Session else fallback to fsockopen
        $this->httpConnect=(function_exists('curl_init')) ? 'curl' : 'fsockopen';

        return true;
    }

    public function rootServer ($ID,$action,$type='dedicated',$extraData=null) {
        $this->tempID=$ID;
        $this->ID[$type][$ID] = array();
        $this->ID[$type][$ID]['action'] = $action;
        $this->type=$type;
        $this->extraData=$extraData;
        $imageID=(isset($extraData['imageID'])) ? $extraData['imageID'] : 0;
        $hostID = 0;
        $userID = 0;
        $resellerID = 0;
        if(isid($imageID,10)) {
            $query=$this->sql->prepare("SELECT `distro`,`bitversion` FROM `resellerimages` WHERE `id`=? AND `active`='Y' LIMIT 1");
            $query->execute(array($imageID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $distro=$row['distro'];
                $guestos=($row['bitversion'] == '32') ? $row['distro'] : $row['distro'] . '-' . $row['bitversion'];
            }
        }
        // get Root Data from DB
        if ($this->type == 'dedicated') {
            $query=$this->sql->prepare("SELECT d.*,u.`cname` FROM `rootsDedicated` d LEFT JOIN `userdata` u ON d.`userID`=u.`id` WHERE d.`dedicatedID`=? LIMIT 1");
            $query->execute(array($this->tempID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $this->ID[$type][$ID]['useDHCP'] = $row['useDHCP'];
                $this->ID[$type][$ID]['hostname'] = 'dedi-'.$ID;
                $this->ID[$type][$ID]['usePXE'] = $row['usePXE'];
                $this->ID[$type][$ID]['pxeID'] = $row['pxeID'];
                $this->ID[$type][$ID]['mac'] = $row['mac'];
                $this->ID[$type][$ID]['ip'] = $row['ip'];
                $this->ID[$type][$ID]['restart'] = $row['restart'];
                $this->ID[$type][$ID]['apiRequestType'] = $row['apiRequestType'];
                $this->ID[$type][$ID]['apiRequestRestart'] = $row['apiRequestRestart'];
                $this->ID[$type][$ID]['apiRequestStop'] = $row['apiRequestStop'];
                $this->ID[$type][$ID]['https'] = $row['https'];
                $this->ID[$type][$ID]['apiURL'] = $row['apiURL'];
                $userID=$row['userID'];
                $resellerID=$row['resellerID'];
            }

            // Get VMware Data
        } else if ($this->type == 'vmware') {
            $query=$this->sql->prepare("SELECT c.*,u.`id` AS `userID`,u.`cname`,h.`cores` AS `hcore`,h.`esxi`,h.`id` AS `hostID`,h.`ip` AS `hip`,AES_DECRYPT(h.`port`,:aeskey) AS `dport`,AES_DECRYPT(h.`user`,:aeskey) AS `duser`,AES_DECRYPT(h.`pass`,:aeskey) AS `dpass`,h.`publickey`,h.`keyname` FROM `virtualcontainer` c INNER JOIN `userdata` u ON c.`userid`=u.`id` INNER JOIN `virtualhosts` h ON c.`hostid`=h.`id` WHERE c.`id`=:vmID LIMIT 1");
            $query->execute(array(':aeskey' => $this->aeskey,':vmID' => $this->tempID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if (!isset($this->vmwareHosts[$row['hostID']])) {
                    $this->vmwareHosts[$row['hostID']]['vmIDs']['ip'] = $row['hip'];
                    $this->vmwareHosts[$row['hostID']]['vmIDs']['dport'] = $row['dport'];
                    $this->vmwareHosts[$row['hostID']]['vmIDs']['duser'] = $row['duser'];
                    $this->vmwareHosts[$row['hostID']]['vmIDs']['dpass'] = $row['dpass'];
                    $this->vmwareHosts[$row['hostID']]['vmIDs']['publickey'] = $row['publickey'];
                    $this->vmwareHosts[$row['hostID']]['vmIDs']['keyname'] = $row['keyname'];
                }
                $this->ID[$type][$ID]['cores'] = $row['cores'];
                $this->ID[$type][$ID]['mountpoint'] = $row['mountpoint'];
                $this->ID[$type][$ID]['hostname'] = $row['cname'] . '-' . $this->tempID;
                $this->ID[$type][$ID]['ram']=1024*$row['ram'];
                $this->ID[$type][$ID]['minram']=1024*$row['minram'];
                $this->ID[$type][$ID]['maxram']=1024*$row['maxram'];
                $this->ID[$type][$ID]['minmhz'] = $row['cores']*$row['minmhz'];
                $this->ID[$type][$ID]['maxmhz'] = $row['cores']*$row['maxmhz'];
                $this->ID[$type][$ID]['hddsize'] = $row['hddsize'].'GB';
                $this->ID[$type][$ID]['mac'] = $row['mac'];
                $this->ID[$type][$ID]['pxeID'] = $row['pxeID'];
                $this->ID[$type][$ID]['hostname'] = 'vmware-'.$ID;
                $this->ID[$type][$ID]['mac'] = $row['mac'];
                $this->ID[$type][$ID]['ip'] = $row['ip'];
                $this->ID[$type][$ID]['usePXE'] = 'Y';
                $this->ID[$type][$ID]['restart'] = 'Y';
                $this->ID[$type][$ID]['distro']=(isset($distro)) ? $distro : '';
                $this->ID[$type][$ID]['guestos']=(isset($guestos)) ? $guestos : '';
                $hostID=$row['hostid'];
                $userID=$row['userid'];
                $resellerID=$row['resellerid'];
                $this->vmwareHosts[$row['hostID']]['actions'][]=array('action' => $action,'id' => $ID);
            }
        }
        if (!isset($row['ip'])) return 'Database Error: Could not find VM or ESX(i) host for ID: '.$this->tempID;
        if (!in_array($action, array('md','ad','dl','rp')) and ($this->type == 'vmware' or ($this->type == 'dedicated' and $this->ID[$type][$ID]['usePXE'] == 'Y')) and !isid($imageID,10)) {
            unset($this->ID[$type][$ID]);
            return 'Image Error: No imageID defined for Server with ID: '.$this->tempID;
        } else if (!in_array($action, array('md','ad','dl','rp')) and ($this->type == 'vmware' or ($this->type == 'dedicated' and $this->ID[$type][$ID]['usePXE'] == 'Y')) and !isset($guestos)) {
            unset($this->ID[$type][$ID]);
            return 'Image Error: Cannot find image with imageID '.$imageID.' defined for Server with ID: '.$this->tempID;
        } else if (!in_array($action, array('re','st'))) {
            // get DHCP Data from DB
            if ($this->type == 'vmware' or ($this->type == 'dedicated' and $this->ID[$type][$ID]['useDHCP'] == 'Y')) {
                $ex=explode('.',$this->ID[$type][$ID]['ip']);
                $subnet=$ex[0] . '.' . $ex[1] . '.' . $ex[2].'.0';
                $this->ID[$type][$ID]['subnet'] = $subnet;
                $searchFor=$ex[0] . '.' . $ex[1] . '.' . $ex[2].'.';
                $query=$this->sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass` FROM `rootsDHCP` WHERE `active`='Y' AND (`ips` LIKE :ip OR `ips` LIKE :subnet)");
                $query->execute(array(':aeskey' => $this->aeskey,':ip' => '%'.$this->ID[$type][$ID]['ip'].'%',':subnet' => '%'.$searchFor.'%'));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (!isset($foundDHCP) and in_array($this->ID[$type][$ID]['ip'],ipstoarray($row['ips']))) {
                        $foundDHCP = true;
                        if (!isset($this->dhcpData[$row['id']])) {
                            $this->dhcpData[$row['id']]['ip'] = $row['ip'];
                            $this->dhcpData[$row['id']]['port'] = $row['dport'];
                            $this->dhcpData[$row['id']]['user'] = $row['duser'];
                            $this->dhcpData[$row['id']]['pass'] = $row['dpass'];
                            $this->dhcpData[$row['id']]['publickey'] = $row['publickey'];
                            $this->dhcpData[$row['id']]['keyname'] = $row['keyname'];
                            $this->dhcpData[$row['id']]['netmask'] = $row['netmask'];
                            $this->dhcpData[$row['id']]['startCmd'] = $row['startCmd'];
                            $this->dhcpData[$row['id']]['dhcpFile'] = $row['dhcpFile'];
                            $this->dhcpData[$row['id']]['subnetOptions'] = $row['subnetOptions'];
                        }
                        $this->dhcpData[$row['id']]['actions'][]=array('action' => $action,'id' => $ID,'type' => $type,'imageID' => $imageID,'hostID' => $hostID,'userID' => $userID,'resellerID' => $resellerID);
                        $modID=$row['id'];
                    }
                }
                if (isset($foundDHCP) and $action == 'md' and isset($this->extraData['oldip']) and $this->extraData['oldip'] != $this->ID[$type][$ID]['ip']) {
                    $ex=explode('.',$this->extraData['oldip']);
                    $searchForOld=$ex[0] . '.' . $ex[1] . '.' . $ex[2].'.';
                    $query=$this->sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass` FROM `rootsDHCP` WHERE `active`='Y' AND (`ips` LIKE :ip OR `ips` LIKE :subnet)");
                    $query->execute(array(':aeskey' => $this->aeskey,':ip' => '%'.$this->extraData['oldip'].'%',':subnet' => '%'.$searchForOld.'%'));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        if (in_array($this->extraData['oldip'],ipstoarray($row['ips']))) {
                            if (!isset($this->dhcpData[$row['id']])) {
                                $this->dhcpData[$row['id']]['ip'] = $row['ip'];
                                $this->dhcpData[$row['id']]['port'] = $row['dport'];
                                $this->dhcpData[$row['id']]['user'] = $row['duser'];
                                $this->dhcpData[$row['id']]['pass'] = $row['dpass'];
                                $this->dhcpData[$row['id']]['publickey'] = $row['publickey'];
                                $this->dhcpData[$row['id']]['keyname'] = $row['keyname'];
                                $this->dhcpData[$row['id']]['netmask'] = $row['netmask'];
                                $this->dhcpData[$row['id']]['startCmd'] = $row['startCmd'];
                                $this->dhcpData[$row['id']]['dhcpFile'] = $row['dhcpFile'];
                                $this->dhcpData[$row['id']]['subnetOptions'] = $row['subnetOptions'];
                            }
                            if (isset($modID) and $modID != $row['id']) $this->dhcpData[$row['id']]['actions'][]=array('action' => 'del','id' => $ID,'type' => $type,'imageID' => $imageID,'hostID' => $hostID,'userID' => $userID,'resellerID' => $resellerID);
                        }
                    }
                }
                if (!isset($foundDHCP)) {
                    unset($this->ID[$type][$ID]);
                    return 'Database Error: Could not find DHCP Server with Subnet: '.$subnet;
                }
            }
            // Get PXE Data
            if (!in_array($action, array('md','ad','rp')) and ($this->type == 'vmware' or ($this->type == 'dedicated' and $this->ID[$type][$ID]['usePXE'] == 'Y')) and isid($imageID,10)) {
                if (isid($this->ID[$type][$ID]['pxeID'],10)) {
                    $query=$this->sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass` FROM `rootsPXE` WHERE `active`='Y' AND `id`=:pxeID LIMIT 1");
                    $query->execute(array(':aeskey' => $this->aeskey,':pxeID' => $this->ID[$type][$ID]['pxeID']));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        if(!isset($this->PXEData[$row['id']])){
                            $this->PXEData[$row['id']]['ip'] = $row['ip'];
                            $this->PXEData[$row['id']]['port'] = $row['dport'];
                            $this->PXEData[$row['id']]['user'] = $row['duser'];
                            $this->PXEData[$row['id']]['pass'] = $row['dpass'];
                            $this->PXEData[$row['id']]['publickey'] = $row['publickey'];
                            $this->PXEData[$row['id']]['keyname'] = $row['keyname'];
                            $this->PXEData[$row['id']]['PXEFolder'] = $row['PXEFolder'];
                        }
                        $foundPXE = true;
                        $this->PXEData[$row['id']]['actions'][]=array('action' => $action,'id' => $ID,'type' => $type,'imageID' => $imageID);
                        $this->ID[$type][$ID]['pxeIP'] = $row['ip'];
                        $this->ID[$type][$ID]['pxeID'] = $row['id'];
                    }
                }
                if (!isset($foundPXE) and $action!='dl') {
                    $query=$this->sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass` FROM `rootsPXE` WHERE `active`='Y' ORDER BY RAND() LIMIT 1");
                    $query->execute(array(':aeskey' => $this->aeskey));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        if(!isset($this->PXEData[$row['id']])){
                            $this->PXEData[$row['id']]['ip'] = $row['ip'];
                            $this->PXEData[$row['id']]['port'] = $row['dport'];
                            $this->PXEData[$row['id']]['user'] = $row['duser'];
                            $this->PXEData[$row['id']]['pass'] = $row['dpass'];
                            $this->PXEData[$row['id']]['publickey'] = $row['publickey'];
                            $this->PXEData[$row['id']]['keyname'] = $row['keyname'];
                            $this->PXEData[$row['id']]['PXEFolder'] = $row['PXEFolder'];
                        }
                        $foundPXE = true;
                        $this->PXEData[$row['id']]['actions'][]=array('action' => $action,'id' => $ID,'type' => $type,'imageID' => $imageID);
                        $this->ID[$type][$ID]['pxeIP'] = $row['ip'];
                        $this->ID[$type][$ID]['pxeID'] = $row['id'];
                    }
                }
                if (!isset($foundPXE) and $action!='dl') {
                    unset($this->ID[$type][$ID]);
                    return 'Database Error: Could not find PXE Server';
                }
            }
        }
        if ($this->ID[$type][$ID]['restart'] == 'A' and $this->type == 'dedicated' and $action!='rp') $this->startStop[]=array('action'=>((isset($this->extraData['oldactive']) and $this->extraData['oldactive'] == 'Y') or in_array($action, array('ad','st','dl'))) ? 'st' : 're','id' => $ID and $action!='rp');
        else if ($this->type == 'dedicated' and $action!='rp') return 'Restart not allowed for Server with ID: '.$this->tempID;
        return true;
    }
    public function dhcpFiles () {
        $removeArray = array();
        foreach ($this->dhcpData as $k=>$v) {
            $i = 0;
            unset($tempBad);
            if ($v['publickey']=="Y") {

                # https://github.com/easy-wi/developer/issues/70
                $sshkey=removePub($v['keyname']);
                $pubkey=EASYWIDIR . '/keys/'.$sshkey.'.pub';
                $key=EASYWIDIR . '/keys/'.$sshkey;

                $ssh2=(file_exists($pubkey) and file_exists($key)) ? @ssh2_connect($v['ip'],$v['port'], array('hostkey' => 'ssh-rsa')) : false;
            } else {
                $ssh2= @ssh2_connect($v['ip'],$v['port']);
            }
            if ($ssh2==true) {
                $connect_ssh2=($v['publickey'] == 'Y' and isset($pubkey,$key)) ? @ssh2_auth_pubkey_file($ssh2,$v['user'],$pubkey,$key) : @ssh2_auth_password($ssh2,$v['user'],$v['pass']);
                if ($connect_ssh2==true) {
                    $sftp=ssh2_sftp($ssh2);
                    $file=(substr($v['dhcpFile'],0,1) == '/') ? 'ssh2.sftp://'.$sftp.$v['dhcpFile'] : 'ssh2.sftp://'.$sftp.'/home/'.$v['user']. '/' . $v['dhcpFile'];
                    $fileErrorOutput=(substr($v['dhcpFile'],0,1) == '/') ? $v['dhcpFile'] : '/home/'.$v['user']. '/' . $v['dhcpFile'];
                    $buffer = '';
                    $fp=@fopen($file,'r');
                    if ($fp) {
                        $filesize=filesize($file);
                        while (strlen($buffer)<$filesize) $buffer.=fread($fp,$filesize);
                        fclose($fp);
                        $config=$this->parseDhcpConfig(str_replace(array("\0","\b","\r","\Z"),'',$buffer));
                        if (is_array($config)) {
                            foreach ($v['actions'] as $a) {
                                if ($a['action'] == 'del' and isset($config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']])) {
                                    unset($config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]);
                                } else if (isset($this->ID[$a['type']][$a['id']])) {
                                    $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['hardware ethernet'] = $this->ID[$a['type']][$a['id']]['mac'].';';
                                    $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['fixed-address'] = $this->ID[$a['type']][$a['id']]['ip'].';';
                                    if ($this->ID[$a['type']][$a['id']]['usePXE'] == 'Y' and (in_array($a['action'], array('ad','ri','rc')))) {
                                        $removeArray[]=array('type'=>($a['type'] == 'dedicated') ? 'de' : 'vs','affectedID' => $a['id'],'name' => $this->ID[$a['type']][$a['id']]['ip'],'imageID' => $a['imageID'],'hostID' => $a['hostID'],'userID' => $a['userID'],'resellerID' => $a['resellerID'],'extraData'=>array('runAt' => strtotime("+5 minutes")));
                                        $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['filename'] = 'pxelinux.0;';
                                        $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['next-server'] = $this->ID[$a['type']][$a['id']]['pxeIP'].';';
                                    } else {
                                        unset($config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['filename']);
                                        unset($config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['next-server']);
                                    }
                                }
                                $i++;
                            }
                            $fp=@fopen($file,'w');
                            $write= @fwrite($fp,$this->assembleDhcpConfig($config,$k));
                            if ($write) fclose($fp);
                            else $tempBad[] = 'Could not write DHCP file '.$fileErrorOutput.' at DHCP server: '.$v['ip'] . ':' . $v['port'];
                        } else $tempBad[] = 'Could not process DHCP file '.$fileErrorOutput.' at DHCP server: '.$v['ip'] . ':' . $v['port'];
                    } else $tempBad[] = 'Could not open DHCP file '.$fileErrorOutput.' at DHCP server: '.$v['ip'] . ':' . $v['port'];
                    if ($i>0 and !isset($tempBad)) @ssh2_exec($ssh2,$v['startCmd'].' &');
                } else $tempBad[] = 'Could login to DHCP server: '.$v['ip'] . ':' . $v['port'];
            } else $tempBad[] = 'Could not connect to DHCP server: '.$v['ip'] . ':' . $v['port'];
            if (isset($tempBad) and isset($bad)) {
                $bad=array_merge($bad,$tempBad);
            } else if (isset($tempBad) and !isset($bad)) {
                $bad=$tempBad;
            }
        }
        if (isset($bad)) return implode(' ',$bad);
        return $removeArray;
    }
    public function PXEFiles() {
        foreach($this->PXEData as $k=>$v) {
            if ($v['publickey']=="Y") {

                # https://github.com/easy-wi/developer/issues/70
                $sshkey=removePub($v['keyname']);
                $pubkey=EASYWIDIR . '/keys/'.$sshkey.'.pub';
                $key=EASYWIDIR . '/keys/'.$sshkey;

                $ssh2=(file_exists($pubkey) and file_exists($key)) ? @ssh2_connect($v['ip'],$v['port'], array('hostkey' => 'ssh-rsa')) : false;
            } else {
                $ssh2= @ssh2_connect($v['ip'],$v['port']);
            }
            if ($ssh2==true) {
                $connect_ssh2=($v['publickey'] == 'Y' and isset($pubkey,$key)) ? @ssh2_auth_pubkey_file($ssh2,$v['user'],$pubkey,$key) : @ssh2_auth_password($ssh2,$v['user'],$v['pass']);
                if ($connect_ssh2==true) {
                    $sftp=ssh2_sftp($ssh2);
                    foreach($v['actions'] as $a) {
                        $extraSlash=(substr($v['PXEFolder'],-1) != '/' and strlen($v['PXEFolder'])>0) ? '/' : '';
                        $pathWithPXEMac=$v['PXEFolder'].$extraSlash.'01-'.str_replace(':','-',$this->ID[$a['type']][$a['id']]['mac']);
                        $file=(substr($v['PXEFolder'],0,1) == '/') ? 'ssh2.sftp://'.$sftp.$pathWithPXEMac : 'ssh2.sftp://'.$sftp.'/home/'.$v['user']. '/' . $pathWithPXEMac;
                        $fileWithPath=(substr($v['PXEFolder'],0,1) == '/') ? $pathWithPXEMac : '/home/'.$v['user']. '/' . $pathWithPXEMac;
                        if (in_array($a['action'], array('dl','rt','md'))) {
                            @ssh2_sftp_unlink($sftp,$fileWithPath);
                        } else if (in_array($a['action'], array('ri','rc'))) {
                            $query=$this->sql->prepare("SELECT `pxelinux` FROM `resellerimages` WHERE `id`=? AND `active`='Y' LIMIT 1");
                            $query->execute(array($a['imageID']));
                            $pxeconfig=$query->fetchColumn();
                            if (strlen($pxeconfig)>0) {
                                $newPass=passwordgenerate(12);
                                $pxeconfig=str_replace('%rescuepass%',$newPass,$pxeconfig);
                                if ($a['type'] == 'dedicated') {
                                    $query=$this->sql->prepare("UPDATE `rootsDedicated` SET `initialPass`=AES_ENCRYPT(?,?),`pxeID`=? WHERE `id`=? LIMIT 1");
                                    $query->execute(array($newPass,$this->aeskey,$k,$a['id']));
                                } else {
                                    $query=$this->sql->prepare("UPDATE `virtualcontainer` SET `pass`=AES_ENCRYPT(?,?),`pxeID`=? WHERE `id`=? LIMIT 1");
                                    $query->execute(array($newPass,$this->aeskey,$k,$a['id']));
                                }
                                $fp=@fopen($file,'w');
                                $write= @fwrite($fp,$pxeconfig);
                                if ($write) fclose($fp);
                                else $tempBad[] = 'Could not write PXE file '.$fileWithPath.' at PXE server: '.$v['ip'] . ':' . $v['port'];
                            } else $tempBad[] = 'pxefile template empty for imageID: '.$a['imageID'];
                        }
                    }
                } else $tempBad[] = 'Could login to PXE server: '.$v['ip'] . ':' . $v['port'];
            } else $tempBad[] = 'Could not connect to PXE server: '.$v['ip'] . ':' . $v['port'];
            if (isset($tempBad) and isset($bad)) $bad=array_merge($bad,$tempBad);
            else if (isset($tempBad) and !isset($bad)) $bad=$tempBad;
        }
        if (isset($bad)) return implode(' ',$bad);
        return true;
    }
    public function startStop(){
        foreach ($this->startStop as $a) {
            $postParams = array();
            $file = '';
            $requestString=($a['action'] == 're') ? $this->ID['dedicated'][$a['id']]['apiRequestRestart'] : $this->ID['dedicated'][$a['id']]['apiRequestStop'];
            $apiPath=str_replace(array('http://','https://',':8080',':80',':443'),'',$this->ID['dedicated'][$a['id']]['apiURL']);
            $ex=preg_split("/\//",$apiPath,-1,PREG_SPLIT_NO_EMPTY);
            $i = 1;
            while (count($ex)>$i) {
                $file.='/'. $ex[$i];
                $i++;
            }
            $file.='/';
            if($this->ID['dedicated'][$a['id']]['apiRequestType'] == 'G') {
                $file.=$requestString;
            } else {
                foreach (explode('&',str_replace(array('&amp;','?'), array('&',''),$requestString)) as $param) {
                    $ex=explode('=',$param);
                    if (isset($ex[1])) $postParams[$ex[0]] = $ex[1];
                }
            }
            webhostRequest($ex[0],'easy-wi.com',$file,$postParams,($this->ID['dedicated'][$a['id']]['https'] == 'Y') ? 443 : 80);
        }
        return true;
    }
    private function parseDhcpConfig ($dhcpConfig) {
        $config = array();
        $doNotTouch = array();
        $splitConfig=preg_split('/\n/',str_replace("\r",'',$dhcpConfig),-1,PREG_SPLIT_NO_EMPTY);
        foreach ($splitConfig as $split) {
            if (isset($subnetStart) and isset($subnet)){
                if (isset($hostStart,$host)){
                    if (strpos($split,'}')!==false) {
                        unset($hostStart,$host);
                    } else {
                        $cleanedLine=preg_replace('/^[\s+]{1,}(.*?)$/','$1',preg_replace('/\s+/',' ',$split));
                        if (strpos($split,'#')!==false) {
                            $config[$subnet][$host]['comment'][] = $cleanedLine;
                        } else {
                            $ex=explode(' ',$cleanedLine);
                            $v=$ex[count($ex)-1];
                            unset($ex[count($ex)-1]);
                            $k=implode(' ',$ex);
                            $config[$subnet][$host][$k] = $v;
                        }
                    }
                } else if (preg_match('/^(\s+|)host[\s+]{1,}[\w\-\_]{1,}[\s+]{1,}[\{]$/',$split)) {
                    $hostStart = true;
                    $host=preg_replace('/\s+/','',preg_replace('/host[\s+]{1,}(.*?)[\s+]{1,}[\{]/','$1',$split,-1));
                    $config[$subnet][$host] = array();
                } else if (strpos($split,'}')!==false) {
                    unset($subnetStart,$subnet);
                }
            } else if (preg_match('/^[\s+]{0,}subnet[\s+]{1,}[\d]{1,3}.[\d]{1,3}.[\d]{1,3}.[0][\s+]{1,}netmask[\s+]{1,}[\d]{1,3}.[\d]{1,3}.[\d]{1,3}\.[0][\s+]{0,}[\{]$/',$split)) {
                $subnetStart = true;
                $subnet=preg_replace('/^[\s+]{0,}subnet[\s+]{1,}(.*)[\s+]{1,}netmask[\s+]{1,}[\d]{1,3}.[\d]{1,3}.[\d]{1,3}\.[0][\s+]{0,}[\{]$/','\1',$split);
            } else {
                $doNotTouch[] = $split;
            }
        }
        return array('raw' => $doNotTouch,'subnet' => $config);
    }
    private function assembleDhcpConfig ($array,$id) {
        $config = '';
        foreach($array['raw'] as $l) {
            $config.=$l."\r\n";
        }
        foreach($array['subnet'] as $subnets=>$hosts) {
            $config.="subnet ${subnets} netmask ".$this->dhcpData[$id]['netmask']." {\r\n";
            $config.=str_replace('%subnet-mask%',$this->dhcpData[$id]['netmask'],$this->dhcpData[$id]['subnetOptions'])."\r\n";
            foreach ($hosts as $host=>$values) {
                $config.="  host ${host} {\r\n";
                foreach ($values as $opt=>$val) {
                    $config.="      $opt $val\r\n";
                }
                $config.="  }\r\n";
            }
            $config.="}\r\n";
        }
        return $config;
    }
    public function VMWare(){
        foreach ($this->vmwareHosts as $hID =>$h) {
            if ($this->vmwareHosts[$hID]['vmIDs']['publickey'] == 'Y') {

                # https://github.com/easy-wi/developer/issues/70
                $sshkey=removePub($this->vmwareHosts[$hID['hostID']]['vmIDs']['keyname']);
                $pubkey=EASYWIDIR . '/keys/'.$sshkey.'.pub';
                $key=EASYWIDIR . '/keys/'.$sshkey;

                $ssh2=(file_exists($pubkey) and file_exists($key)) ? @ssh2_connect($this->vmwareHosts[$hID]['vmIDs']['ip'],$this->vmwareHosts[$hID]['vmIDs']['dport'], array('hostkey' => 'ssh-rsa')) : false;
            } else {
                $ssh2=@ssh2_connect($this->vmwareHosts[$hID]['vmIDs']['ip'],$this->vmwareHosts[$hID]['vmIDs']['dport']);
            }
            if ($ssh2==true) {
                $connectSSH2=($this->vmwareHosts[$hID]['vmIDs']['publickey'] == 'Y' and isset($pubkey,$key)) ? @ssh2_auth_pubkey_file($ssh2,$this->vmwareHosts[$hID]['vmIDs']['duser'],$pubkey,$key) : @ssh2_auth_password($ssh2,$this->vmwareHosts[$hID]['vmIDs']['duser'],$this->vmwareHosts[$hID]['vmIDs']['dpass']);
                if ($connectSSH2==true) {
                    print "Prepare: unregister any invalid vms\r\n";
                    $cmd='vim-cmd vmsvc/getallvms | grep \'Skipping\' | while read line; do vim-cmd vmsvc/unregister `echo $line | grep \'Skipping\' |  awk -F "\'" \'{print $2}\'`; done';
                    $this->execCmd($cmd,$ssh2);
                    foreach ($h['actions'] as $v) {
                        $dir='/vmfs/volumes/'.$this->ID['vmware'][$v['id']]['mountpoint']. '/' . $this->ID['vmware'][$v['id']]['hostname'];
                        if(in_array($v['action'], array('md','dl','st','ri','re'))) {
                            print "Step 1: Stop and remove if needed\r\n";
                            $cmd="i(){ echo `vim-cmd vmsvc/getallvms 2> /dev/null | grep -v 'Skipping' | grep '".$this->ID['vmware'][$v['id']]['hostname'].".vmx' | awk '{print $1}'`;}; o(){ vim-cmd vmsvc/power.off `i ".$this->ID['vmware'][$v['id']]['hostname']."`; vim-cmd vmsvc/unregister `i ".$this->ID['vmware'][$v['id']]['hostname']."`;}; o;";
                            if (in_array($v['action'], array('dl','ri','re'))) $cmd.=" rm -rf /vmfs/volumes/".$this->ID['vmware'][$v['id']]['mountpoint']. '/' . $this->ID['vmware'][$v['id']]['hostname'];
                            $this->execCmd($cmd,$ssh2);
                        }
                        if (in_array($v['action'], array('md','ad','ri','re'))) {
                            $harddisk=($this->ID['vmware'][$v['id']]['distro'] == 'windows7srv-64') ? 'lsisas1068' : 'lsilogic';
                            $sftp=ssh2_sftp($ssh2);
                            ssh2_sftp_mkdir($sftp,rtrim($dir, '/'),0774,true);
                            $fp=fopen('ssh2.sftp://'.$sftp.'/vmfs/volumes/'.$this->ID['vmware'][$v['id']]['mountpoint']. '/' . $this->ID['vmware'][$v['id']]['hostname']. '/' . $this->ID['vmware'][$v['id']]['hostname'].'.vmx','w');
                             if ($fp) {
                                 $vmxFile='.encoding = "UTF-8"'."\n";
                                 $vmxFile.='config.version = "8"'."\n";
                                 $vmxFile.='displayName = "'.$this->ID['vmware'][$v['id']]['hostname'].'"'."\n";
                                 $vmxFile.='ethernet0.present = "TRUE"'."\n";
                                 $vmxFile.='ethernet0.virtualDev = "e1000"'."\n";
                                 $vmxFile.='ethernet0.networkName = "VM Network"'."\n";
                                 $vmxFile.='ethernet0.addressType = "static"'."\n";
                                 $vmxFile.='ethernet0.Address = "'.$this->ID['vmware'][$v['id']]['mac'].'"'."\n";
                                 $vmxFile.='extendedConfigFile = "'.$this->ID['vmware'][$v['id']]['hostname'].'.vmxf"'."\n";
                                 $vmxFile.='floppy0.clientDevice = "TRUE"'."\n";
                                 $vmxFile.='floppy0.fileName = ""'."\n";
                                 $vmxFile.='floppy0.present = "TRUE"'."\n";
                                 $vmxFile.='floppy0.startConnected = "FALSE"'."\n";
                                 $vmxFile.='guestOS = "'.$this->ID['vmware'][$v['id']]['guestos'].'"'."\n";
                                 $vmxFile.='ide1:0.present = "TRUE"'."\n";
                                 $vmxFile.='ide1:0.clientDevice = "TRUE"'."\n";
                                 $vmxFile.='ide1:0.deviceType = "cdrom-raw"'."\n";
                                 $vmxFile.='ide1:0.startConnected = "FALSE"'."\n";
                                 $vmxFile.='memsize = "'.$this->ID['vmware'][$v['id']]['ram'].'"'."\n";
                                 $vmxFile.='numvcpus = "'.$this->ID['vmware'][$v['id']]['cores'].'"'."\n";
                                 $vmxFile.='nvram = "'.$this->ID['vmware'][$v['id']]['hostname'].'.nvram"'."\n";
                                 $vmxFile.='pciBridge0.present = "TRUE"'."\n";
                                 $vmxFile.='pciBridge4.present = "TRUE"'."\n";
                                 $vmxFile.='pciBridge4.virtualDev = "pcieRootPort"'."\n";
                                 $vmxFile.='pciBridge4.functions = "8"'."\n";
                                 $vmxFile.='pciBridge5.present = "TRUE"'."\n";
                                 $vmxFile.='pciBridge5.virtualDev = "pcieRootPort"'."\n";
                                 $vmxFile.='pciBridge5.functions = "8"'."\n";
                                 $vmxFile.='pciBridge6.present = "TRUE"'."\n";
                                 $vmxFile.='pciBridge6.virtualDev = "pcieRootPort"'."\n";
                                 $vmxFile.='pciBridge6.functions = "8"'."\n";
                                 $vmxFile.='pciBridge7.present = "TRUE"'."\n";
                                 $vmxFile.='pciBridge7.virtualDev = "pcieRootPort"'."\n";
                                 $vmxFile.='pciBridge7.functions = "8"'."\n";
                                 $vmxFile.='powerType.powerOff = "soft"'."\n";
                                 $vmxFile.='powerType.powerOn = "hard"'."\n";
                                 $vmxFile.='powerType.suspend = "hard"'."\n";
                                 $vmxFile.='powerType.reset = "soft"'."\n";
                                 $vmxFile.='sched.cpu.min = "'.$this->ID['vmware'][$v['id']]['minmhz'].'"'."\n";
                                 $vmxFile.='sched.cpu.units = "mhz"'."\n";
                                 $vmxFile.='sched.cpu.shares = "normal"'."\n";
                                 $vmxFile.='sched.cpu.max = "'.$this->ID['vmware'][$v['id']]['maxmhz'].'"'."\n";
                                 $vmxFile.='sched.cpu.affinity = "all"'."\n";
                                 $vmxFile.='sched.mem.max = "'.$this->ID['vmware'][$v['id']]['maxram'].'"'."\n";
                                 $vmxFile.='sched.mem.minsize = "'.$this->ID['vmware'][$v['id']]['minram'].'"'."\n";
                                 $vmxFile.='sched.mem.shares = "normal"'."\n";
                                 $vmxFile.='scsi0.present = "TRUE"'."\n";
                                 $vmxFile.='scsi0.sharedBus = "none"'."\n";
                                 $vmxFile.='scsi0.virtualDev = "'.$harddisk.'"'."\n";
                                 $vmxFile.='scsi0:0.present = "TRUE"'."\n";
                                 $vmxFile.='scsi0:0.fileName = "'.$this->ID['vmware'][$v['id']]['hostname'].'.vmdk"'."\n";
                                 $vmxFile.='scsi0:0.deviceType = "scsi-hardDisk"'."\n";
                                 $vmxFile.='uuid.location = "56 4d ce 4e ce 1e 51 4b-3f 61 d8 45 c0 c8 93 90"'."\n";
                                 $vmxFile.='uuid.bios = "56 4d ce 4e ce 1e 51 4b-3f 61 d8 45 c0 c8 93 90"'."\n";
                                 $vmxFile.='vc.uuid = "52 9c 06 a8 19 e6 40 c0-61 1b 6e 23 34 c8 c7 f9"'."\n";
                                 $vmxFile.='virtualHW.productCompatibility = "hosted"'."\n";
                                 $vmxFile.='virtualHW.version = "7"'."\n";
                                 $vmxFile.='vmci0.present = "TRUE"'."\n";
                                 $vmxFile.='uuid.action = "create"'."\n";
                                 $vmxFile.='bios.bootOrder = "ethernet0"'."\n";
                                 if(fwrite($fp,$vmxFile)) {
                                     print "Step 2: Create/edit vmx file (OK)\r\n";
                                 } else {
                                     print "Step 2: Create/edit vmx file (FAILED)\r\n";
                                 }
                                 unset($fp);
                            } else {
                                 print 'could not open: /vmfs/volumes/'.$this->ID['vmware'][$v['id']]['mountpoint']. '/' . $this->ID['vmware'][$v['id']]['hostname']. '/' . $this->ID['vmware'][$v['id']]['hostname'].'.vmx'."\r\n";
                            }
                            if(is_resource($sftp)) fclose($sftp);
                            else unset ($sftp);
                            print "Step 3: create volume\r\n";
                            $cmd="a() { vmkfstools -c ".$this->ID['vmware'][$v['id']]['hddsize']." -a lsilogic -d thin /vmfs/volumes/".$this->ID['vmware'][$v['id']]['mountpoint']. '/' . $this->ID['vmware'][$v['id']]['hostname']. '/' . $this->ID['vmware'][$v['id']]['hostname'].".vmdk >/dev/null 2>&1;}; a";
                            $this->execCmd($cmd,$ssh2);
                        } else {
                            print "Step 2-3: skipped as not required\r\n";
                        }
                        if (in_array($v['action'], array('md','ad','re','ri','rc'))) {
                            print "Step 4: Start VM\r\n";
                            $cmd="a() { vim-cmd vmsvc/power.on `vim-cmd solo/registervm /vmfs/volumes/".$this->ID['vmware'][$v['id']]['mountpoint']. '/' . $this->ID['vmware'][$v['id']]['hostname']. '/' . $this->ID['vmware'][$v['id']]['hostname'].".vmx 2> /dev/null` >/dev/null 2>&1;}; a&";
                            $this->execCmd($cmd,$ssh2);
                        } else {
                            print "Step 4: skipped as not required\r\n";
                        }
                    }
                    if (is_resource($ssh2)) @ssh2_exec($ssh2,'exit');
                } else print "No connection\r\n";
            }
        }
        return true;
    }
    private function execCmd($cmd,$ssh2) {
        $return=@ssh2_exec($ssh2,$cmd);
        stream_set_blocking($return,true);
        if ($return) print "ok: ${cmd}\r\n";
        else print "failed: ${cmd}\r\n";
        print 'Reply from ESX(i) host was: '.stream_get_contents($return)."\r\n";
        fclose($return);
    }
}