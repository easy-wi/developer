<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=pn"><?php echo $gsprache->news;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=pn&amp;d=ad&amp;r=pn" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label" for="inputComments"><?php echo $gsprache->comments;?></label>
                        <div class="controls">
                            <select id="inputComments" name="comments">
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                                <option value="N"><?php echo $gsprache->no;?></option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="inputRelease"><?php echo $sprache->release;?></label>
                        <div class="controls">
                            <select id="inputRelease" name="released">
                                <option value="1"><?php echo $gsprache->yes.' ('.$sprache->public.')';?></option>
                                <!--<option value="3"><?php echo $gsprache->yes.' ('.$sprache->intern.')';?></option>-->
                                <option value="2"><?php echo $gsprache->no;?></option>
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
                                <input id="checkboxLanguage-<?php echo $lg;?>" type="checkbox" name="language[]" value="<?php echo $lg;?>" onclick="textdrop('<?php echo $lg;?>');" checked> <img src="images/flags/<?php echo $lg;?>.png" alt="<?php echo $lg;?>"/>
                            </label>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="inputAdd"><?php echo $gsprache->add;?></label>
                        <div class="controls">
                            <button class="btn btn-primary" id="inputAdd" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php foreach ($lang_avail as $lg) { ?>
            <hr>
            <div id="<?php echo $lg;?>" class="row-fluid">
                <div class="span8">
                    <div class="control-group">
                        <label class="control-label" for="title[<?php echo $lg;?>]"><img src="images/flags/<?php echo $lg;?>.png" alt="<?php echo $lg;?>"/></label>
                        <div class="controls">
                            <input class="span11" type="text" name="title[<?php echo $lg;?>]" id="title[<?php echo $lg;?>]" value="">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="text[<?php echo $lg;?>]"></label>
                        <div class="controls">
                            <textarea class="span11" rows="10" name="text[<?php echo $lg;?>]" id="text[<?php echo $lg;?>]" lang="<?php echo $lg;?>"></textarea>
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
                        <h4><?php echo $sprache->categories;?></h4>
                        <table id="categories_list_<?php echo $lg;?>" class="table table-bordered table-hover table-striped">
                            <?php foreach($categories[$lg]  as $category){ ?>
                            <tr>
                                <td><input type="checkbox" name="categories[<?php echo $lg;?>][]" value="<?php echo $category;?>"> <?php echo $category;?></td>
                            </tr>
                            <?php }?>
                        </table>
                        <div class="row-fluid">
                            <label class="control-label input-append">
                                <input type="text" id="newcategory[<?php echo $lg;?>]" name="newcategory[<?php echo $lg;?>]"><input type="button" class="btn btn-primary" value="+" onClick="AddCategory('newcategory[<?php echo $lg;?>]','categories_list_<?php echo $lg;?>','<?php echo $lg;?>');">
                            </label>
                        </div>
                    </div>
                    <hr>
                    <div class="row-fluid">
                        <h4><?php echo $sprache->keywords;?></h4>
                        <label>
                            <textarea id="keywords[<?php echo $lg;?>]" name="keywords[<?php echo $lg;?>]" lang="<?php echo $lg;?>"></textarea>
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