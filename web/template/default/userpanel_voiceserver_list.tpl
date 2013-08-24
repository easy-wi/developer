<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->voiceserver;?></li>
        </ul>
    </div>
</div>
<?php foreach ($table as $table_row) { ?>
<div class="row-fluid span11 alert <?php if($table_row['stopped']=='Y') echo 'alert-block'; else if($table_row['stopped']=='C') echo 'alert-error'; else echo 'alert-success';?>">
    <h5><?php echo $table_row['server'];?></h5>
    <div class="row-fluid">
        <div class="span2">
            <form method="post" action="userpanel.php?w=vo&amp;d=st&amp;id=<?php echo $table_row['id'];?>&amp;r=vo" onsubmit="return confirm('<?php echo $table_row['address'];?>: <?php echo $sprache->confirm_restart;?>');" class="form-inline">
                <input type="hidden" name="action" value="re">
                <a href="#"><button class="btn btn-mini btn-success"><i class="icon-white icon-play"></i> <?php echo $gsprache->start;?></button></a>
            </form>
        </div>
        <?php if($table_row['stopped']=='N'){ ?>
        <div class="span2">
            <form method="post" action="userpanel.php?w=vo&amp;d=st&amp;id=<?php echo $table_row['id'];?>&amp;r=vo" onsubmit="return confirm('<?php echo $table_row['address'];?>: <?php echo $sprache->confirm_stop;?>');" class="form-inline">
                <input type="hidden" name="action" value="so">
                <a href="#"><button class="btn btn-mini btn-danger"><i class="icon-white icon-stop"></i> <?php echo $gsprache->stop;?></button></a>
            </form>
        </div>
        <?php } ?>
        <div class="span2">
            <a href="userpanel.php?w=vo&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-primary"><i class="icon-white icon-pencil"></i> <?php echo $gsprache->settings;?></span></a>
        </div>
        <div class="span2">
            <form method="post" action="userpanel.php?w=vo&amp;d=rs&amp;id=<?php echo $table_row['id'];?>&amp;r=vo" onsubmit="return confirm('<?php echo $table_row['address'];?>: <?php echo $sprache->confirm_restart;?>');" class="form-inline">
                <input type="hidden" name="action" value="rs">
                <a href="#"><button class="btn btn-mini btn-warning"><i class="icon-white icon-refresh"></i> <?php echo $sprache->reset;?></button></a>
            </form>
        </div><?php if($table_row['backup']=='Y'){ ?>
        <div class="span2">
            <a href="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-info"><i class="icon-white icon-bold"></i> <?php echo $sprache->backup;?></span></a>
        </div>
        <?php } ?>
        <div class="span2">
            <a href="userpanel.php?w=vo&amp;d=pk&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-inverse"><i class="icon-white icon-lock"></i> Token</span></a>
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