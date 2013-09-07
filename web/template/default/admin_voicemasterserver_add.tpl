<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->voiceserver." ".$gsprache->master;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form name="form" class="form-horizontal" action="admin.php?w=vm&amp;d=ad" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
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
                <label class="control-label" for="inputExternalID">externalID</label>
                <div class="controls"><input id="inputExternalID" type="text" name="externalID" value="" maxlength="255"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputManaged">Managed</label>
                <div class="controls">
                    <select id="inputManaged" name="managedServer" onchange="textdrop('reseller');">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group display_none" id="reseller">
                <label class="control-label" for="inputManagedForID"><?php echo $gsprache->reseller;?></label>
                <div class="controls">
                    <select id="inputManagedForID" name="managedForID">
                        <?php foreach ($resellerIDs as $k=>$v){ ?><option value="<?php echo $k;?>"><?php echo $v;?></option><?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputAutoRestart"><?php echo $sprache->autorestart;?></label>
                <div class="controls">
                    <select id="inputAutoRestart" name="autorestart">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultName"><?php echo $sprache->defaultname;?></label>
                <div class="controls"><input id="inputDefaultName" type="text" name="defaultname" value="My Voiceserver <?php echo $brandname;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputType"><?php echo $sprache->type;?></label>
                <div class="controls">
                    <select id="inputType" name="type">
                        <option value="ts3"><?php echo $sprache->ts3;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputNewUser"><?php echo $sprache->newuser;?></label>
                <div class="controls">
                    <select id="inputNewUser" name="newuser">
                        <option value="1"><?php echo $sprache->generate;?></option>
                        <option value="2"><?php echo $sprache->enter;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputQueryPort"><?php echo $sprache->queryport;?></label>
                <div class="controls"><input id="inputQueryPort" type="text" name="queryport" value="10011"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputQueryPassword"><?php echo $sprache->querypassword;?></label>
                <div class="controls"><input id="inputQueryPassword" type="text" name="querypassword" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFiletransferPort"><?php echo $sprache->filetransferport;?></label>
                <div class="controls"><input id="inputFiletransferPort" type="text" name="filetransferport" value="30033"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUseDns"><?php echo $sprache->usedns;?></label>
                <div class="controls">
                    <select id="inputUseDns" name="usedns">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultDns"><?php echo $sprache->defaultdns;?></label>
                <div class="controls"><input id="inputDefaultDns" type="text" name="defaultdns"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTsdnsServerID"><?php echo $sprache->tsdnsServerID;?></label>
                <div class="controls">
                    <select id="inputTsdnsServerID" name="tsdnsServerID">
                        <option value=""><?php echo $gsprache->no;?></option>
                        <?php foreach ($externalDNS as $k=>$v) { ?><option value="<?php echo $k;?>"><?php echo $v;?></option><?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputExternalDefaultDns"><?php echo $sprache->externalDefaultDNS;?></label>
                <div class="controls">
                    <select id="inputExternalDefaultDns" name="externalDefaultDNS">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxserver"><?php echo $sprache->maxserver;?></label>
                <div class="controls"><input id="inputMaxserver" type="text" name="maxserver" value="10"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxSlots"><?php echo $sprache->maxslots;?></label>
                <div class="controls"><input id="inputMaxSlots" type="text" name="maxslots" value="512"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultWelcome"><?php echo $sprache->defaultwelcome;?></label>
                <div class="controls"><input id="inputDefaultWelcome" type="text" name="defaultwelcome" value="Voiceserver <?php echo $brandname;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultBannerUrl"><?php echo $sprache->defaulthostbanner_url;?></label>
                <div class="controls"><input id="inputDefaultBannerUrl" type="url" name="defaulthostbanner_url" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultBannerGfxUrl"><?php echo $sprache->defaulthostbanner_gfx_url;?></label>
                <div class="controls"><input id="inputDefaultBannerGfxUrl" type="url" name="defaulthostbanner_gfx_url" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultButtonTooltip"><?php echo $sprache->defaulthostbutton_tooltip;?></label>
                <div class="controls"><input id="inputDefaultButtonTooltip" type="text" name="defaulthostbutton_tooltip" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultHustButtonUrl"><?php echo $sprache->defaulthostbutton_url;?></label>
                <div class="controls"><input id="inputDefaultHustButtonUrl" type="url" name="defaulthostbutton_url" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultHostButtonGfxUrl"><?php echo $sprache->defaulthostbutton_gfx_url;?></label>
                <div class="controls"><input id="inputDefaultHostButtonGfxUrl" type="url" name="defaulthostbutton_gfx_url" value=""></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultFlexSlotsFree"><?php echo $sprache->defaultFlexSlotsFree;?></label>
                <div class="controls"><input id="inputDefaultFlexSlotsFree" type="text" name="defaultFlexSlotsFree" value="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefaultFlexSlotsPercent"><?php echo $sprache->defaultFlexSlotsPercent;?></label>
                <div class="controls"><input id="inputDefaultFlexSlotsPercent" type="text" name="defaultFlexSlotsPercent" value="80"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputAddType"><?php echo $sprache->addtype;?></label>
                <div class="controls">
                    <select id="inputAddType" name="addtype" onchange="details2(this.value,'details')">
                        <!--<option value="1"><?php echo $sprache->add;?></option>-->
                        <option value="2"><?php echo $sprache->import;?></option>
                    </select>
                </div>
            </div>
            <!--<div class="control-group">
                <label class="control-label" for="1"><?php echo $sprache->rootserver;?></label>
                <div class="controls">
                    <select id="1" name="rootid">
                        <?php echo implode('\r\n',$roots);?>
                    </select>
                </div>
            </div>-->
            <div class="control-group">
                <label class="control-label" for="inputServerDir"><?php echo $sprache->serverdir;?></label>
                <div class="controls"><input id="inputServerDir" type="text" name="serverdir"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSSHIP"><?php echo $sprache->ssh_ip;?></label>
                <div class="controls"><input id="inputSSHIP" type="text" name="ip" maxlength="15"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIps"><?php echo $sprache->ips;?></label>
                <div class="controls"><textarea id="inputIps" name="ips" rows="5" cols="23" ></textarea></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshPort"><?php echo $sprache->ssh_port;?></label>
                <div class="controls"><input id="inputSshPort" type="text" name="port" value="22" maxlength="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshUser"><?php echo $sprache->ssh_user;?></label>
                <div class="controls"><input id="inputSshUser" type="text" name="user" value="easy-wi" maxlength="15"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSshPass"><?php echo $sprache->ssh_pass;?></label>
                <div class="controls"><input id="inputSshPass" type="password" name="pass"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyUse"><?php echo $sprache->keyuse;?></label>
                <div class="controls">
                    <select id="inputKeyUse" name="publickey">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N"><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputKeyName"><?php echo $sprache->keyname;?></label>
                <div class="controls"><input id="inputKeyName" type="text" name="keyname" maxlength="20" value="id_rsa"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputOsBit"><?php echo $sprache->os_bit;?></label>
                <div class="controls">
                    <select id="inputOsBit" name="bit">
                        <option value="32">32</option>
                        <option value="64">64</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>