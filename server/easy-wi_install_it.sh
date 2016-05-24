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

function yellowMessage {
	echo -e "\\033[33;1m${@}\033[0m"
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

function removeIfExists {
    if [ "$1" != "" -a -f "$1" ]; then
        rm -f $1
    fi
}

function runSpinner {

    SPINNER=("-" "\\" "|" "/")

    for SEQUENCE in `seq 1 $1`; do
        for I in "${SPINNER[@]}"; do
            echo -ne "\b$I"
            sleep 0.1
        done
    done
}

function okAndSleep {
    greenMessage $1
    sleep 1
}

function makeDir {
    if [ "$1" != "" -a ! -d $1 ]; then
        mkdir -p $1
    fi
}

function backUpFile {
    if [ ! -f "$1.easy-install.backup" ]; then
        cp "$1" "$1.easy-install.backup"
    fi
}

function checkInstall {
    if [ "`dpkg-query -s $1 2>/dev/null`" == "" ]; then
        okAndSleep "Installing package $1"
        apt-get install -y $1
    fi
}

INSTALLER_VERSION="1.6"
OS=""
USERADD=`which useradd`
USERMOD=`which usermod`
USERDEL=`which userdel`
GROUPADD=`which groupadd`
MACHINE=`uname -m`
LOCAL_IP=`ip route get 8.8.8.8 | awk '{print $NF; exit}'`

if [ "$LOCAL_IP" == "" ]; then
    HOST_NAME=`hostname -f | awk '{print tolower($0)}'`
else
    HOST_NAME=`getent hosts $LOCAL_IP | awk '{print tolower($2)}' | head -n 1`
fi

cyanMessage "Controllando per l'utima versione dell'installer"
LATEST_VERSION=`wget -q --timeout=60 -O - http://l.easy-wi.com/installer_version.php | sed 's/^\xef\xbb\xbf//g'`

if [ "`printf "${LATEST_VERSION}\n${INSTALLER_VERSION}" | sort -V | tail -n 1`" != "$INSTALLER_VERSION" ]; then
    errorAndExit "Stai utilizzado la versione ${INSTALLER_VERSION}. Perfavore effttua l'upgrade alla versione ${LATEST_VERSION} e riprova."
else
    okAndSleep "Stai utilizzando la versione aggiornata dell'installer ${INSTALLER_VERSION}."
fi

# We need to be root to install and update
if [ "`id -u`" != "0" ]; then
    cyanMessage "Per poter proseguire con l'installazione è necessario utilizzare l'account di root"
    su -
fi

if [ "`id -u`" != "0" ]; then
    errorAndExit "Ancora non si hanno i privilegi di root, abortendo"
fi

# Debian and its derivatives store their version at /etc/debian_version
if [ -f /etc/debian_version ]; then

    cyanMessage " "
    okAndSleep "Effettuare l'update dei pacchetti di sistema all'ultima versione? Richiesto, alcune dipendenze potrbbero essere mancanti!"

    OPTIONS=("Si" "Esci")
    select UPDATE_UPGRADE_SYSTEM in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1 ) break;;
            2 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    apt-get update && apt-get upgrade -y && apt-get dist-upgrade -y

    checkInstall curl
    checkInstall debconf-utils
    checkInstall lsb-release

    OS=`lsb_release -i 2> /dev/null | grep 'Distributor' | awk '{print tolower($3)}'`
    OSVERSION=`lsb_release -r 2> /dev/null | grep 'Release' | awk '{print $2}'`
    OSBRANCH=`lsb_release -c 2> /dev/null | grep 'Codename' | awk '{print $2}'`
fi

if [ "$OS" == "" ]; then
    errorAndExit "Errore: Non sono in grado di stabilire il sistema operativo in uso. Attualmente sono supportati solo Debian ed Ubuntu. Abortendo!"
else
    okAndSleep "OS rilevato: $OS"
fi

if [ "$OSBRANCH" == "" ]; then
    errorAndExit "Errore: Non posso rilevare nessuna distro di un OS. Abortendo"
else
    okAndSleep "Distro rilevata: $OSBRANCH"
fi

cyanMessage " "
cyanMessage "Cosa dovrebbe essere installato/preparato?"

OPTIONS=("Server principale di gioco" "Server principale di voce" "Pannello web Easy-WI" "Server principale di spazio web" "MySQL" "Esci")
select OPTION in "${OPTIONS[@]}"; do
    case "$REPLY" in
        1|2|3|4|5 ) break;;
        6 ) errorAndQuit;;
        *) errorAndContinue;;
    esac
done

if [ "$OPTION" == "Easy-WI Webpanel" ]; then
    INSTALL="EW"
elif [ "$OPTION" == "Gameserver Root" ]; then
    INSTALL="GS"
elif [ "$OPTION" == "Voicemaster" ]; then
    INSTALL="VS"
elif [ "$OPTION" == "Webspace Root" ]; then
    INSTALL="WR"
elif [ "$OPTION" == "MySQL" ]; then
    INSTALL="MY"
fi

OTHER_PANEL=""

if [ "$INSTALL" != "VS" ]; then
    if [ -f /etc/init.d/psa ]; then
        OTHER_PANEL="Plesk"
    elif [ -f /usr/local/vesta/bin/v-change-user-password ]; then
        OTHER_PANEL="VestaCP"
    elif [ -d /root/confixx ]; then
        OTHER_PANEL="Confixx"
    elif [ -d /var/www/froxlor ]; then
        OTHER_PANEL="Froxlor"
    elif [ -d /etc/imscp ]; then
        OTHER_PANEL="i-MSCP"
    elif [ -d /usr/local/ispconfig ]; then
        OTHER_PANEL="ISPConfig"
    elif [ -d /var/cpanel ]; then
        OTHER_PANEL="cPanel"
    elif [ -d /usr/local/directadmin ]; then
        OTHER_PANEL="DirectAdmin"
    fi
fi

if [ "$OTHER_PANEL" != "" ]; then
    if [ "$INSTALL" == "GS" ]; then
        yellowMessage " "
        yellowMessage "Attenzione, una installazione del pannello di controllo $OTHER_PANEL è stata rilevata."
        yellowMessage "Se continuerai l'installazione potrebbe interrompersi danneggiando $OTHER_PANEL o alcune parti dell'Easy-WI potrebbero non funzionare."
        OPTIONS=("Continua" "Esci")
        select UPDATE_UPGRADE_SYSTEM in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1 ) break;;
                2 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done
    else
        errorAndExit "Annullando onde prevenire il rischio di danneggiamento del pannello $OTHER_PANEL, rischio troppo elevato"
    fi
fi

# Run the domain/IP check up front to avoid late error out.
if [ "$INSTALL" == "EW" ]; then

    cyanMessage " "
    cyanMessage "In quale URL/Dominio l'Easy-Wi dovrebbe essere posizionato?"
    OPTIONS=("$HOST_NAME" "$LOCAL_IP" "Other" "Quit")
    select OPTION in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1|2|3 ) break;;
            4 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    if [ "$OPTION" == "Other" ]; then
        cyanMessage " "
        cyanMessage "Perfavore, specifica l'IP o il dominio su cui l'Easy-Wi dovrebbe funzionare."
        read IP_DOMAIN
    else
        IP_DOMAIN=$OPTION
    fi

    if [ "`grep -E '\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b' <<< $IP_DOMAIN`" == "" -a "`grep -E '^(([a-zA-Z](-?[a-zA-Z0-9])*)\.)*[a-zA-Z](-?[a-zA-Z0-9])+\.[a-zA-Z]{2,}$' <<< $IP_DOMAIN`" == "" ]; then
        errorAndExit "Errore: $IP_DOMAIN non è né un dominio né un IPv4!"
    fi
fi

