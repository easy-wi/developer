<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=se&amp;r=se" onsubmit="return confirm('<?php echo $sprache->confirm_change; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label" for="inputLanguage"><?php echo $sprache->language;?></label>
                <div class="controls">
                    <select class="span10" id="inputLanguage" name="language">
                        <?php foreach ($selectlanguages as $la) { ?>
                        <option value="<?php echo $la;?>" <?php if ($la==$language_choosen) echo 'selected="selected"'; ?>><?php echo $la;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTemplate">Template</label>
                <div class="controls">
                    <select class="span10" id="inputTemplate" name="template">
                        <?php foreach ($templates as $tp) { ?>
                        <option value="<?php echo $tp;?>" <?php if ($tp==$template_choosen) echo 'selected="selected"'; ?>><?php echo $tp;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTime"><?php echo $sprache->timezone."<br />".$servertime;?></label>
                <div class="controls">
                    <select class="span10" id="inputTime" name="timezone">
                        <option <?php if($timezone=="-24") echo 'selected="selected"'; ?>>-24</option>
                        <option <?php if($timezone=="-23") echo 'selected="selected"'; ?>>-23</option>
                        <option <?php if($timezone=="-22") echo 'selected="selected"'; ?>>-22</option>
                        <option <?php if($timezone=="-21") echo 'selected="selected"'; ?>>-21</option>
                        <option <?php if($timezone=="-20") echo 'selected="selected"'; ?>>-20</option>
                        <option <?php if($timezone=="-19") echo 'selected="selected"'; ?>>-19</option>
                        <option <?php if($timezone=="-18") echo 'selected="selected"'; ?>>-18</option>
                        <option <?php if($timezone=="-17") echo 'selected="selected"'; ?>>-17</option>
                        <option <?php if($timezone=="-16") echo 'selected="selected"'; ?>>-16</option>
                        <option <?php if($timezone=="-15") echo 'selected="selected"'; ?>>-15</option>
                        <option <?php if($timezone=="-14") echo 'selected="selected"'; ?>>-14</option>
                        <option <?php if($timezone=="-13") echo 'selected="selected"'; ?>>-13</option>
                        <option <?php if($timezone=="-12") echo 'selected="selected"'; ?>>-12</option>
                        <option <?php if($timezone=="-11") echo 'selected="selected"'; ?>>-11</option>
                        <option <?php if($timezone=="-10") echo 'selected="selected"'; ?>>-10</option>
                        <option <?php if($timezone=="-9") echo 'selected="selected"'; ?>>-9</option>
                        <option <?php if($timezone=="-8") echo 'selected="selected"'; ?>>-8</option>
                        <option <?php if($timezone=="-7") echo 'selected="selected"'; ?>>-7</option>
                        <option <?php if($timezone=="-6") echo 'selected="selected"'; ?>>-6</option>
                        <option <?php if($timezone=="-5") echo 'selected="selected"'; ?>>-5</option>
                        <option <?php if($timezone=="-4") echo 'selected="selected"'; ?>>-4</option>
                        <option <?php if($timezone=="-3") echo 'selected="selected"'; ?>>-3</option>
                        <option <?php if($timezone=="-2") echo 'selected="selected"'; ?>>-2</option>
                        <option <?php if($timezone=="-1") echo 'selected="selected"'; ?>>-1</option>
                        <option <?php if($timezone=="0") echo 'selected="selected"'; ?>>+0</option>
                        <option <?php if($timezone=="+1") echo 'selected="selected"'; ?>>+1</option>
                        <option <?php if($timezone=="+2") echo 'selected="selected"'; ?>>+2</option>
                        <option <?php if($timezone=="+3") echo 'selected="selected"'; ?>>+3</option>
                        <option <?php if($timezone=="+4") echo 'selected="selected"'; ?>>+4</option>
                        <option <?php if($timezone=="+5") echo 'selected="selected"'; ?>>+5</option>
                        <option <?php if($timezone=="+6") echo 'selected="selected"'; ?>>+6</option>
                        <option <?php if($timezone=="+7") echo 'selected="selected"'; ?>>+7</option>
                        <option <?php if($timezone=="+8") echo 'selected="selected"'; ?>>+8</option>
                        <option <?php if($timezone=="+9") echo 'selected="selected"'; ?>>+9</option>
                        <option <?php if($timezone=="+10") echo 'selected="selected"'; ?>>+10</option>
                        <option <?php if($timezone=="+11") echo 'selected="selected"'; ?>>+11</option>
                        <option <?php if($timezone=="+12") echo 'selected="selected"'; ?>>+12</option>
                        <option <?php if($timezone=="+13") echo 'selected="selected"'; ?>>+13</option>
                        <option <?php if($timezone=="+14") echo 'selected="selected"'; ?>>+14</option>
                        <option <?php if($timezone=="+15") echo 'selected="selected"'; ?>>+15</option>
                        <option <?php if($timezone=="+16") echo 'selected="selected"'; ?>>+16</option>
                        <option <?php if($timezone=="+17") echo 'selected="selected"'; ?>>+17</option>
                        <option <?php if($timezone=="+18") echo 'selected="selected"'; ?>>+18</option>
                        <option <?php if($timezone=="+19") echo 'selected="selected"'; ?>>+19</option>
                        <option <?php if($timezone=="+20") echo 'selected="selected"'; ?>>+20</option>
                        <option <?php if($timezone=="+21") echo 'selected="selected"'; ?>>+21</option>
                        <option <?php if($timezone=="+22") echo 'selected="selected"'; ?>>+22</option>
                        <option <?php if($timezone=="+23") echo 'selected="selected"'; ?>>+23</option>
                        <option <?php if($timezone=="+24") echo 'selected="selected"'; ?>>+24</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPhone"><?php echo $sprache->supportnumber;?></label>
                <div class="controls">
                    <input class="span10" id="inputPhone" type="tel" name="supportnumber" value="<?php echo $supportnumber;?>">
                </div>
            </div>
            <hr>
            <h4><?php echo $gsprache->user;?></h4>
            <div class="control-group">
                <label class="control-label" for="inputLogins"><?php echo $sprache->faillogins;?></label>
                <div class="controls">
                    <input class="span10" id="inputLogins" type="number" name="faillogins" value="<?php echo $faillogins;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPrefix1"><?php echo $sprache->prefix1;?></label>
                <div class="controls">
                    <select class="span10" id="inputPrefix1" name="prefix1" onchange="textdrop('prefix');">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($prefix1=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group <?php if ($prefix1=='N') echo 'display_none';?>" id="prefix">
                <label class="control-label" for="inputPrefix2"><?php echo $sprache->prefix3;?></label>
                <div class="controls">
                    <input class="span10" id="inputPrefix2" type="text" name="prefix2" value="<?php echo $prefix2;?>">
                </div>
            </div>
            <hr>
            <h4><?php echo $gsprache->gameserver.' + '.$gsprache->voiceserver;?></h4>
            <div class="control-group">
                <label class="control-label" for="inputDown"><?php echo $sprache->down_checks;?></label>
                <div class="controls">
                    <input class="span10" id="inputDown" type="number" name="down_checks" value="<?php echo $down_checks;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBrandname"><?php echo $sprache->brandname;?></label>
                <div class="controls">
                    <input class="span10" id="inputBrandname" type="text" name="brandname" value="<?php echo $brandname;?>">
                </div>
            </div>
            <hr>
            <h4><?php echo $gsprache->gameserver;?></h4>
            <div class="control-group">
                <label class="control-label" for="inputServertag"><?php echo $sprache->noservertag;?></label>
                <div class="controls">
                    <select class="span10" id="inputServertag" name="noservertag">
                        <option value="1"><?php echo $sprache->shutdown;?></option>
                        <option value="2" <?php if ($noservertag=='2') echo 'selected="selected"';?>><?php echo $sprache->warn;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputNoPass"><?php echo $sprache->nopassword;?></label>
                <div class="controls">
                    <select class="span10" id="inputNoPass" name="nopassword">
                        <option value="1"><?php echo $sprache->shutdown;?></option>
                        <option value="2" <?php if ($nopassword=='2') echo 'selected="selected"';?>><?php echo $sprache->warn;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSlots"><?php echo $sprache->tohighslots;?></label>
                <div class="controls">
                    <select class="span10" id="inputSlots" name="tohighslots">
                        <option value="1"><?php echo $sprache->shutdown;?></option>
                        <option value="2" <?php if ($tohighslots=='2') echo 'selected="selected"';?>><?php echo $sprache->warn;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputImageServer"><?php echo $sprache->image_server2;?></label>
                <div class="controls">
                    <textarea class="span10" id="inputImageServer" name="imageserver" rows="5"><?php echo $imageserver;?></textarea>
                </div>
            </div>
            <hr>
            <h4><?php echo $gsprache->voiceserver;?></h4>
            <div class="control-group">
                <label class="control-label" for="inputBackups"><?php echo $sprache->maxbackups;?></label>
                <div class="controls">
                    <input class="span10" id="inputBackups" type="number" name="voice_maxbackup" value="<?php echo $voice_maxbackup;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputAutoBackup">Voice Autobackup</label>
                <div class="controls">
                    <select class="span10" id="inputAutoBackup" name="voice_autobackup" onchange="textdrop('voice_autobackup');">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($voice_autobackup=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group <?php if ($voice_autobackup=='N') echo 'display_none';?>" id="voice_autobackup">
                <label class="control-label" for="inputAutoBackup2"><?php echo $sprache->eachday;?></label>
                <div class="controls">
                    <input class="span10" id="inputAutoBackup2" type="number" name="voice_autobackup_intervall" value="<?php echo $voice_autobackup_intervall;?>">
                </div>
            </div>
            <hr>
            <h4>Cronjobs</h4>
            <div class="control-group">
                <label class="control-label" for="inputlastCronWarnStatus"><?php echo $sprache->lastCronWarnStatus;?></label>
                <div class="controls">
                    <select class="span10" id="inputlastCronWarnStatus" name="lastCronWarnStatus">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($lastCronWarnStatus=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputlastCronWarnReboot"><?php echo $sprache->lastCronWarnReboot;?></label>
                <div class="controls">
                    <select class="span10" id="inputlastCronWarnReboot" name="lastCronWarnReboot">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($lastCronWarnReboot=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputlastCronWarnUpdates"><?php echo $sprache->lastCronWarnUpdates;?></label>
                <div class="controls">
                    <select class="span10" id="inputlastCronWarnUpdates" name="lastCronWarnUpdates">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($lastCronWarnUpdates=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputlastCronWarnJobs"><?php echo $sprache->lastCronWarnJobs;?></label>
                <div class="controls">
                    <select class="span10" id="inputlastCronWarnJobs" name="lastCronWarnJobs">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($lastCronWarnJobs=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputlastCronWarnCloud"><?php echo $sprache->lastCronWarnCloud;?></label>
                <div class="controls">
                    <select class="span10" id="inputlastCronWarnCloud" name="lastCronWarnCloud">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($lastCronWarnCloud=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCronjobIPs">Cronjob IPs</label>
                <div class="controls">
                    <textarea class="span10" id="inputCronjobIPs" name="cronjobIPs" rows="8"><?php echo $cronjobIPs;?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="md">
                </div>
            </div>
        </form>
    </div>
</div>