<section class="content-header">
    <h1><?php echo $sprache->heading;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ti"><i class="fa fa-life-ring"></i> <?php echo $gsprache->support;?></a></li>
        <li><a href="admin.php?w=ti&amp;d=mt"><i class="fa fa-wrench"></i> <?php echo $sprache->heading;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $topic;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=ti&amp;d=mt&amp;id=<?php echo $id;?>&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputMainTopic"><?php echo $sprache->topic;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputMainTopic" name="maintopic">
                                    <option value="none"><?php echo $sprache->none;?></option>
                                    <?php foreach ($options as $option) { ?><?php echo $option;?><?php }?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPriority"><?php echo $sprache->priority;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputPriority" name="priority">
                                    <option value="1"><?php echo $sprache->priority_low;?></option>
                                    <option value="2" <?php if($priority=="2") echo 'selected="selected"';?>><?php echo $sprache->priority_medium;?></option>
                                    <option value="3" <?php if($priority=="3") echo 'selected="selected"';?>><?php echo $sprache->priority_high;?></option>
                                    <option value="4" <?php if($priority=="4") echo 'selected="selected"';?>><?php echo $sprache->priority_very_high;?></option>
                                    <option value="5" <?php if($priority=="5") echo 'selected="selected"';?>><?php echo $sprache->priority_critical;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputTopicName"><?php echo $sprache->topic_name;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputTopicName" type="text" name="topic_name" value="<?php echo $topic;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php foreach ($foundlanguages as $array){ ?>
                            <label class="checkbox-inline">
                                <input name="language[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>');" type="checkbox" <?php echo $array['checked'];?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($foundlanguages as $array) { ?>
                        <div id="<?php echo $array['lang'];?>" class="form-group <?php echo $array['style'];?>">
                            <label for="inputLangs-<?php echo $array['lang'];?>"></label>
                            <div class="controls">
                                <div class="input-group">
                                    <span class="input-group-addon"><img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/></span>
                                    <input class="form-control" id="inputLangs-<?php echo $array['lang'];?>" type="text" name="subject_<?php echo $array['lang'];?>"  value="<?php echo $array['subject'];?>">
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>