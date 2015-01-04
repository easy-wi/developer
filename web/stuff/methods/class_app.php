<?php

/**
 * File: class_app.php.
 * Author: Ulrich Block
 * Date: 26.10.14
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

// Include PHPSeclib if not already included
if (!class_exists('Net_SSH2')) {
    include(EASYWIDIR . '/third_party/phpseclib/Net/SSH2.php');
}

if (!class_exists('Crypt_RSA')) {
    include(EASYWIDIR . '/third_party/phpseclib/Crypt/RSA.php');
}

if (!class_exists('Net_SFTP')) {
    include(EASYWIDIR . '/third_party/phpseclib/Net/SFTP.php');
}

// Include EasyWi FTP if not already included
if (!class_exists('EasyWiFTP')) {
    include(EASYWIDIR . '/stuff/methods/class_ftp.php');
}

class AppServer {

    private $uniqueHex, $winCmds = array(), $shellScriptHeader, $shellScripts = array('user' => '', 'server' => array()), $commandReturns = array();

    public $appMasterServerDetails = array(), $appServerDetails = false;

    // The constructor gathers the root data
    function __construct($id) {

        global $sql, $aeskey;

        $this->uniqueHex = dechex(mt_rand());

        $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,AES_DECRYPT(`steamAccount`,:aeskey) AS `decryptedsteamAccount`,AES_DECRYPT(`steamPassword`,:aeskey) AS `decryptedsteamPassword` FROM `rserverdata` WHERE `id`=:serverID LIMIT 1");
        $query->execute(array(':serverID' => $id, ':aeskey' => $aeskey));

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->appMasterServerDetails['id'] = (int) $id;
            $this->appMasterServerDetails['notified'] = (int) $row['notified'];
            $this->appMasterServerDetails['ssh2IP'] = (string) $row['ip'];
            $this->appMasterServerDetails['ssh2Port'] = (int) $row['decryptedport'];
            $this->appMasterServerDetails['ssh2User'] = (string) $row['decrypteduser'];
            $this->appMasterServerDetails['ssh2Publickey'] = (string) $row['publickey'];
            $this->appMasterServerDetails['ssh2DecryptedPass'] = (string) $row['decryptedpass'];
            $this->appMasterServerDetails['ssh2KeyName'] = (string) $row['keyname'];
            $this->appMasterServerDetails['ftpPort'] = (string) $row['ftpport'];
            $this->appMasterServerDetails['os'] = (string) $row['os'];
            $this->appMasterServerDetails['iniVars'] = @parse_ini_string($row['install_paths'], true);

            # https://github.com/easy-wi/developer/issues/70
            $this->appMasterServerDetails['privateKey'] = EASYWIDIR . '/keys/' . removePub($this->appMasterServerDetails['ssh2KeyName']);

            $this->appMasterServerDetails['quotaActive'] = $row['quota_active'];
            $this->appMasterServerDetails['quotaCmd'] = $row['quota_cmd'];
            $this->appMasterServerDetails['repquotaCmd'] = $row['repquota_cmd'];
            $this->appMasterServerDetails['blocksize'] = $row['blocksize'];
            $this->appMasterServerDetails['inodeBlockRatio'] = $row['inode_block_ratio'];

            $this->appMasterServerDetails['configBadFiles'] = preg_split('/,/', $row['config_bad_files'], -1, PREG_SPLIT_NO_EMPTY);
            $this->appMasterServerDetails['configBadTime'] = (int) $row['config_bad_time'];
            $this->appMasterServerDetails['configBinaries'] = preg_split('/,/', $row['config_binaries'], -1, PREG_SPLIT_NO_EMPTY);
            $this->appMasterServerDetails['configDemoTime'] = (int) $row['config_demo_time'];
            $this->appMasterServerDetails['configFiles'] = preg_split('/,/', $row['config_files'], -1, PREG_SPLIT_NO_EMPTY);
            $this->appMasterServerDetails['configIonice'] = (string) $row['config_ionice'];
            $this->appMasterServerDetails['configLogTime'] = (int) $row['config_log_time'];
            $this->appMasterServerDetails['configUserID'] = ($row['config_user_id'] > 0) ? (int) $row['config_user_id'] : 1000;
            $this->appMasterServerDetails['configZtmpTime'] = (int) $row['config_ztmp_time'];


            if ($this->appMasterServerDetails['os'] == 'L') {
                $this->shellScriptHeader = "#!/bin/bash\n";
                $this->shellScriptHeader .= "if ionice -c3 true 2>/dev/null; then IONICE='ionice -n 7 '; fi\n";
                $this->shellScripts['user'] = $this->shellScriptHeader . 'rm /home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/userCud-' . $this->uniqueHex . '.sh' . "\n";
            }
        }

        return ($query->rowCount() > 0) ? true : false;
    }

    // Function that gathers the details of the currently active app
    public function getAppServerDetails($id) {

        // Those three variables are always defined, when this class is used
        global $sql, $aeskey, $resellerLockupID;

        $query = $sql->prepare("SELECT g.*,AES_DECRYPT(g.`ppassword`,:aeskey) AS `decryptedppass`,AES_DECRYPT(g.`ftppassword`,:aeskey) AS `decryptedftppass`,u.`cname` FROM `gsswitch` AS g INNER JOIN `userdata` AS u ON u.`id`=g.`userid` WHERE g.`id`=:id AND g.`resellerid`=:resellerID LIMIT 1");
        $query->execute(array(':id' => $id, ':aeskey' => $aeskey, ':resellerID' => $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            // If app details can not be found return false
            if (!$this->getAppDetails($row['serverid'], $id)) {

                $query2 = $sql->prepare("SELECT `id` FROM `serverlist` WHERE `switchID`=? LIMIT 1");
                $query2->execute(array($id));
                $row['serverid'] = $query2->fetchColumn();

                if ($row['serverid'] > 0) {
                    $query2 = $sql->prepare("UPDATE `gsswitch` SET `serverid`=? WHERE `id`=? LIMIT 1");
                    $query2->execute(array($row['serverid'], $id));
                }

                if (!$this->getAppDetails($row['serverid'], $id)) {

                    $this->appServerDetails = false;

                    return false;
                }
            }

            $this->appServerDetails['app']['id'] = $row['serverid'];

            $this->appServerDetails['id'] = (int) $row['id'];
            $this->appServerDetails['type'] = (string) $row['type'];
            $this->appServerDetails['lendServer'] = (string) $row['lendserver'];
            $this->appServerDetails['protectionModeAllowed'] = ($this->appServerDetails['template']['protectedApp'] == 'Y') ? (string) $row['pallowed'] : 'N';
            $this->appServerDetails['protectionModeStarted'] = ($this->appServerDetails['protectionModeAllowed'] == 'Y') ? (string) $row['protected'] : 'N';
            $this->appServerDetails['eacAllowed'] = (string) $row['eacallowed'];
            $this->appServerDetails['tvAllowed'] = (string) $row['tvenable'];
            $this->appServerDetails['serverIP'] = (string) $row['serverip'];
            $this->appServerDetails['port'] = (int) $row['port'];
            $this->appServerDetails['port2'] = (int) $row['port2'];
            $this->appServerDetails['port3'] = (int) $row['port3'];
            $this->appServerDetails['port4'] = (int) $row['port4'];
            $this->appServerDetails['port5'] = (int) $row['port5'];
            $this->appServerDetails['minram'] = ($row['minram'] > 0) ? (int) $row['minram'] : 512;
            $this->appServerDetails['maxram'] = ($row['maxram'] > 0) ? (int) $row['maxram'] : 1024;
            $this->appServerDetails['slots'] = (int) $row['slots'];
            $this->appServerDetails['userMasterFastDownload'] = (string) $row['masterfdl'];
            $this->appServerDetails['specificFastDownLoadData'] = (string) $row['mfdldata'];
            $this->appServerDetails['useTaskSet'] = (string) $row['taskset'];
            $this->appServerDetails['cores'] = (string) $row['cores'];
            $this->appServerDetails['maxCores'] = count(preg_split("/\,/", $this->appServerDetails['cores'], -1, PREG_SPLIT_NO_EMPTY));
            $this->appServerDetails['maxCores'] = ($this->appServerDetails['maxCores'] == 0) ? 1 : $this->appServerDetails['maxCores'];
            $this->appServerDetails['userName'] = ($row['newlayout'] == 'Y') ? (string) $row['cname'] . '-' . $id : (string) $row['cname'];
            $this->appServerDetails['userNameExecute'] = ($this->appServerDetails['protectionModeStarted'] == 'Y') ? (string) $this->appServerDetails['userName'] . '-p' : (string) $this->appServerDetails['userName'];
            $this->appServerDetails['hdd'] = (int) $row['hdd'];
            $this->appServerDetails['homeLabel'] = (string) $row['homeLabel'];

            // Password value is only used for setting. In case a server is inactive we need to generate a random one, so the customer can no longer log in.
            $this->appServerDetails['ftpPassword'] = ($row['active'] == 'Y') ? (string) $row['decryptedftppass'] : passwordgenerate(10);
            $this->appServerDetails['ftpPasswordProtected'] = ($row['active'] == 'Y') ? (string) $row['decryptedppass'] : passwordgenerate(10);

            // This password will be used, when a FTP connection needs to be setup
            $this->appServerDetails['ftpPasswordExecute'] = ($this->appServerDetails['protectionModeStarted'] == 'Y') ? (string) $row['decryptedppass'] : (string) $row['decryptedftppass'];

            // As the data loading is sequential, required parameters for the ternary operator will not be available within getAppDetails() function
            $this->appServerDetails['app']['templateChoosen'] = ($this->appServerDetails['app']['servertemplate'] == 1 or $this->appServerDetails['protectionModeStarted'] == 'Y') ? $this->appServerDetails['template']['shorten'] : $this->appServerDetails['template']['shorten'] . '-' . $this->appServerDetails['app']['servertemplate'];
            $this->appServerDetails['app']['uploadDir'] = ($this->appServerDetails['tvAllowed'] == 'Y') ? $this->appServerDetails['app']['uploadDir'] : false;

            $this->appServerDetails['homeDir'] = ($this->appMasterServerDetails['iniVars'] and isset($this->appMasterServerDetails['iniVars'][$row['homeLabel']]['path'])) ? (string) $this->appMasterServerDetails['iniVars'][$row['homeLabel']]['path'] : '/home';

            $serverTemplateDir = $this->appServerDetails['homeDir'] . '/' . $this->appServerDetails['userName'];
            $serverTemplateDir .= ($this->appServerDetails['protectionModeStarted'] == 'Y') ? '/pserver/' : '/server/';
            $this->appServerDetails['absolutePath'] = $this->removeSlashes($serverTemplateDir . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '/' . $this->appServerDetails['app']['templateChoosen'] . '/');

            // For protected users the pserver/ directory is the home folder
            // We deliberately let admins that failed to setup a chrooted FTP environment run into errors
            $absoluteFTPPath = ($this->appServerDetails['protectionModeStarted'] == 'Y') ? '/' : '/server/';
            $absoluteFTPPath .= $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '/' . $this->appServerDetails['app']['templateChoosen'];
            if ($this->getGameType() == 'hl2') {
                $absoluteFTPPath .= '/' . $this->appServerDetails['template']['binarydir'];
            }
            $absoluteFTPPath .= '/' . $this->appServerDetails['template']['modfolder'] . '/';

            $this->appServerDetails['absoluteFTPPath'] = $this->removeSlashes($absoluteFTPPath);
}

        return ($query->rowCount() > 0) ? true : false;
    }

    // Function that gathers the details of the currently active app
    private function getAppDetails($id, $appServerID) {

        global $sql, $aeskey;

        $query = $sql->prepare("SELECT t.`id` AS `template_id`,t.`gameq`,t.`shorten`,t.`protected`,t.`protectedSaveCFGs`,t.`gamebinary`,t.`gamebinaryWin`,t.`binarydir`,t.`modfolder`,t.`cmd` AS `template_cmd`,t.`modcmds` AS `template_modcmds`,`configedit`,s.*,AES_DECRYPT(s.`uploaddir`,:aeskey) AS `d_uploaddir`,AES_DECRYPT(s.`webapiAuthkey`,:aeskey) AS `d_webapiauthkey` FROM `serverlist` AS s INNER JOIN `servertypes` AS t ON t.`id`=s.`servertype` WHERE s.`id`=:id AND s.`switchID`=:appServerID LIMIT 1");
        $query->execute(array(':aeskey' => $aeskey, ':id' => $id, ':appServerID' => $appServerID));

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            // First block will be global app template settings
            $this->appServerDetails['template']['id'] = (string) $row['template_id'];
            $this->appServerDetails['template']['gameq'] = (string) $row['gameq'];
            $this->appServerDetails['template']['shorten'] = (string) $row['shorten'];
            $this->appServerDetails['template']['protectedApp'] = (string) $row['protected'];
            $this->appServerDetails['template']['protectedSaveCFGs'] = $row['protectedSaveCFGs'];
            $this->appServerDetails['template']['gameBinary'] = ($this->appMasterServerDetails['os'] == 'L') ? (string) $row['gamebinary'] : (string) $row['gamebinaryWin'];
            $this->appServerDetails['template']['binarydir'] = (string) $row['binarydir'];
            $this->appServerDetails['template']['modfolder'] = (string) $row['modfolder'];
            $this->appServerDetails['template']['modcmds'] = (string) $row['template_modcmds'];
            $this->appServerDetails['template']['configedit'] = $row['configedit'];

            // second block will be specific app settings
            $this->appServerDetails['app']['anticheat'] = (int) $row['anticheat'];
            $this->appServerDetails['app']['fps'] = (int) $row['fps'];
            $this->appServerDetails['app']['tic'] = (int) $row['tic'];
            $this->appServerDetails['app']['servertemplate'] = (int) $row['servertemplate'];
            $this->appServerDetails['app']['map'] = (string) $row['map'];
            $this->appServerDetails['app']['workShop'] = (string) $row['workShop'];
            $this->appServerDetails['app']['mapGroup'] = (string) $row['mapGroup'];
            $this->appServerDetails['app']['workshopCollection'] = (int) $row['workshopCollection'];
            $this->appServerDetails['app']['webApiAuthKey'] = (string) $row['d_webapiauthkey'];

            $this->appServerDetails['app']['upload'] = (int) $row['upload'];
            $this->appServerDetails['app']['uploadDir'] = (strlen($row['d_uploaddir']) > 0) ? (string) $row['d_uploaddir'] : false;

            $this->appServerDetails['app']['modcmd'] = (string) $row['modcmd'];
            $this->appServerDetails['app']['gamemod'] = (string) $row['gamemod'];
            $this->appServerDetails['app']['gamemod2'] = (string) $row['gamemod2'];

            // Third will be app settings which might get overwritten by global settings
            $this->appServerDetails['app']['cmd'] = ($row['owncmd'] == 'Y') ? (string) $row['cmd'] : (string) $row['template_cmd'];
        }

        return ($query->rowCount() > 0) ? true : false;
    }

    private function getReplacements () {

        if ($this->appServerDetails['lendServer'] == 'Y') {
            $lendDetails = $this->getLendDetails();
        }

        if (!isset($lendDetails) or !is_array($lendDetails)) {
            $lendDetails = array('rcon' => '', 'password' => '', 'slots' => $this->appServerDetails['slots']);
        }

        $placeholder = array('%binary%', '%tickrate%', '%tic%', '%ip%', '%port%', '%tvport%', '%port2%', '%port3%', '%port4%', '%port5%', '%slots%', '%map%', '%mapgroup%', '%fps%', '%minram%', '%maxram%', '%maxcores%', '%folder%', '%user%', '%absolutepath%');

        $replacePlaceholderWith = array(
            $this->appServerDetails['template']['gameBinary'],
            $this->appServerDetails['app']['tic'],
            $this->appServerDetails['app']['tic'],
            $this->appServerDetails['serverIP'],
            $this->appServerDetails['port'],
            $this->appServerDetails['port2'],
            $this->appServerDetails['port2'],
            $this->appServerDetails['port3'],
            $this->appServerDetails['port4'],
            $this->appServerDetails['port5'],
            ($this->appServerDetails['lendServer'] == 'Y') ? $lendDetails['slots'] : $this->appServerDetails['slots'],
            $this->appServerDetails['app']['map'],
            $this->appServerDetails['app']['mapGroup'],
            $this->appServerDetails['app']['fps'],
            $this->appServerDetails['minram'],
            $this->appServerDetails['maxram'],
            $this->appServerDetails['maxCores'],
            $this->appServerDetails['app']['templateChoosen'],
            $this->appServerDetails['userName'],
            $this->appServerDetails['absolutePath']
        );

        return array('placeholder' => $placeholder, 'replacePlaceholderWith' => $replacePlaceholderWith);
    }

    // function that gathers the details for all installed addons
    public function getAddonDetails () {

        global $sql;

        $this->appServerDetails['extensions']['addons'] = array();
        $this->appServerDetails['extensions']['addonSettings'] = array();
        $this->appServerDetails['extensions']['maps'] = array();
        $this->appServerDetails['extensions']['cmds'] = array();
        $this->appServerDetails['extensions']['rmcmd'] = array();

        $query = $sql->prepare("SELECT a.`id`,a.`cmd`,a.`rmcmd`,a.`addon`,a.`type`,a.`paddon`,a.`depending`,a.`folder` FROM `addons_installed` AS i INNER JOIN `addons` AS a ON a.`id`=i.`addonid` WHERE i.`serverid`=? AND i.`paddon`=? AND i.`servertemplate`=?");
        $query->execute(array($this->appServerDetails['app']['id'], $this->appServerDetails['protectionModeStarted'], $this->appServerDetails['app']['servertemplate']));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            if ($row['type'] == 'tool') {
                $this->appServerDetails['extensions']['addons'][$row['id']] = $row['addon'];
            } else {
                $this->appServerDetails['extensions']['maps'][$row['id']] = $row['addon'];
            }

            $this->appServerDetails['extensions']['addonSettings'][$row['id']] = array('protectedAllowed' => $row['paddon'], 'folder' => $row['folder']);

            // Maps are allowed with protection mode in any case. Addons can be limited. We need to filter addons which should not be running when protection mode is active
            if ($row['type'] == 'map' or $this->appServerDetails['protectionModeStarted'] == 'N' or ($this->appServerDetails['protectionModeStarted'] == 'Y' and $row['paddon'] == 'Y')) {
                if (strlen($row['cmd']) > 0) {
                    $this->appServerDetails['extensions']['cmds'][] = (substr($row['cmd'], 0, 12) == '[no_padding]') ? trim(substr($row['cmd'], 12)) : ' ' . $row['cmd'];
                }

                if (strlen($row['rmcmd']) > 0) {
                    foreach (preg_split("/\r\n/", $row['rmcmd'], -1, PREG_SPLIT_NO_EMPTY) as $removeCommand) {
                        if (strlen($removeCommand) > 0) {
                            $this->appServerDetails['extensions']['removeCmds'][] = $removeCommand;
                        }
                    }
                }
            }
        }
    }

    // Function that checks if the game server is landed and if yes with which details
    private function getLendDetails () {

        global $sql, $aeskey;

        $cmd = '';

        $query = $sql->prepare("SELECT `rcon`,`password`,`slots`,AES_DECRYPT(`ftpuploadpath`,?) AS `decyptedftpuploadpath` FROM `lendedserver` WHERE `serverid`=? LIMIT 1");
        $query->execute(array($aeskey, $this->appServerDetails['app']['id']));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->appServerDetails['slots'] = (int) $row['slots'];

            if (strlen($row['decyptedftpuploadpath']) > 0 and $row['decyptedftpuploadpath'] != 'ftp://username:password@1.1.1.1/demos') {
                $this->appServerDetails['app']['uploadDir'] = $row['decyptedftpuploadpath'];
            }

            $gameType = $this->getGameType();

            if ($gameType == 'hl2') {
                $cmd .= ' +rcon_password ' .$row['rcon'] . ' +sv_password ' . $row['password']. ' +tv_enable 1 +tv_autorecord 1';
            } else if ($gameType == 'hl1') {
                $cmd .= ' +rcon_password ' . $row['rcon'] . ' +sv_password ' . $row['password'];
            } else if ($gameType == 'cod') {
                $cmd .= ' +set rcon_password ' . $row['rcon'] . ' +set g_password ' . $row['password'];
            } else {
                $cmd = array('rcon' => $row['rcon'], 'password' => $row['password'], 'slots' => $row['slots']);
            }
        }

        return $cmd;
    }

    /*
     * Abstract helper functions
     */
    private function addLogline ($logName, $logLine) {
        $this->shellScripts['user'] .= 'echo "`date`: ' . $logLine . '" >> "/home/' . $this->appMasterServerDetails['ssh2User'] . '/logs/' . $logName . '"' . "\n";
    }

    private function removeSlashes ($string) {

        while (strpos($string, '//') !== false) {
            $string = str_replace('//', '/', $string);
        }

        return $string;
    }

    // Often we need to proceed depending on game engine type
    private function getGameType () {

        // First do a check against the binary
        if (in_array($this->appServerDetails['template']['gameBinary'], array('srcds_run', 'srcds.exe'))) {
            return 'hl2';
        }

        if (in_array($this->appServerDetails['template']['gameBinary'], array('hlds_run', 'hlds.exe'))) {
            return 'hl1';
        }

        if (in_array($this->appServerDetails['template']['gameBinary'], array('cod4_lnxded', 'codwaw_lnxded', 'iw3mp.exe', 'CoDWaWmp.exe'))) {
            return 'cod';
        }

        // The admin might have configured a shell script instead of the native binary or script
        // We also need to check against the gameQ setting to be on the safe side
        if (in_array($this->appServerDetails['template']['gameq'], array('minecraft', 'minequery'))) {
            return 'mc';
        }

        if (in_array($this->appServerDetails['template']['gameq'], array('aoc', 'csgo', 'css', 'dods', 'hl2dm', 'l4d', 'l4d2', 'ns2', 'tf2', 'zps'))) {
            return 'hl2';
        }

        if (in_array($this->appServerDetails['template']['gameq'], array('cs16', 'cscz', 'dod', 'insurgency', 'ns', 'tfc'))) {
            return 'hl1';
        }

        if (substr($this->appServerDetails['template']['gameq'], 0, 3) == 'cod') {
            return 'cod';
        }

        return $this->appServerDetails['template']['gameq'];
    }

    /*
     * Following code contains the user management related funtions
     */

    private function linuxAddModUserGenerate ($userName, $password, $protected = false, $deactivate = false) {

        $password = ($deactivate == false) ? $password : passwordgenerate(10);
        $userNameHome = ($protected == false) ? $this->appServerDetails['userName'] : $this->appServerDetails['userName'] . '/pserver';

        // Check if the user can be found. If not, add it, if yes, edit
        $this->shellScripts['user'] .=  'if [ "`id ' . $userName . ' 2>/dev/null`" == "" ]; then' . "\n";

        $this->shellScripts['user'] .=  'CONFIGUSERID=' . $this->appMasterServerDetails['configUserID'] . "\n";
        $this->shellScripts['user'] .=  'USER=`ls -la /var/run/screen | grep S-' . $userName . ' | head -n 1 | awk \'{print $3}\'`' . "\n";
        $this->shellScripts['user'] .=  'if [ "$USER" != "" -a $USER -eq $USER 2> /dev/null ]; then CONFIGUSERID=$USER; fi' . "\n";
        $this->shellScripts['user'] .=  'USERID=`getent passwd | cut -f3 -d: | sort -un | awk \'BEGIN { id=\'${CONFIGUSERID}\' } $1 == id { id++ } $1 > id { print id; exit }\'`' . "\n";
        $this->shellScripts['user'] .=  'if [ "`ls -la /var/run/screen | awk \'{print $3}\' | grep $USERID`" == "" -a "`grep \"x:$USERID:\" /etc/passwd`" == "" ]; then' . "\n";
        $this->shellScripts['user'] .=  'sudo /usr/sbin/useradd -m -p `perl -e \'print crypt("\'' . $password . '\'","Sa")\'` -d ' . $this->removeSlashes($this->appServerDetails['homeDir'] . '/' . $userNameHome) . ' -g ' . $this->appMasterServerDetails['ssh2User'] . ' -s /bin/bash -u $USERID ' . $userName . ' 2>/dev/null' . "\n";
        $this->shellScripts['user'] .=  'else' . "\n";
        $this->shellScripts['user'] .=  'while [ "`ls -la /var/run/screen | awk \'{print $3}\' | grep $USERID`" != "" -o "`grep \"x:$USERID:\" /etc/passwd`" != "" ]; do' . "\n";
        $this->shellScripts['user'] .=  'USERID=$[USERID+1]' . "\n";
        $this->shellScripts['user'] .=  'if [ "`ls -la /var/run/screen | awk \'{print $3}\' | grep $USERID`" == "" -a "`grep \"x:$USERID:\" /etc/passwd`" == "" ]; then' . "\n";
        $this->shellScripts['user'] .=  'sudo /usr/sbin/useradd -m -p `perl -e \'print crypt("\'' . $password . '\'","Sa")\'` -m -d ' . $this->removeSlashes($this->appServerDetails['homeDir'] . '/' . $userNameHome) . ' -g ' . $this->appMasterServerDetails['ssh2User'] . ' -s /bin/bash -u $USERID ' . $userName . ' 2>/dev/null' . "\n";
        $this->shellScripts['user'] .=  'fi' . "\n";
        $this->shellScripts['user'] .=  'done' . "\n";
        $this->shellScripts['user'] .=  'fi' . "\n";
        $this->addLogline('user.log', 'User ' . $userName . ' added');
        $this->shellScripts['user'] .=  'else' . "\n";
        $this->shellScripts['user'] .=  'sudo /usr/sbin/usermod -p `perl -e \'print crypt("\'' . $password . '\'","Sa")\'` -m -d ' . $this->removeSlashes($this->appServerDetails['homeDir'] . '/' . $userNameHome) . ' ' . $userName . "\n";
        $this->addLogline('user.log', 'User ' . $userName . ' edited');
        $this->shellScripts['user'] .=  'fi' . "\n";

    }

    private function linuxAddModUser ($deactivate) {

        $this->linuxAddModUserGenerate ($this->appServerDetails['userName'], $this->appServerDetails['ftpPassword'], false, $deactivate);

        if ($this->appServerDetails['protectionModeAllowed']) {
            $this->linuxAddModUserGenerate ($this->appServerDetails['userName'] . '-p', $this->appServerDetails['ftpPasswordProtected'], true, $deactivate);
        }
    }

    private function windowsAddModUser () {

    }

    private function linuxDeleteUserGenerate ($userName) {

        $this->shellScripts['user'] .=  'if [ "`id ' . $userName . ' 2>/dev/null`" != "" ]; then' . "\n";
        $this->shellScripts['user'] .=  '${IONICE}nice -n +19 sudo /usr/sbin/userdel -fr ' . $userName . ' > /dev/null 2>&1 ' . "\n";
        $this->addLogline('user.log', 'User ' . $userName . ' deleted');
        $this->shellScripts['user'] .=  'fi' . "\n";

    }

    private function linuxDelUser ($type) {

        $this->linuxDeleteUserGenerate($this->appServerDetails['userName'] . '-p');

        if ($type == 'both') {
            $this->linuxDeleteUserGenerate($this->appServerDetails['userName']);
        }
    }

    private function windowsDeluser ($type) {

    }

    public function userCud ($action, $type = false, $deactivate = false) {

        if ($this->appServerDetails and isset($this->appMasterServerDetails['os'])) {

            if ($action == 'del') {
                if ($this->appMasterServerDetails['os'] == 'L') {
                    $this->linuxDelUser($type);
                } else {
                    $this->windowsDeluser($type);
                }
            } else {
                if ($this->appMasterServerDetails['os'] == 'L') {
                    $this->linuxAddModUser($deactivate);
                } else {
                    $this->windowsAddModUser($deactivate);
                }
            }

            return true;
        }

        return false;
    }

    // Quotas are a Linux technique to define the diskspace a user is allowed to use.
    public function setQuota () {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L' and $this->appMasterServerDetails['quotaActive'] == 'Y' and strlen($this->appMasterServerDetails['quotaCmd']) > 0 and $this->appServerDetails['hdd'] > 0) {

            // setquota works with KibiByte and Inodes; Stored is Megabyte
            $sizeInKibiByte = $this->appServerDetails['hdd'] * 1024;
            $sizeInByte = $this->appServerDetails['hdd'] * 1048576;
            $blockAmount = round(($sizeInByte /$this->appMasterServerDetails['blocksize']));
            $inodeAmount = round($blockAmount / $this->appMasterServerDetails['inodeBlockRatio']);
            $mountPoint = (isset($this->appMasterServerDetails['iniVars'][$this->appServerDetails['homeLabel']]['mountpoint'])) ? $this->appMasterServerDetails['iniVars'][$this->appServerDetails['homeLabel']]['mountpoint'] : $this->appServerDetails['homeDir'];

            $this->shellScripts['user'] .=  str_replace('%cmd%', ' -u ' . $this->appServerDetails['userName'] . ' ' . $sizeInKibiByte . ' ' . $sizeInKibiByte . ' ' . $inodeAmount . ' ' . $inodeAmount . ' ' . $mountPoint, $this->appMasterServerDetails['quotaCmd']) . "\n";

            $this->addLogline('user.log', 'Userquota set for ' . $this->appServerDetails['userName']);
        }
    }

    /*
     * The next section contains private and public functions.
     * When the root is Linux, they will generate the self deleting temporary shell scripts in ram, write them to the root and execute.
     * When the root is Windows, they will generate the to be executed commands in ram and start them all at once at the end. 
     */

    // Generic function that add a user´s script to the to be generated and executed list
    // The execution of scripts as a user will be sequential and blocking
    // That way we can ensure that a server is installed before it gets started
    private function addLinuxScript ($scriptName, $script, $userName = false, $doNotexecute = false) {

        $userName = ($userName == false) ? $this->appServerDetails['userNameExecute'] : $userName;

        if ($doNotexecute == false) {
            $this->shellScripts['user'] .= 'chmod 770 ' . $scriptName . "\n";
            $this->shellScripts['user'] .= 'sudo -u ' . $userName . ' ' . $scriptName . "\n";
        }

        $this->shellScripts['server']["{$scriptName}"] = $script;
    }

    // Usecase: IP or port was changed for a server. Now the files need to be moved locally
    private function linuxMoveServerLocal ($oldIP, $oldPort) {

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/move-' . $this->appServerDetails['userName'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '-' . $oldIP . '-' . $oldPort . '.sh');
        $script = $this->shellScriptHeader;
        $script .= 'rm ' . $scriptName . "\n";
        $script .= 'cd ' . $this->removeSlashes($this->appServerDetails['homeDir'] . '/' . $this->appServerDetails['userName'] . '/server') . "\n";
        $script .= 'if [ -d "' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port']. '" ]; then rm -rf "' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '"; fi' . "\n";
        $script .= 'mv ' . $oldIP . '_' . $oldPort . ' ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . "\n";

        $this->addLinuxScript($scriptName, $script);

        $this->addLogline('app_server.log', 'moved app from ' . $oldIP . '_' . $oldPort . ' to ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port']);
    }

    public function moveServerLocal ($oldIP, $oldPort) {

        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
            $this->linuxMoveServerLocal($oldIP, $oldPort);
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {

        }
    }

    // Function that generated the script for adding an app
    private function linuxAddApp ($templates, $standalone = true) {

        if ($standalone) {
            $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/add-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '-apps.sh');
        }

        $serverDir = ($this->appServerDetails['protectionModeStarted'] == 'Y') ? 'pserver/' : 'server/';
        $absolutePath = $this->removeSlashes($this->appServerDetails['homeDir'] . '/' . $this->appServerDetails['userName'] . '/' . $serverDir . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port']);

        $copyFileExtensions = array('xml', 'vdf', 'cfg', 'con', 'conf', 'config', 'ini', 'gam', 'txt', 'log', 'smx', 'sp', 'db', 'lua', 'props', 'properties', 'json', 'example');

        if ($standalone and isset($scriptName)) {
            $script = $this->shellScriptHeader;
            $script .= 'rm ' . $scriptName . "\n";
        } else {
            $script = '';
        }

        $script .= 'PATTERN="valve\|overviews/\|scripts/\|media/\|particles/\|gameinfo.txt\|steam.inf\|/sound/\|steam_appid.txt\|/hl2/\|/overviews/\|/resource/\|/sprites/"' . "\n";

        foreach ($templates as $template) {

            $absoluteTargetTemplatePath = $this->removeSlashes($absolutePath . '/' . $template . '/');
            $sourceTemplate = (substr($template, -2) == '-2' or substr($template, -2) == '-3') ? substr($template, 0, (strlen($template) -2)) : $template;
            $absoluteSourceTemplatePath = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/masterserver/' . $sourceTemplate . '/');

            $script .= 'if [ ! -d "' . $absoluteTargetTemplatePath . '" ]; then mkdir -p "' . $absoluteTargetTemplatePath . '"; fi' . "\n";
            $script .= 'cd ' . $absoluteSourceTemplatePath . "\n";
            $script .= 'FDLFILEFOUND=(`find -mindepth 1 -type f -name "*.' . implode('" -o -name "*.', $copyFileExtensions) . '" | grep -v "$PATTERN"`)' . "\n";
            $script .= 'for FILTEREDFILES in ${FDLFILEFOUND[@]}; do' . "\n";
            $script .= 'FOLDERNAME=`dirname "$FILTEREDFILES"`' . "\n";
            $script .= 'if ([[ `find "$FOLDERNAME" -maxdepth 0 -type d` ]] && [[ ! -d "' . $absoluteTargetTemplatePath . '$FOLDERNAME" ]]); then mkdir -p "' . $absoluteTargetTemplatePath . '$FOLDERNAME"; fi' . "\n";
            $script .= 'if [ -f "' . $absoluteTargetTemplatePath . '$FILTEREDFILES" ]; then find "' . $absoluteTargetTemplatePath . '$FILTEREDFILES" -maxdepth 1 -type l -delete; fi' . "\n";
            $script .= 'if [ ! -f "' . $absoluteTargetTemplatePath . '$FILTEREDFILES" ]; then ${IONICE}cp "' . $absoluteSourceTemplatePath . '$FILTEREDFILES" "' . $absoluteTargetTemplatePath . '$FILTEREDFILES"; fi' . "\n";
            $script .= 'done' . "\n";
            $script .= 'cp -sr ' . $absoluteSourceTemplatePath . '* ' . $absoluteTargetTemplatePath . ' > /dev/null 2>&1 ' . "\n";

            $this->addLogline('app_server.log', 'Server template ' . $absoluteTargetTemplatePath . ' owned by user ' . $this->appServerDetails['userNameExecute'] . ' added/synced');
        }

        $dirChmod = 700;
        $fileChmod = 600;

        if ($this->appServerDetails['protectionModeStarted'] == 'Y') {
            $dirChmod = 750;
            $fileChmod = 640;
        }
        $script .= '${IONICE}nice -n +19 find ' . $absolutePath . '/ -type d -print0 | xargs -0 chmod ' . $dirChmod . "\n";
        $script .= '${IONICE}nice -n +19 find ' . $absolutePath . '/ -type f -print0 | xargs -0 chmod ' . $fileChmod . "\n";
        $script .= '${IONICE}nice -n +19 find -L ' . $absolutePath . '/ -type l -delete' . "\n";

        if ($standalone and isset($scriptName)) {
            $this->addLinuxScript($scriptName, $script);
        }

        return $script;
    }

    public function addApp ($templates = array()) {

        if (count($templates) == 0) {
            $templates = array($this->appServerDetails['app']['templateChoosen']);
        }

        if ($this->appServerDetails) {
            if ($this->appMasterServerDetails['os'] == 'L') {
                $this->linuxAddApp($templates);
            } else if ($this->appMasterServerDetails['os'] == 'W') {
            }
        }
    }

    private function linuxRemoveApp($templates) {

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/del-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '-templates.sh');
        $serverDir = ($this->appServerDetails['protectionModeStarted'] == 'Y') ? 'pserver/' : 'server/';

        $script = $this->shellScriptHeader;
        $script .= 'rm ' . $scriptName . "\n";
        $script .= 'cd ' . $this->removeSlashes($this->appServerDetails['homeDir'] . $this->appServerDetails['userName'] . '/' . $serverDir . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '/') . "\n";

        foreach ($templates as $template) {
            $script .= 'if [ -d "' . $template . '" ]; then ${IONICE}rm -rf "' . $template . '"; fi' . "\n";
            $this->addLogline('app_server.log', 'Server template ' . $serverDir . $template . ' owned by user ' . $this->appServerDetails['userNameExecute'] . ' deleted');
        }

        $this->addLinuxScript($scriptName, $script);
    }

    public function removeApp ($templates) {

        if ($this->appServerDetails) {

            $this->easyAntiCheatSettings('stop');

            if (count($templates) > 0) {
                if ($this->appMasterServerDetails['os'] == 'L') {
                    $this->linuxRemoveApp($templates);
                } else if ($this->appMasterServerDetails['os'] == 'W') {
                }
            }
        }
    }

    private function linuxMcWorldSave ($standalone = true) {

        $screenName = $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'];
        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/worldsave-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

        if ($standalone === true) {

            $script = $this->shellScriptHeader;
            $script .= 'rm ' . $scriptName . "\n";

        } else {
            $script = '';
        }

        $script .= 'if [ "`screen -ls | grep ' . $screenName . ' | awk \'{print $1}\'`" != "" ]; then' . "\n";
        $script .= 'screen -p 0 -S ' . $screenName . ' -X stuff $\'\n\'' . "\n";
        $script .= 'screen -p 0 -S ' . $screenName . ' -X stuff "say SERVER WILL SAVE THE WORLD NOW"' . "\n";
        $script .= 'screen -p 0 -S ' . $screenName . ' -X stuff $\'\n\'' . "\n";
        $script .= 'screen -p 0 -S ' . $screenName . ' -X stuff $\'\n\'' . "\n";
        $script .= 'screen -p 0 -S ' . $screenName . ' -X stuff "save-all"' . "\n";
        $script .= 'screen -p 0 -S ' . $screenName . ' -X stuff $\'\n\'' . "\n";
        $script .= 'sleep 10' . "\n";
        $script .= 'fi' . "\n";

        if ($standalone === true) {

            $this->addLinuxScript($scriptName, $script);

            $this->addLogline('app_server.log', 'Minecraft worldsave ' . $screenName . ' owned by user ' . $this->appServerDetails['userNameExecute']);
        }

        return $script;
    }

    public function mcWorldSave () {
        if ($this->appServerDetails and $this->getGameType() == 'mc') {
            if ($this->appMasterServerDetails['os'] == 'L') {
            } else if ($this->appMasterServerDetails['os'] == 'W') {
            }
        }
    }

    private function linuxStopApp ($standalone = true, $scriptName = '') {

        $screenName = $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'];

        if ($standalone === true) {

            $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/stop-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

            $script = $this->shellScriptHeader;
            $script .= 'rm ' . $scriptName . "\n";

        } else {
            $script = '';
        }

        $script .= 'screen -wipe > /dev/null 2>&1' . "\n";
        $script .= 'if [[ `screen -ls | grep ' . $screenName . '` ]]; then' . "\n";
        $script .= 'if [ "`screen -ls | grep ' . $screenName . ' | wc -l`" == "1" ]; then screen -r ' . $screenName . ' -X quit; fi' . "\n";

        if ($this->appServerDetails['template']['gameq'] == 'minecraft') {
            $script .= $this->linuxMcWorldSave(false);
        }

        $gameType = $this->getGameType();

        if ($gameType == 'hl2' and $this->appServerDetails['tvAllowed'] == 'Y') {
            $script .= 'screen -p 0 -S ' . $screenName . ' -X stuff $\'\n\'' . "\n";
            $script .= 'screen -p 0 -S ' . $screenName . ' -X stuff "tv_stoprecord"' . "\n";
            $script .= 'screen -p 0 -S ' . $screenName . ' -X stuff $\'\n\'' . "\n";
        }

        $script .= 'fi' . "\n";

        $script .= 'ps x | grep -v ' . $scriptName . ' | grep ' . $screenName . ' | grep -v grep | awk \'{print $1}\' | while read PID; do' . "\n";
        $script .= 'kill $PID > /dev/null 2>&1' . "\n";
        $script .= 'kill -9 $PID > /dev/null 2>&1' . "\n";
        $script .= 'done' . "\n";

        $script .= 'ps x | grep -v ' . $scriptName . ' | grep ' . $screenName . ' | grep java | grep -v grep | awk \'{print $1}\' | while read PID; do' . "\n";
        $script .= 'kill $PID > /dev/null 2>&1' . "\n";
        $script .= 'kill -9 $PID > /dev/null 2>&1' . "\n";
        $script .= 'done' . "\n";

        if ($gameType == 'hl2' and $this->appServerDetails['tvAllowed'] == 'Y' and in_array($this->appServerDetails['app']['upload'], array(2, 3)) and $this->appServerDetails['app']['uploadDir']) {
            $script .= $this->linuxDemoUpload(false);
        }

        if ($standalone === true) {
            $this->addLinuxScript($scriptName, $script);
            $this->addLogline('app_server.log', 'App ' . $screenName . ' owned by user ' . $this->appServerDetails['userNameExecute'] . ' stopped');
        }

        return $script;
    }

    public function stopApp () {

        global $sql;

        if ($this->appServerDetails) {

            $this->easyAntiCheatSettings('stop');

            if ($this->appMasterServerDetails['os'] == 'L') {
                $this->linuxStopApp();
            } else if ($this->appMasterServerDetails['os'] == 'W') {
            }

            $query = $sql->prepare("UPDATE `gsswitch` SET `stopped`='Y' WHERE `id`=? LIMIT 1");
            $query->execute(array($this->appServerDetails['id']));
        }
    }

    private function linuxHardStop ($userName) {

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/hardstop-' . $userName . '.sh');

        $script = $this->shellScriptHeader;
        $script .= 'rm ' . $scriptName . "\n";

        $script .= 'crontab -r' . "\n";
        $script .= 'screen -wipe > /dev/null 2>&1' . "\n";
        $script .= 'pkill -u `whoami`' . "\n";

        $this->addLinuxScript($scriptName, $script, $userName);
        $this->addLogline('app_server.log', 'Hard stop for user ' . $userName);
    }

    public function stopAppHard () {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {

            $this->linuxHardStop($this->appServerDetails['userName']);

            if ($this->appServerDetails['protectionModeAllowed'] == 'Y') {
                $this->linuxHardStop($this->appServerDetails['userNameExecute']);
            }

        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    private function protectedSettingsToArray () {

        $cvarProtectArray = array();
        $lendServerReplaceMents = array();

        $replaceSettings = $this->getReplacements();

        foreach (explode("\n", $this->appServerDetails['template']['configedit']) as $line) {

            $line = str_replace(array("\r"), '', $line);

            if (preg_match('/^(\[[\w\/\.\-\_]{1,}\]|\[[\w\/\.\-\_]{1,}\] (xml|ini|cfg|lua|json))$/', $line)) {

                $exploded = preg_split("/\s+/", $line, -1, PREG_SPLIT_NO_EMPTY);

                $cvarType = (isset($exploded[1])) ? $exploded[1] : 'cfg';

                $configPathAndFile = substr($exploded[0], 1, strlen($exploded[0]) - 2);

                $cvarProtectArray[$configPathAndFile]['type'] = $cvarType;

            } else if (isset($configPathAndFile) and isset($cvarProtectArray[$configPathAndFile]['type'])) {

                unset($splitLine);

                if ($cvarProtectArray[$configPathAndFile]['type'] == 'cfg') {

                    $splitLine = preg_split("/\s+/", $line, -1, PREG_SPLIT_NO_EMPTY);

                } else if ($cvarProtectArray[$configPathAndFile]['type'] == 'ini') {

                    $splitLine = preg_split("/\=/", $line, -1, PREG_SPLIT_NO_EMPTY);

                } else if ($cvarProtectArray[$configPathAndFile]['type'] == 'lua') {

                    $splitLine = preg_split("/\=/", $line, -1, PREG_SPLIT_NO_EMPTY);

                } else if ($cvarProtectArray[$configPathAndFile]['type'] == 'json') {

                    $splitLine = preg_split("/:/", $line, -1, PREG_SPLIT_NO_EMPTY);

                // In case of XML configs the splitting is more complicated
                } else if ($cvarProtectArray[$configPathAndFile]['type'] == 'xml') {

                    $exploded = explode('>', $line);

                    if (isset($exploded[1])) {

                        $key = str_replace('<', '', $exploded[0]);

                        @list($value) = explode('<', $exploded[1]);

                        $splitLine = array($key, $value);
                    }
                }

                if (isset($splitLine[1])) {

                    $replacedLine = str_replace($replaceSettings['placeholder'], $replaceSettings['replacePlaceholderWith'], $splitLine[1]);

                    foreach (customColumns('G', $this->appServerDetails['id']) as $customColumn) {
                        $replacedLine = str_replace('%' . $customColumn['name'] . '%', $customColumn['value'], $replacedLine);
                    }

                    $cvarProtectArray[$configPathAndFile]['cvars'][$splitLine[0]] = $replacedLine;
                }
            }
        }

        if ($this->appServerDetails['lendServer'] == 'Y') {

            if ($this->appServerDetails['lendServer'] == 'Y') {
                $lendDetails = $this->getLendDetails();
            }

            if (!isset($lendDetails) or !is_array($lendDetails)) {
                $lendDetails = array('rcon' => '', 'password' => '', 'slots' => $this->appServerDetails['slots']);
            }

            $gameType = $this->getGameType();

            if ($gameType == 'mc') {
                $lendServerReplaceMents = array('enable-rcon' => 'true', 'rcon.password' => $lendDetails['rcon']);
            } else if ($gameType == 'hl2' or $gameType == 'hl1') {
                $lendServerReplaceMents = array('sv_password' => $lendDetails['password'], 'rcon' => $lendDetails['rcon']);
            } else if ($gameType == 'cod') {
                $lendServerReplaceMents = array('g_password' => $lendDetails['password'], 'rcon_password' => $lendDetails['rcon']);
            } else if ($gameType == 'teeworlds') {
                $lendServerReplaceMents = array('sv_password' => $lendDetails['password'], 'sv_rcon_password' => $lendDetails['rcon']);
            } else if ($gameType == 'samp') {
                $lendServerReplaceMents = array('password' => $lendDetails['password'], 'rcon' => 1, 'rcon_password' => $lendDetails['rcon']);
            }
        }

        // Remove configs that do not contain any overwrite settings
        // Removing will prevent unnecessary FTP connections
        foreach ($cvarProtectArray as $config => $values) {

            if ($this->appServerDetails['lendServer'] == 'Y') {

                foreach ($lendServerReplaceMents as $cvar => $value) {
                    $cvarProtectArray[$config]['cvars'][$cvar] = $value;
                }
            }

            if (!isset($values['cvars']) or count($values['cvars']) == 0) {
                unset($cvarProtectArray[$config]);
            }
        }

        return $cvarProtectArray;
    }

    private function getFileAndPathName($fileWithPath) {

        $splitConfigPath = preg_split('/\//', $this->removeSlashes($fileWithPath), -1, PREG_SPLIT_NO_EMPTY);
        $folderFileCount = count($splitConfigPath) - 1;

        $i = 0;

        $path = '';

        while ($i < $folderFileCount) {
            $path .= '/' . $splitConfigPath[$i];
            $i++;
        }

        return array('path' => $path, 'file' => $splitConfigPath[$i]);
    }

    private function correctProtectedFiles () {

        $protectedConfigs = $this->protectedSettingsToArray();

        if (count($protectedConfigs) > 0) {

            $ftpObect = new EasyWiFTP($this->appMasterServerDetails['ssh2IP'], $this->appMasterServerDetails['ftpPort'], $this->appServerDetails['userNameExecute'], $this->appServerDetails['ftpPasswordExecute']);

            if ($ftpObect->loggedIn === true) {

                foreach ($protectedConfigs as $config => $values) {

                    $cvarsNotFound = $values['cvars'];

                    $fileWithPath = $this->appServerDetails['absoluteFTPPath'] . '/' . $config;

                    $fileAndPath = $this->getFileAndPathName($fileWithPath);

                    $path = $fileAndPath['path'];
                    $fileName = $fileAndPath['file'];

                    $ftpObect->downloadToTemp($fileWithPath);

                    $configFileContent = $ftpObect->getTempFileContent();

                    // We have one temp handle for all files to reduce the amount of needed ram
                    $ftpObect->tempHandle = null;

                    // Depending how the file was uploaded and written, there might be lots of not needed characters in the file
                    // A clean up will make the file handling lot easier
                    $configFileContent = str_replace(array("\0","\b","\r","\Z"),"", $configFileContent);

                    $lines = explode("\n", $configFileContent);
                    $lineCount = count($lines) - 1;
                    $i = 0;

                    // iterate over all lines
                    foreach ($lines as $singeLine) {

                        // Set to false on each iteration to be able to detect config overwrites
                        $edited = false;

                        // For easier comparison make a string to lower
                        $loweredSingleLine = strtolower($singeLine);

                        foreach ($values['cvars'] as $cvar => $value) {

                            if ($values['type'] == 'cfg' and preg_match('/^[\s\/]{0,}' . strtolower($cvar) . '\s+(.*)$/', $loweredSingleLine)) {

                                $edited = true;

                                unset($cvarsNotFound[$cvar]);

                                $splitLine = preg_split('/' . $cvar . '/', $singeLine, -1, PREG_SPLIT_NO_EMPTY);

                                $ftpObect->writeContentToTemp((isset($splitLine[1])) ? $splitLine[0] . $cvar . '  ' . $value : $cvar . '  ' . $value);

                            } else if ($values['type'] == 'ini' and preg_match('/^[\s\/]{0,}' . strtolower($cvar) . '[\s+]{0,}\=[\s+]{0,}(.*)$/', $loweredSingleLine)) {

                                $edited = true;

                                unset($cvarsNotFound[$cvar]);

                                $ftpObect->writeContentToTemp($cvar . '=' . $value);

                            } else if ($values['type'] == 'lua' and preg_match("/^(.*)" . strtolower($cvar) . "[\s+]{0,}\=[\s+]{0,}(.*)[\,]$/", $loweredSingleLine)) {

                                $edited = true;

                                unset($cvarsNotFound[$cvar]);

                                $splitLine = preg_split('/' . $cvar . '/', $singeLine, -1, PREG_SPLIT_NO_EMPTY);

                                $ftpObect->writeContentToTemp((isset($splitLine[1])) ? $splitLine[0] . $cvar. ' = ' .$value : $cvar . '=' . $value);

                            } else if ($values['type'] == 'json' and preg_match("/^(.*)[\"]" . strtolower($cvar) . "[\s+]{0,}:[\s+]{0,}(.*)[\,]{0,1}$/", $loweredSingleLine)) {

                                $edited = true;

                                unset($cvarsNotFound[$cvar]);

                                $splitLine = preg_split('/' . $cvar . '/', $singeLine, -1, PREG_SPLIT_NO_EMPTY);

                                $ftpObect->writeContentToTemp((isset($splitLine[1])) ? $splitLine[0] . $cvar. ' : ' .$value : $cvar . ':' . $value);

                            } else if ($values['type'] == 'xml' and preg_match("/^(.*)<" . strtolower($cvar) . ">(.*)<\/" . strtolower($cvar) . ">(.*)$/", $loweredSingleLine)) {

                                $edited = true;

                                unset($cvarsNotFound[$cvar]);

                                $splitLine = preg_split('/\<' . $cvar . '/', $singeLine, -1, PREG_SPLIT_NO_EMPTY);

                                $ftpObect->writeContentToTemp((isset($splitLine[1])) ? $splitLine[0] . '<' .$cvar . '>' . $value . '</' . $cvar . '>' : '<' . $cvar . '> ' . $value . '</' . $cvar . '>');
                            }
                        }

                        // Write untouched content
                        if ($edited == false) {
                            $ftpObect->writeContentToTemp($singeLine);
                        }

                        // If we do not count, we would add a newline at the end every time, a file is edited
                        if ($i < $lineCount) {
                            $ftpObect->writeContentToTemp("\r\n");
                        }

                        $i++;
                    }

                    // In case of ini or CFG files we can add entries, which are missing from the file and should be protected
                    foreach ($cvarsNotFound as $cvar => $value) {

                        if ($values['type'] == 'cfg') {

                            $ftpObect->writeContentToTemp($cvar . '  ' . $value . "\r\n");

                        } else if ($values['type'] == 'ini') {

                            $ftpObect->writeContentToTemp($cvar . '=' . $value . "\r\n");
                        }
                    }

                    $ftpObect->uploadFileFromTemp($path, $fileName, false);
                }
            }

            $ftpObect->logOut();
        }
    }

    // If EAC is available and active the server.cfg needs to be retrieved and EAC setup
    private function easyAntiCheatSettings ($action = 'start') {

        global $resellerLockupID;

        if ($this->appServerDetails['eacAllowed'] == 'Y') {

            $gameType = $this->getGameType();

            // On app start we only run commands for supported games
            if ($action == 'start' and in_array($this->appServerDetails['app']['anticheat'], array(3, 4, 5, 6)) and ($gameType == 'hl1' or $gameType == 'hl2')) {

                if ($gameType == 'hl2') {
                    $config = 'cfg/server.cfg';
                } else if ($gameType == 'hl1') {
                    $config = 'server.cfg';
                } else {
                    $config = 'main/server.cfg';
                }

                $ftpObect = new EasyWiFTP($this->appMasterServerDetails['ssh2IP'], $this->appMasterServerDetails['ftpPort'], $this->appServerDetails['userNameExecute'], $this->appServerDetails['ftpPasswordExecute']);

                if ($ftpObect->loggedIn === true) {

                    $ftpObect->downloadToTemp($this->appServerDetails['absoluteFTPPath'] . $config);

                    $configFile = $ftpObect->getTempFileContent();

                    $configFile = str_replace(array("\0","\b","\r","\Z"), '', $configFile);
                    $configFile = preg_replace('/\s+/', ' ', $configFile);

                    $lines = explode("\n", $configFile);

                    foreach ($lines as $singeLine) {

                        // Do a rough check if the line is a comment
                        if (preg_match("/\w/", substr($singeLine, 0, 1))) {

                            if (preg_match("/\"/", $singeLine)) {

                                $exploded = explode('"', $singeLine);
                                $cvar = str_replace(' ', '', $exploded[0]);

                            } else {

                                $exploded = explode(' ', $singeLine);
                                $cvar = $exploded[0];
                            }

                            if ($cvar == 'rcon_password' and isset($exploded[1])) {
                                $rconPassword = $exploded[1];
                            }
                        }
                    }

                    if (isset($rconPassword)) {
                        eacchange('change', $this->appServerDetails['template']['id'], $rconPassword, $resellerLockupID);
                    }
                }

            // On app stop we run commands in any case to ensure we remove left overs
            } else if ($action == 'stop') {
                eacchange('remove', $this->appServerDetails['template']['id'], '', $resellerLockupID);
            }
        }
    }

    private function generateStartCommand () {

        $gameType = $this->getGameType();

        // https://github.com/easy-wi/developer/issues/205
        // In case Workshop is on we need to remove mapgroup
        $startCommand = ($this->appServerDetails['app']['workShop'] == 'Y') ? str_replace(array('%mapgroup%', ' +mapgroup'), '', $this->appServerDetails['app']['cmd']) : $this->appServerDetails['app']['cmd'];

        // In case of hl2 based servers and no TV allowed, turn off the source tv capabilities
        if ($gameType == 'hl2' and $this->appServerDetails['tvAllowed'] == 'N') {
            $startCommand .= ' -nohltv -tvdisable';
        }

        // If the user decided to use EAC instead of VAC, or turned VAC off on porpuse
        if (($gameType == 'hl1' or $gameType == 'hl2') and ($this->appServerDetails['app']['anticheat'] == 2 or ($this->appServerDetails['app']['anticheat'] > 2 and $this->appServerDetails['eacAllowed'] == 'Y'))) {
            $startCommand .= ' -insecure';
        }

        // Mod commands are typically used at CS:GO, COD and so and load build in modifications like arms race
        $modCommand = $this->appServerDetails['app']['modcmd'];

        foreach (explode("\r\n", $this->appServerDetails['template']['modcmds']) as $line) {
            if (preg_match('/^(\[[\w\/\.\-\_\= ]{1,}\])$/', $line)) {

                $exploded = preg_split("/\=/", trim($line,'[]'), -1, PREG_SPLIT_NO_EMPTY);
                $name = trim($exploded[0]);

                // This construction appears redundant, but is required as a fallback in case there was an issue in the DB and at least one mod command is required
                if (isset($exploded[1]) and trim($exploded[1]) == 'default' and ($modCommand === null or $modCommand == '')) {
                    $modCommand = trim($exploded[0]);
                }

                if (!isset($modsCmds[$name])) {
                    $modsCmds[$name] = array();
                }

            } else if (isset($name) and isset ($modsCmds[$name]) and $line != '') {
                $modsCmds[$name][] = $line;
            }
        }

        if (isset($modsCmds[$modCommand]) and is_array($modsCmds[$modCommand])) {
            foreach ($modsCmds[$modCommand] as $singleModADD) {
                $startCommand .= ' ' . $singleModADD;
            }
        }

        // Steam Workshop support
        if ($this->appServerDetails['app']['workShop'] == 'Y' and strlen($this->appServerDetails['app']['webApiAuthKey']) > 0 and strlen($this->appServerDetails['app']['workshopCollection']) > 0) {
            if (in_array($this->appServerDetails['template']['shorten'], array('gmod', 'garrysmod'))) {
                $startCommand .= ' -nodefaultmap +host_workshop_collection ' . $this->appServerDetails['app']['workshopCollection'] . ' -authkey ' . $this->appServerDetails['app']['webApiAuthKey'];
            } else {
                $startCommand .= ' -nodefaultmap +host_workshop_collection ' . $this->appServerDetails['app']['workshopCollection'] . ' +workshop_start_map ' . $this->appServerDetails['app']['map'] . ' -authkey ' . $this->appServerDetails['app']['webApiAuthKey'];
                $startCommand = preg_replace('/[\s\s+]{1,}\+map[\s\s+]{1,}[\w-_!%]{1,}/', '', $startCommand);
            }
        }

        if ($this->appServerDetails['lendServer'] == 'Y') {

            $lendDetails = $this->getLendDetails();

            if (!is_array($lendDetails)) {
                $startCommand .= $this->getLendDetails();
            }
        }

        // Add addon commands
        if (isset($this->appServerDetails['extensions']['cmds']) and count($this->appServerDetails['extensions']['cmds']) > 0) {
            $startCommand .= implode('', $this->appServerDetails['extensions']['cmds']);
        }

        // Remove what needs to be removed according to installed addons
        if (isset($this->appServerDetails['extensions']['removeCmds']) and count($this->appServerDetails['extensions']['removeCmds']) > 0) {
            foreach ($this->appServerDetails['extensions']['removeCmds'] as $removeCommand) {
                $startCommand = str_replace($removeCommand, '', $startCommand);
            }
        }

        $replaceSettings = $this->getReplacements();

        $startCommand = str_replace($replaceSettings['placeholder'], $replaceSettings['replacePlaceholderWith'], $startCommand);

        foreach (customColumns('G', $this->appServerDetails['id']) as $customColumn) {
            $startCommand = str_replace("%${customColumn['name']}%", $customColumn['value'], $startCommand);
        }

        //If a template is set up for both OS, we might need to alter the start of the command
        if ($this->appMasterServerDetails['os'] == 'W') {

            if (substr($startCommand, 0, 2) == './') {
                $startCommand = substr($startCommand, 3);
            }

            if (substr($startCommand, 0, 1) == '.') {
                $startCommand = substr($startCommand, 2);
            }

            $shellCommand = '';

        } else {
            $shellCommand = ($this->appServerDetails['useTaskSet'] == 'Y' and strlen($this->appServerDetails['cores']) > 0) ? 'taskset -c ' . $this->appServerDetails['cores'] : '';
            $shellCommand .= ' screen -A -m -d -L -S ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' ' . $startCommand;
        }

        return $shellCommand;
    }

    private function linuxStartApp () {

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/start-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

        $serverDir = $this->appServerDetails['homeDir'] . '/' . $this->appServerDetails['userName'];
        $serverDir .= ($this->appServerDetails['protectionModeStarted'] == 'Y') ? '/pserver/' : '/server/';
        $serverDir = $this->removeSlashes($serverDir);
        $serverTemplateDir = $this->removeSlashes($serverDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '/');

        $script = $this->shellScriptHeader;
        $script .= 'rm ' . $scriptName . "\n";

        $script .= $this->linuxStopApp(false, $scriptName);

        $script .= '${IONICE}find -L ' . $serverDir . ' -type l -delete' . "\n";

        if ($this->appServerDetails['protectionModeStarted'] == 'Y') {
            $script .= '${IONICE}nice -n +19 find ' . $serverDir . ' -type d -print0 | xargs -0 chmod 750' . "\n";
            $script .= '${IONICE}nice -n +19 ' . $serverDir . ' -type f -print0 | xargs -0 chmod 640' . "\n";
        } else {
            $script .= '${IONICE}nice -n +19 find ' . $serverDir . ' -type d -print0 | xargs -0 chmod 700' . "\n";
            $script .= '${IONICE}nice -n +19 find ' . $serverDir . ' -type f -print0 | xargs -0 chmod 600' . "\n";
            $script .= '${IONICE}nice -n +19 find ' . $this->removeSlashes($this->appServerDetails['homeDir'] . '/' . $this->appServerDetails['userName']) . ' -mindepth 2 -maxdepth 3 \( -type f -or -type l \) ! -name \"*.bz2\" -delete' . "\n";
            $script .= '${IONICE}nice -n +19 find /home/' . $this->appMasterServerDetails['ssh2User'] . '/fdl_data -type f -user `whoami` ! -name \"*.bz2\" -delete' . "\n";
        }

        if (count($this->appMasterServerDetails['configBinaries']) > 0 or count($this->appMasterServerDetails['configFiles']) > 0 ) {

            $script .= 'FILESFOUND=(`find ' . $serverDir . ' -type f';

            if (count($this->appMasterServerDetails['configBinaries']) > 0) {
                $script .= ' -name "*.' . implode('" -o -name "*.', $this->appMasterServerDetails['configBinaries']) . '"';
            }

            if (count($this->appMasterServerDetails['configFiles']) > 0) {
                $script .= ' -wholename "' . implode('" -o -wholename "', $this->appMasterServerDetails['configFiles']) . '"';
            }

            $script .= '`)' . "\n";

            $script .= 'for BADFILE in ${FILESFOUND[@]}; do' . "\n";
            $script .= 'chmod 666 $BADFILE > /dev/null 2>&1' . "\n";
            $script .= 'rm $BADFILE > /dev/null 2>&1' . "\n";
            $script .= 'if [ -f $BADFILE ]; then exit 0; fi' . "\n";
            $script .= 'done' . "\n";
        }

        if ($this->appMasterServerDetails['configBadTime'] > 0 and count($this->appMasterServerDetails['configBadFiles']) > 0) {
            $script .= '${IONICE}find ' . $serverDir . ' -type f -name "*.' . implode('" -o -name "*.', $this->appMasterServerDetails['configBadFiles']) . '" -mtime +' . $this->appMasterServerDetails['configBadTime'] . ' -delete' . "\n";
        }

        if ($this->appMasterServerDetails['configDemoTime'] > 0) {
            $script .= '${IONICE}find ' . $serverTemplateDir . ' -type f -name "*.dem"  -mtime +' . $this->appMasterServerDetails['configDemoTime'] . ' -delete' . "\n";
        }

        if ($this->appMasterServerDetails['configLogTime'] > 0) {
            $script .= '${IONICE}find ' . $serverTemplateDir . ' -type f -name "*.log"  -mtime +' . $this->appMasterServerDetails['configLogTime'] . ' -delete' . "\n";
        }

        if ($this->appMasterServerDetails['configZtmpTime'] > 0) {
            $script .= '${IONICE}find ' . $serverTemplateDir . ' -type f -name "*.ztmp"  -mtime +' . $this->appMasterServerDetails['configZtmpTime'] . ' -delete' . "\n";
        }

        $script .= 'cd ' . $this->appServerDetails['absolutePath'] . "\n";
        $script .= 'if [ -f screenlog.0 ]; then rm screenlog.0; fi' . "\n";
        $script .= $this->generateStartCommand() . "\n";

        if ($this->getGameType() == 'hl2' and $this->appServerDetails['tvAllowed'] == 'Y' and in_array($this->appServerDetails['app']['upload'], array(4, 5)) and $this->appServerDetails['app']['uploadDir']) {
            $script .= $this->linuxDemoUpload(false);
        }

        $this->addLinuxScript($scriptName, $script);
        $this->addLogline('app_server.log', 'App ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' owned by user ' . $this->appServerDetails['userNameExecute'] . ' started');
    }

    public function startApp () {

        global $sql;

        if ($this->appServerDetails) {

            $this->getAddonDetails();

            $this->correctProtectedFiles();

            $this->easyAntiCheatSettings();

            if ($this->appMasterServerDetails['os'] == 'L') {

                if ($this->appServerDetails['protectionModeStarted'] == 'Y') {
                    $this->linuxHardStop($this->appServerDetails['userName']);
                } else if ($this->appServerDetails['protectionModeAllowed'] == 'Y' and $this->appServerDetails['protectionModeStarted'] == 'N') {
                    $this->linuxHardStop($this->appServerDetails['userName'] . '-p');
                }

                $this->linuxAddApp(array($this->appServerDetails['app']['templateChoosen']));
                $this->linuxAddAddons();

                $this->linuxStartApp();

            } else if ($this->appMasterServerDetails['os'] == 'W') {
            }

            $query = $sql->prepare("UPDATE `gsswitch` SET `stopped`='N' WHERE `id`=? LIMIT 1");
            $query->execute(array($this->appServerDetails['id']));
        }
    }

    private function linuxDemoUpload ($standalone = true) {

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/demo-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

        if ($standalone == true) {
            $script = $this->shellScriptHeader;
            $script .= 'rm ' . $scriptName . "\n";
        } else {
            $script = '';
        }

        if (in_array($this->appServerDetails['app']['upload'], array(3, 5))) {
            $script .= 'KEEP="-k"' . "\n";
        }

        // This if cases have to be run on the root as the PHP script does not know what is installed there
        $script .= 'LSOF=`which lsof`' . "\n";
        $script .= 'if [ "$LSOF" == "" ]; then KEEP="-k"; fi' . "\n";
        $script .= 'cd ' . $this->appServerDetails['absolutePath'] . "\n";

        $uploadScript = 'if [[ `which zip` ]]; then' . "\n";
        $uploadScript .= 'if [ "$KEEP" == "" ]; then KEEP="-m"; fi' . "\n";
        $uploadScript .= '${IONICE}nice -n +19 zip -q $KEEP $DEMOPATH/$DEMO.zip $DEMOPATH/$DEMO' . "\n";
        $uploadScript .= 'ZIP="zip"' . "\n";
        $uploadScript .= 'elif [[ `which bzip2` ]]; then' . "\n";
        $uploadScript .= '${IONICE}nice -n +19 bzip2 -s -q -9 $KEEP $DEMOPATH/$DEMO' . "\n";
        $uploadScript .= 'ZIP="bz2"' . "\n";
        $uploadScript .= 'fi' . "\n";
        $uploadScript .= 'DEMOANDPATH="$DEMOPATH/$DEMO.$ZIP"' . "\n";
        $uploadScript .= 'wput -q --limit-rate=1024K --remove-source-files --tries 3 --basename="${DEMOPATH/\/\///}" "${DEMOANDPATH/\/\///}" "' . $this->appServerDetails['app']['uploadDir'] . '"' . "\n";

        // 2 and 3 are one time run (manuel mode)
        if (in_array($this->appServerDetails['app']['upload'], array(2, 3))) {
            $script .= 'cd `find -mindepth 1 -maxdepth 3 -type d -name "' . $this->appServerDetails['template']['modfolder'] . '" | head -n1`' . "\n";
            $script .= 'find . -maxdepth 2 -type f -name "*.dem" | while read LINE; do' . "\n";
            $script .= 'DEMOPATH="`dirname $LINE`/"' . "\n";
            $script .= 'DEMO="`basename $LINE`"' . "\n";
            $script .= 'if [ "$LSOF" != "" ]; then ' . "\n";
            $script .= 'if [[ ! `lsof $LINE` ]]; then' . "\n";
            $script .= $uploadScript;
            $script .= 'fi' . "\n";
            $script .= 'else' . "\n";
            $script .= $uploadScript;
            $script .= 'fi' . "\n";
            $script .= 'done' . "\n";

        // 4 and 5 is continuous run with a tail of the screenlog
        } else if (in_array($this->appServerDetails['app']['upload'], array(4, 5))) {

            $script .= 'DEMOPATH=`find -mindepth 1 -maxdepth 3 -type d -name "' . $this->appServerDetails['template']['modfolder'] . '" | head -n1`' . "\n";
            $script .= 'SCREENLOG="`find ' . $this->appServerDetails['absolutePath'] . ' -name "screenlog.0" | head -n1`"' . "\n";
            $script .= 'if [ "$SCREENLOG" != "" ]; then' . "\n";
            $script .= 'cd `dirname $SCREENLOG`' . "\n";
            $script .= 'tail -f screenlog.0 | while read LINE; do' . "\n";

            $script .= 'if [[ `echo $LINE | grep "Completed SourceTV demo"` ]]; then' . "\n";
            $script .= 'DEMO=`echo -n "$LINE" | awk \'{print $4}\' | tr -d \'"\' | tr -d \',\'`' . "\n";

            $script .= 'if [ "$LSOF" != "" ]; then ' . "\n";
            $script .= 'if [[ ! `lsof $DEMOPATH/$DEMO` ]]; then' . "\n";
            $script .= $uploadScript;
            $script .= 'fi' . "\n";
            $script .= 'else' . "\n";
            $script .= $uploadScript;
            $script .= 'fi' . "\n";
            $script .= 'fi' . "\n";
            $script .= 'done' . "\n";
            $script .= 'fi' . "\n";
        }

        if (in_array($this->appServerDetails['app']['upload'], array(2, 3, 4, 5))) {

            // The demo listener needs to be started in a separate screen
            if (in_array($this->appServerDetails['app']['upload'], array(4, 5))) {

                $this->addLinuxScript($scriptName, $script, $this->appServerDetails['userNameExecute'], true);

                $screenScriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/demo-start-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

                $script = $this->shellScriptHeader;
                $script .= 'rm ' . $screenScriptName . "\n";

                // Kill any screen that is running with the same name
                $script .= 'ps fx | grep \'SCREEN\' | grep \'demo_' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '\' | grep -v grep | awk \'{print $1}\' | while read PID; do' . "\n";
                $script .= 'kill $PID > /dev/null 2>&1' . "\n";
                $script .= 'kill -9 $PID > /dev/null 2>&1' . "\n";
                $script .= 'done' . "\n";

                $script .= 'screen -d -m -S demo_' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' ' . $scriptName . "\n";

                // Rename for the function return
                $scriptName = $screenScriptName;
            }

            $this->addLogline('app_server.log', 'Demo upload started for ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' owned by user ' . $this->appServerDetails['userNameExecute']);

            if ($standalone == true) {
                $this->addLinuxScript($scriptName, $script);
            }

            return $script;
        }

        return '';
    }

    public function demoUpload () {
        if ($this->appServerDetails and $this->appServerDetails['app']['uploadDir'] and $this->appMasterServerDetails['os'] == 'L') {
            $this->linuxDemoUpload();
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    private function linuxAddonShellGeneric ($type, $name, $action, $folders = '') {

        $masterAddonFolder = '/home/' . $this->appMasterServerDetails['ssh2User'] . '/';
        $masterAddonFolder .= ($type == 'addon') ? 'masteraddons/' : 'mastermaps/';
        $masterAddonFolder .= $name . '/';

        if (strlen($this->appServerDetails['template']['modfolder']) == 0) {
            $script = 'GAMEDIR="' . $this->appServerDetails['absolutePath'] . '"' . "\n";
        } else {
            $script = 'if [ "`find ' . $this->appServerDetails['absolutePath'] . ' -mindepth 1 -maxdepth 3 -type d -name ' . $this->appServerDetails['template']['modfolder'] . ' | wc -l`" == "1" ]; then' . "\n";
            $script .= 'GAMEDIR=`find ' . $this->appServerDetails['absolutePath'] . ' -mindepth 1 -maxdepth 3 -type d -name "' . $this->appServerDetails['template']['modfolder'] . '" | head -n 1`' . "\n";
            $script .= 'else' . "\n";
            $script .= 'GAMEDIR=`find ' . $this->appServerDetails['absolutePath'] . ' -mindepth 1 -maxdepth 1 -type d -name "' . $this->appServerDetails['template']['modfolder'] . '" | head -n 1`' . "\n";
            $script .= 'fi' . "\n";
        }

        $script .= 'if [ -d "' . $masterAddonFolder . '" -a "$GAMEDIR" != "" ]; then' . "\n";

        $script .= 'cd ' . $masterAddonFolder . "\n";

        if ($action == 'add') {
            $script .= $this->linuxAddAddonShellCommands($type, $masterAddonFolder);
        } else {
            $script .= $this->linuxRemoveAddonShellCommands($folders);
         }

        $script .= 'fi' . "\n";

        return $script;
    }

    private function linuxAddAddonShellCommands ($type, $masterAddonFolder) {

        $script = '';

        if ($type == 'addon') {
            $script = 'find -type f | grep -i -E -w \'(xml|cfg|con|conf|config|gam|ini|txt|vdf|smx|sp|ext|sma|amxx|lua|json)$\' | sed \'s/\.\///g\' | while read FILE; do' . "\n";
            $script .= 'FOLDER=`dirname $FILE`' . "\n";
            $script .= 'FILENAME=`basename $FILE`' . "\n";
            $script .= 'if [ ! -d $GAMEDIR/$FOLDER ]; then mkdir -p $GAMEDIR/$FOLDER/; fi' . "\n";
            $script .= 'find $GAMEDIR/$FILE -type l -delete > /dev/null 2>&1' . "\n";
            $script .= 'if [ "$FILENAME" == "liblist.gam" ]; then' . "\n";
            $script .= 'mv $GAMEDIR/$FILE $GAMEDIR/$FILE.old' . "\n";
            $script .= 'cp ' . $masterAddonFolder . '$FILE $GAMEDIR/$FILE' . "\n";
            $script .= 'elif [ "$FILENAME" == "plugins.ini" ]; then' . "\n";
            $script .= 'if [ -f $GAMEDIR/$FILE ]; then' . "\n";
            $script .= 'cat ' . $masterAddonFolder . '$FILE | while read $LINE; do' . "\n";
            $script .= 'if [ `grep "$LINE" $GAMEDIR/$FILE` == "" ]; then echo $LINE >> $GAMEDIR/$FILE; fi' . "\n";
            $script .= 'done' . "\n";
            $script .= 'else' . "\n";
            $script .= 'cp ' . $masterAddonFolder . '$FILE $GAMEDIR/$FILE' . "\n";
            $script .= 'fi' . "\n";
            $script .= 'elif [ "$FILENAME" == "gametypes.txt" ]; then' . "\n";
            $script .= 'if [ "$FOLDER" != "cfg/mani_admin_plugin" ]; then cp ' . $masterAddonFolder . '$FILE $GAMEDIR/$FILE; fi' . "\n";
            $script .= 'elif [ ! -f $GAMEDIR/$FILE -a ! -f "$GAMEDIR/$FOLDER/disabled/$FILENAME" ]; then' . "\n";
            $script .= 'cp ' . $masterAddonFolder . '$FILE $GAMEDIR/$FILE' . "\n";
            $script .= 'elif [ -a ! -f $GAMEDIR/$FILE ]; then' . "\n";
            $script .= 'cp ' . $masterAddonFolder . '$FILE $GAMEDIR/$FILE' . "\n";
            $script .= 'fi' . "\n";
            $script .= 'done' . "\n";
        }

        $script .= 'cp -sr ' . $masterAddonFolder . '* $GAMEDIR/ > /dev/null 2>&1' . "\n";

        return $script;
    }

    private function linuxAddAddons ($id = false) {

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/addons-add-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

        $script = $this->shellScriptHeader;
        $script .= 'rm ' . $scriptName . "\n";

        if ($id === false) {

            $logLine = '';

            if (isset($this->appServerDetails['extensions']['addons']) and count($this->appServerDetails['extensions']['addons']) > 0) {

                foreach ($this->appServerDetails['extensions']['addons'] as $id => $addon) {

                    // A possible scenario is that the addon has been installed while unprotected and the mode switched later on
                    // In such a case we need to ensure that the addon is not installed on restart
                    if ($this->appServerDetails['protectionModeStarted'] == 'N' or ($this->appServerDetails['protectionModeStarted'] == 'Y' and $this->appServerDetails['extensions']['addonSettings'][$id]['protectedAllowed'] == 'Y')) {
                        $script .= $this->linuxAddonShellGeneric('addon', $addon, 'add');
                    }
                }

                $logLine .= 'added addon(s) ' . implode(',', $this->appServerDetails['extensions']['addons']);
            }

            if (isset($this->appServerDetails['extensions']['maps']) and count($this->appServerDetails['extensions']['maps']) > 0) {

                foreach ($this->appServerDetails['extensions']['maps'] as $addon) {
                    $script .= $this->linuxAddonShellGeneric('map', $addon, 'add');
                }

                $logLine .= ' added map(s) ' . implode(',', $this->appServerDetails['extensions']['maps']);
            }


        } else if (isset($this->appServerDetails['extensions']['addons'][$id])) {

            $script .= $this->linuxAddonShellGeneric('addon', $this->appServerDetails['extensions']['addons'][$id], 'add');

            $logLine = 'Added addon ' . $this->appServerDetails['extensions']['addons'][$id];

        } else if (isset($this->appServerDetails['extensions']['maps'][$id])) {

            $script .= $this->linuxAddonShellGeneric('map', $this->appServerDetails['extensions']['maps'][$id], 'add');

            $logLine = 'Added map ' . $this->appServerDetails['extensions']['maps'][$id];

        }

        if (isset($logLine) and strlen($logLine) > 0) {
            $this->addLinuxScript($scriptName, $script);
            $this->addLogline('app_server.log', $logLine . ' to app ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' owned by user ' . $this->appServerDetails['userNameExecute']);
        }
    }

    public function addAddon ($id = false) {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
            $this->linuxAddAddons($id);
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    private function getDependentAddonsQuery($id) {

        global $sql;

        $array = array();

        $query = $sql->prepare("SELECT a.`id`,a.`addon` FROM `addons` AS a INNER JOIN `addons_installed` AS i ON i.`addonid`=a.`id` AND i.`serverid`=? AND i.`servertemplate`=? WHERE a.`depending`=?");
        $query->execute(array($this->appServerDetails['app']['id'], $this->appServerDetails['app']['servertemplate'], $id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $array[] = $row['id'];
        }

        return $array;
    }

    private function getDependentAddons ($id) {

        $addonIDs = $this->getDependentAddonsQuery($id);

        if (count($addonIDs) > 0) {

            foreach ($addonIDs as $addonID) {
                foreach($this->getDependentAddons($addonID) as $dependentAddonID) {
                    $addonIDs[] = $dependentAddonID;
                }
            }
        }

        return $addonIDs;
    }

    private function linuxRemoveAddonShellCommands ($folders) {

        $script = 'find -mindepth 1 -type f | sed \'s/\.\///g\' | while read FILES; do' . "\n";
        $script .= 'if [ "`basename $FILES`" == "liblist.gam" ]; then' . "\n";
        $script .= 'mv $GAMEDIR/$FILES.old $GAMEDIR/$FILES' . "\n";
        $script .= 'elif [ "`basename $FILES`" == "plugins.ini" ]; then' . "\n";

        $script .= 'if [ -f /home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/$USER.pluginlist.temp ]; then rm /home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/$USER.pluginlist.temp; fi' . "\n";

        $script .= 'cat $GAMEDIR/$FILES | while read LINE; do' . "\n";
        $script .= 'if [[ `grep "$LINE" $FILES` == "" ]]; then  echo "$LINE" >> /home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/$USER.pluginlist.temp; fi' . "\n";
        $script .= 'done' . "\n";
        $script .= 'cp /home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/$USER.pluginlist.temp $GAMEDIR/$FILES' . "\n";
        $script .= 'rm /home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/$USER.pluginlist.temp' . "\n";
        $script .= 'else' . "\n";
        $script .= 'rm -rf "$GAMEDIR/$FILES" > /dev/null 2>&1' . "\n";
        $script .= 'if [ "$FILES" == "liblist.gam" ]; then mv $GAMEDIR/$FILES.old $GAMEDIR/$FILES > /dev/null 2>&1; fi' . "\n";
        $script .= 'fi' . "\n";
        $script .= 'done' . "\n";
        $script .= 'cd $GAMEDIR' . "\n";
        $script .= 'find -mindepth 1 -type d -empty -delete' . "\n";

        // Check for to be removed folders
        if (count($folders) > 0) {
            $script .= 'find -mindepth 1 -name "' . implode('" -o -name "', $folders) . '" -print0 | xargs -0 rm -rf' . "\n";
        }

        return $script;
    }

    private function linuxRemoveAddons ($ids) {

        $names = array();

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/addons-del-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

        $script = $this->shellScriptHeader;
        $script .= 'rm ' . $scriptName . "\n";
        $script .= 'USER=`id -un`' . "\n";

        foreach ($ids as $id) {

            $folders = (isset($this->appServerDetails['extensions']['addonSettings'][$id]['folder'])) ? preg_split('/(\s+|,)/', $this->appServerDetails['extensions']['addonSettings'][$id]['folder'], -1, PREG_SPLIT_NO_EMPTY) : array();

            if (isset($this->appServerDetails['extensions']['addons'][$id])) {

                $names[] = $this->appServerDetails['extensions']['addons'][$id];

                $script .= $this->linuxAddonShellGeneric('addon', $this->appServerDetails['extensions']['addons'][$id], 'del', $folders);

            } else if (isset($this->appServerDetails['extensions']['maps'][$id])) {

                $names[] = $this->appServerDetails['extensions']['maps'][$id];

                $script .= $this->linuxAddonShellGeneric('map', $this->appServerDetails['extensions']['maps'][$id], 'del', $folders);
            }
        }

        if (count($names) > 0) {
            $this->addLinuxScript($scriptName, $script);
            $this->addLogline('app_server.log', 'Removed addon(s)/map(s) ' . implode(',', $names) . ' from app ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' owned by user ' . $this->appServerDetails['userNameExecute']);
        }
    }

    public function removeAddon ($id) {

        global $sql;

        if ($this->appServerDetails) {

            $toBeRemovedAddonIDs = array($id);

            foreach ($this->getDependentAddons($id) as $addonID) {
                $toBeRemovedAddonIDs[] = $addonID;
            }

            if ($this->appMasterServerDetails['os'] == 'L') {
                $this->linuxRemoveAddons($toBeRemovedAddonIDs);
            } else if ($this->appMasterServerDetails['os'] == 'W') {
            }

            $query = $sql->prepare("DELETE FROM `addons_installed` WHERE `addonid`=? AND `serverid`=? AND `servertemplate`=? LIMIT 1");
            foreach ($toBeRemovedAddonIDs as $addonID) {
                $query->execute(array($addonID, $this->appServerDetails['app']['id'], $this->appServerDetails['app']['servertemplate']));
            }
        }
    }

    private function linuxMigrateServer ($sourceFTP, $targetTemplate, $modFolder) {

        $serverDir = $this->removeSlashes($this->appServerDetails['homeDir'] . $this->appServerDetails['userName'] . '/server/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '/' . $targetTemplate . '/');

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/migrate-' . $this->appServerDetails['userName'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

        $script = $this->shellScriptHeader;
        $script .= 'rm ' . $scriptName . "\n";
        $script .= 'if [ -d "' . $serverDir . '" ]; then ${IONICE}rm -rf "' . $serverDir . '"; fi' . "\n";

        $script .= $this->linuxAddApp(array($targetTemplate), false);

        $script .= 'if [ ! -d "' . $serverDir . '" ]; then mkdir -p "' . $serverDir . '"; fi' . "\n";
        $script .= 'cd ' . $serverDir . "\n";

        if (strlen($modFolder) > 0) {
            $script .= 'MODFOLDER=`find -mindepth 1 -maxdepth 3 -type d -name "' . $modFolder . '" | head -n 1`' . "\n";
            $script .= 'if [ "$MODFOLDER" != "" ]; then cd $MODFOLDER; fi' . "\n";
        }

        $cutDirs = count(preg_split('/\//', $sourceFTP['path'], -1, PREG_SPLIT_NO_EMPTY));

        if ($cutDirs < 0) {
            $cutDirs = 0;
        }

        $script .= 'find -type f -print0 | xargs -0 rm -f' . "\n";
        $script .= 'wget -q -r -l inf -nc -nH --limit-rate=4096K --retr-symlinks --no-check-certificate --ftp-user=' . $sourceFTP['user'] . ' --ftp-password=' . $sourceFTP['password'] . ' --cut-dirs=' . $cutDirs . ' ' . $sourceFTP['connectString'] . "\n";

        $script .= $this->linuxAddApp(array($targetTemplate), false);

        $this->addLinuxScript($scriptName, $script);
        $this->addLogline('app_server.log', 'Migrated server to ' . $targetTemplate . ' belonging to app ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' owned by user ' . $this->appServerDetails['userName']);
    }

    public function migrateToEasyWi ($sourceFTP, $targetTemplate, $modFolder) {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
            $this->linuxMigrateServer($sourceFTP, $targetTemplate, $modFolder);
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    private function linuxFastDLSync ($fdlConnectString) {

        $gameType = $this->getGameType();

        if (in_array($gameType, array('hl1', 'hl2', 'cod'))) {

            $fdlFileList = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/conf/fdl-' . $this->appServerDetails['template']['shorten'] . '.list');

            $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/fdl-sync-' . $this->appServerDetails['userName'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

            $script = $this->shellScriptHeader;
            $script .= 'rm ' . $scriptName . "\n";
            $script .= 'USERNAME=`id -un`' . "\n";

            $script .= 'if [ -f "' . $fdlFileList . '" ]; then' . "\n";
            $script .= 'cd ' . $this->appServerDetails['absolutePath'] . "\n";

            $excludePattern = '\.log\|\.txt\|\.cfg\|\.vdf\|\.db\|\.dat\|\.ztmp\|\.blib\|log\/\|logs\/\|downloads\/\|DownloadLists\/\|metamod\/\|amxmodx\/\|hl\/\|hl2\/\|cfg\/\|addons\/\|bin\/\|classes/';

            if ($gameType == 'hl2') {

                $fdlMasterFolder = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/fdl_data/hl2/' . $this->appServerDetails['template']['shorten'] . '/');

                $script .= 'if [ ! -d "' . $fdlMasterFolder . '" ]; then mkdir -p "' . $fdlMasterFolder . '"; fi' . "\n";
                $script .= 'find "' . $fdlMasterFolder . '" -maxdepth 1 -type d -user `whoami` -exec chmod 770 {} \;' . "\n";

                $logFile = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/logs/fdl-hl2.log');

                if (strlen($this->appServerDetails['template']['binarydir']) > 0) {
                    $script .= 'cd ' . $this->appServerDetails['template']['binarydir'] . "\n";
                }

                if ($this->appServerDetails['template']['gameq'] == 'l4d2') {
                    $script .= 'cd left4dead2/left4dead2/' . "\n";
                } else if (strlen($this->appServerDetails['template']['modfolder']) > 0) {
                    $script .= 'cd ' . $this->appServerDetails['template']['modfolder'] . '/' . "\n";
                }

                $script .= 'ABSOLUTEGAMEPATH=`readlink -f .`' . "\n";

                $script .= 'find particles/ maps/ materials/ resource/ models/ sound/ -type l -or -type f 2> /dev/null | grep -v "' . $excludePattern . '" | while read FOUNDFILE; do' . "\n";

            } else if ($gameType == 'hl1') {

                $logFile = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/logs/fdl-hl1.log');

                $script .= 'cd ' . $this->appServerDetails['template']['modfolder'] . '/' . "\n";

                $script .= 'find . -type l -or -type f 2> /dev/null | grep -v "' . $excludePattern . '" | while read FOUNDFILE; do' . "\n";

            } else {

                $logFile = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/logs/fdl-cod.log');

                $script .= 'find usermaps/ mods/ -type l -or -type f \( -iname "*.ff" -or -iname "*.iwd" \) 2> /dev/null | grep -v "' . $excludePattern . '" | while read FOUNDFILE; do' . "\n";
            }

            $script .= 'FILTEREDFILE=${FOUNDFILE//\.\//}' . "\n";
            $script .= 'if [[ ! `grep "$FILTEREDFILE" "' . $fdlFileList . '"` ]]; then' . "\n";
            $script .= 'FILENAME=`basename $FILTEREDFILE`' . "\n";

            if ($gameType == 'hl2' and isset($fdlMasterFolder)) {

                $script .= 'cd ' . $fdlMasterFolder . "\n";
                $script .= 'ABSOLUTEFILTEREDFILE="$ABSOLUTEGAMEPATH/$FILTEREDFILE"' . "\n";
                $script .= 'FDLDATADIR=' . $fdlMasterFolder . '`dirname "$FILTEREDFILE"`' . "\n";
                $script .= 'if [ ! -d $FDLDATADIR ]; then mkdir -p $FDLDATADIR; chmod 770 $FDLDATADIR; fi' . "\n";
                $script .= 'FDLDATAFILENAME="$FDLDATADIR/$FILENAME"' . "\n";
                $script .= 'CHECKSUMNEW=`${IONICE}nice -n +19 md5sum "$ABSOLUTEFILTEREDFILE" | awk \'{print $1}\'`' . "\n";
                $script .= 'if [ -f "$FDLDATAFILENAME.stat" -a -f "$FDLDATAFILENAME.bz2" ]; then' . "\n";
                $script .= 'CHECKSUMOLD=`head -n 1 "$FDLDATAFILENAME.stat" 2> /dev/null`' . "\n";
                $script .= 'else' . "\n";
                $script .= 'CHECKSUMOLD=""' . "\n";
                $script .= 'fi' . "\n";
                $script .= 'if [ "$CHECKSUMOLD" != "$CHECKSUMNEW" ]; then' . "\n";
                $script .= '${IONICE}nice -n +19 bzip2 -k -s -q -9 -f -c "$ABSOLUTEFILTEREDFILE" > "$FDLDATAFILENAME.bz2"' . "\n";
                $script .= 'echo $CHECKSUMNEW > "$FDLDATAFILENAME.stat"' . "\n";
                $script .= 'chmod 660 "$FDLDATAFILENAME.stat" "$FDLDATAFILENAME.bz2"' . "\n";
                $script .= 'fi' . "\n";
                $script .= 'if [ "$CHECKSUMOLD" != "$CHECKSUMNEW" -a "$CHECKSUMOLD" != "" ]; then' . "\n";
                $script .= 'wput -q --reupload --limit-rate=1024K "$FILTEREDFILE.bz2" ' . $fdlConnectString . "\n";
                $script .= 'echo "`date`: $USERNAME: ' . $this->appServerDetails['app']['templateChoosen'] . ' file $FILENAME compressed and uploaded" >> ' . $logFile . "\n";
                $script .= 'else' . "\n";
                $script .= 'wput -q --dont-continue --limit-rate=1024K "$FILTEREDFILE.bz2" ' . $fdlConnectString . "\n";
                $script .= 'echo "`date`: $USERNAME: ' . $this->appServerDetails['app']['templateChoosen'] . ' file $FILENAME uploaded" >> ' . $logFile . "\n";
                $script .= 'fi' . "\n";

            } else {
                $script .= 'if [ "`wput -q -nv --limit-rate=1024K "$FILTEREDFILE" ' . $fdlConnectString . ' | grep \"Skipping file\"`" != "" ]; then' . "\n";
                $script .= 'wput -qN --limit-rate=1024K "$FILTEREDFILE" ' . $fdlConnectString . "\n";
                $script .= 'echo "`date`: $USERNAME: ' . $this->appServerDetails['app']['templateChoosen'] . ' file $FILENAME checked" >> ' . $logFile . "\n";
                $script .= 'else' . "\n";
                $script .= 'echo "`date`: $USERNAME: ' . $this->appServerDetails['app']['templateChoosen'] . ' file $FILENAME uploaded" >> ' . $logFile . "\n";
                $script .= 'fi' . "\n";
            }

            $script .= 'fi' . "\n";
            $script .= 'done' . "\n";

            if ($gameType == 'hl2' and isset($fdlMasterFolder)) {
                $script .= 'find "' . $fdlMasterFolder . '" -type d -user $USERNAME -exec chmod 770 {} \;' . "\n";
                $script .= 'find "' . $fdlMasterFolder . '" -type f -user $USERNAME -exec chmod 660 {} \;' . "\n";
            }

            $script .= 'fi' . "\n";

            $this->addLinuxScript($scriptName, $script);
            $this->addLogline('fdl.log', 'FDL sync started for app on server ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' owned by user ' . $this->appServerDetails['userName']);
        }
    }

    public function fastDLSync ($fdlConnectString) {

        if (strlen($fdlConnectString) > 0) {

            if (substr($fdlConnectString, -1, 1) != '/') {
                $fdlConnectString .= '/';
            }

            $fdlConnectString .= $this->appServerDetails['template']['shorten'] . '/';

            if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
                $this->linuxFastDLSync($fdlConnectString);
            } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
            }
        }
    }

    private function linuxBackupCreate ($ftpUploadString) {

        global $resellerLockupID;

        $backupDir = $this->removeSlashes($this->appServerDetails['homeDir'] . $this->appServerDetails['userName'] . '/backup/');
        $serverDir = $this->removeSlashes($this->appServerDetails['homeDir'] . $this->appServerDetails['userName'] . '/server/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '/');

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/backup-create-' . $this->appServerDetails['userName'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

        $script = $this->shellScriptHeader;
        $script .= 'rm ' . $scriptName . "\n";

        $script .= 'if [ ! -d "' . $backupDir . '" ]; then mkdir -p "' . $backupDir . '"; fi' . "\n";
        $script .= 'find "' . $backupDir . '" -maxdepth 1 -type f -name "*.tar.bz2" -delete' . "\n";
        $script .= 'find "' . $serverDir . '" -mindepth 1 -maxdepth 1 -type d | while read FOLDER; do' . "\n";
        $script .= 'GAMETEMPLATE=`basename $FOLDER`' . "\n";
        $script .= 'cd "' . $serverDir . '/$GAMETEMPLATE"' . "\n";
        $script .= '${IONICE}nice -n +19 tar cfj "' . $this->removeSlashes($backupDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-$GAMETEMPLATE.tar.bz2" .') . "\n";

        if (strlen($ftpUploadString) > 0) {
            $script .= 'wput -q --limit-rate=4098 --basename="' . $backupDir . '" "' . $backupDir . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-$GAMETEMPLATE.tar.bz2" "' . $ftpUploadString . '"' . "\n";
        }

        $script .= 'done' . "\n";
        $script .= 'wget -q --timeout=60 --no-check-certificate -O - ' . webhostdomain($resellerLockupID) . '/get_password.php?w=bu\\&shorten=`id -un`\\id=' . $this->appServerDetails['port'] . '\\&ip=' . $this->appServerDetails['serverIP']  . "\n";

        $this->addLinuxScript($scriptName, $script);
        $this->addLogline('app_server.log', 'Created backup for apps on server ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' owned by user ' . $this->appServerDetails['userName']);
    }

    public function backupCreate ($ftpUploadString) {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
            $this->linuxBackupCreate($ftpUploadString);
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    private function linuxBackupDeploy ($template, $ftpDownloadString) {

        global $resellerLockupID;

        $backupDir = $this->removeSlashes($this->appServerDetails['homeDir'] . $this->appServerDetails['userName'] . '/backup/');
        $serverDir = $this->removeSlashes($this->appServerDetails['homeDir'] . $this->appServerDetails['userName'] . '/server/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '/');

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/backup-deploy-' . $this->appServerDetails['userName'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

        $script = $this->shellScriptHeader;
        $script .= 'rm ' . $scriptName . "\n";

        if (strlen($ftpDownloadString) > 0) {
            $script .= 'if [ ! -d "' . $backupDir . '" ]; then mkdir -p "' . $backupDir . '"; fi' . "\n";
            $script .= 'cd ' . $backupDir . "\n";
            $script .= 'mv "' . $this->removeSlashes($backupDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-' . $template . '.tar.bz2"') . '" "' . $this->removeSlashes($backupDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-' . $template . '_old.tar.bz2"') . "\n";
            $script .= 'wget -q --timeout=10 --no-check-certificate ' . $ftpDownloadString . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-' . $template . '.tar.bz2' . "\n";
            $script .= 'if [ -f "' . $this->removeSlashes($backupDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-' . $template . '.tar.bz2"') . '" ]; then' . "\n";
            $script .= 'rm "' . $this->removeSlashes($backupDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-' . $template . '_old.tar.bz2"') . "\n";
            $script .= 'else' . "\n";
            $script .= 'mv "' . $this->removeSlashes($backupDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-' . $template . '_old.tar.bz2"') . '" "' . $this->removeSlashes($backupDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-' . $template . '.tar.bz2"') . "\n";
            $script .= 'fi' . "\n";
        }

        $script .= 'if [ -f "' . $this->removeSlashes($backupDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-' . $template . '.tar.bz2') . '" ]; then' . "\n";
        $script .= 'rm -rf ' . $this->removeSlashes($serverDir . '/' . $template . '/*') . "\n";

        $script .= 'fi' . "\n";

        $script .= 'if [ ! -d "' . $this->removeSlashes($serverDir . '/' . $template) . '" ]; then mkdir -p "' . $this->removeSlashes($serverDir . '/' . $template) . '"; fi' . "\n";

        $script .= '${IONICE}nice -n +19 tar -C "' . $this->removeSlashes($serverDir . '/' . $template) . '" -xjf "' . $this->removeSlashes($backupDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '-' . $template . '.tar.bz2"') . "\n";
        $script .= 'wget -q --no-check-certificate -O - ' . webhostdomain($resellerLockupID) . '/get_password.php?w=rb\\&shorten=`id -un`\\id=' . $this->appServerDetails['port'] . '\\&ip=' . $this->appServerDetails['serverIP'] . "\n";

        $this->addLinuxScript($scriptName, $script);
        $this->addLogline('app_server.log', 'Deployed backup for app template ' . $template . ' on server ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' owned by user ' . $this->appServerDetails['userName']);
    }

    public function backupDeploy ($template, $ftpDownloadString) {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
            $this->linuxBackupDeploy($template, $ftpDownloadString);
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    private function getKeyAndOrPassword () {

        if ($this->appMasterServerDetails['ssh2Publickey'] != 'N' and file_exists($this->appMasterServerDetails['privateKey'])) {

            $ssh2Pass = new Crypt_RSA();

            if ($this->appMasterServerDetails['ssh2Publickey'] == 'B') {
                $ssh2Pass->setPassword($this->appMasterServerDetails['ssh2DecryptedPass']);
            }

            $ssh2Pass->loadKey(file_get_contents($this->appMasterServerDetails['privateKey']));

        } else {
            $ssh2Pass = $this->appMasterServerDetails['ssh2DecryptedPass'];
        }

        return $ssh2Pass;
    }

    private function handleFailedConnectAttemps () {

        global $sql, $resellerLockupID, $rSA;

        $query = $sql->prepare("UPDATE `rserverdata` SET `notified`=`notified`+1 WHERE `id`=? LIMIT 1");
        $query->execute(array($this->appMasterServerDetails['id']));

        // While we keep on counting up, the mail is send only once to prevent spam
        if (($this->appMasterServerDetails['notified'] + 1) == $rSA['down_checks']) {
            $query = ($resellerLockupID == 0) ? $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `resellerid`=0 AND `accounttype`='a'") : $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE (`id`=${$resellerLockupID} AND `id`=`resellerid`) OR `resellerid`=0 AND `accounttype`='a'");
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                if ($row['mail_serverdown'] == 'Y') {
                    sendmail('emaildown', $row['id'], $this->appMasterServerDetails['ssh2IP'], '');
                }
            }
        }
    }

    private function executeLinux () {

        if (strlen($this->shellScripts['user']) > 0 or count($this->shellScripts['server']) > 0) {

            $sftpObject = new Net_SFTP($this->appMasterServerDetails['ssh2IP'], $this->appMasterServerDetails['ssh2Port']);

            $ssh2Pass = $this->getKeyAndOrPassword();

            $loginReturn = $sftpObject->login($this->appMasterServerDetails['ssh2User'], $ssh2Pass);

            if ($loginReturn) {

                $this->commandReturns[] = $sftpObject->put('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/userCud-' . $this->uniqueHex . '.sh', $this->shellScripts['user']);
                $this->commandReturns[] = $sftpObject->chmod(0700, '/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/userCud-' . $this->uniqueHex . '.sh');

                foreach($this->shellScripts['server'] as $fileName => $scriptContent) {
                    $this->commandReturns[] = 'script added: ' . $fileName;
                    $this->commandReturns[] = $sftpObject->put($fileName, $scriptContent);
                }

                // Files have been created, now login with SSH2 and execute the gobal script
                $sshObject = new Net_SSH2($this->appMasterServerDetails['ssh2IP'], $this->appMasterServerDetails['ssh2Port']);

                if ($sshObject->login($this->appMasterServerDetails['ssh2User'], $ssh2Pass)) {
                    $this->commandReturns[] = $sshObject->exec('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/userCud-' . $this->uniqueHex . '.sh & ');
                }

                return true;
            }

            $this->handleFailedConnectAttemps();
        }

        return false;
    }

    public function execute () {

        // Linux and Windows deamon are reached via SSH2.

        if ($this->appMasterServerDetails['os'] == 'L') {
            return $this->executeLinux();
            // create the script in server array
            // run the user script which than will execute the other scripts
        }

        return false;
    }

    public function debug() {

        if ($this->appMasterServerDetails['os'] == 'L') {
            return array($this->shellScripts['user'], implode("\r\n", $this->shellScripts['server']), implode("\r\n", $this->commandReturns));
        }

        if ($this->appMasterServerDetails['os'] == 'W') {
            return array(implode("\r\n", $this->winCmds), implode("\r\n", $this->commandReturns));
        }

        return array();
    }

    function __destruct() {

    }
}