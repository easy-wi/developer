<section class="content-header">
    <h1><?php echo $gsprache->fastdownload;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li><a href="userpanel.php?w=fd"><i class="fa fa-cloud-upload"></i> <?php echo $gsprache->fastdownload;?></a></li>
        <li class="active"><?php echo $serverip.':'.$port;?></li>
    </ol>
</section>


<section class="content">

	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_fdl;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="userpanel.php?w=fd&amp;d=es&amp;id=<?php echo $id;?>&amp;r=fd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="action" value="md">


                    <div class="box-body">

                        <?php if (count($errors)>0){ ?>
                        <div class="alert alert-error">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h4><?php echo $gsprache->errors;?></h4>
                            <?php echo implode(', ',$errors);?>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label for="inputFLD"><?php echo $sprache->haupt2;?></label>

                            <select class="form-control" id="inputFLD" name="masterfdl" onchange="SwitchShowHideRows('details');">
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                                <option value="N" <?php if ($masterfdl=="N") echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                            </select>
                        </div>

                        <div class="N switch <?php if ($masterfdl=='Y') echo 'display_none'; ?> form-group<?php if(isset($errors['ftp_adresse'])) echo ' has-error';?>">
                            <label for="input_ftp_adresse"><?php echo $gameSprache->ftp_adresse;?></label>
                            <input class="form-control"  id="input_ftp_adresse" type="text" name="ftp_adresse" value="<?php echo $ftp_adresse;?>" required>
                        </div>

                        <div class="N <?php if ($masterfdl=='Y') echo 'display_none'; ?> switch form-group<?php if(isset($errors['ftp_port'])) echo ' has-error';?>">
                            <label for="input_ftp_port"><?php echo $gameSprache->ftp_port;?></label>
                            <input class="form-control"  id="input_ftp_port" type="text" name="ftp_port" value="<?php echo $ftp_port;?>" required>
                        </div>

                        <div class="N <?php if ($masterfdl=='Y') echo 'display_none'; ?> switch form-group<?php if(isset($errors['ftp_user'])) echo ' has-error';?>">
                            <label for="input_ftp_user"><?php echo $gameSprache->ftp_user;?></label>
                            <input class="form-control"  id="input_ftp_port" type="text" name="ftp_user" value="<?php echo $ftp_user;?>" required>
                        </div>

                        <div class="N <?php if ($masterfdl=='Y') echo 'display_none'; ?> switch form-group<?php if(isset($errors['ftp_password'])) echo ' has-error';?>">
                            <label for="input_ftp_password"><?php echo $gameSprache->ftp_password;?></label>
                            <input class="form-control"  id="input_ftp_port" type="text" name="ftp_password" value="<?php echo $ftp_password;?>" required>
                        </div>

                        <div class="N <?php if ($masterfdl=='Y') echo 'display_none'; ?> switch form-group">
                            <label for="input_ftp_path"><?php echo $gameSprache->ftp_path;?></label>
                            <input class="form-control"  id="input_ftp_port" type="text" name="ftp_path" value="<?php echo $ftp_path;?>">
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