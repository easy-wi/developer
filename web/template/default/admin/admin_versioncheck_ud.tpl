<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=vc"><?php echo $gsprache->versioncheck;?></a> <span class="divider">/</span></li>
            <li class="active">Easy-Wi Update ( <?php echo $ewVersions['version'];?> )</li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11 alert alert-block">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <?php echo $vcsprache->prepare1;?><br /><br />
        <?php echo $vcsprache->prepare2;?><br /><br />
        <?php echo $vcsprache->prepare3;?>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=vc&amp;d=ud" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="action" value="ud">
            <div class="control-group">
                <label class="control-label" for="inputUpdate"><?php echo $vcsprache->start;?></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputUpdate" type="submit"><i class="fa fa-refresh"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>