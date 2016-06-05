<section class="content-header">
    <h1><?php echo $sprache->heading;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ti"><i class="fa fa-life-ring"></i> <?php echo $gsprache->support;?></a></li>
        <li><a href="admin.php?w=ti&amp;d=mt"><i class="fa fa-wrench"></i> <?php echo $sprache->heading;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <?php echo $sprache->heading;?> <a href="admin.php?w=ti&amp;d=at"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
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
                                <th><?php echo $sprache->topic;?></th>
                                <th><?php echo $sprache->priority;?></th>
                                <th><?php echo $gsprache->action;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($table as $table_row) { ?>
                            <tr>
                                <td><?php echo $table_row['topic'];?></td>
                                <td><?php echo $table_row['id'];?></td>
                                <td><?php echo $table_row['mTopic'];?></td>
                                <td><?php echo $table_row['priority'];?></td>
                                <td>
                                    <a href="admin.php?w=ti&amp;d=dt&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> <?php echo $gsprache->del;?></span></a>
                                    <a href="admin.php?w=ti&amp;d=mt&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> <?php echo $gsprache->mod;?></span></a>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><?php echo $sprache->name;?></th>
                                <th>ID</a></th>
                                <th><?php echo $sprache->topic;?></th>
                                <th><?php echo $sprache->priority;?></th>
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