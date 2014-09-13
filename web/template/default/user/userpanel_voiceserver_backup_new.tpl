<section class="content-header">
    <h1><?php echo $gsprache->voiceserver.' '.$sprache->backup.' '.$gsprache->add;?></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $targetFile;?>"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a></li>
        <li><?php echo $sprache->backup.' '.$gsprache->add;?></li>
        <li class="active"><?php echo $server;?></li>
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
                <?php echo $sprache->help_voiceserver_backup;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
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
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-play"></i> <?php echo $gsprache->exec;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>