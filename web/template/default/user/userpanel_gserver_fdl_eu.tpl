<section class="content-header">
    <h1><?php echo $gsprache->fastdownload;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a></li>
        <li><?php echo $gsprache->fastdownload;?></li>
        <li class="active"><?php echo $sprache->haupt;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_fdl;?>
            </div>
        </div>
    </div>

    <?php if (count($errors)>0){ ?>
    <div class="box box-danger">
        <div class="box-header">
            <i class="fa fa-warning"></i>
            <h3 class="box-title"><?php echo $gsprache->errors;?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <b><?php echo $gsprache->errors;?>:</b> <?php echo implode(', ',$errors);?>
            </div>
        </div>
    </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">

                <form role="form" action="userpanel.php?w=fd&amp;d=eu&amp;r=fd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="action" value="md">

                    <div class="box-body">
                        <div class="form-group<?php if(isset($errors['ftp_adresse'])) echo ' error';?>">
                            <label for="input_ftp_adresse"><?php echo $gameSprache->ftp_adresse;?></label>
                            <input class="form-control"  id="input_ftp_adresse" type="text" name="ftp_adresse" value="<?php echo $ftp_adresse;?>" required>
                        </div>

                        <div class="form-group<?php if(isset($errors['ftp_port'])) echo ' error';?>">
                            <label for="input_ftp_port"><?php echo $gameSprache->ftp_port;?></label>
                            <input class="form-control"  id="input_ftp_port" type="text" name="ftp_port" value="<?php echo $ftp_port;?>" required>
                        </div>

                        <div class="form-group<?php if(isset($errors['ftp_user'])) echo ' error';?>">
                            <label for="input_ftp_user"><?php echo $gameSprache->ftp_user;?></label>
                            <input class="form-control"  id="input_ftp_port" type="text" name="ftp_user" value="<?php echo $ftp_user;?>" required>
                        </div>

                        <div class="form-group<?php if(isset($errors['ftp_password'])) echo ' error';?>">
                            <label for="input_ftp_password"><?php echo $gameSprache->ftp_password;?></label>
                            <input class="form-control"  id="input_ftp_port" type="text" name="ftp_password" value="<?php echo $ftp_password;?>" required>
                        </div>

                        <div class="form-group">
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