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
    <link href="css/default/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">body { padding-top: 60px;padding-bottom: 40px;}</style>
    <link href="css/default/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="css/default/easy-wi.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.ico" />
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="js/default/html5shiv.js"></script>
    <![endif]-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" ></script>
    <script src="js/default/bootstrap.min.js"></script>
    <script src="js/default/footable.js" type="text/javascript"></script>
    <script type="text/javascript">$(function() { $('table').footable();});</script>
    <script src="js/default/main.js" type="text/javascript"></script>
    <?php if(isset($ajaxonload)) echo $ajaxonload; ?>
    <script type="text/javascript">window.onDomReady(onReady); function onReady() { SwitchShowHideRows('init_ready');}</script>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <p class="navbar-text pull-left">
                <?php foreach ($languages as $language){ echo '<a href="userpanel.php?l='.$language.'"><img src="images/flags/'.$language.'.png" alt="Flag: '.$language.'.png."></a>';} ?>
            </p>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Easy-WI.com<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="http://wiki.easy-wi.com" target="_blank">Wiki</a></li>
                            <li><a href="http://easy-wi.com" target="_blank">About</a></li>
                            <li><a href="http://forum.easy-wi.com" target="_blank">Forum</a></li>
                            <li><a href="https://github.com/ValveSoftware/steam-for-linux/issues" target="_blank">Steam Bugtracker</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $gsprache->welcome.$great_vname." ".$great_name;?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><?php echo $gsprache->last.'<br />'.$great_last;?></a></li>
                            <li class="divider"></li>
                            <?php if ($support_phonenumber!="") echo '<li><a href="#">'.$gsprache->hotline.": ".$support_phonenumber.'</a></li>';?>
                            <li class="divider"></li>
                            <li><a href="admin.php?w=su"><?php echo $gsprache->settings;?></a></li>
                            <li class="divider"></li>
                            <li><a href="login.php?w=lo">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!--/.nav-collapse -->
            <div class="navbar-text pull-right">
                <?php if($pa['usertickets'] and $crashedArray['ticketsReseller']>0 and $reseller_id!=0) { ?><a href="admin.php?w=tr"><span class="badge badge-info"><?php echo $crashedArray['ticketsReseller'].'/'.$crashedArray['ticketsResellerOpen'].' '.$sprache_bad->tickets; ?></span></a><?php }?>
                <?php if($pa['tickets'] and $crashedArray['ticketsOpen']>0) { ?><a href="admin.php?w=ti"><span class="badge badge-info"><?php echo $crashedArray['tickets'].'/'.$crashedArray['ticketsOpen'].' '.$sprache_bad->tickets; ?></span></a><?php }?>
                <?php if($pa['gserver'] and $gserver_module) { ?>
                <?php if($crashedArray['gsCrashed']>0) { ?><a href="admin.php?w=gs&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['gsCrashed'].' '.$sprache_bad->gserver_crashed; ?></span></a><?php }?>
                <?php if($crashedArray['gsPWD']>0) { ?><a href="admin.php?w=gs&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['gsPWD'].' '.$sprache_bad->gserver_removed; ?></span></a><?php }?>
                <?php if($crashedArray['gsTag']>0) { ?><a href="admin.php?w=gs&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['gsTag'].' '.$sprache_bad->gserver_tag_removed; ?></span></a><?php }?>
                <?php }?>
                <?php if($pa['voiceserver'] and $crashedArray['ts3']>0 and $voserver_module) { ?><a href="admin.php?w=vo&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['ts3'].' '.$sprache_bad->voice_crashed; ?></span></a><?php }?>
                <?php if($pa['voicemasterserver'] and $crashedArray['ts3Master']>0 and $voserver_module) { ?><a href="admin.php?w=vo&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['ts3Master'].' '.$sprache_bad->ts3master_crashed; ?></span></a><?php }?>
                <?php if($pa['roots'] and $crashedArray['masterserver']>0 and $gserver_module) { ?><a href="admin.php?w=ro&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['masterserver'].' '.$sprache_bad->master_crashed; ?></span></a><?php }?>
                <?php if($pa['vserverhost'] and $crashedArray['virtualHosts']>0 and $vserver_module and $reseller_id==0) { ?><a href="admin.php?w=vh&amp;d=md"><span class="badge badge-important"><?php echo $crashedArray['virtualHosts'].' '.$sprache_bad->host_crashed; ?></span></a><?php }?>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <div class="well sidebar-nav">
                <div class="accordion" id="accordionMenu">
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseOne">Dashboard</a>
                        </div>
                        <div id="collapseOne" class="accordion-body collapse <?php if(in_array($w,array('da','ho','ib','lo','ml','ip'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <li <?php if($w=='da' or $w=='ho') echo 'class="active"';?>><a href="admin.php?w=da">Dashboard</a></li>
                                    <?php if($pa['ipBans']) { ?><li <?php if($w=='ib') echo 'class="active"';?>><a href="admin.php?w=ib">IP Bans</a></li><?php } ?>
                                    <?php if($pa['log']) { ?>
                                    <li <?php if($w=='lo') echo 'class="active"';?>><a href="admin.php?w=lo"><?php echo $gsprache->logs;?></a></li>
                                    <li <?php if($w=='ml') echo 'class="active"';?>><a href="admin.php?w=ml">Mail <?php echo $gsprache->logs;?></a></li>
                                    <?php } ?>
                                    <li <?php if($w=='ip') echo 'class="active"';?>><a href="admin.php?w=ip"><?php echo $gsprache->imprint;?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" href="admin.php?w=sr"><?php echo $gsprache->search;?></a>
                        </div>
                    </div>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseSettings">Easy-WI <?php echo $gsprache->settings;?></a>
                        </div>
                        <div id="collapseSettings" class="accordion-body collapse <?php if(in_array($w,array('se','sm','vc','cc','bu'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['settings']) { ?>
                                    <li <?php if($w=='se') echo 'class="active"';?>><a href="admin.php?w=se"><?php echo $gsprache->settings;?></a></li>
                                    <li <?php if($w=='sm') echo 'class="active"';?>><a href="admin.php?w=sm">E-Mail <?php echo $gsprache->settings;?></a></li>
                                    <?php } ?>
                                    <li <?php if($w=='vc') echo 'class="active"';?>><a href="admin.php?w=vc"><?php echo $gsprache->versioncheck;?></a></li>
                                    <?php if($pa['root'] and $reseller_id==0) { ?>
                                    <li <?php if($w=='cc') echo 'class="active"';?>><a href="admin.php?w=cc"><?php echo $gsprache->columns;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['root'] and $reseller_id==0) { ?>
                                    <li <?php if($w=='bu') echo 'class="active"';?>><a href="admin.php?w=bu"><?php echo $gsprache->databases;?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php if($pa['jobs'] or $pa['apiSettings']) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseTwo"><?php echo $gsprache->jobs.'/'.$gsprache->api;?></a>
                        </div>
                        <div id="collapseTwo" class="accordion-body collapse <?php if(in_array($w,array('ap','aa','jb','ui'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['jobs']) { ?><li <?php if($w=='jb') echo 'class="active"';?>><a href="admin.php?w=jb"><?php echo $gsprache->jobs.' '.$gsprache->overview;?></a></li><?php } ?>
                                    <?php if($pa['apiSettings']) { ?>
                                    <li <?php if($w=='ap') echo 'class="active"';?>><a href="admin.php?w=ap"><?php echo $gsprache->api.' '.$gsprache->settings;?></a></li>
                                    <li <?php if($w=='aa') echo 'class="active"';?>><a href="admin.php?w=aa"><?php echo $gsprache->apiAuth;?></a></li>
                                    <li <?php if($w=='ui') echo 'class="active"';?>><a href="admin.php?w=ui"><?php echo $gsprache->userImport;?></a></li>
                                    <?php }?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($pa['feeds']) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseThree"><?php echo $gsprache->feeds;?></a>
                        </div>
                        <div id="collapseThree" class="accordion-body collapse <?php if(in_array($w,array('fe','fn'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <li <?php if($w=='fn') echo 'class="active"';?>><a href="admin.php?w=fn"><?php echo $gsprache->feeds.' '.$gsprache->news;?></a></li>
                                    <li <?php if($w=='fe' and !in_array($d,array('ad','se'))) echo 'class="active"';?>><a href="admin.php?w=fe"><?php echo $gsprache->feeds;?></a></li>
                                    <li <?php if($w=='fe' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=fe&amp;d=se"><?php echo $gsprache->settings;?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($reseller_id==0 and ($pa['cms_settings'] or $pa['cms_pages'] or $pa['cms_news'])) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseFour">CMS</a>
                        </div>
                        <div id="collapseFour" class="accordion-body collapse <?php if(in_array($w,array('pn','pp','ps','pc','pd'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['cms_news']) { ?>
                                    <li <?php if($w=='pn') echo 'class="active"';?>><a href="admin.php?w=pn"><?php echo $gsprache->news;?></a></li>
                                    <li <?php if($w=='pc') echo 'class="active"';?>><a href="admin.php?w=pc"><?php echo $gsprache->comments;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['cms_pages']) { ?>
                                    <li <?php if($w=='pp') echo 'class="active"';?>><a href="admin.php?w=pp"><?php echo $gsprache->pages;?></a></li>
                                    <li <?php if($w=='pd') echo 'class="active"';?>><a href="admin.php?w=pd"><?php echo $gsprache->downloads;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['cms_settings']) { ?>
                                    <li <?php if($w=='ps') echo 'class="active"';?>><a href="admin.php?w=ps"><?php echo $gsprache->settings;?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if(($pa['mysql_settings'] or $pa['mysql'])) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseFive">MYSQL</a>
                        </div>
                        <div id="collapseFive" class="accordion-body collapse <?php if($w=='my') echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['mysql']) { ?>
                                    <li <?php if($w=='my' and !in_array($d,array('ms','as','ds'))) echo 'class="active"';?>><a href="admin.php?w=my"><?php echo $gsprache->databases;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['mysql_settings']) { ?>
                                    <li <?php if($w=='my' and in_array($d,array('ms','as','ds'))) echo 'class="active"';?>><a href="admin.php?w=my&amp;d=ms">Server</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($pa['usertickets'] and $reseller_id!=0) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseSix"><?php echo $gsprache->support;?></a>
                        </div>
                        <div id="collapseSix" class="accordion-body collapse <?php if($w=='my') echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <li <?php if($w=='tr') echo 'class="active"';?>><a href="admin.php?w=tr"><?php echo $gsprache->overview;?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($pa['user'] or $pa['user_users'] or $pa['userGroups'] ) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseSeven"><?php echo $gsprache->user;?></a>
                        </div>
                        <div id="collapseSeven" class="accordion-body collapse <?php if(in_array($w,array('us','ug'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['user'] or $pa['user_users']) { ?>
                                    <li <?php if($w=='us') echo 'class="active"';?>><a href="admin.php?w=us&amp;d=md"><?php echo $gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['userGroups']) { ?>
                                    <li <?php if($w=='ug') echo 'class="active"';?>><a href="admin.php?w=ug"><?php echo $gsprache->groups;?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($pa['roots'] or $pa['masterServer']) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseEight"><?php echo $gsprache->gameroot;?></a>
                        </div>
                        <div id="collapseEight" class="accordion-body collapse <?php if(in_array($w,array('ro','ma'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <li <?php if($w=='ro') echo 'class="active"';?>><a href="admin.php?w=ro&amp;d=md"><?php echo $gsprache->overview;?></a></li>
                                    <?php if($pa['masterServer']) { ?>
                                    <li <?php if($w=='ma' and $d!='ud') echo 'class="active"';?>><a href="admin.php?w=ma&amp;d=md"><?php echo $gsprache->master.' '.$gsprache->overview;?></a></li>
                                    <li <?php if($w=='ma' and $d=='ud') echo 'class="active"';?>><a href="admin.php?w=ma&amp;d=ud"><?php echo $gsprache->master.' '.$gsprache->update;?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if(($pa['gserver'] or $pa['addons'] or $pa['gimages'] or $pa['eac']) and $gserver_module) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseNine"><?php echo $gsprache->gameserver;?></a>
                        </div>
                        <div id="collapseNine" class="accordion-body collapse <?php if(in_array($w,array('gs','ea','im','ad'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['gserver']) { ?>
                                    <li <?php if($w=='gs') echo 'class="active"';?>><a href="admin.php?w=gs&amp;d=md"><?php echo $gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['eac']) { ?>
                                    <li <?php if($w=='ea') echo 'class="active"';?>><a href="admin.php?w=ea">Easy Anti Cheat</a></li>
                                    <?php } ?>
                                    <?php if($pa['gimages']) { ?>
                                    <li <?php if($w=='im') echo 'class="active"';?>><a href="admin.php?w=im&amp;d=md"><?php echo $gsprache->template;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['addons']) { ?>
                                    <li <?php if($w=='ad') echo 'class="active"';?>><a href="admin.php?w=ad"><?php echo $gsprache->addon;?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($pa['voicemasterserver'] or $pa['voiceserver'] or $pa['voiceserverStats'] or $pa['voiceserverSettings']) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseTen"><?php echo $gsprache->voiceserver;?></a>
                        </div>
                        <div id="collapseTen" class="accordion-body collapse <?php if(in_array($w,array('vo','vu','vd','vm','vr'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['voiceserver']) { ?>
                                    <li <?php if($w=='vo') echo 'class="active"';?>><a href="admin.php?w=vo"><?php echo $gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['voicemasterserver']) { ?>
                                    <li <?php if($w=='vm') echo 'class="active"';?>><a href="admin.php?w=vm"><?php echo $gsprache->master;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['voiceserver']) { ?>
                                    <li <?php if($w=='vr') echo 'class="active"';?>><a href="admin.php?w=vr">TSDNS <?php echo $gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['voicemasterserver']) { ?>
                                    <li <?php if($w=='vd') echo 'class="active"';?>><a href="admin.php?w=vd">TSDNS <?php echo $gsprache->master;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['voiceserverStats']) { ?>
                                    <li <?php if($w=='vu' and $d!='se') echo 'class="active"';?>><a href="admin.php?w=vu&amp;d=md"><?php echo $gsprache->stats;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['voiceserverSettings']) { ?>
                                    <li <?php if($w=='vu' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=vu&amp;d=se"><?php echo $gsprache->settings;?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($gserver_module and ($pa['lendserver'] or $pa['lendserverSettings'])) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseEleven"><?php echo $gsprache->lendserver;?></a>
                        </div>
                        <div id="collapseEleven" class="accordion-body collapse <?php if($w=='le') echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['lendserver']) { ?>
                                    <li <?php if($w=='le' and $d!='se') echo 'class="active"';?>><a href="admin.php?w=le"><?php echo $gsprache->overview;?></a></li>
                                    <?php } ?>
                                    <?php if($pa['lendserverSettings']) { ?>
                                    <li <?php if($w=='le' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=le&amp;d=se"><?php echo $gsprache->settings;?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if($pa['tickets']) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseTwelve"><?php echo $gsprache->support;?></a>
                        </div>
                        <div id="collapseTwelve" class="accordion-body collapse <?php if($w=='ti') echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <li <?php if($w=='ti' and $d!='mt') echo 'class="active"';?>><a href="admin.php?w=ti"><?php echo $gsprache->overview;?></a></li>
                                    <li <?php if($w=='ti' and $d=='mt') echo 'class="active"';?>><a href="admin.php?w=ti&amp;d=mt"><?php echo $gsprache->settings;?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if(rsellerpermisions($admin_id,$sql) and $vserver_module) { ?>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseThirteen">Rootserver</a>
                        </div>
                        <div id="collapseThirteen" class="accordion-body collapse <?php if(in_array($w,array('vs','dp','vh','rd','rp','rh','ot','tf'))) echo 'in';?>">
                            <div class="accordion-inner">
                                <ul class="nav nav-pills nav-stacked">
                                    <?php if($pa['modvserver'] or $pa['delvserver'] or $pa['usevserver']) { ?><li <?php if($w=='vs') echo 'class="active"';?>><a href="admin.php?w=vs&amp;d=md"><?php echo $gsprache->virtual;?></a></li><?php } ?>
                                    <?php if($pa['dedicatedServer']) { ?><li <?php if($w=='rh') echo 'class="active"';?>><a href="admin.php?w=rh"><?php echo $gsprache->dedicated;?></a></li><?php } ?>
                                    <?php if($pa['traffic']) { ?><li <?php if($w=='tf' and $d!='se') echo 'class="active"';?>><a href="admin.php?w=tf"><?php echo $gsprache->traffic;?></a></li><?php } ?>
                                    <?php if($pa['vserverhost'] and $reseller_id==0) { ?><li <?php if($w=='vh') echo 'class="active"';?>><a href="admin.php?w=vh&amp;d=md">ESX(I) Host</a></li><?php } ?>
                                    <?php if($pa['dhcpServer'] and $reseller_id==0) { ?><li <?php if($w=='rd') echo 'class="active"';?>><a href="admin.php?w=rd">DHCP</a></li><?php } ?>
                                    <?php if($pa['pxeServer'] and $reseller_id==0) { ?><li <?php if($w=='rp') echo 'class="active"';?>><a href="admin.php?w=rp">PXE</a></li><?php } ?>
                                    <?php if($pa['resellertemplates'] and $reseller_id==0) { ?><li <?php if($w=='ot') echo 'class="active"';?>><a href="admin.php?w=ot"><?php echo $gsprache->template;?></a></li><?php } ?>
                                    <?php if($reseller_id=='0' and $pa['trafficsettings']) { ?><li <?php if($w=='tf' and $d=='se') echo 'class="active"';?>><a href="admin.php?w=tf&amp;d=se"><?php echo $gsprache->traffic.' '.$gsprache->settings;?></a></li><?php } ?>
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