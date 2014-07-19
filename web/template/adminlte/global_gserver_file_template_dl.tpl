<section class="content-header">
    <h1><?php echo $gsprache->template.' '.$gsprache->del;?></h1>
    <ol class="breadcrumb">
		<li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="<?php echo $targetFile;?>?w=im"><?php echo $gsprache->template;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $name;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
	<div class="box box-info">
		<div class="box-body">
			<form role="form" action="<?php echo $targetFile;?>?w=gt&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=gt" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
				<input type="hidden" name="token" value="<?php echo token();?>">
				<input type="hidden" name="action" value="dl">
			<div class="input-group">
				<label class="input-group-addon"><i class="fa fa-file"></i></label>
				<span class="form-control"><?php echo $name;?></span>
			</div>
        </div>
    </div>
					<label class="control-label" for="inputEdit"></label>
						<button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash-o"> <?php echo $gsprache->del;?></i></button>
			</form>
</section>