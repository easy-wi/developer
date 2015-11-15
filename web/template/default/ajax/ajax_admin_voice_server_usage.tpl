<div class="form-group">
    <label for="inputServerInstalled"><?php echo $sprache->installedserver;?></label>
    <input class="form-control" id="inputServerInstalled" type="text" name="serverInstalled" value="<?php echo $installedServer.'/'.$maxServer;?>" disabled="disabled">
</div>

<div class="form-group">
    <label for="inputSlotsInstalled"><?php echo $sprache->installedslots;?></label>
    <input class="form-control" id="inputSlotsInstalled" type="text" name="slotsInstalled" value="<?php echo $installedSlots.'/'.$maxSlots;?>" disabled="disabled">
</div>

<div class="form-group">
    <label for="inputIP"><?php echo $sprache->ip;?></label>
    <div class="controls">
        <select class="form-control" id="inputIP" name="ip" onchange="usedPorts(this.value);">
            <?php foreach ($ips as $v){ ?>
            <option <?php if($v==$currentIP) echo 'selected="selected"';?>><?php echo $v;?></option>
            <?php } ?>
        </select>
    </div>
</div>

<div id="portWrapper"></div>


<div class="form-group">
    <label for="inputDNS"><?php echo $sprache->dns;?></label>
    <div class="controls"><input class="form-control" id="inputDNS" type="text" name="dns" value="<?php echo $dns; ?>"></div>
</div>

<div class="form-group">
    <label for="inputName"><?php echo $sprache->name;?></label>
    <div class="controls"><input class="form-control" id="inputName" type="text" name="name" value="<?php echo $name; ?>"></div>
</div>

<div class="form-group">
    <label for="inputWelcome"><?php echo $sprache->welcome;?></label>
    <div class="controls"><input class="form-control" id="inputWelcome" type="text" name="welcome" value="<?php echo $welcome; ?>"></div>
</div>

<div class="form-group">
    <label for="inputHostbannerUrl"><?php echo $sprache->hostbanner_url;?></label>
    <div class="controls"><input class="form-control" id="inputHostbannerUrl" type="text" name="hostbanner_url" value="<?php echo $hostbanner_url; ?>"></div>
</div>

<div class="form-group">
    <label for="inputHostbannerGFXUrl"><?php echo $sprache->hostbanner_gfx_url;?></label>
    <div class="controls"><input class="form-control" id="inputHostbannerGFXUrl" type="text" name="hostbanner_gfx_url" value="<?php echo $hostbanner_gfx_url; ?>"></div>
</div>

<div class="form-group">
    <label for="inputHostbuttonTooltip"><?php echo $sprache->hostbutton_tooltip;?></label>
    <div class="controls"><input class="form-control" id="inputHostbuttonTooltip" type="text" name="hostbutton_tooltip" value="<?php echo $hostbutton_tooltip; ?>"></div>
</div>

<div class="form-group">
    <label for="inputHostButtonUrl"><?php echo $sprache->hostbutton_url;?></label>
    <div class="controls"><input class="form-control" id="inputHostButtonUrl" type="text" name="hostbutton_url" value="<?php echo $hostbutton_url; ?>"></div>
</div>

<div class="form-group">
    <label for="inputHostButtonGFXUrl"><?php echo $sprache->hostbutton_gfx_url;?></label>
    <div class="controls"><input class="form-control" id="inputHostButtonGFXUrl" type="text" name="hostbutton_gfx_url" value="<?php echo $hostbutton_gfx_url; ?>"></div>
</div>

<div class="form-group">
    <label for="inputFlexSlots"><?php echo $sprache->flexSlots;?></label>
    <div class="controls">
        <select class="form-control" id="inputFlexSlots" name="flexSlots" onchange="toggleID('#flexSlotsBox', this.value)">
            <option value="N"><?php echo $gsprache->no;?></option>
            <option value="Y" <?php if($flexSlots=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
        </select>
    </div>
</div>

<div id="flexSlotsBox">
    <div class="form-group">
        <label for="inputFlexSlotsFree"><?php echo $sprache->flexSlotsFree;?></label>
        <div class="controls"><input class="form-control" id="inputFlexSlotsFree" type="number" name="flexSlotsFree" value="<?php echo $flexSlotsFree;?>"></div>
    </div>

    <div class="form-group">
        <label for="inputFlexSlotsPercent"><?php echo $sprache->flexSlotsPercent;?></label>
        <div class="controls"><input class="form-control" id="inputFlexSlotsPercent" type="number" name="flexSlotsPercent" value="<?php echo $flexSlotsPercent;?>"></div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        toggleID('#flexSlotsBox', $('#inputFlexSlots').val());
    });
</script>