# Run the TS3 server version detect up front to avoid user executing steps first and fail at download last.
if [ "$INSTALL" == "VS" ]; then

    if [ "$MACHINE" == "x86_64" ]; then
        ARCH="amd64"
    elif [ "$MACHINE" == "i386" ]||[ "$MACHINE" == "i686" ]; then
        ARCH="x86"
    else
        errorAndExit "$MACHINE non è supportata!"
    fi

    okAndSleep "Cercando l'ultima build per hardware di tipo $MACHINE con architettura $ARCH."

    for VERSION in `curl -s "http://dl.4players.de/ts/releases/?C=M;O=D" | grep -Po '(?<=href=")[0-9]+(\.[0-9]+){2,3}(?=/")' | sort -Vr`; do

        DOWNLOAD_URL_VERSION="http://dl.4players.de/ts/releases/$VERSION/teamspeak3-server_linux_$ARCH-$VERSION.tar.bz2"
        STATUS=`curl -I $DOWNLOAD_URL_VERSION 2>&1 | grep "HTTP/" | awk '{print $2}'`

        if [ "$STATUS" == "200" ]; then
            DOWNLOAD_URL=$DOWNLOAD_URL_VERSION
            break
        fi
    done

    if [ "$STATUS" == "200" -a "$DOWNLOAD_URL" != "" ]; then
        okAndSleep "Rilevata l'ultima versione del server con versione $VERSION all'URL di download $DOWNLOAD_URL"
    else
        errorAndExit "Non posso rilevare l'ultima versione del server"
    fi
fi

# If we need to install and configure a webspace than we need to identify the groupID
if [ "$INSTALL" == "EW" -o  "$INSTALL" == "WR" ]; then

    WEBGROUPID=`getent group www-data | awk -F ':' '{print $3}'`

    if [ "$INSTALL" == "EW" ]; then
        OPTION="Si"
    else
        cyanMessage " "
        cyanMessage "Trovato il gruppo www-data con gruppo ID $WEBGROUPID. Usarlo come gruppo per i server web?"

        OPTIONS=("Si" "No" "Esci")
        select OPTION in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1|2 ) break;;
                3 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done
    fi

    if [ "$OPTION" == "No" ]; then

        cyanMessage "Perfavore scrivi il nome del gruppo da utilizzare per i server web"
        read WEBGROUP

        WEBGROUPID=`getent group $WEBGROUP | awk -F ':' '{print $3}'`

        if [ "$WEBGROUPID" == "" ]; then
            $GROUPADD $WEBGROUP
            WEBGROUPID=`getent group $WEBGROUP | awk -F ':' '{print $3}'`
        fi
    fi

    if [ "$WEBGROUPID" == "" ]; then
        errorAndExit "Errore fatale: ID del server web mancante"
    fi
fi

if [ "$INSTALL" != "MY" ]; then

    cyanMessage "Perfavore inserisci il nome dell'utente principale. Se non esiste, l'installer provvederà a crearlo."
    read MASTERUSER

    if [ "$MASTERUSER" == "" ]; then
        errorAndExit "Errore fatale: Nessun nome utente specificato"
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

    elif [ "$INSTALL" != "VS" -a "$INSTALL" != "MY" ]; then

        okAndSleep "Utente \"$MASTERUSER\" trovato, applicandogli \"$MASTERUSER\" come grppo principale"

        if [ "$INSTALL" == "EW" -o  "$INSTALL" == "WR" ]; then
            $USERMOD -G $WEBGROUPID $MASTERUSER
        else

            if [ "`getent group $MASTERUSER`" == "" ]; then
                $GROUPADD $MASTERUSER
            fi

            $USERMOD -G $MASTERUSER $MASTERUSER
        fi
    else
        okAndSleep "L'utente \"$MASTERUSER\" è già esistente."
    fi

    cyanMessage " "
    cyanMessage "Creare una chiave o impostare una password per il login?"
    cyanMessage "Il modo più sicuro di effettuare il login è mediante una chiave protetta da password."

    if [ "$INSTALL" == "EW" ]; then
        cyanMessage "Nessuno dei due è richiesto per l'installazione del pannello web dell' Easy-WI."
    fi

    OPTIONS=("Crea una chiave" "Imposta una password" "Salta" "Esci")
    select OPTION in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1|2|3 ) break;;
            4 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    if [ "$OPTION" == "Crea una chiave" ]; then

        if [ -d /home/$MASTERUSER/.ssh ]; then
            rm -rf /home/$MASTERUSER/.ssh
            mkdir -p /home/$MASTERUSER/.ssh
            chown $MASTERUSER:$MASTERUSER /home/$MASTERUSER/.ssh
        fi

        cyanMessage " "
        cyanMessage "È raccomandato ma non richesto impostare una password"
        su -c "ssh-keygen -t rsa" $MASTERUSER

        cd /home/$MASTERUSER/.ssh

        KEYNAME=`find -maxdepth 1 -name "*.pub" | head -n 1`

        if [ "$KEYNAME" != "" ]; then
            su -c "cat $KEYNAME >> authorized_keys" $MASTERUSER
        else
            redMessage "Errore: non posso trovare una chiave. Potresti dover crearne una manualemnte più avanti."
        fi

    elif [ "$OPTION" == "Imposta una password" ]; then
        passwd $MASTERUSER
    fi
fi

# only in case we want to manage webspace we need the additional skel dir
if [ "$INSTALL" == "WR" -o "$INSTALL" == "EW" ]; then
    makeDir /home/$MASTERUSER/sites-enabled/
    makeDir /home/$MASTERUSER/skel/htdocs
    makeDir /home/$MASTERUSER/skel/logs
    makeDir /home/$MASTERUSER/skel/session
    makeDir /home/$MASTERUSER/skel/tmp
fi

