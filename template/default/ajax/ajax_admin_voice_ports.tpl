<div class="form-group">
    <label><?php echo $sprache->usedports;?></label>
    <div class="controls" id="portList">
        <?php echo $portList;?>
    </div>
</div>

<div class="form-group">
    <label for="inputPortMain"><?php echo $sprache->port;?></label>
    <div class="controls"><input class="form-control" id="inputPortMain" type="number" name="port" value="<?php echo $port;?>" min="1"></div>
</div>