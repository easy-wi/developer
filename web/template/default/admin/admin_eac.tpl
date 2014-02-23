<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active">Easy Anti Cheat</li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=ea&amp;r=ea" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input id="" type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($eac_active=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputType"><?php echo $gssprache->type;?></label>
                <div class="controls">
                    <select id="inputType" name="type" onchange="SwitchShowHideRows(this.value)">
                        <option value="M">MySQL</option>
                        <option value="S" <?php if ($type=='S') echo 'selected="selected"'; ?>>SSH2</option>
                    </select>
                </div>
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> control-group">
                <label class="control-label" for="inputIP"><?php echo $sprache->haupt_ip;?></label>
                <div class="controls"><input id="inputIP" type="text" name="ip" value="<?php echo $eac_ip;?>" maxlength="15" required="required"></div>
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> control-group">
                <label class="control-label" for="inputPortSSH2"><?php echo $sprache->ssh_port;?></label>
                <div class="controls"><input id="inputPortSSH2" type="text" name="port" value="<?php echo $eac_port;?>" maxlength="5" required="required"></div>
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> control-group">
                <label class="control-label" for="inputUserSSH2"><?php echo $sprache->ssh_user;?></label>
                <div class="controls"><input id="inputUserSSH2" type="text" name="user" value="<?php echo $eac_user;?>" maxlength="15" required="required"></div>
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> control-group">
                <label class="control-label" for="inputKeyuseSSH2"><?php echo $sprache->keyuse;?></label>
                <div class="controls">
                    <select id="inputKeyuseSSH2" name="publickey">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="B" <?php if ($eac_publickey=="B") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                        <option value="N" <?php if ($eac_publickey=="N") echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> control-group">
                <label class="control-label" for="inputPassSSH2"><?php echo $sprache->ssh_pass;?></label>
                <div class="controls"><input id="inputPassSSH2" type="password" name="pass" value="<?php echo $eac_pass;?>"></div>
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> control-group">
                <label class="control-label" for="inputKeynameSSH2"><?php echo $sprache->keyname;?></label>
                <div class="controls"><input id="inputKeynameSSH2" type="text" name="keyname" maxlength="20" value="<?php echo $eac_keyname;?>"></div>
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> control-group">
                <label class="control-label" for="inputCFGDir"><?php echo $sprache->cfgdir;?></label>
                <div class="controls"><input id="inputCFGDir" type="text" name="cfgdir" value="<?php echo $eac_cfgdir;?>" ></div>
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> control-group">
                <label class="control-label" for="inputMysqlServer">MySQL <?php echo $gssprache->server;?></label>
                <div class="controls"><input id="inputMysqlServer" type="text" name="mysql_server" value="<?php echo $mysql_server;?>"></div>
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> control-group">
                <label class="control-label" for="inputMysqlPort">MySQL <?php echo $gssprache->port;?></label>
                <div class="controls"><input id="inputMysqlPort" type="text" name="mysql_port" value="<?php echo $mysql_port;?>"></div>
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> control-group">
                <label class="control-label" for="inputMysqlDB">MySQL <?php echo $mysprache->dbname;?></label>
                <div class="controls"><input id="inputMysqlDB" type="number" name="mysql_db" value="<?php echo $mysql_db;?>"></div>
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> control-group">
                <label class="control-label" for="inputMysqlTable">MySQL <?php echo $mysprache->table;?></label>
                <div class="controls"><input id="inputMysqlTable" type="number" name="mysql_table" value="<?php echo $mysql_table;?>"></div>
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> control-group">
                <label class="control-label" for="inputMysqlUser">MySQL <?php echo $mysprache->user;?></label>
                <div class="controls"><input id="inputMysqlUser" type="number" name="mysql_user" value="<?php echo $mysql_user;?>"></div>
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> control-group">
                <label class="control-label" for="inputMysqlPassword">MySQL <?php echo $mysprache->password;?></label>
                <div class="controls"><input id="inputMysqlPassword" type="number" name="mysql_password" value="<?php echo $mysql_password;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSrcdsCOD4War">SRCDS <?php echo $gssprache->war?></label>
                <div class="controls"><input id="inputSrcdsCOD4War" type="checkbox" name="normal_3" value="Y" <?php if ($normal_3=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSrcdsCOD4Pub">SRCDS Public</label>
                <div class="controls"><input id="inputSrcdsCOD4Pub" type="checkbox" name="normal_4" value="Y" <?php if ($normal_4=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHLDSWar">HLDS <?php echo $gssprache->war?></label>
                <div class="controls"><input id="inputHLDSWar" type="checkbox" name="hlds_3" value="Y" <?php if ($hlds_3=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHLDSWPub">HLDS Public</label>
                <div class="controls"><input id="inputHLDSWPub" type="checkbox" name="hlds_4" value="Y" <?php if ($hlds_4=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHLDSWWar32">HLDS <?php echo $gssprache->war?> 32Bit</label>
                <div class="controls"><input id="inputHLDSWWar32" type="checkbox" name="hlds_5" value="Y" <?php if ($hlds_5=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHLDSWPub32">HLDS Public 32Bit</label>
                <div class="controls"><input id="inputHLDSWPub32" type="checkbox" name="hlds_6" value="Y" <?php if ($hlds_6=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls"><button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button></div>
            </div>
        </form>
    </div>
</div>