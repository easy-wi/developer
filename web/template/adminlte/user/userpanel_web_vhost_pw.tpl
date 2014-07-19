<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->webspace.' '.$sprache->ftpPassword;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
		<li><a href="userpanel.php?w=wv"><?php echo $gsprache->webspace;?></a></li>
		<li><?php echo $sprache->ftpPassword;?></li>
		<li class="active"><?php echo $dns;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">
	
	<div class="box box-info">	
		<div class="box-body">
			<?php if (count($errors)>0){ ?>
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4><?php echo $gsprache->errors;?></h4>
				<?php echo implode(', ',$errors);?>
			</div>
			<?php }?>

			<form role="form" action="userpanel.php?w=wv&amp;d=pw&amp;id=<?php echo $id;?>&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
				<input type="hidden" name="token" value="<?php echo token();?>">
				<input type="hidden" name="action" value="pw">
				<div class="form-group">
					<label for="inputPassword1"><?php echo $sprache->ftpPassword;?></label>
						<input class="form-control" id="inputPassword1" type="password" name="password1" value="" maxlength="40">
				</div>
				<div class="form-group">
					<label for="inputPassword2"><?php echo $sprache->ftpPasswordRepeat;?></label>
						<input class="form-control" id="inputPassword2" type="password" name="password2" value="" maxlength="40">
				</div>
		</div>
	</div>
					<label for="inputEdit"></label>
						<button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save"></i> <?php echo $gsprache->save;?></button>
			</form>	
</section>