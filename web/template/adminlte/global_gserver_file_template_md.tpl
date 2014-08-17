<section class="content-header">
    <h1><?php echo $gsprache->template.' '.$gsprache->mod;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="<?php echo $targetFile;?>?w=gt"><?php echo $gsprache->gameserver.' '.$gsprache->file.' '.$gsprache->template;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $name;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="box box-info">
        <div class="box-body">
            <?php if (count($errors)>0){ ?>
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><?php echo $gsprache->errors;?></h4>
                <?php echo implode(', ',$errors);?>
            </div>
            <?php }?>

            <form role="form" action="<?php echo $targetFile;?>?w=gt&amp;d=md&amp;id=<?php echo $id;?>&amp;r=gt" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                <input type="hidden" name="token" value="<?php echo token();?>">
                <input type="hidden" name="action" value="md">

                <div class="form-group<?php if(isset($errors['name'])) echo ' error';?>">
                    <label for="inputName"><?php echo $sprache->description;?></label>
                    <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $name;?>">
                </div>

                <div id="mods" class="form-group<?php if(isset($errors['servertype'])) echo ' error';?>">
                    <label for="inputServertype"><?php echo $sprache->game;?></label>
                    <select class="form-control" id="inputServertype" name="servertype">
                        <?php foreach ($table as $k=>$v) { ?>
                        <option value="<?php echo $k;?>" <?php if($k==$servertype) echo 'selected="selected"'; ?>><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group<?php if(isset($errors['content'])) echo ' error';?>">
                    <label for="inputContent"><?php echo $gsprache->template;?></label>
                    <textarea class="form-control" id="inputContent" rows="20" name="content"><?php echo $content;?></textarea>
                </div>
                <div class="form-group">
                    <label class="control-label" for="inputEdit"></label>
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-edit"> <?php echo $gsprache->mod;?></i></button>
                </div>
            </form>
        </div>
    </div>
</section>