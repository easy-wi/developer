<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Easy-WI <?php echo $gsprache->databases;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=bu&amp;d=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="action" value="bu">
            <div class="control-group">
                <label class="control-label" for="inputUpdate"><?php echo $gsprache->backup;?></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputUpdate" type="submit"><i class="icon-download icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=bu&amp;d=rp&amp;r=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="action" value="rp">
            <div class="control-group">
                <label class="control-label" for="inputUpdate"><?php echo $gsprache->database.' '.$gsprache->repair;?></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputUpdate" type="submit"><i class="icon-refresh icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>