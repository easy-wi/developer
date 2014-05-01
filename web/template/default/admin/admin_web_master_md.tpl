<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=wm"><?php echo $gsprache->webspace." ".$gsprache->master;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip;?></li>
        </ul>
    </div>
</div>
<div class="alert alert-info">
    <?php echo $sprache->help_web_master;?>
</div>
<?php if (count($errors)>0){ ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4><?php echo $gsprache->errors;?></h4>
    <?php echo implode(', ',$errors);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=wm&amp;d=md&amp;id=<?php echo $id;?>&amp;r=wm" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputExternalID"><?php echo $gsprache->externalID;?></label>
                <div class="controls"><input id="inputExternalID" class="span11" type="text" name="externalID" value="<?php echo $externalID;?>" maxlength="255"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUsageType"><?php echo $sprache->usageType;?></label>
                <div class="controls">
                    <select id="inputUsageType" class="span11" name="usageType">
                        <option value="F"><?php echo $gsprache->fastdownload;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $dedicatedLanguage->active;?></label>
                <div class="controls">
                    <select id="inputActive" class="span11" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($active=="N") echo 'selected="selected";'?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['ip'])) echo ' error';?>">
                <label class="control-label" for="inputSshIP"><?php echo $dedicatedLanguage->ssh_ip;?></label>
                <div class="controls"><input id="inputSshIP" class="span11" type="text" name="ip" value="<?php echo $ip;?>" maxlength="15" required></div>
            </div>
            <div class="control-group<?php if(isset($errors['port'])) echo ' error';?>">
                <label class="control-label" for="inputSshPort"><?php echo $dedicatedLanguage->ssh_port;?></label>
                <div class="controls"><input id="inputSshPort" class="span11" type="number" name="port" value="<?php echo $port;?>" maxlength="5" required></div>
            </div>
            <div class="control-group<?php if(isset($errors['user'])) echo ' error';?>">
                <label class="control-label" for="inputSshUser"><?php echo $dedicatedLanguage->ssh_user;?></label>
                <div class="controls"><input id="inputSshUser" class="span11" type="text" name="user" value="<?php echo $user;?>" required></div>
            </div>
            <div class="control-group<?php if(isset($errors['publickey'])) echo ' error';?>">
                <label class="control-label" for="inputKeyUse"><?php echo $dedicatedLanguage->keyuse;?></label>
                <div class="controls">
                    <select id="inputKeyUse" class="span11" name="publickey" onchange="SwitchShowHideRows(this.value);">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($publickey=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                        <option value="B" <?php if ($publickey=='B') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                    </select>
                </div>
            </div>
            <div class="Y switch control-group<?php if(isset($errors['keyname'])) echo ' error';?><?php if($publickey=='N') echo ' display_none';?>">
                <label class="control-label" for="inputKeyName"><?php echo $dedicatedLanguage->keyname;?></label>
                <div class="controls"><input id="inputKeyName" class="span11" type="text" name="keyname" maxlength="20" value="<?php echo $keyname;?>"></div>
            </div>
            <div class="N switch control-group<?php if(isset($errors['pass'])) echo ' error';?><?php if($publickey=='Y') echo ' display_none';?>">
                <label class="control-label" for="inputSshPass"><?php echo $dedicatedLanguage->ssh_pass;?></label>
                <div class="controls"><input id="inputSshPass" class="span11" type="password" name="pass" value="<?php echo $pass;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputftpIP"><?php echo $sprache->ftpIP.' '.$sprache->optional;?></label>
                <div class="controls"><input id="inputftpIP" class="span11" type="text" name="ftpIP" value="<?php echo $ftpIP;?>" maxlength="15" ></div>
            </div>
            <div class="control-group<?php if(isset($errors['ftpPort'])) echo ' error';?>">
                <label class="control-label" for="inputftpPort"><?php echo $sprache->ftpPort;?></label>
                <div class="controls"><input id="inputftpPort" class="span11" type="number" name="ftpPort" value="<?php echo $ftpPort;?>" maxlength="5" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $dedicatedLanguage->description;?></label>
                <div class="controls"><input id="inputDesc" class="span11" type="text" name="description" value="<?php echo $description;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxVhost"><?php echo $sprache->maxVhost;?></label>
                <div class="controls"><input id="inputMaxVhost" class="span11" type="number" name="maxVhost" value="<?php echo $maxVhost;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxHDD"><?php echo $sprache->maxHDD;?></label>
                <div class="controls">
                    <div class="input-append span12">
                        <input id="inputMaxHDD" class="span11" type="number" name="maxHDD" value="<?php echo $maxHDD;?>">
                        <span class="add-on">MB</span>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHddOverbook"><?php echo $sprache->hddOverbook;?></label>
                <div class="controls">
                    <select id="inputHddOverbook" class="span11" name="hddOverbook" onchange="textdrop('overbookPercent');">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($hddOverbook=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div id="overbookPercent" class="control-group <?php if ($hddOverbook=='N') echo 'display_none';?>">
                <label class="control-label" for="inputOverbookPercent"><?php echo $sprache->overbookPercent;?></label>
                <div class="controls">
                    <div class="input-append span12">
                        <input id="inputOverbookPercent" class="span11" type="number" name="overbookPercent" value="<?php echo $overbookPercent;?>">
                        <span class="add-on">%</span>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultDNS"><?php echo $sprache->defaultdns;?></label>
                <div class="controls"><input id="inputDefaultDNS" class="span11" type="text" name="defaultdns" value="<?php echo $defaultdns;?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUserGroup"><?php echo $sprache->userGroup;?></label>
                <div class="controls"><input id="inputUserGroup" class="span11" type="text" name="userGroup" value="<?php echo $userGroup;?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputuserAddCmd"><?php echo $sprache->userAddCmd;?></label>
                <div class="controls"><input id="inputuserAddCmd" class="span11" type="text" name="userAddCmd" value="<?php echo $userAddCmd;?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputuserModCmd"><?php echo $sprache->userModCmd;?></label>
                <div class="controls"><input id="inputuserModCmd" class="span11" type="text" name="userModCmd" value="<?php echo $userModCmd;?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputuserDelCmd"><?php echo $sprache->userDelCmd;?></label>
                <div class="controls"><input id="inputuserDelCmd" class="span11" type="text" name="userDelCmd" value="<?php echo $userDelCmd;?>" required></div>
            </div>
            <div class="control-group<?php if(isset($errors['publickey'])) echo ' error';?>">
                <label class="control-label" for="inputServerType"><?php echo $sprache->serverType;?></label>
                <div class="controls">
                    <select id="inputServerType" class="span11" name="serverType" onchange="loadServerSettings(this.value);">
                        <option value="N">Nginx</option>
                        <option value="L" <?php if ($serverType=='L') echo 'selected="selected"';?>>Lighttpd</option>
                        <option value="H" <?php if ($serverType=='H') echo 'selected="selected"'; ?>>Hiawatha</option>
                        <option value="A" <?php if ($serverType=='A') echo 'selected="selected"'; ?>>Apache</option>
                        <option value="O" <?php if ($serverType=='O') echo 'selected="selected"'; ?>><?php echo $sprache->other;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDirHttpd"><?php echo $sprache->dirHttpd;?></label>
                <div class="controls"><input id="inputDirHttpd" class="span11" type="text" name="dirHttpd" value="<?php echo $dirHttpd;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDirLogs"><?php echo $sprache->dirLogs;?></label>
                <div class="controls"><input id="inputDirLogs" class="span11" type="text" name="dirLogs" value="<?php echo $dirLogs;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHttpdCmd"><?php echo $sprache->httpdCmd;?></label>
                <div class="controls"><input id="inputHttpdCmd" class="span11" type="text" name="httpdCmd" value="<?php echo $httpdCmd;?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputVhostStoragePath"><?php echo $sprache->vhostStoragePath;?></label>
                <div class="controls"><input id="inputVhostStoragePath" class="span11" type="text" name="vhostStoragePath" value="<?php echo $vhostStoragePath;?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputVhostConfigPath"><?php echo $sprache->vhostConfigPath;?></label>
                <div class="controls"><input id="inputVhostConfigPath" class="span11" type="text" name="vhostConfigPath" value="<?php echo $vhostConfigPath;?>" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputVhostTemplate"><?php echo $sprache->vhostTemplate;?></label>
                <div class="controls">
                    <textarea id="inputVhostTemplate" class="span11" name="vhostTemplate" rows="20" required><?php echo $vhostTemplate;?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputQuotaActive"><?php echo $sprache->quotaActive;?></label>
                <div class="controls">
                    <select id="inputQuotaActive" class="span11" name="quotaActive" onchange="SwitchShowHideRows(this.value,'switch2');">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($quotaActive=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="Y switch2 control-group<?php if($quotaActive=='N') echo ' display_none';?>">
                <label class="control-label" for="inputQuotaCmd"><?php echo $sprache->quotaCmd;?></label>
                <div class="controls"><input id="inputQuotaCmd" class="span11" type="text" name="quotaCmd" value="<?php echo $quotaCmd;?>"></div>
            </div>
            <div class="Y switch2 control-group<?php if($quotaActive=='N') echo ' display_none';?>">
                <label class="control-label" for="inputRepuotaCmd"><?php echo $sprache->repquotaCmd;?></label>
                <div class="controls"><input id="inputRepuotaCmd" class="span11" type="text" name="repquotaCmd" value="<?php echo $repquotaCmd;?>"></div>
            </div>
            <div class="Y switch2 control-group<?php if($quotaActive=='N') echo ' display_none';?>">
                <label class="control-label" for="inputBlocksize"><?php echo $sprache->blocksize;?></label>
                <div class="controls"><input id="inputBlocksize" class="span11" type="number" name="blocksize" value="<?php echo $blocksize;?>"><span class="help-block alert alert-info"><?php echo $sprache->help_blocksize;?></span></div>
            </div>
            <div class="Y switch2 control-group<?php if($quotaActive=='N') echo ' display_none';?>">
                <label class="control-label" for="inputInodeBlockRatio"><?php echo $sprache->inodeBlockRatio;?></label>
                <div class="controls"><input id="inputInodeBlockRatio" class="span11" type="number" name="inodeBlockRatio" value="<?php echo $inodeBlockRatio;?>"><span class="help-block alert alert-info"><?php echo $sprache->help_inode_block_ratio;?></span></div>
            </div>
            <div class="N switch2 control-group<?php if($quotaActive=='Y') echo ' display_none';?>">
                <div class="controls alert alert-error"><?php echo $sprache->quotaWarning;?></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->mod;?></button>
                </div>
            </div>
        </form>
    </div>
</div>