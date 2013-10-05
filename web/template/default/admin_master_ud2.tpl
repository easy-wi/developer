<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $sprache->header_update;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span10">
        <dl class="dl-horizontal">
            <?php foreach($ips as $id=>$ip) { ?>
            <dt><?php echo $ip;?></dt>
            <dd id="<?php echo $id;?>"><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></dd>
            <?php } ?>
        </dl>
    </div>
</div>