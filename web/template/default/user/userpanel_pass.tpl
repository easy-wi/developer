<section class="content-header">
    <h1><?php echo $gsprache->settings.' '.$sprache->passw;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=se"><?php echo $gsprache->settings;?></a></li>
        <li class="active"><?php echo $sprache->passw;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-11">
            <div class="box box-info">
                <form role="form" action="userpanel.php?w=se&amp;d=pw&amp;r=se" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="password1"><?php echo $sprache->passw_1;?></label>
                            <input class="form-control" id="password1" type="password" name="password" value="" required>
                        </div>

                        <div class="form-group">
                            <label for="password2"><?php echo $sprache->passw_2;?></label>
                            <input class="form-control" id="password2" type="password" name="pass2" value="" required>
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