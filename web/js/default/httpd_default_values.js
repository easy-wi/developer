/**
 * File: httpd_default_values.js.
 * Author: Ulrich Block
 * Contact: <ulrich.block@easy-wi.com>
 * Date: 15.03.14
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

function loadServerSettings (serverType) {

    if (serverType == 'N') {

        defaultRestartCMD = 'sudo /etc/init.d/nginx reload';

        defaultVhostConfigPath ='/etc/nginx/sites-enabled/';

        defaultVhostTemplate = 'server {\r\n';
        defaultVhostTemplate += '   listen 80;\r\n';
        defaultVhostTemplate += '   server_name %url%;\r\n';
        defaultVhostTemplate += '   autoindex off;\r\n';
        defaultVhostTemplate += '   access_log %vhostpath%/%user%/logs/access.log;\r\n';
        defaultVhostTemplate += '   error_log %vhostpath%/%user%/logs/error.log;\r\n';
        defaultVhostTemplate += '   root %vhostpath%/%user%/htdocs/;\r\n';
        defaultVhostTemplate += '   if ($http_user_agent != "Half-Life 2") {\r\n';
        defaultVhostTemplate += '      return 403;\r\n';
        defaultVhostTemplate += '   }\r\n';
        defaultVhostTemplate += '   location / {\r\n';
        defaultVhostTemplate += '      index index.html index.htm;\r\n';
        defaultVhostTemplate += '   }\r\n';
        defaultVhostTemplate += '}';

    } else if (serverType == 'A') {

        defaultRestartCMD = 'sudo /etc/init.d/apache reload';

        defaultVhostConfigPath ='/etc/apache/sites-enabled/';

        defaultVhostTemplate = '<VirtualHost *:80>\r\n';
        defaultVhostTemplate += '    ServerAdmin %email%\r\n';
        defaultVhostTemplate += '    DocumentRoot "%vhostpath%/%user%/htdocs"\r\n';
        defaultVhostTemplate += '    ServerName %url%\r\n';
        defaultVhostTemplate += '    ErrorLog "%vhostpath%/%user%/logs/error.log"\r\n';
        defaultVhostTemplate += '    CustomLog "%vhostpath%/%user%/logs/access.log" common\r\n';
        defaultVhostTemplate += '    <Directory %vhostpath%/%user%/htdocs>\r\n';
        defaultVhostTemplate += '        Options -Indexes FollowSymLinks Includes\r\n';
        defaultVhostTemplate += '        AllowOverride All\r\n';
        defaultVhostTemplate += '        Order allow,deny\r\n';
        defaultVhostTemplate += '        Allow from all\r\n';
        defaultVhostTemplate += '    </Directory>\r\n';
        defaultVhostTemplate += '</VirtualHost>';

    } else if (serverType == 'L') {

        defaultRestartCMD = 'sudo /etc/init.d/lighttpd reload';

        defaultVhostConfigPath ='/etc/lighttpd/sites-enabled/';

        defaultVhostTemplate = '$HTTP["host"] == "%url%" {\r\n';
        defaultVhostTemplate += '    server.document-root = "%vhostpath%/%user%/htdocs"\r\n';
        defaultVhostTemplate += '    server.errorlog = "%vhostpath%/%user%/logs/error.log"\r\n';
        defaultVhostTemplate += '    accesslog.filename = "%vhostpath%/%user%/logs/access.log"\r\n';
        defaultVhostTemplate += '    dir-listing.activate = "disable""\r\n';
        defaultVhostTemplate += '}';

    } else if (serverType == 'H') {

        defaultRestartCMD = 'sudo /etc/init.d/hiawatha reload';

        defaultVhostConfigPath ='/etc/hiawatha/sites-enabled/';

        defaultVhostTemplate = 'VirtualHost {\r\n';
        defaultVhostTemplate += '    Hostname = %url%\r\n';
        defaultVhostTemplate += '    WebsiteRoot = %vhostpath%/%user%/htdocs\r\n';
        defaultVhostTemplate += '    AccessLogfile = %vhostpath%/%user%/logs/access.log\r\n';
        defaultVhostTemplate += '    ErrorLogfile = %vhostpath%/%user%/logs/error.log\r\n';
        defaultVhostTemplate += '    ShowIndex = No\r\n';
        defaultVhostTemplate += '}';

    } else {

        defaultRestartCMD = 'sudo /etc/init.d/toBeReplaced reload';

        defaultVhostConfigPath ='/etc/toBeReplaced/sites-enabled/';

        defaultVhostTemplate = '';

    }

    document.getElementById("inputHttpdCmd").value = defaultRestartCMD;

    document.getElementById("inputVhostConfigPath").value = defaultVhostConfigPath;

    document.getElementById("inputVhostTemplate").value = defaultVhostTemplate;

}