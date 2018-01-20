<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <?php if(isset($header)) echo $header;?>
    <title><?php if(isset($title)) echo $title;?></title>

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <meta name="robots" content="noindex">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo (isset($rSA['favicon']) and !empty($rSA['favicon'])) ? $rSA['favicon'] : 'images/favicon.ico';?>" />

    <!-- bootstrap -->
    <link href="css/default/bootstrap.min.css" rel="stylesheet">

    <!-- font Awesome -->
    <link href="css/default/font-awesome.min.css" rel="stylesheet">

    <!-- Theme style -->
    <link href="css/default/AdminLTE.css" rel="stylesheet" type="text/css" />

    <?php echo implode('',$htmlExtraInformation['css']);?>

    <?php echo implode('',$htmlExtraInformation['js']);?>

</head>

<body class="login-page" <?php echo implode(' ',$htmlExtraInformation['body']);?>>

    <div class="login-box">

        <div class="login-box-body">

            <p class="login-box-msg"><?php echo $sprache->protect;?></p>

            <form action="protectioncheck.php" method="post">
                <div class="input-group has-<?php if(isset($protected) and $protected=='Y'){ echo 'success'; }else{ echo 'error';}?>">
                    <span class="input-group-addon">
                        <i class="fa fa-shield"></i>
                    </span>
                    <input class="form-control" type="text" name="serveraddress" value="<?php echo $ipvalue ?>" maxlength="22" required >
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-flat"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </form>
        </div>

        <?php if(isset($protected) and $protected=="Y"){ ?>
        <div class="login-box-body">

            <p class="login-box-msg"><?php echo $sprache->since.' '.$since;?></p>

            <ul class="timeline">

                <?php foreach($logs as $date => $dateLog) { ?>
                <!-- timeline time label -->
                <li class="time-label">
                    <span class="bg-red"><?php echo $date;?></span>
                </li>
                <!-- /.timeline-label -->
                <?php foreach($dateLog as $time => $timeLog) { ?>
                <?php foreach($timeLog as $actionType => $log) { ?>
                <!-- timeline item -->
                <li>
                    <!-- timeline icon -->
                    <i class="fa fa-<?php echo $actionType;?> bg-blue"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> <?php echo $time;?></span>

                        <div class="timeline-body">
                            <?php echo $log;?>
                        </div>
                    </div>
                </li>
                <!-- END timeline item -->
                <?php } ?>
                <?php } ?>
                <?php } ?>

            </ul>
        </div>
        <?php } ?>

    </div>
</body>
</html>

