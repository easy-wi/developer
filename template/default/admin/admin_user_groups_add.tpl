<section class="content-header">
    <h1><?php echo $gsprache->groups;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><i class="fa fa-user"></i> <?php echo $gsprache->user;?></li>
        <li><i class="fa fa-group"></i> <?php echo $gsprache->groups;?></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">

                <form role="form" action="admin.php?w=ug&amp;d=ad&amp;r=ug" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputDefault">Default</label>
                            <div class="controls">
                                <select class="form-control" id="inputDefault" name="defaultgroup">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputType"><?php echo $sprache->accounttype;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputType" name="grouptype" onchange="SwitchShowHideRows(this.value)">
                                    <option value="u"><?php echo $sprache->accounttype_user;?></option>
                                    <?php if($reseller_id=="0" and $pa['user']) { ?><option value="a"><?php echo $sprache->accounttype_admin;?></option><?php }?>
                                    <?php if($reseller_id=="0" or $admin_id==$reseller_id) { ?><option value="r"><?php echo $sprache->accounttype_reseller;?></option><?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputType"><?php echo $sprache->groupname;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputType" type="text" name="groupname" value="" pattern="[0-9A-Za-z ]{2,255}" required>
                            </div>
                        </div>
                        <?php if($pa['root']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputRoot" type="checkbox" name="root" value="Y">
                                <?php echo $sprache->root;?>
                            </label>
                        </div>
                        <?php };if($pa['log']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputLogs" type="checkbox" name="log" value="Y">
                                <?php echo $gsprache->logs;?>
                            </label>
                        </div>
                        <?php };if($pa['settings']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputSettings" type="checkbox" name="settings" value="Y">
                                <?php echo $sprache->settings;?>
                            </label>
                        </div>
                        <?php };if($pa['ipBans']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputIpBans" type="checkbox" name="ipBans" value="Y">
                                IP Bans
                            </label>
                        </div>
                        <?php };if($pa['updateEW']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputUpdateEW" type="checkbox" name="updateEW" value="Y">
                                Easy-Wi Update
                            </label>
                        </div>
                        <?php };if($pa['apiSettings']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputApiSettings" type="checkbox" name="apiSettings" value="Y">
                                <?php echo $gsprache->api;?>
                            </label>
                        </div>
                        <?php };if($pa['jobs']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputJobs" type="checkbox" name="jobs" value="Y">
                                <?php echo $gsprache->jobs;?>
                            </label>
                        </div>
                        <?php };if($pa['user']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputUser" type="checkbox" name="user" value="Y">
                                <?php echo $sprache->user_admins;?>
                            </label>
                        </div>
                        <?php };if($pa['user_users']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputUserUsers" type="checkbox" name="user_users" value="Y">
                                <?php echo $sprache->user_users;?>
                            </label>
                        </div>
                        <?php };if($pa['userGroups']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputUserGroups" type="checkbox" name="userGroups" value="Y">
                                <?php echo $gsprache->groups;?>
                            </label>
                        </div>
                        <?php };if($pa['userPassword']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputUserPassword" type="checkbox" name="userPassword" value="Y">
                                <?php echo $sprache->passw;?>
                            </label>
                        </div>
                        <?php };if($pa['mysql']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputMYSQL" type="checkbox" name="mysql" value="Y">
                                MySQL <?php echo $gsprache->databases;?>
                            </label>
                        </div>
                        <?php };if($pa['mysql_settings']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputMYSQLServer" type="checkbox" name="mysql_settings" value="Y">
                                MySQL Server
                            </label>
                        </div>
                        <?php };if($pa['webmaster']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputAWebMaster" type="checkbox" name="webmaster" value="Y">
                                <?php echo $gsprache->webspace.' '.$gsprache->master;?>
                            </label>
                        </div>
                        <?php };if($pa['webvhost']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputAWebVhost" type="checkbox" name="webvhost" value="Y">
                                <?php echo $gsprache->webspace;?>
                            </label>
                        </div>
                        <?php };if($pa['tickets']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputTickets" type="checkbox" name="tickets" value="Y">
                                <?php echo $sprache->tickets;?>
                            </label>
                        </div>
                        <?php };if($pa['cms_news']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputNews" type="checkbox" name="cms_news" value="Y">
                                <?php echo $gsprache->news;?>
                            </label>
                        </div>
                        <?php };if($pa['cms_pages']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputPages" type="checkbox" name="cms_pages" value="Y">
                                <?php echo $gsprache->pages;?>
                            </label>
                        </div>
                        <?php };if($pa['cms_settings']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputCMSSettings" type="checkbox" name="cms_settings" value="Y">
                                CMS <?php echo $gsprache->settings;?>
                            </label>
                        </div>
                        <?php };if($pa['eac']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputEAC" type="checkbox" name="eac" value="Y">
                                Easy Anti Cheat
                            </label>
                        </div>
                        <?php };if($pa['gserver']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputGserver" type="checkbox" name="gserver" value="Y">
                                <?php echo $sprache->gserver;?>
                            </label>
                        </div>
                        <?php };if($pa['gimages']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputGserverImages" type="checkbox" name="gimages" value="Y">
                                <?php echo $gsprache->gameserver . ' ' . $gsprache->template;?>
                            </label>
                        </div>
                        <?php };if($pa['addons']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputGserverAddons" type="checkbox" name="addons" value="Y">
                                <?php echo $gsprache->gameserver . ' ' . $gsprache->addon;?>
                            </label>
                        </div>
                        <?php };if($pa['masterServer']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputMaster" type="checkbox" name="masterServer" value="Y">
                                <?php echo $gsprache->master;?>
                            </label>
                        </div>
                        <?php };if($pa['voiceserver'] and $easywiModules['vo']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVoiceserver" type="checkbox" name="voiceserver" value="Y">
                                <?php echo $gsprache->voiceserver;?>
                            </label>
                        </div>
                        <?php };if($pa['voicemasterserver'] and $easywiModules['vo']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVoiceserverMaster" type="checkbox" name="voicemasterserver" value="Y">
                                <?php echo $gsprache->voiceserver . ' ' . $gsprache->master;?>
                            </label>
                        </div>
                        <?php };if($pa['voiceserverSettings'] and $easywiModules['vo']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVoiceserverStats" type="checkbox" name="voiceserverStats" value="Y">
                                <?php echo $gsprache->voiceserver . ' ' . $gsprache->stats;?>
                            </label>
                        </div>
                        <?php };if($pa['voiceserverStats'] and $easywiModules['vo']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVoiceserverStatsSettings" type="checkbox" name="voiceserverSettings" value="Y">
                                <?php echo $gsprache->voiceserver . ' ' . $gsprache->stats.' '.$gsprache->settings;?>
                            </label>
                        </div>
                        <?php };if($pa['lendserver']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputLendserver" type="checkbox" name="lendserver" value="Y">
                                <?php echo $gsprache->lendserver;?>
                            </label>
                        </div>
                        <?php };if($pa['lendserverSettings']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputLendserverSettings" type="checkbox" name="lendserverSettings" value="Y">
                                <?php echo $gsprache->lendserver.' '.$gsprache->settings;?>
                            </label>
                        </div>
                        <?php };if($pa['roots']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputRoots" type="checkbox" name="roots" value="Y">
                                <?php echo $gsprache->root;?>
                            </label>
                        </div>
                        <?php };if($pa['addvserver']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVirtualAdd" type="checkbox" name="addvserver" value="Y">
                                <?php echo $gsprache->virtual . ' ' . $gsprache->add;?>
                            </label>
                        </div>
                        <?php };if($pa['modvserver']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVirtualMod" type="checkbox" name="modvserver" value="Y">
                                <?php echo $gsprache->virtual . ' ' . $gsprache->mod;?>
                            </label>
                        </div>
                        <?php };if($pa['delvserver']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVirtualDel" type="checkbox" name="delvserver" value="Y">
                                <?php echo $gsprache->virtual . ' ' . $gsprache->del;?>
                            </label>
                        </div>
                        <?php };if($pa['usevserver'] and $easywiModules['ro']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVirtualUse" type="checkbox" name="usevserver" value="Y">
                                <?php echo $gsprache->virtual . ' ' . $rsprache->reinstall."/".$rsprache->rescue."/".$sprache->restart;?>
                            </label>
                        </div>
                        <?php };if($pa['vserverhost'] and $easywiModules['ro']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVirtualHost" type="checkbox" name="vserverhost" value="Y">
                                <?php echo $gsprache->reseller . ' ' . $gsprache->hostsystem;?>
                            </label>
                        </div>
                        <?php };if($pa['resellertemplates'] and $easywiModules['ro']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVirtualTemplate" type="checkbox" name="resellertemplates" value="Y">
                                <?php echo $gsprache->reseller . ' ' . $gsprache->template;?>
                            </label>
                        </div>
                        <?php };if($pa['dhcpServer'] and $easywiModules['ro']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputDHCP" type="checkbox" name="dhcpServer" value="Y">
                                DHCP <?php echo $gsprache->settings;?>
                            </label>
                        </div>
                        <?php };if($pa['pxeServer'] and $easywiModules['ro']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputPXE" type="checkbox" name="pxeServer" value="Y">
                                PXE <?php echo $gsprache->settings;?>
                            </label>
                        </div>
                        <?php };if($pa['vserversettings'] and $easywiModules['ro']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputVserverSettings" type="checkbox" name="vserversettings" value="Y">
                                <?php echo $gsprache->reseller . ' ' . $gsprache->settings;?>
                            </label>
                        </div>
                        <?php }; if($pa['dedicatedServer'] and $easywiModules['ro']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputDedicated" type="checkbox" name="dedicatedServer" value="Y">
                                <?php echo $gsprache->dedicated;?>
                            </label>
                        </div>
                        <?php };if($pa['traffic'] and $easywiModules['ro']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputTraffic" type="checkbox" name="traffic" value="Y">
                                <?php echo $gsprache->traffic;?>
                            </label>
                        </div>
                        <?php };if($pa['trafficsettings'] and $easywiModules['ro']) { ?>
                        <div class="a <?php if ($grouptype!='a') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputTrafficSettings" type="checkbox" name="trafficsettings" value="Y">
                                <?php echo $gsprache->traffic.' '.$gsprache->settings;?>
                            </label>
                        </div>
                        <?php } ?>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUApi" type="checkbox" name="uapiSettings" value="Y">
                                <?php echo $gsprache->api;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUJobs" type="checkbox" name="ujobs" value="Y">
                                <?php echo $gsprache->jobs;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputMiniRoot" type="checkbox" name="miniroot" value="Y">
                                <?php echo $sprache->miniroot;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputRoots" type="checkbox" name="uroots" value="Y">
                                <?php echo $gsprache->root;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUlogs" type="checkbox" name="ulog" value="Y">
                                <?php echo $gsprache->logs;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputVoiceserver" type="checkbox" name="voiceserver" value="Y">
                                <?php echo $gsprache->voiceserver;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUVoiceserverStats" type="checkbox" name="uvoiceserverStats" value="Y">
                                <?php echo $gsprache->voiceserver . ' ' . $gsprache->stats;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputURestart" type="checkbox" name="restart" value="Y">
                                <?php echo $gsprache->gameserver . ' ' . $sprache->restart;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputGsReset" type="checkbox" name="reset" value="Y">
                                <?php echo $gsprache->gameserver . ' ' . $sprache->reset;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUAddons" type="checkbox" name="useraddons" value="Y">
                                <?php echo $gsprache->addon;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUFastDL" type="checkbox" name="ufastdl" value="Y">
                                <?php echo $sprache->fastdl;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUmodFastDL" type="checkbox" name="modfastdl" value="Y">
                                <?php echo $sprache->modfastdl;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUWebVhost" type="checkbox" name="uwebvhost" value="Y">
                                <?php echo $gsprache->webspace;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputMYSQL" type="checkbox" name="umysql" value="Y">
                                MySQL <?php echo $gsprache->databases;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUserSettings" type="checkbox" name="usersettings" value="Y">
                                <?php echo $sprache->usersettings;?>
                            </label>
                        </div>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputFtpAccess" type="checkbox" name="ftpaccess" value="Y">
                                <?php echo $sprache->ftpaccess;?>
                            </label>
                        </div>
                        <?php if(($pa['tickets'] and $admin_id==$reseller_id) or $reseller_id=="0") { ?>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUserTickets" type="checkbox" name="usertickets" value="Y">
                                <?php echo $sprache->usertickets;?>
                            </label>
                        </div><?php } ?>
                        <div class="u switch checkbox">
                            <label>
                                <input id="inputUserBackup" type="checkbox" name="ftpbackup" value="Y">
                                <?php echo $gsprache->backup;?>
                            </label>
                        </div>
                        <?php if($pa['root']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="InputResellerRoot" type="checkbox" name="rroot" value="Y">
                                <?php echo $sprache->root;?>
                            </label>
                        </div>
                        <?php };if($pa['log']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerLogs" type="checkbox" name="rlog" value="Y">
                                <?php echo $gsprache->logs;?>
                            </label>
                        </div>
                        <?php };if($pa['settings']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerSettings" type="checkbox" name="rsettings" value="Y">
                                <?php echo $sprache->settings;?>
                            </label>
                        </div>
                        <?php };if($pa['apiSettings']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerApi" type="checkbox" name="rapiSettings" value="Y">
                                <?php echo $gsprache->api;?>
                            </label>
                        </div>
                        <?php };if($pa['jobs']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerJobs" type="checkbox" name="rjobs" value="Y">
                                <?php echo $gsprache->jobs;?>
                            </label>
                        </div>
                        <?php };if($pa['user']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerUserAdmins" type="checkbox" name="ruser" value="Y">
                                <?php echo $sprache->user_admins;?>
                            </label>
                        </div>
                        <?php };if($pa['user_users']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerUsers" type="checkbox" name="ruser_users" value="Y">
                                <?php echo $sprache->user_users;?>
                            </label>
                        </div>
                        <?php };if($pa['userGroups']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerGroups" type="checkbox" name="ruserGroups" value="Y">
                                <?php echo $gsprache->groups;?>
                            </label>
                        </div>
                        <?php };if($pa['userPassword']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerPassword" type="checkbox" name="ruserPassword" value="Y">
                                <?php echo $sprache->passw;?>
                            </label>
                        </div>
                        <?php };if($pa['mysql']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerMysqlDB" type="checkbox" name="rmysql" value="Y">
                                MySQL <?php echo $gsprache->databases;?>
                            </label>
                        </div>
                        <?php };if($pa['mysql_settings']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerMysqlServer" type="checkbox" name="rmysql_settings" value="Y">
                                MySQL Server
                            </label>
                        </div>
                        <?php };if($pa['webmaster']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputRWebMaster" type="checkbox" name="rwebmaster" value="Y">
                                <?php echo $gsprache->webspace.' '.$gsprache->master;?>
                            </label>
                        </div>
                        <?php };if($pa['webvhost']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputRWebVhost" type="checkbox" name="rwebvhost" value="Y">
                                <?php echo $gsprache->webspace;?>
                            </label>
                        </div>
                        <?php };if($pa['roots']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerRoots" type="checkbox" name="rroots" value="Y">
                                <?php echo $gsprache->root;?>
                            </label>
                        </div>
                        <?php };if($pa['tickets']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerTickets" type="checkbox" name="rtickets" value="Y">
                                <?php echo $sprache->tickets;?>
                            </label>
                        </div>
                        <?php };if($pa['gserver']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerGserver" type="checkbox" name="rgserver" value="Y">
                                <?php echo $sprache->gserver;?>
                            </label>
                        </div>
                        <?php };if($pa['gimages']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerGserverTemplate" type="checkbox" name="rgimages" value="Y">
                                <?php echo $gsprache->gameserver . ' ' . $gsprache->template;?>
                            </label>
                        </div>
                        <?php };if($pa['addons']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerGserverAddons" type="checkbox" name="raddons" value="Y">
                                <?php echo $gsprache->gameserver . ' ' . $gsprache->addon;?>
                            </label>
                        </div>
                        <?php };if($pa['eac']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerEAC" type="checkbox" name="reac" value="Y">
                                Easy Anti Cheat
                            </label>
                        </div>
                        <?php };if($pa['masterServer']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerMasterserver" type="checkbox" name="rmasterServer" value="Y">
                                <?php echo $gsprache->master;?>
                            </label>
                        </div>
                        <?php }; if($pa['voiceserver'] and $easywiModules['vo']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerVoiceserver" type="checkbox" name="rvoiceserver" value="Y">
                                <?php echo $gsprache->voiceserver;?>
                            </label>
                        </div>
                        <?php }; if($pa['voicemasterserver'] and $easywiModules['vo']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerVoiceMaster" type="checkbox" name="rvoicemasterserver" value="Y">
                                <?php echo $gsprache->voiceserver . ' ' . $gsprache->master;?>
                            </label>
                        </div>
                        <?php };if($pa['voiceserverSettings'] and $easywiModules['vo']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerVoiceStats" type="checkbox" name="rvoiceserverStats" value="Y">
                                <?php echo $gsprache->voiceserver.' '.$gsprache->stats;?>
                            </label>
                        </div>
                        <?php };if($pa['voiceserverStats'] and $easywiModules['vo']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerVoiceStatsSettings" type="checkbox" name="rvoiceserverSettings" value="Y">
                                <?php echo $gsprache->voiceserver.' '.$gsprache->stats.' '.$gsprache->settings;?>
                            </label>
                        </div>
                        <?php };if($pa['lendserver']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerLendserver" type="checkbox" name="rlendserver" value="Y">
                                <?php echo $gsprache->lendserver;?>
                            </label>
                        </div>
                        <?php };if($pa['lendserverSettings']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerLendserverSettings" type="checkbox" name="rlendserverSettings" value="Y">
                                <?php echo $gsprache->lendserver.' '.$gsprache->settings;?>
                            </label>
                        </div>
                        <?php };if($pa['usertickets']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerUserTickets" type="checkbox" name="rusertickets" value="Y">
                                <?php echo $sprache->usertickets;?>
                            </label>
                        </div>
                        <?php } ?>
                        <?php if($easywiModules['ro'] and $pa['addvserver'] and $reseller_id==0) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerVirtualAdd" type="checkbox" name="raddvserver" value="Y">
                                <?php echo $gsprache->virtual . ' ' . $gsprache->add;?>
                            </label>
                        </div>
                        <?php };if($pa['delvserver'] and $reseller_id==0) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerVirtualDel" type="checkbox" name="rdelvserver" value="Y">
                                <?php echo $gsprache->virtual . ' ' . $gsprache->del;?>
                            </label>
                        </div>
                        <?php };if($pa['modvserver']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerVirtualMod" type="checkbox" name="rmodvserver" value="Y">
                                <?php echo $gsprache->virtual . ' ' . $gsprache->mod;?>
                            </label>
                        </div>
                        <?php };if($pa['usevserver']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerVirtualReset" type="checkbox" name="rusevserver" value="Y">
                                <?php echo $gsprache->virtual . ' ' . $sprache->reset."/".$sprache->restart;?>
                            </label>
                        </div>
                        <?php if($pa['traffic'] and $easywiModules['ro'] and $reseller_id==0) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerTraffic" type="checkbox" name="rtraffic" value="Y">
                                <?php echo $gsprache->traffic;?>
                            </label>
                        </div>
                        <?php }; if($pa['dedicatedServer'] and $easywiModules['ro']) { ?>
                        <div class="r <?php if ($grouptype!='r') echo 'display_none';?> switch checkbox">
                            <label>
                                <input id="inputResellerDedicated" type="checkbox" name="rdedicatedServer" value="Y">
                                <?php echo $gsprache->dedicated;?>
                            </label>
                        </div>
                        <?php }} ?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>