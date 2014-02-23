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
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_calendar;?></div>
</div>
<hr>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=ca&amp;id=<?php echo $id;?>&amp;r=ca" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <script type="text/javascript">
			$(document).ready(function (){
				$('#inputSwitch').change(function() {
					var shorten=$('#inputSwitch').val();
					$('#inputTemplate1').text(shorten);
					$('#inputTemplate2').text(shorten+'-2');
					$('#inputTemplate3').text(shorten+'-3');
					if($(this).find('option:selected').data('protected')=='Y') {
						$('#protectedSettings').collapse('show');
					}
					else {
						$('#protectedSettings').collapse('hide');
					}
					if($(this).find('option:selected').data('gameq')=='minecraft') {
						console.log('minecraft');
						$('#worldsaveSettings').collapse('show');
						$('#mapSettings').collapse('hide');
					}
					else {
						console.log('not minecraft');
						$('#worldsaveSettings').collapse('hide');
						$('#mapSettings').collapse('show');
					}
					if(shorten=='csgo') {
						$('#mapGroupSettings').collapse('show');
					}
					else {
						$('#mapGroupSettings').collapse('hide');
					}
				});					
				$('#inputSwitch').change();
			});
			</script>
            
            <div class="control-group">
                <label class="control-label" for="inputBackup"><?php echo $gsprache->backup;?></label>
                <div class="controls">
                    <select name="backup" id="inputBackup">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if($backup=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div id="worldsaveSettings">
	            <div class="control-group">
	                <label class="control-label" for="inputWorldSave">Minecraft Worldsave</label>
	                <div class="controls">
	                    <select name="worldsafe" id="inputWorldSave">
	                        <option value="N"><?php echo $gsprache->no;?></option>
	                        <option value="Y" <?php if($worldsafe=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
	                    </select>
	                </div>
	            </div>
	        </div>
            <?php if(in_array('srcds_run',$binaryArray) and (in_array(2,$uploadallowed) or in_array(3,$uploadallowed))){ ?>
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
                            <option value="<?php echo $table_row['shorten'];?>" <?php if($gsswitch==$table_row['shorten']) echo 'selected="selected"';?> data-protected="<?php echo $table_row['protected'];?>" data-gameq="<?php echo $table_row['gameq'];?>"><?php echo $table_row['description'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputTemplate"><?php echo $gsprache->template;?></label>
                    <div class="controls">
                        <select name="template" id="inputTemplate">
                            <option id="inputTemplate1" value="1"></option>
                            <option id="inputTemplate2" value="2" <?php if($template=="2") echo 'selected="selected"';?>></option>
                            <option id="inputTemplate3" value="3" <?php if($template=="3") echo 'selected="selected"';?>></option>
                        </select>
                    </div>
                </div>
                <?php if ($pallowed=="Y") { ?>
                <div id="protectedSettings">
	                <div class="control-group">
	                    <label class="control-label" for="inputProtected"><?php echo $sprache->protect;?></label>
	                    <div class="controls">
	                        <select name="protected" id="inputProtected">
	                            <option value="N" data-toggle="collapse" data-target="#anticheatSettings"><?php echo $sprache->off2;?></option>
	                            <option value="Y" data-toggle="collapse" data-target="#anticheatSettings" <?php if($pro=="Y") echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
	                        </select>
	                    </div>
	                </div>
	            </div>
                <?php } ?>
                <div id="anticheatSettings" class="collapse <?php if ($pro!='Y') echo 'in';?>">
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
	            </div>
	            <div id="mapSettings">
	                <div class="control-group">
	                    <label class="control-label" for="inputMap"><?php echo $sprache->map;?></label>
	                    <div class="controls">
	                        <input id="inputMap" type="text" name="map" value="<?php echo $map;?>" >
	                    </div>
	                </div>
	            </div>
	            <div id="mapGroupSettings">
	                <div class="control-group" id="mapGroup">
	                    <label class="control-label" for="inputMapGroup"><?php echo $sprache->startmapgroup;?></label>
	                    <div class="controls">
	                        <input id="inputMapGroup" type="text" name="mapGroup" value="<?php echo $mapGroup;?>" >
	                    </div>
	                </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="date" value="<?php echo $date2;?>">
                    <input type="hidden" name="edit2" value="edit">
                </div>
            </div>
        </form>
    </div>
</div>