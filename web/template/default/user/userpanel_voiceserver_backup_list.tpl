<section class="content-header">
    <h1><?php echo $gsprache->voiceserver.' '.$sprache->backup;?></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $targetFile;?>"><i class="fa fa-home"></i> Home</a></li>
		<li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a></li>
		<li><?php echo $sprache->backup;?></li>
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
            <div class="box box-primary">
                <div class="box-body">

                    <div>
                        <a href="userpanel.php?w=vo&amp;d=bu&amp;po=1&amp;id=<?php echo $id;?>"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $sprache->backup ." (".($voice_maxbackup-$backupcount)." ".$sprache->left.")";?></span></a>
                    </div>

                    <hr>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th><?php echo $sprache->date;?></th>
                                <th><?php echo $sprache->backupname;?></th>
                                <th><?php echo $sprache->recover;?></th>
                                <th><?php echo $gsprache->del;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($table as $table_row) { ?>
                            <tr>
                                <td><?php echo $table_row['date']; ?></td>
                                <td><?php echo $table_row['name']; ?></td>
                                <td>
                                    <form method="post" action="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $id;?>&amp;r=vo" name="form" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                                        <button class="btn btn-primary btn-sm" id="inputEdit" type="submit"><i class="fa fa-refresh"></i></button>
                                        <input type="hidden" name="action" value="md" />
                                        <input type="hidden" name="use" value="md" />
                                        <input type="hidden" name="id" value="<?php echo $table_row['id'];?>" />
                                    </form>
                                </td>
                                <td>
                                    <form method="post" action="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $id;?>&amp;r=vo" name="form" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                                        <button class="btn btn-danger btn-sm" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i></button>
                                        <input type="hidden" name="action" value="md" />
                                        <input type="hidden" name="delete" value="md" />
                                        <input type="hidden" name="id" value="<?php echo $table_row['id'];?>" />
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>