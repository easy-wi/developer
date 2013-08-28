<?php
/**
 * File: mysql_backup_class.php.
 * Author: Ulrich Block
 * Date: 13.06.12
 * Time: 20:57
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

if (isset($createBackup) and $createBackup==true) {
    class createDBDump {
        private $connection;
        private $tableList=array();
        private $count=0;
        private $SQLDump;
        function __construct($db,$version,$sql) {
            $this->connection=$sql;
            $query=$this->connection->prepare("SHOW TABLES");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $table) {
                $this->tableList[current($table)]='';
            }
            $this->SQLDump='-- Easy-Wi SQL Dump
-- version '.$version.'
-- http://easy-wi.com
--
-- Created: '.date('d. F Y H:m:s').'
--
-- Database: `'.$db.'`
--


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


';
        }
        private function getContent($table) {
            $this->SQLDump .='--
-- Table structure for table `'.$table.'`
--

';
            $this->connection->query("LOCK TABLE `".$table."`WRITE");
            $query=$this->connection->prepare("SHOW CREATE TABLE `".$table."`");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $create) {
                $this->SQLDump .=next($create).";\n\n";
            }
            $query=$this->connection->prepare("SHOW COLUMNS FROM `".$table."`");
            $query->execute();
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $column) {
                $columnName=$column['Field'];
                $inserts[]='`'.$columnName.'`';
                $this->tableList[$table][$columnName]=$column['Type'];
            }
            $query=$this->connection->prepare("SELECT COUNT(*) AS `amount` FROM `".$table."`");
            $query->execute();
            $this->count=$query->fetchColumn();
            if ($this->count>0) {
                $this->SQLDump .='--
-- Data Table `'.$table.'`
--

';
                $this->SQLDump .='INSERT INTO `'.$table.'` ('.implode(',',$inserts).') VALUES'."\n";
                $this->createInserts($table);
            }
        }
        private function createInserts ($table) {
            $query=$this->connection->prepare("SELECT * FROM `".$table."`");
            $query->execute();
            $i=1;
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $inserts=array();
                foreach($row as $key => $val){
                    if (in_array($this->tableList[$table][$key],array('tinyint','smallint','int','bigint'))) {
                        $inserts[]=$val;
                    } else if ($this->tableList[$table][$key]!='blob') {
                        $inserts[]="'".str_replace("\r\n",'\r\n',$val)."'";
                    } else {
                        $inserts[]='0x'.bin2hex($val);
                    }
                }
                if ($this->count==$i) {
                    $this->SQLDump .='('.implode(', ',$inserts).');'."\n\n\n";
                } else {
                    $this->SQLDump .='('.implode(', ',$inserts).'),'."\n";
                }
                $i++;
            }
        }
        public function createDump () {
            foreach ($this->tableList as $table => $value) {
                $this->getContent($table);
            }
            $this->connection->query("UNLOCK TABLES");
        }
        public function getDump () {
            return $this->SQLDump;
        }
        function __destruct() {
            unset($this->connection);
            unset($this->tableList);
        }
    }
}