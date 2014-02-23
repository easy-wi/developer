<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=pp"><?php echo $gsprache->pages;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $pageTitle;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=pp&amp;d=md&amp;id=<?php echo $id;?>&amp;r=pp" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label" for="inputComments"><?php echo $gsprache->comments;?></label>
                        <div class="controls">
                            <select id="inputComments" name="comments">
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                                <option value="N" <?php if ($comments=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="inputRelease"><?php echo $sprache->release;?></label>
                        <div class="controls">
                            <select id="inputRelease" name="released">
                                <option value="1"><?php echo $gsprache->yes.' ('.$sprache->public.')';?></option>
                                <!--<option value="3" <?php if ($released=='3') echo 'selected="selected"';?>><?php echo $gsprache->yes.' ('.$sprache->intern.')';?></option>-->
                                <option value="2" <?php if ($released=='2') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="inputNaviDisplay"><?php echo $sprache->naviDisplay;?></label>
                        <div class="controls">
                            <select id="inputNaviDisplay" name="naviDisplay">
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                                <option value="N" <?php if($naviDisplay=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="inputSubpage"><?php echo $sprache->subpage;?></label>
                        <div class="controls">
                            <select id="inputSubpage" name="subpage">
                                <option value="0"><?php echo $gsprache->no;?></option>
                                <?php foreach ($subpages as $key => $value) { ?>
                                <option value="<?php echo $key;?>" <?php if ($key==$subpage) echo 'selected="selected"';?>><?php echo $value;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label" for="inputLanguages"><?php echo $sprache->languages;?></label>
                        <div class="controls">
                            <?php foreach ($lang_avail as $lg) { ?>
                            <label class="checkbox inline">
                                <input id="checkboxLanguage-<?php echo $lg;?>" type="checkbox" name="language[]" value="<?php echo $lg;?>" onclick="textdrop('<?php echo $lg;?>');" <?php if($table[$lg]['text']!=false) echo 'checked';?>> <img src="images/flags/<?php echo $lg;?>.png" alt="<?php echo $lg;?>"/>
                            </label>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="inputEdit"><?php echo $gsprache->mod;?></label>
                        <div class="controls">
                            <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php foreach ($lang_avail as $lg) { ?>
            <hr>
            <div id="<?php echo $lg;?>" class="row-fluid <?php if($table[$lg]['text']==false) echo 'display_none';?>">
                <div class="span8">
                    <div class="control-group">
                        <label class="control-label" for="title[<?php echo $lg;?>]"><img src="images/flags/<?php echo $lg;?>.png" alt="<?php echo $lg;?>"/></label>
                        <div class="controls">
                            <input class="span11" type="text" name="title[<?php echo $lg;?>]" id="title[<?php echo $lg;?>]" value="<?php echo $table[$lg]['title'];?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="text[<?php echo $lg;?>]"></label>
                        <div class="controls">
                            <textarea class="span11" rows="10" name="text[<?php echo $lg;?>]" id="text[<?php echo $lg;?>]" lang="<?php echo $lg;?>"><?php echo $table[$lg]['text'];?></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="inputPreview-<?php echo $lg;?>"></label>
                        <div class="controls">
                            <input id="inputPreview-<?php echo $lg;?>" type="button" value="<?php echo $sprache->preview;?>" onclick="post_data('index.php?site=news&preview=true',['text[<?php echo $lg;?>]','title[<?php echo $lg;?>]']);"  class="btn btn-primary btn-small pull-right">
                        </div>
                    </div>
                </div>
                <div class="span4">
                    <div class="row-fluid">
                        <h4><?php echo $sprache->keywords;?></h4>
                        <label>
                            <textarea id="keywords[<?php echo $lg;?>]" name="keywords[<?php echo $lg;?>]" lang="<?php echo $lg;?>"><?php echo $table[$lg]['keywords'];?></textarea>
                        </label>
                        <div class="form-inline">
                            <?php foreach($keywords[$lg] as $keyword){ ?>
                            <label>
                                <input class="btn" type="button" name="<?php echo $lg.'_'.$keyword;?>" value="<?php echo $keyword;?>" onClick="AddKey(this.value,'keywords[<?php echo $lg;?>]');">
                            </label>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </form>
    </div>
</div>