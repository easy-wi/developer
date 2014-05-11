<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Dashboard</li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <?php if($easywiModules['gs'] and $pa['roots'] and $easywiModules['gs']) { ?>
    <div class="span3">
        <h5><a href="admin.php?w=ro"><?php echo $statsArray['gameMasterInstalled'].' '.$gsprache->gameroot;?></a></h5>
        <strong><?php echo $gsprache->active;?></strong><span class="pull-right"><?php echo $statsArray['gameMasterActivePercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-success" style="width: <?php echo $statsArray['gameMasterActivePercent'];?>%;"></div>
            <div class="bar bar-warning" style="width: <?php echo 100-$statsArray['gameMasterActivePercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->master_crashed;?></strong><span class="pull-right"><?php echo $statsArray['gameMasterCrashedPercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-danger" style="width: <?php echo $statsArray['gameMasterCrashedPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['gameMasterCrashedPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->master_installed_server;?></strong><span class="pull-right"><?php echo $statsArray['gameMasterServerPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['gameMasterServerPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['gameMasterServerPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->master_installed_slots;?></strong><span class="pull-right"><?php echo $statsArray['gameMasterSlotsPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['gameMasterSlotsPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['gameMasterSlotsPercent'];?>%;"></div>
        </div>
    </div>
    <?php }?>
    <?php if($easywiModules['gs'] and $pa['gserver'] and $easywiModules['gs']){ ?>
    <div class="span3">
        <h5><a href="admin.php?w=gs&d=md"><?php echo $statsArray['gameserverInstalled'].' '.$gsprache->gameserver;?></a></h5>
        <strong><?php echo $gsprache->active;?></strong><span class="pull-right"><?php echo $statsArray['gameserverActivePercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-success" style="width: <?php echo $statsArray['gameserverActivePercent'];?>%;"></div>
            <div class="bar bar-warning" style="width: <?php echo 100-$statsArray['gameserverActivePercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->gserver_crashed;?></strong><span class="pull-right"><?php echo $statsArray['gameserverCrashedPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-danger" style="width: <?php echo  $statsArray['gameserverCrashedPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['gameserverCrashedPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->usage_slots;?></strong><span class="pull-right"><?php echo $statsArray['gameserverSlotsUsedPercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['gameserverSlotsUsedPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['gameserverSlotsUsedPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->gserver_tag_removed;?></strong><span class="pull-right"><?php echo $statsArray['gameserverTagPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-danger" style="width: <?php echo $statsArray['gameserverTagPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['gameserverTagPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->gserver_removed;?></strong><span class="pull-right"><?php echo $statsArray['gameserverPasswordPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-danger" style="width: <?php echo $statsArray['gameserverPasswordPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['gameserverPasswordPercent'];?>%;"></div>
        </div>
    </div>
    <?php }?>
    <?php if($pa['voicemasterserver'] and $easywiModules['vo']) { ?>
    <div class="span3">
        <h5><a href="admin.php?w=vm"><?php echo $statsArray['voiceMasterInstalled'].' '.$gsprache->voiceserver.' '.$gsprache->master;?></a></h5>
        <strong><?php echo $gsprache->active;?></strong><span class="pull-right"><?php echo $statsArray['voiceMasterActivePercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-success" style="width: <?php echo $statsArray['voiceMasterActivePercent'];?>%;"></div>
            <div class="bar bar-warning" style="width: <?php echo 100-$statsArray['voiceMasterActivePercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->ts3master_crashed?></strong><span class="pull-right"><?php echo $statsArray['voiceMasterCrashedPercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-danger" style="width: <?php echo $statsArray['voiceMasterCrashedPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['voiceMasterCrashedPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->master_installed_server;?></strong><span class="pull-right"><?php echo $statsArray['voiceMasterServerPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['voiceMasterServerPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['voiceMasterServerPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->master_installed_slots;?></strong><span class="pull-right"><?php echo $statsArray['voiceMasterSlotsPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['voiceMasterSlotsPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['voiceMasterSlotsPercent'];?>%;"></div>
        </div>
    </div>
    <?php }?>
    <?php if($pa['voiceserver'] and $easywiModules['vo']) { ?>
    <div class="span3">
        <h5><a href="admin.php?w=vo"><?php echo $statsArray['voiceserverInstalled'].' '.$gsprache->voiceserver;?></a></h5>
        <strong><?php echo $gsprache->active;?></strong><span class="pull-right"><?php echo $statsArray['voiceserverActivePercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-success" style="width: <?php echo $statsArray['voiceserverActivePercent'];?>%;"></div>
            <div class="bar bar-warning" style="width: <?php echo 100-$statsArray['voiceserverActivePercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->voice_crashed;?></strong><span class="pull-right"><?php echo $statsArray['voiceserverCrashedPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-danger" style="width: <?php echo  $statsArray['voiceserverCrashedPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['voiceserverCrashedPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->usage_slots;?></strong><span class="pull-right"><?php echo $statsArray['voiceserverSlotsUsedPercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['voiceserverSlotsUsedPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['voiceserverSlotsUsedPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->usage_traffic;?></strong><span class="pull-right"><?php echo $statsArray['voiceserverTrafficPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['voiceserverTrafficPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['voiceserverTrafficPercent'];?>%;"></div>
        </div>
    </div>
    <?php }?>
</div>
<div class="row-fluid">
    <?php if($easywiModules['ws'] and $pa['webmaster']) { ?>
    <div class="span3">
        <h5><a href="admin.php?w=wm"><?php echo $statsArray['webMasterInstalled'].' '.$gsprache->webspace.' '.$gsprache->master;?></a></h5>
        <strong><?php echo $gsprache->active;?></strong><span class="pull-right"><?php echo $statsArray['webMasterActivePercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-success" style="width: <?php echo $statsArray['webMasterActivePercent'];?>%;"></div>
            <div class="bar bar-warning" style="width: <?php echo 100-$statsArray['webMasterActivePercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->master_installed_vhosts;?></strong><span class="pull-right"><?php echo $statsArray['webMasterVhostPercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['webMasterVhostPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['webMasterVhostPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->master_installed_space;?></strong><span class="pull-right"><?php echo $statsArray['webMasterSpaceUsedPercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['webMasterSpaceUsedPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['webMasterSpaceUsedPercent'];?>%;"></div>
        </div>
    </div>
    <?php }?>
    <?php if($easywiModules['ws'] and $pa['webvhost']) { ?>
    <div class="span3">
        <h5><a href="admin.php?w=wv"><?php echo $statsArray['webspaceInstalled'].' '.$gsprache->webspace;?></a></h5>
        <strong><?php echo $gsprache->active;?></strong><span class="pull-right"><?php echo $statsArray['webspaceActivePercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-success" style="width: <?php echo $statsArray['webspaceActivePercent'];?>%;"></div>
            <div class="bar bar-warning" style="width: <?php echo 100-$statsArray['webspaceActivePercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->usage_space;?></strong><span class="pull-right"><?php echo $statsArray['webspaceSpaceUsedPercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['webspaceSpaceUsedPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['webspaceSpaceUsedPercent'];?>%;"></div>
        </div>
    </div>
    <?php }?>
    <?php if($easywiModules['my'] and $pa['mysql_settings']) { ?>
    <div class="span3">
        <h5><a href="admin.php?w=ms"><?php echo $statsArray['mysqlMasterInstalled'].' MySQL '.$gsprache->master;?></a></h5>
        <strong><?php echo $gsprache->active;?></strong><span class="pull-right"><?php echo $statsArray['mysqlMasterActivePercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-success" style="width: <?php echo $statsArray['mysqlMasterActivePercent'];?>%;"></div>
            <div class="bar bar-warning" style="width: <?php echo 100-$statsArray['mysqlMasterActivePercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->master_installed_db;?></strong><span class="pull-right"><?php echo $statsArray['mysqlMasterDBPercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-warning" style="width: <?php echo $statsArray['mysqlMasterDBPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['mysqlMasterDBPercent'];?>%;"></div>
        </div>
    </div>
    <?php }?>
    <?php if($easywiModules['my'] and $pa['mysql']) { ?>
    <div class="span3">
        <h5><a href="admin.php?w=my&amp;d=my"><?php echo $statsArray['webspaceInstalled'].' '.$gsprache->databases;?></a></h5>
        <strong><?php echo $gsprache->active;?></strong><span class="pull-right"><?php echo $statsArray['mysqlActivePercent'];?>%</span>
        <div class="progress">
            <div class="bar bar-success" style="width: <?php echo $statsArray['mysqlActivePercent'];?>%;"></div>
            <div class="bar bar-warning" style="width: <?php echo 100-$statsArray['mysqlActivePercent'];?>%;"></div>
        </div>
    </div>
    <?php }?>
</div>
<div class="row-fluid">
    <?php if($pa['usertickets'] or $pa['tickets']) { ?>
    <div class="span3">
        <h5><a href="admin.php?w=tr"><?php echo $sprache_bad->tickets;?></a></h5>
        <strong><?php echo $sprache_bad->tickets_new;?></strong><span class="pull-right"><?php echo $statsArray['ticketsNewPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-danger" style="width: <?php echo $statsArray['ticketsNewPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['ticketsNewPercent'];?>%;"></div>
        </div>
        <strong><?php echo $sprache_bad->tickets_open;?></strong><span class="pull-right"><?php echo $statsArray['ticketsPercent'];?>%</span>
        <div class="progress progress-danger active">
            <div class="bar bar-danger" style="width: <?php echo $statsArray['ticketsPercent'];?>%;"></div>
            <div class="bar bar-success" style="width: <?php echo 100-$statsArray['ticketsPercent'];?>%;"></div>
        </div>
    </div>
    <?php }?>
</div>

<?php if(count($feedArray)>0) echo '<hr>';?>
<?php foreach ($feedArray as $url => $array) { ?>
<?php foreach ($array as $feed) { ?>
<div class="row-fluid">
    <h4><a href="<?php echo $feed['link'];?>" target="_blank"><?php echo $feed['title'];?></a></h4>
    <div class="span11">
        <?php echo $feed['text'];?>
    </div>
</div>
<?php } ?>
<hr>
<?php } ?>