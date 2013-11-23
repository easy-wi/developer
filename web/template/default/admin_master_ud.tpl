<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $sprache->header_update;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=ma&amp;d=ud" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ud">
            <div class="row-fluid">
                <div class="span6">
                    <?php foreach ($table as $table_row){ ?>
                    <?php if (isset($table_row['game'])) { ?>
                    <div class="control-group">
                        <label class="control-label" for="inputEdit<?php echo $table_row['game']['description'];?>"><?php echo '<img src="images/games/icons/'.$table_row['game']['shorten'].'.png" alt="'.$table_row['game']['shorten'].'" width="16" />';?> <?php echo $table_row['game']['description'];?></label>
                        <div class="controls"><input id="inputEdit<?php echo $table_row['game']['description'];?>" type="checkbox" name="description[]" value="<?php echo $table_row['game']['description'];?>"></div>
                    </div>
                    <?php }} ?>
                </div>
                <div class="span6">
                    <?php foreach ($table as $table_row){ ?>
                    <?php if (isset($table_row['server'])) { ?>
                    <div class="control-group">
                        <label class="control-label" for="inputEdit<?php echo $table_row['server']['ip'];?>"><?php echo $table_row['server']['ip'];?></label>
                        <div class="controls"><input id="inputEdit<?php echo $table_row['server']['ip'];?>" type="checkbox" name="id[]" value="<?php echo $table_row['server']['id'];?>"></div>
                    </div>
                    <?php }} ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-refresh"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>