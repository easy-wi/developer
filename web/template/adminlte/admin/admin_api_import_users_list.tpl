<section class="content-header">
    <h1><?php echo $gsprache->userImport.' '.$gsprache->overview;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->userImport;?></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-11">
            <?php echo $gsprache->userImport;?> <a href="admin.php?w=ui&amp;d=ad"><span class="btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-md-11">
            <div class="box box-info">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?php echo $sprache->domain;?></th>
                                <th><?php echo $gsprache->active;?></th>
                                <th>ID</th>
                                <th><?php echo $sprache->lastCheck;?></th>
                                <th><?php echo $gsprache->action;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($table as $table_row) { ?>
                            <tr>
                                <td><?php echo $table_row['domain'];?></td>
                                <td><i class="<?php if($table_row['active']=='Y') echo 'fa fa-check'; else echo 'fa fa-ban';?>"></i></td>
                                <td><?php echo $table_row['id'];?></td>
                                <td><?php echo $table_row['lastCheck'];?></td>
                                <td>
                                    <a href="admin.php?w=ui&amp;d=dl&amp;id=<?php echo $table_row['id'];?>"><span class="btn-sm btn-danger"><i class="fa fa-trash-o"></i><?php echo $gsprache->del;?></span></a>
                                    <a href="admin.php?w=ui&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn-sm btn-primary"><i class="fa fa-edit"></i><?php echo $gsprache->mod;?></span></a>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><?php echo $sprache->domain;?></th>
                                <th><?php echo $gsprache->active;?></th>
                                <th>ID</th>
                                <th><?php echo $sprache->lastCheck;?></th>
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