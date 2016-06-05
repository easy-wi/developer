<section class="content-header">
    <h1><?php echo $gsprache->webspace.' '.$gsprache->master;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=wv"><i class="fa fa-cubes"></i> <?php echo $gsprache->webspace;?></a></li>
        <li><a href="admin.php?w=wm"><i class="fa fa-server"></i> <?php echo $gsprache->webspace.' '.$gsprache->master;?></a></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>

<section class="content">

    <?php if (count($errors)>0){ ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><?php echo $gsprache->errors;?></h4>
                <?php echo implode(', ',$errors);?>
            </div>
        </div>
    </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">

                <form role="form" action="admin.php?w=wm&amp;d=ad&amp;r=wm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input class="form-control" type="hidden" name="token" value="<?php echo token();?>">
                    <input class="form-control" type="hidden" name="action" value="ad">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputExternalID"><?php echo $gsprache->externalID;?></label>
                            <div class="controls"><input class="form-control" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID;?>" maxlength="255"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputUsageType"><?php echo $sprache->usageType;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputUsageType" name="usageType" onchange="loadServerSettings($('#inputServerType').val(), this.value);textdrop('inputPhpConfigurationShow');">
                                    <option value="F"><?php echo $gsprache->fastdownload;?></option>
                                    <option value="W"><?php echo $gsprache->webspace;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if($usageType!='W') echo ' display_none';?>" id="inputPhpConfigurationShow">
                            <label for="inputPhpConfiguration"><?php echo $sprache->phpConfiguration;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputPhpConfiguration" name="phpConfiguration" rows="10"><?php echo $phpConfiguration;?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputActive"><?php echo $dedicatedLanguage->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if ($active=="N") echo 'selected="selected";'?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['ip'])) echo ' error';?>">
                            <label for="inputSshIP"><?php echo $dedicatedLanguage->ssh_ip;?></label>
                            <div class="controls"><input class="form-control" id="inputSshIP" type="text" name="ip" value="<?php echo $ip;?>" maxlength="15" required></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['port'])) echo ' error';?>">
                            <label for="inputSshPort"><?php echo $dedicatedLanguage->ssh_port;?></label>
                            <div class="controls"><input class="form-control" id="inputSshPort" type="number" name="port" value="<?php echo $port;?>" maxlength="5" required></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['user'])) echo ' error';?>">
                            <label for="inputSshUser"><?php echo $dedicatedLanguage->ssh_user;?></label>
                            <div class="controls"><input class="form-control" id="inputSshUser" type="text" name="user" value="<?php echo $user;?>" required></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['publickey'])) echo ' error';?>">
                            <label for="inputKeyUse"><?php echo $dedicatedLanguage->keyuse;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputKeyUse" name="publickey" onchange="SwitchShowHideRows(this.value);">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($publickey=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                    <option value="B" <?php if ($publickey=='B') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="Y switch form-group<?php if(isset($errors['keyname'])) echo ' error';?><?php if($publickey=='N') echo ' display_none';?>">
                            <label for="inputKeyName"><?php echo $dedicatedLanguage->keyname;?></label>
                            <div class="controls"><input class="form-control" id="inputKeyName" type="text" name="keyname" maxlength="20" value="<?php echo $keyname;?>"></div>
                        </div>

                        <div class="N switch form-group<?php if(isset($errors['pass'])) echo ' error';?><?php if($publickey=='Y') echo ' display_none';?>">
                            <label for="inputSshPass"><?php echo $dedicatedLanguage->ssh_pass;?></label>
                            <div class="controls"><input class="form-control" id="inputSshPass" type="password" name="pass" value="<?php echo $pass;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputftpIP"><?php echo $sprache->ftpIP.' '.$sprache->optional;?></label>
                            <div class="controls"><input class="form-control" id="inputftpIP" type="text" name="ftpIP" value="<?php echo $ftpIP;?>" maxlength="15" ></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['ftpPort'])) echo ' error';?>">
                            <label for="inputftpPort"><?php echo $sprache->ftpPort;?></label>
                            <div class="controls"><input class="form-control" id="inputftpPort" type="number" name="ftpPort" value="<?php echo $ftpPort;?>" maxlength="5" required></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDesc"><?php echo $dedicatedLanguage->description;?></label>
                            <div class="controls"><input class="form-control" id="inputDesc" type="text" name="description" value="<?php echo $description;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputMaxVhost"><?php echo $sprache->maxVhost;?></label>
                            <div class="controls"><input class="form-control" id="inputMaxVhost" type="number" name="maxVhost" value="<?php echo $maxVhost;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputMaxHDD"><?php echo $sprache->maxHDD;?></label>
                            <div class="controls">
                                <div class="input-group">
                                    <input class="form-control" id="inputMaxHDD" type="number" name="maxHDD" value="<?php echo $maxHDD;?>">
                                    <span class="input-group-addon">MB</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputHddOverbook"><?php echo $sprache->hddOverbook;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputHddOverbook" name="hddOverbook" onchange="textdrop('overbookPercent');">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($hddOverbook=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div id="overbookPercent" class="form-group <?php if ($hddOverbook=='N') echo 'display_none';?>">
                            <label for="inputOverbookPercent"><?php echo $sprache->overbookPercent;?></label>
                            <div class="controls">
                                <div class="input-group">
                                    <input class="form-control" id="inputOverbookPercent" type="number" name="overbookPercent" value="<?php echo $overbookPercent;?>">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultDNS"><?php echo $sprache->defaultdns;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultDNS" type="text" name="defaultdns" value="<?php echo $defaultdns;?>" required></div>
                        </div>

                        <div class="form-group">
                            <label for="inputUserGroup"><?php echo $sprache->userGroup;?></label>
                            <div class="controls"><input class="form-control" id="inputUserGroup" type="text" name="userGroup" value="<?php echo $userGroup;?>" required></div>
                        </div>

                        <div class="form-group">
                            <label for="inputuserAddCmd"><?php echo $sprache->userAddCmd;?></label>
                            <div class="controls"><input class="form-control" id="inputuserAddCmd" type="text" name="userAddCmd" value="<?php echo $userAddCmd;?>" required></div>
                        </div>

                        <div class="form-group">
                            <label for="inputuserModCmd"><?php echo $sprache->userModCmd;?></label>
                            <div class="controls"><input class="form-control" id="inputuserModCmd" type="text" name="userModCmd" value="<?php echo $userModCmd;?>" required></div>
                        </div>

                        <div class="form-group">
                            <label for="inputuserDelCmd"><?php echo $sprache->userDelCmd;?></label>
                            <div class="controls"><input class="form-control" id="inputuserDelCmd" type="text" name="userDelCmd" value="<?php echo $userDelCmd;?>" required></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['publickey'])) echo ' error';?>">
                            <label for="inputServerType"><?php echo $sprache->serverType;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputServerType" name="serverType" onchange="loadServerSettings(this.value, $('#inputUsageType').val());">
                                    <option value="N">Nginx</option>
                                    <option value="L" <?php if ($serverType=='L') echo 'selected="selected"';?>>Lighttpd</option>
                                    <option value="H" <?php if ($serverType=='H') echo 'selected="selected"'; ?>>Hiawatha</option>
                                    <option value="A" <?php if ($serverType=='A') echo 'selected="selected"'; ?>>Apache</option>
                                    <option value="O" <?php if ($serverType=='O') echo 'selected="selected"'; ?>><?php echo $sprache->other;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDirHttpd"><?php echo $sprache->dirHttpd;?></label>
                            <div class="controls"><input class="form-control" id="inputDirHttpd" type="text" name="dirHttpd" value="<?php echo $dirHttpd;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDirLogs"><?php echo $sprache->dirLogs;?></label>
                            <div class="controls"><input class="form-control" id="inputDirLogs" type="text" name="dirLogs" value="<?php echo $dirLogs;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputHttpdCmd"><?php echo $sprache->httpdCmd;?></label>
                            <div class="controls"><input class="form-control" id="inputHttpdCmd" type="text" name="httpdCmd" value="<?php echo $httpdCmd;?>" required></div>
                        </div>

                        <div class="form-group">
                            <label for="inputVhostStoragePath"><?php echo $sprache->vhostStoragePath;?></label>
                            <div class="controls"><input class="form-control" id="inputVhostStoragePath" type="text" name="vhostStoragePath" value="<?php echo $vhostStoragePath;?>" required></div>
                        </div>

                        <div class="form-group">
                            <label for="inputVhostConfigPath"><?php echo $sprache->vhostConfigPath;?></label>
                            <div class="controls"><input class="form-control" id="inputVhostConfigPath" type="text" name="vhostConfigPath" value="<?php echo $vhostConfigPath;?>" required></div>
                        </div>

                        <div class="form-group">
                            <label for="inputVhostTemplate"><?php echo $sprache->vhostTemplate;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputVhostTemplate" name="vhostTemplate" rows="10" required><?php echo $vhostTemplate;?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputQuotaActive"><?php echo $sprache->quotaActive;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputQuotaActive" name="quotaActive" onchange="SwitchShowHideRows(this.value,'switch2');">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($quotaActive=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="Y switch2 form-group<?php if($quotaActive=='N') echo ' display_none';?>">
                            <label for="inputQuotaCmd"><?php echo $sprache->quotaCmd;?></label>
                            <div class="controls"><input class="form-control" id="inputQuotaCmd" type="text" name="quotaCmd" value="<?php echo $quotaCmd;?>"></div>
                        </div>

                        <div class="Y switch2 form-group<?php if($quotaActive=='N') echo ' display_none';?>">
                            <label for="inputRepuotaCmd"><?php echo $sprache->repquotaCmd;?></label>
                            <div class="controls"><input class="form-control" id="inputRepuotaCmd" type="text" name="repquotaCmd" value="<?php echo $repquotaCmd;?>"></div>
                        </div>

                        <div class="Y switch2 form-group<?php if($quotaActive=='N') echo ' display_none';?>">
                            <label for="inputBlocksize"><?php echo $sprache->blocksize;?></label>
                            <div class="controls"><input class="form-control" id="inputBlocksize" type="number" name="blocksize" value="<?php echo $blocksize;?>"><span class="help-block alert alert-info"><?php echo $sprache->help_blocksize;?></span></div>
                        </div>

                        <div class="Y switch2 form-group<?php if($quotaActive=='N') echo ' display_none';?>">
                            <label for="inputInodeBlockRatio"><?php echo $sprache->inodeBlockRatio;?></label>
                            <div class="controls"><input class="form-control" id="inputInodeBlockRatio" type="number" name="inodeBlockRatio" value="<?php echo $inodeBlockRatio;?>"><span class="help-block alert alert-info"><?php echo $sprache->help_inode_block_ratio;?></span></div>
                        </div>

                        <div class="N switch2 form-group<?php if($quotaActive=='Y') echo ' display_none';?>">
                            <div class="alert alert-warning"><?php echo $sprache->quotaWarning;?></div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>