<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->webspace.' '.$gsprache->fdlInfo;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
		<li><a href="userpanel.php?w=wv"><?php echo $gsprache->webspace;?></a></li>
		<li><?php echo $sprache->fdlInfo;?></li>
		<li class="active"><?php echo $dns;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">

	<!-- Content Help -->
	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-11">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_fdl_attention;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-11">
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
        <div class="col-md-11">
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