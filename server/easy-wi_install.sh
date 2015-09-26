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

function greenMessage {
    echo -e "\\033[32;1m${@}\033[0m"
}

function cyanMessage {
    echo -e "\\033[36;1m${@}\033[0m"
}

function redMessage {
    echo -e "\\033[31;1m${@}\033[0m"
}

function errorAndQuit {
    errorAndExit "Exit now!"
}

function errorAndExit {
    redMessage ${@}
    exit 0
}

function errorAndContinue {
    redMessage "Invalid option."
    continue
}

function runSpinner {

    SEQUENCE_END=`echo $1/0.4 | bc`
    SPINNER=("-" "\\" "|" "/")

    for SEQUENCE in `seq 1 $SEQUENCE_END`; do
        for I in "${SPINNER[@]}"; do
            echo -ne "\b$I"
            sleep 0.1
        done
    done
}

function makeDir {
    if [ "$1" != "" -a ! -d $1 ]; then
        mkdir -p $1
    fi
}

INSTALLER_VERSION="1.0"
OS=""
USERADD=`which useradd`
USERMOD=`which usermod`
USERDEL=`which userdel`
GROUPADD=`which groupadd`
MACHINE=`uname -m`
LOCAL_IP=`ifconfig | awk '/inet addr/{print substr($2,6)}' | grep -v '127.0.0.1' | head -n 1`

if [ "$LOCAL_IP" == "" ]; then
    HOST_NAME=`hostname -f | awk '{print tolower($0)}'`
else
    HOST_NAME=`getent hosts $LOCAL_IP | awk '{print tolower($2)}' | head -n 1`
fi

cyanMessage "Checking for the latest latest installer"
LATEST_VERSION=`wget -q --timeout=30 -O - http://l.easy-wi.com/installer_version.php | sed 's/^\xef\xbb\xbf//g'`

if [ "$LATEST_VERSION" != "" -a "`echo $LATEST_VERSION'>'$INSTALLER_VERSION | bc -l`" == "1" ]; then
    errorAndExit "You are using the old version ${INSTALLER_VERSION}. Please upgrade to version ${LATEST_VERSION} and retry."
else
    greenMessage "You are using the up to date version ${INSTALLER_VERSION}."
fi

# We need to be root to install and update
if [ "`id -u`" != "0" ]; then
    cyanMessage "Change to root account required"
    su -
fi

if [ "`id -u`" != "0" ]; then
    errorAndExit "Still not root, aborting"
fi

# Debian and its derivatives store their version at /etc/debian_version
if [ -f /etc/debian_version ]; then

    DISTRIBUTORID=`lsb_release -i 2> /dev/null | grep 'Distributor' | awk '{print $3}'`
    OSVERSION=`lsb_release -r 2> /dev/null | grep 'Release' | awk '{print $2}'`

    if [ "$DISTRIBUTORID" == "Ubuntu" ]; then
        OSBRANCH=`lsb_release -c 2> /dev/null | grep 'Codename' | awk '{print $2}'`
        OS="ubuntu"
    else
        OSBRANCH=`cat /etc/*release | grep 'VERSION=' | awk '{print $2}' | tr -d '()"'`
        OS="debian"
    fi
fi

if [ "$OS" == "" ]; then
    errorAndExit "Error: Could not detect OS. Currently only Debian and Ubuntuu are supported. Aborting!"
else
    greenMessage "Detected OS $OS"
fi

if [ "$OSBRANCH" == "" ]; then
    errorAndExit "Error: Could not detect branch of OS. Aborting"
else
    greenMessage "Detected branch $OSBRANCH"
fi

# Start with the install process by asking what to do
cyanMessage " "
cyanMessage "What shall be installed/prepared?"

OPTIONS=("Gameserver Root" "Voicemaster" "Easy-WI Webpanel" "Webspace Root" "Quit")
select OPTION in "${OPTIONS[@]}"; do
    case "$REPLY" in
        1 ) break;;
        2 ) break;;
        3 ) break;;
        4 ) break;;
        5 ) errorAndQuit;;
        *) errorAndContinue;;
    esac
done

if [ "$OPTION" == "Easy-WI Webpanel" ]; then
    INSTALL="EW"
#    errorAndExit "Not supported yet!"
elif [ "$OPTION" == "Gameserver Root" ]; then
    INSTALL="GS"
elif [ "$OPTION" == "Voicemaster" ]; then
    INSTALL="VS"
elif [ "$OPTION" == "Webspace Root" ]; then
    INSTALL="WR"
fi

# Run the domain/IP check up front to avoid late error out.
if [ "$INSTALL" == "EW" ]; then

    cyanMessage " "
    cyanMessage "At which URL/Domain should Easy-Wi be placed?"
    OPTIONS=("$HOST_NAME" "$LOCAL_IP" "Other" "Quit")
    select OPTION in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1 ) break;;
            2 ) break;;
            3 ) break;;
            4 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    if [ "$OPTION" == "Other" ]; then

        cyanMessage " "
        cyanMessage "Please specify the IP or domain Easy-Wi should run at."
        read IP_DOMAIN

    else
        IP_DOMAIN=$OPTION
    fi

    if [ "`grep -E '\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b' <<< $IP_DOMAIN`" == "" -a "`grep -E '^(([a-zA-Z](-?[a-zA-Z0-9])*)\.)*[a-zA-Z](-?[a-zA-Z0-9])+\.[a-zA-Z]{2,}$' <<< $IP_DOMAIN`" == "" ]; then
        errorAndExit "Error: $IP_DOMAIN is neither a domain nor an IPv4 address!"
    fi
fi

