<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->voiceserver;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<?php if((!is_numeric($licenceDetails['lVo']) or $licenceDetails['lVo']>0) and ($licenceDetails['left']>0 or !is_numeric($licenceDetails['left']))) { ?>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->voiceserver;?> <a href="admin.php?w=vo&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<?php } ?>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=vo&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=vo&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=vo&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=vo&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=vo&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=vo&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='am') { echo 'dm'; } else { echo 'am'; } ?>"><?php echo $sprache->server;?></a></th>
                <th data-hide="phone"><a href="admin.php?w=vo&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID</a></th>
                <th data-hide="phone"><a href="admin.php?w=vo&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='dv') { echo 'av'; } else { echo 'dv'; } ?>">VirtualID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vo&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $gsprache->status;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vo&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='aa') { echo 'da'; } else { echo 'aa'; } ?>"><?php echo $sprache->user;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vo&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='an') { echo 'dn'; } else { echo 'an'; } ?>"><?php echo $sprache->user;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vo&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ap') { echo 'dp'; } else { echo 'ap'; } ?>"><?php echo $gsprache->jobs;?></a></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr class="<?php if($table_row['img']=='16_ok') echo 'success'; else if($table_row['img']=='16_bad') echo 'warning'; else echo 'error';?>">
                <td><?php echo $table_row['server'];?></td>
                <td><?php echo $table_row['id'];?></td>
                <td><?php echo $table_row['virtualID'];?></td>
                <td><i class="<?php if($table_row['active']=='Y') echo 'icon-ok'; else echo 'icon-ban-circle';?>"></i></td>
                <td><a href="switch.php?id=<?php echo $table_row['userid'];?>"><?php echo $table_row['cname'];?></a></td>
                <td><?php echo $table_row['names'];?></td>
                <td><?php echo $table_row['jobPending'];?></td>
                <td><a href="admin.php?w=vo&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=vo&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>