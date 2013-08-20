<?php

class EasyWiRestAPI {
	
	// define internal vars
	private $method,$timeout,$connect=false,$user,$pwd,$handle=null,$ssl,$port,$url;
	protected $response=array();
	
	// Constructor that sets defaults which can be overwritten
	__construct($url,$user,$pwd,$timeout=10,$ssl=false,$port=80,$method='xml',$connect='curl') {
		$this->timeout=$timeout;
		// check if curl is choosen and available and initiate cURL-Session
		if ($connect=='curl' and function_exists('curl_init')) {
			if ($this->startCurl($url,$ssl,$port)===true) {
				$this->connect='curl';
			}
			
		// Use and or fallback to fsockopen if possible and create socket
		} else if (($connect=='fsockopen' or !function_exists('curl_init')) and function_exists('fsockopen')) {
			if ($this->startSocket($url,$ssl,$port)===true) {
				$this->connect='fsockopen';
			}
		}
		
		// If connection was successfull, go on and set values
		if ($this->connect!==false) {
			$this->user=$user;
			$this->pwd=$pwd;
			$this->ssl=$ssl;
			$this->port=$port;
			$this->url=$url;
			// Use json, or xml to communicate
			if ($method=='json') {
				$this->method='json';
			} else {
				$this->method='xml';
			}
		} else {
			$this->throwException(10);
		}
	}
	
	// False usage of the object needs to be handled and execution stopped
	private function throwException ($rawError,$extraText=false) {
		// If an exception is caught from imbedded class use the raw error
		if (is_object($rawError)) {
			$errorcode=$rawError->getMessage();
			
		// else use the custom messages
		} else {
		
			// default custom messages
			$errorArray=array(
				1=>'Bad data: Only Strings and Integers are allowed!',
				2=>'Bad data: Only Strings are allowed!',
				3=>'Bad data: Only Integers are allowed!',
				4=>'Bad data: Only arrays are allowed!',
				5=>'Bad data: Unknown Error!',
				6=>'Bad data: Empty values!',
				10=>'Connection Error: Could not connect to!'.$this->url
			);
			
			// if the message is not predifined use the raw input
			if (array_key_exists($rawError,$errorArray)) {
				$errorcode=$errorArray["${rawError}"];
			} else {
				$errorcode=$rawError;
			}
		}
		
		// Add some extra info if given
		if ($extraText!==false) {
			$errorcode.=$extraText;
		}
		throw new Exception('<p>'.$errorcode.'</p>');
		die;
	}
	// 
	private function startCurl ($url,$ssl,$port) {
		// create the URL to call
		if (substr($url,-1)=='/') {
			$url=substr($url,0,-1);
		}
		$url=str_replace(array('http://','https://',':8080',':80',':443'),'',$url);
		if ($ssl==true) {
			$url='https://'.$url;
		} else {
			$url='http://'.$url;
		}
		$url=$url.'/api.php';
		
		// create cURL-Handle
		$this->handle=curl_init($url);
		
		// check success
		if ($this->handle===false) {
			return false;
		} else {
		
			// Set options
			$this->setbasicCurlOpts();
			return true;
		}
	}
	
