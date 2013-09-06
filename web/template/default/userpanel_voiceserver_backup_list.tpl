<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->backup;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $server;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <dl class="dl-horizontal">
        <dt><?php echo $gsprache->add;?></dt>
        <dd><a href="userpanel.php?w=vo&amp;d=bu&amp;po=1&amp;id=<?php echo $server_id;?>"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a></dd>
        <dt><?php echo $sprache->backup;?></dt>
        <dd><?php echo $backupcount.'/'.$voice_maxbackup;?></dd>
    </dl>
</div>
<hr>
<div class="row-fluid">
    <table class="table table-condensed table-striped table-hover">
        <thead>
        <tr>
            <th><?php echo $sprache->date;?></th>
            <th colspan="3"><?php echo $sprache->backupname;?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($table as $table_row) { ?>
        <tr>
            <td><?php echo $table_row['date']; ?></td>
            <td><?php echo $table_row['name']; ?></td>
            <td>
                <form method="post" action="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $id;?>&amp;r=vo" name="form" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                    <button class="btn btn-danger btn-mini" id="inputEdit" type="submit"><i class="icon-white icon-remove-sign"></i></button>
                    <input type="hidden" name="action" value="md" />
                    <input type="hidden" name="delete" value="md" />
                    <input type="hidden" name="id" value="<?php echo $table_row['id'];?>" />
                </form>
            </td>
            <td>
                <form method="post" action="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $id;?>&amp;r=vo" name="form" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                    <button class="btn btn-primary btn-mini" id="inputEdit" type="submit"><i class="icon-white icon-refresh"></i></button>
                    <input type="hidden" name="action" value="md" />
                    <input type="hidden" name="use" value="md" />
                    <input type="hidden" name="id" value="<?php echo $table_row['id'];?>" />
                </form>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>