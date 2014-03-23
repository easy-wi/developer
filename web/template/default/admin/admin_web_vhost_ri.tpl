<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=wv"><?php echo $gsprache->webspace;?> Vhost</a> <span class="divider">/</span></li>
            <li><?php echo $dedicatedLanguage->reinstall;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $dns;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->dns?></dt>
            <dd><?php echo $dns;?></dd>
            <dt><?php echo $dedicatedLanguage->user?></dt>
            <dd><?php echo $user;?></dd>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=wv&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ri">
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-refresh"></i> <?php echo $dedicatedLanguage->reinstall;?></button>
                </div>
            </div>
        </form>
    </div>
</div>