<section class="content-header">
    <h1><?php echo $gsprache->voiceserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><i class="fa fa-cog"></i> <?php echo $gsprache->settings;?></li>
        <li class="active"><?php echo $ip.':'.$port;?> <?php if ($usedns=='Y') echo $dns;?></li>
    </ol>
</section>

<section class="content">

	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_voiceserver_md;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <form role="form" action="userpanel.php?w=vo&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vo" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <?php if ($usedns=='Y'){ ?>
                        <div class="form-group">
                            <label for="defaultdns"><?php echo $sprache->defaultdns;?></label>
                            <input class="form-control" id="defaultdns" type="text" name="defaultdns" value="<?php echo $defaultdns; ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="dns"><?php echo $sprache->dns;?></label>
                            <input class="form-control" id="dns" type="text" name="dns" value="<?php echo $dns; ?>">
                        </div>
                        <?php } ?>

                        <div class="form-group">
                            <label for="initialpassword"><?php echo $sprache->initialpassword;?></label>
                            <input class="form-control" id="initialpassword" type="text" name="initialpassword" value="<?php echo $initialpassword; ?>" <?php if ($password=='Y') echo 'required';?>>
                        </div>

                        <div class="form-group">
                            <label for="virtualserver_reserved_slots"><?php echo $sprache->virtualserver_reserved_slots;?></label>
                            <input class="form-control" id="virtualserver_reserved_slots" type="text" name="virtualserver_reserved_slots" value="<?php echo $virtualserver_reserved_slots;?>">
                        </div>

                        <div class="form-group">
                            <label for="name"><?php echo $sprache->name;?></label>
                            <input class="form-control" id="name" type="text" name="name" value="<?php echo $name; ?>">
                        </div>

                        <div class="form-group">
                            <label for="welcome"><?php echo $sprache->welcome;?></label>
                            <input class="form-control" id="welcome" type="text" name="welcome" value="<?php echo $welcome; ?>"<?php if ($forcewelcome=='Y') echo 'disabled';?>>
                        </div>

                        <div class="form-group">
                            <label for="hostbanner_url"><?php echo $sprache->hostbanner_url;?></label>
                            <input class="form-control" id="hostbanner_url" type="text" name="hostbanner_url" value="<?php echo $hostbanner_url; ?>" <?php if ($forcebanner=='Y') echo 'disabled';?>>
                        </div>

                        <div class="form-group">
                            <label for="hostbanner_gfx_url"><?php echo $sprache->hostbanner_gfx_url;?></label>
                            <input class="form-control" id="hostbanner_gfx_url" type="text" name="hostbanner_gfx_url" value="<?php echo $hostbanner_gfx_url; ?>"<?php if ($forcebanner=='Y') echo 'disabled';?>>
                        </div>

                        <div class="form-group">
                            <label for="virtualserver_hostbanner_gfx_interval"><?php echo $sprache->virtualserver_hostbanner_gfx_interval;?></label>
                            <input class="form-control" id="virtualserver_hostbanner_gfx_interval" type="text" name="virtualserver_hostbanner_gfx_interval" value="<?php echo $virtualserver_hostbanner_gfx_interval;?>" <?php if ($forcebanner=='Y') echo 'disabled';?>>
                        </div>

                        <div class="form-group">
                            <label for="hostbutton_url"><?php echo $sprache->hostbutton_url;?></label>
                            <input class="form-control" id="hostbutton_url" type="text" name="hostbutton_url" value="<?php echo $hostbutton_url; ?>" <?php if ($forcebutton=='Y') echo 'disabled';?>>
                        </div>

                        <div class="form-group">
                            <label for="hostbutton_gfx_url"><?php echo $sprache->hostbutton_gfx_url;?></label>
                            <input class="form-control" id="hostbutton_gfx_url" type="text" name="hostbutton_gfx_url" value="<?php echo $hostbutton_gfx_url; ?>" <?php if ($forcebutton=='Y') echo 'disabled';?>>
                        </div>

                        <div class="form-group">
                            <label for="hostbutton_tooltip"><?php echo $sprache->hostbutton_tooltip;?></label>
                            <input class="form-control" id="hostbutton_tooltip" type="text" name="hostbutton_tooltip" value="<?php echo $hostbutton_tooltip; ?>" <?php if ($forcebutton=='Y') echo 'disabled';?>>
                        </div>

                        <div class="form-group">
                            <label for="virtualserver_hostmessage_mode"><?php echo $sprache->virtualserver_hostmessage_mode;?></label>
                            <select class="form-control" id="virtualserver_hostmessage_mode" name="virtualserver_hostmessage_mode">
                                <option value="0"><?php echo $sprache->virtualserver_hostmessage_mode_0;?></option>
                                <option value="1" <?php if($virtualserver_hostmessage_mode==1) echo 'selected="selected"';?>><?php echo $sprache->virtualserver_hostmessage_mode_1;?></option>
                                <option value="2" <?php if($virtualserver_hostmessage_mode==2) echo 'selected="selected"';?>><?php echo $sprache->virtualserver_hostmessage_mode_2;?></option>
                                <option value="3" <?php if($virtualserver_hostmessage_mode==3) echo 'selected="selected"';?>><?php echo $sprache->virtualserver_hostmessage_mode_3;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="virtualserver_needed_identity_security_level"><?php echo $sprache->virtualserver_needed_identity_security_level;?></label>
                            <input class="form-control" id="virtualserver_needed_identity_security_level" type="text" name="virtualserver_needed_identity_security_level" value="<?php echo $virtualserver_needed_identity_security_level;?>">
                        </div>

                        <div class="form-group">
                            <label for="virtualserver_antiflood_points_tick_reduce"><?php echo $sprache->virtualserver_antiflood_points_tick_reduce;?></label>
                            <input class="form-control" id="virtualserver_antiflood_points_tick_reduce" type="text" name="virtualserver_antiflood_points_tick_reduce" value="<?php echo $virtualserver_antiflood_points_tick_reduce;?>">
                        </div>

                        <div class="form-group">
                            <label for="virtualserver_antiflood_points_needed_command_block"><?php echo $sprache->virtualserver_antiflood_points_needed_command_block;?></label>
                            <input class="form-control" id="virtualserver_antiflood_points_needed_command_block" type="text" name="virtualserver_antiflood_points_needed_command_block" value="<?php echo $virtualserver_antiflood_points_needed_command_block;?>">
                        </div>

                        <div class="form-group">
                            <label for="virtualserver_antiflood_points_needed_ip_block"><?php echo $sprache->virtualserver_antiflood_points_needed_ip_block;?></label>
                            <input class="form-control" id="virtualserver_antiflood_points_needed_ip_block" type="text" name="virtualserver_antiflood_points_needed_ip_block" value="<?php echo $virtualserver_antiflood_points_needed_ip_block;?>">
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>