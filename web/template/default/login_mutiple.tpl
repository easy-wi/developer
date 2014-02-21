<!DOCTYPE html>
<html>
<head>
    <?php if(isset($header)) echo $header; ?>
    <title><?php if(isset($title)) echo $title; ?></title>
    <link rel="shortcut icon" href="images/favicon.png" type="image/png" />
    <meta name="robots" content="noindex" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="//netdna.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <?php echo implode('',$htmlExtraInformation['css']);?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" ></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>
    <style type="text/css">
        body { padding-top: 40px;padding-bottom: 40px;background-color: #f5f5f5;}
        .form-signin { max-width: 500px;padding: 19px 29px 29px;margin: 0 auto 20px;background-color: #fff;border: 1px solid #e5e5e5;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05); -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);box-shadow: 0 1px 2px rgba(0,0,0,.05);}
        .form-signin .form-signin-heading { margin-bottom: 10px;}
    </style>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <?php echo implode('',$htmlExtraInformation['js']);?>
</head>
<body <?php echo implode(' ',$htmlExtraInformation['body']);?>>
<div class="container">
    <div class="form-signin">
        <h2 class="form-signin-heading"><?php echo $sprache->multipleHeader; ?></h2>
        <div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $sprache->multipleHelper; ?></div>
        <div class="row-fluid">
            <ul class="nav nav-tabs nav-stacked">
                <?php foreach($connectedUsers as $k=>$v){ ?>
                <li><a href="login.php?serviceProvider=<?php echo $serviceProvider;?>&amp;loginUserId=<?php echo $k;?>"><?php echo $v;?></a></li>
                <?php }?>
                <?php foreach($connectedSubstitutes as $k=>$v){ ?>
                <li><a href="login.php?serviceProvider=<?php echo $serviceProvider;?>&amp;loginSubstituteId=<?php echo $k;?>"><?php echo $v;?></a></li>
                <?php }?>
            </ul>
        </div>
        <hr>
        <div>
            &copy; <a href="https://easy-wi.com" target="_blank" title="free gameserver, voiceserver, dedicated and virtualserver webinterface easy-wi.com">Easy-WI.com</a> 2011 - <?php echo date('Y'); ?>
        </div>
    </div>
</div>
</body>
</html>