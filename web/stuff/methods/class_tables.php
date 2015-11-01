<?php

/**
 * File: class_tables.php.
 * Author: Ulrich Block
 * Date: 17.10.15
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

class Tables {

    private $dbName;
    private $sql;
    private $recreatePrimaryKey = false;
    private $primaryKeyExist = false;
    private $tableDefinitions = array();
    private $tableConfigurations = array();
    private $executedSql = array();

    function __construct($dbName) {

        global $sql;

        $this->dbName = $dbName;
        $this->sql = $sql;

        $this->getTableDefinitions();
        $this->getTablesColumnConfigurations();
    }

    function __destruct() {
        unset($this->tableDefinitions, $this->tableConfigurations, $this->sql);
    }

    private function getTableDefinitions() {

        $defined = array();

        $dataPath = EASYWIDIR . '/stuff/data/';

        // Grab the dir with the table table definitions inside
        $dir = dir($dataPath);

        // Now lets loop the directories
        while (false !== ($entry = $dir->read())) {

            $matches = null;

            preg_match('/^table_([\w_]{1,})\.php$/', $entry, $matches);

            // Only relevant files should be included in the next step
            if (!is_file($dataPath . $entry) or !isset($matches[1]) or isset($defined[$matches[1]])) {
                continue;
            }

            include($dataPath . $entry);
        }

        // sort
        ksort($defined);

        // Make definitions internally available
        $this->tableDefinitions = $defined;
    }

    private function getTablesColumnConfigurations() {

        foreach (array_keys($this->tableDefinitions) as $tableName) {

            $query = $this->sql->prepare("SHOW COLUMNS FROM `{$tableName}`");
            $query->execute(array($this->dbName, $tableName));

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $this->tableConfigurations[$tableName][$row['Field']] = array(
                    'Type' => $row['Type'],
                    'Null' => $row['Null'],
                    'Key' => $row['Key'],
                    'Default' => $row['Default'],
                    'Extra' => $row['Extra']
                );
            }
        }
    }

    private function executeChange($sqlStatement) {

        $query = $this->sql->prepare($sqlStatement);
        $query->execute();

        $this->executedSql[] = $sqlStatement;
    }

    private function correctTableStatus($tableName) {

        $query = $this->sql->prepare("SHOW TABLE STATUS LIKE '{$tableName}'");
        $query->execute();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            if ($row['Engine'] == 'MyISAM') {
                $this->executeChange("ALTER TABLE `{$tableName}` ENGINE = InnoDB");
            }

            if ($row['Collation'] != 'utf8_general_ci') {
                $this->executeChange("ALTER TABLE `{$tableName}` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
            }
        }
    }

    public function correctTablesStatus() {
        foreach (array_keys($this->tableDefinitions) as $tableName) {
            $this->correctTableStatus($tableName);
        }
    }

    private function generateNullCommand($defaultValue) {
        return ($defaultValue == 'NO') ? 'NOT NULL' : 'NULL';
    }

    private function generateDefaultCommand($definitions) {

        if ($definitions['Extra'] == 'auto_increment') {
            return "AUTO_INCREMENT";
        }

        if (in_array($definitions['Default'], array('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')) or preg_match('/^([\d]{1,}|[\d]{1,}.[\d]{1,})$/', $definitions['Default'])) {
            return 'DEFAULT ' . $definitions['Default'];
        }

        if ($definitions['Default'] != '') {
            return "DEFAULT '{$definitions['Default']}'";
        }

        if ($definitions['Null'] == 'NO') {
            return true;
        }

        if ($definitions['Null'] == 'YES' and $definitions['Default'] == '') {
            return "DEFAULT NULL";
        }

        if ($definitions['Default'] == '' and strpos($definitions['Type'], 'char') !== false) {
            return "DEFAULT ''";
        }

        if ($definitions['Default'] == '' and strpos($definitions['Type'], 'int') !== false) {
            return "DEFAULT 0";
        }

        return '';
    }

    private function columnsToCreateSQL($tableName) {

        $entries = array();
        $primaryKey = array();
        $keys = array();

        foreach ($this->tableDefinitions[$tableName] as $columnName => $definitions) {

            $entries[] = '`' . $columnName . '` ' . $definitions['Type'] . ' ' . $this->generateNullCommand($definitions['Null']) . ' ' . $this->generateDefaultCommand($definitions);

            if ($definitions['Key'] == 'PRI') {
                $primaryKey[] = $columnName;
            }

            if ($definitions['Key'] == 'MUL') {
                $keys[] = $columnName;
            }
        }

        if (count($primaryKey) > 0) {
            $entries[] = 'PRIMARY KEY (`' . implode('`,`', $primaryKey) . '`)';
        }

        foreach ($keys as $key) {
            $entries[] = 'KEY (`' . $key . '`)';
        }

        return implode(',', $entries);
    }

    private function generateCreateSQL($tableName) {
        return "CREATE TABLE IF NOT EXISTS `{$tableName}` (" . $this->columnsToCreateSQL($tableName) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
    }

    public function createMissingTables() {

        foreach (array_keys($this->tableDefinitions) as $tableName) {
            if (!isset($this->tableConfigurations[$tableName])) {
                $this->executeChange($this->generateCreateSQL($tableName));
            }
        }
    }

    private function removeColumnsAndIndexes($tableName) {

        $entries = array();

        foreach ($this->tableConfigurations[$tableName] as $columnName => $definitions) {

            if (!isset($this->tableDefinitions[$tableName][$columnName])) {
                $entries[] = 'DROP COLUMN `' . $columnName . '`';
            } else if ($definitions['Key'] == 'MUL' and $this->tableDefinitions[$tableName][$columnName]['Key'] != 'MUL') {
                array_unshift($entries, 'DROP INDEX `' . $columnName . '`');
            } else if ($definitions['Key'] == 'PRI') {

                $this->primaryKeyExist = true;

                if ($this->tableDefinitions[$tableName][$columnName]['Key'] != 'PRI') {
                    $this->recreatePrimaryKey = true;
                }
            }
        }

        if (count($entries) > 0) {
            $this->executeChange('ALTER TABLE `' . $tableName . '` ' . implode(', ', $entries));
        }
    }

    private function recreatePrimaryKey($tableName, $columns) {

        if ($this->recreatePrimaryKey == true) {

            if ($this->primaryKeyExist == true) {
                $this->executeChange('ALTER TABLE `' . $tableName . '` DROP PRIMARY KEY');
            }

            if (count($columns) > 0) {
                $this->executeChange('ALTER TABLE `' . $tableName . '` ADD PRIMARY KEY(' . implode(',', $columns) . ')');
            }
        }
    }

    private function changeConfiguredColumn($tableName, $columnName, $definitions) {

        if ($definitions['Type'] !== $this->tableConfigurations[$tableName][$columnName]['Type']) {
            return true;
        }

        if ($definitions['Default'] !== $this->tableConfigurations[$tableName][$columnName]['Default']) {

            if ($definitions['Extra'] == 'auto_increment') {
                return false;
            }

            if ($definitions['Default'] != $this->tableConfigurations[$tableName][$columnName]['Default']) {
                return true;
            }

            if ($definitions['Null'] == 'NO' or $this->tableConfigurations[$tableName][$columnName]['Default'] === null) {
                return true;
            }

            if ($definitions['Null'] == 'YES' and $definitions['Default'] == '' and $this->tableConfigurations[$tableName][$columnName]['Default'] !== null) {
                return true;
            }
        }

        return false;
    }

    private function createChangeColumnsAndIndexes($tableName) {

        $entries = array();
        $primaryKeyColumns = array();
        $keys = array();

        foreach ($this->tableDefinitions[$tableName] as $columnName => $definitions) {

            $columnMissing = (!isset($this->tableConfigurations[$tableName][$columnName]));

            if ($columnMissing) {
                $entries[] = 'ADD COLUMN `' . $columnName . '` ' . $definitions['Type'] . ' ' . $this->generateNullCommand($definitions['Null']) . ' ' . $this->generateDefaultCommand($definitions);
            } else if ($this->changeConfiguredColumn($tableName, $columnName, $definitions)) {
                $entries[] = 'CHANGE COLUMN `' . $columnName . '` `' . $columnName . '` ' . $definitions['Type'] . ' ' . $this->generateNullCommand($definitions['Null']) . ' ' . $this->generateDefaultCommand($definitions);
            }

            if ($definitions['Key'] == 'MUL' and ($columnMissing or $this->tableConfigurations[$tableName][$columnName]['Key'] != 'MUL')) {
                $keys[] = 'ADD INDEX (`' . $columnName . '`)';
            }

            if ($definitions['Key'] == 'PRI' and ($columnMissing or $this->tableConfigurations[$tableName][$columnName]['Key'] != 'PRI')) {
                $this->recreatePrimaryKey = true;
            }

            if ($definitions['Key'] == 'PRI') {
                $primaryKeyColumns[] = '`' . $columnName . '`';
            }
        }

        $entries = array_merge($entries, $keys);

        if (count($entries) > 0) {
            $this->executeChange('ALTER TABLE `' . $tableName . '` ' . implode(', ', $entries));
        }

        $this->recreatePrimaryKey($tableName, $primaryKeyColumns);
    }

    private function compareDefinitionWithConfiguration($tableName) {

        $this->recreatePrimaryKey = false;
        $this->primaryKeyExist = false;

        $this->removeColumnsAndIndexes($tableName);
        $this->createChangeColumnsAndIndexes($tableName);
    }

    public function correctExistingTables() {

        foreach (array_keys($this->tableDefinitions) as $tableName) {
            if (isset($this->tableConfigurations[$tableName])) {
                $this->compareDefinitionWithConfiguration($tableName);
            }
        }
    }

    public function getExecutedSql() {
        return $this->executedSql;
    }
}