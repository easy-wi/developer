<!-- Content Header -->
<section class="content-header">
    <h1>MySQL <?php echo $gsprache->databases;?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
		<li class="active">MySQL <?php echo $gsprache->databases;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">
	<?php foreach ($table as $table_row) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body">
                    <h4><?php echo $table_row['dbname'].' '.$table_row['description'];?></h4>

                    <!-- Mysql Buttons -->
                    <div class="form-group">
                        <a href="userpanel.php?w=my&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-primary btn-sm"><i class="fa fa-cog"></i> <?php echo $gsprache->settings;?></span></a>
                        <?php if(strlen($table_row['interface'])>0){ ?><a href="<?php echo $table_row['interface'];?>" target="_blank"><span class="btn btn-sm btn-primary"><i class="fa fa-hdd-o"></i> phpMyAdmin</span></a><?php }?>
                        <a href="userpanel.php?w=my&amp;d=ri&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-sm btn-warning"><i class="fa fa-refresh"></i> <?php echo $sprache->reinstall;?></span></a>
                    </div>

                    <!-- Mysql Details -->
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
        </div>
    </div>
	<?php } ?>
</section>