# Run the TS3 server version detect up front to avoid user executing steps first and fail at download last.
if [ "$INSTALL" == "VS" ]; then

    if [ "$MACHINE" == "x86_64" ]; then
        ARCH="amd64"
    elif [ "$MACHINE" == "i386" ]||[ "$MACHINE" == "i686" ]; then
        ARCH="x86"
    else
        errorAndExit "$MACHINE is not supported!"
    fi

    greenMessage "Searching latest build for hardware type $MACHINE with arch $ARCH."

    for VERSION in ` wget "http://dl.4players.de/ts/releases/?C=M;O=D" -q -O -| grep -i dir | egrep -o '<a href=\".*\/\">.*\/<\/a>' | egrep -o '[0-9\.?]+'| uniq | sort -r -g -t "." -k 1,1 -k 2,2 -k 3,3 -k 4,4`; do

        DOWNLOAD_URL_VERSION="http://dl.4players.de/ts/releases/$VERSION/teamspeak3-server_linux-$ARCH-$VERSION.tar.gz"
        STATUS=`wget -S --spider --tries 1 -q $DOWNLOAD_URL_VERSION 2>&1 | grep "HTTP/" | awk '{print $2}'`

        if [ "$STATUS" == "200" ]; then
            DOWNLOAD_URL=$DOWNLOAD_URL_VERSION
            break
        fi
    done

    if [ "$STATUS" == "200" -a "$DOWNLOAD_URL" != "" ]; then
        greenMessage "Detected latest server version as $VERSION with download URL $DOWNLOAD_URL"
    else
        errorAndExit "Could not detect latest server version"
    fi
fi

cyanMessage " "
greenMessage "Updating the system packages to the latest version."

if [ "$OS" == "debian" -o  "$OS" == "ubuntu" ]; then
    apt-get update && apt-get upgrade && apt-get dist-upgrade
fi

# If we need to install and configure a webspace than we need to identify the groupID
if [ "$INSTALL" == "EW" -o  "$INSTALL" == "WR" ]; then

    WEBGROUPID=`id -g www-data 2> /dev/null`

    if [ "$INSTALL" == "EW" ]; then
        OPTION="Yes"
    else
        cyanMessage " "
        cyanMessage "Found group www-data with group ID $WEBGROUPID. Use as webservergroup?"

        OPTIONS=("Yes" "No" "Quit")
        select OPTION in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1 ) break;;
                2 ) break;;
                3 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done
    fi

    if [ "$OPTION" == "No" ]; then

        cyanMessage "Please name the group you want to use as webservergroup"
        read WEBGROUP

        WEBGROUPID=`id -g $WEBGROUP 2> /dev/null`
        if [ "$WEBGROUPID" == "" ]; then
            $GROUPADD $WEBGROUP
            WEBGROUPID=`id -g $WEBGROUP 2> /dev/null`
        fi
    fi

    if [ "$WEBGROUPID" == "" ]; then
        errorAndExit "Fatal Error: missing webservergroup ID"
    fi
fi

cyanMessage "Please enter the name of the masteruser. If it does not exists, the installer will create it."
read MASTERUSER

if [ "$MASTERUSER" == "" ]; then
    errorAndExit "Fatal Error: No masteruser specified"
fi

if [ "`id $MASTERUSER 2> /dev/null`" == "" ]; then

    if [ "$INSTALL" == "EW" -o  "$INSTALL" == "WR" ]; then
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

elif [ "$INSTALL" != "VS" ]; then
    greenMessage "User \"$MASTERUSER\" found setting group \"$MASTERUSER\" as mastegroup"
    usermod -g $MASTERUSER $MASTERUSER
else
    greenMessage "User \"$MASTERUSER\" already exists."
fi

cyanMessage " "
cyanMessage "Create key or set password for login?"
cyanMessage "Safest way of login is a password protected key."

if [ "$INSTALL" == "EW" ]; then
    cyanMessage "Neither is not required, when installing Easy-WI Webpanel."
fi

OPTIONS=("Create key" "Set password" "Skip" "Quit")
select OPTION in "${OPTIONS[@]}"; do
    case "$REPLY" in
        1 ) break;;
        2 ) break;;
        3 ) break;;
        4 ) errorAndQuit;;
        *) errorAndContinue;;
    esac
done

if [ "$OPTION" == "Create key" ]; then

    if [ -d /home/$MASTERUSER/.ssh ]; then
        rm -rf /home/$MASTERUSER/.ssh
    fi

    cyanMessage " "
    cyanMessage "It is recommended but not required to set a password"
    su -c "ssh-keygen -t rsa" $MASTERUSER

    cd /home/$MASTERUSER/.ssh

    KEYNAME=`find -maxdepth 1 -name "*.pub" | head -n 1`

    if [ "$KEYNAME" != "" ]; then
        su -c "cat $KEYNAME >> authorized_keys" $MASTERUSER
    else
        redMessage "Error: could not find a key. You might need to create one manually at a later point."
    fi

elif [ "$OPTION" == "Set password" ]; then
    passwd $MASTERUSER
fi

# only in case we want to manage webspace we need the additional skel dir
if [ "$INSTALL" == "WR" -o "$INSTALL" == "EW" ]; then
    makeDir /home/$MASTERUSER/skel/logs
    makeDir /home/$MASTERUSER/skel/htdocs
    makeDir /home/$MASTERUSER/sites-enabled/
fi

