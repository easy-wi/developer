<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li>TSDNS <?php echo $gsprache->master;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form name="form" class="form-horizontal" action="admin.php?w=vd&amp;d=ad&amp;r=vd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputAutoRestart"><?php echo $sprache->autorestart;?></label>
                <div class="controls">
                    <select id="inputAutoRestart" name="autorestart">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultDns"><?php echo $sprache->defaultdns;?></label>
                <div class="controls"><input id="inputDefaultDns" type="text" name="defaultdns"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputServerDir"><?php echo $sprache->serverdir;?></label>
                <div class="controls"><input id="inputServerDir" type="text" name="serverdir"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshIp"><?php echo $sprache->ssh_ip;?></label>
                <div class="controls"><input id="inputSshIp" type="text" name="ip" maxlength="15"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshPort"><?php echo $sprache->ssh_port;?></label>
                <div class="controls"><input id="inputSshPort" type="text" name="port" value="22" maxlength="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshUser"><?php echo $sprache->ssh_user;?></label>
                <div class="controls"><input id="inputSshUser" type="text" name="user" value="easy-wi" maxlength="15"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshPass"><?php echo $sprache->ssh_pass;?></label>
                <div class="controls"><input id="inputSshPass" type="password" name="pass"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyUse"><?php echo $sprache->keyuse;?></label>
                <div class="controls">
                    <select id="inputKeyUse" name="publickey">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyName"><?php echo $sprache->keyname;?></label>
                <div class="controls"><input id="inputKeyName" type="text" name="keyname" maxlength="20" value="id_rsa"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputOsBit"><?php echo $sprache->os_bit;?></label>
                <div class="controls">
                    <select id="inputOsBit" name="bit">
                        <option value="64">64</option>
                        <option value="32">32</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                <div class="controls"><textarea id="inputDesc" name="description"></textarea></div>
            </div>

            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>