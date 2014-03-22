<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=vm"><?php echo $gsprache->voiceserver." ".$gsprache->master;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip;?></li>
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
        <form name="form" class="form-horizontal" action="admin.php?w=vm&amp;d=md&amp;id=<?php echo $id?>&amp;r=vm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label<?php if(isset($errors['active'])) echo ' error';?>" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputManaged">Managed</label>
                <div class="controls">
                    <select id="inputManaged" name="managedServer" onchange="textdrop('reseller');">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if($managedServer=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group <?php if($managedServer=='N') echo 'display_none';?>" id="reseller">
                <label class="control-label" for="inputManagedForID"><?php echo $gsprache->reseller;?></label>
                <div class="controls">
                    <select id="inputManagedForID" name="managedForID">
                        <?php foreach ($resellerIDs as $k=>$v){ ?><option value="<?php echo $k;?>" <?php if($managedForID==$k) echo 'selected="selected"';?>><?php echo $v;?></option><?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputExternalID">externalID</label>
                <div class="controls"><input id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID?>" maxlength="255"></div>
            </div>
            <div class="control-group">
                <label class="control-label<?php if(isset($errors['autorestart'])) echo ' error';?>" for="inputAutoRestart"><?php echo $sprache->autorestart;?></label>
                <div class="controls">
                    <select id="inputAutoRestart" name="autorestart">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($autorestart=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultName"><?php echo $sprache->defaultname;?></label>
                <div class="controls"><input id="inputDefaultName" type="text" name="defaultname" value="<?php echo $defaultname;?>"></div>
            </div>
            <?php if($type == 'ts3') { ?>
            <div class="control-group<?php if(isset($errors['queryport'])) echo ' error';?>">
                <label class="control-label" for="inputQueryPort"><?php echo $sprache->queryport;?></label>
                <div class="controls"><input id="inputQueryPort" type="text" name="queryport" value="<?php echo $queryport;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['querypassword'])) echo ' error';?>">
                <label class="control-label" for="inputQueryPassword"><?php echo $sprache->querypassword;?></label>
                <div class="controls"><input id="inputQueryPassword" type="text" name="querypassword" value="<?php echo $querypassword;?>"/></div>
            </div>
            <div class="control-group<?php if(isset($errors['filetransferport'])) echo ' error';?>">
                <label class="control-label" for="inputFiletransferPort"><?php echo $sprache->filetransferport;?></label>
                <div class="controls"><input id="inputFiletransferPort" type="text" name="filetransferport" value="<?php echo $filetransferport;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label<?php if(isset($errors['usedns'])) echo ' error';?>" for="inputUseDns"><?php echo $sprache->usedns;?></label>
                <div class="controls">
                    <select id="inputUseDns" name="usedns">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($usedns=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultDns"><?php echo $sprache->defaultdns;?></label>
                <div class="controls"><input id="inputDefaultDns" type="text" name="defaultdns" value="<?php echo $defaultdns;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTsdnsServerID"><?php echo $sprache->tsdnsServerID;?></label>
                <div class="controls">
                    <select id="inputTsdnsServerID" name="tsdnsServerID">
                        <option value=""><?php echo $gsprache->no;?></option>
                        <?php foreach ($externalDNS as $k=>$v) { ?>
                        <option value="<?php echo $k;?>" <?php if($k==$tsdnsServerID) echo 'selected="selected"';?>><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputExternalDefaultDNS"><?php echo $sprache->externalDefaultDNS;?></label>
                <div class="controls">
                    <select id="inputExternalDefaultDNS" name="externalDefaultDNS">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($externalDefaultDNS=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['maxserver'])) echo ' error';?>">
                <label class="control-label" for="inputMaxServer"><?php echo $sprache->maxserver;?></label>
                <div class="controls"><input id="inputMaxServer" type="text" name="maxserver" value="<?php echo $maxserver;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['maxslots'])) echo ' error';?>">
                <label class="control-label" for="inputMaxSlots"><?php echo $sprache->maxslots;?></label>
                <div class="controls"><input id="inputMaxSlots" type="text" name="maxslots" value="<?php echo $maxslots;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultWelcome"><?php echo $sprache->defaultwelcome;?></label>
                <div class="controls"><input id="inputDefaultWelcome" type="text" name="defaultwelcome" value="<?php echo $defaultwelcome;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultHostBannerUrl"><?php echo $sprache->defaulthostbanner_url;?></label>
                <div class="controls"><input id="inputDefaultHostBannerUrl" type="url" name="defaulthostbanner_url" value="<?php echo $defaulthostbanner_url;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultHostBannerGfxUrl"><?php echo $sprache->defaulthostbanner_gfx_url;?></label>
                <div class="controls"><input id="inputDefaultHostBannerGfxUrl" type="url" name="defaulthostbanner_gfx_url" value="<?php echo $defaulthostbanner_gfx_url;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultHostButtonTooltip"><?php echo $sprache->defaulthostbutton_tooltip;?></label>
                <div class="controls"><input id="inputDefaultHostButtonTooltip" type="text" name="defaulthostbutton_tooltip" value="<?php echo $defaulthostbutton_tooltip;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultHostButtonUrl"><?php echo $sprache->defaulthostbutton_url;?></label>
                <div class="controls"><input id="inputDefaultHostButtonUrl" type="url" name="defaulthostbutton_url" value="<?php echo $defaulthostbutton_url;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultHostButtonGfxUrl"><?php echo $sprache->defaulthostbutton_gfx_url;?></label>
                <div class="controls"><input id="inputDefaultHostButtonGfxUrl" type="url" name="defaulthostbutton_gfx_url" value="<?php echo $defaulthostbutton_gfx_url;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultFlexSlotsFree"><?php echo $sprache->defaultFlexSlotsFree;?></label>
                <div class="controls"><input id="inputDefaultFlexSlotsFree" type="text" name="defaultFlexSlotsFree" value="<?php echo $defaultFlexSlotsFree;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultFlexSlotsPercent"><?php echo $sprache->defaultFlexSlotsPercent;?></label>
                <div class="controls"><input id="inputDefaultFlexSlotsPercent" type="text" name="defaultFlexSlotsPercent" value="<?php echo $defaultFlexSlotsPercent;?>"></div>
            </div>
            <?php } ; if($addedby==1) { ?>
            <div class="control-group">
                <label class="control-label"><?php echo $sprache->rootserver;?></label>
                <div class="controls"><?php echo $root;?></div>
            </div>
            <?php } else if ($addedby==2){ ?>
            <div class="control-group">
                <label class="control-label" for="inputServerDir"><?php echo $sprache->serverdir;?></label>
                <div class="controls"><input id="inputServerDir" type="text" name="serverdir" value="<?php echo $serverdir;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['ip'])) echo ' error';?>">
                <label class="control-label" for="inputSshIP"><?php echo $sprache->ssh_ip;?></label>
                <div class="controls"><input id="inputSshIP" type="text" name="ip" maxlength="15" value="<?php echo $ip;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIps"><?php echo $sprache->ips;?></label>
                <div class="controls"><textarea id="inputIps" name="ips" rows="5" cols="23" ><?php echo $ips;?></textarea></div>
            </div>
            <div class="control-group<?php if(isset($errors['port'])) echo ' error';?>">
                <label class="control-label" for="inputSshPort"><?php echo $sprache->ssh_port;?></label>
                <div class="controls"><input id="inputSshPort" type="text" name="port" maxlength="5" value="<?php echo $port;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['user'])) echo ' error';?>">
                <label class="control-label" for="inputSshUser"><?php echo $sprache->ssh_user;?></label>
                <div class="controls"><input id="inputSshUser" type="text" name="user" maxlength="15" value="<?php echo $user;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['publickey'])) echo ' error';?>">
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
                <div class="controls"><input id="inputSshPass" type="password" name="pass" value="<?php echo $pass;?>"></div>
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
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>