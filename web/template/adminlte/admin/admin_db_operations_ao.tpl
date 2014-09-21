<!-- Content Header -->
<section class="content-header">
    <h1>Easy-WI <?php echo $gsprache->databases.' '.$gsprache->gameserver.' '.$gsprache->addons;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li>Easy-WI <?php echo $gsprache->databases;?></li>
        <li class="active"><?php echo $gsprache->gameserver.' '.$gsprache->addons;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <form role="form" action="admin.php?w=bu&amp;d=ra&amp;r=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                <input type="hidden" name="action" value="ra">

                <div class="box box-info">
                    <div class="box-body">

                        <div class="box-header">
                            <h3 class="box-title"><?php echo $gsprache->gameserver.' '.$gsprache->template;?></h3>
                        </div>

                        <div class="box-body">

                            <?php foreach ($gameAddons as $image) { ?>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="inputGame-<?php echo $image[':addon'];?>" name="addons[]" value="<?php echo $image[':addon'];?>">
                                    <?php echo $image[':menudescription'];?> <?php echo '('.implode(', ',$image[':supported']).')';?>
                                </label>
                            </div>
                            <?php } ?>

                            <div class="checkbox">
                                <label>
                                    <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'addons[]')">
                                    <?php echo $gsprache->all;?>
                                </label>
                            </div>

                            <div class="form-group" id="typeGroup">
                                <label for="actionType"></label>
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-success active">
                                        <input type="radio" name="actionType" value="1" checked> <?php echo $gsprache->add;?>
                                    </label>
                                    <label class="btn btn-warning">
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