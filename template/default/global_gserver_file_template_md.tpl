<section class="content-header">
    <h1><?php echo $gsprache->file.' '.$gsprache->template;?></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $targetFile;?>"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="<?php echo $targetFile;?>?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li><a href="<?php echo $targetFile;?>?w=gt"><i class="fa fa-floppy-o"></i> <?php echo $gsprache->file.' '.$gsprache->template;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $name;?></li>
    </ol>
</section>


<section class="content">

    <?php if (count($errors)>0){ ?>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header">
                    <i class="fa fa-warning"></i>
                    <h3 class="box-title"><?php echo $gsprache->errors;?></h3>
                </div>
                <div class="box-body">
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <b><?php echo $gsprache->errors;?>:</b> <?php echo implode(', ',$errors);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <form role="form" action="<?php echo $targetFile;?>?w=gt&amp;d=md&amp;id=<?php echo $id;?>&amp;r=gt" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                        <input type="hidden" name="token" value="<?php echo token();?>">
                        <input type="hidden" name="action" value="md">

                        <div class="form-group<?php if(isset($errors['name'])) echo ' has-error';?>">
                            <label for="inputName"><?php echo $sprache->description;?></label>
                            <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $name;?>" required>
                        </div>

                        <div id="mods" class="form-group<?php if(isset($errors['servertype'])) echo ' has-error';?>">
                            <label for="inputServertype"><?php echo $sprache->game;?></label>
                            <select class="form-control chosen-select" id="inputServertype" name="servertype">
                                <?php foreach ($table as $k=>$v) { ?>
                                <option value="<?php echo $k;?>" <?php if($k==$servertype) echo 'selected="selected"'; ?>><?php echo $v;?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group<?php if(isset($errors['content'])) echo ' has-error';?>">
                            <label for="inputContent"><?php echo $gsprache->template;?></label>
                            <textarea class="form-control" id="inputContent" rows="20" name="content" required><?php echo $content;?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="inputEdit"></label>
                            <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-edit"> <?php echo $gsprache->mod;?></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $("#inputServertype").chosen({
        disable_search_threshold: 3,
        inherit_select_classes: true,
        no_results_text: '<?php echo $gsprache->chosenNoResult;?>',
        placeholder_text_single: '<?php echo $gsprache->chosenSelect;?>',
        placeholder_text_multiple: '<?php echo $gsprache->chosenSelect;?>',
        width: "100%"
    });
</script>