<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a>
        <li><?php echo $sprache->config;?></li>
        <li><?php echo $serverip.':'.$port;?></li>
        <li class="active"><?php echo htmlentities($configname);?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">

    <!-- Content Help -->
	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_config;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <form role="form" action="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $id;?>&amp;type=full&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="config" value="<?php echo $postconfig;?>">
                    <input type="hidden" name="update" value="1">

                    <div class="box-header">
                        <h3 class="box-title"><?php echo htmlentities($configname);?></h3>
                    </div>

                    <div class="body-box">
                        <div class="form-group">
                            <label for="inputConfig"></label>
                            <textarea class="form-control" id="inputConfig" rows="15" name="cleanedconfig"><?php echo $cleanedconfig;?></textarea>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>

</section>