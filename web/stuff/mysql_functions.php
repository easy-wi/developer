<?php

/**
 * File: mysql_functions.php.
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
	function __construct($ip,$port,$user,$password) {
		try {

			$this->remotesql = new PDO('mysql:host=' .$ip . ';' . $port . '=' . $port,$user,$password);
            $this->remotesql->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $error) {
			$this->error = $error->getMessage();
		}

		if (!isset($this->error)) {
			$this->error='ok';
		}
	}
	function AddUser ($username,$password,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour) {

        if ($this->error!='ok') {
			return $this->error;
		}

        try {

            $query = $this->remotesql->prepare("CREATE USER ?@'' IDENTIFIED BY ?");
            $query->execute(array($username,$password));

            $query = $this->remotesql->prepare("GRANT USAGE ON *.* TO ?@'' IDENTIFIED BY ? WITH MAX_QUERIES_PER_HOUR $max_queries_per_hour MAX_CONNECTIONS_PER_HOUR $max_connections_per_hour MAX_UPDATES_PER_HOUR $max_updates_per_hour MAX_USER_CONNECTIONS $max_userconnections_per_hour");
            $query->execute(array($username,$password));

            $this->remotesql->exec("FLUSH PRIVILEGES; FLUSH HOSTS;");

            return 'ok';

        } catch (PDOException $error) {
            return $error->getMessage();
        }
	}
	function AddDB ($dbname,$password,$ips,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour) {
		if ($this->error!='ok') {
			return $this->error;
		}

        try {

            $query = $this->remotesql->prepare("CREATE USER ?@'' IDENTIFIED BY ?");
            $query->execute(array($dbname,$password));

            $query = $this->remotesql->prepare("GRANT USAGE ON *.* TO ?@'' IDENTIFIED BY ? WITH MAX_QUERIES_PER_HOUR $max_queries_per_hour MAX_CONNECTIONS_PER_HOUR $max_connections_per_hour MAX_UPDATES_PER_HOUR $max_updates_per_hour MAX_USER_CONNECTIONS $max_userconnections_per_hour");
            $query->execute(array($dbname,$password));

            $query = $this->remotesql->prepare("GRANT USAGE ON *.* TO ?@'localhost' IDENTIFIED BY ? WITH MAX_QUERIES_PER_HOUR $max_queries_per_hour MAX_CONNECTIONS_PER_HOUR $max_connections_per_hour MAX_UPDATES_PER_HOUR $max_updates_per_hour MAX_USER_CONNECTIONS $max_userconnections_per_hour");
            $query->execute(array($dbname,$password));

            $query = $this->remotesql->prepare("SELECT `host` FROM `mysql`.`host` WHERE `host`='localhost' AND `db`='%' LIMIT 1");
            $query->execute(array());
            if ($query->rowCount()==0) {
                $this->remotesql->exec("INSERT INTO `mysql`.`host` (`host`,`db`,`Select_priv`,`Insert_priv`,`Update_priv`,`Delete_priv`,`Create_priv`,`Drop_priv`,`Alter_priv`) VALUES ('localhost','%','Y','Y','Y','Y','Y','Y','Y')");
            }

            $this->remotesql->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");

            $query = $this->remotesql->prepare("GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,REFERENCES,INDEX,ALTER,CREATE TEMPORARY TABLES,LOCK TABLES,CREATE VIEW,SHOW VIEW,CREATE ROUTINE,ALTER ROUTINE,EXECUTE ON `$dbname`.* TO ?@''");
            $query->execute(array($dbname));


            $query = $this->remotesql->prepare("SELECT `host` FROM `mysql`.`host` WHERE `host`=? AND `db`=? LIMIT 1");
            $query2 = $this->remotesql->prepare("INSERT INTO `mysql`.`host` (`host`,`db`,`Select_priv`,`Insert_priv`,`Update_priv`,`Delete_priv`,`Create_priv`,`Drop_priv`,`Alter_priv`) VALUES (?,?,'Y','Y','Y','Y','Y','Y','Y')");
            foreach (ipstoarray($ips) as $ip) {

                $query->execute(array($ip,$dbname));

                if ($query->rowCount()==0) {
                    $query2->execute(array($ip,$dbname));
                }
            }
            $this->remotesql->exec("FLUSH PRIVILEGES; FLUSH HOSTS;");

        } catch (PDOException $error) {
            return $error->getMessage();
        }

        return 'ok';
	}
	function ModDB ($dbname,$password,$ips,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour) {

        if ($this->error!='ok') {
			return $this->error;
		}

        try {

            $query = $this->remotesql->prepare("SET PASSWORD FOR ?@'' = PASSWORD(?)");
            $query->execute(array($dbname,$password));

            $this->remotesql->exec("GRANT USAGE ON * . * TO '$dbname'@'' WITH MAX_QUERIES_PER_HOUR $max_queries_per_hour MAX_CONNECTIONS_PER_HOUR $max_connections_per_hour MAX_UPDATES_PER_HOUR $max_updates_per_hour MAX_USER_CONNECTIONS $max_userconnections_per_hour");

            $query = $this->remotesql->prepare("GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,REFERENCES,INDEX,ALTER,CREATE TEMPORARY TABLES,LOCK TABLES,CREATE VIEW,SHOW VIEW,CREATE ROUTINE,ALTER ROUTINE,EXECUTE ON `$dbname`.* TO ?@''");
            $query->execute(array($dbname));

            $iparray=ipstoarray($ips);
            $allowedips = array();

            $query = $this->remotesql->prepare("SELECT `host` FROM `mysql`.`host` WHERE `db`=?");
            $query->execute(array($dbname));
            foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                $allowedips[] = $row['host'];
            }

            $query = $this->remotesql->prepare("INSERT INTO `mysql`.`host` (`host`,`db`,`Select_priv`,`Insert_priv`,`Update_priv`,`Delete_priv`,`Create_priv`,`Drop_priv`,`Alter_priv`) VALUES (?,?,'Y','Y','Y','Y','Y','Y','Y')");
            foreach ($iparray as $ip) {
                if (!in_array($ip,$allowedips)) {
                    $query->execute(array($ip,$dbname));
                }
            }

            $query = $this->remotesql->prepare("DELETE FROM `mysql`.`host` WHERE `host`=? AND `db`=? LIMIT 1");
            foreach ($allowedips as $ip) {
                if (!in_array($ip,$iparray)) {
                    $query->execute(array($ip,$dbname));
                }
            }

            $this->remotesql->exec("FLUSH PRIVILEGES; FLUSH HOSTS;");

        } catch (PDOException $error) {
            return $error->getMessage();
        }

        return 'ok';
	}
	function DelDB ($dbname) {

		if ($this->error!='ok') {
			return $this->error;
		}

        try {

            $query = $this->remotesql->prepare("DELETE FROM `mysql`.`host` WHERE `db`=?");
            $query->execute(array($dbname));

            $this->remotesql->exec("DROP DATABASE `$dbname`");

            $this->remotesql->exec("FLUSH PRIVILEGES; FLUSH HOSTS;");

        } catch (PDOException $error) {
            return $error->getMessage();
        }

        return 'ok';
	}

	function DelUser ($username) {

		if ($this->error != 'ok') {
			return $this->error;
        }

        try {

            $query = $this->remotesql->prepare("DROP USER ?@''");
            $query->execute(array($username));

            $query = $this->remotesql->prepare("DROP USER ?@'localhost'");
            $query->execute(array($username));

            $this->remotesql->exec("FLUSH PRIVILEGES; FLUSH HOSTS;");

        } catch (PDOException $error) {
            return $error->getMessage();
        }

        return 'ok';
	}

	function __destruct() {
		$this->remotesql = null;
	}
}