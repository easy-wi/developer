<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip.':'.$port;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=gs&amp;d=md&amp;id=<?php echo $server_id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <input type="hidden" name="gamestring" value="<?php echo $gamestring;?>">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <?php if(($licenceDetails['lG']>'0' and $active=="N") or ($licenceDetails['lG']>='0' and $active=="Y")) { ?><option value="Y"><?php echo $gsprache->yes;?></option><?php } ?>
                        <option value="N" <?php if ($active=='N') echo 'selected="selected"'; ?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputAutorestart">Auto Restart</label>
                <div class="controls">
                    <select id="inputAutorestart" name="autoRestart">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($autoRestart=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputLend"><?php echo $sprache->lendserver;?></label>
                <div class="controls">
                    <select id="inputLend" name="lendserver">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($lendserver=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPassFTP">FTP <?php echo $sprache->password;?></label>
                <div class="controls"><input id="inputPassFTP" type="text" name="password" value="<?php echo $password;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEAC">Easy Anti Cheat</label>
                <div class="controls">
                    <select id="inputEAC" name="eacallowed">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($eacallowed=="Y") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIP"><?php echo $sprache->ip;?></label>
                <div class="controls">
                    <select id="inputIP" name="ip" onchange="getdetails('serverallocation.php?ip=', this.value)">
                        <option value="<?php echo $serverip?>"><?php echo $serverip?></option>
                        <?php if ($ip!=$serverip) echo '<option value="'.$ip.'">'.$ip.'</option>';?>
                        <?php foreach($altips as $extraip) { ?>
                        <?php if (isip($extraip,"ip4") and $extraip!=$serverip) echo '<option value="'.$extraip.'">'.$extraip.'</option>';?>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="information"><?php echo $sprache->usedports;?></label>
                <div id="information" class="controls"><?php echo $ports; ?></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPortMain"><?php echo $sprache->port_gs;?></label>
                <div class="controls"><input id="inputPortMain" type="text" name="port" value="<?php echo $port;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSlots"><?php echo $sprache->slots;?></label>
                <div class="controls"><input id="inputSlots" type="text" name="slots" value="<?php echo $slots;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPmode"><?php echo $sprache->protect;?></label>
                <div class="controls">
                    <select id="inputPmode" name="pallowed">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($pallowed=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCoreBind">Core Bind</label>
                <div class="controls">
                    <select id="inputCoreBind" name="taskset" onchange="textdrop('theCores');">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if($taskset=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div id="theCores" class="control-group <?php if ($taskset=='N') echo 'display_none';?>">
                <label class="control-label" for="inputCores">Cores<br /><?php echo '0-'.$c.' ('.$unbound;?>)</label>
                <div class="controls">
                    <select id="inputCores" name="cores[]" multiple="multiple">
                        <?php foreach($cores as $core => $count) { ?>
                        <option <?php if (in_array($core,$usedcores)) echo 'selected="selected"'; ?> value="<?php echo $core;?>"><?php echo $core.' ('.$count.') ';?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBrand"><?php echo $sprache->brandname;?></label>
                <div class="controls">
                    <select id="inputBrand" name="brandname">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if($brandname=="Y") echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputWar"><?php echo $sprache->war;?></label>
                <div class="controls">
                    <select id="inputWar" name="war">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($war=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTV"><?php echo $sprache->tv;?></label>
                <div class="controls">
                    <select id="inputTV" name="tvenable">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($tvenable=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort2"><?php echo $sprache->port;?> 2</label>
                <div class="controls"><input id="inputPort2" type="text" name="port2" value="<?php echo $port2;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort3"><?php echo $sprache->port;?> 3</label>
                <div class="controls"><input id="inputPort3" type="text" name="port3" value="<?php echo $port3;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort4"><?php echo $sprache->port;?> 4</label>
                <div class="controls"><input id="inputPort4" type="text" name="port4" value="<?php echo $port4;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort5"><?php echo $sprache->port;?> 5</label>
                <div class="controls"><input id="inputPort5" type="text" name="port5" value="<?php echo $port5;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMinRam"><?php echo $sprache->ramMin;?></label>
                <div class="controls"><input id="inputMinRam" type="text" name="minram" value="<?php echo $minram;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxRam"><?php echo $sprache->ramMax;?></label>
                <div class="controls"><input id="inputMaxRam" type="text" name="maxram" value="<?php echo $maxram;?>"></div>
            </div>
            <?php foreach(customColumns('G',$server_id) as $row){ ?>
            <div class="control-group">
                <label class="control-label" for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                <div class="controls"><?php echo $row['input'];?></div>
            </div>
            <?php }?>
            <hr>
            <?php foreach ($table as $table_row){ ?>
            <h3><a href="javascript:void(0)" onclick="SwitchShowHideRows('<?php echo $table_row['id'];?>'); return false;"><?php echo $table_row['description'];?> <span class="btn btn-large btn-link"><i class="icon-plus"></i></span></a></h3>
            <input type="hidden" name="id_<?php echo $table_row['shorten'];?>" value="<?php echo $table_row['id'];?>">
            <input type="hidden" name="shorten_<?php echo $table_row['shorten'];?>" value="<?php echo $table_row['shorten'];?>">
            <?php if ($table_row['upload']==0) { ?><input type="hidden" name="upload_<?php echo $table_row['shorten'];?>" value="0"><?php }?>
            <div class="<?php echo $table_row['id'];?> display_none switch row-fluid">
                <div class="span6"><h5><?php echo $gsprache->settings;?></h5></div>
                <div class="span6"><h5><?php echo $sprache->useredit;?></h5></div>
            </div>
            <?php if ($table_row['upload']>0) { ?>
            <div class="<?php echo $table_row['id'];?> display_none switch row-fluid">
                <div class="span12">
                    <div class="control-group">
                        <label class="control-label" for="input-<?php echo $table_row['shorten'];?>-STV">SourceTV Demoupload</label>
                        <div class="controls">
                            <select id="input-<?php echo $table_row['shorten'];?>-STV" name="upload_<?php echo $table_row['shorten'];?>">
                                <option value="1">OFF</option>
                                <option value="2" <?php if ($table_row['upload']=="2") echo 'selected="selected"'; ?> >Cron+Manual File remove</option>
                                <option value="3" <?php if ($table_row['upload']=="3") echo 'selected="selected"'; ?> >Cron+Manual</option>
                                <option value="4" <?php if ($table_row['upload']=="4") echo 'selected="selected"'; ?> >Autoupload File remove</option>
                                <option value="5" <?php if ($table_row['upload']=="5") echo 'selected="selected"'; ?> >Autoupload</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
            <div class="<?php echo $table_row['id'];?> display_none switch row-fluid">
                <div class="span6">
                    <?php if ($table_row['upload']>0) { ?>
                    <div class="control-group">
                        <label class="control-label" for="input-<?php echo $table_row['shorten'];?>-UploadFTP">Upload FTP</label>
                        <div class="controls"><input id="input-<?php echo $table_row['shorten'];?>-UploadFTP" type="text" name="uploaddir_<?php echo $table_row['shorten'];?>" value="<?php echo $table_row['uploaddir'];?>"></div>
                    </div>
                    <?php }?>
                    <div class="control-group">
                        <label class="control-label" for="input-<?php echo $table_row['shorten'];?>-FPS"><?php echo $sprache->fps;?></label>
                        <div class="controls"><input id="input-<?php echo $table_row['shorten'];?>-FPS" type="text" name="fps_<?php echo $table_row['shorten'];?>" value="<?php echo $table_row['fps'];?>"></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="input-<?php echo $table_row['shorten'];?>-Tick"><?php echo $sprache->tick;?></label>
                        <div class="controls"><input id="input-<?php echo $table_row['shorten'];?>-Tick" type="text" name="tic_<?php echo $table_row['shorten'];?>" value="<?php echo $table_row['tic'];?>"></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="input-<?php echo $table_row['shorten'];?>-Map"><?php echo $sprache->startmap;?></label>
                        <div class="controls"><input id="input-<?php echo $table_row['shorten'];?>-Map" type="text" name="map_<?php echo $table_row['shorten'];?>" value="<?php echo $table_row['map'];?>"></div>
                    </div>
                </div>
                <div class="span6">
                    <?php if ($table_row['upload']>0) { ?>
                    <div class="control-group">
                        <label class="control-label"><input type="checkbox" name="user_uploaddir_<?php echo $table_row['shorten'];?>" value="Y" <?php if ($table_row['user_uploaddir']=="Y") echo 'checked="checked"'; ?>></label>
                    </div>
                    <?php }?>
                    <div class="control-group">
                        <label class="control-label"><input type="checkbox" name="user_fps_<?php echo $table_row['shorten'];?>" value="Y" <?php if ($table_row['userfps']=="Y") echo 'checked="checked"'; ?>></label>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><input type="checkbox" name="user_tick_<?php echo $table_row['shorten'];?>" value="Y" <?php if ($table_row['usertick']=="Y") echo 'checked="checked"'; ?>></label>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><input type="checkbox" name="user_map_<?php echo $table_row['shorten'];?>" value="Y" <?php if ($table_row['usermap']=="Y") echo 'checked="checked"'; ?>></label>
                    </div>
                </div>
            </div>
            <div class="<?php echo $table_row['id'];?> display_none switch control-group">
                <label class="control-label" for="input-<?php echo $table_row['shorten'];?>-MapGroup"><?php echo $sprache->startmapgroup;?></label>
                <div class="controls"><input id="input-<?php echo $table_row['shorten'];?>-MapGroup" type="text" name="mapGroup_<?php echo $table_row['shorten'];?>" value="<?php echo $table_row['mapGroup'];?>"></div>
            </div>
            <div class="<?php echo $table_row['id'];?> display_none switch control-group">
                <label class="control-label" for="input-<?php echo $table_row['shorten'];?>-OwnCMD"><?php echo $sprache->start_own;?></label>
                <div class="controls">
                    <select id="input-<?php echo $table_row['shorten'];?>-OwnCMD" name="owncmd_<?php echo $table_row['shorten'];?>">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($table_row['owncmd']=="N") echo 'selected="selected"'; ?> ><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="<?php echo $table_row['id'];?> display_none switch control-group">
                <label class="control-label" for="input-<?php echo $table_row['shorten'];?>-CMD"><?php echo $sprache->start;?></label>
                <div class="controls">
                    <textarea id="input-<?php echo $table_row['shorten'];?>-CMD" rows="5" name="cmd_<?php echo $table_row['shorten'];?>"><?php echo $table_row['cmd'];?></textarea>
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>