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

class App {

    private $gameServerDetails = array(), $appMasterServerDetails = array(), $winCmds = array(), $shellScriptHeader, $shellScripts = array('user' => '', 'server' => array());

    function __construct($id) {

        global $sql, $aeskey;

        $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass`,AES_DECRYPT(`steamAccount`,:aeskey) AS `decryptedsteamAccount`,AES_DECRYPT(`steamPassword`,:aeskey) AS `decryptedsteamPassword` FROM `rserverdata` WHERE `id`=:serverID LIMIT 1");
        $query->execute(array(':serverID' => $id, ':aeskey' => $aeskey));

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $this->appMasterServerDetails['notified'] = (int) $row['notified'];
            $this->appMasterServerDetails['ssh2IP'] = (string) $row['ip'];
            $this->appMasterServerDetails['ssh2Port'] = (int) $row['decryptedport'];
            $this->appMasterServerDetails['ssh2User'] = (string) $row['decrypteduser'];
            $this->appMasterServerDetails['ssh2Publickey'] = (string) $row['publickey'];
            $this->appMasterServerDetails['ssh2DecryptedPass'] = (string) $row['decryptedpass'];
            $this->appMasterServerDetails['ssh2KeyName'] = (string) $row['keyname'];
            $this->appMasterServerDetails['os'] = (string) $row['os'];
            $this->appMasterServerDetails['installationPaths'] = (string) $row['install_paths'];
            $this->appMasterServerDetails['configBadFiles'] = preg_split('/,/', $row['configBadFiles'], -1, PREG_SPLIT_NO_EMPTY);
            $this->appMasterServerDetails['configBadTime'] = (int) $row['configBadTime'];
            $this->appMasterServerDetails['configBinaries'] = preg_split('/,/', $row['configBinaries'], -1, PREG_SPLIT_NO_EMPTY);
            $this->appMasterServerDetails['configDemoTime'] = (int) $row['configDemoTime'];
            $this->appMasterServerDetails['configFiles'] = preg_split('/,/', $row['configFiles'], -1, PREG_SPLIT_NO_EMPTY);
            $this->appMasterServerDetails['configIonice'] = (string) $row['configIonice'];
            $this->appMasterServerDetails['configLogTime'] = (int) $row['configLogTime'];
            $this->appMasterServerDetails['configUserID'] = (int) $row['configUserID'];
            $this->appMasterServerDetails['configZtmpTime'] = (int) $row['configZtmpTime'];

            # https://github.com/easy-wi/developer/issues/70
            $this->appMasterServerDetails['privateKey'] = EASYWIDIR . '/keys/' . removePub($this->gameServerDetails['ssh2KeyName']);

            if ($this->appMasterServerDetails['os'] == 'L') {
                $this->shellScriptHeader = "#!/bin/bash\r\n";
                $this->shellScriptHeader .= "if ionice -c3 true 2>/dev/null; then IONICE='ionice -n 7 '; fi\r\n";
                $this->shellScripts['user'] = $this->shellScriptHeader . "#rm /home/{$this->gameServerDetails['ssh2User']}/temp/userCrud.sh";
            }
        }

        return ($query->rowCount() > 0) ? true : false;
    }

    private function removeSlashes ($string) {
        return str_replace(array('//', '///', '////'), '/', $string);
    }

    // Function that gathers the details of the currently active app
    public function getAppServerDetails($id) {

        // Those three variables are always defined, when this class is used
        global $sql, $aeskey, $resellerLockupID;

        $query = $sql->prepare("SELECT *,AES_DECRYPT(g.`ppassword`,:aeskey) AS `decryptedppass`,AES_DECRYPT(g.`ftppassword`,:aeskey) AS `decryptedftppass`,u.`cname` FROM `gsswitch` AS g INNER JOIN `userdata` AS u ON u.`id`=g.`userid` WHERE g.`id`=:serverid AND g.`resellerid`=:resellerID LIMIT 1");
        $query->execute(array(':id' => $id, ':aeskey' => $aeskey, ':resellerID' => $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            // If app details can not be found return false
            if (!$this->getAppDetails($this->gameServerDetails['serverid'], $id)) {

                $this->gameServerDetails = false;
                return false;
            }

            $this->gameServerDetails['type'] = (string) $row['type'];
            $this->gameServerDetails['protectionModeAllowed'] = ($this->gameServerDetails['template']['protectedApp'] == 'Y') ? (string) $row['pallowed'] : 'N';
            $this->gameServerDetails['protectionModeStarted'] = ($this->gameServerDetails['protectionModeAllowed'] == 'Y') ? (string) $row['protected'] : 'N';
            $this->gameServerDetails['eacAllowed'] = (string) $row['eacallowed'];
            $this->gameServerDetails['tvAllowed'] = (string) $row['tvenable'];
            $this->gameServerDetails['serverIP'] = (string) $row['serverip'];
            $this->gameServerDetails['port'] = (int) $row['port'];
            $this->gameServerDetails['port2'] = (int) $row['port2'];
            $this->gameServerDetails['port3'] = (int) $row['port3'];
            $this->gameServerDetails['port4'] = (int) $row['port4'];
            $this->gameServerDetails['port5'] = (int) $row['port5'];
            $this->gameServerDetails['minram'] = ($row['minram'] > 0) ? (int) $row['minram'] : 512;
            $this->gameServerDetails['maxram'] = ($row['maxram'] > 0) ? (int) $row['maxram'] : 1024;
            $this->gameServerDetails['slots'] = (int) $row['slots'];
            $this->gameServerDetails['userMasterFastDownload'] = (string) $row['masterfdl'];
            $this->gameServerDetails['specificFastDownLoadData'] = (string) $row['mfdldata'];
            $this->gameServerDetails['useTaskSet'] = (string) $row['taskset'];
            $this->gameServerDetails['cores'] = (string) $row['cores'];
            $this->gameServerDetails['userName'] = ($row['newlayout'] == 'Y') ? (string) $row['cname'] . '-' . $row['switchID'] : (string) $row['cname'];
            $this->gameServerDetails['hdd'] = (int) $row['hdd'];

            // Password value is only used for setting. In case a server is inactive we need to generate a random one, so the customer can no longer log in.
            $this->gameServerDetails['ftpPassword'] = ($row['active'] == 'Y') ? (string) $row['decryptedftppass'] : passwordgenerate(10);
            $this->gameServerDetails['ftpPasswordProtected'] = ($row['active'] == 'Y') ? (string) $row['decryptedppass'] : passwordgenerate(10);

            $iniVars = @parse_ini_string($this->appMasterServerDetails['installationPaths'], true);
            $this->gameServerDetails['homeDir'] = ($iniVars and isset($iniVars[$row['homeLabel']]['path'])) ? (string) $iniVars[$row['homeLabel']]['path'] : '/home';
        }

        return ($query->rowCount() > 0) ? true : false;
    }

    // Function that gathers the details of the currently active app
    private function getAppDetails($id, $appServerID) {

        global $sql;

        $query = $sql->prepare("SELECT t.`shorten`,t.`protected`,t.`protectedSaveCFGs`,t.`gamebinary`,t.`gamebinaryWin`,t.`binarydir`,t.`modfolder`,t.`cmd` AS `template_cmd`,t.`modcmds` AS `template_modcmds`,`configedit`,s.*,AES_DECRYPT(s.`uploaddir`,:aeskey) AS `d_uploaddir`,AES_DECRYPT(s.`webapiAuthkey`,:aeskey) AS `d_webapiauthkey` FROM `serverlist` AS s INNER JOIN `servertypes` AS t ON t.`id`=s.`servertype` WHERE s.`id`=? AND s.`switchID`=? LIMIT 1");
        $query->execute(array($id, $appServerID));

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            // First block will be global app template settings
            $this->gameServerDetails[$appServerID]['template']['shorten'] = (string) $row['shorten'];
            $this->gameServerDetails[$appServerID]['template']['protectedApp'] = (string) $row['protected'];
            $this->gameServerDetails[$appServerID]['template']['protectedSaveCFGs'] = (string) $row['protectedSaveCFGs'];
            $this->gameServerDetails[$appServerID]['template']['gameBinary'] = ($this->appMasterServerDetails['os'] == 'L') ? (string) $row['gamebinary'] : (string) $row['gamebinaryWin'];
            $this->gameServerDetails[$appServerID]['template']['binarydir'] = (string) $row['binarydir'];
            $this->gameServerDetails[$appServerID]['template']['modfolder'] = (string) $row['modfolder'];
            $this->gameServerDetails[$appServerID]['template']['modcmds'] = (string) $row['template_modcmds'];
            $this->gameServerDetails[$appServerID]['template']['configedit'] = (string) $row['configedit'];

            // second block will be specific app settings
            $this->gameServerDetails[$appServerID]['app']['anticheat'] = (int) $row['anticheat'];
            $this->gameServerDetails[$appServerID]['app']['fps'] = (int) $row['fps'];
            $this->gameServerDetails[$appServerID]['app']['tic'] = (int) $row['tic'];
            $this->gameServerDetails[$appServerID]['app']['servertemplate'] = (int) $row['servertemplate'];

            $this->gameServerDetails[$appServerID]['app']['map'] = (string) $row['map'];
            $this->gameServerDetails[$appServerID]['app']['workShop'] = (string) $row['workShop'];
            $this->gameServerDetails[$appServerID]['app']['mapGroup'] = (string) $row['mapGroup'];
            $this->gameServerDetails[$appServerID]['app']['workshopCollection'] = (int) $row['workshopCollection'];
            $this->gameServerDetails[$appServerID]['app']['webApiAuthKey'] = (string) $row['d_webapiauthkey'];

            $this->gameServerDetails[$appServerID]['app']['upload'] = (int) $row['upload'];
            $this->gameServerDetails[$appServerID]['app']['uploadDir'] = (string) $row['d_uploaddir'];

            // Third will be app settings which might get overwritten by global settings

            $this->gameServerDetails[$appServerID]['app']['cmd'] = ($row['owncmd'] == 'Y') ? (string) $row['cmd'] : (string) $row['template_cmd'];
            $this->gameServerDetails[$appServerID]['app']['modcmd'] = (string) $row['modcmd'];
            $this->gameServerDetails[$appServerID]['app']['gamemod'] = (string) $row['gamemod'];
            $this->gameServerDetails[$appServerID]['app']['gamemod2'] = (string) $row['gamemod2'];
        }

        return ($query->rowCount() > 0) ? true : false;
    }

    private function linuxAddModUserGenerate ($userName, $password) {

        /*
         * function add_user {
# adduser username ftpPassword homeDir (optional protected ftpPassword)
CONFIGUSERID=`grep CONFIGUSERID $HOMEFOLDER/conf/config.cfg 2> /dev/null | awk -F "=" '{print $2}' | tr -d '"'`
if [ "$CONFIGUSERID" == "" ]; then CONFIGUSERID=1000; fi
USER=`ls -la /var/run/screen | grep S-$VARIABLE2 | head -n 1 | awk '{print $3}'`
if [ $USER -eq $USER 2> /dev/null ]; then USERID=$USER; fi
USERGROUPD=`ls -l $VARIABLE0 | awk '{print $4}'`
if [ "$VARIABLE4" == "" ]; then VARIABLE4="/home"; fi
if [ "$USERID" != "" ]; then
	sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` -d "`echo ${VARIABLE4}/${VARIABLE2} | sed 's/\/\//\//g'`" -g $USERGROUPD -s /bin/bash -u $USERID $VARIABLE2 2>/dev/null
else
	USERID=`getent passwd | cut -f3 -d: | sort -un | awk 'BEGIN { id='${CONFIGUSERID}' } $1 == id { id++ } $1 > id { print id; exit }'`
	if [ "`ls -la /var/run/screen | awk '{print $3}' | grep $USERID`" == "" -a "`grep \"x:$USERID:\" /etc/passwd`" == "" ]; then
		sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` -d "`echo ${VARIABLE4}/${VARIABLE2} | sed 's/\/\//\//g'`" -g $USERGROUPD -s /bin/bash -u $USERID $VARIABLE2 2>/dev/null
	else
		while [ "`ls -la /var/run/screen | awk '{print $3}' | grep $USERID`" != "" -o "`grep \"x:$USERID:\" /etc/passwd`" != "" ]; do
			USERID=$[USERID+1]
			if [ "`ls -la /var/run/screen | awk '{print $3}' | grep $USERID`" == "" -a "`grep \"x:$USERID:\" /etc/passwd`" == "" ]; then
				sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` -d "`echo ${VARIABLE4}/${VARIABLE2} | sed 's/\/\//\//g'`" -g $USERGROUPD -s /bin/bash -u $USERID $VARIABLE2 2>/dev/null
			fi
		done
	fi
fi
if [ "$VARIABLE5" != "" ]; then
	PUSER=`ls -la /var/run/screen | grep S-$VARIABLE2-p | head -n 1 | awk '{print $3}'`
	if [ $PUSER -eq $PUSER 2> /dev/null ]; then PUSERID=$PUSER;  fi
	if [ "$PUSERID" != "" ]; then
		sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE5'","Sa")'` -d "`echo ${VARIABLE4}/${VARIABLE2}/pserver | sed 's/\/\//\//g'`" -g $USERGROUPD -s /bin/bash -u $PUSERID $VARIABLE2-p
	else
		PUSERID=`getent passwd | cut -f3 -d: | sort -un | awk 'BEGIN { id='${CONFIGUSERID}' } $1 == id { id++ } $1 > id { print id; exit }'`
		if [ "`ls -la /var/run/screen | awk '{print $3}' | grep $PUSERID`" == "" -a "`grep \"x:$PUSERID:\" /etc/passwd`" == "" ]; then
			sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` -d "`echo ${VARIABLE4}/${VARIABLE2}/pserver | sed 's/\/\//\//g'`" -g $USERGROUPD -s /bin/bash -u $PUSERID $VARIABLE2-p
		else
			while [ "`ls -la /var/run/screen | awk '{print $3}' | grep $PUSERID`" != "" -o "`grep \"x:$PUSERID:\" /etc/passwd`" != "" ]; do
				PUSERID=$[PUSERID+1]
				if [ "`ls -la /var/run/screen | awk '{print $3}' | grep $PUSERID`" == "" -a "`grep \"x:$PUSERID:\" /etc/passwd`" == "" ]; then
					sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` -d "`echo ${VARIABLE4}/${VARIABLE2}/pserver | sed 's/\/\//\//g'`" -g $USERGROUPD -s /bin/bash -u $PUSERID $VARIABLE2-p
				fi
			done
		fi
	fi
fi
}
         */
        // Check if the user can be found. If not, add it, if yes, edit
        $this->shellScripts['user'] .=  'if [ "`id ' . $userName . ' 2>/dev/null`" == "" ]; then' . "\r\n";
        $this->shellScripts['user'] .=  'else' . "\r\n";
        $this->shellScripts['user'] .=  '/usr/sbin/usermod -p `perl -e \'print crypt("\'' . $password . '\'","Sa")\'` ' . $userName . "\r\n";
        $this->shellScripts['user'] .=  'echo "`date`: User ' . $userName . ' edited" >> /home/' . $this->gameServerDetails['ssh2User'] . '/logs/update.log' . "\r\n";
        $this->shellScripts['user'] .=  'fi' . "\r\n";

    }

    private function linuxAddModUser () {

        $this->linuxAddModUserGenerate ($this->gameServerDetails['userName'], $this->gameServerDetails['ftpPassword']);

        if ($this->gameServerDetails['protectionModeAllowed']) {
            $this->linuxAddModUserGenerate ($this->gameServerDetails['userName'] . '-p', $this->gameServerDetails['ftpPasswordProtected']);
        }
    }

    private function windowsAddModUser () {

    }

    private function linuxDeleteUserGenerate ($userName) {

        $this->shellScripts['user'] .=  'if [ "`id ' . $userName . ' 2>/dev/null`" != "" ]; then' . "\r\n";
        $this->shellScripts['user'] .=  '${IONICE}nice -n +19 sudo /usr/sbin/userdel -fr ' . $userName . "\r\n";
        $this->shellScripts['user'] .=  'echo "`date`: User ' . $userName . ' deleted" >> /home/' . $this->gameServerDetails['ssh2User'] . '/logs/update.log' . "\r\n";
        $this->shellScripts['user'] .=  'fi' . "\r\n";

    }

    private function linuxDelUser ($type) {

        $this->linuxDeleteUserGenerate($this->gameServerDetails['userName'] . '-p');

        if ($type == 'both') {
            $this->linuxDeleteUserGenerate($this->gameServerDetails['userName']);
        }
    }

    private function windowsDeluser ($type) {

    }

    public function userCud ($action, $type = false) {

        if (isset($this->appMasterServerDetails['os'])) {

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

    public function execute () {

    }

    function __destruct() {

    }
}