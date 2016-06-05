<?php foreach ($table as $row){ ?>
<h4>
    <img src="images/games/icons/<?php echo $row['shorten'];?>.png" alt="<?php echo $row['description'];?>" width="16"> <a href="javascript:void(0)" onclick="toggleGameDetail('<?php echo $row['id'];?>');"><?php echo $row['description'];?> <i id="openCloseIcon-<?php echo $row['id'];?>" class="fa fa-arrow-circle-o-up"></i></a>
</h4>

<?php if ($row['uploadAllowed']>0) { ?>
<div class="form-group gameDetail-<?php echo $row['id'];?>">
    <label for="input-<?php echo $row['id'];?>-STV">SourceTV Demoupload</label>
    <div class="controls">
        <select class="form-control" id="input-<?php echo $row['id'];?>-STV" name="upload[<?php echo $row['id'];?>]">
            <option value="1">OFF</option>
            <option value="2" <?php if($row['upload']==2) echo 'selected="selected"';?>>Cron+Manual File remove</option>
            <option value="3" <?php if($row['upload']==3) echo 'selected="selected"';?>>Cron+Manual</option>
            <option value="4" <?php if($row['upload']==4) echo 'selected="selected"';?>>Autoupload File remove</option>
            <option value="5" <?php if($row['upload']==5) echo 'selected="selected"';?>>Autoupload</option>
        </select>
    </div>
</div>

<div class="form-group gameDetail-<?php echo $row['id'];?>">
    <label for="input-<?php echo $row['id'];?>-UploadFTP">Upload FTP</label>
    <div class="controls"><input class="form-control" id="input-<?php echo $row['id'];?>-UploadFTP" type="text" name="uploadDir[<?php echo $row['id'];?>]" value="<?php echo $row['uploadDir'];?>"></div>
</div>

<div class="checkbox gameDetail-<?php echo $row['id'];?>">
    <label>
        <input type="checkbox" name="userUploadDir[<?php echo $row['id'];?>]" value="Y" <?php if($row['userUploadDir']=='Y') echo 'checked="checked"';?>>
        Upload FTP <?php echo $sprache->useredit;?>
    </label>
</div>
<?php }else{ ?>
<input type="hidden" name="upload[<?php echo $row['id'];?>]" value="0">
<?php }?>

<?php if(strlen($row['fps'])>0){ ?>
<div class="form-group gameDetail-<?php echo $row['id'];?>">
    <label for="input-<?php echo $row['id'];?>-FPS"><?php echo $sprache->fps;?></label>
    <div class="controls"><input class="form-control" id="input-<?php echo $row['id'];?>-FPS" type="text" name="fps[<?php echo $row['id'];?>]" value="<?php echo $row['fps'];?>"></div>
</div>

<div class="checkbox gameDetail-<?php echo $row['id'];?>">
    <label>
        <input type="checkbox" name="userFps[<?php echo $row['id'];?>]" value="Y" <?php if($row['userFps']=='Y') echo 'checked="checked"';?>>
        <?php echo $sprache->fps.' '.$sprache->useredit;?>
    </label>
</div>
<?php } ?>

<?php if(strlen($row['tic'])>0){ ?>
<div class="form-group gameDetail-<?php echo $row['id'];?>">
    <label for="input-<?php echo $row['id'];?>-Tick"><?php echo $sprache->tick;?></label>
    <div class="controls"><input class="form-control" id="input-<?php echo $row['id'];?>-Tick" type="text" name="tic[<?php echo $row['id'];?>]" value="<?php echo $row['tic'];?>"></div>
</div>

<div class="checkbox gameDetail-<?php echo $row['id'];?>">
    <label>
        <input type="checkbox" name="userTick[<?php echo $row['id'];?>]" value="Y" <?php if($row['userTick']=='Y') echo 'checked="checked"';?>>
        <?php echo $sprache->tick.' '.$sprache->useredit;?>
    </label>
</div>
<?php } ?>

<?php if(strlen($row['map'])>0){ ?>
<div class="form-group gameDetail-<?php echo $row['id'];?>">
    <label for="input-<?php echo $row['id'];?>-Map"><?php echo $sprache->startmap;?></label>
    <div class="controls"><input class="form-control" id="input-<?php echo $row['id'];?>-Map" type="text" name="map[<?php echo $row['id'];?>]" value="<?php echo $row['map'];?>"></div>
</div>
<?php } ?>

<?php if(strlen($row['mapGroup'])>0){ ?>
<div class="form-group gameDetail-<?php echo $row['id'];?>">
    <label for="input-<?php echo $row['id'];?>-MapGroup"><?php echo $sprache->startmapgroup;?></label>
    <div class="controls"><input class="form-control" id="input-<?php echo $row['id'];?>-MapGroup" type="text" name="mapGroup[<?php echo $row['id'];?>]" value="<?php echo $row['mapGroup'];?>"></div>
</div>
<?php } ?>

<?php if(strlen($row['map'])>0 or strlen($row['mapGroup'])>0){ ?>
<div class="checkbox gameDetail-<?php echo $row['id'];?>">
    <label>
        <input type="checkbox" name="userMap[<?php echo $row['id'];?>]" value="Y"  <?php if($row['userMap']=='Y') echo 'checked="checked"';?>>
        <?php echo $sprache->startmap.'/'.$sprache->startmapgroup.' '.$sprache->useredit;?>
    </label>
</div>
<?php } ?>

<div class="form-group gameDetail-<?php echo $row['id'];?>">
    <label for="input-<?php echo $row['id'];?>-OwnCMD"><?php echo $sprache->start_own;?></label>
    <div class="controls">
        <select class="form-control" id="input-<?php echo $row['id'];?>-OwnCMD" name="ownCmd[<?php echo $row['id'];?>]">
            <option value="N"><?php echo $gsprache->no;?></option>
            <option value="Y" <?php if($row['ownCmd']=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
        </select>
    </div>
</div>

<div class="form-group gameDetail-<?php echo $row['id'];?>">
    <label for="input-<?php echo $row['id'];?>-CMD"><?php echo $sprache->start;?></label>
    <div class="controls">
        <textarea class="form-control" id="input-<?php echo $row['id'];?>-CMD" rows="5" name="cmd[<?php echo $row['id'];?>]"><?php echo $row['cmd'];?></textarea>
    </div>
</div>
<?php } ?>