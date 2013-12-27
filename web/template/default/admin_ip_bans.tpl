<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active">IP Bans</li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=ib&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur; ?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=ib&amp;o=<?php echo $o; ?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=ib&amp;a=50&amp;o=<?php echo $o; ?>&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=ib&amp;a=100&amp;o=<?php echo $o; ?>&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=ib&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor; ?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form method="post" action="admin.php?w=ib&amp;r=ib" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">
            <div class="row-fluid">
                <table class="table table-bordered table-hover table-striped footable">
                    <thead>
                    <tr>
                        <th data-class="expand"><a href="admin.php?w=ib&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ap') { echo 'dp'; } else { echo 'ap'; } ?>"><?php echo $sprache->ip;?></a></th>
                        <th data-hide="phone,tablet"><a href="admin.php?w=ib&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">Bann ID</a></th>
                        <th data-hide="phone"><a href="admin.php?w=ib&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->banned_till;?></a></th>
                        <th data-hide="phone,tablet"><a href="admin.php?w=ib&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='af') { echo 'df'; } else { echo 'af'; } ?>"><?php echo $sprache->failcount;?></a></th>
                        <th data-hide="phone"><a href="admin.php?w=ib&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ar') { echo 'dr'; } else { echo 'ar'; } ?>"><?php echo $sprache->reason;?></a></th>
                        <th><?php echo $gsprache->del;?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($table as $table_row) { ?>
                    <tr>
                        <td><?php echo $table_row['badip']; ?></td>
                        <td><?php echo $table_row['id'];?></td>
                        <td><?php echo $table_row['logday']." ".$table_row['loghour']; ?></td>
                        <td><?php echo $table_row['failcount'].'/'.$faillogins; ?></a></td>
                        <td><?php echo $table_row['reason']; ?></a></td>
                        <td><input type="checkbox" name="id[]" value="<?php echo $table_row['id'];?>"></td>
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