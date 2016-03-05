<?php

/**
 * File: class_httpd.php.
 * Author: Ulrich Block
 * Date: 08.03.14
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

class HttpdManagement {

    // Data
    private $sql, $aeskey, $resellerID, $hostID, $ssh2Pass, $hostData = array(), $vhostData = false, $dataPrepared = false;
    public $ssh2Object = false, $sftpObject = false, $masterNotfound = false;

    public function __destruct() {
        unset($this->sql, $this->aeskey, $this->hostID, $this->ssh2Object, $this->sftpObject);
    }

    public function __construct($hostID, $resellerID) {

        // retrieve global vars
        global $sql, $aeskey;

        // define internal vars
        $this->sql = $sql;
        $this->aeskey = $aeskey;
        $this->resellerID = $resellerID;

        $query = $this->sql->prepare("SELECT *,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass` FROM `webMaster` WHERE `webMasterID`=:id AND `resellerID`=:resellerID LIMIT 1");
        $query->execute(array(':aeskey' => $this->aeskey, ':id' => $hostID, ':resellerID' => $this->resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            if ($row['active'] == 'N') {
                return false;
            }

            $this->hostData['defaultdns'] = $row['defaultdns'];
            $this->hostData['ip'] = $row['ip'];
            $this->hostData['port'] = $row['port'];
            $this->hostData['ftpIP'] = (strlen($row['ftpIP']) > 0) ? $row['ftpIP'] : $row['ip'];
            $this->hostData['ftpPort'] = $row['ftpPort'];
            $this->hostData['user'] = $row['decrypteduser'];
            $this->hostData['httpdCmd'] = $row['httpdCmd'];
            $this->hostData['userGroup'] = $row['userGroup'];
            $this->hostData['userAddCmd'] = $row['userAddCmd'];
            $this->hostData['userModCmd'] = $row['userModCmd'];
            $this->hostData['userDelCmd'] = $row['userDelCmd'];
            $this->hostData['vhostStoragePath'] = $row['vhostStoragePath'];
            $this->hostData['vhostConfigPath'] = $row['vhostConfigPath'];
            $this->hostData['vhostTemplate'] = $row['vhostTemplate'];
            $this->hostData['blocksize'] = $row['blocksize'];
            $this->hostData['inodeBlockRatio'] = $row['inodeBlockRatio'];
            $this->hostData['dirHttpd'] = $row['dirHttpd'];
            $this->hostData['dirLogs'] = $row['dirLogs'];
            $this->hostData['usageType'] = $row['usageType'];
            $this->hostData['skelDir'] = $this->removeNotNeededSlashes($this->hostData['vhostStoragePath'] . '/' . $this->hostData['user'] . '/skel/');

            if ($row['quotaActive'] == 'Y') {
                $this->hostData['quotaCmd'] = $row['quotaCmd'];
                $this->hostData['repquotaCmd'] = $row['repquotaCmd'];
            }


            if ($row['publickey'] != 'N') {

                $privateKey = EASYWIDIR . '/keys/' . removePub($row['keyname']);

                if (!file_exists($privateKey)) {
                    return false;
                }

                $this->ssh2Pass = new Crypt_RSA();

                if ($row['publickey'] == 'B') {
                    $this->ssh2Pass->setPassword($row['pass']);
                }

                $this->ssh2Pass->loadKey(file_get_contents($privateKey));

            } else {

                $this->ssh2Pass = $row['decryptedpass'];

            }

            $this->dataPrepared = true;

            return true;
        }

        $this->masterNotfound = true;

        return false;
    }

    private function removeNotNeededSlashes ($value) {
        return str_replace(array('//', '///'), '/', $value);
    }

    private function getVhostData ($vhostID) {

        if ($this->vhostData == false) {

            $query = $this->sql->prepare("SELECT v.`active`,v.`userID`,v.`description`,v.`hdd`,v.`ftpUser`,v.`phpConfiguration`,v.`defaultDomain`,AES_DECRYPT(v.`ftpPassword`,?) AS `decryptedFTPPass`,u.`mail` FROM `webVhost` AS v INNER JOIN `userdata` AS u ON u.`id`=v.`userID` WHERE v.`webVhostID`=? AND v.`resellerID`=? LIMIT 1");
            $query->execute(array($this->aeskey, $vhostID, $this->resellerID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $this->vhostData['userID'] = $row['userID'];
                $this->vhostData['hdd'] = $row['hdd'];
                $this->vhostData['ftpUser'] = $row['ftpUser'];
                $this->vhostData['ftpPassword'] = $row['decryptedFTPPass'];
                $this->vhostData['vhostConfigFile'] = $this->removeNotNeededSlashes($this->hostData['vhostConfigPath'] . '/' . $this->vhostData['ftpUser']) . '.conf';
                $this->vhostData['vhostHomeDir'] = $this->removeNotNeededSlashes($this->hostData['vhostStoragePath'] . '/' . $this->vhostData['ftpUser']);
                $this->vhostData['description'] = (strlen($row['description']) > 0) ? $row['description'] : 'web-' . $vhostID;

                $this->vhostData['defaultDomain'] = (isdomain($row['defaultDomain'])) ? $row['defaultDomain'] : 'web-' . $vhostID . '.' . $this->hostData['defaultdns'];

                $phpConfigurationVhost = @json_decode($row['phpConfiguration']);
                $this->vhostData['dns'] = array();

                // Workaround for migrations and other admin is breaking something faults
                $query2 = $this->sql->prepare("SELECT 1 FROM `webVhostDomain` WHERE `webVhostID`=? LIMIT 1");
                $query2->execute(array($vhostID));
                if ($query2->rowCount() == 0) {

                    try {
                        $query2 = $this->sql->prepare("INSERT INTO `webVhostDomain` (`webVhostID`,`userID`,`resellerID`,`domain`,`path`,`ownVhost`,`vhostTemplate`) VALUES (?,?,?,?,'','N',?)");
                        $query2->execute(array($vhostID, $row['userID'], $this->resellerID, $this->vhostData['defaultDomain'], $this->hostData['vhostTemplate']));

                        // There is always a catch ...
                    } catch(PDOException $error) {
                        $error = $error->getMessage();
                    }
                }

                $this->vhostData['templateFileContent'] = "# DO NOT EDIT DIRECTLY!\r\n# This file is autogenerated by easy-wi.com.\r\n# Date and time of generation was " . date('Y-m-d H:i:s') . "\r\n\r\n";

                $query2 = $this->sql->prepare("SELECT `path`,`domain`,`ownVhost`,`vhostTemplate` FROM `webVhostDomain` WHERE `webVhostID`=?");
                $query2->execute(array($vhostID));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                    $this->vhostData['dns'][] = $row2['domain'];

                    $templateFileContentTemp = ($row2['ownVhost'] == 'Y') ? $row2['vhostTemplate'] : $this->hostData['vhostTemplate'];

                    if ($phpConfigurationVhost and $this->hostData['usageType'] == 'W') {

                        preg_match('/(\s{1,}%phpConfiguration%)/', $templateFileContentTemp, $matches);
                        $match = array_shift($matches);
                        $whiteSpace = str_replace('%phpConfiguration%', '', $match);

                        $phpOptions = '';

                        foreach ($phpConfigurationVhost as $phpOption) {
                            $phpOptions .= $whiteSpace . $phpOption;
                        }

                        $templateFileContentTemp = str_replace('%phpConfiguration%', $phpOptions, $templateFileContentTemp);

                    } else {
                        $templateFileContentTemp = str_replace('%phpConfiguration%', '', $templateFileContentTemp);
                    }

                    $templateFileContentTemp = $this->removeNotNeededSlashes(str_replace(array('%user%', '%group%', '%vhostpath%', '%email%', '%htdocs%', '%logDir%'), array($row['ftpUser'], $this->hostData['userGroup'], $this->hostData['vhostStoragePath'], $row['mail'], $this->hostData['dirHttpd'], $this->hostData['dirLogs']), $templateFileContentTemp)) . "\r\n";
                    $templateFileContentTemp = $this->removeNotNeededSlashes(str_replace(array('%path%', '%url%', '%domain%'), array($row2['path'], $row2['domain'], $row2['domain']), $templateFileContentTemp)) . "\r\n";

                    $this->vhostData['templateFileContent'] .= $templateFileContentTemp;
                }

                return true;
            }

        } else {
            return true;
        }

        return false;
    }

    private function addVhost ($vhostID, $fullAdd = true, $reinstall = false) {

        if ($this->getVhostData($vhostID) != false) {

            if ($this->ssh2Object != false) {

                if ($fullAdd == true) {

                    $mailConnectInfo = array(
                        'ip' => $this->hostData['ftpIP'],
                        'port' => $this->hostData['ftpPort']
                    );

                    sendmail('emailserverinstall', $this->vhostData['userID'], $this->vhostData['description'], implode(', ', $this->vhostData['dns']), $mailConnectInfo);

                    $removeCmd = '';

                    if ($reinstall == true) {
                        $removeCmd = str_replace('%cmd%', ' -fr ' . $this->vhostData['ftpUser'], $this->hostData['userDelCmd']) . ' > /dev/null 2>&1; ';
                    }

                    $cmd = 'a() { ' . $removeCmd . str_replace('%cmd%', ' -md ' . $this->vhostData['vhostHomeDir'] . ' -p `perl -e \'print crypt("' . $this->vhostData['ftpPassword'] . '","Sa")\'` -g ' . $this->hostData['userGroup'] . ' -s /bin/false -k ' . $this->hostData['skelDir'] . ' '. $this->vhostData['ftpUser'], $this->hostData['userAddCmd']) . ' > /dev/null 2>&1; }; a';

                    $this->ssh2Object->exec($cmd);
                }

                if (isset($this->hostData['quotaCmd']) and strlen($this->hostData['quotaCmd']) > 0) {

                    // setquota works with KibiByte and Inodes
                    $sizeInKibiByte = $this->vhostData['hdd'] * 1024;
                    $sizeInByte = $this->vhostData['hdd'] * 1048576;
                    $blockAmount = round(($sizeInByte / $this->hostData['blocksize']));
                    $inodeAmount = round($blockAmount / $this->hostData['inodeBlockRatio']);

                    $cmd = 'q() { ' . str_replace('%cmd%', ' -u ' . $this->vhostData['ftpUser'] . ' ' . $sizeInKibiByte . ' ' . $sizeInKibiByte . ' ' . $inodeAmount . ' ' . $inodeAmount . ' -a ' . $this->removeNotNeededSlashes($this->hostData['vhostStoragePath'] . '/'), $this->hostData['quotaCmd']) . ' > /dev/null 2>&1; }; q&';

                    $this->ssh2Object->exec($cmd);
                }
            }

            if ($this->sftpObject != false) {
                $this->sftpObject->put($this->vhostData['vhostConfigFile'], $this->vhostData['templateFileContent']);
            }
        }
    }

    private function removeVhost ($vhostID, $fullRemove = true) {

        if ($this->getVhostData($vhostID) != false) {

            if ($this->sftpObject != false) {
                $this->sftpObject->delete($this->vhostData['vhostConfigFile'], true);
            }

            if ($this->ssh2Object != false and $fullRemove == true) {

                $cmd = 'r() { ' . str_replace('%cmd%', ' -fr ' . $this->vhostData['ftpUser'], $this->hostData['userDelCmd']) . ' > /dev/null 2>&1; }; r&';

                $this->ssh2Object->exec($cmd);

            }

            $this->vhostData = false;

        }
    }

    public function changePassword ($vhostID, $newPassword = false) {

        if ($this->getVhostData($vhostID) != false) {
            if ($this->ssh2Object != false) {

                $this->getVhostData($vhostID);

                $password = ($newPassword == false) ? $this->vhostData['ftpPassword'] : $newPassword;

                $cmd = 'p() { ' . str_replace('%cmd%', '-p `perl -e \'print crypt("' . $password .  '","Sa")\'` ' . $this->vhostData['ftpUser'], $this->hostData['userModCmd']) . ' > /dev/null 2>&1; }; p&';

                $this->ssh2Object->exec($cmd);

            }
        }
    }

    public function sftpConnect () {

        if ($this->dataPrepared == false) {
            return false;
        }

        $this->sftpObject = new Net_SFTP($this->hostData['ip'], $this->hostData['port']);

        if ($this->sftpObject->login($this->hostData['user'], $this->ssh2Pass)) {
            return true;
        }

        return false;

    }

    public function ssh2Connect () {

        if ($this->dataPrepared == false) {
            return false;
        }

        $this->ssh2Object = new Net_SSH2($this->hostData['ip'], $this->hostData['port']);

        if ($this->ssh2Object->login($this->hostData['user'], $this->ssh2Pass)) {
            return true;
        }

        return false;

    }

    public function setInactive ($vhostID) {

        $this->changePassword($vhostID, passwordgenerate(10));
        $this->removeVhost($vhostID, false);

        $this->vhostData = false;

    }

    public function vhostMod ($vhostID) {

        $this->addVhost($vhostID, false);
        $this->changePassword ($vhostID);

        $this->vhostData = false;

    }

    public function vhostCreate ($vhostID) {

        $this->addVhost($vhostID);

        $this->vhostData = false;
    }

    public function vhostDelete ($vhostID) {

        $this->removeVhost($vhostID);

        $this->vhostData = false;
    }

    public function vhostReinstall ($vhostID) {

        $this->addVhost($vhostID, true, true);

        $this->vhostData = false;
    }

    public function restartHttpdServer () {

        if ($this->ssh2Object != false) {

            $cmd = 'r() { ' . $this->hostData['httpdCmd'] . ' > /dev/null 2>&1; }; r&';

            $this->ssh2Object->exec($cmd);
        }
    }

    public function checkQuotaUsage () {

        global $dbConnect;

        if ($this->ssh2Object != false and isset($this->hostData['repquotaCmd']) and strlen($this->hostData['repquotaCmd']) > 0) {

            $cmd = 'for DIR in `grep -E \'usrjquota=|usrquota\' /etc/fstab | awk \'{print $2}\'`; do for USER in `' . str_replace('%cmd%', '' ,$this->hostData['repquotaCmd']) . ' -u -v -s $DIR 2>/dev/null | grep \'web-\' | awk \'{print $1":"$3}\'`; do USERS="$USERS;$USER"; done; done; echo "$USERS;"';

            $return = $this->ssh2Object->exec($cmd);

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                echo "Execute command: {$cmd}\r\n";
                echo "Command returns: {$return}\r\n";
            }

            $splitIntoHosts = preg_split('/;/', trim(preg_replace('/\s+/', '', $return)), -1, PREG_SPLIT_NO_EMPTY);

            $query = $this->sql->prepare("UPDATE `webVhost` SET `hddUsage`=? WHERE `webVhostID`=? LIMIT 1");

            if (count($splitIntoHosts) == 0) {
                print "No web user found in server return: {$return}\r\n";
            }

            foreach ($splitIntoHosts as $ftpUser) {

                unset($user, $usage, $webVhostID);

                @list($user, $usage) = explode(':', $ftpUser);
                @list($prefix, $webVhostID) = explode('-', $user);

                if (isset($usage) and isset($webVhostID)) {

                    $webVhostID = (int) $webVhostID;

                    if (substr($usage, -1) == 'K') {
                        $usage = round(((int) substr($usage, 0, (strlen($usage) - 1))) / 1000);
                    } else if  (substr($usage, -1) == 'M') {
                        $usage = (int) substr($usage, 0, (strlen($usage) - 1));
                    } else if  (substr($usage, -1) == 'G') {
                        $usage = ((int) substr($usage, 0, (strlen($usage) - 1))) * 1000;
                    } else {
                        $usage = 0;
                    }

                    $query->execute(array($usage, $webVhostID));

                    if ($query->rowCount() > 0) {
                        print "Found and updated webhost with FTP user {$user}, webVhostID {$webVhostID} and a usage of {$usage} MB\r\n";
                    } else {
                        print "Cannot find or no update for webhost with FTP user {$user}, webVhostID {$webVhostID} which has a usage of {$usage} MB\r\n";
                    }
                } else {
                    print "Cannot parse server return {$ftpUser}\r\n";
                }
            }

        } else {
            print "No SSH Connection or quota not allowed\r\n";
        }
    }
}