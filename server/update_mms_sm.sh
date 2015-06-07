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

function checkCreateVersionFile {
    if [ ! -f $HOME/versions/$1 ]; then
        touch $HOME/versions/$1
    fi
}

function checkCreateVersionFolder {
	if [ ! -d $HOME/versions ]; then
		mkdir -p $HOME/versions
	fi
}

function checkCreateVersionFile {
    if [ ! -f $HOME/versions/$1 ]; then
        touch $HOME/versions/$1
    fi
}

function downloadExtractFile {

    if [ ! -d $HOME/masteraddons/$1/ ]; then
        mkdir -p $HOME//masteraddons/$1/
    fi

    cd $HOME/masteraddons/$1/

    if [ -f $2 ]; then
       rm $2
    fi

    wget $3

    if [ -f $2 ]; then

        tar xfv $2
        rm $2

        find -type f ! -perm -750 -exec chmod 640 {} \;
        find -type d -exec chmod 750 {} \;
    fi
}

function updateAddons {

    FILE_NAME=`echo $2 | egrep -o '(sourcemod|mmsource)-[[:digit:]].*$' | tail -1`
    LOCAL_VERSION=`cat $HOME/versions/$1 | tail -1`
    CURRENT_VERSION=`echo $2 | egrep -o '(mmsource|sourcemod)-[0-9a-z.-]{1,}[0-9]' | tail -1`

    if ([ "$CURRENT_VERSION" != "$LOCAL_VERSION" -o "$LOCAL_VERSION" == "" ] && [ "$CURRENT_VERSION" != "" ]); then

	    greenMessage "Updating $1 from $LOCAL_VERSION to $CURRENT_VERSION. Name of file is $FILE_NAME"

        downloadExtractFile $3 $FILE_NAME $2
        echo "$CURRENT_VERSION" > $HOME/versions/$1

    else
        cyanMessage "Already up to date. Local version is $LOCAL_VERSION. Most recent version is $CURRENT_VERSION"
    fi
}

function updatesAddonSnapshots {

    cyanMessage "Searching snapshot updates for $1 ($3) and revision $2"

    checkCreateVersionFile ${1}_snapshot_${3}.txt

    if [ "$1" == "sourcemod" ]; then
        DOWNLOAD_URL=`lynx -dump "http://www.sourcemod.net/smdrop/$2/" | egrep -o "http:.*sourcemod-.*-linux.*" | tail -1`
    else
        DOWNLOAD_URL=`lynx -dump "http://www.metamodsource.net/mmsdrop/$2/" | egrep -o "http:.*mmsource-.*-linux.*" | tail -1`
    fi

    updateAddons ${1}_snapshot_${3}.txt $DOWNLOAD_URL ${1}-${3}
}

function updatesAddonSstables {

    cyanMessage "Searching updates for $1 stable"

    checkCreateVersionFile ${1}_stable.txt

    if [ "$1" == "sourcemod" ]; then
        PAGE_URL=`lynx -dump www.sourcemod.net/downloads.php | egrep -o "http:.*sourcemod-.*-linux.*" | tail -1`
    else
        PAGE_URL=`lynx -dump www.metamodsource.net/ | egrep -o "http:.*mmsource-.*-linux.*" | tail -1`
    fi

    DOWNLOAD_URL=`lynx -dump $PAGE_URL | grep -v "www.sourcemod.net|www.metamodsource.net" | egrep -o "http:.*sourcemod-.*-linux.*|http:.*mmsource-.*-linux.*" | tail -1`

    updateAddons ${1}_stable.txt $DOWNLOAD_URL $1
}

checkCreateVersionFolder

updatesAddonSstables "sourcemod"
updatesAddonSstables "metamod"
updatesAddonSnapshots "metamod" "1.10" "stable"
updatesAddonSnapshots "metamod" "1.11" "dev"
updatesAddonSnapshots "sourcemod" "1.7" "stable"
updatesAddonSnapshots "sourcemod" "1.8" "dev"

exit 0
