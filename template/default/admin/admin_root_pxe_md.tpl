<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=rp">PXE</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=rp&amp;d=md&amp;id=<?php echo $id;?>&amp;r=rp" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($active=="N") echo 'selected="selected";'?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshIP"><?php echo $sprache->ssh_ip;?>:</label>
                <div class="controls"><input id="inputSshIP" type="text" name="ip" value="<?php echo $ip;?>" maxlength="15"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshPort"><?php echo $sprache->ssh_port;?>:</label>
                <div class="controls"><input id="inputSshPort" type="number" name="port" value="<?php echo $port;?>" maxlength="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshUser"><?php echo $sprache->ssh_user;?>:</label>
                <div class="controls"><input id="inputSshUser" type="text" name="user" value="<?php echo $user;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshPass"><?php echo $sprache->ssh_pass;?>:</label>
                <div class="controls"><input id="inputSshPass" type="password" name="pass" value="<?php echo $pass;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyUse"><?php echo $sprache->keyuse;?></label>
                <div class="controls">
                    <select id="inputKeyUse" name="publickey">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($publickey=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                        <option value="B" <?php if ($publickey=="B") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyName"><?php echo $sprache->keyname;?></label>
                <div class="controls"><input id="inputKeyName" type="text" name="keyname" maxlength="20" value="<?php echo $keyname;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPxeFolder"><?php echo $sprache->PXEFolder;?>:</label>
                <div class="controls"><input id="inputPxeFolder" type="text" name="PXEFolder" value="<?php echo $PXEFolder;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->description;?>:</label>
                <div class="controls"><input id="inputDesc" type="text" name="description" value="<?php echo $description;?>"></div>
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