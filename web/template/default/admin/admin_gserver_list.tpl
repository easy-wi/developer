<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->gameserver;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->gameserver;?> <a href="admin.php?w=gs&amp;d=ad"<span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=gs&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=gs&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=gs&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=gs&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=gs&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<input type="hidden" name="action" value="md">
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=gs&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; } ?>"><?php echo $sprache->server;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=gs&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">ID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=gs&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $gsprache->status;?></a></th>
                <th data-hide="phone"><a href="admin.php?w=gs&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='aa') { echo 'da'; } else { echo 'aa'; } ?>"><?php echo $sprache->user;?></a></th>
                <th data-hide="phone,tablet"><?php echo $sprache->reinstall;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->stop;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->restarts;?></th>
                <th data-hide="phone,tablet"><?php echo $gsprache->jobPending;?></th>
                <th><?php echo $gsprache->del;?></a></th>
                <th><?php echo $gsprache->mod;?></a></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr class="<?php if($table_row['img']=='16_ok') echo 'success'; else if($table_row['img']=='16_bad') echo 'warning'; else echo 'error';?>">
                <td><img src="images/games/icons/<?php echo $table_row['shorten'];?>.png" alt="<?php echo $table_row['shorten'];?>" width="16"/> <?php echo $table_row['server'];?><?php echo $table_row['premoved']." ".$table_row['nameremoved'];?></td>
                <td><?php echo $table_row['serverid'];?></td>
                <td><i class="<?php if($table_row['img']=='16_ok') echo 'icon-ok'; else if($table_row['img']=='16_bad') echo 'icon-stop'; else echo 'icon-warning-sign';?>"></i></td>
                <td><a href="switch.php?id=<?php echo $table_row['id'];?>"><?php echo $table_row['cname'];?></a> <?php echo $table_row['names'];?></td>
                <td><a href="admin.php?w=gs&amp;d=ri&amp;id=<?php echo $table_row['serverid'];?>"><span class="btn btn-mini btn-warning"><i class="fa fa-refresh"></i></span></a></td>
                <td><a href="admin.php?w=gs&amp;d=st&amp;id=<?php echo $table_row['serverid'];?>&amp;r=gs"><span class="btn btn-mini btn-danger"><i class="icon-white icon-stop"></i></span></a></td>
                <td><a href="admin.php?w=gs&amp;d=rs&amp;id=<?php echo $table_row['serverid'];?>&amp;r=gs"><span class="btn btn-mini btn-success"><i class="icon-white icon-play"></i></span></a></td>
                <td><?php echo $table_row['jobPending'];?></td>
                <td><a href="admin.php?w=gs&amp;d=dl&amp;id=<?php echo $table_row['serverid'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=gs&amp;d=md&amp;id=<?php echo $table_row['serverid'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>