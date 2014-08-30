<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->webspace;?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
		<li class="active"><?php echo $gsprache->webspace;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">
	<?php foreach ($table as $table_row) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body">
                    <h4><?php echo $table_row['dns'];?></h4>

                    <!-- Webspace Buttons -->
                    <div class="form-group">
                        <a href="userpanel.php?w=wv&amp;d=pw&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-sm btn-primary"><i class="fa fa-lock"></i> <?php echo $sprache->ftpPassword;?></span></a>
                        <?php if($table_row['usageType']=='F'){ ?><a href="userpanel.php?w=wv&amp;d=if&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-sm btn-info"><i class="fa fa-info-circle"></i> <?php echo $sprache->fdlInfo;?></button></a><?php }?>
                        <a href="userpanel.php?w=wv&amp;d=ri&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-sm btn-warning"><i class="fa fa-refresh"></i> <?php echo $dedicatedLanguage->reinstall;?></button></a>
                        <!--<a href="userpanel.php?w=wv&amp;d=bu&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-sm btn-primary"><i class="fa fa-floppy-o"></i> <?php echo $sprache->backup;?></span></a>-->
                    </div>

                    <!-- Webspace Details -->
                    <dl class="dl-horizontal">
                        <?php if($table_row['quotaActive']=='Y'){ ;?>
                        <dt><?php echo $sprache->usage;?></dt>
                        <dd><?php echo $table_row['hddUsage'];?>MB</dd>
                        <dt><?php echo $sprache->hdd;?></dt>
                        <dd><?php echo $table_row['hdd'];?>MB</dd>
                        <?php }?>
                        <dt><?php echo $gsSprache->ftp_link;?></dt>
                        <dd><a href="ftp://<?php echo $table_row['ftpUser'].':'.$table_row['ftpPass'].'@'.$table_row['ftpIP'].':'.$table_row['ftpPort'];?>">ftp://<?php echo $table_row['ftpUser'].':'.$table_row['ftpPass'].'@'.$table_row['ftpIP'].':'.$table_row['ftpPort'];?></a></dd>
                        <dt><?php echo $sprache->ftpIP;?></dt>
                        <dd>ftp://<?php echo $table_row['ftpIP'].':'.$table_row['ftpPort'];?></dd>
                        <dt><?php echo $gsSprache->ftp_user;?></dt>
                        <dd><?php echo $table_row['ftpUser'];?></dd>
                        <dt><?php echo $sprache->ftpPassword;?></dt>
                        <dd><?php echo $table_row['ftpPass'];?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
	<?php } ?>
</section>