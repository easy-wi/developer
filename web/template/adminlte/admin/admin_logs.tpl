<section class="content-header">
    <h1><?php echo $gsprache->logs;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->logs;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
            <li><a href="admin.php?w=lo&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur; ?>"><i class="fa fa-step-backward"></i></a></li>
            <li><a href="admin.php?w=lo&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=lo&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=lo&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=lo&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor; ?>"><i class="fa fa-step-forward"></i></a></li>
        </ul>
    </div>
    <br/>
    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
        <table class="table table-bordered table-hover">
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
</section>