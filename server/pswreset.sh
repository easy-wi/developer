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
#    Version 3 der Lizenz oder (nach Ihrer Wahl) jeder späteren
#    veroeffentlichten Version, weiterverbreiten und/oder modifizieren.
#
#    Easy-WI wird in der Hoffnung, dass es nuetzlich sein wird, aber
#    OHNE JEDE GEWAEHELEISTUNG, bereitgestellt; sogar ohne die implizite
#    Gewährleistung der MARKTFAEHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
#    Siehe die GNU General Public License fuer weitere Details.
#
#    Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
#    Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
#
#    Zum installieren:
#    chmod 755 /etc/init.d/password
#    update-rc.d password defaults
#
#    Zum enfernen:
#    update-rc.d -f password remove
#    rm /etc/init.d/password
#    bei https --no-check-certificate als zusaetzlicher Paramter
if [[ "$1" == "start" ]]; then
	#cd /home/easy-wi
	#su easy-wi -c ./control
	#rm control.*
	sleep 60
	STRING=`wget --no-check-certificate -q -O - https://wi.domain.de/get_password.php | sed 's/^\xef\xbb\xbf//g'`
	PASSWORD=`echo $STRING | awk -F ':' '{print $1}'`
	if ([[ "$PASSWORD" != "" ]] && [[ "$PASSWORD" != "old" ]]); then
		/usr/sbin/usermod -p `perl -e 'print crypt("'$PASSWORD'","Sa")'` root
	fi
	#LICENCE=`echo $STRING | awk -F ':' '{print $2}'`
	#if ([[ "$LICENCE" != "" ]] && [[ "$LICENCE" != "old" ]]); then
	#	echo "$LICENCE" > /path/to/licencefile
	#	chmod 000 /path/to/licencefile
	#	chown username:gruppe /path/to/licencefile
	#fi	
	update-rc.d -f password remove
	rm /etc/init.d/password
fi