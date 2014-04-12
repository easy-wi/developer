<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->webspace." ".$gsprache->master;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->webspace." ".$gsprache->master;?> <a href="admin.php?w=wm&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=wm&amp;o=<?php if ($o=='ap') { echo 'dp'; } else { echo 'ap'; } ?>"><?php echo $dedicatedLanguage->ip;?></a></th>
                <th data-hide="phone"><a href="admin.php?w=wm&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID</a></th>
                <th data-hide="phone"><a href="admin.php?w=wm&amp;o=<?php if ($o=='ad') { echo 'dd'; } else { echo 'ad'; } ?>"><?php echo $dedicatedLanguage->description;?></a></th>
                <th data-hide="phone,tablet"><?php echo $sprache->installedVhost;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->hddUsage;?></th>
                <th data-hide="phone,tablet"><?php echo $sprache->installedHDD;?></th>
                <th><?php echo $sprache->recreate;?></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr class="<?php if($table_row['active']=='Y') echo 'success'; else echo 'error';?>">
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
                                    <th><?php echo $sprache->dns;?></th>
                                    <th><?php echo $gsprache->del;?></a></th>
                                    <th><?php echo $gsprache->mod;?></a></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($table_row['dns'] as $row){ ?>
                                <tr class="<?php if($row['active']=='Y') echo 'success'; else echo 'warning';?>">
                                    <td><?php echo $row['dns'];?></td>
                                    <td><a href="admin.php?w=wv&amp;d=dl&amp;id=<?php echo $row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                                    <td><a href="admin.php?w=wv&amp;d=md&amp;id=<?php echo $row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
                                </tr>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
                <td><?php echo $table_row['id'];?></td>
                <td><?php echo $table_row['description'];?></td>
                <td><?php echo $table_row['maxVhost'];?></td>
                <td><?php echo $table_row['hddUsage'];?> MB</td>
                <td><?php echo $table_row['maxHDD'];?> MB</td>
                <td><a href="admin.php?w=wm&amp;d=ri&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-warning"><i class="fa fa-refresh"></i></span></a></td>
                <td><a href="admin.php?w=wm&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=wm&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>