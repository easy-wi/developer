<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->backup;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.":".$port;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->settings;?></li>
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
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=bu&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

            <input type="hidden" name="action" value="md2" >

            <div class="control-group<?php if(isset($errors['ftp_adresse'])) echo ' error';?>">
                <label class="control-label" for="input_ftp_adresse"><?php echo $sprache->ftp_adresse;?></label>
                <div id="information" class="controls">
                    <input id="input_ftp_adresse" type="text" name="ftp_adresse" value="<?php echo $ftp_adresse;?>" required>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['ftp_port'])) echo ' error';?>">
                <label class="control-label" for="input_ftp_port"><?php echo $sprache->ftp_port;?></label>
                <div id="information" class="controls">
                    <input id="input_ftp_port" type="text" name="ftp_port" value="<?php echo $ftp_port;?>" required>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['ftp_user'])) echo ' error';?>">
                <label class="control-label" for="input_ftp_user"><?php echo $sprache->ftp_user;?></label>
                <div id="information" class="controls">
                    <input id="input_ftp_port" type="text" name="ftp_user" value="<?php echo $ftp_user;?>" required>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['ftp_password'])) echo ' error';?>">
                <label class="control-label" for="input_ftp_password"><?php echo $sprache->ftp_password;?></label>
                <div id="information" class="controls">
                    <input id="input_ftp_port" type="text" name="ftp_password" value="<?php echo $ftp_password;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="input_ftp_path"><?php echo $sprache->ftp_path;?></label>
                <div id="information" class="controls">
                    <input id="input_ftp_port" type="text" name="ftp_path" value="<?php echo $ftp_path;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>