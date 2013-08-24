<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=rd">DHCP</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=rd&amp;d=md&amp;id=<?php echo $id;?>&amp;r=rd" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" class="span11" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($active=="N") echo 'selected="selected";'?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshIP"><?php echo $sprache->ssh_ip;?></label>
                <div class="controls"><input id="inputSshIP" class="span11" type="text" name="ip" value="<?php echo $ip;?>" maxlength="15"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshPort"><?php echo $sprache->ssh_port;?></label>
                <div class="controls"><input id="inputSshPort" class="span11" type="number" name="port" value="<?php echo $port;?>" maxlength="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshUser"><?php echo $sprache->ssh_user;?></label>
                <div class="controls"><input id="inputSshUser" class="span11" type="text" name="user" value="<?php echo $user;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshPass"><?php echo $sprache->ssh_pass;?></label>
                <div class="controls"><input id="inputSshPass" class="span11" type="password" name="pass" value="<?php echo $pass;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyUse"><?php echo $sprache->keyuse;?></label>
                <div class="controls">
                    <select id="inputKeyUse" class="span11" name="publickey">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($publickey=="N") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyName"><?php echo $sprache->keyname;?></label>
                <div class="controls"><input id="inputKeyName" class="span11" type="text" name="keyname" maxlength="20" value="<?php echo $keyname;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                <div class="controls"><input id="inputDesc" class="span11" type="text" name="description" value="<?php echo $description;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputNetMask"><?php echo $sprache->netmask;?></label>
                <div class="controls"><input id="inputNetMask" class="span11" type="text" name="netmask" value="<?php echo $netmask;?>" maxlength="15"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->startCmd;?></label>
                <div class="controls"><input id="inputDesc" class="span11" type="text" name="startCmd" value="<?php echo $startCmd;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDhcpFile"><?php echo $sprache->dhcpFile;?></label>
                <div class="controls"><input id="inputDhcpFile" class="span11" type="text" name="dhcpFile" value="<?php echo $dhcpFile;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIps"><?php echo $sprache->ips;?></label>
                <div class="controls"><textarea id="inputIps" class="span11" name="ips" rows="5" cols="23" ><?php echo $ips;?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSubnet">Subnet Options</label>
                <div class="controls">
                    <textarea id="inputSubnet" class="span11" name="subnetOptions" rows="5"><?php echo $subnetOptions;?></textarea>
                </div>
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