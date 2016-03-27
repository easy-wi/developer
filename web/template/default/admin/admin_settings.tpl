<section class="content-header">
    <h1><?php echo $gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></li>
    </ol>
</section>
<form role="form" action="admin.php?w=se&amp;r=se" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
<input type="hidden" name="token" value="<?php echo token();?>">
<input type="hidden" name="action" value="md">
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
   	                <h3 class="box-title"><?php echo $gsprache->generalsettings;?></h3>
                    <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputLanguage"><?php echo $sprache->language;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputLanguage" name="language">
                                    <?php foreach ($selectlanguages as $la) { ?>
                                    <option value="<?php echo $la;?>" <?php if ($la==$language_choosen) echo 'selected="selected"'; ?>><?php echo $la;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputTemplate">Template</label>
                            <div class="controls">
                                <select class="form-control" id="inputTemplate" name="template" onchange="SwitchShowHideRows(this.value);">
                                    <?php foreach ($templates as $tp => $templateDetails) { ?>
                                    <option value="<?php echo $tp;?>" <?php if ($tp==$template_choosen) echo 'selected="selected"'; ?>><?php echo $tp;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <?php foreach ($templates as $tp => $templateDetails) { if (is_array($templateDetails)) {  ?>
                        <div class="<?php echo $tp;?> form-group <?php echo (($tp!=$template_choosen)) ? 'display_none' : ''; ?> switch">
                            <label for="inputTemplateSkin-<?php echo $tp;?>">Skin <?php echo $tp;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputTemplateSkin-<?php echo $tp;?>" name="templateColor[<?php echo $tp;?>]">
                                    <?php foreach ($templateDetails['colors'] as $color) { ?>
                                    <option <?php if ($color==$templateColor) echo 'selected="selected"'; ?>><?php echo $color;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php }} ?>

                        <div class="form-group">
                            <label for="inputTime"><?php echo $sprache->timezone;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputTime" name="timezone">
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

                        <div class="form-group">
                            <label for="inputPhone"><?php echo $sprache->supportnumber;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputPhone" type="tel" name="supportnumber" value="<?php echo $supportnumber;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputFavicon">Favicon</label>
                            <div class="controls">
                                <input class="form-control" id="inputFavicon" type="text" name="favicon" value="<?php echo $favicon;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputHeaderIcon">Header Icon</label>
                            <div class="controls">
                                <input class="form-control" id="inputHeaderIcon" type="text" name="headerIcon" value="<?php echo $headerIcon;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputHeaderText">Header Text</label>
                            <div class="controls">
                                <input class="form-control" id="inputHeaderText" type="text" name="headerText" value="<?php echo $headerText;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputHeaderHref">Header href</label>
                            <div class="controls">
                                <input class="form-control" id="inputHeaderHref" type="text" name="headerHref" value="<?php echo $headerHref;?>">
                            </div>
                        </div>
                    </div>
            </div>
            <div class="box box-primary">
                    <div class="box-header with-border">
                    <h3 class="box-title">Cronjobs</h3>
                    <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputlastCronWarnStatus"><?php echo $sprache->lastCronWarnStatus;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputlastCronWarnStatus" name="lastCronWarnStatus">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($lastCronWarnStatus=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputlastCronWarnReboot"><?php echo $sprache->lastCronWarnReboot;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputlastCronWarnReboot" name="lastCronWarnReboot">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($lastCronWarnReboot=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputlastCronWarnUpdates"><?php echo $sprache->lastCronWarnUpdates;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputlastCronWarnUpdates" name="lastCronWarnUpdates">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($lastCronWarnUpdates=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputlastCronWarnJobs"><?php echo $sprache->lastCronWarnJobs;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputlastCronWarnJobs" name="lastCronWarnJobs">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($lastCronWarnJobs=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputlastCronWarnCloud"><?php echo $sprache->lastCronWarnCloud;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputlastCronWarnCloud" name="lastCronWarnCloud">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($lastCronWarnCloud=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputCronjobIPs">Cronjob IPs</label>
                            <div class="controls">
                                <textarea class="form-control" id="inputCronjobIPs" name="cronjobIPs" rows="8"><?php echo $cronjobIPs;?></textarea>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                    <div class="box-header with-border">
                    <h3 class="box-title"><?php echo $gsprache->user;?></h3>
                    <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputLogins"><?php echo $sprache->faillogins;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputLogins" type="number" name="faillogins" value="<?php echo $faillogins;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPrefix1"><?php echo $sprache->prefix1;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputPrefix1" name="prefix1" onchange="textdrop('prefix');">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($prefix1=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group <?php if ($prefix1=='N') echo 'display_none';?>" id="prefix">
                            <label for="inputPrefix2"><?php echo $sprache->prefix3;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputPrefix2" type="text" name="prefix2" value="<?php echo $prefix2;?>">
                            </div>
                        </div>
                    </div>
            </div>       
            <div class="box box-primary">
                    <div class="box-header with-border">
                    <h3 class="box-title"><?php echo $gsprache->gameserver.' + '.$gsprache->voiceserver;?></h3>
                    <div class="box-tools pull-right">
                       <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputDown"><?php echo $sprache->down_checks;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputDown" type="number" name="down_checks" value="<?php echo $down_checks;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputBrandname"><?php echo $sprache->brandname;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputBrandname" type="text" name="brandname" value="<?php echo $brandname;?>">
                            </div>
                        </div>

                        <hr>

                        <h4><?php echo $gsprache->gameserver;?></h4>

                        <div class="form-group">
                            <label for="inputServertag"><?php echo $sprache->noservertag;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputServertag" name="noservertag">
                                    <option value="1"><?php echo $sprache->shutdown;?></option>
                                    <option value="2" <?php if ($noservertag=='2') echo 'selected="selected"';?>><?php echo $sprache->warn;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputNoPass"><?php echo $sprache->nopassword;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputNoPass" name="nopassword">
                                    <option value="1"><?php echo $sprache->shutdown;?></option>
                                    <option value="2" <?php if ($nopassword=='2') echo 'selected="selected"';?>><?php echo $sprache->warn;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputSlots"><?php echo $sprache->tohighslots;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputSlots" name="tohighslots">
                                    <option value="1"><?php echo $sprache->shutdown;?></option>
                                    <option value="2" <?php if ($tohighslots=='2') echo 'selected="selected"';?>><?php echo $sprache->warn;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputImageServer"><?php echo $sprache->image_server2;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputImageServer" name="imageserver" rows="5"><?php echo $imageserver;?></textarea>
                            </div>
                        </div>

                        <hr>

                        <h4><?php echo $gsprache->voiceserver;?></h4>

                        <div class="form-group">
                            <label for="inputBackups"><?php echo $sprache->maxbackups;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputBackups" type="number" name="voice_maxbackup" value="<?php echo $voice_maxbackup;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputAutoBackup">Voice Autobackup</label>
                            <div class="controls">
                                <select class="form-control" id="inputAutoBackup" name="voice_autobackup" onchange="textdrop('voice_autobackup');">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($voice_autobackup=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group <?php if ($voice_autobackup=='N') echo 'display_none';?>" id="voice_autobackup">
                            <label for="inputAutoBackup2"><?php echo $sprache->eachday;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputAutoBackup2" type="number" name="voice_autobackup_intervall" value="<?php echo $voice_autobackup_intervall;?>">
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <div class="row">
     <div class="col-md-12">
      <div class="box box-primary">
       <div class="box-footer">
        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
       </div>
      </div>
     </div>
    </div>
</section>
</form>