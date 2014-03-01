<?php

/**
 * File: roots_subnets.php.
 * Author: Ulrich Block
 * Date: 11.01.14
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['root']) or $reseller_id != 0) {
    header('Location: admin.php');
    die;
}

$sprache = getlanguagefile('subnets', $user_language, $resellerLockupID);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
$logreseller = 0;
$logsubuser = 0;

// Define the ID variable which will be used at the form and SQLs
$id = $ui->id('id', 10, 'get');

// CSFR protection with hidden tokens. If token(true) returns false, we likely have an attack
if ($ui->w('action',4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

// Add and modify entries. Same validation can be used.
} else if ($ui->st('d', 'get') == 'ad' or $ui->st('d', 'get') == 'md') {

    // Error handling. Check if required attributes are set and can be validated
    $errors = array();
    $dhcpServers = array();

    // At this point all variables are defined that can come from the user
    $dhcpServer = (int) $ui->id('dhcpServer', 10, 'post');
    $subnet = (string) $ui->ip4('subnet', 'post');
    $netmask = (string) $ui->ip4('netmask', 'post');
    $active = (string) $ui->active('active', 'post');
    $vlan = (string) $ui->active('vlan', 'post');
    $vlanName = (string) $ui->description('vlanName', 'post');

    if ($ui->escaped('subnetOptions', 'post')) {
        $subnetOptions = $ui->escaped('subnetOptions', 'post');
    } else {
        $subnetOptions = 'option broadcast-address 1.1.1.1;
option routers 1.1.1.1;
option domain-name-servers 1.1.1.1;';
    }

    $query = $sql->prepare("SELECT `id`,`description` FROM `rootsDHCP` WHERE `active`='Y'");
    $query->execute(array($id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $dhcpServers[$row['id']] = $row['description'];
    }

    // Add or mod is opened
    if (!$ui->smallletters('action', 2, 'post')) {

        // Gather data for adding if needed and define add template
        if ($ui->st('d', 'get') == 'ad') {

            $template_file = 'admin_root_subnets_add.tpl';

            // Gather data for modding in case we have an ID and define mod template
        } else if ($ui->st('d', 'get') == 'md' and $id) {

            $query = $sql->prepare("SELECT * FROM `rootsSubnets` WHERE `subnetID`=? LIMIT 1");
            $query->execute(array($id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $dhcpServer = (int) $row['dhcpServer'];
                $subnet = (string) $row['subnet'];
                $netmask = (string) $row['netmask'];
                $active = (string) $row['active'];
                $vlan = (string) $row['vlan'];
                $vlanName = (string) $row['vlanName'];
                $subnetOptions = (string) $row['subnetOptions'];
            }

            // Check if database entry exists and if not display 404 page
            $template_file =  ($query->rowCount() > 0) ? 'admin_root_subnets_md.tpl' : 'admin_404.tpl';

            // Show 404 if GET parameters did not add up or no ID was given with mod
        } else {
            $template_file = 'admin_404.tpl';
        }

        // Form is submitted
    } else if ($ui->st('action', 'post') == 'md' or $ui->st('action', 'post') == 'ad') {

        if (!$dhcpServer or !isset($dhcpServers[$dhcpServer])) {
            $errors['dhcpServer'] = 'DHCP';
        }

        if (!$active) {
            $errors['active'] = $gsprache->active;
        }

        if (!$subnet and $ui->st('action', 'post') == 'ad') {
            $errors['subnet'] = $sprache->subnet;
        }

        if (!$netmask) {
            $errors['netmask'] = $sprache->netmask;
        }

        if (!$vlan) {

            $errors['vlan'] = $sprache->vlan;

        } else if ($vlan == 'Y') {

            if (!$vlanName) {
                $errors['vlanName'] = $sprache->vlanName;
            } else {
                if ($ui->st('d', 'get') == 'ad') {

                    $query = $sql->prepare("SELECT 1 FROM `rootsSubnets` WHERE `vlanName`=? LIMIT 1");
                    $query->execute(array($vlanName));

                } else {

                    $query = $sql->prepare("SELECT 1 FROM `rootsSubnets` WHERE `vlanName`=? AND `subnetID`!=? LIMIT 1");
                    $query->execute(array($vlanName, $id));

                }

                if ($query->rowCount() > 0) {
                    $errors['vlanName'] = $sprache->vlanName;
                }
            }
        }

        // Submitted values are OK
        if (count($errors) == 0) {

            // Make the inserts or updates define the log entry and get the affected rows from insert
            if ($ui->st('action', 'post') == 'ad') {

                $query = $sql->prepare("INSERT INTO `rootsSubnets` (`dhcpServer`,`active`,`subnet`,`subnetOptions`,`netmask`,`vlan`,`vlanName`) VALUES (?,?,?,?,?,?,?)");
                $query->execute(array($dhcpServer, $active, $subnet, $subnetOptions, $netmask, $vlan, $vlanName));
                $rowCount = $query->rowCount();
                $loguseraction = '%add% %subnets% ' . $subnet;

                $id = $sql->lastInsertId();

                $ex = explode('.', $subnet);
                if (isset($ex[2])) {
                    $query = $sql->prepare("INSERT INTO `rootsIP4` (`subnetID`,`ip`) VALUES (?,?) ON DUPLICATE KEY UPDATE `ip`=VALUES(`ip`)");
                    for ($lastTriple = 2; $lastTriple < 255; $lastTriple++) {
                        $query->execute(array($id, $ex[0] . '.' . $ex[1] . '.' . $ex[2] . '.' . $lastTriple));
                    }
                }

            } else if ($ui->st('action', 'post') == 'md' and $id) {

                $query = $sql->prepare("UPDATE `rootsSubnets` SET `dhcpServer`=?,`active`=?,`subnetOptions`=?,`netmask`=?,`vlan`=?,`vlanName`=? WHERE `subnetID` =? LIMIT 1");
                $query->execute(array($dhcpServer, $active, $subnetOptions, $netmask, $vlan, $vlanName, $id));
                $rowCount = $query->rowCount();

                $loguseraction = '%mod% %subnets% ' . $subnet;

                if ($rowCount > 0) {
                    # insert job to change dhcp server config
                }
            }

            // Check if a row was affected during insert or update
            if (isset($rowCount) and $rowCount > 0) {
                $insertlog->execute();
                $template_file = $spracheResponse->table_add;

                // No update or insert failed
            } else {
                $template_file = $spracheResponse->error_table;
            }

            // An error occurred during validation unset the redirect information and display the form again
        } else {
            unset($header, $text);
            $template_file = ($ui->st('d', 'get') == 'ad') ? 'admin_root_subnets_add.tpl' : 'admin_root_subnets_md.tpl';
        }
    }

// Remove entries in case we have an ID given with the GET request
} else if ($ui->st('d', 'get') == 'dl' and $id) {

    $query = $sql->prepare("SELECT `subnet`,`netmask` FROM `rootsSubnets` WHERE `subnetID`=? LIMIT 1");
    $query->execute(array($id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $subnet = (string) $row['subnet'];
        $netmask = (string) $row['netmask'];
    }

    // Nothing submitted yet, display the delete form
    if (!$ui->st('action', 'post')) {

        // Check if we could find an entry and if not display 404 page
        $template_file = ($query->rowCount() > 0) ? 'admin_root_subnets_dl.tpl' : 'admin_404.tpl';

        // User submitted remove the entry
    } else if ($ui->st('action', 'post') == 'dl' and $subnet) {

        $serversLeftCount = 0;

        $ex = explode('.', $subnet);

        if (isset($ex[2])) {

            $query = $sql->prepare("SELECT 1 FROM `virtualcontainer` WHERE `ip` LIKE :sub OR `ips` LIKE :sub LIMIT 1");
            $query->execute(array(':sub' => $ex[0] . '.' . $ex[1] . '.' . $ex[2] . '.%'));
            $serversLeftCount += $query->rowCount();

            $query = $sql->prepare("SELECT 1 FROM `rootsDedicated` WHERE `ip` LIKE :sub OR `ips` LIKE :sub LIMIT 1");
            $query->execute(array(':sub' => $ex[0] . '.' . $ex[1] . '.' . $ex[2] . '.%'));
            $serversLeftCount += $query->rowCount();

        }

        if ($serversLeftCount == 0) {
            $query = $sql->prepare("DELETE FROM `rootsSubnets` WHERE `subnetID`=? LIMIT 1");
            $query->execute(array($id));
            $query2 = $sql->prepare("DELETE FROM `rootsIP4` WHERE `subnetID`=?");
            $query2->execute(array($id));

            // Check if a row was affected meaning an entry could be deleted. If yes add log entry and display success message
            if ($query->rowCount() > 0) {
                $template_file = $spracheResponse->table_del;
                $loguseraction = '%del% %subnets% ' . $subnet;
                $insertlog->execute();

                // Nothing was deleted, display an error
            } else {
                $template_file = $spracheResponse->error_table;
            }
        } else {
            $template_file = $sprache->error_server_exists;
        }

        // GET Request did not add up. Display 404 error.
    } else {
        $template_file = 'admin_404.tpl';
    }

// List the available entries
} else {

    $table = array();

    $o = ($ui->st('o', 'get')) ? (string) $ui->st('o', 'get') : 'di';

    if ($o == 'dv') {
        $orderby = '`vlanName` DESC';
    } else if ($o == 'av') {
        $orderby = '`vlanName` ASC';
    } else if ($o == 'ds') {
        $orderby = '`subnet` DESC';
    } else if ($o == 'as') {
        $orderby = '`subnet` ASC';
    } else if ($o == 'di') {
        $orderby = '`subnetID` DESC';
    } else {
        $orderby = '`subnetID` ASC';
    }

    $query = $sql->prepare("SELECT COUNT(`subnetID`) AS `amount` FROM `rootsSubnets`");
    $query->execute();
    $colcount = $query->fetchColumn();

    $amount = (isset($amount)) ? $amount : 20;
    $start = (isset($start) and $start < $colcount) ? $start : 0;
    $next = (isset($amount) and ($start + $amount) < $colcount) ? ($start + $amount) : 20;
    $vor = ($colcount > $next) ? $start + $amount : $start;
    $zur = (($start - $amount) > -1) ? $start - $amount : $start;
    $pageamount = ceil($colcount / $amount);

    $link = '<a href="admin.php?w=sn&amp;o=' . $o . '&amp;a=' . $amount;
    $link .= ($start == 0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';

    $pages[] = $link;
    $i = 2;

    while ($i <= $pageamount) {
        $selectpage = ($i - 1) * $amount;
        $pages[] = ($start == $selectpage) ? '<a href="admin.php?w=sn&amp;o=' . $o . '&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=sn&amp;o=' . $o . '&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        $i++;
    }
    $pages = implode(', ', $pages);

    $query = $sql->prepare("SELECT * FROM `rootsSubnets` ORDER BY $orderby LIMIT $start,$amount");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[] = array('id' => $row['subnetID'], 'active' => $row['active'], 'subnet' => $row['subnet'], 'vlanName' => $row['vlanName']);
    }

    $template_file = 'admin_root_subnets_list.tpl';
}