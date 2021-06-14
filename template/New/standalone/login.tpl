<!DOCTYPE html>
<html lang="en">

<head>
<?php if(isset($header)) echo $header; ?>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Login Page</title>
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo (isset($rSA['favicon']) and !empty($rSA['favicon'])) ? $rSA['favicon'] : 'images/favicon.ico';?>" />
    <link href="css/admin.css" rel="stylesheet">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/04f8aba366.js" crossorigin="anonymous"></script>
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
              <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                  </div>
                  <form class="user" action="login.php" method="post">
                    <div class="form-group">
                      <input type="text" name="username" class="form-control" placeholder="<?php echo $sprache->user;?>" required>
                    </div>
                    <div class="form-group">
                      <input type="password" id="inputPassword" name="password" class="form-control" placeholder="<?php echo $sprache->password;?>" required>
                    </div><hr>
                     <?php if ($ewCfg['captcha']==1) { ?>
                    <div class="form-group">
                        <label >Captcha: </label><br>
                        <span class="input-group-addon" style="height:background-color: grey;"><img width="100px" src="images.php" alt="Captcha" /></span></div>
                  <div class="form-group input-group has-feedback">
                <input name="captcha" type="text" class="form-control" placeholder="Captcha" pattern="^[\w]{4}$" required>
                    
                    </div><hr><?php } ?>
                   <button type="submit" class="btn btn-primary btn-block btn-flat">Login</button><br>
                  </form>
					 <?php if(count($serviceProviders)>0){ ?>
               	<?php foreach($serviceProviders as $k=>$css){ ?>
            	<a href="login.php?serviceProvider=<?php echo $k;?>" class="btn btn-secondary btn-block btn-flat btn-social btn-<?php echo $css;?>"><i class="fa fa-<?php echo $css;?>"></i> Sign in using <?php echo $k;?></a>
            	<?php } ?>
        		<?php } ?>
					<br>
					
                   
                  <a href="login.php?w=pr" class="btn btn-secondary btn-block btn-flat ">
                    <span class="icon text-white-50">
                      <i class="fas fa-arrow-right"></i>
                    </span>
                    <span class="text">password recovery</span>
                  </a>
					
					
				
				  
				  </div>
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

</body>

</html>