if [ "$INSTALL" == "EW" -o "$INSTALL" == "WR" -o "$INSTALL" == "MY" ]; then

    if [ "$OS" == "debian" -a "$INSTALL" != "MY" ]; then

        cyanMessage " "
        cyanMessage "Utilizzare la repo dotdeb.org per le aggiornare le versioni di PHP?"

        OPTIONS=("Si" "No" "Esci")
        select DOTDEB in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1|2 ) break;;
                3 ) errorAndQuit;;
                 *) errorAndContinue;;
            esac
        done

        if [ "$DOTDEB" == "Si" ]; then
            if [ "`grep 'packages.dotdeb.org' /etc/apt/sources.list`" == "" ]; then

                okAndSleep "Aggiungendo le voci /etc/apt/sources.list"

                if [ "$OSBRANCH" == "squeeze" -o "$OSBRANCH" == "wheezy" ]; then
                    checkInstall python-software-properties
                elif [ "$OSBRANCH" == "jessie" ]; then
                    checkInstall software-properties-common
                fi

                add-apt-repository "deb http://packages.dotdeb.org $OSBRANCH all"
                add-apt-repository "deb-src http://packages.dotdeb.org $OSBRANCH all"
                curl --remote-name http://www.dotdeb.org/dotdeb.gpg
                apt-key add dotdeb.gpg
                removeIfExists dotdeb.gpg
                apt-get update
             fi
        fi
    fi

    if [ "$INSTALL" != "MY" ]; then
        cyanMessage " "
        cyanMessage "Perfavore selezione il server web che vorresti utilizzare"
    fi

    if [ "$INSTALL" == "EW" ]; then

        cyanMessage "Apache è raccomandato in caso si vogliano utilizzare ulteriori siti soltre a questo host."
        cyanMessage "Nginx è raccomandato se il server dovrebbe solo eseguire il pannello web Easy-WI."

        OPTIONS=("Nginx" "Apache" "Esci")
        select WEBSERVER in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1|2 ) break;;
                3 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done

    elif [ "$INSTALL" != "MY" ]; then

        cyanMessage "Nginx è raccomandato per per il FastDL e un pò più efficente per i vhosts"
        cyanMessage "Apache è raccomandato in caso in caso si voglia eseguire più siti in PHP supportando Vhosts ovvero hosting web in massa"

        OPTIONS=("Nginx" "Apache" "Lighttpd" "Nessuno" "Esci")
        select WEBSERVER in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1|2|3|4 ) break;;
                5 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done
    fi

    if [ "$WEBSERVER" == "Nginx" -a "$INSTALL" != "MY" ]; then
        checkInstall nginx-full
    elif [ "$WEBSERVER" == "Lighttpd" -a "$INSTALL" != "MY" ]; then
        checkInstall lighttpd
    elif [ "$WEBSERVER" == "Apache" -a "$INSTALL" != "MY" ]; then
        checkInstall apache2
    fi

    if [ "$INSTALL" == "EW" ]; then

        okAndSleep "Perfavore, prendi nota del fatto che l'Easy-Wi richiede MySQL o MariaDB installato, ed installerà MySQL se nessun servizio risulterà installato"

        if [ "`ps ax | grep mysql | grep -v grep`" == "" ]; then
            SQL="MySQL"
        else
            SQL=""
        fi

    else

        cyanMessage " "
        cyanMessage "Perfavore selezione quale database si desidera installare."
        cyanMessage "Seleziona \"Nessuno\" nel caso il server debba essere utilizzato solamente come spazio web Fastdownload."

        OPTIONS=("MySQL" "MariaDB" "Nessuno" "Esci")
        select SQL in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1|2|3 ) break;;
                4 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done
    fi

    if [ "$SQL" == "MySQL" -o "$SQL" == "MariaDB" ]; then
        if [ "`ps fax | grep 'mysqld' | grep -v 'grep'`" ]; then

            cyanMessage " "
            cyanMessage "Perfavore fornisci ;a password di root per il database."
            read MYSQL_ROOT_PASSWORD

            mysql -uroot -p$MYSQL_ROOT_PASSWORD -e exit 2> /dev/null
            ERROR_CODE=$?

            until [ $ERROR_CODE == 0 ]; do

                cyanMessage "Password non corretta, perfavore fornisci la password di root per il database."
                read MYSQL_ROOT_PASSWORD

                mysql -uroot -p$MYSQL_ROOT_PASSWORD -e exit 2> /dev/null
                ERROR_CODE=$?
            done

        else
            until [ "$MYSQL_ROOT_PASSWORD" != "" ]; do
                cyanMessage "Perfavore fornisci la passwrod di root per il database."
                read MYSQL_ROOT_PASSWORD
            done
        fi

        export DEBIAN_FRONTEND="noninteractive"
        echo "mysql-server mysql-server/root_password password $MYSQL_ROOT_PASSWORD" | debconf-set-selections
        echo "mysql-server mysql-server/root_password_again password $MYSQL_ROOT_PASSWORD" | debconf-set-selections
    fi

    if [ "$SQL" == "MySQL" ]; then

        apt-get install mysql-server mysql-client mysql-common -y

    elif [ "$SQL" == "MariaDB" ]; then

        RUNUPDATE=0

        if ([ "`printf "${OSVERSION}\n8.0" | sort -V | tail -n 1`" == "8.0" -o "$OS" == "ubuntu" ] && [ "`grep '/mariadb/' /etc/apt/sources.list`" == "" ]); then

            checkInstall python-software-properties
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

        if [ "$OS" == "debian" -a "$DOTDEB" == "Si" ]; then
            echo "Package: *" > /etc/apt/preferences.d/mariadb.pref
            echo "Pin: origin mirror.netcologne.de" >> /etc/apt/preferences.d/mariadb.pref
            echo "Pin-Priority: 1000" >> /etc/apt/preferences.d/mariadb.pref
            RUNUPDATE=1
        fi

        if [ "$RUNUPDATE" == "1" ]; then
            apt-get update
        fi

        if ([ "`printf "${OSVERSION}\n8.0" | sort -V | tail -n 1`" == "8.0" -o "$OS" == "ubuntu" ] && [ "`grep '/mariadb/' /etc/apt/sources.list`" == "" ]); then
            apt-get install mariadb-server mariadb-client mysql-common -y
        else
            apt-get install mariadb-server mariadb-client mariadb-common -y
        fi
    fi

    if [ "$SQL" == "MySQL" -o "$SQL" == "MariaDB" ]; then

        if [ "$INSTALL" == "WR" -o "$INSTALL" == "MY" ]; then

            cyanMessage " "
            cyanMessage "Il pannello Easy-Wi è installato su un server diverso da questo?."

            OPTIONS=("Si" "No" "Esci")
            select EXTERNAL_INSTALL in "${OPTIONS[@]}"; do
                case "$REPLY" in
                    1|2 ) break;;
                    3 ) errorAndQuit;;
                    *) errorAndContinue;;
                esac
            done
        fi

        cyanMessage " "
        okAndSleep "Securing MySQL by running \"mysql_secure_installation\" commands."
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "DELETE FROM mysql.user WHERE User=''" 2> /dev/null
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1')" 2> /dev/null
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\_%'" 2> /dev/null
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "FLUSH PRIVILEGES" 2> /dev/null
    fi

    if [ "$EXTERNAL_INSTALL" == "Si" ]; then

        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "GRANT USAGE ON *.* TO 'root'@'' IDENTIFIED BY '$MYSQL_ROOT_PASSWORD' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0" 2> /dev/null
        mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "UPDATE mysql.user SET Select_priv='Y',Insert_priv='Y',Update_priv='Y',Delete_priv='Y',Create_priv='Y',Drop_priv='Y',Reload_priv='Y',Shutdown_priv='Y',Process_priv='Y',File_priv='Y',Grant_priv='Y',References_priv='Y',Index_priv='Y',Alter_priv='Y',Show_db_priv='Y',Super_priv='Y',Create_tmp_table_priv='Y',Lock_tables_priv='Y',Execute_priv='Y',Repl_slave_priv='Y',Repl_client_priv='Y',Create_view_priv='Y',Show_view_priv='Y',Create_routine_priv='Y',Alter_routine_priv='Y',Create_user_priv='Y',Event_priv='Y',Trigger_priv='Y',Create_tablespace_priv='Y' WHERE User='root' AND Host=''" 2> /dev/null

        if [ "$LOCAL_IP" == "" ]; then

            cyanMessage " "
            cyanMessage "Non posso riconoscere l'IP locale. Perfavore specifica quale usare."
            read LOCAL_IP
        fi

        if [ "$LOCAL_IP" != "" -a -f /etc/mysql/my.cnf ]; then
            if [ "`grep 'bind-address' /etc/mysql/my.cnf`" ]; then
                sed -i "s/bind-address.*/bind-address = 0.0.0.0/g" /etc/mysql/my.cnf
            else
                echo "bind-address = 0.0.0.0" >> /etc/mysql/my.cnf
            fi
        fi
    fi

    MYSQL_VERSION=`mysql -V | awk {'print $5'} | tr -d ,`

    if [ "`grep -E 'key_buffer[[:space:]]*=' /etc/mysql/my.cnf`" != "" -a "printf "${MYSQL_VERSION}\n5.5" | sort -V | tail -n 1" != 5.5 ]; then
        sed -i "s/key_buffer[[:space:]]*=/key_buffer_size = /g" /etc/mysql/my.cnf
    fi

    /etc/init.d/mysql restart

    if [ "$INSTALL" == "EW" -a "`ps ax | grep mysql | grep -v grep`" == "" ]; then
        cyanMessage " "
        errorAndExit "Errore: nessun server SQL è attualemte in esecuzione, tuttavia questo è richiesto dal pannello web."
    fi

    if [ "$INSTALL" == "EW" ]; then

        okAndSleep "Perfavore, prendere nota del fatto che l'Easy-Wi installerà i pacchetti PHP richiesti."
        PHPINSTALL="Si"

    elif [ "$INSTALL" != "MY" ]; then

        cyanMessage " "
        cyanMessage "Installare/Aggiornare PHP?"
        cyanMessage "Selezionare \"Nessuno\" nel caso il server debba ospitare solo webserver Fastdownload."

        OPTIONS=("Si" "No" "Nessuno" "Esci")
        select PHPINSTALL in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1|2|3 ) break;;
                4 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done
    fi

    if [ "$PHPINSTALL" == "Si" ]; then

        USE_PHP_VERSION='5'

        if [ "$OS" == "ubuntu" -a "`printf "${OSVERSION}\n16.03" | sort -V | tail -n 1`" != "16.03" ]; then
            USE_PHP_VERSION='7.0'
        fi 

        if [ "$OS" == "debian" -a "$DOTDEB" == "Si" ]; then

            cyanMessage " "

            if [ "$OSBRANCH" == "wheezy" ]; then

                cyanMessage "Quale versione di PHP dovrebbe essere utilizzata?"

                OPTIONS=("5.4" "5.5", "5.6", "5.6 Zend thread safety" "Esci")
                select DOTDEBPHPUPGRADE in "${OPTIONS[@]}"; do
                    case "$REPLY" in
                        1|2|3|4 ) break;;
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

            if [ "$DOTDEBPHPUPGRADE" == "Si" ]; then
                apt-get update
                apt-get upgrade -y && apt-get dist-upgrade -y
            fi
        fi

        checkInstall php${USE_PHP_VERSION}-common
        checkInstall php${USE_PHP_VERSION}-curl
        checkInstall php${USE_PHP_VERSION}-gd
        checkInstall php${USE_PHP_VERSION}-mcrypt
        checkInstall php${USE_PHP_VERSION}-mysql
        checkInstall php${USE_PHP_VERSION}-cli

        if [ "$WEBSERVER" == "Nginx" -o "$WEBSERVER" == "Lighttpd" ]; then

            checkInstall php${USE_PHP_VERSION}-fpm

            if [ "$WEBSERVER" == "Lighttpd" ]; then
                lighttpd-enable-mod fastcgi
                lighttpd-enable-mod fastcgi-php
            fi

            makeDir /home/$MASTERUSER/fpm-pool.d/
            sed -i "s/include=\/etc\/php5\/fpm\/pool.d\/\*.conf/include=\/home\/$MASTERUSER\/fpm-pool.d\/\*.conf/g" /etc/php5/fpm/php-fpm.conf
            sed -i "s/include=\/etc\/php/7.0\/fpm\/pool.d\/\*.conf/include=\/home\/$MASTERUSER\/fpm-pool.d\/\*.conf/g" /etc/php/7.0/fpm/php-fpm.conf

        elif [ "$WEBSERVER" == "Apache" ]; then
            checkInstall apache2-mpm-itk
            checkInstall libapache2-mpm-itk
            checkInstall libapache2-mod-php${USE_PHP_VERSION}
            checkInstall php${USE_PHP_VERSION}
            a2enmod php${USE_PHP_VERSION}
        fi
        
        #In case of php 7 the socket is different
        PHP_VERSION=`php -v | grep -E '^PHP' | awk '{print $2}' | awk -F '.' '{print $1}'`
        PHP_SOCKET="/var/run/php${PHP_VERSION}-fpm-${FILE_NAME}.sock"
    fi
