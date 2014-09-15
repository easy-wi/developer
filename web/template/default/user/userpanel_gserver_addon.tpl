<section class="content-header">
    <h1><?php echo $gsprache->addon;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
        <li><?php echo $gsprache->addon;?></li>
        <li><?php echo $table['serverip'].':'.$table['port'];?></li>
        <li><?php echo $currentTemplate;?></li>
        <li><?php echo $description;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
	<?php if($userWantsHelpText=='Y'){ ?>
    <div class="row hidden-xs">
        <div class="col-md-11">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_addons;?>
            </div>
        </div>
    </div>
	<?php } ?>

    <div class="row">
        <div class="col-md-11">
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title"><?php echo $sprache->tools;?></h3>
                </div>

                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <?php foreach ($table['tools'] as $table_row) { ?>
                        <tr>
                            <td style="width: 20%">
                                <?php echo $table_row['menudescription'];?>
                                <?php if($table_row['addescription']!=''){ ?>
                                <a href="#" id="<?php echo $table_row['adid'].'-'.$table['id'];?>" data-toggle="tooltip" data-placement="right" title="<?php echo $table_row['addescription'];?>"><i class="fa fa-question-circle"></i></a>
                                <?php }?>
                            </td>
                            <td>
                                <?php if($table_row['action']=='none'){ ?>
                                <a href="#" id="<?php echo 'requires-'.$table_row['adid'].'-'.$table['id'];?>" data-toggle="tooltip" data-placement="right" title="<?php echo $table_row['alt'];?>">
                                    <span class="btn btn-sm btn-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                    </span>
                                </a>
                                <?php } else { ?>
                                <a href="<?php echo $table_row['link'];?>" onclick="return confirm('<?php echo $gsprache->sure;?>');">
                                    <span class="btn btn-sm btn-<?php if($table_row['action']=='ad') echo 'success'; else echo 'danger'; ?>">
                                        <i class="fa <?php if($table_row['action']=='ad') echo 'fa fa-plus-circle'; else echo 'fa-trash-o';?>"></i> <?php echo ($table_row['action']=='ad') ? $gsprache->add : $gsprache->del; ?>
                                    </span>
                                </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-11">
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title"><?php echo $sprache->maps;?></h3>
                </div>

                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <?php foreach ($table['maps'] as $table_row) { ?>
                        <tr>
                            <td style="width: 20%">
                                <?php echo $table_row['menudescription'];?>
                                <?php if($table_row['addescription']!=''){ ?>
                                <a href="#" id="<?php echo $table_row['adid'].'-'.$table['id'];?>" data-toggle="tooltip" data-placement="right" title="<?php echo $table_row['addescription'];?>"><i class="fa fa-question-circle"></i></a>
                                <?php }?>
                            </td>
                            <td>
                                <a href="<?php echo $table_row['link'];?>" onclick="return confirm('<?php echo $gsprache->sure;?>');">
                                    <span class="btn btn-sm btn-<?php if($table_row['action']=='ad') echo 'success'; else echo 'danger'; ?>">
                                        <i class="fa <?php if($table_row['action']=='ad') echo 'fa fa-plus-circle'; else echo 'fa-trash-o';?>"></i> <?php echo ($table_row['action']=='ad') ? $gsprache->add : $gsprache->del; ?>
                                    </span>
                                </a>
                            </td>
                        </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
$(document).popover({
    selector : '.popover-source-dynamic[data-trigger="hover"]',
    trigger : 'hover'
});
</script>
