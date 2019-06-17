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

    <!-- Bootstrap CSS -->
    <link href="css/default/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="css/default/font-awesome.min.css" rel="stylesheet">

    <!-- Theme style -->
    <link href="css/default/AdminLTE.css" rel="stylesheet" type="text/css" />
    <link href="css/default/skin-<?php echo $rSA['templateColor'];?>.css" rel="stylesheet" type="text/css" />

    <!-- Easy-Wi custom styles -->
    <link href="css/default/easy-wi.css" rel="stylesheet" type="text/css" />

    <?php echo implode('',$htmlExtraInformation['css']);?>

    <!-- jQuery -->
    <script src="js/default/jquery.min.js" type="text/javascript"></script>

    <!-- Bootstrap JS -->
    <script src="js/default/bootstrap.min.js" type="text/javascript"></script>

    <!-- AdminLTE App -->
    <script src="js/default/app.js" type="text/javascript"></script>

    <!-- Easy-Wi -->
    <script src="js/default/easy-wi.js" type="text/javascript"></script>

    <?php echo implode('',$htmlExtraInformation['js']);?>

</head>

<body class="skin-<?php echo $rSA['templateColor'];?>" <?php echo implode(' ',$htmlExtraInformation['body']);?>>

<div class="wrapper">

    <!-- header logo: style can be found in header.less -->
    <header class="main-header">
    <a href="<?php echo $rSA['header_href'];?>" class="logo" target="_blank">
        <img src="images/<?php echo $rSA['header_icon'];?>" title="<?php echo $rSA['header_text'];?>" width="32">
        <?php echo $rSA['header_text'];?>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">

        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php if($statsArray['ticketsTotal']>0){ ?>
                <li class="dropdown messages-menu hidden-xs">
                    <a href="userpanel.php?w=ti">
                        <i class="fa fa-life-ring"></i>
                        <span class="label label-success"><?php echo $statsArray['ticketsTotal'];?></span>
                    </a>
                </li>
                <?php } ?>

                <?php if($statsArray['warningTotal']>0){ ?>
                <li class="dropdown notifications-menu hidden-xs">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-warning"></i>
                        <span class="label label-danger"><?php echo $statsArray['warningTotal'];?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <?php if($statsArray['gameserverNotRunning']>0){ ?><li><a href="userpanel.php?w=gs&amp;d=md"><i class="fa fa-warning danger"></i> <?php echo $statsArray['gameserverNotRunning'].' '.$sprache_bad->gserver_crashed;?></a></li><?php }?>
                                <?php if($statsArray['gameserverNoPassword']>0){ ?><li><a href="userpanel.php?w=gs&amp;d=md"><i class="fa fa-warning warning"></i> <?php echo $statsArray['gameserverNoPassword'].' '.$sprache_bad->gserver_removed;?></a></li><?php }?>
                                <?php if($statsArray['gameserverNoTag']>0){ ?><li><a href="userpanel.php?w=gs&amp;d=md"><i class="fa fa-warning warning"></i> <?php echo $statsArray['gameserverNoTag'].' '.$sprache_bad->gserver_tag_removed;?></a></li><?php }?>
                                <?php if($statsArray['voiceserverCrashed']>0){ ?><li><a href="userpanel.php?w=vo&amp;d=md"><i class="fa fa-warning danger"></i> <?php echo $statsArray['voiceserverCrashed'].' '.$sprache_bad->voice_crashed;?></a></li><?php }?>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php } ?>

                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="images/Places-user-identity-icon.png" class="user-image" alt="User Image">
                        <span><?php echo $great_user;?> <i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header hidden-xs">
                            <img src="images/Places-user-identity-icon.png" class="img-circle" alt="User Image">
                            <p>
                                <?php echo $great_user;?> <br/>
                            </p>
                        </li>

                        <!-- Main Area -->
                        <!-- Menu Body -->
                        <li class="hidden-xs"><a href="#"><?php echo $gsprache->last.'<br />'.$great_last;?></a></li>
                        <li class="divider hidden-xs"></li>
                        <?php if ($support_phonenumber!="") echo '<li><a href="#"><i class="fa fa-phone fa-fw"></i> '.$gsprache->hotline.": ".$support_phonenumber.'</a></li><li class="divider"></li>';?>
                        <li><a href="admin.php?w=su&amp;d=pw"><i class="fa fa-key fa-fw"></i> <?php echo $gsprache->password." ".$gsprache->change;?></a></li>
                        <li><a href="admin.php?w=su"><i class="fa fa-cog fa-fw"></i> <?php echo $gsprache->settings;?></a></li>
                        <li class="divider  hidden-xs"></li>
                        <li><a href="https://easy-wi.com" target="_blank"><i class="fa fa-info-circle fa-fw"></i> About</a></li>
                        <li><a href="https://discord.gg/quJvvfF" target="_blank"><i class="fa fa-question-circle fa-fw"></i> Easy-WI @ Discord</a></li>
                        <li><a href="https://gitter.im/easy-wi/Lobby?utm_source=share-link&utm_medium=link&utm_campaign=share-link" target="_blank"><i class="fa fa-github fa-fw"></i> Easy-WI @ Gitter</a></li>
                        <li><a href="https://github.com/easy-wi/developer" target="_blank"><i class="fa fa-github fa-fw"></i> Easy-WI @ Github</a></li>
                        <li><a href="https://www.facebook.com/easywi" target="_blank"><i class="fa fa-facebook-square fa-fw"></i> Easy-WI @ Facebook</a></li>
                        <li><a href="https://twitter.com/EasyWI" target="_blank"><i class="fa fa-twitter fa-fw"></i> Easy-WI @ Twitter</a></li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="userpanel.php?w=se" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="login.php?w=lo" class="btn btn-default btn-danger" style="color:white;"><i class="fa fa-power-off"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>

        </div>

        <div class="navbar-right">
            <div class="navbar-text">
                <?php foreach ($languages as $language){ echo '<a href="userpanel.php?l='.$language.'"><img src="images/flags/'.$language.'.png" alt="Flag: '.$language.'.png."></a> ';} ?>
            </div>
        </div>
    </nav>
    </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar sidebar-offcanvas">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu" data-widget="tree">

                    <li class="treeview <?php if(in_array($w,array('da','se','lo','ip','ho','su'))) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-home fa-fw"></i>
                            <span>Home</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if($w=='da' or $w=='ho') echo 'class="active"';?>><a href="userpanel.php?w=da"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                            <?php if(!isset($_SESSION['sID'])){ ?><li <?php if($w=='su') echo 'class="active"';?>><a href="userpanel.php?w=su"><i class="fa fa-users"></i> <?php echo $gsprache->substitutes;?></a></li><?php }?>
                            <li <?php if($w=='lo') echo 'class="active"';?>><a href="userpanel.php?w=lo"><i class="fa fa-list-alt"></i> <?php echo $gsprache->logs;?></a></li>
                            <?php if($easywiModules['ip']){ ?><li <?php if($w=='ip') echo 'class="active"';?>><a href="userpanel.php?w=ip"><i class="fa fa-gavel"></i> <?php echo $gsprache->imprint;?></a></li><?php }?>
                            <?php foreach ($customModules['us'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="userpanel.php?w='.$k.'"><i class="fa fa-angle-double-right"></i> '.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>

                    <?php if($easywiModules['ti'] and $pa['usertickets']) { ?>
                    <li class="treeview <?php if($w=='ti') echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-life-ring"></i>
                            <span><?php echo $gsprache->support;?></span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if($w=='ti' and $d!='ad') echo 'class="active"';?>><a href="userpanel.php?w=ti"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li>
                            <li <?php if($w=='ti' and $d=='ad') echo 'class="active"';?>><a href="userpanel.php?w=ti&amp;d=ad"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->support2;?></a></li>
                            <?php foreach ($customModules['ti'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><i class="fa fa-angle-double-right"></i> <a href="userpanel.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['gs'] and $gscount>0 and $pa['restart']) { ?>
                    <li class="treeview <?php if(in_array($w,array('gs','gt','fd','ao','ca','bu','ms','pr'))) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-gamepad"></i> <span><?php echo $gsprache->gameserver;?></span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($pa['restart']) { ?>
                            <li <?php if(in_array($w,array('gs','ao','ca','bu','pr'))) echo 'class="active"';?>><a href="userpanel.php?w=gs"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li>
                            <?php } ?>
                            <?php if($pa['fastdl']) { ?>
                            <li <?php if($w=='fd') echo 'class="active"';?>><a href="userpanel.php?w=fd"><i class="fa fa-cloud-upload"></i> <?php echo $gsprache->fastdownload;?></a></li>
                            <?php } ?>
                            <?php if($pa['restart']) { ?>
                            <li <?php if($w=='ms') echo 'class="active"';?>><a href="userpanel.php?w=ms"><i class="fa fa-truck"></i> <?php echo $gsprache->migration;?></a></li>
                            <li <?php if($w=='gt') echo 'class="active"';?>><a href="userpanel.php?w=gt"><i class="fa fa-floppy-o"></i> <?php echo $gsprache->file.' '.$gsprache->template;?></a></li>
                            <?php } ?>
                            <?php foreach ($customModules['gs'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><i class="fa fa-angle-double-right"></i> <a href="userpanel.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['vo'] and ($voicecount>0 or $tsdnscount>0) and $pa['voiceserver']) { ?>
                    <li class="treeview <?php if(in_array($w,array('vo','vu','vd'))) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-microphone"></i> <span><?php echo $gsprache->voiceserver;?></span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($voicecount>0) { ?><li <?php if($w=='vo') echo 'class="active"';?>><a href="userpanel.php?w=vo"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li><?php } ?>
                            <?php if($voicecount>0) { ?><li <?php if($w=='vu') echo 'class="active"';?>><a href="userpanel.php?w=vu"><i class="fa fa-area-chart"></i> <?php echo $gsprache->stats;?></a></li><?php } ?>
                            <?php if($tsdnscount>0) { ?><li <?php if($w=='vd') echo 'class="active"';?>><a href="userpanel.php?w=vd"><i class="fa fa-link"></i> TS3 DNS</a></li><?php } ?>
                            <?php foreach ($customModules['vo'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><i class="fa fa-angle-double-right"></i> <a href="userpanel.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['ws'] and $vhostcount>0 and $pa['webvhost']) { ?>
                    <li class="treeview <?php if(in_array($ui->smallletters('w',255,'get'),array('wv')) or isset($customModules['ws'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-cubes"></i> <span><?php echo $gsprache->webspace;?></span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if($ui->smallletters('w',255,'get')=='wv') echo 'class="active"';?>><a href="userpanel.php?w=wv"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li>
                            <?php foreach ($customModules['ws'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><i class="fa fa-angle-double-right"></i> <a href="userpanel.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['my'] and $dbcount>0 and ($pa['mysql'] or $pa['mysql'])) { ?>
                    <li class="treeview <?php if($w=='my') echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-database"></i> <span>MySQL</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if($w=='my') echo 'class="active"';?>><a href="userpanel.php?w=my"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li>
                            <?php foreach ($customModules['my'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><i class="fa fa-angle-double-right"></i> <a href="userpanel.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['ro'] and ($virtualcount+$rootcount)>0 and $pa['roots']) { ?>
                    <li class="treeview <?php if(in_array($w,array('de','vm'))) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-laptop"></i> <span>Rootserver</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($rootcount>0) { ?><li <?php if($w=='de') echo 'class="active"';?>><a href="userpanel.php?w=de"><i class="fa fa-angle-double-right"></i> <?php echo $gsprache->dedicated;?></a></li><?php } ?>
                            <?php if($virtualcount>0) { ?><li <?php if($w=='vm') echo 'class="active"';?>><a href="userpanel.php?w=vm"><i class="fa fa-angle-double-right"></i> <?php echo $gsprache->virtual;?></a></li><?php } ?>
                            <?php foreach ($customModules['ro'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><i class="fa fa-angle-double-right"></i> <a href="userpanel.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if(count($customModules['mo'])>0) { ?>
                    <li class="treeview <?php if(isset($customModules['mo'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-tasks"></i> <span><?php echo $gsprache->modules;?></span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php foreach ($customModules['mo'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="userpanel.php?w='.$k.'"><i class="fa fa-tasks"></i> '.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="content-wrapper">