fi

if ([ "$INSTALL" == "WR" -o "$INSTALL" == "EW" ] && [ "`grep '/bin/false' /etc/shells`" == "" ]); then
    echo "/bin/false" >> /etc/shells
fi

if [ "$INSTALL" != "VS" -a "$INSTALL" != "EW" -a "$INSTALL" != "MY" ]; then

    cyanMessage " "
    cyanMessage "Installare/Aggiornare ProFTPD?"

    OPTIONS=("Si" "No" "Esci")
    select OPTION in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1|2 ) break;;
            3 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    if [ "$OPTION" == "Si" ]; then

        echo "proftpd-basic shared/proftpd/inetd_or_standalone select standalone" | debconf-set-selections
        apt-get install proftpd -y

        if [ -f /etc/proftpd/modules.conf ]; then

            backUpFile /etc/proftpd/modules.conf

            sed -i 's/.*LoadModule mod_tls_memcache.c.*/#LoadModule mod_tls_memcache.c/g' /etc/proftpd/modules.conf
        fi

        backUpFile /etc/proftpd/proftpd.conf

        sed -i 's/.*UseIPv6.*/UseIPv6 off/g' /etc/proftpd/proftpd.conf
        sed -i 's/#.*DefaultRoot.*~/DefaultRoot ~/g' /etc/proftpd/proftpd.conf
        sed -i 's/# RequireValidShell.*/RequireValidShell on/g' /etc/proftpd/proftpd.conf

        if [ -f /etc/proftpd/proftpd.conf -a "$INSTALL" != "GS" ]; then

            sed -i 's/Umask.*/Umask 037 027/g' /etc/proftpd/proftpd.conf

        elif [ -f /etc/proftpd/proftpd.conf -a "$INSTALL" == "GS" ]; then

            sed -i 's/Umask.*/Umask 077 077/g' /etc/proftpd/proftpd.conf

            cyanMessage " "
            cyanMessage "Install/Update ProFTPD Rules?"

            OPTIONS=("Si" "No" "Esci")
            select OPTION in "${OPTIONS[@]}"; do
                case "$REPLY" in
                    1|2 ) break;;
                    3 ) errorAndQuit;;
                    *) errorAndContinue;;
                esac
            done

            if [ "$OPTION" == "Si" -a "`grep '<Directory \/home\/\*\/pserver\/\*>' /etc/proftpd/proftpd.conf`" == "" -a ! -f "/etc/proftpd/conf.d/easy-wi.conf" ]; then

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
<Directory ~/*/*/>
    HideFiles (^\..+|srcds_run|srcds_linux|hlds_run|hlds_amd|hlds_i686|\.rc|\.sh|\.zip|\.rar|\.7z|\.dll)$
    PathDenyFilter (^\..+|srcds_run|srcds_linux|hlds_run|hlds_amd|hlds_i686|\.rc|\.sh|\.zip|\.rar|\.7z|\.dll)$
    HideNoAccess on
</Directory>" >> /etc/proftpd/conf.d/easy-wi.conf


                GAMES=("ark" "arma3" "bukkit" "hexxit" "mc" "mtasa" "projectcars" "rust" "samp" "spigot" "teeworlds" "tekkit" "tekkit-classic")

                for GAME in ${GAMES[@]}; do
                    echo "<Directory ~/server/*/$GAME*/*>
    Umask 077 077
    <Limit RNFR RNTO STOR DELE MKD RMD>
        AllowAll
    </Limit>
</Directory>" >> /etc/proftpd/conf.d/easy-wi.conf
                done

                GAME_MODS=("csgo" "cstrike" "czero" "orangebox" "dod" "garrysmod")

                for GAME_MOD in ${GAME_MODS[@]}; do
                    echo "<Directory ~/server/*/*/${GAME_MOD}/*>
    Umask 077 077
    <Limit RNFR RNTO STOR DELE MKD RMD>
        AllowAll
    </Limit>
</Directory>" >> /etc/proftpd/conf.d/easy-wi.conf
                done

                FOLDERS=("addons" "cfg" "maps")

                for FOLDER in ${FOLDERS[@]}; do
                    echo "<Directory ~/*/*/*/*/${FOLDER}>
    Umask 077 077
    <Limit RNFR RNTO STOR DELE>
        AllowAll
    </Limit>
</Directory>
<Directory ~/*/*/*/${FOLDER}>
    Umask 077 077
    <Limit RNFR RNTO STOR DELE MKD RMD>
        AllowAll
    </Limit>
</Directory>" >> /etc/proftpd/conf.d/easy-wi.conf
                done
            fi
        fi

        if [ -f /etc/init.d/proftpd ]; then
            service proftpd restart
        fi
    fi
fi

