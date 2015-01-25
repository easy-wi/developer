<section class="content-header">
    <h1><?php echo $gsprache->imprint.' '.$gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li class="active"><i class="fa fa-legal"></i> <?php echo $gsprache->imprint.' '.$gsprache->settings;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=si&amp;r=si" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <?php foreach ($foundLanguages as $array){ ?>
                            <label class="checkbox-inline">
                                <input name="languages[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($foundLanguages as $array) { ?>
                        <div id="<?php echo $array['lang'];?>" class="form-group  <?php if ($array['style']==0) echo 'display_none';?>">
                            <label for="inputLangs-<?php echo $array['lang'];?>"><img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/></label>
                            <textarea class="form-control" id="inputLangs-<?php echo $array['lang'];?>" name="description[<?php echo $array['lang'];?>]" rows="8"><?php echo $array['imprint'];?></textarea>
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