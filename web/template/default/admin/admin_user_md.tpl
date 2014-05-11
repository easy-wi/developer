<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=us"><?php echo $gsprache->user;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $cname;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->user;?></dt>
            <dd><?php echo $cname;?></dd>
            <dt>creationTime</dt>
            <dd><?php echo $creationTime;?></dd>
            <dt>updateTime</dt>
            <dd><?php echo $updateTime;?></dd>
            <dt><?php echo $sprache->accounttype;?></dt>
            <dd><?php ;if($accounttype=='r'){ echo $sprache->accounttype_reseller;}else if($accounttype=='a'){ echo $sprache->accounttype_admin;}else{ echo $sprache->accounttype_user;}?></dd>
            <dt><?php echo $gsprache->jobPending;?></dt>
            <dd><?php echo $jobPending;?></dd>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=us&amp;d=md&amp;id=<?php echo $id;?>&amp;r=us" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
        <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                        <option value="R" <?php if ($active=='R') echo 'selected="selected"';?>><?php echo $sprache->activeRegister;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSalutation"><?php echo $sprache->salutation;?></label>
                <div class="controls">
                    <select id="inputSalutation" name="salutation">
                        <option value="1"><?php echo $sprache->salutation2;?></option>
                        <option value="2" <?php if ($salutation==2) echo 'selected="selected"';?>><?php echo $sprache->salutation3;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCountry"><?php echo $sprache->country;?></label>
                <div class="controls">
                    <select id="inputCountry" name="flagmenu">
                        <?php foreach ($selectlanguages as $la) { ?>
                        <option value="<?php echo $la;?>" <?php if ($la==$country) echo 'selected="selected"'; ?>><?php echo $la;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFname"><?php echo $sprache->fname;?></label>
                <div class="controls">
                    <input id="inputFname" type="text" name="name" value="<?php echo $name;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputVname"><?php echo $sprache->vname;?></label>
                <div class="controls">
                    <input id="inputVname" type="text" name="vname" value="<?php echo $vname;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBirthday"><?php echo $sprache->birthday;?></label>
                <div class="controls">
                    <input id="inputBirthday" type="text" name="birthday" value="<?php echo $birthday;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMail"><?php echo $sprache->email;?>*</label>
                <div class="controls">
                    <input id="inputMail" type="email" name="mail" value="<?php echo $mail;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTel"><?php echo $sprache->tel;?></label>
                <div class="controls">
                    <input id="inputTel" type="tel" name="phone" value="<?php echo $phone;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFax">Fax</label>
                <div class="controls">
                    <input id="inputFax" type="tel" name="fax" value="<?php echo $fax;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHandy"><?php echo $sprache->han;?></label>
                <div class="controls">
                    <input id="inputHandy" type="tel" name="handy" value="<?php echo $handy;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCity"><?php echo $sprache->stadt;?></label>
                <div class="controls">
                    <input id="inputCity" type="text" name="city" value="<?php echo $city;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCityn"><?php echo $sprache->plz;?></label>
                <div class="controls">
                    <input id="inputCityn" type="text" name="cityn" value="<?php echo $cityn;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputStreet"><?php echo $sprache->str;?></label>
                <div class="controls">
                    <input id="inputStreet" type="text" name="street" value="<?php echo $street;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHnum"><?php echo $sprache->hnum;?></label>
                <div class="controls">
                    <input id="inputHnum" type="text" name="streetn" value="<?php echo $streetn;?>">
                </div>
            </div>
            <?php if ($reseller_id==0 or $id!=$admin_id) { ?>
            <div class="control-group">
                <label class="control-label" for="inputGroups"><?php echo $gsprache->groups;?></label>
                <div class="controls">
                    <select id="inputGroups" multiple="multiple" name="groups[]">
                        <?php foreach ($groups as $gid => $group){ ?><option value="<?php echo $gid;?>" <?php if(in_array($gid,$groupsAssigned)) echo 'selected="selected"';?>><?php echo $group;?></option><?php }?>
                    </select>
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputMailBackup"><?php echo $sprache->mail_backup;?></label>
                <div class="controls">
                    <input id="inputMailBackup" type="checkbox" name="mail_backup" value="Y" <?php if ($mail_backup=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMailServerDown"><?php echo $sprache->mail_serverdown;?></label>
                <div class="controls">
                    <input id="inputMailServerDown" type="checkbox" name="mail_serverdown" value="Y" <?php if ($mail_serverdown=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMailTicket"><?php echo $sprache->mail_ticket;?></label>
                <div class="controls">
                    <input id="inputMailTicket" type="checkbox" name="mail_ticket" value="Y" <?php if ($mail_ticket=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php if ($accounttype=='a' or $accounttype=='r') { ?>
            <div class="control-group">
                <label class="control-label" for="inputMailUpdate"><?php echo $sprache->mail_gsupdate;?></label>
                <div class="controls">
                    <input id="inputMailUpdate" type="checkbox" name="mail_gsupdate" value="Y" <?php if ($mail_gsupdate=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMailSecurity"><?php echo $sprache->mail_securitybreach;?></label>
                <div class="controls">
                    <input id="inputMailSecurity" type="checkbox" name="mail_securitybreach" value="Y" <?php if ($mail_securitybreach=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMailVserver"><?php echo $sprache->mail_vserver;?></label>
                <div class="controls">
                    <input id="inputMailVserver" type="checkbox" name="mail_vserver" value="Y" <?php if ($mail_vserver=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php } else if ($accounttype=='u') { ?>
            <div class="control-group">
                <label class="control-label" for="inputFastDl"><?php echo $sprache->fastdl2;?></label>
                <div class="controls">
                    <input id="inputMailTicket" type="url" name="fdlpath" value="<?php echo $fdlpath;?>">
                </div>
            </div>
            <?php }; if ($accounttype=='r') { ?>
            <div class="control-group">
                <label class="control-label" for="inputMaxUser"><?php echo $sprache->max." ".$gsprache->user;?></label>
                <div class="controls">
                    <input id="inputMaxUser" type="number" name="maxuser" value="<?php echo $maxuser;?>">
                </div>
            </div>
            <?php if($easywiModules['gs']) { ?>
            <div class="control-group">
                <label class="control-label" for="inputMaxGserver"><?php echo $sprache->max." ".$gsprache->gameserver;?></label>
                <div class="controls">
                    <input id="inputMaxGserver" type="number" name="maxgserver" value="<?php echo $maxgserver;?>">
                </div>
            </div>
            <?php }; if($easywiModules['vo']) { ?>
            <div class="control-group">
                <label class="control-label" for="inputMaxVoserver"><?php echo $sprache->max." ".$gsprache->voiceserver;?></label>
                <div class="controls">
                    <input id="inputMaxVoserver" type="number" name="maxvoiceserver" value="<?php echo $maxvoiceserver;?>">
                </div>
            </div>
            <?php }; if($easywiModules['ro']) { ?>
            <div class="control-group">
                <label class="control-label" for="inputMaxDedis"><?php echo $sprache->max." ".$gsprache->dedicated;?></label>
                <div class="controls">
                    <input id="inputMaxDedis" type="number" name="maxdedis" value="<?php echo $maxdedis;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxVirtual"><?php echo $sprache->max." ".$gsprache->virtual;?></label>
                <div class="controls">
                    <input id="inputMaxVirtual" type="number" name="maxvserver" value="<?php echo $maxvserver;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxUserMHZ"><?php echo $sprache->max." ".$rsprache->mhz;?></label>
                <div class="controls">
                    <input id="inputMaxUserMHZ" type="number" name="maxusermhz" value="<?php echo $maxusermhz;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxUserRam"><?php echo $sprache->max." ".$rsprache->ram;?></label>
                <div class="controls">
                    <input id="inputMaxUserRam" type="number" name="maxuserram" value="<?php echo $maxuserram;?>">
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputResellerUserActive"><?php echo $gsprache->reseller." ".$gsprache->user." ".$sprache->active;?></label>
                <div class="controls">
                    <select id="inputResellerUserActive" name="useractive">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($useractive=='N') echo 'selected="selected"'; ?> />><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <?php }?>
            <?php foreach(customColumns('U',$id) as $row){ ?>
            <div class="control-group">
                <label class="control-label" for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                <div class="controls">
                    <?php echo $row['input'];?>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inputMod"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputMod" type="submit"><i class="icon-white icon-edit"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>