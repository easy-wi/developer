<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=ma"><?php echo $gsprache->master;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->add;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=ma&amp;d=ad&amp;id=<?php echo $id;?>&amp;r=ma" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <?php foreach ($table as $table_row){ ?>
            <div class="control-group">
                <label class="control-label" for="input<?php echo $table_row['shorten'];?>"><?php echo '<img src="images/games/icons/'.$table_row['shorten'].'.png" alt="'.$table_row['shorten'].'" width="16" /> '.$table_row['description'];?></label>
                <div class="controls"><input id="input<?php echo $table_row['shorten'];?>" type="checkbox" name="id[]" value="<?php echo $table_row['id'];?>"></div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>