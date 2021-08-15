<section class="content-header">
    <h4>Dashboard</h4>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
    </ol>
</section>

<section class="content">
 <div class="row">
    <?php if($easywiModules['gs'] and $pa['roots'] and $statsArray['gameMasterInstalled']>0) { ?>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->appRoot;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['gameMasterActive'].'/'.$statsArray['gameMasterInstalled']; echo" $gsprache->active";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-server fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameMasterActivePercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['gameMasterActivePercent'].'% '. $gsprache->active;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->appRoot;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['gameMasterCrashed'].'/'.$statsArray['gameMasterActive']; echo" $sprache_bad->crashed";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-heartbeat fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameMasterCrashedPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['gameMasterCrashedPercent'].'% '.$sprache_bad->crashed;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->appRoot;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['gameserverInstalled'].'/'.$statsArray['gameMasterServerAvailable']; echo" $sprache_bad->master_installed_server";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-signal fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameMasterServerPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['gameMasterServerPercent'].'% '. $sprache_bad->master_installed_server;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->appRoot;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['gameserverSlotsInstalled'].'/'.$statsArray['gameMasterSlotsAvailable'];echo" $sprache_bad->master_installed_slots";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-signal fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameMasterSlotsPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['gameMasterSlotsPercent'].'% '. $sprache_bad->master_installed_slots;?></div>
                </div>
            </div>
        </div>
    </div>
</div>
 <?php }?>

    <?php if($easywiModules['gs'] and $pa['gserver'] and $statsArray['gameserverInstalled']>0){ ?>
        <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->gameserver;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['gameserverActive'].'/'.$statsArray['gameserverInstalled']; echo" $gsprache->active";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-gamepad fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameserverActivePercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['gameserverActivePercent'].'% '. $gsprache->active;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->gameserver;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['gameserverNotRunning'].'/'.$statsArray['gameserverActive']; echo" $sprache_bad->crashed";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-heartbeat fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameserverCrashedPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['gameserverCrashedPercent'].'% '.$sprache_bad->crashed;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->gameserver;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo ($statsArray['gameserverNoTag'] + $statsArray['gameserverNoPassword']).'/'.$statsArray['gameserverActive']; echo" $sprache_bad->rulebreak";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-gavel fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameserverRuleBreakPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['gameserverRuleBreakPercent'].'% '. $sprache_bad->rulebreak;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->gameserver;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['gameserverSlotsUsed'].'/'.$statsArray['gameserverSlotsActive'];echo" $sprache_bad->usage_slots";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-plug fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameserverSlotsUsedPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['gameserverSlotsUsedPercent'].'% '. $sprache_bad->usage_slots;?></div>
                </div>
            </div>
        </div>
    </div>
</div>

  <?php }?>