if [ "$INSTALL" == "GS" -o "$INSTALL" == "WR" ]; then

    cyanMessage " "
    cyanMessage "Installare Quota?"

    OPTIONS=("Si" "No" "Esci")
    select QUOTAINSTALL in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1|2 ) break;;
            3 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    if [ "$QUOTAINSTALL" == "Si" ]; then

        checkInstall quota

        cyanMessage " "
        cyanMessage " "
        removeIfExists /root/tempfstab
        removeIfExists /root/tempmountpoints

        cat /etc/fstab | while read LINE; do
            if [[ `echo $LINE | grep '/' | egrep -v '#|boot|proc|swap|floppy|cdrom|usrquota|usrjquota|/sys|/shm|/pts'` ]]; then
                CURRENTOPTIONS=`echo $LINE | awk '{print $4}'`
                echo $LINE | sed "s/$CURRENTOPTIONS/$CURRENTOPTIONS,usrjquota=aquota.user,jqfmt=vfsv0/g" >> /root/tempfstab
                echo $LINE | awk '{print $2}' >> /root/tempmountpoints
            else
                echo $LINE >> /root/tempfstab
            fi
        done

        cat /root/tempfstab

        cyanMessage " "
        cyanMessage " "
        cyanMessage "perfavore, controlla l'output sovrestante ed assicurati che sia corretto. Alla conferma, il file /etc/fstab sarà sostituito per poter attivare Quotas!"

        OPTIONS=("Si" "No" "Esci")
        select QUOTAFSTAB in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1|2 ) break;;
                3 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done

        if [ "$QUOTAFSTAB" == "Si" ]; then
            backUpFile /etc/fstab
            mv /root/tempfstab /etc/fstab
        fi

        removeIfExists /root/tempfstab
        removeIfExists /aquota.user
        touch /aquota.user
        chmod 600 /aquota.user

        if [ -f /root/tempmountpoints ]; then

            cat /root/tempmountpoints | while read LINE; do

                quotaoff -ugv $LINE

                removeIfExists $LINE/aquota.user

                okAndSleep "Rimontando $LINE"
                mount -o remount $LINE

                quotacheck -vumc $LINE
                quotaon -uv $LINE
            done

            removeIfExists /root/tempmountpoints
        fi
    fi
fi

if [ "$INSTALL" == "WR" -o "$INSTALL" == "EW" ]; then

    if [ "$WEBSERVER" == "Nginx" ]; then

        backUpFile /etc/nginx/nginx.conf

        if [ "`grep '/home/$MASTERUSER/sites-enabled/' /etc/nginx/nginx.conf`" == "" ]; then
            sed -i "\/etc\/nginx\/sites-enabled\/\*;/a \ \ \ \ \ \ \ \ include \/home\/$MASTERUSER\/sites-enabled\/\*;" /etc/nginx/nginx.conf
        fi

    elif [ "$WEBSERVER" == "Lighttpd" ]; then

        backUpFile /etc/lighttpd/lighttpd.conf
        echo "include_shell \"find /home/$MASTERUSER/sites-enabled/ -maxdepth 1 -type f -exec cat {} \;\"" >> /etc/lighttpd/lighttpd.conf

    elif [ "$WEBSERVER" == "Apache" ]; then

        backUpFile /etc/apache2/apache2.conf

        if [ "`grep 'ServerName localhost' /etc/apache2/apache2.conf`" == "" ]; then
            echo '# Added to prevent error message Could not reliably determine the servers fully qualified domain name' >> /etc/apache2/apache2.conf
            echo 'ServerName localhost' >> /etc/apache2/apache2.conf
        fi

        APACHE_VERSION=`apache2 -v | grep 'Server version'`

        if [ "`grep '/home/$MASTERUSER/sites-enabled/' /etc/apache2/apache2.conf`" == "" ]; then
            if [[ $APACHE_VERSION =~ .*Apache/2.2.* ]]; then
                sed -i "/Include sites-enabled\//a Include \/home\/$MASTERUSER\/sites-enabled\/" /etc/apache2/apache2.conf
                sed -i "/Include \/etc\/apache2\/sites-enabled\//a \/home\/$MASTERUSER\/sites-enabled\/" /etc/apache2/apache2.conf
            else
                sed -i "/IncludeOptional sites-enabled\//a IncludeOptional \/home\/$MASTERUSER\/sites-enabled\/*.conf" /etc/apache2/apache2.conf
                sed -i "/IncludeOptional \/etc\/apache2\/sites-enabled\//a IncludeOptional \/home\/$MASTERUSER\/sites-enabled\/*.conf" /etc/apache2/apache2.conf
            fi
        fi

        okAndSleep "Attivando il modulo mod_rewrite di Apache."
        a2enmod rewrite
        a2enmod version 2> /dev/null
    fi

    #TODO: Logrotate
fi

# No direct root access for masteruser. Only limited access through sudo
if [ "$INSTALL" == "GS" -o  "$INSTALL" == "WR" ]; then

    checkInstall sudo

    if [ -f /etc/sudoers -a "`grep $MASTERUSER /etc/sudoers | grep $USERADD`" == "" ]; then
        echo "$MASTERUSER ALL = NOPASSWD: $USERADD" >> /etc/sudoers
    fi

    if [ -f /etc/sudoers -a "`grep $MASTERUSER /etc/sudoers | grep $USERMOD`" == "" ]; then
        echo "$MASTERUSER ALL = NOPASSWD: $USERMOD" >> /etc/sudoers
    fi

    if [ -f /etc/sudoers -a "`grep $MASTERUSER /etc/sudoers | grep $USERDEL`" == "" ]; then
        echo "$MASTERUSER ALL = NOPASSWD: $USERDEL" >> /etc/sudoers
    fi

    if [ "$QUOTAINSTALL" == "Si" -a -f /etc/sudoers ]; then
        if [ "`grep $MASTERUSER /etc/sudoers | grep setquota`" == "" ]; then
            echo "$MASTERUSER ALL = NOPASSWD: `which setquota`" >> /etc/sudoers
        fi

        if [ "`grep $MASTERUSER /etc/sudoers | grep repquota`" == "" ]; then
            echo "$MASTERUSER ALL = NOPASSWD: `which repquota`" >> /etc/sudoers
        fi
    fi

    if [ "$INSTALL" == "GS" -a -f /etc/sudoers -a "`grep $MASTERUSER /etc/sudoers | grep temp`" == "" ]; then
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

    if [ "$HTTPDBIN" != "" -a -f /etc/sudoers ]; then
        if [ "`grep $MASTERUSER /etc/sudoers | grep $HTTPDBIN`" == "" ]; then
            echo "$MASTERUSER ALL = NOPASSWD: $HTTPDBIN" >> /etc/sudoers
        fi

        if [ "`grep $MASTERUSER /etc/sudoers | grep $HTTPDSCRIPT`" == "" ]; then
            echo "$MASTERUSER ALL = NOPASSWD: $HTTPDSCRIPT" >> /etc/sudoers
        fi
    fi
fi

if [ "$INSTALL" == "WR" ]; then

    chown -R $MASTERUSER:$WEBGROUPID /home/$MASTERUSER/

    greenMessage "I seguenti dati devono essere inseriti nel pannello Easy-Wi:"

    greenMessage "Il percorso alla cartella \"sites-enabled\" è:"
    greenMessage "/home/$MASTERUSER/sites-enabled/"

    greenMessage "Il comando per aggiungere un utente è:"
    greenMessage "sudo $USERADD %cmd%"

    greenMessage "Il comando usermod è:"
    greenMessage "sudo $USERMOD %cmd%"

    greenMessage "Il comando userdel è:"
    greenMessage "sudo $USERDEL %cmd%"

    greenMessage "Il comando per riavviare HTTPD è:"
    greenMessage "sudo $HTTPDSCRIPT reload"
fi

if ([ "$INSTALL" == "GS" -o "$INSTALL" == "WR" ] && [ "$QUOTAINSTALL" == "Si" ]); then
    greenMessage "Il comapndo per impostare i quota è:"
    greenMessage "sudo `which setquota` %cmd%"
    greenMessage "Il comando per soostituire i quota è:"
    greenMessage "sudo `which repquota` %cmd%"
fi