if [ "$INSTALL" == "EW" -o  "$INSTALL" == "WR" ]; then

    if [ "$OS" == "debian" ]; then

            cyanMessage " "
            cyanMessage "Use dotdeb.org repository for more up to date server and PHP versions?"

            OPTIONS=("Yes" "No" "Quit")
            select DOTDEB in "${OPTIONS[@]}"; do
                    case "$REPLY" in
                            1 ) break;;
                            2 ) break;;
                            3 ) errorAndQuit;;
                            *) errorAndContinue;;
                    esac
            done

            if [ "$DOTDEB" == "Yes" ]; then
                    if [ "`grep 'packages.dotdeb.org' /etc/apt/sources.list`" == "" ]; then
                            greenMessage "Adding entries to /etc/apt/sources.list"
                            add-apt-repository "deb http://packages.dotdeb.org $OSBRANCH all"
                            add-apt-repository "deb-src http://packages.dotdeb.org $OSBRANCH all"
                            wget http://www.dotdeb.org/dotdeb.gpg
                            apt-key add dotdeb.gpg
                            rm -f dotdeb.gpg
                            apt-get update
                    fi
            fi
    fi

    cyanMessage " "
    cyanMessage "Please select the webserver you would like to use"

    if [ "$INSTALL" == "EW" ]; then

        cyanMessage "Apache is recommended in case you want to run additional sites on this host."
        cyanMessage "Nginx is recommended if the server should only run the Easy-WI Web Panel."

        OPTIONS=("Nginx" "Apache" "Quit")
        select WEBSERVER in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1 ) break;;
                2 ) break;;
                3 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done

    else

        cyanMessage "Nginx is recommended for FastDL and few but high efficient vhosts"
        cyanMessage "Apache is recommended in case you want to run many PHP supporting Vhosts aka mass web hosting"

        OPTIONS=("Nginx" "Apache" "Lighttpd" "None" "Quit")
        select WEBSERVER in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1 ) break;;
                2 ) break;;
                3 ) break;;
                4 ) break;;
                5 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done
    fi

    if [ "$WEBSERVER" == "Nginx" ]; then
        apt-get install nginx-full
    elif [ "$WEBSERVER" == "Lighttpd" ]; then
        apt-get install lighttpd
    elif [ "$WEBSERVER" == "Apache" ]; then
        apt-get install apache2
    fi

    if [ "$INSTALL" == "EW" ]; then

        greenMessage "Please note that Easy-Wi requires a MySQL or MariaDB installed and will install MySQL if no DB is installed"

        if [ "`ps x | grep mysql | grep -v grep`" == "" ]; then
            SQL="MySQL"
        else
            SQL=""
        fi

    else

        cyanMessage " "
        cyanMessage "Please select if an which database server to install."
        cyanMessage "Select \"None\" in case this server should host only Fastdownload webspace."

        OPTIONS=("MySQL" "MariaDB" "None" "Quit")
        select SQL in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1 ) break;;
                2 ) break;;
                4 ) break;;
                5 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done
    fi

    if [ "$SQL" == "MySQL" ]; then

        apt-get install mysql-server mysql-client mysql-common

        cyanMessage " "
        greenMessage "Securing MySQL by running \"mysql_secure\"."
        mysql_secure_installation

    elif [ "$SQL" == "MariaDB" ]; then

        RUNUPDATE=0

        if ([ "`echo $OSVERSION'>='8.0 | bc -l`" == "0" -o "$OS" == "ubuntu" ] && [ "`grep '/mariadb/' /etc/apt/sources.list`" == "" ]); then

            apt-get install python-software-properties
            apt-key adv --recv-keys --keyserver keyserver.ubuntu.com 0xcbcb082a1bb943db

            if [ "$SQL" == "MariaDB" -a "`apt-cache search mariadb-server-10.0`" == "" ]; then
                if [ "$OS" == "debian" ]; then
                    add-apt-repository "deb http://mirror.netcologne.de/mariadb/repo/10.0/debian $OSBRANCH main"
                    RUNUPDATE=1
                elif [ "$OS" == "ubuntu" ]; then
                    add-apt-repository "deb http://mirror.netcologne.de/mariadb/repo/10.0/ubuntu $OSBRANCH main"
                    RUNUPDATE=1
                fi
            fi
        fi

        if [ "$OS" == "debian" -a "$DOTDEB" == "Yes" ]; then
            echo "Package: *" > /etc/apt/preferences.d/mariadb.pref
            echo "Pin: origin mirror.netcologne.de" >> /etc/apt/preferences.d/mariadb.pref
            echo "Pin-Priority: 1000" >> /etc/apt/preferences.d/mariadb.pref
            RUNUPDATE=1
        fi

        if [ "$RUNUPDATE" == "1" ]; then
            apt-get update
        fi

        if ([ "`echo $OSVERSION'>='8.0 | bc -l`" == "0" -o "$OS" == "ubuntu" ] && [ "`grep '/mariadb/' /etc/apt/sources.list`" == "" ]); then
            apt-get install mariadb-server mariadb-client mysql-common
        else
            apt-get install mariadb-server mariadb-client mariadb-common
        fi

        mysql_secure_installation
    fi

    if [ "$INSTALL" == "EW" -a "`ps x | grep mysql | grep -v grep`" == "" ]; then
        cyanMessage " "
        errorAndExit "Error: No SQL server running but required for Webpanel installation."
    fi

    if [ "$INSTALL" == "EW" ]; then

        greenMessage "Please note that Easy-Wi will install required PHP packages."
        PHPINSTALL="Yes"

    else

        cyanMessage " "
        cyanMessage "Install/Update PHP?"
        cyanMessage "Select \"None\" in case this server should host only Fastdownload webspace."

        OPTIONS=("Yes" "No" "None" "Quit")
        select PHPINSTALL in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1 ) break;;
                2 ) break;;
                3 ) break;;
                4 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done
    fi

    if [ "$PHPINSTALL" == "Yes" ]; then

        if [ "$OS" == "debian" -a "$DOTDEB" == "Yes" ]; then

            cyanMessage " "

            if [ "$OSBRANCH" == "wheezy" ]; then

                cyanMessage "Which PHP version should be used?"

                OPTIONS=("5.4" "5.5", "5.6", "5.6 Zend thread safety" "Quit")
                select DOTDEBPHPUPGRADE in "${OPTIONS[@]}"; do
                    case "$REPLY" in
                        1 ) break;;
                        2 ) break;;
                        3 ) break;;
                        4 ) break;;
                        5 ) errorAndQuit;;
                        *) errorAndContinue;;
                    esac
                done

                if [ "$DOTDEBPHPUPGRADE" == "5.5" -a "`grep 'wheezy-php55' /etc/apt/sources.list`" == "" ]; then
                    add-apt-repository "deb http://packages.dotdeb.org wheezy-php55 all"
                    add-apt-repository "deb-src http://packages.dotdeb.org wheezy-php55 all"
                elif [ "$DOTDEBPHPUPGRADE" == "5.6" -a "`grep 'wheezy-php56' /etc/apt/sources.list`" == "" ]; then
                    add-apt-repository "deb http://packages.dotdeb.org wheezy-php56 all"
                    add-apt-repository "deb-src http://packages.dotdeb.org wheezy-php56 all"
                elif [ "$DOTDEBPHPUPGRADE" == "5.6 Zend thread safety" -a "`grep 'wheezy-php56-zts' /etc/apt/sources.list`" == "" ]; then
                    add-apt-repository "deb http://packages.dotdeb.org wheezy-php56-zts all"
                    add-apt-repository "deb-src http://packages.dotdeb.org wheezy-php56-zts all"
                fi

            elif [ "$OSBRANCH" == "squeeze" -a "`grep 'squeeze-php54' /etc/apt/sources.list`" == "" ]; then
                add-apt-repository "deb http://packages.dotdeb.org squeeze-php54 all"
                add-apt-repository "deb-src http://packages.dotdeb.org squeeze-php54 all"
            fi

            if [ "$DOTDEBPHPUPGRADE" == "Yes" ]; then
                apt-get update
                apt-get upgrade
            fi
        fi
        
        apt-get install php5-common php5-curl php5-gd php5-mcrypt php5-mysql php5-cli

        if [ "$WEBSERVER" == "Nginx" -o "$WEBSERVER" == "Lighttpd" ]; then

            apt-get install php5-fpm

            if [ "$WEBSERVER" == "Lighttpd" ]; then
                lighttpd-enable-mod fastcgi
                lighttpd-enable-mod fastcgi-php
            fi

            makeDir /home/$MASTERUSER/fpm-pool.d/
            sed -i "s/include=\/etc\/php5\/fpm\/pool.d\/\*.conf/include=\/home\/$MASTERUSER\/fpm-pool.d\/\*.conf/g" /etc/php5/fpm/php-fpm.conf

        elif [ "$WEBSERVER" == "Apache" ]; then
            apt-get install apache2-mpm-itk libapache2-mod-php5 php5
            a2enmod php5
        fi
    fi
