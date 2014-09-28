<div class="form-group">
    <label for="inputServerInstalled"><?php echo $sprache->serverinstalled;?></label>
    <input class="form-control" id="inputServerInstalled" type="text" name="serverInstalled" value="<?php echo $installedServer.'/'.$maxServer;?>" disabled="disabled">
</div>

<div class="form-group">
    <label for="inputSlotsInstalled"><?php echo $sprache->slotsinstalled;?></label>
    <input class="form-control" id="inputSlotsInstalled" type="text" name="slotsInstalled" value="<?php echo $installedSlots.'/'.$maxSlots;?>" disabled="disabled">
</div>

<div class="form-group">
    <label for="inputRamUsed"><?php echo $sprache->raminstalled;?></label>
    <input class="form-control" id="inputRamUsed" type="text" name="ramUsed" value="<?php echo $installedRam.'/'.$maxRam;?>" disabled="disabled">
</div>

<div class="form-group">
    <label for="inputGames"><?php echo $sprache->games;?></label>
    <div class="controls">
        <select class="form-control chosen-select" id="inputGames" name="gameIDs[]" multiple="multiple" onchange="gameDetails(true);">
            <option></option>
            <?php foreach ($table as $key=>$arr){ ?>
            <option value="<?php echo $key;?>" <?php if(in_array($key,$installedGames)) echo 'selected="selected"';?>><?php echo $arr['description'];?></option>
            <?php } ?>
        </select>
    </div>
</div>

<?php if(!$ui->id('gameServerID', 10, 'get')){ ?>
<div class="form-group">
    <label for="inputPrimary"><?php echo $sprache->primary;?>:</label>
    <div class="controls">
        <select class="form-control chosen-select" id="inputPrimary" name="primary" onchange="bestPorts(this.value);" required="required">
            <option></option>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="inputGamesInstall"><?php echo $sprache->installGames;?>:</label>
    <div class="controls">
        <select class="form-control" id="inputGamesInstall" name="installGames">
            <option value="2"><?php echo $sprache->primary;?></option>
            <option value="1"><?php echo $sprache->installAll;?></option>
            <option value="3"><?php echo $gsprache->no;?></option>
        </select>
    </div>
</div>
<?php } ?>

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

<div class="form-group">
    <label><?php echo $sprache->usedports;?></label>
    <div class="controls" id="portList">
        <?php echo $ports;?>
    </div>
</div>

<div id="portWrapper">
    <div class="form-group">
        <label for="inputPortMain"><?php echo $sprache->port;?> 1</label>
        <div class="controls"><input class="form-control" id="inputPortMain" type="text" name="port" value="<?php echo $port;?>"></div>
    </div>

    <div class="form-group">
        <label for="inputPort2"><?php echo $sprache->port;?> 2</label>
        <div class="controls"><input class="form-control" id="inputPort2" type="text" name="port2" value="<?php echo $port2;?>"></div>
    </div>

    <div class="form-group">
        <label for="inputPort3"><?php echo $sprache->port;?> 3</label>
        <div class="controls"><input class="form-control" id="inputPort3" type="text" name="port3" value="<?php echo $port3;?>"></div>
    </div>

    <div class="form-group">
        <label for="inputPort4"><?php echo $sprache->port;?> 4</label>
        <div class="controls"><input class="form-control" id="inputPort4" type="text" name="port4" value="<?php echo $port4;?>"></div>
    </div>

    <div class="form-group">
        <label for="inputPort5"><?php echo $sprache->port;?> 5</label>
        <div class="controls"><input class="form-control" id="inputPort5" type="text" name="port5" value="<?php echo $port5;?>"></div>
    </div>
</div>

<div class="form-group">
    <label for="inputCoreBind">Core Bind</label>
    <div class="controls">
        <select class="form-control" id="inputCoreBind" name="taskset" onchange="toggleID('#theCores', this.value);">
            <option value="N"><?php echo $gsprache->no;?></option>
            <option value="Y" <?php if($taskset=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
        </select>
    </div>
</div>

<div id="theCores" class="form-group">
    <label for="inputCores">Cores <?php echo '0-'.($coreCount-1);?></label>
    <div class="controls">
        <select class="form-control" id="inputCores" name="cores[]" multiple="multiple">
            <?php foreach($cores as $core => $count) { ?>
            <option value="<?php echo $core;?>" <?php if(in_array($core,$usedCores)) echo 'selected="selected"';?>><?php echo $core.' ('.$count.') ';?></option>
            <?php }?>
        </select>
    </div>
</div>