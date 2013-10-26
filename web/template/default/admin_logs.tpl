<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->logs;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=lo&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur; ?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=lo&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=lo&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=lo&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=lo&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor; ?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><?php echo $sprache->date;?></th>
                <th data-hide="phone"><?php echo $sprache->account;?></th>
                <th><?php echo $sprache->action;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->ip;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->hostname;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['logday'].' '.$table_row['loghour']; ?></td>
                <td><?php echo $table_row['username']; ?></td>
                <td><?php echo $table_row['useraction']; ?></td>
                <td><?php echo $table_row['ip']; ?></td>
                <td><?php echo $table_row['hostname']; ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>