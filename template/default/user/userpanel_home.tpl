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

        <div class="col-md-4 col-sm-12 col-xs-12">
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

        <div class="col-md-4 col-sm-12 col-xs-12">
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

        <div class="col-md-4 col-sm-12 col-xs-12">
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

    <?php if($easywiModules['vo'] and ($voicecount>0) and $pa['voiceserver']) { ?>
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12">
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

        <div class="col-md-4 col-sm-12 col-xs-12">
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

        <div class="col-md-4 col-sm-12 col-xs-12">
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