<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->addon;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $table['serverip'].':'.$table['port'];?> <span class="divider">/</span></li>
            <li class="active"><?php echo $currentTemplate;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $description;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_addons;?></div>
</div>
<hr>
<div class="row-fluid">
    <div class="span8">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr><th colspan="2"><?php echo $sprache->tools;?></th></tr>
            </thead>
            <tbody>
            <?php foreach ($table['tools'] as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['menudescription'];?> <a href="#" id="<?php echo $table_row['adid'].'-'.$table['id'];?>" data-toggle="tooltip" data-placement="right" title="<?php echo $table_row['addescription'];?>"><i class="icon-question-sign"></i></a><?php echo ($table_row['alt']=='Install' or $table_row['alt']=='Remove') ? '': ' '.$table_row['alt'];?></td>
                <td class="span1"><a href="<?php echo $table_row['link'];?>" onclick="return confirm('<?php echo $gsprache->sure;?>');"><span class="btn btn-<?php if($table_row['bootstrap']=='icon-warning-sign') echo 'warning'; elseif($table_row['bootstrap']=='icon-plus-sign') echo 'success'; else echo 'danger'; ?> btn-mini"><i class="<?php echo $table_row['bootstrap'];?> icon-white"></i> <?php if($table_row['bootstrap']=='icon-warning-sign') echo ""; elseif($table_row['bootstrap']=='icon-plus-sign') echo $gsprache->add; else echo $gsprache->del; ?></span></a></td>
            </tr>
            <?php }?>
            </tbody>
        </table>
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr><th colspan="2"><?php echo $sprache->maps;?></th></tr>
            </thead>
            <tbody>
            <?php foreach ($table['maps'] as $table_row) { ?>
            <tr>
                <td><?php echo $table_row['menudescription'];?> <a href="#" id="<?php echo $table_row['adid'].'-'.$table['id'];?>" data-toggle="tooltip" data-placement="right" title="<?php echo $table_row['addescription'];?>"><i class="icon-question-sign"></i></a><?php echo ($table_row['alt']=='Install' or $table_row['alt']=='Remove') ? '': ' '.$table_row['alt'];?></td>
                <td class="span1"><a href="<?php echo $table_row['link'];?>" onclick="return confirm('<?php echo $gsprache->sure;?>');"><span class="btn btn-<?php if($table_row['bootstrap']=='icon-warning-sign') echo 'warning'; elseif($table_row['bootstrap']=='icon-plus-sign') echo 'success'; else echo 'danger'; ?> btn-mini"><i class="<?php echo $table_row['bootstrap'];?> icon-white"></i> <?php if($table_row['bootstrap']=='icon-warning-sign') echo ""; elseif($table_row['bootstrap']=='icon-plus-sign') echo $gsprache->add; else echo $gsprache->del; ?></span></a></td>
            </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
</div>