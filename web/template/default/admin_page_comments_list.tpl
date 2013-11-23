<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->comments;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <h4>Total (<?php echo $totalCount;?>)</h4>
        <a href="admin.php?w=pc&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;o=<?php echo $o;?>&amp;spam=<?php echo $s;?>">Spam</a> (<?php echo $spamCount;?>)<?php if($ui->id('spam',1,'get')==1) echo ' <i class="icon-check"></i>';?>,
        <a href="admin.php?w=pc&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;o=<?php echo $o;?>&amp;mod=<?php echo $m;?>"><?php echo $sprache->moderate;?></a> (<?php echo $moderationExpectedCount;?>)<?php if($ui->id('mod',1,'get')==1) echo ' <i class="icon-check"></i>';?>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=pc&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=pc&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=pc&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=pc&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=pc&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=pc&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; }; echo $getParams; ?>">URL</a></th>
                <th data-hide="phone"><a href="admin.php?w=pc&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='an') { echo 'dn'; } else { echo 'an'; }; echo $getParams; ?>"><?php echo $sprache->author;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=pc&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; }; echo $getParams; ?>">ID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=pc&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='ad') { echo 'dd'; } else { echo 'ad'; }; echo $getParams; ?>"><?php echo $sprache->date;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=pc&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='am') { echo 'dm'; } else { echo 'am'; }; echo $getParams; ?>"><?php echo $sprache->moderate;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=pc&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; }; echo $getParams; ?>">Spam</a></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><a href="<?php echo $table_row['link'];?>" target="_blank"><?php echo $table_row['title'];?></a></td>
                <td onclick="textdrop('<?php echo $table_row['commentID'];?>');"><?php echo $table_row['authorname'];?></td>
                <td onclick="textdrop('<?php echo $table_row['commentID'];?>');"><?php echo $table_row['commentID'];?></td>
                <td onclick="textdrop('<?php echo $table_row['commentID'];?>');"><?php echo $table_row['date'];?></td>
                <td onclick="textdrop('<?php echo $table_row['commentID'];?>');"><?php echo $table_row['moderated'];?></td>
                <td onclick="textdrop('<?php echo $table_row['commentID'];?>');"><?php echo $table_row['spam'];?></td>
                <td><a href="admin.php?w=pc&amp;d=dl&amp;r=pc&amp;id=<?php echo $table_row['commentID'];?>"onclick="return confirm('<?php echo $gsprache->sure;?>');"><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=pc&amp;d=md&amp;id=<?php echo $table_row['commentID'];?>"><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>