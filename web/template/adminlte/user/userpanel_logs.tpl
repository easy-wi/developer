<section class="content-header">
    <h1><?php echo $gsprache->logs;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->logs;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_logs;?>
            </div>
        </div>
    </div>
    
    <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
            <li><a href="userpanel.php?w=lo&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur; ?>"><i class="fa fa-step-backward"></i></a></li>
            <li><a href="userpanel.php?w=lo&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="userpanel.php?w=lo&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="userpanel.php?w=lo&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="userpanel.php?w=lo&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor; ?>"><i class="fa fa-step-forward"></i></a></li>
        </ul>
    </div>
    <br/>

    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th><?php echo $sprache->date;?></th>
                    <th><?php echo $sprache->account;?></th>
                    <th><?php echo $sprache->action;?></th>
                    <th><?php echo $sprache->ip;?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
                <tr>
                    <td><?php echo $table_row['logday'].' '.$table_row['loghour']; ?></td>
                    <td><?php echo $table_row['username']; ?></td>
                    <td><?php echo $table_row['useraction']; ?></td>
                    <td><?php echo $table_row['ip']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
	
</section><!-- /.content -->