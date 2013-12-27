#!/bin/bash

#    Author:     Ulrich Block <ulrich.block@easy-wi.com>
#
#    This file is part of Easy-WI.
#
#    Easy-WI is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    Easy-WI is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with Easy-WI.  If not, see <http://www.gnu.org/licenses/>.
#
#    Diese Datei ist Teil von Easy-WI.
#
#    Easy-WI ist Freie Software: Sie koennen es unter den Bedingungen
#    der GNU General Public License, wie von der Free Software Foundation,
#    Version 3 der Lizenz oder (nach Ihrer Wahl) jeder spaeteren
#    veroeffentlichten Version, weiterverbreiten und/oder modifizieren.
#
#    Easy-WI wird in der Hoffnung, dass es nuetzlich sein wird, aber
#    OHNE JEDE GEWAEHELEISTUNG, bereitgestellt; sogar ohne die implizite
#    Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
#    Siehe die GNU General Public License fuer weitere Details.
#
#    Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
#    Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.


if [ "$1" == "install" ]; then
	TOOLS=('adduser' 'awk' 'basename' 'bzip2' 'cat' 'chmod' 'chown' 'deluser' 'dirname' 'find' 'grep' 'groupadd' 'id' 'ionice' 'lsof' 'mkdir' 'mv' 'pwd' 'rm' 'rsync' 'sleep' 'tar' 'touch' 'tr' 'useradd' 'userdel' 'usermod' 'wget' 'wput' 'zip')
	for TOOL in ${TOOLS[@]}; do
		if command -v $TOOL >/dev/null 2>&1; then echo "required tool found: $TOOL"; else echo "required tool not found or no access to it: $TOOL"; fi
	done
fi
CVERSION="4.2"
IONICE=''
HOMEFOLDER=$PWD
LOGDIR=$HOMEFOLDER/logs
DATADIR=$HOMEFOLDER/fdl_data
MAPDIR=$HOMEFOLDER/mastermaps
ADDONDIR=$HOMEFOLDER/masteraddons
MASTERSERVERDIR=$HOMEFOLDER/masterserver
TEMPFOLDER=$HOMEFOLDER/temp
VARIABLE0="$0"
VARIABLE1="$1"
VARIABLE2="$2"
VARIABLE3="$3"
VARIABLE4="$4"
VARIABLE5="$5"
VARIABLE6="$6"
VARIABLE7="$7"
VARIABLE8="$8"
VARIABLE9="$9"
VARIABLEALL="$@"
SCRIPTNAME=`basename $0`
MASTERUSER=`echo $HOMEFOLDER | awk -F "/" '{print $3}'`
NOUPDATES=`grep NOUPDATES $HOMEFOLDER/conf/config.cfg 2> /dev/null | awk -F "=" '{print $2}' | tr -d '"'`
IONICEALLOWED=`grep IONICE $HOMEFOLDER/conf/config.cfg 2> /dev/null | awk -F "=" '{print $2}' | tr -d '"'`
if [ "$SCRIPTNAME" == "control" ]; then
	cp control control.sh
	chmod +x control.sh
fi
if [ "$IONICEALLOWED" == "1" ]; then
	if ionice -c3 true 2>/dev/null; then IONICE='ionice -n 7 '; fi
fi
if [ "`id -u`" != "0" ]; then screen -wipe > /dev/null 2>&1; fi
function wget_remove {
	if [ "`id -u`" != "0" -a "`id -u`" == "`id -u $MASTERUSER`" -a ! -f $HOMEFOLDER/.updateLock ]; then
		rm wget-log > /dev/null 2>&1
		find $HOMEFOLDER -maxdepth 1 -name "control_new.*" -delete
		find $HOMEFOLDER \( -iname "wget-*" \) -delete
		find $HOMEFOLDER/conf/ -maxdepth 1 -name "wget-*" -delete
	fi
}
function updatecheck {
	if [ ! -f $HOMEFOLDER/.updateLock ]; then
		touch $HOMEFOLDER/.updateLock
		if [ "$ISROOT" == "0" ]; then
			LOGFILES=(addons hl2 server fdl update fdl-hl2)
			for LOGFILE in ${LOGFILES[@]}; do
				if [ "$LOGFILE" != "" -a ! -f "$LOGDIR/$LOGFILE.log" ]; then touch "$LOGDIR/$LOGFILE.log"; fi
				if [ -f "$LOGDIR/$LOGFILE.log" ]; then chmod 660 "$LOGDIR/$LOGFILE.log"; fi
			done
		fi
		CURRENTFDLVERSION=`wget -q --timeout=10 -O - http://update.easy-wi.com/if_version.php | sed 's/^\xef\xbb\xbf//g'`
		if [ -z $CURRENTFDLVERSION ]; then
			cd $HOMEFOLDER
			if [ -f $HOMEFOLDER/control_new.tar ]; then rm $HOMEFOLDER/control_new.tar; fi
		elif [ "$CVERSION" != "$CURRENTFDLVERSION" ]; then
			if [ "$ISROOT" == "1" ]; then echo "control.sh is outdated fetching update"; fi
			cd $HOMEFOLDER
			if [ -f $HOMEFOLDER/control_new.tar ]; then rm $HOMEFOLDER/control_new.tar; fi
			wget -q --timeout=10 http://update.easy-wi.com/programs/bash/control_new.tar
			if [ -f $HOMEFOLDER/control_new.tar ]; then
				tar xfp control_new.tar
				if [ -f $HOMEFOLDER/control_new.sh ]; then
					if [[ `$HOMEFOLDER/control_new.sh 2> /dev/null | grep 'Current version'` ]]; then
						if [ -f $HOMEFOLDER/control_new.sh ]; then
							mv $HOMEFOLDER/control.sh $HOMEFOLDER/control.old.$CVERSION.sh
							mv $HOMEFOLDER/control_new.sh $HOMEFOLDER/control.sh
							if [ "$ISROOT" == "0" ]; then echo "`date`: Updated the controlprogram from $CVERSION version to $CURRENTFDLVERSION" >> $LOGDIR/update.log; fi
						fi
						chmod 750 control.sh
					fi
				fi
				if [ ! -f $HOMEFOLDER/control.sh ]; then
					OLDVERSION=`ls $HOMEFOLDER/control.old.*.sh | sort -f -r | head -n1`
					if [ "$OLDVERSION" != "" ]; then mv $OLDVERSION $HOMEFOLDER/control.sh; fi
				fi
				rm control_new.tar control_new.tar.* control_new.sh control.tar.* control.old.2.3.* 2> /dev/null
			fi
			if [ "$ISROOT" == "1" ]; then echo "control.sh has been updated to version $CURRENTFDLVERSION."; fi
		fi
		rm $HOMEFOLDER/.updateLock
	fi
}
if [ "$NOUPDATES" != "1" -a "$SCRIPTNAME" != "control_new.sh" -a "$VARIABLE1" != "fixpermissions" ]; then
	if [ "`id -u`" != "0" -a "`id -u`" == "`id -u $MASTERUSER`" ]; then
		ISROOT=0
		updatecheck&
	elif [ "`id -u`" == "0" ]; then
		ISROOT=1
		updatecheck
		if [ "`find -maxdepth 1 -name \"control.old.*\"`" != "" ]; then
			rm control.old.*
		fi
	fi
fi
function rsyncExists {
if [ "$VARIABLE1" == "syncaddons" -o "$VARIABLE1" == "syncserver" ]; then
	IMAGESERVER=$VARIABLE2
else
	IMAGESERVER=$VARIABLE5
fi
if [ "$IMAGESERVER" == "none" ]; then
	SYNCTOOL='none'
elif [ "$IMAGESERVER" == "" -o "$IMAGESERVER" == "easywi" ]; then
	if [ "`which rsync`" != "" ]; then
		SYNCTOOL='rsync'
		SYNCCMD="rsync -azuvx 84.200.78.232::easy-wi"
	else
		SYNCTOOL='wget'
		SYNCCMD="wget -r -N -l inf -nH --no-check-certificate --cut-dirs=1 ftp://imageuser:BMpRP4HEORkKGj@84.200.78.232"
	fi
else
	if [ "`which rsync`" != "" -a "`echo $IMAGESERVER | grep -E '^ftp(s|)\:(.*)'`" == "" ]; then
		SYNCTOOL='rsync'
		SYNCCMD="rsync -azuvx $IMAGESERVER"
	else
		SYNCTOOL='wget'
		SYNCCMD="wget -r -N -l inf -nH --no-check-certificate --cut-dirs=1 $IMAGESERVER"
	fi
fi
}
function install_control {
echo "Control Version is: $CVERSION"
if [ "`id -u`" != "0" ]; then echo "You need to be root, to to use the install function"; exit 0; fi
if [ "$VARIABLE3" == "" ]; then
	echo "Please enter the name of the masteruser"
	read INSTALLMASTER
else
	INSTALLMASTER=$VARIABLE3
fi
if [ "$INSTALLMASTER" == "" ]; then
	echo "Error: Masteruser Value is empty. Shutting down to prevent corrupted config and ini files"
	exit 0
fi
if [ "`grep \"$INSTALLMASTER:\" /etc/passwd | awk -F ":" '{print $1}'`" != "$INSTALLMASTER" ]; then
	if [ -d /home/$INSTALLMASTER ]; then
		groupadd $INSTALLMASTER
		/usr/sbin/useradd -d /home/$INSTALLMASTER -s /bin/bash -g $INSTALLMASTER $INSTALLMASTER
	else
		groupadd $INSTALLMASTER
		/usr/sbin/useradd -m -b /home -s /bin/bash -g $INSTALLMASTER $INSTALLMASTER
	fi
	if [ "$VARIABLE2" != "yesall" ]; then
		echo "Set password for the user? It is not needed if you connect with a more secure keyfile!"
		echo "Enter yes if you want to set it:"
		read READPASSWORD
		if [ "$READPASSWORD" == "yes" ]; then
			passwd $INSTALLMASTER
		else
			publicKeyGenerate
		fi
	elif [ "$VARIABLE4" != "" ]; then
		/usr/sbin/usermod -p `perl -e 'print crypt("'$VARIABLE4'","Sa")'` $INSTALLMASTER
	fi
else
	echo "User found setting group \"$INSTALLMASTER\" as mastegroup"
	usermod -g $INSTALLMASTER $INSTALLMASTER
fi
if [[ ! `grep "^${INSTALLMASTER}:" /etc/passwd` ]]; then
	echo "Error: User $INSTALLMASTER could not be installed. Shutting down to prevent corrupted config and ini files."
	exit 0
fi
chown -R $INSTALLMASTER:$INSTALLMASTER /home/$INSTALLMASTER/
chmod -R 750 /home/$INSTALLMASTER/
sleep 1
echo "Creating folders and files"
CREATEDIRS=('conf' 'fdl_data/hl2' 'logs' 'masteraddons' 'mastermaps' 'masterserver' 'temp')
for CREATEDIR in ${CREATEDIRS[@]}; do
	echo "Adding dir: /home/$INSTALLMASTER/$CREATEDIR"
	mkdir -p /home/$INSTALLMASTER/$CREATEDIR
done
chmod -R 750 /home/$INSTALLMASTER/ /home/$INSTALLMASTER/fdl_data
chmod 770 /home/$INSTALLMASTER/logs/ /home/$INSTALLMASTER/temp/
LOGFILES=('addons' 'hl2' 'server' 'fdl' 'update' 'fdl-hl2')
for LOGFILE in ${LOGFILES[@]}; do
	touch "/home/$INSTALLMASTER/logs/$LOGFILE.log"
done
chmod 660 /home/$INSTALLMASTER/logs/*.log
if [ -f /etc/debian_version ]; then
	if [ "$VARIABLE2" == "yesall" ]; then
		INSTALLPACKAGES="yes"
	else
		echo "You are running Debian `cat /etc/debian_version`. Enter yes if you want to install the neccessary packages if needed"
		read INSTALLPACKAGES
	fi
	if [ "$INSTALLPACKAGES" == "yes" ]; then
		apt-get update
		if [ "$VARIABLE2" == "yesall" ]; then
			apt-get upgrade -y
			echo "proftpd-basic shared/proftpd/inetd_or_standalone select standalone" | debconf-set-selections
			apt-get install wget wput screen bzip2 sudo rsync -y
			if [ "`uname -m`" == "x86_64" ]; then
				if [[ `cat /etc/debian_version | grep '7.'` ]]; then
					dpkg --add-architecture i386
					apt-get update
				fi
				apt-get install ia32-libs -y
			fi
		else
			apt-get upgrade
			apt-get install wget wput screen bzip2 sudo rsync
			if [ "`uname -m`" == "x86_64" ]; then
				if [[ `cat /etc/debian_version | grep '7.'` ]]; then
					dpkg --add-architecture i386
					apt-get update
				fi
				apt-get install ia32-libs lib32readline5 lib32ncursesw5
			else
				apt-get install libreadline5 libncursesw5
			fi
		fi
	fi
	if [ "$VARIABLE2" == "yesall" ]; then
		PROFTPD="yes"
	else
		echo "The recommended FTP Server is proftpd. It will be installed if you enter yes"
		read PROFTPD
	fi
	if [ "$PROFTPD" == "yes" ]; then
		if [ "$VARIABLE2" == "yesall" ]; then
			apt-get install proftpd -y
			ADDFTPRULES="yes"
		else
			apt-get install proftpd
			echo "Add FTP rules? You might need to enhance them later. Enter \"yes\" or \"no\""
			read ADDFTPRULES
		fi
		if [ "`grep 'DefaultRoot\s*\~' /etc/proftpd/proftpd.conf`" == "" ]; then
				echo '
DefaultRoot ~
' >> /etc/proftpd/proftpd.conf
		fi
		if [ "`grep 'Include\s*\/etc\/proftpd\/conf.d\/' /etc/proftpd/proftpd.conf`" == "" ]; then
				echo '
Include /etc/proftpd/conf.d/
' >> /etc/proftpd/proftpd.conf
		fi
		if [ "$ADDFTPRULES" == "yes" -a "`grep '<Directory \/home\/\*\/pserver\/\*>' /etc/proftpd/proftpd.conf`" == "" -a ! -f "/etc/proftpd/conf.d/easy-wi.conf" ]; then
		if [ ! -d "/etc/proftpd/conf.d/" ]; then
			mkdir -p "/etc/proftpd/conf.d/"
			chmod 755 "/etc/proftpd/conf.d/"
		fi
		echo '
<Directory ~>
        HideFiles (^\..+|\.ssh|\.bash_history|\.bash_logout|\.bashrc|\.profile|srcds_run|srcds_linux|hlds_run|hlds_amd|hlds_i686|\.rc|\.sh|\.zip|\.rar|\.7z|\.dll)$
        PathDenyFilter (^\..+|\.ssh|\.bash_history|\.bash_logout|\.bashrc|\.profile|srcds_run|srcds_linux|hlds_run|hlds_amd|hlds_i686|\.rc|\.sh|\.zip|\.rar|\.7z|\.dll)$
        HideNoAccess on
        <Limit RNTO RNFR STOR DELE CHMOD SITE_CHMOD MKD RMD>
                DenyAll
        </Limit>
</Directory>' > /etc/proftpd/proftpd.conf
echo "<Directory /home/$INSTALLMASTER>" >> /etc/proftpd/proftpd.conf
echo '	HideFiles (^\..+|\.ssh|\.bash_history|\.bash_logout|\.bashrc|\.profile)$
	PathDenyFilter (^\..+|\.ssh|\.bash_history|\.bash_logout|\.bashrc|\.profile)$
	HideNoAccess on
	Umask 137 027
	<Limit RNTO RNFR STOR DELE CHMOD SITE_CHMOD MKD RMD>
		AllowAll
	</Limit>
</Directory>
<Directory /home/*/pserver/*>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/backup>
        Umask 177 077
        <Limit RNTO RNFR STOR DELE>
                AllowAll
        </Limit>
</Directory>
<Directory ~/server/*/mc*/*>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/server/*/bukkit*/*>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/server/*/samp*/*>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/server/*/mtasa*/*>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/server/*/teeworlds*/*>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/server/*/*/orangebox/*/*>
	Umask 077 077
	<Limit RNFR RNTO STOR DELE MKD RMD>
		AllowAll
	</Limit>
</Directory>
<Directory ~/server/*/*/csgo/*>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/server/*/*/cstrike/*>
	Umask 077 077
	<Limit RNFR RNTO STOR DELE MKD RMD>
		AllowAll
	</Limit>
</Directory>
<Directory ~/server/*/*/dod/*>
	Umask 077 077
	<Limit RNFR RNTO STOR DELE MKD RMD>
		AllowAll
	</Limit>
</Directory>
<Directory ~/server/*/*/czero/*>
	Umask 077 077
	<Limit RNFR RNTO STOR DELE MKD RMD>
		AllowAll
	</Limit>
</Directory>
<Directory ~/*/*/>
	HideFiles (^\..+|srcds_run|srcds_linux|hlds_run|hlds_amd|hlds_i686|\.rc|\.sh|\.zip|\.rar|\.7z|\.dll|\.so)$
	PathDenyFilter (^\..+|srcds_run|srcds_linux|hlds_run|hlds_amd|hlds_i686|\.rc|\.sh|\.zip|\.rar|\.7z|\.dll|\.so)$
	HideNoAccess on
</Directory>
<Directory ~/*/*/*/*/addons>
	Umask 077 077
	<Limit RNFR RNTO STOR DELE>
		AllowAll
	</Limit>
</Directory>
<Directory ~/*/*/*/*/cfg>
	Umask 077 077
	<Limit RNFR RNTO STOR DELE>
		AllowAll
	</Limit>
</Directory>
<Directory ~/*/*/*/*/maps>
	Umask 077 077
	<Limit RNFR RNTO STOR DELE>
		AllowAll
	</Limit>
</Directory>
<Directory ~/*/*/*/addons>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/*/*/*/cfg>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/*/*/*/maps>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/*/*/cstrike/*>
	Umask 077 077
	<Limit RNFR RNTO STOR DELE>
		AllowAll
	</Limit>
</Directory>
<Directory ~/*/*/dod/*>
	Umask 077 077
	<Limit RNFR RNTO STOR DELE>
		AllowAll
	</Limit>
</Directory>
<Directory ~/*/*/czero/*>
	Umask 077 077
	<Limit RNFR RNTO STOR DELE>
		AllowAll
	</Limit>
</Directory>
' >> /etc/proftpd/conf.d/easy-wi.conf
	fi
	if [ -f /etc/init.d/proftpd ]; then /etc/init.d/proftpd restart; fi
	fi
fi
if [ -f /etc/sudoers -a "`grep $INSTALLMASTER /etc/sudoers`" == "" ]; then
echo "
$INSTALLMASTER ALL = NOPASSWD: /usr/sbin/useradd
$INSTALLMASTER ALL = NOPASSWD: /usr/sbin/userdel
$INSTALLMASTER ALL = NOPASSWD: /usr/sbin/deluser
$INSTALLMASTER ALL = NOPASSWD: /usr/sbin/usermod
$INSTALLMASTER ALL = (ALL, !root:$INSTALLMASTER) NOPASSWD: /home/$INSTALLMASTER/control.sh" >>  /etc/sudoers
fi

mv $HOMEFOLDER/control.sh /home/$INSTALLMASTER/control.sh
chmod 770 /home/$INSTALLMASTER/control.sh
cd /home/$INSTALLMASTER/masterserver
echo "Downloading hldsupdatetool"
sleep 1
wget -q --timeout=10 http://storefront.steampowered.com/download/hldsupdatetool.bin
if [ -f hldsupdatetool.bin ]; then
	chmod 777 hldsupdatetool.bin
	chown -R $INSTALLMASTER:$INSTALLMASTER /home/$INSTALLMASTER/masterserver
	su -c "./hldsupdatetool.bin <<< yes" $INSTALLMASTER
fi
sleep 1
echo "Downloading SteamCmd"
mkdir -p /home/$INSTALLMASTER/masterserver/steamCMD/
cd /home/$INSTALLMASTER/masterserver/steamCMD/
wget -q --timeout=10 http://media.steampowered.com/client/steamcmd_linux.tar.gz
if [ -f steamcmd_linux.tar.gz ]; then
	tar xfvz steamcmd_linux.tar.gz
	rm steamcmd_linux.tar.gz
	chown -R $INSTALLMASTER:$INSTALLMASTER /home/$INSTALLMASTER/masterserver/steamCMD
	su -c "./steamcmd.sh +login anonymous +quit" $INSTALLMASTER
fi
sleep 1
chown -R $INSTALLMASTER:$INSTALLMASTER /home/$INSTALLMASTER
echo "Updating the hldsupdatetool with the User $INSTALLMASTER in the screen \"hldsupdate\""
su -c "screen -dmS hldsupdate ./steam -command update" $INSTALLMASTER
echo "Please add the user $INSTALLMASTER to your AllowUsers entries in the file /etc/ssh/sshd_config"
if [ -d /root/masterserver ]; then
	rm -rf /root/masterserver
fi
if [ -f /etc/crontab ]; then
echo "Enter yes if you want to install cleanup cronjobs"
read READCRON
if [ "$VARIABLE2" == "yesall" -o "$READCRON" == "yes" ]; then
echo "#Minecraft can easily produce 1GB+ logs within one hour
*/5 * * * * root nice -n +19 ionice -n 7 find /home/*/server/*/*/ -maxdepth 2 -type f -name \"screenlog.0\" -size +100M -delete

# Even sudo /usr/sbin/deluser --remove-all-files is used some data remain from time to time
*/5 * * * * root nice -n +19 $IONICE find /home/ -maxdepth 2 -type d -nouser -delete
*/5 * * * * root nice -n +19 $IONICE find /home/*/fdl_data/ /home/*/temp/ /tmp/ /var/run/screen/ -nouser -delete" >> /etc/crontab
/etc/init.d/cron restart
fi
fi
echo "Enter yes if you want the autoupdater being active"
read READUPDATES
if [ "$VARIABLE2" == "yesall" -o "$READUPDATES" == "yes" ]; then
	UPDATES=0
else
	UPDATES=1
fi
cat > /home/$INSTALLMASTER/conf/config.cfg <<EOF
# Binary/Runscript list for chmod and exploit protection
BINS="srcds_run srcds_linux hlds_run hlds_amd hlds_i686 ucc-bin ucc-bin-real"

# File list for exploit protection
FILES="*/cfg/valve.rc"

# Cleanup
# Time after logs will be deleted
LOGTIME="7"

# Time after demos will be deleted
DEMOTIME=""

# Time after ztmp files will be deleted
ZTMPTIME="7"

# Unwanted files
# Filenames to delete to prevent filesharing
BADFILES="zip rar 7zip bz2"

# Time after .rar files will be deleted
# Set 0 for instant
BADTIME="0"

# Use Ionice to reduce IO load 0=off 1=on
IONICE="0"

# Do not Autoupdate
NOUPDATES="$UPDATES"
EOF
chmod 640 /home/$INSTALLMASTER/conf/config.cfg
chown $INSTALLMASTER:$INSTALLMASTER /home/$INSTALLMASTER/conf/config.cfg
echo "The setup is finished"
}

