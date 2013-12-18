<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip.':'.$port;?> <?php if ($usedns=='Y') echo $dns;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_voiceserver_md;?></div>
</div>
<hr>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=vo&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vo" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <?php if ($usedns=='Y'){ ?>
            <div class="control-group">
                <label class="control-label" for="defaultdns"><?php echo $sprache->defaultdns;?></label>
                <div class="controls">
                    <input id="defaultdns" type="text" name="defaultdns" value="<?php echo $defaultdns; ?>" disabled>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="dns"><?php echo $sprache->dns;?></label>
                <div class="controls">
                    <input id="dns" type="text" name="dns" value="<?php echo $dns; ?>">
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="initialpassword"><?php echo $sprache->initialpassword;?></label>
                <div class="controls">
                    <input id="initialpassword" type="text" name="initialpassword" value="<?php echo $initialpassword; ?>" <?php if ($password=='Y') echo 'required';?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="virtualserver_reserved_slots"><?php echo $sprache->virtualserver_reserved_slots;?></label>
                <div class="controls">
                    <input id="virtualserver_reserved_slots" type="text" name="virtualserver_reserved_slots" value="<?php echo $virtualserver_reserved_slots;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="name"><?php echo $sprache->name;?></label>
                <div class="controls">
                    <input id="name" type="text" name="name" value="<?php echo $name; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="welcome"><?php echo $sprache->welcome;?></label>
                <div class="controls">
                    <input id="welcome" type="text" name="welcome" value="<?php echo $welcome; ?>"<?php if ($forcewelcome=='Y') echo 'disabled';?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="hostbanner_url"><?php echo $sprache->hostbanner_url;?></label>
                <div class="controls">
                    <input id="hostbanner_url" type="text" name="hostbanner_url" value="<?php echo $hostbanner_url; ?>" <?php if ($forcebanner=='Y') echo 'disabled';?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="hostbanner_gfx_url"><?php echo $sprache->hostbanner_gfx_url;?></label>
                <div class="controls">
                    <input id="hostbanner_gfx_url" type="text" name="hostbanner_gfx_url" value="<?php echo $hostbanner_gfx_url; ?>"<?php if ($forcebanner=='Y') echo 'disabled';?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="virtualserver_hostbanner_gfx_interval"><?php echo $sprache->virtualserver_hostbanner_gfx_interval;?></label>
                <div class="controls">
                    <input id="virtualserver_hostbanner_gfx_interval" type="text" name="virtualserver_hostbanner_gfx_interval" value="<?php echo $virtualserver_hostbanner_gfx_interval;?>" <?php if ($forcebanner=='Y') echo 'disabled';?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="hostbutton_url"><?php echo $sprache->hostbutton_url;?></label>
                <div class="controls">
                    <input id="hostbutton_url" type="text" name="hostbutton_url" value="<?php echo $hostbutton_url; ?>" <?php if ($forcebutton=='Y') echo 'disabled';?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="hostbutton_gfx_url"><?php echo $sprache->hostbutton_gfx_url;?></label>
                <div class="controls">
                    <input id="hostbutton_gfx_url" type="text" name="hostbutton_gfx_url" value="<?php echo $hostbutton_gfx_url; ?>" <?php if ($forcebutton=='Y') echo 'disabled';?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="hostbutton_tooltip"><?php echo $sprache->hostbutton_tooltip;?></label>
                <div class="controls">
                    <input id="hostbutton_tooltip" type="text" name="hostbutton_tooltip" value="<?php echo $hostbutton_tooltip; ?>" <?php if ($forcebutton=='Y') echo 'disabled';?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="virtualserver_hostmessage_mode"><?php echo $sprache->virtualserver_hostmessage_mode;?></label>
                <div class="controls">
                    <select id="virtualserver_hostmessage_mode" name="virtualserver_hostmessage_mode">
                        <option value="0"><?php echo $sprache->virtualserver_hostmessage_mode_0;?></option>
                        <option value="1" <?php if($virtualserver_hostmessage_mode==1) echo 'selected="selected"';?>><?php echo $sprache->virtualserver_hostmessage_mode_1;?></option>
                        <option value="2" <?php if($virtualserver_hostmessage_mode==2) echo 'selected="selected"';?>><?php echo $sprache->virtualserver_hostmessage_mode_2;?></option>
                        <option value="3" <?php if($virtualserver_hostmessage_mode==3) echo 'selected="selected"';?>><?php echo $sprache->virtualserver_hostmessage_mode_3;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="virtualserver_needed_identity_security_level"><?php echo $sprache->virtualserver_needed_identity_security_level;?></label>
                <div class="controls">
                    <input id="virtualserver_needed_identity_security_level" type="text" name="virtualserver_needed_identity_security_level" value="<?php echo $virtualserver_needed_identity_security_level;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="virtualserver_antiflood_points_tick_reduce"><?php echo $sprache->virtualserver_antiflood_points_tick_reduce;?></label>
                <div class="controls">
                    <input id="virtualserver_antiflood_points_tick_reduce" type="text" name="virtualserver_antiflood_points_tick_reduce" value="<?php echo $virtualserver_antiflood_points_tick_reduce;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="virtualserver_antiflood_points_needed_command_block"><?php echo $sprache->virtualserver_antiflood_points_needed_command_block;?></label>
                <div class="controls">
                    <input id="virtualserver_antiflood_points_needed_command_block" type="text" name="virtualserver_antiflood_points_needed_command_block" value="<?php echo $virtualserver_antiflood_points_needed_command_block;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="virtualserver_antiflood_points_needed_ip_block"><?php echo $sprache->virtualserver_antiflood_points_needed_ip_block;?></label>
                <div class="controls">
                    <input id="virtualserver_antiflood_points_needed_ip_block" type="text" name="virtualserver_antiflood_points_needed_ip_block" value="<?php echo $virtualserver_antiflood_points_needed_ip_block;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="md">
                </div>
            </div>
        </form>
    </div>
</div>