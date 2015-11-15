<section class="content-header">
    <h1><?php echo $gsprache->gameserver.' '.$gsprache->template;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li><a href="admin.php?w=im"><i class="fa fa-file-text-o"></i> <?php echo $gsprache->gameserver.' '.$gsprache->template;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $description;?></li>
    </ol>
</section>

<section class="content">
    <?php if (count($errors)>0){ ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><?php echo $gsprache->errors;?></h4>
                <?php echo implode(', ',$errors);?>
            </div>
        </div>
    </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=im&amp;d=md&amp;id=<?php echo $id;?>&amp;r=im" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group<?php if(isset($errors['updates'])) echo ' has-error';?>">
                            <label class="control-label" for="inputUpdates">Autoupdate</label>
                            <div class="controls">
                                <select class="form-control" id="inputUpdates" name="updates">
                                    <option value="1">Vendor + Rsync/FTP Sync</option>
                                    <option value="2" <?php if ($updates==2) echo 'selected="selected"'; ?>>Vendor</option>
                                    <option value="4" <?php if ($updates==4) echo 'selected="selected"'; ?>>Rsync/FTP Sync</option>
                                    <option value="3" <?php if ($updates==3) echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['steamgame'])) echo ' has-error';?>">
                            <label class="control-label" for="inputSteamGame"><?php echo $sprache->steam;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputSteamGame" name="steamgame">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="S" <?php if ($steamgame=="S") echo 'selected="selected"'; ?>>SteamCmd</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputSteamCmd">SteamCmd Account</label>
                            <div class="controls"><input class="form-control" id="inputSteamCmd" type="text" name="steamAccount" value="<?php echo $steamAccount;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputSteamCmdPWD">SteamCmd <?php echo $sprache->password;?></label>
                            <div class="controls"><input class="form-control" id="inputSteamCmdPWD" type="text" name="steamPassword" value="<?php echo $steamPassword;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputSteamAppID">Steam appID</label>
                            <div class="controls"><input class="form-control" id="inputSteamAppID" type="text" name="appID" value="<?php echo $appID;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['gamemod'])) echo ' has-error';?>">
                            <label class="control-label" for="inputMods"><?php echo $sprache->mods;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputMods" name="gamemod" onchange="SwitchShowHideRows(this.value,'switch',1);">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if($gamemod=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="Y switch form-group <?php if($gamemod=='N') echo 'display_none'; ?>">
                            <label class="control-label" for="inputMods2"><?php echo $sprache->mods2;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputMods2" name="gamemod2">
                                    <option></option>
                                    <?php foreach ($table as $table_row) { ?>
                                    <option value="<?php echo $table_row['shorten'];?>" <?php if($table_row['shorten']==$gamemod2) echo 'selected="selected"'; ?>><?php echo $table_row['shorten'];?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputProtect"><?php echo $gssprache->protect;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputProtect" name="protected">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($protected=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputWorkshop">Steam Workshop</label>
                            <div class="controls">
                                <select class="form-control" id="inputWorkshop" name="workShop">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($workShop=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputRamLimited"><?php echo $sprache->ramLimited;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputRamLimited" name="ramLimited">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($ramLimited=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputFtpAccess"><?php echo $sprache->ftpAccess;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputFtpAccess" name="ftpAccess">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($ftpAccess=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputLiveConsole"><?php echo $sprache->liveConsole;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputLiveConsole" name="liveConsole">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($liveConsole=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputOS"><?php echo $rsprache->os;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputOS" name="os">
                                    <option value="L">Linux</option>
                                    <!--<option value="W" <?php if($os=='W') echo 'selected="selected"';?>>Windows</option>
                                    <option value="B" <?php if($os=='B') echo 'selected="selected"';?>>Linux + Windows</option>-->
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['shorten'])) echo ' has-error';?>">
                            <label class="control-label" for="inputShorten"><?php echo $sprache->abkuerz;?></label>
                            <div class="controls"><input class="form-control" id="inputShorten" type="text" name="shorten" value="<?php echo $shorten;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputGameQ">GameQ</label>
                            <div class="controls">
                                <select class="form-control chosen-select" id="inputGameQ" name="gameq">
                                    <option value="">Other</option>
                                    <?php foreach ($protocols as $k=>$v){ ?>
                                    <option value="<?php echo $k;?>" <?php if($k==$gameq) echo 'selected="selected"';?>><?php echo $v;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                            <div class="controls"><input class="form-control" id="inputDesc" type="text" name="description" value="<?php echo $description;?>"></div>
                        </div>

                        <div class="L form-group switch <?php if($os=='W') echo 'display_none';if(isset($errors['gamebinary'])) echo 'error';?>">
                            <label class="control-label" for="inputBin">Linux <?php echo $sprache->bin;?></label>
                            <div class="controls"><input class="form-control" id="inputBin" type="text" name="gamebinary" value="<?php echo $gamebinary;?>"></div>
                        </div>

                        <div class="W form-group switch <?php if($os=='L') echo 'display_none';if(isset($errors['gamebinaryWin'])) echo 'error';?>">
                            <label class="control-label" for="inputBinWin">Windows <?php echo $sprache->bin;?></label>
                            <div class="controls"><input class="form-control" id="inputBinWin" type="text" name="gamebinaryWin" value="<?php echo $gamebinaryWin;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputBinDir"><?php echo $sprache->bin_folder;?></label>
                            <div class="controls"><input class="form-control" id="inputBinDir" type="text" name="binarydir" value="<?php echo $binarydir;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputCopyStartBinary"><?php echo $sprache->copyStartBinary;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputCopyStartBinary" name="copyStartBinary">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($copyStartBinary=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputModfolder"><?php echo $sprache->mod;?></label>
                            <div class="controls"><input class="form-control" id="inputModfolder" type="text" name="modfolder" value="<?php echo $modfolder;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputMap"><?php echo $sprache->startmap;?></label>
                            <div class="controls"><input class="form-control" id="inputMap" type="text" name="map" value="<?php echo $map;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputMapgroup"><?php echo $sprache->startmapgroup;?></label>
                            <div class="controls"><input class="form-control" id="inputMapgroup" type="text" name="mapGroup" value="<?php echo $mapGroup;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputPortMax"><?php echo $sprache->portMax;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputPortMax" name="portMax">
                                    <option>1</option>
                                    <option <?php if($portMax==2) echo 'selected="selected"'; ?>>2</option>
                                    <option <?php if($portMax==3) echo 'selected="selected"'; ?>>3</option>
                                    <option <?php if($portMax==4) echo 'selected="selected"'; ?>>4</option>
                                    <option <?php if($portMax==5) echo 'selected="selected"'; ?>>5</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputUseQueryPort"><?php echo $sprache->useQueryPort;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputUseQueryPort" name="useQueryPort">
                                    <option>1</option>
                                    <option <?php if($useQueryPort==2) echo 'selected="selected"'; ?>>2</option>
                                    <option <?php if($useQueryPort==3) echo 'selected="selected"'; ?>>3</option>
                                    <option <?php if($useQueryPort==4) echo 'selected="selected"'; ?>>4</option>
                                    <option <?php if($useQueryPort==5) echo 'selected="selected"'; ?>>5</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputPortStep"><?php echo $sprache->portStep;?></label>
                            <div class="controls"><input class="form-control" id="inputPortStep" type="text" name="portStep" value="<?php echo $portStep;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputPort1"><?php echo $sprache->port;?> 1</label>
                            <div class="controls"><input class="form-control" id="inputPort1" type="text" name="portOne" value="<?php echo $portOne;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputPort2"><?php echo $sprache->port;?> 2</label>
                            <div class="controls"><input class="form-control" id="inputPort2" type="text" name="portTwo" value="<?php echo $portTwo;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputPort3"><?php echo $sprache->port;?> 3</label>
                            <div class="controls"><input class="form-control" id="inputPort3" type="text" name="portThree" value="<?php echo $portThree;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputPort4"><?php echo $sprache->port;?> 4</label>
                            <div class="controls"><input class="form-control" id="inputPort4" type="text" name="portFour" value="<?php echo $portFour;?>"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputPort5"><?php echo $sprache->port;?> 5</label>
                            <div class="controls"><input class="form-control" id="inputPort5" type="text" name="portFive" value="<?php echo $portFive;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['cmd'])) echo ' has-error';?>">
                            <label class="control-label" for="inputCmd"><?php echo $sprache->start;?></label>
                            <div class="controls"><textarea class="form-control" id="inputCmd" rows="5" name="cmd"><?php echo $cmd;?></textarea></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputConfigs"><?php echo $sprache->configs;?></label>
                            <div class="controls"><textarea class="form-control" id="inputConfigs" rows="5" name="configs"><?php echo $configs;?></textarea></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputConfigsEdit"><?php echo $sprache->configedit;?></label>
                            <div class="controls"><textarea class="form-control" id="inputConfigsEdit" rows="5" name="configedit"><?php echo $configedit;?></textarea></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputModsCmd"><?php echo $sprache->modcmds;?></label>
                            <div class="controls"><textarea class="form-control" id="inputModsCmd" rows="5" name="modcmds"><?php echo $modcmds;?></textarea></div>
                        </div>

                        <!--<div class="form-group">
                            <label class="control-label" for="inputIptables"><?php echo $sprache->iptables;?></label>
                            <div class="controls"><textarea class="form-control" id="inputIptables" rows="5" name="iptables"><?php echo $iptables;?></textarea></div>
                        </div>-->

                        <div class="form-group">
                            <label class="control-label" for="inputProtectedSaveCFGs"><?php echo $sprache->protectedSaveCFGs;?></label>
                            <div class="controls"><textarea class="form-control" id="inputProtectedSaveCFGs" rows="5" name="protectedSaveCFGs"><?php echo $protectedSaveCFGs;?></textarea></div>
                        </div>
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
    $("#inputGameQ").chosen({
        disable_search_threshold: 3,
        inherit_select_classes: true,
        allow_single_deselect: true,
        no_results_text: '<?php echo $gsprache->chosenNoResult;?>',
        placeholder_text_single: '<?php echo $gsprache->chosenSelect;?>',
        placeholder_text_multiple: '<?php echo $gsprache->chosenSelect;?>',
        width: "100%"
    });
</script>