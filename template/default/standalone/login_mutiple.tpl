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


    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <?php echo implode('',$htmlExtraInformation['js']);?>

</head>

<body class="login-page" <?php echo implode(' ',$htmlExtraInformation['body']);?>>

<div class="login-box">

    <div class="login-box-body">

        <div class="callout callout-info">
            <?php echo $sprache->multipleHelper;?>
        </div>

        <div class="social-auth-links text-center">

            <?php foreach($connectedUsers as $k=>$v){ ?>
            <a href="login.php?serviceProvider=<?php echo $serviceProvider;?>&amp;loginUserId=<?php echo $k;?>" class="btn btn-block btn-social btn-<?php echo $serviceProviders[$serviceProvider];?> btn-flat"><i class="fa fa-<?php echo $serviceProviders[$serviceProvider];?>"></i> <?php echo $v;?></a>
            <?php }?>
            <?php foreach($connectedSubstitutes as $k=>$v){ ?>
            <a href="login.php?serviceProvider=<?php echo $serviceProvider;?>&amp;loginSubstituteId=<?php echo $k;?>" class="btn btn-block btn-social btn-<?php echo $serviceProviders[$serviceProvider];?> btn-flat"><i class="fa fa-<?php echo $serviceProviders[$serviceProvider];?>"></i> <?php echo $v;?></a>
            <?php }?>
        </div><!-- /.social-auth-links -->

    </div><!-- /.login-box-body -->

    <div>
        &copy; <a href="https://easy-wi.com" target="_blank" title="free gameserver, voiceserver, dedicated and virtualserver webinterface easy-wi.com">Easy-WI.com</a> 2011 - <?php echo date('Y'); ?>
    </div>
</div>

<!-- jQuery -->
<script src="js/default/jquery.min.js" type="text/javascript"></script>

<!-- Bootstrap JS -->
<script src="js/default/bootstrap.min.js" type="text/javascript"></script>

</body>
</html>