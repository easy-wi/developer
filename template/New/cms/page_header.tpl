<!DOCTYPE html>
<html dir="ltr" lang="<?php echo $page_data->language;?>">
<head>
    <?php if(isset($header)) echo $header; ?>
    <meta charset="utf-8">
    <title><?php echo $page_data->title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index,follow,noodp,noydir" />
    <meta name="description" content="">
    <meta name="author" content="2012 - <?php echo date('Y'); ?> Ulrich Block">

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo (isset($rSA['favicon'])) ? $rSA['favicon'] : 'images/favicon.ico';?>" />

    <link rel="canonical" href="<?php echo $page_data->canurl;?>" />
    <link href="<?php echo $page_data->getDefaultUrl();?>" hreflang="x-default" rel="alternate">
    <?php foreach ($page_data->getLangLinks() as $l=>$v){ ?>
    <?php echo '<link href="'.$v.'" hreflang="'.$l.'" rel="alternate">'."\n"; ?>
    <?php }?>

    <link href="<?php echo $page_data->pageurl;?>css/admin.css" rel="stylesheet">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/04f8aba366.js" crossorigin="anonymous"></script>
    <link href="<?php echo $page_data->pageurl;?>css/ftnaws.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $page_data->pageurl;?>css/custom.css" rel="stylesheet" type="text/css">
        <!-- jQuery -->
    <script src="<?php echo $page_data->pageurl;?>js/default/jquery.min.js" type="text/javascript"></script>

    <!-- Bootstrap JS -->
    <script src="<?php echo $page_data->pageurl;?>js/default/bootstrap.min.js" type="text/javascript"></script>

    <!-- AdminLTE App -->
    <script src="<?php echo $page_data->pageurl;?>js/default/app.js" type="text/javascript"></script>

    <!-- Easy-Wi -->
    <script src="<?php echo $page_data->pageurl;?>js/default/easy-wi.js" type="text/javascript"></script>
    <?php echo implode('',$htmlExtraInformation['js']);?>
    <?php echo implode('',$htmlExtraInformation['css']);?>


    <?php if(isset($page_feeds)) echo $page_feeds; ?>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript">window.onDomReady(onReady); function onReady() { SwitchShowHideRows('init_ready');}</script>
    <script type="text/javascript">$(function() { $('table').footable();});</script>
</head>

<body <?php echo implode(' ',$htmlExtraInformation['body']);?>>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner text-center">
       
        <div class="nav-collapse collapse">
            <p class="navbar-text pull-left">
                &nbsp;&nbsp;
                <?php foreach ($page_data->getLangLinks() as $l=>$v) { echo '<a href="'.$v.'"><img src="images/flags/'.$language.'.png" alt="Flag: '.$language.'.png."></a>';}?>

                
                &nbsp;&nbsp;
            </p>

            <?php if (isset($admin_id) or isset($user_id)) { ?>

            <a href="<?php echo removeDoubleSlashes($page_data->pageurl . '/login.php?w=lo');?>" class="navbar-text pull-right navbar-logout">
                <span class="btn btn-mini btn-danger"><i class="fa fa-sign-out"></i> Logout</span>
            </a>
            <ul class="nav pull-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $great_user;?><b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#"><?php echo $gsprache->last.'<br />'.$great_last;?></a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo (isset($admin_id)) ? removeDoubleSlashes($page_data->pageurl.'/admin.php') : removeDoubleSlashes($page_data->pageurl.'/userpanel.php');?>"><i class="fa fa-sign-in fa-fw"></i> Backend</a></li>
                        <li class="divider"></li>
                        <?php if ($support_phonenumber!="") echo '<li><a href="#"><i class="fa fa-phone fa-fw"></i> '.$gsprache->hotline.": ".$support_phonenumber.'</a></li>';?>
                        <li class="divider"></li>

                    </ul>
                 </li>
            </ul>
            <?php } else { ?>
            <div class="navbar-text navbar-form pull-right navbar-logout">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#Modal">
            Login
            </button></div>
            <?php } ?>
        </div>
    </div>
