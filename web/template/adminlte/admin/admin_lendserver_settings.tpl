<section class="content-header">
    <h1><?php echo $gsprache->lendserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->lendserver;?></a></li>
        <li class="active"><?php echo $gsprache->settings;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

<div class="box box-info">	
    <div class="box-body">
        <form role="form" action="admin.php?w=le&amp;d=se&amp;r=le" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="form-group">
                <label for="inputEmpty"><?php echo $sprache->shutdownempty;?></label>
                    <select class="form-control" id="inputEmpty" name="shutdownempty">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($shutdownempty=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
            </div>
            <div class="form-group">
                <label for="inputEmptyTime"><?php echo $sprache->shutdownemptytime;?></label>
                <input class="form-control" id="inputEmptyTime"  type="text" name="shutdownemptytime" maxlength="3" value="<?php echo $shutdownemptytime;?>"/>
            </div>
            <div class="form-group">
                <label for="inputLendAccess"><?php echo $sprache->lendaccess;?></label>
                    <select class="form-control" id="inputLendAccess" name="lendaccess"><?php if ($reseller_id=='0') { ?>
                        <option value="1">XML + Page</option>
                        <option value="2" <?php if ($lendaccess=="2") echo 'selected="selected"';?>>Page</option><?php } ?>
                        <option value="3" <?php if ($lendaccess=="3") echo 'selected="selected"';?>>XML</option>
                    </select>
            </div>
            <hr>
            <h4><?php echo $sprache->settingsGsGeneral;?></h4>
            <div class="form-group">
                <label for="inputActiveGS"><?php echo $sprache->activeGS;?></label>
                    <select class="form-control" id="inputActiveGS" name="activeGS">
                        <option value="B"><?php echo $sprache->all;?></option>
                        <option value="R" <?php if ($activeGS=="R") echo 'selected="selected"';?>><?php echo $sprache->registered;?></option>
                        <option value="A" <?php if ($activeGS=="A") echo 'selected="selected"';?>><?php echo $sprache->anonymous;?></option>
                        <option value="N" <?php if ($activeGS=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
            </div>
            <div class="form-group">
                <label for="inputFTPUpload"><?php echo $sprache->ftpupload;?></label>
                    <select class="form-control" id="inputFTPUpload" name="ftpupload">
                        <option value="Y"><?php echo $sprache->all;?></option>
                        <option value="R" <?php if ($ftpupload=="R") echo 'selected="selected"';?>><?php echo $sprache->registered;?></option>
                        <option value="A" <?php if ($ftpupload=="A") echo 'selected="selected"';?>><?php echo $sprache->anonymous;?></option>
                        <option value="N" <?php if ($ftpupload=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
            </div>
            <div class="form-group">
                <label for="inputFTPPath"><?php echo $sprache->ftpuploadpath;?></label>
                <input class="form-control" id="inputFTPPath"  type="text" name="ftpuploadpath" value="<?php echo $ftpuploadpath;?>"/>
            </div>
            <hr>
            <h4><?php echo $sprache->settingsGsAnonymous;?></h4>
            <div class="form-group">
                <label for="inputMintime"><?php echo $sprache->mintime;?></label>
                <input class="form-control" id="inputMintime"  type="text" name="mintime" value="<?php echo $mintime;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputMaxTime"><?php echo $sprache->maxtime;?></label>
                <input class="form-control" id="inputMaxTime"  type="text" name="maxtime" value="<?php echo $maxtime;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputTimeSteps"><?php echo $sprache->timesteps;?></label>
                <input class="form-control" id="inputTimeSteps"  type="text" name="timesteps" value="<?php echo $timesteps;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputMinplayer"><?php echo $sprache->minplayer;?></label>
                <input class="form-control" id="inputMinplayer"  type="text" name="minplayer" value="<?php echo $minplayer;?>" maxlength="3"/>
            </div>
            <div class="form-group">
                <label for="inputMaxPlayer"><?php echo $sprache->maxplayer;?></label>
                <input class="form-control" id="inputMaxPlayer"  type="text" name="maxplayer" value="<?php echo $maxplayer;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputPlayerSteps"><?php echo $sprache->playersteps;?></label>
                <input class="form-control" id="inputPlayerSteps"  type="text" name="playersteps" value="<?php echo $playersteps;?>" maxlength="3" required>
            </div>
            <hr>
            <h4><?php echo $sprache->settingsGsRegistered;?></h4>
            <div class="form-group">
                <label for="inputMintimeRegistered"><?php echo $sprache->mintime;?></label>
                <input class="form-control" id="inputMintimeRegistered"  type="text" name="mintimeRegistered" value="<?php echo $mintimeRegistered;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputMaxTimeRegistered"><?php echo $sprache->maxtime;?></label>
                <input class="form-control" id="inputMaxTimeRegistered"  type="text" name="maxtimeRegistered" value="<?php echo $maxtimeRegistered;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputTimeStepsRegistered"><?php echo $sprache->timesteps;?></label>
                <input class="form-control" id="inputTimeStepsRegistered"  type="text" name="timestepsRegistered" value="<?php echo $timestepsRegistered;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputMinplayerRegistered"><?php echo $sprache->minplayer;?></label>
                <input class="form-control" id="inputMinplayerRegistered"  type="text" name="minplayerRegistered" value="<?php echo $minplayerRegistered;?>" maxlength="3"/>
            </div>
            <div class="form-group">
                <label for="inputMaxPlayerRegistered"><?php echo $sprache->maxplayer;?></label>
                <input class="form-control" id="inputMaxPlayerRegistered"  type="text" name="maxplayerRegistered" value="<?php echo $maxplayerRegistered;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputPlayerStepsRegistered"><?php echo $sprache->playersteps;?></label>
                <input class="form-control" id="inputPlayerStepsRegistered"  type="text" name="playerstepsRegistered" value="<?php echo $playerstepsRegistered;?>" maxlength="3" required>
            </div>
            <hr>
            <h4><?php echo $sprache->settingsVsGeneral;?></h4>
            <div class="form-group">
                <label for="inputActiveVS"><?php echo $sprache->activeVS;?></label>
                    <select class="form-control" id="inputActiveVS" name="activeVS">
                        <option value="B"><?php echo $sprache->all;?></option>
                        <option value="R" <?php if ($activeVS=="R") echo 'selected="selected"';?>><?php echo $sprache->registered;?></option>
                        <option value="A" <?php if ($activeVS=="A") echo 'selected="selected"';?>><?php echo $sprache->anonymous;?></option>
                        <option value="N" <?php if ($activeVS=="N") echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
            </div>
            <hr>
            <h4><?php echo $sprache->settingsVsAnonymous;?></h4>
            <div class="form-group">
                <label for="inputMinTimeVoice"><?php echo $sprache->mintime;?></label>
                <input class="form-control" id="inputMinTimeVoice"  type="text" name="vomintime" value="<?php echo $vomintime;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputMaxTimeVoice"><?php echo $sprache->maxtime;?></label>
                <input class="form-control" id="inputMaxTimeVoice"  type="text" name="vomaxtime" value="<?php echo $vomaxtime;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputTimeStepsVoice"><?php echo $sprache->timesteps;?></label>
                <input class="form-control" id="inputTimeStepsVoice"  type="text" name="votimesteps" value="<?php echo $votimesteps;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputMinPlayerVoice"><?php echo $sprache->minplayer;?></label>
                <input class="form-control" id="inputMinPlayerVoice"  type="text" name="vominplayer" value="<?php echo $vominplayer;?>" maxlength="3"/>
            </div>
            <div class="form-group">
                <label for="inputMaxPlayerVoice"><?php echo $sprache->maxplayer;?></label>
                <input class="form-control" id="inputMaxPlayerVoice"  type="text" name="vomaxplayer" value="<?php echo $vomaxplayer;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputPlayerStepsVoice"><?php echo $sprache->playersteps;?></label>
                <input class="form-control" id="inputPlayerStepsVoice"  type="text" name="voplayersteps" value="<?php echo $voplayersteps;?>" maxlength="3" required>
            </div>
            <hr>
            <h4><?php echo $sprache->settingsVsRegistered;?></h4>
            <div class="form-group">
                <label for="inputMinTimeVoiceRegistered"><?php echo $sprache->mintime;?></label>
                <input class="form-control" id="inputMinTimeVoiceRegistered"  type="text" name="vomintimeRegistered" value="<?php echo $vomintimeRegistered;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputMaxTimeVoiceRegistered"><?php echo $sprache->maxtime;?></label>
                <input class="form-control" id="inputMaxTimeVoiceRegistered"  type="text" name="vomaxtimeRegistered" value="<?php echo $vomaxtimeRegistered;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputTimeStepsVoiceRegistered"><?php echo $sprache->timesteps;?></label>
                <input class="form-control" id="inputTimeStepsVoiceRegistered"  type="text" name="votimestepsRegistered" value="<?php echo $votimestepsRegistered;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputMinPlayerVoiceRegistered"><?php echo $sprache->minplayer;?></label>
                <input class="form-control" id="inputMinPlayerVoiceRegistered"  type="text" name="vominplayerRegistered" value="<?php echo $vominplayerRegistered;?>" maxlength="3"/>
            </div>
            <div class="form-group">
                <label for="inputMaxPlayerVoiceRegistered"><?php echo $sprache->maxplayer;?></label>
                <input class="form-control" id="inputMaxPlayerVoiceRegistered"  type="text" name="vomaxplayerRegistered" value="<?php echo $vomaxplayerRegistered;?>" maxlength="3" required>
            </div>
            <div class="form-group">
                <label for="inputPlayerStepsVoiceRegistered"><?php echo $sprache->playersteps;?></label>
                <input class="form-control" id="inputPlayerStepsVoiceRegistered"  type="text" name="voplayerstepsRegistered" value="<?php echo $voplayerstepsRegistered;?>" maxlength="3" required>
            </div>
    </div>
</div>
                <label for="inputEdit"></label>
                <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
        </form>
</section>