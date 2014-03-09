
<div class="control-group">
    <label class="control-label lead" for="displayUsage"><b><?php echo $sprache->usage;?></b></label>
    <div class="controls">
        <dl id="displayUsage">
            <dt><?php echo $sprache->maxVhost;?></dt>
            <dd><?php echo $totalVhosts.'/'.$maxVhost;?></dd>
            <dt><?php echo $sprache->maxHDD;?></dt>
            <dd><?php echo $leftHDD.'/'.$maxHDD;?> MB</dd>
        </dl>
    </div>
</div>
<?php if($quotaActive=='Y'){ ?>
<div class="control-group<?php if(isset($errors['hdd'])) echo ' error';?>">
    <label class="control-label" for="inputHDD"><?php echo $sprache->hdd;?></label>
    <div class="controls">
        <div class="input-append span12">
            <input id="inputHDD" class="span11" type="number" name="hdd" value="1000">
            <span class="add-on">MB</span>
        </div>
    </div>
</div>
<?php } ?>
<div class="control-group">
    <label class="control-label" for="inputDNS"><?php echo $sprache->dns;?></label>
    <div class="controls"><input id="inputDNS" class="span11" type="text" name="dns" value="<?php echo $dns;?>" required></div>
</div>
<div class="control-group<?php if(isset($errors['ownVhost'])) echo ' error';?>">
    <label class="control-label" for="inputOwnVhost"><?php echo $sprache->ownVhost;?></label>
    <div class="controls">
        <select id="inputOwnVhost" class="span11" name="ownVhost" onchange="textdrop('OwnVhostTemplate')">
            <option value="N"><?php echo $gsprache->no;?></option>
            <option value="Y"><?php echo $gsprache->yes;?></option>
        </select>
    </div>
</div>
<div class="control-group" id="OwnVhostTemplate" style="display: none;">
    <label class="control-label" for="inputvhostTemplate"><?php echo $sprache->vhostTemplate;?></label>
    <div class="controls">
        <textarea id="inputvhostTemplate" class="span11" name="vhostTemplate" rows="20"><?php echo $vhostTemplate;?></textarea>
    </div>
</div>