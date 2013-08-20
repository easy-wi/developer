<?php 
/**
 * File: api_users.php.
 * Author: Ulrich Block
 * Copyright 2010-2012
 * Contact: support@easy-wi.com
 * Page: easy-wi.com
 */

// include config file
require_once ('api_config.php');

// Initial parameters
$error=array();

// There is no need to check every user every time
// Start looking only for new IDs
if (isset($_GET['lastID']) and is_numeric($_GET['lastID'])) {
	$lastID=(int)$_GET['lastID'];
} else {
	$lastID=0;
}

// this requieres that a column exists which is updated every time the account gets an update:
// ALTER TABLE `yourUserTable` ADD COLUMN `updatetime` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// This might lead to false posivives if data like the logintime is stored in that table.
// The more accurate way would be to fill/update the column only in wanted cases
if (isset($_GET['updateTime']) and @strtotime($_GET['updateTime'])) {

	// convert to string and back to date so proper format is ensured
	$updateTime=date('Y-m-d H:i:s',strtotime($_GET['updateTime']));
} else {
	$updateTime='0000-00-00 00:00:00';
}

// Processing all users at once can lead to memory issues if system is small or userbase large.
if (isset($_GET['chunkSize']) and is_numeric($_GET['chunkSize'])) {
	$chunkSize=(int)$_GET['chunkSize'];
} else {
	$chunkSize=10;
}

// To be able to properly get data in chunks the starting point needs to be defined.
if (isset($_GET['start']) and is_numeric($_GET['start'])) {
	$start=(int)$_GET['start'];
} else {
	$start=0;
}

// Check if the IP is whitelisted
if(isset($_SERVER['REMOTE_ADDR']) and in_array($_SERVER['REMOTE_ADDR'],$config['allowedIPs'])) {
	$config['externalIP']=$_SERVER['REMOTE_ADDR'];
} else {
	$error[]='Scipt called locally or IP is not whitelisted.';
}

// Check if access token was send and is correct
if (!isset($_GET['passwordToken'])) {
	$error[]='No password token has been send.';
} else if ($_GET['passwordToken']!=$config['passwordToken']) {
	$error[]='Bad password token has been send.';
}

// Send header data
header("Content-type: application/json; charset=UTF-8");

// If there was an error send error and stop script
if (count($error)>0) {
	echo json_encode(array('error'=>$error));

// Else check for new users
} else {

	// Establish database connection
	try {
		$connection=new PDO("mysql:host=".$config['dbHost'].";dbname=".$config['dbName'],$config['dbUser'],$config['dbPwd'],array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
		$connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 		
		// Get amount of users that are new or received an update
		// The Query needs to be altered to your database. This is just an example!
		$sql="SELECT COUNT(`userID`) AS `amount` FROM `ws_C4J_user`
		WHERE (`userID`>? OR `updatetime`>?) AND `activated`=1 AND `banned` IS NULL";
		$query=$connection->prepare($sql);
		$query->execute(array($lastID,$updateTime));			
		$total=$query->fetchColumn();

		// JSON array
		$json=array();
		
		// This query fetches the actual data.
		// The Query needs to be altered to your database. This is just an example!
		// specify the needed columns to reduce database load.
		$sql="SELECT `userID`,`email`,`username`,`firstname`,`lastname`,`birthday`,`country`,`tel`,`fax`,`mobile`,`town`,`postcode`,`street`,`streetnr`,`updatetime`
		FROM `usertable`
		WHERE (`userID`>? OR `updatetime`>?) AND `activated`=1 AND (`banned` IS NULL OR `banned`='')
		ORDER BY `userID`
		LIMIT $start,$chunkSize";
		
		$query=$connection->prepare($sql);
		$query->execute(array($lastID,$updateTime));
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
			// Easy-Wi stores the salutation with numbers
			if (isset($row['salutation']) and $row['salutation']=='mr') {
				$salutation=1;
			} else if (isset($row['salutation']) and $row['salutation']=='ms') {
				$salutation=2;
			} else {
				$salutation='';
			}
			// the keys need to be adjusted to your table layout and query!
			$json[]=array(
				'externalID'=>$row['userID'],
				'salutation'=>$salutation,
				'email'=>$row['email'],
				'loginName'=>$row['username'],
				'firstName'=>$row['firstname'],
				'lastName'=>$row['lastname'],
				'birthday'=>$row['birthday'],
				'country'=>$row['country'],
				'phone'=>$row['tel'],
				'fax'=>$row['fax'],
				'handy'=>$row['mobile'],
				'city'=>$row['town'],
				'cityn'=>$row['postcode'],
				'street'=>$row['street'],
				'streetn'=>$row['streetnr'],
				'updatetime'=>$row['updatetime']
				);
		}
		
		// Echo the JSON reply with 
		echo json_encode(array('total'=>$total,'users'=>$json));
	}
	
	// Catch database error and display
	catch(PDOException $error) {
		echo json_encode(array('error'=>$error->getMessage()));
	}
}