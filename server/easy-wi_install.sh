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

OS=''
USERADD=`which useradd`
USERMOD=`which usermod`
USERDEL=`which userdel`
GROUPADD=`which groupadd`

# We need to be root to install and update
if [ "`id -u`" != "0" ]; then
        echo "Change to root account required"
        su -
fi

if [ "`id -u`" != "0" ]; then
        echo "Still not root, aborting"
        exit 0
fi

# Debian and its derivatives store their version at /etc/debian_version
if [ -f /etc/debian_version ]; then

        DISTRIBUTORID=`lsb_release -a 2> /dev/null | grep 'Distributor' | awk '{print $3}'`

        if [ "$DISTRIBUTORID" == "Ubuntu" ]; then
			OSBRANCH=`lsb_release -a 2> /dev/null | grep 'Codename' | awk '{print $2}'`
            OS='ubuntu'
        else
			OSBRANCH=`cat /etc/*release | grep 'VERSION=' | awk '{print $2}' | tr -d '()"'`
            OS='debian'
        fi
fi

if [ "$OS" == "" ]; then
        echo "Error: Could not detect OS. Aborting"
        exit 0
else
        echo "Detected OS $OS"
fi

if [ "$OSBRANCH" == "" ]; then
        echo "Error: Could not detect branch of OS. Aborting"
        exit 0
else
        echo "Detected branch $OSBRANCH"
fi

# Start with the install process by asking what to do
echo " "
echo "What shall be installed/prepared?"

OPTIONS=("Easy-Wi Webpanel" "Gameserver Root" "Voicemaster" "Webspace Root" "Quit")
select OPTION in "${OPTIONS[@]}"; do
    case "$REPLY" in
        1 ) break;;
        2 ) break;;
        3 ) break;;
        4 ) break;;
        5 ) echo "Exit now!"; exit 0;;
        *) echo "Invalid option.";continue;;
    esac
done

if [ "$OPTION" == "Easy-Wi Webpanel" ]; then
	INSTALL='EW'
elif [ "$OPTION" == "Gameserver Root" ]; then
	INSTALL='GS'
elif [ "$OPTION" == "Voicemaster" ]; then
	INSTALL='VS'
elif [ "$OPTION" == "Webspace Root" ]; then
	INSTALL='WR'
fi

echo " "
echo "Will start installing $OPTION by updating the system packages to the latest version"
if [ "$OS" == "debian" -o  "$OS" == "ubuntu" ]; then
       apt-get update && apt-get upgrade && apt-get dist-upgrade
fi

# If we need to install and configure a webspace than we need to identify the groupID
if [ "$INSTALL" == 'EW' -o  "$INSTALL" == 'WR' ]; then

	WEBGROUPID=`id -g www-data 2> /dev/null`

	echo " "
	echo "Found group www-data with group ID $WEBGROUPID. Use as webservergroup?"

	OPTIONS=("Yes" "No" "Quit")
	select OPTION in "${OPTIONS[@]}"; do
		case "$REPLY" in
			1 ) break;;
			2 ) break;;
			3 ) echo "Exit now!"; exit 0;;
			*) echo "Invalid option.";continue;;
		esac
	done

	if [ "$OPTION" == "No" ]; then

		echo "Please name the group you want to use as webservergroup"
		read WEBGROUP

		WEBGROUPID=`id -g $WEBGROUP 2> /dev/null`
		if [ "$WEBGROUPID" == "" ]; then
			$GROUPADD $WEBGROUP
			WEBGROUPID=`id -g $WEBGROUP 2> /dev/null`
		fi
	fi

	if [ "$WEBGROUPID" == "" ]; then
		echo "Fatal Error: missing webservergroup ID"
		exit 0
	fi
fi

