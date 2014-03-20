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

if (!class_exists('EasyWiFTP')) {
    include(EASYWIDIR . '/stuff/methods/class_ftp.php');
}

class HttpdManagement {

    // Data
    private $sql, $aeskey, $resellerID, $hostID, $ssh2Pass, $hostData = array(), $ssh2Object = false, $sftpObject = false, $vhostData = false, $dataPrepared = false;

    public function __destruct() {
        unset($this->sql, $this->aeskey, $this->hostID, $this->sshConnection, $this->sftpConnection);
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
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            if ($row['active'] == 'N') {
                return false;
            }

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
            $this->hostData['skelDir'] = $this->removeNotNeededSlashes($this->hostData['vhostStoragePath'] . '/' . $this->hostData['user'] . '/skel/');

            if ($row['quotaActive'] == 'Y') {
                $this->hostData['quotaCmd'] = $row['quotaCmd'];
            }

            $this->hostData['createDirs'] = preg_split('/\;/', $row['createDirs'], -1, PREG_SPLIT_NO_EMPTY);

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

        return false;

    }

    private function removeNotNeededSlashes ($value) {
        return str_replace(array('//', '///'), '/', $value);
    }

    private function getVhostData ($vhostID) {

        if ($this->vhostData == false) {

            $query = $this->sql->prepare("SELECT v.`active`,v.`ownVhost`,v.`vhostTemplate`,v.`dns`,v.`hdd`,v.`ftpUser`,AES_DECRYPT(v.`ftpPassword`,?) AS `decryptedFTPPass`,u.`mail` FROM `webVhost` AS v INNER JOIN `userdata` AS u ON u.`id`=v.`userID` WHERE v.`webVhostID`=? AND v.`resellerID`=? LIMIT 1");
            $query->execute(array($this->aeskey, $vhostID, $this->resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                if ($row['active'] == 'N') {
                    return false;
                }

                $this->vhostData['hdd'] = $row['hdd'];
                $this->vhostData['dns'] = $row['dns'];
                $this->vhostData['ftpUser'] = $row['ftpUser'];
                $this->vhostData['ftpPassword'] = $row['decryptedFTPPass'];
                $this->vhostData['vhostConfigFile'] = $this->removeNotNeededSlashes($this->hostData['vhostConfigPath'] . '/' . $this->vhostData['ftpUser'] . '.conf');
                $this->vhostData['vhostHomeDir'] = $this->removeNotNeededSlashes($this->hostData['vhostStoragePath'] . '/' . $this->vhostData['ftpUser']);

                $this->vhostData['templateFileContent'] = '# DO NOT EDIT DIRECTLY! This file is autogenerated by easy-wi.com. Date and time of generation was ' . date('Y-m-d H:i:s') . "\r\n";
                $this->vhostData['templateFileContent'] .= ($row['ownVhost'] == 'Y') ? $row['vhostTemplate'] : $this->hostData['vhostTemplate'];

                $this->vhostData['templateFileContent'] = str_replace(array('%url%', '%user%', '%vhostpath%', '%email%'), array($row['dns'], $row['ftpUser'], $this->hostData['vhostStoragePath'], $row['mail']), $this->vhostData['templateFileContent']);

                return true;
            }

        } else {
            return true;
        }

        return false;
    }

    private function addVhost ($vhostID, $fullAdd = true) {

        if ($this->getVhostData($vhostID) != false) {

            if ($this->ssh2Object != false) {

                if ($fullAdd == true) {

                    $cmd = 'a() { ' . str_replace('%cmd%', ' -md ' . $this->vhostData['vhostHomeDir'] . ' -p `perl -e \'print crypt("' . $this->vhostData['ftpPassword'] . '","Sa")\'` -g ' . $this->hostData['userGroup'] . ' -s /bin/false -k ' . $this->hostData['skelDir'] . ' '. $this->vhostData['ftpUser'], $this->hostData['userAddCmd']) . ' > /dev/null 2>&1; }; a';

                    $this->ssh2Object->exec($cmd);

                    if (count($this->hostData['createDirs']) > 0) {
                        $ftpConnection = new EasyWiFTP($this->hostData['ftpIP'], $this->hostData['ftpPort'], $this->vhostData['ftpUser'], $this->vhostData['ftpPassword']);
                        $ftpConnection->createDirs($this->hostData['createDirs']);
                        $ftpConnection->logOut();
                    }
                }

                if (isset($this->hostData['quotaCmd']) and strlen($this->hostData['quotaCmd']) > 0) {

                    $cmd = 'q() { ' . str_replace('%cmd%', ' -u ' . $this->vhostData['ftpUser'] . ' -b -l ' . $this->vhostData['hdd'] . 'M /', $this->hostData['quotaCmd']) . ' > /dev/null 2>&1; }; q&';

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

        $this->ssh2Object = new Net_SFTP($this->hostData['ip'], $this->hostData['port']);

        if ($this->ssh2Object->login($this->hostData['user'], $this->ssh2Pass)) {
            return true;
        }

        return false;

    }

    public function setInactive ($vhostID) {

        $this->changePassword ($vhostID, passwordgenerate(10));
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

    public function restartHttpdServer () {

        if ($this->ssh2Object != false) {

            $cmd = 'r() {' . $this->hostData['httpdCmd'] . ' > /dev/null 2>&1; }; r&';

            $this->ssh2Object->exec($cmd);
        }
    }
}