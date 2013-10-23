<div class="row-fluid">
    <div class="12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->backup;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.":".$port;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span5">
        <form class="form-horizontal" action="userpanel.php?w=bu&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="inputCreate"><?php echo $gsprache->backup." ".$sprache->create;?></label>
                <div class="controls">
                    <input type="hidden" name="action" value="mb" />
                    <button class="btn btn-primary pull-right" id="inputCreate" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row-fluid">
    <div class="span5">
        <form class="form-horizontal" action="userpanel.php?w=bu&amp;id=<?php echo $id;?>" method="post">
            <div class="control-group">
                <label class="control-label" for="inputRecover"><?php echo $gsprache->backup." ".$sprache->recover;?></label>
                <div class="controls">
                    <input type="hidden" name="action" value="rb" />
                    <button class="btn btn-primary pull-right" id="inputRecover" type="submit"><i class="icon-refresh icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row-fluid">
    <div class="span5">
        <form class="form-horizontal" action="userpanel.php?w=bu&amp;id=<?php echo $id;?>" method="post">
            <div class="control-group">
                <label class="control-label" for="inputSettings"><?php echo $gsprache->settings;?></label>
                <div class="controls">
                    <input type="hidden" name="action" value="md" />
                    <button class="btn btn-primary pull-right" id="inputSettings" type="submit"><i class="icon-pencil icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>