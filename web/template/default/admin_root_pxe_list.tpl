<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li>PXE <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        PXE <a href="admin.php?w=rp&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=rp&amp;o=<?php if ($o=='dp') { echo 'ap'; } else { echo 'dp'; } ?>">IP</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=rp&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=rp&amp;t=<?php echo $list_type; ?>&amp;g=<?php echo $list_gtype; ?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; } ?>"><?php echo $gsprache->status;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=rp&amp;o=<?php if ($o=='dd') { echo 'dd'; } else { echo 'dd'; } ?>"><?php echo $sprache->description; ?></a></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['ip'];?></td>
                <td><?php echo $table_row['id'];?></td>
                <td><i class="<?php if($table_row['active']=='Y') echo 'icon-ok'; else echo 'icon-ban-circle';?>"></i></td>
                <td><?php echo $table_row['description'];?></td>
                <td><a href="admin.php?w=rp&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=rp&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>