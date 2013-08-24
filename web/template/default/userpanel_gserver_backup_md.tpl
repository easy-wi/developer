<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->backup;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.":".$port;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="userpanel.php?w=bu&amp;id=<?php echo $server_id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="inputSettings">FTP</label>
                <div class="controls">
                    <div class="input-append">
                        <input type="hidden" name="action" value="md2" >
                        <input class="span10 input-block-level" id="inputSettings" type="text" name="ftpbackup" value="<?php echo $ftpbackup;?>" />
                        <button class="btn btn-primary"><i class="icon-pencil icon-white"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>