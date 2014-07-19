<section class="content-header">
    <h1><?php echo $gsprache->userImport.' '.$gsprache->overview;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->userImport;?></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

	<?php echo $gsprache->userImport;?> <a href="admin.php?w=ui&amp;d=ad"><span class="btn-primary btn-sm"><i class="fa fa-plus"></i></span></a>
    
    <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
            <li><a href="admin.php?w=ui&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $zur;?>"><i class="fa fa-step-backward"></i></a></li>
            <li><a href="admin.php?w=ui&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=ui&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=ui&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=ui&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $vor;?>"><i class="fa fa-step-forward"></i></a></li>
        </ul>
    </div>
    <br/>

    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover footable">
                <thead>
                <tr>
                    <th data-class="expand"><a href="admin.php?w=ui&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='an') { echo 'dn'; } else { echo 'an'; } ?>"><?php echo $sprache->domain;?></a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=ui&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='aa') { echo 'da'; } else { echo 'aa'; } ?>"><?php echo $gsprache->active;?></a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=ui&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">ID</a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=ui&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->lastCheck;?></a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=ui&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->lastExternalID;?></a></th>
                    <th><?php echo $gsprache->del;?></th>
                    <th><?php echo $gsprache->mod;?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($table as $table_row) { ?>
                <tr>
                    <td><?php echo $table_row['domain'];?></td>
                    <td><i class="<?php if($table_row['active']=='Y') echo 'fa fa-check'; else echo 'fa fa-ban';?>"></i></td>
                    <td><?php echo $table_row['id'];?></td>
                    <td><?php echo $table_row['lastCheck'];?></td>
                    <td><?php echo $table_row['lastID'];?></td>
                    <td><a href="admin.php?w=ui&amp;d=dl&amp;id=<?php echo $table_row['id'];?>"><span class="btn-sm btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                    <td><a href="admin.php?w=ui&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn-sm btn-primary"><i class="fa fa-edit"></i></span></a></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>