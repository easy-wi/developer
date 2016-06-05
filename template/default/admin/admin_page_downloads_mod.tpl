<section class="content-header">
    <h1><?php echo $gsprache->downloads;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=pn"><i class="fa fa-globe"></i> CMS</a></li>
        <li><a href="admin.php?w=pd"><i class="fa fa-download"></i> <?php echo $gsprache->downloads;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $description;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=pd&amp;d=md&amp;id=<?php echo $id;?>&amp;r=pd" enctype="multipart/form-data" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputCount"><?php echo $gsprache->downloads;?></label>
                            <div class="controls"><input class="form-control" id="inputCount" type="text" name="downloads" value="<?php echo $count?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputFile"><?php echo $gsprache->file;?></label>
                            <div class="controls"><input class="form-control" id="inputFile" type="text" name="file" value="<?php echo $id.'.'.$fileExtension?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDate"><?php echo $sprache->date;?></label>
                            <div class="controls"><input class="form-control" id="inputDate" type="text" name="date" value="<?php echo $date?>" readonly="readonly"></div>
                        </div>
                    </div>

                    <div class="box-body">

                        <div class="form-group">
                            <label class="control-label" for="inputUpload"><?php echo $sprache->upload;?></label>
                            <div class="controls">
                                <input id="inputUpload" type="file" name="upload">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="show">
                                    <option value="E"><?php echo $sprache->public;?></option>
                                    <option value="A" <?php if($show=='A') echo 'selected="selected"';?>><?php echo $sprache->admin;?></option>
                                    <option value="R" <?php if($show=='R') echo 'selected="selected"';?>><?php echo $sprache->register;?></option>
                                    <option value="N" <?php if($show=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputTitle"><?php echo $sprache->name;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputTitle" type="text" name="description" value="<?php echo $description;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputFileName"><?php echo $gsprache->file;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputFileName" type="text" name="fileName" value="<?php echo $fileName;?>">
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
                            <textarea class="form-control" id="inputLangs-<?php echo $array['lang'];?>" name="text[<?php echo $array['lang'];?>]"><?php echo $array['description'];?></textarea>
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