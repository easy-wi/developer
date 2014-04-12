<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=fv"><?php echo $gsprache->webspace;?> Vhost</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $dns;?></li>
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
        <form class="form-horizontal" action="admin.php?w=wv&amp;d=md&amp;id=<?php echo $id;?>&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label lead" for="displayServerInfo"><b><?php echo $gsprache->webspace.' '.$gsprache->master;?></b></label>
                <div class="controls">
                    <dl id="displayServerInfo">
                        <dt><?php echo $sprache->maxHDD;?></dt>
                        <dd><?php echo $leftHDD.'/'.$maxHDD;?> MB</dd>
                        <dt><?php echo $sprache->ftpIP;?></dt>
                        <dd><?php echo $ftpServer;?></dd>
                    </dl>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label lead" for="displayServerInfo"><b><?php echo $gsprache->webspace;?></b></label>
                <div class="controls">
                    <dl id="displayVhostInfo">
                        <dt><?php echo $sprache->hddUsage;?></dt>
                        <dd><?php echo $hddUsage.'/'.$hdd;?> MB</dd>
                    </dl>
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
            <?php if($quotaActive=='Y'){ ?>
            <div class="control-group<?php if(isset($errors['hdd'])) echo ' error';?>">
                <label class="control-label" for="inputHDD"><?php echo $sprache->hdd;?></label>
                <div class="controls">
                    <div class="input-append span11">
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
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->mod;?></button>
                </div>
            </div>
        </form>
    </div>
</div>