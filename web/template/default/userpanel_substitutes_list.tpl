<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->substitutes;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->substitutes;?> <a href="userpanel.php?w=su&amp;d=ad"<span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th><?php echo $sprache->user;?></th>
                <th><?php echo $gsprache->del;?></a></th>
                <th><?php echo $gsprache->mod;?></a></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr class="<?php if($table_row['active']=='Y') echo 'success'; else echo 'warning';?>">
                <td><?php echo $table_row['loginName'];?></td>
                <td><a href="userpanel.php?w=su&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-remove-sign"></i></span></a></td>
                <td><a href="userpanel.php?w=su&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>