function publicKeyGenerate {
	if ([ "`id -u`" == "0" ] && [ -z $INSTALLMASTER ]); then
		INSTALLMASTER=`find /home/*/control.sh -maxdepth 1 | awk -F '/' '{print $3}' | head -n 1`
	elif [ "`id -u`" != "0"  ]; then
		INSTALLMASTER=`whoami`
	fi
	if [ "$INSTALLMASTER" != "" ]; then
		if [ -d /home/$INSTALLMASTER/.ssh ]; then rm -r /home/$INSTALLMASTER/.ssh; fi
		if [ "`id -u`" == "0" ]; then
			su -c 'ssh-keygen -t rsa' $INSTALLMASTER
		else
			ssh-keygen -t rsa
		fi
		cd /home/$INSTALLMASTER/.ssh
		KEYNAME=`find -maxdepth 1 -name "*.pub" | head -n 1`
		if [ "$KEYNAME" != "" ]; then
			if [ "`id -u`" == "0" ]; then
				su -c "cat $KEYNAME >> authorized_keys" $INSTALLMASTER
			else
				cat $KEYNAME >> authorized_keys
			fi
		else
			echo "Error: could not find a key"
		fi
	fi
}

function fdlList {
	PATTERN="\.log\|\.txt\|\.cfg\|\.vdf\|\.db\|\.dat\|\.ztmp\|\.blib\|log\/\|logs\/\|downloads\/\|DownloadLists\/\|metamod\/\|amxmodx\/\|hl\/\|hl2\/\|cfg\/\|addons\/\|bin\/\|classes/"
	echo "PATTERN=$PATTERN" >> $1
	echo "SED=\"sed \"'s/\.\///g'\"\"" >> $1
	echo "if [ -f $HOMEFOLDER/conf/fdl-$UPDATE.list ]; then" >> $1
	echo "	rm $HOMEFOLDER/conf/fdl-$UPDATE.list" >> $1
	echo 'fi' >> $1
	echo "cd $MASTERSERVERDIR/$UPDATE" >> $1
	echo 'if [[ `find -maxdepth 2 -name srcds_run` ]]; then' >> $1
	echo '	cd `find -mindepth 1 -maxdepth 2 -type d -name "$FDLFOLDER" | head -n 1`' >> $1
	echo '	SEARCHFOLDERS="particles/ maps/ materials/ resource/ models/ sound/"' >> $1
	echo '	SEARCH=1' >> $1
	echo 'elif [[ `find -maxdepth 2 -name hlds_run` ]]; then' >> $1
	echo '	cd `find -mindepth 1 -maxdepth 1 -type d -name "$FDLFOLDER" | head -n 1`' >> $1
	echo '	SEARCHFOLDERS=""' >> $1
	echo '	SEARCH=1' >> $1
	echo 'elif [[ `find -maxdepth 2 -name "cod4_lnxded"` ]]; then' >> $1
	echo '	SEARCHFOLDERS="usermaps/ mods/"' >> $1
	echo '	SEARCH=1' >> $1
	echo 'fi' >> $1
	echo 'if [ "$SEARCH" == "1" ]; then' >> $1
	echo "${IONICE}"'nice -n +19 find $SEARCHFOLDERS -type f 2> /dev/null | grep -v "$PATTERN" | $SED | while read FILTEREDFILES; do' >> $1
	echo '		echo $FILTEREDFILES >> $HOMEFOLDER/conf/fdl-'"$UPDATE"'.list' >> $1
	echo '	done' >> $1
	echo "if [ -f $HOMEFOLDER/conf/fdl-$UPDATE.list ]; then" >> $1
	echo "	chmod 640 $HOMEFOLDER/conf/fdl-$UPDATE.list" >> $1
	echo 'fi' >> $1
	echo 'if [ -f '"$LOGDIR"'/fdl.log ]; then' >> $1
	echo 'echo "`date`: Updated filelist for the game '"$UPDATE"'" >> '"$LOGDIR"'/fdl.log' >> $1
	echo 'fi' >> $1
	echo 'fi' >> $1
}

function steamCmdUpdate {
ps x | grep 'SteamCmdUpdate-Screen'  | grep -v 'grep' | awk '{print $1}' | while read PID; do
	kill $PID > /dev/null 2>&1
	kill -9 $PID > /dev/null 2>&1
done
cat > $TEMPFOLDER/updateSteamCmd.sh << EOF
#!/bin/bash
rm $TEMPFOLDER/updateSteamCmd.sh
VARIABLE3="$VARIABLE3"
VARIABLE4="$VARIABLE4"
VARIABLE5="$VARIABLE5"
LOGDIR="$LOGDIR"
DATADIR="$DATADIR"
UPDATE="$UPDATE"
HOMEFOLDER="$HOMEFOLDER"
MASTERSERVERDIR="$MASTERSERVERDIR"
cd $MASTERSERVERDIR
EOF
echo "BOMRM=\"sed \"'s/^\xef\xbb\xbf//g'\"\"" >> $TEMPFOLDER/updateSteamCmd.sh
if [ ! -d "$MASTERSERVERDIR/steamCMD/" ]; then
	mkdir -p "$MASTERSERVERDIR/steamCMD/"
	cd "$MASTERSERVERDIR/steamCMD/"
	echo 'if [ ! -f steamcmd.sh ]; then
		wget -q --timeout=10 http://media.steampowered.com/client/steamcmd_linux.tar.gz
		if [ -f steamcmd_linux.tar.gz ]; then
			tar xfz steamcmd_linux.tar.gz
			rm steamcmd_linux.tar.gz
			chmod +x steamcmd.sh
			./steamcmd.sh +login anonymous +quit
		fi
	fi' >> $TEMPFOLDER/updateSteamCmd.sh
fi

UPDATECMD="taskset -c 0 $IONICE nice -n +19  ./steamcmd.sh"
if [ "$VARIABLE6" != "" -a "$VARIABLE7" != "" ]; then
	UPDATECMD="$UPDATECMD +login $VARIABLE6 $VARIABLE7"
else
	UPDATECMD="$UPDATECMD +login anonymous"
fi
I=0
A=0
for UPDATE in $VARIABLE3; do
	if [ $I == 0 ]; then
		DIRCMD=" +force_install_dir $MASTERSERVERDIR/$UPDATE"
		GAMENAME=$UPDATE
		if [ ! -d "$MASTERSERVERDIR/$UPDATE" ]; then
			mkdir -p "$MASTERSERVERDIR/$UPDATE"
		fi
		if [ "$SYNCTOOL" == 'rsync' ]; then
			echo "$SYNCCMD/masterserver/$UPDATE $MASTERSERVERDIR/ > $LOGDIR/steamCmd.log" >> $TEMPFOLDER/updateSteamCmd.sh
		elif [ "$SYNCTOOL" == 'wget' ]; then
			echo "$SYNCCMD/masterserver/$UPDATE > $LOGDIR/steamCmd-update.log" >> $TEMPFOLDER/updateSteamCmd.sh
			echo "${IONICE}nice -n +19 find $MASTERSERVERDIR/$UPDATE -type f -name \"*.listing\" -delete" >> $TEMPFOLDER/updateSteamCmd.sh
		fi
		echo "`date`: Update started for $UPDATE" >> $LOGDIR/update.log
		I=1
	else
		if [ "$UPDATE" == "90" ]; then
			UPDATECMD="$UPDATECMD $DIRCMD +app_set_config 90 mod $GAMENAME +app_update 90 validate"
		else
			UPDATECMD="$UPDATECMD $DIRCMD +app_update $UPDATE validate"
		fi
		I=0
		A=$[A+1]
	fi
done
UPDATECMD="$UPDATECMD +quit >> $LOGDIR/steamCmd.log"
if [ $A -gt 0 ]; then
FOLDERS=''
cat >> $TEMPFOLDER/updateSteamCmd.sh << EOF
HOME="$MASTERSERVERDIR/steamCMD"
cd $MASTERSERVERDIR/steamCMD
$UPDATECMD
EOF
echo 'I=0
for UPDATE in $VARIABLE3; do
	if [ $I == 0 ]; then' >> $TEMPFOLDER/updateSteamCmd.sh
echo '	LASTUPDATE=$UPDATE' >> $TEMPFOLDER/updateSteamCmd.sh
echo "${IONICE}"'nice -n +19 find $MASTERSERVERDIR/$UPDATE -type f \( -iname "srcds_*" -or -iname "hlds_*" -or -iname "*.run" -or -iname "*.sh" \) -print0 | xargs -0 chmod 750' >> $TEMPFOLDER/updateSteamCmd.sh
echo "${IONICE}"'nice -n +19 find $MASTERSERVERDIR/$UPDATE -type f ! -perm -750 ! -perm -755 -print0 | xargs -0 chmod 640' >> $TEMPFOLDER/updateSteamCmd.sh
echo "${IONICE}"'nice -n +19 find $MASTERSERVERDIR/$UPDATE -type f -name "subscribed_file_ids.txt" -o -name "subscribed_collection_ids.txt " | xargs -0 rm -f' >> $TEMPFOLDER/updateSteamCmd.sh
echo "${IONICE}"'nice -n +19 find $MASTERSERVERDIR/$UPDATE -type d -print0 | xargs -0 chmod 750' >> $TEMPFOLDER/updateSteamCmd.sh
echo '	ls $MASTERSERVERDIR/$UPDATE | while read dir; do' >> $TEMPFOLDER/updateSteamCmd.sh
echo '		if [[ `echo $dir| grep '"'"'[a-z0-9]\{40\}'"'"'` ]]; then' >> $TEMPFOLDER/updateSteamCmd.sh
echo '			rm -rf $MASTERSERVERDIR/$UPDATE/$dir' >> $TEMPFOLDER/updateSteamCmd.sh
echo '		fi' >> $TEMPFOLDER/updateSteamCmd.sh
echo '	done' >> $TEMPFOLDER/updateSteamCmd.sh
echo '		I=1' >> $TEMPFOLDER/updateSteamCmd.sh
echo '	else' >> $TEMPFOLDER/updateSteamCmd.sh
echo '		I=0' >> $TEMPFOLDER/updateSteamCmd.sh
echo '	if [ "`grep $UPDATE $LOGDIR/steamCmd.log` | grep '"'"'Success!'"'"' | grep '"'"'fully installed'"'"'" != "" ]; then' >> $TEMPFOLDER/updateSteamCmd.sh
echo '		SENDUPDATE="YES"' >> $TEMPFOLDER/updateSteamCmd.sh
echo '	fi' >> $TEMPFOLDER/updateSteamCmd.sh
if [ "$VARIABLE2" == "install" ]; then
	echo 'SENDUPDATE="YES"' >> $TEMPFOLDER/updateSteamCmd.sh
fi
echo '		if [ "$SENDUPDATE" == "YES" ]; then'  >> $TEMPFOLDER/updateSteamCmd.sh
echo '			A=0' >> $TEMPFOLDER/updateSteamCmd.sh
echo '			CHECK=`wget -q --timeout=10 --no-check-certificate -O - $VARIABLE4/get_password.php?w=ms\&shorten=$LASTUPDATE | $BOMRM`' >> $TEMPFOLDER/updateSteamCmd.sh
echo '			while [ "$CHECK" != "ok" -a "$A" -le "10" ]; do' >> $TEMPFOLDER/updateSteamCmd.sh
echo '				if [ "$CHECK" == "" ]; then' >> $TEMPFOLDER/updateSteamCmd.sh
echo '					A=11' >> $TEMPFOLDER/updateSteamCmd.sh
echo '				else' >> $TEMPFOLDER/updateSteamCmd.sh
echo "					sleep 30" >> $TEMPFOLDER/updateSteamCmd.sh
echo '					A=$[A+1]' >> $TEMPFOLDER/updateSteamCmd.sh
echo '					CHECK=`wget -q --timeout=10 --no-check-certificate -O - $VARIABLE4/get_password.php?w=ms\&shorten=$LASTUPDATE | $BOMRM`' >> $TEMPFOLDER/updateSteamCmd.sh
echo '				fi' >> $TEMPFOLDER/updateSteamCmd.sh
echo "			done" >> $TEMPFOLDER/updateSteamCmd.sh
echo "		fi" >> $TEMPFOLDER/updateSteamCmd.sh
echo '	fi' >> $TEMPFOLDER/updateSteamCmd.sh
echo 'done' >> $TEMPFOLDER/updateSteamCmd.sh
fi

I=0
for UPDATE in $VARIABLE3; do
	if [ $I == 0 ]; then
		if [ "$UPDATE" == "css" ]; then
			echo "FDLFOLDER='cstrike'">> $TEMPFOLDER/updateSteamCmd.sh
		elif [ "$UPDATE" == "dods" ]; then
			echo "FDLFOLDER='dod'">> $TEMPFOLDER/updateSteamCmd.sh
		else
			echo "FDLFOLDER='$UPDATE'">> $TEMPFOLDER/updateSteamCmd.sh
		fi
		fdlList $TEMPFOLDER/updateSteamCmd.sh $UPDATE
		I=1
	else
		I=0
	fi
done
chmod +x $TEMPFOLDER/updateSteamCmd.sh
screen -d -m -S SteamCmdUpdate-Screen $TEMPFOLDER/updateSteamCmd.sh
}

