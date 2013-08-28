<?php

/**
 * File: update.php.
 * Author: Ulrich Block
 * Date: 03.08.12
 * Time: 17:09
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


if (!isset($updateinclude) or $updateinclude==false) {
	ini_set('display_errors',1);
	error_reporting(E_ALL|E_STRICT);
    define('EASYWIDIR',$_SERVER['DOCUMENT_ROOT'].'/');
	function getconfigcvars($file) {
		$fp= @fopen($file, 'rb');
		if ($fp == true) {
			$configfile="";
			while (!feof($fp)){
				$line=fgets($fp);
				if(strpos(strtolower($line), strtolower("<?php")) === false and strpos(strtolower($line), strtolower("?>")) === false) {
					$configfile .="$line\r\n";
				}
			}
			fclose($fp);
			$lines=explode("\r\n", $configfile);
			foreach ($lines as $line) {
				if(strpos(strtolower($line), strtolower("//")) === false and strpos(strtolower($line), strtolower("=")) == true) {
					$data=explode("=", $line);
					$cvar=preg_replace('/\s+/', '', $data['0']);
					$cvar=str_replace('$', "", $cvar);
					$data2=explode(";", $data['1']);
					$stringlenght=strlen($data2['0']);
					$stop=$stringlenght-2;
					$value=substr($data2['0'],"1",$stop);
					$vars["$cvar"]=$value;
				}
			}
			return $vars;
		} else {
			die("No configdata!");
		}
	}
	function isinteger($value) {
	  if(preg_match("/^[\d+(.\d+|$)]+$/", $value) or $value=="0") {
		return true;
	  }
	}
	class UpdateResponse {
		public $response='';
		function __construct() {
			$this->response='';
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
	$panelcfgcvars=getconfigcvars("../stuff/config.php");
	$databanktype=$panelcfgcvars['databanktype'];
	$host=$panelcfgcvars['host'];
	$db=$panelcfgcvars['db'];
	$user=$panelcfgcvars['user'];
	$pwd=$panelcfgcvars['pwd'];
	$captcha=$panelcfgcvars['captcha'];
	$title=$panelcfgcvars['title'];
	try {
		$sql=new PDO("$databanktype:host=$host;dbname=$db",$user,$pwd,array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
	}
	catch(PDOException $error) {
		echo $error->getMessage();
		die();
	}
	$response=new UpdateResponse();
} else if (!defined('EASYWIDIR')) {
    define('EASYWIDIR',$_SERVER['DOCUMENT_ROOT'].'/');
}
function versioncheck ($current,$new,$file,$response,$sql) {
    $include=true;
	if ($current<$new) {
		$response->add("Upgrading Databe from $current to $new<br />");
		if (is_file(EASYWIDIR.'/'.$file)) {
			$response->add("Found updaterfile ".$file.". Executing it now<br />");
			include(EASYWIDIR.'/'.$file);
		} else if (is_file(EASYWIDIR.'/install/'.$file)) {
			$response->add("Found updaterfile ".'install/'.$file.". Executing it now<br />");
			include(EASYWIDIR.'/install/'.$file);
		} else {
			die("File $file is missing<br />");
		}
		if ($new<'2.08') {
			$update_easywiversion=$sql->prepare("UPDATE `easywi_version` SET `version`=?");
			$update_easywiversion->execute(array($new));
			$response->add('<br />Action: update_easywiversion done: ');
			$error=$update_easywiversion->errorinfo();
			$update_easywiversion->closecursor();
			if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) $response->add($error['2'].'<br />');
			else $response->add('OK<br />');
		}
		return true;
	} else {
		return false;
	}
}
$query=$sql->prepare("SELECT `version` FROM `easywi_version` ORDER BY `id` DESC LIMIT 1");
$query->execute();
$version=$query->fetchColumn();
$admin_id=1;
$main=1;
$reseller_id=0;
$error=$query->errorinfo();
if (isset($error['2']) and $error['2']!="" and $error['2']!=null and !isinteger($error['2'])) {
	$response->add("Current database version: 1.9<br />");
	$version="1.9";
} else {
	$response->add("Current database version: $version<br />");
}
if (versioncheck ($version,'2.00','update_1x-20.php',$response,$sql)) $version='2.00';
if (versioncheck ($version,'2.01','update_200-201.php',$response,$sql)) $version='2.01';
if (versioncheck ($version,'2.02','update_201-202.php',$response,$sql)) $version='2.02';
if (versioncheck ($version,'2.03','update_202-203.php',$response,$sql)) $version='2.03';
if (versioncheck ($version,'2.04','update_203-204.php',$response,$sql)) $version='2.04';
if (versioncheck ($version,'2.05','update_204-205.php',$response,$sql)) $version='2.05';
if (versioncheck ($version,'2.06','update_205-206.php',$response,$sql)) $version='2.06';
if (versioncheck ($version,'2.07','update_206-207.php',$response,$sql)) $version='2.07';
if (versioncheck ($version,'2.08','update_207-208.php',$response,$sql)) $version='2.08';
if (versioncheck ($version,'2.09','update_208-209.php',$response,$sql)) $version='2.09';
if (versioncheck ($version,'2.10','update_209-210.php',$response,$sql)) $version='2.10';
if (!isset($updateinclude) or $updateinclude==false) include('../stuff/tables_add.php');
else include(EASYWIDIR.'/stuff/tables_add.php');
if (versioncheck ($version,'2.11','update_210-211.php',$response,$sql)) $version='2.11';
if (versioncheck ($version,'3.00','update_211-300.php',$response,$sql)) $version='3.00';
if (versioncheck ($version,'3.01','update_300-301.php',$response,$sql)) $version='3.01';
if (versioncheck ($version,'3.02','update_301-302.php',$response,$sql)) $version='3.02';
if (versioncheck ($version,'3.03','update_302-303.php',$response,$sql)) $version='3.03';
if (versioncheck ($version,'3.04','update_303-304.php',$response,$sql)) $version='3.04';
if (versioncheck ($version,'3.05','update_304-305.php',$response,$sql)) $version='3.05';
if (versioncheck ($version,'3.06','update_305-306.php',$response,$sql)) $version='3.06';
if (versioncheck ($version,'3.07','update_306-307.php',$response,$sql)) $version='3.07';
if (versioncheck ($version,'3.08','update_307-308.php',$response,$sql)) $version='3.08';
if (versioncheck ($version,'3.09','update_308-309.php',$response,$sql)) $version='3.09';
if (versioncheck ($version,'3.10','update_309-310.php',$response,$sql)) $version='3.10';
if (versioncheck ($version,'3.20','update_310-320.php',$response,$sql)) $version='3.20';
if (versioncheck ($version,'3.30','update_320-330.php',$response,$sql)) $version='3.30';
if (versioncheck ($version,'3.40','update_330-340.php',$response,$sql)) $version='3.40';
if (versioncheck ($version,'3.60','update_340-360.php',$response,$sql)) $version='3.60';
if (versioncheck ($version,'3.70','update_360-370.php',$response,$sql)) $version='3.70';
if (versioncheck ($version,'4.00','update_370-400.php',$response,$sql)) $version='4.00';
$response->add('Repairing tables if needed.');
if (!isset($updateinclude) or $updateinclude==false) include('../stuff/tables_repair.php');
else include(EASYWIDIR.'/stuff/tables_repair.php');

# Ende
if (!isset($updateinclude) or $updateinclude==false) {
	$response->add("<br />Database successfully updated!<br /> <b> Please remove the \"install/\" folder and all of it´s content.</b>");
	echo $response->printresponse();
	$sql=null;
}