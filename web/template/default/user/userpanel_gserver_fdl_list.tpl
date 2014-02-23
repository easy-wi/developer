<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->fastdownload;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_fdl;?></div>
</div>
<hr>
<div class="row-fluid">
    <div class="span12">
        <table class="table table-striped table-bordered table-hover">
            <tbody>
            	<?php if ($pa['modfastdl']==true) { ?>
            	<tr>
            		<td><?php echo $sprache->haupt;?></td>
            		<td><?php echo $fdlpath[1];?></td>
            		<td><a href="userpanel.php?w=fd&amp;d=eu"><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i> <?php echo $sprache->haupt.' '.$gsprache->settings;?></span></a></td>
            	</tr>
            	<?php } ?>
				<?php foreach ($table as $table_row){ ?>
				<tr>
            		<td><?php echo $table_row['serverip']?>:<?php echo $table_row['port']?></td>
            		<td>
            			<form class="form-inline" method="post" action="userpanel.php?w=fd&amp;d=ud&amp;id=<?php echo $table_row['id']?>&amp;r=fd" onsubmit="return confirm('<?php echo $table_row['serverip']?>:<?php echo $table_row['port']?>: <?php echo $sprache->startfdl;?>');">
            				<button class="btn btn-mini btn-primary"><i class="fa fa-refresh"></i> <?php echo $sprache->startfdl;?></button>
        				</form>
        			</td>
            		<td><?php if ($pa['modfastdl']==true) { ?><a href="userpanel.php?w=fd&amp;d=es&amp;id=<?php echo $table_row['id']?>"><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i> <?php echo $gsprache->settings;?></span></a><?php } ?></td>
            	</tr>
            	<?php } ?>
            </tbody>
		</table>
	</div>
</div>
				