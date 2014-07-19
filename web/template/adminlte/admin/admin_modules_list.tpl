<section class="content-header">
    <h1><?php echo $gsprache->logs;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->modules;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="form-group">
        <?php echo $gsprache->modules;?> <a href="admin.php?w=mo&amp;d=ad"><span class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></span></a>
    </div>
    
    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th><?php echo $sprache->name;?></th>
                <th><?php echo $sprache->type;?></th>
                <th><?php echo $gsprache->del;?></a></th>
                <th><?php echo $gsprache->mod;?></a></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr class="<?php if($table_row['active']=='Y') echo 'success'; else echo 'error';?>">
                <td><?php echo $table_row['name'];?></td>
                <td><?php echo $table_row['type'];?></td>
                <td><?php if($table_row['id']>1000) { ?><a href="admin.php?w=mo&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></span></a><?php } ?></td>
                <td><a href="admin.php?w=mo&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
</section>