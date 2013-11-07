<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->fastdownload;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_fdl;?></div>
</div>
<hr>
<?php if ($pa['modfastdl']==true) { ?>
<div class="row-fluid">
    <div class="span4"><?php echo $sprache->haupt;?></div>
    <div class="span4"><?php echo $fdlpath[1];?></div>
    <div class="span4"><a href="userpanel.php?w=fd&amp;d=eu"><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i> <?php echo $sprache->haupt.' '.$gsprache->settings;?></span></a></div>
</div>
<?php } ?>
<?php foreach ($table as $table_row){ ?>
<hr>
<div class="row-fluid">
    <div class="span4"><?php echo $table_row['serverip']?>:<?php echo $table_row['port']?></div>
    <div class="span4">
        <form class="form-inline" method="post" action="userpanel.php?w=fd&amp;d=ud&amp;id=<?php echo $table_row['id']?>&amp;r=fd" onsubmit="return confirm('<?php echo $table_row['serverip']?>:<?php echo $table_row['port']?>: <?php echo $sprache->startfdl;?>');">
            <button class="btn btn-mini btn-primary"><i class="icon-white icon-refresh"></i> <?php echo $sprache->startfdl;?></button>
        </form>
    </div>
    <div class="span4"><?php if ($pa['modfastdl']==true) { ?><a href="userpanel.php?w=fd&amp;d=es&amp;id=<?php echo $table_row['id']?>"><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i> <?php echo $gsprache->settings;?></span></a><?php } ?></div>
</div>
<?php } ?>