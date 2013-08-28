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
			$this->remotesql=new PDO("mysql:host=$ip;port=$port",$user,$password);
		}
		catch(PDOException $error) {
			$this->error=$error->getMessage();
		}
		if (!isset($this->error)) {
			$this->error='ok';
		}
	}
	function AddUser ($username,$password,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour) {
		if ($this->error!='ok') {
			return $this->error;
		} else {
			$createuser=$this->remotesql->prepare("CREATE USER ?@'' IDENTIFIED BY ?");
			$createuser->execute(array($username,$password));
			$grantusageon=$this->remotesql->prepare("GRANT USAGE ON *.* TO ?@'' IDENTIFIED BY ? WITH MAX_QUERIES_PER_HOUR ? MAX_CONNECTIONS_PER_HOUR ? MAX_UPDATES_PER_HOUR ? MAX_USER_CONNECTIONS ?");
			$grantusageon->execute(array($username,$password,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour));
            $this->remotesql->exec("FLUSH PRIVILEGES; FLUSH HOSTS;");
			return 'ok';
		}
	}
	function AddDB ($dbname,$password,$ips,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour) {
		if ($this->error!='ok') {
			return $this->error;
		} else {
			$createuser=$this->remotesql->prepare("CREATE USER ?@'' IDENTIFIED BY ?");
			$createuser->execute(array($dbname,$password));
			$grantusageon=$this->remotesql->prepare("GRANT USAGE ON *.* TO ?@'' IDENTIFIED BY ? WITH MAX_QUERIES_PER_HOUR ? MAX_CONNECTIONS_PER_HOUR ? MAX_UPDATES_PER_HOUR ? MAX_USER_CONNECTIONS ?");
			$grantusageon->execute(array($dbname,$password,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour));
            $grantusageon=$this->remotesql->prepare("GRANT USAGE ON *.* TO ?@'localhost' IDENTIFIED BY ? WITH MAX_QUERIES_PER_HOUR ? MAX_CONNECTIONS_PER_HOUR ? MAX_UPDATES_PER_HOUR ? MAX_USER_CONNECTIONS ?");
            $grantusageon->execute(array($dbname,$password,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour));
			$check=$this->remotesql->prepare("SELECT `host` FROM `mysql`.`host` WHERE `host`='localhost' AND `db`='%' LIMIT 1");
			$check->execute(array());
			if ($check->rowcount()==0) {
				$this->remotesql->exec("INSERT INTO `mysql`.`host` (`host`,`db`,`Select_priv`,`Insert_priv`,`Update_priv`,`Delete_priv`,`Create_priv`,`Drop_priv`,`Alter_priv`) VALUES ('localhost','%','Y','Y','Y','Y','Y','Y','Y')");
			}
			$this->remotesql->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
			$grantpriviliges=$this->remotesql->prepare("GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,REFERENCES,INDEX,ALTER,CREATE TEMPORARY TABLES,LOCK TABLES,CREATE VIEW,SHOW VIEW,CREATE ROUTINE,ALTER ROUTINE,EXECUTE ON `$dbname`.* TO ?@''");
			$grantpriviliges->execute(array($dbname));
			foreach (ipstoarray($ips) as $ip) {
				$check2=$this->remotesql->prepare("SELECT `host` FROM `mysql`.`host` WHERE `host`=? AND `db`=? LIMIT 1");
				$check2->execute(array($ip,$dbname));
				if ($check2->rowcount()==0) {
					$createaccess=$this->remotesql->prepare("INSERT INTO `mysql`.`host` (`host`,`db`,`Select_priv`,`Insert_priv`,`Update_priv`,`Delete_priv`,`Create_priv`,`Drop_priv`,`Alter_priv`) VALUES (?,?,'Y','Y','Y','Y','Y','Y','Y')");
					$createaccess->execute(array($ip,$dbname));
				}
			}
            $this->remotesql->exec("FLUSH PRIVILEGES; FLUSH HOSTS;");
			return 'ok';
		}
	}
	function ModDB ($dbname,$password,$ips,$max_queries_per_hour,$max_connections_per_hour,$max_updates_per_hour,$max_userconnections_per_hour) {
		if ($this->error!='ok') {
			return $this->error;
		} else {
			$grantusageon=$this->remotesql->prepare("SET PASSWORD FOR ?@'' = PASSWORD(?)");
			$grantusageon->execute(array($dbname,$password));
			$this->remotesql->exec("GRANT USAGE ON * . * TO '$dbname'@'' WITH MAX_QUERIES_PER_HOUR $max_queries_per_hour MAX_CONNECTIONS_PER_HOUR $max_connections_per_hour MAX_UPDATES_PER_HOUR $max_updates_per_hour MAX_USER_CONNECTIONS $max_userconnections_per_hour");
			$grantpriviliges=$this->remotesql->prepare("GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,REFERENCES,INDEX,ALTER,CREATE TEMPORARY TABLES,LOCK TABLES,CREATE VIEW,SHOW VIEW,CREATE ROUTINE,ALTER ROUTINE,EXECUTE ON `$dbname`.* TO ?@''");
			$grantpriviliges->execute(array($dbname));
			$iparray=ipstoarray($ips);
			$allowedips=array();
			$select=$this->remotesql->prepare("SELECT `host` FROM `mysql`.`host` WHERE `db`=?");
			$select->execute(array($dbname));
			foreach ($select->fetchall() as $row) {
				$allowedips[]=$row['host'];
			}
			foreach ($iparray as $ip) {
				if (!in_array($ip,$allowedips)) {
					$createaccess=$this->remotesql->prepare("INSERT INTO `mysql`.`host` (`host`,`db`,`Select_priv`,`Insert_priv`,`Update_priv`,`Delete_priv`,`Create_priv`,`Drop_priv`,`Alter_priv`) VALUES (?,?,'Y','Y','Y','Y','Y','Y','Y')");
					$createaccess->execute(array($ip,$dbname));
				}
			}
			foreach ($allowedips as $ip) {
				if (!in_array($ip,$iparray)) {
					$delete=$this->remotesql->prepare("DELETE FROM `mysql`.`host` WHERE `host`=? AND `db`=? LIMIT 1");
					$delete->execute(array($ip,$dbname));
				}
			}
            $this->remotesql->exec("FLUSH PRIVILEGES; FLUSH HOSTS;");
			return 'ok';
		}
	}
	function DelDB ($dbname) {
		if ($this->error!='ok') {
			return $this->error;
		} else {
			$this->remotesql->exec("DROP DATABASE IF EXISTS `$dbname`");
			$delete=$this->remotesql->prepare("DELETE FROM `mysql`.`host` WHERE `db`=?");
			$delete->execute(array($dbname));
			$dropuser=$this->remotesql->prepare("DROP USER ?@''");
			$dropuser->execute(array($dbname));
            $this->remotesql->exec("FLUSH PRIVILEGES; FLUSH HOSTS;");
			return 'ok';
		}
	}
	function DelUser ($username) {
		if ($this->error!='ok') {
			return $this->error;
		} else {
			$dropuser=$this->remotesql->prepare("DROP USER ?@''");
			$dropuser->execute(array($username));
            $this->remotesql->exec("FLUSH PRIVILEGES; FLUSH HOSTS;");
			return 'ok';
		}
	}
	function __destruct() {
		$this->remotesql=null;
	}
}