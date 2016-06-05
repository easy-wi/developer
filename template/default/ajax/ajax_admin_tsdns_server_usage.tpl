<div class="form-group">
    <label for="inputServerInstalled"><?php echo $sprache->installedserver;?></label>
    <div class="controls">
        <input class="form-control" id="inputServerInstalled" type="text" name="serverInstalled" value="<?php echo $installedServer.'/'.$maxServer;?>" disabled="disabled">
    </div>
</div>

<div class="form-group">
    <label for="inputDns"><?php echo $sprache->dns;?></label>
    <div class="controls">
        <input class="form-control" id="inputDns" type="text" name="dns" value="<?php echo $dns;?>">
    </div>
</div>