function noSteamCmdUpdate {
if [ ! -f $MASTERSERVERDIR/hldsupdatetool.bin ]; then
	cd $MASTERSERVERDIR
	wget -q --timeout=10 http://storefront.steampowered.com/download/hldsupdatetool.bin
	chmod +x hldsupdatetool.bin
	./hldsupdatetool.bin >/dev/null 2>/dev/null <<< yes
	cd
fi
if [ "$VARIABLE5" == "" -o  "$VARIABLE4" == "easywi" ]; then
	VARIABLE4="ftp://imageuser:BMpRP4HEORkKGj@84.200.78.232"
fi
for UPDATE in $VARIABLE3; do
	SAVEAS=`echo "$UPDATE" | awk -F ';' '{print $2}'`
	DOWNLOADURL=`echo "$UPDATE" | awk -F ';' '{print $3}'`
	UPDATE=`echo "$UPDATE" | awk -F ';' '{print $1}'`
	if [[ ! `screen -ls | grep $UPDATE.update` ]]; then
cat > $TEMPFOLDER/update_$UPDATE.sh << EOF
#!/bin/bash
rm $TEMPFOLDER/update_$UPDATE.sh
VARIABLE4="$VARIABLE4"
VARIABLE5="$VARIABLE5"
LOGDIR="$LOGDIR"
DATADIR="$DATADIR"
UPDATE="$UPDATE"
HOMEFOLDER="$HOMEFOLDER"
MASTERSERVERDIR="$MASTERSERVERDIR"
cd "$MASTERSERVERDIR"
FDLFOLDER="$UPDATE"
I=0
EOF
		# Create folder if needed
		echo "BOMRM=\"sed \"'s/^\xef\xbb\xbf//g'\"\"" >> $TEMPFOLDER/update_$UPDATE.sh
		echo 'if [ ! -d $UPDATE ]; then mkdir -p $UPDATE; fi' >> $TEMPFOLDER/update_$UPDATE.sh

		# Retreive files from mirror and clean up afterwards
		if [ "$SYNCTOOL" == 'rsync' ]; then
			echo "$SYNCCMD/masterserver/$UPDATE $MASTERSERVERDIR/ > $LOGDIR/update-$UPDATE.log" >> $TEMPFOLDER/update_$UPDATE.sh
		elif [ "$SYNCTOOL" == 'wget' ]; then
			echo "$SYNCCMD/masterserver/$UPDATE > $LOGDIR/update-$UPDATE.log" >> $TEMPFOLDER/update_$UPDATE.sh
			echo "${IONICE}"'nice -n +19 find $MASTERSERVERDIR/$UPDATE -type f -name "*.listing" -delete' >> $TEMPFOLDER/update_$UPDATE.sh
		fi

		# Neither HLDS nor steamCmd
		FDLFOLDER="$UPDATE"
		if [ "$VARIABLE1" == "noSteamCmd" ]; then
			echo 'PBUSTER=`find $UPDATE -maxdepth 1 -type f -name pbsetup.run | head -n 1`' >> $TEMPFOLDER/update_$UPDATE.sh
			echo 'cd `dirname $PBUSTER`' >> $TEMPFOLDER/update_$UPDATE.sh
			echo './pbsetup.run -u --i-accept-the-pb-eula >> $LOGDIR/update-$UPDATE.log' >> $TEMPFOLDER/update_$UPDATE.sh
			echo 'TEXT="needs to be updated."' >> $TEMPFOLDER/update_$UPDATE.sh

		# Minecraft
		elif [ "$VARIABLE1" == "mcUpdate" ]; then
			echo 'cd $UPDATE' >> $TEMPFOLDER/update_$UPDATE.sh
			echo "wget $DOWNLOADURL --output-document $SAVEAS" >> $TEMPFOLDER/update_$UPDATE.sh
			echo "chmod 750 $SAVEAS" >> $TEMPFOLDER/update_$UPDATE.sh
		# HLDS
		elif [ "$VARIABLE1" == "hldsCmd" ]; then

			# Update the game and updater
			if [ "$UPDATE" = "css" ]; then
				echo "${IONICE}"'nice -n +19 ./steam -command update -game "Counter-Strike Source" -dir $MASTERSERVERDIR/$UPDATE -verify_all -retry >> $LOGDIR/update-$UPDATE.log' >> $TEMPFOLDER/update_$UPDATE.sh
				echo 'FDLFOLDER="cstrike"' >> $TEMPFOLDER/update_$UPDATE.sh
				FDLFOLDER="cstrike"
			else
				if [ "$UPDATE" = "dods" ]; then
					echo 'FDLFOLDER="dod"' >> $TEMPFOLDER/update_$UPDATE.sh
					FDLFOLDER="dod"
				fi
				echo "${IONICE}"'nice -n +19 ./steam -command update -game $UPDATE -dir $MASTERSERVERDIR/$UPDATE -verify_all -retry >> $LOGDIR/update-$UPDATE.log' >> $TEMPFOLDER/update_$UPDATE.sh
			fi

			echo 'TEXT="downloading"' >> $TEMPFOLDER/update_$UPDATE.sh

			# Create FDL Folders in advance
			echo 'cd $MASTERSERVERDIR/$UPDATE' >> $TEMPFOLDER/update_$UPDATE.sh
			echo 'if [[ `find -maxdepth 2 -name srcds_run | head -n 1` ]]; then' >> $TEMPFOLDER/update_$UPDATE.sh
			echo '	cd `dirname */*/steam.inf | head -n 1`' >> $TEMPFOLDER/update_$UPDATE.sh
			echo '	if [ ! -f cfg/server.cfg ]; then' >> $TEMPFOLDER/update_$UPDATE.sh
			echo '		touch cfg/server.cfg' >> $TEMPFOLDER/update_$UPDATE.sh
			echo '	fi' >> $TEMPFOLDER/update_$UPDATE.sh
			echo '	find particles/ maps/ materials/ resource/ models/ sound/ -type d 2> /dev/null | while read FOLDERS; do' >> $TEMPFOLDER/update_$UPDATE.sh
			echo '		if [[ ! -d $DATADIR/hl2/$UPDATE/$FOLDERS ]]; then' >> $TEMPFOLDER/update_$UPDATE.sh
			echo '			mkdir -p $DATADIR/hl2/$UPDATE/$FOLDERS' >> $TEMPFOLDER/update_$UPDATE.sh
			echo '			chmod 770 $DATADIR/hl2/$UPDATE/$FOLDERS' >> $TEMPFOLDER/update_$UPDATE.sh
			echo '		fi' >> $TEMPFOLDER/update_$UPDATE.sh
			echo '	done' >> $TEMPFOLDER/update_$UPDATE.sh
			echo 'fi' >> $TEMPFOLDER/update_$UPDATE.sh
		fi
		echo 'if [ -f $LOGDIR/update-$UPDATE.log ]; then' >> $TEMPFOLDER/update_$UPDATE.sh
		echo '	if [[ `grep "$TEXT" $LOGDIR/update-$UPDATE.log | grep -v "No files"` ]]; then'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '		SENDUPDATE="YES"' >> $TEMPFOLDER/update_$UPDATE.sh
		echo '	fi' >> $TEMPFOLDER/update_$UPDATE.sh
		echo 'fi' >> $TEMPFOLDER/update_$UPDATE.sh
		if [ "$VARIABLE2" == "install" ]; then
			echo 'SENDUPDATE="YES"' >> $TEMPFOLDER/update_$UPDATE.sh
		fi
		# Report back to Easy-WI
		echo 'if [ "$SENDUPDATE" == "YES" ]; then'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '	CHECK=`wget -q --timeout=10 --no-check-certificate -O - $VARIABLE4/get_password.php?w=ms\&shorten=$UPDATE | $BOMRM`'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '	while [ "$CHECK" != "ok" -a "$I" -le "10" ]; do'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '		if [ "$CHECK" == "" ]; then'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '			I=11'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '		else'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '			sleep 30'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '			I=$[I+1]'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '			CHECK=`wget -q --timeout=10 --no-check-certificate -O - $VARIABLE4/get_password.php?w=ms\&shorten=$UPDATE | $BOMRM`'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '		fi'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo '	done'  >> $TEMPFOLDER/update_$UPDATE.sh
		echo 'fi'  >> $TEMPFOLDER/update_$UPDATE.sh
		fdlList $TEMPFOLDER/update_$UPDATE.sh $FDLFOLDER
		echo 'find $HOMEFOLDER -type f -iname "wget-*" -delete' >> $TEMPFOLDER/update_$UPDATE.sh
		echo 'cd' >> $TEMPFOLDER/update_$UPDATE.sh
cat >> $TEMPFOLDER/update_$UPDATE.sh << EOF
${IONICE}nice -n +19 find $HOMEFOLDER/masterserver/$UPDATE -type f \( -iname "srcds_*" -or -iname "hlds_*" -or -iname "*.run" -or -iname "*.sh" \) -print0 | xargs -0 chmod 750
${IONICE}nice -n +19 find $HOMEFOLDER/masterserver/$UPDATE -type f ! -perm -750 ! -perm -755 -print0 | xargs -0 chmod 640
${IONICE}nice -n +19 find $HOMEFOLDER/masterserver/$UPDATE -type d -print0 | xargs -0 chmod 750
EOF
		chmod +x $TEMPFOLDER/update_$UPDATE.sh
		screen -d -m -S $UPDATE.update $TEMPFOLDER/update_$UPDATE.sh
		echo "`date`: Update started for $UPDATE" >> $LOGDIR/update.log
	fi
done
}

function server_delete {
COUNT="`echo $VARIABLE2 | awk -F "_" '{ print $1 }'`"
COUNT=$[COUNT+1]
i=2
while [ $i -le $COUNT ]; do
	GAMENAME=`echo $VARIABLE2 | awk -F_ '{ print $'$i' }'`
	if [ "$GAMENAME" != "" ]; then
		screen -dmS $GAMENAME.delete rm -rf $HOMEFOLDER/masterserver/$GAMENAME $HOMEFOLDER/mastermaps/$GAMENAME $HOMEFOLDER/masteraddons/$GAMENAME 
		echo "`date`: Masterserver $GAMENAME deleted" >> $LOGDIR/update.log
	fi
	i=$[i+1]
done
echo "Server deleted"
}

function add_customer {
USER=`ls -la /var/run/screen | grep S-$VARIABLE2 | head -n 1 | awk '{print $3}'`
if [ $USER -eq $USER 2> /dev/null ]; then USERID=$USER; fi
PUSER=`ls -la /var/run/screen | grep S-$VARIABLE2-p | head -n 1 | awk '{print $3}'`
if [ $PUSER -eq $PUSER 2> /dev/null ]; then PUSERID=$PUSER;  fi
if [ "$USERID" != "" ]; then
	sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` -g $VARIABLE4 -s /bin/bash -u $USERID $VARIABLE2
else
	USERID=`getent passwd | cut -f3 -d: | sort -un | awk 'BEGIN { id=1000 } $1 == id { id++ } $1 > id { print id; exit }'`
	if [ "`ls -la /var/run/screen | awk '{print $3}' | grep $USERID`" == "" -a "`grep \"x:$USERID:\" /etc/passwd`" == "" ]; then
		sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` -g $VARIABLE4 -s /bin/bash -u $USERID $VARIABLE2
	else
		while [ "`ls -la /var/run/screen | awk '{print $3}' | grep $USERID`" != "" -o "`grep \"x:$USERID:\" /etc/passwd`" != "" ]; do
			USERID=$[USERID+1]
			if [ "`ls -la /var/run/screen | awk '{print $3}' | grep $USERID`" == "" -a "`grep \"x:$USERID:\" /etc/passwd`" == "" ]; then
				sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` -g $VARIABLE4 -s /bin/bash -u $USERID $VARIABLE2
			fi
		done
	fi
fi
if [ "$PUSERID" != "" ]; then
	sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE5'","Sa")'` -d /home/$VARIABLE2/pserver -g $VARIABLE4 -s /bin/bash -u $PUSERID $VARIABLE2-p