fi

if [ "$INSTALL" != "VS" -a "$INSTALL" != "EW" ]; then

    cyanMessage " "
    cyanMessage "Install/Update ProFTPD?"

    OPTIONS=("Yes" "No" "Quit")
    select OPTION in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1 ) break;;
            2 ) break;;
            3 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    if [ "$OPTION" == "Yes" ]; then

        apt-get install proftpd

        if [ -f /etc/proftpd/modules.conf ]; then

            cp /etc/proftpd/modules.conf /etc/proftpd/modules.conf.easy-install.backup

            sed -i 's/.*LoadModule mod_tls_memcache.c.*/#LoadModule mod_tls_memcache.c/g' /etc/proftpd/modules.conf
        fi

        if [ -f /etc/proftpd/proftpd.conf -a "$INSTALL" != "GS" ]; then

            cp /etc/proftpd/proftpd.conf /etc/proftpd/proftpd.conf.easy-install.backup

            sed -i 's/.*UseIPv6.*/UseIPv6 off/g' /etc/proftpd/proftpd.conf
            sed -i 's/Umask.*/Umask 037 027/g' /etc/proftpd/proftpd.conf 
            sed -i 's/.*DefaultRoot.*/DefaultRoot ~/g' /etc/proftpd/proftpd.conf
            sed -i 's/# RequireValidShell.*/RequireValidShell off/g' /etc/proftpd/proftpd.conf

        elif [ -f /etc/proftpd/proftpd.conf -a "$INSTALL" == "GS" ]; then

            cp /etc/proftpd/proftpd.conf /etc/proftpd/proftpd.conf.easy-install.backup

            sed -i 's/.*UseIPv6.*/UseIPv6 off/g' /etc/proftpd/proftpd.conf
            sed -i 's/Umask.*/Umask 077 077/g' /etc/proftpd/proftpd.conf
            sed -i 's/.*DefaultRoot.*/DefaultRoot ~/g' /etc/proftpd/proftpd.conf

            cyanMessage " "
            cyanMessage "Install/Update ProFTPD Rules?"

            OPTIONS=("Yes" "No" "Quit")
            select OPTION in "${OPTIONS[@]}"; do
                case "$REPLY" in
                    1 ) break;;
                    2 ) break;;
                    3 ) errorAndQuit;;
                    *) errorAndContinue;;
                esac
            done

            if [ "$OPTION" == "Yes" -a "`grep '<Directory \/home\/\*\/pserver\/\*>' /etc/proftpd/proftpd.conf`" == "" -a ! -f "/etc/proftpd/conf.d/easy-wi.conf" ]; then

                makeDir /etc/proftpd/conf.d/
                chmod 755 /etc/proftpd/conf.d/
                
                echo "
