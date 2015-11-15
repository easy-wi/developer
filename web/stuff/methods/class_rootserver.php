<?php

/**
 * File: class_rootserver.php.
 * Author: Ulrich Block
 * Date: 03.10.12
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

if (!class_exists('Net_SSH2')) {
    include(EASYWIDIR . '/third_party/phpseclib/Net/SSH2.php');
}

if (!class_exists('Crypt_RSA')) {
    include(EASYWIDIR . '/third_party/phpseclib/Crypt/RSA.php');
}

if (!class_exists('Net_SFTP')) {
    include(EASYWIDIR . '/third_party/phpseclib/Net/SFTP.php');
}

class rootServer {

    // Data
    protected $sql, $aeskey, $httpConnect, $tempID, $netmask, $extraData, $ID = array(), $type= array(), $dhcpData= array(), $PXEData= array(), $startStop= array(), $vmwareHosts = array();

    function __destruct() {
        unset($this->sql, $this->aeskey, $this->netmask, $this->ID, $this->type, $this->httpConnect, $this->dhcpData, $this->startStop, $this->vmwareHosts);
    }

    function __construct($aeskey) {

        // retrieve global vars
        global $sql;

        // define internal vars
        $this->sql = $sql;
        $this->aeskey = $aeskey;

        // check if curl is chosen and available and initiate cURL-Session else fallback to fsockopen
        $this->httpConnect = (function_exists('curl_init')) ? 'curl' : 'fsockopen';

        return true;
    }

    public function rootServer ($ID, $action, $type = 'dedicated', $extraData = null) {

        $this->tempID = $ID;
        $this->ID[$type][$ID] = array();
        $this->ID[$type][$ID]['action'] = $action;
        $this->type = $type;
        $this->extraData = $extraData;

        $imageID = (isset($extraData['imageID'])) ? $extraData['imageID'] : 0;
        $hostID = 0;
        $userID = 0;
        $resellerID = 0;

        if (isid($imageID, 10)) {
            $query = $this->sql->prepare("SELECT `distro`,`bitversion` FROM `resellerimages` WHERE `id`=? AND `active`='Y' LIMIT 1");
            $query->execute(array($imageID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $distro = $row['distro'];
                $guestos = ($row['bitversion'] == '32') ? $row['distro'] : $row['distro'] . '-' . $row['bitversion'];
            }
        }

        // get Root Data from DB
        if ($this->type == 'dedicated') {
            $query = $this->sql->prepare("SELECT d.*,u.`cname` FROM `rootsDedicated` d LEFT JOIN `userdata` u ON d.`userID`=u.`id` WHERE d.`dedicatedID`=? LIMIT 1");
            $query->execute(array($this->tempID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $this->ID[$type][$ID]['useDHCP'] = $row['useDHCP'];
                $this->ID[$type][$ID]['hostname'] = 'dedi-' . $ID;
                $this->ID[$type][$ID]['usePXE'] = $row['usePXE'];
                $this->ID[$type][$ID]['pxeID'] = $row['pxeID'];
                $this->ID[$type][$ID]['pxeIP'] = '';
                $this->ID[$type][$ID]['mac'] = $row['mac'];
                $this->ID[$type][$ID]['ip'] = $row['ip'];
                $this->ID[$type][$ID]['restart'] = $row['restart'];
                $this->ID[$type][$ID]['apiRequestType'] = $row['apiRequestType'];
                $this->ID[$type][$ID]['apiRequestRestart'] = $row['apiRequestRestart'];
                $this->ID[$type][$ID]['apiRequestStop'] = $row['apiRequestStop'];
                $this->ID[$type][$ID]['https'] = $row['https'];
                $this->ID[$type][$ID]['apiURL'] = $row['apiURL'];
                $userID = $row['userID'];
                $resellerID = $row['resellerID'];
            }

            // Get VMware data
        } else if ($this->type == 'vmware') {

            $query = $this->sql->prepare("SELECT c.*,u.`id` AS `userID`,u.`cname`,h.`cores` AS `hcore`,h.`esxi`,h.`id` AS `hostID`,h.`ip` AS `hip`,AES_DECRYPT(h.`port`,:aeskey) AS `dport`,AES_DECRYPT(h.`user`,:aeskey) AS `duser`,AES_DECRYPT(h.`pass`,:aeskey) AS `dpass`,h.`publickey`,h.`keyname` FROM `virtualcontainer` c INNER JOIN `userdata` u ON c.`userid`=u.`id` INNER JOIN `virtualhosts` h ON c.`hostid`=h.`id` WHERE c.`id`=:vmID LIMIT 1");
            $query->execute(array(':aeskey' => $this->aeskey,':vmID' => $this->tempID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

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
                $this->ID[$type][$ID]['ram'] = 1024 * $row['ram'];
                $this->ID[$type][$ID]['minram'] = 1024*$row['minram'];
                $this->ID[$type][$ID]['maxram'] = 1024*$row['maxram'];
                $this->ID[$type][$ID]['minmhz'] = $row['cores']*$row['minmhz'];
                $this->ID[$type][$ID]['maxmhz'] = $row['cores']*$row['maxmhz'];
                $this->ID[$type][$ID]['hddsize'] = $row['hddsize'].'GB';
                $this->ID[$type][$ID]['mac'] = $row['mac'];
                $this->ID[$type][$ID]['pxeID'] = $row['pxeID'];
                $this->ID[$type][$ID]['hostname'] = 'vmware-' . $ID;
                $this->ID[$type][$ID]['mac'] = $row['mac'];
                $this->ID[$type][$ID]['ip'] = $row['ip'];
                $this->ID[$type][$ID]['usePXE'] = 'Y';
                $this->ID[$type][$ID]['restart'] = 'Y';
                $this->ID[$type][$ID]['distro'] = (isset($distro)) ? $distro : '';
                $this->ID[$type][$ID]['guestos'] = (isset($guestos)) ? $guestos : '';
                $hostID = $row['hostid'];
                $userID = $row['userid'];
                $resellerID = $row['resellerid'];
                $this->vmwareHosts[$row['hostID']]['actions'][] = array('action' => $action, 'id' => $ID);
            }
        }

        if (!isset($row['ip'])) {
            return 'Database Error: Could not find VM or ESX(i) host for ID: ' . $this->tempID;
        }

        if (!in_array($action, array('md', 'ad', 'dl', 'rp')) and ($this->type == 'vmware' or ($this->type == 'dedicated' and $this->ID[$type][$ID]['usePXE'] == 'Y')) and !isid($imageID, 10)) {

            unset($this->ID[$type][$ID]);
            return 'Image Error: No imageID defined for Server with ID: ' . $this->tempID;

        } else if (!in_array($action, array('md', 'ad', 'dl', 'rp')) and ($this->type == 'vmware' or ($this->type == 'dedicated' and $this->ID[$type][$ID]['usePXE'] == 'Y')) and !isset($guestos)) {

            unset($this->ID[$type][$ID]);
            return 'Image Error: Cannot find image with imageID ' . $imageID . ' defined for Server with ID: ' . $this->tempID;

        } else if (!in_array($action, array('re', 'st'))) {

            // get DHCP Data from DB
            if ($action != 'rp' and ($this->type == 'vmware' or ($this->type == 'dedicated' and $this->ID[$type][$ID]['useDHCP'] == 'Y'))) {

                $query = $this->sql->prepare("SELECT s.*,d.*,AES_DECRYPT(d.`port`,:aeskey) AS `dport`,AES_DECRYPT(d.`user`,:aeskey) AS `duser`,AES_DECRYPT(d.`pass`,:aeskey) AS `dpass` FROM `rootsIP4` i INNER JOIN `rootsSubnets` s ON i.`subnetID`=s.`subnetID` INNER JOIN `rootsDHCP` d ON s.`dhcpServer`=d.`id` WHERE i.`ip`=:ip AND d.`active`='Y' LIMIT 1");
                $query->execute(array(':aeskey' => $this->aeskey, ':ip' => $this->ID[$type][$ID]['ip']));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                    $foundDHCP = true;

                    $modID = $row['id'];

                    if (!isset($this->dhcpData[$row['id']])) {

                        $this->dhcpData[$row['id']]['ip'] = $row['ip'];
                        $this->dhcpData[$row['id']]['port'] = $row['dport'];
                        $this->dhcpData[$row['id']]['user'] = $row['duser'];
                        $this->dhcpData[$row['id']]['pass'] = $row['dpass'];
                        $this->dhcpData[$row['id']]['publickey'] = $row['publickey'];
                        $this->dhcpData[$row['id']]['keyname'] = $row['keyname'];
                        $this->dhcpData[$row['id']]['startCmd'] = $row['startCmd'];
                        $this->dhcpData[$row['id']]['dhcpFile'] = $row['dhcpFile'];

                        $this->dhcpData[$row['id']]['subnets'][$row['subnet']] = array('netmask' => $row['netmask'], 'subnetOptions' => $row['subnetOptions']);

                    }

                    if ($row['vlan'] == 'Y') {
                        $this->ID[$type][$ID]['vlan'] = $row['vlanName'];
                    }

                    $this->ID[$type][$ID]['subnet'] = $row['subnet'];

                    $this->dhcpData[$row['id']]['actions'][] = array('action' => $action, 'id' => $ID, 'type' => $type, 'imageID' => $imageID, 'hostID' => $hostID, 'userID' => $userID, 'resellerID' => $resellerID);

                }

                if ($action == 'md' and isset($this->extraData['oldip']) and $this->extraData['oldip'] != $this->ID[$type][$ID]['ip']) {

                    $query = $this->sql->prepare("SELECT s.*,d.*,AES_DECRYPT(d.`port`,:aeskey) AS `dport`,AES_DECRYPT(d.`user`,:aeskey) AS `duser`,AES_DECRYPT(d.`pass`,:aeskey) AS `dpass` FROM `rootsIP4` i INNER JOIN `rootsSubnets` s ON i.`subnetID`=s.`subnetID` INNER JOIN `rootsDHCP` d ON s.`dhcpServer`=d.`id` WHERE i.`ip`=:ip AND d.`active`='Y' LIMIT 1");
                    $query->execute(array(':aeskey' => $this->aeskey, ':ip' => $this->extraData['oldip']));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

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
                        }

                        if ($row['vlan'] == 'Y') {
                            $this->ID[$type][$ID]['oldVlan'] = $row['vlanName'];
                        }

                        $this->ID[$type][$ID]['oldSubnet'] = $row['subnet'];

                        if (isset($modID) and $modID != $row['id']) {
                            $this->dhcpData[$row['id']]['actions'][] = array('action' => 'del', 'id' => $ID, 'type' => $type, 'imageID' => $imageID, 'hostID' => $hostID, 'userID' => $userID, 'resellerID' => $resellerID);
                        }
                    }
                }

                if (!isset($foundDHCP)) {
                    unset($this->ID[$type][$ID]);
                    return 'Database Error: Could not find DHCP Server for IP: ' . $this->extraData['oldip'];
                }
            }

            // Get PXE Data
            if ($this->ID[$type][$ID]['usePXE'] == 'Y') {

                if (isid($this->ID[$type][$ID]['pxeID'], 10)) {
                    $query = $this->sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass` FROM `rootsPXE` WHERE `active`='Y' AND `id`=:pxeID LIMIT 1");
                    $query->execute(array(':aeskey' => $this->aeskey, ':pxeID' => $this->ID[$type][$ID]['pxeID']));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                        $foundPXE = true;

                        $this->ID[$type][$ID]['pxeIP'] = $row['ip'];
                        $this->ID[$type][$ID]['pxeID'] = $row['id'];

                        if (!isset($this->PXEData[$row['id']])){
                            $this->PXEData[$row['id']]['ip'] = $row['ip'];
                            $this->PXEData[$row['id']]['port'] = $row['dport'];
                            $this->PXEData[$row['id']]['user'] = $row['duser'];
                            $this->PXEData[$row['id']]['pass'] = $row['dpass'];
                            $this->PXEData[$row['id']]['publickey'] = $row['publickey'];
                            $this->PXEData[$row['id']]['keyname'] = $row['keyname'];
                            $this->PXEData[$row['id']]['PXEFolder'] = $row['PXEFolder'];
                        }

                        $this->PXEData[$row['id']]['actions'][] = array('action' => $action, 'id' => $ID,'type' => $type,'imageID' => $imageID, 'hostID' => $hostID, 'userID' => $userID, 'resellerID' => $resellerID);
                    }
                }

                if ((!isset($foundPXE) or !isip($this->ID[$type][$ID]['pxeIP'], 'ip4')) and $action != 'dl') {
                    $query = $this->sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass` FROM `rootsPXE` WHERE `active`='Y' ORDER BY RAND() LIMIT 1");
                    $query->execute(array(':aeskey' => $this->aeskey));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                        $foundPXE = true;

                        $this->ID[$type][$ID]['pxeIP'] = $row['ip'];
                        $this->ID[$type][$ID]['pxeID'] = $row['id'];

                        if (!isset($this->PXEData[$row['id']])){
                            $this->PXEData[$row['id']]['ip'] = $row['ip'];
                            $this->PXEData[$row['id']]['port'] = $row['dport'];
                            $this->PXEData[$row['id']]['user'] = $row['duser'];
                            $this->PXEData[$row['id']]['pass'] = $row['dpass'];
                            $this->PXEData[$row['id']]['publickey'] = $row['publickey'];
                            $this->PXEData[$row['id']]['keyname'] = $row['keyname'];
                            $this->PXEData[$row['id']]['PXEFolder'] = $row['PXEFolder'];
                        }

                        $this->PXEData[$row['id']]['actions'][] = array('action' => $action, 'id' => $ID, 'type' => $type, 'imageID' => $imageID, 'hostID' => $hostID, 'userID' => $userID, 'resellerID' => $resellerID);
                    }
                }

                if (!isset($foundPXE) and $action != 'dl') {
                    unset($this->ID[$type][$ID]);
                    return 'Database Error: Could not find PXE Server';
                }
            }
        }

        if ($this->ID[$type][$ID]['restart'] == 'A' and $this->type == 'dedicated' and $action != 'rp') {

            $this->startStop[] = array(
                'action' => ((isset($this->extraData['oldactive']) and $this->extraData['oldactive'] == 'Y') or in_array($action, array('ad','st','dl'))) ? 'st' : 're','id' => $ID
            );

        } else if ($this->type == 'dedicated' and $action != 'rp') {
            return 'Restart not allowed for Server with ID: ' . $this->tempID;
        }

        return true;

    }

    public function dhcpFiles () {

        foreach ($this->dhcpData as $k => $v) {

            if (count($v['actions']) > 0) {

                unset($tempBad, $changed);

                # https://github.com/easy-wi/developer/issues/70
                $privateKey = EASYWIDIR . '/keys/' . removePub($v['keyname']);

                $sftpObject = new Net_SFTP($v['ip'], $v['port']);

                if (file_exists($privateKey) and $sftpObject->error === false) {

                    if ($v['publickey'] != 'N') {

                        $ssh2Pass = new Crypt_RSA();

                        if ($v['publickey'] == 'B') {
                            $ssh2Pass->setPassword($v['pass']);
                        }

                        $ssh2Pass->loadKey(file_get_contents($privateKey));

                    } else {
                        $ssh2Pass = $v['pass'];
                    }

                    if ($sftpObject->login($v['user'], $ssh2Pass)) {

                        $file = (substr($v['dhcpFile'], 0, 1) == '/') ? $v['dhcpFile'] : '/home/' . $v['user']. '/' . $v['dhcpFile'];

                        $buffer = $sftpObject->get($file);
                        $config = $this->parseDhcpConfig(str_replace(array("\0", "\b", "\r", "\Z"), '', $buffer));

                        if (is_array($config)) {
                            foreach ($v['actions'] as $a) {

                                if ($a['action'] == 'del') {

                                    if (isset($config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']])) {

                                        unset($config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]);
                                        $changed = true;

                                    } else if (isset($config['vlan'][$this->ID[$a['type']][$a['id']]['oldVlan']][$this->ID[$a['type']][$a['id']]['oldSubnet']][$this->ID[$a['type']][$a['id']]['hostname']])) {

                                        unset($config['vlan'][$this->ID[$a['type']][$a['id']]['oldVlan']][$this->ID[$a['type']][$a['id']]['oldSubnet']][$this->ID[$a['type']][$a['id']]['hostname']]);
                                        $changed = true;

                                    } else if (isset($config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']])) {

                                        unset($config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]);
                                        $changed = true;

                                    } else if (isset($config['subnet'][$this->ID[$a['type']][$a['id']]['oldSubnet']][$this->ID[$a['type']][$a['id']]['hostname']])) {

                                        unset($config['subnet'][$this->ID[$a['type']][$a['id']]['oldSubnet']][$this->ID[$a['type']][$a['id']]['hostname']]);
                                        $changed = true;

                                    }

                                } else if (isset($this->ID[$a['type']][$a['id']])) {

                                    if (isset($this->ID[$a['type']][$a['id']]['vlan'])) {

                                        if (!isset($config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['hardware ethernet']) or $config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['hardware ethernet'] != $this->ID[$a['type']][$a['id']]['mac'].';') {

                                            $config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['hardware ethernet'] = $this->ID[$a['type']][$a['id']]['mac'] . ';';

                                            $changed = true;

                                        }

                                        if (!isset($config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['fixed-address']) or $config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['fixed-address'] != $this->ID[$a['type']][$a['id']]['ip'].';') {

                                            $config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['fixed-address'] = $this->ID[$a['type']][$a['id']]['ip'] . ';';

                                            $changed = true;
                                        }

                                        if ($this->ID[$a['type']][$a['id']]['usePXE'] == 'Y' and (!isset($config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['filename']) or $config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['filename'] != 'pxelinux.0;')) {

                                            $config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['filename'] = 'pxelinux.0;';

                                            $changed = true;
                                        }

                                        if ($this->ID[$a['type']][$a['id']]['usePXE'] == 'Y' and (!isset($config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['next-server']) or $config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['next-server'] != $this->ID[$a['type']][$a['id']]['pxeIP'].';')) {

                                            $config['vlan'][$this->ID[$a['type']][$a['id']]['vlan']][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['next-server'] = $this->ID[$a['type']][$a['id']]['pxeIP'] . ';';

                                            $changed = true;
                                        }

                                    } else {

                                        if (!isset($config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['hardware ethernet']) or $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['hardware ethernet'] != $this->ID[$a['type']][$a['id']]['mac'].';') {

                                            $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['hardware ethernet'] = $this->ID[$a['type']][$a['id']]['mac'] . ';';

                                            $changed = true;

                                        }

                                        if (!isset($config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['fixed-address']) or $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['fixed-address'] != $this->ID[$a['type']][$a['id']]['ip'].';') {

                                            $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['fixed-address'] = $this->ID[$a['type']][$a['id']]['ip'] . ';';

                                            $changed = true;
                                        }

                                        if ($this->ID[$a['type']][$a['id']]['usePXE'] == 'Y' and (!isset($config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['filename']) or $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['filename'] != 'pxelinux.0;')) {

                                            $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['filename'] = 'pxelinux.0;';

                                            $changed = true;
                                        }

                                        if ($this->ID[$a['type']][$a['id']]['usePXE'] == 'Y' and (!isset($config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['next-server']) or $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['next-server'] != $this->ID[$a['type']][$a['id']]['pxeIP'].';')) {

                                            $config['subnet'][$this->ID[$a['type']][$a['id']]['subnet']][$this->ID[$a['type']][$a['id']]['hostname']]['next-server'] = $this->ID[$a['type']][$a['id']]['pxeIP'] . ';';

                                            $changed = true;
                                        }
                                    }
                                }
                            }

                            if (isset($changed)) {

                                $sftpObject->put($file, $this->assembleDhcpConfig($config, $k));

                                $sshObject = new Net_SSH2($v['ip'], $v['port']);

                                if ($sshObject->error === false) {

                                    if ($v['publickey'] != 'N') {

                                        $ssh2Pass = new Crypt_RSA();

                                        if ($v['publickey'] == 'B') {
                                            $ssh2Pass->setPassword($v['pass']);
                                        }

                                        $ssh2Pass->loadKey(file_get_contents($privateKey));

                                    } else {
                                        $ssh2Pass = $v['pass'];
                                    }

                                    if ($sshObject->login($v['user'], $ssh2Pass)) {
                                        $sshObject->exec($v['startCmd'] . ' &');
                                    } else {
                                        $tempBad[] = 'Could login to DHCP server: ' . $v['ip'] . ':' . $v['port'];
                                    }
                                } else {
                                    $tempBad[] = 'Could not connect to DHCP server: ' . $v['ip'] . ':' . $v['port'];
                                }
                            }


                        } else {
                            $tempBad[] = 'Could not process DHCP file ' . $file . ' at DHCP server: ' . $v['ip'] . ':' . $v['port'];
                        }
                    } else {
                        $tempBad[] = 'Could not login to DHCP server: ' . $v['ip'] . ':' . $v['port'];
                    }
                } else {
                    $tempBad[] = 'Could not connect to DHCP server: ' . $v['ip'] . ':' . $v['port'];
                }

                if (isset($tempBad) and isset($bad)) {
                    $bad = array_merge($bad, $tempBad);
                } else if (isset($tempBad) and !isset($bad)) {
                    $bad = $tempBad;
                }
            }
        }

        if (isset($bad)) {
            return implode(' ', $bad);
        }

        return true;

    }

    public function PXEFiles() {

        $removeArray = array();

        foreach($this->PXEData as $k => $v) {

            $privateKey = EASYWIDIR . '/keys/' . removePub($v['keyname']);

            $sftpObject = new Net_SFTP($v['ip'], $v['port']);

            if ($sftpObject->error === false) {

                if ($v['publickey'] == 'Y' and file_exists($privateKey)) {

                    $ssh2Pass = new Crypt_RSA();

                    if ($v['publickey'] == 'B') {
                        $ssh2Pass->setPassword($v['pass']);
                    }

                    $ssh2Pass->loadKey(file_get_contents($privateKey));

                } else {
                    $ssh2Pass = $v['pass'];
                }


                if ($sftpObject->login($v['user'], $ssh2Pass)) {
                    foreach($v['actions'] as $a) {

                        $extraSlash = (substr($v['PXEFolder'], -1) != '/' and strlen($v['PXEFolder']) > 0) ? '/' : '';
                        $pathWithPXEMac = $v['PXEFolder'] . $extraSlash . '01-' . str_replace(':', '-', $this->ID[$a['type']][$a['id']]['mac']);

                        $fileWithPath = (substr($v['PXEFolder'], 0, 1) == '/') ? $pathWithPXEMac : '/home/' . $v['user'] . '/' . $pathWithPXEMac;

                        if (in_array($a['action'], array('dl', 'md', 'rp', 'rt'))) {

                            $sftpObject->delete($pathWithPXEMac);

                        } else if (in_array($a['action'], array('ad', 'ri', 'rc'))) {

                            $removeArray[] = array('type' => ($a['type'] == 'dedicated') ? 'de' : 'vs', 'affectedID' => $a['id'], 'name' => $this->ID[$a['type']][$a['id']]['ip'], 'imageID' => $a['imageID'], 'hostID' => $a['hostID'], 'userID' => $a['userID'], 'resellerID' => $a['resellerID'], 'extraData' => array('runAt' => strtotime("+5 minutes")));

                            $query = $this->sql->prepare("SELECT `pxelinux` FROM `resellerimages` WHERE `id`=? AND `active`='Y' LIMIT 1");
                            $query->execute(array($a['imageID']));
                            $pxeconfig = $query->fetchColumn();


                            if (strlen($pxeconfig) > 0) {

                                $newPass = passwordgenerate(12);

                                $pxeconfig = str_replace('%rescuepass%', $newPass, $pxeconfig);

                                if ($a['type'] == 'dedicated') {
                                    $query = $this->sql->prepare("UPDATE `rootsDedicated` SET `initialPass`=AES_ENCRYPT(?,?),`pxeID`=? WHERE `dedicatedID`=? LIMIT 1");
                                    $query->execute(array($newPass, $this->aeskey, $k, $a['id']));
                                } else {
                                    $query = $this->sql->prepare("UPDATE `virtualcontainer` SET `pass`=AES_ENCRYPT(?,?),`pxeID`=? WHERE `id`=? LIMIT 1");
                                    $query->execute(array($newPass, $this->aeskey, $k, $a['id']));
                                }

                                $sftpObject->put($fileWithPath, $pxeconfig);

                            } else {
                                $tempBad[] = 'pxefile template empty for imageID: '.$a['imageID'];
                            }
                        }
                    }
                } else {
                    $tempBad[] = 'Could login to PXE server: ' . $v['ip'] . ':' . $v['port'];
                }
            } else {
                $tempBad[] = 'Could not connect to PXE server: ' . $v['ip'] . ':' . $v['port'];
            }

            if (isset($tempBad) and isset($bad)) {
                $bad = array_merge($bad, $tempBad);
            } else if (isset($tempBad) and !isset($bad)) {
                $bad = $tempBad;
            }
        }

        if (isset($bad)) {
            print_r(implode(' ', $bad));
        }

        return $removeArray;

    }

    public function startStop() {
        foreach ($this->startStop as $a) {
            $postParams = array();
            $file = '';
            $requestString=($a['action'] == 're') ? $this->ID['dedicated'][$a['id']]['apiRequestRestart'] : $this->ID['dedicated'][$a['id']]['apiRequestStop'];
            $apiPath=str_replace(array('http://', 'https://', ':8080', ':80', ':443'), '', $this->ID['dedicated'][$a['id']]['apiURL']);
            $ex=preg_split("/\//", $apiPath,-1,PREG_SPLIT_NO_EMPTY);
            $i = 1;
            $exCount = count($ex);
            while ($exCount > $i) {
                $file .= '/' . $ex[$i];
                $i++;
            }
            $file .= '/';
            if ($this->ID['dedicated'][$a['id']]['apiRequestType'] == 'G') {
                $file .= $requestString;
            } else {
                foreach (explode('&',str_replace(array('&amp;','?'), array('&',''), $requestString)) as $param) {
                    $ex=explode('=', $param);
                    if (isset($ex[1])) $postParams[$ex[0]] = $ex[1];
                }
            }
            webhostRequest($ex[0], 'easy-wi.com', $file, $postParams,($this->ID['dedicated'][$a['id']]['https'] == 'Y') ? 443 : 80);
        }
        return true;
    }

    private function getKeyValue ($line) {
        $line = trim($line);

        $ex = explode(' ', $line);
        $k = $ex[0];
        $count = count($ex);
        $last = $count - 1;

        if (strpos($line, ',') !== false) {

            $i = 1;
            $v = array();

            while ($i < $last and strpos($ex[$i], ',') === false) {
                $k .= ' ' . $ex[$i];
                $i++;
            }

            while ($i < $count) {
                $v[] = $ex[$i];
                $i++;
            }

            $v = implode(' ', $v);

        } else {

            $i = 1;
            $v = $ex[$last];

            while ($i < $last) {
                $k .= ' ' . $ex[$i];
                $i++;
            }
        }
        return array('k' => $k, 'v' => $v);
    }

    private function parseDhcpConfig ($dhcpConfig) {

        $subnets = array();
        $vlans = array();
        $doNotTouch = array();
        $subnetOptions = array();

        $splitConfig = preg_split('/\n/', str_replace("\r", '', $dhcpConfig), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($splitConfig as $split) {

            if (isset($subnetStart) and isset($subnet)) {

                if (isset($hostStart, $host)) {

                    if (strpos($split, '}') !== false) {

                        unset($hostStart, $host);

                    } else {

                        $cleanedLine = preg_replace('/^[\s+]{1,}(.*?)$/', '$1', preg_replace('/\s+/', ' ', $split));

                        if (strpos($split, '#') !== false) {

                            if (isset($vlanStart) and isset($vlan)) {
                                $vlans[$vlan][$subnet][$host]['comment'][] = $cleanedLine;
                            } else {
                                $subnets[$subnet][$host]['comment'][] = $cleanedLine;
                            }

                        } else {

                            $return = $this->getKeyValue($split);

                            if (isset($vlanStart) and isset($vlan)) {
                                $vlans[$vlan][$subnet][$host][$return['k']] = $return['v'];
                            } else {
                                $subnets[$subnet][$host][$return['k']] = $return['v'];
                            }
                        }
                    }

                } else if (preg_match('/^(\s+|)host[\s+]{1,}[\w\-\_\.]{1,}[\s+]{1,}[\{]$/', $split)) {

                    $hostStart = true;
                    $host = preg_replace('/\s+/','',preg_replace('/host[\s+]{1,}(.*?)[\s+]{1,}[\{]/','$1', $split, -1));

                    if (isset($vlanStart) and isset($vlan)) {
                        $vlans[$vlan][$subnet][$host] = array();
                    } else {
                        $subnets[$subnet][$host] = array();
                    }

                } else if (strpos($split, '}') !== false) {

                    unset($subnetStart, $subnet);

                } else {

                    $return = $this->getKeyValue($split);
                    $subnetOptions[$subnet][$return['k']] = $return['v'];

                }

            } else if (isset($vlanStart) and !isset($subnetStart) and strpos($split, '}') !== false) {

                unset($vlanStart, $vlan);

            } else if (preg_match('/^[\s+]{0,}subnet[\s+]{1,}[\d]{1,3}.[\d]{1,3}.[\d]{1,3}.[\d]{1,3}[\s+]{1,}netmask[\s+]{1,}[\d]{1,3}.[\d]{1,3}.[\d]{1,3}\.[\d]{1,3}[\s+]{0,}[\{]$/', $split)) {

                $subnetStart = true;

                $subnet = preg_replace('/^[\s+]{0,}subnet[\s+]{1,}(.*)[\s+]{1,}netmask[\s+]{1,}[\d]{1,3}.[\d]{1,3}.[\d]{1,3}\.[\d]{1,3}[\s+]{0,}[\{]$/', '\1', $split);
                $subnetOptions[$subnet]['netmask'] = preg_replace('/^[\s+]{0,}subnet[\s+]{1,}[\d]{1,3}.[\d]{1,3}.[\d]{1,3}\.[\d]{1,3}[\s+]{1,}netmask[\s+]{1,}(.*)[\s+]{0,}[\{]$/', '\1', $split);

                if (isset($vlanStart) and isset($vlan) and !isset($vlans[$vlan][$subnet])) {
                    $vlans[$vlan][$subnet] = array();
                } else if (!isset($subnets[$subnet])) {
                    $subnets[$subnet] = array();
                }

            } else if (preg_match('/^[\s+]{0,}shared-network[\s+]{1,}[\w\-\_\.]{1,}[\s+]{0,}[\{]$/', $split)) {

                $vlanStart = true;

                $vlan =  preg_replace('/^[\s+]{0,}shared-network[\s+]{1,}(.*)[\s+]{1,}[\s+]{0,}[\{]$/', '\1', $split);

            } else {
                $doNotTouch[] = $split;
            }
        }

        return array('raw' => $doNotTouch, 'subnet' => $subnets, 'vlan' => $vlans, 'subnetOptions' => $subnetOptions);

    }

    private function removeDoubleSemicolon ($value) {

        while (strpos($value, ';;') != false) {
            $value = str_replace(';;', ';', $value);
        }

        return $value;

    }

    private function subnetToConfig ($array, $options, $id) {

        $config = '';

        foreach($array as $subnets => $hosts) {

            $netmask = (isset($this->dhcpData[$id]['subnets'][$subnets]['netmask'])) ? $this->dhcpData[$id]['subnets'][$subnets]['netmask'] : $options[$subnets]['netmask'];

            $config .= 'subnet ' . $subnets . ' netmask ' . $netmask . " {\r\n";

            if (isset($this->dhcpData[$id]['subnets'][$subnets]['subnetOptions'])) {
                $config .= $this->removeDoubleSemicolon(str_replace('%subnet-mask%', $netmask, $this->dhcpData[$id]['subnets'][$subnets]['subnetOptions'])) . "\r\n";
            } else {
                foreach ($options[$subnets] as $k => $sub) {
                    if ($k != 'netmask') {
                        $config .= $this->removeDoubleSemicolon($k . ' ' . $sub . ";\r\n");
                    }
                }
            }

            foreach ($hosts as $host => $values) {

                $config .= '  host ' . $host . " {\r\n";

                foreach ($values as $opt => $val) {
                    $config .= $this->removeDoubleSemicolon('      ' . $opt . ' ' . $val . "\r\n");
                }

                $config .= "  }\r\n";

            }

            $config .= "}\r\n";

        }

        return $config;

    }

    private function assembleDhcpConfig ($array, $id) {

        $config = '';

        foreach($array['raw'] as $l) {
            $config .= $l . "\r\n";
        }

        $config .= $this->subnetToConfig($array['subnet'], $array['subnetOptions'], $id);

        foreach ($array['vlan'] as $vlanName => $data) {

            $config .= 'shared-network ' . $vlanName . " {\r\n";

            $config .= $this->subnetToConfig($data, $array['subnetOptions'], $id);

            $config .= "}\r\n";
        }

        return $config;
    }

    public function VMWare() {

        foreach ($this->vmwareHosts as $hID => $h) {

            $privateKey = EASYWIDIR . '/keys/' .  removePub($this->vmwareHosts[$hID['hostID']]['vmIDs']['keyname']);

            $sftpObject = new Net_SFTP($this->vmwareHosts[$hID]['vmIDs']['ip'], $this->vmwareHosts[$hID]['vmIDs']['dport']);

            if (file_exists($privateKey) and $sftpObject->error === false) {

                if ($this->vmwareHosts[$hID]['vmIDs']['publickey'] != 'N') {

                    $ssh2Pass = new Crypt_RSA();

                    if ($this->vmwareHosts[$hID]['vmIDs']['publickey'] == 'B') {
                        $ssh2Pass->setPassword($this->vmwareHosts[$hID]['vmIDs']['dpass']);
                    }

                    $ssh2Pass->loadKey(file_get_contents($privateKey));

                } else {
                    $ssh2Pass = $this->vmwareHosts[$hID]['vmIDs']['dpass'];
                }

                if ($sftpObject->login($this->vmwareHosts[$hID]['vmIDs']['duser'], $ssh2Pass)) {

                    $sshObject = new Net_SSH2($this->vmwareHosts[$hID]['vmIDs']['ip'], $this->vmwareHosts[$hID]['vmIDs']['dport']);

                    if (file_exists($privateKey) and $sshObject->error === false) {

                        if ($sshObject->login($this->vmwareHosts[$hID]['vmIDs']['duser'], $ssh2Pass)) {

                            print "Prepare: unregister any invalid vms\r\n";

                            $cmd = 'vim-cmd vmsvc/getallvms | grep \'Skipping\' | while read line; do vim-cmd vmsvc/unregister `echo $line | grep \'Skipping\' |  awk -F "\'" \'{print $2}\'`; done';
                            $sshObject->exec($cmd);

                            foreach ($h['actions'] as $v) {

                                $dir='/vmfs/volumes/'.$this->ID['vmware'][$v['id']]['mountpoint']. '/' . $this->ID['vmware'][$v['id']]['hostname'];

                                if (in_array($v['action'], array('md', 'dl', 'st', 'ri', 're'))) {

                                    print "Step 1: Stop and remove if needed\r\n";

                                    // Get current VM ID
                                    $cmd = 'i(){ echo `vim-cmd vmsvc/getallvms 2> /dev/null | grep -v \'Skipping\' | grep \'' . $this->ID['vmware'][$v['id']]['hostname'] . '.vmx\' | awk \'{print $1}\'`;};';

                                    // Stop the VM
                                    $cmd .= ' o(){ vim-cmd vmsvc/power.off `i ' . $this->ID['vmware'][$v['id']]['hostname'] . '`; vim-cmd vmsvc/unregister `i ' . $this->ID['vmware'][$v['id']]['hostname'] . '`;}; o;';

                                    if (in_array($v['action'], array('dl','ri','re'))) {
                                        $cmd .= ' rm -rf /vmfs/volumes/' . $this->ID['vmware'][$v['id']]['mountpoint'] . '/' . $this->ID['vmware'][$v['id']]['hostname'];
                                    }

                                    $sshObject->exec($cmd);
                                }

                                if (in_array($v['action'], array('md', 'ad', 'ri', 're'))) {

                                    $harddisk = ($this->ID['vmware'][$v['id']]['distro'] == 'windows7srv-64') ? 'lsisas1068' : 'lsilogic';

                                    if ($sftpObject->mkdir(rtrim($dir, '/'), -1, true)) {

                                        $vmxFile = '.encoding = "UTF-8"' . "\n";
                                        $vmxFile .= 'config.version = "8"' . "\n";
                                        $vmxFile .= 'displayName = "' . $this->ID['vmware'][$v['id']]['hostname'] . '"' . "\n";
                                        $vmxFile .= 'ethernet0.present = "TRUE"' . "\n";
                                        $vmxFile .= 'ethernet0.virtualDev = "e1000"' . "\n";
                                        $vmxFile .= 'ethernet0.networkName = "VM Network"' . "\n";
                                        $vmxFile .= 'ethernet0.addressType = "static"' . "\n";
                                        $vmxFile .= 'ethernet0.Address = "' . $this->ID['vmware'][$v['id']]['mac'] . '"' . "\n";
                                        $vmxFile .= 'extendedConfigFile = "' . $this->ID['vmware'][$v['id']]['hostname'] .'.vmxf"' . "\n";
                                        $vmxFile .= 'floppy0.clientDevice = "TRUE"' . "\n";
                                        $vmxFile .= 'floppy0.fileName = ""' . "\n";
                                        $vmxFile .= 'floppy0.present = "TRUE"' . "\n";
                                        $vmxFile .= 'floppy0.startConnected = "FALSE"' . "\n";
                                        $vmxFile .= 'guestOS = "' . $this->ID['vmware'][$v['id']]['guestos'] . '"' . "\n";
                                        $vmxFile .= 'ide1:0.present = "TRUE"' . "\n";
                                        $vmxFile .= 'ide1:0.clientDevice = "TRUE"' . "\n";
                                        $vmxFile .= 'ide1:0.deviceType = "cdrom-raw"' . "\n";
                                        $vmxFile .= 'ide1:0.startConnected = "FALSE"' . "\n";
                                        $vmxFile .= 'memsize = "' . $this->ID['vmware'][$v['id']]['ram'] . '"' . "\n";
                                        $vmxFile .= 'numvcpus = "' . $this->ID['vmware'][$v['id']]['cores'] . '"' . "\n";
                                        $vmxFile .= 'nvram = "' . $this->ID['vmware'][$v['id']]['hostname'] .'.nvram"' . "\n";
                                        $vmxFile .= 'pciBridge0.present = "TRUE"' . "\n";
                                        $vmxFile .= 'pciBridge4.present = "TRUE"' . "\n";
                                        $vmxFile .= 'pciBridge4.virtualDev = "pcieRootPort"' . "\n";
                                        $vmxFile .= 'pciBridge4.functions = "8"' . "\n";
                                        $vmxFile .= 'pciBridge5.present = "TRUE"' . "\n";
                                        $vmxFile .= 'pciBridge5.virtualDev = "pcieRootPort"' . "\n";
                                        $vmxFile .= 'pciBridge5.functions = "8"' . "\n";
                                        $vmxFile .= 'pciBridge6.present = "TRUE"' . "\n";
                                        $vmxFile .= 'pciBridge6.virtualDev = "pcieRootPort"' . "\n";
                                        $vmxFile .= 'pciBridge6.functions = "8"' . "\n";
                                        $vmxFile .= 'pciBridge7.present = "TRUE"' . "\n";
                                        $vmxFile .= 'pciBridge7.virtualDev = "pcieRootPort"' . "\n";
                                        $vmxFile .= 'pciBridge7.functions = "8"' . "\n";
                                        $vmxFile .= 'powerType.powerOff = "soft"' . "\n";
                                        $vmxFile .= 'powerType.powerOn = "hard"' . "\n";
                                        $vmxFile .= 'powerType.suspend = "hard"' . "\n";
                                        $vmxFile .= 'powerType.reset = "soft"' . "\n";
                                        $vmxFile .= 'sched.cpu.min = "' . $this->ID['vmware'][$v['id']]['minmhz'] . '"' . "\n";
                                        $vmxFile .= 'sched.cpu.units = "mhz"' . "\n";
                                        $vmxFile .= 'sched.cpu.shares = "normal"' . "\n";
                                        $vmxFile .= 'sched.cpu.max = "' . $this->ID['vmware'][$v['id']]['maxmhz'] . '"' . "\n";
                                        $vmxFile .= 'sched.cpu.affinity = "all"' . "\n";
                                        $vmxFile .= 'sched.mem.max = "' . $this->ID['vmware'][$v['id']]['maxram'] . '"' . "\n";
                                        $vmxFile .= 'sched.mem.minsize = "' . $this->ID['vmware'][$v['id']]['minram'] . '"' . "\n";
                                        $vmxFile .= 'sched.mem.shares = "normal"' . "\n";
                                        $vmxFile .= 'scsi0.present = "TRUE"' . "\n";
                                        $vmxFile .= 'scsi0.sharedBus = "none"' . "\n";
                                        $vmxFile .= 'scsi0.virtualDev = "' . $harddisk . '"' . "\n";
                                        $vmxFile .= 'scsi0:0.present = "TRUE"' . "\n";
                                        $vmxFile .= 'scsi0:0.fileName = "' . $this->ID['vmware'][$v['id']]['hostname'] . '.vmdk"' . "\n";
                                        $vmxFile .= 'scsi0:0.deviceType = "scsi-hardDisk"' . "\n";
                                        $vmxFile .= 'uuid.location = "56 4d ce 4e ce 1e 51 4b-3f 61 d8 45 c0 c8 93 90"' . "\n";
                                        $vmxFile .= 'uuid.bios = "56 4d ce 4e ce 1e 51 4b-3f 61 d8 45 c0 c8 93 90"' . "\n";
                                        $vmxFile .= 'vc.uuid = "52 9c 06 a8 19 e6 40 c0-61 1b 6e 23 34 c8 c7 f9"' . "\n";
                                        $vmxFile .= 'virtualHW.productCompatibility = "hosted"' . "\n";
                                        $vmxFile .= 'virtualHW.version = "7"' . "\n";
                                        $vmxFile .= 'vmci0.present = "TRUE"' . "\n";
                                        $vmxFile .= 'uuid.action = "create"' . "\n";
                                        $vmxFile .= 'bios.bootOrder = "ethernet0"' . "\n";

                                        $filename = '/vmfs/volumes/' . $this->ID['vmware'][$v['id']]['mountpoint'] . '/' . $this->ID['vmware'][$v['id']]['hostname'] . '/' . $this->ID['vmware'][$v['id']]['hostname'] . '.vmx';

                                        if ($sftpObject->put($filename, $vmxFile)) {
                                            print "Step 2: Create/edit vmx file (OK)\r\n";
                                        } else {
                                            print "Step 2: Create/edit vmx file (FAILED)\r\n";
                                        }

                                    } else {
                                        print "Step 2: Create/edit vmx file (FAILED)\r\n";
                                    }

                                    print "Step 3: create volume\r\n";

                                    $cmd = 'a() { vmkfstools -c ' . $this->ID['vmware'][$v['id']]['hddsize'] . ' -a lsilogic -d thin /vmfs/volumes/' . $this->ID['vmware'][$v['id']]['mountpoint'] . '/' . $this->ID['vmware'][$v['id']]['hostname'] . '/' . $this->ID['vmware'][$v['id']]['hostname'] . '.vmdk >/dev/null 2>&1;}; a';
                                    $sshObject->exec($cmd);

                                } else {
                                    print "Step 2-3: skipped as not required\r\n";
                                }

                                if (in_array($v['action'], array('md', 'ad', 're', 'ri', 'rc'))) {

                                    print "Step 4: Start VM\r\n";

                                    $cmd = 'a() { vim-cmd vmsvc/power.on `vim-cmd solo/registervm /vmfs/volumes/' . $this->ID['vmware'][$v['id']]['mountpoint'] . '/' . $this->ID['vmware'][$v['id']]['hostname'] . '/' . $this->ID['vmware'][$v['id']]['hostname'] . '.vmx 2> /dev/null` >/dev/null 2>&1;}; a&';
                                    $sshObject->exec($cmd);

                                } else {
                                    print "Step 4: skipped as not required\r\n";
                                }
                            }
                        } else {
                            print "No Login\r\n";
                        }
                    } else {
                        print "No connection\r\n";
                    }
                } else {
                    print "No login connection\r\n";
                }
            } else {
                print "No connection\r\n";
            }
        }

        return true;

    }
}