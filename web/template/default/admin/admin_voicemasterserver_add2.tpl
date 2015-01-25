<section class="content-header">
    <h1><?php echo $gsprache->voiceserver.' '.$gsprache->master;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><a href="admin.php?w=vm"><i class="fa fa-hdd-o"></i> <?php echo $gsprache->voiceserver.' '.$gsprache->master;?></a></li>
        <li class="active"><?php echo $gsprache->add.'/'.$sprache->import;?></li>
    </ol>
</section>

<div class="row">
    <div class="col-md-12">
        <div class="box box-success">

            <form role="form" action="admin.php?w=vm&amp;d=<?php echo $ui->st('d','get');?>&amp;id=<?php echo $masterid;?>&amp;r=vm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                <input class="form-control" type="hidden" name="token" value="<?php echo token();?>">
                <input class="form-control" type="hidden" name="action" value="ad2">

                <div class="box-body">

                    <?php if(count($servers)>0){ ?>
                    <?php foreach ($servers as $virtualserver_id => $values) { ?>


                    <h5><?php echo $values['virtualserver_ip'].':'.$values['virtualserver_port'].' '.$values['virtualserver_dns'].' ('.$values['virtualserver_name'].')';?></h5>

                    <input class="form-control" type="hidden" name="virtualserver_id[]" value="<?php echo $virtualserver_id;?>">
                    <input class="form-control" type="hidden" name="<?php echo $virtualserver_id;?>-virtualserver_maxclients" value="<?php echo $values['virtualserver_maxclients'];?>">
                    <input class="form-control" type="hidden" name="<?php echo $virtualserver_id;?>-virtualserver_port" value="<?php echo $values['virtualserver_port'];?>">
                    <input class="form-control" type="hidden" name="<?php echo $virtualserver_id;?>-virtualserver_dns" value="<?php echo $values['virtualserver_dns'];?>">

                    <div class="form-group">
                        <label for="inputImport"><?php echo $sprache->import;?></label>
                        <div class="controls">
                            <select class="form-control" id="inputImport" name="<?php echo $virtualserver_id;?>-import">
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                                <option value="N"><?php echo $gsprache->no;?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputUser"><?php echo $gsprache->user ;?>:</label>
                        <div class="controls">
                            <select class="form-control" id="inputUser" name="<?php echo $virtualserver_id;?>-customer" onchange="SwitchShowHideRows(this.value,'<?php echo $virtualserver_id;?>-customer');">
                                <option value="0"><?php echo $sprache->newuser;?></option>
                                <?php foreach ($table as $key=>$value) { ?>
                                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <?php if ($rSA['prefix1']=='N') { ?>

                    <div class="0 <?php echo $virtualserver_id;?>-customer form-group">
                        <label for="inputUserName"><?php echo $sprache->user;?></label>
                        <div class="controls"><input class="form-control" id="inputUserName" type="text" name="<?php echo $virtualserver_id;?>-username"></div>
                    </div>

                    <div class="0 <?php echo $virtualserver_id;?>-customer form-group">
                        <label for="inputUserMail"><?php echo $usprache->email;?></label>
                        <div class="controls"><input class="form-control" id="inputUserMail" type="email" name="<?php echo $virtualserver_id;?>-email" value="ts3@import.mail"></div>
                    </div>

                    <?php } ?>

                    <div class="form-group">
                        <label for="inputPassword"><?php echo $sprache->password;?></label>
                        <div class="controls">
                            <select class="form-control" id="inputPassword" name="<?php echo $virtualserver_id;?>-password">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if($values['virtualserver_flag_password']=='1') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputForceWelcome"><?php echo $sprache->forcewelcome;?></label>
                        <div class="controls">
                            <select class="form-control" id="inputForceWelcome" name="<?php echo $virtualserver_id;?>-forcewelcome">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputForceBanner"><?php echo $sprache->forcebanner;?></label>
                        <div class="controls">
                            <select class="form-control" id="inputForceBanner" name="<?php echo $virtualserver_id;?>-forcebanner">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputForceButton"><?php echo $sprache->forcebutton;?></label>
                        <div class="controls">
                            <select class="form-control" id="inputForceButton" name="<?php echo $virtualserver_id;?>-forcebutton">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputForceServerTag"><?php echo $sprache->forceservertag;?></label>
                        <div class="controls">
                            <select class="form-control" id="inputForceServerTag" name="<?php echo $virtualserver_id;?>-forceservertag">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputFlexSlots"><?php echo $sprache->flexSlots;?></label>
                        <div class="controls">
                            <select class="form-control" id="inputFlexSlots" name="<?php echo $virtualserver_id;?>-flexSlots">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputFlexSlotsFree"><?php echo $sprache->flexSlotsFree;?></label>
                        <div class="controls"><input class="form-control" id="inputFlexSlotsFree" type="text" name="<?php echo $virtualserver_id;?>-flexSlotsFree" value="<?php echo $defaultFlexSlotsFree;?>"></div>
                    </div>

                    <div class="form-group">
                        <label for="inputFlexSlotsPercent"><?php echo $sprache->flexSlotsPercent;?></label>
                        <div class="controls"><input class="form-control" id="inputFlexSlotsPercent" type="text" name="<?php echo $virtualserver_id;?>-flexSlotsPercent" value="<?php echo $defaultFlexSlotsFree;?>"></div>
                    </div>

                    <?php }} else { ?>
                    <?php echo $sprache->noVoiceServer;?>
                    <?php } ?>

                </div>

                <div class="box-footer">
                    <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
</section>