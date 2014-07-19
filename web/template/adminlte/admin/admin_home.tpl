<section class="content-header">
    <h4>Dashboard</h4>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- Main row -->
    <div class="row">

        <?php if($easywiModules['ti'] and $pa['usertickets']) { ?>
        <section class="col-md-4">
            <div class="box box-danger" id="loading-example">
                <div class="box-header">
                    <i class="fa fa-support"></i>
                    <h3 class="box-title"><?php echo $gsprache->support;?></h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-xs-6 text-center" style="border-right: 1px solid #f4f4f4">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['ticketsNew'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['ticketsTotal'];?>" data-fgColor="#00a65a">
                            <div class="knob-label"><?php echo $statsArray['ticketsNew'].'/'.$statsArray['ticketsTotal'];?> <?php echo $sprache_bad->tickets_new;?></div>
                        </div><!-- ./col -->
                        <div class="col-xs-6 text-center">
                            <input type="text" class="knob" data-readonly="true" value="<?php echo $statsArray['ticketsInProcess'];?>" data-width="60" data-height="60" data-min="0" data-max="<?php echo $statsArray['ticketsTotal'];?>" data-fgColor="#3c8dbc">
                            <div class="knob-label"><?php echo $statsArray['ticketsInProcess'].'/'.$statsArray['ticketsTotal'];?> <?php echo $sprache_bad->tickets_open;?></div>
                        </div><!-- ./col -->
                    </div><!-- /.row - inside box -->
                </div><!-- /.box-body -->
            </div>
        </section>
        <?php } ?>