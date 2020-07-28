<!DOCTYPE html>
<html dir="ltr" lang="<?php echo $user_language;?>">

<head>
    <?php if(isset($header)) echo $header; ?>
    <meta charset="UTF-8">
    <title><?php if(isset($ewCfg['title'])) echo $ewCfg['title']; ?></title>

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <meta name="robots" content="index,follow,noodp,noydir" />
    <meta name="description" content="">
    <meta name="author" content="2012 - <?php echo date('Y'); ?> <?php if(isset($ewCfg['title'])) echo $ewCfg['title']; ?>">

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo (isset($rSA['favicon']) and !empty($rSA['favicon'])) ? $rSA['favicon'] : 'images/favicon.ico';?>" />

    
    <!-- AdminLTE App -->
    <script src="js/default/app.js" type="text/javascript"></script>
    <!-- Easy-Wi -->
    <script src="js/default/easy-wi.js" type="text/javascript"></script>
        <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/04f8aba366.js" crossorigin="anonymous"></script>

    <!-- Theme style -->
    <link href="css/custom.css" rel="stylesheet" type="text/css" />
    <link href="css/default/skin-<?php echo $rSA['templateColor'];?>.css" rel="stylesheet" type="text/css" />
    <!-- Easy-Wi custom styles -->
    <link href="css/default/easy-wi.css" rel="stylesheet" type="text/css" />  

    <?php echo implode('',$htmlExtraInformation['css']);?>


    <?php echo implode('',$htmlExtraInformation['js']);?>


</head>

<style type="text/css">
    .navbar-dark .navbar-nav .nav-link {
    color: rgba(255,255,255,.9);
    }
