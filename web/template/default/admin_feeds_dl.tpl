<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=fe"><?php echo $gsprache->feeds;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->del;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $feedUrl;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=fe&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=fe" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">
            <div class="control-group">
                <label class="control-label" for="inputEdit"><?php echo $feedUrl;?></label>
                <div class="controls">
                    <button class="btn btn-danger pull-left" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
                </div>
            </div>
        </form>
    </div>
</div>