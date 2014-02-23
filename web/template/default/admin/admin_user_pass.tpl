<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=us"><?php echo $gsprache->user;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->passw;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $cname;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=us&amp;d=pw&amp;id=<?php echo $id;?>&amp;r=us" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="pw">
            <dl class="dl-horizontal">
                <dt><?php echo $sprache->user;?></dt>
                <dd><?php echo $cname;?></dd>
            </dl>
            <div class="control-group">
                <label class="control-label" for="inputPass1"><?php echo $sprache->passw_1;?></label>
                <div class="controls">
                    <input id="inputPass1" type="password" name="password" value="" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputName"><?php echo $sprache->passw_2;?></label>
                <div class="controls">
                    <input id="inputPass2" type="password" name="pass2" value="" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMod"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputMod" type="submit"><i class="icon-white icon-edit"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>