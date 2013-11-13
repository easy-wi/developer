<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->feeds.' '.$gsprache->news;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->feeds.' '.$gsprache->news;?> <a href="admin.php?w=fn&amp;d=ud&amp;r=fn"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=fn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=fn&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=fn&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=fn&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=fn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<form action="admin.php?w=fn&amp;d=md&amp;r=fn" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
    <input type="hidden" name="action" value="md">
    <div class="row-fluid">
        <div class="span11">
            <table class="table table-bordered table-hover table-striped footable">
                <thead>
                <tr>
                    <th data-class="expand"><a href="admin.php?w=fn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ah') { echo 'dh'; } else { echo 'ah'; } ?>"><?php echo $sprache->title;?></a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=fn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID:</a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=fn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>">Twitter:</th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=fn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='dd') { echo 'ad'; } else { echo 'dd'; } ?>"><?php echo $sprache->pubDate;?></a></th>
                    <th><a href="admin.php?w=fn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ds') { echo 'as'; } else { echo 'ds'; } ?>"><?php echo $sprache->status;?></a></th>
                    <th><?php echo $gsprache->del;?>:</a></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($table as $table_row) { ?>
                <tr>
                    <td><a href="<?php echo $table_row['link'];?>" target="_blank"><?php echo $table_row['title'];?></a></td>
                    <td><?php echo $table_row['id'];?><input type="hidden" name="ids[<?php echo $table_row['id'];?>][id]" value="<?php echo $table_row['id'];?>"></td>
                    <td><?php echo $table_row['twitter'];?></td>
                    <td><?php echo $table_row['pubDate'];?></td>
                    <td><img src="images/<?php echo $table_row['img'];?>.png" alt="<?php echo $table_row['alt'];?>" title="<?php echo $table_row['alt'];?>" /> <input type="checkbox" name="ids[<?php echo $table_row['id'];?>][active]" value="Y" <?php if($table_row['active']=='Y') echo 'checked="checked"'; ?>/></td>
                    <td><input type="checkbox" name="ids[<?php echo $table_row['id'];?>][dl]" value="Y"/></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span11">
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </div>
    </div>
</form>