else
	PUSERID=$[USERID+1]
	if [ "`ls -la /var/run/screen | awk '{print $3}' | grep $PUSERID`" == "" -a "`grep \"x:$PUSERID:\" /etc/passwd`" == "" ]; then
		sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` -d /home/$VARIABLE2/pserver -g $VARIABLE4 -s /bin/bash -u $PUSERID $VARIABLE2-p
	else
		while [ "`ls -la /var/run/screen | awk '{print $3}' | grep $PUSERID`" != "" -o "`grep \"x:$PUSERID:\" /etc/passwd`" != "" ]; do
			PUSERID=$[PUSERID+1]
			if [ "`ls -la /var/run/screen | awk '{print $3}' | grep $PUSERID`" == "" -a "`grep \"x:$PUSERID:\" /etc/passwd`" == "" ]; then
				sudo /usr/sbin/useradd -m -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` -d /home/$VARIABLE2/pserver -g $VARIABLE4 -s /bin/bash -u $PUSERID $VARIABLE2-p
			fi
		done
	fi
fi
echo "user added"
echo "`date`: User $VARIABLE2 created" >> $LOGDIR/update.log
}

function customerDelete {
echo "#!/bin/bash
rm $HOMEFOLDER/temp/del-user-${VARIABLE2}.sh
#${IONICE}nice -n +19 sudo /usr/sbin/deluser --remove-all-files ${VARIABLE2}-p
#${IONICE}nice -n +19 sudo /usr/sbin/deluser --remove-all-files ${VARIABLE2}
${IONICE}nice -n +19 sudo /usr/sbin/userdel -fr ${VARIABLE2}-p
${IONICE}nice -n +19 sudo /usr/sbin/userdel -fr ${VARIABLE2}" > $HOMEFOLDER/temp/del-user-${VARIABLE2}.sh
chmod +x $HOMEFOLDER/temp/del-user-${VARIABLE2}.sh
screen -d -m -S del-user-${VARIABLE2} $HOMEFOLDER/temp/del-user-${VARIABLE2}.sh
echo "`date`: User $VARIABLE2 deleted" >> $LOGDIR/update.log
}

function del_customer_screen {
	ps x | grep "SCREEN" | grep -v "grep" | awk '{print $1}' | while read PID; do
		kill $PID
	done
	screen -wipe > /dev/null 2>&1
}

function mod_customer {
sudo /usr/sbin/usermod -p `perl -e 'print crypt("'$VARIABLE4'","Sa")'` $VARIABLE2-p
sudo /usr/sbin/usermod -p `perl -e 'print crypt("'$VARIABLE3'","Sa")'` $VARIABLE2
echo "user edited"
echo "`date`: Userpassword for $VARIABLE2 edited" >> $LOGDIR/update.log
}

function imagesymlinks {
echo "GAMENAME=$GAMENAME
if [ ! -d $SERVERDIR/$VARIABLE4/$GAMENAME ]; then
	mkdir -p $SERVERDIR/$VARIABLE4/$GAMENAME
fi" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
if [ "$MODINSTALL" == "1" ]; then
	echo "if [ -d $HOMEFOLDER/masterserver/$MODNAME ]; then" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	echo "cd $HOMEFOLDER/masterserver/$MODNAME" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	echo 'FDLFILEFOUND=(`find -mindepth 1 -type f -name "*.xml" -o -name "*.vdf" -o -name "*.cfg" -o -name "*.ini" -o -name "*.conf" -o -name "*.gam" -o -name "*.txt" -o -name "*.log" -o -name "*.smx" -o -name "*.sp" -o -name "*.db" -o -name "*.lua" -o -name "server.properties"  -o -name "*.example"| grep -v "$PATTERN"`)' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	echo 'for FILTEREDFILES in ${FDLFILEFOUND[@]}; do
	FOLDERNAME=`dirname "$FILTEREDFILES"`
	if ([[ `find "$FOLDERNAME" -maxdepth 0 -type d` ]] && [[ ! -d "$SERVERDIR/$VARIABLE4/$GAMENAME/$FOLDERNAME" ]]); then
		mkdir -p "$SERVERDIR/$VARIABLE4/$GAMENAME/$FOLDERNAME"
	fi
	if [[ -f "$SERVERDIR/$VARIABLE4/$GAMENAME/$FILTEREDFILES" ]]; then
		find "$SERVERDIR/$VARIABLE4/$GAMENAME/$FILTEREDFILES" -type l -delete
	fi
	if [[ ! `find "$SERVERDIR/$VARIABLE4/$GAMENAME/$FILTEREDFILES" -type f` ]]; then
		'"${IONICE}"'cp "$HOMEFOLDER/masterserver/$MODNAME/$FILTEREDFILES" "$SERVERDIR/$VARIABLE4/$GAMENAME/$FILTEREDFILES"
	fi
done
'"${IONICE}"'cp -sr $HOMEFOLDER/masterserver/$MODNAME/* $SERVERDIR/$VARIABLE4/$GAMENAME/
fi' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
fi
echo "if [ -d $HOMEFOLDER/masterserver/$GAMENAME2 ]; then" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
echo "cd $HOMEFOLDER/masterserver/$GAMENAME2" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
echo 'FDLFILEFOUND=(`find -mindepth 1 -type f -name "*.xml" -o -name "*.vdf" -o -name "*.cfg" -o -name "*.ini" -o -name "*.conf" -o -name "*.gam" -o -name "*.txt" -o -name "*.log" -o -name "*.smx" -o -name "*.sp" -o -name "*.db" -o -name "*.lua" -o -name "server.properties" | grep -v "$PATTERN"`)' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
echo 'for FILTEREDFILES in ${FDLFILEFOUND[@]}; do
	FOLDERNAME=`dirname "$FILTEREDFILES"`
	if ([[ `find "$FOLDERNAME" -maxdepth 0 -type d` ]] && [[ ! -d "$SERVERDIR/$VARIABLE4/$GAMENAME/$FOLDERNAME" ]]); then
		mkdir -p "$SERVERDIR/$VARIABLE4/$GAMENAME/$FOLDERNAME"
	fi
	if [ -f "$SERVERDIR/$VARIABLE4/$GAMENAME/$FILTEREDFILES" ]; then
		find "$SERVERDIR/$VARIABLE4/$GAMENAME/$FILTEREDFILES" -type l -delete
	fi
	if [ ! -f "$SERVERDIR/$VARIABLE4/$GAMENAME/$FILTEREDFILES" ]; then
		'"${IONICE}"'cp "$HOMEFOLDER/masterserver/$GAMENAME2/$FILTEREDFILES" "$SERVERDIR/$VARIABLE4/$GAMENAME/$FILTEREDFILES"
	fi
done
'"${IONICE}"'cp -sr $HOMEFOLDER/masterserver/$GAMENAME2/* $SERVERDIR/$VARIABLE4/$GAMENAME/
fi' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
}

function remove_folders {
echo "if [ ! -d $SERVERDIR/$VARIABLE4/$GAMENAME ]; then
	IPPORT=`echo "$SERVERDIR/$VARIABLE4/$GAMENAME" | awk -F '/' '{print $2}'`
	PORT=`echo "$SERVERDIR/$VARIABLE4/$GAMENAME" | awk -F '/' '{print $2}' | awk -F '_' '{print $2}'`" >> $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
echo '	if [ -d $SERVERDIR/$PORT/$GAMENAME ]; then
		VARIABLE4=$PORT
	fi
fi' >> $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
echo "if [ -d $SERVERDIR/$VARIABLE4/$GAMENAME ]; then
	rm -rf $SERVERDIR/$VARIABLE4/$GAMENAME
fi" >> $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
if [ -f $LOGDIR/update.log ]; then
	echo "`date`: Server $VARIABLE4/$VARIABLE3 owned by user $VARIABLE2 deleted" >> $LOGDIR/update.log
fi
}

function del_customer_server {
if [ "$VARIABLE5" == "protected" ]; then
	USERVAR=`echo $VARIABLE2 | awk -F "-" '{print $2}'`
	VARIABLE2=`echo $VARIABLE2 | awk -F "-" '{print $1}'`
	if [ "$USERVAR" != "" -a "$USERVAR" != "p" ]; then
		VARIABLE2="$VARIABLE2-$USERVAR"
	fi
	SERVERDIR=/home/$VARIABLE2/pserver
else
	SERVERDIR=/home/$VARIABLE2/server
fi
echo "#!/bin/bash

HOMEFOLDER=$HOMEFOLDER
rm $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
VARIABLE2=$VARIABLE2
VARIABLE4=$VARIABLE4
SERVERDIR=$SERVERDIR" > $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
COUNT=`echo $VARIABLE3 | awk -F_ '{ print $1 }'`
COUNT=$[COUNT+1]
i=2
while [ $i -le $COUNT ]; do
	GAMENAME=`echo $VARIABLE3 | awk -F "_" '{ print $'$i' }'`
	GAMENAME2=$GAMENAME
	if [ "$GAMENAME" != "" ]; then
		if [ "$VARIABLE5" == "" ]; then
			TEMPLATE=4
		elif [ "$VARIABLE5" != "protected" ]; then
			TEMP=(`echo $VARIABLE5 | sed -e 's/-/ /g'`)
			TEMPLATE=${TEMP[$[i-2]]}
		else
			TEMPLATE=1
		fi
		if [ "$TEMPLATE" == 1 -o "$TEMPLATE" == 4 ]; then
			remove_folders
		fi
		if [ "$VARIABLE5" != "protected" ]; then
			if [ "$TEMPLATE" == 2 -o "$TEMPLATE" == 4 ]; then
				GAMENAME="${GAMENAME2}-2"
				echo "GAMENAME=$GAMENAME" >> $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
				remove_folders
			fi
			if [ "$TEMPLATE" == 3 -o "$TEMPLATE" == 4 ]; then
				GAMENAME="${GAMENAME2}-3"
				echo "GAMENAME=$GAMENAME" >> $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
				remove_folders
			fi
		fi
	fi
	i=$[i+1]
done
echo 'if ([ "`ls $SERVERDIR/$VARIABLE4 | wc -l`" == "0" ] && [ -d "$SERVERDIR/$VARIABLE4" ]); then' >> $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
echo "	rm -rf $SERVERDIR/$VARIABLE4
fi" >> $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
chmod +x $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
screen -dmS del-$VARIABLE2-$VARIABLE4 $HOMEFOLDER/temp/del-$VARIABLE2-$VARIABLE4.sh
echo "server deleted"
}

function add_customer_server {
if [ "$VARIABLE5" == "protected" ]; then
	USERVAR=`echo $VARIABLE2 | awk -F "-" '{print $2}'`
	VARIABLE2=`echo $VARIABLE2 | awk -F "-" '{print $1}'`
	if [ "$USERVAR" != "" -a "$USERVAR" != "p" ]; then
		VARIABLE2="$VARIABLE2-$USERVAR"
	fi
	SERVERDIR=/home/$VARIABLE2/pserver
else
	SERVERDIR=/home/$VARIABLE2/server
fi
if [[ ! `screen -ls | grep "add-$VARIABLE2-$VARIABLE4"` ]]; then
if [ "$VARIABLE1" != "migrateserver" ]; then
echo "#!/bin/bash

HOMEFOLDER=$HOMEFOLDER
rm $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
VARIABLE2=$VARIABLE2
VARIABLE4=$VARIABLE4
SERVERDIR=$SERVERDIR
PATTERN='valve\|overviews/\|scripts/\|media/\|particles/\|gameinfo.txt\|steam.inf\|/sound/\|steam_appid.txt\|/hl2/\|/overviews/\|/resource/\|/sprites/'" > $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
echo 'while [[ `screen -ls | grep "del-$VARIABLE2-$VARIABLE4"` ]]; do
	sleep 5
done' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
fi

COUNT=`echo $VARIABLE3 | awk -F_ '{ print $1 }'`
COUNT=$[COUNT+1]
i=2
while [ $i -le $COUNT ]; do
	MODNAME=""
	GAMENAME=`echo $VARIABLE3 | awk -F "_" '{print $'$i'}'`
	if [ "$GAMENAME" != "" ]; then
		if [ "$VARIABLE5" == "" ]; then
			TEMPLATE=4
		elif [ "$VARIABLE5" != "protected" ]; then
			TEMP=(`echo $VARIABLE5 | sed -e 's/-/ /g'`)
			TEMPLATE=${TEMP[$[i-2]]}
		else
			TEMPLATE=1
		fi
		MODNAME=`echo $GAMENAME | awk -F "." '{print $2}'`
		FILEFOUND=""
		FDLFILEFOUND=""
		if [ "$MODNAME" != "" ]; then
			MODINSTALL="1"
			GAMENAME=`echo $GAMENAME | awk -F "." '{print $1}'`
			GAMENAME2=$GAMENAME
		else
			GAMENAME2=$GAMENAME
		fi
		echo "GAMENAME2=$GAMENAME" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
		if [ "$TEMPLATE" == 1 -o "$TEMPLATE" == 4 ]; then
			imagesymlinks
		fi
		if [ "$VARIABLE5" != "protected" ]; then
			if [ "$TEMPLATE" == 2 -o "$TEMPLATE" == 4 ]; then
				GAMENAME="${GAMENAME2}-2"
				imagesymlinks
			fi
			if [ "$TEMPLATE" == 3 -o "$TEMPLATE" == 4 ]; then
				GAMENAME="${GAMENAME2}-3"
				imagesymlinks
			fi
		fi
		if [ "$TEMPLATE" == 4 ]; then
			echo "${IONICE}nice -n +19 find $SERVERDIR/$VARIABLE4/ -type d -print0 | xargs -0 chmod 700" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
			echo "${IONICE}nice -n +19 find $SERVERDIR/$VARIABLE4/ -type f -print0 | xargs -0 chmod 600" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
			echo "${IONICE}nice -n +19 find -L $SERVERDIR/$VARIABLE4/ -type l -print0 | xargs -0 -rf" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
		else
			echo "${IONICE}nice -n +19 find $SERVERDIR/$VARIABLE4/$GAMENAME/ -type d -print0 | xargs -0 chmod 700" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
			echo "${IONICE}nice -n +19 find $SERVERDIR/$VARIABLE4/$GAMENAME/ -type f -print0 | xargs -0 chmod 600" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
			echo "${IONICE}nice -n +19 find -L $SERVERDIR/$VARIABLE4/$GAMENAME/ -type l -print0 | xargs -0 rm -f" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
		fi
		echo 'echo "`date`: Server $VARIABLE4/$GAMENAME2 for user $VARIABLE2 created" >> '"$LOGDIR/update.log" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	fi
	i=$[i+1]
done
if [ "$VARIABLE1" != "migrateserver" ]; then
	chmod +x $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	screen -d -m -S add-$VARIABLE2-$VARIABLE4 $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
fi
fi
}

function reinst_customer_server {
	del_customer_server
	add_customer_server
}

function migration {
	CUTDIRS=${VARIABLE8/ftps:\/\//}
	CUTDIRS=${CUTDIRS/ftp:\/\//}
	CUTDIRS=${CUTDIRS//\/\//\/}
	CUTDIRS=(${CUTDIRS//\// })
	CUTDIRS=${#CUTDIRS[@]}
	CUTDIRS=$[CUTDIRS-1]
	SERVERDIR=/home/$VARIABLE2/server
echo "#!/bin/bash

HOMEFOLDER=$HOMEFOLDER
rm $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
sleep 30
VARIABLE2=$VARIABLE2
VARIABLE4=$VARIABLE4
VARIABLE9=$VARIABLE9
SERVERDIR=$SERVERDIR
PATTERN='valve\|overviews/\|scripts/\|media/\|particles/\|gameinfo.txt\|steam.inf\|/sound/\|steam_appid.txt\|/hl2/\|/overviews/\|/resource/\|/sprites/'" > $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	echo 'while [[ `screen -ls | grep "del-$VARIABLE2-$VARIABLE4"` ]]; do sleep 5; done' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	if [ "$VARIABLE5" == "" -o "$VARIABLE5" == "1" ]; then
		GSTEMPLATE=`echo $VARIABLE3 | awk -F "_" '{ print $2 }'`
	else
		GSTEMPLATE=`echo $VARIABLE3 | awk -F "_" '{ print $2 }'`"-$VARIABLE5"
	fi
	echo "GSTEMPLATE=$GSTEMPLATE" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	add_customer_server
	echo 'if [ ! -d "$SERVERDIR/$VARIABLE4/$GSTEMPLATE/" ]; then mkdir -p "$SERVERDIR/$VARIABLE4/$GSTEMPLATE/"; fi' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	echo 'cd $SERVERDIR/$VARIABLE4/$GSTEMPLATE/' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	echo 'VARIABLE9=`echo $VARIABLE9 | tr -d '"'"'/'"'"'`' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	echo 'MODFOLDER=`find -mindepth 1 -maxdepth 3 -type d -name "$VARIABLE9" | head -n 1`' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	echo 'if [ "$MODFOLDER" != "" ]; then cd $MODFOLDER; fi' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	echo 'find $SERVERDIR/$VARIABLE4/$GSTEMPLATE/ -type f -print0 | xargs -0 rm -f' >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	echo "wget -q -r -l inf -nc -nH --limit-rate=4096K --retr-symlinks --ftp-user=$VARIABLE6 --ftp-password=$VARIABLE7 --cut-dirs=$CUTDIRS --no-check-certificate $VARIABLE8" >> $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	add_customer_server
	chmod +x $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
	screen -d -m -S add-$VARIABLE2-$VARIABLE4 $HOMEFOLDER/temp/add-$VARIABLE2-$VARIABLE4.sh
}

function port_move {
	cd /home/$VARIABLE2/server
	mv $VARIABLE3 $VARIABLE4
}

function map_list {
	if [[ "$VARIABLE1" == "addaddon" ]]; then
		GAMESHORTEN=`echo $GAMEDIR | awk -F '/' '{print $6}'`
		cd $HOMEFOLDER/masterserver/$GAMESHORTEN
		if [ "`find -maxdepth 2 -name srcds_run`" != "" ]; then
			MAPCFGS="1"
			MAPTYPE="bsp"
		elif [ "`find -maxdepth 2 -name hlds_run`" != "" ]; then
			MAPTYPE="bsp"
		elif [ "`find -maxdepth 1 -name ucc-bin`" != "" ]; then
			MAPTYPE="rom"
		fi
		cd $ADDONFOLDER
	else
		if [ "`find -maxdepth 2 -name srcds_run`" != "" ]; then
			MAPCFGS="1"
			MAPTYPE="bsp"
		elif [ "`find -maxdepth 2 -name hlds_run`" != "" ]; then
			MAPTYPE="bsp"
		elif [ "`find -maxdepth 1 -name ucc-bin`" != "" ]; then
			MAPTYPE="rom"
			cd ..
		fi
	fi
	if [ -n $MAPTYPE ]; then
		if [[ "$VARIABLE1" == "addaddon" ]]; then
			if [ "$MAPTYPE" == "bsp" ]; then
				cd `find -maxdepth 2 -name maps | head -n 1`
			elif [ "$MAPTYPE" == "rom" ]; then
				cd `find -maxdepth 2 -name maps | head -n 1`
			fi
			ls *.$MAPTYPE | grep -v "test_hardware\|test_speakers" | awk -F "." '{print $1}' > $GAMEDIR/$VARIABLE3.txt
			cd $ADDONFOLDER
		else
			if [ "`find -maxdepth 2 -name steam.inf | awk -F '/' '{print $2}' | grep -v 'valve' | wc -l`" == "1" ]; then
				cd `find -maxdepth 2 -name steam.inf | awk -F '/' '{print $2}' | grep -v 'valve'`
			elif [ "`find -maxdepth 2 -name steam.inf | awk -F '/' '{print $2}' | grep -v 'valve\|cstrike' | wc -l`" == "1" ]; then
				cd `find -maxdepth 2 -name steam.inf | awk -F '/' '{print $2}' | grep -v 'valve\|cstrike'`
			elif [[ `find -name da2` ]]; then
				cd `find -name da2`
			fi
			if [ -f maplist.txt ]; then rm maplist.txt; fi
			if [ "$MAPTYPE" == "bsp" ]; then
				cd `find -maxdepth 3 -type d -name "maps" | head -n 1`
			elif [ "$MAPTYPE" == "rom" ]; then
				cd `find -maxdepth 3 -type d -name "maps" | head -n 1`
			fi
			ls *.$MAPTYPE 2> /dev/null | grep -v "test_hardware\|test_speakers" | awk -F "." '{print $1}' | while read MAPNAME; do
				echo $MAPNAME >> ../maplist.txt
			done
		fi
	fi
}

function run_backup {
	if [ "$SHORTEN" != "" ]; then
		echo "VARIABLE2=$VARIABLE2
SHORTEN=$SHORTEN
find $BACKUPDIR/ -maxdepth 1 -type f -name \"$VARIABLE2-$SHORTEN*.tar.bz2\" -delete" >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo 'if [ -d /home/$USERNAME/server/$VARIABLE2/$SHORTEN ]; then' >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo "cd /home/$USERNAME/server/$VARIABLE2/$SHORTEN" >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo "${IONICE}"'nice -n +19 tar cfj $BACKUPDIR/$VARIABLE2-$SHORTEN.tar.bz2 .' >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo 'fi' >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo 'if [ -d /home/$USERNAME/server/$VARIABLE2/$SHORTEN-2 ]; then' >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo "cd /home/$USERNAME/server/$VARIABLE2/$SHORTEN-2" >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo "${IONICE}"'nice -n +19 tar cfj $BACKUPDIR/$VARIABLE2-$SHORTEN-2.tar.bz2 .' >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo 'fi' >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo 'if [ -d /home/$USERNAME/server/$VARIABLE2/$SHORTEN-3 ]; then' >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo "cd /home/$USERNAME/server/$VARIABLE2/$SHORTEN-3" >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo "${IONICE}"'nice -n +19 tar cfj $BACKUPDIR/$VARIABLE2-$SHORTEN-3.tar.bz2 .' >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		echo 'fi' >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		if [ "$VARIABLE5" != "" ]; then
			echo "wput -q --limit-rate=$FTPUPLOADLIMIT --basename=/home/$USERNAME/backup/ \"$BACKUPDIR/$VARIABLE2-$SHORTEN.tar.bz2\" \"$VARIABLE5\"" >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
			echo "wput -q --limit-rate=$FTPUPLOADLIMIT --basename=/home/$USERNAME/backup/ \"$BACKUPDIR/$VARIABLE2-$SHORTEN-2.tar.bz2\" \"$VARIABLE5\"" >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
			echo "wput -q --limit-rate=$FTPUPLOADLIMIT --basename=/home/$USERNAME/backup/ \"$BACKUPDIR/$VARIABLE2-$SHORTEN-3.tar.bz2\" \"$VARIABLE5\"" >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
		fi
	fi
}

function backup_servers {
	USERNAME=`id -un`
	BACKUPDIR="/home/$USERNAME/backup"
	echo "#!/bin/bash

rm $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
BACKUPDIR=$BACKUPDIR
USERNAME=$USERNAME" > $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
	if [ ! -d $BACKUPDIR ]; then
		mkdir -p $BACKUPDIR
	fi
	for SHORTEN in $VARIABLE3; do
		SCREENNAME="backup-$VARIABLE2-$SHORTEN"
		if [ "`screen -ls | grep \"$SCREENNAME\"`" == "" ]; then
			run_backup
		fi
	done
	IP=`echo $VARIABLE2 | awk -F '_' '{print $1}'`
	PORT=`echo $VARIABLE2 | awk -F '_' '{print $2}'`
	if [ "$PORT" == "" ]; then
		QUERY="id=$VARIABLE2"
	else
		QUERY="id=$PORT\\&ip=$IP"
	fi
	echo "wget -q --timeout=10 --no-check-certificate -O - $VARIABLE4/get_password.php?w=bu\\&shorten=$USERNAME\\&$QUERY" >> $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
	chmod +x $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
	screen -dmS $SCREENNAME $HOMEFOLDER/temp/backup-$VARIABLE2-$USERNAME.sh
}

