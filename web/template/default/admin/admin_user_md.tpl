<section class="content-header">
    <h1><?php echo $gsprache->user;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=us"><i class="fa fa-user"></i> <?php echo $gsprache->user;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $cname;?></li>
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

                <form role="form" action="admin.php?w=us&amp;d=md&amp;id=<?php echo $id;?>&amp;r=us" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputExternalID">externalID</label>
                            <div class="controls">
                                <input class="form-control" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if ($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                    <option value="R" <?php if ($active=='R') echo 'selected="selected"';?>><?php echo $sprache->activeRegister;?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group<?php if(isset($errors['mail'])) echo ' has-error';?>">
                            <label for="inputMail"><?php echo $sprache->email;?>*</label>
                            <div class="controls">
                                <input class="form-control" id="inputMail" type="email" name="mail" value="<?php echo $mail;?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputSalutation"><?php echo $sprache->salutation;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputSalutation" name="salutation">
                                    <option value="1"><?php echo $sprache->salutation2;?></option>
                                    <option value="2" <?php if ($salutation==2) echo 'selected="selected"';?>><?php echo $sprache->salutation3;?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputFname"><?php echo $sprache->fname;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputFname" type="text" name="name" value="<?php echo $name;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputVname"><?php echo $sprache->vname;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputVname" type="text" name="vname" value="<?php echo $vname;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputCountry"><?php echo $sprache->country;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputCountry" name="country">
                                    <?php foreach ($selectlanguages as $la) { ?>
                                    <option value="<?php echo $la;?>" <?php if ($la==$country) echo 'selected="selected"'; ?>><?php echo $la;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputBirthday"><?php echo $sprache->birthday;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputBirthday" type="text" name="birthday" value="<?php echo $birthday;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputTel"><?php echo $sprache->tel;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputTel" type="tel" name="phone" value="<?php echo $phone;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputFax">Fax</label>
                            <div class="controls">
                                <input class="form-control" id="inputFax" type="tel" name="fax" value="<?php echo $fax;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputHandy"><?php echo $sprache->han;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputHandy" type="tel" name="handy" value="<?php echo $handy;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputCity"><?php echo $sprache->stadt;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputCity" type="text" name="city" value="<?php echo $city;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputCityn"><?php echo $sprache->plz;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputCityn" type="text" name="cityn" value="<?php echo $cityn;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputStreet"><?php echo $sprache->str;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputStreet" type="text" name="street" value="<?php echo $street;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputHnum"><?php echo $sprache->hnum;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputHnum" type="text" name="streetn" value="<?php echo $streetn;?>">
                            </div>
                        </div>
                        <?php if ($reseller_id==0 or $id!=$admin_id) { ?>
                        <div class="form-group">
                            <label for="inputGroups"><?php echo $gsprache->groups;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputGroups" multiple="multiple" name="groups[]">
                                    <?php foreach ($groups[$accountType] as $gid => $group){ ?><option value="<?php echo $gid;?>" <?php if(in_array($gid,$groupsAssigned)) echo 'selected="selected"';?>><?php echo $group;?></option><?php }?>
                                </select>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label for="inputMailBackup">
                                <input id="inputMailBackup" type="checkbox" name="mail_backup" value="Y" <?php if ($mail_backup=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_backup;?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="inputMailServerDown">
                                <input id="inputMailServerDown" type="checkbox" name="mail_serverdown" value="Y" <?php if ($mail_serverdown=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_serverdown;?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="inputMailTicket">
                                <input id="inputMailTicket" type="checkbox" name="mail_ticket" value="Y" <?php if ($mail_ticket=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_ticket;?>
                            </label>
                        </div>
                        <?php if ($accountType=='a' or $accountType=='r') { ?>
                        <div class="form-group">
                            <label for="inputMailUpdate">
                                <input id="inputMailUpdate" type="checkbox" name="mail_gsupdate" value="Y" <?php if ($mail_gsupdate=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_gsupdate;?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="inputMailSecurity">
                                <input id="inputMailSecurity" type="checkbox" name="mail_securitybreach" value="Y" <?php if ($mail_securitybreach=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_securitybreach;?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="inputMailVserver">
                                <input id="inputMailVserver" type="checkbox" name="mail_vserver" value="Y" <?php if ($mail_vserver=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_vserver;?>
                            </label>
                        </div>
                        <?php } else if ($accountType=='u') { ?>
                        <div class="form-group">
                            <label for="inputFastDl"><?php echo $sprache->fastdl2;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMailTicket" type="url" name="fdlpath" value="<?php echo $fdlpath;?>">
                            </div>
                        </div>
                        <?php }; if ($accountType=='r') { ?>
                        <div class="form-group">
                            <label for="inputMaxUser"><?php echo $sprache->max.' '.$gsprache->user;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxUser" type="number" name="maxuser" value="<?php echo $maxuser;?>">
                            </div>
                        </div>
                        <?php if($easywiModules['gs']) { ?>
                        <div class="form-group">
                            <label for="inputMaxGserver"><?php echo $sprache->max.' '.$gsprache->gameserver;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxGserver" type="number" name="maxgserver" value="<?php echo $maxgserver;?>">
                            </div>
                        </div>
                        <?php }; if($easywiModules['vo']) { ?>
                        <div class="form-group">
                            <label for="inputMaxVoserver"><?php echo $sprache->max.' '.$gsprache->voiceserver;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxVoserver" type="number" name="maxvoserver" value="<?php echo $maxvoserver;?>">
                            </div>
                        </div>
                        <?php }; if($easywiModules['ro']) { ?>
                        <div class="form-group">
                            <label for="inputMaxDedis"><?php echo $sprache->max.' '.$gsprache->dedicated;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxDedis" type="number" name="maxdedis" value="<?php echo $maxdedis;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputMaxVirtual"><?php echo $sprache->max.' '.$gsprache->virtual;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxVirtual" type="number" name="maxvserver" value="<?php echo $maxvserver;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputMaxUserMHZ"><?php echo $sprache->max.' '.$rsprache->mhz;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxUserMHZ" type="number" name="maxusermhz" value="<?php echo $maxusermhz;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputMaxUserRam"><?php echo $sprache->max.' '.$rsprache->ram;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputMaxUserRam" type="number" name="maxuserram" value="<?php echo $maxuserram;?>">
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label for="inputResellerUserActive"><?php echo $gsprache->reseller.' '.$gsprache->user.' '.$sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputResellerUserActive" name="useractive">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if ($useractive=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>
                        <?php }?>

                        <?php foreach(customColumns('U',$id) as $row){ ?>
                        <div class="form-group">
                            <label for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                            <div class="controls">
                                <input class="form-control" id="inputCustom-<?php echo $row['customID'];?>" type="<?php echo $row['type']=='V' ? 'text' : 'number';?>" name="<?php echo $row['name'];?>" value="<?php echo $row['value'];?>" maxlength="<?php echo $row['length'];?>">
                            </div>
                        </div>
                        <?php }?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>