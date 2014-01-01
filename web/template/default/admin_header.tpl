<!DOCTYPE html>
<html dir="ltr" lang="<?php echo $user_language;?>">
<head>
    <?php if(isset($header)) echo $header; ?>
    <title><?php if(isset($ewCfg['title'])) echo $ewCfg['title']; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index,follow,noodp,noydir" />
    <meta name="description" content="">
    <meta name="author" content="2012 - <?php echo date('Y'); ?> <?php if(isset($ewCfg['title'])) echo $ewCfg['title']; ?>">
    <link href="//netdna.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <link href="css/default/easy-wi.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.ico" />
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" ></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>
    <script src="js/default/footable.js" type="text/javascript"></script>
    <script type="text/javascript">$(function() { $('table').footable();});</script>
    <script src="js/default/main.js" type="text/javascript"></script>
    <?php if(isset($ajaxonload)) echo $ajaxonload; ?>
    <script type="text/javascript">window.onDomReady(onReady); function onReady() { SwitchShowHideRows('init_ready');}</script>
</head>
<body>
<div class="nav navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner text-center">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="nav-collapse collapse">
            <p class="navbar-text pull-left">
                &nbsp;&nbsp;
                <?php foreach ($languages as $language){ echo '<a href="admin.php?l='.$language.'"><img src="images/flags/'.$language.'.png" alt="Flag: '.$language.'.png."></a> ';} ?>
                &nbsp;&nbsp;
            </p>

            <div class="navbar-text pull-left">
                <?php if($pa['usertickets'] and $crashedArray['ticketsReseller']>0 and $reseller_id!=0) { ?><a href="admin.php?w=tr"><span class="badge badge-info"><?php echo $crashedArray['ticketsReseller'].'/'.$crashedArray['ticketsResellerOpen'].' '.$sprache_bad->tickets; ?></span></a><?php }?>
                <?php if($pa['tickets'] and $crashedArray['ticketsOpen']>0) { ?><a href="admin.php?w=ti"><span class="badge badge-info"><?php echo $crashedArray['tickets'].'/'.$crashedArray['ticketsOpen'].' '.$sprache_bad->tickets; ?></span></a><?php }?>
                <?php if($pa['gserver'] and $gserver_module) { ?>
                <?php if($crashedArray['gsCrashed']>0) { ?><a href="admin.php?w=gs&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['gsCrashed'].' '.$sprache_bad->gserver_crashed; ?></span></a><?php }?>
                <?php if($crashedArray['gsPWD']>0) { ?><a href="admin.php?w=gs&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['gsPWD'].' '.$sprache_bad->gserver_removed; ?></span></a><?php }?>
                <?php if($crashedArray['gsTag']>0) { ?><a href="admin.php?w=gs&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['gsTag'].' '.$sprache_bad->gserver_tag_removed; ?></span></a><?php }?>
                <?php }?>
                <?php if($pa['voiceserver'] and $crashedArray['ts3']>0 and $voserver_module) { ?><a href="admin.php?w=vo&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['ts3'].' '.$sprache_bad->voice_crashed; ?></span></a><?php }?>
                <?php if($pa['voicemasterserver'] and $crashedArray['ts3Master']>0 and $voserver_module) { ?><a href="admin.php?w=vo&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['ts3Master'].' '.$sprache_bad->ts3master_crashed; ?></span></a><?php }?>
                <?php if($pa['roots'] and $crashedArray['masterserver']>0 and $gserver_module) { ?><a href="admin.php?w=ro"><span class="badge badge-important"><?php echo $crashedArray['masterserver'].' '.$sprache_bad->master_crashed; ?></span></a><?php }?>
                <?php if($pa['vserverhost'] and $crashedArray['virtualHosts']>0 and $vserver_module and $reseller_id==0) { ?><a href="admin.php?w=vh"><span class="badge badge-important"><?php echo $crashedArray['virtualHosts'].' '.$sprache_bad->host_crashed; ?></span></a><?php }?>
            </div>

            <span class="navbar-text">Easy-WI.com</span>

            <a href="login.php?w=lo" class="navbar-text pull-right navbar-logout">
                <span class="btn btn-mini btn-danger"><i class="fa fa-sign-out"></i> Logout</span>
            </a>

            <ul class="nav pull-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i> <?php echo $great_user;?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#"><?php echo $gsprache->last.'<br />'.$great_last;?></a></li>
                        <li class="divider"></li>
                        <?php if ($support_phonenumber!="") echo '<li><a href="#"><i class="fa fa-phone fa-fw"></i> '.$gsprache->hotline.": ".$support_phonenumber.'</a></li><li class="divider"></li>';?>
                        <li><a href="admin.php?w=su&amp;d=pw"><i class="fa fa-key fa-fw"></i> <?php echo $gsprache->password." ".$gsprache->change;?></a></li>
                        <li><a href="admin.php?w=su"><i class="fa fa-cog fa-fw"></i> <?php echo $gsprache->settings;?></a></li>
                        <li class="divider"></li>
                        <li><a href="https://easy-wi.com" target="_blank"><i class="fa fa-info-circle fa-fw"></i> About</a></li>
                        <li><a href="https://easy-wi.com/forum/" target="_blank"><i class="fa fa-comments fa-fw"></i> Forum</a></li>
                        <li><a href="http://wiki.easy-wi.com" target="_blank"><i class="fa fa-question-circle fa-fw"></i> Wiki</a></li>
                        <li><a href="https://twitter.com/EasyWI" target="_blank"><i class="fa fa-twitter fa-fw"></i> Easy-WI @ Twitter</a></li>
                        <li><a href="https://github.com/easy-wi/developer" target="_blank"><i class="fa fa-github fa-fw"></i> Easy-WI @ Github</a></li>
                        <li><a href="https://github.com/ValveSoftware/steam-for-linux/issues" target="_blank"><i class="fa fa-bug fa-fw"></i> Steam Bugtracker</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="container-fluid" id="content">
    <div class="row-fluid">
        <div class="span3">
            <div class="well sidebar-nav">
                <div class="accordion" id="accordionMenu">
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseOne"><i class="fa fa-home fa-fw"></i> Home</a>
                        </div>
                        <div id="collapseOne" class="accordion-body collapse <?php if(in_array($ui->smallletters('w',255,'get'),array('da','ho','ib','lo','ml','ip'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <li <?php if($ui->smallletters('w',255,'get')=='da' or $ui->smallletters('w',255,'get')=='ho') echo 'class="active"';?>><a href="admin.php?w=da">Dashboard</a></li>
                                    <?php if($pa['ipBans']) { ?><li <?php if($ui->smallletters('w',255,'get')=='ib') echo 'class="active"';?>><a href="admin.php?w=ib">IP Bans</a></li><?php } ?>
                                    <?php if($pa['log']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='lo') echo 'class="active"';?>><a href="admin.php?w=lo"><?php echo $gsprache->logs;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ml') echo 'class="active"';?>><a href="admin.php?w=ml">Mail <?php echo $gsprache->logs;?></a></li>
                                    <?php } ?>
                                    <?php if($easywiModules['ip']) { ?><li <?php if($ui->smallletters('w',255,'get')=='ip') echo 'class="active"';?>><a href="admin.php?w=ip"><?php echo $gsprache->imprint;?></a></li><?php }?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" href="admin.php?w=sr"><i class="fa fa-search fa-fw"></i> <?php echo $gsprache->search;?></a>
                        </div>
                    </div>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseSettings"><i class="fa fa-cogs fa-fw"></i> Easy-WI <?php echo $gsprache->settings;?></a>
                        </div>
                        <div id="collapseSettings" class="accordion-body collapse <?php if(in_array($ui->smallletters('w',255,'get'),array('se','si','sm','vc','cc','bu','mo'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['settings']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='se') echo 'class="active"';?>><a href="admin.php?w=se"><?php echo $gsprache->settings;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='sm') echo 'class="active"';?>><a href="admin.php?w=sm">E-Mail <?php echo $gsprache->settings;?></a></li>
                                    <?php if($easywiModules['ip']) { ?><li <?php if($ui->smallletters('w',255,'get')=='si') echo 'class="active"';?>><a href="admin.php?w=si"><?php echo $gsprache->imprint.' '.$gsprache->settings;?></a></li><?php }?>
                                    <?php } ?>
                                    <?php if($pa['root'] and $reseller_id==0) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='vc') echo 'class="active"';?>><a href="admin.php?w=vc"><?php echo $gsprache->versioncheck;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='cc') echo 'class="active"';?>><a href="admin.php?w=cc"><?php echo $gsprache->columns;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='mo') echo 'class="active"';?>><a href="admin.php?w=mo"><?php echo $gsprache->modules;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['root']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='bu') echo 'class="active"';?>><a href="admin.php?w=bu"><?php echo $gsprache->databases;?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php if($pa['jobs'] or $pa['apiSettings']) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseTwo"><i class="fa fa-cloud fa-fw"></i> <?php echo $gsprache->jobs.'/'.$gsprache->api;?></a>
                        </div>
                        <div id="collapseTwo" class="accordion-body collapse <?php if(in_array($ui->smallletters('w',255,'get'),array('ap','aa','jb','ui'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['jobs']) { ?><li <?php if($ui->smallletters('w',255,'get')=='jb') echo 'class="active"';?>><a href="admin.php?w=jb"><?php echo $gsprache->jobs.' '.$gsprache->overview;?></a></li><?php } ?>
                                    <?php if($pa['apiSettings']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ap') echo 'class="active"';?>><a href="admin.php?w=ap"><?php echo $gsprache->api.' '.$gsprache->settings;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='aa') echo 'class="active"';?>><a href="admin.php?w=aa"><?php echo $gsprache->apiAuth;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ui') echo 'class="active"';?>><a href="admin.php?w=ui"><?php echo $gsprache->userImport;?></a></li>
                                    <?php }?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($pa['feeds']) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseThree"><i class="fa fa-rss fa-fw"></i> <?php echo $gsprache->feeds;?></a>
                        </div>
                        <div id="collapseThree" class="accordion-body collapse <?php if(in_array($ui->smallletters('w',255,'get'),array('fe','fn'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <li <?php if($ui->smallletters('w',255,'get')=='fn') echo 'class="active"';?>><a href="admin.php?w=fn"><?php echo $gsprache->feeds.' '.$gsprache->news;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='fe' and !in_array($d,array('ad','se'))) echo 'class="active"';?>><a href="admin.php?w=fe"><?php echo $gsprache->feeds;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='fe' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=fe&amp;d=se"><?php echo $gsprache->settings;?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($easywiModules['pn'] and $reseller_id==0 and ($pa['cms_settings'] or $pa['cms_pages'] or $pa['cms_news'])) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseFour"><i class="fa fa-globe fa-fw"></i> CMS</a>
                        </div>
                        <div id="collapseFour" class="accordion-body collapse <?php if(in_array($ui->smallletters('w',255,'get'),array('pn','pp','ps','pc','pd')) or isset($customModules['pa'][$ui->smallletters('w',255,'get')])) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['cms_news']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='pn') echo 'class="active"';?>><a href="admin.php?w=pn"><?php echo $gsprache->news;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='pc') echo 'class="active"';?>><a href="admin.php?w=pc"><?php echo $gsprache->comments;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['cms_pages']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='pp') echo 'class="active"';?>><a href="admin.php?w=pp"><?php echo $gsprache->pages;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='pd') echo 'class="active"';?>><a href="admin.php?w=pd"><?php echo $gsprache->downloads;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['cms_settings']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ps') echo 'class="active"';?>><a href="admin.php?w=ps"><?php echo $gsprache->settings;?></a></li>
                                    <?php } ?>
                                    <?php foreach ($customModules['pa'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($easywiModules['my'] and ($pa['mysql_settings'] or $pa['mysql'])) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseFive"><i class="fa fa-sitemap fa-fw"></i> MySQL</a>
                        </div>
                        <div id="collapseFive" class="accordion-body collapse <?php if($ui->smallletters('w',255,'get')=='my' or isset($customModules['my'][$ui->smallletters('w',255,'get')])) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['mysql']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='my' and !in_array($d,array('ms','as','ds'))) echo 'class="active"';?>><a href="admin.php?w=my"><?php echo $gsprache->databases;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['mysql_settings']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='my' and in_array($d,array('ms','as','ds'))) echo 'class="active"';?>><a href="admin.php?w=my&amp;d=ms">Server</a></li>
                                    <?php } ?>
                                    <?php foreach ($customModules['my'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($easywiModules['ti'] and $pa['usertickets'] and $reseller_id!=0) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseSix"><i class="fa fa-h-square fa-fw"></i> <?php echo $gsprache->support;?></a>
                        </div>
                        <div id="collapseSix" class="accordion-body collapse <?php if($ui->smallletters('w',255,'get')=='tr') echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <li <?php if($ui->smallletters('w',255,'get')=='tr' and $ui->smallletters('d',255,'get')!='ad') echo 'class="active"';?>><a href="admin.php?w=tr"><?php echo $gsprache->overview;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ti' and $ui->smallletters('d',255,'get')=='ad') echo 'class="active"';?>><a href="admin.php?w=tr&amp;d=ad"><?php echo $gsprache->support2;?></a></li>
                                    <?php foreach ($customModules['ti'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($pa['user'] or $pa['user_users'] or $pa['userGroups'] ) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseSeven"><i class="fa fa-users fa-fw"></i> <?php echo $gsprache->user;?></a>
                        </div>
                        <div id="collapseSeven" class="accordion-body collapse <?php if(in_array($ui->smallletters('w',255,'get'),array('us','ug')) or isset($customModules['us'][$ui->smallletters('w',255,'get')])) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['user'] or $pa['user_users']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='us') echo 'class="active"';?>><a href="admin.php?w=us&amp;d=md"><?php echo $gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['userGroups']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ug') echo 'class="active"';?>><a href="admin.php?w=ug"><?php echo $gsprache->groups;?></a></li>
                                    <?php } ?>
                                    <?php foreach ($customModules['us'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($easywiModules['gs'] and ($pa['gserver'] or $pa['addons'] or $pa['gimages'] or $pa['eac'] or $pa['roots'] or $pa['masterServer']) and $gserver_module) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseNine"><i class="fa fa-gamepad fa-fw"></i> <?php echo $gsprache->gameserver;?></a>
                        </div>
                        <div id="collapseNine" class="accordion-body collapse <?php if(in_array($ui->smallletters('w',255,'get'),array('gs','ea','im','ad','ro','ma')) or isset($customModules['gs'][$ui->smallletters('w',255,'get')])) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['gserver']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='gs') echo 'class="active"';?>><a href="admin.php?w=gs&amp;d=md"><?php echo $gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($easywiModules['ea'] and $pa['eac']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ea') echo 'class="active"';?>><a href="admin.php?w=ea">Easy Anti Cheat</a></li>
                                    <?php } ?>
                                    <?php if($pa['gimages']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='im') echo 'class="active"';?>><a href="admin.php?w=im&amp;d=md"><?php echo $gsprache->template;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['addons']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ad') echo 'class="active"';?>><a href="admin.php?w=ad"><?php echo $gsprache->addon;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['roots']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ro') echo 'class="active"';?>><a href="admin.php?w=ro"><?php echo $gsprache->gameroot.' '.$gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['masterServer']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ma' and $d!='ud') echo 'class="active"';?>><a href="admin.php?w=ma&amp;d=md"><?php echo $gsprache->master.' '.$gsprache->overview;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ma' and $d=='ud') echo 'class="active"';?>><a href="admin.php?w=ma&amp;d=ud"><?php echo $gsprache->master.' '.$gsprache->update;?></a></li>
                                    <?php } ?>
                                    <?php foreach ($customModules['gs'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($easywiModules['vo'] and ($pa['voicemasterserver'] or $pa['voiceserver'] or $pa['voiceserverStats'] or $pa['voiceserverSettings'])) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseTen"><i class="fa fa-microphone fa-fw"></i> <?php echo $gsprache->voiceserver;?></a>
                        </div>
                        <div id="collapseTen" class="accordion-body collapse <?php if(in_array($ui->smallletters('w',255,'get'),array('vo','vu','vd','vm','vr')) or isset($customModules['vo'][$ui->smallletters('w',255,'get')])) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['voiceserver']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='vo') echo 'class="active"';?>><a href="admin.php?w=vo"><?php echo $gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['voicemasterserver']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='vm') echo 'class="active"';?>><a href="admin.php?w=vm"><?php echo $gsprache->master;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['voiceserver']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='vr') echo 'class="active"';?>><a href="admin.php?w=vr">TSDNS <?php echo $gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['voicemasterserver']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='vd') echo 'class="active"';?>><a href="admin.php?w=vd">TSDNS <?php echo $gsprache->master;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['voiceserverStats']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='vu' and $d!='se') echo 'class="active"';?>><a href="admin.php?w=vu&amp;d=md"><?php echo $gsprache->stats;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['voiceserverSettings']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='vu' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=vu&amp;d=se"><?php echo $gsprache->settings;?></a></li>
                                    <?php } ?>
                                    <?php foreach ($customModules['vo'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($easywiModules['le'] and ($gserver_module and ($pa['lendserver'] or $pa['lendserverSettings']))) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseEleven"><i class="fa fa-smile-o fa-fw"></i> <?php echo $gsprache->lendserver;?></a>
                        </div>
                        <div id="collapseEleven" class="accordion-body collapse <?php if($ui->smallletters('w',255,'get')=='le') echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['lendserver']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='le' and $d!='se') echo 'class="active"';?>><a href="admin.php?w=le"><?php echo $gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['lendserverSettings']) { ?>
                                    <li <?php if($ui->smallletters('w',255,'get')=='le' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=le&amp;d=se"><?php echo $gsprache->settings;?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($easywiModules['ti'] and $pa['tickets']) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseTwelve"><i class="fa fa-h-square fa-fw"></i> <?php echo $gsprache->support;?></a>
                        </div>
                        <div id="collapseTwelve" class="accordion-body collapse <?php if($ui->smallletters('w',255,'get')=='ti' or isset($customModules['ti'][$ui->smallletters('w',255,'get')])) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <li <?php if($ui->smallletters('w',255,'get')=='ti' and $d!='mt') echo 'class="active"';?>><a href="admin.php?w=ti"><?php echo $gsprache->overview;?></a></li>
                                    <li <?php if($ui->smallletters('w',255,'get')=='ti' and $d=='mt') echo 'class="active"';?>><a href="admin.php?w=ti&amp;d=mt"><?php echo $gsprache->settings;?></a></li>
                                    <?php foreach ($customModules['ti'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($easywiModules['ro'] and rsellerpermisions($admin_id,$sql) and $vserver_module) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseThirteen"><i class="fa fa-laptop fa-fw"></i> Rootserver</a>
                        </div>
                        <div id="collapseThirteen" class="accordion-body collapse <?php if(in_array($ui->smallletters('w',255,'get'),array('vs','dp','vh','rd','rp','rh','ot','tf')) or isset($customModules['ro'][$ui->smallletters('w',255,'get')])) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['modvserver'] or $pa['delvserver'] or $pa['usevserver']) { ?><li <?php if($ui->smallletters('w',255,'get')=='vs') echo 'class="active"';?>><a href="admin.php?w=vs&amp;d=md"><?php echo $gsprache->virtual;?></a></li><?php } ?>
                                    <?php if($pa['dedicatedServer']) { ?><li <?php if($ui->smallletters('w',255,'get')=='rh') echo 'class="active"';?>><a href="admin.php?w=rh"><?php echo $gsprache->dedicated;?></a></li><?php } ?>
                                    <?php if($pa['traffic']) { ?><li <?php if($ui->smallletters('w',255,'get')=='tf' and $d!='se') echo 'class="active"';?>><a href="admin.php?w=tf"><?php echo $gsprache->traffic;?></a></li><?php } ?>
                                    <?php if($pa['vserverhost'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='vh') echo 'class="active"';?>><a href="admin.php?w=vh">ESX(I) Host</a></li><?php } ?>
                                    <?php if($pa['dhcpServer'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='rd') echo 'class="active"';?>><a href="admin.php?w=rd">DHCP</a></li><?php } ?>
                                    <?php if($pa['pxeServer'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='rp') echo 'class="active"';?>><a href="admin.php?w=rp">PXE</a></li><?php } ?>
                                    <?php if($pa['resellertemplates'] and $reseller_id==0) { ?><li <?php if($ui->smallletters('w',255,'get')=='ot') echo 'class="active"';?>><a href="admin.php?w=ot"><?php echo $gsprache->template;?></a></li><?php } ?>
                                    <?php if($reseller_id=='0' and $pa['trafficsettings']) { ?><li <?php if($ui->smallletters('w',255,'get')=='tf' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=tf&amp;d=se"><?php echo $gsprache->traffic.' '.$gsprache->settings;?></a></li><?php } ?>
                                    <?php foreach ($customModules['ro'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if(count($customModules['mo'])>0) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseFourteen"><i class="fa fa-tasks fa-fw"></i> <?php echo $gsprache->modules;?></a>
                        </div>
                        <div id="collapseFourteen" class="accordion-body collapse <?php if(isset($customModules['mo'][$ui->smallletters('w',255,'get')])) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php foreach ($customModules['mo'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
            <?php if(isset($header)){ ?><div class="alert alert-block"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $text; ?></div><?php } ?>
            <?php if(isset($toooldversion)){ ?><div class="alert alert-block"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $toooldversion; ?></div><?php } ?>
            <?php if($rSA['lastCronWarnStatus']=='Y' and (time()-$rSA['lastCronStatus'])>600 and $reseller_id==0){ ?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> Cronjob: statuscheck.php</div><?php }?>
            <?php if($rSA['lastCronWarnReboot']=='Y' and (time()-$rSA['lastCronReboot'])>5400 and $reseller_id==0){ ?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> Cronjob: reboot.php</div><?php }?>
            <?php if($rSA['lastCronWarnUpdates']=='Y' and (time()-$rSA['lastCronUpdates'])>300 and $reseller_id==0){ ?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> Cronjob: startupdates.php</div><?php }?>
            <?php if($rSA['lastCronWarnJobs']=='Y' and (time()-$rSA['lastCronJobs'])>300 and $reseller_id==0){ ?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> Cronjob: jobs.php</div><?php }?>
            <?php if($rSA['lastCronWarnCloud']=='Y' and (time()-$rSA['lastCronCloud'])>1200 and $reseller_id==0){ ?><div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> Cronjob: cloud.php</div><?php }?>