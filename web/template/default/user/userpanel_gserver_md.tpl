<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a></li>
        <li><?php echo $sprache->server_details;?></li>
        <li class="active"><?php echo $address;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <!-- Content Help -->
	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-11">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_md;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-11">
            <div class="box box-info">

                <form role="form" action="userpanel.php?w=gs&amp;d=md&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <?php if ($pa['ftpaccess'] and $ftpAccess=='Y') { ?>
                        <div class="form-group">
                            <label for="ftppass"><?php echo $sprache->ftp_password;?></label>
                            <input class="form-control" id="ftppass" type="text" name="ftppass" value="<?php echo $ftppass;?>" required>
                        </div>
                        <?php } ?>

                        <div class="form-group">
                            <label for="gameswitch"><?php echo $sprache->gameswitch;?></label>
                            <select class="form-control" id="gameswitch" name="shorten" onchange="SwitchShowHideRows(this.value);">
                                <?php foreach ($table as $table_row){ ?>
                                <option value="<?php echo $table_row['id'];?>" <?php if($serverID==$table_row['id']) echo 'selected="selected"';?>><?php echo $table_row['description'];?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <?php foreach ($table as $table_row) { ?>
                        <div class="<?php echo $table_row['id'].' '.$table_row['displayNone'];?> switch">


                            <?php if(count($table_row['mods'])>0){ ?>
                            <div class="form-group">
                                <label for="mod_<?php echo $table_row['id'];?>">Mods</label>
                                <select class="form-control" id="mod_<?php echo $table_row['id'];?>" name="mod_<?php echo $table_row['id'];?>">
                                    <?php foreach ($table_row['mods'] as $mod_single) { ?>
                                    <option <?php if($table_row['mod']==$mod_single) echo 'selected="selected"';?>><?php echo $mod_single;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php } ?>

                            <div class="form-group">
                                <label for="servertemplate_<?php echo $table_row['id'];?>"><?php echo $gsprache->template;?></label>
                                <select class="form-control" id="servertemplate_<?php echo $table_row['id'];?>" name="servertemplate_<?php echo $table_row['id'];?>">
                                    <option value="1"><?php echo $table_row['shorten'];?></option>
                                    <option value="2" <?php if($table_row['servertemplate']==2) echo "selected";?>><?php echo $table_row['shorten'];?>-2</option>
                                    <option value="3" <?php if($table_row['servertemplate']==3) echo "selected";?>><?php echo $table_row['shorten'];?>-3</option>
                                </select>
                            </div>

                            <?php if (in_array($table_row['gamebinary'],array('hlds_run','srcds_run','cod4_lnxded'))) { ?>
                            <div class="form-group">
                                <label for="anticheat_<?php echo $table_row['id'];?>"><?php echo $sprache->anticheat;?></label>
                                <select class="form-control" id="anticheat_<?php echo $table_row['id'];?>" name="anticheat_<?php echo $table_row['id'];?>">
                                    <option value="1"><?php echo $table_row['anticheatsoft']." ".$sprache->on;?></option>
                                    <option value="2" <?php if($table_row['anticheat']==2) echo 'selected="selected"';?>><?php echo $table_row['anticheatsoft']." ".$sprache->off2;?></option>
                                    <?php foreach($table_row['eac'] as $eac) echo $eac;?>
                                </select>
                            </div>
                            <?php } ?>

                            <?php if ($table_row['upload']==true) { ?>
                            <div class="form-group">
                                <label for="uploaddir_<?php echo $table_row['id'];?>">SourceTV Demo FTP</label>
                                <input class="form-control" id="uploaddir_<?php echo $table_row['id'];?>" type="text" name="uploaddir_<?php echo $table_row['id'];?>" value="<?php echo $table_row['uploaddir'];?>">
                            </div>
                            <?php } ?>

                            <?php if ($table_row['userfps']=="Y") { ?>
                            <div class="form-group">
                                <label for="fps_<?php echo $table_row['id'];?>"><?php echo $sprache->fps;?></label>
                                <input class="form-control" id="fps_<?php echo $table_row['id'];?>" type="text" name="fps_<?php echo $table_row['id'];?>" value="<?php echo $table_row['fps'];?>">
                            </div>
                            <?php } ?>

                            <?php if ($table_row['usertick']=="Y") { ?>
                            <div class="form-group">
                                <label for="tic_<?php echo $table_row['id'];?>"><?php echo $sprache->tick;?></label>
                                <input class="form-control" id="tic_<?php echo $table_row['id'];?>" type="text" name="tic_<?php echo $table_row['id'];?>" value="<?php echo $table_row['tic'];?>">
                            </div>
                            <?php } ?>

                            <?php if ($table_row['usermap']=="Y" and !in_array($table_row['map'],array('',null))) { ?>
                            <div class="form-group">
                                <label for="map_<?php echo $table_row['id'];?>"><?php echo $sprache->startmap;?></label>
                                <input class="form-control" id="map_<?php echo $table_row['id'];?>" type="text" name="map_<?php echo $table_row['id'];?>" value="<?php echo $table_row['map'];?>">
                            </div>

                            <?php if($table_row['workshopCollection']!==false){ ?>
                            <div class="form-group">
                                <label for="workShop_<?php echo $table_row['id'];?>">Steam Workshop</label>
                                <select class="form-control" id="workShop_<?php echo $table_row['id'];?>" name="workShop_<?php echo $table_row['id'];?>" onchange="SwitchShowHideRows(this.value,'mapGroupWorkShop-<?php echo $table_row['id'];?>');">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if($table_row['workShop']=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                            <div class="Y <?php if($table_row['workShop']=='N') echo 'display_none';?> mapGroupWorkShop-<?php echo $table_row['id'];?> form-group">
                                <label for="workshopCollection_<?php echo $table_row['id'];?>">Workshop Collection</label>
                                <input class="form-control" id="workshopCollection_<?php echo $table_row['id'];?>" type="text" name="workshopCollection_<?php echo $table_row['id'];?>" value="<?php echo $table_row['workshopCollection'];?>">
                                <span class="help-block"><?php echo $sprache->collection_info;?></span>
                            </div>

                            <div class="Y <?php if($table_row['workShop']=='N') echo 'display_none';?> mapGroupWorkShop-<?php echo $table_row['id'];?> form-group">
                                <label for="webapiAuthkey_<?php echo $table_row['id'];?>">Steam Webapi Authkey</label>
                                <input class="form-control" id="webapiAuthkey_<?php echo $table_row['id'];?>" type="text" name="webapiAuthkey_<?php echo $table_row['id'];?>" value="<?php echo $table_row['webapiAuthkey'];?>">
                                <span class="help-block"><?php echo $sprache->authkey_info;?></span>
                            </div>
                            <?php } ?>

                            <?php if(!in_array($table_row['defaultMapGroup'],array('',null))){ ?>
                            <div class="N <?php if($table_row['workShop']=='Y') echo 'display_none';?> mapGroupWorkShop-<?php echo $table_row['id'];?> form-group">
                                <label for="mapGroup_<?php echo $table_row['id'];?>"><?php echo $sprache->startmapgroup;?></label>
                                <select class="form-control" id="mapGroup_<?php echo $table_row['id'];?>" name="mapGroup_<?php echo $table_row['id'];?>">
                                    <?php foreach($table_row['mapGroupsAvailable'] as $g){ ?>
                                    <option<?php if($g==$table_row['mapGroup']) echo ' selected="selected"';?>><?php echo $g;?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <?php } ?>
                            <?php } ?>
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
    window.onDomReady = initReady;

    function initReady(fn) {
        if(document.addEventListener) {
            document.addEventListener("DOMContentLoaded", fn, false);
        } else {
            document.onreadystatechange = function() {
                readyState(fn);
            }
        }
    }

    function readyState(func) {
        if(document.readyState == "interactive" || document.readyState == "complete") {
            func();
        }
    }

    window.onDomReady(onReady); function onReady() {
        SwitchShowHideRows('init_ready');
    }
</script>