</div>

         
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
             <a class="navbar-brand" href="/index.php"> <?php echo $rSA['header_text'];?></a>

                  <div class="navbar-collapse collapse" id="navbarResponsive" style="">
                    <ul class="navbar-nav ml-auto">

                    <li class="nav-item" <?php if($s=='news') echo 'class="active"';?>><a class="nav-link" href="<?php echo $page_data->pages['news']['link'];?>"><i class="fa fa-bullhorn fa-fw"></i> <?php echo $page_data->pages['news']['linkname'];?></a></li>

                    <li class="nav-item" <?php if($s=='about') echo 'class="active"';?>><a class="nav-link" href="<?php echo $page_data->pages['about']['link'];?>"><i class="fa fa-info-circle fa-fw"></i> <?php echo $page_data->pages['about']['linkname'];?></a></li>

                    <li class="nav-item" <?php if($s=='gallery') echo 'class="active"';?>><a class="nav-link" href="<?php echo $page_data->pages['gallery']['link'];?>"><i class="fa fa-picture-o fa-fw"></i> <?php echo $page_data->pages['gallery']['linkname'];?></a></li>

                    <?php if($easywiModules['ip']){ ?><li class="nav-item" <?php if($s=='imprint') echo 'class="active"';?>><a class="nav-link" href="<?php echo $page_data->pages['imprint']['link'];?>"><i class="fa fa-gavel fa-fw"></i> <?php echo $page_data->pages['imprint']['linkname'];?></a></li><?php }?>-->

                    <li class="nav-item" <?php if($s=='contact') echo 'class="active"';?>><a class="nav-link" href="<?php echo $page_data->pages['contact']['link'];?>"><i class="fa fa-envelope fa-fw"></i> <?php echo $page_data->pages['contact']['linkname'];?></a></li>

                    <li class="nav-item" <?php if($s=='downloads') echo 'class="active"';?>><a class="nav-link" href="<?php echo $page_data->pages['downloads']['link'];?>"><i class="fa fa-download fa-fw"></i> <?php echo $page_data->pages['downloads']['linkname'];?></a></li> 

                    <?php if($page_data->protectioncheck=='Y'){ ?><li class="nav-item" <?php if($s=='protectioncheck') echo 'class="active"';?>><a class="nav-link" href="<?php echo $page_data->pages['protectioncheck']['link'];?>"><i class="fa fa-shield fa-fw"></i> <?php echo $page_data->pages['protectioncheck']['linkname'];?></a></li><?php } ?>
                    <li class="divider"></li>

                    <?php if($page_data->lendactive=='Y'){ ?>
                     <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle"" href="<?php echo $page_data->pages['lendserver']['link'];?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-list fa-fw"></i> <?php echo $page_data->pages['lendserver']['linkname'];?></a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="<?php echo $page_data->pages['lendserver']['link'];?>"><i class="fa fa-list fa-fw"></i> Übersicht</a>
                           <?php if (isset($page_data->pages['lendservervoice'])) { ?><a class="dropdown-item" href="<?php echo $page_data->pages['lendservervoice']['link'];?>"><i class="fa fa-microphone fa-fw"></i> <?php echo $page_data->pages['lendservervoice']['linkname'];?></a><?php } ?>

                        <?php if (isset($page_data->pages['lendservergs'])) { ?><a class="dropdown-item" href="<?php echo $page_data->pages['lendservergs']['link'];?>"><i class="fa fa-gamepad fa-fw"></i> <?php echo $page_data->pages['lendservergs']['linkname'];?></a><?php } ?>
                            </div>
                      </li>
                   
            

 
                   <li class="divider"></li>
                    <?php
                        function GetSubLinks($pagelist,$id,$sub=1){ global $page_id; $return='';if(isset($pagelist[$id])){foreach($pagelist[$id] as $k=>$sl){ if ($id!=$k){ $return.='<li class="nav-item"  '; if(isset($page_id) and $page_id==$k) $return.=' class="active"';  $return.='>';$return.=$sl['href'].'</li>';$return.=GetSubLinks($pagelist,$k,$sub+1);}}}return $return;}
                        foreach ($page_data->pages as $key=>$value){if(isid($key,'30')){ echo'<li class="nav-item '; if(isset($page_id) and $page_id==$key) echo ' class="active"';  echo '>'.$value[$key]['href'].'</li>';echo GetSubLinks($page_data->pages,$key);}}
                    ?>

                    
                      
        </div>
    </div>
</div></li>

                    <?php } ?>

                    </ul>
                  </div>
                </div>
              </nav>


              <?php if (!isset($admin_id) and !isset($user_id)) { ?>
<!-- Modal -->
<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document"><div class="modal-content">
        <form action="/login.php" method="post">
            <div class="container">
  <div class="row">
    <div class="col-sm">
  <div class="form-group">
    <label for="inputUser">Username / EMail:</label>
    <input name="username" id="inputUser" type="text" class="form-control" placeholder="User/Email" required >
  </div>
  <div class="form-group">
    <label for="inputPassword">Password:</label>
    <input name="password" id="inputPassword" type="password" class="form-control" placeholder="Password" required >
  </div><?php if ($ewCfg['captcha']==1) { ?><hr>
  <div class="form-group">
    <label class="form-check-label" for="inputCaptcha">Captcha:</label>
    <img width="100px" src="<?php echo $page_data->pageurl;?>images.php" alt="Captcha" />
     <input name="captcha" id="inputCaptcha" type="text" class="form-control" placeholder="Captcha" pattern="^[\w]{4}$" required >
  </div><?php } ?><hr>
  <button type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
</form>
<a href="login.php?w=pr" class="btn btn-secondary btn-block btn-flat ">
                    <span class="icon text-white-50">
                      <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </span>
                    <span class="text">password recovery</span>
                  </a>
</div></div></div></div>
</div></div>
<?php } ?>
<br><br>
<div class="container">

    <div class="row">

      <div class="col-lg-3">
        <h1 class="my-4">Informations</h1>
        <div class="list-group">

             <?php if (isset($admin_id) or isset($user_id)) { ?>
             
            <a href="#" class="dropdown-toggle btn" data-toggle="dropdown"><?php echo $great_user;?><b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
                            <li class="user-header hidden-xs">
                         
                          </li>
                           <li class="hidden-xs">
                          <div class="alert alert-secondary" role="alert">
                              <?php echo $gsprache->last.'<br />'.$great_last;?>
                            </div></li>
                        
                        <li class="divider hidden-xs"></li>
                         
                        <li class="divider"></li>
                        <?php if ($support_phonenumber!="") echo '<li><a href="#"><i class="fa fa-phone fa-fw"></i> '.$gsprache->hotline.": ".$support_phonenumber.'</a></li>';?>
                        
                        <li><a href="mailto:<?php include 'template/AEON/config.php'; echo "$mailkontakt"; ?>"  class="btn"><i class="fas fa-envelope-open-text"></i> <?php include 'template/AEON/config.php'; echo "$mailkontakt"; ?></a></li><li class="divider"></li>
                        <li><a href="/userpanel.php?w=se&amp;d=pw"  class="btn"><i class="fa fa-key fa-fw"></i> <?php echo $gsprache->password." ".$gsprache->change;?></a></li>
                        <li><a href="/userpanel.php?w=se"  class="btn"><i class="fa fa-cog fa-fw"></i> <?php echo $gsprache->settings;?></a></li>
                        <li class="divider  hidden-xs"></li>
                        <div style="padding-left: 20%"><li>
                        <h4>Sprachen:</h4></li>
                        <?php foreach ($page_data->getLangLinks() as $l=>$v) { echo '<a href="'.$v.'"><img src="/images/flags/'.$l.'.png" alt="Flag: '.$language.'.png."></a>';}?>
                    <br><small>Translations still incomplete!</small></div>
            </ul>
            <hr>
            <a href="<?php echo removeDoubleSlashes($page_data->pageurl . '/login.php?w=lo');?>" class="navbar-text pull-right navbar-logout">
            <button  type="button" class="btn btn-danger" data-toggle="modal" data-target="#Modal">
            <i class="fa fa-sign-out"></i> Logout
            </button></a>
          <a href="<?php echo (isset($admin_id)) ? removeDoubleSlashes($page_data->pageurl.'/admin.php') : removeDoubleSlashes($page_data->pageurl.'/userpanel.php');?>">
            <button  type="button" class="btn btn-primary" data-toggle="modal" data-target="#Modal">
            <i class="fa fa-sign-in fa-fw"></i> Backend
            </button></a>
          <?php } else { ?>
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#Modal">
            Login
            </button>
            <?php } ?>
        </div>
      </div>
      <!-- /.col-lg-3 -->
<div class="col-lg-9"><br><br>

     