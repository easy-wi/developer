<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a>
		<li><?php echo $sprache->config;?></li>
		<li><?php echo $serverip.':'.$port;?></li>
		<li class="active"><?php echo htmlentities($configname);?></li>		
    </ol>
</section>
<!-- Main Content -->
<section class="content">

	<!-- Content Help -->
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_config;?>
            </div>
        </div>
    </div>
	
    <form role="form" action="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $id;?>&amp;type=full&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
	<div class="box box-info">
		<div class="body-box">
			<div class="input-group">
            	<label class="input-group-addon"><?php echo htmlentities($configname);?></label>
				<textarea class="form-control" id="inputConfig" rows="20" name="cleanedconfig"><?php echo $cleanedconfig;?></textarea>
			</div>
		</div>
	</div>
				<button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
				<input type="hidden" name="config" value="<?php echo $postconfig;?>">
				<input type="hidden" name="update" value="1">	
</section>