<Directory ~>
        HideFiles (^\..+|\.ssh|\.bash_history|\.bash_logout|\.bashrc|\.profile|srcds_run|srcds_linux|hlds_run|hlds_amd|hlds_i686|\.rc|\.sh|\.zip|\.rar|\.7z|\.dll)$
        PathDenyFilter (^\..+|\.ssh|\.bash_history|\.bash_logout|\.bashrc|\.profile|srcds_run|srcds_linux|hlds_run|hlds_amd|hlds_i686|\.rc|\.sh|\.zip|\.rar|\.7z|\.dll)$
        HideNoAccess on
        <Limit RNTO RNFR STOR DELE CHMOD SITE_CHMOD MKD RMD>
                DenyAll
        </Limit>
</Directory>" > /etc/proftpd/conf.d/easy-wi.conf
                echo "<Directory /home/$MASTERUSER>" >> /etc/proftpd/conf.d/easy-wi.conf
                echo "    HideFiles (^\..+|\.ssh|\.bash_history|\.bash_logout|\.bashrc|\.profile)$
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
<Directory ~/server/*/projectcars*/*>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
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
<Directory ~/server/*/tekkit*/*>
        Umask 077 077
        <Limit RNFR RNTO STOR DELE MKD RMD>
                AllowAll
        </Limit>
