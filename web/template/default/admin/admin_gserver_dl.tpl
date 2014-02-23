<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->del;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.':'.$port;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=gs&amp;d=dl&amp;id=<?php echo $server_id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">
            <div class="control-group">
                <label class="control-label" for="inputSafe"><?php echo $gsprache->del;?></label>
                <div class="controls">
                    <select id="inputSafe" name="safeDelete">
                        <option value="S"><?php echo $gsprache->delSafe;?></option>
                        <option value="A"><?php echo $gsprache->delAny;?></option>
                        <option value="D"><?php echo $gsprache->delDB;?></option>
                    </select>
                </div>
            </div>
            <?php foreach ($table as $table_row){ ?>
            <div class="control-group">
                <label class="control-label" for="inputShorten-<?php echo $table_row['id'];?>"><?php echo '<img src="images/games/icons/'.$table_row['shorten'].'.png" alt="'.$table_row['shorten'].'" width="16" /> '.$table_row['description'];?></label>
                <div class="controls"><input id="inputShorten-<?php echo $table_row['id'];?>" type="checkbox" name="id[]" value="<?php echo $table_row['id'];?>"></div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-danger pull-left" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
                </div>
            </div>
        </form>
    </div>
</div>