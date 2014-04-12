<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->webspace;?></li>
        </ul>
    </div>
</div>
<hr>
<?php foreach ($table as $table_row) { ?>
<div class="row-fluid span11 alert alert-success">
    <h4 class="row-fluid span12 inline"><?php echo $table_row['dns'];?></h4>
    <div class="row-fluid">
        <div class="span12 btn-group-vertical">
            <a href="userpanel.php?w=wv&amp;d=pw&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-primary"><i class="fa fa-lock"></i> <?php echo $sprache->ftpPassword;?></span></a>
            <?php if($table_row['usageType']=='F'){ ?><a href="userpanel.php?w=wv&amp;d=if&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-mini btn-info"><i class="fa fa-info-circle"></i> <?php echo $sprache->fdlInfo;?></button></a><?php }?>
            <a href="userpanel.php?w=wv&amp;d=ri&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-mini btn-warning"><i class="fa fa-refresh"></i> <?php echo $dedicatedLanguage->reinstall;?></button></a>
            <!--<a href="userpanel.php?w=wv&amp;d=bu&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-primary"><i class="fa fa-floppy-o"></i> <?php echo $sprache->backup;?></span></a>-->
        </div>
    </div>
    <div class="row-fluid">
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
<?php } ?>