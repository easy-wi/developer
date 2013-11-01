<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->config;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.':'.$port;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <p class="span11"><?php echo $sprache->help_config;?></p>
</div>
<hr>
<?php foreach ($configs as $config){ ?>
<div class="row-fluid">
    <div class="span6 pull-left">
        <hr>
        <strong><?php echo $config['line'];?></strong>&ensp;
        <?php if($config['permission']=="easy" or $config['permission']=="both") { ?>
        <a href="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $id;?>&amp;type=easy&amp;config=<?php echo urlencode($config['line']);?>"><span class="btn btn-primary btn-mini"><i class="icon-edit icon-white"></i> <?php echo $sprache->easy;?></span></a>
        <?php } ?>
        <?php if($config['permission']=="full" or $config['permission']=="both") { ?>
        <a href="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $id;?>&amp;type=full&amp;config=<?php echo urlencode($config['line']);?>"><span class="btn btn-primary btn-mini"><i class="icon-edit icon-white"></i> <?php echo $sprache->full;?></span></a>
        <?php } ?>
    </div>
</div>
<?php } ?>
