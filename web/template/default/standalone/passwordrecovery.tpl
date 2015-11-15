<!DOCTYPE html>
<html lang="en">
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
        .form-signin { max-width: 300px;padding: 19px 29px 29px;margin: 0 auto 20px;background-color: #fff;border: 1px solid #e5e5e5;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05); -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);box-shadow: 0 1px 2px rgba(0,0,0,.05);}
        .form-signin .form-signin-heading,
        .form-signin { margin-bottom: 10px;}
    </style>
</head>
<body>
<div class="container">
    <form class="form-signin" action="login.php?w=pr<?php echo $token;?>" method="post">
        <h2 class="form-signin-heading"><?php echo $sprache->passwordr;?></h2>
        <?php if(isset($header)) echo '<div id="redirect" class="control-group"><img src="images/16_notice.png" alt="notice" />'.$text.'</div>'; ?>
        <?php if (isset($send) and $send==true) { ?>
        <div class="control-group">
            <?php echo $text;?>
        </div>
        <div class="control-group">
            <div class="controls">
                <button class="btn btn-large btn-primary" type="submit"><?php echo $sprache->back;?></button>
            </div>
        </div>
        <?php } else if (isset($recover) and $recover==true) { ?>
        <div class="control-group">
            <label class="control-label" for="inputPass"></label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input name="password1" type="password" id="inputPass" placeholder="<?php echo $sprache->password;?>" required >
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputPassRepeat"></label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input name="password2" type="password" id="inputPassRepeat" placeholder="<?php echo $sprache->password;?>" required >
                </div>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button class="btn btn-large btn-primary" type="submit"><?php echo $sprache->passwordreset;?></button>
            </div>
        </div>
        <?php } else if (isset($recover) and $recover==false) { ?>
        <div class="control-group">
            <?php echo $sprache->linkexpired;?>
        </div>
        <div class="control-group">
            <div class="controls">
                <button class="btn btn-large btn-primary" type="submit"><?php echo $sprache->back;?></button>
            </div>
        </div>
        <?php } else if (isset($text)) { ?>
        <div class="control-group">
            <?php echo $text;?>
        </div>
        <div class="control-group">
            <div class="controls">
                <button class="btn btn-large btn-primary" type="submit"><?php echo $sprache->back;?></button>
            </div>
        </div>
        <?php } else { ?>
        <div class="control-group">
            <label class="control-label" for="inputEmail"></label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-envelope"></i></span>
                    <input name="um" type="text" id="inputEmail" placeholder="<?php echo $sprache->email;?>" required >
                </div>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button class="btn btn-large btn-primary" type="submit"><?php echo $sprache->passwordreset;?></button>
            </div>
        </div>
        <?php } ?>
    </form>
</div>
<script src="js/default/jquery.js"></script>
<script src="js/default/bootstrap.min.js"></script>
</body>
</html>