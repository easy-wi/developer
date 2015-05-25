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

    <!-- Bootstrap CSS -->
    <link href="css/default/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="css/default/font-awesome.min.css" rel="stylesheet">

    <!-- Theme style -->
    <link href="css/default/AdminLTE.css" rel="stylesheet" type="text/css" />
    <link href="css/black/skin-black.css" rel="stylesheet" type="text/css" />

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

<body class="skin-black" <?php echo implode(' ',$htmlExtraInformation['body']);?>>

<div class="wrapper">

    <!-- header logo: style can be found in header.less -->
    <header class="main-header">

        <a href="https://easy-wi.com" class="logo" target="_blank">
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

                <div class="nav navbar-btn pull-right" style="padding-right: 10px;">
                    <a href="login.php?w=lo"><span class="btn btn-sm btn-danger"><i class="fa fa-power-off"></i> Logout</span></a>
                </div>

                <ul class="nav navbar-nav">

                    <?php if($statsArray['ticketsTotal']>0){ ?>
                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu hidden-xs">
                        <a href="admin.php?w=ti" class="dropdown-toggle">
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
                                    <?php if($pa['gserver'] and $easywiModules['gs']) { ?>
                                    <?php if($statsArray['gameserverNotRunning']>0){ ?><li><a href="admin.php?w=gs"><i class="fa fa-warning danger"></i> <?php echo $statsArray['gameserverNotRunning'].' '.$sprache_bad->gserver_crashed;?></a></li><?php }?>
                                    <?php if($statsArray['gameserverNoPassword']>0){ ?><li><a href="admin.php?w=gs"><i class="fa fa-warning warning"></i> <?php echo $statsArray['gameserverNoPassword'].' '.$sprache_bad->gserver_removed;?></a></li><?php }?>
                                    <?php if($statsArray['gameserverNoTag']>0){ ?><li><a href="admin.php?w=gs"><i class="fa fa-warning warning"></i> <?php echo $statsArray['gameserverNoTag'].' '.$sprache_bad->gserver_tag_removed;?></a></li><?php }?>
                                    <?php }?>

                                    <?php if($pa['voiceserver'] and $statsArray['voiceserverCrashed']>0 and $easywiModules['vo']) { ?><li><a href="admin.php?w=vo"><i class="fa fa-warning danger"></i> <?php echo $statsArray['voiceserverCrashed'].' '.$sprache_bad->voice_crashed;?></a></li><?php }?>
                                    <?php if($pa['voicemasterserver'] and $statsArray['voiceMasterCrashed']>0 and $easywiModules['vo']) { ?><li><a href="admin.php?w=vo"><i class="fa fa-warning danger"></i><?php echo $statsArray['voiceMasterCrashed'].' '.$sprache_bad->ts3master_crashed;?></a></li><?php }?>
                                    <?php if($pa['roots'] and $statsArray['gameMasterCrashed']>0 and $easywiModules['gs']) { ?><li><a href="admin.php?w=ro"><i class="fa fa-warning danger"></i><?php echo $statsArray['gameMasterCrashed'].' '.$sprache_bad->ts3master_crashed;?></a></li><?php }?>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                            <span><?php echo $great_user;?> <i class="caret"></i></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><?php echo $gsprache->last.'<br />'.$great_last;?></a></li>
                            <li class="divider"></li>
                            <?php if ($support_phonenumber!="") echo '<li><a href="#"><i class="fa fa-phone fa-fw"></i> '.$gsprache->hotline.": ".$support_phonenumber.'</a></li><li class="divider"></li>';?>
                            <li><a href="admin.php?w=su&amp;d=pw"><i class="fa fa-key fa-fw"></i> <?php echo $gsprache->password." ".$gsprache->change;?></a></li>
                            <li><a href="admin.php?w=su"><i class="fa fa-cog fa-fw"></i> <?php echo $gsprache->settings;?></a></li>
                            <li class="divider"></li>
                            <li><a href="https://easy-wi.com" target="_blank"><i class="fa fa-info-circle fa-fw"></i> About</a></li>
                            <li><a href="https://easy-wi.com/forum/" target="_blank"><i class="fa fa-comments fa-fw"></i> Forum</a></li>
                            <li><a href="https://easy-wi.com/de/handbuch/" target="_blank"><i class="fa fa-question-circle fa-fw"></i> Wiki</a></li>
                            <li><a href="https://www.facebook.com/easywi" target="_blank"><i class="fa fa-facebook-square fa-fw"></i> Easy-WI @ Facebook</a></li>
                            <li><a href="https://twitter.com/EasyWI" target="_blank"><i class="fa fa-twitter fa-fw"></i> Easy-WI @ Twitter</a></li>
                            <li><a href="https://github.com/easy-wi/developer" target="_blank"><i class="fa fa-github fa-fw"></i> Easy-WI @ Github</a></li>
                            <li><a href="https://github.com/ValveSoftware/steam-for-linux/issues" target="_blank"><i class="fa fa-bug fa-fw"></i> Steam Bugtracker</a></li>
                        </ul>
                    </li>
                </ul>

            </div>
            <div class="navbar-right">
                <div class="navbar-text">
                    <?php foreach ($languages as $language){ echo '<a href="admin.php?l='.$language.'"><img src="images/flags/'.$language.'.png" alt="Flag: '.$language.'.png."></a> ';} ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="wrapper row-offcanvas row-offcanvas-left">
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar sidebar-offcanvas">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- search form -->
                <form action="admin.php" method="get" class="sidebar-form">
                    <input type="hidden" name="w" value="sr">
                    <?php if($pa['gserver']){ ?><input type="hidden" name="type[]" value="gs"><?php }?>
                    <?php if($pa['gimages']){ ?><input type="hidden" name="type[]" value="im"><?php }?>
                    <?php if($pa['addons']){ ?><input type="hidden" name="type[]" value="ad"><?php }?>
                    <?php if($pa['voiceserver']){ ?><input type="hidden" name="type[]" value="vo"><?php }?>
                    <?php if($pa['addvserver'] or $pa['modvserver'] or $pa['delvserver'] or $pa['usevserver']){ ?><input type="hidden" name="type[]" value="vs"><?php }?>
                    <?php if($pa['roots']){ ?><input type="hidden" name="type[]" value="ro"><?php }?>
                    <?php if($pa['user'] or $pa['user_users']){ ?><input type="hidden" name="type[]" value="us"><?php }?>
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search...">
                        <span class="input-group-btn">
                            <button type='submit' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>

                <ul class="sidebar-menu">

                    <li class="treeview <?php if(in_array($w,array('da','ho','ib','ip','lo','ml','sc')) or isset($customModules['ip'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-home fa-fw"></i>
                            <span>Home</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if($w=='da' or $w=='ho') echo 'class="active"';?>><a href="admin.php?w=da"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                            <?php if($pa['ipBans']) { ?><li <?php if($ui->smallletters('w',255,'get')=='ib') echo 'class="active"';?>><a href="admin.php?w=ib"><i class="fa fa-ban"></i> IP Bans</a></li><?php } ?>
                            <?php if($pa['log']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='lo') echo 'class="active"';?>><a href="admin.php?w=lo"><i class="fa fa-list-alt"></i> <?php echo $gsprache->logs;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='ml') echo 'class="active"';?>><a href="admin.php?w=ml"><i class="fa fa-envelope"></i> Mail <?php echo $gsprache->logs;?></a></li>
                            <?php } ?>
                            <?php if($easywiModules['ip']) { ?><li <?php if($ui->smallletters('w',255,'get')=='ip') echo 'class="active"';?>><a href="admin.php?w=ip"><i class="fa fa-legal"></i> <?php echo $gsprache->imprint;?></a></li><?php }?>
                            <?php if($pa['settings'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='sc') echo 'class="active"';?>><a href="admin.php?w=sc"><i class="fa fa-heartbeat"></i> <?php echo $gsprache->system_check;?></a></li><?php } ?>
                        </ul>
                    </li>

                    <?php if($pa['settings']) { ?>
                    <li class="treeview  <?php if(in_array($w,array('se','sm','si','vc','cc','mo','bu'))) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-wrench"></i>
                            <span><?php echo $gsprache->settings;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if($ui->smallletters('w',255,'get')=='se') echo 'class="active"';?>><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='sm') echo 'class="active"';?>><a href="admin.php?w=sm"><i class="fa fa-envelope"></i> E-Mail <?php echo $gsprache->settings;?></a></li>
                            <?php if($easywiModules['ip']) { ?><li <?php if($ui->smallletters('w',255,'get')=='si') echo 'class="active"';?>><a href="admin.php?w=si"><i class="fa fa-legal"></i> <?php echo $gsprache->imprint.' '.$gsprache->settings;?></a></li><?php }?>
                            <?php if($pa['root'] and $reseller_id==0) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='vc') echo 'class="active"';?>><a href="admin.php?w=vc"><i class="fa fa-check"></i> <?php echo $gsprache->versioncheck;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='cc') echo 'class="active"';?>><a href="admin.php?w=cc"><i class="fa fa-list"></i> <?php echo $gsprache->columns;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='mo') echo 'class="active"';?>><a href="admin.php?w=mo"><i class="fa fa-th-large"></i> <?php echo $gsprache->modules;?></a></li>
                            <?php } ?>
                            <?php if($pa['root']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='bu') echo 'class="active"';?>><a href="admin.php?w=bu"><i class="fa fa-database"></i> <?php echo $gsprache->databases;?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($pa['jobs'] or $pa['apiSettings']) { ?>
                    <li class="treeview <?php if(in_array($w,array('jb','ap','aa','ui'))) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-cloud fa-fw"></i>
                            <span><?php echo $gsprache->jobs.'/'.$gsprache->api;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                        <?php if($pa['jobs']) { ?><li <?php if($ui->smallletters('w',255,'get')=='jb') echo 'class="active"';?>><a href="admin.php?w=jb"><i class="fa fa-tasks"></i> <?php echo $gsprache->jobs.' '.$gsprache->overview;?></a></li><?php } ?>
                        <?php if($pa['apiSettings']) { ?>
                        <li <?php if($ui->smallletters('w',255,'get')=='ap') echo 'class="active"';?>><a href="admin.php?w=ap"><i class="fa fa-wrench"></i> <?php echo $gsprache->api.' '.$gsprache->settings;?></a></li>
                        <li <?php if($ui->smallletters('w',255,'get')=='aa') echo 'class="active"';?>><a href="admin.php?w=aa"><i class="fa fa-cloud-download"></i> <?php echo $gsprache->apiAuth;?></a></li>
                        <li <?php if($ui->smallletters('w',255,'get')=='ui') echo 'class="active"';?>><a href="admin.php?w=ui"><i class="fa fa-download"></i> <?php echo $gsprache->userImport;?></a></li>
                        <?php }?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($pa['feeds']) { ?>
                    <li class="treeview <?php if(in_array($w,array('fn','fe'))) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-rss fa-fw"></i>
                            <span><?php echo $gsprache->feeds;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if($ui->smallletters('w',255,'get')=='fn') echo 'class="active"';?>><a href="admin.php?w=fn"><i class="fa fa-rss"></i> <?php echo $gsprache->news;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='fe' and !in_array($d,array('ad','se'))) echo 'class="active"';?>><a href="admin.php?w=fe"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='fe' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=fe&amp;d=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['pn'] and $reseller_id==0 and ($pa['cms_settings'] or $pa['cms_pages'] or $pa['cms_news'])) { ?>
                    <li class="treeview <?php if(in_array($w,array('pn','pc','pp','pd','ps')) or isset($customModules['pa'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-globe fa-fw"></i>
                            <span>CMS</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($pa['cms_news']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='pn') echo 'class="active"';?>><a href="admin.php?w=pn"><i class="fa fa-newspaper-o"></i> <?php echo $gsprache->news;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='pc') echo 'class="active"';?>><a href="admin.php?w=pc"><i class="fa fa-comments"></i> <?php echo $gsprache->comments;?></a></li>
                            <?php } ?>
                            <?php if($pa['cms_pages']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='pp') echo 'class="active"';?>><a href="admin.php?w=pp"><i class="fa fa-copy"></i> <?php echo $gsprache->pages;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='pd') echo 'class="active"';?>><a href="admin.php?w=pd"><i class="fa fa-download"></i> <?php echo $gsprache->downloads;?></a></li>
                            <?php } ?>
                            <?php if($pa['cms_settings']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='ps') echo 'class="active"';?>><a href="admin.php?w=ps"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
                            <?php } ?>
                            <?php foreach ($customModules['pa'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['ws'] and ($pa['webvhost'] or $pa['webmaster'])) { ?>
                    <li class="treeview <?php if(in_array($w,array('wv','wm')) or isset($customModules['ws'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-cubes fa-fw"></i>
                            <span><?php echo $gsprache->webspace;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if($ui->smallletters('w',255,'get')=='wv') echo 'class="active"';?>><a href="admin.php?w=wv"><i class="fa fa-columns"></i> Vhosts <?php echo $gsprache->overview;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='wm') echo 'class="active"';?>><a href="admin.php?w=wm"><i class="fa fa-server"></i> <?php echo $gsprache->master;?></a></li>
                            <?php foreach ($customModules['ws'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['my'] and ($pa['mysql_settings'] or $pa['mysql'])) { ?>
                    <li class="treeview <?php if(in_array($w,array('my','md')) or isset($customModules['my'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-database fa-fw"></i>
                            <span>MySQL</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($pa['mysql']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='md') echo 'class="active"';?>><a href="admin.php?w=md"><i class="fa fa-columns"></i> <?php echo $gsprache->databases.' '.$gsprache->overview;?></a></li>
                            <?php } ?>
                            <?php if($pa['mysql_settings']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='my') echo 'class="active"';?>><a href="admin.php?w=my"><i class="fa fa-server"></i> <?php echo $gsprache->master;?></a></li>
                            <?php } ?>
                            <?php foreach ($customModules['my'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($pa['user'] or $pa['user_users'] or $pa['userGroups'] ) { ?>
                    <li class="treeview <?php if(in_array($w,array('us','ug','up')) or isset($customModules['us'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-users fa-fw"></i>
                            <span><?php echo $gsprache->user;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($pa['user'] or $pa['user_users']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='us') echo 'class="active"';?>><a href="admin.php?w=us"><i class="fa fa-columns"></i> <?php echo $gsprache->user.' '.$gsprache->overview;?></a></li>
                            <?php } ?>
                            <?php if($pa['userGroups']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='ug') echo 'class="active"';?>><a href="admin.php?w=ug"><i class="fa fa-group"></i> <?php echo $gsprache->groups;?></a></li>
                            <?php } ?>
                            <?php if($pa['root'] and $reseller_id==0) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='up') echo 'class="active"';?>><a href="admin.php?w=up"><i class="fa fa-cloud"></i> Social Auth Provider</a></li>
                            <?php } ?>
                            <?php foreach ($customModules['us'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['gs'] and ($pa['gserver'] or $pa['addons'] or $pa['gimages'] or $pa['eac'] or $pa['masterServer']) and $easywiModules['gs']) { ?>
                    <li class="treeview <?php if(in_array($w,array('gs','im','ad','gt','ea')) or isset($customModules['gs'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-gamepad fa-fw"></i>
                            <span><?php echo $gsprache->gameserver;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($pa['gserver']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='gs') echo 'class="active"';?>><a href="admin.php?w=gs"><i class="fa fa-columns"></i><?php echo $gsprache->overview;?></a></li>
                            <?php } ?>
                            <?php if($pa['gimages']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='im') echo 'class="active"';?>><a href="admin.php?w=im"><i class="fa fa-file-text-o"></i><?php echo $gsprache->gameserver.' '.$gsprache->template;?></a></li>
                            <?php } ?>
                            <?php if($pa['addons']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='ad') echo 'class="active"';?>><a href="admin.php?w=ad"><i class="fa fa-gears"></i><?php echo $gsprache->addon;?></a></li>
                            <?php } ?>
                            <?php if($pa['gserver']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='gt') echo 'class="active"';?>><a href="admin.php?w=gt"><i class="fa fa-floppy-o"></i><?php echo $gsprache->file.' '.$gsprache->template;?></a></li>
                            <?php } ?>
                            <?php if($easywiModules['ea'] and $pa['eac']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='ea') echo 'class="active"';?>><a href="admin.php?w=ea"><i class="fa fa-eye"></i>Easy Anti Cheat</a></li>
                            <?php } ?>
                            <?php foreach ($customModules['gs'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($pa['roots'] or $pa['masterServer']) { ?>
                    <li class="treeview <?php if(in_array($w,array('ro','ma'))) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-hdd-o fa-fw"></i>
                            <span><?php echo $gsprache->appRoot;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($pa['roots']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='ro') echo 'class="active"';?>><a href="admin.php?w=ro"><i class="fa fa-columns"></i><?php echo $gsprache->overview;?></a></li>
                            <?php } ?>
                            <?php if($pa['masterServer']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='ma' and $d!='ud') echo 'class="active"';?>><a href="admin.php?w=ma&amp;d=md"><i class="fa fa-puzzle-piece"></i> <?php echo $gsprache->master_apps;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='ma' and $d=='ud') echo 'class="active"';?>><a href="admin.php?w=ma&amp;d=ud"><i class="fa fa-spinner"></i> <?php echo $gsprache->master_apps.' '.$gsprache->update;?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['vo'] and ($pa['voicemasterserver'] or $pa['voiceserver'] or $pa['voiceserverStats'] or $pa['voiceserverSettings'])) { ?>
                    <li class="treeview <?php if(in_array($w,array('vo','vm','vr','vd','vu')) or isset($customModules['vo'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-microphone fa-fw"></i>
                            <span><?php echo $gsprache->voiceserver;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($pa['voiceserver']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='vo') echo 'class="active"';?>><a href="admin.php?w=vo"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li>
                            <?php } ?>
                            <?php if($pa['voicemasterserver']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='vm') echo 'class="active"';?>><a href="admin.php?w=vm"><i class="fa fa-server"></i> <?php echo $gsprache->master;?></a></li>
                            <?php } ?>
                            <?php if($pa['voiceserver']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='vr') echo 'class="active"';?>><a href="admin.php?w=vr"><i class="fa fa-columns"></i> TSDNS <?php echo $gsprache->overview;?></a></li>
                            <?php } ?>
                            <?php if($pa['voicemasterserver']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='vd') echo 'class="active"';?>><a href="admin.php?w=vd"><i class="fa fa-server"></i> TSDNS <?php echo $gsprache->master;?></a></li>
                            <?php } ?>
                            <?php if($pa['voiceserverStats']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='vu') echo 'class="active"';?>><a href="admin.php?w=vu"><i class="fa fa-area-chart"></i> <?php echo $gsprache->stats;?></a></li>
                            <?php } ?>
                            <?php foreach ($customModules['vo'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['le'] and (($easywiModules['gs'] or $easywiModules['vo']) and ($pa['lendserver'] or $pa['lendserverSettings']))) { ?>
                    <li class="treeview <?php if(in_array($w,array('le'))) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-flask fa-fw"></i>
                            <span><?php echo $gsprache->lendserver;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($pa['lendserver']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='le' and $d!='se') echo 'class="active"';?>><a href="admin.php?w=le"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li>
                            <?php } ?>
                            <?php if($pa['lendserverSettings']) { ?>
                            <li <?php if($ui->smallletters('w',255,'get')=='le' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=le&amp;d=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['ti'] and $pa['usertickets'] and $reseller_id!=0) { ?>
                    <li class="treeview <?php if($w=='tr' or isset($customModules['tr'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-life-ring fa-fw"></i>
                            <span><?php echo $gsprache->reseller.' '.$gsprache->support;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if($ui->smallletters('w',255,'get')=='tr' and $ui->smallletters('d',255,'get')!='ad') echo 'class="active"';?>><a href="admin.php?w=tr"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='tr' and $ui->smallletters('d',255,'get')=='ad') echo 'class="active"';?>><a href="admin.php?w=tr&amp;d=ad"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->support2;?></a></li>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['ti'] and $pa['tickets']) { ?>
                    <li class="treeview <?php if($w=='ti' or isset($customModules['ti'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-life-ring fa-fw"></i>
                            <span><?php echo $gsprache->support;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li <?php if($ui->smallletters('w',255,'get')=='ti' and !in_array($d,array('at','mt','dt'))) echo 'class="active"';?>><a href="admin.php?w=ti"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li>
                            <li <?php if($ui->smallletters('w',255,'get')=='ti' and in_array($d,array('at','mt','dt'))) echo 'class="active"';?>><a href="admin.php?w=ti&amp;d=mt"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
                            <?php foreach ($customModules['ti'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['ro'] and (($pa['modvserver'] or $pa['delvserver'] or $pa['usevserver'] or $pa['dedicatedServer'] or ($pa['vserverhost'] and $reseller_id==0)) or ($pa['resellertemplates'] and $reseller_id==0))) { ?>
                    <li class="treeview <?php if(in_array($w,array('vs','rh','vh','ot')) or isset($customModules['ro'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-laptop fa-fw"></i>
                            <span>Rootserver</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($pa['modvserver'] or $pa['delvserver'] or $pa['usevserver']) { ?><li <?php if($ui->smallletters('w',255,'get')=='vs') echo 'class="active"';?>><a href="admin.php?w=vs&amp;d=md"><i class="fa fa-cloud"></i> <?php echo $gsprache->virtual;?></a></li><?php } ?>
                            <?php if($pa['dedicatedServer']) { ?><li <?php if($ui->smallletters('w',255,'get')=='rh') echo 'class="active"';?>><a href="admin.php?w=rh"><i class="fa fa-laptop"></i> <?php echo $gsprache->dedicated;?></a></li><?php } ?>
                            <?php if($pa['vserverhost'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='vh') echo 'class="active"';?>><a href="admin.php?w=vh"><i class="fa fa-server"></i> ESX(I) Host</a></li><?php } ?>
                            <?php if($pa['resellertemplates'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='ot') echo 'class="active"';?>><a href="admin.php?w=ot"><i class="fa fa-file-text-o"></i> <?php echo $gsprache->template;?></a></li><?php } ?>
                            <?php foreach ($customModules['ro'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if($easywiModules['ro'] and ($pa['traffic'] or ($reseller_id=='0' and ($pa['trafficsettings'] or $pa['dhcpServer'] or $pa['pxeServer'] or $pa['root'])))) { ?>
                    <li class="treeview <?php if(in_array($w,array('tf','rd','rp','sn'))) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-sitemap fa-fw"></i>
                            <span><?php echo $gsprache->network;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if($pa['traffic']) { ?><li <?php if($ui->smallletters('w',255,'get')=='tf' and $d!='se') echo 'class="active"';?>><a href="admin.php?w=tf"><i class="fa fa-area-chart"></i> <?php echo $gsprache->traffic;?></a></li><?php } ?>
                            <?php if($reseller_id=='0' and $pa['trafficsettings']) { ?><li <?php if($ui->smallletters('w',255,'get')=='tf' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=tf&amp;d=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->traffic.' '.$gsprache->settings;?></a></li><?php } ?>
                            <?php if($pa['dhcpServer'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='rd') echo 'class="active"';?>><a href="admin.php?w=rd"><i class="fa fa-tty"></i> DHCP</a></li><?php } ?>
                            <?php if($pa['pxeServer'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='rp') echo 'class="active"';?>><a href="admin.php?w=rp"><i class="fa fa-folder-open-o"></i> PXE</a></li><?php } ?>
                            <?php if($pa['root'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='sn') echo 'class="active"';?>><a href="admin.php?w=sn"><i class="fa fa-sitemap"></i> <?php echo $gsprache->subnets;?></a></li><?php } ?>
                        </ul>
                    </li>
                    <?php } ?>

                    <?php if(count($customModules['mo'])>0) { ?>
                    <li class="treeview <?php if(isset($customModules['mo'][$ui->smallletters('w',255,'get')])) echo 'active';?>">
                        <a href="#">
                            <i class="fa fa-tasks fa-fw"></i>
                            <span><?php echo $gsprache->modules;?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php foreach ($customModules['mo'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                        </ul>
                    </li>
                    <?php } ?>

                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="content-wrapper">

            <?php if(isset($header)){ ?><div class="row"><div class="col-md-12"><div class="callout callout-warning"><p><?php echo $text;?></p></div></div></div><?php } ?>
            <?php if(isset($toooldversion)){ ?><div class="row"><div class="col-md-12"><div class="callout callout-warning"><p><?php echo $toooldversion;?></p></div></div></div><?php } ?>
            <?php if($rSA['lastCronWarnStatus']=='Y' and (time()-$rSA['lastCronStatus'])>600 and $reseller_id==0){ ?><div class="row"><div class="col-md-12"><div class="callout callout-danger"><p>Cronjob: statuscheck.php</div></div></div><?php }?>
            <?php if($rSA['lastCronWarnReboot']=='Y' and (time()-$rSA['lastCronReboot'])>5400 and $reseller_id==0){ ?><div class="row"><div class="col-md-12"><div class="callout callout-danger"><p>Cronjob: reboot.php</div></div></div><?php }?>
            <?php if($rSA['lastCronWarnUpdates']=='Y' and (time()-$rSA['lastCronUpdates'])>300 and $reseller_id==0){ ?><div class="row"><div class="col-md-12"><div class="callout callout-danger"><p>Cronjob: startupdates.php</div></div></div><?php }?>
            <?php if($rSA['lastCronWarnJobs']=='Y' and (time()-$rSA['lastCronJobs'])>300 and $reseller_id==0){ ?><div class="row"><div class="col-md-12"><div class="callout callout-danger"><p>Cronjob: jobs.php</div></div></div><?php }?>
            <?php if($rSA['lastCronWarnCloud']=='Y' and (time()-$rSA['lastCronCloud'])>1200 and $reseller_id==0){ ?><div class="row"><div class="col-md-12"><div class="callout callout-danger"><p>Cronjob: cloud.php</div></div></div><?php }?>