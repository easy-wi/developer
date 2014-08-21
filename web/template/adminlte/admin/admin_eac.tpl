<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?> Easy Anti Cheat</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->gameserver;?></li>
		<li class="active">Easy Anti Cheat</li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">
	
    <div class="box box-info">	
        <div class="box-body">
        <form role="form" action="admin.php?w=ea&amp;r=ea" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="form-group">
                <label for="inputActive"><?php echo $sprache->active;?></label>
                    <select class="form-control" id="inputActive" name="active">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($eac_active=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
            </div>
            <div class="form-group">
                <label for="inputType"><?php echo $gssprache->type;?></label>
                    <select class="form-control" id="inputType" name="type" onchange="SwitchShowHideRows(this.value)">
                        <option value="M">MySQL</option>
                        <option value="S" <?php if ($type=='S') echo 'selected="selected"'; ?>>SSH2</option>
                    </select>
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> form-group">
                <label for="inputIP"><?php echo $sprache->haupt_ip;?></label>
                <input class="form-control" id="inputIP" type="text" name="ip" value="<?php echo $eac_ip;?>" maxlength="15" required="required">
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> form-group">
                <label for="inputPortSSH2"><?php echo $sprache->ssh_port;?></label>
                <input class="form-control" id="inputPortSSH2" type="text" name="port" value="<?php echo $eac_port;?>" maxlength="5" required="required">
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> form-group">
                <label for="inputUserSSH2"><?php echo $sprache->ssh_user;?></label>
                <input class="form-control" id="inputUserSSH2" type="text" name="user" value="<?php echo $eac_user;?>" maxlength="15" required="required">
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> form-group">
                <label for="inputKeyuseSSH2"><?php echo $sprache->keyuse;?></label>
                    <select class="form-control" id="inputKeyuseSSH2" name="publickey">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="B" <?php if ($eac_publickey=="B") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                        <option value="N" <?php if ($eac_publickey=="N") echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> form-group">
                <label for="inputPassSSH2"><?php echo $sprache->ssh_pass;?></label>
                <input class="form-control" id="inputPassSSH2" type="password" name="pass" value="<?php echo $eac_pass;?>">
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> form-group">
                <label for="inputKeynameSSH2"><?php echo $sprache->keyname;?></label>
                <input class="form-control" id="inputKeynameSSH2" type="text" name="keyname" maxlength="20" value="<?php echo $eac_keyname;?>">
            </div>
            <div class="S switch<?php if($type=='M') echo 'display_none';?> form-group">
                <label for="inputCFGDir"><?php echo $sprache->cfgdir;?></label>
                <input class="form-control" id="inputCFGDir" type="text" name="cfgdir" value="<?php echo $eac_cfgdir;?>" >
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> form-group">
                <label for="inputMysqlServer">MySQL <?php echo $gssprache->server;?></label>
                <input class="form-control" id="inputMysqlServer" type="text" name="mysql_server" value="<?php echo $mysql_server;?>">
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> form-group">
                <label for="inputMysqlPort">MySQL <?php echo $gssprache->port;?></label>
                <input class="form-control" id="inputMysqlPort" type="text" name="mysql_port" value="<?php echo $mysql_port;?>">
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> form-group">
                <label for="inputMysqlDB">MySQL <?php echo $mysprache->dbname;?></label>
                <input class="form-control" id="inputMysqlDB" type="number" name="mysql_db" value="<?php echo $mysql_db;?>">
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> form-group">
                <label for="inputMysqlTable">MySQL <?php echo $mysprache->table;?></label>
                <input class="form-control" id="inputMysqlTable" type="number" name="mysql_table" value="<?php echo $mysql_table;?>">
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> form-group">
                <label for="inputMysqlUser">MySQL <?php echo $mysprache->user;?></label>
                <input class="form-control" id="inputMysqlUser" type="number" name="mysql_user" value="<?php echo $mysql_user;?>">
            </div>
            <div class="M switch<?php if($type=='S') echo 'display_none';?> form-group">
                <label for="inputMysqlPassword">MySQL <?php echo $mysprache->password;?></label>
                <input class="form-control" id="inputMysqlPassword" type="number" name="mysql_password" value="<?php echo $mysql_password;?>">
            </div>
            <div class="form-group">
                <label for="inputSrcdsCOD4War">SRCDS <?php echo $gssprache->war?></label>
                <input class="form-control" id="inputSrcdsCOD4War" type="checkbox" name="normal_3" value="Y" <?php if ($normal_3=='Y') echo 'checked="checked"'; ?>>
            </div>
            <div class="form-group">
                <label for="inputSrcdsCOD4Pub">SRCDS Public</label>
                <input class="form-control" id="inputSrcdsCOD4Pub" type="checkbox" name="normal_4" value="Y" <?php if ($normal_4=='Y') echo 'checked="checked"'; ?>>
            </div>
            <div class="form-group">
                <label for="inputHLDSWar">HLDS <?php echo $gssprache->war?></label>
                <input class="form-control" id="inputHLDSWar" type="checkbox" name="hlds_3" value="Y" <?php if ($hlds_3=='Y') echo 'checked="checked"'; ?>>
            </div>
            <div class="form-group">
                <label for="inputHLDSWPub">HLDS Public</label>
                <input class="form-control" id="inputHLDSWPub" type="checkbox" name="hlds_4" value="Y" <?php if ($hlds_4=='Y') echo 'checked="checked"'; ?>>
            </div>
            <div class="form-group">
                <label for="inputHLDSWWar32">HLDS <?php echo $gssprache->war?> 32Bit</label>
                <input class="form-control" id="inputHLDSWWar32" type="checkbox" name="hlds_5" value="Y" <?php if ($hlds_5=='Y') echo 'checked="checked"'; ?>>
            </div>
            <div class="form-group">
                <label for="inputHLDSWPub32">HLDS Public 32Bit</label>
                <input class="form-control" id="inputHLDSWPub32" type="checkbox" name="hlds_6" value="Y" <?php if ($hlds_6=='Y') echo 'checked="checked"'; ?>>
            </div>
    
                <label for="inputEdit"></label>
                <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
        </form>
        </div>
    </div>
</section>