</Directory>
<Directory ~/server/*/tekkit-classic*/*>
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
<Directory ~/server/*/*/czero/*>
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
<Directory ~/server/*/*/garrysmod/*>
    Umask 077 077
    <Limit RNFR RNTO STOR DELE MKD RMD>
        AllowAll
    </Limit>
</Directory>
<Directory ~/server/*/ark*/*>
    Umask 077 077
    <Limit RNFR RNTO STOR DELE MKD RMD>
        AllowAll
    </Limit>
</Directory> 
<Directory ~/*/*/>
    HideFiles (^\..+|srcds_run|srcds_linux|hlds_run|hlds_amd|hlds_i686|\.rc|\.sh|\.zip|\.rar|\.7z|\.dll)$
    PathDenyFilter (^\..+|srcds_run|srcds_linux|hlds_run|hlds_amd|hlds_i686|\.rc|\.sh|\.zip|\.rar|\.7z|\.dll)$
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
<Directory ~/*/*/czero/*>
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
" >> /etc/proftpd/conf.d/easy-wi.conf
            fi
        fi

        if [ -f /etc/init.d/proftpd ]; then
            /etc/init.d/proftpd restart
        fi
    fi
fi

if [ "$INSTALL" == "GS" -o "$INSTALL" == "WR" ]; then

    cyanMessage " "
    cyanMessage "Install Quota?"

    OPTIONS=("Yes" "No" "Quit")
    select QUOTAINSTALL in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1 ) break;;
            2 ) break;;
            3 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    if [ "$QUOTAINSTALL" == "Yes" ]; then
        apt-get install quota

        cyanMessage " "
        cyanMessage " "
        if [ -f /root/tempfstab ]; then
            rm -f /root/tempfstab
        fi
        if [ -f /root/tempmountpoints ]; then
            rm -f /root/tempmountpoints
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

        cyanMessage " "
        cyanMessage " "
        cyanMessage "Please check above output and confirm it is correct. On confirmation the current /etc/fstab will be replaced in order to activate Quotas!"

        OPTIONS=("Yes" "No" "Quit")
        select QUOTAFSTAB in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1 ) break;;
                2 ) break;;
                3 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done

        if [ "$QUOTAFSTAB" == "Yes" ]; then
            mv /etc/fstab /etc/fstab.backup
            mv /root/tempfstab /etc/fstab
        fi

        if [ -f /root/tempfstab ]; then
            rm -f /root/tempfstab
        fi

        if [ -f /root/tempmountpoints ]; then
        
            cat /root/tempmountpoints | while read LINE; do

                quotaoff -ugv $LINE

                if [ -f $LINE/aquota.user ]; then
                    rm -f $LINE/aquota.user
                fi

                greenMessage "Remounting $LINE"
                mount -o remount $LINE

                quotacheck -vumc $LINE
                quotaon -uv $LINE
            done

            rm -f /root/tempmountpoints
        fi
    fi
fi

if [ "$INSTALL" == "WR" -o "$INSTALL" == "EW" ]; then

    if [ "$WEBSERVER" == "Nginx" ]; then

        if [ -f /etc/nginx/sites-available/default ]; then
            cp /etc/nginx/sites-available/default /home/$MASTERUSER/sites-enabled/
        fi

        cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.easy-install.backup

        sed -i "s/\/etc\/nginx\/sites-enabled\/\*;/\/home\/$MASTERUSER\/sites-enabled\/\*;/g" /etc/nginx/nginx.conf

    elif [ "$WEBSERVER" == "Lighttpd" ]; then

        if [ -f /etc/lighttpd/sites-available/default ]; then
            cp /etc/lighttpd/sites-available/default /home/$MASTERUSER/sites-enabled/
        fi

        cp /etc/lighttpd/lighttpd.conf /etc/lighttpd/lighttpd.conf.easy-install.backup
        echo "include_shell \"find /home/$MASTERUSER/sites-enabled/ -maxdepth 1 -type f -exec cat {} \;\"" >> /etc/lighttpd/lighttpd.conf

    elif [ "$WEBSERVER" == "Apache" ]; then

        if [ -f /etc/apache2/sites-available/default ]; then
            cp /etc/apache2/sites-available/default /home/$MASTERUSER/sites-enabled/
        fi

        if [ -f /etc/apache2/sites-available/default-ssl ]; then
            cp /etc/apache2/sites-available/default-ssl /home/$MASTERUSER/sites-enabled/
        fi

        cp /etc/apache2/apache2.conf /etc/apache2/apache2.conf.easy-install.backup

        APACHE_VERSION=`apache2 -v | grep 'Server version'`

        if [[ $APACHE_VERSION =~ .*Apache/2.2.* ]]; then
            sed -i "s/Include sites-enabled\//Include \/home\/$MASTERUSER\/sites-enabled\//g" /etc/apache2/apache2.conf
            sed -i "s/Include \/etc\/apache2\/sites-enabled\//\/home\/$MASTERUSER\/sites-enabled\//g" /etc/apache2/apache2.conf
        else
            sed -i "s/IncludeOptional sites-enabled\//IncludeOptional \/home\/$MASTERUSER\/sites-enabled\//g" /etc/apache2/apache2.conf
            sed -i "s/IncludeOptional \/etc\/apache2\/sites-enabled\//IncludeOptional \/home\/$MASTERUSER\/sites-enabled\//g" /etc/apache2/apache2.conf
        fi
    fi

    #TODO: Logrotate
fi

# No direct root access for masteruser. Only limited access through sudo
if [ "$INSTALL" == "GS" -o  "$INSTALL" == "WR" ]; then

    cyanMessage " "
    greenMessage "Installing sudo"
    apt-get install sudo

    if [ -f /etc/sudoers -a "`grep $MASTERUSER /etc/sudoers`" == "" ]; then

        echo "$MASTERUSER ALL = NOPASSWD: $USERADD" >> /etc/sudoers
        echo "$MASTERUSER ALL = NOPASSWD: $USERMOD" >> /etc/sudoers
        echo "$MASTERUSER ALL = NOPASSWD: $USERDEL" >> /etc/sudoers

        if [ "$QUOTAINSTALL" == "Yes" ]; then
            echo "$MASTERUSER ALL = NOPASSWD: `which setquota`" >> /etc/sudoers
            echo "$MASTERUSER ALL = NOPASSWD: `which repquota`" >> /etc/sudoers
        fi

        if [ "$INSTALL" == "GS" ]; then
            echo "$MASTERUSER ALL = (ALL, !root:easywi) NOPASSWD: /home/$MASTERUSER/temp/*.sh" >> /etc/sudoers
        fi

        if [ "$WEBSERVER" == "Nginx" ]; then
            HTTPDBIN=`which nginx`
            HTTPDSCRIPT="/etc/init.d/nginx"
        elif [ "$WEBSERVER" == "Lighttpd" ]; then
            HTTPDBIN=`which lighttpd`
            HTTPDSCRIPT="/etc/init.d/lighttpd"
        elif [ "$WEBSERVER" == "Apache" ]; then
            HTTPDBIN=`which apache2`
            HTTPDSCRIPT="/etc/init.d/apache2"
        fi

        if [ "$HTTPDBIN" != "" ]; then
            echo "$MASTERUSER ALL = NOPASSWD: $HTTPDBIN" >> /etc/sudoers
            echo "$MASTERUSER ALL = NOPASSWD: $HTTPDSCRIPT" >> /etc/sudoers
        fi
    fi
fi

if [ "$INSTALL" == "WR" ]; then

    chown -R $MASTERUSER:$WEBGROUPID /home/$MASTERUSER/

    greenMessage "Following data need to be configured at the easy-wi.com panel:"

    greenMessage "The path to the folder \"sites-enabled\" is:"
    greenMessage "/home/$MASTERUSER/sites-enabled/"

    greenMessage "The useradd command is:"
    greenMessage "sudo $USERADD %cmd%"

    greenMessage "The usermod command is:"
    greenMessage "sudo $USERMOD %cmd%"

    greenMessage "The userdel command is:"
    greenMessage "sudo $USERDEL %cmd%"

    greenMessage "The HTTPD restart command is:"
    greenMessage "sudo $HTTPDSCRIPT reload"
fi

if ([ "$INSTALL" == "GS" -o "$INSTALL" == "WR" ] && [ "$QUOTAINSTALL" == "Yes" ]); then
    greenMessage "The setquota command is:"
    greenMessage "sudo `which setquota` %cmd%"
fi

if [ "$INSTALL" == "GS" ]; then

    cyanMessage " "
    cyanMessage "Java JRE will be required for running Minecraft and its mods. Shall it be installed?"
    OPTIONS=("Yes" "No" "Quit")
    select OPTION in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1 ) break;;
            2 ) break;;
            3 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    if [ "$OPTION" == "Yes" ]; then
        apt-get install default-jre
    fi

    greenMessage "Creating folders and files"
    CREATEDIRS=("conf" "fdl_data/hl2" "logs" "masteraddons" "mastermaps" "masterserver" "temp")
    for CREATEDIR in ${CREATEDIRS[@]}; do
        greenMessage "Adding dir: /home/$MASTERUSER/$CREATEDIR"
        makeDir /home/$MASTERUSER/$CREATEDIR
    done

    LOGFILES=("addons" "hl2" "server" "fdl" "update" "fdl-hl2")
    for LOGFILE in ${LOGFILES[@]}; do
        touch "/home/$MASTERUSER/logs/$LOGFILE.log"
    done
    chmod 660 /home/$MASTERUSER/logs/*.log

    chown -R $MASTERUSER:$MASTERUSER /home/$MASTERUSER/
    chmod -R 750 /home/$MASTERUSER/
    chmod -R 770 /home/$MASTERUSER/logs/ /home/$MASTERUSER/temp/ /home/$MASTERUSER/fdl_data/

    if [ "$OS" == "debian" -a "`uname -m`" == "x86_64" -a "`cat /etc/debian_version | grep '6.'`" == "" ]; then
        dpkg --add-architecture i386
    fi

    if [ "$OS" == "debian" -o  "$OS" == "ubuntu" ]; then

        apt-get update

        apt-get install wget wput screen bzip2 sudo rsync zip unzip

        if [ "`uname -m`" == "x86_64" ]; then
            if [ "$OS" == "debian" -a "`echo $OSVERSION'>='8.0 | bc -l`" == "1" ]; then
                apt-get install zlib1g lib32z1 lib32gcc1 libgcc1:i386 lib32readline5 libreadline5:i386 lib32ncursesw5 libncursesw5:i386
            else
                apt-get install ia32-libs lib32readline5 lib32ncursesw5 lib32stdc++6
            fi
        else
            apt-get install libreadline5 libncursesw5
        fi
    fi

    greenMessage "Downloading SteamCmd"

    cd /home/$MASTERUSER/masterserver
    makeDir /home/$MASTERUSER/masterserver/steamCMD/
    cd /home/$MASTERUSER/masterserver/steamCMD/
    wget -q --timeout=30 http://media.steampowered.com/client/steamcmd_linux.tar.gz

    if [ -f steamcmd_linux.tar.gz ]; then
        tar xfvz steamcmd_linux.tar.gz
        rm -f steamcmd_linux.tar.gz
        chown -R $MASTERUSER:$MASTERUSER /home/$MASTERUSER/masterserver/steamCMD
        su -c "./steamcmd.sh +login anonymous +quit" $MASTERUSER
    fi

    chown -R $INSTALLMASTER:$INSTALLMASTER /home/$INSTALLMASTER

    if [ -f /etc/crontab -a "`grep 'Minecraft can easily produce 1GB' /etc/crontab`" == "" ]; then

        if ionice -c3 true 2>/dev/null; then
            IONICE="ionice -n 7 "
        fi

        echo "#Minecraft can easily produce 1GB+ logs within one hour" >> /etc/crontab
        echo "*/5 * * * * root nice -n +19 ionice -n 7 find /home/*/server/*/*/ -maxdepth 2 -type f -name \"screenlog.0\" -size +100M -delete" >> /etc/crontab
        echo "# Even sudo /usr/sbin/deluser --remove-all-files is used some data remain from time to time" >> /etc/crontab
        echo "*/5 * * * * root nice -n +19 $IONICE find /home/ -maxdepth 2 -type d -nouser -delete" >> /etc/crontab
        echo "*/5 * * * * root nice -n +19 $IONICE find /home/*/fdl_data/ /home/*/temp/ /tmp/ /var/run/screen/ -nouser -print0 | xargs -0 rm -rf" >> /etc/crontab
        echo "*/5 * * * * root nice -n +19 $IONICE find /var/run/screen/ -maxdepth 1 -type d -nouser -print0 | xargs -0 rm -rf" >> /etc/crontab

        /etc/init.d/cron restart
    fi
