<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <?php if(isset($header)) echo $header;?>
    <title><?php if(isset($title)) echo $title;?></title>

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <meta name="robots" content="noindex">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo (isset($rSA['favicon'])) ? $rSA['favicon'] : 'images/favicon.ico';?>" />

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
    <!-- 
        <div class="login-logo">
            <a href="../../index2.html"><b>Admin</b>LTE</a>
        </div> /.login-logo
    -->

    <div class="login-box-body">


        <?php if (isset($sus)) { ?>
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-warning"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <b><?php echo $sprache->sus_heading;?></b> <?php echo $sus;?>
        </div>
        <?php } else { ?>

        <p class="login-box-msg"><?php echo $sprache->heading;?></p>

        <?php if(isset($header)){ ?>
        <div class="box box-primary">
            <div class="alert alert-danger">
                <p><?php echo $text;?></p>
            </div>
        </div>
        <?php } ?>

        <form action="login.php" method="post">

            <div class="form-group has-feedback">
                <input type="text" name="username" class="form-control" placeholder="<?php echo $sprache->user;?>" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" id="inputPassword" name="password" class="form-control" placeholder="<?php echo $sprache->password;?>" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <?php if ($ewCfg['captcha']==1) { ?>
            <div class="form-group input-group has-feedback">
                <span class="input-group-addon"><img src="images.php" alt="Captcha" /></span>
                <input name="captcha" type="text" class="form-control" placeholder="Captcha" pattern="^[\w]{4}$" required>
            </div>
            <?php } ?>
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div><!-- /.col -->
            </div>
        </form>
		</br>
		<a href="login.php?w=pr"><button class="btn btn-primary btn-block btn-flat">I forgot my password</button></a>

        <?php if(count($serviceProviders)>0){ ?>
		<br>
        <div class="social-auth-links text-center">
            <p>- OR Sign in using social networks -</p>
            <?php foreach($serviceProviders as $k=>$css){ ?>
            <a href="login.php?serviceProvider=<?php echo $k;?>" class="btn btn-block btn-social btn-<?php echo $css;?> btn-flat"><i class="fa fa-<?php echo $css;?>"></i> Sign in using <?php echo $k;?></a>
            <?php } ?>
        </div><!-- /.social-auth-links -->
        <?php } ?>

        <?php }?>

    </div><!-- /.login-box-body -->
	
	<!-- Copyright -->
	
    <div class="copyright" style="position: fixed; bottom: 5px; right: 5px; padding: 10px;
	border: 1px rgb(89,89,89) solid; background: rgb(255,255,255);
	text-align: center; -webkit-border-radius: 6px; -moz-border-radius: 6px;
	border-radius: 6px; -moz-box-shadow:  -6px 7px 36px 2px rgb(128,128,128);
	-webkit-box-shadow:  -6px 7px 36px 2px rgb(128,128,128);
	box-shadow:  -6px 7px 36px 2px rgb(128,128,128);">
	
        &copy; <a href="https://easy-wi.com" target="_blank" title="free gameserver, voiceserver, dedicated and virtualserver webinterface easy-wi.com">Easy-WI.com</a> 2011 - <?php echo date('Y'); ?>
    </div>
</div>

<!-- jQuery -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js" type="text/javascript"></script>

</body>
</html>
