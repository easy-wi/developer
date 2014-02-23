<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->config;?> <span class="divider">/</span></li>
            <li><?php echo $serverip.':'.$port;?> <span class="divider">/</span></li>
            <li class="active"><?php echo htmlentities($configname);?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_config;?></div>
</div>
<hr>
<div class="row-fluid">
    <div class="span12">
        <form class="form-horizontal" action="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $id;?>&amp;type=full&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <textarea id="inputConfig"  rows="20" class="span12" name="cleanedconfig"><?php echo $cleanedconfig;?></textarea>
            </div>
            <div class="control-group">
                <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                <input type="hidden" name="config" value="<?php echo $postconfig;?>">
                <input type="hidden" name="update" value="1">
            </div>
        </form>
    </div>
</div>