if [ "$INSTALL" != 'EW' ]; then

	echo "Please enter the name of the masteruser. If it does not exists, the installer will create it."
	read MASTERUSER

	if [ "$MASTERUSER" == "" ]; then
		echo "Fatal Error: No masteruser specified"
		exit 0
	fi


	if [ "`id $MASTERUSER 2> /dev/null`" == "" ]; then

		if [ "$INSTALL" == 'EW' -o  "$INSTALL" == 'WR' ]; then
			$USERADD -m -b /home -s /bin/bash -g $WEBGROUPID $MASTERUSER
		else
			if [ -d /home/$MASTERUSER ]; then
				$GROUPADD $MASTERUSER
				$USERADD -d /home/$MASTERUSER -s /bin/bash -g $MASTERUSER $MASTERUSER
			else
				$GROUPADD $MASTERUSER
				$USERADD -m -b /home -s /bin/bash -g $MASTERUSER $MASTERUSER
			fi
		fi

	elif [ "$INSTALL" == 'GS' ]; then

		echo "User \"$MASTERUSER\" found setting group \"$MASTERUSER\" as mastegroup"
		usermod -g $MASTERUSER $MASTERUSER
	fi

	echo " "
	echo "Create key or set password for login?"
	echo "Safest way of login is a password protected key."

	OPTIONS=("Create key" "Set password" "Skip" "Quit")
	select OPTION in "${OPTIONS[@]}"; do
		case "$REPLY" in
			1 ) break;;
			2 ) break;;
			3 ) break;;
			4 ) echo "Exit now!"; exit 0;;
			*) echo "Invalid option.";continue;;
		esac
	done

	if [ "$OPTION" == "Create key" ]; then

		if [ -d /home/$MASTERUSER/.ssh ]; then
			rm -r /home/$MASTERUSER/.ssh
		fi

		echo " "
		echo "It is recommended but not required to set a password"
		su -c 'ssh-keygen -t rsa' $MASTERUSER

		cd /home/$MASTERUSER/.ssh

		KEYNAME=`find -maxdepth 1 -name "*.pub" | head -n 1`

		if [ "$KEYNAME" != "" ]; then
			su -c "cat $KEYNAME >> authorized_keys" $MASTERUSER
		else
			echo "Error: could not find a key. You might need to create one manually at a later point."
		fi

	elif [ "$OPTION" == "Set password" ]; then
		passwd $MASTERUSER
	fi
fi

# only in case we want to manage webspace we need the additional skel dir
if [ "$INSTALL" == 'WR' ]; then
	mkdir -p /home/$MASTERUSER/skel/logs
	mkdir -p /home/$MASTERUSER/skel/htdocs
	mkdir -p /home/$MASTERUSER/sites-enabled/
fi

