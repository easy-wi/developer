<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_list;?>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php foreach ($table as $table_row){ ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel box <?php if($table_row['img']=='16_bad') echo 'box-warning'; else if($table_row['img']=='16_error') echo 'box-danger'; else echo 'box-success';?>">

                <div class="box-header">
                    <h3 class="box-title"><img src="images/games/icons/<?php echo $table_row['gameShorten'];?>.png" alt="<?php echo $table_row['gameShorten'];?>" width="18"> <a href="hlsw://<?php echo $table_row['server'];?>"><?php echo $table_row['server'].' '.$table_row['name'];?></a></h3>
                </div>

                <div class="box-body">
                    <?php if(!empty($table_row['premoved'])){ ?><div class="alert alert-danger alert-dismissable"><i class="fa fa-warning"></i> <?php echo $table_row['premoved'];?></div><?php } ?>
                    <?php if(!empty($table_row['nameremoved'])){ ?><div class="alert alert-danger alert-dismissable"><i class="fa fa-warning"></i> <?php echo $table_row['nameremoved'];?></div><?php } ?>
                    <div class="form-group">
                        <a href="userpanel.php?w=gs&amp;d=rs&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onclick="return confirm('<?php echo $table_row['server'];?>: <?php echo $sprache->confirm_restart;?>');"><button class="btn btn-sm btn-success inline"><i class="icon-white icon-play"></i> <?php echo $sprache->restarts;?></button></a>
                        <?php if($table_row['stopped']=='N'){ ?><a href="userpanel.php?w=gs&amp;d=st&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onclick="return confirm('<?php echo $table_row['server'];?>: <?php echo $sprache->confirm_stop;?>');"><button class="btn btn-sm btn-danger"><i class="fa fa-power-off"></i> <?php echo $sprache->stop;?></button></a><?php } ?>
                        <?php if(!empty($table_row['pro'])){ ?><a href="userpanel.php?w=pr&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onclick="return confirm('<?php echo $table_row['server'];?>: <?php echo $sprache->protect . ' '; echo ($table_row['imgp']=='16_protected') ? $sprache->off2 : $sprache->on;?>?');"><button class="btn btn-sm <?php if($table_row['imgp']=='16_protected')echo 'btn-info';else if($table_row['imgp']=='16_unprotected') echo 'btn-warning';?>"><i class="fa fa-shield"></i> <?php echo $sprache->protect.' '.$table_row['pro'];?></button></a><?php } ?>
                        <a href="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-sm btn-primary"><i class="fa fa-cogs"></i> <?php echo $sprache->config;?></button></a>
                        <?php if(($pa['ftpaccess'] or $pa['miniroot']) and $table_row['imgp']!='16_protected' and $table_row['ftpAllowed']) { ?>
                        <a href="userpanel.php?w=gs&amp;d=wf&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-sm btn-primary"><i class="fa fa-files-o"></i> <?php echo $sprache->webFtp;?></button></a>
                        <?php } ?>
                        <?php if($pa['useraddons']){ ?><a href="userpanel.php?w=ao&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-sm btn-primary"><i class="fa fa-puzzle-piece"></i> 				<?php echo $gsprache->addon;?></button></a><?php } ?>
                        <a href="userpanel.php?w=ca&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-sm btn-primary"><i class="fa fa-calendar"></i> <?php echo $sprache->restarttime;?></button></a>
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-sm btn-primary dropdown-toggle"><i class="fa fa-floppy-o"></i> <?php echo $gsprache->backup;?> <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="userpanel.php?w=bu&amp;id=<?php echo $table_row['id'];?>&amp;action=mb&amp;r=gs"><i class="fa fa-plus-circle fa-fw"></i> <?php echo $sprache->create;?></a></li>
                                <li><a href="userpanel.php?w=bu&amp;id=<?php echo $table_row['id'];?>&amp;action=rb"><i class="fa fa-refresh fa-fw"></i> <?php echo $sprache->recover;?></a></li>
                                <li><a href="userpanel.php?w=bu&amp;id=<?php echo $table_row['id'];?>&amp;action=md"><i class="fa fa-cog fa-fw"></i> <?php echo $gsprache->settings;?></a></li>
                            </ul>
                        </div>

                        <a class="btn btn-sm btn-primary" data-toggle="modal" data-target="#compose-modal-<?php echo $table_row['id'];?>"><i class="fa fa-terminal"></i> <?php echo $gsprache->logs;?></a>

                        <div class="modal fade" id="compose-modal-<?php echo $table_row['id'];?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" id="modal-content-<?php echo $table_row['id'];?>">
                                </div>
                            </div>
                        </div>
                        <a href="userpanel.php?w=gs&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-sm btn-primary"><i class="fa fa-cog"></i> <?php echo $gsprache->settings;?></button></a>
                        <a href="userpanel.php?w=gs&amp;d=ri&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-sm btn-warning"><i class="fa fa-refresh"></i> <?php echo $sprache->reinstall;?></button></a>
                        <?php if($table_row['upload']==true){ ?><a href="userpanel.php?w=gs&amp;d=du&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');"><button class="btn btn-sm btn-info"><i class="icon-white icon-film"></i> SourceTV</button></a><?php } ?>
                    </div>

                    <dl class="dl-horizontal">
                        <dt><?php echo $gsprache->template;?></dt>
                        <dd><?php echo $table_row['shorten'];?></dd>
                        <dt><?php echo $sprache->updatetime;?></dt>
                        <dd><?php echo $table_row['updatetime'];?></dd>
                        <dt><?php echo $sprache->map;?></dt>
                        <dd><?php echo $table_row['map'];?></dd>
                        <dt><?php echo $sprache->player;?></dt>
                        <dd><?php echo $table_row['numplayers']?>/<?php echo $table_row['maxplayers'];?></dd>
                        <?php if($table_row['ramLimited']=='Y') { ?>
                        <dt><?php echo $sprache->ramMax;?></dt>
                        <dd><?php echo $table_row['maxram'];?> MB</dd>
                        <?php } ?>
                        <?php if($table_row['taskset']=='Y' and $table_row['coreCount']>0) { ?>
                        <dt><?php echo $sprache->coreBind;?></dt>
                        <dd><?php echo $table_row['cores'].' ('.$table_row['coreCount'].')';?></dd>
                        <?php } ?>
                        <?php if(($pa['ftpaccess'] or $pa['miniroot']) and $table_row['imgp']!='16_protected' and $table_row['ftpAllowed']) { ?>
                        <dt><?php echo $sprache->ftp_link;?></dt>
                        <dd><a href="<?php echo $table_row['ftpdata'];?>"><?php echo $table_row['ftpdata'];?></a></dd>
                        <dt><?php echo $sprache->ftp_adresse;?></dt>
                        <dd>ftp://<?php echo $table_row['ip'].":".$table_row['ftpport'].$pserver.$table_row['ip'].'_'.$table_row['port']."/".$table_row['shorten'];?></dd>
                        <dt><?php echo $sprache->ftp_user;?></dt>
                        <dd><?php echo $table_row['cname'];?></dd>
                        <dt><?php echo $sprache->ftp_password;?></dt>
                        <dd><?php echo $table_row['cftppass'];?></dd>
                        <?php } ?>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <script type='text/javascript'>$('#compose-modal-<?php echo $table_row['id'];?>').on('show.bs.modal',function(){ $('#modal-content-<?php echo $table_row['id'];?>').load('serverlog.php?id=<?php echo $table_row['id'];?>');});</script>
    <?php }?>
</section>