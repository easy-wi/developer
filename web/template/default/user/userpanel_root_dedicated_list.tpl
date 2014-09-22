<section class="content-header">
    <h1>Rootserver <?php echo $gsprache->dedicated;?></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $targetFile;?>"><i class="fa fa-home"></i> Home</a></li>
		<li>Rootserver</li>
		<li><?php echo $gsprache->dedicated;?></li>
		<li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="box box-primary">
        <div class="box-body table-responsive no-padding">
			<table class="table table-hover">
				<thead>
				<tr>
					<th data-class="expand"><?php echo $sprache->ip;?></th>
					<th data-hide="phone,tablet">ID</th>
					<th data-hide="phone"><?php echo $gsprache->status;?></th>
					<th data-hide="phone"><?php echo $gsprache->jobPending;?></th>
					<th><?php echo $sprache->rescue.' / '.$sprache->reinstall;?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($table as $table_row) { ?>
				<tr>
					<td><?php echo $table_row['ip'];?></td>
					<td><?php echo $table_row['id'];?></td>
					<td><i class="<?php if($table_row['active']=='Y') echo 'icon-ok'; else if($table_row['active']=='C') echo 'warning-sign'; else echo 'icon-ban-circle';?>"></i></td>
					<td><?php echo $table_row['jobPending'];?></td>
					<td><a href="userpanel.php?w=de&amp;d=ri&amp;id=<?php echo $table_row['id'];?>"><span class="btn btn-sm btn-primary"><i class="fa fa-refresh"></i></span></a></td>
				</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</section>