if [ "$INSTALL" == 'EW' -o  "$INSTALL" == 'WR' ]; then
	if [ "$OS" == "debian" ]; then

			echo " "
			echo "Use dotdeb.org repository for more up to date server and PHP versions?"

			OPTIONS=("Yes" "No" "Quit")
			select DOTDEB in "${OPTIONS[@]}"; do
					case "$REPLY" in
							1 ) break;;
							2 ) break;;
							3 ) echo "Exit now!"; exit 0;;
							*) echo "Invalid option.";continue;;
					esac
			done

			if [ "$DOTDEB" == "Yes" ]; then
					if [ "`grep 'packages.dotdeb.org' /etc/apt/sources.list`" == "" ]; then
							echo "Adding entries to /etc/apt/sources.list"
							add-apt-repository "deb http://packages.dotdeb.org $OSBRANCH all"
							add-apt-repository "deb-src http://packages.dotdeb.org $OSBRANCH all"
							wget http://www.dotdeb.org/dotdeb.gpg
							apt-key add dotdeb.gpg
							rm dotdeb.gpg
							apt-get update
					fi
			fi
	fi

	echo " "
	echo "Please select the webserver you would like to use"
	echo "Nginx is recommended for FastDL and few but high efficient vhosts"
	echo "Apache is recommended in case you want to run many PHP supporting Vhosts aka mass web hosting"

	OPTIONS=("Nginx" "Apache" "Lighttpd" "None" "Quit")
	select WEBSERVER in "${OPTIONS[@]}"; do
		case "$REPLY" in
			1 ) break;;
			2 ) break;;
			3 ) break;;
			4 ) break;;
			5 ) echo "Exit now!"; exit 0;;
			*) echo "Invalid option.";continue;;
		esac
	done

	if [ "$WEBSERVER" == "Nginx" ]; then
		apt-get install nginx-full
	elif [ "$WEBSERVER" == "Lighttpd" ]; then
		apt-get install lighttpd
	elif [ "$WEBSERVER" == "Apache" ]; then
		apt-get install apache2
	fi

	echo " "
	echo "Please select if an which database server to install."
	echo "Select \"None\" in case this server should host only Fastdownload webspace."
	if [ "$INSTALL" == 'EW' ]; then
		echo "Please note that Easy-Wi requires a MySQL or MariaDB installed."
	fi

	OPTIONS=("MySQL" "MariaDB 5.5" "MariaDB 10.0" "None" "Quit")
	select SQL in "${OPTIONS[@]}"; do
		case "$REPLY" in
			1 ) break;;
			2 ) break;;
			3 ) break;;
			4 ) break;;
			5 ) echo "Exit now!"; exit 0;;
			*) echo "Invalid option.";continue;;
		esac
	done

	if [ "$SQL" == "MySQL" ]; then

		apt-get install mysql-server mysql-client mysql-common

	elif [ "$SQL" == "MariaDB 5.5" -o "$SQL" == "MariaDB 10.0" ]; then

		if [ "`grep '/mariadb/' /etc/apt/sources.list`" == "" ]; then

			apt-get install python-software-properties
			apt-key adv --recv-keys --keyserver keyserver.ubuntu.com 0xcbcb082a1bb943db

			if [ "$SQL" == "MariaDB 5.5" -a "`apt-cache search mariadb-server-5.5`" == "" ]; then

				if [ "$OS" == "debian" ]; then
					add-apt-repository "deb http://mirror.netcologne.de/mariadb/repo/5.5/debian $OSBRANCH main"
				elif [ "$OS" == "ubuntu" ]; then
					add-apt-repository "deb http://mirror.netcologne.de/mariadb/repo/5.5/ubuntu $OSBRANCH main"
				fi

			elif [ "$SQL" == "MariaDB 10.0" -a "`apt-cache search mariadb-server-10.0`" == "" ]; then

				if [ "$OS" == "debian" ]; then
					add-apt-repository "deb http://mirror.netcologne.de/mariadb/repo/10.0/debian $OSBRANCH main"
				elif [ "$OS" == "ubuntu" ]; then
					add-apt-repository "deb http://mirror.netcologne.de/mariadb/repo/10.0/ubuntu $OSBRANCH main"
				fi
			fi
		fi

		if [ "$OS" == "debian" -a "$DOTDEB" == "Yes" ]; then
			echo "Package: *" > /etc/apt/preferences.d/mariadb.pref
			echo "Pin: origin mirror.netcologne.de" >> /etc/apt/preferences.d/mariadb.pref
			echo "Pin-Priority: 1000" >> /etc/apt/preferences.d/mariadb.pref
		fi

		apt-get update
		apt-get install mariadb-server mariadb-client mysql-common
	fi

	if [ "$INSTALL" == 'EW' -a "`ps x | grep mysql | grep -v grep`" == "" ]; then
		echo " "
		echo "Error: No SQL server running but required for Webpanel installation."
		exit 0
	fi

	echo " "
	echo "Install/Update PHP?"
	echo "Select \"None\" in case this server should host only Fastdownload webspace."
	if [ "$INSTALL" == 'EW' ]; then
		echo "Please note that Easy-Wi requires PHP installed."
	fi

	OPTIONS=("Yes" "No" "None" "Quit")
	select PHPINSTALL in "${OPTIONS[@]}"; do
		case "$REPLY" in
			1 ) break;;
			2 ) break;;
			3 ) break;;
			4 ) echo "Exit now!"; exit 0;;
			*) echo "Invalid option.";continue;;
		esac
	done

	if [ "$PHPINSTALL" == "Yes" ]; then

		if [ "$OSBRANCH" == "wheezy" -o "$OSBRANCH" == "precise" -o  "$OSBRANCH" == "saucy" ]; then

			echo " "
			echo "Install/Update Facebook HHVM?"
			OPTIONS=("Yes" "No" "Quit")
			select HHVM in "${OPTIONS[@]}"; do
				case "$REPLY" in
					1 ) break;;
					2 ) break;;
					3 ) echo "Exit now!"; exit 0;;
					*) echo "Invalid option.";continue;;
				esac
			done

			if [ "$HHVM" == "Yes" ]; then

				if [ "`grep 'dl.hhvm.com' /etc/apt/sources.list`" == "" ]; then

					wget -O - http://dl.hhvm.com/conf/hhvm.gpg.key | apt-key add -

					if [ "$OSBRANCH" == "wheezy" ]; then
						add-apt-repository "deb http://dl.hhvm.com/debian wheezy main"
					else
						add-apt-repository "deb http://dl.hhvm.com/ubuntu $OSBRANCH main"
					fi
				fi

				apt-get update
				apt-get install hhvm
			fi
		fi

		if [ "$HHVM" != "Yes" -a "$OS" == "debian" -a "$DOTDEB" == "Yes" ]; then

			echo " "

			if [ "$OSBRANCH" == "wheezy" ]; then
				echo "Install/Update to Dotdeb PHP 5.5?"
			else
				echo "Install/Update Dotdeb PHP 5.4?"
			fi

			OPTIONS=("Yes" "No" "Quit")
			select DOTDEBPHPUPGRADE in "${OPTIONS[@]}"; do
				case "$REPLY" in
					1 ) break;;
					2 ) break;;
					3 ) echo "Exit now!"; exit 0;;
					*) echo "Invalid option.";continue;;
				esac
			done
			
			if [ "$DOTDEBPHPUPGRADE" == "Yes" -a "$OSBRANCH" == "wheezy" -a "`grep 'wheezy-php55' /etc/apt/sources.list`" == "" ]; then
				add-apt-repository "deb http://packages.dotdeb.org wheezy-php55 all"
				add-apt-repository "deb-src http://packages.dotdeb.org wheezy-php55 all"
			elif [ "$DOTDEBPHPUPGRADE" == "Yes" -a "`grep 'squeeze-php54' /etc/apt/sources.list`" == "" ]; then
				add-apt-repository "deb http://packages.dotdeb.org squeeze-php54 all"
				add-apt-repository "deb-src http://packages.dotdeb.org squeeze-php54 all"
			fi

			if [ "$DOTDEBPHPUPGRADE" == "Yes" ]; then
				apt-get update
				apt-get upgrade
			fi
		fi

		if [ "$HHVM" != "Yes" ]; then

			apt-get install php5 php5-common php5-curl php5-gd php5-mcrypt php5-mysql php5-cli

			if [ "$WEBSERVER" == "Nginx" -o "$WEBSERVER" == "Lighttpd" ]; then

				apt-get install php5-fpm

				if [ "$WEBSERVER" == "Lighttpd" ]; then
					lighttpd-enable-mod fastcgi
					lighttpd-enable-mod fastcgi-php
				fi

			elif [ "$WEBSERVER" == "Apache" ]; then
				apt-get install apache2-mpm-itk
			fi
		fi
	fi

	if [ "$INSTALL" == 'EW' -a "$PHPINSTALL" != "Yes" ]; then
		echo " "
		echo "Error: No SQL server running but required for Webpanel installation."
		exit 0
	fi
