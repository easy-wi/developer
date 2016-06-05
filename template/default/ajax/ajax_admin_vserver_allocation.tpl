<dl class="dl-horizontal">
        <dt><?php echo $sprache->cpu;?>:</dt>
        <dd><?php echo $besthostcpu;?></dd>
        <dt><?php echo $sprache->cpu." ".$sprache->cores;?>:</dt>
        <dd><?php foreach ($core as $core_row) { ?><?php echo $sprache->core." ".$core_row.": ".$cpucore[$core_row]."/".$mhz.$sprache->mhz;?><br /><?php } ?></dd>
        <dt><?php echo $gsprache->virtual;?>:</dt>
        <dd><?php echo $i2."/".$maxserver;?></dd>
        <dt><?php echo $sprache->ram;?>:</dt>
        <dd><?php echo $ramused."/".$ram;?></dd>
        <dt><?php echo $sprache->hdd;?>:</dt>
        <dd><?php foreach ($best_hdd as $hdd_row) { ?><?php echo $hdd_row." ".$mountunused[$hdd_row]."/".$mountsize[$hdd_row]." GB";?><br /><?php } ?></dd>
</dl>
<div class="control-group">
    <label class="control-label" for="inputMount"><?php echo $sprache->mount;?></label>
    <div class="controls">
        <select id="inputMount" class="span12" name="mount">
            <?php foreach ($hdd as $hdd_row) { ?><option><?php echo $hdd_row;?></option><?php } ?>
        </select>
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="inputCores"><?php echo $sprache->cores;?></label>
    <div class="controls">
        <select id="inputCores" class="span12" name="cores">
            <?php foreach ($add_core as $core_row){ ?><option><?php echo $core_row;?></option><?php } ?>
        </select>
    </div>
</div>