<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=ug"><?php echo $gsprache->groups;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->del;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $name;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=ug&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=ug" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">
            <dl class="dl-horizontal">
                <dt><?php echo $sprache->groupname;?></dt>
                <dd><?php echo $name;?></dd>
                <dt><?php echo $sprache->accounttype;?></dt>
                <dd><?php echo $grouptype;?></dd>
            </dl>
            <div class="control-group">
                <label class="control-label" for="inputMod"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputMod" type="submit"><i class="fa fa-trash-o"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>