fi

if [ "$INSTALL" != 'VS' -a "$INSTALL" != 'EW' ]; then
	echo " "
	echo "Install/Update ProFTPD?"

	OPTIONS=("Yes" "No" "Quit")
	select OPTION in "${OPTIONS[@]}"; do
		case "$REPLY" in
			1 ) break;;
			2 ) break;;
			3 ) echo "Exit now!"; exit 0;;
			*) echo "Invalid option.";continue;;
		esac
	done

	if [ "$OPTION" == "Yes" ]; then

		apt-get install proftpd
		
		if [ -f /etc/proftpd/modules.conf ]; then
			mv /etc/proftpd/modules.conf /etc/proftpd/modules.conf.backup
			sed 's/.*LoadModule mod_tls_memcache.c.*/#LoadModule mod_tls_memcache.c/g' /etc/proftpd/modules.conf.backup > /etc/proftpd/modules.conf
		fi

		if [ -f /etc/proftpd/proftpd.conf -a "$INSTALL" != 'GS' ]; then
			mv /etc/proftpd/proftpd.conf /etc/proftpd/proftpd.conf.backup
			sed 's/.*UseIPv6.*/UseIPv6 off/g' /etc/proftpd/proftpd.conf.backup | sed 's/Umask.*/Umask 037 027/g' | sed 's/.*DefaultRoot.*/DefaultRoot ~/g' | sed 's/# RequireValidShell.*/RequireValidShell off/g' > /etc/proftpd/proftpd.conf
		elif [ -f /etc/proftpd/proftpd.conf -a "$INSTALL" == 'GS' ]; then
		fi

		/etc/init.d/proftpd restart

	fi
