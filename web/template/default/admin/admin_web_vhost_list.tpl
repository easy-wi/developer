<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=wv"><?php echo $gsprache->webspace;?> Vhost</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->webspace;?> Vhost <a href="admin.php?w=wv&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i> <?php echo $gsprache->add;?></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=wv&amp;o=<?php echo $o;?>&amp;a=<?php echo $amount;?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=wv&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=wv&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=wv&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=wv&amp;o=<?php echo $o;?>&amp;a=<?php echo $amount;?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=wv&amp;a=<?php echo $amount;?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ad') { echo 'dd'; } else { echo 'ad'; } ?>"><?php echo $sprache->dns;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=wv&amp;a=<?php echo $amount;?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">ID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=wv&amp;a=<?php echo $amount;?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; } ?>"><?php echo $gsprache->status;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=wv&amp;a=<?php echo $amount;?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ac') { echo 'dc'; } else { echo 'ac'; } ?>"><?php echo $dedicatedLanguage->user;?></a></th>
                <th data-hide="phone,tablet"><?php echo $sprache->hddUsage;?></th>
                <th data-hide="phone,tablet"><?php echo $gsprache->jobPending;?></th>
                <th><?php echo $dedicatedLanguage->reinstall;?></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['dns'];?></td>
                <td><?php echo $table_row['id'];?></td>
                <td><i class="<?php if($table_row['active']=='Y') echo 'fa fa-check'; else echo 'fa fa-ban';?>"></i></td>
                <td><?php if(isid($table_row['userID'], 10)) { ?><a href="switch.php?id=<?php echo $table_row['userID'];?>"><?php echo $table_row['cname'];?></a><?php }?></td>
                <td><?php echo $table_row['hddUsage'];?> MB</td>
                <td><?php echo $table_row['jobPending'];?></td>
                <td><a href="admin.php?w=wv&amp;d=ri&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-warning"><i class="fa fa-refresh"></i></span></a></td>
                <td><a href="admin.php?w=wv&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=wv&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>