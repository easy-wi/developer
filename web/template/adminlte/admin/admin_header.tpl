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

    <!-- bootstrap 3.0.2 -->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- font Awesome -->
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" rel="stylesheet">

    <!-- Ionicons -->
    <link href="//cdn.jsdelivr.net/ionicons/1.4.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />

    <!-- Theme style -->
    <link href="css/adminlte/AdminLTE.css" rel="stylesheet" type="text/css" />

    <?php echo implode('',$htmlExtraInformation['css']);?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

</head>

<body class="skin-blue" <?php echo implode(' ',$htmlExtraInformation['body']);?>>
<!-- header logo: style can be found in header.less -->
<header class="header">
<a href="https://easy-wi.com" class="logo" target="_blank">
    <!-- Add the class icon to your logo image or logo icon to add the margining -->
    <img src="images/logo_180px.png" title="Easy-Wi" width="32">
    Easy-Wi
</a>

<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top" role="navigation">

<!-- Sidebar toggle button-->
<a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
    <span class="sr-only">Toggle navigation</span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
</a>

    <div class="navbar-right">

        <ul class="nav navbar-nav">

            <?php if($statsArray['ticketsTotal']>0){ ?>
            <!-- Messages: style can be found in dropdown.less-->
            <li class="dropdown messages-menu">
                <a href="userpanel.php?w=ti" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-life-ring"></i>
                    <span class="label label-success"><?php echo $statsArray['ticketsTotal'];?></span>
                </a>
            </li>
            <?php } ?>

            <?php if($statsArray['warningTotal']>0){ ?>
            <!-- Notifications: style can be found in dropdown.less -->
            <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-warning"></i>
                    <span class="label label-danger"><?php echo $statsArray['warningTotal'];?></span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <!-- inner menu: contains the actual data -->
                        <ul class="menu">
							<?php if(($pa['tickets'] or $pa['usertickets']) and $statsArray['ticketsInProcess']>0) { ?><a href="admin.php?w=ti"><span class="badge badge-info"><?php echo $statsArray['ticketsNew'].'/'.$statsArray['ticketsInProcess'].' '.$sprache_bad->tickets; ?></span></a><?php }?>
							<?php if($pa['gserver'] and $easywiModules['gs']) { ?>
                            <?php if($statsArray['gameserverNotRunning']>0){ ?><li><a href="admin.php?w=gs&amp;d=md"><i class="fa fa-warning danger"></i> <?php echo $statsArray['gameserverNotRunning'].' '.$sprache_bad->gserver_crashed;?></a></li><?php }?>
                            <?php if($statsArray['gameserverNoPassword']>0){ ?><li><a href="admin.php?w=gs&amp;d=md"><i class="fa fa-warning warning"></i> <?php echo $statsArray['gameserverNoPassword'].' '.$sprache_bad->gserver_removed;?></a></li><?php }?>
                            <?php if($statsArray['gameserverNoTag']>0){ ?><li><a href="admin.php?w=gs&amp;d=md"><i class="fa fa-warning warning"></i> <?php echo $statsArray['gameserverNoTag'].' '.$sprache_bad->gserver_tag_removed;?></a></li><?php }?>
                            <?php }?>
							<?php if($pa['voiceserver'] and $statsArray['voiceserverCrashed']>0 and $easywiModules['vo']) { ?><li><a href="admin.php?w=vo&amp;d=md"><i class="fa fa-warning danger"></i> <?php echo $statsArray['voiceserverCrashed'].' '.$sprache_bad->voice_crashed;?></a></li><?php }?>
							<?php if($pa['voicemasterserver'] and $statsArray['voiceMasterCrashed']>0 and $easywiModules['vo']) { ?><li><a href="admin.php?w=vo&amp;d=md"><i class="fa fa-warning danger"></i><?php echo $statsArray['voiceMasterCrashed'].' '.$sprache_bad->ts3master_crashed;?></a></li><?php }?>
							<?php if($pa['roots'] and $statsArray['gameMasterCrashed']>0 and $easywiModules['gs']) { ?><li><a href="admin.php?w=ro"><i class="fa fa-warning danger"></i><?php echo $statsArray['gameMasterCrashed'].' '.$sprache_bad->ts3master_crashed;?></a></li><?php }?>
						</ul>
                    </li>
                </ul>
            </li>
            <?php } ?>

            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-user"></i>
                    <span><?php echo $great_user;?> <i class="caret"></i></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="#"><?php echo $gsprache->last.'<br />'.$great_last;?></a></li>
                    <li class="divider"></li>
                    <?php if ($support_phonenumber!="") echo '<li><a href="#"><i class="fa fa-phone fa-fw"></i> '.$gsprache->hotline.": ".$support_phonenumber.'</a></li><li class="divider"></li>';?>
                    <li><a href="login.php?w=lo"><i class="fa fa-sign-out"></i> Logout</a></li>
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
    <aside class="left-side sidebar-offcanvas">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">

                <li class="treeview <?php if(in_array($w,array('da','ho','ib','lo','ml','ip'))) echo 'active';?>">
                    <a href="#">
                        <i class="fa fa-home fa-fw"></i>
                        <span>Home</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li <?php if($w=='da' or $w=='ho') echo 'class="active"';?>><a href="admin.php?w=da"><i class="fa fa-eye"></i> Dashboard</a></li>
                        <?php if($pa['ipBans']) { ?><li <?php if($ui->smallletters('w',255,'get')=='ib') echo 'class="active"';?>><a href="admin.php?w=ib"><i class="fa fa-ban"></i> IP Bans</a></li><?php } ?>
						<?php if($pa['log']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='lo') echo 'class="active"';?>><a href="admin.php?w=lo"><i class="fa fa-file-text"></i> <?php echo $gsprache->logs;?></a></li>
                        <li <?php if($ui->smallletters('w',255,'get')=='ml') echo 'class="active"';?>><a href="admin.php?w=ml"><i class="fa fa-envelope"></i> Mail <?php echo $gsprache->logs;?></a></li>
                        <?php } ?>
						<?php if($easywiModules['ip']) { ?><li <?php if($ui->smallletters('w',255,'get')=='ip') echo 'class="active"';?>><a href="admin.php?w=ip"><i class="fa fa-angle-double-right"></i> <?php echo $gsprache->imprint;?></a></li><?php }?>
                    </ul>
                </li>

                <?php if($pa['settings']) { ?>
                <li class="treeview <?php if($w=='se') echo 'active';?>">
                    <a href="#">
                        <i class="fa fa-wrench"></i>
                        <span><?php echo $gsprache->settings;?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<li <?php if($ui->smallletters('w',255,'get')=='se') echo 'class="active"';?>><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
						<li <?php if($ui->smallletters('w',255,'get')=='sm') echo 'class="active"';?>><a href="admin.php?w=sm"><i class="fa fa-envelope"></i> E-Mail <?php echo $gsprache->settings;?></a></li>
						<?php if($easywiModules['ip']) { ?><li <?php if($ui->smallletters('w',255,'get')=='si') echo 'class="active"';?>><a href="admin.php?w=si"><i class="fa fa-angle-double-right"></i> <?php echo $gsprache->imprint.' '.$gsprache->settings;?></a></li><?php }?>
						<?php if($pa['root'] and $reseller_id==0) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='vc') echo 'class="active"';?>><a href="admin.php?w=vc"><i class="fa fa-check"></i> <?php echo $gsprache->versioncheck;?></a></li>
						<li <?php if($ui->smallletters('w',255,'get')=='cc') echo 'class="active"';?>><a href="admin.php?w=cc"><i class="fa fa-columns"></i> <?php echo $gsprache->columns;?></a></li>
						<li <?php if($ui->smallletters('w',255,'get')=='mo') echo 'class="active"';?>><a href="admin.php?w=mo"><i class="fa fa-th-large"></i> <?php echo $gsprache->modules;?></a></li>
						<?php } ?>
						<?php if($pa['root']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='bu') echo 'class="active"';?>><a href="admin.php?w=bu"><i class="fa fa-bars"></i> <?php echo $gsprache->databases;?></a></li>
						<?php } ?>
                    </ul>
                </li>
                <?php } ?>
	
                <?php if($pa['jobs'] or $pa['apiSettings']) { ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-cloud fa-fw"></i>
                        <span><?php echo $gsprache->jobs.'/'.$gsprache->api;?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
					<?php if($pa['jobs']) { ?><li <?php if($ui->smallletters('w',255,'get')=='jb') echo 'class="active"';?>><a href="admin.php?w=jb"><i class="fa fa-code-fork"></i> <?php echo $gsprache->jobs.' '.$gsprache->overview;?></a></li><?php } ?>
					<?php if($pa['apiSettings']) { ?>
					<li <?php if($ui->smallletters('w',255,'get')=='ap') echo 'class="active"';?>><a href="admin.php?w=ap"><i class="fa fa-wrench"></i> <?php echo $gsprache->api.' '.$gsprache->settings;?></a></li>
					<li <?php if($ui->smallletters('w',255,'get')=='aa') echo 'class="active"';?>><a href="admin.php?w=aa"><i class="fa fa-group"></i> <?php echo $gsprache->apiAuth;?></a></li>
					<li <?php if($ui->smallletters('w',255,'get')=='ui') echo 'class="active"';?>><a href="admin.php?w=ui"><i class="fa fa-mail-forward"></i> <?php echo $gsprache->userImport;?></a></li>
					<?php }?>
                    </ul>
                </li>
                <?php } ?>

                <?php if($pa['feeds']) { ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-rss fa-fw"></i>
                        <span><?php echo $gsprache->feeds;?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<li <?php if($ui->smallletters('w',255,'get')=='fn') echo 'class="active"';?>><a href="admin.php?w=fn"><i class="fa fa-info"></i> <?php echo $gsprache->feeds.' '.$gsprache->news;?></a></li>
						<li <?php if($ui->smallletters('w',255,'get')=='fe' and !in_array($d,array('ad','se'))) echo 'class="active"';?>><a href="admin.php?w=fe"><i class="fa fa-rss fa-fw"></i> <?php echo $gsprache->feeds;?></a></li>
						<li <?php if($ui->smallletters('w',255,'get')=='fe' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=fe&amp;d=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
                    </ul>
                </li>
                <?php } ?>

                <?php if($easywiModules['pn'] and $reseller_id==0 and ($pa['cms_settings'] or $pa['cms_pages'] or $pa['cms_news'])) { ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-globe fa-fw"></i>
                        <span>CMS</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<?php if($pa['cms_news']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='pn') echo 'class="active"';?>><a href="admin.php?w=pn"><i class="fa fa-globe"></i> <?php echo $gsprache->news;?></a></li>
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
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-clipboard fa-fw"></i>
                        <span><?php echo $gsprache->webspace;?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<li <?php if($ui->smallletters('w',255,'get')=='wv') echo 'class="active"';?>><a href="admin.php?w=wv"><i class="fa fa-globe"></i> Vhosts</a></li>
						<li <?php if($ui->smallletters('w',255,'get')=='wm') echo 'class="active"';?>><a href="admin.php?w=wm"><i class="fa fa-hdd-o"></i> <?php echo $gsprache->master;?></a></li>
						<?php foreach ($customModules['ws'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
					</ul>
                </li>
                <?php } ?>

                <?php if($easywiModules['my'] and ($pa['mysql_settings'] or $pa['mysql'])) { ?>
                <li class="treeview <?php if($w=='my') echo 'active';?>">
                    <a href="#">
                        <i class="fa fa-hdd-o fa-fw"></i>
                        <span>MySQL</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<?php if($pa['mysql']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='my' and !in_array($d,array('ms','as','ds','rs'))) echo 'class="active"';?>><a href="admin.php?w=my"><i class="fa fa-bars"></i> <?php echo $gsprache->databases;?></a></li>
						<?php } ?>
						<?php if($pa['mysql_settings']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='my' and in_array($d,array('ms','as','ds','rs'))) echo 'class="active"';?>><a href="admin.php?w=my&amp;d=ms"><i class="fa fa-hdd-o"></i> Server</a></li>
						<?php } ?>
						<?php foreach ($customModules['my'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
					</ul>
                </li>
                <?php } ?>
				
                <?php if($easywiModules['ti'] and $pa['usertickets'] and $reseller_id!=0) { ?>
                <li class="treeview <?php if($w=='ti') echo 'active';?>">
                    <a href="#">
                        <i class="fa fa-h-square fa-fw"></i>
                        <span><?php echo $gsprache->support;?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<li <?php if($ui->smallletters('w',255,'get')=='tr' and $ui->smallletters('d',255,'get')!='ad') echo 'class="active"';?>><a href="admin.php?w=tr"><i class="fa fa-users"></i> <?php echo $gsprache->overview;?></a></li>
						<li <?php if($ui->smallletters('w',255,'get')=='ti' and $ui->smallletters('d',255,'get')=='ad') echo 'class="active"';?>><a href="admin.php?w=tr&amp;d=ad"><i class="fa fa-pencil-square-o"></i> <?php echo $gsprache->support2;?></a></li>
						<?php foreach ($customModules['ti'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
					</ul>
                </li>
                <?php } ?>

                <?php if($pa['user'] or $pa['user_users'] or $pa['userGroups'] ) { ?>
                <li class="treeview <?php if($w=='us') echo 'active';?>">
                    <a href="#">
                        <i class="fa fa-users fa-fw"></i>
                        <span><?php echo $gsprache->user;?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<?php if($pa['user'] or $pa['user_users']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='us') echo 'class="active"';?>><a href="admin.php?w=us&amp;d=md"><i class="fa fa-user"></i> <?php echo $gsprache->overview;?></a></li>
						<?php } ?>
						<?php if($pa['userGroups']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='ug') echo 'class="active"';?>><a href="admin.php?w=ug"><i class="fa fa-group"></i> <?php echo $gsprache->groups;?></a></li>
						<?php } ?>
						<?php if($pa['root'] and $reseller_id==0) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='up') echo 'class="active"';?>><a href="admin.php?w=up"><i class="fa fa-external-link"></i> Social Auth Provider</a></li>
						<?php } ?>
						<?php foreach ($customModules['us'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
					</ul>
                </li>
                <?php } ?>

                <?php if($easywiModules['gs'] and ($pa['gserver'] or $pa['addons'] or $pa['gimages'] or $pa['eac'] or $pa['roots'] or $pa['masterServer']) and $easywiModules['gs']) { ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-gamepad fa-fw"></i>
                        <span><?php echo $gsprache->gameserver;?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<?php if($pa['gserver']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='gs') echo 'class="active"';?>><a href="admin.php?w=gs&amp;d=md"><i class="fa fa-columns"></i><?php echo $gsprache->overview;?></a></li>
						<?php } ?>
						<?php if($pa['gimages']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='im') echo 'class="active"';?>><a href="admin.php?w=im&amp;d=md"><i class="fa fa-file-text-o"></i><?php echo $gsprache->gameserver.' '.$gsprache->template;?></a></li>
						<?php } ?>
						<?php if($pa['addons']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='ad') echo 'class="active"';?>><a href="admin.php?w=ad"><i class="fa fa-gears"></i><?php echo $gsprache->addon;?></a></li>
						<?php } ?>
						<?php if($pa['gserver']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='gt') echo 'class="active"';?>><a href="admin.php?w=gt"><i class="fa fa-file-o"></i><?php echo $gsprache->file.' '.$gsprache->template;?></a></li>
						<?php } ?>
						<?php if($easywiModules['ea'] and $pa['eac']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='ea') echo 'class="active"';?>><a href="admin.php?w=ea"><i class="fa fa-eye"></i>Easy Anti Cheat</a></li>
						<?php } ?>
						<?php if($pa['roots']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='ro') echo 'class="active"';?>><a href="admin.php?w=ro"><i class="fa fa-hdd-o"></i><?php echo $gsprache->gameroot.' '.$gsprache->overview;?></a></li>
						<?php } ?>
						<?php if($pa['masterServer']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='ma' and $d!='ud') echo 'class="active"';?>><a href="admin.php?w=ma&amp;d=md"><i class="fa fa-sitemap"></i> <?php echo $gsprache->master.' '.$gsprache->overview;?></a></li>
						<li <?php if($ui->smallletters('w',255,'get')=='ma' and $d=='ud') echo 'class="active"';?>><a href="admin.php?w=ma&amp;d=ud"><i class="fa fa-download"></i> <?php echo $gsprache->master.' '.$gsprache->update;?></a></li>
						<?php } ?>
						<?php foreach ($customModules['gs'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if($easywiModules['vo'] and ($pa['voicemasterserver'] or $pa['voiceserver'] or $pa['voiceserverStats'] or $pa['voiceserverSettings'])) { ?>
                <li class="treeview">
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
						<li <?php if($ui->smallletters('w',255,'get')=='vm') echo 'class="active"';?>><a href="admin.php?w=vm"><i class="fa fa-hdd-o"></i> <?php echo $gsprache->master;?></a></li>
						<?php } ?>
						<?php if($pa['voiceserver']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='vr') echo 'class="active"';?>><a href="admin.php?w=vr"><i class="fa fa-columns"></i> TSDNS <?php echo $gsprache->overview;?></a></li>
						<?php } ?>
						<?php if($pa['voicemasterserver']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='vd') echo 'class="active"';?>><a href="admin.php?w=vd"><i class="fa fa-hdd-o"></i> TSDNS <?php echo $gsprache->master;?></a></li>
						<?php } ?>
						<?php if($pa['voiceserverStats']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='vu' and $d!='se') echo 'class="active"';?>><a href="admin.php?w=vu&amp;d=md"><i class="fa fa-dashboard"></i> <?php echo $gsprache->stats;?></a></li>
						<?php } ?>
						<?php if($pa['voiceserverSettings']) { ?>
						<li <?php if($ui->smallletters('w',255,'get')=='vu' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=vu&amp;d=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
						<?php } ?>
						<?php foreach ($customModules['vo'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if($easywiModules['le'] and (($easywiModules['gs'] or $easywiModules['vo']) and ($pa['lendserver'] or $pa['lendserverSettings']))) { ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-smile-o fa-fw"></i>
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
				
                <?php if($easywiModules['ti'] and $pa['tickets']) { ?>
                <li class="treeview <?php if($w=='ti') echo 'active';?>">
                    <a href="#">
                        <i class="fa fa-h-square fa-fw"></i>
                        <span><?php echo $gsprache->support;?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<li <?php if($ui->smallletters('w',255,'get')=='ti' and $d!='mt') echo 'class="active"';?>><a href="admin.php?w=ti"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a></li>
						<li <?php if($ui->smallletters('w',255,'get')=='ti' and $d=='mt') echo 'class="active"';?>><a href="admin.php?w=ti&amp;d=mt"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
						<?php foreach ($customModules['ti'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if($easywiModules['ro'] and rsellerpermisions($admin_id,$sql) and $easywiModules['ro']) { ?>
                <li class="treeview <?php if($w=='ti') echo 'active';?>">
                    <a href="#">
                        <i class="fa fa-laptop fa-fw"></i>
                        <span>Rootserver</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<?php if($pa['modvserver'] or $pa['delvserver'] or $pa['usevserver']) { ?><li <?php if($ui->smallletters('w',255,'get')=='vs') echo 'class="active"';?>><a href="admin.php?w=vs&amp;d=md"><i class="fa fa-laptop"></i> <?php echo $gsprache->virtual;?></a></li><?php } ?>
						<?php if($pa['dedicatedServer']) { ?><li <?php if($ui->smallletters('w',255,'get')=='rh') echo 'class="active"';?>><a href="admin.php?w=rh"><i class="fa fa-hdd-o"></i> <?php echo $gsprache->dedicated;?></a></li><?php } ?>
						<?php if($pa['vserverhost'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='vh') echo 'class="active"';?>><a href="admin.php?w=vh"><i class="fa fa-hdd-o"></i> ESX(I) Host</a></li><?php } ?>
						<?php if($pa['resellertemplates'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='ot') echo 'class="active"';?>><a href="admin.php?w=ot"><i class="fa fa-file-text-o"></i> <?php echo $gsprache->template;?></a></li><?php } ?>
						<?php foreach ($customModules['ro'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if($easywiModules['ro'] and $easywiModules['ro']) { ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-sitemap fa-fw"></i>
                        <span><?php echo $gsprache->network;?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<?php if($pa['traffic']) { ?><li <?php if($ui->smallletters('w',255,'get')=='tf' and $d!='se') echo 'class="active"';?>><a href="admin.php?w=tf"><i class="fa fa-dashboard"></i> <?php echo $gsprache->traffic;?></a></li><?php } ?>
						<?php if($reseller_id=='0' and $pa['trafficsettings']) { ?><li <?php if($ui->smallletters('w',255,'get')=='tf' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=tf&amp;d=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->traffic.' '.$gsprache->settings;?></a></li><?php } ?>
						<?php if($pa['dhcpServer'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='rd') echo 'class="active"';?>><a href="admin.php?w=rd"><i class="fa fa-sitemap"></i> DHCP</a></li><?php } ?>
						<?php if($pa['pxeServer'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='rp') echo 'class="active"';?>><a href="admin.php?w=rp"><i class="fa fa-paste"></i> PXE</a></li><?php } ?>
						<?php if($pa['root'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='sn') echo 'class="active"';?>><a href="admin.php?w=sn"><i class="fa fa-sitemap"></i> <?php echo $gsprache->subnets;?></a></li><?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if(count($customModules['mo'])>0) { ?>
                <li class="treeview">
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
    <aside class="right-side">

		<?php if(isset($header)){ ?><div class="alert alert-block"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $text; ?></div><?php } ?>
		<?php if(isset($toooldversion)){ ?><div class="alert alert-block"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $toooldversion; ?></div><?php } ?>
		<?php if($rSA['lastCronWarnStatus']=='Y' and (time()-$rSA['lastCronStatus'])>600 and $reseller_id==0){ ?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> Cronjob: statuscheck.php</div><?php }?>
		<?php if($rSA['lastCronWarnReboot']=='Y' and (time()-$rSA['lastCronReboot'])>5400 and $reseller_id==0){ ?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> Cronjob: reboot.php</div><?php }?>
		<?php if($rSA['lastCronWarnUpdates']=='Y' and (time()-$rSA['lastCronUpdates'])>300 and $reseller_id==0){ ?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> Cronjob: startupdates.php</div><?php }?>
		<?php if($rSA['lastCronWarnJobs']=='Y' and (time()-$rSA['lastCronJobs'])>300 and $reseller_id==0){ ?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> Cronjob: jobs.php</div><?php }?>
		<?php if($rSA['lastCronWarnCloud']=='Y' and (time()-$rSA['lastCronCloud'])>1200 and $reseller_id==0){ ?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> Cronjob: cloud.php</div><?php }?>