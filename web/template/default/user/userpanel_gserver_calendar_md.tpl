<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $sprache->restarttime;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a></li>
        <li><?php echo $sprache->restarttime;?></li>
        <li><?php echo $serverip.':'.$port;?></li>
        <li class="active"><?php echo $day.' '.$hour." ".$sprache->hour;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">

                <form role="form" action="userpanel.php?w=ca&amp;id=<?php echo $id;?>&amp;r=ca" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input class="form-control" type="hidden" name="date" value="<?php echo $date2;?>">
                    <input class="form-control" type="hidden" name="edit2" value="edit">

                    <div class="box-body">

                        <div id="restartSettings">
                            <div class="form-group">
                                <label for="inputSwitch"><?php echo $sprache->gameswitch;?></label>
                                <select class="form-control" name="shorten" id="inputSwitch" onchange="changeSwitch(this.value);">
                                    <?php foreach ($table as $table_row){ ?>
                                    <option value="<?php echo $table_row['shorten'];?>" <?php if($gsswitch==$table_row['shorten']) echo 'selected="selected"';?> data-protected="<?php echo $table_row['protected'];?>" data-gameq="<?php echo $table_row['gameq'];?>"><?php echo $table_row['description'];?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputTemplate"><?php echo $gsprache->template;?></label>
                            <select class="form-control" name="template" id="inputTemplate">
                                <option id="inputTemplate1" value="1"></option>
                                <option id="inputTemplate2" value="2" <?php if($template=="2") echo 'selected="selected"';?>></option>
                                <option id="inputTemplate3" value="3" <?php if($template=="3") echo 'selected="selected"';?>></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputRestart"><?php echo $sprache->restarts;?></label>
                            <select class="form-control" name="restart" id="inputRestart" onchange="toggleRestart(this.value);">
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                                <option value="N" <?php if($restart=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                            </select>
                        </div>

                        <?php if ($pallowed=="Y") { ?>
                        <div class="form-group" id="protectedSettings">
                            <label for="inputProtected"><?php echo $sprache->protect;?></label>
                            <select class="form-control" name="protected" id="inputProtected">
                                <option value="N"><?php echo $sprache->off2;?></option>
                                <option value="Y" <?php if($pro=="Y") echo 'selected="selected"';?>><?php echo $sprache->on;?></option>
                            </select>
                        </div>
                        <?php } ?>

                        <div id="anticheatSettings" class="form-group">
                            <div class="form-group">
                                <label for="inputCheat"><?php echo $sprache->anticheat;?></label>
                                <select class="form-control"  name="anticheat" id="inputCheat">
                                    <option value="1"><?php echo $anticheatsoft." ".$sprache->on;?></option>
                                    <option value="2" <?php if($anticheat==2) echo 'selected="selected"';?>><?php echo $anticheatsoft." ".$sprache->off2;?></option>
                                    <?php foreach($eac as $ea) echo $ea;?>
                                </select>
                            </div>
                        </div>

                        <div id="mapSettings" class="form-group">
                            <label for="inputMap"><?php echo $sprache->map;?></label>
                            <input class="form-control" id="inputMap" type="text" name="map" value="<?php echo $map;?>" >
                        </div>

                        <div id="mapGroupSettings" class="form-group">
                            <label for="inputMapGroup"><?php echo $sprache->startmapgroup;?></label>
                            <input class="form-control" id="inputMapGroup" type="text" name="mapGroup" value="<?php echo $mapGroup;?>" >
                        </div>

                        <div class="form-group" id="worldsaveSettings">
                            <label for="inputWorldSave">Minecraft Worldsave</label>
                            <select class="form-control" name="worldsafe" id="inputWorldSave">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if($worldsafe=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group" id="backupSettings">
                            <label for="inputBackup"><?php echo $gsprache->backup;?></label>
                            <select class="form-control" name="backup" id="inputBackup">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if($backup=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <?php if(in_array('srcds_run',$binaryArray) and (in_array(2,$uploadallowed) or in_array(3,$uploadallowed))){ ?>
                        <div class="form-group">
                            <label for="inputSourceTV">SourceTV Demo Upload</label>
                            <select class="form-control" name="upload" id="inputSourceTV">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if($upload=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function changeSwitch (value) {

        $('#inputTemplate1').text(value);
        $('#inputTemplate2').text(value + '-2');
        $('#inputTemplate3').text(value + '-3');

        if(value == 'csgo') {
            $('#mapGroupSettings').collapse('show');
        } else {
            $('#mapGroupSettings').collapse('hide');
        }


        var details = $('#inputSwitch').find('option:selected');

        if(details.data('protected')=='Y') {
            $('#protectedSettings').collapse('show');
        } else {
            $('#protectedSettings').collapse('hide');
        }

        if(details.data('gameq') == 'minecraft') {
            $('#worldsaveSettings').collapse('show');
            $('#mapSettings').collapse('hide');
            $('#anticheatSettings').collapse('hide');
        } else {
            $('#worldsaveSettings').collapse('hide');
            $('#mapSettings').collapse('show');
            $('#anticheatSettings').collapse('show');
        }

        $( "#mapGroup" ).load('ajax.php?mapgroup=' + value);

        toggleRestart($('#inputRestart').val());
    }

    function toggleRestart (restart) {

        var gameQ = $('#inputSwitch').find('option:selected').data('gameq');

        if (restart == 'Y') {

            if (gameQ != 'minecraft') {
                $('#protectedSettings').collapse('show');
                $('#anticheatSettings').collapse('show');
                $('#mapSettings').collapse('show');
                $('#mapGroupSettings').collapse('show');
            } else {
                $('#worldsaveSettings').collapse('hide');
            }

            $('#backupSettings').collapse('hide');

        } else {
            if (gameQ != 'minecraft') {
                $('#protectedSettings').collapse('hide');
                $('#anticheatSettings').collapse('hide');
                $('#mapSettings').collapse('hide');
                $('#mapGroupSettings').collapse('hide');
            } else {
                $('#worldsaveSettings').collapse('show');
            }

            $('#backupSettings').collapse('show');
        }
    }

    $(function() {
        toggleRestart($('#inputRestart').val());
        changeSwitch($('#inputSwitch').val());
    });
</script>