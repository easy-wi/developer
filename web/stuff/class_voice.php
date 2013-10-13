<?php

/**
 * File: class_voice.php.
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

class TS3 {

	private $socket = false, $connected = false, $ip = false, $maxExecutionTime = 20, $timeStarted = 0, $debug = null;

	public $errorcode = false, $socketConnected = false;

	private function ReplaceToTS3 ($value) {
		$return = str_replace(array('\\', '/', ' ', '|'), array('\\\\', '\/', '\s', '\p'), $value);
		return $return;
	}

	private function ReplaceFromTS3 ($value) {
		$return = str_replace(array('\\\\', '\/', '\s', '\p'), array('\\', '/', ' ', '|'), $value);
		return $return;
	}

	public function SendCommand ($value) {
		if ($this->connected == true) {

			$return = array();
            $response = '';

			if (is_array($value)) {
                fputs($this->socket, $value[0]."\n");
            } else {
                fputs($this->socket, $value."\n");
            }

            stream_set_blocking($this->socket, true);
            stream_set_timeout($this->socket, $this->maxExecutionTime);
            $metaData = stream_get_meta_data($this->socket);
			while (strpos($response,'msg=') === false or strpos($response,'error id=') === false) {

                if ($metaData['unread_bytes'] < 1) {
                    break;
                }

				$new = fgets($this->socket);
                $response .= $new;

                $metaData = stream_get_meta_data($this->socket);
            }
            if ($this->debug == true) {
                print "Raw query return: ${response}\r\n";
            }

			list($databody,$errorcode) = explode('error id=', str_replace(array("\r", "\n"), '', $response));
			$this->errorcode = 'error id=' . $errorcode;

			if ($databody == '' or $databody==null) {
                $databody = $this->errorcode;
            }

			if (!is_array($value) or (isset($value[1]) and $value[1] == 'array')) {
				foreach (explode('|', $databody) as $data) {
					$cvars = array();
					foreach (explode(' ', $data) as $singleline) {
						$splitted = preg_split('/=(?!$)/', $singleline, -1, PREG_SPLIT_NO_EMPTY);
						$count = count($splitted);
						if ($count > 2) {
							$i = 1;
							$buffered = '';

							while ($i < $count) {
								$buffered .= $splitted[$i];
								$i++;
							}

							$splitted[1] = $buffered;
						}

						if (!isset($splitted[1])) {
                            $splitted[1] = '';
                        }

						$index = trim(preg_replace('/\s+/','',$splitted[0]));

						if ($index!='error') {
                            $cvars[$index] = (isinteger(trim($splitted[1]))) ? (int) trim($splitted[1]) : trim($splitted[1]);
                        }
					}

					$return[] = $cvars;
				}

			} else {
				$return = $databody;
			}

			return $return;

		} else {
			return "error: not connected";
		}
	}

	public function CloseConnection () {
		if (is_resource($this->socket)) {
			@fputs($this->socket, "quit\n");
			fclose($this->socket);
		}
	}

	function __construct($ip, $queryport, $admin, $querypassword, $debug = false) {
        $this->maxExecutionTime = (int) (ini_get('max_execution_time') - 5);
        $this->timeStarted = time();
		$this->ip = $ip;
        $this->debug = $debug;
		$this->socket = @fsockopen('tcp://' . $ip,$queryport,$errnum,$errstr,5);
		if ($this->socket == true) {
            $this->socketConnected = true;
			if(strpos(fgets($this->socket, 8192),'TS3') !== false) {
				$welcome=fgets($this->socket, 8192);
				@fputs($this->socket, "login $admin $querypassword"."\n");
				$this->errorcode = $this->ReplaceFromTS3(fgets($this->socket, 8192));
				if (strpos($this->errorcode,'error id=0') === false) {
					$this->connected = false;
				} else {
					$this->connected = true;				
				}
			} else {
				$this->connected = false;
			}
		} else {
			$this->errorcode = $errstr;
			$this->connected = false;
		}
		return $this->connected;
	}
	private function UseServer ($virtualserver_id) {
		return $this->ReplaceFromTS3($this->SendCommand('use '.$virtualserver_id));
	}
	public function AddServer ($maxclients,$ip,$port,$password,$name,$message,$download,$upload,$banner_url,$banner_gfx,$button_url,$button_gfx,$tooltip) {
        #." virtualserver_ip=".$ip
		$addcommand="servercreate virtualserver_maxclients=".$maxclients." virtualserver_port=".$port." virtualserver_password=".$password." virtualserver_name=".$this->ReplaceToTS3($name)." virtualserver_welcomemessage=".$this->ReplaceToTS3($message[1]);
		$addcommand .=" virtualserver_max_download_total_bandwidth=".$download." virtualserver_max_upload_total_bandwidth=".$upload." virtualserver_hostbanner_url=".$banner_url[1]." virtualserver_hostbanner_gfx_url=".$banner_gfx;
		$addcommand .=" virtualserver_hostbutton_url=".$button_url[1]." virtualserver_hostbutton_gfx_url=".$button_gfx." virtualserver_hostbutton_tooltip=".$this->ReplaceToTS3($tooltip);
		fputs($this->socket,$addcommand."\n");
		$response = $this->ReplaceFromTS3(fgets($this->socket,8192));
		if (strpos($response,'error id=0') !== false or strpos($response,'sid=') !== false) {
			$error = $this->ReplaceFromTS3(fgets($this->socket,8192));
			$ex1=explode(' ',$response);
			$ex2=explode('=',$ex1[0]);
			$virtualserver_id = $ex2[1];
			$useserver = $this->UseServer($virtualserver_id);
			if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
				$remove=array('b_virtualserver_start','i_needed_modify_power_virtualserver_start',
				'b_virtualserver_stop','i_needed_modify_power_virtualserver_stop',
				'b_virtualserver_modify_maxclients','i_needed_modify_power_virtualserver_modify_maxclients',
				'b_virtualserver_modify_port','i_needed_modify_power_virtualserver_modify_port',
				'b_virtualserver_modify_autostart','i_needed_modify_power_virtualserver_modify_autostart',
				'b_virtualserver_modify_ft_settings','i_needed_modify_power_virtualserver_modify_ft_settings');
				if ($message[0] == 'Y') {
					$remove[]="b_virtualserver_modify_welcomemessage";
					$remove[]="i_needed_modify_power_virtualserver_modify_welcomemessage";
				}
				if ($banner_url[0] == 'Y') {
					$remove[]="b_virtualserver_modify_hostbanner";
					$remove[]="i_needed_modify_power_virtualserver_modify_hostbanner";
				}
				if ($button_url[0] == 'Y') {
					$remove[]="b_virtualserver_modify_hostbutton";
					$remove[]="i_needed_modify_power_virtualserver_modify_hostbutton";
				}
				$servergroups = $this->SendCommand('servergrouplist');
				foreach ($servergroups as $servegroup) {
					if ($servegroup['type']==1) {
						$delcommand="servergroupdelperm sgid=".$servegroup['sgid']." permsid=".implode('|permsid=', $remove);
						$this->SendCommand($delcommand);
					}
				}
				return $virtualserver_id;
			} else {
				return 'Could not modify groups';
			}
		} else {
			return $response;
		}
	}
	public function StopServer($virtualserver_id) {
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			$this->SendCommand("serveredit virtualserver_autostart=0");
			$response = $this->SendCommand('serverstop sid='.$virtualserver_id);			
			return $response;
		}
        return false;
	}
	public function StartServer($virtualserver_id) {
		$response = $this->SendCommand('serverstart sid='.$virtualserver_id);
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			$this->SendCommand("serveredit virtualserver_autostart=1");
		}
		return $response;
	}
	public function DelServer($virtualserver_id) {
		$this->SendCommand('serverstop sid='.$virtualserver_id);
		$delete = $this->SendCommand('serverdelete sid='.$virtualserver_id);
		return $delete;
	}
	public function ModServer ($virtualserver_id,$maxclients,$ip,$port,$password,$name,$message,$download,$upload,$banner_url,$banner_gfx,$button_url,$button_gfx,$tooltip,$virtualserver_reserved_slots=null,$virtualserver_needed_identity_security_level=null,$virtualserver_hostmessage_mode=null,$virtualserver_hostbanner_gfx_interval=null,$virtualserver_antiflood_points_tick_reduce=null,$virtualserver_antiflood_points_needed_command_block=null,$virtualserver_antiflood_points_needed_ip_block=null) {
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
            #." virtualserver_ip=".$this->ReplaceToTS3($ip);
			$modcommand="serveredit virtualserver_maxclients=".$maxclients." virtualserver_port=".$port." virtualserver_password=".$this->ReplaceToTS3($password)." virtualserver_name=".$this->ReplaceToTS3($name)." virtualserver_welcomemessage=".$this->ReplaceToTS3($message);
			$modcommand .=" virtualserver_max_download_total_bandwidth=".$download." virtualserver_max_upload_total_bandwidth=".$upload;
            if ($banner_url!='' and $banner_url != null) $modcommand .=" virtualserver_hostbanner_url=".$this->ReplaceToTS3($banner_url);
            if ($banner_gfx!='' and $banner_gfx != null) $modcommand .=" virtualserver_hostbanner_gfx_url=".$this->ReplaceToTS3($banner_gfx);
            if ($button_url!='' and $button_url != null) $modcommand .=" virtualserver_hostbutton_url=".$this->ReplaceToTS3($button_url);
            if ($button_gfx!='' and $button_gfx != null) $modcommand .=" virtualserver_hostbutton_gfx_url=".$this->ReplaceToTS3($button_gfx);
            if ($tooltip!='' and $tooltip != null) $modcommand .=" virtualserver_hostbutton_tooltip=".$this->ReplaceToTS3($tooltip);

            # Ticket https://github.com/easy-wi/developer/issues/13 "Bearbeiten von TS3 Servern im Usermodul erweitern"
            if ($virtualserver_reserved_slots != '' and $virtualserver_reserved_slots != null) $modcommand.=" virtualserver_reserved_slots=".$this->ReplaceToTS3($virtualserver_reserved_slots);
            if ($virtualserver_needed_identity_security_level!='' and $virtualserver_needed_identity_security_level != null) $modcommand.=" virtualserver_needed_identity_security_level=".$this->ReplaceToTS3($virtualserver_needed_identity_security_level);
            if ($virtualserver_hostmessage_mode!='' and $virtualserver_hostmessage_mode != null) $modcommand.=" virtualserver_hostmessage_mode=".$this->ReplaceToTS3($virtualserver_hostmessage_mode);
            if ($virtualserver_hostbanner_gfx_interval!='' and $virtualserver_hostbanner_gfx_interval != null) $modcommand.=" virtualserver_hostbanner_gfx_interval=".$this->ReplaceToTS3($virtualserver_hostbanner_gfx_interval);
            if ($virtualserver_antiflood_points_tick_reduce!='' and $virtualserver_antiflood_points_tick_reduce != null) $modcommand.=" virtualserver_antiflood_points_tick_reduce=".$this->ReplaceToTS3($virtualserver_antiflood_points_tick_reduce);
            if ($virtualserver_antiflood_points_needed_command_block != '' and $virtualserver_antiflood_points_needed_command_block != null) $modcommand.=" virtualserver_antiflood_points_needed_command_block=".$this->ReplaceToTS3($virtualserver_antiflood_points_needed_command_block);
            if ($virtualserver_antiflood_points_needed_ip_block != '' and $virtualserver_antiflood_points_needed_ip_block != null) $modcommand.=" virtualserver_antiflood_points_needed_ip_block=".$this->ReplaceToTS3($virtualserver_antiflood_points_needed_ip_block);
			$response = $this->SendCommand($modcommand);
			return $response;
		}
        return false;
	}
	public function ImportModServer ($virtualserver_id,$maxclients,$ip,$port,$array) {
		$this->StartServer($virtualserver_id);
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
            #." virtualserver_ip=".$ip
			$modcommand="serveredit virtualserver_maxclients=".$maxclients." virtualserver_port=".$port;
			foreach ($array as $key => $value) {
				$modcommand .= ' ' . $key.'='.$this->ReplaceToTS3($value);
			}
			$response = $this->SendCommand($modcommand);
			return $response;
		}
        return false;
	}
	public function AdminPermissions ($virtualserver_id,$what,$permlist) {
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			$servergroups = $this->SendCommand('servergrouplist');
			$permissioncount = 0;
			foreach ($servergroups as $servegroups) {
				if ($this->ReplaceFromTS3($servegroups['type'])==1) {
					$newcount=count($this->SendCommand("servergrouppermlist sgid=".$servegroups['sgid']));
					if ($newcount>$permissioncount) {
						$admingroupid = $servegroups['sgid'];
						$permissioncount = $newcount;
					} else if ($newcount==$permissioncount) {
						if (isset($admingroupid) and is_array($admingroupid)) {
							$admingroupid[] = $servegroups['sgid'];
						} else {
							if (isset($admingroupid)) $admingroupid=array($admingroupid,$servegroups['sgid']);
						}
						$permissioncount = $newcount;
					}
				}
			}
			if (isset($admingroupid) and is_array($admingroupid)) {
				foreach ($admingroupid as $id) {
					if ($what == 'add') {
						$command="servergroupaddperm sgid=".$id." permsid=".implode(' permvalue=1 permnegated=0 permskip=0|permsid=', $permlist)." permvalue=1 permnegated=0 permskip=0";
                        $this->SendCommand($command);
					} else if ($what == 'del') {
						$command="servergroupdelperm sgid=".$id." permsid=".implode('|permsid=', $permlist);
                        $this->SendCommand($command);
					}
				}
			} else if (isset($admingroupid)) {
				if ($what == 'add') {
					$command="servergroupaddperm sgid=".$admingroupid." permsid=".implode(' permvalue=1 permnegated=0 permskip=0|permsid=', $permlist)." permvalue=1 permnegated=0 permskip=0";
                    $this->SendCommand($command);
				} else if ($what == 'del') {
					$command="servergroupdelperm sgid=".$admingroupid." permsid=".implode('|permsid=', $permlist);
                    $this->SendCommand($command);
				}
			}
		}
	}
	public function ImportData ($dnsarray) {
		$serverdetails = array();
		$serverlist = $this->SendCommand('serverlist');
		foreach ($serverlist as $server) {
			if (isset($server['virtualserver_id'])) {
				$virtualserver_id = $server['virtualserver_id'];
				if ($server['virtualserver_status'] == 'offline') {
					$this->StartServer($virtualserver_id);
				}
				$useserver = $this->UseServer($virtualserver_id);
				if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
                    $virtualserver_ip=(isset($serverdetails_query[0]['virtualserver_ip']) and isip($serverdetails_query[0]['virtualserver_ip'], 'all')) ? $serverdetails_query[0]['virtualserver_ip'] : $this->ip;
					$serverdetails_query = $this->SendCommand('serverinfo');
					$virtualserver_server = $virtualserver_ip . ':' . $server['virtualserver_port'];
                    $virtualserver_dns=(array_key_exists($virtualserver_server,$dnsarray)) ? $dnsarray[$virtualserver_server] : '';

                    //https://github.com/easy-wi/developer/issues/74 check if array keys exists
					if (isset($serverdetails_query[0]) and ($serverdetails_query[0]['virtualserver_maxclients'])) {
						$serverdetails[$virtualserver_id] = array(
							'virtualserver_ip' => $virtualserver_ip,
							'virtualserver_maxclients' => $serverdetails_query[0]['virtualserver_maxclients'],
							'virtualserver_port' => $server['virtualserver_port'],
							'virtualserver_dns' => $virtualserver_dns,
							'virtualserver_name' => $this->ReplaceFromTS3($serverdetails_query[0]['virtualserver_name']),
							'virtualserver_welcomemessage' => $serverdetails_query[0]['virtualserver_welcomemessage'],
							'virtualserver_flag_password' => $serverdetails_query[0]['virtualserver_flag_password'],
							'virtualserver_max_download_total_bandwidth' => $serverdetails_query[0]['virtualserver_max_download_total_bandwidth'],
							'virtualserver_max_upload_total_bandwidth' => $serverdetails_query[0]['virtualserver_max_upload_total_bandwidth'],
							'virtualserver_hostbanner_url' => $serverdetails_query[0]['virtualserver_hostbanner_url'],
							'virtualserver_hostbanner_gfx_url' => $serverdetails_query[0]['virtualserver_hostbanner_gfx_url'],
							'virtualserver_hostbutton_tooltip' => $serverdetails_query[0]['virtualserver_hostbutton_tooltip'],
							'virtualserver_hostbutton_url' => $serverdetails_query[0]['virtualserver_hostbutton_url'],
							'virtualserver_hostbutton_gfx_url' => $serverdetails_query[0]['virtualserver_hostbutton_gfx_url']
						);
					} else {
						$serverdetails[$virtualserver_id] = array(
							'virtualserver_ip' => $virtualserver_ip,
							'virtualserver_maxclients' => $serverdetails_query[0]['virtualserver_maxclients'],
							'virtualserver_port' => $server['virtualserver_port'],
							'virtualserver_dns' => $virtualserver_dns,
							'virtualserver_name' => '',
							'virtualserver_welcomemessage' => '',
							'virtualserver_flag_password' => '',
							'virtualserver_max_download_total_bandwidth' => '',
							'virtualserver_max_upload_total_bandwidth' => '',
							'virtualserver_hostbanner_url' => '',
							'virtualserver_hostbanner_gfx_url' => '',
							'virtualserver_hostbutton_tooltip' => '',
							'virtualserver_hostbutton_url' => '',
							'virtualserver_hostbutton_gfx_url' => ''
						);
					}
				}
				if ($server['virtualserver_status'] == 'offline') {
					$this->StopServer($virtualserver_id);
				}
				if (!isset($serverdetails[$virtualserver_id])) {
                    $virtualserver_ip=(isset($virtualserver_ip)) ? $virtualserver_ip :'';
                    $virtualserver_dns=(isset($virtualserver_dns)) ? $virtualserver_dns :'';
					$serverdetails[$virtualserver_id] = array(
						'virtualserver_ip' => $virtualserver_ip,
						'virtualserver_maxclients' => $server['virtualserver_maxclients'],
						'virtualserver_port' => $server['virtualserver_port'],
						'virtualserver_dns' => $virtualserver_dns,
						'virtualserver_name' => '',
						'virtualserver_welcomemessage' => '',
						'virtualserver_flag_password' => '',
						'virtualserver_max_download_total_bandwidth' => '',
						'virtualserver_max_upload_total_bandwidth' => '',
						'virtualserver_hostbanner_url' => '',
						'virtualserver_hostbanner_gfx_url' => '',
						'virtualserver_hostbutton_tooltip' => '',
						'virtualserver_hostbutton_url' => '',
						'virtualserver_hostbutton_gfx_url' => ''
					);
				}
			}
		}
		return $serverdetails;
	}
	public function ServerList () {
		$serverdetails = array();
        $array = $this->SendCommand('serverlist');
        if (is_array($array) or is_object($array)) foreach ($array as $k=>$v) $serverdetails[$k] = $this->ReplaceFromTS3($v);
        if ($this->debug == true){
            print "ServerList:";
            print_r($serverdetails);
            print "\r\n";
        }
		return $serverdetails;
	}
	public function ServerDetails ($virtualserver_id) {
		$serverdetails=array('virtualserver_name' => '','virtualserver_welcomemessage' => '','virtualserver_hostbanner_url' => '','virtualserver_hostbanner_gfx_url' => '','virtualserver_hostbutton_tooltip' => '','virtualserver_hostbutton_url' => '','virtualserver_hostbutton_gfx_url' => '','virtualserver_maxclients' => '','virtualserver_flag_password' => '','virtualserver_max_download_total_bandwidth' => '','virtualserver_max_upload_total_bandwidth' => '','virtualserver_clientsonline' => 0,'virtualserver_queryclientsonline' => 0,'virtualserver_uptime' => 20,'virtualserver_status' => '','connection_filetransfer_bytes_sent_total' => '','connection_filetransfer_bytes_received_total' => '','connection_bytes_sent_total' => '','connection_bytes_received_total' => '');
        $useserver = $this->UseServer($virtualserver_id);
        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			$serverdetails_query = $this->SendCommand('serverinfo');
			$serverdetails=array(
				'virtualserver_name' => $this->ReplaceFromTS3($serverdetails_query[0]['virtualserver_name']),
				'virtualserver_welcomemessage' => $this->ReplaceFromTS3($serverdetails_query[0]['virtualserver_welcomemessage']),
				'virtualserver_hostbanner_url' => $this->ReplaceFromTS3($serverdetails_query[0]['virtualserver_hostbanner_url']),
				'virtualserver_hostbanner_gfx_url' => $this->ReplaceFromTS3($serverdetails_query[0]['virtualserver_hostbanner_gfx_url']),
				'virtualserver_hostbutton_tooltip' => $this->ReplaceFromTS3($serverdetails_query[0]['virtualserver_hostbutton_tooltip']),
				'virtualserver_hostbutton_url' => $this->ReplaceFromTS3($serverdetails_query[0]['virtualserver_hostbutton_url']),
				'virtualserver_hostbutton_gfx_url' => $this->ReplaceFromTS3($serverdetails_query[0]['virtualserver_hostbutton_gfx_url']),
				'virtualserver_maxclients' => $serverdetails_query[0]['virtualserver_maxclients'],
				'virtualserver_flag_password' => $serverdetails_query[0]['virtualserver_flag_password'],
				'virtualserver_max_download_total_bandwidth' => $serverdetails_query[0]['virtualserver_max_download_total_bandwidth'],
				'virtualserver_max_upload_total_bandwidth' => $serverdetails_query[0]['virtualserver_max_upload_total_bandwidth'],
				'virtualserver_clientsonline' => $serverdetails_query[0]['virtualserver_clientsonline'],
				'virtualserver_queryclientsonline' => $serverdetails_query[0]['virtualserver_queryclientsonline'],
				'virtualserver_uptime' => $serverdetails_query[0]['virtualserver_uptime'],
				'virtualserver_status' => $serverdetails_query[0]['virtualserver_status'],
				'connection_filetransfer_bytes_sent_total' => $serverdetails_query[0]['connection_filetransfer_bytes_sent_total'],
				'connection_filetransfer_bytes_received_total' => $serverdetails_query[0]['connection_filetransfer_bytes_received_total'],
				'connection_bytes_sent_total' => $serverdetails_query[0]['connection_bytes_sent_total'],
				'connection_bytes_received_total' => $serverdetails_query[0]['connection_bytes_received_total'],

                # Ticket https://github.com/easy-wi/developer/issues/13 "Bearbeiten von TS3 Servern im Usermodul erweitern"
                'virtualserver_reserved_slots' => $serverdetails_query[0]['virtualserver_reserved_slots'],
                'virtualserver_needed_identity_security_level' => $serverdetails_query[0]['virtualserver_needed_identity_security_level'],
                'virtualserver_hostmessage_mode' => $serverdetails_query[0]['virtualserver_hostmessage_mode'],
                'virtualserver_hostbanner_gfx_interval' => $serverdetails_query[0]['virtualserver_hostbanner_gfx_interval'],
                'virtualserver_antiflood_points_tick_reduce' => $serverdetails_query[0]['virtualserver_antiflood_points_tick_reduce'],
                'virtualserver_antiflood_points_needed_command_block' => $serverdetails_query[0]['virtualserver_antiflood_points_needed_command_block'],
                'virtualserver_antiflood_points_needed_ip_block' => $serverdetails_query[0]['virtualserver_antiflood_points_needed_ip_block']
			);
            if ($this->debug == true){
                print "Serverdetails:";
                print_r($serverdetails);
                print "\r\n";
            }
		} else if($this->debug == true) {
            print "Userserver at serverdetails failed:";
            print_r($useserver);
            print "\r\n";
            if (isset($useserver[0])) print_r($useserver[0]);
            else echo '$useserver[0] not set';
            print "\r\n";
            if (isset($useserver[0])) print_r($useserver[0]['msg']);
            else echo '$useserver[0]["msg"] not set';
            print "\r\n";
        }
		return $serverdetails;
	}
	public function AdminList ($virtualserver_id) {
        $adminlist = '';
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			$servergroups = $this->SendCommand('servergrouplist');
			$permissioncount = 0;
			unset($admingroupid);
			foreach ($servergroups as $servegroups) {
				if (isset ($servegroups['type']) and $servegroups['type']==1 and $servegroups['name'] == 'Server\sAdmin') {
					$admingroupid = $servegroups['sgid'];
				}
			}
			if (!isset($admingroupid)) {
				foreach ($servergroups as $servegroups) {
					if (isset($servegroups['type']) and $this->ReplaceFromTS3($servegroups['type'])==1) {
						$newcount=count($this->SendCommand("servergrouppermlist sgid=".$servegroups['sgid']));
						if ($newcount>$permissioncount) {
							$admingroupid = $servegroups['sgid'];
							$permissioncount = $newcount;
						} else if ($newcount==$permissioncount) {
							if (isset($admingroupid) and is_array($admingroupid)) {
								$admingroupid[] = $servegroups['sgid'];
							} else {
								if (isset($admingroupid)) $admingroupid=array($admingroupid,$servegroups['sgid']);
							}
							$permissioncount = $newcount;
						}
					}
				}
			}
			if (isset($admingroupid) and is_array($admingroupid)) {
				foreach ($admingroupid as $id) {
					$userlistraw = $this->SendCommand("servergroupclientlist sgid=".$id);
				}
			} else {
				if (isset($admingroupid)) $userlistraw = $this->SendCommand("servergroupclientlist sgid=".$admingroupid);
			}
			if (isset($userlistraw) and is_array($userlistraw) and isset($userlistraw[0]['cldbid'])) {
				foreach ($userlistraw as $userid) {
					$cldbid = $userid['cldbid'];
					$userdata = $this->SendCommand("clientdbinfo cldbid=".$cldbid);
					$client_unique_identifier = $this->ReplaceFromTS3($userdata[0]['client_unique_identifier']);
					$adminlist[$cldbid] = $client_unique_identifier;
				}
			} else {
				$adminlist = array();
			}
		}
		return $adminlist;
	}
	public function KeyList ($virtualserver_id) {
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			$return = array();
			$servergroups = $this->SendCommand('servergrouplist');
			foreach ($servergroups as $servergroup) {
				if ($servergroup['type']==1) $admingroupid[$servergroup['sgid']] = $this->ReplaceFromTS3($servergroup['name']);
			}
			$this->SendCommand("privilegekeylist");
			foreach ($this->SendCommand("privilegekeylist") as $key) {
				if (isset($key['token_type']) and $key['token_type']==0 and isset($admingroupid)) $return[] = array('token' => $key['token'], 'groupname' => $this->ReplaceFromTS3($admingroupid[$key['token_id1']]));
			}
		} else {
			$return = $useserver[0]['msg'];
		}
		return $return;
	}
	public function DelKey ($virtualserver_id,$token) {
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			return $this->SendCommand('privilegekeydelete token='.$token);
		}
        return false;
	}
	public function AddKey ($virtualserver_id,$group) {
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			return $this->SendCommand('privilegekeyadd tokentype=0 tokenid1='.$group.' tokenid2=0');
		}
        return false;
	}
	public function PermReset ($virtualserver_id) {
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			foreach ($this->SendCommand('ftlist') as $ftl) {
				if (isset($ftl['serverftfid'])) {
					$this->SendCommand('ftstop serverftfid='.$ftl['serverftfid'].' delete=1');
				}
			}
			$channellist = $this->SendCommand('channellist -flags');
			foreach ($channellist as $channel) {
				$cid = $channel['cid'];
				if ($channel['channel_flag_default']) {
					$this->SendCommand('channeledit cid='.$cid.' channel_name=Default\sChannel channel_flag_permanent=1 channel_password= channel_needed_subscribe_power=0 channel_codec=2 channel_codec_quality=7 channel_maxclients=-1 channel_maxfamilyclients=-1');
					unset($filedeletecmd);
					foreach ($this->SendCommand('ftgetfilelist cid='.$cid.' cpw= path=\/') as $content) {
						if (isset($content['name']) and isset($filedeletecmd)) {
							$filedeletecmd .= '|name=\/'.$content['name'];
						} else if (isset($content['name']) and !isset($filedeletecmd)) {
							$filedeletecmd='ftdeletefile cid='.$cid.' cpw= name=\/'.$content['name'];
						}
					}
					if (isset($filedeletecmd)) {
						$this->SendCommand($filedeletecmd);
					}
				} else {
					$this->SendCommand('channeldelete cid='.$cid.' force=1');
				}
			}
			$this->SendCommand('bandelall');
			$return = $this->SendCommand('permreset');
			return $return;
		}
        return false;
	}
	public function ServerGroups($virtualserver_id) {
        $return = array();
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			$servergroups = $this->SendCommand('servergrouplist');
			foreach ($servergroups as $servergroup) {
				$return[] = array('id' => $servergroup['sgid'], 'name' => $this->ReplaceFromTS3($servergroup['name']),'type' => $servergroup['type']);
			}
			return $return;
		}
        return false;
	}
	public function Snapshotcreate($virtualserver_id) {
		$this->StartServer($virtualserver_id);
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			$return = $this->SendCommand(array('serversnapshotcreate'));
		} else {
			$return = $useserver;
		}
		return $return;
	}
	public function Snapshotdeploy($virtualserver_id,$snapshot) {
		$this->StartServer($virtualserver_id);
		$useserver = $this->UseServer($virtualserver_id);
		if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
			$return = $this->SendCommand('serversnapshotdeploy '.$snapshot);
		} else {
			$return = $useserver;
		}
		return $return;
	}
    public function channelList($virtualserver_id) {
        $this->StartServer($virtualserver_id);
        $useserver = $this->UseServer($virtualserver_id);
        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg'])==strtolower('ok')) {
            $channels = array();
            $channelList = $this->SendCommand('channellist');
            if (is_array($channelList)) {
                foreach ($channelList as $channel) {
                    if (isset($channel['cid']) and isid($channel['cid'],30)) {
                        $props = $this->SendCommand('channelinfo cid='.$channel['cid']);
                        if (isset($props[0]['channel_name'])) $channels[$this->ReplaceFromTS3($props[0]['channel_name'])] = $this->ReplaceFromTS3($props[0]['channel_filepath']);
                    }
                }
            }
            $return=json_encode($channels);
        } else {
            $return = $useserver;
        }
        return $return;
    }
}


function tsbackup ($action,$sship,$sshport,$sshuser,$keyuse,$sshkey,$sshpw,$notified,$path,$virtualserver_id,$backupid,$reseller_id,$sql,$move=array()) {
	if ($keyuse=="Y") {
        # https://github.com/easy-wi/developer/issues/70
        $sshkey=removePub($sshkey);
        $pubkey = EASYWIDIR . '/keys/' . $sshkey . '.pub';
        $key = EASYWIDIR . '/keys/' . $sshkey;

		if (file_exists($pubkey) and file_exists($key)) {
			$ssh2= @ssh2_connect($sship,$sshport, array('hostkey' => 'ssh-rsa'));
		} else {
			$ssh2 = false;
		}
	} else {
		$ssh2= @ssh2_connect($sship,$sshport);
	}
	if ($ssh2 == true) {
		if ($keyuse=="Y" and isset($pubkey) and isset($key)) {
			$connect_ssh2= @ssh2_auth_pubkey_file($ssh2,$sshuser,$pubkey,$key);
		} else {
			$connect_ssh2= @ssh2_auth_password($ssh2,$sshuser,$sshpw);
		}
	    if ($connect_ssh2 == true) {
			$split_config=preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);
			$folderfilecount=count($split_config)-1;
			$i = 0;
            if (substr($path,0,1) == '/') {
                $folders='/';
            } else {
                $folders='/home/'.$sshuser.'/';
            }
			while ($i<=$folderfilecount) {
				$folders .= $split_config[$i] . '/';
				$i++;
			}
			if ($folders == '') {
				$folders='.';
			}
            if (substr($folders,-1) != '/') $folders = $folders.'/';
			$filefolder = $folders.'files/virtualserver_'.$virtualserver_id.'/';
			$backupfolder = $folders.'backups/virtualserver_'.$virtualserver_id.'/';
			if ($action == 'create') {
				$function='function backup () { mkdir -p '.$backupfolder.' && nice -n +19 tar cfj '.$backupfolder.$backupid.'.tar.bz2 '.$filefolder.'; }';
			} else if ($action == 'delete') {
				$function='function backup () { nice -n +19 rm -f '.$backupfolder.$backupid.'.tar.bz2; }';
			} else if ($action == 'deploy') {
				$function='function backup () { nice -n +19 rm -rf '.$filefolder.'* && nice -n +19 tar xfj '.$backupfolder.$backupid.'.tar.bz2 -C /';
                if (count($move)>0) foreach ($move as $o=>$n) $function .= ' && mv '.$o . ' ' . $n;
                $function .= '; }';
			}
            if (isset($function)) {
                $ssh2cmd='cd '.$folders.' && '.$function.'; backup& ';
                ssh2_exec($ssh2,$ssh2cmd);
                if ($notified== 'Y') {
                    $query = $sql->prepare("UPDATE `voice_masterserver` SET `notified`='N' WHERE `ssh2ip`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($sship,$reseller_id));
                }
            } else {
                $bad="Incorrect action";
            }
		} else {
			$bad="The login data does not work";
		}
	} else {
		$bad="Could not connect to Server";
	}
	if (isset($bad) and $notified!='Y') {
		if ($reseller_id==0) {
            $query = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `resellerid`=0 AND `accounttype`='a'");
            $query->execute();
		} else {
            $query = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE (`id`=? AND `id`=`resellerid`) OR (`resellerid`=0 AND `accounttype`='a')");
            $query->execute(array($reseller_id));
		}
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
			if ($row['mail_serverdown'] == 'Y') {
				sendmail('emaildown', $row['id'], 'TS3 Master '.$sship.' ( '.$bad.' )','');
			}
		}
        $query = $sql->prepare("UPDATE `voice_masterserver` SET `notified`='Y' WHERE `ssh2ip`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($sship,$reseller_id));
		return $bad;
	} else {
		return 'ok';
	}
}
function tsdns ($action,$sship,$sshport,$sshuser,$keyuse,$sshkey,$sshpw,$notified,$path,$bitversion,$tsip,$tsport,$tsdns,$reseller_id,$sql,$maxnotified=2) {
	if ($keyuse=="Y") {
		$pubkey=EASYWIDIR . "/keys/".$sshkey.".pub";
		$key=EASYWIDIR . "/keys/".$sshkey;
		if (file_exists($pubkey) and file_exists($key)) {
			$ssh2= @ssh2_connect($sship,$sshport, array('hostkey' => 'ssh-rsa'));
		} else {
			$ssh2 = false;
		}
	} else {
		$ssh2= @ssh2_connect($sship,$sshport);
	}
	if ($ssh2 == true) {
		if ($keyuse=="Y") {
			$connect_ssh2= @ssh2_auth_pubkey_file($ssh2,$sshuser,$pubkey,$key);
		} else {
			$connect_ssh2= @ssh2_auth_password($ssh2,$sshuser,$sshpw);
		}
	    if ($connect_ssh2 == true) {
			$split_config=preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);
			$folderfilecount=count($split_config)-1;
			$i = 0;
            if (substr($path,0,1) == '/') {
                $folders='/';
            } else {
                $folders = '';
            }
            $lastFolder = '';
            while ($i<=$folderfilecount) {
                $folders .= $split_config[$i] . '/';
                $lastFolder = $split_config[$i];
                $i++;
            }
            if ($lastFolder!='tsdns' or substr($path,0,1) != '/') {
                $folders .="tsdns/";
            }
			if ($bitversion == '32') {
				$bin='tsdnsserver_linux_x86';
			} else {
				$bin='tsdnsserver_linux_amd64';
			}
			$ssh2cmd='cd '.$folders.' && function restart () { if [ "`ps fx | grep '.$bin.' | grep -v grep`" == "" ]; then ./'.$bin.' > /dev/null & else ./'.$bin.' --update > /dev/null & fi }; restart& ';
			if ($action == 'md' or $action == 'dl') {
				$newip = $tsip[0];
				if (isset($tsip[1])) {
					$oldip = $tsip[1];
				} else {
					$oldip = '';
				}
				$newport = $tsport[0];
				if (isset($tsport[1])) {
					$oldport = $tsport[1];
				} else {
					$oldport = '';
				}
				$newdns = $tsdns[0];
				if (isset($tsdns[1])) {
					$olddns = $tsdns[1];
				} else {
					$olddns = '';
				}
			} else {
				$dnsarray = array();
			}
			$sftp=ssh2_sftp($ssh2);
            if (substr($path,0,1) == '/') {
                $file='ssh2.sftp://'.$sftp.$folders.'tsdns_settings.ini';
            } else {
                $file='ssh2.sftp://'.$sftp.'/home/'.$sshuser. '/' . $folders.'tsdns_settings.ini';
            }
			if ($action!='rs') {
				$tsdns_read= @fopen($file,'r');
                if ($tsdns_read) {
                    $buffer = '';
                    $filesize=filesize($file);
                    if ($filesize==0) {
                        $filesize = 1;
                    }
                    while (strlen($buffer)<$filesize) {
                        $buffer.=fread($tsdns_read,$filesize);
                    }
                    fclose($tsdns_read);
                    $data=str_replace(array("\0","\b","\r","\Z"),'',$buffer);
                }
			}
			if ($action!='rs' and $action!='mw' and $tsdns_read) {
				$edited = false;
				$ca = array();
				foreach (preg_split('/\n/',$data,-1,PREG_SPLIT_NO_EMPTY) as $configLine) {
					if ($action!='li' and $configLine!="$olddns = $oldip:$oldport" and $configLine!="$newdns = $newip:$newport") {
						$ca[] = $configLine."\r\n";
					} else if ($action == 'md' and $edited == false and ($configLine=="$olddns = $oldip:$oldport" or $configLine=="$newdns = $newip:$newport")) {
						$edited = true;
						$ca[]="$newdns = $newip:$newport\r\n";
					}
					if ($action == 'li' and $configLine!='' and !preg_match('/^#(|\s+)(.*)$/',$configLine)) {
						$dnsconfig=explode('=',$configLine);
						if (isset($dnsconfig[1])) {
							$linedns = $dnsconfig[0];
							$lineserver = $dnsconfig[1];
							$dnsarray[$lineserver] = $linedns;
						}
					}
				}
				if ($action == 'md' and $edited == false) {
					$ca[]="$newdns = $newip:$newport\r\n";
				}
				if ($action!='li') {
                    $ca=array_unique($ca);
                    sort($ca);
					$newcfg = '';
					foreach ($ca as $line) {
						$newcfg .= $line;
					}
					if ($newcfg== '') {
						$newcfg='# No TSDNS data entered';
					}
					$tsdns_write= fopen($file,'w');
					$writefile= fwrite($tsdns_write,$newcfg);
					if ($writefile == false) {
						$bad='Could not upload tsdns_settings.ini';
					}
					fclose($tsdns_write);
				}
			}
			if ($action == 'mw' and isset($data)) {
                $usedIPs = array();
                foreach (preg_split('/\n/',$data,-1,PREG_SPLIT_NO_EMPTY) as $configLine) {
                    if ($configLine!='' and !preg_match('/^#(|\s+)(.*)$/',$configLine)) {
                        $splittedLine=preg_split('/\=/',$configLine,-1,PREG_SPLIT_NO_EMPTY);
                        if (isset($splittedLine[1])) {
                            $usedIPs[] = array('dns' => $splittedLine[0], 'address' => $splittedLine[1]);
                        } else {
                            $usedIPs[] = $configLine;
                        }
                    } else {
                        $usedIPs[] = $configLine;
                    }
                }
                foreach ($tsip as $newLine) {
                    $splittedLine=preg_split('/\=/',strtolower($newLine),-1,PREG_SPLIT_NO_EMPTY);
                    if (isset($splittedLine[1]) and !array_key_exists($splittedLine[1],$usedIPs)) {
                        $usedIPs[] = array('dns' => $splittedLine[0], 'address' => $splittedLine[1]);
                    }
                }
                function array_multi_dimensional_unique($multi){
                    $unique = array();
                    foreach($multi as $sub){
                        if(!in_array($sub,$unique)){
                            $unique[] = $sub;
                        }
                    }
                    return $unique;
                }
                $usedIPs=array_multi_dimensional_unique($usedIPs);
                sort($usedIPs);
				$newCfg = '';
				foreach ($usedIPs as $value) {
                    if (isset($value['dns']) and isset($value['address']) and !preg_match('/^#(|\s+)(.*)$/',$value['dns'])) {
                        $newCfg .= $value['dns'].'='.$value['address']."\r\n";
                    } else {
                        $newCfg .= $value."\r\n";
                    }
				}
				if ($newCfg== '') {
                    $bad='Nothing to write';
				} else {
                    $tsdns_write= @fopen($file,'w');
                    $writefile= @fwrite($tsdns_write,$newCfg);
                    if ($writefile == false) {
                        $bad='Could not upload tsdns_settings.ini';
                    } else {
                        fclose($tsdns_write);
                    }
                }
			}
			if (!isset($bad) and $action!='li') {
				ssh2_exec($ssh2,$ssh2cmd);
				if ($notified>0) {
                    $query = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=0 WHERE `ssh2ip`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($sship,$reseller_id));
				}
			}
		} else {
			$bad="The login data does not work";
		}
	} else {
		$bad="Could not connect to Server";
	}
	if (isset($bad) and $notified==$maxnotified) {
		if ($reseller_id==0) {
			$query = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `resellerid`=0 AND `accounttype`='a'");
			$query->execute();
		} else {
			$query = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE (`id`=? AND `id`=`resellerid`) OR (`resellerid`=0 AND `accounttype`='a')");
			$query->execute(array($reseller_id));
		}
		foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
			if ($row['mail_serverdown'] == 'Y') {
				sendmail('emaildown', $row['id'], 'TS3 Master '.$sship.' ( '.$bad.' )','');
			}
		}
        $query = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=`notified`+1 WHERE `ssh2ip`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($sship,$reseller_id));
		return $bad;
    } else if (isset($bad)) {
        return $bad;
	} else if ($action == 'li' and isset($dnsarray)) {
		return $dnsarray;
	} else {
		return 'ok';
	}
}
function checkDNS ($dns,$id=null,$user_id=null,$type='') {
    global $sql;
    global $reseller_id;
    if ($type == 'server') {
        $query = $sql->prepare("SELECT `masterserver` FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $masterID = $query->fetchColumn();
        $query = $sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dns`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($dns,$reseller_id));
        $query2 = $sql->prepare("SELECT `masterserver` FROM `voice_server` WHERE `id`!=? AND `dns`=? AND `resellerid`=? LIMIT 1");
        $query2->execute(array($id,$dns,$reseller_id));
    } else if ($type == 'dns') {
        $query = $sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dnsID`!=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $masterID = $query->fetchColumn();
        $query = $sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dnsID`!=? AND `dns`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$dns,$reseller_id));
        $query2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `dns`=? AND `resellerid`=? LIMIT 1");
        $query2->execute(array($dns,$reseller_id));
    } else {
        $query = $sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dns`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($dns,$reseller_id));
        $query2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `dns`=? AND `resellerid`=? LIMIT 1");
        $query2->execute(array($dns,$reseller_id));
    }
    if ($query->rowCount()>0 or $query2->rowCount()>0) {
        return false;
    }
    if ($user_id != null) {
        $serverdnsArray = array();
        $query = $sql->prepare("SELECT `id`,`defaultdns`,`externalDefaultDNS` FROM `voice_masterserver` WHERE `resellerid`=?");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if ($row['externalDefaultDNS'] == 'N') {
                if ($type == 'server' and $id != null and $row['id'] == $masterID) {
                    $defaultdns=strtolower($id . '.' . $row['defaultdns']);
                    $partCount=count(explode('.',$defaultdns));
                }
                unset($temp);
                $ex=explode('.', $row['defaultdns']);
                $i=count($ex)-1;
                while ($i>=0) {
                    if (isset($temp)) {
                        $temp = $ex[$i] . '.' . $temp;
                    } else {
                        $temp = $ex[$i];
                    }
                    $serverdnsArray[] = $temp;
                    $i--;
                }
            } else if ($type == 'server' and $row['externalDefaultDNS'] == 'Y' and $id != null and $row['id'] == $masterID) {
                $tsdnsServerID = $row['tsdnsServerID'];
            }
        }
        $query = $sql->prepare("SELECT `id`,`defaultdns` FROM `voice_tsdns` WHERE `resellerid`=?");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if ((isset($tsdnsServerID) and $id != null and $row['id'] == $tsdnsServerID) or ($type == 'dns' and $id != null and $row['id'] == $masterID)) {
                $defaultdns=strtolower($id . '-' . getusername($user_id) . '.' . $row['defaultdns']);
                $partCount=count(explode('.',$defaultdns));
            }
            unset($temp);
            $ex=explode('.', $row['defaultdns']);
            $i=count($ex)-1;
            while ($i>=0) {
                if (isset($temp)) {
                    $temp = $ex[$i] . '.' . $temp;
                } else {
                    $temp = $ex[$i];
                }
                $serverdnsArray[] = $temp;
                $i--;
            }
        }
        if (isset($defaultdns) and $dns==$defaultdns) {
            return true;
        }
        $ex=explode('.',$dns);
        $dnsPartCount=count($ex);
        $first = $ex[0];
        if (isset($partCount) and $partCount==$dnsPartCount and isid($first,10) and ($type == 'dns' or ($type == 'server' and $first != $id))) {
            return false;
        }
        $ex=explode('-',$first);
        if ($type == 'dns' and isset($partCount) and $partCount==$dnsPartCount and $ex[0] != $id) {
            return false;
        }
        $serverdnsArray=array_unique($serverdnsArray);
        if (((isset($defaultdns) and $dns != $defaultdns) or !isset($defaultdns)) and in_array($dns,$serverdnsArray)) {
            return false;
        }
    }
    return true;
}