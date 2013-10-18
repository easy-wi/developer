<?php

/**
 * File: api_users.php.
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

require_once 'api_config.php';
 
// Initial parameters
$error=array();
 
// There is no need to check every user every time
// Start looking only for new IDs
$lastID=(isset($_GET['lastID']) and is_numeric($_GET['lastID'])) ? (int)$_GET['lastID'] : 0;
 
// this requires that a column exists which is updated every time the account gets an update:
// ALTER TABLE `yourUserTable` ADD COLUMN `updatetime` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// This might lead to false positives if data like the login time is stored in that table.
// The more accurate way would be to fill/update the column only in wanted cases
// convert to string and back to date so proper format is ensured
$updateTime=date('Y-m-d H:i:s',strtotime((isset($_GET['updateTime']) and @strtotime($_GET['updateTime'])) ? $_GET['updateTime'] : '0000-00-00 00:00:00'));
 
// Processing all users at once can lead to memory issues if system has small recources or large database large.
$chunkSize=(isset($_GET['chunkSize']) and is_numeric($_GET['chunkSize'])) ? (int)$_GET['chunkSize'] : 10;
 
// To be able to properly get data in chunks the starting point needs to be defined.
$start=(isset($_GET['start']) and is_numeric($_GET['start'])) ? (int)$_GET['start'] : 0;
 
// Check if the IP is white listed
if(isset($_SERVER['REMOTE_ADDR']) and in_array($_SERVER['REMOTE_ADDR'],$config['allowedIPs'])) {
	$config['externalIP']=(string)$_SERVER['REMOTE_ADDR'];
} else {
	$error[]='Script called locally or IP is not white listed.';
}
 
// Check if access token was send and is correct
if (!isset($_GET['passwordToken'])) {
	$error[]='No password token has been send.';
} else if ($_GET['passwordToken']!=$config['passwordToken']) {
	$error[]='Bad password token has been send.';
}

$list=(in_array($_GET['list'],array('user','substitutes','gameserver','voicemaster','voiceserver','dedicated','virtual','hostnode'))) ? (string)$_GET['list'] : 'user';

// Send header data
header("Content-type: application/json; charset=UTF-8");
 
// If there was an error send error and stop script
if (count($error)>0) {
	echo json_encode(array('error' => $error));
 
// Else check for new users
} else {
 
	// Establish database connection
	try {
		$pdo=new PDO("mysql:host=".$config['dbHost'].";dbname=".$config['dbName'],$config['dbUser'],$config['dbPwd'],array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 
		// Define default values so we always have a proper return
		$total = 0;
		$json = array();
		
		// User export
		if ($list == 'user') {
			// This query fetches the actual data.
			// The Query needs to be altered to your database. This is just an example!
			// specify the needed columns to reduce database load.
			// webspell
			if ($config['sourceType']=='webspell') {
 
				// Get amount of users that are new or received an update
				// The Query needs to be altered to your database. This is just an example!
				$sql="SELECT COUNT(`userID`) AS `amount` FROM `{$config['tblPrefix']}_user`
				WHERE (`userID`>? OR `updatetime`>?) AND `activated`=1 AND `banned` IS NULL";
				$query=$pdo->prepare($sql);
				$query->execute(array($lastID,$updateTime));			
				$total=$query->fetchColumn();

				$sql = "SELECT * FROM `{$config['tblPrefix']}_usertable`
				WHERE (`userID`>? OR `updatetime`>?) AND `activated`=1 AND (`banned` IS NULL OR `banned`='')
				LIMIT $start,$chunkSize";
				$query=$pdo->prepare($sql);
				$query->execute(array($lastID,$updateTime));
				foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
					// Easy-Wi stores the salutation with numbers
					if (isset($row['salutation']) and $row['salutation']=='mr') {
						$salutation = 1;
					} else if (isset($row['salutation']) and $row['salutation']=='ms') {
						$salutation = 2;
					} else {
						$salutation = null;
					}
					// the keys needs to be adjusted to your table layout and query!
					$json[]=array(
						'externalID' => $row['userID'],
						'salutation' => $salutation,
						'email' => $row['email'],
						'loginName' => $row['username'],
						'firstName' => $row['firstname'],
						'lastName' => $row['lastname'],
						'birthday' => $row['birthday'],
						'country' => $row['country'],
						'phone' => $row['tel'],
						'fax' => $row['fax'],
						'handy' => $row['mobile'],
						'city' => $row['town'],
						'cityn' => $row['postcode'],
						'street' => $row['street'],
						'streetn' => $row['streetnr'],
						'updatetime' => $row['updatetime'],
						'usertype'=>'u',
						'password' => $row['password']
						);
				}

			} else if ($config['sourceType']=='teklab') {

				// Get amount of users that are new or received an update
				$sql="SELECT COUNT(`id`) AS `amount` FROM `{$config['tblPrefix']}_members`
				WHERE `rank`=1";
				$query=$pdo->prepare($sql);
				$query->execute();			
				$total=$query->fetchColumn();

				// users
				$sql = "SELECT * FROM `{$config['tblPrefix']}_members`
				WHERE `rank`=1
				LIMIT $start,$chunkSize";
				$query=$pdo->prepare($sql);
				$query->execute();
				foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

					// Teklab has also 3 for company which Easy-WI currently does not maintain
					if ($row['title'] == 0) {
						$salutation = 1;
					} else if ($row['title']==1) {
						$salutation = 2;
					} else {
						$salutation = null;
					}

					// Easy-WI uses ISO code for storing countries
					if ($row['country'] == 1) {
						$country = 'de';
					} else if ($row['country'] == 2) {
						$country = 'uk';
					} else if ($row['country'] == 3) {
						$country = 'at';
					} else if ($row['country'] == 4) {
						$country = 'ch';
					} else {
						$country = null;
					}

					// Street and streetnumber are stored in the same column Easy-WI has individual columns
					$exploded = explode(" ", $row['street']);
					if (count($exploded) > 1) {
						$streetNumber = $exploded[count($exploded) - 1];
						unset($exploded[count($exploded) - 1]);
						$streetName = implode(' ', $exploded);
					} else {
						$streetName = null;
						$streetNumber = null;
					}

					$json[]=array(
						'externalID' => $row['id'],
						'salutation' => $salutation,
						'email' => $row['email'],
						'loginName' => $row['member'],
						'firstName' => $row['surname'],
						'lastName' => $row['name'],
						'birthday'=> date('Y-m-d H:m:s', strtotime($row['birthday'])),
						'country' => $country,
						'phone' => $row['phone'],
						'fax' => $row['fax'],
						'handy' => null,
						'city' => $row['city'],
						'cityn' => $row['zipcode'],
						'street' => $streetName,
						'streetn' => $streetNumber,
						'updatetime' => null,
						'usertype'=>'u',
						'password' => $row['password']
						);
				}
			}

/**	
		} else if ($list == 'gameroots') {
			if ($config['sourceType']=='teklab') {

				// Get amount of users that are new or received an update
				$sql="SELECT COUNT(`id`) AS `amount` FROM `{$config['tblPrefix']}_rootserver`
				WHERE `active`=1
				AND `games`=1";
				$query=$pdo->prepare($sql);
				$query->execute();			
				$total=$query->fetchColumn();

				// users
				$sql = "SELECT * FROM `{$config['tblPrefix']}_rootserver`
				WHERE `active`=1
				AND `games`=1
				LIMIT $start,$chunkSize";
				$query=$pdo->prepare($sql);
				$query->execute();
				foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
					$json[]=array(
						'externalID' => $row['id'],
						'userID' => $row['memberid'],
						'description' => $row['name'],
						'serverIP' => $row['serverip'],
						'sshPort' => $row['sshport'],
						'ftpPort' => $row['ftpport'],
						'cpuCores' => $row['cpucores'],
						'maxRam' => $row['ram']
						);
				}
			
			}
		
		} else if ($list == 'gameimages') {
			if ($config['sourceType']=='teklab') {
			
			}

		} else if ($list == 'gameserver') {
			if ($config['sourceType']=='teklab') {
			
			}
/**
		} else if ($list == 'addons') {

			if ($config['sourceType']=='teklab') {

				// Get amount of users that are new or received an update
				$sql="SELECT COUNT(`id`) AS `amount` FROM `{$config['tblPrefix']}_games_addons`";
				$query=$pdo->prepare($sql);
				$query->execute();			
				$total=$query->fetchColumn();

				// users
				$sql = "SELECT * FROM `{$config['tblPrefix']}_games_addons`
				LIMIT $start,$chunkSize";
				$query=$pdo->prepare($sql);
				$query->execute();
				foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
					$json[]=array(
						'externalID' => $row['id'],
						'name' => $row['addonname'],
						'description' => $row['text'],
						'shortName' => $row['sname']
						);
				}
			
			}
**/
		} else if ($list == 'voice') {
			if ($config['sourceType']=='teklab') {
			
			}

		// Substitutes at last so we can get access permissions as well
		} else if ($list == 'substitutes') {
			if ($config['sourceType']=='teklab') {

				// Get amount of users that are new or received an update
				$sql="SELECT COUNT(`id`) AS `amount` FROM `{$config['tblPrefix']}_subusers`";
				$query=$pdo->prepare($sql);
				$query->execute();			
				$total=$query->fetchColumn();

				// users
				$sql = "SELECT * FROM `{$config['tblPrefix']}_subusers`
				LIMIT $start,$chunkSize";
				$query=$pdo->prepare($sql);
				$query->execute();
				foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
					$json[]=array(
						'externalID' => $row['id'],
						'belongsToExternalID' => $row['memberid'],
						'loginName' => $row['user'],
						'firstName' => null,
						'lastName' => null,
						'password' => $row['password']
						);
				}
			}
		}

		// Echo the JSON reply with 
		echo json_encode(array('total' => $total,'entries' => $json));

	// Catch database error and display
	} catch(PDOException $error) {
		echo json_encode(array('error' => $error->getMessage()));
	}
}