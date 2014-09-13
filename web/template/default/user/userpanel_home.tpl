<section class="content-header">
    <h1>Dashboard</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>

<!-- Main content -->
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

    <!-- Main row -->
    <div class="row">

        <?php if($easywiModules['ti'] and $pa['usertickets']) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        <?php echo $gsprache->support;?>
                    </h3>
                    <p>
                        <?php echo $statsArray['ticketsInProcess'].'/'.$statsArray['ticketsTotal'];?> <?php echo $sprache_bad->tickets_open;?><br><br><br>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-support"></i>
                </div>
                <a href="userpanel.php?w=ti" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php } ?>

        <?php if($easywiModules['ws'] and $vhostcount>0 and $pa['webvhost']) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        <?php echo $gsprache->webspace;?>
                    </h3>
                    <p>
                        <?php echo $statsArray['webspaceSpaceUsed'].'/'.$statsArray['webspaceSpaceGivenActive'];?> MB<br><br><br>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-cubes"></i>
                </div>
                <a href="userpanel.php?w=wv" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php } ?>

        <?php if($easywiModules['my'] and $dbcount>0 and ($pa['mysql'] or $pa['mysql'])) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        MySQL
                    </h3>
                    <p>
                        <?php echo $statsArray['mysqlDBSpaceUsed'];?> MB<br><br><br>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-database"></i>
                </div>
                <a href="userpanel.php?w=my" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php } ?>

        <?php if($easywiModules['gs'] and $gscount>0 and $pa['restart']) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        <?php echo $gsprache->gameserver;?>
                    </h3>
                    <p>
                        <?php echo $statsArray['gameserverSlotsUsed'].'/'.$statsArray['gameserverSlotsActive'];?> <?php echo $sprache_bad->usage_slots;?><br>
                        <?php echo $statsArray['gameserverNotRunning'].'/'.$statsArray['gameserverActive'];?> <?php echo $sprache_bad->crashed;?><br>
                        <?php echo ($statsArray['gameserverNoTag'] + $statsArray['gameserverNoPassword']).'/'.$statsArray['gameserverActive'];?> <?php echo $sprache_bad->rulebreak;?>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-gamepad"></i>
                </div>
                <a href="userpanel.php?w=gs" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php } ?>

        <?php if($easywiModules['vo'] and ($voicecount>0) and $pa['voiceserver']) { ?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>
                        <?php echo $gsprache->voiceserver;?>
                    </h3>
                    <p>
                        <?php echo $statsArray['voiceserverSlotsUsed'].'/'.$statsArray['voiceserverSlotsActive'];?> <?php echo $sprache_bad->usage_slots;?><br>
                        <?php echo $statsArray['voiceserverCrashed'].'/'.$statsArray['voiceserverActive'];?> <?php echo $sprache_bad->crashed;?><br>
                        <?php echo $statsArray['voiceserverTrafficUsed'].'/'.$statsArray['voiceserverTrafficAllowed'];?> <?php echo $sprache_bad->traffic;?>
                    </p>
                </div>
                <div class="icon">
                    <i class="fa fa-microphone"></i>
                </div>
                <a href="userpanel.php?w=vo" class="small-box-footer">
                    <?php echo $gsprache->moreInfo;?> <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <?php } ?>
    </div>

    <?php if(count($feedArray)>0) { ; echo '<hr>';?>
    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <!-- The time line -->
            <ul class="timeline">


                <?php foreach ($feedArray as $url => $array) { ?>
                    <?php foreach ($array as $feed) { ?>

                    <?php if($lastdate!=$feed['date']){ ?>

                    <li class="time-label"><span class="bg-green"><?php echo $feed['date'];?></span></li>

                    <?php }; $lastdate=$feed['date'];?>


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
                <?php } ?>
           </ul>
        </div>
    </div>
    <?php }?>
</section><!-- /.content -->
