<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->virtual;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php if(($reseller_id==0 or $admin_id==$reseller_id) and $pa['addvserver'] and $licenceDetails['lVs']>0) { ?><?php echo $gsprache->virtual;?> <a href="admin.php?w=vs&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a><?php } ?>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=vs&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=vs&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=vs&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=vs&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=vs&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=vs&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='ap') { echo 'dp'; } else { echo 'ap'; } ?>"><?php echo $sprache->ip;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vs&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">ID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vs&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='ah') { echo 'dh'; } else { echo 'ah'; } ?>"><?php echo $gsprache->hostsystem;?> ID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=rh&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ac') { echo 'dc'; } else { echo 'ac'; } ?>"><?php echo $sprache->user;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vs&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; } ?>"><?php echo $sprache->status;?></a></th>
                <th data-hide="phone,tablet"><?php echo $gsprache->jobPending;?></th>
                <th><?php echo $sprache->rescue.' / '.$sprache->reinstall;?></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['cip'];?></td>
                <td><?php echo $table_row['id'];?></td>
                <td><?php echo $table_row['hid'];?></td>
                <td><?php if(isid($table_row['userid'],10)) { ?><a href="switch.php?id=<?php echo $table_row['userid'];?>"><?php echo $table_row['cname'];?></a><?php }?></td>
                <td><?php echo $table_row['status'];?></td>
                <td><?php echo $table_row['jobPending'];?></td>
                <td><a href="admin.php?w=vs&amp;d=va&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-primary"><i class="fa fa-refresh"></i></span></a></td>
                <td><a href="admin.php?w=vs&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=vs&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>