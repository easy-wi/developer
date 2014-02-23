<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $sprache->topic_name;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->mod;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=ti&amp;d=mt&amp;id=<?php echo $id;?>&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputMainTopic"><?php echo $sprache->topic;?></label>
                <div class="controls">
                    <select id="inputMainTopic" name="maintopic">
                        <option value="none"><?php echo $sprache->none;?></option>
                        <?php foreach ($options as $option) { ?><?php echo $option;?><?php }?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPriority"><?php echo $sprache->priority;?></label>
                <div class="controls">
                    <select id="inputPriority" name="priority">
                        <option value="1"><?php echo $sprache->priority_low;?></option>
                        <option value="2" <?php if($priority=="2") echo 'selected="selected"';?>><?php echo $sprache->priority_medium;?></option>
                        <option value="3" <?php if($priority=="3") echo 'selected="selected"';?>><?php echo $sprache->priority_high;?></option>
                        <option value="4" <?php if($priority=="4") echo 'selected="selected"';?>><?php echo $sprache->priority_very_high;?></option>
                        <option value="5" <?php if($priority=="5") echo 'selected="selected"';?>><?php echo $sprache->priority_critical;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTopicName"><?php echo $sprache->topic_name;?></label>
                <div class="controls">
                    <input id="inputTopicName" type="text" name="topic_name" value="<?php echo $topic;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputLanguages"><?php echo $sprache->languages;?></label>
                <div class="controls">
                    <?php foreach ($foundlanguages as $array) { ?>
                    <label class="checkbox inline">
                        <input id="checkboxLanguage-<?php echo $array['lang'];?>" type="checkbox" name="language[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>');" <?php echo $array['checked'];?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="<?php echo $array['lang'];?>"/>
                    </label>
                    <?php } ?>
                </div>
            </div>
            <?php foreach ($foundlanguages as $array) { ?>
            <div id="<?php echo $array['lang'];?>" class="control-group <?php echo $array['style']; ?>">
                <label class="control-label" for="inputName-<?php echo $array['lang'];?>"><?php echo '<img src="images/flags/'.$array['lang'].'.png" alt="Flag: '.$array['lang'].'.png"/>'; ?></label>
                <div class="controls">
                    <input id="inputName-<?php echo $array['lang'];?>" type="text" name="subject_<?php echo $array['lang'];?>" value="<?php echo $array['subject'];?>">
                </div>
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