fi

if [ "$INSTALL" == 'GS' -o "$INSTALL" == 'WR' ]; then

	echo " "
	echo "Install Quota?"

	OPTIONS=("Yes" "No" "Quit")
	select QUOTAINSTALL in "${OPTIONS[@]}"; do
		case "$REPLY" in
			1 ) break;;
			2 ) break;;
			3 ) echo "Exit now!"; exit 0;;
			*) echo "Invalid option.";continue;;
		esac
	done

	if [ "$QUOTAINSTALL" == "Yes" ]; then
		apt-get install quota

		echo " "
		echo " "
		if [ -f /root/tempfstab ]; then
			rm /root/tempfstab
		fi
		if [ -f /root/tempmountpoints ]; then
			rm /root/tempmountpoints
		fi

		cat /etc/fstab | while read LINE; do
			if [[ `echo $LINE | grep '/' | egrep -v '#|boot|proc|swap|floppy|cdrom|usrquota|/sys|/shm|/pts'` ]]; then
				CURRENTOPTIONS=`echo $LINE | awk '{print $4}'`
				echo $LINE | sed "s/$CURRENTOPTIONS/$CURRENTOPTIONS,usrquota/g" >> /root/tempfstab
				echo $LINE | awk '{print $2}' >> /root/tempmountpoints
			else
				echo $LINE >> /root/tempfstab
			fi
		done

		cat /root/tempfstab

		echo " "
		echo " "
		echo "Please check above output and confirm it is correct. On confirmation the current /etc/fstab will be replaced in order to activate Quotas!"

		select QUOTAFSTAB in "${OPTIONS[@]}"; do
			case "$REPLY" in
				1 ) break;;
				2 ) break;;
				3 ) echo "Exit now!"; exit 0;;
				*) echo "Invalid option.";continue;;
			esac
		done

		if [ "$QUOTAFSTAB" == "Yes" ]; then
			mv /root/fstab /etc/fstab.backup
			mv /root/tempfstab /etc/fstab

			cat /root/tempmountpoints | while read LINE; do
				mount -o remount $LINE
			done

			if [[ `quotaon -p -a 2> /dev/null` ]]; then
				 quotaoff -a
			fi

			quotacheck -avugmc

			if [[ ! `quotaon -p -a 2> /dev/null` ]]; then
				quotaon -a
			fi
		fi

		if [ -f /root/tempfstab ]; then
			rm /root/tempfstab
		fi
		if [ -f /root/tempmountpoints ]; then
			rm /root/tempmountpoints
		fi
	fi
fi

