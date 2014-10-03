<section class="content-header">
    <h1>Easy-Wi Update</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li><a href="admin.php?w=vc"><i class="fa fa-check"></i> <?php echo $gsprache->versioncheck;?></a></li>
        <li class="active">Easy-Wi Update ( <?php echo $ewVersions['version'];?> )</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">

                <form role="form" action="admin.php?w=vc&amp;d=ud" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="action" value="ud">

                    <div class="box-header">
                        <h3 class="box-title"><?php echo $ewVersions['version'];?></h3>
                    </div>

                    <div class="box-body">
                        <div class="alert alert-warning alert-dismissable">
                            <i class="fa fa-warning"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $vcsprache->prepare1;?>
                        </div>

                        <div class="alert alert-warning alert-dismissable">
                            <i class="fa fa-warning"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $vcsprache->prepare2;?>
                        </div>

                        <div class="alert alert-warning alert-dismissable">
                            <i class="fa fa-warning"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $vcsprache->prepare3;?>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-warning" id="inputEdit" type="submit"><i class="fa fa-refresh">&nbsp;<?php echo $vcsprache->start;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>