<section class="content-header">
   <h1><?php echo $gsprache->gameserver;?></h1>
   <ol class="breadcrumb">
      <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
      <li><a href="userpanel.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
      <li class="active">
         <?php echo $gsprache->overview;?>
      </li>
   </ol>
</section>
<?php if($userWantsHelpText=='Y'){ ?>
<section style="padding-left: 20px" class="content">
   <div class="row hidden-xs">
      <div class="col-md-12">
         <div class="alert alert-info alert-dismissable">
            <i class="fa fa-info"></i>
            <?php echo $sprache->help_list;?>
         </div>
      </div>
   </div>
</section>
<?php } ?>
<div class="container-fluid">
   <div class="row">
      <?php 
         $gsa = 0;
         foreach ($table as $table_row){ $gsa++;?>
      <div class="col-md-12">
         <div class="border panel box <?php if($table_row['img']=='16_bad') echo 'alert alert-warning'; else if($table_row['img']=='16_error') echo 'alert alert-danger '; else echo 'alert alert-success ';?>">
            <div class="box-header">
               <h3 class="box-title"><img height="30px" width="auto" src="images/games/icons/<?php echo $table_row['gameShorten'];?>.png" alt="<?php echo $table_row['gameShorten'];?>" width="18"> <?php if($table_row['gameShorten']=="arkse" || $table_row['gameShorten']=="arksoft"){ $port = $table_row['port']; echo $table_row['ip'].':'.++$port; } else { echo $table_row['server']; } echo ' '.$table_row['name'];?> <a class="badge float-right">Server <?php echo "$gsa" ?> </a></h3>
            </div>
            <div class="box-body">
               <?php if(!empty($table_row['premoved'])){ ?>
               <div class="alert alert-danger alert-dismissable"><i class="fa fa-warning"></i>
                  <?php echo $table_row['premoved'];?>
               </div>
               <?php } ?>
               <?php if(!empty($table_row['nameremoved'])){ ?>
               <div class="alert alert-danger alert-dismissable"><i class="fa fa-warning"></i>
                  <?php echo $table_row['nameremoved'];?>
               </div>
               <?php } ?>
               <section <?php if ($gsnavigation=='1' ){ echo 'style="padding-left: 20px"';} else { echo 'style="display:none!important"';} ?>>
                  <div class="form-group">
                     <a href="userpanel.php?w=gs&amp;d=rs&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onclick="return confirm('<?php echo $table_row['server'];?>: <?php echo $sprache->confirm_restart;?>');">
                     <button class="btn btn-outline-success inline"><i class="icon-white icon-play"></i>
                     <?php if ($table_row['stopped']=='N') {echo "Restart";} else {echo "Start";} ?>
                     </button>
                     </a>
                     <?php if($table_row['stopped']=='N'){ ?>
                     <a href="userpanel.php?w=gs&amp;d=st&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onclick="return confirm('<?php echo $table_row['server'];?>: <?php echo $sprache->confirm_stop;?>');">
                     <button class="btn btn-outline-danger"><i class="fa fa-power-off"></i>
                     <?php echo $sprache->stop;?>
                     </button>
                     </a>
                     <?php } ?>
                     <?php if(!empty($table_row['pro'])){ ?><a href="userpanel.php?w=pr&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onclick="return confirm('<?php echo $table_row['server'];?>: <?php echo $sprache->protect . ' '; echo ($table_row['imgp']=='16_protected') ? $sprache->off2 : $sprache->on;?>?');"><button class="btn btn-outline-<?php if($table_row['imgp']=='16_protected')echo 'info';else if($table_row['imgp']=='16_unprotected') echo 'warning';?>"><i class="fas fa-shield-alt"></i> <?php echo $sprache->protect.' '.$table_row['pro'];?></button></a>
                     <?php } ?>
                     <a href="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $table_row['id'];?>">
                     <button class="btn btn-outline-info"><i class="fa fa-cogs"></i>
                     <?php echo $sprache->config;?>
                     </button>
                     </a>
                     <?php if(($pa['ftpaccess'] or $pa['miniroot']) and $table_row['ftpAllowed']) { ?>
                     <a href="userpanel.php?w=gs&amp;d=wf&amp;id=<?php echo $table_row['id'];?>">
                     <button class="btn btn-outline-info"><i class="fa fa-files-o"></i>
                     <?php echo $sprache->webFtp;?>
                     </button>
                     </a>
                     <?php } ?>
                     <?php if($pa['useraddons']){ ?>
                     <a href="userpanel.php?w=ao&amp;id=<?php echo $table_row['id'];?>">
                     <button class="btn btn-outline-info"><i class="fa fa-puzzle-piece"></i>
                     <?php echo $gsprache->addon;?>
                     </button>
                     </a>
                     <?php } ?>
                     <a href="userpanel.php?w=ca&amp;id=<?php echo $table_row['id'];?>">
                     <button class="btn btn-outline-info"><i class="fa fa-calendar"></i>
                     <?php echo $sprache->restarttime;?>
                     </button>
                     </a>
                     <div class="btn-group">
                        <button data-toggle="dropdown" class="btn btn-outline-info dropdown-toggle"><i class="fa fa-floppy-o"></i>
                        <?php echo $gsprache->backup;?> <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                           <li><a href="userpanel.php?w=bu&amp;id=<?php echo $table_row['id'];?>&amp;action=mb&amp;r=gs"><i class="fa fa-plus-circle fa-fw"></i> <?php echo $sprache->create;?></a></li>
                           <li><a href="userpanel.php?w=bu&amp;id=<?php echo $table_row['id'];?>&amp;action=rb"><i class="fa fa-refresh fa-fw"></i> <?php echo $sprache->recover;?></a></li>
                           <li><a href="userpanel.php?w=bu&amp;id=<?php echo $table_row['id'];?>&amp;action=md"><i class="fa fa-cog fa-fw"></i> <?php echo $gsprache->settings;?></a></li>
                        </ul>
                     </div>
                     <a href="userpanel.php?w=gs&amp;d=sl&amp;id=<?php echo $table_row['id'];?>">
                     <button class="btn btn-outline-info"><i class="fa fa-terminal"></i>
                     <?php echo $imageSprache->liveConsole;?>
                     </button>
                     </a>
                     <a href="userpanel.php?w=gs&amp;d=md&amp;id=<?php echo $table_row['id'];?>">
                     <button class="btn btn-outline-info"><i class="fa fa-cog"></i>
                     <?php echo $gsprache->settings;?>
                     </button>
                     </a>
                     <a href="userpanel.php?w=gs&amp;d=ri&amp;id=<?php echo $table_row['id'];?>">
                     <button class="btn btn-outline-dark"><i class="fa fa-refresh"></i>
                     <?php echo $sprache->reinstall;?>
                     </button>
                     </a>
                     <?php if($table_row['upload']==true){ ?>
                     <a href="userpanel.php?w=gs&amp;d=du&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                     <button class="btn btn-sm btn-info"><i class="icon-white icon-film"></i> SourceTV</button>
                     </a>
                     <?php } ?>
               </section>
               </div>
               <section>
                  <style type="text/css">
                     .list-group-item {
                     height: 30px;
                     font-size: 14px;
                     border: 0px solid rgba(0, 0, 0, 0)!important;
                     background-color: <?php if($table_row['img']=='16_bad') echo 'rgba(255, 138, 0, 0.01)';
                        else if($table_row['img']=='16_error') echo 'rgba(210, 0, 0, 0.01)';
                        else echo 'rgba(0, 150, 136, 0.01)';
                        ?>
                     }
                  </style>
                  <hr>
                  <div class="row">
                     <div class="col">
                        <ul class="list-group list-group-flush">
                           <li class="list-group-item"><b>GameSwitch:</b>
                              <?php echo $table_row['shorten'];?>
                           </li>
                           <li class="list-group-item"><b>Ping:</b>
                              <?php 
                                 $port = $table_row['port']; 
                                 $host = $table_row['ip'].':'.++$port;
                                 $host2 = $table_row['ip'];
                                 if ($pingcfg == '1') { $pingTime = shell_exec('ping -W 3 -q -c 2 -s 21 ' . $host2 . ' | grep -o "... ms" '); 
                                 echo "$pingTime"; } else {echo "ping function disabled";}
                                 ?>
                           </li>
                           <li class="list-group-item"><b>Location:</b>
                              <?php $host2 = $table_row['ip']; $xmlcu = simplexml_load_file("http://ip-api.com/xml/".$host2); echo "$xmlcu->country"; ?>
                           </li>
                           <li class="list-group-item"><b><?php echo $sprache->updatetime;?>:</b>
                              <?php echo $table_row['updatetime'];?>
                           </li>
                           <li class="list-group-item"><b><?php echo $sprache->map;?>:</b>
                              <?php echo $table_row['map'];?>
                           </li>
                           <li class="list-group-item"><b><?php echo $sprache->player;?>:</b>
                              <?php echo $table_row['numplayers']?> /
                              <?php echo $table_row['maxplayers'];?>
                           </li>
                     </div>
                     <div class="col">
                     <?php if($table_row['ramLimited']=='Y') { ?>
                     <li class="list-group-item"><b><?php echo $sprache->ramMax;?>:</b>
                     <?php echo $table_row['maxram'];?> MB</li>
                     <?php } ?>
                     <?php if($table_row['taskset']=='Y' and $table_row['coreCount']>0) { ?>
                     <li class="list-group-item"><b><?php echo $sprache->coreBind;?>:</b>
                     <?php echo $table_row['cores'].' ('.$table_row['coreCount'].')';?>
                     </li>
                     <?php } ?>
                     <?php if(($pa['ftpaccess'] or $pa['miniroot']) and $table_row['ftpAllowed']) { ?>
                     <li class="list-group-item"><b><?php echo $sprache->ftp_link;?>:</b>
                     <a href="<?php echo $table_row['ftpdata'];?>">
                     <?php echo $table_row['ftpdata'];?>
                     </a>
                     </li>
                     <?php } ?>
                     <li class="list-group-item"><b><?php echo $sprache->ftp_adresse;?>:</b> ftp://
                     <?php echo $table_row['ip'].":".$table_row['ftpport'].$pserver.$table_row['shorten'];?>
                     </li>
                     <li class="list-group-item"><b><?php echo $sprache->ftp_user;?>:</b>
                     <?php echo $table_row['cname'];?>
                     </li>
                     <li class="list-group-item"><b><?php echo $sprache->ftp_password;?>:</b>
                     <?php echo $table_row['cftppass'];?>
                     </li>
                     <li class="list-group-item"></li>
                     </ul>
                     </div>
</div></section></section></div></div>
            <?php }?>
