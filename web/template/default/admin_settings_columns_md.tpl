<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=cc"><?php echo $gsprache->columns;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->mod?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=cc&amp;d=md&amp;id=<?php echo $id;?>&amp;r=cc" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $gsprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($active=='N')echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputName"><?php echo $sprache->name;?></label>
                <div class="controls">
                    <input id="inputName" type="text" name="name" value="<?php echo $name;?>" pattern="^[a-zA-Z0-9-_]{1,255}$" required />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputLength"><?php echo $sprache->length;?></label>
                <div class="controls">
                    <input id="inputLength" type="number" name="length" value="<?php echo $length;?>"  min="1" max="255" required />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputType"><?php echo $sprache->type;?></label>
                <div class="controls">
                    <select id="inputType" name="type">
                        <option value="I"><?php echo $sprache->int;?></option>
                        <option value="V" <?php if($type=='V')echo 'selected="selected"';?>><?php echo $sprache->var;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputItem"><?php echo $sprache->item;?></label>
                <div class="controls">
                    <select id="inputItem" name="item">
                        <option value="D"><?php echo $gsprache->databases;?></option>
                        <option value="G" <?php if($item=='G')echo 'selected="selected"';?>><?php echo $gsprache->gameserver;?></option>
                        <option value="S" <?php if($item=='S')echo 'selected="selected"';?>><?php echo $gsprache->dedicated;?></option>
                        <option value="T" <?php if($item=='T')echo 'selected="selected"';?>><?php echo $gsprache->voiceserver;?></option>
                        <option value="U" <?php if($item=='U')echo 'selected="selected"';?>><?php echo $gsprache->user;?></option>
                        <option value="V" <?php if($item=='V')echo 'selected="selected"';?>><?php echo $gsprache->virtual;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMenuName"><?php echo $sprache->menuname;?></label>
                <div class="controls">
                    <?php foreach ($foundlanguages as $array){ ?>
                    <label class="checkbox inline">
                        <?php echo $array['checkbox'];?> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/>
                    </label>
                    <?php } ?>
                </div>
            </div>
            <?php foreach ($foundlanguages as $array) { ?>
            <div id="<?php echo $array['lang'];?>" class="control-group<?php echo $array['class'];?>">
                <label class="control-label" for="inputMenuName<?php echo $array['lang'];?>"><img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/></label>
                <div class="controls">
                    <input id="inputMenuName<?php echo $array['lang'];?>" type="text" name="menu[<?php echo $array['lang'];?>]" value="<?php echo $array['text'];?>" pattern="^[a-zA-Z0-9-_ ]{1,255}$">
                </div>
            </div>
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
