<section class="content-header">
    <h1><?php echo $gsprache->voiceserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">
	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_voiceserver_list;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <?php foreach ($table as $table_row) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-secondary" role="alert">
            <div class="panel box <?php if($table_row['stopped']=='Y') echo 'box-primary'; else if($table_row['stopped']=='C') echo 'box-danger'; else echo 'box-success';?>">
                <div class="box-body">

                    <h4><?php echo (strlen($table_row['description']) == 0) ? $table_row['server'] : $table_row['description'] . ' ' . $table_row['server'];?></h4>

                    <div class="form-group">
                        <a href="userpanel.php?w=vo&amp;d=st&amp;id=<?php echo $table_row['id'];?>&amp;action=re&amp;r=vo" onclick="return confirm('<?php echo $table_row['address'];?>: <?php echo $sprache->confirm_restart;?>');"><button class="btn btn-outline-success"><i class="icon-white icon-play"></i> <?php echo $gsprache->start;?></button></a>
                        <a href="userpanel.php?w=vo&amp;d=st&amp;id=<?php echo $table_row['id'];?>&amp;action=so&amp;r=vo" onclick="return confirm('<?php echo $table_row['address'];?>: <?php echo $sprache->confirm_stop;?>');"><button class="btn btn-outline-danger"><i class="fa fa-power-off"></i> <?php echo $gsprache->stop;?></button></a>
                        <a href="userpanel.php?w=vo&amp;d=pk&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-outline-info"><i class="fa fa-key"></i> Token</span></a>
                        <a href="userpanel.php?w=vo&amp;d=bl&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-outline-info"><i class="fa fa-ban"></i> <?php echo $sprache->banList;?></span></a>
                        <?php if($table_row['backup']=='Y'){ ?><a href="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-outline-info"><i class="fa fa-floppy-o"></i> <?php echo $sprache->backup;?></span></a><?php } ?>
                        <a href="userpanel.php?w=vo&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-outline-info"><i class="fa fa-cog"></i> <?php echo $gsprache->settings;?></span></a>
                        <a href="userpanel.php?w=vo&amp;d=rs&amp;id=<?php echo $table_row['id'];?>&amp;action=rs&amp;r=vo" onclick="return confirm('<?php echo $table_row['address'];?>: <?php echo $sprache->confirm_restart;?>');"><button class="btn btn-outline-warning"><i class="fa fa-refresh"></i> <?php echo $sprache->reset;?></button></a>
                    </div>

                    <dl class="dl-horizontal">
                        <dt>ID</dt>
                        <dd><?php echo $table_row['virtual_id'];?></dd>
                        <dt><?php echo $sprache->usage; ?></dt>
                        <dd><?php echo $table_row['usage'];?></dd>
                        <dt>Uptime</dt>
                        <dd><?php echo $table_row['uptime'];?></dd>
                        <dt>Traffic</dt>
                        <dd><?php echo $table_row['filetraffic'].'/'.$table_row['maxtraffic'];?> MB</dd>
                    </dl>
                </div>
            </div>
        </div></div>
    </div>
    <?php } ?>
</section>