if [ "$INSTALL" == "GS" ]; then

    cyanMessage " "
    cyanMessage "Java JRE è richiesto per eseguire Minecraft e le sue mod. Dovrebbe essere installato?"
    OPTIONS=("Si" "No" "Esci")
    select OPTION in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1|2 ) break;;
            3 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    if [ "$OPTION" == "Si" ]; then
        checkInstall default-jre
    fi

    okAndSleep "Crando le cartelle ed i files"
    CREATEDIRS=("conf" "fdl_data/hl2" "logs" "masteraddons" "mastermaps" "masterserver" "temp")
    for CREATEDIR in ${CREATEDIRS[@]}; do
        greenMessage "Aggiungendo la directory: /home/$MASTERUSER/$CREATEDIR"
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

        okAndSleep "installando i pacchetti richiesti: wput screen bzip2 sudo rsync zip unzip"
        apt-get install wput screen bzip2 sudo rsync zip unzip -y

        if [ "`uname -m`" == "x86_64" ]; then

            okAndSleep "Installando il supporto a 32bit per i sistemi a 64bit."

            if ([ "$OS" == "ubuntu" ] || [ "$OS" == "debian" -a "`printf "${OSVERSION}\n8.0" | sort -V | tail -n 1`" == "$OSVERSION" ]); then
                apt-get install zlib1g lib32z1 lib32gcc1 lib32readline5 lib32ncursesw5 -y
                apt-get install lib32stdc++6 -y
                apt-get install lib64stdc++6 -y
                apt-get install libstdc++6 -y
                apt-get install libgcc1:i386 -y
                apt-get install libreadline5:i386 -y
                apt-get install libncursesw5:i386 -y
                apt-get install zlib1g:i386 -y
            else
                apt-get install ia32-libs lib32readline5 lib32ncursesw5 lib32stdc++6 -y
            fi
        else
            apt-get install libreadline5 libncursesw5 -y
        fi
    fi

    okAndSleep "Scaricando SteamCmd"

    cd /home/$MASTERUSER/masterserver
    makeDir /home/$MASTERUSER/masterserver/steamCMD/
    cd /home/$MASTERUSER/masterserver/steamCMD/
    curl --remote-name http://media.steampowered.com/client/steamcmd_linux.tar.gz

    if [ -f steamcmd_linux.tar.gz ]; then
        tar xfvz steamcmd_linux.tar.gz
        removeIfExists steamcmd_linux.tar.gz
        chown -R $MASTERUSER:$MASTERUSER /home/$MASTERUSER/masterserver/steamCMD
        su -c "./steamcmd.sh +login anonymous +quit" $MASTERUSER
        if [ -f /home/$MASTERUSER/masterserver/steamCMD/linux32/steamclient.so ]; then
            su -c "mkdir -p ~/.steam/sdk32/" $MASTERUSER
            su -c "chmod 750 -R ~/.steam/" $MASTERUSER
            su -c "ln -s ~/masterserver/steamCMD/linux32/steamclient.so ~/.steam/sdk32/steamclient.so" $MASTERUSER
        fi
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

        service cron restart
    fi
fi

