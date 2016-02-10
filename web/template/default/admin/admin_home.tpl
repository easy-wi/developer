<section class="content-header">
    <h4>Dashboard</h4>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
    </ol>
</section>

<section class="content">

    <?php if($easywiModules['gs'] and $pa['roots'] and $statsArray['gameMasterInstalled']>0) { ?>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-server"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->appRoot;?></span>
                    <span class="info-box-number"><?php echo $statsArray['gameMasterActive'].'/'.$statsArray['gameMasterInstalled'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['gameMasterActivePercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameMasterActivePercent'].'% '. $gsprache->active;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->appRoot;?></span>
                    <span class="info-box-number"><?php echo $statsArray['gameMasterCrashed'].'/'.$statsArray['gameMasterActive'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['gameMasterCrashedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameMasterCrashedPercent'].'% '.$sprache_bad->crashed;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-signal"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->appRoot;?></span>
                    <span class="info-box-number"><?php echo $statsArray['gameserverInstalled'].'/'.$statsArray['gameMasterServerAvailable'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['gameMasterServerPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameMasterServerPercent'].'% '.$sprache_bad->master_installed_server;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-signal"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->appRoot;?></span>
                    <span class="info-box-number"><?php echo $statsArray['gameserverSlotsInstalled'].'/'.$statsArray['gameMasterSlotsAvailable'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['gameMasterSlotsPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameMasterSlotsPercent'].'% '.$sprache_bad->master_installed_slots;?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <?php if($easywiModules['gs'] and $pa['gserver'] and $statsArray['gameserverInstalled']>0){ ?>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-gamepad"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->gameserver;?></span>
                    <span class="info-box-number"><?php echo $statsArray['gameserverActive'].'/'.$statsArray['gameserverInstalled'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['gameserverActivePercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameserverActivePercent'].'% '.$gsprache->active;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->gameserver;?></span>
                    <span class="info-box-number"><?php echo $statsArray['gameserverNotRunning'].'/'.$statsArray['gameserverActive'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['gameserverCrashedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameserverCrashedPercent'].'% '.$sprache_bad->crashed;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-gavel"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->gameserver;?></span>
                    <span class="info-box-number"><?php echo ($statsArray['gameserverNoTag'] + $statsArray['gameserverNoPassword']).'/'.$statsArray['gameserverActive'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['gameserverRuleBreakPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameserverRuleBreakPercent'].'% '.$sprache_bad->rulebreak;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-plug"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->gameserver;?></span>
                    <span class="info-box-number"><?php echo $statsArray['gameserverSlotsUsed'].'/'.$statsArray['gameserverSlotsActive'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['gameserverSlotsUsedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameserverSlotsUsedPercent'].'% '.$sprache_bad->usage_slots;?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <?php if($easywiModules['vo'] and $pa['voicemasterserver'] and $statsArray['voiceMasterInstalled']>0) { ?>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-server"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->voiceserver.' '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['voiceMasterActive'].'/'.$statsArray['voiceMasterInstalled'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['voiceMasterActivePercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceMasterActivePercent'].'% '. $gsprache->active;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->voiceserver.' '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['voiceMasterCrashed'].'/'.$statsArray['voiceMasterActive'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['voiceMasterCrashedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceMasterCrashedPercent'].'% '.$sprache_bad->crashed;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-signal"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->voiceserver.' '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['voiceserverInstalled'].'/'.$statsArray['voiceMasterServerAvailable'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['voiceMasterServerPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceMasterServerPercent'].'% '.$sprache_bad->master_installed_server;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-signal"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->voiceserver.' '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['voiceserverSlotsInstalled'].'/'.$statsArray['voiceMasterSlotsAvailable'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['voiceMasterSlotsPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceMasterSlotsPercent'].'% '.$sprache_bad->master_installed_slots;?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <?php if($easywiModules['vo'] and $pa['voiceserver'] and $statsArray['voiceserverInstalled']>0) { ?>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-microphone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->voiceserver;?></span>
                    <span class="info-box-number"><?php echo $statsArray['voiceserverActive'].'/'.$statsArray['voiceserverInstalled'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['gameserverActivePercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceserverActivePercent'].'% '.$gsprache->active;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->voiceserver;?></span>
                    <span class="info-box-number"><?php echo $statsArray['voiceserverCrashed'].'/'.$statsArray['voiceserverActive'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['voiceserverCrashedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceserverCrashedPercent'].'% '.$sprache_bad->crashed;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-signal"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->voiceserver;?></span>
                    <span class="info-box-number"><?php echo $statsArray['voiceserverTrafficUsed'].'/'.$statsArray['voiceserverTrafficAllowed'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['voiceserverTrafficPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceserverTrafficPercent'].'% '.$sprache_bad->traffic;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-plug"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->voiceserver;?></span>
                    <span class="info-box-number"><?php echo $statsArray['voiceserverSlotsUsed'].'/'.$statsArray['voiceserverSlotsActive'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['voiceserverSlotsUsedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceserverSlotsUsedPercent'].'% '.$sprache_bad->usage_slots;?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <?php if($easywiModules['ws'] and ($pa['webvhost'] or $pa['webmaster']) and $statsArray['webMasterInstalled']>0) { ?>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-server"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->webspace.' '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['webMasterActive'].'/'.$statsArray['webMasterInstalled'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['webMasterActivePercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['webMasterActivePercent'].'% '. $gsprache->active;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->webspace.' '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['webMasterCrashed'].'/'.$statsArray['webMasterActive'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['webMasterCrashedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['webMasterCrashedPercent'].'% '.$sprache_bad->crashed;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-signal"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->webspace.' '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['webspaceInstalled'].'/'.$statsArray['webMasterVhostAvailable'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['webMasterVhostPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['webMasterVhostPercent'].'% '.$sprache_bad->master_installed_vhosts;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-signal"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo $gsprache->webspace.' '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['webspaceSpaceGiven'].'/'.$statsArray['webMasterSpaceAvailable'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['webMasterSpaceUsedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['webMasterSpaceUsedPercent'].'% '.$sprache_bad->master_installed_space;?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <?php if($easywiModules['my'] and ($pa['mysql'] or $pa['mysql_settings']) and $statsArray['mysqlMasterInstalled']>0) { ?>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-server"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo 'MySQL '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['mysqlMasterActive'].'/'.$statsArray['mysqlMasterInstalled'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['mysqlMasterActivePercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['mysqlMasterActivePercent'].'% '. $gsprache->active;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo 'MySQL '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['mysqlMasterCrashed'].'/'.$statsArray['mysqlMasterActive'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['mysqlMasterCrashedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['mysqlMasterCrashedPercent'].'% '.$sprache_bad->crashed;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-signal"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo 'MySQL '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['mysqlDBInstalled'].'/'.$statsArray['mysqlMasterDBAvailable'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $statsArray['mysqlMasterDBPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['mysqlMasterDBPercent'].'% '.$sprache_bad->master_installed_db;?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-signal"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo 'MySQL '.$gsprache->master;?></span>
                    <span class="info-box-number"><?php echo $statsArray['mysqlDBSpaceUsed'];?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['mysqlDBSpaceUsed'].'MB';?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <?php if(count($feedArray)>0) { ?>
    <hr>

    <?php foreach ($feedArray as $url => $array) { ?>
    <div class="row">
        <div class="col-md-12">

            <h3><?php echo $url;?></h3>

            <ul class="timeline">
                <?php $lastdate=0;?>
                <?php foreach ($array as $feed) { ;?>
                <?php if($lastdate!=$feed['date']){ ?>

                <li class="time-label"><span class="bg-green"><?php echo $feed['date'];?></span></li>

                <?php };?>
                <?php $lastdate=$feed['date'];?>

                <li>
                    <i class="fa fa-info bg-blue"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> <?php echo $feed['time'];?></span>
                        <h3 class="timeline-header"><?php echo $feed['title'];?></h3>
                        <div class="timeline-body">
                            <?php echo $feed['text'];?>
                        </div>
                        <div class='timeline-footer'>
                            <a class="btn btn-primary btn-xs" href="<?php echo $feed['link'];?>" target="_blank">Read more</a>
                        </div>
                    </div>
                </li>
                <?php } ?>

                <li>
                    <i class="fa fa-clock-o"></i>
                </li>
            </ul>
        </div>
    </div>
    <?php } ?>
    <?php }?>
</section>