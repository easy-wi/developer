<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=up">Social Auth Provider</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod?> <span class="divider">/</span></li>
            <li class="active"><?php echo $name?></li>
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
    <div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $sprache->multipleHelperEndpoint; ?></div>
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=up&amp;d=md&amp;id=<?php echo $id;?>&amp;r=up" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group<?php if(isset($errors['active'])) echo ' error';?>">
                <label class="control-label" for="inputActive"><?php echo $gsprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['name'])) echo ' error';?>">
                <label class="control-label" for="inputName">Social Auth Provider</label>
                <div class="controls">
                    <select id="inputName" name="name">
                        <?php foreach($serviceProviders as $sp){ ?>
                        <option<?php if($sp == $name) echo ' selected="selected"';?>><?php echo $sp;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['keyID'])) echo ' error';?>">
                <label class="control-label" for="inputKeyID">ID/Key</label>
                <div class="controls">
                    <input id="inputKeyID" type="text" name="keyID" value="<?php echo $keyID;?>">
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['providerToken'])) echo ' error';?>">
                <label class="control-label" for="inputToken">Token</label>
                <div class="controls">
                    <input id="inputToken" type="text" name="providerToken" value="<?php echo $providerToken;?>">
                </div>
            </div>
            <div class="control-group pull-left">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->mod;?></button>
                </div>
            </div>
        </form>
    </div>
</div>
