<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->voiceserver;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <h4><?php echo $gsprache->master." ".$sprache->usage;?></h4>
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->installedslots;?></dt>
            <dd><?php echo $installedslots."/".$maxslots;?></dd>
            <dt><?php echo $sprache->installedserver;?></dt>
            <dd><?php echo $installedserver."/".$maxserver;?></dd>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <h4><?php echo $gsprache->voiceserver." ".$gsprache->add;?></h4>
        <form name="form" class="form-horizontal" action="admin.php?w=vo&amp;d=ad&amp;r=vo" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="customer" value="<?php echo $customer;?>">
            <input type="hidden" name="masterserver" value="<?php echo $masterserver;?>">
            <input type="hidden" name="customer" value="<?php echo $customer;?>">
            <input type="hidden" name="action" value="ad2">
            <div class="control-group">
                <label class="control-label" for="inputLendServer"><?php echo $gsprache->lendserver;?></label>
                <div class="controls">
                    <select id="inputLendServer" name="lendserver">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRestart">Auto Restart</label>
                <div class="controls">
                    <select id="inputRestart" name="autoRestart">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputExternalID">externalID</label>
                <div class="controls"><input id="inputExternalID" type="text" name="externalID" value="" maxlength="255"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPassword"><?php echo $sprache->password;?></label>
                <div class="controls">
                    <select id="inputPassword" name="password">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFlexSlots"><?php echo $sprache->flexSlots;?></label>
                <div class="controls">
                    <select id="inputFlexSlots" name="flexSlots">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFlexSlotsFree"><?php echo $sprache->flexSlotsFree;?></label>
                <div class="controls"><input id="inputFlexSlotsFree" type="text" name="flexSlotsFree" value="<?php echo $defaultFlexSlotsFree;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFlexSlotsPercent"><?php echo $sprache->flexSlotsPercent;?></label>
                <div class="controls"><input id="inputFlexSlotsPercent" type="text" name="flexSlotsPercent" value="<?php echo $defaultFlexSlotsPercent;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputForceWelcome"><?php echo $sprache->forcewelcome;?></label>
                <div class="controls">
                    <select id="inputForceWelcome" name="forcewelcome">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputForceBanner"><?php echo $sprache->forcebanner;?></label>
                <div class="controls">
                    <select id="inputForceBanner" name="forcebanner">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputForceButton"><?php echo $sprache->forcebutton;?></label>
                <div class="controls">
                    <select id="inputForceButton" name="forcebutton">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputForceServerTag"><?php echo $sprache->forceservertag;?></label>
                <div class="controls">
                    <select id="inputForceServerTag" name="forceservertag">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBackup"><?php echo $sprache->backup;?></label>
                <div class="controls">
                    <select id="inputBackup" name="backup">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIP"><?php echo $sprache->ip;?></label>
                <div class="controls">
                    <select id="inputIP" name="ip" onchange="getdetails('serverallocation.php?&ip=', this.value)">
                        <?php foreach($ips as $ip) { ?><?php if (isip($ip,"ip4")) echo "<option>$ip</option>";?><?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="information"><?php echo $sprache->usedports;?></label>
                <div id="information" class="controls"><?php echo implode(', ',$ports); ?></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort"><?php echo $sprache->port;?></label>
                <div class="controls"><input id="inputPort" type="text" name="port" value="<?php echo $port; ?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSlots"><?php echo $sprache->slots;?></label>
                <div class="controls"><input id="inputSlots" type="text" name="slots" value="50"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxtraffic"><?php echo $sprache->maxtraffic;?></label>
                <div class="controls"><input id="inputMaxtraffic" type="text" name="maxtraffic" value="1000" /> MB</div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxBandwidth"><?php echo $sprache->max_download_total_bandwidth;?></label>
                <div class="controls"><input id="inputMaxBandwidth" type="text" name="max_download_total_bandwidth" value="65536"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTotalBandwidth"><?php echo $sprache->max_upload_total_bandwidth;?></label>
                <div class="controls"><input id="inputTotalBandwidth" type="text" name="max_upload_total_bandwidth" value="65536"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDNS"><?php echo $sprache->dns;?></label>
                <div class="controls"><input id="inputDNS" type="text" name="dns" value="<?php echo $dns; ?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputName"><?php echo $sprache->name;?></label>
                <div class="controls"><input id="inputName" type="text" name="name" value="<?php echo $name; ?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputWelcome"><?php echo $sprache->welcome;?></label>
                <div class="controls"><input id="inputWelcome" type="text" name="welcome" value="<?php echo $welcome; ?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHostbannerUrl"><?php echo $sprache->hostbanner_url;?></label>
                <div class="controls"><input id="inputHostbannerUrl" type="text" name="hostbanner_url" value="<?php echo $hostbanner_url; ?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHostbannerGFXUrl"><?php echo $sprache->hostbanner_gfx_url;?></label>
                <div class="controls"><input id="inputHostbannerGFXUrl" type="text" name="hostbanner_gfx_url" value="<?php echo $hostbanner_gfx_url; ?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHostbuttonTooltip"><?php echo $sprache->hostbutton_tooltip;?></label>
                <div class="controls"><input id="inputHostbuttonTooltip" type="text" name="hostbutton_tooltip" value="<?php echo $hostbutton_tooltip; ?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHostButtonUrl"><?php echo $sprache->hostbutton_url;?></label>
                <div class="controls"><input id="inputHostButtonUrl" type="text" name="hostbutton_url" value="<?php echo $hostbutton_url; ?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHostButtonGFXUrl"><?php echo $sprache->hostbutton_gfx_url;?></label>
                <div class="controls"><input id="inputHostButtonGFXUrl" type="text" name="hostbutton_gfx_url" value="<?php echo $hostbutton_gfx_url; ?>"></div>
            </div>
            <?php foreach(customColumns('T') as $row){ ?>
            <div class="control-group">
                <label class="control-label" for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                <div class="controls"><?php echo $row['input'];?></div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>