</style>

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
  <a class="navbar-brand" href="<?php echo $rSA['header_href'];?>"  target="_blank">
    <img src="images/<?php echo $rSA['header_icon'];?>" title="<?php echo $rSA['header_text'];?>" width="52" height="auto" alt="<?php echo $rSA['header_text'];?>">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-home fa-fw"></i> Home
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="userpanel.php?w=da"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
           <?php if(!isset($_SESSION['sID'])){ ?>
          <a class="dropdown-item" href="userpanel.php?w=su"><i class="fa fa-users"></i> <?php echo $gsprache->substitutes;?></a>
              <?php }?>
          <a class="dropdown-item" href="userpanel.php?w=lo"><i class="fa fa-list-alt"></i> <?php echo $gsprache->logs;?></a></a>
          <?php if($easywiModules['ip']){ ?>
          <a class="dropdown-item" href="userpanel.php?w=ip"><i class="fa fa-gavel"></i> <?php echo $gsprache->imprint;?></a></a>
          <?php }?>
        <?php foreach ($customModules['us'] as $k => $v) { echo '<a class="dropdown-item" '; echo ($ui->smallletters('w',255,'get')==$k) ? '' : ''; echo '><a href="userpanel.php?w='.$k.'" class="dropdown-item"><i class="fa fa-angle-double-right"></i> '.$v.'</a></a>'; }; ?>
        </div>
      </li>
      <?php if($easywiModules['ti'] and $pa['usertickets']) { ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-life-ring"></i> Ticket System 
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="userpanel.php?w=ti"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
          <a  class="dropdown-item" href="userpanel.php?w=ti&amp;d=ad"><i class="fa fa-plus-circle"></i> Ticket</a>
          <?php foreach ($customModules['ti'] as $k => $v) { echo '<a class="dropdown-item"'; echo ($ui->smallletters('w',255,'get')==$k) ? '' : ''; echo '><i class="fa fa-angle-double-right"></i> <a  class="dropdown-item" href="userpanel.php?w='.$k.'">'.$v.'</a></a>'; }; ?>

        </div>
      </li>
       <?php } ?>


                                        <?php if($easywiModules['gs'] and $gscount>0 and $pa['restart']) { ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <?php if($pa['restart']) { ?>
          <a class="dropdown-item"  href="userpanel.php?w=gs"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
           <?php } ?><?php if($pa['fastdl']) { ?>
          <a class="dropdown-item" href="userpanel.php?w=fd"><i class="fas fa-cloud"></i> <?php echo $gsprache->fastdownload;?></a>
          <?php } ?><?php if($pa['restart']) { ?>
          <a class="dropdown-item"  href="userpanel.php?w=ms"><i class="fa fa-truck"></i> <?php echo $gsprache->migration;?></a>
          <a class="dropdown-item" href="userpanel.php?w=gt"><i class="fa fa-floppy-o"></i> <?php echo $gsprache->file.' '.$gsprache->template;?></a>
          <?php } ?>
          <?php foreach ($customModules['gs'] as $k => $v) { echo '<a class="dropdown-item" '; echo ($ui->smallletters('w',255,'get')==$k) ? '' : ''; echo '><i class="fa fa-angle-double-right"></i> <a  href="userpanel.php?w='.$k.'">'.$v.'</a></a>'; }; ?>
        </div>
      </li>
      <?php } ?>

                                        <?php if($easywiModules['vo'] and ($voicecount>0 or $tsdnscount>0) and $pa['voiceserver']) { ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
         <i class="fab fa-teamspeak"></i> Teamspeak³|5
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <?php if($voicecount>0) { ?>
          <a class="dropdown-item" href="userpanel.php?w=vo"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
          <?php } ?><?php if($voicecount>0) { ?>
          <a class="dropdown-item" href="userpanel.php?w=vu"><i class="fa fa-area-chart"></i> <?php echo $gsprache->stats;?></a>
           <?php } ?><?php if($tsdnscount>0) { ?>
          <a class="dropdown-item" href="userpanel.php?w=vd"><i class="fa fa-link"></i> TS3 DNS</a>
          <?php } ?>
          <?php foreach ($customModules['vo'] as $k => $v) { echo '<a class="dropdown-item"'; echo ($ui->smallletters('w',255,'get')==$k) ? '' : ''; echo '><i class="fa fa-angle-double-right"></i> <a  href="userpanel.php?w='.$k.'">'.$v.'</a></a>'; }; ?>
        </div>
      </li>
      <?php } ?>

                                                <?php if($easywiModules['ws'] and $vhostcount>0 and $pa['webvhost']) { ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
         <i class="fa fa-cubes"></i> <?php echo $gsprache->webspace;?>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="userpanel.php?w=wv"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
          <?php foreach ($customModules['ws'] as $k => $v) { echo '<a class="dropdown-item" '; echo ($ui->smallletters('w',255,'get')==$k) ? '' : ''; echo '><i class="fa fa-angle-double-right"></i> <a href="userpanel.php?w='.$k.'">'.$v.'</a></a>'; }; ?>
        </div>
      </li>
      <?php } ?>


                                                        <?php if($easywiModules['my'] and $dbcount>0 and ($pa['mysql'] or $pa['mysql'])) { ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-database"></i> MySQL
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a  class="dropdown-item" href="userpanel.php?w=my"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
          <?php foreach ($customModules['my'] as $k => $v) { echo '<a class="dropdown-item" '; echo ($ui->smallletters('w',255,'get')==$k) ? '' : ''; echo '><i class="fa fa-angle-double-right"></i> <a href="userpanel.php?w='.$k.'">'.$v.'</a></a>'; }; ?>
        </div>
      </li>
      <?php } ?>


                                                                <?php if($easywiModules['ro'] and ($virtualcount+$rootcount)>0 and $pa['roots']) { ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-laptop"></i> Rootserver
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <?php if($rootcount>0) { ?>
          <a  class="dropdown-item" href="userpanel.php?w=de"><i class="fa fa-angle-double-right"></i> <?php echo $gsprache->dedicated;?></a>
           <?php } ?><?php if($virtualcount>0) { ?>
          <a  class="dropdown-item" <i class="fa fa-angle-double-right"></i> <?php echo $gsprache->virtual;?></a>
          <?php } ?>
          <?php foreach ($customModules['ro'] as $k => $v) { echo '<a class="dropdown-item" '; echo ($ui->smallletters('w',255,'get')==$k) ? '' : ''; echo '><i class="fa fa-angle-double-right"></i> <a href="userpanel.php?w='.$k.'">'.$v.'</a></a>'; }; ?>
        </div>
      </li>
       <?php } ?>

                                                                        <?php if(count($customModules['mo'])>0) { ?>
      <li class="nav-item dropdown justify-content-end">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-tasks"></i> <?php echo $gsprache->modules;?>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <?php foreach ($customModules['mo'] as $k => $v) { echo '<a class="dropdown-item" '; echo ($ui->smallletters('w',255,'get')==$k) ? '' : ''; echo '><a href="userpanel.php?w='.$k.'"><i class="fa fa-tasks"></i> '.$v.'</a></a>'; }; ?>
        </div>
      </li>
      <?php } ?>
    </ul>
  </div>
