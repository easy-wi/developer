<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->jobs.' '.$gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=jb&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>&amp;o=<?php echo $o;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=jb&amp;o=<?php echo $o; ?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=jb&amp;a=50&amp;o=<?php echo $o; ?>&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=jb&amp;a=100&amp;o=<?php echo $o; ?>&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=jb&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>&amp;o=<?php echo $o;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form method="post" action="admin.php?w=jb&amp;r=jb" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">
            <div class="row-fluid">
                <table class="table table-bordered table-hover footable">
                    <thead>
                    <tr>
                        <th data-class="expand"><a href="admin.php?w=jb&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;o=<?php if ($o=='ac') { echo 'dc'; } else { echo 'ac'; } ?>"><?php echo $sprache->action;?></a></th>
                        <th><a href="admin.php?w=jb&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; } ?>"><?php echo $sprache->status;?></a></th>
                        <th data-hide="phone"><a href="admin.php?w=jb&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">jobID:</a></th>
                        <th data-hide="phone,tablet"><a href="admin.php?w=jb&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->type;?></a></th>
                        <th data-hide="phone,tablet"><a href="admin.php?w=jb&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;o=<?php if ($o=='au') { echo 'du'; } else { echo 'au'; } ?>">userID</a></th>
                        <th data-hide="phone,tablet"><a href="admin.php?w=jb&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;o=<?php if ($o=='an') { echo 'dn'; } else { echo 'an'; } ?>"><?php echo $sprache->name;?></a></th>
                        <th data-hide="phone,tablet"><a href="admin.php?w=jb&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;o=<?php if ($o=='ad') { echo 'dd'; } else { echo 'ad'; } ?>"><?php echo $sprache->date;?></a></th>
                        <th data-hide="phone,tablet"><a href="admin.php?w=jb&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;o=<?php if ($o=='aa') { echo 'da'; } else { echo 'aa'; } ?>"><?php echo $gsprache->api;?></a></th>
                        <th><?php echo $gsprache->del;?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($table as $table_row) { ?>
                    <tr class="<?php if($table_row['status']==null) echo 'info'; else if($table_row['status']==1) echo 'error'; else if($table_row['status']==2) echo 'warning'; else echo 'success';?>">
                        <td><?php echo $table_row['action']; ?></td>
                        <td><i class="<?php if($table_row['status']==null) echo 'icon-refresh'; else if($table_row['status']==1) echo 'icon-ban-circle'; else if($table_row['status']==2) echo 'icon-warning-sign'; else echo 'icon-ok';?>"></i></td>
                        <td><?php echo $table_row['jobID']; ?></td>
                        <td><?php echo $table_row['type']; ?></td>
                        <td><?php echo $table_row['userID']; ?></td>
                        <td><?php echo $table_row['name']; ?></td>
                        <td><?php echo $table_row['date']; ?></td>
                        <td><?php echo $table_row['api']; ?></td>
                        <td><input type="checkbox" name="id[]" value="<?php echo $table_row['jobID'];?>"></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="row-fluid">
                <div class="control-group span6">
                    <label class="checkbox inline" for="checkAll"><?php echo $sprache->all;?></label>
                    <div class="controls checkbox inline">
                        <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'id[]')">
                    </div>
                </div>
                <div class="control-group span6">
                    <label class="control-label" for="inputDelete"></label>
                    <div class="controls">
                        <button class="btn btn-danger pull-right" id="inputDelete" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>