<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->gameroot;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<?php if($reseller_id==0){ ?>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->gameroot;?> <a href="admin.php?w=ro&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<?php }?>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=ro&amp;d=md&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=ro&amp;d=md&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=ro&amp;d=md&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=ro&amp;d=md&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=ro&amp;d=md&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=ro&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ap') { echo 'dp'; } else { echo 'ap'; } ?>"><?php echo $sprache->haupt_ip;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=ro&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID</a></th>
                <th data-hide="phone"><a href="admin.php?w=ro&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ds') { echo 'as'; } else { echo 'ds'; } ?>"><?php echo $gsprache->status;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=ro&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='am') { echo 'dm'; } else { echo 'am'; } ?>"><?php echo $sprache->maxserver;?></a></th>
                <th data-hide="phone,tablet">Ram</th>
                <th data-hide="phone,tablet"><a href="admin.php?w=ro&amp;d=md&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='am') { echo 'dm'; } else { echo 'am'; } ?>"><?php echo $sprache->desc;?></a></th>
                <th><?php echo $gsSprache->reinstall;?></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td>
                    <a href="#modalID-<?php echo $table_row['id'];?>" role="button" class="btn" data-toggle="modal"><?php echo $table_row['ip'];?></a>
                    <div id="modalID-<?php echo $table_row['id'];?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel-<?php echo $table_row['id'];?>" aria-hidden="true">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 id="myModalLabel"><?php echo $table_row['ip'];?> <?php echo $table_row['description'];?></h3>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered table-hover table-condensed">
                                <thead>
                                <tr>
                                    <th><?php echo $gsSprache->server;?></th>
                                    <th><?php echo $gsSprache->reinstall;?></th>
                                    <th><?php echo $gsSprache->stop;?></th>
                                    <th><?php echo $gsSprache->restarts;?></th>
                                    <th><?php echo $gsprache->del;?></a></th>
                                    <th><?php echo $gsprache->mod;?></a></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($table_row['server'] as $row){ ?>
                                <tr class="<?php if($row['status']==1) echo 'success'; else if($row['status']==2) echo 'warning'; else echo 'error';?>">
                                    <td><?php echo '<img src="images/games/icons/'.$row['shorten'].'.png" alt="'.$row['shorten'].'" width="14" />';?> <?php echo $row['address'];?></td>
                                    <td><a href="admin.php?w=gs&amp;d=ri&amp;id=<?php echo $row['id'];?>"><span class="btn btn-mini btn-warning"><i class="fa fa-refresh"></i></span></a></td>
                                    <td><a href="admin.php?w=gs&amp;d=st&amp;id=<?php echo $row['id'];?>&amp;r=gs"><span class="btn btn-mini btn-danger"><i class="icon-white icon-stop"></i></span></a></td>
                                    <td><a href="admin.php?w=gs&amp;d=rs&amp;id=<?php echo $row['id'];?>&amp;r=gs"><span class="btn btn-mini btn-success"><i class="icon-white icon-play"></i></span></a></td>
                                    <td><a href="admin.php?w=gs&amp;d=dl&amp;id=<?php echo $row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                                    <td><a href="admin.php?w=gs&amp;d=md&amp;id=<?php echo $row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
                                </tr>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
                <td><?php echo $table_row['id'];?></td>
                <td><i class="<?php if($table_row['active']=='Y') echo 'icon-ok'; else echo 'icon-ban-circle';?>"></i></td>
                <td><?php echo $table_row['installedserver']."/".$table_row['maxserver'];?></td>
                <td><?php echo $table_row['assignedRam']."/".$table_row['ram'];?></td>
                <td><?php echo $table_row['description'];?></td>
                <td><a href="admin.php?w=ro&amp;d=ri&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="fa fa-refresh"></i></span></a></td>
                <td><?php if($reseller_id==0 and $pa['roots'] and $table_row['deleteAllowed']) { ?><a href="admin.php?w=ro&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></span></a><?php } ?></td>
                <td><a href="admin.php?w=ro&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>