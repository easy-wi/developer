<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li>Easy-WI <?php echo $gsprache->databases;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->gameserver.' '.$gsprache->addons;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=bu&amp;d=ra&amp;r=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="action" value="ra">
            <?php foreach ($gameAddons as $image) { ?>
            <div class="control-group">
                <label class="control-label" for="inputGame-<?php echo $image[':addon'];?>"><?php echo $image[':menudescription'];?></label>
                <div class="controls">
                    <input type="checkbox" id="inputGame-<?php echo $image[':addon'];?>" name="addons[]" value="<?php echo $image[':addon'];?>">
                    <span class="help-inline"><?php echo '('.implode(', ',$image[':supported']).')';?></span>
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="checkAll"><?php echo $gsprache->all;?></label>
                <div class="controls">
                    <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'addons[]')">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="actionType"></label>
                <div class="controls">
                    <select name="actionType" id="actionType">
                        <option value="1"><?php echo $gsprache->add;?></option>
                        <option value="2"><?php echo $gsprache->mod;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group pull-left">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>