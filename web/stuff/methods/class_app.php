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

    private $winCmds = array(), $shellScriptHeader, $shellScripts = array('user' => '', 'server' => array()), $commandReturns = array();

    public $appMasterServerDetails = array(), $appServerDetails = array();

    // The constructor gathers the root data
    function __construct($id) {

        global $sql, $aeskey;

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
                $this->shellScripts['user'] = $this->shellScriptHeader . "#rm /home/{$this->appMasterServerDetails['ssh2User']}/temp/userCud.sh\n";
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

            $this->appServerDetails['homeDir'] = ($this->appMasterServerDetails['iniVars'] and isset($this->appMasterServerDetails['iniVars'][$row['homeLabel']]['path'])) ? (string) $this->appMasterServerDetails['iniVars'][$row['homeLabel']]['path'] : '/home';

            $serverTemplateDir = $this->appServerDetails['homeDir'] . '/' . $this->appServerDetails['userName'];
            $serverTemplateDir .= ($this->appServerDetails['protectionModeStarted'] == 'Y') ? '/pserver/' : '/server/';
            $this->appServerDetails['absolutePath'] = $this->removeSlashes($serverTemplateDir . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '/' . $this->appServerDetails['app']['templateChoosen'] . '/');

    // For protected users the pserver/ directory is the home folder
            // We deliberately let admins that failed to setup a chrooted FTP environment run into errors
            $absoluteFTPPath = ($this->appServerDetails['protectionModeStarted'] == 'Y') ? '/' : '/server/';
            $absoluteFTPPath .= $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '/' . $this->appServerDetails['app']['templateChoosen'];
            if (in_array($this->appServerDetails['template']['gameBinary'], array('srcds_run', 'srcds.exe'))) {
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
            $this->appServerDetails['app']['id'] = (int) $row['id'];
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
            $this->appServerDetails['app']['uploadDir'] = (string) $row['d_uploaddir'];

            $this->appServerDetails['app']['modcmd'] = (string) $row['modcmd'];
            $this->appServerDetails['app']['gamemod'] = (string) $row['gamemod'];
            $this->appServerDetails['app']['gamemod2'] = (string) $row['gamemod2'];

            // Third will be app settings which might get overwritten by global settings
            $this->appServerDetails['app']['cmd'] = ($row['owncmd'] == 'Y') ? (string) $row['cmd'] : (string) $row['template_cmd'];
        }

        return ($query->rowCount() > 0) ? true : false;
    }

    private function getReplacements () {

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
            $this->appServerDetails['slots'],
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

    // function that gatheers the details for all installed addons
    private function getAddonDetails () {

        global $sql;

        $this->appServerDetails['extensions']['addons'] = array();
        $this->appServerDetails['extensions']['maps'] = array();
        $this->appServerDetails['extensions']['cmds'] = array();
        $this->appServerDetails['extensions']['rmcmd'] = array();

        $query = $sql->prepare("SELECT a.`id`,a.`cmd`,a.`rmcmd`,a.`addon`,a.`type` FROM `addons_installed` AS i INNER JOIN `addons` AS a ON a.`id`=i.`addonid` WHERE i.`serverid`=? AND i.`paddon`=? AND i.`servertemplate`=?");
        $query->execute(array($this->appServerDetails['app']['id'], $this->appServerDetails['protectionModeStarted'], $this->appServerDetails['app']['servertemplate']));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            if ($row['type'] == 'tool') {
                $this->appServerDetails['extensions']['addons'][$row['id']] = $row['addon'];
            } else {
                $this->appServerDetails['extensions']['maps'][$row['id']] = $row['addon'];
            }

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

            if (in_array($this->appServerDetails['template']['gameBinary'], array('srcds_run', 'srcds.exe'))) {
                $cmd .= ' +rcon_password ' .$row['rcon'] . ' +sv_password ' . $row['password']. ' +tv_enable 1 +tv_autorecord 1';
            } else if (in_array($this->appServerDetails['template']['gameBinary'], array('hlds_run', 'hlds.exe'))) {
                $cmd .= ' +rcon_password ' . $row['rcon'] . ' +sv_password ' . $row['password'];
            } else if ($this->appServerDetails['template']['gameBinary'] == 'cod4_lnxded') {
                $cmd .= ' +set rcon_password ' . $row['rcon'] . ' +set g_password ' . $row['password'];
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

    /*
     * Following code contains the user management related funtions
     */

    private function linuxAddModUserGenerate ($userName, $password, $protected = false) {

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

    private function linuxAddModUser () {

        $this->linuxAddModUserGenerate ($this->appServerDetails['userName'], $this->appServerDetails['ftpPassword']);

        if ($this->appServerDetails['protectionModeAllowed']) {
            $this->linuxAddModUserGenerate ($this->appServerDetails['userName'] . '-p', $this->appServerDetails['ftpPasswordProtected'], true);
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

    public function userCud ($action, $type = false) {

        if ($this->appServerDetails and isset($this->appMasterServerDetails['os'])) {

            if ($action == 'del') {
                if ($this->appMasterServerDetails['os'] == 'L') {
                    $this->linuxDelUser($type);
                } else {
                    $this->windowsDeluser($type);
                }
            } else {
                if ($this->appMasterServerDetails['os'] == 'L') {
                    $this->linuxAddModUser($type);
                } else {
                    $this->windowsAddModUser($type);
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
    private function addLinuxScript ($scriptName, $script, $userName = false) {

        $userName = ($userName == false) ? $this->appServerDetails['userNameExecute'] : $userName;

        $this->shellScripts['user'] .= 'chmod 770 ' . $scriptName . "\n";
        $this->shellScripts['user'] .= 'sudo -u ' . $userName . ' ' . $scriptName . "\n";
        $this->shellScripts['server']["{$scriptName}"] = $script;
    }

    // Usecase: IP or port was changed for a server. Now the files need to be moved locally
    private function linuxMoveServerLocal ($oldIP, $oldPort) {

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/move-' . $this->appServerDetails['userName'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '-' . $oldIP . '-' . $oldPort . '.sh');
        $script = $this->shellScriptHeader;
        $script .= '#rm ' . $scriptName . "\n";
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
    private function linuxAddApp ($templates) {

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/add-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '-apps.sh');
        $serverDir = ($this->appServerDetails['protectionModeStarted'] == 'Y') ? 'pserver/' : 'server/';
        $absolutePath = $this->removeSlashes($this->appServerDetails['homeDir'] . '/' . $this->appServerDetails['userName'] . '/' . $serverDir . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port']);

        $copyFileExtensions = array('xml', 'vdf', 'cfg', 'con', 'conf', 'config', 'ini', 'gam', 'txt', 'log', 'smx', 'sp', 'db', 'lua', 'props', 'properties', 'json', 'example');

        $script = $this->shellScriptHeader;
        $script .= '#rm ' . $scriptName . "\n";
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

        $this->addLinuxScript($scriptName, $script);
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
        $script .= '#rm ' . $scriptName . "\n";
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
            $script .= '#rm ' . $scriptName . "\n";

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
        if ($this->appServerDetails and $this->appServerDetails['template']['gameq'] == 'minecraft') {
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
            $script .= '#rm ' . $scriptName . "\n";

        } else {
            $script = '';
        }

        $script .= 'screen -wipe > /dev/null 2>&1' . "\n";
        $script .= 'if [[ `screen -ls | grep ' . $screenName . '` ]]; then' . "\n";
        $script .= 'if [ "`screen -ls | grep ' . $screenName . ' | wc -l`" == "1" ]; then screen -r ' . $screenName . ' -X quit; fi' . "\n";

        if ($this->appServerDetails['template']['gameq'] == 'minecraft') {
            $script .= $this->linuxMcWorldSave(false);
        }

        if ($this->appServerDetails['template']['gameBinary'] == 'srcds_run' and $this->appServerDetails['tvAllowed'] == 'Y') {
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

        //TODO: inlcude demo upload
        /*if ($this->appServerDetails['template']['gameBinary'] == 'srcds_run' and $this->appServerDetails['tvAllowed'] == 'Y') {
            $script .= $this->demoUpload();
        }*/

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
        $script .= '#rm ' . $scriptName . "\n";

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

        // Remove configs that do not contain any overwrite settings
        // Removing will prevent unnecessary FTP connections
        foreach ($cvarProtectArray as $config => $values) {
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

                            if ($values['type'] == 'cfg' and preg_match("/^(.*)" . strtolower($cvar) . "\s+(.*)$/", $loweredSingleLine)) {

                                $edited = true;

                                unset($cvarsNotFound[$cvar]);

                                $splitLine = preg_split('/' . $cvar . '/', $singeLine, -1, PREG_SPLIT_NO_EMPTY);

                                $ftpObect->writeContentToTemp((isset($splitLine[1])) ? $splitLine[0] . $cvar . '  ' . $value : $cvar . '  ' . $value);

                            } else if ($values['type'] == 'ini' and preg_match("/^(.*)" . strtolower($cvar) . "[\s+]{0,}\=[\s+]{0,}(.*)$/", $loweredSingleLine)) {

                                $edited = true;

                                unset($cvarsNotFound[$cvar]);

                                $ftpObect->writeContentToTemp($cvar . '=' . $value);

                            } else if ($values['type'] == 'lua' and preg_match("/^(.*)" . strtolower($cvar) . "[\s+]{0,}\=[\s+]{0,}(.*)[\,]$/", $loweredSingleLine)) {

                                $edited = true;

                                unset($cvarsNotFound[$cvar]);

                                $splitLine = preg_split('/' . $cvar . '/', $singeLine, -1, PREG_SPLIT_NO_EMPTY);

                                $ftpObect->writeContentToTemp((isset($splitLine[1])) ? $splitLine[0] . $cvar. ' = ' .$value : $cvar . '=' . $value);

                            } else if ($values['type'] == 'json' and preg_match("/^(.*)" . strtolower($cvar) . "[\s+]{0,}:[\s+]{0,}(.*)[\,]{0,1}$/", $loweredSingleLine)) {

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

            // On app start we only run commands for supported games
            if ($action == 'start' and in_array($this->appServerDetails['app']['anticheat'], array(3, 4, 5, 6)) and in_array($this->appServerDetails['template']['gameBinary'], array('srcds_run', 'srcds.exe', 'hlds_run', 'hlds.exe'))) {

                if (in_array($this->appServerDetails['template']['gameBinary'], array('srcds_run', 'srcds.exe'))) {
                    $config = 'cfg/server.cfg';
                } else if (in_array($this->appServerDetails['template']['gameBinary'], array('hlds_run', 'hlds.exe'))) {
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

        // https://github.com/easy-wi/developer/issues/205
        // In case Workshop is on we need to remove mapgroup
        $startCommand = ($this->appServerDetails['app']['workShop'] == 'Y') ? str_replace(array('%mapgroup%', ' +mapgroup'), '', $this->appServerDetails['app']['cmd']) : $this->appServerDetails['app']['cmd'];

        // In case of hl2 based servers and no TV allowed, turn off the source tv capabilities
        if (in_array($this->appServerDetails['template']['gameBinary'], array('srcds_run', 'srcds.exe')) and $this->appServerDetails['tvAllowed'] == 'N') {
            $startCommand .= ' -nohltv -tvdisable';
        }

        // If the user decided to use EAC instead of VAC, or turned VAC off on porpuse
        if (in_array($this->appServerDetails['template']['gameBinary'], array('srcds_run', 'srcds.exe', 'hlds_run', 'hlds.exe')) and ($this->appServerDetails['app']['anticheat'] == 2 or ($this->appServerDetails['app']['anticheat'] > 2 and $this->appServerDetails['eacAllowed'] == 'Y'))) {
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
            $startCommand .= $this->getLendDetails();
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

        }

        $shellCommand = ($this->appServerDetails['useTaskSet'] == 'Y' and strlen($this->appServerDetails['cores']) > 0) ? 'taskset -c ' . $this->appServerDetails['cores'] : '';
        $shellCommand .= ' screen -A -m -d -L -S ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' ' . $startCommand;

        return $shellCommand;
    }

    private function linuxStartApp () {

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/start-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

        $serverDir = $this->appServerDetails['homeDir'] . '/' . $this->appServerDetails['userName'];
        $serverDir .= ($this->appServerDetails['protectionModeStarted'] == 'Y') ? '/pserver/' : '/server/';
        $serverDir = $this->removeSlashes($serverDir);
        $serverTemplateDir = $this->removeSlashes($serverDir . '/' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . '/');

        $script = $this->shellScriptHeader;
        $script .= '#rm ' . $scriptName . "\n";

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

        //TODO: inlcude demo upload. In this case check if deamon mode should be active and start loop that tails the screenlog
        // Should be improved like: check if file has been removed and added newly (how to check for the creationdate of a file)

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

    public function demoUpload () {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    private function linuxAddAddonShellCommands ($type, $name) {

        $masterAddonFolder = '/home/' . $this->appMasterServerDetails['ssh2User'] . '/';
        $masterAddonFolder .= ($type == 'addon') ? 'masteraddons/' : 'mastermaps/';
        $masterAddonFolder .= $name . '/';

        if (strlen($this->appServerDetails['template']['modfolder']) == 0) {
            $script = 'GAMEDIR="' . $this->appServerDetails['absolutePath'] . '"' . "\n";
        } else {
            $script = 'if [ "`find ' . $this->appServerDetails['absolutePath'] . ' -mindepth 1 -maxdepth 3 -type d -name ' . $this->appServerDetails['template']['modfolder'] . ' | wc -l`" == "1" ]; then';
            $script .= 'GAMEDIR=`find ' . $this->appServerDetails['absolutePath'] . ' -mindepth 1 -maxdepth 3 -type d -name "' . $this->appServerDetails['template']['modfolder'] . '" | head -n 1`' . "\n";
            $script .= 'else' . "\n";
            $script .= 'GAMEDIR=`find ' . $this->appServerDetails['absolutePath'] . ' -mindepth 1 -maxdepth 1 -type d -name "' . $this->appServerDetails['template']['modfolder'] . '" | head -n 1`' . "\n";
            $script .= 'fi' . "\n";
        }

        $script .= 'cd ' . $masterAddonFolder . "\n";

        $script .= 'find -type f | grep -i -E -w \'(xml|cfg|con|conf|config|gam|ini|txt|vdf|smx|sp|ext|sma|amxx|lua|json)$\' | sed \'s/\.\///g\' | while read FILE; do' . "\n";
        $script .= 'FOLDER=`dirname $FILE`' . "\n";
        $script .= 'FILENAME=`basename $FILE`' . "\n";
        $script .= 'if [ ! -d $GAMEDIR/$FOLDER ]; then' . "\n";
        $script .= 'mkdir -p $GAMEDIR/$FOLDER/' . "\n";
        $script .= 'fi' . "\n";
        $script .= 'find $GAMEDIR/$FILE -type l -delete > /dev/null 2>&1' . "\n";

        if ($type == 'addon') {
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
            $script .= 'cp -sr ' . $masterAddonFolder . '* $GAMEDIR/ > /dev/null 2>&1' . "\n";
        }

        return $script;
    }

    private function linuxAddAddons ($id = false) {

        $scriptName = $this->removeSlashes('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/addons-add-' . $this->appServerDetails['userNameExecute'] . '-' . $this->appServerDetails['serverIP'] . '-' . $this->appServerDetails['port'] . '.sh');

        $script = $this->shellScriptHeader;
        $script .= '#rm ' . $scriptName . "\n";

        if ($id === false) {

            $logLine = '';

            if (isset($this->appServerDetails['extensions']['addons']) and count($this->appServerDetails['extensions']['addons']) > 0) {

                foreach ($this->appServerDetails['extensions']['addons'] as $addon) {
                    $script .= $this->linuxAddAddonShellCommands('addon', $addon);
                }

                $logLine .= 'added addon(s) ' . implode(',', $this->appServerDetails['extensions']['addons']);
            }

            if (isset($this->appServerDetails['extensions']['maps']) and count($this->appServerDetails['extensions']['maps']) > 0) {
                foreach ($this->appServerDetails['extensions']['maps'] as $addon) {
                    $script .= $this->linuxAddAddonShellCommands('map', $addon);
                }

                $logLine .= ' added map(s) ' . implode(',', $this->appServerDetails['extensions']['maps']);
            }


        } else if (isset($this->appServerDetails['extensions']['addons'][$id])) {

            $script .= $this->linuxAddAddonShellCommands('addon', $this->appServerDetails['extensions']['addons'][$id]);

            $logLine = 'Added addon ' . $this->appServerDetails['extensions']['addons'][$id];

        } else if (isset($this->appServerDetails['extensions']['maps'][$id])) {

            $script .= $this->linuxAddAddonShellCommands('map', $this->appServerDetails['extensions']['maps'][$id]);

            $logLine = 'Added map ' . $this->appServerDetails['extensions']['maps'][$id];

        }

        if (isset($logLine) and strlen($logLine) > 0) {
            $this->addLinuxScript($scriptName, $script);
            $this->addLogline('app_server.log', $logLine . ' to app ' . $this->appServerDetails['serverIP'] . '_' . $this->appServerDetails['port'] . ' owned by user ' . $this->appServerDetails['userNameExecute']);
        }
    }

    public function addAddon ($id) {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
            $this->linuxAddAddons($id);
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    public function removeAddon () {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    public function migrateToEasyWi () {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    public function fastDLSync () {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    public function backupCreate () {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
        } else if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'W') {
        }
    }

    public function backupDeploy () {
        if ($this->appServerDetails and $this->appMasterServerDetails['os'] == 'L') {
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

                $this->commandReturns[] = $sftpObject->put('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/userCud.sh', $this->shellScripts['user']);
                $this->commandReturns[] = $sftpObject->chmod(0700, '/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/userCud.sh');

                foreach($this->shellScripts['server'] as $fileName => $scriptContent) {
                    $this->commandReturns[] = 'script added: ' . $fileName;
                    $this->commandReturns[] = $sftpObject->put($fileName, $scriptContent);
                }

                // Files have been created, now login with SSH2 and execute the gobal script
                $sshObject = new Net_SSH2($this->appMasterServerDetails['ssh2IP'], $this->appMasterServerDetails['ssh2Port']);

                if ($sshObject->login($this->appMasterServerDetails['ssh2User'], $ssh2Pass)) {
                    $this->commandReturns[] = $sshObject->exec('/home/' . $this->appMasterServerDetails['ssh2User'] . '/temp/userCud.sh & ');
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