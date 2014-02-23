<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->backup.' '.$gsprache->add;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $server;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_voiceserver_backup;?></div>
</div>
<hr>
<div class="row-fluid">
    <dl class="dl-horizontal">
        <dt><?php echo $sprache->backups;?></dt>
        <dd><?php echo $backupcount.'/'.$voice_maxbackup;?></dd>
    </dl>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $id;?>&amp;r=vo" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="name"><?php echo $sprache->backupname;?></label>
                <div class="controls">
                    <input id="name" type="text" name="name" placeholder="New Backup" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-play-circle icon-white"></i> <?php echo $gsprache->exec;?></button>
                    <input type="hidden" name="action" value="nb">
                </div>
            </div>
        </form>
    </div>
</div>