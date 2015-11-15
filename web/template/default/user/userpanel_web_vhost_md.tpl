<section class="content-header">
    <h1><?php echo $gsprache->webspace;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=wv"><i class="fa fa-cubes"></i> <?php echo $gsprache->webspace;?></a></li>
        <li><i class="fa fa-cog"></i> <?php echo $gsprache->settings;?></li>
        <li class="active"><?php echo $dns;?></li>
    </ol>
</section>


<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <form role="form" action="userpanel.php?w=wv&amp;d=md&amp;id=<?php echo $id;?>&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputDescription"><?php echo $dedicatedLanguage->description;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputDescription" type="text" name="description" value="<?php echo $description;?>">
                            </div>
                        </div>

                        <?php foreach($phpConfigurationMaster as $groupName => $array) { ?>
                        <div class="form-group">
                            <label for="input<?php echo str_replace(' ', '', $groupName);?>"><?php echo $groupName;?></label>
                            <div class="controls">
                                <select class="form-control" id="input<?php echo str_replace(' ', '', $groupName);?>" name="<?php echo str_replace(' ', '', $groupName);?>">
                                    <?php foreach($array as $key => $value) { ?>
                                    <?php echo ($phpConfigurationVhost->$groupName == $key) ? '<option value="' . $key . '" selected="selected">' . $value . '</option>' : '<option value="' . $key . '">' . $value . '</option>'; ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php } ?>

                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save"></i> <?php echo $gsprache->save;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>