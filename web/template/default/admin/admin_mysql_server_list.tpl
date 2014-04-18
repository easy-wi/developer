<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li>MySQL Server <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        MySQL Server <a href="admin.php?w=my&amp;d=as"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=my&amp;d=ms&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=my&amp;d=ms&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=my&amp;d=ms&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=my&amp;d=ms&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=my&amp;d=ms&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=my&amp;d=ms&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='dp') { echo 'ap'; } else { echo 'dp'; } ?>">IP<a/></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=my&amp;d=ms&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID<a/></th>
                <th data-hide="phone"><a href="admin.php?w=my&amp;d=ms&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ds') { echo 'as'; } else { echo 'ds'; } ?>"><?php echo $gsprache->status;?><a/></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=my&amp;d=ms&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='df') { echo 'af'; } else { echo 'df'; } ?>"><?php echo $sprache->interface;?></a></th>
                <th data-hide="phone,tablet"><?php echo $sprache->usage;?></th>
                <th><?php echo $gsprache->reinstall;?></a></th>
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
                            <h3 id="myModalLabel"><?php echo $table_row['ip'];?></h3>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered table-hover table-condensed">
                                <thead>
                                <tr>
                                    <th><?php echo $sprache->dbname;?></th>
                                    <th><?php echo $gsprache->reinstall;?></a></th>
                                    <th><?php echo $gsprache->del;?></a></th>
                                    <th><?php echo $gsprache->mod;?></a></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($table_row['server'] as $row){ ?>
                                <tr class="<?php if($row['status']==1) echo 'success'; else if($row['status']==2) echo 'warning'; else echo 'error';?>">
                                    <td><?php echo $row['address'];?></td>
                                    <td><a href="admin.php?w=my&amp;d=rd&amp;id=<?php echo $row['id'];?>" ><span class="btn btn-mini btn-warning"><i class="fa fa-refresh"></i></span></a></td>
                                    <td><a href="admin.php?w=my&amp;d=dd&amp;id=<?php echo $row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                                    <td><a href="admin.php?w=my&amp;d=md&amp;id=<?php echo $row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
                                </tr>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
                <td><?php echo $table_row['id'];?></td>
                <td><i class="<?php if($table_row['active']=='Y') echo 'icon-ok'; else echo 'icon-ban-circle';?>"></i></td>
                <td><a href="<?php echo $table_row['interface'];?>" target="_blank"><?php echo $table_row['interface'];?></a></td>
                <td><?php echo $table_row['dbcount']."/".$table_row['max_databases'];?></td>
                <td><a href="admin.php?w=my&amp;d=rs&amp;id=<?php echo $table_row['id'];?>"<span class="btn btn-mini btn-warning"><i class="fa fa-refresh"></i></span></a></td>
                <td><a href="admin.php?w=my&amp;d=ds&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=my&amp;d=ms&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>