<section class="content-header">
    <h1><?php echo $gsprache->voiceserver.' '.$gsprache->stats;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"> Home</a></i></li>
        <li><a href="admin.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><i class="fa fa-area-chart"></i> <?php echo $gsprache->stats;?></li>
        <li class="active"><?php echo $display;?></li>
    </ol>
</section>


<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <form role="form" action="admin.php?w=vu" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="dateRange"><?php echo $sprache->dmy;?></label>
                            <div class="input-prepend input-group">
                                <span class="add-on input-group-addon"><i class="fa fa-calendar"></i></span>
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
                            <select class="form-control" id="inputKind" name="kind">
                                <option value="al"><?php echo $sprache->all;?></option>
                                <option value="ma" <?php if ($kind=='ma') echo 'selected="selected"'?>><?php echo $gsprache->master;?></option>
                                <option value="se" <?php if ($kind=='se') echo 'selected="selected"'?>><?php echo $sprache->server;?></option>
                                <option value="us" <?php if ($kind=='us') echo 'selected="selected"'?>><?php echo $sprache->user;?></option>
                            </select>
                        </div>

                        <div class="form-group" id="serverSelect">
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-edit"></i> <?php echo $gsprache->mod;?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?php echo $voSprache->slots;?></h3>
                </div>
                <div class="box-body chart-responsive">
                    <div class="chart" id="slots-chart" style="height: 300px; position: relative;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?php echo $gsprache->traffic;?></h3>
                </div>
                <div class="box-body chart-responsive">
                    <div class="chart" id="traffic-chart" style="height: 300px; position: relative;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?php echo $voSprache->slots;?> + <?php echo $gsprache->traffic;?></h3>
                </div>
                <div class="box-body chart-responsive">
                    <div class="chart" id="usage-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(function(){
        $('#serverSelect').load('ajax.php?d=adminVoiceStats&w=' + $("#inputKind").val() + '&selectedID=' + <?php echo $selectedID;?>);
    });
    $('#inputKind').on('change', function() {
        $('#serverSelect').load('ajax.php?d=adminVoiceStats&w=' + $("#inputKind").val());
    });
</script>