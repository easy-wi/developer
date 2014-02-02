<?php

/**
 * File: update_430-440.php.
 * Author: Ulrich Block
 * Date: 02.02.14
 * Contact: <ulrich.block@easy-wi.com>
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

if (isset($include) and $include == true) {
    $query = $sql->prepare("INSERT INTO `easywi_version` (`version`,`de`,`en`) VALUES
('4.40','<div align=\"right\">23.10.2013</div>
<b>Änderungen:</b><br/>
<ul>
<li></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li></li>
</ul>
','<div align=\"right\">10.23.2013</div>
<b>Changes:</b><br/>
<ul>
<li></li>
</ul>
<br/><br/>
<b>Bugfixes:</b><br/>
<ul>
<li></li>
</ul>
')");
    $query->execute();
    $response->add('Action: insert_easywi_version done: ');
    $query->closecursor();

    $query = $sql->prepare("SELECT `active`,`subnetOptions`,`ips`,`netmask`,`resellerid` FROM `rootsDHCP`");
    $query2 = $sql->prepare("SELECT 1 FROM `rootsSubnets` WHERE `subnet`=? LIMIT 1");
    $query3 = $sql->prepare("INSERT INTO `rootsSubnets` (`active`,`subnet`,`subnetOptions`,`netmask`,`vlan`,`vlanName`) VALUES (?,?,?,?,'N','')");

    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

        foreach (explode("\r\n", $row['ips']) as $exip) {

            $ex = explode('.', $exip);

            if (isset($ex[2])) {
                $query2->execute(array($ex[0] . '.' . $ex[1] . '.' . $ex[2] . '.0'));

                if ($query2->rowCount() == 0) {
                    $query3->execute(array($row['active'], $ex[0] . '.' . $ex[1] . '.' . $ex[2] . '.0', str_replace("option subnet-mask %subnet-mask%;\r\n", '', $row['subnetOptions']), $row['netmask']));
                }
            }
        }
    }

} else {
    echo "Error: this file needs to be included by the updater!<br />";
}