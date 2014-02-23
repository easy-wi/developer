<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=pd"><?php echo $gsprache->downloads;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $description;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <dl class="dl-horizontal">
            <dt><?php echo $gsprache->downloads;?></dt>
            <dd><?php echo $count;?></dd>
            <dt><?php echo $gsprache->file;?></dt>
            <dd><?php echo $id.'.'.$fileExtension;?></dd>
            <dt><?php echo $sprache->date;?></dt>
            <dd><?php echo $date;?></dd>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=pd&amp;d=md&amp;id=<?php echo $id;?>&amp;r=pd" enctype="multipart/form-data" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputUpload"><?php echo $sprache->upload;?></label>
                <div class="controls">
                    <input class="span12" id="inputUpload" type="file" name="upload">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select class="span12" id="inputActive" name="show">
                        <option value="E"><?php echo $sprache->public;?></option>
                        <option value="A" <?php if($show=='A') echo 'selected="selected"';?>><?php echo $sprache->admin;?></option>
                        <option value="R" <?php if($show=='R') echo 'selected="selected"';?>><?php echo $sprache->register;?></option>
                        <option value="N" <?php if($show=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTitle"><?php echo $sprache->name;?></label>
                <div class="controls">
                    <input class="span12" id="inputTitle" type="text" name="description" value="<?php echo $description;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFileName"><?php echo $gsprache->file;?></label>
                <div class="controls">
                    <input class="span12" id="inputFileName" type="text" name="fileName" value="<?php echo $fileName;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDescription"><?php echo $sprache->description;?></label>
                <div class="controls"><?php foreach ($foundLanguages as $array) echo '<label class="checkbox inline">'.$array['checkbox'].'<img src="images/flags/'.$array['lang'].'.png" alt="Flag: '.$array['lang'].'.png"/></label>';?></div>
            </div>
            <?php foreach ($foundLanguages as $array) { ?>
            <div id="<?php echo $array['lang'];?>" class="control-group <?php echo $array['display'];?>">
                <label class="control-label" for="inputLangs-<?php echo $array['lang'];?>"><img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/></label>
                <div class="controls"><textarea class="span12" id="inputLangs-<?php echo $array['lang'];?>" name="text[<?php echo $array['lang'];?>]" rows="5"><?php echo $array['description'];?></textarea></div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>