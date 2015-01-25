<section class="content-header">
    <h1>TSDNS <?php echo $gsprache->master;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><a href="admin.php?w=vd"><i class="fa fa-hdd-o"></i> TSDNS <?php echo $gsprache->master;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $ssh2ip;?></li>
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
            <div class="box box-primary">

                <form role="form" action="admin.php?w=vd&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group<?php if(isset($errors['active'])) echo ' has-error';?>">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputExternalID"><?php echo $gsprache->externalID;?></label>
                            <div class="controls"><input class="form-control" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID;?>" maxlength="255"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['autorestart'])) echo ' has-error';?>">
                            <label for="inputAutoRestart"><?php echo $sprache->autorestart;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputAutoRestart" name="autorestart">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($autorestart=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultDns"><?php echo $sprache->defaultdns;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultDns" type="text" name="defaultdns" value="<?php echo $defaultdns;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputServerDir"><?php echo $sprache->serverdir;?></label>
                            <div class="controls"><input class="form-control" id="inputServerDir" type="text" name="serverdir" value="<?php echo $serverdir;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['ip'])) echo ' has-error';?>">
                            <label for="inputSshIP"><?php echo $sprache->ssh_ip;?></label>
                            <div class="controls"><input class="form-control" id="inputSshIP" type="text" name="ip" maxlength="15" value="<?php echo $ssh2ip;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['port'])) echo ' has-error';?>">
                            <label for="inputSshPort"><?php echo $sprache->ssh_port;?></label>
                            <div class="controls"><input class="form-control" id="inputSshPort" type="text" name="port" maxlength="5" value="<?php echo $ssh2port;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['user'])) echo ' has-error';?>">
                            <label for="inputSshUser"><?php echo $sprache->ssh_user;?></label>
                            <div class="controls"><input class="form-control" id="inputSshUser" type="text" name="user" maxlength="15" value="<?php echo $ssh2user;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['publickey'])) echo ' has-error';?>">
                            <label for="inputKeyUse"><?php echo $sprache->keyuse;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputKeyUse" name="publickey">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="B" <?php if ($publickey=="B") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?> + <?php echo $gsprache->password;?></option>
                                    <option value="N" <?php if($publickey=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['pass'])) echo ' has-error';?>">
                            <label for="inputSshPass"><?php echo $sprache->ssh_pass;?></label>
                            <div class="controls"><input class="form-control" id="inputSshPass" type="password" name="pass" value="<?php echo $ssh2password;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['keyname'])) echo ' has-error';?>">
                            <label for="inputKeyName"><?php echo $sprache->keyname;?></label>
                            <div class="controls"><input class="form-control" id="inputKeyName" type="text" name="keyname" maxlength="20" value="<?php echo $keyname;?>"/></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['bit'])) echo ' has-error';?>">
                            <label for="inputOsBit"><?php echo $sprache->os_bit;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputOsBit" name="bit">
                                    <option value="32">32</option>
                                    <option value="64" <?php if($bit=='64') echo 'selected="selected"';?>>64</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputMaxDns"><?php echo $sprache->maxserver;?></label>
                            <div class="controls"><input class="form-control" id="inputMaxDns" type="number" name="maxDns" maxlength="10" value="<?php echo $maxDns;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDesc"><?php echo $sprache->description;?></label>
                            <div class="controls"><textarea class="form-control" id="inputDesc" name="description"><?php echo $description;?></textarea></div>
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