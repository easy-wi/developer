<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Dashboard</li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <h4><?php echo $gsprache->greating_home;?></h4>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $gsprache->help_home;?></div>
</div>
<hr>
<?php if($crashedArray['ticketsOpen']>0){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><?php echo $sprache_bad->tickets; ?></h4>
        <a href="userpanel.php?w=ti"><?php echo $crashedArray['tickets']."/".$crashedArray['ticketsOpen'].' '.$sprache_bad->tickets_open; ?></a>
    </div>
</div>
<?php } ?>
<?php if($crashedArray['gsCrashed']>0){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><?php echo $crashedArray['gsCrashed'].' '.$sprache_bad->gserver_crashed; ?></h4>
        <?php foreach ($crashed as $row) { ?>
        <div class="row-fluid"><?php echo $row['address'];?></div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php if($crashedArray['gsTag']>0){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><?php echo $crashedArray['gsTag'].' '.$sprache_bad->gserver_tag_removed; ?></h4>
        <?php foreach ($tag_removed as $row) { ?>
        <div class="row-fluid"><?php echo $row['address'];?></div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php if($crashedArray['gsPWD']>0){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><?php echo $crashedArray['gsPWD'].' '.$sprache_bad->gserver_removed; ?></h4>
        <?php foreach ($pwd_removed as $row) { ?>
        <div class="row-fluid"><?php echo $row['address'];?></div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php if($crashedArray['ts3']>0){ ?>
<div class="row-fluid">
    <div class="span11 alert alert-block alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><?php echo $crashedArray['ts3'].' '.$sprache_bad->voice_crashed; ?></h4>
        <?php foreach ($crashed_ts3 as $row) { ?>
        <div class="row-fluid"><?php echo $row['address'];?></div>
        <?php } ?>
    </div>
</div>
<?php } ?>
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