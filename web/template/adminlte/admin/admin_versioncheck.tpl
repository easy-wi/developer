<section class="content-header">
    <h1><?php echo $gsprache->versioncheck;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->versioncheck;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">

                    <div class="callout callout-<?php if($state==1) echo 'danger'; else echo 'info';?>">
                        <p><?php echo $isok;?></p>
                    </div>

                    <hr>

                    <h3><?php echo $vcsprache->changelog;?></h3>

                    <ul class="timeline">
                        <?php foreach($table as $changelog) { ?>
                        <li>
                            <i class="fa fa-info bg-blue"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header"><?php echo $changelog['version'];?></h3>
                                <div class="timeline-body">
                                    <?php echo $changelog['text'];?>
                                </div>
                            </div>
                        </li>
                        <?php } ?>

                        <li>
                            <i class="fa fa-clock-o"></i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>