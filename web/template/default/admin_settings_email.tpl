<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active">E-Mail <?php echo $gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=sm&amp;r=sm" onsubmit="return confirm('<?php echo $sprache->confirm_change; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label" for="inputType">E-Mail</label>
                <div class="controls">
                    <select class="span10" id="inputType" name="email_settings_type" onchange="SwitchShowHideRows(this.value);">
                        <option value="P">PHP Mail</option>
                        <option value="S" <?php if($email_settings_type=='S') echo 'selected="selected"';?>>SMTP</option>
                        <option value="D" <?php if($email_settings_type=='D') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEmail"><?php echo $sprache->email;?></label>
                <div class="controls">
                    <input class="span10" id="inputEmail" type="email" name="email" value="<?php echo $email;?>">
                </div>
            </div>
            <div class="S switch control-group <?php if($email_settings_type!='S') echo 'display_none';?>">
                <label class="control-label" for="inputSSL">SSL/TLS</label>
                <div class="controls">
                    <select class="span10" id="inputSSL" name="email_settings_ssl">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="T" <?php if($email_settings_ssl=='T') echo 'selected="selected"';?>>STARTTLS</option>
                    </select>
                </div>
            </div>
            <div class="S switch control-group <?php if($email_settings_type!='S') echo 'display_none';?>">
                <label class="control-label" for="inputHost">Host</label>
                <div class="controls">
                    <input class="span10" id="inputHost" type="text" name="email_settings_host" value="<?php echo $email_settings_host;?>">
                </div>
            </div>
            <div class="S switch control-group <?php if($email_settings_type!='S') echo 'display_none';?>">
                <label class="control-label" for="inputPassword">Port</label>
                <div class="controls">
                    <input class="span10" id="inputPassword" type="text" name="email_settings_port" value="<?php echo $email_settings_port;?>">
                </div>
            </div>
            <div class="S switch control-group <?php if($email_settings_type!='S') echo 'display_none';?>">
                <label class="control-label" for="inputUser"><?php echo $gsprache->user;?></label>
                <div class="controls">
                    <input class="span10" id="inputUser" type="text" name="email_settings_user" value="<?php echo $email_settings_user;?>">
                </div>
            </div>
            <div class="S switch control-group <?php if($email_settings_type!='S') echo 'display_none';?>">
                <label class="control-label" for="inputPassword"><?php echo $sprache->password;?></label>
                <div class="controls">
                    <input class="span10" id="inputPassword" type="text" name="email_settings_password" value="<?php echo $email_settings_password;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEmailRegards"><?php echo $sprache->emailregards;?></label>
                <div class="controls">
                    <textarea class="span10" id="inputEmailRegards" name="emailregards" rows="8"><?php echo $emailregards;?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEmailFooter"><?php echo $sprache->emailfooter;?></label>
                <div class="controls">
                    <textarea class="span10" id="inputEmailFooter" name="emailfooter" rows="8"><?php echo $emailfooter;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $gsprache->backup." ".$gssprache->create;?></label>
                <div class="controls">
                    <?php foreach ($emailbackup_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inlineCheckboxBackupCreate<?php echo $array['lang'];?>" name="languages-emailbackup[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailbackup');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emailbackup_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emailbackup";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputBackupCreate<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputBackupCreate<?php echo $array['lang'];?>" name="emailbackup_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineCheckboxBackupCreateTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineCheckboxBackupCreateTemplate" name="emailbackup" rows="8"><?php echo $emailbackup;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $gsprache->backup." ".$gssprache->recover;?></label>
                <div class="controls">
                    <?php foreach ($emailbackuprestore_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inlineCheckboxBackupRestore<?php echo $array['lang'];?>" name="languages-emailbackuprestore[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailbackuprestore');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emailbackuprestore_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emailbackuprestore";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputBackupRestore<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputBackupRestore<?php echo $array['lang'];?>" name="emailbackuprestore_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineCheckboxBackupRestoreTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineCheckboxBackupRestoreTemplate" name="emailbackuprestore" rows="8"><?php echo $emailbackuprestore;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $sprache->emaildown;?></label>
                <div class="controls">
                    <?php foreach ($emaildown_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inlineEmailDown<?php echo $array['lang'];?>" name="languages-emaildown[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emaildown');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emaildown_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emaildown";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputEmailDown<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputEmailDown<?php echo $array['lang'];?>" name="emaildown_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineCheckboxBackupRestoreTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineCheckboxBackupRestoreTemplate" name="emaildown" rows="8"><?php echo $emaildown;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $sprache->emaildownrestart;?></label>
                <div class="controls">
                    <?php foreach ($emaildownrestart_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inlineEmailDown<?php echo $array['lang'];?>" name="languages-emaildownrestart[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emaildownrestart');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emaildownrestart_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emaildownrestart";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputEmailDown<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputEmailDown<?php echo $array['lang'];?>" name="emaildownrestart_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineCheckboxDownRestartTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineCheckboxDownRestartTemplate" name="emaildownrestart" rows="8"><?php echo $emaildownrestart;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $gsprache->master." ".$gsprache->update;?></label>
                <div class="controls">
                    <?php foreach ($emailgserverupdate_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inputCheckboxGserverUpdate<?php echo $array['lang'];?>" name="languages-emailgserverupdate[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailgserverupdate');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emailgserverupdate_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emailgserverupdate";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputGserverUpdate<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputGserverUpdate<?php echo $array['lang'];?>" name="emailgserverupdate_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineGserverUpdateTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineGserverUpdateTemplate" name="emailgserverupdate" rows="8"><?php echo $emailgserverupdate;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $sprache->emailpasswordrecovery;?></label>
                <div class="controls">
                    <?php foreach ($emailpwrecovery_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inputCheckboxPWDRecovery<?php echo $array['lang'];?>" name="languages-emailpwrecovery[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailpwrecovery');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emailpwrecovery_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emailpwrecovery";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputPWDRecovery<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputPWDRecovery<?php echo $array['lang'];?>" name="emailpwrecovery_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlinePWDRecoveryTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlinePWDRecoveryTemplate" name="emailpwrecovery" rows="8"><?php echo $emailpwrecovery;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $sprache->emailnewticket;?></label>
                <div class="controls">
                    <?php foreach ($emailnewticket_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inputCheckboxEmailNewTicket<?php echo $array['lang'];?>" name="languages-emailnewticket[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailnewticket');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emailnewticket_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emailnewticket";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputEmailNewTicket<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputEmailNewTicket<?php echo $array['lang'];?>" name="emailnewticket_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineEmailNewTicketTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineEmailNewTicketTemplate" name="emailnewticket" rows="8"><?php echo $emailnewticket;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $sprache->emailsecuritybreach;?></label>
                <div class="controls">
                    <?php foreach ($emailsecuritybreach_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inputCheckboxEmailSecurityBreach<?php echo $array['lang'];?>" name="languages-emailsecuritybreach[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailsecuritybreach');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emailsecuritybreach_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emailsecuritybreach";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputEmailSecurityBreach<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputEmailSecurityBreach<?php echo $array['lang'];?>" name="emailsecuritybreach_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
                </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineEmailSecurityBreachTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineEmailSecurityBreachTemplate" name="emailsecuritybreach" rows="8"><?php echo $emailsecuritybreach;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $gsprache->user." ".$gsprache->add;?></label>
                <div class="controls">
                    <?php foreach ($emailuseradd_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inputCheckboxEmailUserAdd<?php echo $array['lang'];?>" name="languages-emailuseradd[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailuseradd');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emailuseradd_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emailuseradd";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputEmailUserAdd<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputEmailUserAdd<?php echo $array['lang'];?>" name="emailuseradd_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineEmailUserAddTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineEmailUserAddTemplate" name="emailuseradd" rows="8"><?php echo $emailuseradd;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $gsprache->user." ".$gsprache->registration;?></label>
                <div class="controls">
                    <?php foreach ($emailregister_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inputCheckboxEmailRegister<?php echo $array['lang'];?>" name="languages-emailregister[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailregister');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emailregister_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emailregister";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputEmailRegister<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputEmailRegister<?php echo $array['lang'];?>" name="emailregister_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineEmailRegisterTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineEmailRegisterTemplate" name="emailregister" rows="8"><?php echo $emailregister;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $gsprache->virtual." ".$gsprache->add;?></label>
                <div class="controls">
                    <?php foreach ($emailvinstall_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inputCheckboxEmailVinstall<?php echo $array['lang'];?>" name="languages-emailvinstall[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailvinstall');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emailvinstall_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emailvinstall";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputEmailVinstall<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputEmailVinstall<?php echo $array['lang'];?>" name="emailvinstall_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineEmailVinstallTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineEmailVinstallTemplate" name="emailvinstall" rows="8"><?php echo $emailvinstall;?></textarea>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label"><?php echo $gsprache->virtual;?> Rescue</label>
                <div class="controls">
                    <?php foreach ($emailvrescue_xml as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inputCheckboxEmailVRescue<?php echo $array['lang'];?>" name="languages-emailvrescue[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailvrescue');" <?php if ($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($emailvrescue_xml as $array) { ?>
            <div id="<?php echo $array['lang']."-emailvrescue";?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputEmailVRescue<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputEmailVRescue<?php echo $array['lang'];?>" name="emailvrescue_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inlineEmailVRescueTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <textarea class="span10" id="inlineEmailVRescueTemplate" name="emailvrescue" rows="8"><?php echo $emailvrescue;?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="md">
                </div>
            </div>
        </form>
    </div>
</div>