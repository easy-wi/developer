<?php

$host = 'wi.domain.de';
$path = '/api.php';
$user = 'user';
$pwd = '123456';
if (isset($_GET['id'])) {
	$localID=$_GET['id'];
} else {
	$localID='';
}
if (isset($_GET['userID'])) {
	$userID=$_GET['userID'];
} else {
	$userID='';
}
if (isset($_GET['action'])) {
	$action=$_GET['action'];
} else {
	$action='add';
}
if ($_GET['test']=='user') {
$type = 'user';
$postxml = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE users>
<users>
	<action>$action</action>
	<identify_by>localid</identify_by>
	<username></username>
	<external_id>26</external_id>
	<localid>$localID</localid>
	<groupID>570</groupID>
	<email>testing2@mail.de</email>
	<password></password>
	<active>Y</active>
</users>
XML;
} else if ($_GET['test']=='gserver') {
$type = 'gserver';
$postxml = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE server>
<server>
	<action>$action</action>
	<identify_user_by>user_localid</identify_user_by>
	<identify_server_by>server_local_id</identify_server_by>
	<username></username>
	<user_externalid></user_externalid>
	<user_localid>$userID</user_localid>
	<shorten>css</shorten>
	<shorten>cstrike</shorten>
	<primary>cstrike</primary>
	<slots>12</slots>
	<restart>re</restart>
	<private>N</private>
	<server_external_id></server_external_id>
	<server_local_id>$localID</server_local_id>
	<active>N</active>
	<master_server_id></master_server_id>
	<master_server_external_id></master_server_external_id>
	<taskset></taskset>
	<cores></cores>
	<eacallowed></eacallowed>
	<brandname></brandname>
	<tvenable></tvenable>
	<pallowed></pallowed>
	<opt1>123</opt1>
	<opt2></opt2>
	<opt3></opt3>
	<opt4></opt4>
	<opt5></opt5>
	<port>2000</port>
	<port2>2001</port2>
	<port3>2003</port3>
	<port4>2004</port4>
	<port5></port5>
	<minram></minram>
	<maxram></maxram>
	<initialpassword></initialpassword>
</server>
XML;
if(isset($_GET['restart']) and $_GET['restart']=='re' or $_GET['st']) {
$restart=$_GET['restart'];
$postxml = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE server>
<server>
	<action>$action</action>
	<identify_server_by>server_local_id</identify_server_by>
	<restart>$restart</restart>
	<server_external_id></server_external_id>
	<server_local_id>$localID</server_local_id>
</server>
XML;
}
} else if ($_GET['test']=='voice') {
$type = 'voice';
$postxml = <<<XML
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE server>
<server>
	<action>$action</action>
	<identify_user_by>user_localid</identify_user_by>
	<identify_server_by>server_local_id</identify_server_by>
	<username></username>
	<user_externalid></user_externalid>
	<user_localid>$userID</user_localid>
	<shorten>ts3</shorten>
	<slots>12</slots>
	<private>N</private>
	<server_external_id></server_external_id>
	<server_local_id>$localID</server_local_id>
	<active>N</active>
	<master_server_id>44</master_server_id>
	<master_server_external_id></master_server_external_id>
	<max_download_total_bandwidth></max_download_total_bandwidth>
	<max_upload_total_bandwidth></max_upload_total_bandwidth>
	<maxtraffic></maxtraffic>
	<forcebanner></forcebanner>
	<forcebutton></forcebutton>
	<forceservertag></forceservertag>
	<forcewelcome></forcewelcome>
</server>
XML;
} else {
	echo '<pre>';
	print_r();
	echo '<pre>';
}
if (!isset($stop)) {
	if (isset($postxml)) echo $postxml.'<br />';
	$data = 'pwd='.urlencode($pwd).'&user='.$user.'&xml='.urlencode(base64_encode($postxml)).'&type='.$type;
	$useragent=$_SERVER['HTTP_HOST'];
	$fp = @fsockopen($host, 80, $errno, $errstr, 30);
	$buffer="";
	if ($fp) {
		$send = "POST ".$path." HTTP/1.1\r\n";
		$send .= "Host: ".$host."\r\n";
		$send .="User-Agent: $useragent\r\n";
		$send .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
		$send .= "Content-Length: ".strlen($data)."\r\n";
		$send .= "Connection: Close\r\n\r\n";
		$send .= $data;
		fwrite($fp, $send);
		while (!feof($fp)) {
			$buffer .= fgets($fp, 1024);
		}
		fclose($fp);
	}
	list($header,$response)=explode("\r\n\r\n",$buffer);
	$raw=$response;
	$header=str_replace(array("\r\n","\r"),"\n",$header);
	$header=str_replace("\t",' ',$header);
	$ex=explode("\n",$header);
	list($type,$errocode,$errortext)=explode(' ',$ex[0]);
	echo 'Here comes the response:<br /><pre>';
	if ($errocode>400) {
		print_r(substr($response,4,-3));	
	} else {
		while(substr($response,0,1)!='<' and strlen($response)>0) {
			$response=substr($response,1);
		}
		while(substr($response,-1)!='>' and strlen($response)>0) {
			$response=substr($response,0,-1);
		}
		$object=@simplexml_load_string($response);
		if ($object) {
			echo '<pre>';
			print_r($object);
			echo '</pre>';
		} else {
			echo 'Could not decode response<br />';
			echo $raw;
		}
	}
}