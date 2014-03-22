<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=wv"><?php echo $gsprache->webspace;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->ftpPassword;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $dns;?></li>
        </ul>
    </div>
</div>
<?php if (count($errors)>0){ ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4><?php echo $gsprache->errors;?></h4>
    <?php echo implode(', ',$errors);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span12">
        <form class="form-horizontal" action="userpanel.php?w=wv&amp;d=pw&amp;id=<?php echo $id;?>&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="pw">
            <div class="control-group">
                <label class="control-label" for="inputPassword1"><?php echo $sprache->ftpPassword;?></label>
                <div class="controls">
                    <input id="inputPassword1" type="password" name="password1" value="" maxlength="40">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPassword2"><?php echo $sprache->ftpPasswordRepeat;?></label>
                <div class="controls">
                    <input id="inputPassword2" type="password" name="password2" value="" maxlength="40">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>