<?php if($easywiModules['vo'] and $pa['voicemasterserver'] and $statsArray['voiceMasterInstalled']>0) { ?>

          <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->voiceserver.' '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['voiceMasterActive'].'/'.$statsArray['voiceMasterInstalled']; echo" $gsprache->active";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-server fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceMasterActivePercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['voiceMasterActivePercent'].'% '. $gsprache->active;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->voiceserver.' '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['voiceMasterCrashed'].'/'.$statsArray['voiceMasterActive']; echo" $sprache_bad->crashed";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-heartbeat fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceMasterCrashedPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['voiceMasterCrashedPercent'].'% '.$sprache_bad->crashed;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->voiceserver.' '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['voiceserverInstalled'].'/'.$statsArray['voiceMasterServerAvailable']; echo" $sprache_bad->master_installed_server";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-signal fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceMasterServerPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['voiceMasterServerPercent'].'% '. $sprache_bad->master_installed_server;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->voiceserver.' '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['voiceserverSlotsInstalled'].'/'.$statsArray['voiceMasterSlotsAvailable'];echo" $sprache_bad->master_installed_slots";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-signal fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceMasterSlotsPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['voiceMasterSlotsPercent'].'% '. $sprache_bad->master_installed_slots;?></div>
                </div>
            </div>
        </div>
    </div>
</div>
   <?php }?>

    <?php if($easywiModules['vo'] and $pa['voiceserver'] and $statsArray['voiceserverInstalled']>0) { ?>
            <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->voiceserver;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['voiceserverActive'].'/'.$statsArray['voiceserverInstalled']; echo" $gsprache->active";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-microphone fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceserverActivePercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['voiceserverActivePercent'].'% '. $gsprache->active;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->voiceserver;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['voiceserverCrashed'].'/'.$statsArray['voiceserverActive']; echo" $sprache_bad->crashed";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-heartbeat fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceserverCrashedPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['voiceserverCrashedPercent'].'% '.$sprache_bad->crashed;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->voiceserver;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['voiceserverTrafficUsed'].'/'.$statsArray['voiceserverTrafficAllowed']; echo" $sprache_bad->traffic";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-signal fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceserverTrafficPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['voiceserverTrafficPercent'].'% '. $sprache_bad->traffic;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->voiceserver;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['voiceserverSlotsUsed'].'/'.$statsArray['voiceserverSlotsActive'];echo" $sprache_bad->usage_slots";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-plug fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceserverSlotsUsedPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['voiceserverSlotsUsedPercent'].'% '. $sprache_bad->usage_slots;?></div>
                </div>
            </div>
        </div>
    </div>
</div>
   <?php }?>
       <?php if($easywiModules['ws'] and ($pa['webvhost'] or $pa['webmaster']) and $statsArray['webMasterInstalled']>0) { ?>
                   <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->webspace.' '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['webMasterActive'].'/'.$statsArray['webMasterInstalled']; echo" $gsprache->active";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-server fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['webMasterActivePercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['webMasterActivePercent'].'% '. $gsprache->active;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->webspace.' '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['webMasterCrashed'].'/'.$statsArray['webMasterActive']; echo" $sprache_bad->crashed";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-heartbeat fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['webMasterCrashedPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['webMasterCrashedPercent'].'% '.$sprache_bad->crashed;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->webspace.' '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['webspaceInstalled'].'/'.$statsArray['webMasterVhostAvailable']; echo" $sprache_bad->master_installed_vhosts";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-signal fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['webMasterVhostPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['webMasterVhostPercent'].'% '. $sprache_bad->master_installed_vhosts;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo $gsprache->webspace.' '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['webspaceSpaceGiven'].'/'.$statsArray['webMasterSpaceAvailable'];echo" $sprache_bad->master_installed_space";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-signal fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['webMasterSpaceUsedPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['webMasterSpaceUsedPercent'].'% '. $sprache_bad->master_installed_space;?></div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php }?>

    <?php if($easywiModules['my'] and ($pa['mysql'] or $pa['mysql_settings']) and $statsArray['mysqlMasterInstalled']>0) { ?>
                     <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo 'MySQL '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['mysqlMasterActive'].'/'.$statsArray['mysqlMasterInstalled']; echo" $gsprache->active";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-server fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['mysqlMasterActivePercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['mysqlMasterActivePercent'].'% '. $gsprache->active;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo 'MySQL '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['mysqlMasterCrashed'].'/'.$statsArray['mysqlMasterActive']; echo" $sprache_bad->crashed";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-heartbeat fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['mysqlMasterCrashedPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['mysqlMasterCrashedPercent'].'% '.$sprache_bad->crashed;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo 'MySQL '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['mysqlDBInstalled'].'/'.$statsArray['mysqlMasterDBAvailable']; echo" $sprache_bad->master_installed_db";?> 
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-signal fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['mysqlMasterDBPercent'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['mysqlMasterDBPercent'].'% '. $sprache_bad->master_installed_db;?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                    <div class="text-sm font-weight-bold text-primary text-uppercase mb-2"> <?php echo 'MySQL '.$gsprache->master;?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo $statsArray['mysqlDBSpaceUsed'];echo" MB";?>  
                        </div>
                    </div>
                    <div class="col-auto">
                      <i class="fa fa-signal fa-3x text-gray-400"></i>
                    </div>
                    </div>
                    <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['mysqlDBSpaceUsed'];?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $statsArray['mysqlDBSpaceUsed'].'MB';?></div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php }?>
	
	
	<?php if(isset($easywitweets) && $easywitweets) { ?>
	<div class="row">
        <div class="col-md-8">
           <ul class="timeline">
                <div class="card shadow mb-4">
                <div class="card-header py-3">
					<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
				 <a style="width:100%" class="twitter-timeline" href="https://twitter.com/easy_wi?ref_src=twsrc^tfw">Tweets by easy_wi</a>
                </div>            
              	</div>
            </ul>
        </div>
    </div>
    <?php } ?>
     <?php if(count($feedArray)>0) { ?>
    <hr>
    <?php foreach ($feedArray as $url => $array) { ?>
    <div class="row">
        <div class="col-md-12">

            <h3><?php echo $url;?></h3>

            <ul class="timeline">
                <?php $lastdate=0;?>
                <?php foreach ($array as $feed) { ;?>


                <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h4 class="m-0 font-weight-bold text-primary"><?php echo $feed['title'];?></h4>
                  <div class="dropdown-menu-right" >
                  <?php if($lastdate!=$feed['date']){ ?>
                <span><i class="far fa-calendar-alt"></i> <?php echo $feed['date'];?></span>
                <span><i class="fa fa-clock-o"></i> <?php echo $feed['time'];?></span></div>
                <?php };?>
                <?php $lastdate=$feed['date'];?>
                </div>            
                <div class="card-body">
                  <?php echo $feed['text'];?>
                </div>
                    <div>
                    <a class="btn btn-primary btn-xs" href="<?php echo $feed['link'];?>" target="_blank">Read more</a>
                    </div>
              </div>
        <?php } ?>
            </ul>
        </div>
    </div>
    <?php } ?>
    <?php }?>
    </section>
