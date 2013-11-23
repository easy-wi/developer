<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=vr">TSDNS</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form name="form" class="form-horizontal" action="admin.php?w=vr&amp;d=ad" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group">
                <label class="control-label" for="inputUser"><?php echo $sprache->user;?></label>
                <div class="controls">
                    <select id="inputUser" name="userID">
                        <?php foreach ($table as $key=>$value){ ?><option value="<?php echo $key;?>"><?php echo $value;?></option><?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaster">TSDNS <?php echo $gsprache->master;?></label>
                <div class="controls">
                    <select id="inputMaster" name="tsdnsID">
                        <?php foreach ($table2 as  $key=>$value){ ?><option value="<?php echo $key;?>"><?php echo $value;?></option><?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>