<section class="content-header">
    <h1><?php echo $gsprache->pages;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=pn"><i class="fa fa-globe"></i> CMS</a></li>
        <li><a href="admin.php?w=pp"><i class="fa fa-copy"></i> <?php echo $gsprache->pages;?></a></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>


<section class="content">

    <form role="form" action="admin.php?w=pp&amp;d=ad&amp;r=pp" onsubmit="submitToForm(); return confirm('<?php echo $gsprache->sure;?>');" method="post" >

        <input type="hidden" name="token" value="<?php echo token();?>">
        <input type="hidden" name="action" value="ad">

        <div class="row">
            <div class="col-md-12">

                <div class="box box-success">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputComments"><?php echo $gsprache->comments;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputComments" name="comments">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputRelease"><?php echo $sprache->release;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputRelease" name="released">
                                    <option value="1"><?php echo $gsprache->yes.' ('.$sprache->public.')';?></option>
                                    <!--<option value="3"><?php echo $gsprache->yes.' ('.$sprache->intern.')';?></option>-->
                                    <option value="2"><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputNaviDisplay"><?php echo $sprache->naviDisplay;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputNaviDisplay" name="naviDisplay">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputSubpage"><?php echo $sprache->subpage;?></label>
                            <div class="controls">
                                <select class="form-control chosen-select" id="inputSubpage" name="subpage">
                                    <option value="0"><?php echo $gsprache->no;?></option>
                                    <?php foreach ($subpage as $key => $value) { ?>
                                    <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo $sprache->languages;?></label>
                            <div class="controls">
                                <?php foreach ($lang_avail as $lg){ ?>
                                <label class="checkbox-inline">
                                    <input name="language[]" value="<?php echo $lg;?>" onclick="textdrop('<?php echo $lg;?>');" type="checkbox" <?php if($lg==$default_language) echo 'checked="checked"';?>> <img src="images/flags/<?php echo $lg;?>.png" alt="Flag: 16_<?php echo $lg;?>.png" >
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach ($lang_avail as $lg) { ?>

        <div class="display_none">
            <textarea id="textValue-<?php echo $lg;?>" name="text[<?php echo $lg;?>]"></textarea>
        </div>

        <div id="<?php echo $lg;?>" class="row <?php if($lg!=$default_language) echo 'display_none';?>">
            <div class="col-md-9">

                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title"><img src="images/flags/<?php echo $lg;?>.png" alt="<?php echo $lg;?>"></h3>
                    </div>

                    <div class="box-body">

                        <div class="form-group">
                            <label for="title-<?php echo $lg;?>"></label>
                            <div class="controls">
                                <input class="form-control" type="text" name="title[<?php echo $lg;?>]" id="title-<?php echo $lg;?>" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="text-<?php echo $lg;?>"></label>
                            <div class="controls">
                                <div id="text-<?php echo $lg;?>"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-3">

                <div class="box box-success">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputPreview-<?php echo $lg;?>">
                                <span id="inputPreview-<?php echo $lg;?>" class="btn btn-primary btn-sm" onclick="post_data('<?php echo $lg;?>');"><i class="fa fa-eye"></i> <?php echo $sprache->preview;?></span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="keywords-<?php echo $lg;?>"><?php echo $sprache->keywords;?></label>
                            <div class="controls">
                                <textarea id="keywords-<?php echo $lg;?>" class="form-control" name="keywords[<?php echo $lg;?>]" lang="<?php echo $lg;?>"></textarea>
                            </div>
                        </div>

                        <div class="form-inline">
                            <?php foreach($keywords[$lg] as $keyword){ ?>
                            <label>
                                <input class="btn" type="button" name="<?php echo $lg.'_'.$keyword;?>" value="<?php echo $keyword;?>" onClick="AddKey(this,'keywords-<?php echo $lg;?>');">
                            </label>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </form>
</section>



<script type="text/javascript">

    $("#inputSubpage").chosen({
        disable_search_threshold: 3,
        inherit_select_classes: true,
        no_results_text: '<?php echo $gsprache->chosenNoResult;?>',
        placeholder_text_single: '<?php echo $gsprache->chosenSelect;?>',
        placeholder_text_multiple: '<?php echo $gsprache->chosenSelect;?>',
        width: "100%"
    });
</script>

<script type="text/javascript">
    $(function () {
        <?php foreach ($lang_avail as $lg) echo "$('#text-$lg').summernote({height: 300});"; ?>
    });
</script>