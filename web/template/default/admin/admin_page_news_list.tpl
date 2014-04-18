<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->news;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->news;?>: <a href="admin.php?w=pn&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=pn&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=pn&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=pn&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=pn&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=pn&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="admin.php?w=pn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->title;?></a></th>
                <th data-hide="phone"><a href="admin.php?w=pn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">ID</a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=pn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='al') { echo 'dl'; } else { echo 'al'; } ?>"><?php echo $sprache->languages;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=pn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='ar') { echo 'dr'; } else { echo 'ar'; } ?>"><?php echo $sprache->released;?></a></th>
                <th data-hide="phone"><a href="admin.php?w=pn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='aa') { echo 'da'; } else { echo 'aa'; } ?>"><?php echo $sprache->author;?></a></th>
                <th data-hide="phone,tablet"><a href="admin.php?w=pn&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='ad') { echo 'dd'; } else { echo 'ad'; } ?>"><?php echo $sprache->date;?></a></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr>
                <td><a href="<?php echo $table_row['link'];?>" alt="<?php echo $table_row['title'];?>" target="_blank"><?php echo $table_row['title'];?></a></td>
                <td><?php echo $table_row['id'];?></td>
                <td><?php echo implode(', ',$table_row['languages']);?></td>
                <td><?php echo $table_row['released'];?></td>
                <td><?php echo $table_row['author'];?></td>
                <td><?php echo $table_row['date'];?></td>
                <td><a href="admin.php?w=pn&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php?w=pn&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>