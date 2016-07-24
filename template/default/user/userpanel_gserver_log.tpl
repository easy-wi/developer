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

                <div class="box-body" id="boxBody" style="overflow-y:auto;font-family:'Courier';">
                </div>

                <div class="box-footer">
                    <?php if ($liveConsole=='Y') { ?>
                    <div class="input-group">
                        <input id="inputCommand" type="text" class="form-control" name="command" value="" onkeydown="enterUsed(event)">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-flat" type="button" onclick="submitForm()"><i class="fa fa-play-circle"></i></button>
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
    var getRequestStarted = false;
    var errorft = true; //ft means first time
    var boxBody = $('#boxBody');

    function enterUsed(event) {
        if (event.keyCode === 13) {
            submitForm();
        }
    }

    function submitForm() {

        var inputCommand = $('#inputCommand');

        $.ajax({
            url: 'ajax.php?d=serverLog&id=<?php echo $id;?>',
            cache: false,
            method: 'POST',
            data: {
                cmd: inputCommand.val()
            }
        }).done(function(jsonReturn) {
            setTimeout(function(){getLog()},1000);
        });

        inputCommand.val('');

        return false;
    }

    function getLog() {

        if (getRequestStarted === false) {

            getRequestStarted = true;

            $.ajax({
                url: 'ajax.php?d=serverLog&id=<?php echo $id;?>&lastLog=' + lastLog,
                cache: false
            }).done(function(jsonReturn) {

                getRequestStarted = false;

                var jsonParsed = JSON.parse(jsonReturn);



                if (jsonParsed.error) {

                  if (errorft){
                  boxBody.css( "background-color","rgba(255, 0, 0, 0.30)" );
                  boxBody.parent().css( "border-top-color","rgba(210, 0, 0, 1)" );

                  boxBody.html('<h1 id="errorcde" style="text-align: center; padding-top: 9%" >' + jsonParsed.error + "</h1>");

                  }
                  errorft = false;

                } else {

                  if (!errorft){
                    //rimuovere error code (id errorcde)
                    $("#errorcde").remove();
                    errorft = true;
                  }

                    boxBody.css( "background-color","rgba(0, 250, 136, 0.08)" );
                    boxBody.parent().css( "border-top-color","#00a65a" );


                    lastLog = jsonParsed.lastLog;

                    if (jsonParsed.log.length > 0) {
                        boxBody.append(jsonParsed.log);
                        boxBody.scrollTop(boxBody.prop("scrollHeight"));
                    }
                }
            });
        }
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
        getLog();

        setInterval(function(){getLog()},3000);
    });
</script>
