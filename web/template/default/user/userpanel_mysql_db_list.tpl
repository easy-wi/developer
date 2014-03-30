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
        </div>
    </div>
    <div class="row-fluid">
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->dbname;?></dt>
            <dd><?php echo $table_row['dbname'];?></dd>
            <dt><?php echo $sprache->description;?></dt>
            <dd><?php echo $table_row['description'];?></dd>
            <dt>IP</dt>
            <dd><?php echo $table_row['ip'];?></dd>
            <dt>Port</dt>
            <dd><?php echo $table_row['port'];?></dd>
            <dt><?php echo $sprache->dbSize;?></dt>
            <dd><?php echo $table_row['dbSize'];?>MB</dd>
            <?php if(strlen($table_row['interface'])>0){ ?>
            <dt>phpMyAdmin</dt>
            <dd><a href="<?php echo $table_row['interface'];?>" target="_blank"><?php echo $table_row['interface'];?></a></dd>
            <?php }?>
        </dl>
    </div>
</div>
<?php } ?>