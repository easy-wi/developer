<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li class="active">TS3 DNS</li>
        </ul>
    </div>
</div>
<?php foreach ($table as $table_row) { ?>
<div class="row-fluid span11 alert alert-success">
    <div class="row-fluid">
        <dl class="dl-horizontal">
            <dt><?php echo $gsprache->settings; ?></dt>
            <dd><a href="userpanel.php?w=vd&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-primary btn-mini"><i class="icon-edit icon-white"></i> <?php echo $gsprache->mod;?></span></a></dd>
            <dt>ID</dt>
            <dd><?php echo $table_row['id'];?></dd>
            <dt>TS3 DNS</dt>
            <dd><?php echo $table_row['dns'];?></dd>
            <dt><?php echo $sprache->ip; ?></dt>
            <dd><?php echo $table_row['address'];?></dd>
        </dl>
    </div>
</div>
<?php } ?>