fi

if [ "$INSTALL" == "EW" ]; then

    if [ -f /home/easywi_web/htdocs/serverallocation.php ]; then
    
        cyanMessage " "
        cyanMessage "There is already an existing installation. Should it be removed?"
        OPTIONS=("Yes" "Quit")
        select OPTION in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1 ) break;;
                2 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done

        rm -rf /home/easywi_web/htdocs/*
    fi

    if [ "`id easywi_web 2> /dev/null`" == "" ]; then
        $USERADD -md /home/easywi_web -g www-data -s /bin/false -k /home/easywi/skel/ easywi_web
    fi

    if [ "`id easywi_web 2> /dev/null`" == "" ]; then
        errorAndExit "Web user easywi_web does not exists! Exiting now!"
    fi

    if [ ! -d /home/easywi_web ]; then
        errorAndExit "No home dir created! Exiting now!"
    fi

    cyanMessage "Installing required tool unzip"
    apt-get install unzip -y

    makeDir /home/easywi_web/htdocs/
    makeDir /home/$MASTERUSER/fpm-pool.d/

    cd /home/easywi_web/htdocs/

    wget https://easy-wi.com/uk/downloads/get/3/ -O web.zip

    if [ ! -f web.zip ]; then
        errorAndExit "Can not download Easy-WI. Aborting!"
    fi

    unzip web.zip
    rm -f web.zip

    find /home/easywi_web/ -type f -print0 | xargs -0 chmod 640
    find /home/easywi_web/ -mindepth 1 -type d -print0 | xargs -0 chmod 750

    chown -R easywi_web:www-data /home/easywi_web

    DB_PASSWORD=`< /dev/urandom tr -dc A-Za-z0-9 | head -c18`

    cyanMessage "The MySQL Root password is required."
    mysql -u root -p -Bse "CREATE DATABASE IF NOT EXISTS easy_wi; GRANT ALL ON easy_wi.* TO 'easy_wi'@'localhost' IDENTIFIED BY '$DB_PASSWORD'; FLUSH PRIVILEGES;"

    if [ "$WEBSERVER" == "Nginx" ]; then

        FILE_NAME=${IP_DOMAIN//./_}

        echo "[$FILE_NAME]
user = easywi_web
group = www-data
listen = /var/run/php5-fpm-$FILE_NAME.sock
listen.owner = easywi_web
listen.group = www-data
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
pm.max_requests = 500
chdir = /
php_flag[display_errors] = off
php_admin_value[error_log] = /home/easywi_web/log/fpm-php.log
php_admin_flag[log_errors] = on
php_admin_value[memory_limit] = 32M
"> /home/$MASTERUSER/fpm-pool.d/$FILE_NAME.conf

        echo 'server {
    listen 80;
    root /home/easywi_web/htdocs/;
    index index.html index.htm index.php;
    server_name "%IP_DOMAIN%";

    location ^~ /(keys|stuff|template|languages|downloads|tmp)/ { deny all; }

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:%SOCKET_NAME%;
    }
}' > /home/$MASTERUSER/sites-enabled/$FILE_NAME

        sed -i "s/    server_name \"%IP_DOMAIN%\";/    server_name \"$IP_DOMAIN\";/g" /home/$MASTERUSER/sites-enabled/$FILE_NAME
        sed -i "s/        fastcgi_pass unix:%SOCKET_NAME%;/        fastcgi_pass unix:\/var\/run\/php5-fpm-$FILE_NAME.sock;/g" /home/$MASTERUSER/sites-enabled/$FILE_NAME

        chown -R $MASTERUSER:$WEBGROUPID /home/$MASTERUSER/

        /etc/init.d/php5-fpm restart
        /etc/init.d/nginx restart
    fi

    if [ "`grep reboot.php /etc/crontab`" == "" ]; then
        echo '0 */1 * * * root su -s /bin/bash -c "cd /home/easywi_web/htdocs && timeout 300 php ./reboot.php >/dev/null 2>&1" easywi_web
