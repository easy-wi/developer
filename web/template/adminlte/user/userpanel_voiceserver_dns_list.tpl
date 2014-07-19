<!-- Content Header -->
<section class="content-header">
    <h1>TS3 DNS</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
		<li class="active">TS3 DNS</li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">
	<?php foreach ($table as $table_row) { ?>
		<div class="box box-success">
			<div class="box-body">
				<dl class="dl-horizontal">
					<dt><?php echo $gsprache->settings; ?></dt>
					<dd><a href="userpanel.php?w=vd&amp;d=md&amp;id=<?php echo $table_row['id'];?>"><span class="btn-primary btn-sm"><i class="fa fa-edit"></i> <?php echo $gsprache->mod;?></span></a></dd>
					<dt>ID</dt>
					<dd><?php echo $table_row['id'];?></dd>
					<dt>TS3 DNS</dt>
					<dd><?php echo $table_row['dns'];?></dd>
					<dt><?php echo $sprache->ip; ?></dt>
					<dd><?php echo $table_row['address'];?></dd>
				</dl>
			</div>
		</div>
	<?php } ?>
</section>