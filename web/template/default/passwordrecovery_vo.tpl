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
    <link href="css/default/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        body { padding-top: 40px;padding-bottom: 40px;background-color: #f5f5f5;}
        .form-signin { max-width: 300px;padding: 19px 29px 29px;margin: 0 auto 20px;background-color: #fff;border: 1px solid #e5e5e5;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05); -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);box-shadow: 0 1px 2px rgba(0,0,0,.05);}
        .form-signin .form-signin-heading,
        .form-signin .checkbox { margin-bottom: 10px;}
    </style>
    <link href="css/default/bootstrap-responsive.min.css" rel="stylesheet">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="js/default/html5shiv.js"></script>
    <![endif]-->
</head>
<body>
<?php if(isset($header)) echo '<div id="redirect"><img src="images/16_notice.png" alt="notice" />$text</div>'; ?>
<div class="container">
    <form class="form-signin" action="login.php?w=pr&amp;d=vo" method="post">
        <h2 class="form-signin-heading"><?php echo $sprache->passwordr;?></h2>
        <?php if (isset($text)) { ?>
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
            <label class="control-label" for="inputIP"></label>
            <div class="controls">
                <input name="ip" type="text" id="inputIP" placeholder="<?php echo $vosprache->ip;?>" required >
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputPort"></label>
            <div class="controls">
                <input name="port" type="text" id="inputPort" placeholder="<?php echo $vosprache->port;?>" required >
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputID"></label>
            <div class="controls">
                <input name="uid" type="text" id="inputID" placeholder="Unique ID" required >
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputDBID"></label>
            <div class="controls">
                <input name="dbid" type="text" id="inputDBID" placeholder="Database ID" required >
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputDBID"></label>
            <div class="controls">
                <input name="mail" type="email" id="inputDBID" placeholder="Database ID" required >
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
</body>
</html>