function restore_backup {
	USERNAME=`id -un`
	IP=`echo $VARIABLE2 | awk -F '_' '{print $1}'`
	PORT=`echo $VARIABLE2 | awk -F '_' '{print $2}'`
	if [ "$PORT" == "" ]; then
		QUERY="id=$VARIABLE2"
	else
		QUERY="id=$PORT\\&ip=$IP"
	fi
	SCREENNAME=restorerestore-$VARIABLE2-$VARIABLE3
	if ([[ ! `screen -ls | grep "$SCREENNAME"` ]] && [[ ! `screen -ls | grep "backup-$VARIABLE2-$SHORTEN"` ]]); then
		echo "#!/bin/bash" > $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
		echo "rm $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
		if [ "$VARIABLE5" != "" ]; then
			echo "cd /home/$USERNAME/backup/" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
			echo "mv /home/$USERNAME/backup/$VARIABLE2-$VARIABLE3.tar.bz2 /home/$USERNAME/backup/$VARIABLE2-$VARIABLE3_old.tar.bz2" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
			echo "wget -q --timeout=10 --no-check-certificate $VARIABLE5/$VARIABLE2-$VARIABLE3.tar.bz2" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
			echo "if [ -f /home/$USERNAME/backup/$VARIABLE2-$VARIABLE3.tar.bz2 ]; then" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
			echo "	rm /home/$USERNAME/backup/$VARIABLE2-$VARIABLE3_old.tar.bz2" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
			echo "else" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
			echo "	mv /home/$USERNAME/backup/$VARIABLE2-$VARIABLE3_old.tar.bz2 /home/$USERNAME/backup/$VARIABLE2-$VARIABLE3.tar.bz2" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
			echo "fi" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
		fi
		echo "if [ -f /home/$USERNAME/backup/$VARIABLE2-$VARIABLE3.tar.bz2 ]; then" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
		echo "rm -rf /home/$USERNAME/server/$VARIABLE2/$VARIABLE3/*" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
		echo "${IONICE}nice -n +19 tar -C /home/$USERNAME/server/$VARIABLE2/$VARIABLE3 -xjf /home/$USERNAME/backup/$VARIABLE2-$VARIABLE3.tar.bz2" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
		echo "wget -q --no-check-certificate -O - $VARIABLE4/get_password.php?w=rb\\&shorten=$USERNAME\\&$QUERY" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
		echo "fi" >> $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
		chmod +x $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
		screen -dmS $SCREENNAME $HOMEFOLDER/temp/restore-$VARIABLE2-$VARIABLE3-$USERNAME.sh
	fi
}

