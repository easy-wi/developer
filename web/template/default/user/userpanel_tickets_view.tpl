<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=ti"><?php echo $gsprache->support;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $topic;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <dl class="dl-horizontal">
        <dt><?php echo $sprache->status;?></dt>
        <dd><?php echo $status;?></dd>
        <dt><?php echo $sprache->priority;?></dt>
        <dd><?php echo $priority;?></dd>
        <?php if($open=="Y") { ?>
        <dt><?php echo $gsprache->mod;?></dt>
        <dd><a href="userpanel.php?w=ti&d=md&amp;id=<?php echo $id;?>&amp;action=md"><span class="btn btn-primary btn-mini"><i class="icon-edit icon-white"></i></a></dd>
        <dt><?php echo $sprache->close_heading;?></dt>
        <dd><a href="userpanel.php?w=ti&d=md&amp;id=<?php echo $id;?>&amp;action=cl"><span class="btn btn-primary btn-mini"><i class="icon-lock icon-white"></i></span></a></dd>
        <?php } else if ($open=="D") { ?>
        <dt><?php echo $sprache->reopen;?></dt>
        <dd><a href="userpanel.php?w=ti&d=md&amp;id=<?php echo $id;?>&amp;action=op&amp;r=ti"><span class="btn btn-primary btn-mini"><i class="icon-lock icon-white"></i></a></dd>
        <?php } ?>
    </dl>
</div>
<hr>
<?php foreach ($table as $table_row) { ?>
<div class="row-fluid">
    <h5><a href="#" onclick="textdrop('<?php echo $table_row['writedate'];?>')"><?php echo $table_row['writedate'];?></a> <?php echo $sprache->writer.': '.$table_row['writer'];?></h5>
    <div id="<?php echo $table_row['writedate'];?>" class="span12">
        <?php echo $table_row['ticket'];?>
    </div>
</div>
<?php } ?>