<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->webspace.' '.$dedicatedLanguage->reinstall;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
		<li><a href="userpanel.php?w=wv"><?php echo $gsprache->webspace;?> Vhost</a></li>
		<li><?php echo $dedicatedLanguage->reinstall;?></li>
		<li class="active"><?php echo $dns;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">
	
	<div class="box box-info">	
		<div class="box-body">
			<div class="form-group">
				<label><?php echo $sprache->dns?></label>
				<input class="form-control" value="<?php echo $dns;?>" disabled>
			</div>
				
			<form role="form" action="userpanel.php?w=wv&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
				<input type="hidden" name="token" value="<?php echo token();?>">
				<input type="hidden" name="action" value="ri">
		</div>
	</div>
					<label for="inputEdit"></label>
					<button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-refresh"></i> <?php echo $dedicatedLanguage->reinstall;?></button>
			</form>
</section>