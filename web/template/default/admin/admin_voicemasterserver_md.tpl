<section class="content-header">
    <h1><?php echo $gsprache->voiceserver.' '.$gsprache->master;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><a href="admin.php?w=vm"><i class="fa fa-hdd-o"></i> <?php echo $gsprache->voiceserver.' '.$gsprache->master;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $ip;?></li>
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

                <form role="form" action="admin.php?w=vm&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input class="form-control" type="hidden" name="token" value="<?php echo token();?>">
                    <input class="form-control" type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputDescription"><?php echo $sprache->description;?></label>
                            <div class="controls"><input class="form-control" id="inputDescription" type="text" name="description" value="<?php echo $description;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label<?php if(isset($errors['active'])) echo ' has-error';?>" for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputManaged">Managed</label>
                            <div class="controls">
                                <select class="form-control" id="inputManaged" name="managedServer" onchange="textdrop('reseller');">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if($managedServer=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group <?php if($managedServer=='N') echo 'display_none';?>" id="reseller">
                            <label for="inputManagedForID"><?php echo $gsprache->reseller;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputManagedForID" name="managedForID">
                                    <?php foreach ($resellerIDs as $k=>$v){ ?><option value="<?php echo $k;?>" <?php if($managedForID==$k) echo 'selected="selected"';?>><?php echo $v;?></option><?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputExternalID">externalID</label>
                            <div class="controls"><input class="form-control" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID?>" maxlength="255"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label<?php if(isset($errors['autorestart'])) echo ' has-error';?>" for="inputAutoRestart"><?php echo $sprache->autorestart;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputAutoRestart" name="autorestart">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($autorestart=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultName"><?php echo $sprache->defaultname;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultName" type="text" name="defaultname" value="<?php echo $defaultname;?>"></div>
                        </div>

                        <?php if($type == 'ts3') { ?>

                        <div class="form-group<?php if(isset($errors['queryport'])) echo ' has-error';?>">
                            <label for="inputQueryPort"><?php echo $sprache->queryport;?></label>
                            <div class="controls"><input class="form-control" id="inputQueryPort" type="text" name="queryport" value="<?php echo $queryport;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['querypassword'])) echo ' has-error';?>">
                            <label for="inputQueryPassword"><?php echo $sprache->querypassword;?></label>
                            <div class="controls"><input class="form-control" id="inputQueryPassword" type="text" name="querypassword" value="<?php echo $querypassword;?>"/></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['filetransferport'])) echo ' has-error';?>">
                            <label for="inputFiletransferPort"><?php echo $sprache->filetransferport;?></label>
                            <div class="controls"><input class="form-control" id="inputFiletransferPort" type="text" name="filetransferport" value="<?php echo $filetransferport;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label<?php if(isset($errors['usedns'])) echo ' has-error';?>" for="inputUseDns"><?php echo $sprache->usedns;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputUseDns" name="usedns">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($usedns=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultDns"><?php echo $sprache->defaultdns;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultDns" type="text" name="defaultdns" value="<?php echo $defaultdns;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputTsdnsServerID"><?php echo $sprache->tsdnsServerID;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputTsdnsServerID" name="tsdnsServerID">
                                    <option value=""><?php echo $gsprache->no;?></option>
                                    <?php foreach ($externalDNS as $k=>$v) { ?>
                                    <option value="<?php echo $k;?>" <?php if($k==$tsdnsServerID) echo 'selected="selected"';?>><?php echo $v;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputExternalDefaultDNS"><?php echo $sprache->externalDefaultDNS;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputExternalDefaultDNS" name="externalDefaultDNS">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($externalDefaultDNS=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['maxserver'])) echo ' has-error';?>">
                            <label for="inputMaxServer"><?php echo $sprache->maxserver;?></label>
                            <div class="controls"><input class="form-control" id="inputMaxServer" type="text" name="maxserver" value="<?php echo $maxserver;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['maxslots'])) echo ' has-error';?>">
                            <label for="inputMaxSlots"><?php echo $sprache->maxslots;?></label>
                            <div class="controls"><input class="form-control" id="inputMaxSlots" type="text" name="maxslots" value="<?php echo $maxslots;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultWelcome"><?php echo $sprache->defaultwelcome;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultWelcome" type="text" name="defaultwelcome" value="<?php echo $defaultwelcome;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultHostBannerUrl"><?php echo $sprache->defaulthostbanner_url;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultHostBannerUrl" type="url" name="defaulthostbanner_url" value="<?php echo $defaulthostbanner_url;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultHostBannerGfxUrl"><?php echo $sprache->defaulthostbanner_gfx_url;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultHostBannerGfxUrl" type="url" name="defaulthostbanner_gfx_url" value="<?php echo $defaulthostbanner_gfx_url;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultHostButtonTooltip"><?php echo $sprache->defaulthostbutton_tooltip;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultHostButtonTooltip" type="text" name="defaulthostbutton_tooltip" value="<?php echo $defaulthostbutton_tooltip;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultHostButtonUrl"><?php echo $sprache->defaulthostbutton_url;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultHostButtonUrl" type="url" name="defaulthostbutton_url" value="<?php echo $defaulthostbutton_url;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultHostButtonGfxUrl"><?php echo $sprache->defaulthostbutton_gfx_url;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultHostButtonGfxUrl" type="url" name="defaulthostbutton_gfx_url" value="<?php echo $defaulthostbutton_gfx_url;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultFlexSlotsFree"><?php echo $sprache->defaultFlexSlotsFree;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultFlexSlotsFree" type="text" name="defaultFlexSlotsFree" value="<?php echo $defaultFlexSlotsFree;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultFlexSlotsPercent"><?php echo $sprache->defaultFlexSlotsPercent;?></label>
                            <div class="controls"><input class="form-control" id="inputDefaultFlexSlotsPercent" type="text" name="defaultFlexSlotsPercent" value="<?php echo $defaultFlexSlotsPercent;?>"></div>
                        </div>

                        <?php } ; if($addedby==1) { ?>

                        <div class="form-group">
                            <label><?php echo $sprache->rootserver;?></label>
                            <div class="controls"><?php echo $root;?></div>
                        </div>

                        <?php } else if ($addedby==2){ ?>

                        <div class="form-group">
                            <label for="inputServerDir"><?php echo $sprache->serverdir;?></label>
                            <div class="controls"><input class="form-control" id="inputServerDir" type="text" name="serverdir" value="<?php echo $serverdir;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['ip'])) echo ' has-error';?>">
                            <label for="inputSshIP"><?php echo $sprache->ssh_ip;?></label>
                            <div class="controls"><input class="form-control" id="inputSshIP" type="text" name="ip" maxlength="15" value="<?php echo $ip;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputIps"><?php echo $sprache->ips;?></label>
                            <div class="controls"><textarea class="form-control" id="inputIps" name="ips" rows="5" cols="23" ><?php echo $ips;?></textarea></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['port'])) echo ' has-error';?>">
                            <label for="inputSshPort"><?php echo $sprache->ssh_port;?></label>
                            <div class="controls"><input class="form-control" id="inputSshPort" type="text" name="port" maxlength="5" value="<?php echo $port;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['user'])) echo ' has-error';?>">
                            <label for="inputSshUser"><?php echo $sprache->ssh_user;?></label>
                            <div class="controls"><input class="form-control" id="inputSshUser" type="text" name="user" maxlength="15" value="<?php echo $user;?>"></div>
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
                            <div class="controls"><input class="form-control" id="inputSshPass" type="password" name="pass" value="<?php echo $pass;?>"></div>
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
                        <?php } ?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>