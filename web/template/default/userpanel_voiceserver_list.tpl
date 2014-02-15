<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->voiceserver;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_voiceserver_list;?></div>
</div>
<hr>
<?php foreach ($table as $table_row) { ?>
<div class="row-fluid span11 alert <?php if($table_row['stopped']=='Y') echo 'alert-block'; else if($table_row['stopped']=='C') echo 'alert-error'; else echo 'alert-success';?>">
    <h4 class="row-fluid span12 inline"><?php echo $table_row['server'];?></h4>
    <div class="row-fluid">
        <div class="span12 btn-group-vertical">
            <a href="userpanel.php?w=vo&amp;d=st&amp;id=<?php echo $table_row['id'];?>&amp;action=re&amp;r=vo" onclick="return confirm('<?php echo $table_row['address'];?>: <?php echo $sprache->confirm_restart;?>');"><button class="btn btn-mini btn-success"><i class="icon-white icon-play"></i> <?php echo $gsprache->start;?></button></a>
            <a href="userpanel.php?w=vo&amp;d=st&amp;id=<?php echo $table_row['id'];?>&amp;action=so&amp;r=vo" onclick="return confirm('<?php echo $table_row['address'];?>: <?php echo $sprache->confirm_stop;?>');"><button class="btn btn-mini btn-danger"><i class="fa fa-power-off"></i> <?php echo $gsprache->stop;?></button></a>
            <a href="userpanel.php?w=vo&amp;d=pk&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-primary"><i class="fa fa-key"></i> Token</span></a>
            <a href="userpanel.php?w=vo&amp;d=bl&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-primary"><i class="fa fa-ban"></i> <?php echo $sprache->banList;?></span></a>
            <?php if($table_row['backup']=='Y'){ ?><a href="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-primary"><i class="fa fa-floppy-o"></i> <?php echo $sprache->backup;?></span></a><?php } ?>
            <a href="userpanel.php?w=vo&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-primary"><i class="fa fa-cog"></i> <?php echo $gsprache->settings;?></span></a>
            <a href="userpanel.php?w=vo&amp;d=rs&amp;id=<?php echo $table_row['id'];?>&amp;action=rs&amp;r=vo" onclick="return confirm('<?php echo $table_row['address'];?>: <?php echo $sprache->confirm_restart;?>');"><button class="btn btn-mini btn-warning"><i class="fa fa-refresh"></i> <?php echo $sprache->reset;?></button></a>
        </div>
    </div>
    <div class="row-fluid">
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
<?php } ?>