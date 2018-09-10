<?php

/**
 * File: class_mysql.php.
 * Author: Ulrich Block
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

class ExternalSQL {

    private $remotesql;
    public $error;

	public function __construct($ip, $port, $user, $password) {

		try {

			$this->remotesql = new PDO('mysql:host=' . $ip . ';' . $port . '=' . $port, $user, $password);
            $this->remotesql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $error) {
			$this->error = $error->getMessage();
		}

		if (!isset($this->error)) {
			$this->error = 'ok';
		}
	}

    private function errorReturn($sqlError, $sql) {
        return $sqlError . ' while executing the SQL statement: ' . $sql;
    }

    public function getDBSizeList () {

        if ($this->error != 'ok') {
            return $this->error;
        }

        try {

            $query = $this->remotesql->prepare("SELECT `table_schema` AS `dbName`,ROUND(SUM(`data_length` + `index_length`)/1048576, 1) AS `dbSize` FROM `information_schema`.`tables` GROUP BY `table_schema`");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    private function flushAll() {

        try {

            $sql = "FLUSH PRIVILEGES";
            $this->remotesql->exec($sql);

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        try {

            $sql = "FLUSH HOSTS";
            $this->remotesql->exec($sql);

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        return true;
    }

    public function AddUser ($username, $password, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour) {

        if ($this->error != 'ok') {
			return $this->error;
		}

        try {

            $sql = "CREATE USER ?@'' IDENTIFIED BY ?";
            $query = $this->remotesql->prepare($sql);
            $query->execute(array($username, $password));

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        try {

            $sql = "GRANT USAGE ON *.* TO ?@'' IDENTIFIED BY ? WITH MAX_QUERIES_PER_HOUR " . $max_queries_per_hour . " MAX_CONNECTIONS_PER_HOUR " . $max_connections_per_hour . " MAX_UPDATES_PER_HOUR " . $max_updates_per_hour . " MAX_USER_CONNECTIONS " . $max_userconnections_per_hour;
            $query = $this->remotesql->prepare($sql);
            $query->execute(array($username, $password));

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        $dbReturn = $this->flushAll();

        if ($dbReturn !== true) {
            return $dbReturn;
        }

        return 'ok';
	}

    private function noHostTable() {

        $sql = "SELECT VERSION()";
        $query = $this->remotesql->prepare($sql);
        $query->execute(array());

        list($version) = explode('-', $query->fetchColumn());

        return (version_compare($version, '5.6.7') >= 0);
    }

    private function checkUserHostExists($dbname, $hostName) {

        $sql = "SELECT 1 AS `found` FROM `mysql`.`user` WHERE  `User`=? AND `Host`=? LIMIT 1";
        $query = $this->remotesql->prepare($sql);
        $query->execute(array($dbname, $hostName));

        return ($query->fetchColumn() == 1);
    }

    private function createUserOn($hostName, $dbname, $password) {

        if ($this->checkUserHostExists($dbname, $hostName)) {
            return true;
        }

        try {

            $sql = "CREATE USER ?@? IDENTIFIED BY ?";
            $query = $this->remotesql->prepare($sql);
            $query->execute(array($dbname, $hostName, $password));

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        return true;
    }

    private function grantUsageOn($hostName, $dbname, $password, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour) {

        try {

            $sql = "GRANT USAGE ON *.* TO ?@? IDENTIFIED BY ? WITH MAX_QUERIES_PER_HOUR " . $max_queries_per_hour . " MAX_CONNECTIONS_PER_HOUR " . $max_connections_per_hour . " MAX_UPDATES_PER_HOUR " . $max_updates_per_hour . " MAX_USER_CONNECTIONS " . $max_userconnections_per_hour;
            $query = $this->remotesql->prepare($sql);
            $query->execute(array($dbname, $hostName, $password));

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        return true;
    }

    private function addLocalHostEntry() {

        try {

            $sql = "SELECT `host` FROM `mysql`.`host` WHERE `host`='localhost' AND `db`='%' LIMIT 1";
            $query = $this->remotesql->prepare($sql);
            $query->execute(array());

            if ($query->rowCount()==0) {

                try {

                    $sql = "INSERT INTO `mysql`.`host` (`host`,`db`,`Select_priv`,`Insert_priv`,`Update_priv`,`Delete_priv`,`Create_priv`,`Drop_priv`,`Alter_priv`) VALUES ('localhost','%','Y','Y','Y','Y','Y','Y','Y')";
                    $this->remotesql->exec($sql);

                } catch (PDOException $error) {
                    return $this->errorReturn($error->getMessage(), $sql);
                }
            }

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        return true;
    }

    private function grantAll($hostName, $dbname) {

        try {

            $sql = "GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,REFERENCES,INDEX,ALTER,CREATE TEMPORARY TABLES,LOCK TABLES,CREATE VIEW,SHOW VIEW,CREATE ROUTINE,ALTER ROUTINE,EXECUTE ON `" . $dbname . "`.* TO ?@?";
            $query = $this->remotesql->prepare($sql);
            $query->execute(array($dbname, $hostName));

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        return true;
    }

    private function grantCreate($hostNames, $dbname, $password, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour) {

        foreach ($hostNames as $hostName) {

            $dbReturn = $this->createUserOn($hostName, $dbname, $password);

            if ($dbReturn !== true) {
                return $dbReturn;
            }

            $dbReturn = $this->grantUsageOn($hostName, $dbname, $password, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour);

            if ($dbReturn !== true) {
                return $dbReturn;
            }

            $dbReconfigure = $this->grantAll($hostName, $dbname);

            if ($dbReconfigure !== true) {
                return $dbReconfigure;
            }
        }

        return true;
    }

    private function createGrantFlush($dbname, $password, $ips, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour) {

        $hostList = array_merge(array('localhost'), ipstoarray($ips));

        $sql = "DROP USER IF EXISTS ?@?";
        $query = $this->remotesql->prepare($sql);

        foreach ($this->getUsersHostList($dbname) as $host) {
            if (!in_array($host, $hostList)) {
                $query->execute(array($dbname, $host));
            }
        }

        $dbReturn = $this->grantCreate($hostList, $dbname, $password, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour);

        if ($dbReturn !== true) {
            return $dbReturn;
        }

        if (!$this->noHostTable()) {

            $dbReturn = $this->addLocalHostEntry();

            if ($dbReturn !== true) {
                return $dbReturn;
            }

            $dbReturn = $this->reconfigureHostTable($ips, $dbname);

            if ($dbReturn !== true) {
                return $dbReturn;
            }
        }

        $dbReturn = $this->flushAll();

        if ($dbReturn !== true) {
            return $dbReturn;
        }

        return true;
    }

    public function AddDB ($mailData, $dbname, $password, $ips, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour) {

		if ($this->error != 'ok') {
			return $this->error;
		}

        try {

            $sql = "CREATE DATABASE IF NOT EXISTS `" . $dbname . "`";
            $this->remotesql->exec($sql);

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        $dbReturn = $this->createGrantFlush($dbname, $password, $ips, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour);

        if ($dbReturn !== true) {
            return $dbReturn;
        }

        sendmail('emailserverinstall', $mailData['userId'], $mailData['name'], 'MySQL', $mailData['mailConnectInfo']);

        return 'ok';
	}

    private function reconfigureHostTable($ips, $dbname) {

        $iparray = ipstoarray($ips);
        $allowedips = array();

        try {
            $sql = "SELECT `host` FROM `mysql`.`host` WHERE `db`=?";
            $query = $this->remotesql->prepare($sql);
            $query->execute(array($dbname));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $allowedips[] = $row['host'];
            }

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        try {

            $sql = "INSERT INTO `mysql`.`host` (`host`,`db`,`Select_priv`,`Insert_priv`,`Update_priv`,`Delete_priv`,`Create_priv`,`Drop_priv`,`Alter_priv`) VALUES (?,?,'Y','Y','Y','Y','Y','Y','Y')";
            $query = $this->remotesql->prepare($sql);

            foreach ($iparray as $ip) {
                if (!in_array($ip, $allowedips)) {
                    $query->execute(array($ip, $dbname));
                }
            }

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        try {

            $sql = "DELETE FROM `mysql`.`host` WHERE `host`=? AND `db`=? LIMIT 1";
            $query = $this->remotesql->prepare($sql);

            foreach ($allowedips as $ip) {
                if (!in_array($ip, $iparray)) {
                    $query->execute(array($ip, $dbname));
                }
            }

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        return true;
    }

    public function ModDB ($dbname, $password, $ips, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour) {

        if ($this->error!='ok') {
			return $this->error;
		}

        $dbReturn = $this->createGrantFlush($dbname, $password, $ips, $max_queries_per_hour, $max_connections_per_hour, $max_updates_per_hour, $max_userconnections_per_hour);

        if ($dbReturn !== true) {
            return $dbReturn;
        }

        try {
            if ($this->noHostTable()) {
                $sql = "UPDATE `mysql`.`user` SET `authentication_string`=PASSWORD(?) WHERE`User`=?";
            } else {
                $sql = "UPDATE `mysql`.`user` SET `Password`=PASSWORD(?) WHERE `User`=?";
            }

            $query = $this->remotesql->prepare($sql);
            $query->execute(array($password, $dbname));

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        return 'ok';
	}

    public function DelDB ($dbname) {

		if ($this->error!='ok') {
			return $this->error;
		}

        if (!$this->noHostTable()) {
            try {
                $sql = "DELETE FROM `mysql`.`host` WHERE `db`=?";
                $query = $this->remotesql->prepare($sql);
                $query->execute(array($dbname));
            } catch (PDOException $error) {
                return $this->errorReturn($error->getMessage(), $sql);
            }
        }

        try {
            $sql = "DROP DATABASE `" . $dbname . "`";
            $this->remotesql->exec($sql);
        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        $dbReturn = $this->flushAll();

        if ($dbReturn !== true) {
            return $dbReturn;
        }

        return 'ok';
	}

    private function getUsersHostList($username) {

        $hosts = array();

        $sql = "SELECT `Host` FROM `mysql`.`user` WHERE `User`=?";
        $query = $this->remotesql->prepare($sql);
        $query->execute(array($username));

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $hosts[] = $row['Host'];
        }

        return $hosts;
    }

    public function DelUser ($username) {

		if ($this->error != 'ok') {
			return $this->error;
        }

        try {

            $sql = "DROP USER ?@?";
            $query = $this->remotesql->prepare($sql);

            foreach ($this->getUsersHostList($username) as $host) {
                $query->execute(array($username, $host));
            }

        } catch (PDOException $error) {
            return $this->errorReturn($error->getMessage(), $sql);
        }

        return 'ok';
	}

	function __destruct() {
		$this->remotesql = null;
        unset($this->error);
	}
}
