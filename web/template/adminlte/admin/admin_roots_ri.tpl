<section class="content-header">
    <h1><?php echo $gsprache->appRoot;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ro"><?php echo $gsprache->appRoot;?></a></li>
        <li><?php echo $gsprache->reinstall;?></li>
        <li class="active"><?php echo $ip;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-11">
            <div class="box box-warning">

                <form role="form" action="admin.php?w=ro&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=ro" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ri">

                    <div class="box-body">

                        <?php foreach($table as $k=>$v){ ?>
                        <div class="form-group">
                            <input id="inputServer-<?php echo $k;?>" type="checkbox" name="serverID[]" value="<?php echo $k;?>" checked="checked">
                            <label class="control-label" for="inputServer-<?php echo $k;?>"><?php echo $v['ip'].':'.$v['port'];?></label>
                        </div>
                        <?php }?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-warning" id="inputReinstall" type="submit"><i class="fa fa-refresg">&nbsp;<?php echo $gsprache->reinstall;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>