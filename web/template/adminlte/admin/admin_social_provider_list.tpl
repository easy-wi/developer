<section class="content-header">
    <h1>Social Auth Provider</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><i class="fa fa-cloud"></i> Social Auth Provider</li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            Social Auth Provider <a href="admin.php?w=up&amp;d=ad"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body table-responsive">
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Service Provider</th>
                            <th><?php echo $gsprache->status;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($table as $table_row) { ?>
                        <tr>
                            <td><?php echo $table_row['name'];?></a></td>
                            <td><i class="fa <?php if($table_row['active']=='Y') echo 'fa-check-circle-o'; else echo 'fa-ban';?>"></i></td>
                            <td>
                                <a href="admin.php?w=up&amp;d=dl&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> <?php echo $gsprache->del;?></span></a>
                                <a href="admin.php?w=up&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> <?php echo $gsprache->mod;?></span></a>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Service Provider</th>
                            <th><?php echo $gsprache->status;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>