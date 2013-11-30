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
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <?php if(isset($header)) echo $text; ?>
    <?php if ($servertype=='g' and $gslallowed==true) { ?>
    <form class="form-signin" action="lend.php?w=gs" method="post" >
        <h2 class="form-signin-heading"><?php echo $gsprache->lendserver.' '.$gsprache->gameserver; ?></h2>
        <?php if ($volallowed==true) { ?><h3><a href="lend.php?w=vo"><?php echo $gsprache->voiceserver;?></a></h3><?php } ?>
        <p><?php echo $sprache->nextfree.' '.$nextfree." ".$sprache->minutes;?></p>
        <p><?php echo $sprache->nextcheck.' '.$nextcheck.' '.$sprache->minutes;?></p>
        <?php foreach ($status as $key=>$value){ ?>
        <p><?php echo $key.': '.$sprache->available.' '.$value['amount'].'/'.$value['total'];?></p>
        <?php } ?>
        <?php if ($serveravailable==true) { ?>
        <div class="control-group">
            <label class="control-label" for="inputGame"><?php echo $gssprache->game;?></label>
            <div class="controls">
                <select name="game" id="inputGame">
                    <?php foreach($gameselect as $key=>$option) echo '<option value="'.$key.'">'.$option.'</option>';?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputSlots"><?php echo $gssprache->slots;?></label>
            <div class="controls">
                <select name="slots" id="inputSlots">
                    <?php foreach($slotselect as $option) echo '<option>'.$option.'</option>';?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputTime"><?php echo $sprache->maxtime;?></label>
            <div class="controls">
                <select name="time" id="inputTime">
                    <?php foreach($timeselect as $option) echo '<option>'.$option.'</option>';?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputRcon"><?php echo $gssprache->rcon;?></label>
            <div class="controls">
                <input name="rcon" type="text" id="inputRcon" value="<?php echo $rcon;?>" pattern="[0-9a-zA-Z]{3,20}" required>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputPassword"><?php echo $gssprache->password;?></label>
            <div class="controls">
                <input name="password" type="text" id="inputPassword" value="<?php echo $password;?>" pattern="[0-9a-zA-Z]{3,20}" required>
            </div>
        </div>
        <?php if ($ftpupload=='Y') { ?>
        <div class="control-group">
            <label class="control-label" for="inputDemo"><?php echo $sprache->ftpuploadpath;?></label>
            <div class="controls">
                <input name="ftpuploadpath" type="text" id="inputDemo" value="ftp://username:password@1.1.1.1/demos" pattern="^(ftp|ftps):\/\/([\w\.\:\/\-\_]{1,}:[\w]{1,}|[\w]{1,})@[\w\.\:\/\-\_]{1,}$" >
            </div>
        </div>
        <?php } ?>
        <?php } ?>
    <?php } else if ($volallowed==true) { ?>
    <form class="form-signin" action="lend.php?w=vo" method="post">
        <h2 class="form-signin-heading"><?php echo $gsprache->lendserver.' '.$gsprache->voiceserver; ?></h2>
        <?php if ($gslallowed==true) { ?><h3 class="middle"><a href="lend.php?w=gs"><?php echo $gsprache->gameserver;?></a></h3><?php } ?>
        <p><?php echo $sprache->nextfreevo.' '.$nextfree." ".$sprache->minutes;?></p>
        <p><?php echo $sprache->nextcheck.' '.$nextcheck.' '.$sprache->minutes;?></p>
        <?php if ($serveravailable==true) { ?>
        <div class="control-group">
            <label class="control-label" for="inputSlots"><?php echo $vosprache->slots;?></label>
            <div class="controls">
                <select name="slots" id="inputSlots">
                    <?php foreach($voslotselect as $option) echo '<option>'.$option.'</option>';?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputTime"><?php echo $sprache->maxtime;?></label>
            <div class="controls">
                <select name="time" id="inputTime">
                    <?php foreach($votimeselect as $option) echo '<option>'.$option.'</option>';?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputPassword"><?php echo $gssprache->password;?></label>
            <div class="controls">
                <input name="password" type="text" id="inputPassword" value="<?php echo $password;?>" pattern="[0-9a-zA-Z]{3,20}" required>
            </div>
        </div>
        <div class="hide" aria-hidden="true"><input type="hidden" name="voice" value="1" ></div>
        <?php } ?>
        <?php } ?>
        <?php if ($serveravailable==true) { ?>
            <div class="control-group">
                <div class="controls">
                    <button class="btn btn-large btn-primary pull-right" type="submit"><?php echo $sprache->lend; ?></button>
                </div>
            </div>
            <div class="hide" aria-hidden="true">
                <input type="text" name="email">
            </div>
        <?php }?>
    </form>
        <hr>
        <div>
            &copy; <a href="https://easy-wi.com" target="_blank" title="free gameserver, voiceserver, dedicated and virtualserver webinterface easy-wi.com">Easy-WI.com</a> 2011 - <?php echo date('Y'); ?>
        </div>
</div>
</body>
</html>