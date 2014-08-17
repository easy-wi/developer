<section class="content-header">
    <h1><?php echo $gsprache->settings.' '.$sprache->passw;?></h1>
    <ol class="breadcrumb">
		<li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
		<li><a href="userpanel.php?w=se"><?php echo $gsprache->settings;?></a></li>
		<li class="active"><?php echo $sprache->passw;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="box box-info">
        <div class="box-body">
			<form role="form" action="userpanel.php?w=se&amp;d=pw&amp;r=se" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

				<input type="hidden" name="token" value="<?php echo token();?>">

                <input type="hidden" name="action" value="md">
				<div class="form-group">
					<label for="password1"><?php echo $sprache->passw_1;?></label>
					<input class="form-control" id="password1" type="password" name="password" value="" required>
				</div>

				<div class="form-group">
					<label for="password2"><?php echo $sprache->passw_2;?></label>
					<input class="form-control" id="password2" type="password" name="pass2" value="" required>
				</div>

                <div class="form-group">
                    <label for="inputEdit"></label>
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-edit"></i> <?php echo $gsprache->save;?></button>
                </div>
            </form>
		</div>
	</div>
</section>