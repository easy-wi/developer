<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->imprint.' '.$gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=si&amp;r=si" onsubmit="return confirm('<?php echo $sprache->confirm_change; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label"><?php echo $gsprache->imprint;?></label>
                <div class="controls">
                    <?php foreach ($foundlanguages as $array) { ?>
                    <label class="checkbox inline">
                        <input type="checkbox" id="inlineCheckbox<?php echo $array['lang'];?>" name="languages[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>');" <?php if ($array['style']!=0) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png" class="inline"/>
                    </label>
                    <?php }?>
                </div>
            </div>
            <?php foreach ($foundlanguages as $array) { ?>
            <div id="<?php echo $array['lang'];?>" class="control-group <?php if ($array['style']==0) echo 'display_none';?>">
                <label class="control-label" for="inputImprint<?php echo $array['lang'];?>"><img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
                <div class="controls">
                    <textarea class="span10" id="inputImprint<?php echo $array['lang'];?>" name="description[<?php echo $array['lang'];?>]" rows="8"><?php echo $array['imprint'];?></textarea>
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="md">
                </div>
            </div>
        </form>
    </div>
</div>