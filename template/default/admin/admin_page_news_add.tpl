<section class="content-header">
    <h1><?php echo $gsprache->news;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=pn"><i class="fa fa-globe"></i> CMS</a></li>
        <li><a href="admin.php?w=pn"><i class="fa fa-newspaper-o"></i> <?php echo $gsprache->news;?></a></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>


<section class="content">

    <form role="form" action="admin.php?w=pn&amp;d=ad&amp;r=pn" onsubmit="submitToForm(); return confirm('<?php echo $gsprache->sure;?>');" method="post" >

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
                                    <option value="1"><?php echo $gsprache->yes;?></option>
                                    <!--<option value="3"><?php echo $gsprache->yes.' ('.$sprache->intern.')';?></option>-->
                                    <option value="2"><?php echo $gsprache->no;?></option>
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
                                <div class="summernote" id="text-<?php echo $lg;?>"></div>
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

                        <hr>

                        <h4><?php echo $sprache->categories;?></h4>

                        <div class="form-group" style="height:200px;overflow:auto;">
                            <table id="categories_list_<?php echo $lg;?>" class="table table-bordered table-hover table-striped">
                                <?php foreach($categories[$lg] as $category){ ?>
                                <tr>
                                    <td><input type="checkbox" name="categories[<?php echo $lg;?>][]" value="<?php echo $category;?>"> <?php echo $category;?></td>
                                </tr>
                                <?php }?>
                            </table>
                        </div>

                        <div class="input-group input-group-sm">
                            <input type="text" id="newcategory[<?php echo $lg;?>]" name="newcategory[<?php echo $lg;?>]" class="form-control">
                            <span class="input-group-btn">
                                <span class="btn btn-success btn-flat" onClick="addCategory('newcategory[<?php echo $lg;?>]','categories_list_<?php echo $lg;?>','<?php echo $lg;?>');"><i class="fa fa-plus-circle"></i></span>
                            </span>
                        </div>

                        <hr>

                        <h4><?php echo $sprache->keywords;?></h4>

                        <div class="form-group">
                            <label for="keywords-<?php echo $lg;?>"></label>
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
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 300
        });
    });
</script>