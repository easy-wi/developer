<section class="content-header">
    <h1>Easy-WI <?php echo $gsprache->databases.' '.$gsprache->gameserver.' '.$gsprache->template;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li><i class="fa fa-database"></i> Easy-WI <?php echo $gsprache->databases;?></li>
        <li class="active"><?php echo $gsprache->gameserver.' '.$gsprache->template;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <form role="form" action="admin.php?w=bu&amp;d=rg&amp;r=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                <input type="hidden" name="action" value="rg">

                <div class="box box-primary">
                    <div class="box-body">

                        <div class="box-header">
                            <h3 class="box-title"><?php echo $gsprache->gameserver.' '.$gsprache->template;?></h3>
                        </div>

                        <div class="box-body">

                            <?php foreach ($gameImages as $image) { ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputGame-<?php echo $image[':shorten'];?>" name="games[]" value="<?php echo $image[':shorten'];?>">
                                    <img src="images/games/icons/<?php echo $image[':shorten'];?>.png" alt="<?php echo $image[':shorten'];?>" width="16"> <?php echo $image[':description'];?>
                                </label>
                            </div>
                            <?php } ?>

                            <div class="checkbox">
                                <label>
                                    <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'games[]')">
                                    <?php echo $gsprache->all;?>
                                </label>
                            </div>

                            <div class="form-group" id="typeGroup">
                                <label for="actionType"></label>
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-success active">
                                        <input type="radio" name="actionType" value="1" checked> <?php echo $gsprache->add;?>
                                    </label>
                                    <label class="btn btn-primary">
                                        <input type="radio" name="actionType" value="2"> <?php echo $gsprache->mod;?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-play-circle"></i> <?php echo $gsprache->exec;?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>