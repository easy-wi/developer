  <!DOCTYPE html>
<html lang="en">

<head>
    <?php if(isset($header)) echo $header; ?>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo $page_data->title; ?></title>
    <link href="css/admin.css" rel="stylesheet">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/04f8aba366.js" crossorigin="anonymous"></script>
    <link href="css/ftnaws.min.css" rel="stylesheet" type="text/css">
    <link href="css/custom.css" rel="stylesheet" type="text/css">
        <!-- jQuery -->
    <script src="js/default/jquery.min.js" type="text/javascript"></script>

    <!-- Bootstrap JS -->
    <script src="js/default/bootstrap.min.js" type="text/javascript"></script>

    <!-- AdminLTE App -->
    <script src="js/default/app.js" type="text/javascript"></script>

    <!-- Easy-Wi -->
    <script src="js/default/easy-wi.js" type="text/javascript"></script>

</head>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-2"><?php echo $sprache->passwordr;?></h1>
                  </div>
                  <form class="user" action="login.php?w=pr<?php echo $token;?>" method="post">
                     <?php if (isset($send) and $send==true) { ?>
                    <div class="form-group">
                      <?php echo $text;?>
                    </div>
                    <div class="row">
                      <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo $sprache->back;?></button>
                </div>
            </div>
                         <?php } else if (isset($recover) and $recover==true) { ?>
<div class="form-group has-feedback">
                <input type="password" id="inputPass" name="password1" class="form-control" placeholder="<?php echo $sprache->password;?>" required>
                <span class="fa fa-lock form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
                <input type="password" id="inputPassRepeat" name="password2" class="form-control" placeholder="<?php echo $sprache->password;?>" required>
                <span class="fa fa-lock form-control-feedback"></span>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo $sprache->passwordreset;?></button>
                </div>
            </div>

            <?php } else if (isset($recover) and $recover==false) { ?>

                    <div class="form-group">
                <?php echo $sprache->linkexpired;?>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo $sprache->back;?></button>
                </div>
            </div>

            <?php } else if (isset($text)) { ?>

            <div class="form-group">
                <?php echo $text;?>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo $sprache->back;?></button>
                </div>
            </div>

            <?php } else { ?>

            <div class="form-group has-feedback"><hr>
                <input type="text" name="um" class="form-control" placeholder="<?php echo $sprache->email;?>" required>
                
            </div>

                    <button type="submit" class="btn btn-primary btn-block btn-flat "><?php echo $sprache->passwordreset;?></button><br>

            <?php } ?>

        </form>
                  
                  <a href="login.php" class="btn btn-secondary btn-block btn-flat ">
                    <span class="icon text-white-50">
                      <i class="fas fa-arrow-right"></i>
                    </span>
                    <span class="text">Already got an account? Login!</span>
                  </a></div><hr>
                  <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Coded by <a href="https://easy-wi.com" target="_blank" title="free gameserver, voiceserver, dedicated and virtualserver webinterface easy-wi.com">Easy-WI.com</a> 2011 - <?php echo date('Y'); ?>  <br>  Redesigned with ♥ by <a href="https://glitch.management/"> Glitch.Management </a>2018 - <?php echo date('Y'); ?> </span>
          </div>
        </div>
      </footer>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Bootstrap core JavaScript-->

</body>

</html>
