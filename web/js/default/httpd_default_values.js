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

function loadServerSettings (serverType, usageType) {

    var defaultRestartCMD = '';
    var defaultVhostTemplate = '';

    if (serverType == 'N') {

        defaultRestartCMD = 'sudo /etc/init.d/nginx reload';

        defaultVhostTemplate = 'server {\r\n';
        defaultVhostTemplate += '   listen 80;\r\n';
        defaultVhostTemplate += '   server_name %url%;\r\n';
        defaultVhostTemplate += '   autoindex off;\r\n';
        defaultVhostTemplate += '   access_log %vhostpath%/%user%/%logDir%/access.log;\r\n';
        defaultVhostTemplate += '   error_log %vhostpath%/%user%/%logDir%/error.log;\r\n';
        defaultVhostTemplate += '   root %vhostpath%/%user%/%htdocs%/;\r\n';

        if (usageType == 'W') {

            defaultVhostTemplate += '   index index.php index.html index.htm;\r\n';
            defaultVhostTemplate += '   location / {\r\n';
            defaultVhostTemplate += '      try_files $uri $uri/ =404;\r\n';
            defaultVhostTemplate += '   }\r\n';

            defaultVhostTemplate += '   location ~ \.php$ {\r\n';
            defaultVhostTemplate += '      #       fastcgi_split_path_info ^(.+\.php)(/.+)$;\r\n';
            defaultVhostTemplate += '      #       # NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini\r\n';
            defaultVhostTemplate += '      #\r\n';
            defaultVhostTemplate += '      #       # With php5-cgi alone:\r\n';
            defaultVhostTemplate += '      #       fastcgi_pass 127.0.0.1:9000;\r\n';
            defaultVhostTemplate += '      #       # With php5-fpm:\r\n';
            defaultVhostTemplate += '      fastcgi_pass unix:/var/run/php5-fpm.sock;\r\n';
            defaultVhostTemplate += '      fastcgi_index index.php;\r\n';
            defaultVhostTemplate += '      include fastcgi_params;\r\n';
            defaultVhostTemplate += '      fastcgi_param  PHP_VALUE "open_basedir=%vhostpath%/%user%/%htdocs%\nsession.save_path=%vhostpath%/%user%/sessions\nupload_tmp_dir=%vhostpath%/%user%/tmp\nallow_url_fopen=Off\nallow_url_include=Off\n%phpConfiguration%";\r\n';
            defaultVhostTemplate += '   }\r\n';

        } else {

            defaultVhostTemplate += '   location / {\r\n';
            defaultVhostTemplate += '      index index.html index.htm;\r\n';
            defaultVhostTemplate += '   }\r\n';

        }

        defaultVhostTemplate += '}';

    } else if (serverType == 'A') {

        defaultRestartCMD = 'sudo /etc/init.d/apache reload';

        defaultVhostTemplate = '<VirtualHost *:80>\r\n';
        defaultVhostTemplate += '    ServerAdmin %email%\r\n';
        defaultVhostTemplate += '    DocumentRoot "%vhostpath%/%user%/%htdocs%"\r\n';
        defaultVhostTemplate += '    ServerName %url%\r\n';
        defaultVhostTemplate += '    ErrorLog "%vhostpath%/%user%/%logDir%/error.log"\r\n';
        defaultVhostTemplate += '    CustomLog "%vhostpath%/%user%/%logDir%/access.log" common\r\n';

        if (usageType == 'W') {

            defaultVhostTemplate += '    DirectoryIndex index.php index.html\r\n';
            defaultVhostTemplate += '    <IfModule mpm_itk_module>\r\n';
            defaultVhostTemplate += '       AssignUserId %user% %group%\r\n';
            defaultVhostTemplate += '       MaxClientsVHost 50\r\n';
            defaultVhostTemplate += '       NiceValue 10\r\n';
            defaultVhostTemplate += '       php_admin_value open_basedir "%vhostpath%/%user%/%htdocs%"\r\n';
            defaultVhostTemplate += '       php_admin_value session.save_path "%vhostpath%/%user%/sessions"\r\n';
            defaultVhostTemplate += '       php_admin_value upload_tmp_dir "%vhostpath%/%user%/tmp"\r\n';
            defaultVhostTemplate += '       php_admin_flag allow_url_fopen Off\r\n';
            defaultVhostTemplate += '       php_admin_flag allow_url_include Off\r\n';
            defaultVhostTemplate += '       %phpConfiguration%\r\n';
            defaultVhostTemplate += '    </IfModule>\r\n';

        }

        defaultVhostTemplate += '    <Directory %vhostpath%/%user%/%htdocs%>\r\n';
        defaultVhostTemplate += '        Options -Indexes FollowSymLinks Includes\r\n';
        defaultVhostTemplate += '        AllowOverride All\r\n';
        defaultVhostTemplate += '        Order allow,deny\r\n';
        defaultVhostTemplate += '        Allow from all\r\n';
        defaultVhostTemplate += '    </Directory>\r\n';
        defaultVhostTemplate += '</VirtualHost>';

    } else if (serverType == 'L') {

        defaultRestartCMD = 'sudo /etc/init.d/lighttpd reload';

        defaultVhostTemplate = '$HTTP["host"] == "%url%" {\r\n';
        defaultVhostTemplate += '    server.document-root = "%vhostpath%/%user%/%htdocs%"\r\n';
        defaultVhostTemplate += '    server.errorlog = "%vhostpath%/%user%/%logDir%/error.log"\r\n';
        defaultVhostTemplate += '    accesslog.filename = "%vhostpath%/%user%/%logDir%/access.log"\r\n';
        defaultVhostTemplate += '    dir-listing.activate = "disable""\r\n';
        defaultVhostTemplate += '}';

    } else if (serverType == 'H') {

        defaultRestartCMD = 'sudo /etc/init.d/hiawatha reload';

        defaultVhostTemplate = 'VirtualHost {\r\n';
        defaultVhostTemplate += '    Hostname = %url%\r\n';
        defaultVhostTemplate += '    WebsiteRoot = %vhostpath%/%user%/%htdocs%\r\n';
        defaultVhostTemplate += '    AccessLogfile = %vhostpath%/%user%/%logDir%/access.log\r\n';
        defaultVhostTemplate += '    ErrorLogfile = %vhostpath%/%user%/%logDir%/error.log\r\n';
        defaultVhostTemplate += '    ShowIndex = No\r\n';
        defaultVhostTemplate += '}';

    } else {

        defaultRestartCMD = 'sudo /etc/init.d/toBeReplaced reload';

        defaultVhostTemplate = '';

    }

    document.getElementById("inputHttpdCmd").value = defaultRestartCMD;

    document.getElementById("inputVhostTemplate").value = defaultVhostTemplate;

}