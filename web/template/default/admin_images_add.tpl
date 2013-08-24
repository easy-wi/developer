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
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=im&amp;d=ad&amp;r=im" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group">
                <label class="control-label" for="inputUpdates">Autoupdate</label>
                <div class="controls">
                    <select class="span12" id="inputUpdates" name="updates">
                        <option value="1">Vendor + Rsync/FTP Sync</option>
                        <option value="2">Vendor</option>
                        <option value="4">Rsync/FTP Sync</option>
                        <option value="3"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSteamGame"><?php echo $sprache->steam;?></label>
                <div class="controls">
                    <select class="span12" id="inputSteamGame" name="steamgame">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y">HLDSUpdater</option>
                        <option value="S">SteamCmd</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSteamAppID">Steam appID</label>
                <div class="controls"><input class="span12" id="inputSteamAppID" type="text" name="appID" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMods"><?php echo $sprache->mods;?></label>
                <div class="controls">
                    <select class="span12" id="inputMods" name="gamemod" onchange="textdrop('mods');">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div id="mods" class="control-group <?php if($gamemod=='N') echo 'display_none'; ?>">
                <label class="control-label" for="inputMods2"><?php echo $sprache->mods2;?></label>
                <div class="controls">
                    <select class="span12" id="inputMods2" name="gamemod2">
                        <?php foreach ($table as $table_row) { ?>
                        <option value="<?php echo $table_row['shorten'];?>"><?php echo $table_row['shorten'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputProtect"><?php echo $gssprache->protect;?></label>
                <div class="controls">
                    <select class="span12" id="inputProtect" name="protected">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputShorten"><?php echo $sprache->abkuerz;?></label>
                <div class="controls"><input class="span12" id="inputShorten" type="text" name="shorten" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputQstat"><?php echo $sprache->qstat;?></label>
                <div class="controls">
                    <select class="span12" id="inputQstat" name="qstat">
                        <?php foreach ($table3 as $table_row3) { echo $table_row3['option'];} ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                <div class="controls"><input class="span12" id="inputDesc" type="text" name="description" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBin"><?php echo $sprache->bin;?></label>
                <div class="controls"><input class="span12" id="inputBin" type="text" name="gamebinary" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBinDir"><?php echo $sprache->bin_folder;?>Binary Dir</label>
                <div class="controls"><input class="span12" id="inputBinDir" type="text" name="binarydir" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputModfolder"><?php echo $sprache->mod;?></label>
                <div class="controls"><input class="span12" id="inputModfolder" type="text" name="modfolder" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMap"><?php echo $sprache->startmap;?></label>
                <div class="controls"><input class="span12" id="inputMap" type="text" name="map" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMapgroup"><?php echo $sprache->startmapgroup;?></label>
                <div class="controls"><input class="span12" id="inputMapgroup" type="text" name="mapGroup" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputQstatParam"><?php echo $sprache->qstatpassparam;?></label>
                <div class="controls"><input class="span12" id="inputQstatParam" type="text" name="qstatpassparam" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPortMax"><?php echo $sprache->portMax;?></label>
                <div class="controls">
                    <select class="span12" id="inputPortMax" name="portMax">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPortStep"><?php echo $sprache->portStep;?></label>
                <div class="controls"><input class="span12" id="inputPortStep" type="text" name="portStep" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort1"><?php echo $sprache->port;?> 1</label>
                <div class="controls"><input class="span12" id="inputPort1" type="text" name="portOne" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort2"><?php echo $sprache->port;?> 2</label>
                <div class="controls"><input class="span12" id="inputPort2" type="text" name="portTwo" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort3"><?php echo $sprache->port;?> 3</label>
                <div class="controls"><input class="span12" id="inputPort3" type="text" name="portThree" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort4"><?php echo $sprache->port;?> 4</label>
                <div class="controls"><input class="span12" id="inputPort4" type="text" name="portFour" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort5"><?php echo $sprache->port;?> 5</label>
                <div class="controls"><input class="span12" id="inputPort5" type="text" name="portFive" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCmd"><?php echo $sprache->start;?></label>
                <div class="controls"><textarea class="span12" id="inputCmd" rows="5" name="cmd"></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputConfigs"><?php echo $sprache->configs;?></label>
                <div class="controls"><textarea class="span12" id="inputConfigs" rows="5" name="configs"></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputConfigsEdit"><?php echo $sprache->configedit;?></label>
                <div class="controls"><textarea class="span12" id="inputConfigsEdit" rows="5" name="configedit"></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputModsCmd"><?php echo $sprache->modcmds;?></label>
                <div class="controls"><textarea class="span12" id="inputModsCmd" rows="5" name="modcmds"></textarea></div>
            </div>
            <!--<div class="control-group">
                <label class="control-label" for="inputIptables"><?php echo $sprache->iptables;?></label>
                <div class="controls"><textarea class="span12" id="inputIptables" rows="5" name="iptables"></textarea></div>
            </div>-->
            <div class="control-group">
                <label class="control-label" for="inputProtectedSaveCFGs"><?php echo $sprache->protectedSaveCFGs;?></label>
                <div class="controls"><textarea class="span12" id="inputProtectedSaveCFGs" rows="5" name="protectedSaveCFGs"></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>