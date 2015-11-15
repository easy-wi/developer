<section class="content-header">
    <h1><?php echo $gsprache->webspace.' '.$gsprache->master;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=wv"><i class="fa fa-cubes"></i> <?php echo $gsprache->webspace;?></a></li>
        <li><a href="admin.php?w=wm"><i class="fa fa-server"></i> <?php echo $gsprache->webspace.' '.$gsprache->master;?></a></li>
        <li><?php echo $gsprache->reinstall;?></li>
        <li class="active"><?php echo $ip;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">

                <form role="form" action="admin.php?w=wm&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=wm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ri">

                    <div class="box-body">
                        <?php foreach($table as $id=>$vhostName){ ?>
                        <div class="checkbox">
                            <label>
                                <input id="inputVhost-<?php echo $id;?>" type="checkbox" name="dnsID[]" value="<?php echo $id;?>" checked="checked">
                                <?php echo $vhostName;?>
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