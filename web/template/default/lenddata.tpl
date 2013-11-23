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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" ></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>
    <style type="text/css">
        body { padding-top: 40px;padding-bottom: 40px;background-color: #f5f5f5;}
        .form-signin { max-width: 500px;padding: 19px 29px 69px;margin: 0 auto 30px;background-color: #fff;border: 1px solid #e5e5e5;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05); -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);box-shadow: 0 1px 2px rgba(0,0,0,.05);}
        .form-signin .form-signin-heading,
        .form-signin { margin-bottom: 10px;}
        .form-signin input[type="text"],
        .form-signin input[type="password"] { margin-bottom: 15px;padding: 7px 9px;}
    </style>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="js/default/html5shiv.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <?php if(isset($header)) echo '<div><img src="images/16_notice.png" alt="notice" />'.$text.'</div>'; ?>
    <div class="form-signin">
        <?php if ($servertype=='g' and $gslallowed==true) { ?>
        <?php if ($volallowed==true) { ?><h3><a href="lend.php?w=vo"><?php echo $gsprache->voiceserver;?></a></h3><?php } ?>
        <p><?php echo $gssprache->game.' '.$description;?></p>
        <p><?php echo $gssprache->server;?> <a href="steam://connect/<?php echo $serverip.':'.$port.'/'.$password;?>">connect <?php echo $serverip.':'.$port.'; password '.$password;?></a></p>
        <p><?php echo $gssprache->slots.' '.$slots;?></p>
        <p><?php echo $sprache->timeleft.' '.$timeleft.'/'.$lendtime.' '.$sprache->minutes;?></p>
        <p><?php echo $gssprache->rcon.' '.$rcon;?></p>
        <p><?php echo $gssprache->password.' '.$password;?></p>
        <?php } else if ($volallowed==true) { ?>
        <?php if ($gslallowed==true) { ?><h3 class="middle"><a href="lend.php?w=gs"><?php echo $gsprache->gameserver;?></a></h3><?php } ?>
        <p><?php echo $gssprache->server;?> <a href="ts3server://<?php echo $server.'?password='.$password;?>"><?php echo $server;?></a></p>
        <p><?php echo $gssprache->slots.' '.$slots;?></p>
        <p><?php echo $sprache->timeleft.' '.$timeleft.'/'.$lendtime.' '.$sprache->minutes;?></p>
        <p>Token <?php echo $rcon;?></p>
        <p><?php echo $gssprache->password.' '.$password;?></p>
        <?php } ?>
    </div>
</div>
<hr>
<div>
    &copy; <a href="https://easy-wi.com" target="_blank" title="free gameserver, voiceserver, dedicated and virtualserver webinterface easy-wi.com">Easy-WI.com</a> 2011 - <?php echo date('Y'); ?>
</div>
</body>
</html>