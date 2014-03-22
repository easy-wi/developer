<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=mo"><?php echo $gsprache->modules;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $name;?></li>
        </ul>
    </div>
</div>
<?php if (count($errors)>0){ ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4><?php echo $gsprache->errors;?></h4>
    <?php echo implode(', ',$errors);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=mo&amp;d=md&amp;id=<?php echo $id;?>&amp;r=mo" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group<?php if(isset($errors['active'])) echo ' error';?>">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($active=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <?php if($id>1000){ ?>
            <div class="control-group<?php if(isset($errors['type'])) echo ' error';?>">
                <label class="control-label" for="inputType"><?php echo $sprache->type;?></label>
                <div class="controls">
                    <select id="inputType" name="type">
                        <option value="A"><?php echo $sprache->type_admin;?></option>
                        <option value="P" <?php if ($type=='P') echo 'selected="selected"'; ?>><?php echo $sprache->type_cms;?></option>
                        <option value="U" <?php if ($type=='U') echo 'selected="selected"'; ?>><?php echo $sprache->type_user;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['sub'])) echo ' error';?>">
                <label class="control-label" for="inputSub"><?php echo $sprache->sub;?></label>
                <div class="controls">
                    <select id="inputSub" name="sub">
                        <option value="mo"><?php echo $gsprache->modules;?></option>
                        <option value="gs" <?php if ($sub=='gs') echo 'selected="selected"'; ?>><?php echo $gsprache->gameserver;?></option>
                        <option value="vo" <?php if ($sub=='vo') echo 'selected="selected"'; ?>><?php echo $gsprache->voiceserver;?></option>
                        <option value="ro" <?php if ($sub=='ro') echo 'selected="selected"'; ?>>Rootserver</option>
                        <option value="my" <?php if ($sub=='my') echo 'selected="selected"'; ?>>MySQL</option>
                        <option value="fd" <?php if ($sub=='fd') echo 'selected="selected"'; ?>><?php echo $gsprache->fastdownload;?></option>
                        <option value="gs" <?php if ($sub=='us') echo 'selected="selected"'; ?>><?php echo $gsprache->user;?></option>
                        <option value="ti" <?php if ($sub=='ti') echo 'selected="selected"'; ?>><?php echo $gsprache->support;?></option>
                        <option value="pa" <?php if ($sub=='pa') echo 'selected="selected"'; ?>>CMS</option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['file'])) echo ' error';?>">
                <label class="control-label" for="inputFile"><?php echo $sprache->file;?></label>
                <div class="controls">
                    <select id="inputFile" name="file">
                        <?php foreach($files as $row){ ?>
                        <option <?php if($file==$row) echo 'selected="selected"';?>><?php echo $row;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['get'])) echo ' error';?>">
                <label class="control-label" for="inputGet"><?php echo $sprache->get;?></label>
                <div class="controls">
                    <input id=inputGet type="text" name="get" value="<?php echo $get;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDescription"><?php echo $sprache->description;?></label>
                <div class="controls">
                    <?php foreach ($langAvailable as $lg) { ?>
                        <label class="checkbox inline"><input type="checkbox"  name="lang[]" value="<?php echo $lg;?>" onclick="textdrop('<?php echo $lg;?>');" <?php if(isset($lg) and isset($languageTexts[$lg])) echo 'checked="checked"';?>><img src="images/flags/<?php echo $lg;?>.png" alt="Flag: <?php echo $lg;?>.png"/></label>
                    <?php } ?>
                </div>
            </div>
            <?php foreach ($langAvailable as $lg) { ?>
            <div id="<?php echo $lg;?>" class="control-group <?php echo $array['display'];?>">
                <label class="control-label" for="inputLangs-<?php echo $lg;?>"><img src="images/flags/<?php echo $lg;?>.png" alt="Flag: 16_<?php echo $lg;?>'.png"/></label>
                <div class="controls"><input type="text" id="inputLangs-<?php echo $lg;?>" name="translation[<?php echo $lg;?>]" value="<?php echo $languageTexts[$lg];?>"></div>
            </div>
            <?php } ?>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>