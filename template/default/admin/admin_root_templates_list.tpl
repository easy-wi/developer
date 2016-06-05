<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->template;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->template;?> <a href="admin.php?w=ot&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=ot&amp;d=md&amp;o=<?php if ($o=='ae') { echo 'de'; } else { echo 'ae'; } ?>"><?php echo $sprache->description;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=ot&amp;d=md&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">ID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=ot&amp;d=md&amp;o=<?php if ($o=='ab') { echo 'db'; } else { echo 'ab'; } ?>">Bit</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=ot&amp;d=md&amp;o=<?php if ($o=='ad') { echo 'dd'; } else { echo 'ad'; } ?>"><?php echo $sprache->distro;?></a></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['description'];?></td>
                <td><?php echo $table_row['id'];?></td>
                <td><?php echo $table_row['bitversion'];?></td>
                <td><?php echo $table_row['distro'];?></td>
                <td><a href="admin.php?w=ot&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=ot&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>