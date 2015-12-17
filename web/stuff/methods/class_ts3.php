<?php

/**
 * File: class_ts3.php.
 * Author: Ulrich Block
 * Date: 29.12.13
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

    public function ReplaceToTS3 ($value) {

        if ($value === false or $value === null) {
            $value = '';
        }

        return str_replace(array('\\', '/', ' ', '|'), array('\\\\', '\/', '\s', '\p'), $value);
    }

    private function ReplaceFromTS3 ($value) {
        return str_replace(array('\\\\', '\/', '\s', '\p'), array('\\', '/', ' ', '|'), $value);
    }

    public function SendCommand ($value) {
        if ($this->connected == true and is_resource($this->socket)) {

            $return = array();
            $response = '';

            if (is_array($value)) {
                fputs($this->socket, $value[0] . "\n");
            } else {
                fputs($this->socket, $value . "\n");
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

            @list($databody, $errorcode) = explode('error id=', str_replace(array("\r", "\n"), '', $response));

            if ($errorcode) {
                $this->errorcode = 'error id=' . $errorcode;
            }

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

                        $index = trim(preg_replace('/\s+/','', $splitted[0]));

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
        $this->socket = @fsockopen('tcp://' . $ip, $queryport, $errnum, $errstr, 5);

        if ($this->socket == true) {

            $this->socketConnected = true;

            if (strpos(fgets($this->socket, 8192),'TS3') !== false) {
                $welcome = @fgets($this->socket, 8192);
                @fputs($this->socket, "login $admin $querypassword" . "\n");
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
        return $this->ReplaceFromTS3($this->SendCommand('use ' . $virtualserver_id));
    }

    public function AddServer ($maxclients, $ip, $port, $password, $name, $message, $download, $upload, $banner_url, $banner_gfx, $button_url, $button_gfx, $tooltip, $customConfigurations = array()) {

        #." virtualserver_ip=".$ip
        $addcommand="servercreate virtualserver_maxclients=".$maxclients." virtualserver_port=".$port." virtualserver_password=".$password." virtualserver_name=".$this->ReplaceToTS3($name)." virtualserver_welcomemessage=".$this->ReplaceToTS3($message[1]);
        $addcommand .=" virtualserver_max_download_total_bandwidth=".$download." virtualserver_max_upload_total_bandwidth=".$upload." virtualserver_hostbanner_url=".$banner_url[1]." virtualserver_hostbanner_gfx_url=".$banner_gfx;
        $addcommand .=" virtualserver_hostbutton_url=".$button_url[1]." virtualserver_hostbutton_gfx_url=".$button_gfx." virtualserver_hostbutton_tooltip=".$this->ReplaceToTS3($tooltip);

        foreach ($customConfigurations as $config) {
            $addcommand .= ' ' . $config;
        }

        @fputs($this->socket, $addcommand . "\n");

        $response = $this->ReplaceFromTS3(fgets($this->socket,8192));

        if (strpos($response,'error id=0') !== false or strpos($response,'sid=') !== false) {

            $error = $this->ReplaceFromTS3(@fgets($this->socket,8192));

            $ex1 = explode(' ', $response);
            $ex2 = explode('=', $ex1[0]);
            $virtualserver_id = $ex2[1];

            $useserver = $this->UseServer($virtualserver_id);

            if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

                $remove = array('b_virtualserver_start','i_needed_modify_power_virtualserver_start',
                    'b_virtualserver_stop','i_needed_modify_power_virtualserver_stop',
                    'b_virtualserver_modify_maxclients','i_needed_modify_power_virtualserver_modify_maxclients',
                    'b_virtualserver_modify_port','i_needed_modify_power_virtualserver_modify_port',
                    'b_virtualserver_modify_autostart','i_needed_modify_power_virtualserver_modify_autostart',
                    'b_virtualserver_modify_ft_settings','i_needed_modify_power_virtualserver_modify_ft_settings'
                );

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
                    if ($servegroup['type'] == 1) {
                        $delcommand = "servergroupdelperm sgid=" . $servegroup['sgid']." permsid=" . implode('|permsid=', $remove);
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

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            $this->SendCommand("serveredit virtualserver_autostart=0");

            $response = $this->SendCommand('serverstop sid=' . $virtualserver_id);

            return $response;

        }

        return false;

    }

    public function StartServer($virtualserver_id) {

        $response = $this->SendCommand('serverstart sid=' . $virtualserver_id);

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {
            $this->SendCommand("serveredit virtualserver_autostart=1");
        }

        return $response;

    }

    public function DelServer($virtualserver_id) {

        $this->SendCommand('serverstop sid=' . $virtualserver_id);

        $delete = $this->SendCommand('serverdelete sid=' . $virtualserver_id);

        return $delete;

    }

    public function ModServer ($virtualserver_id, $maxclients, $ip, $port, $password, $name, $message, $download, $upload, $banner_url, $banner_gfx, $button_url, $button_gfx, $tooltip, $virtualserver_reserved_slots = null, $virtualserver_needed_identity_security_level=null, $virtualserver_hostmessage_mode=null, $virtualserver_hostbanner_gfx_interval=null, $virtualserver_antiflood_points_tick_reduce=null, $virtualserver_antiflood_points_needed_command_block=null, $virtualserver_antiflood_points_needed_ip_block=null, $customConfigurations = array()) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            #." virtualserver_ip=".$this->ReplaceToTS3($ip);
            $modcommand = 'serveredit virtualserver_maxclients=' . $maxclients . ' virtualserver_port=' . $port . ' virtualserver_password=' . $this->ReplaceToTS3($password) . ' virtualserver_name=' . $this->ReplaceToTS3($name) . ' virtualserver_welcomemessage=' . $this->ReplaceToTS3($message);
            $modcommand  .= ' virtualserver_max_download_total_bandwidth=' . $download . ' virtualserver_max_upload_total_bandwidth=' . $upload;

            if ($banner_url != '' and $banner_url != null) {
                $modcommand  .= ' virtualserver_hostbanner_url=' . $this->ReplaceToTS3($banner_url);
            }

            if ($banner_gfx != '' and $banner_gfx != null) {
                $modcommand  .= ' virtualserver_hostbanner_gfx_url=' . $this->ReplaceToTS3($banner_gfx);
            }

            if ($button_url != '' and $button_url != null) {
                $modcommand  .= ' virtualserver_hostbutton_url=' . $this->ReplaceToTS3($button_url);
            }

            if ($button_gfx != '' and $button_gfx != null) {
                $modcommand  .= ' virtualserver_hostbutton_gfx_url=' . $this->ReplaceToTS3($button_gfx);
            }

            if ($tooltip != '' and $tooltip != null) {
                $modcommand  .= ' virtualserver_hostbutton_tooltip=' . $this->ReplaceToTS3($tooltip);
            }

            # Ticket https://github.com/easy-wi/developer/issues/13 'Bearbeiten von TS3 Servern im Usermodul erweitern'
            if ($virtualserver_reserved_slots != '' and $virtualserver_reserved_slots != null) {
                $modcommand .= ' virtualserver_reserved_slots=' . $this->ReplaceToTS3($virtualserver_reserved_slots);
            }

            if ($virtualserver_needed_identity_security_level != '' and $virtualserver_needed_identity_security_level != null) {
                $modcommand .= ' virtualserver_needed_identity_security_level=' . $this->ReplaceToTS3($virtualserver_needed_identity_security_level);
            }

            if ($virtualserver_hostmessage_mode != '' and $virtualserver_hostmessage_mode != null) {
                $modcommand .= ' virtualserver_hostmessage_mode=' . $this->ReplaceToTS3($virtualserver_hostmessage_mode);
            }

            if ($virtualserver_hostbanner_gfx_interval != '' and $virtualserver_hostbanner_gfx_interval != null) {
                $modcommand .= ' virtualserver_hostbanner_gfx_interval=' . $this->ReplaceToTS3($virtualserver_hostbanner_gfx_interval);
            }

            if ($virtualserver_antiflood_points_tick_reduce != '' and $virtualserver_antiflood_points_tick_reduce != null) {
                $modcommand .= ' virtualserver_antiflood_points_tick_reduce=' . $this->ReplaceToTS3($virtualserver_antiflood_points_tick_reduce);
            }

            if ($virtualserver_antiflood_points_needed_command_block != '' and $virtualserver_antiflood_points_needed_command_block != null) {
                $modcommand .= ' virtualserver_antiflood_points_needed_command_block=' . $this->ReplaceToTS3($virtualserver_antiflood_points_needed_command_block);
            }

            if ($virtualserver_antiflood_points_needed_ip_block != '' and $virtualserver_antiflood_points_needed_ip_block != null) {
                $modcommand .= ' virtualserver_antiflood_points_needed_ip_block=' . $this->ReplaceToTS3($virtualserver_antiflood_points_needed_ip_block);
            }

            foreach ($customConfigurations as $config) {
                $modcommand .= ' ' . $config;
            }

            $response = $this->SendCommand($modcommand);

            return $response;

        }

        return false;

    }

    public function ImportModServer ($virtualserver_id, $maxclients, $ip, $port, $array) {

        $this->StartServer($virtualserver_id);
        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            #." virtualserver_ip=".$ip
            $modcommand = 'serveredit virtualserver_maxclients=' . $maxclients . ' virtualserver_port=' . $port;

            foreach ($array as $key => $value) {
                $modcommand .= ' ' . $key . '=' . $this->ReplaceToTS3($value);
            }

            $response = $this->SendCommand($modcommand);

            return $response;

        }

        return false;

    }

    public function AdminPermissions ($virtualserver_id, $what, $permlist) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            $permissioncount = 0;

            $servergroups = $this->SendCommand('servergrouplist');

            foreach ($servergroups as $servegroups) {
                if (isset($servegroups['type']) && $this->ReplaceFromTS3($servegroups['type']) == 1) {

                    $newcount = count($this->SendCommand('servergrouppermlist sgid=' . $servegroups['sgid']));

                    if ($newcount > $permissioncount) {

                        $admingroupid = $servegroups['sgid'];
                        $permissioncount = $newcount;

                    } else if ($newcount==$permissioncount) {

                        if (isset($admingroupid) and is_array($admingroupid)) {
                            $admingroupid[] = $servegroups['sgid'];
                        } else if (isset($admingroupid)) {
                            $admingroupid = array($admingroupid, $servegroups['sgid']);
                        }

                        $permissioncount = $newcount;

                    }
                }
            }

            if ($what == 'del') {
                $remove = array('b_virtualserver_start','i_needed_modify_power_virtualserver_start',
                    'b_virtualserver_stop','i_needed_modify_power_virtualserver_stop',
                    'b_virtualserver_modify_maxclients','i_needed_modify_power_virtualserver_modify_maxclients',
                    'b_virtualserver_modify_port','i_needed_modify_power_virtualserver_modify_port',
                    'b_virtualserver_modify_autostart','i_needed_modify_power_virtualserver_modify_autostart',
                    'b_virtualserver_modify_ft_settings','i_needed_modify_power_virtualserver_modify_ft_settings'
                );
                $permlist = array_merge($permlist, $remove);
            }

            if (isset($admingroupid) and is_array($admingroupid)) {
                foreach ($admingroupid as $id) {
                    if ($what == 'add') {

                        $command = 'servergroupaddperm sgid=' . $id . ' permsid=' . implode(' permvalue=1 permnegated=0 permskip=0|permsid=', $permlist) . ' permvalue=1 permnegated=0 permskip=0';

                        $this->SendCommand($command);

                    } else if ($what == 'del') {

                        $command = 'servergroupdelperm sgid=' . $id . ' permsid=' . implode('|permsid=', $permlist);

                        $this->SendCommand($command);
                    }
                }

            } else if (isset($admingroupid)) {

                if ($what == 'add') {

                    $command = 'servergroupaddperm sgid=' . $admingroupid . ' permsid=' . implode(' permvalue=1 permnegated=0 permskip=0|permsid=', $permlist) . ' permvalue=1 permnegated=0 permskip=0';

                    $this->SendCommand($command);

                } else if ($what == 'del') {

                    $command = 'servergroupdelperm sgid=' . $admingroupid . ' permsid=' . implode('|permsid=', $permlist);

                    $this->SendCommand($command);

                }
            }
        }
    }

    private function tenaryReturn ($array, $key) {
        return (isset($array[$key])) ? $array[$key] : '';
    }

    public function ImportData ($dnsarray) {

        if (!is_array($dnsarray)) {
            $dnsarray = (array) $dnsarray;
        }

        $serverdetails = array();

        $serverlist = $this->SendCommand('serverlist');

        foreach ($serverlist as $server) {
            if (isset($server['virtualserver_id'])) {

                $virtualserver_id = $server['virtualserver_id'];

                if ($server['virtualserver_status'] == 'offline') {
                    $this->StartServer($virtualserver_id);
                }

                $useserver = $this->UseServer($virtualserver_id);

                if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

                    $virtualserver_ip = (isset($serverdetails_query[0]['virtualserver_ip']) and isip($serverdetails_query[0]['virtualserver_ip'], 'all')) ? $serverdetails_query[0]['virtualserver_ip'] : $this->ip;

                    $serverdetails_query = $this->SendCommand('serverinfo');
                    $virtualserver_server = $virtualserver_ip . ':' . $server['virtualserver_port'];
                    $virtualserver_dns = (array_key_exists($virtualserver_server, $dnsarray)) ? $dnsarray[$virtualserver_server] : '';

                    $serverdetails[$virtualserver_id] = array(
                        'virtualserver_ip' => $virtualserver_ip,
                        'virtualserver_maxclients' => $this->tenaryReturn($serverdetails_query[0], 'virtualserver_maxclients'),
                        'virtualserver_port' => $server['virtualserver_port'],
                        'virtualserver_dns' => $virtualserver_dns,
                        'virtualserver_name' => $this->ReplaceFromTS3($this->tenaryReturn($serverdetails_query[0], 'virtualserver_name')),
                        'virtualserver_welcomemessage' => $this->tenaryReturn($serverdetails_query[0], 'virtualserver_welcomemessage'),
                        'virtualserver_flag_password' => $this->tenaryReturn($serverdetails_query[0], 'virtualserver_flag_password'),
                        'virtualserver_max_download_total_bandwidth' => $this->tenaryReturn($serverdetails_query[0], 'virtualserver_max_download_total_bandwidth'),
                        'virtualserver_max_upload_total_bandwidth' => $this->tenaryReturn($serverdetails_query[0], 'virtualserver_max_upload_total_bandwidth'),
                        'virtualserver_hostbanner_url' => $this->tenaryReturn($serverdetails_query[0], 'virtualserver_hostbanner_url'),
                        'virtualserver_hostbanner_gfx_url' => $this->tenaryReturn($serverdetails_query[0], 'virtualserver_hostbanner_gfx_url'),
                        'virtualserver_hostbutton_tooltip' => $this->tenaryReturn($serverdetails_query[0], 'virtualserver_hostbutton_tooltip'),
                        'virtualserver_hostbutton_url' => $this->tenaryReturn($serverdetails_query[0], 'virtualserver_hostbutton_url'),
                        'virtualserver_hostbutton_gfx_url' => $this->tenaryReturn($serverdetails_query[0], 'virtualserver_hostbutton_gfx_url')
                    );
                }

                if ($server['virtualserver_status'] == 'offline') {
                    $this->StopServer($virtualserver_id);
                }

                if (!isset($serverdetails[$virtualserver_id])) {
                    $virtualserver_ip = (isset($virtualserver_ip)) ? $virtualserver_ip :'';
                    $virtualserver_dns = (isset($virtualserver_dns)) ? $virtualserver_dns :'';
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

        if (is_array($array) or is_object($array)) {
            foreach ($array as $k => $v) {
                $serverdetails[$k] = $this->ReplaceFromTS3($v);
            }
        }

        if ($this->debug == true){
            print "ServerList:";
            print_r($serverdetails);
            print "\r\n";
        }

        return $serverdetails;
    }

    public function ServerDetails ($virtualserver_id) {

        $serverdetails = array(
            'virtualserver_name' => '',
            'virtualserver_welcomemessage' => '',
            'virtualserver_hostbanner_url' => '',
            'virtualserver_hostbanner_gfx_url' => '',
            'virtualserver_hostbutton_tooltip' => '',
            'virtualserver_hostbutton_url' => '',
            'virtualserver_hostbutton_gfx_url' => '',
            'virtualserver_maxclients' => '',
            'virtualserver_flag_password' => '',
            'virtualserver_max_download_total_bandwidth' => '',
            'virtualserver_max_upload_total_bandwidth' => '',
            'virtualserver_clientsonline' => 0,
            'virtualserver_queryclientsonline' => 0,
            'virtualserver_uptime' => 20,
            'virtualserver_status' => '',
            'connection_filetransfer_bytes_sent_total' => '',
            'connection_filetransfer_bytes_received_total' => '',
            'connection_bytes_sent_total' => '',
            'connection_bytes_received_total' => '',
            'virtualserver_reserved_slots' => '',
            'virtualserver_needed_identity_security_level' => '',
            'virtualserver_hostmessage_mode' => '',
            'virtualserver_hostbanner_gfx_interval' => '',
            'virtualserver_antiflood_points_tick_reduce' => '',
            'virtualserver_antiflood_points_needed_command_block' => '',
            'virtualserver_antiflood_points_needed_ip_block' => ''
        );

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            $serverdetails_query = $this->SendCommand('serverinfo');

            if (isset($serverdetails_query[0]['virtualserver_clientsonline'])) {
                $serverdetails = array(
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
            }

            if ($this->debug == true){
                print "Serverdetails:";
                print_r($serverdetails);
                print "\r\n";
            }

        } else if ($this->debug == true) {

            print "Userserver at serverdetails failed:";
            print_r($useserver);
            print "\r\n";

            if (isset($useserver[0])) {
                print_r($useserver[0]);
            } else {
                echo '$useserver[0] not set';
            }
            print "\r\n";

            if (isset($useserver[0])) {
                print_r($useserver[0]['msg']);
            } else {
                echo '$useserver[0]["msg"] not set';
            }
            print "\r\n";
        }

        return $serverdetails;

    }

    public function AdminList ($virtualserver_id) {

        $adminlist = '';

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            unset($admingroupid);

            $permissioncount = 0;

            $servergroups = $this->SendCommand('servergrouplist');

            foreach ($servergroups as $servegroups) {
                if (isset ($servegroups['type']) and $servegroups['type'] == 1 and $servegroups['name'] == 'Server\sAdmin') {
                    $admingroupid = $servegroups['sgid'];
                }
            }

            if (!isset($admingroupid)) {
                foreach ($servergroups as $servegroups) {
                    if (isset($servegroups['type']) and $this->ReplaceFromTS3($servegroups['type']) == 1) {

                        $newcount = count($this->SendCommand('servergrouppermlist sgid=' . $servegroups['sgid']));

                        if ($newcount > $permissioncount) {

                            $admingroupid = $servegroups['sgid'];
                            $permissioncount = $newcount;

                        } else if ($newcount == $permissioncount) {

                            if (isset($admingroupid) and is_array($admingroupid)) {
                                $admingroupid[] = $servegroups['sgid'];
                            } else if (isset($admingroupid)) {
                                $admingroupid = array($admingroupid, $servegroups['sgid']);
                            }

                            $permissioncount = $newcount;

                        }
                    }
                }
            }

            if (isset($admingroupid) and is_array($admingroupid)) {
                foreach ($admingroupid as $id) {
                    $userlistraw = $this->SendCommand('servergroupclientlist sgid=' . $id);
                }
            } else if (isset($admingroupid)) {
                $userlistraw = $this->SendCommand('servergroupclientlist sgid=' . $admingroupid);
            }

            if (isset($userlistraw) and is_array($userlistraw) and isset($userlistraw[0]['cldbid'])) {

                foreach ($userlistraw as $userid) {

                    $userdata = $this->SendCommand('clientdbinfo cldbid=' . $userid['cldbid']);

                    $client_unique_identifier = $this->ReplaceFromTS3($userdata[0]['client_unique_identifier']);

                    $adminlist[$userid['cldbid']] = $client_unique_identifier;

                }

            } else {
                $adminlist = array();
            }
        }

        return $adminlist;

    }

    public function KeyList ($virtualserver_id) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            $return = array();

            $servergroups = $this->SendCommand('servergrouplist');

            foreach ($servergroups as $servergroup) {
                if (isset($servergroup['type']) and $servergroup['type'] == 1) {
                    $admingroupid[$servergroup['sgid']] = $this->ReplaceFromTS3($servergroup['name']);
                }
            }

            foreach ($this->SendCommand('privilegekeylist') as $key) {
                if (isset($key['token_type']) and $key['token_type'] == 0 and isset($admingroupid)) {
                    $return[] = array('token' => $key['token'], 'groupname' => $this->ReplaceFromTS3($admingroupid[$key['token_id1']]));
                }
            }

        } else {
            $return = $useserver[0]['msg'];
        }

        return $return;

    }

    public function DelKey ($virtualserver_id, $token) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {
            return $this->SendCommand('privilegekeydelete token=' . $token);
        }

        return false;

    }

    public function AddKey ($virtualserver_id, $group) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {
            return $this->SendCommand('privilegekeyadd tokentype=0 tokenid1=' . $group . ' tokenid2=0');
        }

        return false;

    }

    public function PermReset ($virtualserver_id) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            foreach ($this->SendCommand('ftlist') as $ftl) {
                if (isset($ftl['serverftfid'])) {
                    $this->SendCommand('ftstop serverftfid=' . $ftl['serverftfid'] . ' delete=1');
                }
            }

            $channellist = $this->SendCommand('channellist -flags');

            foreach ($channellist as $channel) {

                if ($channel['channel_flag_default']) {

                    unset($filedeletecmd);

                    $this->SendCommand('channeledit cid=' . $channel['cid'] . ' channel_name=Default\sChannel channel_flag_permanent=1 channel_password= channel_needed_subscribe_power=0 channel_codec=2 channel_codec_quality=7 channel_maxclients=-1 channel_maxfamilyclients=-1');

                    foreach ($this->SendCommand('ftgetfilelist cid=' . $channel['cid'] . ' cpw= path=\/') as $content) {

                        if (isset($content['name']) and isset($filedeletecmd)) {
                            $filedeletecmd .= '|name=\/' . $content['name'];
                        } else if (isset($content['name']) and !isset($filedeletecmd)) {
                            $filedeletecmd='ftdeletefile cid=' . $channel['cid'] . ' cpw= name=\/' . $content['name'];
                        }

                    }

                    if (isset($filedeletecmd)) {
                        $this->SendCommand($filedeletecmd);
                    }

                } else {
                    $this->SendCommand('channeldelete cid=' . $channel['cid'] . ' force=1');
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

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            $servergroups = $this->SendCommand('servergrouplist');

            foreach ($servergroups as $servergroup) {
                if (isset($servergroup['sgid'])) {
                    $return[] = array('id' => $servergroup['sgid'], 'name' => $this->ReplaceFromTS3($servergroup['name']),'type' => $servergroup['type']);
                }
            }

            return $return;

        }

        return false;

    }

    public function Snapshotcreate($virtualserver_id) {

        $this->StartServer($virtualserver_id);

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {
            $return = $this->SendCommand(array('serversnapshotcreate'));
        } else {
            $return = $useserver;
        }

        return $return;

    }

    public function Snapshotdeploy($virtualserver_id, $snapshot) {

        $this->StartServer($virtualserver_id);

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {
            $return = $this->SendCommand('serversnapshotdeploy ' . $snapshot);
        } else {
            $return = $useserver;
        }

        return $return;

    }

    public function channelList($virtualserver_id) {

        $this->StartServer($virtualserver_id);

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            $channels = array();

            $channelList = $this->SendCommand('channellist');

            if (is_array($channelList)) {

                foreach ($channelList as $channel) {
                    if (isset($channel['cid']) and isid($channel['cid'], 30)) {

                        $props = $this->SendCommand('channelinfo cid=' . $channel['cid']);

                        if (isset($props[0]['channel_name'])) {
                            $channels[$this->ReplaceFromTS3($props[0]['channel_name'])] = $this->ReplaceFromTS3($props[0]['channel_filepath']);
                        }

                    }
                }
            }

            $return = json_encode($channels);

        } else {
            $return = $useserver;
        }

        return $return;

    }

    public function getClientList ($virtualserver_id) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            $returnRaw = $this->SendCommand('clientlist client_type=0');

            if (is_array($returnRaw)) {

                $return = array();

                foreach ($returnRaw as $row) {
                    if ($row['client_type'] == 0) {
                        $return[] = array('cid' => $row['cid'], 'clid' => $row['clid'], 'client_nickname' => $this->ReplaceFromTS3($row['client_nickname']));
                    }
                }

                return $return;
            }

        }

        return array();
    }

    public function banList ($virtualserver_id) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            $rawReturn = $this->SendCommand('banlist');

            if (is_array($rawReturn)) {

                $return = array();

                foreach ($rawReturn as $r) {
                    if (isset($r['banid'])) {
                        $return[$r['banid']] = array('ip' => $r['ip'], 'name' => $this->ReplaceFromTS3($r['name']), 'lastnickname' => $this->ReplaceFromTS3($r['lastnickname']), 'blocked' => $r['enforcements'], 'duration' => $r['duration'], 'ends' => date('Y-m-d H:m:s', ($r['created'] + $r['duration'])));
                    }
                }

                return $return;

            }

        }

        return array();

    }

    public function banAdd ($virtualserver_id, $cmd) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {

            $rawReturn = $this->SendCommand($cmd);

            return (isset($rawReturn[0]['banid'])) ? 'banid: ' . $rawReturn[0]['banid'] : false;
        }

        return false;

    }

    public function banDel ($virtualserver_id, $bandID) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {
            $rawReturn =  $this->SendCommand('bandel banid=' . $bandID);
            return (isset($rawReturn[0]['msg'])) ? $rawReturn[0]['msg'] : false;
        }

        return false;

    }

    public function clientKick ($virtualserver_id, $userID) {

        $useserver = $this->UseServer($virtualserver_id);

        if (isset($useserver[0]['msg']) and strtolower($useserver[0]['msg']) == 'ok') {
            return $this->SendCommand('clientkick clid=' . $userID . ' reasonid=5');
        }

        return false;
    }

    public function getServerVersion() {

        $array = $this->SendCommand('version');

        return (is_array($array) and isset($array[0]['version'])) ? $array[0]['version'] : false;
    }
}