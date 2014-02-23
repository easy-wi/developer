<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->user;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->user;?> <a href="admin.php?w=us&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11">
        <h4><?php echo $sprache->active;?></h4>
        <a href="<?php echo $ticketLinks['Y'];?>"><?php echo $sprache->active;?></a><?php if(in_array('Y',$selected)) { ?> <i class="icon-check"></i><?php }?>,
        <a href="<?php echo $ticketLinks['N'];?>"><?php echo $sprache->activeIn;?></a><?php if(in_array('N',$selected)) { ?> <i class="icon-check"></i><?php }?>,
        <a href="<?php echo $ticketLinks['R'];?>"><?php echo $sprache->activeRegister;?></a><?php if(in_array('R',$selected)) { ?> <i class="icon-check"></i><?php }?>,
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=us&amp;d=md&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=us&amp;d=md&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=us&amp;d=md&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=us&amp;d=md&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=us&amp;d=md&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=us&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='au') { echo 'du'; } else { echo 'au'; } ?>"><?php echo $sprache->user;?></a></th>
                <th data-hide="phone"><a href="admin.php?w=us&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=us&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='an') { echo 'dn'; } else { echo 'an'; } ?>"><?php echo $sprache->fname;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=us&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='aa') { echo 'da'; } else { echo 'aa'; } ?>"><?php echo $sprache->active;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=us&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->accounttype;?></a></th>
                <th data-hide="phone,tablet"><?php echo $gsprache->jobPending;?></th>
                <?php if ($pa['user_users'] or $pa['user']) { ?><th><?php echo $gsprache->del;?></th><?php } ?>
                <?php if($pa['userPassword']){ ?><th><?php echo $sprache->password;?></th><?php } ?>
                <?php if ($pa['user_users'] or $pa['user']) { ?><th><?php echo $gsprache->mod;?></th><?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><?php if (!$table_row['adminaccount']) { ?><a href="switch.php?id=<?php echo $table_row['id'];?>"><?php echo $table_row['cname'];?></a><?php } else { ?><?php echo $table_row['cname'];?><?php } ?></td>
                <td><?php echo $table_row['id'];?></td>
                <td><?php echo $table_row['name'];?></td>
                <td><i class="<?php if($table_row['active']=='Y') echo 'icon-ok'; else if($table_row['active']=='R') echo 'icon-warning-sign'; else echo 'icon-ban-circle';?>"></i></td>
                <td><?php echo $table_row['accounttype'];?></td>
                <td><?php echo $table_row['jobPending'];?></td>
                <td><?php if ($table_row['id']!=$admin_id and (($table_row['adminaccount'] and $pa['user']) or (!$table_row['adminaccount'] and ($pa['user_users'] or $pa['user'])))) { ?><a href="admin.php?w=us&amp;d=md&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></span></a><?php } ?></td>
                <td><?php if($pa['userPassword'] and (($table_row['adminaccount'] and $pa['user']) or !$table_row['adminaccount'])){ ?><a href="admin.php?w=us&amp;d=pw&amp;id=<?php echo $table_row['id'];?>" alt="modify"><span class="btn btn-mini btn-primary"><i class="icon-white icon-lock"></i></span></a><?php } ?></td>
                <td><?php if (($table_row['adminaccount'] and $pa['user']) or (!$table_row['adminaccount'] and ($pa['user_users'] or $pa['user']))) { ?><a href="admin.php?w=us&amp;d=md&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a><?php } ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>