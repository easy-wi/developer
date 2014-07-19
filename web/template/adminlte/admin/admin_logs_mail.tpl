<section class="content-header">
    <h1>E-Mail <?php echo $gsprache->logs;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">E-Mail <?php echo $gsprache->logs;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
            <li><a href="admin.php?w=ml&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur; ?>"><i class="fa fa-step-backward"></i></a></li>
            <li><a href="admin.php?w=ml&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=ml&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=ml&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=ml&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor; ?>"><i class="fa fa-step-forward"></i></a></li>
        </ul>
    </div>
    <br/>
    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=ml&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='dd') { echo 'ad'; } else { echo 'dd'; } ?>"><?php echo $sprache->date;?></a></th>
                <th data-hide="phone"><a href="admin.php?w=ml&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='au') { echo 'du'; } else { echo 'au'; } ?>"><?php echo $sprache->account;?></a></th>
                <th><a href="admin.php?w=ml&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->topic;?></a></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['logday']." ".$table_row['loghour']; ?></td>
                <td><?php echo $table_row['username']; ?></td>
                <td><?php echo $table_row['topic']; ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
</section>