<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=fd"><?php echo $gsprache->fastdownload;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.':'.$port;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_fdl;?></div>
</div>
<hr>
<?php if (count($errors)>0){ ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4><?php echo $gsprache->errors;?></h4>
    <?php echo implode(', ',$errors);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=fd&amp;d=es&amp;id=<?php echo $id;?>&amp;r=fd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="inputFLD"><?php echo $sprache->haupt2;?></label>
                <div class="controls">
                    <select id="inputFLD" name="masterfdl" onchange="SwitchShowHideRows('details');">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($masterfdl=="N") echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="details <?php if ($masterfdl=='Y') echo 'display_none'; ?> switch control-group<?php if(isset($errors['ftp_adresse'])) echo ' error';?>">
                <label class="control-label" for="input_ftp_adresse"><?php echo $gameSprache->ftp_adresse;?></label>
                <div id="information" class="controls">
                    <input id="input_ftp_adresse" type="text" name="ftp_adresse" value="<?php echo $ftp_adresse;?>" required>
                </div>
            </div>
            <div class="details <?php if ($masterfdl=='Y') echo 'display_none'; ?> switch control-group<?php if(isset($errors['ftp_port'])) echo ' error';?>">
                <label class="control-label" for="input_ftp_port"><?php echo $gameSprache->ftp_port;?></label>
                <div id="information" class="controls">
                    <input id="input_ftp_port" type="text" name="ftp_port" value="<?php echo $ftp_port;?>" required>
                </div>
            </div>
            <div class="details <?php if ($masterfdl=='Y') echo 'display_none'; ?> switch control-group<?php if(isset($errors['ftp_user'])) echo ' error';?>">
                <label class="control-label" for="input_ftp_user"><?php echo $gameSprache->ftp_user;?></label>
                <div id="information" class="controls">
                    <input id="input_ftp_port" type="text" name="ftp_user" value="<?php echo $ftp_user;?>" required>
                </div>
            </div>
            <div class="details <?php if ($masterfdl=='Y') echo 'display_none'; ?> switch control-group<?php if(isset($errors['ftp_password'])) echo ' error';?>">
                <label class="control-label" for="input_ftp_password"><?php echo $gameSprache->ftp_password;?></label>
                <div id="information" class="controls">
                    <input id="input_ftp_port" type="text" name="ftp_password" value="<?php echo $ftp_password;?>" required>
                </div>
            </div>
            <div class="details <?php if ($masterfdl=='Y') echo 'display_none'; ?> switch control-group">
                <label class="control-label" for="input_ftp_path"><?php echo $gameSprache->ftp_path;?></label>
                <div id="information" class="controls">
                    <input id="input_ftp_port" type="text" name="ftp_path" value="<?php echo $ftp_path;?>">
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