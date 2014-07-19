<section class="content-header">
    <h1><?php echo $gsprache->addon;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
        <li><?php echo $gsprache->addon;?></li>
        <li><?php echo $table['serverip'].':'.$table['port'];?></li>
        <li><?php echo $currentTemplate;?></li>
        <li><?php echo $description;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_addons;?>
            </div>
        </div>
    </div>

    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><?php echo $sprache->tools;?></th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($table['tools'] as $table_row) { ?>
                    <tr>
                        <td><?php echo $table_row['menudescription'];?> <a href="#" id="<?php echo $table_row['adid'].'-'.$table['id'];?>" data-toggle="tooltip" data-placement="right" title="<?php echo $table_row['addescription'];?>"><i class="fa fa-question"></i></a><?php echo ($table_row['alt']=='Install' or $table_row['alt']=='Remove') ? '': ' '.$table_row['alt'];?></td>
                    	<td><a href="<?php echo $table_row['link'];?>" onclick="return confirm('<?php echo $gsprache->sure;?>');"><span class="btn btn-<?php if($table_row['bootstrap']=='icon-warning-sign') echo 'warning'; elseif($table_row['bootstrap']=='icon-plus-sign') echo 'success'; else echo 'danger'; ?> btn-mini"><i class="<?php echo $table_row['bootstrap'];?>"></i> <?php if($table_row['bootstrap']=='icon-warning-sign') echo ""; elseif($table_row['bootstrap']=='icon-plus-sign') echo $gsprache->add; else echo $gsprache->del; ?></span></a></td>
                    </tr>
                <?php }?>
                </tbody>
        	</table>
        </div>
	</div>
    <div class="box box-info">
        <div class="box-body table-responsive no-padding">

         	<table class="table table-hover">
                <thead>
                	<tr>
                    	<th><?php echo $sprache->maps;?></th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($table['maps'] as $table_row) { ?>
                	<tr>           
                        <td><?php echo $table_row['menudescription'];?> <a href="#" id="<?php echo $table_row['adid'].'-'.$table['id'];?>" data-toggle="tooltip" data-placement="right" title="<?php echo $table_row['addescription'];?>"><i class="icon-question-sign"></i></a><?php echo ($table_row['alt']=='Install' or $table_row['alt']=='Remove') ? '': ' '.$table_row['alt'];?></td>
						<td><a href="<?php echo $table_row['link'];?>" onclick="return confirm('<?php echo $gsprache->sure;?>');"><span class="btn btn-<?php if($table_row['bootstrap']=='fa fa-warning') echo 'warning'; elseif($table_row['bootstrap']=='icon-plus-sign') echo 'success'; else echo 'danger'; ?> btn-sm"><i class="<?php echo $table_row['bootstrap'];?>"></i> <?php if($table_row['bootstrap']=='fa fa-warning') echo ""; elseif($table_row['bootstrap']=='icon-plus-sign') echo $gsprache->add; else echo $gsprache->del; ?></span></a></td>
            		</tr>
            <?php }?>
            	</tbody>
        	</table>
        </div>
	</div>
</section>

