<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=wv"><?php echo $gsprache->webspace;?> Vhost</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<?php if (count($errors)>0){ ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4><?php echo $gsprache->errors;?></h4>
    <?php echo implode(', ',$errors);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=wv&amp;d=ad&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group<?php if(isset($errors['userID'])) echo ' error';?>">
                <label class="control-label" for="inputUserID"><?php echo $dedicatedLanguage->user;?></label>
                <div class="controls">
                    <select id="inputUserID" name="userID" class="span11">
                        <?php foreach ($table as $k=>$v){ ?>
                        <option value="<?php echo $k;?>" <?php if ($userID==$k) echo 'selected="selected";'?>><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFtpPassword"><?php echo $sprache->ftpPassword;?></label>
                <div class="controls"><input id="inputFtpPassword" class="span11" type="text" name="ftpPassword" value="<?php echo $ftpPassword;?>" required></div>
            </div>
            <div class="control-group<?php if(isset($errors['active'])) echo ' error';?>">
                <label class="control-label" for="inputActive"><?php echo $dedicatedLanguage->active;?></label>
                <div class="controls">
                    <select id="inputActive" class="span11" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($active=='N') echo 'selected="selected";'?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['webMasterID'])) echo ' error';?>">
                <label class="control-label" for="inputWebMasterID"><?php echo $gsprache->master;?></label>
                <div class="controls">
                    <select id="inputWebMasterID" name="webMasterID" class="span11" onchange="getdetails('ajax.php?d=webmaster&id=', this.value)">
                        <?php foreach ($table2 as $k=>$v){ ?>
                        <option value="<?php echo $k;?>" <?php if ($webMasterID==$k) echo 'selected="selected";'?>><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div id="information">
                <div class="control-group">
                    <label class="control-label lead" for="displayUsage"><b><?php echo $sprache->usage;?></b></label>
                    <div class="controls">
                        <dl id="displayUsage">
                            <dt><?php echo $sprache->maxVhost;?></dt>
                            <dd><?php echo $totalVhosts.'/'.$maxVhost;?></dd>
                            <dt><?php echo $sprache->maxHDD;?></dt>
                            <dd><?php echo $leftHDD.'/'.$maxHDD;?> MB</dd>
                        </dl>
                    </div>
                </div>
                <?php if($quotaActive=='Y'){ ?>
                <div class="control-group<?php if(isset($errors['hdd'])) echo ' error';?>">
                    <label class="control-label" for="inputHDD"><?php echo $sprache->hdd;?></label>
                    <div class="controls">
                        <div class="input-append span12">
                            <input id="inputHDD" class="span11" type="number" name="hdd" value="<?php echo $hdd;?>">
                            <span class="add-on">MB</span>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="control-group">
                    <label class="control-label" for="inputDNS"><?php echo $sprache->dns;?></label>
                    <div class="controls"><input id="inputDNS" class="span11" type="text" name="dns" value="<?php echo $dns;?>" required></div>
                </div>
                <div class="control-group<?php if(isset($errors['ownVhost'])) echo ' error';?>">
                    <label class="control-label" for="inputOwnVhost"><?php echo $sprache->ownVhost;?></label>
                    <div class="controls">
                        <select id="inputOwnVhost" class="span11" name="ownVhost" onchange="textdrop('OwnVhostTemplate')">
                            <option value="N"><?php echo $gsprache->no;?></option>
                            <option value="Y" <?php if ($ownVhost=='Y') echo 'selected="selected";'?>><?php echo $gsprache->yes;?></option>
                        </select>
                    </div>
                </div>
                <div class="control-group<?php if($ownVhost=='N') echo ' display_none';?>" id="OwnVhostTemplate">
                    <label class="control-label" for="inputvhostTemplate"><?php echo $sprache->vhostTemplate;?></label>
                    <div class="controls">
                        <textarea id="inputvhostTemplate" class="span11" name="vhostTemplate" rows="20"><?php echo $vhostTemplate;?></textarea>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i> <?php echo $gsprache->add;?></button>
                </div>
            </div>
        </form>
    </div>
</div>