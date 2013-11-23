<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->backup;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.":".$port;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $sprache->recover;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=bu&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="inputTemplate"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <select name="template" id="inputTemplate">
                        <?php foreach($shortens as $shorten) { echo '<option>'.$shorten.'</option>'; } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRecover"> </label>
                <div class="controls">
                    <input type="hidden" name="action" value="rb2" />
                    <button class="btn btn-primary" id="inputRecover" type="submit"><i class="fa fa-refresh"></i> <?php echo $sprache->recover;?></button>
                </div>
            </div>
        </form>
    </div>
</div>