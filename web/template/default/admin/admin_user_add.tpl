<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=us"><?php echo $gsprache->user;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=us&amp;d=ad&amp;r=us" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
        <input type="hidden" name="token" value="<?php echo token();?>">
        <input type="hidden" name="action" value="ad">
        <div class="control-group">
            <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
            <div class="controls">
                <select id="inputActive" name="active">
                    <option value="Y"><?php echo $gsprache->yes;?></option>
                    <option value="N"><?php echo $gsprache->no;?></option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputSalutation"><?php echo $sprache->salutation;?></label>
            <div class="controls">
                <select id="inputSalutation" name="salutation">
                    <option value="1"><?php echo $sprache->salutation2;?></option>
                    <option value="2"><?php echo $sprache->salutation3;?></option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputCountry"><?php echo $sprache->country;?></label>
            <div class="controls">
                <select id="inputCountry" name="flagmenu">
                    <?php foreach ($selectlanguages as $la) { ?>
                    <option value="<?php echo $la;?>"><?php echo $la;?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputFname"><?php echo $sprache->fname;?></label>
            <div class="controls">
                <input id="inputFname" type="text" name="name" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputVname"><?php echo $sprache->vname;?></label>
            <div class="controls">
                <input id="inputVname" type="text" name="vname" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputBirthday"><?php echo $sprache->birthday;?></label>
            <div class="controls">
                <input id="inputBirthday" type="text" name="birthday" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputMail"><?php echo $sprache->email;?>*</label>
            <div class="controls">
                <input id="inputMail" type="email" name="mail" value="" required>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputTel"><?php echo $sprache->tel;?></label>
            <div class="controls">
                <input id="inputTel" type="tel" name="phone" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputFax">Fax</label>
            <div class="controls">
                <input id="inputFax" type="tel" name="fax" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputHandy"><?php echo $sprache->han;?></label>
            <div class="controls">
                <input id="inputHandy" type="tel" name="handy" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputCity"><?php echo $sprache->stadt;?></label>
            <div class="controls">
                <input id="inputCity" type="text" name="city" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputCityn"><?php echo $sprache->plz;?></label>
            <div class="controls">
                <input id="inputCityn" type="text" name="cityn" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputStreet"><?php echo $sprache->str;?></label>
            <div class="controls">
                <input id="inputStreet" type="text" name="street" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputHnum"><?php echo $sprache->hnum;?></label>
            <div class="controls">
                <input id="inputHnum" type="text" name="streetn" value="">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputPassword"><?php echo $sprache->wipsw;?>*</label>
            <div class="controls">
                <input id="inputPassword" type="text" name="security" value="<?php echo $randompass;?>" required>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputAccounttype"><?php echo $sprache->accounttype;?></label>
            <div class="controls">
                <select id="inputAccounttype" name="accounttype" onchange="SwitchShowHideRows(this.value)">
                    <option value="u"><?php echo $sprache->accounttype_user;?></option>
                    <?php if($reseller_id=="0" and $pa['user']) { ?><option value="a"><?php echo $sprache->accounttype_admin;?></option><?php }?>
                    <?php if($reseller_id=="0" or $admin_id==$reseller_id) { ?><option value="r"><?php echo $sprache->accounttype_reseller;?></option><?php }?>
                </select>
            </div>
        </div>
        <div class="u switch control-group">
            <label class="control-label" for="inputGroupsU"><?php echo $gsprache->groups;?></label>
            <div class="controls">
                <select id="inputGroupsU" multiple="multiple" name="groups_u[]">
                    <?php foreach ($groups['u'] as $gid => $group){ ?><option value="<?php echo $gid;?>" <?php if(isset($defaultGroups['u'][$gid])) echo 'selected="selected"';?>><?php echo $group;?></option><?php }?>
                </select>
            </div>
        </div>
        <?php if($reseller_id=="0" or $admin_id==$reseller_id) { ?>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputGroupsR"><?php echo $gsprache->groups;?></label>
            <div class="controls">
                <select id="inputGroupsR" multiple="multiple" name="groups_r[]">
                    <?php foreach ($groups['r'] as $gid => $group){ ?><option value="<?php echo $gid;?>" <?php if(isset($defaultGroups['r'][$gid])) echo 'selected="selected"';?>><?php echo $group;?></option><?php }?>
                </select>
            </div>
        </div>
        <?php }?>
        <?php if($reseller_id=="0" and $pa['user']) { ?>
        <div class="a display_none switch control-group">
            <label class="control-label" for="inputGroupsA"><?php echo $gsprache->groups;?></label>
            <div class="controls">
                <select id="inputGroupsA" multiple="multiple" name="groups_a[]">
                    <?php foreach ($groups['a'] as $gid => $group){ ?><option value="<?php echo $gid;?>" <?php if(isset($defaultGroups['a'][$gid])) echo 'selected="selected"';?>><?php echo $group;?></option><?php }?>
                </select>
            </div>
        </div>
        <?php }?>
        <?php if($prefix1=="N") { ?>
        <div class="u switch control-group">
            <label class="control-label" for="inputUCname"><?php echo $gsprache->user;?></label>
            <div class="controls">
                <input id="inputUCname" type="text" name="cname" value="user">
            </div>
        </div>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputRCname"><?php echo $gsprache->user;?></label>
            <div class="controls">
                <input id="inputRCname" type="text" name="rcname" value="reseller">
            </div>
        </div>
        <?php } ?>
        <div class="a display_none switch control-group">
            <label class="control-label" for="inputACname"><?php echo $gsprache->user;?></label>
            <div class="controls">
                <input id="inputACname" type="text" name="acname" value="admin">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputMailBackup"><?php echo $sprache->mail_backup;?></label>
            <div class="controls">
                <input id="inputMailBackup" type="checkbox" name="mail_backup" value="Y" checked="checked">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputMailServerDown"><?php echo $sprache->mail_serverdown;?></label>
            <div class="controls">
                <input id="inputMailServerDown" type="checkbox" name="mail_serverdown" value="Y" checked="checked">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputMailTicket"><?php echo $sprache->mail_ticket;?></label>
            <div class="controls">
                <input id="inputMailTicket" type="checkbox" name="mail_ticket" value="Y" checked="checked">
            </div>
        </div>
        <div class="a display_none switch control-group">
            <label class="control-label" for="inputAMailUpdate"><?php echo $sprache->mail_gsupdate;?></label>
            <div class="controls">
                <input id="inputAMailUpdate" type="checkbox" name="mail_gsupdate" value="Y" checked="checked">
            </div>
        </div>
        <div class="a display_none switch control-group">
            <label class="control-label" for="inputAMailSecurity"><?php echo $sprache->mail_securitybreach;?></label>
            <div class="controls">
                <input id="inputAMailSecurity" type="checkbox" name="mail_securitybreach" value="Y" checked="checked">
            </div>
        </div>
        <div class="a display_none switch control-group">
            <label class="control-label" for="inputAMailVserver"><?php echo $sprache->mail_vserver;?></label>
            <div class="controls">
                <input id="inputAMailVserver" type="checkbox" name="mail_vserver" value="Y" checked="checked">
            </div>
        </div>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputRMailUpdate"><?php echo $sprache->mail_gsupdate;?></label>
            <div class="controls">
                <input id="inputRMailUpdate" type="checkbox" name="rmail_gsupdate" value="Y" checked="checked">
            </div>
        </div>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputRMailSecurity"><?php echo $sprache->mail_securitybreach;?></label>
            <div class="controls">
                <input id="inputRMailSecurity" type="checkbox" name="rmail_securitybreach" value="Y" checked="checked">
            </div>
        </div>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputRMailVserver"><?php echo $sprache->mail_vserver;?></label>
            <div class="controls">
                <input id="inputRMailVserver" type="checkbox" name="rmail_vserver" value="Y" checked="checked">
            </div>
        </div>
        <div class="u switch control-group">
            <label class="control-label" for="inputFastDl"><?php echo $sprache->fastdl2;?></label>
            <div class="controls">
                <input id="inputMailTicket" type="url" name="fdlpath" value="">
            </div>
        </div>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputMaxUser"><?php echo $sprache->max." ".$gsprache->user;?></label>
            <div class="controls">
                <input id="inputMaxUser" type="number" name="maxuser" value="">
            </div>
        </div>
        <?php if($easywiModules['gs']) { ?>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputMaxGserver"><?php echo $sprache->max." ".$gsprache->gameserver;?></label>
            <div class="controls">
                <input id="inputMaxGserver" type="number" name="maxgserver" value="">
            </div>
        </div>
        <?php }; if($easywiModules['vo']) { ?>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputMaxVoserver"><?php echo $sprache->max." ".$gsprache->voiceserver;?></label>
            <div class="controls">
                <input id="inputMaxVoserver" type="number" name="maxvoiceserver" value="">
            </div>
        </div>
        <?php }; if($easywiModules['ro']) { ?>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputMaxDedis"><?php echo $sprache->max." ".$gsprache->dedicated;?></label>
            <div class="controls">
                <input id="inputMaxDedis" type="number" name="maxdedis" value="">
            </div>
        </div>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputMaxVirtual"><?php echo $sprache->max." ".$gsprache->virtual;?></label>
            <div class="controls">
                <input id="inputMaxVirtual" type="number" name="maxvserver" value="">
            </div>
        </div>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputMaxUserMHZ"><?php echo $sprache->max." ".$rsprache->mhz;?></label>
            <div class="controls">
                <input id="inputMaxUserMHZ" type="number" name="maxusermhz" value="">
            </div>
        </div>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputMaxUserRam"><?php echo $sprache->max." ".$rsprache->ram;?></label>
            <div class="controls">
                <input id="inputMaxUserRam" type="number" name="maxuserram" value="">
            </div>
        </div>
        <?php } ?>
        <div class="r display_none switch control-group">
            <label class="control-label" for="inputResellerUserActive"><?php echo $gsprache->reseller." ".$gsprache->user." ".$sprache->active;?></label>
            <div class="controls">
                <select id="inputResellerUserActive" name="useractive">
                    <option value="Y"><?php echo $gsprache->yes;?></option>
                    <option value="N"><?php echo $gsprache->no;?></option>
                </select>
            </div>
        </div>
        <?php foreach(customColumns('U') as $row){ ?>
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
                <button class="btn btn-primary" id="inputMod" type="submit"><i class="icon-white icon-plus-sign"></i></button>
            </div>
        </div>
        </form>
    </div>
</div>