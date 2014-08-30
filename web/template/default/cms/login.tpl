<!DOCTYPE html>
<html class="bg-black">
<head>
    <meta charset="UTF-8">

    <?php if(isset($header)) echo $header;?>
    <title><?php if(isset($title)) echo $title;?></title>

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <meta name="robots" content="noindex">


    <!-- bootstrap 3.1.1 -->
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
    <?php if (isset($sus)) { ?>
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-warning"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <b><?php echo $sprache->sus_heading;?></b> <?php echo $sus;?>
        </div>
    <?php } else { ?>
        <div class="header"><?php echo $sprache->heading;?></div>
        <?php if(isset($header)){ ?>
            <div class="box box-info">
                <div class="alert alert-danger">
                    <p><?php echo $text;?></p>
                </div>
            </div>
        <?php } ?>
        <form action="login.php" method="post">
            <div class="body bg-gray">
                <div class="form-group">
                    <input type="text" id="inputUser"  name="username" class="form-control" placeholder="<?php echo $sprache->user;?>" required>
                </div>
                <div class="form-group">
                    <input type="password" id="inputPassword" name="password" class="form-control" placeholder="<?php echo $sprache->password;?>" required>
                </div>
                <?php if ($ewCfg['captcha']==1) { ?>
                <div class="form-group input-group">
                    <span class="input-group-addon"><img src="images.php" alt="Captcha" /></span>
                    <input name="captcha" type="text" class="form-control" placeholder="Captcha" pattern="^[\w]{4}$" required>
                </div>
                <?php } ?>
                <button type="submit" class="btn bg-blue btn-block">Login</button>
            </div>
            <div class="footer">

                <?php if(count($serviceProviders)>0){ ?>
                <div class="margin text-center">
                    <span>Sign in using social networks</span>
                    <br/>
                    <?php foreach($serviceProviders as $k=>$css){ ?>
                    <a href="login.php?serviceProvider=<?php echo $k;?>"><button class="btn bg-light-blue btn-circle btn-<?php echo $css;?>"><i class="fa fa-<?php echo $css;?>"></i></button></a>
                    <?php } ?>
                </div>
                <?php } ?>

                <div class="margin">
                    <p><a href="login.php?w=pr">Lost PW</a></p>
                </div>
            </div>
        </form>
    <?php }?>
    <div>
        &copy; <a href="https://easy-wi.com" target="_blank" title="free gameserver, voiceserver, dedicated and virtualserver webinterface easy-wi.com">Easy-WI.com</a> 2011 - <?php echo date('Y'); ?>
    </div>
</div>

<!-- jQuery 2.0.2 -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js" type="text/javascript"></script>

</body>
</html>