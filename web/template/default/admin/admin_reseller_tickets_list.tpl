<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->support;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <h4><?php echo $sprache->status;?></h4>
        <a href="<?php echo $ticketLinks['N'];?>"><?php echo $sprache->status_new;?></a><?php if(in_array('N',$selected)) { ?> <i class="icon-check"></i><?php }?>,
        <a href="<?php echo $ticketLinks['P'];?>"><?php echo $sprache->status_process;?></a><?php if(in_array('P',$selected)) { ?> <i class="icon-check"></i><?php }?>,
        <a href="<?php echo $ticketLinks['R'];?>"><?php echo $sprache->status_reopen;?></a><?php if(in_array('R',$selected)) { ?> <i class="icon-check"></i><?php }?>,
        <a href="<?php echo $ticketLinks['A'];?>"><?php echo $sprache->status_author;?></a><?php if(in_array('A',$selected)) { ?> <i class="icon-check"></i><?php }?>,
        <a href="<?php echo $ticketLinks['D'];?>"><?php echo $sprache->status_done;?></a><?php if(in_array('D',$selected)) { ?> <i class="icon-check"></i><?php }?>,
        <a href="<?php echo $ticketLinks['C'];?>"><?php echo $sprache->status_confirmed;?></a><?php if(in_array('C',$selected)) { ?> <i class="icon-check"></i><?php }?>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span12 pagination">
        <ul>
            <li><a href="<?php echo $ticketLinks['all'];?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="<?php echo $ticketLinks['amount'];?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="<?php echo $ticketLinks['amount'];?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="<?php echo $ticketLinks['amount'];?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="<?php echo $ticketLinks['all'];?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <table class="table table-condensed table-bordered table-hover footable">
            <thead>
            <tr>
                <th data-class="expand"><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->subject;?></a></th>
                <th data-hide="phone"><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">ID</a></th>
                <th data-hide="phone,tablet"><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='ap') { echo 'dp'; } else { echo 'ap'; } ?>"><?php echo $sprache->priority;?></a></th>
                <th data-hide="phone,tablet"><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='ae') { echo 'de'; } else { echo 'ae'; } ?>"><?php echo $sprache->edit2;?></a></th>
                <th data-hide="phone"><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; } ?>"><?php echo $sprache->status;?></a></th>
                <th data-hide="phone"><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='ad') { echo 'dd'; } else { echo 'ad'; } ?>"><?php echo $sprache->date;?></a></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table as $table_row) { ?>
            <tr class="<?php echo $table_row['statusClass']; ?>">
                <td><?php echo $table_row['subject']; ?></td>
                <td><?php echo $table_row['id']; ?></td>
                <td><?php echo $table_row['priority']; ?></td>
                <td><?php echo $table_row['supporter']; ?></td>
                <td><?php echo $table_row['status']; ?></td>
                <td><?php echo $table_row['writedate']; ?></td>
                <td><a href="admin.php?w=tr&amp;d=md&amp;id=<?php echo $table_row['id'];?>" alt="modify"><span class="btn btn-primary btn-mini"><i class="icon-edit icon-white"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>