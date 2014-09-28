<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=gs"><?php echo $gsprache->gameserver;?></a></li>
        <li><?php echo $gsprache->reinstall;?></li>
        <li class="active"><?php echo $serverip.':'.$port;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">

                <form role="form" action="admin.php?w=gs&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ri">
                    <input type="hidden" id="type" name="type" value="N">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="game"><?php echo $gsprache->game;?></label>
                            <select class="form-control" id="game" name="game" onchange="toggleTemplates();">
                                <?php foreach ($table as $table_row){ ?>
                                <option value="<?php echo $table_row['id'];?>" data-shorten="<?php echo $table_row['shorten'];?>"><?php echo $table_row['description'];?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="template"><?php echo $gsprache->template;?></label>
                            <select class="form-control" id="template" name="template">
                                <option id="template1" value="1"><?php echo $shorten;?></option>
                                <option id="template2" value="2" <?php echo $selected2;?>><?php echo $shorten;?>-2</option>
                                <option id="template3" value="3" <?php echo $selected3;?>><?php echo $shorten;?>-3</option>
                                <option value="4"><?php echo $gsprache->all;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php echo $sprache->type;?></label>

                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-primary active">
                                    <input type="radio" name="options" value="N" onchange="$('#type').val(this.value);" checked> <?php echo $sprache->resync;?>
                                </label>
                                <label class="btn btn-primary">
                                    <input type="radio" name="options" value="Y" onchange="$('#type').val(this.value);"> <?php echo $sprache->reinstall;?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-warning" id="inputEdit" type="submit"><i class="fa fa-refresh">&nbsp;<?php echo $gsprache->exec;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function toggleTemplates () {
        var shorten = $('#game').find(':selected').data('shorten');
        $('#template1').text(shorten);
        $('#template2').text(shorten + '-2');
        $('#template3').text(shorten + '-3');
    }
</script>