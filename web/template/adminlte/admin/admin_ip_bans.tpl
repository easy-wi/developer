<section class="content-header">
    <h1>IP Bans</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">IP Bans</li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
            <li><a href="admin.php?w=ib&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur; ?>"><i class="fa fa-step-backward"></i></a></li>
            <li><a href="admin.php?w=ib&amp;o=<?php echo $o; ?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=ib&amp;a=50&amp;o=<?php echo $o; ?>&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=ib&amp;a=100&amp;o=<?php echo $o; ?>&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=ib&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor; ?>"><i class="fa fa-step-forward"></i></a></li>
        </ul>
    </div>
    <br/>
    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
        <form role="form" method="post" action="admin.php?w=ib&amp;r=ib" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">
                <table class="table table-bordered table-hover">
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
                <div class="input-group">
                    <label for="checkAll"><?php echo $sprache->all;?></label>
                        <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'id[]')">
                </div>

        </div>
    </div>
                    <label class="control-label" for="inputDelete"></label>
                        <button class="btn btn-danger" id="inputDelete" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
        </form>
</sections>