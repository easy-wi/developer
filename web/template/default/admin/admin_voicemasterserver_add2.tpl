<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->voiceserver." ".$gsprache->master;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add.'/'.$sprache->import;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form name="form" class="form-horizontal" action="admin.php?w=vm&amp;d=<?php echo $ui->st('d','get');?>&amp;id=<?php echo $masterid;?>&amp;r=vm" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad2">
            <?php foreach ($servers as $virtualserver_id => $values) { ?>
            <h5><?php echo $values['virtualserver_ip'].':'.$values['virtualserver_port'].' '.$values['virtualserver_dns'].' ('.$values['virtualserver_name'].')';?></h5>
            <input type="hidden" name="virtualserver_id[]" value="<?php echo $virtualserver_id;?>">
            <input type="hidden" name="<?php echo $virtualserver_id;?>-virtualserver_maxclients" value="<?php echo $values['virtualserver_maxclients'];?>">
            <input type="hidden" name="<?php echo $virtualserver_id;?>-virtualserver_port" value="<?php echo $values['virtualserver_port'];?>">
            <input type="hidden" name="<?php echo $virtualserver_id;?>-virtualserver_dns" value="<?php echo $values['virtualserver_dns'];?>">
            <div class="control-group">
                <label class="control-label" for="inputImport"><?php echo $sprache->import;?></label>
                <div class="controls">
                    <select id="inputImport" name="<?php echo $virtualserver_id;?>-import">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUser"><?php echo $gsprache->user ;?>:</label>
                <div class="controls">
                    <select id="inputUser" name="<?php echo $virtualserver_id;?>-customer" onchange="SwitchShowHideRows(this.value,'<?php echo $virtualserver_id;?>-customer');">
                        <option value="0"><?php echo $sprache->newuser;?></option>
                        <?php foreach ($table as $key=>$value) { ?>
                        <option value="<?php echo $key;?>"><?php echo $value;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php if ($rSA['prefix1']=='N') { ?>
            <div class="0 <?php echo $virtualserver_id;?>-customer control-group">
                <label class="control-label" for="inputUserName"><?php echo $sprache->user;?></label>
                <div class="controls"><input id="inputUserName" type="text" name="<?php echo $virtualserver_id;?>-username"></div>
            </div>
            <div class="0 <?php echo $virtualserver_id;?>-customer control-group">
                <label class="control-label" for="inputUserMail"><?php echo $usprache->email;?></label>
                <div class="controls"><input id="inputUserMail" type="email" name="<?php echo $virtualserver_id;?>-email" value="ts3@import.mail"></div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputPassword"><?php echo $sprache->password;?></label>
                <div class="controls">
                    <select id="inputPassword" name="<?php echo $virtualserver_id;?>-password">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if($values['virtualserver_flag_password']=='1') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputForceWelcome"><?php echo $sprache->forcewelcome;?></label>
                <div class="controls">
                    <select id="inputForceWelcome" name="<?php echo $virtualserver_id;?>-forcewelcome">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputForceBanner"><?php echo $sprache->forcebanner;?></label>
                <div class="controls">
                    <select id="inputForceBanner" name="<?php echo $virtualserver_id;?>-forcebanner">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputForceButton"><?php echo $sprache->forcebutton;?></label>
                <div class="controls">
                    <select id="inputForceButton" name="<?php echo $virtualserver_id;?>-forcebutton">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputForceServerTag"><?php echo $sprache->forceservertag;?></label>
                <div class="controls">
                    <select id="inputForceServerTag" name="<?php echo $virtualserver_id;?>-forceservertag">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFlexSlots"><?php echo $sprache->flexSlots;?></label>
                <div class="controls">
                    <select id="inputFlexSlots" name="<?php echo $virtualserver_id;?>-flexSlots">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFlexSlotsFree"><?php echo $sprache->flexSlotsFree;?></label>
                <div class="controls"><input id="inputFlexSlotsFree" type="text" name="<?php echo $virtualserver_id;?>-flexSlotsFree" value="<?php echo $defaultFlexSlotsFree;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFlexSlotsPercent"><?php echo $sprache->flexSlotsPercent;?></label>
                <div class="controls"><input id="inputFlexSlotsPercent" type="text" name="<?php echo $virtualserver_id;?>-flexSlotsPercent" value="<?php echo $defaultFlexSlotsFree;?>"></div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
    </div>
</div>