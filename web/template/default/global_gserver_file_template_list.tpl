<section class="content-header">
    <h1><?php echo $gsprache->template;?></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $targetFile;?>"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->gameserver.' '.$gsprache->file.' '.$gsprache->template;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-11">
            <div class="box box-info">
                <div class="box-body">
                    <div>
                        <?php echo $gsprache->template;?> <a href="<?php echo $targetFile;?>?w=gt&amp;d=ad"<span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th><?php echo $sprache->abkuerz;?></th>
                                <th>ID</th>
                                <th><?php echo $sprache->game;?></th>
                                <th><?php echo $gsprache->del;?></th>
                                <th><?php echo $gsprache->mod;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($table as $table_row) { ?>
                            <tr>
                                <td><?php echo $table_row['name'];?></td>
                                <td><?php echo $table_row['id'];?></td>
                                <td><?php echo $table_row['servertype'];?></td>
                                <td><a href="<?php echo $targetFile;?>?w=gt&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                                <td><a href="<?php echo $targetFile;?>?w=gt&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></span></a></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><?php echo $sprache->abkuerz;?></th>
                                <th>ID</th>
                                <th><?php echo $sprache->game;?></th>
                                <th><?php echo $gsprache->del;?></th>
                                <th><?php echo $gsprache->mod;?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
