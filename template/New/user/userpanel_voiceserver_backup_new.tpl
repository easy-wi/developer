<section class="content-header">
    <h1><?php echo $sprache->backup;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><i class="fa fa-floppy-o"></i> <?php echo $sprache->backup;?></li>
        <li><?php echo $gsprache->add;?></li>
        <li class="active"><?php echo $server;?></li>
    </ol>
</section>


<section class="content">

	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_voiceserver_backup;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <form role="form" action="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $id;?>&amp;r=vo" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="action" value="nb">

                    <div class="box-body">
                        <div class="form-group">
                            <label><?php echo $sprache->backups;?></label>
                            <input class="form-control" type="text" class="form-control" placeholder="<?php echo $backupcount.'/'.$voice_maxbackup;?>" disabled/>
                        </div>

                        <div class="form-group">
                            <label for="name"><?php echo $sprache->backupname;?></label>
                            <input class="form-control" id="name" type="text" name="name" placeholder="New Backup" required>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->exec;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>