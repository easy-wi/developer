<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->substitutes;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_substitutes_list;?></div>
</div>
<hr>
<div class="row-fluid">
    <div class="span6">
        <a href="userpanel.php?w=su&amp;d=ad"<span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i> <?php echo $gsprache->substitutes;?></span></a>
    </div>
</div>
<br>
<div class="row-fluid">
    <div class="span8">
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th><?php echo $sprache->user;?></th>
                <th> </th>
                <th> </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr class="<?php if($table_row['active']=='Y') echo 'success'; else echo 'warning';?>">
                <td><?php echo $table_row['loginName'];?></td>
                <td class="span1"><a href="userpanel.php?w=su&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></span></a></td>
                <td class="span1"><a href="userpanel.php?w=su&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i> <?php echo $gsprache->mod;?></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>