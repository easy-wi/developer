<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->backup;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $server;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_voiceserver_backup;?></div>
</div>
<hr>
<div class="row-fluid">
	<a href="userpanel.php?w=vo&amp;d=bu&amp;po=1&amp;id=<?php echo $id;?>"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i> <?php echo $sprache->backup ." (".($voice_maxbackup-$backupcount)." ".$sprache->left.")";?></span></a>
</div>
<br>
<div class="row-fluid">
	<div class="span8">
	    <table class="table table-bordered table-condensed table-striped table-hover">
	        <thead>
	        <tr>
	            <th><?php echo $sprache->date;?></th>
	            <th><?php echo $sprache->backupname;?></th>
	            <th class="span1"> </th>
	            <th class="span1"> </th>
	        </tr>
	        </thead>
	        <tbody>
	        <?php foreach ($table as $table_row) { ?>
	        <tr>
	            <td><?php echo $table_row['date']; ?></td>
	            <td><?php echo $table_row['name']; ?></td>
	            <td class="span1">
	                <form method="post" action="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $id;?>&amp;r=vo" name="form" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
	                    <button class="btn btn-danger btn-mini" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
	                    <input type="hidden" name="action" value="md" />
	                    <input type="hidden" name="delete" value="md" />
	                    <input type="hidden" name="id" value="<?php echo $table_row['id'];?>" />
	                </form>
	            </td>
	            <td class="span1">
	                <form method="post" action="userpanel.php?w=vo&amp;d=bu&amp;id=<?php echo $id;?>&amp;r=vo" name="form" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
	                    <button class="btn btn-primary btn-mini" id="inputEdit" type="submit"><i class="fa fa-refresh"></i> <?php echo $sprache->recover;?></button>
	                    <input type="hidden" name="action" value="md" />
	                    <input type="hidden" name="use" value="md" />
	                    <input type="hidden" name="id" value="<?php echo $table_row['id'];?>" />
	                </form>
	            </td>
	        </tr>
	        <?php } ?>
	        </tbody>
	    </table>
	</div>
</div>