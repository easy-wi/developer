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

        <p class="login-box-msg"><?php echo ($servertype=='g') ? $gsprache->gameserver : $gsprache->voiceserver;?></p>
        <dl>
            <?php if ($servertype=='g' and $gslallowed==true) { ?>
            <dt><?php echo $gssprache->server;?></dt>
            <dd><a href="steam://connect/<?php echo $serverip.':'.$port.'/'.$password;?>">connect <?php echo $serverip.':'.$port.'; password '.$password;?></a></dd>
            <dt><?php echo $gssprache->game;?></dt>
            <dd><?php echo $description;?></dd>
            <dt><?php echo $gssprache->slots;?></dt>
            <dd><?php echo $slots;?></dd>
            <dt><?php echo $sprache->timeleft;?></dt>
            <dd><?php echo $sprache->minutes;?></dd>
            <dt><?php echo $gssprache->rcon;?></dt>
            <dd><?php echo $rcon;?></dd>
            <dt><?php echo $gssprache->password;?></dt>
            <dd><?php echo $password;?></dd>
            <?php } else if ($volallowed==true) { ?>
            <dt><?php echo $vosprache->server;?></dt>
            <dd><a href="ts3server://<?php echo $server;?>?password=<?php echo $password;?>"><?php echo $server;?></a></dd>
            <dt><?php echo $vosprache->slots;?></dt>
            <dd><?php echo $slots;?></dd>
            <dt><?php echo $sprache->timeleft;?></dt>
            <dd><?php echo $timeleft.'/'.$lendtime.' '.$sprache->minutes;?></dd>
            <dt>Token</dt>
            <dd><?php echo $rcon;?></dd>
            <dt><?php echo $gssprache->password;?></dt>
            <dd><?php echo $password;?></dd>
            <?php } ?>
        </dl>
        <div>
            <?php if ($volallowed==true and $servertype=='g') { ?><a href="lend.php?w=vo"><?php echo $gsprache->voiceserver;?></a><?php } ?>
            <?php if ($gslallowed==true and $servertype!='g') { ?><a href="lend.php?w=gs"><?php echo $gsprache->gameserver;?></a><?php } ?>
        </div>
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