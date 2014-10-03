<section class="content-header">
    <h1>Easy Anti Cheat</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
		<li class="active"><i class="fa fa-eye"></i> Easy Anti Cheat</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=ea&amp;r=ea" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">
                        <h3>Easy Anti Cheat <?php echo $gsprache->server;?></h3>

                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <select class="form-control" id="inputActive" name="active">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($eac_active=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputType"><?php echo $gssprache->type;?></label>
                            <select class="form-control" id="inputType" name="type" onchange="SwitchShowHideRows(this.value)">
                                <option value="M">MySQL</option>
                                <option value="S" <?php if ($type=='S') echo 'selected="selected"';?>>SSH2</option>
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
                                <option value="B" <?php if ($eac_publickey=="B") echo 'selected="selected"';?>><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                                <option value="N" <?php if ($eac_publickey=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
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
                            <input class="form-control" id="inputMysqlPort" type="number" name="mysql_port" value="<?php echo $mysql_port;?>" min="1" max="65535">
                        </div>

                        <div class="M switch<?php if($type=='S') echo 'display_none';?> form-group">
                            <label for="inputMysqlDB">MySQL <?php echo $mysprache->dbname;?></label>
                            <input class="form-control" id="inputMysqlDB" type="text" name="mysql_db" value="<?php echo $mysql_db;?>">
                        </div>

                        <div class="M switch<?php if($type=='S') echo 'display_none';?> form-group">
                            <label for="inputMysqlTable">MySQL <?php echo $mysprache->table;?></label>
                            <input class="form-control" id="inputMysqlTable" type="text" name="mysql_table" value="<?php echo $mysql_table;?>">
                        </div>

                        <div class="M switch<?php if($type=='S') echo 'display_none';?> form-group">
                            <label for="inputMysqlUser">MySQL <?php echo $mysprache->user;?></label>
                            <input class="form-control" id="inputMysqlUser" type="text" name="mysql_user" value="<?php echo $mysql_user;?>">
                        </div>

                        <div class="M switch<?php if($type=='S') echo 'display_none';?> form-group">
                            <label for="inputMysqlPassword">MySQL <?php echo $mysprache->password;?></label>
                            <input class="form-control" id="inputMysqlPassword" type="text" name="mysql_password" value="<?php echo $mysql_password;?>">
                        </div>

                        <h3>Easy Anti Cheat <?php echo $gsprache->settings;?></h3>
                        <div class="checkbox">
                            <label>
                                <input id="inputSrcdsCOD4War" type="checkbox" name="normal_3" value="Y" <?php if ($normal_3=='Y') echo 'checked="checked"';?>>
                                SRCDS <?php echo $gssprache->war;?>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input id="inputSrcdsCOD4Pub" type="checkbox" name="normal_4" value="Y" <?php if ($normal_4=='Y') echo 'checked="checked"';?>>
                                SRCDS Public
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input id="inputHLDSWar" type="checkbox" name="hlds_3" value="Y" <?php if ($hlds_3=='Y') echo 'checked="checked"';?>>
                                HLDS <?php echo $gssprache->war;?>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input id="inputHLDSWPub" type="checkbox" name="hlds_4" value="Y" <?php if ($hlds_4=='Y') echo 'checked="checked"';?>>
                                HLDS Public
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input id="inputHLDSWWar32" type="checkbox" name="hlds_5" value="Y" <?php if ($hlds_5=='Y') echo 'checked="checked"';?>>
                                HLDS <?php echo $gssprache->war;?> 32Bit
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input id="inputHLDSWPub32" type="checkbox" name="hlds_6" value="Y" <?php if ($hlds_6=='Y') echo 'checked="checked"';?>>
                                HLDS Public 32Bit
                            </label>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    window.onDomReady = initReady;

    function initReady(fn) {
        if(document.addEventListener) {
            document.addEventListener("DOMContentLoaded", fn, false);
        } else {
            document.onreadystatechange = function() {
                readyState(fn);
            }
        }
    }

    function readyState(func) {
        if(document.readyState == "interactive" || document.readyState == "complete") {
            func();
        }
    }

    window.onDomReady(onReady); function onReady() {
        SwitchShowHideRows('init_ready');
    }
</script>