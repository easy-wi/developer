<?php

/**
 * File: class_masterserver.php.
 * Author: Ulrich Block
 * Date: 16.09.12
 * Time: 11:27
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
if (!class_exists('SSH2')) {
    include(EASYWIDIR . '/third_party/phpseclib/autoloader.php');
}

class masterServer {

    private $updateIDs = array();
    private $removeLogs = array();
    private $winCmds = array();
    private $imageserver, $resellerID, $webhost, $rootOK, $rootID, $rootNotifiedCount, $steamAccount, $steamPassword, $updates, $os, $aeskey, $shellScript, $uniqueHex, $masterserverDir;
    public $sship, $sshport, $sshuser, $sshpass, $publickey, $keyname;
    public $updateAmount = 0;

    function __construct($rootID, $aeskey) {

        // fetch global PDO object
        global $sql;

        $this->aeskey = $aeskey;

        // store the rootserverID
        $this->rootID = $rootID;

        // fetch rootserverdata
        $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `dport`,AES_DECRYPT(`user`,:aeskey) AS `duser`,AES_DECRYPT(`pass`,:aeskey) AS `dpass`,AES_DECRYPT(`steamAccount`,:aeskey) AS `steamAcc`,AES_DECRYPT(`steamPassword`,:aeskey) AS `steamPwd` FROM `rserverdata` WHERE `id`=:id LIMIT 1");
        $query->execute(array(':aeskey' => $aeskey,':id' => $rootID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $active = $row['active'];
            $this->rootNotifiedCount = $row['notified'];
            $this->sship = $row['ip'];
            $this->sshport = $row['dport'];
            $this->sshuser = $row['duser'];
            $this->sshpass = $row['dpass'];
            $this->publickey = $row['publickey'];
            $this->keyname = EASYWIDIR . '/keys/' . removePub($row['keyname']);
            $this->steamAccount = $row['steamAcc'];
            $this->steamPassword = $row['steamPwd'];
            $this->resellerID = $row['resellerid'];
            $this->updates = $row['updates'];
            $this->os = $row['os'];
            $this->masterserverDir = '/home/' . $row['duser'] . '/masterserver/';
        }

        // In case the rootserver could be found and it is active return true
        if (isset($active) and $active == 'Y') {

            $this->rootOK = true;

            $this->getWebHost();

            $this->getImageServer();

            if ($this->os == 'L') {
                $this->startShellScript();
            }

        } else {
            $this->rootOK = false;
        }
    }

    private function getWebHost () {

        global $sql;

        // get the current webhost
        $query = $sql->prepare("SELECT `paneldomain` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
        $query->execute();
        $this->webhost = $query->fetchColumn();
    }

    private function checkIfImageServerIsInSameSubnet ($type, $imageString) {

        // Get the imageserver if possible and use Easy-WI server as fallback
        $mainIp = explode('.', $this->sship);
        $mainSubnet = $mainIp[0] . '.' . $mainIp[1] . '.' . $mainIp[2];

        if ($type == 'rsync') {
            $splitPaths = @preg_split('/\//', $imageString, -1, PREG_SPLIT_NO_EMPTY);
            $splitCredentialsAndServer = (isset($split1[1])) ? preg_split('/\:/', $splitPaths[1], -1, PREG_SPLIT_NO_EMPTY) : preg_split('/\:/', $splitPaths[0], -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $splitPaths = @preg_split('/\//', $imageString, -1, PREG_SPLIT_NO_EMPTY);
            $splitCredentialsAndServer = (isset($split1[1])) ? preg_split('/\@/', $splitPaths[1], -1, PREG_SPLIT_NO_EMPTY) : preg_split('/\@/', $splitPaths[0], -1, PREG_SPLIT_NO_EMPTY);
        }

        foreach ($splitCredentialsAndServer as $splitIp) {

            if ($splitIp != $this->sship && isip($splitIp, 'all')) {

                $ipParts = explode('.', $splitIp);
                $subnet = $ipParts[0] . '.' . $ipParts[1] . '.' . $ipParts[2];

                if ($mainSubnet == $subnet) {
                    return $imageString;
                }
            }
        }

        return false;
    }

    private function getPreferdImageServer ($preferedServer, $allServer) {

        if (count($preferedServer) > 0) {
            $allServer = $preferedServer;
        }

        $serverAmount = count($allServer);
        
        if ($serverAmount > 0) {
            $imageserverCount = $serverAmount - 1;
            $arrayEntry = rand(0, $imageserverCount);
            return $allServer[$arrayEntry];
        }

        return false;
    }

    private function getImageServer () {

        global $sql;

        $query = $sql->prepare("SELECT `imageserver` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($this->resellerID));

        $splitImageservers = preg_split('/\r\n/', $query->fetchColumn(), -1, PREG_SPLIT_NO_EMPTY);
        $rsyncServers = array();
        $ftpServers = array();


        foreach ($splitImageservers as $server) {
            if (isurl($server)) {
                $ftpServers[] = $server;
            } else if (isRsync($server)) {
                $rsyncServers[] = $server;
            }
        }

        $preferedServer = array();

        if ($this->os == 'L' and count($rsyncServers) > 0) {

            foreach ($rsyncServers as $server) {

                $imageServer = $this->checkIfImageServerIsInSameSubnet('rsync', $server);

                if ($imageServer) {
                    $preferedServer[] = $imageServer;
                }
            }

            $imageServer = $this->getPreferdImageServer($preferedServer, $rsyncServers);
        }

        if (!isset($imageServer) and count($ftpServers) > 0) {

            foreach ($ftpServers as $server) {

                $imageServer = $this->checkIfImageServerIsInSameSubnet('ftp', $server);

                if ($imageServer) {
                    $preferedServer[] = $imageServer;
                }
            }

            $imageServer = $this->getPreferdImageServer($preferedServer, $ftpServers);
        }

        if (!isset($imageServer) or !$imageServer or $this->updates == 2) {
            $imageServer = 'none';
        }

        $this->imageserver = $imageServer;
    }

    private function imageStringtoWinDeamon () {

        if (isurl($this->imageserver)) {
            return ftpStringToData($this->imageserver);
        }

        return false;
    }

    public function getCommands () {

        if ($this->os == 'L') {
            return $this->shellScript;
        }

        return implode('<br>', $this->winCmds);
    }

    private function startShellScript () {

        $this->uniqueHex = dechex(mt_rand());

        $this->shellScript = "#!/bin/bash\n";
        $this->shellScript .= 'rm -f /home/' . $this->sshuser . '/temp/master-' . $this->uniqueHex . '.sh' . "\n";
        $this->shellScript .= "if ionice -c3 true 2>/dev/null; then IONICE='ionice -n 7 '; else IONICE=''; fi\n";
        $this->shellScript .= 'UPDATESTATUS=""' . "\n";
        $this->shellScript .= 'BOMRM="sed \"\'s/^\xef\xbb\xbf//g\'\""' . "\n";
        $this->shellScript .= 'PATTERN="\.log\|\.txt\|\.cfg\|\.vdf\|\.db\|\.dat\|\.ztmp\|\.blib\|log\/\|logs\/\|downloads\/\|DownloadLists\/\|metamod\/\|amxmodx\/\|hl\/\|hl2\/\|cfg\/\|addons\/\|bin\/\|classes/"' . "\n";

        if ($this->imageserver != 'none') {

            $this->shellScript .= 'if [ "`which rsync`" != "" -a "`echo ' . $this->imageserver . ' | grep -E \'^ftp(s|)\:(.*)\'`" == "" ]; then' . "\n";
            $this->shellScript .= 'SYNCTOOL="rsync"' . "\n";
            $this->shellScript .= 'SYNCCMD="rsync -azuvx ' . $this->imageserver . '"' . "\n";
            $this->shellScript .= 'else' . "\n";
            $this->shellScript .= 'SYNCTOOL="wget"' . "\n";
            $this->shellScript .= 'SYNCCMD="wget -r -N -l inf -nH --no-check-certificate --cut-dirs=1 ' . $this->imageserver . '"' . "\n";
            $this->shellScript .= 'fi' . "\n";
        }

        $this->shellScript .= 'if [ ! -d "' . $this->masterserverDir . 'steamCMD/" ]; then' . "\n";
        $this->shellScript .= 'mkdir -p "' . $this->masterserverDir . 'steamCMD/"' . "\n";
        $this->shellScript .= 'cd "' . $this->masterserverDir . 'steamCMD/"' . "\n";
        $this->shellScript .= 'if [ ! -f steamcmd.sh ]; then' . "\n";
        $this->shellScript .= 'wget -q --timeout=10 http://media.steampowered.com/client/steamcmd_linux.tar.gz' . "\n";
        $this->shellScript .= 'if [ -f steamcmd_linux.tar.gz ]; then' . "\n";
        $this->shellScript .= 'tar xfz steamcmd_linux.tar.gz' . "\n";
        $this->shellScript .= 'rm -f steamcmd_linux.tar.gz' . "\n";
        $this->shellScript .= 'chmod +x steamcmd.sh' . "\n";
        $this->shellScript .= './steamcmd.sh +login anonymous +quit' . "\n";
        $this->shellScript .= 'fi' . "\n";
        $this->shellScript .= 'fi' . "\n";
        $this->shellScript .= 'fi' . "\n";
        $this->shellScript .= 'cd' . "\n";
    }

    private function serverSync ($shorten, $updateLog) {

        if ($this->os == 'L') {
            $this->shellScript .= 'if [ "$SYNCTOOL" == "rsync" ]; then' . "\n";
            $this->shellScript .= '$SYNCCMD/masterserver/' . $shorten . ' ' . $this->masterserverDir . ' > ' . $updateLog . "\n";
            $this->shellScript .= 'elif [ "$SYNCTOOL" == "wget" ]; then' . "\n";
            $this->shellScript .= '$SYNCCMD/masterserver/' . $shorten . ' > ' . $updateLog . "\n";
            $this->shellScript .= '${IONICE}nice -n +19 find ' . $this->masterserverDir . $shorten . '/ -type f -name "*.listing" -delete' . "\n";
            $this->shellScript .= 'fi' . "\n";
        } else {

            $imageServer = $this->imageStringtoWinDeamon();

            if (is_array($imageServer)) {
                $this->winCmds[] = 'master ' . $shorten . ' ftp:' . $imageServer['server'] . ':' . $imageServer['port'] . ':' . $imageServer['user'] . ':'  . $imageServer['pwd'] . ':/Masterserver ' . $this->webhost . '/get_password.php?w=ms&shorten=' . $shorten;
            }
        }
    }

    private function houseKeeping ($absoluteGamePath) {

        // Workaround for another valve chaos. If the files exist, mapgroups will not work properly
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -maxdepth 2 -type f -name "subscribed_file_ids.txt" -o -name "subscribed_collection_ids.txt" -delete' . "\n";

        // Chmods should be aligned or else the server install for customer will not work
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f \( -iname "*.0" -or -iname "*.1" -or -iname "*.2" -or -iname "*.3" -or -iname "*.3ds" -or -iname "*.4" -or -iname "*.5" -or -iname "*.6" -or -iname "*.7" -or -iname "*.8" -or -iname "*.9" -or -iname "*.amx" -or -iname "*.asi" -or -iname "*.asm" -or -iname "*.bin" \) -exec chmod 640 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f \( -iname "*.bmp" -or -iname "*.BMP" -or -iname "*.bsp" -or -iname "*.bz2" -or -iname "*.c" -or -iname "*.cab" -or -iname "*.cache" -or -iname "*.cfg" -or -iname "*.cmake" -or -iname "*.col" -or -iname "*.conf" -or -iname "*.cpp" -or -iname "*.css" -or -iname "*.csv" \) -exec chmod 640 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f \( -iname "*.cur" -or -iname "*.dat" -or -iname "*.db" -or -iname "*.dds" -or -iname "*.def" -or -iname "*.dff" -or -iname "*.dll" -or -iname "*.doc" -or -iname "*.dsp" -or -iname "*.dxf" -or -iname "*.dylib" -or -iname "*.edf" -or -iname "*.ekv" -or -iname "*.example" \) -exec chmod 640 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f \( -iname "*.exe" -or -iname "*.exp" -or -iname "*.fgd" -or -iname "*.flt" -or -iname "*.fx" -or -iname "*.gam" -or -iname "*.Gbx" -or -iname "*.gif" -or -iname "*.h" -or -iname "*.hpp" -or -iname "*.htm" -or -iname "*.html" -or -iname "*.icns" -or -iname "*.ico" \) -exec chmod 640 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f \( -iname "*.image" -or -iname "*.inc" -or -iname "*.inf" -or -iname "*.ini" -or -iname "*.installed" -or -iname "*.jpg" -or -iname "*.js" -or -iname "*.key" -or -iname "*.kv" -or -iname "*.lib" -or -iname "*.lmp" -or -iname "*.lst" -or -iname "*.lua" -or -iname "*.LUA" \) -exec chmod 640 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f \( -iname "*.manifest" -or -iname "*.map" -or -iname "*.mapRACE" -or -iname "*.mdl" -or -iname "*.mix" -or -iname "*.mp3" -or -iname "*.nav" -or -iname "*.nod" -or -iname "*.nut" -or -iname "*.pak" -or -iname "*.pcx" -or -iname "*.pem" -or -iname "*.pl" \) -exec chmod 640 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f \( -iname "*.png" -or -iname "*.properties" -or -iname "*.psd" -or -iname "*.pwn" -or -iname "*.rad" -or -iname "*.raw" -or -iname "*.rc" -or -iname "*.rec" -or -iname "*.res" -or -iname "*.rules" -or -iname "*.sc" -or -iname "*.scr" -or -iname "*.sfk" \) -exec chmod 640 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f \( -iname "*.sln" -or -iname "*.so" -or -iname "*.spr" -or -iname "*.suo" -or -iname "*.swf" -or -iname "*.tar" -or -iname "*.tga" -or -iname "*.ttf" -or -iname "*.txd" -or -iname "*.txt" -or -iname "*.vbf" -or -iname "*.vcproj" -or -iname "*.vcs" -or -iname "*.vdf" \) -exec chmod 640 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f \( -iname "*.vfe" -or -iname "*.vfont" -or -iname "*.vmf" -or -iname "*.vmt" -or -iname "*.vpk" -or -iname "*.vtf" -or -iname "*.wad" -or -iname "*.wav" -or -iname "*.wv" -or -iname "*.xml" -or -iname "*.xsc" -or -iname "*.yml" -or -iname "*.zip" \) -exec chmod 640 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f -name "srcds_*" -o -name "hlds_*" -o -name "*.run" -o -name "*.sh" -o -name "*.jar" -exec chmod 750 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type f ! -perm -750 ! -perm -755 -exec chmod 640 {} \;' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find ' . $absoluteGamePath . ' -type d -exec chmod 750 {} \;' . "\n";

        // Check for temp files belonging to the steam updater
        $this->shellScript .= 'ls ' . $absoluteGamePath . ' | while read dir; do' . "\n";
        $this->shellScript .= 'if [[ `echo $dir| grep \'[a-z0-9]\{40\}\'` ]]; then rm -rf ' . $absoluteGamePath . '/$dir; fi' . "\n";
        $this->shellScript .= 'done' . "\n";

        // Remove wget left overs
        $this->shellScript .= 'find ' . $absoluteGamePath . ' -type f -iname "wget-*" -delete' . "\n";
    }

    private function createFdlList ($row) {

        $fastDownloadList = '/home/' . $this->sshuser . '/conf/fdl-' . $row['shorten'] . '.list';

        $this->shellScript .= 'if [ -f ' . $fastDownloadList . ' ]; then rm -f ' . $fastDownloadList . '; fi' . "\n";
        $this->shellScript .= 'touch ' . $fastDownloadList . "\n";
        $this->shellScript .= 'cd "' . $this->masterserverDir . '$UPDATE"' . "\n";
        $this->shellScript .= 'SEARCH=0' . "\n";
        $this->shellScript .= 'if [[ `find -maxdepth 2 -name srcds_run` ]]; then' . "\n";
        $this->shellScript .= 'cd `find -mindepth 1 -maxdepth 2 -type d -name "' . $row['modfolder'] . '" | head -n 1`' . "\n";
        $this->shellScript .= 'SEARCHFOLDERS="particles/ maps/ materials/ resource/ models/ sound/"' . "\n";
        $this->shellScript .= 'SEARCH=1' . "\n";
        $this->shellScript .= 'elif [[ `find -maxdepth 2 -name hlds_run` ]]; then' . "\n";
        $this->shellScript .= 'cd `find -mindepth 1 -maxdepth 1 -type d -name "' . $row['modfolder'] . '" | head -n 1`' . "\n";
        $this->shellScript .= 'SEARCHFOLDERS=""' . "\n";
        $this->shellScript .= 'SEARCH=1' . "\n";
        $this->shellScript .= 'elif [[ `find -maxdepth 2 -name "cod4_lnxded"` ]]; then' . "\n";
        $this->shellScript .= 'SEARCHFOLDERS="usermaps/ mods/"' . "\n";
        $this->shellScript .= 'SEARCH=1' . "\n";
        $this->shellScript .= 'fi' . "\n";
        $this->shellScript .= 'if [ "$SEARCH" == "1" ]; then' . "\n";
        $this->shellScript .= '${IONICE}nice -n +19 find $SEARCHFOLDERS -type f 2> /dev/null | grep -v "$PATTERN" | sed \'s/\.\///g\' | while read FILTEREDFILES; do' . "\n";
        $this->shellScript .= 'echo $FILTEREDFILES >> ' . $fastDownloadList . "\n";
        $this->shellScript .= 'done' . "\n";
        $this->shellScript .= 'if [ -f ' . $fastDownloadList . ' ]; then chmod 640 ' . $fastDownloadList . '; fi' . "\n";
        $this->shellScript .= 'if [ -f /home/' . $this->sshuser . '/logs/fdl.log ]; then echo "`date`: Updated filelist for the game ' . $row['shorten'] . '" >> /home/' . $this->sshuser . '/logs/fdl.log; fi' . "\n";
        $this->shellScript .= 'fi' . "\n";
    }

    private function sendUpdateSuccess ($updateLog, $force, $row, $returnSuccessInAnyCase) {

        if (strlen($this->webhost) > 0) {

            if ($force === true or $returnSuccessInAnyCase === true) {
                $this->shellScript .= 'SENDUPDATE="YES"' . "\n";
            }

            // Check if update or install succeeded
            $this->shellScript .= 'if [ -f "' . $updateLog . '" ]; then' . "\n";

            // Check for sync
            $this->shellScript .= 'if [ "`grep ' . $row['appID'] . ' \"' . $updateLog . '\" | grep \'Success\' | grep \'fully installed\'`" != "" ]; then' . "\n";
            $this->shellScript .= 'SENDUPDATE="YES"' . "\n";
            $this->shellScript .= 'fi' . "\n";

            // Check for steamCMD updater
            $this->shellScript .= 'if [ "`grep \'' . $row['shorten'] . '/\' \"' . $updateLog . '\" | head -n 1`" != "" ]; then' . "\n";
            $this->shellScript .= 'SENDUPDATE="YES"' . "\n";
            $this->shellScript .= 'fi' . "\n";

            $this->shellScript .= 'fi' . "\n";

            $this->shellScript .= 'if [ "$SENDUPDATE" == "YES" ]; then' . "\n";
            $this->shellScript .= 'I=0' . "\n";
            $this->shellScript .= 'CHECK=`wget -q --timeout=10 --no-check-certificate -O - ' . $this->webhost . '/get_password.php?w=ms\&shorten=' . $row['shorten'] . ' | $BOMRM`' . "\n";
            $this->shellScript .= 'while [ "$CHECK" != "ok" -a "$I" -le "10" ]; do' . "\n";
            $this->shellScript .= 'if [ "$CHECK" == "" ]; then' . "\n";
            $this->shellScript .= 'I=11' . "\n";
            $this->shellScript .= 'else' . "\n";
            $this->shellScript .= 'sleep 30' . "\n";
            $this->shellScript .= 'I=$[I+1]' . "\n";
            $this->shellScript .= 'CHECK=`wget -q --timeout=10 --no-check-certificate -O - ' . $this->webhost . '/get_password.php?w=ms\&shorten=' . $row['shorten'] . ' | $BOMRM`' . "\n";
            $this->shellScript .= 'fi' . "\n";
            $this->shellScript .= 'done' . "\n";
            $this->shellScript .= 'fi' . "\n";
        }
    }

    private function linuxCollectData ($row, $force, $returnSuccessInAnyCase) {

        $absoluteGamePath = $this->masterserverDir . $row['shorten'];
        $updateLog = '/home/' . $this->sshuser . '/logs/update-' . $row['shorten'] . '.log';

        // Ensure we are in the home folder
        $this->shellScript .= 'cd /home/' . $this->sshuser . "\n";

        if ($row['supdates'] != 3 and $row['updates'] != 3) {

            // Create masterserver folder if it does not exists
            $this->shellScript .= 'if [ ! -d "' . $absoluteGamePath . '" ]; then mkdir -p "' . $absoluteGamePath . '"; fi' . "\n";

            // If template and app master configs allow sync
            if (in_array($row['supdates'], array(1, 4)) and in_array($row['updates'], array(1, 4))) {
                $this->serverSync($row['shorten'], $updateLog);
            }

            // If template and app master configs allow vendor update
            if (in_array($row['supdates'], array(1, 2)) and in_array($row['updates'], array(1, 2))) {

                //Steam updater
                if ($row['steamgame'] == 'S') {

                   $this->shellScript .= 'cd /home/' . $this->sshuser . '/masterserver/steamCMD/'. "\n";
 
                    $this->shellScript .= 'taskset -c 0 ${IONICE}nice -n +19 ./steamcmd.sh +force_install_dir ' . $absoluteGamePath . ' +login ';
 
                    if (strlen($this->steamAccount) > 0) {
                        $this->shellScript .= $this->steamAccount . ' ' . $this->steamPassword;
                    } else if (strlen($row['steamAcc']) > 0) {
                        $this->shellScript .= $row['steamAcc'] . ' ' . $row['steamPwd'];
                    } else {
                        $this->shellScript .= 'anonymous ';
                    }
 
                    $fixedId = workAroundForValveChaos($row['appID'], $row['shorten'], false);
 
                    $this->shellScript .= ($fixedId == 90) ?  ' +app_set_config 90 mod ' . $row['shorten'] . ' +app_update 90' : ' +app_update ' . $fixedId;
                    $this->shellScript .= ' validate  +quit > ' . $updateLog . "\n";

                } else if ($row['steamgame'] == 'N' and ($row['shorten'] == 'mc')) {

                    if (!isurl($row['downloadPath'])) {

                        if (!function_exists('getMinecraftVersion')) {
                            require_once(EASYWIDIR . '/stuff/methods/queries_updates.php');
                        }

                        $mcVersion = getMinecraftVersion();

                        if (isset($mcVersion['downloadPath']) and isurl($mcVersion['downloadPath'])) {
                            $row['downloadPath'] = $mcVersion['downloadPath'];
                        }
                    }

                    if (isurl($row['downloadPath'])) {
                        $this->shellScript .= 'cd ' . $absoluteGamePath . "\n";
                        $this->shellScript .= 'wget -q ' . $row['downloadPath'] . ' --output-document ' . $row['gamebinary'] . '.new' . "\n";
                        $this->shellScript .= 'if [ `stat -c %s ' . $row['gamebinary'] . '.new` -gt 0 ]; then'. "\n";
                        $this->shellScript .= 'mv ' . $row['gamebinary'] . '.new ' . $row['gamebinary'] . "\n";
                        $this->shellScript .= 'else' . "\n";
                        $this->shellScript .= 'rm -f ' . $row['gamebinary'] . '.new ' . "\n";
                        $this->shellScript .= 'fi' . "\n";
                        $this->shellScript .= 'chmod 750 ' . $row['gamebinary'] . "\n";
                    }
                }
            }

            // Housekeeping
            $this->houseKeeping($absoluteGamePath);

            $this->createFdlList($row);

            $this->sendUpdateSuccess($updateLog, $force, $row, $returnSuccessInAnyCase);

            $this->removeLogs[] = $updateLog;

            $this->updateAmount++;
        }
    }

    private function windowsCollectData ($row) {

        if ($row['supdates'] != 3 and $row['updates'] != 3) {

            if (strlen($this->steamAccount) > 0) {

                $connectData = $this->steamAccount;

                if (strlen($this->steamPassword) > 0) {
                    $connectData .= ':' . $this->steamPassword;
                }

            } else if (strlen($row['steamAcc']) > 0) {

                $connectData = $row['steamAcc'];

                if (strlen($this->steamPassword) > 0) {
                    $connectData .= ':' . $row['steamPwd'];
                }

            } else {
                $connectData = 'anonymous';
            }

            $callBackUrl = (strlen($this->webhost) > 0) ? $this->webhost . '/get_password.php?w=ms&shorten=' . $row['shorten'] : '';

            $this->winCmds[] = 'master ' . $row['shorten'] . ' steam:' . $connectData . ':' . workAroundForValveChaos($row['appID'], $row['shorten'], false) . ' ' . $callBackUrl;
        }

        $this->updateAmount++;
    }

    private function addonSync ($serverTypeIDs) {

        if (count($serverTypeIDs) > 0) {

            global $sql;

            $query = $sql->prepare("SELECT t.`addon`,t.`type` FROM `addons_allowed` AS a INNER JOIN `addons` t ON a.`addon_id`=t.`id` WHERE a.`servertype_id` IN (" . implode(',', $serverTypeIDs) . ") AND a.`reseller_id`=? GROUP BY t.`type`,t.`addon`");
            $query->execute(array($this->resellerID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                if ($this->os == 'L') {

                    $addonMasterFolder = ($row['type'] == 'tool') ? 'masteraddons' : 'mastermaps';
                    $absoluteMasterPath = '/home/' . $this->sshuser . '/' . $addonMasterFolder;
                    $absoluteAddonPath = $absoluteMasterPath . '/' . $row['addon'];

                    $this->shellScript .= 'if [ "$SYNCTOOL" == "rsync" ]; then' . "\n";
                    $this->shellScript .= '$SYNCCMD/' . $addonMasterFolder . '/' . $row['addon'] . ' ' . $absoluteMasterPath . '/' . "\n";
                    $this->shellScript .= 'elif [ "$SYNCTOOL" == "wget" ]; then' . "\n";
                    $this->shellScript .= '$SYNCCMD/' . $addonMasterFolder . '/' . $row['addon'] . "\n";
                    $this->shellScript .= 'find ' . $absoluteAddonPath . ' -name .listing -delete' . "\n";
                    $this->shellScript .= 'fi' . "\n";
                    $this->shellScript .= 'if [ -d "' . $absoluteAddonPath . '" ]; then' . "\n";
                    $this->shellScript .= 'find ' . $absoluteAddonPath . ' -type d -exec chmod 750 {} \;' . "\n";
                    $this->shellScript .= 'find ' . $absoluteAddonPath . ' -type f -exec chmod 640 {} \;' . "\n";
                    $this->shellScript .= 'fi' . "\n";

                } else {

                    $imageServer = $this->imageStringtoWinDeamon();

                    if ($row['type'] == 'tool') {
                        $addonMasterFolder = 'MasterAddons';
                        $addonCmd = 'masteraddon';
                    } else {
                        $addonMasterFolder = 'MasterMaps';
                        $addonCmd = 'mastermaps';
                    }

                    if (is_array($imageServer)) {
                        $this->winCmds[] = $addonCmd . ' install ' . $imageServer['server'] . ' ' . $imageServer['port'] . ' ' . $imageServer['user'] . ' '  . $imageServer['pwd'] . ' /' . $addonMasterFolder . ' ' . $row['addon'];
                    }
                }
            }
        }
    }

    // collect data regarding installed games
    public function collectData ($all = true, $force = false, $returnSuccessInAnyCase = true) {
        
        if ($this->rootOK != true) {
            return null;
        }

        // fetch global PDO object
        global $sql;

        if ($force == true) {
            $extraSQL = '';
        } else {
            $extraSQL = 'AND t.`updates`!=3 AND s.`updates`!=3';
        }

        // if an ID is given collect only data for this ID, else collect all game data for this rootserver
        if ($all === true) {
            $query = $sql->prepare("SELECT t.`id` AS `servertype_id`,t.`shorten`,t.`modfolder`,t.`steamgame`,CASE WHEN t.`serverID` IS NULL THEN t.`appID` ELSE t.`serverID` END AS `appID`,t.`steamVersion`,t.`updates`,t.`downloadPath`,t.`gamebinary`,t.`gameq`,AES_DECRYPT(t.`steam_account`,?) AS `steamAcc`,AES_DECRYPT(t.`steam_password`,?) AS `steamPwd`,r.`id` AS `update_id`,r.`localVersion`,s.`updates` AS `supdates`,s.`id` AS `root_id` FROM `rservermasterg` r INNER JOIN `servertypes` t ON r.`servertypeid`=t.`id` INNER JOIN `rserverdata` s ON r.`serverid`=s.`id` WHERE r.`serverid`=? " . $extraSQL);
            $query->execute(array($this->aeskey, $this->aeskey, $this->rootID));
        } else {
            $query = $sql->prepare("SELECT t.`id` AS `servertype_id`,t.`shorten`,t.`modfolder`,t.`steamgame`,CASE WHEN t.`serverID` IS NULL THEN t.`appID` ELSE t.`serverID` END AS `appID`,t.`steamVersion`,t.`updates`,t.`downloadPath`,t.`gamebinary`,t.`gameq`,AES_DECRYPT(t.`steam_account`,?) AS `steamAcc`,AES_DECRYPT(t.`steam_password`,?) AS `steamPwd`,r.`id` AS `update_id`,r.`localVersion`,s.`updates` AS `supdates`,s.`id` AS `root_id` FROM `rservermasterg` r INNER JOIN `servertypes` t ON r.`servertypeid`=t.`id` INNER JOIN `rserverdata` s ON r.`serverid`=s.`id` WHERE r.`serverid`=? AND r.`servertypeid`=? " . $extraSQL . " LIMIT 1");
            $query->execute(array($this->aeskey, $this->aeskey, $this->rootID, $all));
        }


        // Used for addon sync which will be started after the server updates in order to ensure uniqueness of update run
        $serverTypeIDs = array();
        
        // 3 = no Update; 1 = Vendor + Sync; 2 = Vendor; 4 = Sync
        // supdates = appmaster setting; updates = template setting
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $steamVersion = floatval($row['steamVersion']);
            $localVersion = floatval($row['localVersion']);

            if (($steamVersion != $localVersion and ($row['steamgame'] == 'S' or $row['gameq'] == 'minecraft' or $row['gameq'] == 'minequery')) or $force === true) {

                if ($this->os == 'L') {
                    $this->linuxCollectData($row, $force, $returnSuccessInAnyCase);
                } else {
                    $this->windowsCollectData($row, $force, $returnSuccessInAnyCase);
                }

                // Set masterserver to updating
                if ($row['supdates'] != 3 and $row['updates'] != 3) {
                    $this->updateIDs[$row['update_id']] = $row['update_id'];
                }

                // If template and app master configs allow sync
                if (in_array($row['supdates'], array(1, 4)) and in_array($row['updates'], array(1, 4))) {
                    $serverTypeIDs[] = $row['servertype_id'];
                }
            }
        }

        $this->addonSync($serverTypeIDs);
    }

    private function removeUpdateLogs () {

        if (count($this->removeLogs) > 0) {

            $shellScript = '#!/bin/bash'. "\n";
            $shellScript .= 'rm -f /home/' . $this->sshuser . '/temp/remove-update-logs-' . $this->uniqueHex . '.sh'. "\n";

            foreach ($this->removeLogs as $log) {
                $shellScript .= 'if [ -f "' . $log . '" ]; then rm -f "' . $log . '"; fi' . "\n";
            }

            return $shellScript;
        }

        return false;
    }

    private function setUpdating () {

        if (count($this->updateIDs) > 0) {

            global $sql;

            $query = $sql->prepare("UPDATE `rservermasterg` SET `updating`='Y' WHERE `id` IN (" . implode(',', $this->updateIDs) . ")");
            $query->execute();
        }
    }

    private function handleFailedConnectAttemps () {

        global $sql, $resellerLockupID, $rSA;

        $query = $sql->prepare("UPDATE `rserverdata` SET `notified`=`notified`+1 WHERE `id`=? LIMIT 1");
        $query->execute(array($this->rootID));

        // While we keep on counting up, the mail is send only once to prevent spam
        if (($this->rootNotifiedCount + 1) == $rSA['down_checks']) {
            $query = ($resellerLockupID == 0) ? $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `resellerid`=0 AND `accounttype`='a'") : $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE (`id`=${$resellerLockupID} AND `id`=`resellerid`) OR `resellerid`=0 AND `accounttype`='a'");
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                if ($row['mail_serverdown'] == 'Y') {
                    sendmail('emaildown', $row['id'], $this->sship, '');
                }
            }
        }
    }

    private function getKeyAndOrPassword () {

        if ($this->publickey != 'N' and file_exists($this->keyname)) {

            $ssh2Pass = new phpseclib\Crypt\RSA();

            if ($this->publickey == 'B') {
                $ssh2Pass->setPassword($this->sshpass);
            }

            $ssh2Pass->loadKey(file_get_contents($this->keyname));

        } else {
            $ssh2Pass = $this->sshpass;
        }

        return $ssh2Pass;
    }

    private function linuxSshConnectAndExecute ($updating, $getReturn, $ssh2Pass) {

        $sftpObject = new phpseclib\Net\SFTP($this->sship, $this->sshport);

        $loginReturn = $sftpObject->login($this->sshuser, $ssh2Pass);

        if ($loginReturn) {

            $sftpObject->put('/home/' . $this->sshuser . '/temp/master-' . $this->uniqueHex . '.sh', $this->shellScript);
            $sftpObject->chmod(0700, '/home/' . $this->sshuser . '/temp/master-' . $this->uniqueHex . '.sh');

            // File has been created, now login with SSH2 and execute the script
            $sshObject = new phpseclib\Net\SSH2($this->sship, $this->sshport);

            if ($sshObject->login($this->sshuser, $ssh2Pass)) {

                if ($updating === true) {

                    $this->setUpdating();

                    $removeLogs = $this->removeUpdateLogs();

                    if ($removeLogs !== false) {

                        $sftpObject->put('/home/' . $this->sshuser . '/temp/remove-update-logs-' . $this->uniqueHex . '.sh', $removeLogs);
                        $sftpObject->chmod(0700, '/home/' . $this->sshuser . '/temp/remove-update-logs-' . $this->uniqueHex . '.sh');

                        $sshObject->exec('/home/' . $this->sshuser . '/temp/remove-update-logs-' . $this->uniqueHex . '.sh & ');
                    }
                }

                if ($getReturn === false) {

                    $sshObject->exec('/home/' . $this->sshuser . '/temp/master-' . $this->uniqueHex . '.sh & ');

                    return true;
                }

                return $sshObject->exec('/home/' . $this->sshuser . '/temp/master-' . $this->uniqueHex . '.sh');
            }
        }

        return false;
    }

    private function windowsSshConnectAndExecute ($updating, $getReturn, $ssh2Pass) {

        $sshObject = new phpseclib\Net\SSH2($this->sship, $this->sshport);

        if ($sshObject->login($this->sshuser, $ssh2Pass)) {

            if ($updating === true) {
                $this->setUpdating();
            }

            if ($getReturn === false) {

                foreach ($this->winCmds as $command) {
                    $sshObject->exec($command . "\r\n");
                }

                return true;
            }

            $return = '';

            foreach ($this->winCmds as $command) {

                $temp = $sshObject->exec($command . "\r\n");

                if ($temp) {
                    $return .= $temp;
                }
            }

            return $return;
        }

        return false;
    }

    public function sshConnectAndExecute ($updating = true, $getReturn = false) {

        $ssh2Pass = $this->getKeyAndOrPassword();

        $return = ($this->os == 'L') ? $this->linuxSshConnectAndExecute($updating, $getReturn, $ssh2Pass) : $this->windowsSshConnectAndExecute($updating, $getReturn, $ssh2Pass);

        if (!$return) {
            $this->handleFailedConnectAttemps();
        }

        return $return;
    }

    private function linuxCheckForUpdate ($shorten) {

        $updateLog = '/home/' . $this->sshuser . '/logs/update-' . $shorten . '.log';

        // When the logfile is missing the update is still running
        $this->shellScript .= 'if [ ! -f ' . $updateLog . ' ]; then' . "\n";
        $this->shellScript .= 'UPDATESTATUS="${UPDATESTATUS};' . $shorten . '=1"' . "\n";
        $this->shellScript .= 'else' . "\n";

        // If it exists and the update is not running, the update is finished
        $this->shellScript .= 'if [ "`ps fx | grep \'masterserver/' . $shorten . '\' | grep -v grep | head -n 1`" ]; then' . "\n";
        $this->shellScript .= 'UPDATESTATUS="${UPDATESTATUS};' . $shorten . '=1"' . "\n";
        $this->shellScript .= 'else' . "\n";
        $this->shellScript .= 'UPDATESTATUS="${UPDATESTATUS};' . $shorten . '=0"' . "\n";
        $this->shellScript .= 'fi' . "\n";

        $this->shellScript .= 'fi' . "\n";
    }

    public function checkForUpdate ($shorten) {
        if ($this->os == 'L') {
            $this->linuxCheckForUpdate($shorten);
        } else {

        }
    }

    public function getUpdateStatus() {

        if ($this->os == 'L') {
            $this->shellScript .= 'echo $UPDATESTATUS' . "\n";
        } else {

        }

        return $this->sshConnectAndExecute (false, true);
    }

    // Add Server space data - Nexus633
    // Fix Json_decode error
    public function getDiskSpace($path){
        if ($this->os == 'L') {
            //$this->shellScript .= 'df -h | grep -w "' . $path . '" | awk \'{print "{\n  \"mount\":\"' . $path . '\",\n  \"filesystem\":\"" $1 "\",\n  \"size\":\"" $2 "\",\n  \"used\":\"" $3 "\",\n  \"avil\":\"" $4 "\",\n  \"perc\":\"" $5 "\"\n}"}\'' . "\n";
            $this->shellScript = 'df -h | grep -w "' . $path . '" | awk \'{print $1" "$2" "$3" "$4" "$5}\'';
        } else {
            return false;
        }
        $response = $this->sshConnectAndExecute (false, true);
        if(isset($response) && strlen($response) > 0){
            $data = explode(' ', $response);
            $space = new stdClass();
            $space->mount = $path;
            $space->filesystem = trim($data[0]);
            $space->size = trim($data[1]);
            $space->used = trim($data[2]);
            $space->avil = trim($data[3]);
            $space->perc = trim($data[4]);

            return $space;
        }
        return false;
    }

    private function linuxMasterRemove ($shorten) {
        $this->shellScript .= 'if [ -d "' . $this->masterserverDir . $shorten . '" ]; then rm -rf "' . $this->masterserverDir . $shorten . '"; fi' . "\n";
    }

    private function WindowsMasterRemove ($shorten) {
        $this->winCmds[] = 'delmaster ' . $shorten;
    }

    public function masterRemove ($shorten) {

        if ($this->os == 'L') {
            $this->linuxMasterRemove($shorten);
        } else {
            $this->WindowsMasterRemove($shorten);
        }
    }

    function __destruct() {
        unset($this->imageserver, $this->resellerID, $this->webhost, $this->rootOK, $this->rootID, $this->steamAccount, $this->steamPassword, $this->updates, $this->os, $this->aeskey, $this->shellScript, $this->uniqueHex, $this->sship, $this->sshport, $this->sshuser, $this->sshpass, $this->publickey, $this->keyname);
    }
}
