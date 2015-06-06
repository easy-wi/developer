<section class="content-header">
    <h1>E-Mail <?php echo $gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li class="active"><i class="fa fa-envelope"></i> E-Mail <?php echo $gsprache->settings;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=sm&amp;r=sm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label class="control-label" for="inputType">E-Mail</label>
                            <div class="controls">
                                <select class="form-control" id="inputType" name="email_settings_type" onchange="SwitchShowHideRows(this.value,'switch',1);">
                                    <option value="P">PHP Mail</option>
                                    <option value="S" <?php if($email_settings['email_settings_type']=='S') echo 'selected="selected"';?>>SMTP</option>
                                    <option value="D" <?php if($email_settings['email_settings_type']=='D') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputEmail"><?php echo $sprache->email;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputEmail" type="email" name="email" value="<?php echo $email_settings['email'];?>">
                            </div>
                        </div>

                        <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                            <label class="control-label" for="inputSSL">SSL/TLS</label>
                            <div class="controls">
                                <select class="form-control" id="inputSSL" name="email_settings_ssl">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="S" <?php if($email_settings['email_settings_ssl']=='S') echo 'selected="selected"';?>>SSL</option>
                                    <option value="T" <?php if($email_settings['email_settings_ssl']=='T') echo 'selected="selected"';?>>TLS</option>
                                </select>
                            </div>
                        </div>

                        <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                            <label class="control-label" for="inputHost">Host</label>
                            <div class="controls">
                                <input class="form-control" id="inputHost" type="text" name="email_settings_host" value="<?php echo $email_settings['email_settings_host'];?>">
                            </div>
                        </div>

                        <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                            <label class="control-label" for="inputPassword">Port</label>
                            <div class="controls">
                                <input class="form-control" id="inputPassword" type="text" name="email_settings_port" value="<?php echo $email_settings['email_settings_port'];?>">
                            </div>
                        </div>

                        <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                            <label class="control-label" for="inputUser"><?php echo $gsprache->user;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputUser" type="text" name="email_settings_user" value="<?php echo $email_settings['email_settings_user'];?>">
                            </div>
                        </div>

                        <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                            <label class="control-label" for="inputPassword"><?php echo $sprache->password;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputPassword" type="text" name="email_settings_password" value="<?php echo $email_settings['email_settings_password'];?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputEmailRegards"><?php echo $sprache->emailregards;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputEmailRegards" name="emailregards" rows="8"><?php echo $email_settings['emailregards'];?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputEmailFooter"><?php echo $sprache->emailfooter;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputEmailFooter" name="emailfooter" rows="8"><?php echo $email_settings['emailfooter'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $gsprache->backup.' '.$gssprache->create;?></h3>

                        <div class="form-group">
                            <?php foreach ($emailbackup_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inlineCheckboxBackupCreate<?php echo $array['lang'];?>" name="languages-emailbackup[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailbackup');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailbackup_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailbackup';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputBackupCreate<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputBackupCreate<?php echo $array['lang'];?>" name="emailbackup_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineCheckboxBackupCreateTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineCheckboxBackupCreateTemplate" name="emailbackup" rows="8"><?php echo $email_settings['emailbackup'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $gsprache->backup.' '.$gssprache->recover;?></h3>

                        <div class="form-group">
                            <?php foreach ($emailbackuprestore_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inlineCheckboxBackupRestore<?php echo $array['lang'];?>" name="languages-emailbackuprestore[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailbackuprestore');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailbackuprestore_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailbackuprestore';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputBackupRestore<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputBackupRestore<?php echo $array['lang'];?>" name="emailbackuprestore_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineCheckboxBackupRestoreTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineCheckboxBackupRestoreTemplate" name="emailbackuprestore" rows="8"><?php echo $email_settings['emailbackuprestore'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $sprache->emaildown;?></h3>

                        <div class="form-group">
                            <?php foreach ($emaildown_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inlineEmailDown<?php echo $array['lang'];?>" name="languages-emaildown[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emaildown');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emaildown_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emaildown';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputEmailDown<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputEmailDown<?php echo $array['lang'];?>" name="emaildown_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineCheckboxBackupRestoreTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineCheckboxBackupRestoreTemplate" name="emaildown" rows="8"><?php echo $email_settings['emaildown'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $sprache->emaildownrestart;?></h3>

                        <div class="form-group">
                            <?php foreach ($emaildownrestart_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inlineEmailDownRestart<?php echo $array['lang'];?>" name="languages-emaildownrestart[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emaildownrestart');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emaildownrestart_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emaildownrestart';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputEmailDown<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputEmailDown<?php echo $array['lang'];?>" name="emaildownrestart_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineCheckboxDownRestartTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineCheckboxDownRestartTemplate" name="emaildownrestart" rows="8"><?php echo $email_settings['emaildownrestart'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $gsprache->gameserver.' '.$gsprache->master.' '.$gsprache->update;?></h3>

                        <div class="form-group">
                            <?php foreach ($emailgserverupdate_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inputCheckboxGserverUpdate<?php echo $array['lang'];?>" name="languages-emailgserverupdate[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailgserverupdate');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailgserverupdate_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailgserverupdate';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputGserverUpdate<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputGserverUpdate<?php echo $array['lang'];?>" name="emailgserverupdate_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineGserverUpdateTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineGserverUpdateTemplate" name="emailgserverupdate" rows="8"><?php echo $email_settings['emailgserverupdate'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $gsprache->voiceserver.' '.$gsprache->master.' '.$gsprache->update;?></h3>

                        <div class="form-group">
                            <?php foreach ($emailvoicemasterold_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inputCheckboxVoiceUpdate<?php echo $array['lang'];?>" name="languages-emailvoicemasterold[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailvoicemasterold');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailvoicemasterold_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailvoicemasterold';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputVoiceServerUpdate<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputVoiceServerUpdate<?php echo $array['lang'];?>" name="emailvoicemasterold_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineGserverUpdateTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineGserverUpdateTemplate" name="emailgserverupdate" rows="8"><?php echo $email_settings['emailgserverupdate'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $sprache->emailpasswordrecovery;?></h3>

                        <div class="form-group">
                            <?php foreach ($emailpwrecovery_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inputCheckboxPWDRecovery<?php echo $array['lang'];?>" name="languages-emailpwrecovery[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailpwrecovery');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailpwrecovery_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailpwrecovery';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputPWDRecovery<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputPWDRecovery<?php echo $array['lang'];?>" name="emailpwrecovery_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlinePWDRecoveryTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlinePWDRecoveryTemplate" name="emailpwrecovery" rows="8"><?php echo $email_settings['emailpwrecovery'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $sprache->emailnewticket;?></h3>

                        <div class="form-group">
                            <?php foreach ($emailnewticket_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inputCheckboxEmailNewTicket<?php echo $array['lang'];?>" name="languages-emailnewticket[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailnewticket');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailnewticket_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailnewticket';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputEmailNewTicket<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputEmailNewTicket<?php echo $array['lang'];?>" name="emailnewticket_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineEmailNewTicketTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineEmailNewTicketTemplate" name="emailnewticket" rows="8"><?php echo $email_settings['emailnewticket'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $sprache->emailsecuritybreach;?></h3>

                        <div class="form-group">
                            <?php foreach ($emailsecuritybreach_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inputCheckboxEmailSecurityBreach<?php echo $array['lang'];?>" name="languages-emailsecuritybreach[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailsecuritybreach');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailsecuritybreach_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailsecuritybreach';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputEmailSecurityBreach<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputEmailSecurityBreach<?php echo $array['lang'];?>" name="emailsecuritybreach_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineEmailSecurityBreachTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineEmailSecurityBreachTemplate" name="emailsecuritybreach" rows="8"><?php echo $email_settings['emailsecuritybreach'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $gsprache->user.' '.$gsprache->add;?></h3>

                        <div class="form-group">
                            <?php foreach ($emailuseradd_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inputCheckboxEmailUserAdd<?php echo $array['lang'];?>" name="languages-emailuseradd[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailuseradd');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailuseradd_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailuseradd';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputEmailUserAdd<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputEmailUserAdd<?php echo $array['lang'];?>" name="emailuseradd_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineEmailUserAddTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineEmailUserAddTemplate" name="emailuseradd" rows="8"><?php echo $email_settings['emailuseradd'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $gsprache->user.' '.$gsprache->registration;?></h3>

                        <div class="form-group">
                            <?php foreach ($emailregister_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inputCheckboxEmailRegister<?php echo $array['lang'];?>" name="languages-emailregister[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailregister');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailregister_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailregister';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputEmailRegister<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputEmailRegister<?php echo $array['lang'];?>" name="emailregister_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineEmailRegisterTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineEmailRegisterTemplate" name="emailregister" rows="8"><?php echo $email_settings['emailregister'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $gsprache->virtual.' '.$gsprache->add;?></h3>

                        <div class="form-group">
                            <?php foreach ($emailvinstall_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inputCheckboxEmailVinstall<?php echo $array['lang'];?>" name="languages-emailvinstall[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailvinstall');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailvinstall_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailvinstall';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputEmailVinstall<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputEmailVinstall<?php echo $array['lang'];?>" name="emailvinstall_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineEmailVinstallTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineEmailVinstallTemplate" name="emailvinstall" rows="8"><?php echo $email_settings['emailvinstall'];?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h3><?php echo $gsprache->virtual;?> Rescue</h3>

                        <div class="form-group">
                            <?php foreach ($emailvrescue_xml as $array){ ?>
                            <label class="checkbox-inline">
                                <input id="inputCheckboxEmailVRescue<?php echo $array['lang'];?>" name="languages-emailvrescue[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailvrescue');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($emailvrescue_xml as $array) { ?>
                        <div id="<?php echo $array['lang'].'-emailvrescue';?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
                            <label class="control-label" for="inputEmailVRescue<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputEmailVRescue<?php echo $array['lang'];?>" name="emailvrescue_xml_<?php echo $array['lang'];?>" rows="8"><?php echo $array['xml'];?></textarea>
                            </div>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label class="control-label" for="inlineEmailVRescueTemplate"><?php echo $gsprache->template;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inlineEmailVRescueTemplate" name="emailvrescue" rows="8"><?php echo $email_settings['emailvrescue'];?></textarea>
                            </div>
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