<div class="form-group">
    <label for="inputMaxVhost"><?php echo $sprache->maxVhost;?></label>
    <div class="controls">
        <input class="form-control" id="inputMaxVhost" type="text" name="maxVhost" value="<?php echo $totalVhosts.'/'.$maxVhost;?>" readonly="readonly">
    </div>
</div>

<div class="form-group">
    <label for="inputMaxHDD"><?php echo $sprache->maxHDD;?></label>
    <div class="controls">
        <input class="form-control" id="inputMaxHDD" type="text" name="maxHDD" value="<?php echo $leftHDD.'/'.$maxHDD;?>" readonly="readonly">
    </div>
</div>

<?php if($quotaActive=='Y'){ ?>
<div class="form-group<?php if(isset($errors['hdd'])) echo ' has-error';?>">
    <label for="inputHDD"><?php echo $sprache->hdd;?></label>
    <div class="controls">
        <div class="input-group">
            <input class="form-control" id="inputHDD" type="number" name="maxHDD" value="<?php echo $maxHDD;?>">
            <span class="input-group-addon">MB</span>
        </div>
    </div>
</div>
<?php } ?>

<div class="form-group">
    <label for="inputDNS"><?php echo $sprache->dns;?></label>
    <div class="controls">
        <input class="form-control" id="inputDNS" type="text" name="dns" value="<?php echo $dns;?>" required>
    </div>
</div>

<div class="form-group<?php if(isset($errors['ownVhost'])) echo ' has-error';?>">
    <label for="inputOwnVhost"><?php echo $sprache->ownVhost;?></label>
    <div class="controls">
        <select class="form-control" id="inputOwnVhost" name="ownVhost" onchange="SwitchShowHideRows(this.value,'switch',1);">
            <option value="N"><?php echo $gsprache->no;?></option>
            <option value="Y" <?php if ($ownVhost=='Y') echo 'selected="selected";'?>><?php echo $gsprache->yes;?></option>
        </select>
    </div>
</div>

<div class="Y switch form-group <?php if($ownVhost=='N') echo 'display_none';?>">
    <label for="inputvhostTemplate"><?php echo $sprache->vhostTemplate;?></label>
    <div class="controls">
        <textarea class="form-control" id="inputvhostTemplate" name="vhostTemplate" rows="20"><?php echo $vhostTemplate;?></textarea>
    </div>
</div>
<script type="text/javascript">
    SwitchShowHideRows('init_ready');
</script>