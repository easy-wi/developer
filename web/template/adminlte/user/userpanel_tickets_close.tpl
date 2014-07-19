<section class="content-header">
    <h1><?php echo $gsprache->support;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=ti"><?php echo $gsprache->support;?></a></li>
		<li class="active"><?php echo $sprache->close_heading;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="box box-info">
			<div class="box-body">				
				<form class="form-horizontal" action="userpanel.php?w=ti&amp;d=md&amp;id=<?php echo $id;?>&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
					<input type="hidden" name="token" value="<?php echo token();?>">
					<div class="input-group">
						<label class="input-group-addon" for="rating"><i class="fa fa-star"></i></label>
						<select class="form-control" id="rating" name="rating">
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
							<option>6</option>
						</select>
					</div>
					<br/>
					<div class="input-group">
						<label class="input-group-addon" for="comment"><?php echo $sprache->comment;?></label>
						<textarea class="form-control" id="comment" name="comment" rows="10"></textarea>
					</div>			
			</div>
	</div>
						<label class="control-label" for="inputEdit"></label>
						<button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-lock"></i> <?php echo $sprache->close_heading;?></button>
						<input type="hidden" name="action" value="cl">
				</form>
</section>