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

function update {

    echo "Searching updates for $1 and revision $2"

    if [ ! -f $HOME/${1}_version.txt ]; then
        touch $HOME/${1}_version_$2.txt
    fi

    if [ "$1" == "sourcemod" ]; then
        DOWNLOAD_URL=`lynx -dump "http://www.sourcemod.net/smdrop/$2/" | egrep -o "http:.*sourcemod-.*-linux.*" | tail -1`
    else
        DOWNLOAD_URL=`lynx -dump "http://www.metamodsource.net/mmsdrop/$2/" | egrep -o "http:.*mmsource-.*-linux.*" | tail -1`
    fi

    FILE_NAME=`echo $DOWNLOAD_URL | egrep -o '(sourcemod|mmsource)-[[:digit:]].*$' | tail -1`
    LOCAL_VERSION=`cat /home/imageserver/${1}_version_$2.txt | tail -1`
    CURRENT_VERSION=`echo $DOWNLOAD_URL | egrep -o '(git|hg)[0-9]{1,}' | tail -1 | sed 's/[^0-9]*//g'`

    echo "local version is $LOCAL_VERSION. Most recent version is $CURRENT_VERSION"

    if [ "$CURRENT_VERSION" != "$LOCAL_VERSION" -a "$CURRENT_VERSION" != "" ]; then

        if [ ! -d $HOME/masteraddons/${1}-latest-$2/ ]; then
            mkdir -p $HOME//masteraddons/${1}-latest-$2/
        fi

        cd $HOME/masteraddons/${1}-latest-$2/

        if [ -f $FILE_NAME ]; then
           rm $FILE_NAME
        fi

        wget $DOWNLOAD_URL

        if [ -f $FILE_NAME ]; then

            tar xfv $FILE_NAME
            rm $FILE_NAME

            find -type f ! -perm -750 -exec chmod 640 {} \;
            find -type d -exec chmod 750 {} \;
        fi

	echo "Updated $1 $2 from $LOCAL_VERSION to $CURRENT_VERSION"
        echo "$CURRENT_VERSION" > $HOME/${1}_version_$2.txt
    fi
}

update "metamod" "1.10"
update "metamod" "1.11"
update "sourcemod" "1.7"
update "sourcemod" "1.8"

exit 0
