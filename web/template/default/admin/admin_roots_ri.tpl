<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=ro"><?php echo $gsprache->gameroot;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsSprache->reinstall;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=ro&amp;d=ri&amp;id=<?php echo $id;?>" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ri">
            <?php foreach($table as $k=>$v){ ?>
            <div class="control-group">
                <label class="control-label" for="inputServer-<?php echo $k;?>"><?php echo $v['ip'].':'.$v['port'];?></label>
                <div class="controls">
                    <input id="inputServer-<?php echo $k;?>" type="checkbox" name="serverID[]" value="<?php echo $k;?>" checked="checked">
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-refresh"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>