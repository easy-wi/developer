<div class="row-fluid" xmlns="http://www.w3.org/1999/html">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->gameserver;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_list;?></div>
</div>
<hr>
<?php foreach ($table as $table_row){ ?>
<div class="row-fluid span11 alert <?php if($table_row['stopped']=='N') echo 'alert-success'; else echo 'alert-block';?>">
    <h4 class="row-fluid span12 inline"><img src="images/games/icons/<?php echo $table_row['gameShorten'];?>.png" alt="<?php echo $table_row['gameShorten'];?>" width="14" /> <a href="hlsw://<?php echo $table_row['server'];?>"><?php echo $table_row['server'].' '.$table_row['name'];?></a></h4>
    <?php if(!empty($table_row['premoved'])){ ?><div class="row-fluid span12 alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> <?php echo $table_row['premoved'];?></div><?php } ?>
    <?php if(!empty($table_row['nameremoved'])){ ?><div class="row-fluid span12 alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> <?php echo $table_row['nameremoved'];?></div><?php } ?>
    <div class="row-fluid">
        <div class="span12 btn-group-vertical">
            <a href="userpanel.php?w=gs&amp;d=rs&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onclick="return confirm('<?php echo $table_row['server'];?>: <?php echo $sprache->confirm_restart;?>');"><button class="btn btn-mini btn-success inline"><i class="icon-white icon-play"></i> <?php echo $sprache->restarts;?></button></a>
            <?php if($table_row['stopped']=='N'){ ?><a href="userpanel.php?w=gs&amp;d=st&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onclick="return confirm('<?php echo $table_row['server'];?>: <?php echo $sprache->confirm_stop;?>');"><button class="btn btn-mini btn-danger"><i class="icon-white icon-stop"></i> <?php echo $sprache->stop;?></button></a><?php } ?>
            <?php if(!empty($table_row['pro'])){ ?><a href="userpanel.php?w=pr&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onclick="return confirm('<?php echo $table_row['server'];?>: <?php echo $sprache->protect . ' '; echo ($table_row['imgp']=='16_protected') ? $sprache->off2 : $sprache->on;?>?');"><button class="btn btn-mini <?php if($table_row['imgp']=='16_protected')echo 'btn-info';else if($table_row['imgp']=='16_unprotected') echo 'btn-warning';?>"><i class="icon-white icon-lock"></i> <?php echo $sprache->protect.' '.$table_row['pro'];?></button></a><?php } ?>
            <a href="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-mini btn-primary"><i class="icon-white icon-cog"></i> <?php echo $sprache->config;?></button></a>
            <?php if($pa['useraddons']){ ?><a href="userpanel.php?w=ao&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-mini btn-primary"><i class="icon-white icon-gift"></i> <?php echo $gsprache->addon;?></button></a><?php } ?>
            <a href="userpanel.php?w=ca&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-mini btn-primary"><i class="icon-white icon-calendar"></i> <?php echo $sprache->restarttime;?></button></a>    
            <div class="btn-group">
              <button data-toggle="dropdown" class="btn btn-mini btn-primary dropdown-toggle"><i class="icon-white icon-bold"></i> <?php echo $gsprache->backup;?> <span class="caret"></span></button>
              <ul class="dropdown-menu">
                <li><a href="userpanel.php?w=bu&amp;id=<?php echo $table_row['id'];?>"><?php echo $gsprache->backup;?></a></li>
                <li><a href="userpanel.php?w=bu&amp;id=<?php echo $table_row['id'];?>&amp;action=mb"><?php echo $gsprache->backup." ".$sprache->create;?></a></li>
                <li><a href="userpanel.php?w=bu&amp;id=<?php echo $table_row['id'];?>&amp;action=rb"><?php echo $gsprache->backup." ".$sprache->recover;?></a></li>
                <li><a href="userpanel.php?w=bu&amp;id=<?php echo $table_row['id'];?>&amp;action=md"><?php echo $gsprache->settings;?></a></li>
              </ul>
            </div>
            <a href="serverlog.php?id=<?php echo $table_row['id'];?>" onclick="return popup(this.href);"><button class="btn btn-mini btn-primary"><i class="icon-white icon-tasks"></i> <?php echo $gsprache->logs;?></button></a>
            <a href="userpanel.php?w=gs&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-mini btn-primary"><i class="icon-white icon-pencil"></i> <?php echo $gsprache->settings;?></button></a>
            <a href="userpanel.php?w=gs&amp;d=ri&amp;id=<?php echo $table_row['id'];?>"><button class="btn btn-mini btn-warning"><i class="icon-white icon-refresh"></i> <?php echo $sprache->reinstall;?></button></a>

            <?php if($table_row['upload']==true){ ?><a href="userpanel.php?w=gs&amp;d=du&amp;id=<?php echo $table_row['id'];?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');"><button class="btn btn-mini btn-info"><i class="icon-white icon-film"></i> SourceTV</button></a><?php } ?>
        </div>
    </div>
    <div class="row-fluid">
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
<?php }?>