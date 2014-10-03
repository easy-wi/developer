<section class="content-header">
    <h1><?php echo $gsprache->master_apps;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ro"><i class="fa fa-hdd-o"></i> <?php echo $gsprache->appRoot;?></a></li>
        <li><a href="admin.php?w=ma&amp&d=md"><i class="fa fa-puzzle-piece"></i> <?php echo $gsprache->master_apps;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $ip;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=ma&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=ma" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-header">
                        <h3 class="box-title"><?php echo $ip.' '.$description;?></h3>
                    </div>

                    <div class="box-body">
                        <?php foreach ($table as $table_row){ ?>
                        <div class="checkbox">
                            <label>
                                <input id="input<?php echo $table_row['shorten'];?>" type="checkbox" name="id[]" value="<?php echo $table_row['id'];?>">
                                <?php echo '<img src="images/games/icons/'.$table_row['shorten'].'.png" alt="'.$table_row['shorten'].'" width="16" /> '.$table_row['description'];?>
                            </label>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash">&nbsp;<?php echo $gsprache->del;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>