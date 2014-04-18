<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li>MySQL <?php echo $gsprache->databases;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        MySQL <?php echo $gsprache->databases;?> <a href="admin.php?w=my&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=my&amp;d=md&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=my&amp;d=md&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=my&amp;d=md&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=my&amp;d=md&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=my&amp;d=md&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=my&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='dn') { echo 'dn'; } else { echo 'dn'; } ?>"><?php echo $sprache->dbname;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=my&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='dd') { echo 'ad'; } else { echo 'dd'; } ?>"><?php echo $sprache->description;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=my&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID</a></th>
                <th data-hide="phone"><a href="admin.php?w=my&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ds') { echo 'as'; } else { echo 'ds'; } ?>"><?php echo $gsprache->status;?></a></th>
                <th data-hide="phone"><a href="admin.php?w=my&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='du') { echo 'au'; } else { echo 'du'; } ?>"><?php echo $gsprache->user;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=my&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='dp') { echo 'ap'; } else { echo 'dp'; } ?>">IP</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=my&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='aj') { echo 'dj'; } else { echo 'aj'; } ?>"><?php echo $gsprache->jobPending;?></a></th>
                <th><?php echo $gsprache->reinstall;?></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['dbname'];?></td>
                <td><?php echo $table_row['description'];?></td>
                <td><?php echo $table_row['id'];?></td>
                <td><i class="<?php if($table_row['active']=='Y') echo 'icon-ok'; else echo 'icon-ban-circle';?>"></i></td>
                <td><a href="switch.php?id=<?php echo $table_row['uid'];?>"><?php echo $table_row['cname'];?></a></td>
                <td><a href="<?php echo $table_row['interface'];?>" target="_blank"><?php echo $table_row['ip'];?></a></td>
                <td><?php echo $table_row['jobPending'];?></td>
                <td><a href="admin.php?w=my&amp;d=rd&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-warning"><i class="fa fa-refresh"></i></span></a></td>
                <td><a href="admin.php?w=my&amp;d=dd&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=my&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>