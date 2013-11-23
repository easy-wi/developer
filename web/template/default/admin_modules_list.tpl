<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->modules;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->modules;?> <a href="admin.php?w=mo&amp;d=ad"<span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped">
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
                <td><?php if($table_row['id']>1000) { ?><a href="admin.php?w=mo&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a><?php } ?></td>
                <td><a href="admin.php?w=mo&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>