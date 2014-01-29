<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->subnets;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->subnets;?> <a href="admin.php?w=sn&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=sn&amp;o=<?php if ($o=='ds') { echo 'as'; } else { echo 'ds'; } ?>"><?php echo $sprache->subnet;?></a></th>
                <th data-hide="phone"><a href="admin.php?w=sn&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID</a></th>
                <th data-hide="phone"><a href="admin.php?w=sn&amp;o=<?php if ($o=='dv') { echo 'dv'; } else { echo 'dv'; } ?>"><?php echo $sprache->vlan;?></a></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['subnet'];?></td>
                <td><?php echo $table_row['id'];?></td>
                <td><?php echo $table_row['vlanName'];?></td>
                <td><a href="admin.php?w=sn&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=sn&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>