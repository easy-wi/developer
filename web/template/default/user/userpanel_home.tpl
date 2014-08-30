<section class="content-header">
    <h1>Dashboard</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $gsprache->help_home;?>
            </div>
        </div>
    </div>

    <!-- Main row -->
    <div class="row">

        <?php if($easywiModules['ti'] and $pa['usertickets']) { ?>
        <section class="col-md-4">
            <div class="box box-info" id="loading-example">
                <div class="box-header">
                    <i class="fa fa-support"></i>
                    <h3 class="box-title"><?php echo $gsprache->support;?></h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-xs-6 text-center" style="border-right: 1px solid #f4f4f4">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['ticketsNew'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['ticketsTotal'];?>" data-fgColor="#00a65a">
                            <div class="knob-label"><?php echo $statsArray['ticketsNew'].'/'.$statsArray['ticketsTotal'];?> <?php echo $sprache_bad->tickets_new;?></div>
                        </div><!-- ./col -->
                        <div class="col-xs-6 text-center">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['ticketsInProcess'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['ticketsTotal'];?>" data-fgColor="#3c8dbc">
                            <div class="knob-label"><?php echo $statsArray['ticketsInProcess'].'/'.$statsArray['ticketsTotal'];?> <?php echo $sprache_bad->tickets_open;?></div>
                        </div><!-- ./col -->
                    </div><!-- /.row - inside box -->
                </div><!-- /.box-body -->
            </div>
        </section>
        <?php } ?>

        <?php if($easywiModules['ws'] and $vhostcount>0 and $pa['webvhost']) { ?>
        <section class="col-md-3">
            <div class="box box-info" id="loading-example">
                <div class="box-header">
                    <i class="fa fa-cubes"></i>

                    <h3 class="box-title"><?php echo $gsprache->webspace;?></h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['webspaceSpaceUsed'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['webspaceSpaceGivenActive'];?>" data-fgColor="#00a65a">
                            <div class="knob-label"><?php echo $statsArray['webspaceSpaceUsed'].'/'.$statsArray['webspaceSpaceGivenActive'];?> MB</div>
                        </div>
                    </div><!-- /.row - inside box -->
                </div><!-- /.box-body -->
            </div>
        </section>
        <?php } ?>

        <?php if($easywiModules['my'] and $dbcount>0 and ($pa['mysql'] or $pa['mysql'])) { ?>
        <section class="col-md-3">
            <div class="box box-info" id="loading-example">
                <div class="box-header">
                    <i class="fa fa-database"></i>

                    <h3 class="box-title">MySQL</h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['mysqlDBSpaceUsed'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['mysqlDBSpaceUsed'];?>" data-fgColor="#00a65a">
                            <div class="knob-label"><?php echo $statsArray['mysqlDBSpaceUsed'];?> MB</div>
                        </div>
                    </div><!-- /.row - inside box -->
                </div><!-- /.box-body -->
            </div>
        </section>
        <?php } ?>

        <?php if($easywiModules['gs'] and $gscount>0 and $pa['restart']) { ?>
        <section class="col-md-4">
            <!-- Box (with bar chart) -->
            <div class="box box-info" id="loading-example">
                <div class="box-header">
                    <i class="fa fa-gamepad"></i>
                    <h3 class="box-title"><?php echo $gsprache->gameserver;?></h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['gameserverSlotsUsed'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['gameserverSlotsActive'];?>" data-fgColor="#00a65a">
                            <div class="knob-label"><?php echo $statsArray['gameserverSlotsUsed'].'/'.$statsArray['gameserverSlotsActive'];?> <?php echo $sprache_bad->usage_slots;?></div>
                        </div><!-- ./col -->
                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['gameserverNoTag'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['gameserverActive'];?>" data-fgColor="#f39c12">
                            <div class="knob-label"><?php echo ($statsArray['gameserverNoTag'] + $statsArray['gameserverNoPassword']).'/'.$statsArray['gameserverActive'];?> Rulebreak</div>
                        </div><!-- ./col -->
                        <div class="col-xs-4 text-center">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['gameserverNotRunning'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['gameserverActive'];?>" data-fgColor="#f56954">
                            <div class="knob-label"><?php echo $statsArray['gameserverNotRunning'].'/'.$statsArray['gameserverActive'];?> <?php echo $sprache_bad->crashed;?></div>
                        </div><!-- ./col -->
                    </div><!-- /.row - inside box -->
                </div><!-- /.box-body -->
            </div>
        </section>
        <?php } ?>

        <?php if($easywiModules['vo'] and ($voicecount>0) and $pa['voiceserver']) { ?>
        <section class="col-md-4">
            <div class="box box-info" id="loading-example">
                <div class="box-header">
                    <i class="fa fa-microphone"></i>
                    <h3 class="box-title"><?php echo $gsprache->voiceserver;?></h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['voiceserverSlotsUsed'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['voiceserverSlotsActive'];?>" data-fgColor="#00a65a">
                            <div class="knob-label"><?php echo $statsArray['voiceserverSlotsUsed'].'/'.$statsArray['voiceserverSlotsActive'];?> <?php echo $sprache_bad->usage_slots;?></div>
                        </div><!-- ./col -->
                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['voiceserverTrafficUsed'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['voiceserverTrafficAllowed'];?>" data-fgColor="#3c8dbc">
                            <div class="knob-label"><?php echo $statsArray['voiceserverTrafficUsed'].'/'.$statsArray['voiceserverTrafficAllowed'];?> <?php echo $sprache_bad->traffic;?></div>
                        </div><!-- ./col -->
                        <div class="col-xs-4 text-center">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['voiceserverCrashed'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['voiceserverActive'];?>" data-fgColor="#f56954">
                            <div class="knob-label"><?php echo $statsArray['voiceserverCrashed'].'/'.$statsArray['voiceserverActive'];?> <?php echo $sprache_bad->crashed;?></div>
                        </div><!-- ./col -->
                    </div><!-- /.row - inside box -->
                </div><!-- /.box-body -->
            </div>
        </section>
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
                    <!-- timeline time label -->
                    <li class="time-label"><span class="bg-green"><?php echo $feed['date'];?></span></li>
                    <!-- /.timeline-label -->
                    <?php }; $lastdate=$feed['date'];?>

                    <!-- timeline item -->
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

                    <!-- END timeline item -->
                    <li>
                        <i class="fa fa-clock-o"></i>
                    </li>
                <?php } ?>
           </ul>
        </div>
    </div>
    <?php }?>
</section><!-- /.content -->
