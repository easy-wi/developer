<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li class="active">MySQL <?php echo $gsprache->databases;?></li>
        </ul>
    </div>
</div>
<?php foreach ($table as $table_row) { ?>
<div class="row-fluid span11 alert alert-success">
    <h4 class="row-fluid span12 inline"><?php echo $table_row['dbname'].' '.$table_row['description'];?></h4>
    <div class="row-fluid">
        <div class="span12 btn-group-vertical">
            <a href="userpanel.php?w=my&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-primary btn-mini"><i class="fa fa-cog"></i> <?php echo $gsprache->settings;?></span></a>
            <?php if(strlen($table_row['interface'])>0){ ?><a href="<?php echo $table_row['interface'];?>" target="_blank"><span class="btn btn-mini btn-primary"><i class="fa fa-hdd-o"></i> phpMyAdmin</span></a><?php }?>
            <a href="userpanel.php?w=my&amp;d=ri&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-warning"><i class="fa fa-refresh"></i> <?php echo $sprache->reinstall;?></span></a>
        </div>
    </div>
    <div class="row-fluid">
        <dl class="dl-horizontal">
            <?php if(strlen($table_row['description'])>0){ ?>
            <dt><?php echo $sprache->description;?></dt>
            <dd><?php echo $table_row['description'];?></dd>
            <?php } ?>
            <dt><?php echo $sprache->dbname;?></dt>
            <dd><?php echo $table_row['dbname'];?></dd>
            <dt>IP</dt>
            <dd><?php echo $table_row['ip'];?></dd>
            <dt>Port</dt>
            <dd><?php echo $table_row['port'];?></dd>
            <dt><?php echo $sprache->dbSize;?></dt>
            <dd><?php echo $table_row['dbSize'];?>MB</dd>
        </dl>
    </div>
</div>
<?php } ?>