<?php if($statsArray['ticketsTotal']>0){ ?>
    <div class="float-right">
        <a style="color:white;" class="badge badge-success" href="userpanel.php?w=ti">
            <i class="fa fa-life-ring"></i>
            <span class="label"><?php echo $statsArray['ticketsTotal'];?></span>
        </a>
    </div>
    <div style="width: 10px"></div>
    <?php } ?>

  <?php if($statsArray['warningTotal']>0){ ?>
    <div class="float-right">

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
        <li class="nav-item dropdown dropleft">
        <a style="color:white;" class="dropdown-toggle badge badge-danger" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="userpanel.php?w=ti">
            <i class="fa fa-warning"></i>
            <span class="label"><?php echo $statsArray['warningTotal'];?></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="userpanel.php?w=gs&amp;d=md"><i class="fa fa-warning danger"></i> <?php echo $statsArray['gameserverNotRunning'].' '.$sprache_bad->gserver_crashed;?></a>
            <a class="dropdown-item" href="userpanel.php?w=gs&amp;d=md"><i class="fa fa-warning warning"></i> <?php echo $statsArray['gameserverNoPassword'].' '.$sprache_bad->gserver_removed;?></a>
            <a class="dropdown-item" href="userpanel.php?w=gs&amp;d=md"><i class="fa fa-warning warning"></i> <?php echo $statsArray['gameserverNoTag'].' '.$sprache_bad->gserver_tag_removed;?></a>
            <a class="dropdown-item" href="userpanel.php?w=vo&amp;d=md"><i class="fa fa-warning danger"></i> <?php echo $statsArray['voiceserverCrashed'].' '.$sprache_bad->voice_crashed;?></a>
        </div>
    </li>
</ul>
</div>
</div>
<?php } ?>


    <div class="form-inline">
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item dropdown dropleft">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img height="20px" src="images/Places-user-identity-icon.png" class="user-image" alt="User Image">
                    <?php echo $great_user;?> 
                    </a>
                    <div class="dropdown-menu text-muted" aria-labelledby="navbarDropdownMenuLink">
                        <h3 class="p-4 text-muted"><?php echo $great_user;?></h3>
                        <div class="dropdown-divider"></div>
                        <h5 class="dropdown-header"><?php echo $gsprache->last.'<br />'.$great_last;?></h5>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="index.php"><i class="fas fa-sign-in-alt"></i> Frontend</a>
                        <?php if ($support_phonenumber!="") echo '<a class="dropdown-item" href="#"><i class="fa fa-phone fa-fw"></i> '.$gsprache->hotline.": ".$support_phonenumber.'</a>';?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="userpanel.php?w=se&amp;d=pw"><i class="fa fa-key fa-fw"></i> <?php echo $gsprache->password." ".$gsprache->change;?></a>
                        <a class="dropdown-item" href="userpanel.php?w=se"><i class="fa fa-cog fa-fw"></i> <?php echo $gsprache->settings;?></a>
                        <div class="dropdown-divider"></div>
                        <h5 class="dropdown-header">Languages:</h5>
                        <div class="dropdown-header">
                        <?php foreach ($languages as $language){ echo '<a style="align-content: center;" href="userpanel.php?l='.$language.'"> <img src="images/flags/'.$language.'.png" alt="Flag: '.$language.'.png."></a>     ';} ?>
                        </div>
                        <h5 class="dropdown-header">Translations still incomplete!</h5>
                        <div class="dropdown-divider"></div><br>

                        <div class="pull-right">
                        <a href="userpanel.php?w=se" class="btn btn-success btn-flat">Profile</a>
                        <a href="login.php?w=lo" class="btn btn-default btn-danger" style="color:white;"><i class="fa fa-power-off"></i> Logout</a>
                        </div>
                        <div style="padding-right: 20px"></div>
                                                        

                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<body <?php echo implode(' ',$htmlExtraInformation['body']);?>>
    <div class="row">
      <div class="col-md-1"></div>
      <div class="col-md-10">

<div style="height:100px"></div>
