<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=ro"><?php echo $gsprache->gameroot;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
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
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=ro&amp;d=ad&amp;r=ro" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <?php if ($reseller_id==0 or $reseller_id==$admin_id){ ?>
            <div class="control-group">
                <label class="control-label" for="inputOwner"><?php echo $gsprache->user;?></label>
                <div class="controls">
                    <select class="span12" id="inputOwner" name="ownerID">
                        <option></option>
                        <?php foreach ($table as $k=>$v){ ?>
                        <option value="<?php echo $k;?>" <?php if($k==$ownerID) echo 'selected="selected"'; ?>><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php } ?>
            <div class="control-group<?php if(isset($errors['active'])) echo ' error';?>">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select class="span12" id="inputActive" name="active">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($active=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputExternalID">externalID:</label>
                <div class="controls"><input class="span12" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID?>" maxlength="255"></div>
            </div>
            <div class="control-group<?php if(isset($errors['ip'])) echo ' error';?>">
                <label class="control-label" for="inputIP"><?php echo $sprache->haupt_ip;?>:</label>
                <div class="controls"><input class="span12" id="inputIP" type="text" name="ip" value="<?php echo $ip?>" maxlength="15"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIPs"><?php echo $sprache->zusatz_ip;?>:</label>
                <div class="controls"><textarea class="span12" id="inputIPs" name="altips" rows="5"><?php echo $altips?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFTPPort"><?php echo $sprache->ftp_port;?>:</label>
                <div class="controls"><input class="span12" id="inputFTPPort" type="text" name="ftpport" value="<?php echo $ftpport?>" maxlength="5"></div>
            </div>
            <div class="control-group<?php if(isset($errors['port'])) echo ' error';?>">
                <label class="control-label" for="inputSSH2Port"><?php echo $sprache->ssh_port;?>:</label>
                <div class="controls"><input class="span12" id="inputSSH2Port" type="text" name="port" value="<?php echo $port?>" maxlength="5"></div>
            </div>
            <div class="control-group<?php if(isset($errors['user'])) echo ' error';?>">
                <label class="control-label" for="inputSSH2User"><?php echo $sprache->ssh_user;?>:</label>
                <div class="controls"><input class="span12" id="inputSSH2User" type="text" name="user" value="<?php echo $user?>" maxlength="20"></div>
            </div>
            <div class="control-group<?php if(isset($errors['publickey'])) echo ' error';?>">
                <label class="control-label" for="inputKeyUse"><?php echo $sprache->keyuse;?></label>
                <div class="controls">
                    <select class="span12" id="inputKeyUse" name="publickey">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="B" <?php if ($publickey=="B") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                        <option value="N" <?php if ($publickey=="N") echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['pass'])) echo ' error';?>">
                <label class="control-label" for="inputSSH2Pass"><?php echo $sprache->ssh_pass;?>:</label>
                <div class="controls"><input class="span12" id="inputSSH2Pass" type="password" name="pass" value="<?php echo $pass?>" maxlength="50"></div>
            </div>
            <div class="control-group<?php if(isset($errors['keyname'])) echo ' error';?>">
                <label class="control-label" for="inputSSH2Key"><?php echo $sprache->keyname;?></label>
                <div class="controls"><input class="span12" id="inputSSH2Key" type="text" name="keyname" maxlength="20" value="<?php echo $keyname;?>"/></div>
            </div>
            <div class="control-group<?php if(isset($errors['os'])) echo ' error';?>">
                <label class="control-label" for="inputOS"><?php echo $sprache->os;?>:</label>
                <div class="controls">
                    <select class="span12" id="inputBit" name="os">
                        <option value="L">Linux</option>
                        <option value="W" <?php if ($os=="W") echo 'selected="selected"'; ?>>Windows</option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['bit'])) echo ' error';?>">
                <label class="control-label" for="inputBit"><?php echo $sprache->os_bit;?>:</label>
                <div class="controls">
                    <select class="span12" id="inputBit" name="bit">
                        <option value="32">32</option>
                        <option value="64" <?php if ($bit=="64") echo 'selected="selected"'; ?>>64</option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['hyperthreading'])) echo ' error';?>">
                <label class="control-label" for="inputHT">Hyper Threading</label>
                <div class="controls">
                    <select class="span12" id="inputHT" name="hyperthreading">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($hyperthreading=="N") echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCores">Cores</label>
                <div class="controls"><input class="span12" id="inputCores" type="text" name="cores" value="<?php echo $cores;?>" maxlength="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRam">Ram (MB)</label>
                <div class="controls">
                    <input class="span12" id="inputRam" type="text" name="ram" value="<?php echo $ram;?>" maxlength="5">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->desc;?>:</label>
                <div class="controls"><input class="span12" id="inputDesc" type="text" name="desc" value="<?php echo $desc;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSlots"><?php echo $sprache->maxslots;?></label>
                <div class="controls"><input class="span12" id="inputSlots" type="text" name="maxslots" value="<?php echo $maxslots;?>" maxlength="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputServer"><?php echo $sprache->maxserver2;?></label>
                <div class="controls"><input class="span12" id="inputServer" type="text" name="maxserver" value="<?php echo $maxserver;?>" maxlength="4"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputAutoupdate">Autoupdate:</label>
                <div class="controls">
                    <select class="span12" id="inputAutoupdate" name="updates">
                        <option value="1">Vendor + Rsync/FTP Sync</option>
                        <option value="2" <?php if ($updates==2) echo 'selected="selected"'; ?>>Vendor</option>
                        <option value="4" <?php if ($updates==4) echo 'selected="selected"'; ?>>Rsync/FTP Sync</option>
                        <option value="3" <?php if ($updates==3) echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUpdateMinute"><?php echo $sprache->updateMinute;?></label>
                <div class="controls"><input class="span12" id="inputUpdateMinute" type="number" name="updateMinute" value="<?php echo $updateMinute;?>" min="0" max="59"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSteamCmd">SteamCmd Account:</label>
                <div class="controls"><input class="span12" id="inputSteamCmd" type="text" name="steamAccount" value="<?php echo $steamAccount;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSteamCmdPWD">SteamCmd <?php echo $sprache->password;?>:</label>
                <div class="controls"><input class="span12" id="inputSteamCmdPWD" type="text" name="steamPassword" value="<?php echo $steamPassword;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>