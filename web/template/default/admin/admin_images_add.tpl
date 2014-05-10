<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=im"><?php echo $gsprache->template;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=im&amp;d=ad" enctype="multipart/form-data" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo $token;?>">
            <input type="hidden" name="action" value="ad">
            <input type="hidden" name="import" value="1">
            <div class="control-group">
                <label class="control-label" for="inputUpload"><?php echo $gsprache->import;?></label>
                <div class="controls">
                    <input id="inputUpload" type="file" name="file">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-upload icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>
<hr>
<?php if (count($errors)>0){ ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4><?php echo $gsprache->errors;?></h4>
    <?php echo implode(', ',$errors);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=im&amp;d=ad&amp;r=im" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo $token;?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group<?php if(isset($errors['updates'])) echo ' error';?>">
                <label class="control-label" for="inputUpdates">Autoupdate</label>
                <div class="controls">
                    <select class="span12" id="inputUpdates" name="updates">
                        <option value="1">Vendor + Rsync/FTP Sync</option>
                        <option value="2" <?php if ($updates==2) echo 'selected="selected"'; ?>>Vendor</option>
                        <option value="4" <?php if ($updates==4) echo 'selected="selected"'; ?>>Rsync/FTP Sync</option>
                        <option value="3" <?php if ($updates==3) echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['steamgame'])) echo ' error';?>">
                <label class="control-label" for="inputSteamGame"><?php echo $sprache->steam;?></label>
                <div class="controls">
                    <select class="span12" id="inputSteamGame" name="steamgame">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="S" <?php if ($steamgame=="S") echo 'selected="selected"'; ?>>SteamCmd</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSteamAppID">Steam appID</label>
                <div class="controls"><input class="span12" id="inputSteamAppID" type="text" name="appID" value="<?php echo $appID;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['gamemod'])) echo ' error';?>">
                <label class="control-label" for="inputMods"><?php echo $sprache->mods;?></label>
                <div class="controls">
                    <select class="span12" id="inputMods" name="gamemod" onchange="textdrop('mods');">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($gamemod=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div id="mods" class="control-group <?php if($gamemod=='N') echo 'display_none'; ?>">
                <label class="control-label" for="inputMods2"><?php echo $sprache->mods2;?></label>
                <div class="controls">
                    <select class="span12" id="inputMods2" name="gamemod2">
                        <option></option>
                        <?php foreach ($table as $table_row) { ?>
                        <option value="<?php echo $table_row['shorten'];?>" <?php if($table_row['shorten']==$gamemod2) echo 'selected="selected"'; ?>><?php echo $table_row['shorten'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputProtect"><?php echo $gssprache->protect;?></label>
                <div class="controls">
                    <select class="span12" id="inputProtect" name="protected">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($protected=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputWorkshop">Steam Workshop</label>
                <div class="controls">
                    <select class="span12" id="inputWorkshop" name="workShop">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($workShop=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRamLimited"><?php echo $sprache->ramLimited;?></label>
                <div class="controls">
                    <select class="span12" id="inputRamLimited" name="ramLimited">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($ramLimited=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFtpAccess"><?php echo $sprache->ftpAccess;?></label>
                <div class="controls">
                    <select class="span12" id="inputFtpAccess" name="ftpAccess">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($ftpAccess=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputOS"><?php echo $rsprache->os;?></label>
                <div class="controls">
                    <select class="span12" id="inputOS" name="os">
                        <option value="L">Linux</option>
                        <!--<option value="W" <?php if($os=='W') echo 'selected="selected"';?>>Windows</option>
                        <option value="B" <?php if($os=='B') echo 'selected="selected"';?>>Linux + Windows</option>-->
                    </select>
                </div>
            </div>
            <div class="control-group<?php if(isset($errors['shorten'])) echo ' error';?>">
                <label class="control-label" for="inputShorten"><?php echo $sprache->abkuerz;?></label>
                <div class="controls"><input class="span12" id="inputShorten" type="text" name="shorten" value="<?php echo $shorten;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputGameQ">GameQ</label>
                <div class="controls">
                    <select class="span12" id="inputGameQ" name="gameq">
                        <option value="">Other</option>
                        <?php foreach ($protocols as $k=>$v){ ?>
                        <option value="<?php echo $k;?>" <?php if($k==$gameq) echo 'selected="selected"';?>><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                <div class="controls"><input class="span12" id="inputDesc" type="text" name="description" value="<?php echo $description;?>"></div>
            </div>
            <div class="L control-group switch <?php if($os=='W') echo 'display_none';if(isset($errors['gamebinary'])) echo 'error';?>">
                <label class="control-label" for="inputBin">Linux <?php echo $sprache->bin;?></label>
                <div class="controls"><input class="span12" id="inputBin" type="text" name="gamebinary" value="<?php echo $gamebinary;?>"></div>
            </div>
            <div class="W control-group switch <?php if($os=='L') echo 'display_none';if(isset($errors['gamebinaryWin'])) echo 'error';?>">
                <label class="control-label" for="inputBinWin">Windows <?php echo $sprache->bin;?></label>
                <div class="controls"><input class="span12" id="inputBinWin" type="text" name="gamebinaryWin" value="<?php echo $gamebinaryWin;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBinDir"><?php echo $sprache->bin_folder;?></label>
                <div class="controls"><input class="span12" id="inputBinDir" type="text" name="binarydir" value="<?php echo $binarydir;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputModfolder"><?php echo $sprache->mod;?></label>
                <div class="controls"><input class="span12" id="inputModfolder" type="text" name="modfolder" value="<?php echo $modfolder;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMap"><?php echo $sprache->startmap;?></label>
                <div class="controls"><input class="span12" id="inputMap" type="text" name="map" value="<?php echo $map;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMapgroup"><?php echo $sprache->startmapgroup;?></label>
                <div class="controls"><input class="span12" id="inputMapgroup" type="text" name="mapGroup" value="<?php echo $mapGroup;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPortMax"><?php echo $sprache->portMax;?></label>
                <div class="controls">
                    <select class="span12" id="inputPortMax" name="portMax">
                        <option>1</option>
                        <option <?php if($portMax==2) echo 'selected="selected"'; ?>>2</option>
                        <option <?php if($portMax==3) echo 'selected="selected"'; ?>>3</option>
                        <option <?php if($portMax==4) echo 'selected="selected"'; ?>>4</option>
                        <option <?php if($portMax==5) echo 'selected="selected"'; ?>>5</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPortStep"><?php echo $sprache->portStep;?></label>
                <div class="controls"><input class="span12" id="inputPortStep" type="text" name="portStep" value="<?php echo $portStep;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort1"><?php echo $sprache->port;?> 1</label>
                <div class="controls"><input class="span12" id="inputPort1" type="text" name="portOne" value="<?php echo $portOne;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort2"><?php echo $sprache->port;?> 2</label>
                <div class="controls"><input class="span12" id="inputPort2" type="text" name="portTwo" value="<?php echo $portTwo;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort3"><?php echo $sprache->port;?> 3</label>
                <div class="controls"><input class="span12" id="inputPort3" type="text" name="portThree" value="<?php echo $portThree;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort4"><?php echo $sprache->port;?> 4</label>
                <div class="controls"><input class="span12" id="inputPort4" type="text" name="portFour" value="<?php echo $portFour;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort5"><?php echo $sprache->port;?> 5</label>
                <div class="controls"><input class="span12" id="inputPort5" type="text" name="portFive" value="<?php echo $portFive;?>"></div>
            </div>
            <div class="control-group<?php if(isset($errors['cmd'])) echo ' error';?>">
                <label class="control-label" for="inputCmd"><?php echo $sprache->start;?></label>
                <div class="controls"><textarea class="span12" id="inputCmd" rows="5" name="cmd"><?php echo $cmd;?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputConfigs"><?php echo $sprache->configs;?></label>
                <div class="controls"><textarea class="span12" id="inputConfigs" rows="5" name="configs"><?php echo $configs;?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputConfigsEdit"><?php echo $sprache->configedit;?></label>
                <div class="controls"><textarea class="span12" id="inputConfigsEdit" rows="5" name="configedit"><?php echo $configedit;?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputModsCmd"><?php echo $sprache->modcmds;?></label>
                <div class="controls"><textarea class="span12" id="inputModsCmd" rows="5" name="modcmds"><?php echo $modcmds;?></textarea></div>
            </div>
            <!--<div class="control-group">
                <label class="control-label" for="inputIptables"><?php echo $sprache->iptables;?></label>
                <div class="controls"><textarea class="span12" id="inputIptables" rows="5" name="iptables"><?php echo $iptables;?></textarea></div>
            </div>-->
            <div class="control-group">
                <label class="control-label" for="inputProtectedSaveCFGs"><?php echo $sprache->protectedSaveCFGs;?></label>
                <div class="controls"><textarea class="span12" id="inputProtectedSaveCFGs" rows="5" name="protectedSaveCFGs"><?php echo $protectedSaveCFGs;?></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>