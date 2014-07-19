<section class="content-header">
    <h1><?php echo $gsprache->support;?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->support;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <h4><?php echo $sprache->status;?></h4>
    <a href="<?php echo $ticketLinks['N'];?>"><?php echo $sprache->status_new;?></a><?php if(in_array('N',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>,
    <a href="<?php echo $ticketLinks['P'];?>"><?php echo $sprache->status_process;?></a><?php if(in_array('P',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>,
    <a href="<?php echo $ticketLinks['R'];?>"><?php echo $sprache->status_reopen;?></a><?php if(in_array('R',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>,
    <a href="<?php echo $ticketLinks['A'];?>"><?php echo $sprache->status_author;?></a><?php if(in_array('A',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>,
    <a href="<?php echo $ticketLinks['D'];?>"><?php echo $sprache->status_done;?></a><?php if(in_array('D',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>,
    <a href="<?php echo $ticketLinks['C'];?>"><?php echo $sprache->status_confirmed;?></a><?php if(in_array('C',$selected)) { ?> <i class="fa fa-check-square-o"></i><?php }?>
    <br/>

    <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
            <li><a href="userpanel.php?w=lo&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur; ?>"><i class="fa fa-step-backward"></i></a></li>
            <li><a href="userpanel.php?w=lo&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="userpanel.php?w=lo&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="userpanel.php?w=lo&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="userpanel.php?w=lo&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor; ?>"><i class="fa fa-step-forward"></i></a></li>
        </ul>
    </div>
    <br/>

    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->subject;?></a></th>
                    <th><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">ID</a></th>
                    <th><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='ap') { echo 'dp'; } else { echo 'ap'; } ?>"><?php echo $sprache->priority;?></a></th>
                    <th><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='ae') { echo 'de'; } else { echo 'ae'; } ?>"><?php echo $sprache->edit2;?></a></th>
                    <th><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; } ?>"><?php echo $sprache->status;?></a></th>
                    <th><a href="<?php echo $ticketLinks['all'];?>&amp;o=<?php if ($o=='ad') { echo 'dd'; } else { echo 'ad'; } ?>"><?php echo $sprache->date;?></a></th>
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
                    <td><a href="userpanel.php?w=ti&amp;d=md&amp;id=<?php echo $table_row['id'];?>" alt="modify"><span class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></span></a></td>
                </tr>
            <?php } ?>
            </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->

</section><!-- /.content -->