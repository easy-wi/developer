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

if [ "`id -u`" != "0" ]; then
        echo "Change to root account required"
        su -
fi

if [ "`id -u`" != "0" ]; then
        echo "Still not root, aborting"
        exit 0
fi

if [ -f /etc/debian_version ]; then

        DISTRIBUTORID=`lsb_release -a 2> /dev/null | grep 'Distributor' | awk '{print $3}'`

        if [ "$DISTRIBUTORID" == "Ubuntu" ]; then
                OS='ubuntu'
        else
                OS='debian'
        fi
fi

if [ "$OS" == "" ]; then
        echo "Error: Could not detect OS. Aborting"
        exit 0
else
        echo "Detected OS $OS"
        echo "First we will ensure the system is up to date and run the updater"
fi

if [ "$OS" == "debian" -o  "$OS" == "ubuntu" ]; then
       apt-get update && apt-get upgrade && apt-get dist-upgrade
fi

echo " "
echo "Installing sudo"
apt-get install sudo

if [ "$OS" == "debian" ]; then

        echo " "
        echo "Use dotdeb.org repository for newer server versions?"

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
                if [[ ! `grep 'packages.dotdeb.org' /etc/apt/sources.list` ]]; then
                        echo "Adding entries to /etc/apt/sources.list"
                        echo "deb http://packages.dotdeb.org wheezy all" >> /etc/apt/sources.list
                        echo "deb-src http://packages.dotdeb.org wheezy all" >> /etc/apt/sources.list
                        wget http://www.dotdeb.org/dotdeb.gpg
                        apt-key add dotdeb.gpg
                        rm dotdeb.gpg
                        apt-get update
                fi
        fi
fi

echo " "
echo "Please select the webserver you would like to use"

OPTIONS=("Apache" "Nginx" "Lighttpd" "None" "Quit")
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
echo "Install ProFTPD?"

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

	if [ -f /etc/proftpd/proftpd.conf ]; then
		mv /etc/proftpd/proftpd.conf /etc/proftpd/proftpd.conf.backup
		sed 's/.*UseIPv6.*/UseIPv6 off/g' /etc/proftpd/proftpd.conf.backup | sed 's/Umask.*/Umask 037 027/g' | sed 's/.*DefaultRoot.*/DefaultRoot ~/g' | sed 's/# RequireValidShell.*/RequireValidShell off/g' > /etc/proftpd/proftpd.conf
	fi

	/etc/init.d/proftpd restart

fi

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
        groupadd $WEBGROUP
        WEBGROUPID=`id -g $WEBGROUP 2> /dev/null`
    fi
fi

if [ "$WEBGROUPID" == "" ]; then
	echo "Fatal Error: missing webservergroup ID"
	exit 0
fi

echo "Please enter the name of the masteruser easy-wi will use for the login. If it does not exists, the installer will create it."
read MASTERUSER

if [ "$MASTERUSER" == "" ]; then
	echo "Fatal Error: No masteruser specified"
	exit 0
fi

if [ "`id $MASTERUSER 2> /dev/null`" == "" ]; then
	`which useradd` -m -b /home -s /bin/bash -g $WEBGROUPID $MASTERUSER
	passwd $MASTERUSER
fi

if [ -f /etc/sudoers -a "`grep $MASTERUSER /etc/sudoers`" == "" ]; then

	echo "$MASTERUSER ALL = NOPASSWD: `which useradd`" >> /etc/sudoers
	echo "$MASTERUSER ALL = NOPASSWD: `which usermod`" >> /etc/sudoers
	echo "$MASTERUSER ALL = NOPASSWD: `which userdel`" >> /etc/sudoers

	if [ "$QUOTAINSTALL" == "Yes" ]; then
		echo "$MASTERUSER ALL = NOPASSWD: `which setquota`" >> /etc/sudoers
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

mkdir -p /home/$MASTERUSER/skel/logs
mkdir -p /home/$MASTERUSER/skel/htdocs
mkdir -p /home/$MASTERUSER/sites-enabled/

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

elif [ "$WEBSERVER" == "Apache" ]; then

	if [ -f /etc/apache2/sites-available/default ]; then
		mv /etc/apache2/sites-available/default /home/$MASTERUSER/sites-enabled/
	fi

fi

chown -R $MASTERUSER:$WEBGROUPID /home/$MASTERUSER/
