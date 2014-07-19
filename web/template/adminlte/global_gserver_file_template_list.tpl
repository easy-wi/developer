<section class="content-header">
    <h1><?php echo $gsprache->template;?></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $targetFile;?>"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->gameserver.' '.$gsprache->file.' '.$gsprache->template;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
	<?php echo $gsprache->template;?> <a href="<?php echo $targetFile;?>?w=gt&amp;d=ad"<span class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></span></a>

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
                    <th data-class="expand"><a href="<?php echo $targetFile;?>?w=gt&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='an') { echo 'dn'; } else { echo 'an'; } ?>"><?php echo $sprache->abkuerz;?></a></th>
                    <th data-hide="phone"><a href="<?php echo $targetFile;?>?w=gt&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='di') { echo 'ai'; } else { echo 'di'; } ?>">ID</a></th>
                    <th data-hide="phone"><a href="<?php echo $targetFile;?>?w=gt&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->game;?></a></th>
                    <th><?php echo $gsprache->del;?></a></th>
                    <th><?php echo $gsprache->mod;?></a></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($table as $table_row) { ?>
                <tr>
                    <td><?php echo $table_row['name'];?></td>
                    <td><?php echo $table_row['id'];?></td>
                    <td><?php echo $table_row['servertype'];?></td>
                    <td><a href="<?php echo $targetFile;?>?w=gt&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                    <td><a href="<?php echo $targetFile;?>?w=gt&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></span></a></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>        	
        </div>
    </div>
</section>
