<section class="content-header">
    <h1><?php echo $gsprache->master_apps.' '.$gsprache->update;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ro"><i class="fa fa-server"></i> <?php echo $gsprache->appRoot;?></a></li>
        <li class="active"><i class="fa fa-spinner"></i> <?php echo $gsprache->master_apps.' '.$gsprache->update;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">

        <div class="col-md-12">
            <div class="box box-warning">

                <div class="box-header">
                    <h3 class="box-title"><?php echo $gsprache->appRoot;?></h3>
                </div>

                <div class="box-body">
                    <dl class="dl-horizontal">
                        <?php foreach($ips as $id=>$ip) { ?>
                        <dt><?php echo $ip;?></dt>
                        <dd id="progressID-<?php echo $id;?>">
                            <div class="progress progress-striped active">
                                <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                            </div>
                        </dd>
                        <?php } ?>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
$(function() {
    <?php foreach($ajaxStrings as $id=>$masterIDs){ ?>
     masterIDs = [];
     <?php foreach($masterIDs as $masterID){ ?>
     masterIDs[masterIDs.length] = <?php echo $masterID;?>;
     <?php } ?>
     $.get('ajax.php',{d: 'masterappserverupdate', serverID: <?php echo $id;?>, masterIDs: masterIDs } ).done(function(data) { $("#progressID-<?php echo $id;?>").html(data);});
    <?php } ?>
});
</script>

