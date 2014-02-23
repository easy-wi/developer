<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->user;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $sprache->passw;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=su&amp;d=pw&amp;r=su" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label" for="password1"><?php echo $sprache->passw_1;?></label>
                <div class="controls">
                    <input class="span10" id="password1" type="password" name="password" value="" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password2"><?php echo $sprache->passw_2;?></label>
                <div class="controls">
                    <input class="span10" id="password2" type="password" name="pass2" value="" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="md">
                </div>
            </div>
        </form>
    </div>
</div>