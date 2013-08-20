<?php

if (isset($_POST['ip']) and isset($_POST['port']) and isset($_POST['submit']) and !empty($_POST['ip']) and !empty($_POST['port'])) {

	//Verbindung
	$ssh2=ssh2_connect($_POST['ip'],$_POST['port']);
	if ($ssh2==true) {
		echo 'Connect to: '.$_POST['ip'].':'.$_POST['port'].'<br />';
		
		// Login
		if (isset($_POST['user']) and isset($_POST['password']) and !empty($_POST['user']) and !empty($_POST['password'])) {
			$connect_ssh2=ssh2_auth_password($ssh2,$_POST['user'],$_POST['password']);
			if ($connect_ssh2==true) {
				echo 'Logindata works';
			} else {
				echo 'Logindata does not work';
			}
		} else {
			echo 'No Logindata entered';
		}
	} else {
		echo 'could not connect to: '.$_POST['ip'].':'.$_POST['port'];
	}
} else {
	echo extension_loaded('ionCube Loader') ? 'Ioncube extension is installed<br />' : 'Ioncube extension is not installed, please install it.<br />';
	echo extension_loaded('ssh2') ? 'SSH2 extension is installed.<br />' : 'SSH2 extension is not installed, please install it.<br />';
	echo extension_loaded('openssl') ? 'openssl extension is installed.<br />' : 'openssl extension is not installed, please install it.<br />';
	echo extension_loaded('json') ? 'json extension is installed.<br />' : 'json extension is not installed, please install it.<br />';
	echo extension_loaded('hash') ? 'hash extension is installed.<br />' : 'hash extension is not installed, please install it.<br />';
	echo extension_loaded('ftp') ? 'openssl extension is installed.<br />' : 'ftp extension is not installed, please install it.<br />';
	echo extension_loaded('SimpleXML') ? 'session SimpleXMLis installed.<br />' : 'SimpleXML extension is not installed, please install it.<br />';
	echo extension_loaded('curl') ? 'curl extension is installed.<br />' : 'curl extension is not installed, please install it.<br />';
	echo extension_loaded('gd') ? 'gd extension is installed.<br />' : 'gd extension is not installed, please install it.<br />';
	echo extension_loaded('PDO') ? 'PDO extension is installed.<br />' : 'PDO extension is not installed, please install it.<br />';
	echo extension_loaded('pdo_mysql') ? 'pdo_mysql extension is installed.<br />' : 'pdo_mysql extension is not installed, please install it.<br />';
	echo function_exists('fopen') ? 'fopen function can be used.<br />' : 'fopen function cannot be used) and isset( please enable it.<br />';
	if (extension_loaded('ssh2')) {
		echo 'SSH2 extension is installed.<br />';
		echo '<h1>Test SSH2 connection</h1><form method=post action='.$_SERVER['PHP_SELF'].' >IP: <input type=text name=ip required /><br />Port: <input type=text name=port required /><br />User: <input type=text name=user required /><br />Password: <input type=text name=password required /><br /><input type=submit name=submit value=Test /><br /></form>';
	} else {
		echo 'SSH2 extension is not installed, please install it.<br />';
	}
}