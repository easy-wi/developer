<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->lendserver;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=le&amp;d=se&amp;r=le" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputEmpty"><?php echo $sprache->shutdownempty;?></label>
                <div class="controls">
                    <select id="inputEmpty" name="shutdownempty">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($shutdownempty=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEmptyTime"><?php echo $sprache->shutdownemptytime;?></label>
                <div class="controls"><input id="inputEmptyTime"  type="text" name="shutdownemptytime" maxlength="3" value="<?php echo $shutdownemptytime;?>"/></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputLendAccess"><?php echo $sprache->lendaccess;?></label>
                <div class="controls">
                    <select id="inputLendAccess" name="lendaccess"><?php if ($reseller_id=='0') { ?>
                        <option value="1">XML + Page</option>
                        <option value="2" <?php if ($lendaccess=="2") echo 'selected="selected"';?>>Page</option><?php } ?>
                        <option value="3" <?php if ($lendaccess=="3") echo 'selected="selected"';?>>XML</option>
                    </select>
                </div>
            </div>
            <hr>
            <h5><?php echo $sprache->settingsGsGeneral;?></h5>
            <div class="control-group">
                <label class="control-label" for="inputActiveGS"><?php echo $sprache->activeGS;?></label>
                <div class="controls">
                    <select id="inputActiveGS" name="activeGS">
                        <option value="B"><?php echo $sprache->all;?></option>
                        <option value="R" <?php if ($activeGS=="R") echo 'selected="selected"';?>><?php echo $sprache->registered;?></option>
                        <option value="A" <?php if ($activeGS=="A") echo 'selected="selected"';?>><?php echo $sprache->anonymous;?></option>
                        <option value="N" <?php if ($activeGS=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFTPUpload"><?php echo $sprache->ftpupload;?></label>
                <div class="controls">
                    <select id="inputFTPUpload" name="ftpupload">
                        <option value="Y"><?php echo $sprache->all;?></option>
                        <option value="R" <?php if ($ftpupload=="R") echo 'selected="selected"';?>><?php echo $sprache->registered;?></option>
                        <option value="A" <?php if ($ftpupload=="A") echo 'selected="selected"';?>><?php echo $sprache->anonymous;?></option>
                        <option value="N" <?php if ($ftpupload=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFTPPath"><?php echo $sprache->ftpuploadpath;?></label>
                <div class="controls"><input id="inputFTPPath"  type="text" name="ftpuploadpath" value="<?php echo $ftpuploadpath;?>"/></div>
            </div>
            <hr>
            <h5><?php echo $sprache->settingsGsAnonymous;?></h5>
            <div class="control-group">
                <label class="control-label" for="inputMintime"><?php echo $sprache->mintime;?></label>
                <div class="controls"><input id="inputMintime"  type="text" name="mintime" value="<?php echo $mintime;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxTime"><?php echo $sprache->maxtime;?></label>
                <div class="controls"><input id="inputMaxTime"  type="text" name="maxtime" value="<?php echo $maxtime;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTimeSteps"><?php echo $sprache->timesteps;?></label>
                <div class="controls"><input id="inputTimeSteps"  type="text" name="timesteps" value="<?php echo $timesteps;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMinplayer"><?php echo $sprache->minplayer;?></label>
                <div class="controls"><input id="inputMinplayer"  type="text" name="minplayer" value="<?php echo $minplayer;?>" maxlength="3"/></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxPlayer"><?php echo $sprache->maxplayer;?></label>
                <div class="controls"><input id="inputMaxPlayer"  type="text" name="maxplayer" value="<?php echo $maxplayer;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPlayerSteps"><?php echo $sprache->playersteps;?></label>
                <div class="controls"><input id="inputPlayerSteps"  type="text" name="playersteps" value="<?php echo $playersteps;?>" maxlength="3" required></div>
            </div>
            <hr>
            <h5><?php echo $sprache->settingsGsRegistered;?></h5>
            <div class="control-group">
                <label class="control-label" for="inputMintimeRegistered"><?php echo $sprache->mintime;?></label>
                <div class="controls"><input id="inputMintimeRegistered"  type="text" name="mintimeRegistered" value="<?php echo $mintimeRegistered;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxTimeRegistered"><?php echo $sprache->maxtime;?></label>
                <div class="controls"><input id="inputMaxTimeRegistered"  type="text" name="maxtimeRegistered" value="<?php echo $maxtimeRegistered;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTimeStepsRegistered"><?php echo $sprache->timesteps;?></label>
                <div class="controls"><input id="inputTimeStepsRegistered"  type="text" name="timestepsRegistered" value="<?php echo $timestepsRegistered;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMinplayerRegistered"><?php echo $sprache->minplayer;?></label>
                <div class="controls"><input id="inputMinplayerRegistered"  type="text" name="minplayerRegistered" value="<?php echo $minplayerRegistered;?>" maxlength="3"/></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxPlayerRegistered"><?php echo $sprache->maxplayer;?></label>
                <div class="controls"><input id="inputMaxPlayerRegistered"  type="text" name="maxplayerRegistered" value="<?php echo $maxplayerRegistered;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPlayerStepsRegistered"><?php echo $sprache->playersteps;?></label>
                <div class="controls"><input id="inputPlayerStepsRegistered"  type="text" name="playerstepsRegistered" value="<?php echo $playerstepsRegistered;?>" maxlength="3" required></div>
            </div>
            <hr>
            <h5><?php echo $sprache->settingsVsGeneral;?></h5>
            <div class="control-group">
                <label class="control-label" for="inputActiveVS"><?php echo $sprache->activeVS;?></label>
                <div class="controls">
                    <select id="inputActiveVS" name="activeVS">
                        <option value="B"><?php echo $sprache->all;?></option>
                        <option value="R" <?php if ($activeVS=="R") echo 'selected="selected"';?>><?php echo $sprache->registered;?></option>
                        <option value="A" <?php if ($activeVS=="A") echo 'selected="selected"';?>><?php echo $sprache->anonymous;?></option>
                        <option value="N" <?php if ($activeVS=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <hr>
            <h5><?php echo $sprache->settingsVsAnonymous;?></h5>
            <div class="control-group">
                <label class="control-label" for="inputMinTimeVoice"><?php echo $sprache->mintime;?></label>
                <div class="controls"><input id="inputMinTimeVoice"  type="text" name="vomintime" value="<?php echo $vomintime;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxTimeVoice"><?php echo $sprache->maxtime;?></label>
                <div class="controls"><input id="inputMaxTimeVoice"  type="text" name="vomaxtime" value="<?php echo $vomaxtime;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTimeStepsVoice"><?php echo $sprache->timesteps;?></label>
                <div class="controls"><input id="inputTimeStepsVoice"  type="text" name="votimesteps" value="<?php echo $votimesteps;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMinPlayerVoice"><?php echo $sprache->minplayer;?></label>
                <div class="controls"><input id="inputMinPlayerVoice"  type="text" name="vominplayer" value="<?php echo $vominplayer;?>" maxlength="3"/></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxPlayerVoice"><?php echo $sprache->maxplayer;?></label>
                <div class="controls"><input id="inputMaxPlayerVoice"  type="text" name="vomaxplayer" value="<?php echo $vomaxplayer;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPlayerStepsVoice"><?php echo $sprache->playersteps;?></label>
                <div class="controls"><input id="inputPlayerStepsVoice"  type="text" name="voplayersteps" value="<?php echo $voplayersteps;?>" maxlength="3" required></div>
            </div>
            <hr>
            <h5><?php echo $sprache->settingsVsRegistered;?></h5>
            <div class="control-group">
                <label class="control-label" for="inputMinTimeVoiceRegistered"><?php echo $sprache->mintime;?></label>
                <div class="controls"><input id="inputMinTimeVoiceRegistered"  type="text" name="vomintimeRegistered" value="<?php echo $vomintimeRegistered;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxTimeVoiceRegistered"><?php echo $sprache->maxtime;?></label>
                <div class="controls"><input id="inputMaxTimeVoiceRegistered"  type="text" name="vomaxtimeRegistered" value="<?php echo $vomaxtimeRegistered;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTimeStepsVoiceRegistered"><?php echo $sprache->timesteps;?></label>
                <div class="controls"><input id="inputTimeStepsVoiceRegistered"  type="text" name="votimestepsRegistered" value="<?php echo $votimestepsRegistered;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMinPlayerVoiceRegistered"><?php echo $sprache->minplayer;?></label>
                <div class="controls"><input id="inputMinPlayerVoiceRegistered"  type="text" name="vominplayerRegistered" value="<?php echo $vominplayerRegistered;?>" maxlength="3"/></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxPlayerVoiceRegistered"><?php echo $sprache->maxplayer;?></label>
                <div class="controls"><input id="inputMaxPlayerVoiceRegistered"  type="text" name="vomaxplayerRegistered" value="<?php echo $vomaxplayerRegistered;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPlayerStepsVoiceRegistered"><?php echo $sprache->playersteps;?></label>
                <div class="controls"><input id="inputPlayerStepsVoiceRegistered"  type="text" name="voplayerstepsRegistered" value="<?php echo $voplayerstepsRegistered;?>" maxlength="3" required></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls"><button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button></div>
            </div>
        </form>
    </div>
</div>