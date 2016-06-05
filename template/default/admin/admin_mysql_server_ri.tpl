<section class="content-header">
    <h1>MySQL Server</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=md"><i class="fa fa-database"></i> MySQL</a></li>
        <li><a href="admin.php?w=my"><i class="fa fa-server"></i> MySQL Server</a></li>
        <li><?php echo $gsprache->reinstall;?></li>
        <li class="active"><?php echo $ip;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">

                <form role="form" action="admin.php?w=my&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=my" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ri">

                    <div class="box-body">
                        <?php foreach($table as $id=>$dbName){ ?>
                        <div class="checkbox">
                            <label>
                                <input id="inputDB-<?php echo $id;?>" type="checkbox" name="db[]" value="<?php echo $id;?>" checked="checked">
                                <?php echo $dbName;?>
                            </label>
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