function match_addons {
SERVERDIR="/home/$VARIABLE2/server/$VARIABLE3"
if [ -d $SERVERDIR/*/addons/sourcemod ]; then
	for ADDONLIST in $VARIABLE4; do
		if [ -d $HOMEFOLDER/masteraddons/$ADDONLIST/addons/sourcemod ]; then
			cd $HOMEFOLDER/masteraddons/$ADDONLIST/addons/sourcemod
			find gamedata plugins scripting -mindepth 1 \( -iname "*.smx" \) 2> /dev/null | while read FILE; do
				FILENAME=`basename $FILE`
				find $SERVERDIR/*/addons/sourcemod/ -mindepth 2 -name "$FILENAME" | while read FOUNDFILE; do
					if [ "`stat -c %Y $FILE`" -gt "`stat -c %Y $FOUNDFILE`" 2> /dev/null ]; then
						cp $FILE $FOUNDFILE
					fi
				done
			done
		fi
	done
fi
if [ -d $SERVERDIR/*/cfg/mani_admin_plugin ]; then
	for ADDONLIST in $VARIABLE4; do
		if [ -d $HOMEFOLDER/masteraddons/$ADDONLIST/cfg/mani_admin_plugin ]; then
			find $HOMEFOLDER/masteraddons/$ADDONLIST/cfg/mani_admin_plugin -maxdepth 1 -name "gametypes.txt" 2> /dev/null | while read FILE; do
				FILENAME=`basename $FILE`
				find $SERVERDIR/*/cfg/mani_admin_plugin -maxdepth 1 -name "gametypes.txt" | while read FOUNDFILE; do
					if [ "`stat -c %Y $FILE`" -gt "`stat -c %Y $FOUNDFILE`" 2> /dev/null ]; then
						cp $FILE $FOUNDFILE
					fi
				done
			done
		fi
	done
fi
MATCHADDONS=1
ADDONS=$VARIABLE4
VARIABLE4="$VARIABLE2/server/$VARIABLE3"
VARIABLE2="tool"
for VARIABLE3 in $ADDONS; do
	add_addon
done
}

function server_start {
if [ -z $SERVERDIR ]; then
	if [ "$VARIABLE5" == "protected" ]; then
		if [[ "`echo $VARIABLE2 | awk -F "-" '{print $2}'`" == "p" ]]; then
			VARIABLE2=`echo $VARIABLE2 | awk -F "-" '{print $1}'`
		elif [[ "`echo $VARIABLE2 | awk -F "-" '{print $2}'`" == "" ]]; then
			VARIABLE2=`echo $VARIABLE2 | awk -F "-" '{print $1}'`
		else
			VARIABLE2=`echo $VARIABLE2 | awk -F "-" '{print $1"-"$2}'`
		fi
		SERVERDIR=/home/$VARIABLE2/pserver/$VARIABLE3
	else
		SERVERDIR=/home/$VARIABLE2/server/$VARIABLE3
	fi
fi
SCREENNAME="`echo $SERVERDIR | awk  -F '/' '{print $5}'`"
FOLDERCHECK=`readlink -f $VARIABLE0`
if [[ ! `ps x | grep "start-${VARIABLE2}-${SCREENNAME}.sh" | grep -v grep` ]]; then
if [ ! -d $SERVERDIR ]; then
	mkdir -p $SERVERDIR
fi
screen -wipe > /dev/null 2>&1
SYNCGSPATH=`echo $SERVERDIR | awk -F '/' '{print $5}'`
SYNCGSFOLDER=`echo $SERVERDIR | awk -F '/' '{print $6}' | awk -F '-' '{print $1}'`
if [ "$VARIABLE5" != "protected" ]; then
GAMES=`ls /home/$VARIABLE2/server/$SYNCGSPATH | grep $SYNCGSFOLDER | egrep -v '\-2|\-3'`
else
GAMES=`ls /home/$VARIABLE2/pserver/$SYNCGSPATH | grep $SYNCGSFOLDER | egrep -v '\-2|\-3'`
fi
I=0
GAMESTRING=''
for GAME in $GAMES; do
	GAMESTRING="${GAMESTRING}_${GAME}"
	I=$[I+1]
done
GAMESTRING="${I}${GAMESTRING}"
if [ "$VARIABLE5" != "protected" ]; then
	CLEANFILE=$HOMEFOLDER/temp/cleanup-${VARIABLE2}-${SCREENNAME}.sh
	STARTFILE=$HOMEFOLDER/temp/start-${VARIABLE2}-${SCREENNAME}.sh
	CLEANUPDIR="/home/$VARIABLE2/server/"
else
	CLEANFILE=$HOMEFOLDER/temp/cleanup-${VARIABLE2}-p-${SCREENNAME}.sh
	STARTFILE=$HOMEFOLDER/temp/start-${VARIABLE2}-p-${SCREENNAME}.sh
	CLEANUPDIR="/home/$VARIABLE2/pserver/"
fi
cd $SERVERDIR
DONOTTOUCH='*/bin/*.so bin/*.so */cfg/valve.rc srcds_* hlds_* *.sh *.run'
for ISFILE in $DONOTTOUCH; do
	find $ISFILE -maxdepth 1 -type f 2> /dev/null | while read BADFILE; do
		MASTERGAME=`echo $SERVERDIR | awk -F '/' '{print $6}' | awk -F '-' '{print $1}'`
		MASTERGAMEFOLDER=`echo $SERVERDIR | awk -F '/' '{print $7"/"$8}' | sed 's/\/\//\//g' | sed 's/\/\//\//g'`
		MASTERPATH=`echo "$HOMEFOLDER/masterserver/$MASTERGAME/$MASTERGAMEFOLDER/$BADFILE" | sed 's/\/\//\//g'`
		BADFILEPATH=`echo "$SERVERDIR/$BADFILE" | sed 's/\/\//\//g'`
		chmod 666 $BADFILE
		rm $BADFILE
		if [ -f $BADFILE ]; then
			exit 0
		fi
		ln -s $MASTERPATH $BADFILEPATH
	done
	find $ISFILE -maxdepth 1 -type l 2> /dev/null | while read BADFILE; do
		MASTERGAME=`echo $SERVERDIR | awk -F '/' '{print $6}' | awk -F '-' '{print $1}'`
		MASTERGAMEFOLDER=`echo $SERVERDIR | awk -F '/' '{print $7"/"$8}' | sed 's/\/\//\//g' | sed 's/\/\//\//g'`
		MASTERPATH=`echo "$HOMEFOLDER/masterserver/$MASTERGAME/$MASTERGAMEFOLDER/$BADFILE" | sed 's/\/\//\//g'`
		BADFILEPATH=`echo "$SERVERDIR/$BADFILE" | sed 's/\/\//\//g'`
		if [ "`ls -la $BADFILE | awk '{print $11}'`" != "$MASTERPATH" ]; then
			chmod 666 $BADFILE
			rm $BADFILE
			if [ -f $BADFILE ]; then
				exit 0
			fi
			ln -s $MASTERPATH $BADFILEPATH
		fi
	done
done
if [ "`screen -ls | grep '$SCREENNAME.'`" != "" ]; then
	STARTED=no
else
	if [ -d $SERVERDIR ]; then
		map_list
		cd $SERVERDIR
		if [ "$VARIABLE7" != "" ]; then
			TASKSET="taskset -c $VARIABLE7 "
		else
			TASKSET=''
		fi
		LOGTIME=`grep LOGTIME $HOMEFOLDER/conf/config.cfg | awk -F "=" '{print $2}' | tr -d '"'`
		DEMOTIME=`grep DEMOTIME $HOMEFOLDER/conf/config.cfg | awk -F "=" '{print $2}' | tr -d '"'`
		ZTMPTIME=`grep ZTMPTIME $HOMEFOLDER/conf/config.cfg | awk -F "=" '{print $2}' | tr -d '"'`
		BADTIME=`grep BADTIME $HOMEFOLDER/conf/config.cfg | awk -F "=" '{print $2}' | tr -d '"'`
		BADFILES=`grep BADFILES $HOMEFOLDER/conf/config.cfg | awk -F "=" '{print $2}' | tr -d '"' | sed 's/, / /g' | sed 's/,/ /g'`
		if [ ! `echo "$LOGTIME" | grep -E "^[0-9]+$"` ]; then
			LOGTIME="-mtime +7"
		else
			if [ "$LOGTIME" == "0" ]; then
				LOGTIME=""
			else
				LOGTIME="-mtime +$LOGTIME"
			fi
		fi
		if [ ! `echo "$DEMOTIME" | grep -E "^[0-9]+$"` ]; then
			DEMOTIME="-mtime +7"
		else
			if [ "$DEMOTIME" == "0" ]; then
				DEMOTIME=""
			else
				DEMOTIME="-mtime +$DEMOTIME"
			fi
		fi
		if [ ! `echo "$ZTMPTIME" | grep -E "^[0-9]+$"` ]; then
				ZTMPTIME="-mtime +7"
		else
			if [ "$ZTMPTIME" == "0" ]; then
				ZTMPTIME=""
			else
				ZTMPTIME="-mtime +$ZTMPTIME"
			fi
		fi
		if [ ! `echo "$BADTIME" | grep -E "^[0-9]+$"` ]; then
			BADTIME="-mtime +7"
		else
			if [ "$BADTIME" == "0" ]; then
				BADTIME=""
			else
				BADTIME="-mtime +$BADTIME"
			fi
		fi
		if [ ! -f $STARTFILE ]; then
		echo '#!/bin/bash' > $STARTFILE
		echo "rm $STARTFILE" >> $STARTFILE
		echo 'while [ "`ps x | grep '"'add-${VARIABLE2}'"' | grep -v grep`" != "" ]; do' >> $STARTFILE
		echo 'sleep 0.5' >> $STARTFILE
		echo 'done' >> $STARTFILE
		fi
		echo "cd ${SERVERDIR}" >> $STARTFILE
		for FILE in $BADFILES; do
			echo "find $CLEANUPDIR -type f -name \"*.$FILE\" $BADTIME -delete" >> $STARTFILE
		done
		echo "${IONICE}find -L $CLEANUPDIR -type l -delete" >> $STARTFILE
		echo "${IONICE}find $CLEANUPDIR -type f -name '*.log' $LOGTIME -delete" >> $STARTFILE
		echo "${IONICE}find $CLEANUPDIR -type f -name '*.dem' $DEMOTIME -delete" >> $STARTFILE
		echo "${IONICE}find $CLEANUPDIR -type f -name '*.ztmp' $ZTMPTIME -delete" >> $STARTFILE
		if [ "$VARIABLE5" != "protected" ]; then
			echo "${IONICE}nice -n +19 find /home/$VARIABLE2/ -maxdepth 1  \( -type f -or -type l \) ! \( -name ".bashrc" -or -name \".bash_history\" -or -name \".profile\" -or -name \".bash_logout\" \) -delete" >> $STARTFILE
			echo "${IONICE}nice -n +19 find /home/$VARIABLE2/ -mindepth 2 -maxdepth 3 \( -type f -or -type l \) ! -name \"*.bz2\" -delete" >> $STARTFILE
			echo "${IONICE}nice -n +19 find $DATADIR -type f -user `whoami` ! -name \"*.bz2\" -delete" >> $STARTFILE
		fi
		# Steampipe Fix Start
		echo 'if [ -d "tf" -o -d "dod" -o -d "hl2mp" -o -d "cstrike" ]; then' >> $STARTFILE
		echo 'if [ "`find orangebox/ css/ -type f 2> /dev/null | wc -l`" == "0" ]; then rm -rf orangebox/ css/ 2> /dev/null; fi' >> $STARTFILE
		echo 'find orangebox/ css/ -mindepth 1 -maxdepth 1 -type d -name tf -o -name dod -o -name hl2mp -o -name cstrike 2> /dev/null | while read olddir; do' >> $STARTFILE
		echo 'find $olddir -type f | while read oldfile; do' >> $STARTFILE
		echo 'file=${oldfile/orangebox\//}' >> $STARTFILE
		echo 'file=${file/css\//}' >> $STARTFILE
		echo 'dir=`dirname "$file"`' >> $STARTFILE
		echo 'if [ ! -d $dir ]; then mkdir -p $dir; fi' >> $STARTFILE
		echo 'if [ ! -f "$file" ]; then mv "$oldfile" "$file"; fi' >> $STARTFILE
		echo 'done' >> $STARTFILE
		echo 'done' >> $STARTFILE
		echo 'if [ "`find orangebox/ css/ -type f 2> /dev/null | wc -l`" == "0" ]; then rm -rf orangebox/ css/ 2> /dev/null; fi' >> $STARTFILE
		echo 'fi' >> $STARTFILE
		# Steampipe Fix Ende
		echo "if [ -f screenlog.0 ]; then rm screenlog.0; fi" >> $STARTFILE
		echo "${TASKSET} screen -A -m -d -L -S $SCREENNAME $VARIABLE4" >> $STARTFILE
		chmod +x $STARTFILE
		$STARTFILE > /dev/null 2>&1 &
		STARTED=yes
	else
		STARTED=no
	fi
fi
if [ -f $LOGDIR/server.log ]; then
	if [ "$STARTED" == "yes" ]; then
		echo "`date`: User started $VARIABLE2: $VARIABLE4" >> $LOGDIR/server.log
	else
		echo "`date`: Starting server $VARIABLE2 for user $VARIABLE2 failed" >> $LOGDIR/server.log
	fi
fi
fi
} 

function server_stop {
if [ "$VARIABLE5" == "protected" ]; then
	if [[ "`echo $VARIABLE2 | awk -F "-" '{print $2}'`" == "p" ]]; then
		VARIABLE2=`echo $VARIABLE2 | awk -F "-" '{print $1}'`
	elif [[ "`echo $VARIABLE2 | awk -F "-" '{print $2}'`" == "" ]]; then
		VARIABLE2=`echo $VARIABLE2 | awk -F "-" '{print $1}'`
	else
		VARIABLE2=`echo $VARIABLE2 | awk -F "-" '{print $1"-"$2}'`
	fi
	SERVERDIR=/home/$VARIABLE2/pserver/$VARIABLE3
else
	SERVERDIR=/home/$VARIABLE2/server/$VARIABLE3
fi
SCREENNAME="`echo $SERVERDIR | awk  -F '/' '{print $5}'`"
if [ "$VARIABLE5" != "protected" ]; then
	STARTFILE=$HOMEFOLDER/temp/start-${VARIABLE2}-${SCREENNAME}.sh
else
	STARTFILE=$HOMEFOLDER/temp/start-${VARIABLE2}-p-${SCREENNAME}.sh
fi
if [ "$VARIABLE1" == "grestart" ]; then
	echo '#!/bin/bash' > $STARTFILE
	echo "rm $STARTFILE" >> $STARTFILE
	echo "SCREENNAME=$SCREENNAME" >> $STARTFILE
	echo 'while [ "`ps x | grep '"'add-${VARIABLE2}'"' | grep -v grep`" != "" ]; do' >> $STARTFILE
	echo 'sleep 0.5' >> $STARTFILE
	echo 'done' >> $STARTFILE
	addStop $STARTFILE temp/start-${VARIABLE2}-p-${SCREENNAME}.sh
else
	echo "#!/bin/bash" > $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
	echo "rm $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
	echo "SCREENNAME=$SCREENNAME" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
	addStop $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
	echo "${IONICE}nice -n +19 find $HOMEFOLDER/temp/ -type f -user `whoami` -delete" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
	echo "${IONICE}nice -n +19  find /tmp -user `whoami` -delete" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
	echo "crontab -r 2> /dev/null" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
	if [ "$VARIABLE5" == "protected" ]; then
		echo "${IONICE}nice -n +19 find /home/$VARIABLE2/pserver/ -type d -print0 | xargs -0 chmod 700" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
		echo "${IONICE}nice -n +19 find /home/$VARIABLE2/pserver/ -type f -print0 | xargs -0 chmod 600" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
	else
		echo "${IONICE}nice -n +19 find /home/$VARIABLE2/server/ -type d -print0 | xargs -0 chmod 700" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
		echo "${IONICE}nice -n +19 find /home/$VARIABLE2/server/ -type f -print0 | xargs -0 chmod 600" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
		echo "${IONICE}nice -n +19 find /home/$VARIABLE2/ -mindepth 2 -maxdepth 3 \( -type f -or -type l \) ! -name \"*.bz2\" -delete" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
		echo "${IONICE}nice -n +19 find $DATADIR -type f -user `whoami` ! -name \"*.bz2\" -delete" >> $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
	fi
	chmod +x $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
	screen -d -m -S cleanup $HOMEFOLDER/temp/fullstop-${VARIABLE2}-${SCREENNAME}.sh
fi
}

function addStop {
	echo "screen -wipe > /dev/null 2>&1" >> $1
	if [[ `screen -ls | grep $SCREENNAME` ]]; then
		SENDTO=`screen -ls | grep $SCREENNAME | awk '{print $1}' | head -n 1`
		if [ "$VARIABLE6" == "minecraft" -o "$VARIABLE4" == "minecraft" ]; then
			screenEnter $1
			echo "screen -p 0 -S $SENDTO -X stuff \"say SERVER WILL SHUT DOWN IN 10 SECONDS\"" >> $1
			screenEnter $1
			echo "screen -p 0 -S $SENDTO -X stuff \"save-all\"" >> $1
			screenEnter $1
			echo "sleep 10" >> $1
			echo "screen -p 0 -S $SENDTO -X stuff \"stop\"" >> $1
			screenEnter $1
			echo "sleep 5" >> $1
		elif [ "$VARIABLE6" == "srcds_run" -o "$VARIABLE4" == "srcds_run" ]; then
			screenEnter $1
			echo "screen -p 0 -S $SENDTO -X stuff \"tv_stoprecord\"" >> $1
			screenEnter $1
		fi
		echo 'if [ "`screen -ls | grep $SCREENNAME | wc -l`" == "1" ]; then' >> $1
		echo 'screen -r $SCREENNAME -X quit' >> $1
		echo 'fi' >> $1
		echo "ps x | grep -v '$1' | grep -v '$2' | grep $SCREENNAME | grep -v grep | awk '{print "'$1'"}' | while read PID; do" >> $1
		echo 'kill $PID' >> $1
		echo 'kill -9 $PID' >> $1
		echo 'done' >> $1
		echo 'echo "`date`: Server $VARIABLE3 for user $VARIABLE2 stopped" >> '$LOGDIR'/server.log' >> $1
	else
		echo "No screen found: $SCREENNAME"
	fi
	echo "ps x | grep -v '$1' | grep -v '$2' | grep `echo $SCREENNAME | awk -F '_' '{print $1}'` | grep `echo $SCREENNAME | awk -F '_' '{print $2}'` | grep -v grep | awk '{print "'$1'"}' | while read PID; do" >> $1
	echo 'kill $PID' >> $1
	echo 'kill -9 $PID' >> $1
	echo 'done' >> $1
	echo "ps x | grep -v '$1' | grep -v '$2' | grep 'java' | grep -v grep | awk '{print "'$1'"}' | while read PID; do" >> $1
	echo 'kill $PID' >> $1
	echo 'kill -9 $PID' >> $1
	echo 'done' >> $1
}

function screenEnter {
	echo "screen -p 0 -S $SENDTO -X stuff $'\n'" >> $1
}

function mc_worldsafe {
SENDTO=`screen -ls | grep $VARIABLE2 | awk '{print $1}'`
if [ "$SENDTO" != "" ]; then
	screen -p 0 -S $SENDTO -X stuff $'\n'
	screen -p 0 -S $SENDTO -X stuff "say SERVER WILL SAVE THE WORLD NOW"
	screen -p 0 -S $SENDTO -X stuff $'\n'
	screen -p 0 -S $SENDTO -X stuff $'\n'
	screen -p 0 -S $SENDTO -X stuff "save-all"
	screen -p 0 -S $SENDTO -X stuff $'\n'
fi
}

function demo_upload {
	USERNAME=`echo $VARIABLE2 | awk -F '/' '{print $3}'`
	SCREENNAME=`echo $VARIABLE2 | awk -F '/' '{print $5}'`
	if [ "$VARIABLE6" == "" ]; then
		KEEP=''
	else
		if [ "$VARIABLE6" == "keep" ]; then
			KEEP='-k'
		else
			KEEP=''
		fi
	fi
	LSOF=`which lsof`
	if [ "$LSOF" == "" ]; then KEEP='-k'; fi
	if [[ `which zip` ]]; then
		if [ "$KEEP" == "" ]; then
			KEEP='-m'
		fi
		COMPRESS="${IONICE}"'nice -n +19 zip -q $KEEP $DEMOPATH/$DEMO.zip $DEMOPATH/$DEMO'
		ZIP='zip'
	elif [[ `which bzip2` ]]; then
		COMPRESS="${IONICE}"'nice -n +19 bzip2 -s -q -9 $KEEP $DEMOPATH/$DEMO'
		ZIP='bz2'
	fi
	if [ "$ZIP" != "" ]; then
		cat > $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh <<EOF
#!/bin/bash

rm $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
VARIABLE3="$VARIABLE3/"
VARIABLE4="$VARIABLE4"
KEEP="$KEEP"
ZIP="$ZIP"
sleep 5
cd $VARIABLE2
EOF
		echo 'while [ "`screen -ls | grep '"'"'cleanup'"'"'`" != "" ]; do' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
		echo 'sleep 1' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
		echo 'done' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
		echo 'while [ "`ps x | grep -v grep | awk '"'"'{print $7}'"'"' | grep '"'add-${USERNAME}-${SCREENNAME}'"'`" != "" ]; do' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
		echo 'sleep 1' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
		echo 'done' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
		if [ "$VARIABLE5" == "auto" ]; then
			echo 'DEMOPATH=`find -mindepth 1 -maxdepth 1  -type d -name "$VARIABLE4"`' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			echo 'tail -f screenlog.0 | while read LINE; do' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			echo '	if [[ `echo $LINE | grep "Completed SourceTV demo"` ]]; then' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			echo '		DEMO=`echo -n "$LINE" | awk '"'"'{print $4}'"'"' | tr -d '"'"'"'"'"' | tr -d '"'"','"'"'`' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			if [ "$LSOF" != "" ]; then
				echo '	if [[ ! `lsof $DEMOPATH/$DEMO` ]]; then' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			fi
			echo "		$COMPRESS" >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			echo '		wput -q --limit-rate=1024K --remove-source-files --tries 3 --basename="$DEMOPATH" "$DEMOPATH/$DEMO.$ZIP" "$VARIABLE3"' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			if [ "$LSOF" != "" ]; then
				echo '	fi' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			fi
			echo '	fi' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			echo 'done' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
		else
		# pid killen und dann neuen loop
			echo 'cd `find -mindepth 1 -maxdepth 1  -type d -name "$VARIABLE4"`' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			echo 'find . -maxdepth 1 -type f -name "*.dem" | while read LINE; do' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			echo '	DEMOPATH="`dirname $LINE`/"' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			echo '	DEMO="`basename $LINE`/"' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			if [ "$LSOF" != "" ]; then
				echo '	if [[ ! `lsof $LINE` ]]; then' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			fi
			echo "		$COMPRESS" >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			echo '		wput -q --limit-rate=1024K --remove-source-files --tries 3 --basename="$DEMOPATH" "$DEMOPATH/$DEMO.$ZIP" "$VARIABLE3"' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			if [ "$LSOF" != "" ]; then
				echo '	fi' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
			fi
			echo 'done' >> $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
		fi
		chmod +x $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
		screen -d -m -S $USERNAME-$SCREENNAME-upload $TEMPFOLDER/$USERNAME-$SCREENNAME-upload.sh
	fi
}

function copy_addon_files {
	cp -sr $ADDONFOLDER/* $GAMEDIR/ > /dev/null 2>&1
	cd $ADDONFOLDER
	find -type f -name "*.xml" -o -name "*.cfg" -o -name "*.conf" -o -name "*.gam"  -o -name "*.ini" -o -name "*.txt" -o -name "*.vdf" -o -name "*.smx" -o -name "*.sp" -o -name "*.sma" -o -name "*.amxx" -o -name "*.lua" | sed 's/\.\///g' | while read FILES; do
		FOLDER=`dirname $FILES`
		FILENAME=`basename $FILES`
		if [ ! -d $GAMEDIR/$FOLDER ]; then
			mkdir -p $GAMEDIR/$FOLDER/
		fi
		find $GAMEDIR/$FILES -type l -delete > /dev/null 2>&1
		if [ "$FILENAME" == "liblist.gam" -a "$MATCHADDONS" != "1" ]; then
			mv $GAMEDIR/$FILES $GAMEDIR/$FILES.old
			cp $ADDONFOLDER/$FILES $GAMEDIR/$FILES
		elif [ "$FILENAME" == "plugins.ini" -a "$MATCHADDONS" != "1" ]; then
			if [ -f $GAMEDIR/$FILES ]; then
				cat $ADDONFOLDER/$FILES | while read $LINE; do
					if [ `grep "$LINE" $GAMEDIR/$FILES` == "" ]; then
						echo $LINE >> $GAMEDIR/$FILES
					fi
				done
			else
				cp $ADDONFOLDER/$FILES $GAMEDIR/$FILES
			fi
		elif [ "$FILENAME" == "gametypes.txt" -a "$MATCHADDONS" != "1" ]; then
			if [ "$FOLDER" != "cfg/mani_admin_plugin" ]; then
				cp $ADDONFOLDER/$FILES $GAMEDIR/$FILES
			fi
		elif [ "$MATCHADDONS" == "1" -a ! -f $GAMEDIR/$FILES -a ! -f "$GAMEDIR/$FOLDER/disabled/$FILENAME" ]; then
			cp $ADDONFOLDER/$FILES $GAMEDIR/$FILES
		elif [ "$MATCHADDONS" != "1" -a ! -f $GAMEDIR/$FILES ]; then
			cp $ADDONFOLDER/$FILES $GAMEDIR/$FILES
		fi
	done
}

function sync_addons {
	echo "#!/bin/bash" > $HOMEFOLDER/temp/sync-addons.sh
	echo "rm $HOMEFOLDER/temp/sync-addons.sh" >> $HOMEFOLDER/temp/sync-addons.sh
	if [ "$VARIABLE3" != "maps" ]; then
		echo "cd $MAPDIR/" >> $HOMEFOLDER/temp/sync-addons.sh
		for MAPPACKAGE in $VARIABLE3; do
			if [ "$MAPPACKAGE" != "" ]; then
				if [ "$SYNCTOOL" == 'rsync' ]; then
					echo "$SYNCCMD/mastermaps/$MAPPACKAGE $MAPDIR/" >> $HOMEFOLDER/temp/sync-addons.sh
				elif [ "$SYNCTOOL" == 'wget' ]; then
					echo "$SYNCCMD/mastermaps/$MAPPACKAGE" >> $TEMPFOLDER/updateSteamCmd.sh
					echo "find $MAPDIR/$MAPPACKAGE -name .listing -delete" >> $HOMEFOLDER/temp/sync-addons.sh
				fi
				echo "find $MAPDIR/$MAPPACKAGE -type d -print0 | xargs -0 chmod 750" >> $HOMEFOLDER/temp/sync-addons.sh
				echo "find $MAPDIR/$MAPPACKAGE -type f -print0 | xargs -0 chmod 640" >> $HOMEFOLDER/temp/sync-addons.sh
			fi
		done
	fi
	if [ "$VARIABLE4" != "addons" ]; then
		echo "cd $ADDONDIR/" >> $HOMEFOLDER/temp/sync-addons.sh
		for ADDON in $VARIABLE4; do
			if [ "$ADDON" != "" ]; then
				if [ "$SYNCTOOL" == 'rsync' ]; then
					echo "$SYNCCMD/masteraddons/$ADDON $ADDONDIR/" >> $HOMEFOLDER/temp/sync-addons.sh
				elif [ "$SYNCTOOL" == 'wget' ]; then
					echo "$SYNCCMD/mastermaps/$ADDON" >> $TEMPFOLDER/updateSteamCmd.sh
					echo "find $ADDONDIR/$ADDON -name .listing -delete" >> $HOMEFOLDER/temp/sync-addons.sh
				fi
				echo "find $ADDONDIR/$ADDON -type d -print0 | xargs -0 chmod 750" >> $HOMEFOLDER/temp/sync-addons.sh
				echo "find $ADDONDIR/$ADDON -type f -print0 | xargs -0 chmod 640" >> $HOMEFOLDER/temp/sync-addons.sh
			fi
		done
	fi
	chmod +x $HOMEFOLDER/temp/sync-addons.sh
	screen -dmS sync-addons $HOMEFOLDER/temp/sync-addons.sh
}
function sync_server {
	echo "#!/bin/bash" > $TEMPFOLDER/sync-server.sh
	echo "rm $TEMPFOLDER/sync-server.sh" >> $TEMPFOLDER/sync-server.sh
	echo "cd $MASTERSERVERDIR/" >> $TEMPFOLDER/sync-server.sh
	echo "BOMRM=\"sed \"'s/^\xef\xbb\xbf//g'\"\"" >> $TEMPFOLDER/sync-server.sh
	for SERVER in $VARIABLE3; do
		if [ "$SERVER" != "" ]; then
			if [ "$SYNCTOOL" == 'rsync' ]; then
				echo "$SYNCCMD/masterserver/$SERVER $MASTERSERVERDIR/ > $LOGDIR/update-$SERVER.log" >> $TEMPFOLDER/sync-server.sh
				echo "$SYNCCMD/conf/fdl-$SERVER.list $HOMEFOLDER/conf/ > $LOGDIR/update-$SERVER.log" >> $TEMPFOLDER/sync-server.sh
			elif [ "$SYNCTOOL" == 'wget' ]; then
				echo "$SYNCCMD/masterserver/$SERVER > $LOGDIR/update-$SERVER.log" >> $$TEMPFOLDER/sync-server.sh
				echo "cd $HOMEFOLDER/conf/ > $LOGDIR/update-$SERVER.log" >> $TEMPFOLDER/sync-server.sh
				echo "$SYNCCMD/conf/fdl-$SERVER.list > $LOGDIR/update-$SERVER.log" >> $TEMPFOLDER/sync-server.sh
				echo "find $MASTERSERVERDIR/$SERVER -type d -print0 | xargs -0 chmod 750" >> $TEMPFOLDER/sync-server.sh
				echo "find $MASTERSERVERDIR/$SERVER -type f ! -perm -750 ! -perm -755 -print0 | xargs -0 chmod 640" >> $TEMPFOLDER/sync-server.sh
				echo "find $MASTERSERVERDIR/$SERVER -name .listing -delete" >> $TEMPFOLDER/sync-server.sh
			fi
			if [ "$VARIABLE4" != "" ]; then
				echo "VARIABLE4=$VARIABLE4"  >> $TEMPFOLDER/sync-server.sh
				echo "SERVER=$SERVER"  >> $TEMPFOLDER/sync-server.sh
				echo 'I=0'  >> $TEMPFOLDER/sync-server.sh
				echo 'CHECK=`wget -q --timeout=10 --no-check-certificate -O - $VARIABLE4/get_password.php?w=ms\&shorten=$SERVER | $BOMRM`'  >> $TEMPFOLDER/sync-server.sh
				echo 'while [ "$CHECK" != "ok" -a "$I" -le "10" ]; do'  >> $TEMPFOLDER/sync-server.sh
				echo 'if [ "$CHECK" == "" ]; then'  >> $TEMPFOLDER/sync-server.sh
				echo 'I=11'  >> $TEMPFOLDER/sync-server.sh
				echo 'else'  >> $TEMPFOLDER/sync-server.sh
				echo 'sleep 30'  >> $TEMPFOLDER/sync-server.sh
				echo 'I=$[I+1]'  >> $TEMPFOLDER/sync-server.sh
				echo 'CHECK=`wget -q --timeout=10 --no-check-certificate -O - $VARIABLE4/get_password.php?w=ms\&shorten=$SERVER | $BOMRM`'  >> $TEMPFOLDER/sync-server.sh
				echo 'fi'  >> $TEMPFOLDER/sync-server.sh
				echo 'done'  >> $TEMPFOLDER/sync-server.sh
			fi
		fi
	done
	chmod +x $TEMPFOLDER/sync-server.sh
	screen -dmS sync-server $TEMPFOLDER/sync-server.sh
}

function add_addon {
	if [ "$VARIABLE5" != "" ]; then
		if [ "`find /home/$VARIABLE4 -mindepth 1 -maxdepth 3 -type d -name ${VARIABLE5} | wc -l`" == "1" ]; then
			GAMEDIR=`find /home/$VARIABLE4 -mindepth 1 -maxdepth 3 -type d -name "$VARIABLE5" | head -n 1`
		else
			GAMEDIR=`find /home/$VARIABLE4 -mindepth 1 -maxdepth 1 -type d -name "$VARIABLE5" | head -n 1`
		fi
	elif [ -f /home/$VARIABLE4/hlds_run -a -d /home/$VARIABLE4/czero ]; then
		GAMEDIR="/home/$VARIABLE4/czero"
	elif [ -f /home/$VARIABLE4/srcds_run -o -f /home/$VARIABLE4/hlds_run ]; then
		GAMEDIR="`find /home/$VARIABLE4 -mindepth 1 -maxdepth 1 -type d -name csgo -o -name cstrike -o -name dod -o -name hl2mp -o -name tf | head -n1`"
	fi
	if [ "$GAMEDIR" == "" ]; then
		GAMEDIR="/home/$VARIABLE4"
	fi
	if [ "$VARIABLE2" == "map" ]; then
		ADDONFOLDER=$MAPDIR/$VARIABLE3
		if [ ! -d $ADDONFOLDER ]; then
			exit 0
		fi
		cd $ADDONFOLDER
		map_list
	elif [ "$VARIABLE2" == "tool" ]; then
		ADDONFOLDER=$ADDONDIR/$VARIABLE3
		if [ ! -d $ADDONFOLDER ]; then
			exit 0
		fi
		cd $ADDONFOLDER
	else
		exit 0
	fi
	USER=`echo $GAMEDIR | awk -F/ '{print $3}'`
	copy_addon_files&
	if [ -f $LOGDIR/addons.log ]; then
		echo "`date`: Installed $VARIABLE3 at the server $GAMEDIR for user $USER" >> $LOGDIR/addons.log
	fi
}

function del_addon_files {
	find -mindepth 1 -type f | sed 's/\.\///g' | while read FILES; do
		if [ "`basename $FILES`" == "liblist.gam" ]; then
			mv $GAMEDIR/$FILES.old $GAMEDIR/$FILES
		elif [ "`basename $FILES`" == "plugins.ini" ]; then
			if [ -f $HOMEFOLDER/temp/$USER.pluginlist.temp ]; then
				rm $HOMEFOLDER/temp/$USER.pluginlist.temp
			fi
			cat $GAMEDIR/$FILES | while read LINE; do
				if [[ `grep "$LINE" $FILES` == "" ]]; then 
					echo "$LINE" >> $HOMEFOLDER/temp/$USER.pluginlist.temp
				fi
			done
			cp $HOMEFOLDER/temp/$USER.pluginlist.temp $GAMEDIR/$FILES
			rm $HOMEFOLDER/temp/$USER.pluginlist.temp
		else
			rm -rf $GAMEDIR/$FILES > /dev/null 2>&1
			if [ "$FILES" == "liblist.gam" ]; then
				mv $GAMEDIR/$FILES.old $GAMEDIR/$FILES > /dev/null 2>&1
			fi
		fi
	done
	cd $GAMEDIR
	find -mindepth 1 -type d -empty -delete
	if [ "$VARIABLE6" != "" ]; then
		for FOLDER in $VARIABLE6; do
			find -mindepth 1 -name "$FOLDER" | while read FOLDERS; do
				if [ -d $FOLDERS ]; then
					rm -rf $FOLDERS
				fi
			done
		done
	fi
}

function del_addon {
	USER=`echo $GAMEDIR | awk -F/ '{print $3}'`
	if [ "$VARIABLE2" == "map" ]; then
		ADDONFOLDER=$MAPDIR/$VARIABLE3
		if [ ! -d $ADDONFOLDER ]; then
			exit 0
		fi
	elif [ "$VARIABLE2" == "tool" ]; then
		ADDONFOLDER=$ADDONDIR/$VARIABLE3
		if [ ! -d $ADDONFOLDER ]; then
			exit 0
		fi
	else
		exit 0
	fi
	cd $ADDONFOLDER
	if [ "$VARIABLE5" != "" ]; then
		if [ "`find /home/$VARIABLE4 -mindepth 1 -maxdepth 3 -type d -name ${VARIABLE5} | wc -l`" == "1" ]; then
			GAMEDIR=`find /home/$VARIABLE4 -mindepth 1 -maxdepth 3 -type d -name "$VARIABLE5" | head -n 1`
		else
			GAMEDIR=`find /home/$VARIABLE4 -mindepth 1 -maxdepth 1 -type d -name "$VARIABLE5" | head -n 1`
		fi
	else
		GAMEDIR="/home/$VARIABLE4"
	fi
	del_addon_files&
	if [ -f $LOGDIR/addons.log ]; then
		echo "`date`: Removed $VARIABLE3 from the server $GAMEDIR for user $USER" >> $LOGDIR/addons.log
	fi
}

function fdl_update {
	SHORTEN=`echo $VARIABLE3 | awk -F "/" '{print $2}'`
	if [ "`echo $SHORTEN | grep '-'`" == "" ]; then
		SHORTEN=$SHORTEN
	else
		SHORTEN=`echo $SHORTEN | awk -F "-" '{print $1}'`
	fi
	if [ -f $HOMEFOLDER/conf/fdl-$SHORTEN.list ]; then
		if [ "$VARIABLE5" == "protected" ]; then
			SERVERDIR=/home/$VARIABLE2/pserver/$VARIABLE3
		else
			SERVERDIR=/home/$VARIABLE2/server/$VARIABLE3
		fi
		SPORT=`echo $VARIABLE3 | awk -F "/" '{print $1}'`
		if [ ! -d $HOMEFOLDER/conf ]; then
			mkdir -p $HOMEFOLDER/conf
			chmod 770 $HOMEFOLDER/conf
		fi
		cd $SERVERDIR
		if [ "`find -maxdepth 2 -name srcds_run`" != "" ]; then
			GAMETYPE="hl2"
			if [ "$VARIABLE5" == "left4dead2" ]; then
				GSMODFOLDER='left4dead2/left4dead2'
				SERVERDIR=`readlink -f `
			else
				GSMODFOLDER=`find -mindepth 1 -maxdepth 2 -type d -name "$VARIABLE5"`
				SERVERDIR=`readlink -f $GSMODFOLDER`
			fi
		elif [ "`find -maxdepth 1 -name hlds_run`" != "" ]; then
			GAMETYPE="hl1"
			GSMODFOLDER=`find -mindepth 1 -maxdepth 1 -type d -name "$VARIABLE5"`
			SERVERDIR=`readlink -f $GSMODFOLDER`
		elif [ "`find -maxdepth 2 -name cod4_lnxded`" != "" ]; then
			GAMETYPE="cod"
			GSMODFOLDER='.'
			SERVERDIR=`readlink -f $GSMODFOLDER`
		fi
		if [ -f $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh ]; then
			rm $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		fi
		PATTERN="\.log\|\.txt\|\.cfg\|\.vdf\|\.db\|\.dat\|\.ztmp\|\.blib\|log\/\|logs\/\|downloads\/\|DownloadLists\/\|metamod\/\|amxmodx\/\|hl\/\|hl2\/\|cfg\/\|addons\/\|bin\/\|classes/"
		echo "#!/bin/bash" > $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "rm $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "GAMETYPE=$GAMETYPE" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "HOMEFOLDER=$HOMEFOLDER" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "VARIABLE2=$VARIABLE2" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "VARIABLE3=$VARIABLE3" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "VARIABLE4=$VARIABLE4" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "VARIABLE5=$VARIABLE5" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "FTPUPLOADLIMIT=$FTPUPLOADLIMIT" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "SHORTEN=$SHORTEN" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "DATADIR=$DATADIR" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "LOGDIR=$LOGDIR" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "cd $SERVERDIR" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		if [ "$GAMETYPE" == "hl1" ]; then
			SEARCHFOLDERS="."
		elif [ "$GAMETYPE" == "hl2" ]; then
			if [ ! -d $HOMEFOLDER/fdl_data/$GAMETYPE ]; then
				mkdir -p $HOMEFOLDER/fdl_data/$GAMETYPE
				find $HOMEFOLDER/fdl_data/$GAMETYPE -maxdepth 1 -type d -user `whoami` -exec chmod 770 {} \;
			fi
			SEARCHFOLDERS="particles/ maps/ materials/ resource/ models/ sound/"
		elif [ "$GAMETYPE" == "cod" ]; then
			SEARCHFOLDERS="usermaps/ mods/"
		fi
		echo "SEARCHFOLDERS='$SEARCHFOLDERS'" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		echo "PATTERN=$PATTERN" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		if [ "$GAMETYPE" == "hl2" ]; then
			echo 'find $SEARCHFOLDERS -type f 2> /dev/null | grep -v "$PATTERN" | while read FILTEREDFILE1; do' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '	FILTEREDFILES=${FILTEREDFILE1//\.\//}' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '	FILENAME=`basename $FILTEREDFILES`' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '	if [[ ! `grep "$FILTEREDFILES" $HOMEFOLDER/conf/fdl-$SHORTEN.list` ]]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		FDLDATADIR=$DATADIR/$GAMETYPE/$SHORTEN/`dirname "$FILTEREDFILES"`' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		if [ ! -d $FDLDATADIR ]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			mkdir -p $FDLDATADIR' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			chmod 770 $FDLDATADIR' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo "		cd $SERVERDIR" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		if [ -f "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat" ]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			if [ "`head -n 1 \"$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat\"`" != "`'"${IONICE}"'nice -n +19 md5sum \"$FILTEREDFILES\" | awk '"'"'{print $1}'"'"'`" ]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				'"${IONICE}"'nice -n +19 md5sum "$FILTEREDFILES" | awk '"'"'{print $1}'"'"' > "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				if [ -f "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2" ]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '					'"${IONICE}"'nice -n +19 rm "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				'"${IONICE}"'nice -n +19 bzip2 -k -s -q -9 "$FILTEREDFILES"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				'"${IONICE}"'nice -n +19 mv "$FILTEREDFILES.bz2" "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				chmod 660 "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2" "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				cd $DATADIR/$GAMETYPE/$SHORTEN' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				wput -q --reupload --limit-rate=$FTPUPLOADLIMIT "$FILTEREDFILES.bz2" "$VARIABLE4/$SHORTEN/"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				echo "`date`:  $VARIABLE2:Updated $VARIABLE5 file `basename $FILTEREDFILES`" >> $LOGDIR/fdl-hl2.log' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			else' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				cd $DATADIR/$GAMETYPE/$SHORTEN' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				wput -q --dont-continue --limit-rate=$FTPUPLOADLIMIT "$FILTEREDFILES.bz2" "$VARIABLE4/$SHORTEN/"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				echo "`date`: $VARIABLE2: $VARIABLE5 file $FILENAME checked" >> $LOGDIR/fdl-hl2.log' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		else' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			'"${IONICE}"'nice -n +19 md5sum "$FILTEREDFILES" | awk '"'"'{print $1}'"'"' > "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			'"${IONICE}"'nice -n +19 bzip2 -k -s -q -9 "$FILTEREDFILES"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			'"${IONICE}"'nice -n +19 mv "$FILTEREDFILES.bz2" "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			chmod 660 "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2" "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			cd $DATADIR/$GAMETYPE/$SHORTEN' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			wput -q --dont-continue --limit-rate=$FTPUPLOADLIMIT "$FILTEREDFILES.bz2" "$VARIABLE4/$SHORTEN/"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			echo "`date`: $VARIABLE2: Added $SHORTEN file `basename $FILTEREDFILES`" >> $LOGDIR/fdl-hl2.log' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '	fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo 'done' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo "cd $SERVERDIR" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo 'find $SEARCHFOLDERS -type l 2> /dev/null | grep -v "$PATTERN" | while read FILTEREDFILE1; do' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '	FILTEREDFILES=${FILTEREDFILE1//\.\//}' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '	FILENAME=`basename $FILTEREDFILES`' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '	if [[ ! `grep "$FILTEREDFILES" $HOMEFOLDER/conf/fdl-$SHORTEN.list` ]]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		FDLDATADIR=$DATADIR/$GAMETYPE/$SHORTEN/`dirname "$FILTEREDFILES"`' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		if [ ! -d $FDLDATADIR ]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			mkdir -p $FDLDATADIR' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			chmod 770 $FDLDATADIR' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo "		cd $SERVERDIR" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		if [ -f "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat" ]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			if [ "`head -n 1 \"$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat\"`" != "`'"${IONICE}"'nice -n +19 md5sum \"$FILTEREDFILES\" | awk '"'"'{print $1}'"'"'`" ]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				'"${IONICE}"'nice -n +19 md5sum "$FILTEREDFILES" | awk '"'"'{print $1}'"'"' > "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				'"${IONICE}"'nice -n +19 rm "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				if [ -f "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2" ]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '					'"${IONICE}"'nice -n +19 rm "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				'"${IONICE}"'nice -n +19 cp "$FILTEREDFILES" "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				'"${IONICE}"'nice -n +19 bzip2 -s -q -9 "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				chmod 660 "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2" "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				cd $DATADIR/$GAMETYPE/$SHORTEN' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				wput -q --reupload --limit-rate=$FTPUPLOADLIMIT "$FILTEREDFILES.bz2" "$VARIABLE4/$SHORTEN/"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				echo "`date`: Updated $VARIABLE5 file `basename $FILTEREDFILES`" >> $LOGDIR/fdl-hl2.log' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			else' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				cd $DATADIR/$GAMETYPE/$SHORTEN' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				wput -q --dont-continue --limit-rate=$FTPUPLOADLIMIT "$FILTEREDFILES.bz2" "$VARIABLE4/$SHORTEN/"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '				echo "`date`: $VARIABLE2: $VARIABLE5 file $FILENAME checked" >> $LOGDIR/fdl-hl2.log' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		else' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			'"${IONICE}"'nice -n +19 md5sum "$FILTEREDFILES" | awk '"'"'{print $1}'"'"' > "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			'"${IONICE}"'nice -n +19 cp "$FILTEREDFILES" "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			rm "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			'"${IONICE}"'nice -n +19 bzip2 -s -q -9 "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			chmod 660 "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.bz2" "$DATADIR/$GAMETYPE/$SHORTEN/$FILTEREDFILES.stat"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			cd $DATADIR/$GAMETYPE/$SHORTEN' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			wput -q --dont-continue --limit-rate=$FTPUPLOADLIMIT "$FILTEREDFILES.bz2" "$VARIABLE4/$SHORTEN/"' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '			echo "`date`: $VARIABLE2: Added $SHORTEN file `basename $FILTEREDFILES`" >> $LOGDIR/fdl-hl2.log' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '		fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo '	fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo 'done' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo "find $HOMEFOLDER/fdl_data/$GAMETYPE -type d -user `id -nu` -exec chmod 770 {} \;" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo "find $HOMEFOLDER/fdl_data/$GAMETYPE -type f -user `id -nu` -exec chmod 660 {} \;" >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		elif [ "$GAMETYPE" == "hl1" ]; then
			echo 'find $SEARCHFOLDERS -type l -or -type f 2> /dev/null | grep -v "$PATTERN" | while read FILTEREDFILE1; do' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
				echo '	FILTEREDFILES=${FILTEREDFILE1//\.\//}' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
				echo '	if [[ ! `grep "$FILTEREDFILES" $HOMEFOLDER/conf/fdl-$SHORTEN.list` ]]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo 'FILENAME=`basename $FILTEREDFILES`' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo 'if [ "`wput -q -nv --limit-rate=$FTPUPLOADLIMIT "$FILTEREDFILES" $VARIABLE4/$SHORTEN/ | grep \"Skipping file\"`" != "" ]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo '	wput -qN --limit-rate=$FTPUPLOADLIMIT "$FILTEREDFILES" $VARIABLE4/$SHORTEN/' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo '	echo "`date`: $VARIABLE2: $VARIABLE5 file $FILENAME checked" >> $LOGDIR/fdl-hl1.log' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo 'else' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo '	echo "`date`: $VARIABLE2: $VARIABLE5 file $FILENAME uploaded" >> $LOGDIR/fdl-hl1.log' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo 'fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
				echo 'fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo 'done' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		elif [ "$GAMETYPE" == "cod" ]; then
			echo 'find $SEARCHFOLDERS -type l -or -type f \( -iname "*.ff" -or -iname "*.iwd" \) 2> /dev/null | grep -v "$PATTERN" | while read FILTEREDFILE1; do' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
				echo '	FILTEREDFILES=${FILTEREDFILE1//\.\//}' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
				echo '	if [[ ! `grep "$FILTEREDFILES" $HOMEFOLDER/conf/fdl-$SHORTEN.list` ]]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo 'FILENAME=`basename $FILTEREDFILES`' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo 'if [ "`wput -q -nv --limit-rate=$FTPUPLOADLIMIT "$FILTEREDFILES" $VARIABLE4/$SHORTEN/ | grep \"Skipping file\"`" != "" ]; then' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo '	wput -qN --limit-rate=$FTPUPLOADLIMIT "$FILTEREDFILES" $VARIABLE4/$SHORTEN/' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo '	echo "`date`: $VARIABLE2: $VARIABLE5 file $FILENAME checked" >> $LOGDIR/fdl-hl1.log' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo 'else' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo '	echo "`date`: $VARIABLE2: $VARIABLE5 file $FILENAME uploaded" >> $LOGDIR/fdl-hl1.log' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
					echo 'fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
				echo 'fi' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
			echo 'done' >> $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		fi
		screen -wipe > /dev/null 2>&1
		if [[ `ps fx| grep "fdl-$VARIABLE2-$SPORT-$SHORTEN" | grep -v grep` ]]; then
			rm -f $HOMEFOLDER/temp/$VARIABLE2-$SPORT-$SHORTEN.sh
		else
			cd $HOMEFOLDER/temp/
			chmod +x $VARIABLE2-$SPORT-$SHORTEN.sh
			screen -dmS fdl-$VARIABLE2-$SPORT-$SHORTEN ./$VARIABLE2-$SPORT-$SHORTEN.sh
			find $LOGDIR/ -maxdepth 1 -type f -user `whoami` -exec chmod 660 {} \;
		fi
	fi
}

function update_status {
UPDATESTATUS=":"
for GAME in ${VARIABLE2[@]}; do
	if [[ `ps x | grep "$GAME.update" | grep -v 'grep'` ]]; then
		UPDATESTATUS="$UPDATESTATUS$GAME=1:"
	else
		UPDATESTATUS="$UPDATESTATUS$GAME=0:"
	fi
done
if [[ `ps x | grep "SteamCmdUpdate-Screen" | grep -v 'grep'` ]]; then
	UPDATESTATUS=$UPDATESTATUS"steamcmd=1:"
else
	UPDATESTATUS=$UPDATESTATUS"steamcmd=0:"
fi
if [[ `ps x | grep "sync-server" | grep -v 'grep'` ]]; then
	UPDATESTATUS=$UPDATESTATUS"sync=1:"
else
	UPDATESTATUS=$UPDATESTATUS"sync=0:"
fi
echo $UPDATESTATUS
}

case "$1" in
	steamCmd)
		rsyncExists
		steamCmdUpdate
		wget_remove &
	;;
	noSteamCmd)
		rsyncExists
		noSteamCmdUpdate
		wget_remove &
	;;
	hldsCmd)
		rsyncExists
		noSteamCmdUpdate
		wget_remove &
	;;
	mcUpdate)
		rsyncExists
		noSteamCmdUpdate
		wget_remove &
	;;
	delete)
		server_delete
	;;
	grestart)
		server_stop
		server_start&
	;;
	gstop)
		server_stop&
	;;
	addonmatch)
		match_addons&
	;;
	demoupload)
		demo_upload
	;;
	add)
		add_customer
		wget_remove &
	;;
	delscreen)
		del_customer_screen
	;;
	delCustomer)
		customerDelete
		wget_remove &
	;;
	mod)
		mod_customer
		wget_remove &
	;;
	addserver)
		add_customer_server
	;;
	delserver)
		del_customer_server
	;;
	reinstserver)
		reinst_customer_server
	;;
	migrateserver)
		migration
	;;
	syncaddons)
		rsyncExists
		sync_addons
	;;
	syncserver)
		rsyncExists
		sync_server
	;;
	addaddon)
		add_addon
	;;
	deladdon)
		del_addon
	;;
	fastdl)
		FTPUPLOADLIMIT="1024K"
		fdl_update
	;;
	stopall)
		crontab -r
		screen -wipe > /dev/null 2>&1
		pkill -u `whoami`
	;;
	install)
		install_control
	;;
	move)
		port_move
	;;
	mc_ws)
		mc_worldsafe
	;;
	backup)
		FTPUPLOADLIMIT="5096K"
		backup_servers
	;;
	restore)
		FTPUPLOADLIMIT="5096K"
		restore_backup
	;;
	updatestatus)
		update_status
		wget_remove &
	;;
	update)
		ISROOT=1
		updatecheck
	;;
	generateKey)
		publicKeyGenerate
	;;
	*)
		echo "Current version: $CVERSION"
		wget_remove &
	;;
esac
exit 0
