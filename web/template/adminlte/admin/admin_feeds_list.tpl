<section class="content-header">
    <h1><?php echo $gsprache->feeds;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->feeds;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

	<?php echo $gsprache->feeds;?> <a href="admin.php?w=fe&amp;d=ad"<span class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></span></a>
    
    <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
            <li><a href="admin.php?w=fe&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="fa fa-step-backward"></i></a></li>
            <li><a href="admin.php?w=fe&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=fe&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=fe&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=fe&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="fa fa-step-forward"></i></a></li>
        </ul>
    </div>
    <br/>
<input type="hidden" name="action" value="md">
    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th data-class="expand"><a href="admin.php?w=fe&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='au') { echo 'du'; } else { echo 'au'; } ?>">URL</a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=fe&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID</a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=fe&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ds') { echo 'as'; } else { echo 'ds'; } ?>"><?php echo $sprache->status;?></a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=fe&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>">Twitter</th>
                    <th><?php echo $gsprache->del;?></a></th>
                    <th><?php echo $gsprache->mod;?></a></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($table as $table_row) { ?>
                <tr>
                    <td><a href="<?php echo $table_row['feedUrl'];?>" target="_blank"><?php echo $table_row['feedUrl'];?></a></td>
                    <td><?php echo $table_row['id'];?></td>
                    <td><i class="<?php if($table_row['active']=='Y') echo 'icon-ok'; else echo 'icon-ban-circle';?>"></i></td>
                    <td><?php echo $table_row['twitter'];?></td>
                    <td><a href="admin.php?w=fe&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                    <td><a href="admin.php?w=fe&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></span></a></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>