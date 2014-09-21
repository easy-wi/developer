<section class="content-header">
    <h4>Dashboard</h4>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>

<section class="content">

    <div class="row">

        <?php if($easywiModules['gs'] and $pa['roots'] and $statsArray['gameMasterActive']>0) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        <?php echo $gsprache->appRoot;?>
                    </h3>
                    <p>
                        <?php echo $statsArray['gameMasterCrashed'].'/'.$statsArray['gameMasterActive'];?> <?php echo $sprache_bad->crashed;?><br>
                        <?php echo $statsArray['gameserverActive'].'/'.$statsArray['gameMasterServerAvailable'];?> <?php echo $sprache_bad->master_installed_server;?><br>
                        <?php echo $statsArray['gameserverSlotsInstalled'].'/'.$statsArray['gameMasterSlotsAvailable'];?> <?php echo $sprache_bad->master_installed_slots;?>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-hdd-o"></i>
                </div>
                <a href="admin.php?w=ro" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php }?>

        <?php if($easywiModules['gs'] and $pa['gserver'] and $statsArray['gameserverActive']){ ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        <?php echo $gsprache->gameserver;?>
                    </h3>
                    <p>
                        <?php echo $statsArray['gameserverNotRunning'].'/'.$statsArray['gameserverActive'];?> <?php echo $sprache_bad->crashed;?><br>
                        <?php echo $statsArray['gameserverSlotsUsed'].'/'.$statsArray['gameserverSlotsActive'];?> <?php echo $sprache_bad->usage_slots;?><br>
                        <?php if(($statsArray['gameserverNoTag'] + $statsArray['gameserverNoPassword'])> 0){ echo ($statsArray['gameserverNoTag'] + $statsArray['gameserverNoPassword']).'/'.$statsArray['gameserverActive'];?> <?php echo $sprache_bad->rulebreak;}?><br>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-gamepad"></i>
                </div>
                <a href="admin.php?w=gs" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php }?>

        <?php if($easywiModules['vo'] and $pa['voicemasterserver'] and $statsArray['voiceMasterActive']>0) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        <?php echo $gsprache->master;?>
                    </h3>
                    <p>
                        <?php echo $statsArray['voiceMasterCrashed'].'/'.$statsArray['voiceMasterActive'];?> <?php echo $sprache_bad->crashed;?><br>
                        <?php echo $statsArray['voiceserverActive'].'/'.$statsArray['voiceMasterServerAvailable'];?> <?php echo $sprache_bad->master_installed_server;?><br>
                        <?php echo $statsArray['voiceserverSlotsInstalled'].'/'.$statsArray['voiceMasterSlotsAvailable'];?> <?php echo $sprache_bad->master_installed_slots;?>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-microphone"></i>
                </div>
                <a href="admin.php?w=vm" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php }?>

        <?php if($easywiModules['vo'] and $pa['voiceserver'] and $statsArray['voiceserverActive']>0) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        <?php echo $gsprache->voiceserver;?>
                    </h3>
                    <p>
                        <?php echo $statsArray['voiceserverCrashed'].'/'.$statsArray['voiceserverActive'];?> <?php echo $sprache_bad->crashed;?><br>
                        <?php echo $statsArray['voiceserverSlotsUsed'].'/'.$statsArray['voiceserverSlotsActive'];?> <?php echo $sprache_bad->usage_slots;?><br>
                        <?php echo $statsArray['voiceserverTrafficUsed'].'/'.$statsArray['voiceserverTrafficAllowed'];?> <?php echo $sprache_bad->traffic;?>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-microphone"></i>
                </div>
                <a href="admin.php?w=vo" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php }?>

        <?php if($easywiModules['ws'] and ($pa['webvhost'] or $pa['webmaster']) and $statsArray['webMasterInstalled']>0) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        <?php echo $gsprache->webspace;?>
                    </h3>
                    <p>
                        <?php echo $statsArray['webMasterActive'].'/'.$statsArray['webMasterInstalled'];?> <?php echo $gsprache->webspace.' '.$gsprache->master;?><br>
                        <?php echo $statsArray['webspaceActive'].'/'.$statsArray['webMasterVhostAvailable'];?> <?php echo $sprache_bad->master_installed_vhosts;?><br>
                        <?php echo $statsArray['webspaceSpaceUsed'].'/'.$statsArray['webMasterSpaceAvailable'];?> MB <?php echo $sprache_bad->master_installed_space;?>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-cubes"></i>
                </div>
                <a href="admin.php?w=wv" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php }?>

        <?php if($easywiModules['my'] and ($pa['mysql'] or $pa['mysql_settings']) and $statsArray['mysqlMasterInstalled']>0) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        MySQL
                    </h3>
                    <p>
                        <?php echo $statsArray['mysqlMasterActive'].'/'.$statsArray['mysqlMasterInstalled'];?> MySQL <?php echo $gsprache->master;?><br>
                        <?php echo $statsArray['mysqlDBInstalled'].'/'.$statsArray['mysqlMasterDBAvailable'];?> <?php echo $sprache_bad->master_installed_db;?><br>
                        <?php echo $statsArray['mysqlDBSpaceUsed'];?> MB
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-database"></i>
                </div>
                <a href="admin.php?w=my" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php }?>

        <?php if($easywiModules['ti'] and ($pa['usertickets'] or $pa['tickets']) and $statsArray['ticketsTotal']>0) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        <?php echo $gsprache->support;?>
                    </h3>
                    <p>
                        <?php echo $statsArray['ticketsTotal'];?> <?php echo $sprache_bad->tickets_open;?><br><br><br>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-support"></i>
                </div>
                <a href="admin.php?w=ti" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php } ?>
    </div>

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