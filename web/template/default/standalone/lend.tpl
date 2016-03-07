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

        <div class="login-box-body">

            <p class="login-box-msg"><?php echo $gsprache->lendserver;?> <?php echo ($servertype=='g') ? $gsprache->gameserver : $gsprache->voiceserver;?></p>


            <?php if(isset($header)){ ?>
            <div class="box box-primary">
                <div class="alert alert-danger">
                    <p><?php echo $text;?></p>
                </div>
            </div>
            <?php } ?>

            <div class="box box-info">
                <div class="info alert-info">
                    <p>
                        <?php echo $sprache->nextfree.' '.$nextfree." ".$sprache->minutes;?><br>
                        <?php echo $sprache->nextcheck.' '.$nextcheck.' '.$sprache->minutes;?>
                    </p>
                </div>
                <div>
                    <?php if ($volallowed==true and $servertype=='g') { ?><a href="lend.php?w=vo"><?php echo $gsprache->voiceserver;?></a><?php } ?>
                    <?php if ($gslallowed==true and $servertype!='g') { ?><a href="lend.php?w=gs"><?php echo $gsprache->gameserver;?></a><?php } ?>
                </div>
            </div>

            <form action="login.php" method="post">

                <?php if ($servertype=='g' and $gslallowed==true) { ?>
                <div class="form-group">
                    <label for="inputGame"><?php echo $gssprache->game;?></label>
                    <select name="game" id="inputGame" class="form-control">
                        <?php foreach($gameselect as $key=>$option) echo '<option value="'.$key.'">'.$option.'</option>';?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="inputSlots"><?php echo $gssprache->slots;?></label>
                    <select name="slots" id="inputSlots" class="form-control">
                        <?php foreach($slotselect as $option) echo '<option>'.$option.'</option>';?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="inputTime"><?php echo $sprache->maxtime;?></label>
                    <select name="time" id="inputTime" class="form-control">
                        <?php foreach($timeselect as $option) echo '<option>'.$option.'</option>';?>
                    </select>
                </div>

                <div class="form-group has-feedback">
                    <label for="inputRcon"><?php echo $gssprache->rcon;?></label>
                    <input name="rcon" type="text" id="inputRcon" value="<?php echo $rcon;?>" pattern="[0-9a-zA-Z]{3,20}" class="form-control" required>
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    <label for="inputPassword"><?php echo $gssprache->password;?></label>
                    <input name="password" type="text" id="inputPassword" value="<?php echo $password;?>" pattern="[0-9a-zA-Z]{3,20}" class="form-control" required>
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>

                <?php if ($ftpupload=='Y') { ?>
                <div class="form-group has-feedback">
                    <label for="inputDemo"><?php echo $sprache->ftpuploadpath;?></label>
                    <input name="ftpuploadpath" type="text" id="inputDemo" value="ftp://username:password@1.1.1.1/demos" pattern="^(ftp|ftps):\/\/([\w\.\:\/\-\_]{1,}:[\w]{1,}|[\w]{1,})@[\w\.\:\/\-\_]{1,}$" class="form-control" >
                    <span class="fa fa-lock form-control-feedback"></span>
                </div>
                <?php } ?>

                <?php } else if ($volallowed==true) { ?>

                <div class="form-group">
                    <label for="inputSlots"><?php echo $vosprache->slots;?></label>
                    <select name="slots" id="inputSlots" class="form-control">
                        <?php foreach($voslotselect as $option) echo '<option>'.$option.'</option>';?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="inputTime"><?php echo $sprache->maxtime;?></label>
                    <select name="time" id="inputTime" class="form-control">
                        <?php foreach($votimeselect as $option) echo '<option>'.$option.'</option>';?>
                    </select>
                </div>
                <div class="form-group has-feedback">
                    <label for="inputPassword"><?php echo $gssprache->password;?></label>
                    <input name="password" type="text" id="inputPassword" value="<?php echo $password;?>" pattern="[0-9a-zA-Z]{3,20}" class="form-control" required>
                </div>
                <div class="hide" aria-hidden="true"><input type="hidden" name="voice" value="1" ></div>
                <?php } ?>

                <?php if ($serveravailable==true) { ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="hide" aria-hidden="true">
                            <input type="text" name="email">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo $sprache->lend; ?></button>
                    </div><!-- /.col -->
                </div>
                <?php }?>
            </form>
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