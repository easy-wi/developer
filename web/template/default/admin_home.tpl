<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Dashboard</li>
        </ul>
    </div>
</div>
<h3><?php echo $gsprache->overview; ?></h3>
<?php if($pa['usertickets'] and $crashedArray['ticketsReseller']>0 and $reseller_id!=0) { ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><?php echo $sprache_bad->tickets; ?></h4>
        <a href="admin.php?w=tr"><?php echo $crashedArray['ticketsReseller']."/".$crashedArray['ticketsResellerOpen'].' '.$sprache_bad->tickets_open; ?></a>
    </div>
</div>
<?php }?>
<?php if($pa['tickets'] and $crashedArray['ticketsOpen']>0){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><?php echo $sprache_bad->tickets; ?></h4>
        <a href="admin.php?w=ti"><?php echo $crashedArray['tickets']."/".$crashedArray['ticketsOpen'].' '.$sprache_bad->tickets_open; ?></a>
    </div>
</div>
<?php } ?>
<?php if($crashedArray['gsCrashed']>0 and $pa['gserver'] and $gserver_module){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><a href="admin.php?w=gs"><?php echo $crashedArray['gsCrashed'].' '.$sprache_bad->gserver_crashed; ?></a></h4>
        <?php foreach ($crashed as $row) { ?>
        <div class="row-fluid"><?php echo $row['address'];?></div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php if($crashedArray['gsTag']>0 and $pa['gserver'] and $gserver_module){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><a href="admin.php?w=gs"><?php echo $crashedArray['gsTag'].' '.$sprache_bad->gserver_tag_removed; ?></a></h4>
        <?php foreach ($tag_removed as $row) { ?>
        <div class="row-fluid"><?php echo $row['address'];?></div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php if($crashedArray['gsPWD']>0 and $pa['gserver'] and $gserver_module){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><a href="admin.php?w=gs"><?php echo $crashedArray['gsPWD'].' '.$sprache_bad->gserver_removed; ?></a></h4>
        <?php foreach ($removed as $row) { ?>
        <div class="row-fluid"><?php echo $row['address'];?></div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php if($pa['roots'] and $gserver_module and $crashedArray['masterserver']>0) { ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><a href="admin.php?w=ro&amp;d=md"><?php echo $crashedArray['masterserver'].' '.$sprache_bad->master_crashed; ?></a></h4>
    </div>
</div>
<?php }?>
<?php if($pa['voiceserver'] and $voserver_module and $crashedArray['ts3']>0){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><a href="admin.php?w=vo"><?php echo $crashedArray['ts3'].' '.$sprache_bad->voice_crashed; ?></a></h4>
        <?php foreach ($crashed_ts3 as $row) { ?>
        <div class="row-fluid"><?php echo $row['address'];?></div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php if($pa['voicemasterserver'] and $voserver_module and $crashedArray['ts3Master']>0){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><a href="admin.php?w=vm"><?php echo $crashedArray['ts3Master'].' '.$sprache_bad->ts3master_crashed; ?></a></h4>
    </div>
</div>
<?php } ?>
<?php if($pa['vserverhost'] and $vserver_module and $reseller_id==0 and $crashedArray['virtualHosts']>0) { ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><a href="admin.php?w=vh&amp;d=md"><?php echo $crashedArray['virtualHosts'].' '.$sprache_bad->host_crashed.' ESX(i)'; ?></a></h4>
    </div>
</div>
<?php }?>
<hr>
<?php foreach ($feedArray as $url => $array) { ?>
<h3><?php if($url=='News') { echo $gsprache->news; } else  { ?><a href="<?php echo $array[0]['url'];?>" target="_blank"><?php echo $array[0]['url'];?></a><?php } ?></h3>
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