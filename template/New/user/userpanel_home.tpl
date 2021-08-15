<section class="content-header">
    <h1>Dashboard</h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
    </ol>
</section>
<section class="content">

    <?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $gsprache->help_home;?>
            </div>
        </div>
    </div>
    <?php } ?>
<?php if($easywiModules['gs'] and $gscount>0 and $pa['restart']) { ?>
    <div class="row">
                <div class="col-lg-4 mb-4">
                  <div class="card bg-danger text-white shadow">
                    <div class="progress" style="height: 2px;">
  <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameserverCrashedPercent'];?>%"> aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
</div>


<div class="row">
    <div class="col-lg-2 mb-2"><i style="padding-top: 10px;padding-left: 5px;" class="fa fa-heartbeat fa-5x"></i></div>
    <div class="col-lg-10 mb-10">
                    <div class="card-body">
                    <h5><?php echo $gsprache->gameserver;?> - Heartbeat</h5>
                      <div class="text-white-50 small"><?php echo $statsArray['gameserverNotRunning'].'/'.$statsArray['gameserverActive'];?></div>
                      <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo $statsArray['gameserverCrashedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameserverCrashedPercent'].'% '.$sprache_bad->crashed;?>
                    </span>
                    </div>
                  </div>
                </div></div></div>

                <div class="col-lg-4 mb-4">
                  <div class="card bg-warning text-white shadow">
                    <div class="progress" style="height: 2px;">
  <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameserverRuleBreakPercent'];?>%"> aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
</div>


<div class="row">
    <div class="col-lg-2 mb-2"><i style="padding-top: 10px;padding-left: 5px;" class="fa fa-gavel fa-5x"></i></div>
    <div class="col-lg-10 mb-10">
                    <div class="card-body">
                    <h5><?php echo $gsprache->gameserver;?> - Config violation</h5>
                      <div class="text-white-50 small"><?php echo ($statsArray['gameserverNoTag'] + $statsArray['gameserverNoPassword']).'/'.$statsArray['gameserverActive'];?></div>
                      <div class="progress">
                         <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo $statsArray['gameserverRuleBreakPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameserverRuleBreakPercent'].'% '.$sprache_bad->rulebreak;?>
                    </span>
                    </div>
                  </div>
                </div></div></div>

                <div class="col-lg-4 mb-4">
                  <div class="card bg-info text-white shadow">
                    <div class="progress" style="height: 2px;">
  <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['gameserverSlotsUsedPercent'];?>%"> aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div class="row">
    <div class="col-lg-2 mb-2"><i style="padding-top: 10px;padding-left: 5px;" class="fa fa-plug fa-5x"></i></div>
    <div class="col-lg-10 mb-10">
                    <div class="card-body">
                    <h5><?php echo $gsprache->gameserver;?> - Used Slots</h5>
                      <div class="text-white-50 small"><?php echo $statsArray['gameserverSlotsUsed'].'/'.$statsArray['gameserverSlotsActive'];?></div>
                      <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo $statsArray['gameserverSlotsUsedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['gameserverSlotsUsedPercent'].'% '.$sprache_bad->usage_slots;?>
                    </span>
                    </div>
                  </div>
                </div></div></div>
<?php }?>
  <?php if($easywiModules['vo'] and ($voicecount>0) and $pa['voiceserver']) { ?>

    <div class="row">
                <div class="col-lg-4 mb-4">
                  <div class="card bg-danger text-white shadow">
                    <div class="progress" style="height: 2px;">
  <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceserverCrashedPercent'];?>%"> aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div class="row">
    <div class="col-lg-2 mb-2"><i style="padding-top: 10px;padding-left: 5px;" class="fa fa-heartbeat fa-5x"></i></div>
    <div class="col-lg-10 mb-10">
                    <div class="card-body">
                    <h5><?php echo $gsprache->voiceserver;?> - Heartbeat</h5>
                      <div class="text-white-50 small"><?php echo $statsArray['voiceserverCrashed'].'/'.$statsArray['voiceserverActive'];?></div>
                      <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo $statsArray['voiceserverCrashedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceserverCrashedPercent'].'% '.$sprache_bad->crashed;?>
                    </span>
                    </div>
                  </div>
                </div></div></div>

                <div class="col-lg-4 mb-4">
                  <div class="card bg-warning text-white shadow">
                    <div class="progress" style="height: 2px;">
  <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceserverTrafficPercent'];?>%"> aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div class="row">
    <div class="col-lg-2 mb-2"><i style="padding-top: 10px;padding-left: 5px;" class="fa fa-signal fa-6x"></i></div>
    <div class="col-lg-10 mb-10">
                    <div class="card-body">
                    <h5><?php echo $gsprache->voiceserver;?> - Traffic</h5>
                      <div class="text-white-50 small"><?php echo $statsArray['voiceserverTrafficUsed'].'/'.$statsArray['voiceserverTrafficAllowed'];?></div>
                      <div class="progress">
                         <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo $statsArray['voiceserverTrafficPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceserverTrafficPercent'].'% '.$sprache_bad->traffic;?>
                    </span>
                    </div>
                  </div>
                </div></div></div>

                <div class="col-lg-4 mb-4">
                  <div class="card bg-info text-white shadow">
                    <div class="progress" style="height: 2px;">
  <div class="progress-bar" role="progressbar" style="width: <?php echo $statsArray['voiceserverSlotsUsedPercent'];?>%"> aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div class="row">
    <div class="col-lg-2 mb-2"><i style="padding-top: 10px;padding-left: 5px;" class="fa fa-plug fa-5x"></i></div>
    <div class="col-lg-10 mb-10">
                    <div class="card-body">
                    <h5><?php echo $gsprache->voiceserver;?> - Used Slots</h5>
                      <div class="text-white-50 small"><?php echo $statsArray['voiceserverSlotsUsed'].'/'.$statsArray['voiceserverSlotsActive'];?></div>
                      <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo $statsArray['voiceserverSlotsUsedPercent'];?>%"></div>
                    </div>
                    <span class="progress-description">
                        <?php echo $statsArray['voiceserverSlotsUsedPercent'].'% '.$sprache_bad->usage_slots;?>
                    </span>
                    </div>
                  </div>
                </div></div></div>


                 <?php } ?>

    <?php if(count($feedArray)>0) { ?>

    <hr>

    <?php foreach ($feedArray as $url => $array) { ?>
     <div class="col-md-12">

         <h2><?php echo $url;?></h2>

            <ul class="timeline">
                <?php $lastdate=0;?>
                <?php foreach ($array as $feed) { ;?>


                <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h5 class="m-0 font-weight-bold text-primary"><?php echo $feed['title'];?></h5>
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
