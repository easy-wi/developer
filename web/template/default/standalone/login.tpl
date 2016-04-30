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

<body class="login-page" style="background-image: url(images/background.png);" <?php echo implode(' ',$htmlExtraInformation['body']);?>>

<div class="login-box">

        <div class="login-logo" style=
		"background:white;-moz-box-shadow:  2px 2px 27px 1px rgba(0, 0, 0, 0.56);
-webkit-box-shadow:  2px 2px 27px 1px rgba(0, 0, 0, 0.56);
box-shadow:  2px 2px 27px 1px rgba(0, 0, 0, 0.56);">
            <a href="https://easy-wi.com/"><b>Easy-</b>Wi</a>
        </div>

    <div style="-moz-box-shadow:  2px 2px 27px 1px rgba(0, 0, 0, 0.56);
-webkit-box-shadow:  2px 2px 27px 1px rgba(0, 0, 0, 0.56);
box-shadow:  2px 2px 27px 1px rgba(0, 0, 0, 0.56);" class="login-box-body">

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
                <input type="text" name="username" class="form-control" placeholder="<?php echo $sprache->user;?> / Email" required>
                <span class="fa fa-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" id="inputPassword" name="password" class="form-control" placeholder="<?php echo $sprache->password;?>" required>
                <span class="fa fa-lock form-control-feedback"></span>
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

        <?php if(count($serviceProviders)>0){ ?>
        <div class="social-auth-links text-center">
            <p>- OR Sign in using social networks -</p>
            <?php foreach($serviceProviders as $k=>$css){ ?>
            <a href="login.php?serviceProvider=<?php echo $k;?>" class="btn btn-block btn-social btn-<?php echo $css;?> btn-flat"><i class="fa fa-<?php echo $css;?>"></i> Sign in using <?php echo $k;?></a>
            <?php } ?>
        </div><!-- /.social-auth-links -->
        <?php } ?>
        <?php }?>
<a href="login.php?w=pr" >Forgot Password</a>
    </div><!-- /.login-box-body -->

 <div class="copyright" style="position: fixed; bottom: 5px; right: 5px; padding: 5px;
	width: 200px;
text-align: center;

border: 2px rgb(0, 0, 0) inset;

background: rgba(0, 0, 0, 0.8);

-webkit-border-radius: 10px;
-moz-border-radius: 10px;
border-radius: 10px;
filter: alpha(opacity=80);
-moz-opacity: 0.8
-khtml-opacity: 0.8;
opacity: 0.8;

-moz-box-shadow:  0px 0px 10px 1px rgb(0, 0, 0);
-webkit-box-shadow:  0px 0px 10px 1px rgb(0, 0, 0);
box-shadow:  0px 0px 10px 1px rgb(0, 0, 0);
">
	 <span style="color:#FFFFFF;"> &copy; <a href="https://easy-wi.com" target="_blank" title="free gameserver, voiceserver, dedicated and virtualserver webinterface easy-wi.com">Easy-WI.com</a> 2011 - <?php echo date('Y'); ?></a></span>
    </div>
</div>

<!-- jQuery -->
<script src="js/default/jquery.min.js" type="text/javascript"></script>

<!-- Bootstrap JS -->
<script src="js/default/bootstrap.min.js" type="text/javascript"></script>

</body>
</html>
