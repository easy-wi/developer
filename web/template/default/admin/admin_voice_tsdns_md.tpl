<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=vd">TSDNS <?php echo $gsprache->master;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ssh2ip;?></li>
        </ul>
    </div>
</div>
<?php if (count($errors)>0){ ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4><?php echo $gsprache->errors;?></h4>
    <?php echo implode(', ',$errors);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span6">
        <form name="form" class="form-horizontal" action="admin.php?w=vd&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group<?php if(isset($errors['active'])) echo ' error';?>">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['autorestart'])) echo ' error';?>">
                <label class="control-label" for="inputAutoRestart"><?php echo $sprache->autorestart;?></label>
                <div class="controls">
                    <select id="inputAutoRestart" name="autorestart">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($autorestart=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultDns"><?php echo $sprache->defaultdns;?></label>
                <div class="controls"><input id="inputDefaultDns" type="text" name="defaultdns" value="<?php echo $defaultdns;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputServerDir"><?php echo $sprache->serverdir;?></label>
                <div class="controls"><input id="inputServerDir" type="text" name="serverdir" value="<?php echo $serverdir;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['ip'])) echo ' error';?>">
                <label class="control-label" for="inputSshIP"><?php echo $sprache->ssh_ip;?></label>
                <div class="controls"><input id="inputSshIP" type="text" name="ip" maxlength="15" value="<?php echo $ssh2ip;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['port'])) echo ' error';?>">
                <label class="control-label" for="inputSshPort"><?php echo $sprache->ssh_port;?></label>
                <div class="controls"><input id="inputSshPort" type="text" name="port" maxlength="5" value="<?php echo $ssh2port;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['user'])) echo ' error';?>">
                <label class="control-label" for="inputSshUser"><?php echo $sprache->ssh_user;?></label>
                <div class="controls"><input id="inputSshUser" type="text" name="user" maxlength="15" value="<?php echo $ssh2user;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyUse"><?php echo $sprache->keyuse;?></label>
                <div class="controls">
                    <select id="inputKeyUse" name="publickey">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="B" <?php if ($publickey=="B") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                        <option value="N" <?php if($publickey=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['pass'])) echo ' error';?>">
                <label class="control-label" for="inputSshPass"><?php echo $sprache->ssh_pass;?></label>
                <div class="controls"><input id="inputSshPass" type="password" name="pass" value="<?php echo $ssh2password;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['keyname'])) echo ' error';?>">
                <label class="control-label" for="inputKeyName"><?php echo $sprache->keyname;?></label>
                <div class="controls"><input id="inputKeyName" type="text" name="keyname" maxlength="20" value="<?php echo $keyname;?>"/></div>
            </div>
            <div class="control-group<?php if(isset($errors['bit'])) echo ' error';?>">
                <label class="control-label" for="inputOsBit"><?php echo $sprache->os_bit;?></label>
                <div class="controls">
                    <select id="inputOsBit" name="bit">
                        <option value="32">32</option>
                        <option value="64" <?php if($bit=='64') echo 'selected="selected"';?>>64</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                <div class="controls"><textarea id="inputDesc" name="description"><?php echo $description;?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>