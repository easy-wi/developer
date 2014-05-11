<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=ug"><?php echo $gsprache->groups;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $name;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=ug&amp;d=md&amp;id=<?php echo $id;?>&amp;r=ug" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
        <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if ($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDefault">Default</label>
                <div class="controls">
                    <select id="inputDefault" name="defaultgroup">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($defaultgroup=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputType"><?php echo $sprache->accounttype;?></label>
                <div class="controls">
                    <select id="inputType" name="grouptype" onchange="SwitchShowHideRows(this.value)">
                        <option value="u"><?php echo $sprache->accounttype_user;?></option>
                        <?php if($reseller_id=="0" and $pa['user']) { ?><option value="a" <?php if ($grouptype=='a') echo 'selected="selected"'; ?>><?php echo $sprache->accounttype_admin;?></option><?php }?>
                        <?php if($reseller_id=="0" or $admin_id==$reseller_id) { ?><option value="r" <?php if ($grouptype=='r') echo 'selected="selected"'; ?>><?php echo $sprache->accounttype_reseller;?></option><?php }?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputType"><?php echo $sprache->groupname;?></label>
                <div class="controls">
                    <input id="inputType" type="text" name="groupname" value="<?php echo $name;?>" pattern="[0-9A-Za-z ]{2,255}" required>
                </div>
            </div>
            <?php if($pa['root']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputRoot"><?php echo $sprache->root;?></label>
                <div class="controls">
                    <input id="inputRoot" type="checkbox" name="root" value="Y" <?php if ($root=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['log']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputLogs"><?php echo $gsprache->logs;?></label>
                <div class="controls">
                    <input id="inputLogs" type="checkbox" name="log" value="Y" <?php if ($log=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['settings']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputSettings"><?php echo $sprache->settings;?></label>
                <div class="controls">
                    <input id="inputSettings" type="checkbox" name="settings" value="Y" <?php if ($settings=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['ipBans']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputIpBans">IP Bans</label>
                <div class="controls">
                    <input id="inputIpBans" type="checkbox" name="ipBans" value="Y" <?php if ($ipBans=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['updateEW']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUpdateEW">Easy-Wi Update</label>
                <div class="controls">
                    <input id="inputUpdateEW" type="checkbox" name="updateEW" value="Y" <?php if ($updateEW=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['apiSettings']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputApiSettings"><?php echo $gsprache->api;?></label>
                <div class="controls">
                    <input id="inputApiSettings" type="checkbox" name="apiSettings" value="Y" <?php if ($apiSettings=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['jobs']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputJobs"><?php echo $gsprache->jobs;?></label>
                <div class="controls">
                    <input id="inputJobs" type="checkbox" name="jobs" value="Y" <?php if ($jobs=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['user']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUser"><?php echo $sprache->user_admins;?></label>
                <div class="controls">
                    <input id="inputUser" type="checkbox" name="user" value="Y" <?php if ($user=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['user_users']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUserUsers"><?php echo $sprache->user_users;?></label>
                <div class="controls">
                    <input id="inputUserUsers" type="checkbox" name="user_users" value="Y" <?php if ($user_users=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['userGroups']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUserGroups"><?php echo $gsprache->groups;?></label>
                <div class="controls">
                    <input id="inputUserGroups" type="checkbox" name="userGroups" value="Y" <?php if ($userGroups=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['userPassword']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUserPassword"><?php echo $sprache->passw;?></label>
                <div class="controls">
                    <input id="inputUserPassword" type="checkbox" name="userPassword" value="Y" <?php if ($userPassword=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['mysql']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputMYSQL">MySQL <?php echo $gsprache->databases;?></label>
                <div class="controls">
                    <input id="inputMYSQL" type="checkbox" name="mysql" value="Y" <?php if ($mysql=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['mysql_settings']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputMYSQLServer">MySQL Server</label>
                <div class="controls">
                    <input id="inputMYSQLServer" type="checkbox" name="mysql_settings" value="Y" <?php if ($mysql_settings=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['webmaster']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputAWebMaster"><?php echo $sprache->webspace.' '.$sprache->master;?></label>
                <div class="controls"><input id="inputAWebMaster" type="checkbox" name="webmaster" value="Y" <?php if ($webmaster=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['webvhost']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputAWebVhost"><?php echo $sprache->webspace;?></label>
                <div class="controls"><input id="inputAWebVhost" type="checkbox" name="webvhost" value="Y" <?php if ($webvhost=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['tickets']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputTickets"><?php echo $sprache->tickets;?></label>
                <div class="controls">
                    <input id="inputTickets" type="checkbox" name="tickets" value="Y" <?php if ($tickets=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['cms_news']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputNews"><?php echo $gsprache->news;?></label>
                <div class="controls">
                    <input id="inputNews" type="checkbox" name="cms_news" value="Y" <?php if ($cms_news=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['cms_pages']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputPages"><?php echo $gsprache->pages;?></label>
                <div class="controls">
                    <input id="inputPages" type="checkbox" name="cms_pages" value="Y" <?php if ($cms_pages=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['cms_settings']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputCMSSettings">CMS <?php echo $gsprache->settings;?></label>
                <div class="controls">
                    <input id="inputCMSSettings" type="checkbox" name="cms_settings" value="Y" <?php if ($cms_settings=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['eac']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputEAC">Easy Anti Cheat</label>
                <div class="controls">
                    <input id="inputEAC" type="checkbox" name="eac" value="Y" <?php if ($eac=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['gserver']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputGserver"><?php echo $sprache->gserver;?></label>
                <div class="controls">
                    <input id="inputGserver" type="checkbox" name="gserver" value="Y" <?php if ($gserver=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['gimages']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputGserverImages"><?php echo $gsprache->gameserver." ".$gsprache->template;?></label>
                <div class="controls">
                    <input id="inputGserverImages" type="checkbox" name="gimages" value="Y" <?php if ($gimages=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['addons']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputGserverAddons"><?php echo $gsprache->gameserver." ".$gsprache->addon;?></label>
                <div class="controls">
                    <input id="inputGserverAddons" type="checkbox" name="addons" value="Y" <?php if ($addons=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['masterServer']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputMaster"><?php echo $gsprache->master;?></label>
                <div class="controls">
                    <input id="inputMaster" type="checkbox" name="masterServer" value="Y" <?php if ($masterServer=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['voiceserver'] and $easywiModules['vo']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVoiceserver"><?php echo $gsprache->voiceserver;?></label>
                <div class="controls">
                    <input id="inputVoiceserver" type="checkbox" name="voiceserver" value="Y" <?php if ($voiceserver=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['voicemasterserver'] and $easywiModules['vo']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVoiceserverMaster"><?php echo $gsprache->voiceserver." ".$gsprache->master;?></label>
                <div class="controls">
                    <input id="inputVoiceserverMaster" type="checkbox" name="voicemasterserver" value="Y" <?php if ($voicemasterserver=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['voiceserverSettings'] and $easywiModules['vo']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVoiceserverStats"><?php echo $gsprache->voiceserver." ".$gsprache->stats;?></label>
                <div class="controls">
                    <input id="inputVoiceserverStats" type="checkbox" name="voiceserverStats" value="Y" <?php if ($voiceserverStats=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['voiceserverStats'] and $easywiModules['vo']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVoiceserverStatsSettings"><?php echo $gsprache->voiceserver." ".$gsprache->stats.' '.$gsprache->settings;?></label>
                <div class="controls">
                    <input id="inputVoiceserverStatsSettings" type="checkbox" name="voiceserverSettings" value="Y" <?php if ($voiceserverSettings=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['lendserver']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputLendserver"><?php echo $gsprache->lendserver;?></label>
                <div class="controls">
                    <input id="inputLendserver" type="checkbox" name="lendserver" value="Y" <?php if ($lendserver=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['lendserverSettings']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputLendserverSettings"><?php echo $gsprache->lendserver.' '.$gsprache->settings;?></label>
                <div class="controls">
                    <input id="inputLendserverSettings" type="checkbox" name="lendserverSettings" value="Y" <?php if ($lendserverSettings=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['roots']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputRoots"><?php echo $gsprache->root;?></label>
                <div class="controls">
                    <input id="inputRoots" type="checkbox" name="roots" value="Y" <?php if ($roots=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['addvserver']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVirtualAdd"><?php echo $gsprache->virtual." ".$gsprache->add;?></label>
                <div class="controls">
                    <input id="inputVirtualAdd" type="checkbox" name="addvserver" value="Y" <?php if ($addvserver=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['modvserver']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVirtualMod"><?php echo $gsprache->virtual." ".$gsprache->mod;?></label>
                <div class="controls">
                    <input id="inputVirtualMod" type="checkbox" name="modvserver" value="Y" <?php if ($modvserver=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['delvserver']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVirtualDel"><?php echo $gsprache->virtual." ".$gsprache->del;?></label>
                <div class="controls">
                    <input id="inputVirtualDel" type="checkbox" name="delvserver" value="Y" <?php if ($delvserver=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['usevserver'] and $easywiModules['ro']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVirtualUse"><?php echo $gsprache->virtual." ".$rsprache->reinstall."/".$rsprache->rescue."/".$sprache->restart;?></label>
                <div class="controls">
                    <input id="inputVirtualUse" type="checkbox" name="usevserver" value="Y" <?php if ($usevserver=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['vserverhost'] and $easywiModules['ro']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVirtualHost"><?php echo $gsprache->reseller." ".$gsprache->hostsystem;?></label>
                <div class="controls">
                    <input id="inputVirtualHost" type="checkbox" name="vserverhost" value="Y" <?php if ($vserverhost=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['resellertemplates'] and $easywiModules['ro']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVirtualTemplate"><?php echo $gsprache->reseller." ".$gsprache->template;?></label>
                <div class="controls">
                    <input id="inputVirtualTemplate" type="checkbox" name="resellertemplates" value="Y" <?php if ($resellertemplates=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['dhcpServer'] and $easywiModules['ro']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputDHCP">DHCP <?php echo $gsprache->settings;?></label>
                <div class="controls">
                    <input id="inputDHCP" type="checkbox" name="dhcpServer" value="Y" <?php if ($dhcpServer=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['pxeServer'] and $easywiModules['ro']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputPXE">PXE <?php echo $gsprache->settings;?></label>
                <div class="controls">
                    <input id="inputPXE" type="checkbox" name="pxeServer" value="Y" <?php if ($pxeServer=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['vserversettings'] and $easywiModules['ro']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVserverSettings"><?php echo $gsprache->reseller." ".$gsprache->settings;?></label>
                <div class="controls">
                    <input id="inputVserverSettings" type="checkbox" name="vserversettings" value="Y" <?php if ($vserversettings=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php }; if($pa['dedicatedServer'] and $easywiModules['ro']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputDedicated"><?php echo $gsprache->dedicated;?></label>
                <div class="controls">
                    <input id="inputDedicated" type="checkbox" name="dedicatedServer" value="Y" <?php if ($dedicatedServer=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['traffic'] and $easywiModules['ro']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputTraffic"><?php echo $gsprache->traffic;?></label>
                <div class="controls">
                    <input id="inputTraffic" type="checkbox" name="traffic" value="Y" <?php if ($traffic=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php };if($pa['trafficsettings'] and $easywiModules['ro']) { ?>
            <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputTrafficSettings"><?php echo $gsprache->traffic.' '.$gsprache->settings;?></label>
                <div class="controls">
                    <input id="inputTrafficSettings" type="checkbox" name="trafficsettings" value="Y" <?php if ($trafficsettings=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <?php } ?>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUApi"><?php echo $gsprache->api;?></label>
                <div class="controls">
                    <input id="inputUApi" type="checkbox" name="uapiSettings" value="Y" <?php if ($apiSettings=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUJobs"><?php echo $gsprache->jobs;?></label>
                <div class="controls">
                    <input id="inputUJobs" type="checkbox" name="ujobs" value="Y" <?php if ($jobs=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputMiniRoot"><?php echo $sprache->miniroot;?></label>
                <div class="controls">
                    <input id="inputMiniRoot" type="checkbox" name="miniroot" value="Y" <?php if ($miniroot=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputRoots"><?php echo $gsprache->root;?></label>
                <div class="controls">
                    <input id="inputRoots" type="checkbox" name="uroots" value="Y" <?php if ($roots=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUlogs"><?php echo $gsprache->logs;?></label>
                <div class="controls">
                    <input id="inputUlogs" type="checkbox" name="ulog" value="Y" <?php if ($log=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputVoiceserver"><?php echo $gsprache->voiceserver;?></label>
                <div class="controls">
                    <input id="inputVoiceserver" type="checkbox" name="voiceserver" value="Y" <?php if ($voiceserver=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUVoiceserverStats"><?php echo $gsprache->voiceserver." ".$gsprache->stats;?></label>
                <div class="controls">
                    <input id="inputUVoiceserverStats" type="checkbox" name="uvoiceserverStats" value="Y" <?php if ($voiceserverStats=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputURestart"><?php echo $gsprache->gameserver." ".$sprache->restart;?></label>
                <div class="controls">
                    <input id="inputURestart" type="checkbox" name="restart" value="Y" <?php if ($restart=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputGsReset"><?php echo $gsprache->gameserver." ".$sprache->reset;?></label>
                <div class="controls">
                    <input id="inputGsReset" type="checkbox" name="reset" value="Y" <?php if ($reset=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUAddons"><?php echo $gsprache->addon;?></label>
                <div class="controls">
                    <input id="inputUAddons" type="checkbox" name="useraddons" value="Y" <?php if ($useraddons=='Y') echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUFastDL"><?php echo $sprache->fastdl;?></label>
                <div class="controls"><input id="inputUFastDL" type="checkbox" name="ufastdl" value="Y" <?php if ($fastdl=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUmodFastDL"><?php echo $sprache->modfastdl;?></label>
                <div class="controls"><input id="inputUmodFastDL" type="checkbox" name="modfastdl" value="Y" <?php if ($modfastdl=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUWebVhost"><?php echo $sprache->webspace;?></label>
                <div class="controls"><input id="inputUWebVhost" type="checkbox" name="uwebvhost" value="Y" <?php if ($webvhost=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUserSettings"><?php echo $sprache->usersettings;?></label>
                <div class="controls"><input id="inputUserSettings" type="checkbox" name="usersettings" value="Y" <?php if ($usersettings=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputFtpAccess"><?php echo $sprache->ftpaccess;?></label>
                <div class="controls"><input id="inputFtpAccess" type="checkbox" name="ftpaccess" value="Y" <?php if ($ftpaccess=='Y') echo 'checked="checked"'; ?>></div>
            </div><?php if(($pa['tickets'] and $admin_id==$reseller_id) or $reseller_id=="0") { ?>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUserTickets"><?php echo $sprache->usertickets;?></label>
                <div class="controls"><input id="inputUserTickets" type="checkbox" name="usertickets" value="Y" <?php if ($usertickets=='Y') echo 'checked="checked"'; ?>></div>
            </div><?php } ?>
            <div class="u <?php if ($grouptype!='u') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputUserBackup"><?php echo $gsprache->backup;?></label>
                <div class="controls"><input id="inputUserBackup" type="checkbox" name="ftpbackup" value="Y" <?php if ($ftpbackup=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php if($pa['root']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="InputResellerRoot"><?php if($pa['root']) { ?><?php echo $sprache->root;?>:<?php } ?></label>
                <div class="controls"><?php if($pa['root']) { ?><input id="InputResellerRoot" type="checkbox" name="rroot" value="Y" <?php if ($root=='Y') echo 'checked="checked"'; ?> /><?php } ?></div>
            </div>
            <?php };if($pa['log']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerLogs"><?php echo $gsprache->logs;?></label>
                <div class="controls"><input id="inputResellerLogs" type="checkbox" name="rlog" value="Y" <?php if ($log=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['settings']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerSettings"><?php echo $sprache->settings;?></label>
                <div class="controls"><input id="inputResellerSettings" type="checkbox" name="rsettings" value="Y" <?php if ($settings=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['apiSettings']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerApi"><?php echo $gsprache->api;?></label>
                <div class="controls"><input id="inputResellerApi" type="checkbox" name="rapiSettings" value="Y"  <?php if ($apiSettings=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['jobs']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerJobs"><?php echo $gsprache->jobs;?></label>
                <div class="controls"><input id="inputResellerJobs" type="checkbox" name="rjobs" value="Y"  <?php if ($jobs=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['user']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerUserAdmins"><?php echo $sprache->user_admins;?></label>
                <div class="controls"><input id="inputResellerUserAdmins" type="checkbox" name="ruser" value="Y" <?php if ($user=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['user_users']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerUsers"><?php echo $sprache->user_users;?></label>
                <div class="controls"><input id="inputResellerUsers" type="checkbox" name="ruser_users" value="Y" <?php if ($user_users=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['userGroups']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerGroups"><?php echo $gsprache->groups;?></label>
                <div class="controls"><input id="inputResellerGroups" type="checkbox" name="ruserGroups" value="Y"  <?php if ($userGroups=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['userPassword']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerPassword"><?php echo $sprache->passw;?></label>
                <div class="controls"><input id="inputResellerPassword" type="checkbox" name="ruserPassword" value="Y"  <?php if ($userPassword=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['mysql']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerMysqlDB">MySQL <?php echo $gsprache->databases;?></label>
                <div class="controls"><input id="inputResellerMysqlDB" type="checkbox" name="rmysql" value="Y"  <?php if ($mysql=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['mysql_settings']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerMysqlServer">MySQL Server</label>
                <div class="controls"><input id="inputResellerMysqlServer" type="checkbox" name="rmysql_settings" value="Y"  <?php if ($mysql_settings=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['webmaster']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputRWebMaster"><?php echo $sprache->webspace.' '.$sprache->master;?></label>
                <div class="controls"><input id="inputRWebMaster" type="checkbox" name="rwebmaster" value="Y" <?php if ($webmaster=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['webvhost']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputRWebVhost"><?php echo $sprache->webspace;?></label>
                <div class="controls"><input id="inputRWebVhost" type="checkbox" name="rwebvhost" value="Y" <?php if ($webvhost=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['roots']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerRoots"><?php echo $gsprache->root;?></label>
                <div class="controls"><input id="inputResellerRoots" type="checkbox" name="rroots" value="Y" <?php if ($roots=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['tickets']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerTickets"><?php echo $sprache->tickets;?></label>
                <div class="controls"><input id="inputResellerTickets" type="checkbox" name="rtickets" value="Y" <?php if ($tickets=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['gserver']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerGserver"><?php echo $sprache->gserver;?></label>
                <div class="controls"><input id="inputResellerGserver" type="checkbox" name="rgserver" value="Y" <?php if ($gserver=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['gimages']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerGserverTemplate"><?php echo $gsprache->gameserver." ".$gsprache->template;?></label>
                <div class="controls"><input id="inputResellerGserverTemplate" type="checkbox" name="rgimages" value="Y" <?php if ($gimages=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['addons']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerGserverAddons"><?php echo $gsprache->gameserver." ".$gsprache->addon;?></label>
                <div class="controls"><input id="inputResellerGserverAddons" type="checkbox" name="raddons" value="Y" <?php if ($addons=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['eac']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerEAC">Easy Anti Cheat</label>
                <div class="controls"><input id="inputResellerEAC" type="checkbox" name="reac" value="Y"  <?php if ($eac=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['masterServer']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerMasterserver"><?php echo $gsprache->master;?></label>
                <div class="controls"><input id="inputResellerMasterserver" type="checkbox" name="rmasterServer" value="Y"  <?php if ($masterServer=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php }; if($pa['voiceserver'] and $easywiModules['vo']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerVoiceserver"><?php echo $gsprache->voiceserver;?></label>
            <div class="controls"><input id="inputResellerVoiceserver" type="checkbox" name="rvoiceserver" value="Y" <?php if ($voiceserver=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php }; if($pa['voicemasterserver'] and $easywiModules['vo']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerVoiceMaster"><?php echo $gsprache->voiceserver." ".$gsprache->master;?></label>
                <div class="controls"><input id="inputResellerVoiceMaster" type="checkbox" name="rvoicemasterserver" value="Y" <?php if ($voicemasterserver=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['voiceserverSettings'] and $easywiModules['vo']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerVoiceStats"><?php echo $gsprache->voiceserver.' '.$gsprache->stats;?></label>
                <div class="controls"><input id="inputResellerVoiceStats" type="checkbox" name="rvoiceserverStats" value="Y"  <?php if ($voiceserverStats=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['voiceserverStats'] and $easywiModules['vo']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerVoiceStatsSettings"><?php echo $gsprache->voiceserver.' '.$gsprache->stats.' '.$gsprache->settings;?></label>
                <div class="controls"><input id="inputResellerVoiceStatsSettings" type="checkbox" name="rvoiceserverSettings" value="Y"  <?php if ($voiceserverSettings=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['lendserver']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerLendserver"><?php echo $gsprache->lendserver;?></label>
                <div class="controls"><input id="inputResellerLendserver" type="checkbox" name="rlendserver" value="Y" <?php if ($lendserver=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['lendserverSettings']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerLendserverSettings"><?php echo $gsprache->lendserver.' '.$gsprache->settings;?></label>
                <div class="controls"><input id="inputResellerLendserverSettings" type="checkbox" name="rlendserverSettings" value="Y" <?php if ($lendserverSettings=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['usertickets']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerUserTickets"><?php echo $sprache->usertickets;?></label>
                <div class="controls"><input id="inputResellerUserTickets" type="checkbox" name="rusertickets" value="Y" <?php if ($usertickets=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php } ?>
            <?php if($easywiModules['ro'] and $pa['addvserver'] and $reseller_id==0) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerVirtualAdd"><?php echo $gsprache->virtual." ".$gsprache->add;?></label>
                <div class="controls"><input id="inputResellerVirtualAdd" type="checkbox" name="raddvserver" value="Y" <?php if ($addvserver=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['delvserver'] and $reseller_id==0) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerVirtualDel"><?php echo $gsprache->virtual." ".$gsprache->del;?></label>
                <div class="controls"><input id="inputResellerVirtualDel" type="checkbox" name="rdelvserver" value="Y" <?php if ($delvserver=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['modvserver']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerVirtualMod"><?php echo $gsprache->virtual." ".$gsprache->mod;?></label>
                <div class="controls"><input id="inputResellerVirtualMod" type="checkbox" name="rmodvserver" value="Y" <?php if ($modvserver=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php };if($pa['usevserver']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerVirtualReset"><?php echo $gsprache->virtual." ".$sprache->reset."/".$sprache->restart;?></label>
                <div class="controls"><input id="inputResellerVirtualReset" type="checkbox" name="rusevserver" value="Y" <?php if ($usevserver=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php if($pa['traffic'] and $easywiModules['ro'] and $reseller_id==0) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerTraffic"><?php echo $gsprache->traffic;?></label>
                <div class="controls"><input id="inputResellerTraffic" type="checkbox" name="rtraffic" value="Y" <?php if ($traffic=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php }; if($pa['dedicatedServer'] and $easywiModules['ro']) { ?>
            <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch control-group">
                <label class="control-label" for="inputResellerDedicated"><?php echo $gsprache->dedicated;?></label>
                <div class="controls"><input id="inputResellerDedicated" type="checkbox" name="rdedicatedServer" value="Y" <?php if ($dedicatedServer=='Y') echo 'checked="checked"'; ?>></div>
            </div>
            <?php }} ?>
            <div class="control-group">
                <label class="control-label" for="inputMod"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputMod" type="submit"><i class="icon-white icon-edit"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>