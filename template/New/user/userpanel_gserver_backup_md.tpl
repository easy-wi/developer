<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li><i class="fa fa-floppy-o"></i> <?php echo $gsprache->backup;?></li>
        <li><?php echo $serverip.":".$port;?></li>
        <li class="active"><?php echo $gsprache->settings;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">

            <div class="box box-primary">

                <form role="form" action="userpanel.php?w=bu&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="action" value="md2">

                    <div class="box-body">
                        <?php if (count($errors)>0){ ?>
                        <div class="alert alert-danger alert-dismissable">
                            <h4><?php echo $gsprache->errors;?></h4>
                            <?php echo implode(', ',$errors);?>
                        </div>
                        <?php }?>

                        <div class="form-group<?php if(isset($errors['ftp_adresse'])) echo ' has-error';?>">
                            <label for="input_ftp_adresse"><?php echo $sprache->ftp_adresse;?></label>
                            <input id="input_ftp_adresse" type="text" class="form-control" name="ftp_adresse" value="<?php echo $ftp_adresse;?>" required/>
                        </div>

                        <div class="form-group<?php if(isset($errors['ftp_port'])) echo ' has-error';?>">
                            <label for="input_ftp_port"><?php echo $sprache->ftp_port;?></label>
                            <input id="input_ftp_port" type="text" class="form-control" name="ftp_port" value="<?php echo $ftp_port;?>" required/>
                        </div>

                        <div class="form-group<?php if(isset($errors['ftp_user'])) echo ' has-error';?>">
                            <label for="input_ftp_user"><?php echo $sprache->ftp_user;?></label>
                            <input id="input_ftp_user" type="text" class="form-control" name="ftp_user" value="<?php echo $ftp_user;?>" required/>
                        </div>

                        <div class="form-group<?php if(isset($errors['ftp_password'])) echo ' has-error';?>">
                            <label for="input_ftp_password"><?php echo $sprache->ftp_password;?></label>
                            <input id="input_ftp_password" type="text" class="form-control" name="ftp_password" value="<?php echo $ftp_password;?>" required/>
                        </div>

                        <div class="form-group<?php if(isset($errors['ftp_path'])) echo ' has-error';?>">
                            <label for="input_ftp_path"><?php echo $sprache->ftp_path;?></label>
                            <input id="input_ftp_path" type="text" class="form-control" name="ftp_path" value="<?php echo $ftp_path;?>" required/>
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