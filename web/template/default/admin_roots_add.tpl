<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=ro"><?php echo $gsprache->gameroot;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=ro&amp;d=ad&amp;r=ro" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select class="span12" id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputExternalID">externalID:</label>
                <div class="controls"><input class="span12" id="inputExternalID" type="text" name="externalID" value="" maxlength="255"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIP"><?php echo $sprache->haupt_ip;?>:</label>
                <div class="controls"><input class="span12" id="inputIP" type="text" name="ip" value="" maxlength="15" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIPs"><?php echo $sprache->zusatz_ip;?>:</label>
                <div class="controls"><textarea class="span12" id="inputIPs" name="altips" rows="5"></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFTPPort"><?php echo $sprache->ftp_port;?>:</label>
                <div class="controls"><input class="span12" id="inputFTPPort" type="text" name="ftpport" value="21" maxlength="5" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSSH2Port"><?php echo $sprache->ssh_port;?>:</label>
                <div class="controls"><input class="span12" id="inputSSH2Port" type="text" name="port" value="22" maxlength="5" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSSH2User"><?php echo $sprache->ssh_user;?>:</label>
                <div class="controls"><input class="span12" id="inputSSH2User" type="text" name="user" value="" maxlength="15" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyUse"><?php echo $sprache->keyuse;?></label>
                <div class="controls">
                    <select class="span12" id="inputKeyUse" name="publickey" onchange="SwitchShowHideRows(this.value)">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="N switch control-group">
                <label class="control-label" for="inputSSH2Pass"><?php echo $sprache->ssh_pass;?>:</label>
                <div class="controls"><input class="span12" id="inputSSH2Pass" type="password" name="pass" value="" maxlength="50"></div>
            </div>
            <div class="Y switch control-group">
                <label class="control-label" for="inputSSH2Key"><?php echo $sprache->keyname;?></label>
                <div class="controls"><input class="span12" id="inputSSH2Key" type="text" name="keyname" maxlength="20" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBit"><?php echo $sprache->os_bit;?>:</label>
                <div class="controls">
                    <select class="span12" id="inputBit" name="bit">
                        <option value="64">64</option>
                        <option value="32">32</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->desc;?>:</label>
                <div class="controls"><input class="span12" id="inputDesc" type="text" name="desc" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSlots"><?php echo $sprache->maxslots;?></label>
                <div class="controls"><input class="span12" id="inputSlots" type="text" name="maxslots" value="128" maxlength="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputServer"><?php echo $sprache->maxserver2;?></label>
                <div class="controls"><input class="span12" id="inputServer" type="text" name="maxserver" value="12" maxlength="4"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHT">Hyper Threading</label>
                <div class="controls">
                    <select class="span12" id="inputHT" name="hyperthreading">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCores">Cores</label>
                <div class="controls"><input class="span12" id="inputCores" type="text" name="cores" value="4" maxlength="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputAutoupdate">Autoupdate:</label>
                <div class="controls">
                    <select class="span12" id="inputAutoupdate" name="updates">
                        <option value="1">Vendor + Rsync/FTP Sync</option>
                        <option value="2">Vendor</option>
                        <option value="4">Rsync/FTP Sync</option>
                        <option value="3"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUpdateMinute"><?php echo $sprache->updateMinute;?></label>
                <div class="controls"><input class="span12" id="inputUpdateMinute" type="number" name="updateMinute" value="0" min="0" max="59"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSteamCmd">SteamCmd Account:</label>
                <div class="controls"><input class="span12" id="inputSteamCmd" type="text" name="steamAccount" value="anonymous"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSteamCmdPWD">SteamCmd <?php echo $sprache->password;?>:</label>
                <div class="controls"><input class="span12" id="inputSteamCmdPWD" type="text" name="steamPassword" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>