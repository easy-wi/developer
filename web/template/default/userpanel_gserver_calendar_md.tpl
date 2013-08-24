<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $sprache->restarttime;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.':'.$port;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $day.' '.$hour." ".$sprache->hour;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="userpanel.php?w=ca&amp;id=<?php echo $server_id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="inputBackup"><?php echo $gsprache->backup;?></label>
                <div class="controls">
                    <select name="backup" id="inputBackup">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if($backup=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <?php if(in_array('minecraft',$qstat_array)){ ?>
            <div class="control-group">
                <label class="control-label" for="inputWorldSave">Minecraft Worldsave</label>
                <div class="controls">
                    <select name="worldsafe" id="inputWorldSave">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if($worldsafe=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <?php } ?>
            <?php if(in_array('a2s',$qstat_array) and (in_array('2',$uploadallowed) or in_array('3',$uploadallowed))){ ?>
            <div class="control-group">
                <label class="control-label" for="inputSourceTV">SourceTV Demo Upload</label>
                <div class="controls">
                    <select name="upload" id="inputSourceTV">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if($upload=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputRestart"><?php echo $sprache->restarts;?></label>
                <div class="controls">
                    <select name="restart" id="inputRestart">
                        <option value="Y" data-toggle="collapse" data-target="#restartSettings"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($restart=="N") echo 'selected="selected"';?> data-toggle="collapse" data-target="#restartSettings"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div id="restartSettings" class="collapse <?php if ($restart=='Y') echo 'in';?>">
                <div class="control-group">
                    <label class="control-label" for="inputSwitch"><?php echo $sprache->gameswitch;?></label>
                    <div class="controls">
                        <select name="shorten" id="inputSwitch" onchange="$.get('serverallocation.php?mapgroup=' + this.value, function(data) { $('#mapGroup').html(data); });">
                            <?php foreach ($table as $table_row){ ?>
                            <option value="<?php echo $table_row['shorten'];?>" <?php if($gsswitch==$table_row['shorten']) echo 'selected="selected"';?>><?php echo $table_row['description'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputTemplate"><?php echo $gsprache->template;?></label>
                    <div class="controls">
                        <select name="template" id="inputTemplate">
                            <option>1</option>
                            <option <?php if($template=="2") echo 'selected="selected"';?>>2</option>
                            <option <?php if($template=="3") echo 'selected="selected"';?>>3</option>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputCheat"><?php echo $sprache->anticheat;?></label>
                    <div class="controls">
                        <select name="anticheat" id="inputCheat">
                            <option value="1"><?php echo $anticheatsoft." ".$sprache->on;?></option>
                            <option value="2" <?php if($anticheat=="2") echo 'selected="selected"';?>><?php echo $anticheatsoft." ".$sprache->off2;?></option>
                            <?php foreach($eac as $ea) echo $ea;?>
                        </select>
                    </div>
                </div>
                <?php if ($pallowed=="Y") { ?>
                <div class="control-group">
                    <label class="control-label" for="inputProtected"><?php echo $sprache->protect;?></label>
                    <div class="controls">
                        <select name="protected" id="inputProtected">
                            <option value="N"><?php echo $sprache->off2;?></option>
                            <option value="Y" <?php if($pro=="Y") echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                        </select>
                    </div>
                </div>
                <?php } ?>
                <div class="control-group">
                    <label class="control-label" for="inputMap"><?php echo $sprache->map;?></label>
                    <div class="controls">
                        <input id="inputMap" type="text" name="map" value="<?php echo $map;?>" >
                    </div>
                </div>
                <div class="control-group" id="mapGroup">
                    <?php if ($defaultMapGroup!=null){ ?>
                    <label class="control-label" for="inputMapGroup"><?php echo $sprache->startmapgroup;?></label>
                    <div class="controls">
                        <input id="inputMapGroup" type="text" name="mapGroup" value="<?php echo $mapGroup;?>" >
                    </div>
                    <?php }?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i></button>
                    <input type="hidden" name="date" value="<?php echo $date2;?>">
                    <input type="hidden" name="edit2" value="edit">
                </div>
            </div>
        </form>
    </div>
</div>