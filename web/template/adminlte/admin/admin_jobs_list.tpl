<section class="content-header">
    <h1><?php echo $gsprache->jobs.' '.$gsprache->overview;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->jobs.' '.$gsprache->overview;?></li>
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
        <form method="post" action="admin.php?w=jb&amp;r=jb" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">
                <table class="table table-bordered table-hover">
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
                    <tr class="<?php if($table_row['status']==null) echo 'info'; else if($table_row['status']==1) echo 'danger'; else if($table_row['status']==2) echo 'warning'; else echo 'success';?>">
                        <td><?php echo $table_row['action']; ?></td>
                        <td><i class="<?php if($table_row['status']==null) echo 'fa fa-refresh'; else if($table_row['status']==1) echo 'fa fa-ban'; else if($table_row['status']==2) echo 'fa fa-warning'; else echo 'fa fa-ok';?>"></i></td>
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
                <div class="form-group">
                	<div class="checkbox">
                    <label class="checkbox inline" for="checkAll"><?php echo $sprache->all;?></label>
                        <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'id[]')">
                	</div>
                </div>

    </div>
</div>
                    <label class="control-label" for="inputDelete"></label>
                        <button class="btn btn-danger" id="inputDelete" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
        </form>
</section>