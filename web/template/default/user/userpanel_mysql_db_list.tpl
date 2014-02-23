<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li class="active">MySQL <?php echo $gsprache->databases;?></li>
        </ul>
    </div>
</div>
<?php foreach ($table as $table_row) { ?>
<div class="row-fluid span12 alert alert-success">
    <div class="row-fluid">
        <dl class="dl-horizontal">
            <dt><?php echo $gsprache->settings; ?></dt>
            <dd><a href="userpanel.php?w=my&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-primary btn-mini"><i class="icon-edit icon-white"></i></span></a></dd>
            <dt>ID</dt>
            <dd><?php echo $table_row['id'];?></dd>
            <dt><?php echo $sprache->dbname; ?></dt>
            <dd><?php echo $table_row['dbname'];?></dd>
            <dt>IP</dt>
            <dd><a href="<?php echo $table_row['interface'];?>" target="_blank"><?php echo $table_row['ip'];?></a></dd>
        </dl>
    </div>
</div>
<?php } ?>