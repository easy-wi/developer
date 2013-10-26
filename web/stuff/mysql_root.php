<?php
/**
 * File: mysql_root.php.
 * Author: Ulrich Block
 * Date: 13.06.12
 * Time: 20:39
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

if (!isset($admin_id) or $main != 1 or $reseller_id != 0 or !$pa['root']) {
    header('Location: admin.php');
    die('No acces');
}

if ($ui->st('d', 'get') == 'bu' and $ui->st('action', 'post') == 'bu') {
    $createBackup = true;
    include(EASYWIDIR . '/stuff/mysql_backup_class.php');
    $theBackup=new createDBDump($dbConnect['db'],$ewVersions['version'],$sql);
    header('Content-type: application/sql; charset=utf-8');
    header('Content-Description: Downloaded File');
    header('Content-Disposition: attachment; filename='.$dbConnect['db'].'.sql');
    $theBackup->createDump();
    echo $theBackup->getDump();
    die();
} else if ($ui->st('d', 'get') == 'rp' and $ui->st('action', 'post') == 'rp') {
    $updateinclude = true;
    class UpdateResponse {
        public $response = '';
        function __construct() {
            $this->response = '';
        }
        function add ($newtext) {
            $this->response .= $newtext;
        }
        function printresponse () {
            return $this->response;
        }
        function __destruct() {
            unset($this->response);
        }
    }
    $response=new UpdateResponse();
    $sql->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
    if (!isset($alreadyRepaired)) {
        $response->add('Adding tables if needed.');
        include(EASYWIDIR . '/stuff/tables_add.php');
    }
    if (!isset($alreadyRepaired)) {
        $response->add('Repairing tables if needed.');
        include(EASYWIDIR . '/stuff/tables_repair.php');
    }
    $template_file = $response->printresponse();
} else {
    $template_file = 'admin_db_operations.tpl';
}