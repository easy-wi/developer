<section class="content-header">
    <h1><?php echo $gsprache->substitutes;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
		<li><a href="userpanel.php?w=su"><?php echo $gsprache->substitutes;?></a></li>
		<li><a href="userpanel.php?w=su&amp;d=dl"><?php echo $gsprache->del;?></a></li>
		<li class="active"><?php echo $loginName;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
	<div class="box box-info">
		<div class="box-body">


			<form class="form-horizontal" action="userpanel.php?w=su&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=su" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
				<input type="hidden" name="token" value="<?php echo token();?>">
				<input type="hidden" name="action" value="dl">
			<div class="input-group">
				<label class="input-group-addon"><i class="fa fa-user"></i></label>
				<span class="form-control"><?php echo $loginName;?></span>
			</div>
		</div>
	</div>
					<label class="control-label" for="inputEdit"></label>
						<button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
			</form>
</section><!-- /.content -->

