<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=fd"><?php echo $gsprache->fastdownload;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $sprache->haupt;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11"><?php echo $sprache->example;?></div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="userpanel.php?w=fd&amp;d=eu&amp;r=fd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="inputFTP"></label>
                <div class="controls">
                    <div class="input-append">
                        <input type="hidden" name="action" value="md" />
                        <input class="input-block-level" id="inputFTP" type="text" name="fdlpath" value="<?php echo $fdlpath;?>">
                        <button class="btn btn-primary"><i class="icon-pencil icon-white"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>