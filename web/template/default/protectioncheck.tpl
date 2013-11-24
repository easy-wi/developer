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
        .form-signin { max-width: 600px;padding: 19px 29px 69px;margin: 0 auto 30px;background-color: #fff;border: 1px solid #e5e5e5;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05); -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);box-shadow: 0 1px 2px rgba(0,0,0,.05);}
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
    <form class="form-signin" action="protectioncheck.php" method="post">
        <h2 class="form-signin-heading"><?php echo $sprache->protect;?></h2>
        <div class="control-group <?php if(isset($protected) and $protected=='Y'){ echo 'success'; }else{ echo 'error';}?>">
            <div class="controls">
                <label class="control-label" for="inputIP"></label>
                <div class="input-prepend input-append">
                    <span class="add-on"><i class="fa fa-shield"></i></span>
                    <input class="input-block-level" id="inputIP" type="text" name="serveraddress" value="<?php echo $ipvalue ?>" maxlength="22" required >
                    <button class="btn btn-primary" type="submit"><i class="icon-search icon-white"></i></button>
                </div>
            </div>
        </div>
        <?php if(isset($protected) and $protected=="Y"){ ?>
        <table class="table table-striped">
            <tr>
                <th><?php echo $sprache->since.' '.$since;?></th>
            </tr>
            <?php foreach($logs as $log) { ?>
            <tr>
                <td><?php echo $log;?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
    </form>
</div>
</body>
</html>