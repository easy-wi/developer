<section class="content-header">
    <h1><?php echo $imageSprache->liveConsole;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li><i class="fa fa-terminal"></i> <?php echo $imageSprache->liveConsole;?></li>
        <li class="active"><?php echo $serverIp.':'.$port;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <div class="box-body" id="boxBody" style="overflow-y:auto;">
                </div>

                <div class="box-footer">
                    <?php if ($liveConsole=='Z') { ?>
                    <div class="input-group">
                        <input id="inputCommand" type="text" class="form-control" name="command" value="">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-flat" type="button" onclick="submitForm();"><i class="fa fa-play-circle"></i></button>
                        </span>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script type='text/javascript'>

    var lastLog = 0;

    function submitForm() {

        $('#inputCommand').val('');

        $.ajax({
            url: 'ajax.php?d=serverLog&id=<?php echo $id;?>&lastLog=' + lastLog,
            cache: false
        }).done(function(jsonReturn) {

            var jsonParsed = JSON.parse(jsonReturn);

            if (jsonParsed.error) {

                alert(jsonParsed.error);

            } else {

                lastLog = jsonParsed.lastLog;

                if (jsonParsed.log.length > 0) {
                    var boxBody = $('#boxBody');
                    boxBody.append(jsonParsed.log);
                    boxBody.scrollTop(boxBody.prop("scrollHeight"));
                }
            }
        });
    }

    function resizeHeigth() {
        var logHeight = window.innerHeight - $('.content-header').height() - $('.box-footer').height() - $('.main-header').height();
        $('#boxBody').height(logHeight * 0.6);
    }

    $(window).resize(function() {
        resizeHeigth();
    });

    $(function(){
        resizeHeigth();
        submitForm();

        setInterval(function(){submitForm()},3000);
    });
</script>