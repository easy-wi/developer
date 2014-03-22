<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $targetFile;?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $targetFile;?>?w=gt"><?php echo $gsprache->gameserver.' '.$gsprache->file.' '.$gsprache->template;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $name;?></li>
        </ul>
    </div>
</div>
<?php if (count($errors)>0){ ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4><?php echo $gsprache->errors;?></h4>
    <?php echo implode(', ',$errors);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="<?php echo $targetFile;?>?w=gt&amp;d=md&amp;id=<?php echo $id;?>&amp;r=gt" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group<?php if(isset($errors['name'])) echo ' error';?>">
                <label class="control-label" for="inputName"><?php echo $sprache->description;?></label>
                <div class="controls"><input id="inputName" type="text" name="name" value="<?php echo $name;?>"></div>
            </div>
            <div id="mods" class="control-group<?php if(isset($errors['servertype'])) echo ' error';?>">
                <label class="control-label" for="inputServertype"><?php echo $sprache->game;?></label>
                <div class="controls">
                    <select id="inputServertype" name="servertype">
                        <?php foreach ($table as $k=>$v) { ?>
                        <option value="<?php echo $k;?>" <?php if($k==$servertype) echo 'selected="selected"'; ?>><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['content'])) echo ' error';?>">
                <label class="control-label" for="inputContent"><?php echo $gsprache->template;?></label>
                <div class="controls"><textarea id="inputContent" class="span12" rows="20" name="content"><?php echo $content;?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>