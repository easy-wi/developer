<section class="content-header">
    <h1><?php echo $gsprache->webspace;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=wv"><i class="fa fa-cubes"></i> <?php echo $gsprache->webspace;?></a></li>
        <li><?php echo $gsprache->reinstall;?></li>
        <li class="active"><?php echo $dns;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=wv&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">
                        <div class="box-body">

                            <div class="form-group">
                                <label for="inputDns"><?php echo $sprache->dns;?></label>
                                <div class="controls">
                                    <input class="form-control" id="inputDns" type="text" name="dns" value="<?php echo $dns;?>" readonly="readonly">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputUser"><?php echo $dedicatedLanguage->user;?></label>
                                <div class="controls">
                                    <input class="form-control" id="inputUser" type="text" name="user" value="<?php echo $user;?>" readonly="readonly">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputDelete" type="submit"><i class="fa fa-trash-o"></i>&nbsp;<?php echo $gsprache->del;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>