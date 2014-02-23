<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->reinstall;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.':'.$port;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=gs&amp;d=ri&amp;id=<?php echo $server_id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ri">
            <div class="control-group">
                <label class="control-label"><?php echo $sprache->action;?></label>
                <div class="controls">
                    <label class="radio">
                        <input type="radio" name="type" id="optionsRadios1" value="N" checked="checked"> <?php echo $sprache->resync;?>
                    </label>
                    <label class="radio">
                        <input type="radio" name="type" id="optionsRadios2" value="Y"> <?php echo $sprache->reinstall;?>
                    </label>
                </div>
            </div>
            <?php foreach ($table as $table_row){ ?>
            <div class="control-group">
                <label class="control-label" for="inputTemplate-<?php echo $table_row['id'];?>"><?php echo '<img src="images/games/icons/'.$table_row['shorten'].'.png" alt="'.$table_row['shorten'].'" width="16" /> '.$table_row['description'];?></label>
                <div class="controls">
                    <select id="inputTemplate-<?php echo $table_row['id'];?>" name="template[<?php echo $table_row['id'];?>]">
                        <option value="0"><?php echo $gsprache->no;?></option>
                        <option value="1" <?php if($table_row['servertemplate']==1) echo "selected";?>><?php echo $table_row['shorten'];?></option>
                        <option value="2" <?php if($table_row['servertemplate']==2) echo "selected";?>><?php echo $table_row['shorten'];?>-2</option>
                        <option value="3" <?php if($table_row['servertemplate']==3) echo "selected";?>><?php echo $table_row['shorten'];?>-3</option>
                        <option value="4"><?php echo $gsprache->all;?></option>
                    </select>
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-refresh"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>