if [ "$INSTALL" == 'WR' ]; then
	if [ "$WEBSERVER" == "Nginx" ]; then

		if [ -f /etc/nginx/sites-available/default ]; then
			mv /etc/nginx/sites-available/default /home/$MASTERUSER/sites-enabled/
		fi

		mv /etc/nginx/nginx.conf /etc/nginx/nginx.conf.backup
		sed "s/\/etc\/nginx\/sites-enabled\/\*;/\/home\/$MASTERUSER\/sites-enabled\/\*;/g" /etc/nginx/nginx.conf.backup > /etc/nginx/nginx.conf

	elif [ "$WEBSERVER" == "Lighttpd" ]; then

		if [ -f /etc/lighttpd/sites-available/default ]; then
			mv /etc/lighttpd/sites-available/default /home/$MASTERUSER/sites-enabled/
		fi

		cp /etc/lighttpd/lighttpd.conf /etc/lighttpd/lighttpd.conf.backup
		echo "include_shell \"find /home/$MASTERUSER/sites-enabled/ -maxdepth 1 -type f -exec cat {} \;\"" >> /etc/lighttpd/lighttpd.conf

	elif [ "$WEBSERVER" == "Apache" ]; then

		if [ -f /etc/apache2/sites-available/default ]; then
			mv /etc/apache2/sites-available/default /home/$MASTERUSER/sites-enabled/
		fi

		if [ -f /etc/apache2/sites-available/default-ssl ]; then
			mv /etc/apache2/sites-available/default-ssl /home/$MASTERUSER/sites-enabled/
		fi

		mv /etc/apache2/apache2.conf /etc/apache2/apache2.conf.backup
		sed "s/Include sites-enabled\//Include \/home\/$MASTERUSER\/sites-enabled\//g" /etc/apache2/apache2.conf.backup | sed "s/Include \/etc\/apache2\/sites-enabled\//\/home\/$MASTERUSER\/sites-enabled\//g" > /etc/apache2/apache2.conf

	fi
fi

# No direct root access for masteruser. Only limited access through sudo
if [ "$INSTALL" == 'GS' -o  "$INSTALL" == 'WR' ]; then
	echo " "
	echo "Installing sudo"
	apt-get install sudo

	if [ -f /etc/sudoers -a "`grep $MASTERUSER /etc/sudoers`" == "" ]; then

		echo "$MASTERUSER ALL = NOPASSWD: $USERADD" >> /etc/sudoers
		echo "$MASTERUSER ALL = NOPASSWD: $USERMOD" >> /etc/sudoers
		echo "$MASTERUSER ALL = NOPASSWD: $USERDEL" >> /etc/sudoers

		if [ "$QUOTAINSTALL" == "Yes" ]; then
			echo "$MASTERUSER ALL = NOPASSWD: `which setquota`" >> /etc/sudoers
			echo "$MASTERUSER ALL = NOPASSWD: `which repquota`" >> /etc/sudoers
		fi

		if [ "$WEBSERVER" == "Nginx" ]; then
			HTTPDBIN=`which nginx`
			HTTPDSCRIPT='/etc/init.d/nginx'
		elif [ "$WEBSERVER" == "Lighttpd" ]; then
			HTTPDBIN=`which lighttpd`
			HTTPDSCRIPT='/etc/init.d/lighttpd'
		elif [ "$WEBSERVER" == "Apache" ]; then
			HTTPDBIN=`which apache2`
			HTTPDSCRIPT='/etc/init.d/apache2'
		fi

		if [ "$HTTPDBIN" != "" ]; then
			echo "$MASTERUSER ALL = NOPASSWD: $HTTPDBIN" >> /etc/sudoers
			echo "$MASTERUSER ALL = NOPASSWD: $HTTPDSCRIPT" >> /etc/sudoers
		fi
	fi
fi

if [ "$INSTALL" == 'WR' ]; then

	chown -R $MASTERUSER:$WEBGROUPID /home/$MASTERUSER/

	echo "Following data need to be configured at the easy-wi.com panel:"

	echo "The path to the folder \"sites-enabled\" is:"
	echo "/home/$MASTERUSER/sites-enabled/"

	echo "The useradd command is:"
	echo "sudo $USERADD %cmd%"

	echo "The usermod command is:"
	echo "sudo $USERMOD %cmd%"

	echo "The userdel command is:"
	echo "sudo $USERDEL %cmd%"

	echo "The setquota command is:"
	echo "sudo `which setquota` %cmd%"

	echo "The HTTPD restart command is:"
	echo "sudo $HTTPDSCRIPT reload"
fi
