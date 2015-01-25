<section class="content-header">
    <h1><?php echo $gsprache->columns;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li><a href="admin.php?w=cc"><i class="fa fa-list"></i> <?php echo $gsprache->columns;?></a></li>
        <li class="active"><?php echo $gsprache->mod;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=cc&amp;d=md&amp;id=<?php echo $id;?>&amp;r=cc" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputActive"><?php echo $gsprache->active;?></label>
                            <div class="controls">
                                <select class="form-control"id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($active=='N')echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputName"><?php echo $sprache->name;?></label>
                            <div class="controls">
                                <input class="form-control"id="inputName" type="text" name="name" value="<?php echo $name;?>" pattern="^[a-zA-Z0-9-_]{1,255}$" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputLength"><?php echo $sprache->length;?></label>
                            <div class="controls">
                                <input class="form-control"id="inputLength" type="number" name="length" value="<?php echo $length;?>"  min="1" max="255" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputType"><?php echo $sprache->type;?></label>
                            <div class="controls">
                                <select class="form-control"id="inputType" name="type">
                                    <option value="I"><?php echo $sprache->int;?></option>
                                    <option value="V" <?php if($type=='V')echo 'selected="selected"';?>><?php echo $sprache->var;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputItem"><?php echo $sprache->item;?></label>
                            <div class="controls">
                                <select class="form-control"id="inputItem" name="item">
                                    <option value="D"><?php echo $gsprache->databases;?></option>
                                    <option value="G" <?php if($item=='G')echo 'selected="selected"';?>><?php echo $gsprache->gameserver;?></option>
                                    <option value="S" <?php if($item=='S')echo 'selected="selected"';?>><?php echo $gsprache->dedicated;?></option>
                                    <option value="T" <?php if($item=='T')echo 'selected="selected"';?>><?php echo $gsprache->voiceserver;?></option>
                                    <option value="U" <?php if($item=='U')echo 'selected="selected"';?>><?php echo $gsprache->user;?></option>
                                    <option value="V" <?php if($item=='V')echo 'selected="selected"';?>><?php echo $gsprache->virtual;?></option>
                                </select>
                            </div>
                        </div>

                        <h3><?php echo $sprache->menuname;?></h3>

                        <div class="form-group">
                            <?php foreach ($foundLanguages as $array){ ?>
                            <label class="checkbox-inline">
                                <?php echo $array['checkbox'];?> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($foundLanguages as $array) { ?>
                        <div id="<?php echo $array['lang'];?>" class="form-group <?php echo $array['class'];?>">
                            <label for="inputLangs-<?php echo $array['lang'];?>"><img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/></label>
                            <input class="form-control" id="inputLangs-<?php echo $array['lang'];?>" type="text" name="menu[<?php echo $array['lang'];?>]" value="<?php echo $array['text'];?>">
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