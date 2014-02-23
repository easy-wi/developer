<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->config;?> <span class="divider">/</span></li>
            <li><?php echo $serverip.':'.$port;?> <span class="divider">/</span></li>
            <li class="active"><?php echo htmlentities($configname);?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_config;?></div>
</div>
<hr>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $id;?>&amp;type=easy&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <?php if($gamebinary=="srcds_run" and $configname=="server.cfg"){ ?>
            <div class="control-group">
                <label class="control-label" for="hostname">hostname</label>
                <div class="controls">
                    <input id="hostname" type="text" name="hostname" value="<?php if(isset($linearray['hostname'])) echo $linearray['hostname']; else echo "Hostname"; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_password">sv_password</label>
                <div class="controls">
                    <input id="sv_password" type="text" name="sv_password" value="<?php if(isset($linearray['sv_password'])) echo $linearray['sv_password']; else echo ""; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_contact">sv_contact</label>
                <div class="controls">
                    <input id="sv_contact" type="text" name="sv_contact" value="<?php if(isset($linearray['sv_contact'])) echo $linearray['sv_contact']; else echo "email@mail.tld"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_tags">sv_tags</label>
                <div class="controls">
                    <input id="sv_tags" type="text" name="sv_tags" value="<?php if(isset($linearray['sv_tags'])) echo $linearray['sv_tags']; else echo "Homepage, Clanname"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="motdfile">motdfile</label>
                <div class="controls">
                    <input id="motdfile" type="text" name="motdfile" value="<?php if(isset($linearray['motdfile'])) echo $linearray['motdfile']; else echo "motd.txt"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mapcyclefile">mapcyclefile</label>
                <div class="controls">
                    <input id="mapcyclefile" type="text" name="mapcyclefile" value="<?php if(isset($linearray['mapcyclefile'])) echo $linearray['mapcyclefile']; else echo "mapcycle.txt"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_downloadurl">sv_downloadurl</label>
                <div class="controls">
                    <input id="sv_downloadurl" type="text" name="sv_downloadurl" value="<?php if(isset($linearray['sv_downloadurl'])) echo $linearray['sv_downloadurl']; else echo 'http://www.domain.tld/fastdownload';?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="net_maxfilesize">net_maxfilesize</label>
                <div class="controls">
                    <input id="net_maxfilesize" type="text" name="net_maxfilesize" value="<?php if(isset($linearray['net_maxfilesize'])) echo $linearray['net_maxfilesize']; else echo "64"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="rcon_password">rcon_password</label>
                <div class="controls">
                    <input id="rcon_password" type="text" name="rcon_password" value="<?php if(isset($linearray['rcon_password'])) echo $linearray['rcon_password']; else echo "RconPassword"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_rcon_minfailures">sv_rcon_minfailures</label>
                <div class="controls">
                    <input id="sv_rcon_minfailures" type="text" name="sv_rcon_minfailures" value="<?php if(isset($linearray['sv_rcon_minfailures'])) echo $linearray['sv_rcon_minfailures']; else echo "3"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_rcon_maxfailures">sv_rcon_maxfailures</label>
                <div class="controls">
                    <input id="sv_rcon_maxfailures" type="text" name="sv_rcon_maxfailures" value="<?php if(isset($linearray['sv_rcon_maxfailures'])) echo $linearray['sv_rcon_maxfailures']; else echo "5"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_rcon_banpenalty">sv_rcon_banpenalty</label>
                <div class="controls">
                    <input id="sv_rcon_banpenalty" type="text" name="sv_rcon_banpenalty" value="<?php if(isset($linearray['sv_rcon_banpenalty'])) echo $linearray['sv_rcon_banpenalty']; else echo "0"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_rcon_minfailuretime">sv_rcon_minfailuretime</label>
                <div class="controls">
                    <input id="sv_rcon_minfailuretime" type="text" name="sv_rcon_minfailuretime" value="<?php if(isset($linearray['sv_rcon_minfailuretime'])) echo $linearray['sv_rcon_minfailuretime']; else echo "15"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_pure">sv_pure</label>
                <div class="controls">
                    <select id="sv_pure" name="sv_pure">
                        <option value="0">0</option>
                        <option value="1" <?php if(isset($linearray['sv_pure']) and $linearray['sv_pure']=="1") echo "selected=\"selected\""; ?>>1</option>
                        <option value="2" <?php if(isset($linearray['sv_pure']) and $linearray['sv_pure']=="2") echo "selected=\"selected\""; ?>>2</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_pure_kick_clients">sv_pure_kick_clients</label>
                <div class="controls">
                    <select id="sv_pure_kick_clients" name="sv_pure_kick_clients">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_pure_kick_clients']) and $linearray['sv_pure_kick_clients']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_timeout">sv_timeout</label>
                <div class="controls">
                    <input id="sv_timeout" type="text" name="sv_timeout" value="<?php if(isset($linearray['sv_timeout'])) echo $linearray['sv_timeout']; else echo "65"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_alltalk">sv_alltalk</label>
                <div class="controls">
                    <select id="sv_alltalk" name="sv_alltalk">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['sv_alltalk']) and $linearray['sv_alltalk']=="0") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_voiceenable">sv_voiceenable</label>
                <div class="controls">
                    <select id="sv_voiceenable" name="sv_voiceenable">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['sv_voiceenable']) and $linearray['sv_voiceenable']=="9") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_allowdownload">sv_allowdownload</label>
                <div class="controls">
                    <select id="sv_allowdownload" name="sv_allowdownload">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['sv_allowdownload']) and $linearray['sv_allowdownload']=="0") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_allowupload">sv_allowupload</label>
                <div class="controls">
                    <select id="sv_allowupload" name="sv_allowupload">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_allowupload']) and $linearray['sv_allowupload']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_region">sv_region</label>
                <div class="controls">
                    <input id="sv_region" type="text" name="sv_region" value="<?php if(isset($linearray['sv_region'])) echo $linearray['sv_region']; else echo "225"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_friction">sv_friction</label>
                <div class="controls">
                    <input id="sv_friction" type="text" name="sv_friction" value="<?php if(isset($linearray['sv_friction'])) echo $linearray['sv_friction']; else echo "4"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_stopspeed">sv_stopspeed</label>
                <div class="controls">
                    <input id="sv_stopspeed" type="text" name="sv_stopspeed" value="<?php if(isset($linearray['sv_stopspeed'])) echo $linearray['sv_stopspeed']; else echo "50"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="">sv_gravity</label>
                <div class="controls">
                    <input id="sv_gravity" type="text" name="sv_gravity" value="<?php if(isset($linearray['sv_gravity'])) echo $linearray['sv_gravity']; else echo "800"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_accelerate">sv_accelerate</label>
                <div class="controls">
                    <input id="sv_accelerate" type="text" name="sv_accelerate" value="<?php if(isset($linearray['sv_accelerate'])) echo $linearray['sv_accelerate']; else echo "5"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_airaccelerate">sv_airaccelerate</label>
                <div class="controls">
                    <input id="sv_airaccelerate" type="text" name="sv_airaccelerate" value="<?php if(isset($linearray['sv_airaccelerate'])) echo $linearray['sv_airaccelerate']; else echo "10"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_wateraccelerate">sv_wateraccelerate</label>
                <div class="controls">
                    <input id="sv_wateraccelerate" type="text" name="sv_wateraccelerate" value="<?php if(isset($linearray['sv_wateraccelerate'])) echo $linearray['sv_wateraccelerate']; else echo "10"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_allow_color_correction">sv_allow_color_correction</label>
                <div class="controls">
                    <select id="sv_allow_color_correction" name="sv_allow_color_correction">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_allow_color_correction']) and $linearray['sv_allow_color_correction']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_allow_wait_command">sv_allow_wait_command</label>
                <div class="controls">
                    <select id="sv_allow_wait_command" name="sv_allow_wait_command">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_allow_wait_command']) and $linearray['sv_allow_wait_command']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_flashlight">mp_flashlight</label>
                <div class="controls">
                    <select id="mp_flashlight" name="mp_flashlight">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['mp_flashlight']) and $linearray['mp_flashlight']=="0") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_footsteps">mp_footsteps</label>
                <div class="controls">
                    <select id="mp_footsteps" name="mp_footsteps">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['mp_footsteps']) and $linearray['mp_footsteps']=="0") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_falldamage">mp_falldamage</label>
                <div class="controls">
                    <select id="mp_falldamage" name="mp_falldamage">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['mp_falldamage']) and $linearray['mp_falldamage']=="0") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limitteams">mp_limitteams</label>
                <div class="controls">
                    <select id="mp_limitteams" name="mp_limitteams">
                        <option value="1">1</option>
                        <option value="2" <?php if(isset($linearray['mp_limitteams']) and $linearray['mp_limitteams']=="2") echo "selected=\"selected\""; ?>>2</option>
                        <option value="3" <?php if(isset($linearray['mp_limitteams']) and $linearray['mp_limitteams']=="3") echo "selected=\"selected\""; ?>>3</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_friendlyfire">mp_friendlyfire</label>
                <div class="controls">
                    <select id="mp_friendlyfire" name="mp_friendlyfire">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['mp_friendlyfire']) and $linearray['mp_friendlyfire']=="0") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_autokick">mp_autokick</label>
                <div class="controls">
                    <select id="mp_autokick" name="mp_autokick">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['mp_autokick']) and $linearray['mp_autokick']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_forcecamera">mp_forcecamera</label>
                <div class="controls">
                    <select id="mp_forcecamera" name="mp_forcecamera">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['mp_forcecamera']) and $linearray['mp_forcecamera']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_fadetoblack">mp_fadetoblack</label>
                <div class="controls">
                    <select id="mp_fadetoblack" name="mp_fadetoblack">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['mp_fadetoblack']) and $linearray['mp_fadetoblack']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_allowspectators">mp_allowspectators</label>
                <div class="controls">
                    <select id="mp_allowspectators" name="mp_allowspectators">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['mp_allowspectators']) and $linearray['mp_allowspectators']=="0") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_chattime">mp_chattime</label>
                <div class="controls">
                    <input id="mp_chattime" type="text" name="mp_chattime" value="<?php if(isset($linearray['mp_chattime'])) echo $linearray['mp_chattime']; else echo "10"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="log">log</label>
                <div class="controls">
                    <select id="log" name="log">
                        <option value="off"><?php echo $sprache->off2;?></option>
                        <option value="on" <?php if(isset($linearray['log']) and $linearray['log']=="on") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_log_onefile">sv_log_onefile</label>
                <div class="controls">
                    <select id="sv_log_onefile" name="sv_log_onefile">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_log_onefile']) and $linearray['sv_log_onefile']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_logfile">sv_logfile</label>
                <div class="controls">
                    <select id="sv_logfile" name="sv_logfile">
                        <option value="1">1</option>
                        <option value="0" <?php if(isset($linearray['sv_logfile']) and $linearray['sv_logfile']=="0") echo "selected=\"selected\""; ?>>0</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_logbans">sv_logbans</label>
                <div class="controls">
                    <select id="sv_logbans" name="sv_logbans">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_logbans']) and $linearray['sv_logbans']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_logecho">sv_logecho</label>
                <div class="controls">
                    <select id="sv_logecho" name="sv_logecho">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_logecho']) and $linearray['sv_logecho']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_logdetail">mp_logdetail</label>
                <div class="controls"><select id="mp_logdetail" name="mp_logdetail">
                        <option value="0">0</option>
                        <option value="1" <?php if(isset($linearray['mp_logdetail']) and $linearray['mp_logdetail']=="1") echo "selected=\"selected\""; ?>>1</option>
                        <option value="2" <?php if(isset($linearray['mp_logdetail']) and $linearray['mp_logdetail']=="2") echo "selected=\"selected\""; ?>>2</option>
                        <option value="3" <?php if(isset($linearray['mp_logdetail']) and $linearray['mp_logdetail']=="3") echo "selected=\"selected\""; ?>>3</option>
                    </select>

                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_timelimit">mp_timelimit</label>
                <div class="controls">
                    <input id="mp_timelimit" type="text" name="mp_timelimit" value="<?php if(isset($linearray['mp_timelimit'])) echo $linearray['mp_timelimit']; else echo "20"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_winlimit">mp_winlimit</label>
                <div class="controls">
                    <input id="mp_winlimit" type="text" name="mp_winlimit" value="<?php if(isset($linearray['mp_winlimit'])) echo $linearray['mp_winlimit']; else echo "0"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_minrate">sv_minrate</label>
                <div class="controls">
                    <input id="sv_minrate" type="text" name="sv_minrate" value="<?php if(isset($linearray['sv_minrate'])) echo $linearray['sv_minrate']; else echo "20000"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_maxrate">sv_maxrate</label>
                <div class="controls">
                    <input id="sv_maxrate" type="text" name="sv_maxrate" value="<?php if(isset($linearray['sv_maxrate'])) echo $linearray['sv_maxrate']; else echo "100000"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_minupdaterate">sv_minupdaterate</label>
                <div class="controls">
                    <input id="sv_minupdaterate" type="text" name="sv_minupdaterate" value="<?php if(isset($linearray['sv_minupdaterate'])) echo $linearray['sv_minupdaterate']; else echo "40"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_maxupdaterate">sv_maxupdaterate</label>
                <div class="controls">
                    <input id="sv_maxupdaterate" type="text" name="sv_maxupdaterate" value="<?php if(isset($linearray['sv_maxupdaterate'])) echo $linearray['sv_maxupdaterate']; else echo "66"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_mincmdrate">sv_mincmdrate</label>
                <div class="controls">
                    <input id="sv_mincmdrate" type="text" name="sv_mincmdrate" value="<?php if(isset($linearray['sv_mincmdrate'])) echo $linearray['sv_mincmdrate']; else echo "40"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_maxcmdrate">sv_maxcmdrate</label>
                <div class="controls">
                    <input id="sv_maxcmdrate" type="text" name="sv_maxcmdrate" value="<?php if(isset($linearray['sv_maxcmdrate'])) echo $linearray['sv_maxcmdrate']; else echo "66"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_client_cmdrate_difference">sv_client_cmdrate_difference</label>
                <div class="controls">
                    <input id="sv_client_cmdrate_difference" type="text" name="sv_client_cmdrate_difference" value="<?php if(isset($linearray['sv_client_cmdrate_difference'])) echo $linearray['sv_client_cmdrate_difference']; else echo "30"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_client_min_interp_ratio">sv_client_min_interp_ratio</label>
                <div class="controls">
                    <select id="sv_client_min_interp_ratio" name="sv_client_min_interp_ratio">
                        <option value="1">1</option>
                        <option value="2" <?php if(isset($linearray['sv_client_min_interp_ratio']) and $linearray['sv_client_min_interp_ratio']=="2") echo "selected=\"selected\""; ?>>2</option>
                        <option value="0" <?php if(isset($linearray['sv_client_min_interp_ratio']) and $linearray['sv_client_min_interp_ratio']=="0") echo "selected=\"selected\""; ?>>0</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_client_max_interp_ratio">sv_client_max_interp_ratio</label>
                <div class="controls">
                    <select id="sv_client_max_interp_ratio" name="sv_client_max_interp_ratio">
                        <option value="1">1</option>
                        <option value="2" <?php if(isset($linearray['sv_client_max_interp_ratio']) and $linearray['sv_client_max_interp_ratio']=="2") echo "selected=\"selected\""; ?>>2</option>
                        <option value="0" <?php if(isset($linearray['sv_client_max_interp_ratio']) and $linearray['sv_client_max_interp_ratio']=="0") echo "selected=\"selected\""; ?>>0</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_fraglimit">mp_fraglimit</label>
                <div class="controls">
                    <input id="mp_fraglimit" type="text" name="mp_fraglimit" value="<?php if(isset($linearray['mp_fraglimit'])) echo $linearray['mp_fraglimit']; else echo "0"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_maxrounds">mp_maxrounds</label>
                <div class="controls">
                    <input id="mp_maxrounds" type="text" name="mp_maxrounds" value="<?php if(isset($linearray['mp_maxrounds'])) echo $linearray['mp_maxrounds']; else echo "0"?>">
                </div>
            </div>
            <?php } ?>
            <?php if ($shorten=="css" and $configname=="server.cfg"){ ?>
            <div class="control-group">
                <label class="control-label" for="motdfile_text">motdfile_text</label>
                <div class="controls">
                    <input id="motdfile_text" type="text" name="motdfile_text" value="<?php if(isset($linearray['motdfile_text'])) echo $linearray['motdfile_text']; else echo ""?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_disablefreezecam">sv_disablefreezecam</label>
                <div class="controls">
                    <select id="sv_disablefreezecam" name="sv_disablefreezecam">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['sv_disablefreezecam']) and $linearray['sv_disablefreezecam']=="0") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_nonemesis">sv_nonemesis</label>
                <div class="controls">
                    <select id="sv_nonemesis" name="sv_nonemesis">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_nonemesis']) and $linearray['sv_nonemesis']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_nomvp">sv_nomvp</label>
                <div class="controls">
                    <select id="sv_nomvp" name="sv_nomvp">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_nomvp']) and $linearray['sv_nomvp']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_nostats">sv_nostats</label>
                <div class="controls">
                    <select id="sv_nostats" name="sv_nostats">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_nostats']) and $linearray['sv_nostats']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_allowminmodels">sv_allowminmodels</label>
                <div class="controls">
                    <select id="sv_allowminmodels" name="sv_allowminmodels">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['sv_allowminmodels']) and $linearray['sv_allowminmodels']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_hudhint_sound">sv_hudhint_sound</label>
                <div class="controls">
                    <select id="sv_hudhint_sound" name="sv_hudhint_sound">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_hudhint_sound']) and $linearray['sv_hudhint_sound']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_competitive_minspec">sv_competitive_minspec</label>
                <div class="controls">
                    <select id="sv_competitive_minspec" name="sv_competitive_minspec">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_competitive_minspec']) and $linearray['sv_competitive_minspec']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_legacy_grenade_damage">sv_legacy_grenade_damage</label>
                <div class="controls">
                    <select id="sv_legacy_grenade_damage" name="sv_legacy_grenade_damage">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_legacy_grenade_damage']) and $linearray['sv_legacy_grenade_damage']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_enableboost">sv_enableboost</label>
                <div class="controls">
                    <select id="sv_enableboost" name="sv_enableboost">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_enableboost']) and $linearray['sv_enableboost']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="sv_enablebunnyhopping">sv_enablebunnyhopping</label>
                <div class="controls">
                    <select id="sv_enablebunnyhopping" name="sv_enablebunnyhopping">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['sv_enablebunnyhopping']) and $linearray['sv_enablebunnyhopping']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_forceautoteam">mp_forceautoteam</label>
                <div class="controls">
                    <select id="mp_forceautoteam" name="mp_forceautoteam">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['mp_forceautoteam']) and $linearray['mp_forceautoteam']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_enableroundwaittime">mp_enableroundwaittime</label>
                <div class="controls">
                    <select id="mp_enableroundwaittime" name="mp_enableroundwaittime">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['mp_enableroundwaittime']) and $linearray['mp_enableroundwaittime']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_startmoney">mp_startmoney</label>
                <div class="controls">
                    <input id="mp_startmoney" type="text" name="mp_startmoney" value="<?php if(isset($linearray['mp_startmoney'])) echo $linearray['mp_startmoney']; else echo "800"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_roundtime">mp_roundtime</label>
                <div class="controls">
                    <input id="mp_roundtime" type="text" name="mp_roundtime" value="<?php if(isset($linearray['mp_roundtime'])) echo $linearray['mp_roundtime']; else echo "5"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_buytime">mp_buytime</label>
                <div class="controls">
                    <input id="mp_buytime" type="text" name="mp_buytime" value="<?php if(isset($linearray['mp_buytime'])) echo $linearray['mp_buytime']; else echo "0.5"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_c4timer">mp_c4timer</label>
                <div class="controls">
                    <input id="mp_c4timer" type="text" name="mp_c4timer" value="<?php if(isset($linearray['mp_c4timer'])) echo $linearray['mp_c4timer']; else echo "45"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_freezetime">mp_freezetime</label>
                <div class="controls">
                    <input id="mp_freezetime" type="text" name="mp_freezetime" value="<?php if(isset($linearray['mp_freezetime'])) echo $linearray['mp_freezetime']; else echo "6"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_spawnprotectiontime">mp_spawnprotectiontime</label>
                <div class="controls">
                    <input id="mp_spawnprotectiontime" type="text" name="mp_spawnprotectiontime" value="<?php if(isset($linearray['mp_spawnprotectiontime'])) echo $linearray['mp_spawnprotectiontime']; else echo "0"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_hostagepenalty">mp_hostagepenalty</label>
                <div class="controls">
                    <input id="mp_hostagepenalty" type="text" name="mp_hostagepenalty" value="<?php if(isset($linearray['mp_hostagepenalty'])) echo $linearray['mp_hostagepenalty']; else echo "0"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_tkpunish">mp_tkpunish</label>
                <div class="controls">
                    <select id="mp_tkpunish" name="mp_tkpunish">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['mp_tkpunish']) and $linearray['mp_tkpunish']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <?php } else if ($shorten=="dods" and $configname=="server.cfg"){ ?>
            <div class="control-group">
                <label class="control-label" for="dod_bonusround">dod_bonusround</label>
                <div class="controls">
                    <select id="dod_bonusround" name="dod_bonusround">
                        <option value="0"><?php echo $sprache->off2;?></option>
                        <option value="1" <?php if(isset($linearray['dod_bonusround']) and $linearray['dod_bonusround']=="1") echo "selected=\"selected\""; ?>><?php echo $sprache->on;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="dod_bonusroundtime">dod_bonusroundtime</label>
                <div class="controls">
                    <input id="dod_bonusroundtime" type="text" name="dod_bonusroundtime" value="<?php if(isset($linearray['dod_bonusroundtime'])) echo $linearray['dod_bonusroundtime']; else echo "10"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="dod_enableroundwaittime">dod_enableroundwaittime</label>
                <div class="controls">
                    <select id="dod_enableroundwaittime" name="dod_enableroundwaittime">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['dod_enableroundwaittime']) and $linearray['dod_enableroundwaittime']=="0") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="dod_freezecam"></label>
                <div class="controls">
                    <select id="dod_freezecam" name="dod_freezecam">
                        <option value="1"><?php echo $sprache->on;?></option>
                        <option value="0" <?php if(isset($linearray['dod_freezecam']) and $linearray['dod_freezecam']=="0") echo "selected=\"selected\""; ?>><?php echo $sprache->off2;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_allies_rifleman">mp_limit_allies_rifleman</label>
                <div class="controls">
                    <input id="mp_limit_allies_rifleman" type="text" name="mp_limit_allies_rifleman" value="<?php if(isset($linearray['mp_limit_allies_rifleman'])) echo $linearray['mp_limit_allies_rifleman']; else echo "3"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_allies_support">mp_limit_allies_support</label>
                <div class="controls">
                    <input id="mp_limit_allies_support" type="text" name="mp_limit_allies_support" value="<?php if(isset($linearray['mp_limit_allies_support'])) echo $linearray['mp_limit_allies_support']; else echo "-1"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_allies_assault">mp_limit_allies_assault</label>
                <div class="controls">
                    <input id="mp_limit_allies_assault" type="text" name="mp_limit_allies_assault" value="<?php if(isset($linearray['mp_limit_allies_assault'])) echo $linearray['mp_limit_allies_assault']; else echo "-1"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_allies_sniper">mp_limit_allies_sniper</label>
                <div class="controls">
                    <input id="mp_limit_allies_sniper" type="text" name="mp_limit_allies_sniper" value="<?php if(isset($linearray['mp_limit_allies_sniper'])) echo $linearray['mp_limit_allies_sniper']; else echo "1"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_allies_mg">mp_limit_allies_mg</label>
                <div class="controls">
                    <input id="" type="text" name="mp_limit_allies_mg" value="<?php if(isset($linearray['mp_limit_allies_mg'])) echo $linearray['mp_limit_allies_mg']; else echo "1"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_allies_rocket">mp_limit_allies_rocket</label>
                <div class="controls">
                    <input id="mp_limit_allies_rocket" type="text" name="mp_limit_allies_rocket" value="<?php if(isset($linearray['mp_limit_allies_rocket'])) echo $linearray['mp_limit_allies_rocket']; else echo "2"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_axis_rifleman">mp_limit_axis_rifleman</label>
                <div class="controls">
                    <input id="mp_limit_axis_rifleman" type="text" name="mp_limit_axis_rifleman" value="<?php if(isset($linearray['mp_limit_axis_rifleman'])) echo $linearray['mp_limit_axis_rifleman']; else echo "3"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_axis_support">mp_limit_axis_support</label>
                <div class="controls">
                    <input id="mp_limit_axis_support" type="text" name="mp_limit_axis_support" value="<?php if(isset($linearray['mp_limit_axis_support'])) echo $linearray['mp_limit_axis_support']; else echo "-1"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_axis_assault">mp_limit_axis_assault</label>
                <div class="controls">
                    <input id="mp_limit_axis_assault" type="text" name="mp_limit_axis_assault" value="<?php if(isset($linearray['mp_limit_axis_assault'])) echo $linearray['mp_limit_axis_assault']; else echo "-1"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_axis_sniper">mp_limit_axis_sniper</label>
                <div class="controls">
                    <input id="mp_limit_axis_sniper" type="text" name="mp_limit_axis_sniper" value="<?php if(isset($linearray['mp_limit_axis_sniper'])) echo $linearray['mp_limit_axis_sniper']; else echo "1"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_axis_mg">mp_limit_axis_mg</label>
                <div class="controls">
                    <input id="mp_limit_axis_mg" type="text" name="mp_limit_axis_mg" value="<?php if(isset($linearray['mp_limit_axis_mg'])) echo $linearray['mp_limit_axis_mg']; else echo "1"?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mp_limit_axis_rocket">mp_limit_axis_rocket</label>
                <div class="controls">
                    <input id="mp_limit_axis_rocket" type="text" name="mp_limit_axis_rocket" value="<?php if(isset($linearray['mp_limit_axis_rocket'])) echo $linearray['mp_limit_axis_rocket']; else echo "2"?>">
                </div>
            </div>
            <?php } ?>
            <?php foreach ($array_keys as $key) { ?>
            <div class="control-group">
                <label class="control-label" for="<?php echo $key; ?>"><?php echo $key; ?></label>
                <div class="controls">
                    <input id="<?php echo $key; ?>" type="text" name="<?php echo $key; ?>" value="<?php echo $unknownarray[$key]; ?>">
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="config" value="<?php echo $postconfig;?>">
                    <input type="hidden" name="update" value="1">
                    <?php if(isset($oldrcon)){ ?><input type="hidden" name="oldrcon" value="<?php echo $oldrcon;?>" /><?php } ?>
                </div>
            </div>
        </form>
    </div>
</div>