<!DOCTYPE html>
<html class="bg-black">
<head>
    <meta charset="UTF-8">

    <?php if(isset($header)) echo $header;?>
    <title><?php if(isset($title)) echo $title;?></title>

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <meta name="robots" content="noindex">


    <!-- bootstrap 3.0.2 -->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- font Awesome -->
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" rel="stylesheet">

    <!-- Theme style -->
    <link href="css/adminlte/AdminLTE.css" rel="stylesheet" type="text/css" />

    <?php echo implode('',$htmlExtraInformation['css']);?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <?php echo implode('',$htmlExtraInformation['js']);?>

</head>

<body class="bg-black" <?php echo implode(' ',$htmlExtraInformation['body']);?>>

<div class="form-box" id="login-box">

    <div class="header"><?php echo $sprache->multipleHeader;?></div>

    <div class="body bg-gray">
        <div class="callout callout-info">
            <?php echo $sprache->multipleHelper;?>
        </div>

        <div class="box box-solid">
            <div class="box-body">
                <ul>
                    <?php foreach($connectedUsers as $k=>$v){ ?>
                    <li><a href="login.php?serviceProvider=<?php echo $serviceProvider;?>&amp;loginUserId=<?php echo $k;?>"><?php echo $v;?></a></li>
                    <?php }?>
                    <?php foreach($connectedSubstitutes as $k=>$v){ ?>
                    <li><a href="login.php?serviceProvider=<?php echo $serviceProvider;?>&amp;loginSubstituteId=<?php echo $k;?>"><?php echo $v;?></a></li>
                    <?php }?>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer">
        &copy; <a href="https://easy-wi.com" target="_blank" title="free gameserver, voiceserver, dedicated and virtualserver webinterface easy-wi.com">Easy-WI.com</a> 2011 - <?php echo date('Y'); ?>
    </div>
</div>

<!-- jQuery 2.0.2 -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js" type="text/javascript"></script>

</body>
</html>