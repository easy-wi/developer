<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li>Easy-WI <?php echo $gsprache->databases;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->gameserver.' '.$gsprache->template;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=bu&amp;d=rg&amp;r=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="action" value="rg">
            <?php foreach ($gameImages as $image) { ?>
            <div class="control-group">
                <label class="control-label" for="inputGame-<?php echo $image[':shorten'];?>"><img src="images/games/icons/<?php echo $image[':shorten'];?>.png" alt="<?php echo $image[':shorten'];?>" width="16"> <?php echo $image[':description'];?></label>
                <div class="controls">
                    <input type="checkbox" id="inputGame-<?php echo $image[':shorten'];?>" name="games[]" value="<?php echo $image[':shorten'];?>">
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="checkAll"><?php echo $gsprache->all;?></label>
                <div class="controls">
                    <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'games[]')">
                </div>
            </div>
            <div id="typeGroup" class="control-group">
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