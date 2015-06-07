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
    if [ ! -f "$HOME/versions/$1" ]; then
        touch "$HOME/versions/$1"
    fi
}

function checkCreateFolder {
    if [ ! -d "$1" -a "$1" != "" ]; then
        mkdir -p "$1"
    fi
}

function removeFile {
    if [ -f "$1" ]; then
       rm "$1"
    fi
}

function downloadExtractFile {

    checkCreateFolder "$HOME/$4/$1/"

    cd "$HOME/$4/$1/"

    removeFile "$2"

    wget "$3"

    if [ -f "$2" ]; then

        tar xfv "$2"
        rm "$2"

        moveFilesFolders "$2" "$4" "$1"

        find -type f ! -perm -750 -exec chmod 640 {} \;
        find -type d -exec chmod 750 {} \;
    fi
}

function moveFilesFolders {

    FOLDER=`echo $1 | sed -r 's/.tar.gz//g'`

    if [ "$FOLDER" != "" -a "" != "$2" -a "$3" != "" -a -d "$HOME/$2/$3/$FOLDER" ]; then

        cd "$HOME/$2/$3/"

        find "$FOLDER/" -mindepth 1 -type d | while read DIR; do

            NEW_FODLER=${DIR/$FOLDER\//}

            if [ ! -d "$HOME/$2/$3/$NEW_FODLER" ]; then
                mkdir -p "$HOME/$2/$3/$NEW_FODLER"
            fi
        done

        find "$FOLDER/" -type f | while read FILE; do

            MOVE_TO=${FILE/$FOLDER\//.\/}

            if [ "$MOVE_TO" != "" ]; then
                mv "$FILE" "$MOVE_TO"
            fi
        done

        rm -rf "$FOLDER"
    fi
}

function update {

    checkCreateVersionFile "$1"

    FILE_NAME=`echo $2 | egrep -o '(sourcemod|mmsource|multitheftauto_linux|baseconfig)-[[:digit:]].*$' | tail -1`
    LOCAL_VERSION=`cat $HOME/versions/$1 | tail -1`
    CURRENT_VERSION=`echo $2 | egrep -o '(mmsource|sourcemod|multitheftauto_linux|baseconfig)-[0-9a-z.-]{1,}[0-9]' | tail -1`

    if ([ "$CURRENT_VERSION" != "$LOCAL_VERSION" -o "$LOCAL_VERSION" == "" ] && [ "$CURRENT_VERSION" != "" ]); then

	    greenMessage "Updating $1 from $LOCAL_VERSION to $CURRENT_VERSION. Name of file is $FILE_NAME"

        downloadExtractFile "$3" "$FILE_NAME" "$2" "$4"
        echo "$CURRENT_VERSION" > "$HOME/versions/$1"

    else
        cyanMessage "$1 already up to date. Local version is $LOCAL_VERSION. Most recent version is $CURRENT_VERSION"
    fi
}

function updatesAddonSnapshots {

    cyanMessage "Searching snapshot updates for $1 ($3) and revision $2"

    if [ "$1" == "sourcemod" ]; then
        DOWNLOAD_URL=`lynx -dump "http://www.sourcemod.net/smdrop/$2/" | egrep -o "http:.*sourcemod-.*-linux.*" | tail -1`
    else
        DOWNLOAD_URL=`lynx -dump "http://www.metamodsource.net/mmsdrop/$2/" | egrep -o "http:.*mmsource-.*-linux.*" | tail -1`
    fi

    update "${1}_snapshot_${3}.txt" "$DOWNLOAD_URL" "${1}-${3}" "masteraddons"
}

function updatesAddonStables {

    cyanMessage "Searching updates for $1 stable"

    if [ "$1" == "sourcemod" ]; then
        PAGE_URL=`lynx -dump www.sourcemod.net/downloads.php | egrep -o "http:.*sourcemod-.*-linux.*" | tail -1`
    else
        PAGE_URL=`lynx -dump www.metamodsource.net/ | egrep -o "http:.*mmsource-.*-linux.*" | tail -1`
    fi

    DOWNLOAD_URL=`lynx -dump $PAGE_URL | grep -v "www.sourcemod.net|www.metamodsource.net" | egrep -o "http:.*sourcemod-.*-linux.*|http:.*mmsource-.*-linux.*" | tail -1`
    update ${1}_stable.txt "$DOWNLOAD_URL" "$1" "masteraddons"
}

function fileUpdate {

    checkCreateVersionFile "$1"

    checkCreateFolder "$HOME/$4/$2"

    cd "$HOME/$4/$2"

    wget "$2"

    NO_HTTP=${2:6}
    FILE_NAME=${NO_HTTP##/*/}

    if [ "$FILE_NAME" != "" -a -f "$FILE_NAME" ]; then

        LOCAL_VERSION=`cat $HOME/versions/$1 | tail -1`
        CURRENT_VERSION=`stat -c "%Y" $FILE_NAME`

        if ([ "$CURRENT_VERSION" != "$LOCAL_VERSION" -o "$LOCAL_VERSION" == "" ] && [ "$CURRENT_VERSION" != "" ]); then

            greenMessage "Updating $3 from $LOCAL_VERSION to $CURRENT_VERSION. Name of file is $FILE_NAME"

            unzip "$FILE_NAME"

            echo "$CURRENT_VERSION" > "$HOME/versions/$1"

        else
            cyanMessage "$3 already up to date. Local version is $LOCAL_VERSION. Most recent version is $CURRENT_VERSION"
        fi

        rm "$FILE_NAME"
    fi
}

function updateMTA {

    cyanMessage "Searching update for MTA San Andreas"

    DOWNLOAD_URL=`lynx -dump http://linux.mtasa.com/ | egrep -o "http:.*multitheftauto_linux-(.*).tar.gz" | tail -1`
    update server_mta.txt "$DOWNLOAD_URL" "mtasa" "masterserver"

    DOWNLOAD_URL=`lynx -dump http://linux.mtasa.com/ | egrep -o "http:.*baseconfig-(.*).tar.gz" | tail -1`
    update server_mta_baseconfig.txt "$DOWNLOAD_URL" "mtasa" "masterserver"

    if [ "`date +'%H'`" == "00" ]; then
        fileUpdate server_mta_resources.txt "http://mirror.mtasa.com/mtasa/resources/mtasa-resources-latest.zip" "mtasa" "masterserver"
    fi
}

checkCreateFolder $HOME/versions

updateMTA
updatesAddonStables "metamod"
updatesAddonStables "sourcemod"
updatesAddonSnapshots "metamod" "1.10" "stable"
updatesAddonSnapshots "metamod" "1.11" "dev"
updatesAddonSnapshots "sourcemod" "1.7" "stable"
updatesAddonSnapshots "sourcemod" "1.8" "dev"

exit 0
