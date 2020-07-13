<section class="content-header">
    <h1><?php echo $gsprache->user;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=us"><i class="fa fa-user"></i> <?php echo $gsprache->user;?></a></li>
        <li><?php echo $sprache->passw;?></li>
        <li class="active"><?php echo $cname;?></li>
    </ol>
</section>

<section class="content">

    <?php if (count($errors)>0){ ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><?php echo $gsprache->errors;?></h4>
                <?php echo implode(', ',$errors);?>
            </div>
        </div>
    </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=us&amp;d=pw&amp;id=<?php echo $id;?>&amp;r=us" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="pw">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputNickname"><?php echo $sprache->nickname;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputNickname" type="text" name="nickname" value="<?php echo $cname;?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputName"><?php echo $sprache->user;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputName" type="text" name="nickname" value="<?php echo $fullName;?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword"><?php echo $sprache->passw_1;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputPassword" type="password" name="password" value="" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPasswordRepeat"><?php echo $sprache->passw_2;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputPasswordRepeat" type="password" name="pass2" value="" required>
                            </div>
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