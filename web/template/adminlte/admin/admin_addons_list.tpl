<section class="content-header">
    <h1><?php echo $sprache->heading_addons;?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $sprache->heading_addons;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->heading_addons;?>
            </div>
        </div>
    </div>

	<?php echo $sprache->heading_addons;?> <a href="admin.php?w=ad&amp;d=ad"<span class="btn-primary btn-sm"><i class="fa fa-plus"></i></span></a>        

    <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
            <li><a href="admin.php?w=ad&amp;a=<?php echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="fa fa-step-backward"></i></a></li>
            <li><a href="admin.php?w=ad&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=ad&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=ad&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=ad&amp;a=<?php echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="fa fa-step-forward"></i></a></li>
        </ul>
    </div>
    <br/>

    <input type="hidden" name="action" value="md">
    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
        <table class="table table-bordered table-hover footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=ad&amp;a=<?php echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='an') { echo 'dn'; } else { echo 'an'; } ?>"><?php echo $sprache->aname;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=ad&amp;a=<?php echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">ID</a></th>
                <th data-hide="phone"><a href="admin.php?w=ad&amp;a=<?php echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; } ?>"><?php echo $gsprache->status;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=ad&amp;a=<?php echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->type2;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=ad&amp;a=<?php echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->type;?></a></th>
                <th><?php echo $gsprache->export;?></a></th>
                <th><?php echo $gsprache->del;?></a></th>
                <th><?php echo $gsprache->mod;?></a></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['description'];?></td>
                <td><?php echo $table_row['id'];?></td>
                <td><i class="<?php if($table_row['active']=='Y') echo 'icon-ok'; else echo 'icon-ban-circle';?>"></i></td>
                <td><?php echo $table_row['gametype'];?></td>
                <td><?php echo $table_row['type'];?></td>
                <td><a href="admin.php?w=ad&amp;d=ex&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-primary"><i class="fa fa-download"></i></span></a></td>
                <td><a href="admin.php?w=ad&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=ad&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>
