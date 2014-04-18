<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=my">MySQL Server</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->reinstall;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=my&amp;d=rs&amp;id=<?php echo $id;?>&amp;r=my" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="rs">
            <?php foreach($installedDbs as $k => $v){ ?>
            <div class="control-group">
                <label class="control-label" for="inputDB-<?php echo $k;?>"><?php echo $v;?></label>
                <div class="controls">
                    <input id="inputDB-<?php echo $k;?>" type="checkbox" name="dbID[]" value="<?php echo $k;?>" checked="checked">
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-warning" id="inputEdit" type="submit"><i class="fa fa-refresh"></i> <?php echo $sprache->reinstall;?></button>
                </div>
            </div>
        </form>
    </div>
</div>