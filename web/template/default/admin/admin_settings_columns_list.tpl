<section class="content-header">
    <h1><?php echo $gsprache->columns;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li><a href="admin.php?w=cc"><i class="fa fa-list"></i> <?php echo $gsprache->columns;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->columns;?>: <a href="admin.php?w=cc&amp;d=ad"><span class="btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?php echo $sprache->name;?></th>
                                <th>ID</a></th>
                                <th><?php echo $gsprache->status;?></th>
                                <th><?php echo $sprache->type; ?></th>
                                <th><?php echo $gsprache->action;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($table as $table_row) { ?>
                            <tr>
                                <td><?php echo $table_row['name'];?></td>
                                <td><?php echo $table_row['id'];?></td>
                                <td><?php echo ($table_row['active']=='Y') ? '<span class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i>' : '<span class="btn btn-sm btn-warning"><i class="fa fa-exclamation-triangle"></i>';?></td>
                                <td><?php echo $table_row['type'];?></td>
                                <td>
                                    <a href="admin.php?w=cc&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></span></a>
                                    <a href="admin.php?w=cc&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> <?php echo $gsprache->mod;?></span></a>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><?php echo $sprache->name;?></th>
                                <th>ID</a></th>
                                <th><?php echo $gsprache->status;?></th>
                                <th><?php echo $sprache->type; ?></th>
                                <th><?php echo $gsprache->action;?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>