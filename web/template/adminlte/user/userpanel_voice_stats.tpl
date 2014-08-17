<section class="content-header">
    <h1><?php echo $gsprache->voiceserver.' '.$gsprache->stats;?></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $targetFile;?>"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a></li>
        <li><?php echo $gsprache->stats;?></li>
        <li class="active"><?php echo $display;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-body">
                    <form role="form" action="userpanel.php?w=vu" method="post">
                        <input type="hidden" name="token" value="<?php echo token();?>">

                        <div class="form-group">
                            <label for="dateRange"><?php echo $sprache->dmy;?></label>
                            <div class="input-prepend input-group">
                                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                <input type="text" name="dateRange" id="dateRange" class="form-control" value="<?php echo $dateRange;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputFormat"><?php echo $sprache->accuracy;?></label>
                            <select class="form-control" id="inputFormat" name="accuracy">
                                <option value="da"><?php echo $sprache->days;?></option>
                                <option value="mo" <?php if ($accuracy=='mo') echo 'selected="selected"'?>><?php echo $sprache->months;?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputKind"><?php echo $gsprache->stats;?></label>
                            <select class="form-control" id="inputKind" name="kind" onchange="getdetails('ajax.php?d=uservoicestats&amp;w=',this.value, 'serverSelect')">
                                <option value="al"><?php echo $sprache->all;?></option>
                                <option value="se" <?php if ($kind=='se') echo 'selected="selected"'?>><?php echo $sprache->server;?></option>
                            </select>
                        </div>
                        <div class="form-group" id="serverSelect">
                            <?php if($ui->st('kind','post') == 'se'){ ?>
                            <label for="inputSelect"></label>
                            <select class="form-control" id="inputSelect" name="serverID">
                                <?php foreach ($data as $value) echo $value;?>
                            </select>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="inputEdit"></label>
                            <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-edit"></i> <?php echo $gsprache->mod;?></button>
                            <input type="hidden" name="action" value="md">
                        </div>
                    </form>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>

        <div class="col-md-8">
            <!-- AREA CHART -->
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?php echo $voSprache->slots;?></h3>
                </div>
                <div class="box-body chart-responsive">
                    <div class="chart" id="slot-usage" style="height: 228px;"></div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- LINE CHART -->
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title"><?php echo $gsprache->traffic;?></h3>
                </div>
                <div class="box-body chart-responsive">
                    <div class="chart" id="traffic-usage" style="height: 300px;"></div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->

        </div><!-- /.col (RIGHT) -->
    </div><!-- /.row -->
</section>