	// in case of curl setopts
	private function setbasicCurlOpts () {
		curl_setopt($this->handle,CURLOPT_CONNECTTIMEOUT,$this->timeout);
		curl_setopt($this->handle,CURLOPT_USERAGENT,"cURL (Easy-WI; 1.0; Linux)");
		curl_setopt($this->handle,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($this->handle,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($this->handle,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($this->handle,CURLOPT_HEADER,1); 
		//curl_setopt($this->handle,CURLOPT_ENCODING,'deflate');
		if (($this->ssl===true and $this->port!=443) or ($this->ssl===false and $this->port!=80)) {
			curl_setopt($this->handle,CURLOPT_PORT,$this->port);
		}
	}
	
	// method to execute a curl request
	private function execCurl($type,$send) {
		
		// Setting up POST data and add it to the opts
		$postArray['user']=$this->user;
		$postArray['pwd']=$this->pwd;
		$postArray['type']=$type;
		$postArray['xmlstring']=$send;
		curl_setopt($this->handle,CURLOPT_POSTFIELDS,$postArray);
		
		// Execute request, get the response and return it.
		$this->response=curl_exec($this->handle);
		$this->header=curl_getinfo($this->handle);
		return $this->response;
	}
	
	// Ioncube obfuscated files add sometimes data to the REST responses.
	// This will be picked up if fsockopen is used.
	// So there is a need to strip this data.
	private function convertRawData ($rawdata) {
		if ($this->method=='json') {
			$checkStart='{';
			$checkStop='}';
		} else {
			$checkStart='<';
			$checkStop='>';
		}
		$response=$rawdata;
		while (substr($response,0,1)!=$checkStart and strlen($response)>0) {
			$response=substr($response,1);
		}
		while (substr($response,-1)!=$checkStop and strlen($response)>0) {
			$response=substr($response,0,-1);
		}
		
		// Decode the rest of the response string into an object.
		if ($this->method=='json') {
			$decoded=@json_decode($response);
		} else {
			$decoded=@simplexml_load_string($response);
		}
		
		// If decoding was not possible return the raw response, else return the object.
		if ($decoded) {
			unset($rawdata);
			return $decoded;
		} else if ($this->connect=='fsockopen') {
			return substr($rawdata,4,-3);
		} else {
			return $rawdata;
		}
		unset($decoded);
	}
	
	// create the JSON that will be send to the API
	private function JSONPostValue ($paramArray,$action,$params) {
		$jsonArray=array();
		foreach ($paramArray as $param) {
			if (array_key_exists($param,$params)) {
				if (is_array($params[$param])) {
					$jsonArray[$param]=array();
					foreach ($params[$param] as $val) {
						$jsonArray[$param][]=$params[$param];
					}
				} else {
					$jsonArray[$param]=$params[$param];
				}
			} else {
				$jsonArray[$param]='';
			}
		}
		$json=json_encode($jsonArray);
		unset($type,$params,$paramArray,$jsonArray);
		return $json;
	}
	
	// create the XML that will be send to the API
	private function XMLPostValue ($paramArray,$action,$params) {
$xml=new SimpleXMLElement(<<<XML
<?xml version='1.0' standalone='yes'?>
<server></server>
XML
);
		foreach ($paramArray as $param) {
			if (array_key_exists($param,$params)) {
				if (is_array($params[$param])) {
					foreach ($params[$param] as $val) {
						$xml->addChild($param,$val);
					}
				} else {
					$xml->addChild($param,$params[$param]);
				}
			} else {
				$jsonArray[$param]='';
			}
		}
		unset($type,$params,$paramArray);
		return $xml;
	}
	
	
	// Method the external script calls
	public function makeRestCall($type,$action,$params) {
		
		// some param validation. On fail throw an exception
		if (!is_string($type)) {
			$this->throwException(2,': $type');
		}
		if (!is_string($action)) {
			$this->throwException(2,': $action');
		}
		if (!is_array($params)) {
			$this->throwException(4,': $params');
		}
		if (!in_array($type,array('user','gserver','mysql','voice','restart'))) {
			$this->throwException('Error: $type is not defined correctly. Allowed methods are (user, gserver, mysql, vserver, restart)');
		}
		if (!in_array($action,array('mod','add','del','ls','st','re'))) {
			$this->throwException('Error: $action is not defined correctly. Allowed methods are (md, ad, dl, st, re, list)');
		}
		
		// Array keys that all methods have in common
		$generalArray=array('username','user_localid','active');
		
		// Array keys server have in common
		$generalServerArray=array('identify_user_by','user_externalid','identify_server_by','server_external_id','server_local_id','master_server_id','master_server_external_id');
		
		// Keys specfic to user
		$paramArray['user']=array('identify_by','external_id','localid','email','password');
		
		// Keys specfic to gserver
		$paramArray['gserver']=array('private','shorten','slots','primary','taskset','cores','eacallowed','tvenable','pallowed','opt1','opt2','opt3','opt4','opt5','port2','port3','port4','port5','minram','maxram','brandname');
		
		// Keys specfic to voice
		$paramArray['voice']=array('private','shorten','slots','max_download_total_bandwidth','max_upload_total_bandwidth','maxtraffic','forcebanner','forcebutton','forceservertag','forcewelcome');
		
		// Keys specfic to mysql
		$paramArray['mysql']=array();
		
		// create the post value
		if ($this->method=='json') {
			$post=$this->JSONPostValue(array_unique(array_merge($generalArray,$generalServerArray,$paramArray[$type])),$action,$params);
		} else {
			$post=$this->XMLPostValue(array_unique(array_merge($generalArray,$generalServerArray,$paramArray[$type])),$action,$params);
		}
		
		// Call method to send the data depending on the connection type
		if ($this->connect=='curl' and is_recource($this->handle)) {
			$this->execCurl($type,$post);
		} else if ($this->connect=='fsockopen' and is_recource($this->handle)) {
			fclose($this->handle);
		} else {
			$this->throwException(10);
		}
	}
	
	// destructor
	__destruct () {
		if ($this->connect=='curl' and is_recource($this->handle)) {
			curl_close($this->handle);
		} else if ($this->connect=='fsockopen' and is_recource($this->handle)) {
			fclose($this->handle);
		}
		unset($method,$timeout,$connect,$user,$pwd,$handle,$ssl,$port,$response);
	}
}