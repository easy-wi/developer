<section class="content-header">
    <h1><?php echo $gsprache->webspace;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=wv"><i class="fa fa-cubes"></i> <?php echo $gsprache->webspace;?></a></li>
		<li><i class="fa fa-info-circle"></i> <?php echo $sprache->fdlInfo;?></li>
		<li class="active"><?php echo $dns;?></li>
    </ol>
</section>

<section class="content">

	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_fdl_attention;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body">
                    <div class="form-group">
                        <label for="textarea"><?php echo $sprache->help_fdl_hl;?></label>
                        <textarea id="textarea" class="form-control" rows="4"><?php echo $hlCfg;?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body">
                    <div class="form-group">
                        <label for="textarea"><?php echo $sprache->help_fdl_ut;?></label>
                        <textarea id="textarea" class="form-control" rows="4"><?php echo $utCfg;?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body">
                    <div class="form-group">
                        <label for="cfg"><?php echo $sprache->help_fdl_cod;?></label>
                        <textarea id="cfg" class="form-control" rows="4"><?php echo $codCfg;?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>