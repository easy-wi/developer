<section class="content-header">
    <h1><?php echo $gsprache->downloads;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=pn"><i class="fa fa-globe"></i> CMS</a></li>
        <li><a href="admin.php?w=pd"><i class="fa fa-download"></i> <?php echo $gsprache->downloads;?></a></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">

                <form role="form" action="admin.php?w=pd&amp;d=ad&amp;r=pd" enctype="multipart/form-data" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputUpload"><?php echo $sprache->upload;?></label>
                            <div class="controls">
                                <input id="inputUpload" type="file" name="upload" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="show">
                                    <option value="E"><?php echo $sprache->public;?></option>
                                    <option value="A"><?php echo $sprache->admin;?></option>
                                    <option value="R"><?php echo $sprache->register;?></option>
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputTitle"><?php echo $sprache->name;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputTitle" type="text" name="description" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputFileName"><?php echo $gsprache->file;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputFileName" type="text" name="fileName">
                            </div>
                        </div>

                        <h3><?php echo $sprache->description;?></h3>

                        <div class="form-group">
                            <?php foreach ($foundLanguages as $array){ ?>
                            <label class="checkbox-inline">
                                <input name="language[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>');" type="checkbox" <?php if($array['display']!='display_none') echo 'checked="checked"';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($foundLanguages as $array) { ?>
                        <div id="<?php echo $array['lang'];?>" class="form-group <?php echo $array['display'];?>">
                            <label for="inputLangs-<?php echo $array['lang'];?>"><img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/></label>
                            <textarea class="form-control" id="inputLangs-<?php echo $array['lang'];?>" name="text[<?php echo $array['lang'];?>]"></textarea>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>