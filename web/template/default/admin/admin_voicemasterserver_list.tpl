<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->voiceserver." ".$gsprache->master;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->voiceserver." ".$gsprache->master;?> <a href="admin.php?w=vm&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=vm&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=vm&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=vm&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=vm&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=vm&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=vm&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ap') { echo 'dp'; } else { echo 'ap'; } ?>"><?php echo $sprache->ssh_ip;?></a></th>
                <th data-hide="phone"><a href="admin.php?w=vm&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vm&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->type;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vm&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; } ?>"><?php echo $sprache->installedserver;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vm&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='al') { echo 'dl'; } else { echo 'al'; } ?>"><?php echo $sprache->installedslots;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=vm&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ad') { echo 'dd'; } else { echo 'ad'; } ?>"><?php echo $sprache->defaultdns;?></a></th>
                <th><?php echo $sprache->import;?></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr class="<?php if($table_row['img']=='16_ok') echo 'success'; else if($table_row['img']=='16_bad') echo 'warning'; else echo 'error';?>">
                <td>
                    <a href="#modalID-<?php echo $table_row['id'];?>" role="button" class="btn" data-toggle="modal"><?php echo $table_row['ip'];?></a>
                    <div id="modalID-<?php echo $table_row['id'];?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel-<?php echo $table_row['id'];?>" aria-hidden="true">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 id="myModalLabel"><?php echo $table_row['ip'];?></h3>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered table-hover table-condensed">
                                <thead>
                                <tr>
                                    <th><?php echo $sprache->server;?></th>
                                    <th><?php echo $gsprache->del;?></a></th>
                                    <th><?php echo $gsprache->mod;?></a></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($table_row['server'] as $row){ ?>
                                <tr class="<?php if($row['status']==1) echo 'success'; else if($row['status']==2) echo 'warning'; else echo 'error';?>">
                                    <td><?php echo $row['address'];?></td>
                                    <td><a href="admin.php?w=vo&amp;d=dl&amp;id=<?php echo $row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                                    <td><a href="admin.php?w=vo&amp;d=md&amp;id=<?php echo $row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
                                </tr>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
                <td><?php echo $table_row['id'];?></td>
                <td><?php echo $table_row['type'];?></td>
                <td><?php echo $table_row['installedserver'];?></td>
                <td><?php echo $table_row['installedslots'];?></td>
                <td><?php echo $table_row['defaultdns'];?></td>
                <td><?php if($table_row['managedServer']!='Y' or $reseller_id==0){ ?><a href="admin.php?w=vm&amp;d=ri&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="fa fa-refresh"></i></span></a><?php } ?></td>
                <td><?php if($table_row['managedServer']!='Y' or $reseller_id==0){ ?><a href="admin.php?w=vm&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a><?php } ?></td>
                <td><?php if($table_row['managedServer']!='Y' or $reseller_id==0){ ?><a href="admin.php?w=vm&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a><?php } ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>