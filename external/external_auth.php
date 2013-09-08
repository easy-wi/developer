<?php

/**
 * File: external_auth.php.
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
 
$dbName='name';
$dbUser='user';
$dbPwd='pwd';
$dbHost='localhost';
$configUser='meinUsername';
$configPass='meinPasswort';
if (!isset($_POST['postXML'])) {
	$bad='No XML has been send';
}
if (!isset($bad) and isset($_POST['authPWD']) and isset($_POST['userAuth']) and $_POST['authPWD']==$configPass and $_POST['userAuth']==$configUser) {
	// postXML into object
	$xml= @simplexml_load_string(base64_decode($_POST['postXML']));
	if($xml) {
		$user=$xml->user;
		$pwd=$xml->pwd;
		$mail=$xml->mail;
		$externalID=$xml->externalID;
		$hashedPWD=md5($pwd);
		// DB Connection and search user
		try {
			$connection=new PDO("mysql:host=$dbHost;dbname=$dbName",$dbUser,$dbPwd);
			$connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

			// This is just an example! Needs to be fitted to your database structure!
			$query=$connection->prepare("SELECT `pwd` FROM `tbl_userdata` WHERE `loginname`=? LIMIT 1");
			$query->execute(array($user));
			foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$storedHash=$row['pwd'];
			}
		}
		catch(PDOException $error) {
			$bad=$error->getMessage();
		}
		if (!isset($bad) and $hashedPWD==$storedHash) {
			$responseXML = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE login>
<login>
	<user>$user</user>
	<success>1</success>
</login>
XML;
		} else if (!isset($bad)) {
			$bad='bad login data.';
		}
	} else {
		$bad='No valid XML data has been send';
	}
} else {
	$bad='Bad API auth data.';
}
header("Content-type: text/xml");
if(isset($bad)) {
	$responseXML = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE login>
<login>
	<user></user>
	<success>0</success>
	<error>$bad</error>
</login>
XML;
}
echo $responseXML;