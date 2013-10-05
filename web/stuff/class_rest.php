<?php
/**
 * File: class_rest.php.
 * Author: Ulrich Block
 * Date: 07.07.13
 * Time: 13:25
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
/*
class easyWiRest {

    // define internal vars
    private $method,$timeout,$connect=false,$handle=null,$ssl,$url;
    protected $response = array();

    // Constructor that sets defaults which can be overwritten
    function __construct($url,$timeout=10,$ssl='N',$connect='curl') {
        $this->ssl=$ssl;
        $this->port=($this->ssl== 'Y') ? 443 : 80;
        $this->url=$url;
        $this->timeout=$timeout;
        // check if curl is choosen and available and initiate cURL-Session
        if ($connect == 'curl' and function_exists('curl_init')) {
            if ($this->startCurl($url,$ssl,$this->port)===true) {
                $this->connect='curl';
            }

        // Use and or fallback to fsockopen if possible and create socket
        } else if (($connect == 'fsockopen' or !function_exists('curl_init')) and function_exists('fsockopen')) {
            if ($this->startSocket($url,$ssl,$this->port)===true) {
                $this->connect='fsockopen';
            }
        }

        // If connection was successfull, go on and set values
        if ($this->connect!==false) {
            return true;
        } else {
            return 'Connection Error: Could not connect to!'.$this->url;
        }
    }
    public function execRequest($type,$params) {
        $params=str_replace('&amp;','',$params);
        if ($this->connect == 'curl') {
            $this->setbasicCurlOpts();
            $this->execCurl($type,$params);
        } else {
            $this->startSocket($url,$port);
            $this->execSocket($type,$params,);
        }
    }
    private function startSocket ($url,$port) {
        $url=str_replace(array('http://','https://',':8080',':80',':443'),'',$url);
        if (isdomain($url)) {
            $this->handle=@fsockopen($url,$port,$errno,$errstr,10);
            if(!$this->handle) return $errstr;
            return true;
        } else {
            return 'Error: Domain';
        }
    }

    private function execSocket ($type,$params,$url) {
        if($this->handle) {
            if($type == 'P') {
                $send="POST /".$file." HTTP/1.1\r\n";
            } else {
                $send="GET $file HTTP/1.1\r\n";
            }
            $send .= "Host: ".$url."\r\n";
            $send .="User-Agent: easy-wi.com\r\n";
            $send .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
            if(strlen($params)>0) {
                $send .= "Content-Length: ".strlen($params)."\r\n";
            }
            $send .= "Connection: Close\r\n\r\n";
            if(strlen($postParams)>0) $send .= $params;
            fwrite($this->handle,$send);
            $buffer = '';
            while (!feof($this->handle)) $buffer.=fgets($this->handle,4096);
            fclose($this->handle);
            $ex=explode("\r\n\r\n",$buffer);
            if (strpos($ex[0],'404')!==false) {
                return 'file not found: '.$url. '/' . $file;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    //
    private function startCurl ($url,$ssl) {
        // create the URL to call
        if (substr($url,-1) == '/') {
            $url=substr($url,0,-1);
        }
        $url=str_replace(array('http://','https://',':8080',':80',':443'),'',$url);
        if ($ssl== 'Y') {
            $url='https://'.$url;
        } else {
            $url='http://'.$url;
        }
        $url=$url.'/api.php';

        // create cURL-Handle
        $this->handle=curl_init($url);

        // check success
        if ($this->handle===false) return false;
        return true;
    }
    // in case of curl setopts
    private function setbasicCurlOpts () {
        curl_setopt($this->handle,CURLOPT_CONNECTTIMEOUT,$this->timeout);
        curl_setopt($this->handle,CURLOPT_USERAGENT,"cURL (Easy-WI; 1.0; Linux)");
        curl_setopt($this->handle,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->handle,CURLOPT_SSL_VERIFYPEER, false);
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
        $postArray['type'] = $type;
        $postArray['xmlstring'] = $send;
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
        if ($this->method== 'json') {
            $checkStart='{';
            $checkStop='}';
        } else {
            $checkStart='<';
            $checkStop='>';
        }
        $response=$rawdata;
        while (substr($response,0,1) != $checkStart and strlen($response)>0) {
            $response=substr($response,1);
        }
        while (substr($response,-1) != $checkStop and strlen($response)>0) {
            $response=substr($response,0,-1);
        }

        // Decode the rest of the response string into an object.
        if ($this->method== 'json') {
            $decoded=@json_decode($response);
        } else {
            $decoded=@simplexml_load_string($response);
        }

        // If decoding was not possible return the raw response, else return the object.
        if ($decoded) {
            unset($rawdata);
            return $decoded;
        } else if ($this->connect == 'fsockopen') {
            return substr($rawdata,4,-3);
        } else {
            return $rawdata;
        }
    }
}
*/