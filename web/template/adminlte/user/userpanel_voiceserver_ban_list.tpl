<section class="content-header">
    <h1><?php echo $gsprache->template;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"> Home</a></i></li>
            <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a></li>
            <li><?php echo $sprache->banList;?></li>
            <li class="active"><?php echo $server;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

	</p><a href="userpanel.php?w=vo&amp;d=bl&amp;e=ad&amp;id=<?php echo $id;?>"><span class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $sprache->banAdd;?></span></a></p>
	
    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
			<table class="table table-hover table-bordered">
				<thead>
				<tr>
					<th><?php echo $sprache->user;?></th>
					<th><?php echo $sprache->ip;?></th>
					<th><?php echo $sprache->duration.' '.$sprache->seconds;?></th>
					<th><?php echo $sprache->ends;?></th>
					<th><?php echo $sprache->blocked;?></th>
					<th><?php echo $gsprache->del;?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($banList as $k => $row) { ?>
				<tr>
					<td><?php echo $row['lastnickname'];?></td>
					<td><?php echo $row['ip'];?></td>
					<td><?php echo $row['duration'];?></td>
					<td><?php echo $row['ends'];?></td>
					<td><?php echo $row['blocked'];?></td>
					<td>
						<form method="post" action="userpanel.php?w=vo&amp;d=bl&amp;id=<?php echo $id;?>&amp;r=vo" name="form" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
							<button class="btn btn-danger btn-sm" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i></button>
							<input type="hidden" name="action" value="dl">
							<input type="hidden" name="bannID" value="<?php echo $k;?>">
						</form>
					</td>
				</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</section>