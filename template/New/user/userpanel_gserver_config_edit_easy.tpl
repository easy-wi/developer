<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li><i class="fa fa-cogs"></i> <?php echo $sprache->config;?></li>
        <li><?php echo $serverip.':'.$port;?></li>
        <li class="active"><?php echo htmlentities($configname);?></li>
    </ol>
</section>


<section class="content">

	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_config;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="box box-primary">
                <form role="form" action="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $id;?>&amp;type=easy&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input class="form-control" type="hidden" name="config" value="<?php echo $postconfig;?>">
                    <input class="form-control" type="hidden" name="update" value="1">
                    <?php if(isset($oldrcon)){ ?>
                    <input class="form-control" type="hidden" name="oldrcon" value="<?php echo $oldrcon;?>" />
                    <?php } ?>

                    <div class="box-header">
                        <h3 class="box-title"><?php echo htmlentities($configname);?></h3>
                    </div>

                    <div class="box-body">

                    <?php if($gamebinary=="srcds_run" and $configname=="server.cfg"){ ?>

                    <div class="form-group">
                        <label for="hostname">hostname</label>
                        <input class="form-control" id="hostname" type="text" name="hostname" value="<?php echo (isset($linearray['hostname'])) ? $linearray['hostname'] : 'Hostname';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_password">sv_password</label>
                        <input class="form-control" id="sv_password" type="text" name="sv_password" value="<?php echo (isset($linearray['sv_password'])) ? $linearray['sv_password'] : '';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_contact">sv_contact</label>
                        <input class="form-control" id="sv_contact" type="text" name="sv_contact" value="<?php echo (isset($linearray['sv_contact'])) ? $linearray['sv_contact'] : 'email@mail.tld';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_tags">sv_tags</label>
                        <input class="form-control" id="sv_tags" type="text" name="sv_tags" value="<?php echo (isset($linearray['sv_tags'])) ? $linearray['sv_tags'] : 'Homepage, Clanname';?>">
                    </div>

                    <div class="form-group">
                        <label for="motdfile">motdfile</label>
                        <input class="form-control" id="motdfile" type="text" name="motdfile" value="<?php echo (isset($linearray['motdfile'])) ? $linearray['motdfile'] : 'motd.txt';?>">
                    </div>

                    <div class="form-group">
                        <label for="mapcyclefile">mapcyclefile</label>
                        <input class="form-control" id="mapcyclefile" type="text" name="mapcyclefile" value="<?php echo (isset($linearray['mapcyclefile'])) ? $linearray['mapcyclefile'] : 'mapcycle.txt';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_downloadurl">sv_downloadurl</label>
                        <input class="form-control" id="sv_downloadurl" type="text" name="sv_downloadurl" value="<?php echo (isset($linearray['sv_downloadurl'])) ? $linearray['sv_downloadurl'] : 'http://www.domain.tld/fastdownload';?>">
                    </div>

                    <div class="form-group">
                        <label for="net_maxfilesize">net_maxfilesize</label>
                        <input class="form-control" id="net_maxfilesize" type="text" name="net_maxfilesize" value="<?php echo (isset($linearray['net_maxfilesize'])) ? $linearray['net_maxfilesize'] : '64';?>">
                    </div>

                    <div class="form-group">
                        <label for="rcon_password">rcon_password</label>
                        <input class="form-control" id="rcon_password" type="text" name="rcon_password" value="<?php echo (isset($linearray['rcon_password'])) ? $linearray['rcon_password'] : 'RconPassword';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_rcon_minfailures">sv_rcon_minfailures</label>
                        <input class="form-control" id="sv_rcon_minfailures" type="text" name="sv_rcon_minfailures" value="<?php echo (isset($linearray['sv_rcon_minfailures'])) ? $linearray['sv_rcon_minfailures'] : 3;?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_rcon_maxfailures">sv_rcon_maxfailures</label>
                        <input class="form-control" id="sv_rcon_maxfailures" type="text" name="sv_rcon_maxfailures" value="<?php echo (isset($linearray['sv_rcon_maxfailures'])) ? $linearray['sv_rcon_maxfailures'] : 5;?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_rcon_banpenalty">sv_rcon_banpenalty</label>
                        <input class="form-control" id="sv_rcon_banpenalty" type="text" name="sv_rcon_banpenalty" value="<?php echo (isset($linearray['sv_rcon_banpenalty'])) ? $linearray['sv_rcon_banpenalty'] : 0;?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_rcon_minfailuretime">sv_rcon_minfailuretime</label>
                        <input class="form-control" id="sv_rcon_minfailuretime" type="text" name="sv_rcon_minfailuretime" value="<?php echo (isset($linearray['sv_rcon_minfailuretime'])) ? $linearray['sv_rcon_minfailuretime'] : '15';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_pure">sv_pure</label>
                        <select class="form-control"id="sv_pure" name="sv_pure">
                            <option value="0">0</option>
                            <option value="1" <?php if(isset($linearray['sv_pure']) and $linearray['sv_pure']==1) echo 'selected="selected"';?>>1</option>
                            <option value="2" <?php if(isset($linearray['sv_pure']) and $linearray['sv_pure']=="2") echo 'selected="selected"';?>>2</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_pure_kick_clients">sv_pure_kick_clients</label>
                        <select class="form-control"id="sv_pure_kick_clients" name="sv_pure_kick_clients">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_pure_kick_clients']) and $linearray['sv_pure_kick_clients']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_timeout">sv_timeout</label>
                        <input class="form-control" id="sv_timeout" type="text" name="sv_timeout" value="<?php echo (isset($linearray['sv_timeout'])) ? $linearray['sv_timeout'] : '65';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_alltalk">sv_alltalk</label>
                        <select class="form-control"id="sv_alltalk" name="sv_alltalk">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['sv_alltalk']) and $linearray['sv_alltalk']=="0") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_voiceenable">sv_voiceenable</label>
                        <select class="form-control"id="sv_voiceenable" name="sv_voiceenable">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['sv_voiceenable']) and $linearray['sv_voiceenable']=="9") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_allowdownload">sv_allowdownload</label>
                        <select class="form-control"id="sv_allowdownload" name="sv_allowdownload">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['sv_allowdownload']) and $linearray['sv_allowdownload']=="0") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_allowupload">sv_allowupload</label>
                        <select class="form-control"id="sv_allowupload" name="sv_allowupload">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_allowupload']) and $linearray['sv_allowupload']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_region">sv_region</label>
                        <input class="form-control" id="sv_region" type="text" name="sv_region" value="<?php echo (isset($linearray['sv_region'])) ? $linearray['sv_region'] : '225';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_friction">sv_friction</label>
                        <input class="form-control" id="sv_friction" type="text" name="sv_friction" value="<?php echo (isset($linearray['sv_friction'])) ? $linearray['sv_friction'] : 4;?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_stopspeed">sv_stopspeed</label>
                        <input class="form-control" id="sv_stopspeed" type="text" name="sv_stopspeed" value="<?php echo (isset($linearray['sv_stopspeed'])) ? $linearray['sv_stopspeed'] : '50';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_gravity">sv_gravity</label>
                        <input class="form-control" id="sv_gravity" type="text" name="sv_gravity" value="<?php echo (isset($linearray['sv_gravity'])) ? $linearray['sv_gravity'] : '800';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_accelerate">sv_accelerate</label>
                        <input class="form-control" id="sv_accelerate" type="text" name="sv_accelerate" value="<?php echo (isset($linearray['sv_accelerate'])) ? $linearray['sv_accelerate'] : 5;?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_airaccelerate">sv_airaccelerate</label>
                        <input class="form-control" id="sv_airaccelerate" type="text" name="sv_airaccelerate" value="<?php echo (isset($linearray['sv_airaccelerate'])) ? $linearray['sv_airaccelerate'] : '10';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_wateraccelerate">sv_wateraccelerate</label>
                        <input class="form-control" id="sv_wateraccelerate" type="text" name="sv_wateraccelerate" value="<?php echo (isset($linearray['sv_wateraccelerate'])) ? $linearray['sv_wateraccelerate'] : '10';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_allow_color_correction">sv_allow_color_correction</label>
                        <select class="form-control"id="sv_allow_color_correction" name="sv_allow_color_correction">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_allow_color_correction']) and $linearray['sv_allow_color_correction']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_allow_wait_command">sv_allow_wait_command</label>
                        <select class="form-control"id="sv_allow_wait_command" name="sv_allow_wait_command">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_allow_wait_command']) and $linearray['sv_allow_wait_command']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_flashlight">mp_flashlight</label>
                        <select class="form-control"id="mp_flashlight" name="mp_flashlight">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['mp_flashlight']) and $linearray['mp_flashlight']=="0") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_footsteps">mp_footsteps</label>
                        <select class="form-control"id="mp_footsteps" name="mp_footsteps">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['mp_footsteps']) and $linearray['mp_footsteps']=="0") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_falldamage">mp_falldamage</label>
                        <select class="form-control"id="mp_falldamage" name="mp_falldamage">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['mp_falldamage']) and $linearray['mp_falldamage']=="0") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_limitteams">mp_limitteams</label>
                        <select class="form-control"id="mp_limitteams" name="mp_limitteams">
                            <option value="1">1</option>
                            <option value="2" <?php if(isset($linearray['mp_limitteams']) and $linearray['mp_limitteams']=="2") echo 'selected="selected"';?>>2</option>
                            <option value="3" <?php if(isset($linearray['mp_limitteams']) and $linearray['mp_limitteams']=="3") echo 'selected="selected"';?>>3</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_friendlyfire">mp_friendlyfire</label>
                        <select class="form-control"id="mp_friendlyfire" name="mp_friendlyfire">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['mp_friendlyfire']) and $linearray['mp_friendlyfire']=="0") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_autokick">mp_autokick</label>
                        <select class="form-control"id="mp_autokick" name="mp_autokick">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['mp_autokick']) and $linearray['mp_autokick']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_forcecamera">mp_forcecamera</label>
                        <select class="form-control"id="mp_forcecamera" name="mp_forcecamera">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['mp_forcecamera']) and $linearray['mp_forcecamera']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_fadetoblack">mp_fadetoblack</label>
                        <select class="form-control"id="mp_fadetoblack" name="mp_fadetoblack">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['mp_fadetoblack']) and $linearray['mp_fadetoblack']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_allowspectators">mp_allowspectators</label>
                        <select class="form-control"id="mp_allowspectators" name="mp_allowspectators">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['mp_allowspectators']) and $linearray['mp_allowspectators']=="0") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_chattime">mp_chattime</label>
                        <input class="form-control" id="mp_chattime" type="text" name="mp_chattime" value="<?php echo (isset($linearray['mp_chattime'])) ? $linearray['mp_chattime'] : '10';?>">
                    </div>

                    <div class="form-group">
                        <label for="log">log</label>
                        <select class="form-control"id="log" name="log">
                            <option value="off"><?php echo $sprache->off2;?></option>
                            <option value="on" <?php if(isset($linearray['log']) and $linearray['log']=="on") echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_log_onefile">sv_log_onefile</label>
                        <select class="form-control"id="sv_log_onefile" name="sv_log_onefile">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_log_onefile']) and $linearray['sv_log_onefile']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_logfile">sv_logfile</label>
                        <select class="form-control"id="sv_logfile" name="sv_logfile">
                            <option value="1">1</option>
                            <option value="0" <?php if(isset($linearray['sv_logfile']) and $linearray['sv_logfile']=="0") echo 'selected="selected"';?>>0</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_logbans">sv_logbans</label>
                        <select class="form-control"id="sv_logbans" name="sv_logbans">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_logbans']) and $linearray['sv_logbans']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_logecho">sv_logecho</label>
                        <select class="form-control"id="sv_logecho" name="sv_logecho">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_logecho']) and $linearray['sv_logecho']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_logdetail">mp_logdetail</label>
                        <select class="form-control"id="mp_logdetail" name="mp_logdetail">
                            <option value="0">0</option>
                            <option value="1" <?php if(isset($linearray['mp_logdetail']) and $linearray['mp_logdetail']==1) echo 'selected="selected"';?>>1</option>
                            <option value="2" <?php if(isset($linearray['mp_logdetail']) and $linearray['mp_logdetail']=="2") echo 'selected="selected"';?>>2</option>
                            <option value="3" <?php if(isset($linearray['mp_logdetail']) and $linearray['mp_logdetail']=="3") echo 'selected="selected"';?>>3</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_timelimit">mp_timelimit</label>
                        <input class="form-control" id="mp_timelimit" type="text" name="mp_timelimit" value="<?php echo (isset($linearray['mp_timelimit'])) ? $linearray['mp_timelimit'] : '20';?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_winlimit">mp_winlimit</label>
                        <input class="form-control" id="mp_winlimit" type="text" name="mp_winlimit" value="<?php echo (isset($linearray['mp_winlimit'])) ? $linearray['mp_winlimit'] : 0;?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_minrate">sv_minrate</label>
                        <input class="form-control" id="sv_minrate" type="text" name="sv_minrate" value="<?php echo (isset($linearray['sv_minrate'])) ? $linearray['sv_minrate'] : '20000';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_maxrate">sv_maxrate</label>
                        <input class="form-control" id="sv_maxrate" type="text" name="sv_maxrate" value="<?php echo (isset($linearray['sv_maxrate'])) ? $linearray['sv_maxrate'] : '100000';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_minupdaterate">sv_minupdaterate</label>
                        <input class="form-control" id="sv_minupdaterate" type="text" name="sv_minupdaterate" value="<?php echo (isset($linearray['sv_minupdaterate'])) ? $linearray['sv_minupdaterate'] : '40';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_maxupdaterate">sv_maxupdaterate</label>
                        <input class="form-control" id="sv_maxupdaterate" type="text" name="sv_maxupdaterate" value="<?php echo (isset($linearray['sv_maxupdaterate'])) ? $linearray['sv_maxupdaterate'] : '66';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_mincmdrate">sv_mincmdrate</label>
                        <input class="form-control" id="sv_mincmdrate" type="text" name="sv_mincmdrate" value="<?php echo (isset($linearray['sv_mincmdrate'])) ? $linearray['sv_mincmdrate'] : '40';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_maxcmdrate">sv_maxcmdrate</label>
                        <input class="form-control" id="sv_maxcmdrate" type="text" name="sv_maxcmdrate" value="<?php echo (isset($linearray['sv_maxcmdrate'])) ? $linearray['sv_maxcmdrate'] : '66';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_client_cmdrate_difference">sv_client_cmdrate_difference</label>
                        <input class="form-control" id="sv_client_cmdrate_difference" type="text" name="sv_client_cmdrate_difference" value="<?php echo (isset($linearray['sv_client_cmdrate_difference'])) ? $linearray['sv_client_cmdrate_difference'] : '30';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_client_min_interp_ratio">sv_client_min_interp_ratio</label>
                        <select class="form-control"id="sv_client_min_interp_ratio" name="sv_client_min_interp_ratio">
                            <option value="1">1</option>
                            <option value="2" <?php if(isset($linearray['sv_client_min_interp_ratio']) and $linearray['sv_client_min_interp_ratio']=="2") echo 'selected="selected"';?>>2</option>
                            <option value="0" <?php if(isset($linearray['sv_client_min_interp_ratio']) and $linearray['sv_client_min_interp_ratio']=="0") echo 'selected="selected"';?>>0</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_client_max_interp_ratio">sv_client_max_interp_ratio</label>
                        <select class="form-control"id="sv_client_max_interp_ratio" name="sv_client_max_interp_ratio">
                            <option value="1">1</option>
                            <option value="2" <?php if(isset($linearray['sv_client_max_interp_ratio']) and $linearray['sv_client_max_interp_ratio']=="2") echo 'selected="selected"';?>>2</option>
                            <option value="0" <?php if(isset($linearray['sv_client_max_interp_ratio']) and $linearray['sv_client_max_interp_ratio']=="0") echo 'selected="selected"';?>>0</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_fraglimit">mp_fraglimit</label>
                        <input class="form-control" id="mp_fraglimit" type="text" name="mp_fraglimit" value="<?php echo (isset($linearray['mp_fraglimit'])) ? $linearray['mp_fraglimit'] : 0;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_maxrounds">mp_maxrounds</label>
                        <input class="form-control" id="mp_maxrounds" type="text" name="mp_maxrounds" value="<?php echo (isset($linearray['mp_maxrounds'])) ? $linearray['mp_maxrounds'] : 0;?>">
                    </div>
                    <?php } ?>

                    <?php if ($shorten=="css" and $configname=="server.cfg"){ ?>
                    <div class="form-group">
                        <label for="motdfile_text">motdfile_text</label>
                        <input class="form-control" id="motdfile_text" type="text" name="motdfile_text" value="<?php echo (isset($linearray['motdfile_text'])) ? $linearray['motdfile_text'] : '';?>">
                    </div>

                    <div class="form-group">
                        <label for="sv_disablefreezecam">sv_disablefreezecam</label>
                        <select class="form-control"id="sv_disablefreezecam" name="sv_disablefreezecam">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['sv_disablefreezecam']) and $linearray['sv_disablefreezecam']=="0") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_nonemesis">sv_nonemesis</label>
                        <select class="form-control"id="sv_nonemesis" name="sv_nonemesis">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_nonemesis']) and $linearray['sv_nonemesis']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_nomvp">sv_nomvp</label>
                        <select class="form-control"id="sv_nomvp" name="sv_nomvp">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_nomvp']) and $linearray['sv_nomvp']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_nostats">sv_nostats</label>
                        <select class="form-control"id="sv_nostats" name="sv_nostats">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_nostats']) and $linearray['sv_nostats']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_allowminmodels">sv_allowminmodels</label>
                        <select class="form-control"id="sv_allowminmodels" name="sv_allowminmodels">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['sv_allowminmodels']) and $linearray['sv_allowminmodels']==1) echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_hudhint_sound">sv_hudhint_sound</label>
                        <select class="form-control"id="sv_hudhint_sound" name="sv_hudhint_sound">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_hudhint_sound']) and $linearray['sv_hudhint_sound']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_competitive_minspec">sv_competitive_minspec</label>
                        <select class="form-control"id="sv_competitive_minspec" name="sv_competitive_minspec">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_competitive_minspec']) and $linearray['sv_competitive_minspec']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_legacy_grenade_damage">sv_legacy_grenade_damage</label>
                        <select class="form-control"id="sv_legacy_grenade_damage" name="sv_legacy_grenade_damage">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_legacy_grenade_damage']) and $linearray['sv_legacy_grenade_damage']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_enableboost">sv_enableboost</label>
                        <select class="form-control"id="sv_enableboost" name="sv_enableboost">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_enableboost']) and $linearray['sv_enableboost']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sv_enablebunnyhopping">sv_enablebunnyhopping</label>
                        <select class="form-control"id="sv_enablebunnyhopping" name="sv_enablebunnyhopping">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['sv_enablebunnyhopping']) and $linearray['sv_enablebunnyhopping']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_forceautoteam">mp_forceautoteam</label>
                        <select class="form-control"id="mp_forceautoteam" name="mp_forceautoteam">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['mp_forceautoteam']) and $linearray['mp_forceautoteam']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_enableroundwaittime">mp_enableroundwaittime</label>
                        <select class="form-control"id="mp_enableroundwaittime" name="mp_enableroundwaittime">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['mp_enableroundwaittime']) and $linearray['mp_enableroundwaittime']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_startmoney">mp_startmoney</label>
                        <input class="form-control" id="mp_startmoney" type="text" name="mp_startmoney" value="<?php echo (isset($linearray['mp_startmoney'])) ? $linearray['mp_startmoney'] : 800;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_roundtime">mp_roundtime</label>
                        <input class="form-control" id="mp_roundtime" type="text" name="mp_roundtime" value="<?php echo (isset($linearray['mp_roundtime'])) ? $linearray['mp_roundtime'] : 5;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_buytime">mp_buytime</label>
                        <input class="form-control" id="mp_buytime" type="text" name="mp_buytime" value="<?php echo (isset($linearray['mp_buytime'])) ? $linearray['mp_buytime'] : '0.5';?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_c4timer">mp_c4timer</label>
                        <input class="form-control" id="mp_c4timer" type="text" name="mp_c4timer" value="<?php echo (isset($linearray['mp_c4timer'])) ? $linearray['mp_c4timer'] : 45;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_freezetime">mp_freezetime</label>
                        <input class="form-control" id="mp_freezetime" type="text" name="mp_freezetime" value="<?php echo (isset($linearray['mp_freezetime'])) ? $linearray['mp_freezetime'] : 6;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_spawnprotectiontime">mp_spawnprotectiontime</label>
                        <input class="form-control" id="mp_spawnprotectiontime" type="text" name="mp_spawnprotectiontime" value="<?php echo (isset($linearray['mp_spawnprotectiontime'])) ? $linearray['mp_spawnprotectiontime'] : 0;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_hostagepenalty">mp_hostagepenalty</label>
                        <input class="form-control" id="mp_hostagepenalty" type="text" name="mp_hostagepenalty" value="<?php echo (isset($linearray['mp_hostagepenalty'])) ? $linearray['mp_hostagepenalty'] : 0;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_tkpunish">mp_tkpunish</label>
                        <select class="form-control"id="mp_tkpunish" name="mp_tkpunish">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['mp_tkpunish']) and $linearray['mp_tkpunish']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <?php } else if ($shorten=="dods" and $configname=="server.cfg"){ ?>

                    <div class="form-group">
                        <label for="dod_bonusround">dod_bonusround</label>
                        <select class="form-control"id="dod_bonusround" name="dod_bonusround">
                            <option value="0"><?php echo $sprache->off2;?></option>
                            <option value="1" <?php if(isset($linearray['dod_bonusround']) and $linearray['dod_bonusround']==1) echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dod_bonusroundtime">dod_bonusroundtime</label>
                        <input class="form-control" id="dod_bonusroundtime" type="text" name="dod_bonusroundtime" value="<?php echo (isset($linearray['dod_bonusroundtime'])) ? $linearray['dod_bonusroundtime'] : '10';?>">
                    </div>

                    <div class="form-group">
                        <label for="dod_enableroundwaittime">dod_enableroundwaittime</label>
                        <select class="form-control"id="dod_enableroundwaittime" name="dod_enableroundwaittime">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['dod_enableroundwaittime']) and $linearray['dod_enableroundwaittime']=="0") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dod_freezecam"></label>
                        <select class="form-control"id="dod_freezecam" name="dod_freezecam">
                            <option value="1"><?php echo $sprache->on;?></option>
                            <option value="0" <?php if(isset($linearray['dod_freezecam']) and $linearray['dod_freezecam']=="0") echo 'selected="selected"';?>><?php echo $sprache->off2;?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_allies_rifleman">mp_limit_allies_rifleman</label>
                        <input class="form-control" id="mp_limit_allies_rifleman" type="text" name="mp_limit_allies_rifleman" value="<?php echo (isset($linearray['mp_limit_allies_rifleman'])) ? $linearray['mp_limit_allies_rifleman'] : 3;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_allies_support">mp_limit_allies_support</label>
                        <input class="form-control" id="mp_limit_allies_support" type="text" name="mp_limit_allies_support" value="<?php echo (isset($linearray['mp_limit_allies_support'])) ? $linearray['mp_limit_allies_support'] : -1;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_allies_assault">mp_limit_allies_assault</label>
                        <input class="form-control" id="mp_limit_allies_assault" type="text" name="mp_limit_allies_assault" value="<?php echo (isset($linearray['mp_limit_allies_assault'])) ? $linearray['mp_limit_allies_assault'] : -1;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_allies_sniper">mp_limit_allies_sniper</label>
                        <input class="form-control" id="mp_limit_allies_sniper" type="text" name="mp_limit_allies_sniper" value="<?php echo (isset($linearray['mp_limit_allies_sniper'])) ? $linearray['mp_limit_allies_sniper'] : 1;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_allies_mg">mp_limit_allies_mg</label>
                        <input class="form-control" id="mp_limit_allies_mg" type="text" name="mp_limit_allies_mg" value="<?php echo (isset($linearray['mp_limit_allies_mg'])) ? $linearray['mp_limit_allies_mg'] : 1;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_allies_rocket">mp_limit_allies_rocket</label>
                        <input class="form-control" id="mp_limit_allies_rocket" type="text" name="mp_limit_allies_rocket" value="<?php echo (isset($linearray['mp_limit_allies_rocket'])) ? $linearray['mp_limit_allies_rocket'] : 2;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_axis_rifleman">mp_limit_axis_rifleman</label>
                        <input class="form-control" id="mp_limit_axis_rifleman" type="text" name="mp_limit_axis_rifleman" value="<?php echo (isset($linearray['mp_limit_axis_rifleman'])) ? $linearray['mp_limit_axis_rifleman'] : 3;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_axis_support">mp_limit_axis_support</label>
                        <input class="form-control" id="mp_limit_axis_support" type="text" name="mp_limit_axis_support" value="<?php echo (isset($linearray['mp_limit_axis_support'])) ? $linearray['mp_limit_axis_support'] : -1;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_axis_assault">mp_limit_axis_assault</label>
                        <input class="form-control" id="mp_limit_axis_assault" type="text" name="mp_limit_axis_assault" value="<?php echo (isset($linearray['mp_limit_axis_assault'])) ? $linearray['mp_limit_axis_assault'] : -1;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_axis_sniper">mp_limit_axis_sniper</label>
                        <input class="form-control" id="mp_limit_axis_sniper" type="text" name="mp_limit_axis_sniper" value="<?php echo (isset($linearray['mp_limit_axis_sniper'])) ? $linearray['mp_limit_axis_sniper'] : 1;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_axis_mg">mp_limit_axis_mg</label>
                        <input class="form-control" id="mp_limit_axis_mg" type="text" name="mp_limit_axis_mg" value="<?php echo (isset($linearray['mp_limit_axis_mg'])) ? $linearray['mp_limit_axis_mg'] : 1;?>">
                    </div>

                    <div class="form-group">
                        <label for="mp_limit_axis_rocket">mp_limit_axis_rocket</label>
                        <input class="form-control" id="mp_limit_axis_rocket" type="text" name="mp_limit_axis_rocket" value="<?php echo (isset($linearray['mp_limit_axis_rocket'])) ? $linearray['mp_limit_axis_rocket'] : 2;?>">
                    </div>
                    <?php } ?>

                    <?php foreach ($array_keys as $key) { ?>
                    <div class="form-group">
                        <label for="<?php echo $key;?>"><?php echo $key;?></label>
                        <input class="form-control" id="<?php echo $key;?>" type="text" name="<?php echo $key;?>" value="<?php echo $unknownarray[$key];?>">
                    </div>
                    <?php } ?>
                    <div class="col-md-2 float-right box-footer">
                        <button class="btn btn-lg btn-warning" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<div style="height: 50px"></div>




