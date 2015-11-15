<section class="content-header">
    <h1><?php echo $gsprache->appRoot;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ro"><i class="fa fa-server"></i> <?php echo $gsprache->appRoot;?></a></li>
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

                <form role="form" action="admin.php?w=ro&amp;d=ad&amp;r=ro" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputExternalID">externalID</label>
                            <div class="controls"><input class="form-control" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID?>" maxlength="255"></div>
                        </div>

                        <?php if ($reseller_id==0 or $reseller_id==$admin_id){ ?>

                        <div class="form-group">
                            <label for="inputAssignToReseller"><?php echo $sprache->resellerAssign;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputAssignToReseller" name="assignToReseller" onchange="SwitchShowHideRows(this.value, 'resellerID', 1);">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($assignToReseller=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="Y resellerID form-group<?php if($assignToReseller=='N') echo 'display_none';?>">
                            <label for="inputOwner"><?php echo $gsprache->reseller;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputOwner" name="ownerID">
                                    <option></option>
                                    <?php foreach ($table as $k=>$v){ ?>
                                    <option value="<?php echo $k;?>" <?php if($k==$ownerID) echo 'selected="selected"'; ?>><?php echo $v;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="form-group<?php if(isset($errors['active'])) echo ' has-error';?>">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($active=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['ip'])) echo ' has-error';?>">
                            <label for="inputIP"><?php echo $sprache->haupt_ip;?></label>
                            <div class="controls"><input class="form-control" id="inputIP" type="text" name="ip" value="<?php echo $ip?>" maxlength="15"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputConnectIpOnly"><?php echo $gsprache->connect_ip_only;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputConnectIpOnly" name="connectIpOnly">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($connectIpOnly=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputIPs"><?php echo $sprache->zusatz_ip;?></label>
                            <div class="controls"><textarea class="form-control" id="inputIPs" name="altips" rows="5"><?php echo $altips?></textarea></div>
                        </div>

                        <div class="form-group">
                            <label for="inputFTPPort"><?php echo $sprache->ftp_port;?></label>
                            <div class="controls"><input class="form-control" id="inputFTPPort" type="number" name="ftpport" value="<?php echo $ftpport?>" min="1" max="65535"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['port'])) echo ' has-error';?>">
                            <label for="inputSSH2Port"><?php echo $sprache->ssh_port;?></label>
                            <div class="controls"><input class="form-control" id="inputSSH2Port" type="number" name="port" value="<?php echo $port?>" min="1" max="65535"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['user'])) echo ' has-error';?>">
                            <label for="inputSSH2User"><?php echo $sprache->ssh_user;?></label>
                            <div class="controls"><input class="form-control" id="inputSSH2User" type="text" name="user" value="<?php echo $user?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['publickey'])) echo ' has-error';?>">
                            <label for="inputKeyUse"><?php echo $sprache->keyuse;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputKeyUse" name="publickey">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="B" <?php if ($publickey=="B") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                                    <option value="N" <?php if ($publickey=="N") echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['pass'])) echo ' has-error';?>">
                            <label for="inputSSH2Pass"><?php echo $sprache->ssh_pass;?></label>
                            <div class="controls"><input class="form-control" id="inputSSH2Pass" type="password" name="pass" value="<?php echo $pass?>" maxlength="50"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['keyname'])) echo ' has-error';?>">
                            <label for="inputSSH2Key"><?php echo $sprache->keyname;?></label>
                            <div class="controls"><input class="form-control" id="inputSSH2Key" type="text" name="keyname" maxlength="20" value="<?php echo $keyname;?>"/></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['os'])) echo ' has-error';?>">
                            <label for="inputOS"><?php echo $sprache->os;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputBit" name="os">
                                    <option value="L">Linux</option>
                                    <!--<option value="W" <?php if ($os=="W") echo 'selected="selected"'; ?>>Windows</option>-->
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['bit'])) echo ' has-error';?>">
                            <label for="inputBit"><?php echo $sprache->os_bit;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputBit" name="bit">
                                    <option value="32">32</option>
                                    <option value="64" <?php if ($bit=="64") echo 'selected="selected"'; ?>>64</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['hyperthreading'])) echo ' has-error';?>">
                            <label for="inputHT">Hyper Threading</label>
                            <div class="controls">
                                <select class="form-control" id="inputHT" name="hyperthreading">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if ($hyperthreading=="N") echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputCores">Cores</label>
                            <div class="controls"><input class="form-control" id="inputCores" type="number" name="cores" value="<?php echo $cores;?>" maxlength="5"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputRam">Ram (MB)</label>
                            <div class="controls">
                                <input class="form-control" id="inputRam" type="number" name="ram" value="<?php echo $ram;?>" maxlength="5">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDesc"><?php echo $sprache->desc;?></label>
                            <div class="controls"><input class="form-control" id="inputDesc" type="text" name="desc" value="<?php echo $desc;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputSlots"><?php echo $sprache->maxslots;?></label>
                            <div class="controls"><input class="form-control" id="inputSlots" type="number" name="maxslots" value="<?php echo $maxslots;?>" maxlength="5"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputServer"><?php echo $sprache->maxserver2;?></label>
                            <div class="controls"><input class="form-control" id="inputServer" type="number" name="maxserver" value="<?php echo $maxserver;?>" maxlength="4"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputInstallPaths"><?php echo $sprache->installPaths;?></label>
                            <div class="controls"><textarea class="form-control" id="inputInstallPaths" name="installPaths" rows="5"><?php echo $installPaths;?></textarea></div>
                        </div>

                        <div class="form-group">
                            <label for="inputConfigLogTime"><?php echo $sprache->configLogTime;?></label>
                            <div class="controls"><input class="form-control" id="inputConfigLogTime" type="number" name="configLogTime" value="<?php echo $configLogTime;?>" maxlength="3"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputConfigDemoTime"><?php echo $sprache->configDemoTime;?></label>
                            <div class="controls"><input class="form-control" id="inputConfigDemoTime" type="number" name="configDemoTime" value="<?php echo $configDemoTime;?>" maxlength="3"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputConfigZtmpTime"><?php echo $sprache->configZtmpTime;?></label>
                            <div class="controls"><input class="form-control" id="inputConfigZtmpTime" type="number" name="configZtmpTime" value="<?php echo $configZtmpTime;?>" maxlength="3"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputConfigBadTime"><?php echo $sprache->configBadTime;?></label>
                            <div class="controls"><input class="form-control" id="inputConfigBadTime" type="number" name="configBadTime" value="<?php echo $configBadTime;?>" maxlength="3"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputConfigUserID"><?php echo $sprache->configUserID;?></label>
                            <div class="controls"><input class="form-control" id="inputConfigUserID" type="number" name="configUserID" value="<?php echo $configUserID;?>" maxlength="10"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputConfigBinaries"><?php echo $sprache->configBinaries;?></label>
                            <div class="controls"><input class="form-control" id="inputConfigBinaries" type="text" name="configBinaries" value="<?php echo $configBinaries;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputConfigFiles"><?php echo $sprache->configFiles;?></label>
                            <div class="controls"><input class="form-control" id="inputConfigFiles" type="text" name="configFiles" value="<?php echo $configFiles;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputConfigBadFiles"><?php echo $sprache->configBadFiles;?></label>
                            <div class="controls"><input class="form-control" id="inputConfigBadFiles" type="text" name="configBadFiles" value="<?php echo $configBadFiles;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputConfigIonice"><?php echo $sprache->configIonice;?></label>
                            <div class="controls">
                                <select id="inputConfigIonice" class="form-control" name="configIonice">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($configIonice=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputQuotaActive"><?php echo $sprache->quotaActive;?></label>
                            <div class="controls">
                                <select id="inputQuotaActive" class="form-control" name="quotaActive" onchange="SwitchShowHideRows(this.value,'quotaSwitch');">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($quotaActive=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>
                        <div class="Y quotaSwitch form-group<?php if($quotaActive=='N') echo ' display_none';?>">
                            <label for="inputQuotaCmd"><?php echo $sprache->quotaCmd;?></label>
                            <div class="controls"><input id="inputQuotaCmd" class="form-control" type="text" name="quotaCmd" value="<?php echo $quotaCmd;?>"></div>
                        </div>
                        <div class="Y quotaSwitch form-group<?php if($quotaActive=='N') echo ' display_none';?>">
                            <label for="inputRepuotaCmd"><?php echo $sprache->repquotaCmd;?></label>
                            <div class="controls"><input id="inputRepuotaCmd" class="form-control" type="text" name="repquotaCmd" value="<?php echo $repquotaCmd;?>"></div>
                        </div>
                        <div class="Y quotaSwitch form-group<?php if($quotaActive=='N') echo ' display_none';?>">
                            <label for="inputBlocksize"><?php echo $sprache->blocksize;?></label>
                            <div class="controls">
                                <input id="inputBlocksize" class="form-control" type="number" name="blocksize" value="<?php echo $blocksize;?>">
                                <span class="help-block alert alert-info">
                                    <?php echo $sprache->help_blocksize;?>
                                </span>
                            </div>
                        </div>
                        <div class="Y quotaSwitch form-group<?php if($quotaActive=='N') echo ' display_none';?>">
                            <label for="inputInodeBlockRatio"><?php echo $sprache->inodeBlockRatio;?></label>
                            <div class="controls"><input id="inputInodeBlockRatio" class="form-control" type="number" name="inodeBlockRatio" value="<?php echo $inodeBlockRatio;?>"><span class="help-block alert alert-info"><?php echo $sprache->help_inode_block_ratio;?></span></div>
                        </div>
                        <div class="N quotaSwitch form-group<?php if($quotaActive=='Y') echo ' display_none';?>">
                            <div class="controls"><div class="alert alert-warning"><?php echo $sprache->quotaWarning;?></div></div>
                        </div>

                        <div class="form-group">
                            <label for="inputAutoupdate">Autoupdate</label>
                            <div class="controls">
                                <select class="form-control" id="inputAutoupdate" name="updates">
                                    <option value="1">Vendor + Rsync/FTP Sync</option>
                                    <option value="2" <?php if ($updates==2) echo 'selected="selected"'; ?>>Vendor</option>
                                    <option value="4" <?php if ($updates==4) echo 'selected="selected"'; ?>>Rsync/FTP Sync</option>
                                    <option value="3" <?php if ($updates==3) echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputUpdateMinute"><?php echo $sprache->updateMinute;?></label>
                            <div class="controls"><input class="form-control" id="inputUpdateMinute" type="number" name="updateMinute" value="<?php echo $updateMinute;?>" min="0" max="59"></div>
                        </div>

                        <h3>Steam Account</h3>

                        <div class="form-group">
                            <div class="controls"><div class="alert alert-warning"><?php echo $sprache->steamWarning;?></div></div>
                        </div>

                        <div class="form-group">
                            <label for="inputSteamCmd">SteamCmd Account</label>
                            <div class="controls"><input class="form-control" id="inputSteamCmd" type="text" name="steamAccount" value="<?php echo $steamAccount;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputSteamCmdPWD">SteamCmd <?php echo $sprache->password;?></label>
                            <div class="controls"><input class="form-control" id="inputSteamCmdPWD" type="text" name="steamPassword" value="<?php echo $steamPassword;?>"></div>
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