if [ "$INSTALL" == "EW" ]; then

    makeDir /home/$MASTERUSER/fpm-pool.d/

    if [ -f /home/easywi_web/htdocs/serverallocation.php ]; then

        cyanMessage " "
        cyanMessage "È già presente una installazione eistente. Dovrebbe essere rimossa?"
        OPTIONS=("Si" "Esci")
        select OPTION in "${OPTIONS[@]}"; do
            case "$REPLY" in
                1 ) break;;
                2 ) errorAndQuit;;
                *) errorAndContinue;;
            esac
        done

        rm -rf /home/easywi_web/htdocs/*
    fi

    if [ "`id easywi_web 2> /dev/null`" == "" -a ! -d /home/easywi_web ]; then
        $USERADD -md /home/easywi_web -g www-data -s /bin/bash -k /home/$MASTERUSER/skel/ easywi_web
    elif [ "`id easywi_web 2> /dev/null`" == "" -a -d /home/easywi_web ]; then
        $USERADD -d /home/easywi_web -g www-data -s /bin/bash easywi_web
    fi

    makeDir /home/easywi_web/htdocs/
    makeDir /home/easywi_web/logs/
    makeDir /home/easywi_web/tmp/
    makeDir /home/easywi_web/session/
    chown -R easywi_web:$WEBGROUPID /home/easywi_web/htdocs/ /home/easywi_web/logs/ /home/easywi_web/tmp/ /home/easywi_web/session/

    if [ "`id easywi_web 2> /dev/null`" == "" ]; then
        errorAndExit "L'utente web easywi_web non esiste! Uscendo ora!"
    fi

    if [ ! -d /home/easywi_web/htdocs ]; then
        errorAndExit "Nessuna cartella creata in home/htdocs! Uscendo ora!"
    fi

    checkInstall unzip

    cd /home/easywi_web/htdocs/

    okAndSleep "Scaricando l'ultima versione dell'Easy-Wi stabile."
    curl https://easy-wi.com/uk/downloads/get/3/ -o web.zip

    if [ ! -f web.zip ]; then
        errorAndExit "Non posso scaricare l'Easy-Wi. Abortendo!"
    fi

    okAndSleep "Decomprimendo l'archivio contenente l'Easy-WI."
    unzip -u web.zip >/dev/null 2>&1
    removeIfExists web.zip

    find /home/easywi_web/ -type f -print0 | xargs -0 chmod 640
    find /home/easywi_web/ -mindepth 1 -type d -print0 | xargs -0 chmod 750

    chown -R easywi_web:www-data /home/easywi_web

    DB_PASSWORD=`< /dev/urandom tr -dc A-Za-z0-9 | head -c18`

    okAndSleep "Creando il database easy_wi e connettendomici con l'utente easy_wi"
    mysql -uroot -p$MYSQL_ROOT_PASSWORD -Bse "CREATE DATABASE IF NOT EXISTS easy_wi; GRANT ALL ON easy_wi.* TO 'easy_wi'@'localhost' IDENTIFIED BY '$DB_PASSWORD'; FLUSH PRIVILEGES;"

    cyanMessage " "
    cyanMessage "Proteggere i Vhost con SSL? (raccomandato!)"
    OPTIONS=("Si" "No" "Esci")
    select SSL in "${OPTIONS[@]}"; do
        case "$REPLY" in
            1|2 ) break;;
            3 ) errorAndQuit;;
            *) errorAndContinue;;
        esac
    done

    FILE_NAME=${IP_DOMAIN//./_}

    if [ "$SSL" == "Si" ]; then

        checkInstall openssl

        if [ "$WEBSERVER" == "Nginx" ]; then
            SSL_DIR=/etc/nginx/ssl
        elif [ "$WEBSERVER" == "Apache" ]; then
            SSL_DIR=/etc/apache2/ssl
        fi

        makeDir $SSL_DIR

        cyanMessage " "
        okAndSleep "Creando un certificato SSL autofirmato."
        openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout $SSL_DIR/$FILE_NAME.key -out $SSL_DIR/$FILE_NAME.crt -subj "/C=/ST=/L=/O=/OU=/CN=$IP_DOMAIN"
    fi

    if [ "$WEBSERVER" == "Nginx" -o "$WEBSERVER" == "Lighttpd" ]; then

        FILE_NAME_POOL=/home/$MASTERUSER/fpm-pool.d/$FILE_NAME.conf

        echo "[$IP_DOMAIN]" > $FILE_NAME_POOL
        echo "user = easywi_web" >> $FILE_NAME_POOL
        echo "group = www-data" >> $FILE_NAME_POOL
        echo "listen = ${PHP_SOCKET}" >> $FILE_NAME_POOL
        echo "listen.owner = easywi_web" >> $FILE_NAME_POOL
        echo "listen.group = www-data" >> $FILE_NAME_POOL
        echo "pm = dynamic" >> $FILE_NAME_POOL
        echo "pm.max_children = 1" >> $FILE_NAME_POOL
        echo "pm.start_servers = 1" >> $FILE_NAME_POOL
        echo "pm.min_spare_servers = 1" >> $FILE_NAME_POOL
        echo "pm.max_spare_servers = 1" >> $FILE_NAME_POOL
        echo "pm.max_requests = 500" >> $FILE_NAME_POOL
        echo "chdir = /" >> $FILE_NAME_POOL
        echo "access.log = /home/easywi_web/logs/fpm-access.log" >> $FILE_NAME_POOL
        echo "php_flag[display_errors] = off" >> $FILE_NAME_POOL
        echo "php_admin_flag[log_errors] = on" >> $FILE_NAME_POOL
        echo "php_admin_value[error_log] = /home/easywi_web/logs/fpm-error.log" >> $FILE_NAME_POOL
        echo "php_admin_value[memory_limit] = 32M" >> $FILE_NAME_POOL
        echo "php_admin_value[open_basedir] = /home/easywi_web/htdocs/:/home/easywi_web/tmp/" >> $FILE_NAME_POOL
        echo "php_admin_value[upload_tmp_dir] = /home/easywi_web/tmp" >> $FILE_NAME_POOL
        echo "php_admin_value[session.save_path] = /home/easywi_web/session" >> $FILE_NAME_POOL

        chown $MASTERUSER:www-data $FILE_NAME_POOL
    fi

    FILE_NAME_VHOST=/home/$MASTERUSER/sites-enabled/$FILE_NAME

    if [ "$WEBSERVER" == "Nginx" ]; then
        echo 'server {' > $FILE_NAME_VHOST
        echo '    listen 80;' >> $FILE_NAME_VHOST

        if [ "$SSL" == "Si" ]; then

            echo "    server_name $IP_DOMAIN;" >> $FILE_NAME_VHOST
            echo "    return 301 https://$IP_DOMAIN"'$request_uri;' >> $FILE_NAME_VHOST
            echo '}' >> $FILE_NAME_VHOST

            backUpFile /etc/nginx/nginx.conf

            if [ "`grep 'ssl_ecdh_curve secp384r1;' /etc/nginx/nginx.conf`" == "" ]; then
                sed -i '/ssl_prefer_server_ciphers on;/a \\tssl_ecdh_curve secp384r1;' /etc/nginx/nginx.conf
            fi
            if [ "`grep 'ssl_session_cache' /etc/nginx/nginx.conf`" == "" ]; then
                sed -i '/ssl_prefer_server_ciphers on;/a \\tssl_session_cache shared:SSL:10m;' /etc/nginx/nginx.conf
            fi
            if [ "`grep 'ssl_session_timeout' /etc/nginx/nginx.conf`" == "" ]; then
                sed -i '/ssl_prefer_server_ciphers on;/a \\tssl_session_timeout 10m;' /etc/nginx/nginx.conf
            fi
            if [ "`grep 'ssl_ciphers' /etc/nginx/nginx.conf`" == "" ]; then
                sed -i '/ssl_prefer_server_ciphers on;/a \\tssl_ciphers EECDH+AESGCM:EDH+AESGCM:EECDH:EDH:!MD5:!RC4:!LOW:!MEDIUM:!CAMELLIA:!ECDSA:!DES:!DSS:!3DES:!NULL;' /etc/nginx/nginx.conf
            fi

            echo 'server {' >> $FILE_NAME_VHOST
            echo '    listen 443 ssl;' >> $FILE_NAME_VHOST
            echo "    ssl_certificate /etc/nginx/ssl/$FILE_NAME.crt;" >> $FILE_NAME_VHOST
            echo "    ssl_certificate_key /etc/nginx/ssl/$FILE_NAME.key;" >> $FILE_NAME_VHOST
        fi

        echo '    root /home/easywi_web/htdocs/;' >> $FILE_NAME_VHOST
        echo '    index index.html index.htm index.php;' >> $FILE_NAME_VHOST
        echo "    server_name $IP_DOMAIN;" >> $FILE_NAME_VHOST
        echo '    location ~ /(keys|stuff|template|languages|downloads|tmp) { deny all; }' >> $FILE_NAME_VHOST
        echo '    location / {' >> $FILE_NAME_VHOST
        echo '        try_files $uri $uri/ =404;' >> $FILE_NAME_VHOST
        echo '    }' >> $FILE_NAME_VHOST
        echo '    location ~ \.php$ {' >> $FILE_NAME_VHOST
        echo '        fastcgi_split_path_info ^(.+\.php)(/.+)$;' >> $FILE_NAME_VHOST
        echo '        try_files $fastcgi_script_name =404;' >> $FILE_NAME_VHOST
        echo '        set $path_info $fastcgi_path_info;' >> $FILE_NAME_VHOST
        echo '        fastcgi_param PATH_INFO $path_info;' >> $FILE_NAME_VHOST
        echo '        fastcgi_index index.php;' >> $FILE_NAME_VHOST

        if [ -f /etc/nginx/fastcgi.conf ]; then
            echo '        include /etc/nginx/fastcgi.conf;' >> $FILE_NAME_VHOST
        elif [ -f /etc/nginx/fastcgi_params ]; then
            echo '        include /etc/nginx/fastcgi_params;' >> $FILE_NAME_VHOST
        fi

        echo "        fastcgi_pass unix:${PHP_SOCKET};" >> $FILE_NAME_VHOST
        echo '    }' >> $FILE_NAME_VHOST
        echo '}' >> $FILE_NAME_VHOST

        chown -R $MASTERUSER:$WEBGROUPID /home/$MASTERUSER/

        okAndSleep "Riavviando PHP-FPM e Nginx."
        service php${USE_PHP_VERSION}-fpm restart
        service nginx restart

    elif [ "$WEBSERVER" == "Apache" ]; then

        FILE_NAME_VHOST="$FILE_NAME_VHOST.conf"

        echo '<VirtualHost *:80>' > $FILE_NAME_VHOST
        echo "    ServerName $IP_DOMAIN" >> $FILE_NAME_VHOST
        echo "    ServerAdmin info@$IP_DOMAIN" >> $FILE_NAME_VHOST

        if [ "$SSL" == "Si" ]; then

            echo "    Redirect permanent / https://$IP_DOMAIN/" >> $FILE_NAME_VHOST
            echo '</VirtualHost>' >> $FILE_NAME_VHOST

            okAndSleep "Activating TLS/SSL related Apache modules."
            a2enmod ssl
            service apache2 restart

            echo '<VirtualHost *:443>' >> $FILE_NAME_VHOST
            echo "    ServerName $IP_DOMAIN" >> $FILE_NAME_VHOST
            echo '    SSLEngine on' >> $FILE_NAME_VHOST
            echo "    SSLCertificateFile /etc/apache2/ssl/$FILE_NAME.crt" >> $FILE_NAME_VHOST
            echo "    SSLCertificateKeyFile /etc/apache2/ssl/$FILE_NAME.key" >> $FILE_NAME_VHOST

        fi


        echo '    DocumentRoot "/home/easywi_web/htdocs/"' >> $FILE_NAME_VHOST
        echo '    ErrorLog "/home/easywi_web/logs/error.log"' >> $FILE_NAME_VHOST
        echo '    CustomLog "/home/easywi_web/logs/access.log" common' >> $FILE_NAME_VHOST
        echo '    DirectoryIndex index.php index.html' >> $FILE_NAME_VHOST
        echo '    <IfModule mpm_itk_module>' >> $FILE_NAME_VHOST
        echo '       AssignUserId easywi_web www-data' >> $FILE_NAME_VHOST
        echo '       MaxClientsVHost 50' >> $FILE_NAME_VHOST
        echo '       NiceValue 10' >> $FILE_NAME_VHOST
        echo '       php_admin_flag allow_url_include off' >> $FILE_NAME_VHOST
        echo '       php_admin_flag display_errors off' >> $FILE_NAME_VHOST
        echo '       php_admin_flag log_errors on' >> $FILE_NAME_VHOST
        echo '       php_admin_flag mod_rewrite on' >> $FILE_NAME_VHOST
        echo '       php_admin_value open_basedir "/home/easywi_web/htdocs/:/home/easywi_web/tmp"' >> $FILE_NAME_VHOST
        echo '       php_admin_value session.save_path "/home/easywi_web/session"' >> $FILE_NAME_VHOST
        echo '       php_admin_value upload_tmp_dir "/home/easywi_web/tmp"' >> $FILE_NAME_VHOST
        echo '       php_admin_value upload_max_size 32M' >> $FILE_NAME_VHOST
        echo '       php_admin_value memory_limit 32M' >> $FILE_NAME_VHOST
        echo '    </IfModule>' >> $FILE_NAME_VHOST
        echo '    <Directory /home/easywi_web/htdocs/>' >> $FILE_NAME_VHOST
        echo '        Options -Indexes +FollowSymLinks +Includes' >> $FILE_NAME_VHOST
        echo '        AllowOverride None' >> $FILE_NAME_VHOST
        echo '        <IfVersion >= 2.4>' >> $FILE_NAME_VHOST
        echo '            Require all granted' >> $FILE_NAME_VHOST
        echo '        </IfVersion>' >> $FILE_NAME_VHOST
        echo '        <IfVersion < 2.4>' >> $FILE_NAME_VHOST
        echo '            Order allow,deny' >> $FILE_NAME_VHOST
        echo '            Allow from all' >> $FILE_NAME_VHOST
        echo '        </IfVersion>' >> $FILE_NAME_VHOST
        echo '    </Directory>' >> $FILE_NAME_VHOST
        echo '    <LocationMatch "/(keys|stuff|template|languages|downloads|tmp)">' >> $FILE_NAME_VHOST
        echo '        <IfVersion >= 2.4>' >> $FILE_NAME_VHOST
        echo '            Require all denied' >> $FILE_NAME_VHOST
        echo '        </IfVersion>' >> $FILE_NAME_VHOST
        echo '        <IfVersion < 2.4>' >> $FILE_NAME_VHOST
        echo '            Order deny,allow' >> $FILE_NAME_VHOST
        echo '            Deny  from all' >> $FILE_NAME_VHOST
        echo '        </IfVersion>' >> $FILE_NAME_VHOST
        echo '    </LocationMatch>' >> $FILE_NAME_VHOST
        echo '</VirtualHost>' >> $FILE_NAME_VHOST

        okAndSleep "Riavviando Apache2."
        service apache2 restart
    fi

    chown $MASTERUSER:www-data $FILE_NAME_VHOST

    if [ "`grep reboot.php /etc/crontab`" == "" ]; then
        echo '0 */1 * * * easywi_web cd /home/easywi_web/htdocs && timeout 300 php ./reboot.php >/dev/null 2>&1
