<section class="content-header">
    <h1><?php echo $gsprache->groups;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=us"><i class="fa fa-user"></i> <?php echo $gsprache->user;?></a></li>
        <li><a href="admin.php?w=ug"><i class="fa fa-group"></i> <?php echo $gsprache->groups;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->groups;?> <a href="admin.php?w=ug&amp;d=ad"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
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
                            <th><?php echo $sprache->groupname;?></th>
                            <th>ID</th>
                            <th><?php echo $sprache->active;?></th>
                            <th><?php echo $sprache->accounttype;?></th>
                            <th>Default</th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($table as $table_row) { ?>
                        <tr>
                            <td><?php echo $table_row['name'];?></td>
                            <td><?php echo $table_row['id'];?></td>
                            <td><?php echo ($table_row['active']=='Y') ? $gsprache->yes : $gsprache->no;?></td>
                            <td><?php echo $table_row['grouptype'];?></td>
                            <td><?php echo $table_row['defaultgroup'];?></td>
                            <td>
                                <a href="admin.php?w=ug&amp;d=dl&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> <?php echo $gsprache->del;?></span></a>
                                <a href="admin.php?w=ug&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> <?php echo $gsprache->mod;?></span></a>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><?php echo $sprache->groupname;?></th>
                            <th>ID</th>
                            <th><?php echo $sprache->active;?></th>
                            <th><?php echo $sprache->accounttype;?></th>
                            <th>Default</th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>