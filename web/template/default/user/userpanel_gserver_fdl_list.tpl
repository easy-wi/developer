<section class="content-header">
    <h1><?php echo $gsprache->fastdownload;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a></li>
        <li class="active"><?php echo $gsprache->fastdownload;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_fdl;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <?php if ($pa['modfastdl']==true) { ?>
                        <tr>
                            <td><?php echo $sprache->haupt;?></td>
                            <td><?php echo $fdlpath[1];?></td>
                            <td><a href="userpanel.php?w=fd&amp;d=eu"><span class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> <?php echo $sprache->haupt.' '.$gsprache->settings;?></span></a></td>
                        </tr>
                        <?php } ?>
                        <?php foreach ($table as $table_row){ ?>
                        <tr>
                            <td><?php echo $table_row['serverip']?>:<?php echo $table_row['port']?></td>
                            <td>
                                <form class="form-inline" method="post" action="userpanel.php?w=fd&amp;d=ud&amp;id=<?php echo $table_row['id']?>&amp;r=fd" onsubmit="return confirm('<?php echo $table_row['serverip']?>:<?php echo $table_row['port']?>: <?php echo $sprache->startfdl;?>');">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-refresh"></i> <?php echo $sprache->startfdl;?></button>
                                </form>
                            </td>
                            <td><?php if ($pa['modfastdl']==true) { ?><a href="userpanel.php?w=fd&amp;d=es&amp;id=<?php echo $table_row['id']?>"><span class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> <?php echo $gsprache->settings;?></span></a><?php } ?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
