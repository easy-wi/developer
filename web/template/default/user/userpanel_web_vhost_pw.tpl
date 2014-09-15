<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->webspace.' '.$sprache->ftpPassword;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=wv"><?php echo $gsprache->webspace;?></a></li>
        <li><?php echo $sprache->ftpPassword;?></li>
        <li class="active"><?php echo $dns;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

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
        <div class="col-md-11">
            <div class="box box-info">
                <form role="form" action="userpanel.php?w=wv&amp;d=pw&amp;id=<?php echo $id;?>&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="pw">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputPassword1"><?php echo $sprache->ftpPassword;?></label>
                            <input class="form-control" id="inputPassword1" type="password" name="password1" value="" maxlength="40">
                        </div>

                        <div class="form-group">
                            <label for="inputPassword2"><?php echo $sprache->ftpPasswordRepeat;?></label>
                            <input class="form-control" id="inputPassword2" type="password" name="password2" value="" maxlength="40">
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save"></i> <?php echo $gsprache->save;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>