0 */1 * * * root su -s /bin/bash -c "cd /home/easywi_web/htdocs && timeout 300 php ./reboot.php >/dev/null 2>&1" easywi_web
*/5 * * * * root su -s /bin/bash -c "cd /home/easywi_web/htdocs && timeout 290 php ./statuscheck.php >/dev/null 2>&1" easywi_web
*/1 * * * * root su -s /bin/bash -c "cd /home/easywi_web/htdocs && timeout 290 php ./startupdates.php >/dev/null 2>&1" easywi_web
*/5 * * * * root su -s /bin/bash -c "cd /home/easywi_web/htdocs && timeout 290 php ./jobs.php >/dev/null 2>&1" easywi_web
*/10 * * * * root su -s /bin/bash -c "cd /home/easywi_web/htdocs && timeout 290 php ./cloud.php >/dev/null 2>&1" easywi_web' >> /etc/crontab
    fi

    /etc/init.d/cron restart
fi

if [ "$INSTALL" == "VS" ]; then

    ps -u $MASTERUSER | grep ts3server | awk '{print $1}' | while read PID; do
        kill $PID
    done

    if [ -f /home/$MASTERUSER/ts3server_startscript.sh ]; then
        rm -rf /home/$MASTERUSER/*
    fi

    makeDir /home/$MASTERUSER/
    chmod 750 /home/$MASTERUSER/
    chown -R $MASTERUSER:$MASTERUSER /home/$MASTERUSER

    cd /home/$MASTERUSER/

    greenMessage "Downloading TS3 server files."
    su -c "wget $DOWNLOAD_URL -O teamspeak3-server.tar.gz" $MASTERUSER

    if [ ! -f teamspeak3-server.tar.gz ]; then
        errorAndExit "Download failed! Exiting now!"
    fi

    greenMessage "Extracting TS3 server files."
    su -c "tar -xf teamspeak3-server.tar.gz --strip-components=1" $MASTERUSER

    rm -f teamspeak3-server.tar.gz

    if [ ! -f query_ip_whitelist.txt ]; then
        touch query_ip_whitelist.txt
        chown $MASTERUSER.$MASTERUSER query_ip_whitelist.txt
    fi

    if [ "`grep '127.0.0.1' query_ip_whitelist.txt`" == "" ]; then
        echo "127.0.0.1" >> query_ip_whitelist.txt
    fi

    if [ "`grep -E '\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b' <<< $LOCAL_IP`" != "" -a "`grep $LOCAL_IP query_ip_whitelist.txt`" == "" ]; then
        echo $LOCAL_IP >> query_ip_whitelist.txt
    fi

    cyanMessage " "
    cyanMessage "Please secify the IPv4 address of the Easy-WI web panel."
    read IP_ADDRESS

    if [ "`grep -E '\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b' <<< $IP_ADDRESS`" != "" -a "`grep $IP_ADDRESS query_ip_whitelist.txt`" == "" ]; then
        echo $IP_ADDRESS >> query_ip_whitelist.txt
    fi

    QUERY_PASSWORD=`< /dev/urandom tr -dc A-Za-z0-9 | head -c12`

    greenMessage "Starting the TS3 server for the first time and shutting it down again as the password will be visible in the process tree."
    su -c "./ts3server_startscript.sh start serveradmin_password=$QUERY_PASSWORD createinifile=1 inifile=ts3server.ini" $MASTERUSER
    runSpinner 10
    su -c "./ts3server_startscript.sh stop" $MASTERUSER

    greenMessage "Starting the TS3 server permanently."
    su -c "./ts3server_startscript.sh start inifile=ts3server.ini" $MASTERUSER
fi

greenMessage "Removing not needed packages."
apt-get autoremove

if [ "$INSTALL" == "EW" ]; then
    greenMessage "Easy-WI Webpanel setup is done regarding architecture. Please open http://$IP_DOMAIN/install/install.php and complete the installation dialog."
    greenMessage "DB user and table name are \"easy_wi\". The password is \"$DB_PASSWORD\"."
elif [ "$INSTALL" == "GS" ]; then
    greenMessage "Gameserver Root setup is done. Please enter the above data at the webpanel at \"App/Game Master > Overview > Add\"."
elif [ "$INSTALL" == "VS" ]; then
    greenMessage "Teamspeak 3 setup is done. TS3 Query password is $QUERY_PASSWORD. Please enter the data at the webpanel at \"Voiceserver > Master > Add\"."
elif [ "$INSTALL" == "WR" ]; then
    greenMessage "Webspace Root setup is done. Please enter the above data at the webpanel at \"Webspace > Master > Add\"."
fi

exit 0