0 */1 * * * easywi_web cd /home/easywi_web/htdocs && timeout 300 php ./reboot.php >/dev/null 2>&1
*/5 * * * * easywi_web cd /home/easywi_web/htdocs && timeout 290 php ./statuscheck.php >/dev/null 2>&1
*/1 * * * * easywi_web cd /home/easywi_web/htdocs && timeout 290 php ./startupdates.php >/dev/null 2>&1
*/5 * * * * easywi_web cd /home/easywi_web/htdocs && timeout 290 php ./jobs.php >/dev/null 2>&1
*/10 * * * * easywi_web cd /home/easywi_web/htdocs && timeout 290 php ./cloud.php >/dev/null 2>&1' >> /etc/crontab
    fi

    service cron restart
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

    okAndSleep "Scaricando i files del server TS3."
    su -c "curl $DOWNLOAD_URL -o teamspeak3-server.tar.bz2" $MASTERUSER

    if [ ! -f teamspeak3-server.tar.bz2 ]; then
        errorAndExit "Download fallito! Uscendo ora!"
    fi

    okAndSleep "Estraendo i file del serverTS3."
    su -c "tar -xf teamspeak3-server.tar.bz2 --strip-components=1" $MASTERUSER

    removeIfExists teamspeak3-server.tar.bz2

    QUERY_WHITLIST_TXT=/home/$MASTERUSER/query_ip_whitelist.txt

    if [ ! -f $QUERY_WHITLIST_TXT ]; then
        touch $QUERY_WHITLIST_TXT
        chown $MASTERUSER:$MASTERUSER $QUERY_WHITLIST_TXT
    fi

    if [ -f $QUERY_WHITLIST_TXT ]; then
    
        if [ "`grep '127.0.0.1' $QUERY_WHITLIST_TXT`" == "" ]; then
            echo "127.0.0.1" >> $QUERY_WHITLIST_TXT
        fi

        if [ "$LOCAL_IP" != "" ]; then
            if [ "`grep -E '\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b' <<< $LOCAL_IP`" != "" -a "`grep $LOCAL_IP $QUERY_WHITLIST_TXT`" == "" ]; then
                echo $LOCAL_IP >> $QUERY_WHITLIST_TXT
            fi
        fi

        cyanMessage " "
        cyanMessage "Please specify the IPv4 address of the Easy-WI web panel."
        read IP_ADDRESS

        if [ "$IP_ADDRESS" != "" ]; then
            if [ "`grep -E '\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b' <<< $IP_ADDRESS`" != "" -a "`grep $IP_ADDRESS $QUERY_WHITLIST_TXT`" == "" ]; then
                echo $IP_ADDRESS >> $QUERY_WHITLIST_TXT
            fi
        fi
    else
        redMessage "Non posso modificare il file $QUERY_WHITLIST_TXT, perfavore mantenerlo manualmente."
    fi

    QUERY_PASSWORD=`< /dev/urandom tr -dc A-Za-z0-9 | head -c12`

    greenMessage "Avviando il server TS3 per la prima volta e e chiudendolo ancora per rendere la password visibile nell'albero dei processi."
    su -c "./ts3server_startscript.sh start serveradmin_password=$QUERY_PASSWORD createinifile=1 inifile=ts3server.ini" $MASTERUSER
    runSpinner 25
    su -c "./ts3server_startscript.sh stop" $MASTERUSER

    greenMessage "Avviando il server TS3 permanentemente."
    su -c "./ts3server_startscript.sh start inifile=ts3server.ini" $MASTERUSER
fi

okAndSleep "Rimuovendo i pacchetti non necessari."
apt-get autoremove -y

if [ "$INSTALL" == "EW" ]; then

    if [ "$SSL" == "Si" ]; then
        PROTOCOL="https"
    else
        PROTOCOL="http"
    fi

    greenMessage "L'intallazione del pannello Easy-WI è terminata per quanto riguarda l'architettura. perfavore vai al link $PROTOCOL://$IP_DOMAIN/install/install.php per completare il processo di installazione."
    greenMessage "Nome utente e password per il DB sono rispettivamente, Username:\"easy_wi\". La password è:\"$DB_PASSWORD\"."

elif [ "$INSTALL" == "GS" ]; then
    greenMessage "L'installazione del server princiaple per i server di gioco è termniata. Perfavore immetti i dati sovrastanti nel pannello al percorso \"Server Master > Panoramica > Aggiungi\"."
elif [ "$INSTALL" == "VS" ]; then
    greenMessage "L'installazione del server princiaple per i server voce è termniata. Perfavore immetti i dati sovrastanti nel pannello al percorso \"Servers Voce > Server princiapli > Aggiungi\"."
elif [ "$INSTALL" == "WR" ]; then
    greenMessage "L'installazione del server princiaple per i server web è termniata. Perfavore immetti i dati sovrastanti nel pannello al percorso \"Spazio Web > Server Principali > Aggiungi\"."
fi

if ([ "$INSTALL" == "MY" ] || [ "$INSTALL" == "WR" -a "$SQL" != "Nessuno" ]); then
    greenMessage "L'installazione del server principale MySQL è stata effettuata. Perfavore immetti i dati sovrastanti nel pannello al percorso \"MySQL > Server Principali > Aggiungi\"."
fi

exit 0
