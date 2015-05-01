<!DOCTYPE html>
<html dir="ltr" lang="<?php echo $page_data->language;?>">
<head>
    <?php if(isset($header)) echo $header; ?>
    <meta charset="utf-8">
    <title><?php echo $page_data->title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index,follow,noodp,noydir" />
    <meta name="description" content="">
    <meta name="author" content="2012 - <?php echo date('Y'); ?> <?php echo $page_data->title; ?>">

    <link rel="canonical" href="<?php echo $page_data->canurl;?>" />
    <link href="<?php echo $page_data->getDefaultUrl();?>" hreflang="x-default" rel="alternate">
    <?php foreach ($page_data->getLangLinks() as $l=>$v){ ?>
    <?php echo '<link href="'.$v.'" hreflang="'.$l.'" rel="alternate">'."\n"; ?>
    <?php }?>

    <link href="//netdna.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo $page_data->pageurl;?>/css/default/easy-wi.css" rel="stylesheet">
    <?php echo implode('',$htmlExtraInformation['css']);?>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" ></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>

    <script src="<?php echo $page_data->pageurl;?>/js/default/footable.js" type="text/javascript"></script>
    <script src="<?php echo $page_data->pageurl;?>/js/default/main.js" type="text/javascript"></script>
    <?php echo implode('',$htmlExtraInformation['js']);?>

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
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="nav-collapse collapse">
            <p class="navbar-text pull-left">
                &nbsp;&nbsp;
                <?php foreach ($page_data->getLangLinks() as $l=>$v) { echo '<a href="'.$v.'"><img src="'.$page_data->pageurl.'/images/flags/'.$l.'.png" alt="Flag: '.$l.'.png."></a>';}?>
                &nbsp;&nbsp;
            </p>

            <span class="navbar-text">Easy-WI.com</span>

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
            <?php } else { ?>
            <div id="modal" class="navbar-text navbar-form pull-right navbar-logout">
                <a href="#myModal" role="button" class="btn" data-toggle="modal">Login</a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php if (!isset($admin_id) and !isset($user_id)) { ?>
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo removeDoubleSlashes($page_data->pageurl.'/login.php');?>" method="post">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Login</h3>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <div class="span6">
                <div class="control-group">
                    <div class="controls">
                        <label class="control-label" for="inputUser"></label>
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-user"></i></span>
                            <input name="username" id="inputUser" type="text" class="input-block-level" placeholder="User/Email" required >
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <label class="control-label" for="inputPassword"></label>
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-lock"></i></span>
                            <input name="password" id="inputPassword" type="password" class="input-block-level" placeholder="Password" required >
                        </div>
                    </div>
                </div>
                <?php if ($ewCfg['captcha']==1) { ?>
                <div class="control-group">
                    <label class="control-label" for="inputCaptcha"></label>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><img src="images.php" alt="Captcha" /></span>
                            <input name="captcha" id="inputCaptcha" type="text" class="input-block-level" placeholder="Captcha" pattern="^[\w]{4}$" required >
                        </div>
                    </div>
                </div>
                <div class="hide">
                    <label><input type="text" name="email"></label>
                </div>
                <?php } ?>
                <div class="control-group">
                    <label class="control-label" for="inputLogin"></label>
                    <div class="controls">
                        <button id="inputLogin" class="btn btn-primary pull-left">Login</button>
                    </div>
                </div>
            </div>
            <div class="span6">
                <?php foreach($serviceProviders as $k=>$css){ ?>
                <a class="btn btn-block btn-social btn-<?php echo $css;?>" href="login.php?serviceProvider=<?php echo $k;?>">
                    <i class="fa fa-<?php echo $css;?>"></i> Sign in with <?php echo $k;?>
                </a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a class="btn pull-left btn-info" href="<?php echo $page_data->pages['register']['link'];?>"><?php echo $page_data->pages['register']['linkname'];?></a>
        <a class="btn pull-left" href="<?php echo $page_data->pageurl;?>/login.php?w=pr" >Lost PW</a>
    </div>
    </form>
</div>
<?php } ?>
<div class="container-fluid" id="content">
    <div class="row-fluid">
        <div class="span3">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header"><?php echo $gsprache->overview;?></li>
                    <li <?php if($s=='search') echo 'class="active"';?>><a href="<?php echo $page_data->pages['search']['link'];?>"><i class="fa fa-search fa-fw"></i> <?php echo $page_data->pages['search']['linkname'];?></a></li>
                    <li <?php if($s=='news') echo 'class="active"';?>><a href="<?php echo $page_data->pages['news']['link'];?>"><i class="fa fa-bullhorn fa-fw"></i> <?php echo $page_data->pages['news']['linkname'];?></a></li>
                    <li <?php if($s=='about') echo 'class="active"';?>><a href="<?php echo $page_data->pages['about']['link'];?>"><i class="fa fa-info-circle fa-fw"></i> <?php echo $page_data->pages['about']['linkname'];?></a></li>
                    <li <?php if($s=='gallery') echo 'class="active"';?>><a href="<?php echo $page_data->pages['gallery']['link'];?>"><i class="fa fa-picture-o fa-fw"></i> <?php echo $page_data->pages['gallery']['linkname'];?></a></li>
                    <?php if($easywiModules['ip']){ ?><li <?php if($s=='imprint') echo 'class="active"';?>><a href="<?php echo $page_data->pages['imprint']['link'];?>"><i class="fa fa-gavel fa-fw"></i> <?php echo $page_data->pages['imprint']['linkname'];?></a></li><?php }?>
                    <li <?php if($s=='contact') echo 'class="active"';?>><a href="<?php echo $page_data->pages['contact']['link'];?>"><i class="fa fa-envelope fa-fw"></i> <?php echo $page_data->pages['contact']['linkname'];?></a></li>
                    <li <?php if($s=='downloads') echo 'class="active"';?>><a href="<?php echo $page_data->pages['downloads']['link'];?>"><i class="fa fa-download fa-fw"></i> <?php echo $page_data->pages['downloads']['linkname'];?></a></li>
                    <?php if($page_data->protectioncheck=='Y'){ ?><li <?php if($s=='protectioncheck') echo 'class="active"';?>><a href="<?php echo $page_data->pages['protectioncheck']['link'];?>"><i class="fa fa-shield fa-fw"></i> <?php echo $page_data->pages['protectioncheck']['linkname'];?></a></li><?php } ?>
                    <li class="divider"></li>
                    <?php if($page_data->lendactive=='Y'){ ?>
                    <li class="nav-header"><?php echo $page_data->pages['lendserver']['linkname'];?></li>
                    <li <?php if($s=='lendserver' and !isset($servertype)) echo 'class="active"';?>><a href="<?php echo $page_data->pages['lendserver']['link'];?>"><i class="fa fa-list fa-fw"></i> <?php echo $page_data->pages['lendserver']['linkname'];?></a></li>
                    <?php if (isset($page_data->pages['lendservervoice'])) { ?><li <?php if(isset($servertype) and $servertype=='v') echo 'class="active"';?>><a href="<?php echo $page_data->pages['lendservervoice']['link'];?>"><i class="fa fa-microphone fa-fw"></i> <?php echo $page_data->pages['lendservervoice']['linkname'];?></a></li><?php } ?>
                    <?php if (isset($page_data->pages['lendservergs'])) { ?><li <?php if(isset($servertype) and $servertype=='g') echo 'class="active"';?>><a href="<?php echo $page_data->pages['lendservergs']['link'];?>"><i class="fa fa-gamepad fa-fw"></i> <?php echo $page_data->pages['lendservergs']['linkname'];?></a></li><?php } ?>
                    <li class="divider"></li>
                    <?php } ?>
                    <li class="nav-header"><?php echo $gsprache->pages;?></li>
                    <?php
function GetSubLinks($pagelist,$id,$sub=1){ global $page_id; $return='';if(isset($pagelist[$id])){foreach($pagelist[$id] as $k=>$sl){ if ($id!=$k){ $return.='<li'; if(isset($page_id) and $page_id==$k) $return.=' class="active"';  $return.='>';$return.=$sl['href'].'</li>';$return.=GetSubLinks($pagelist,$k,$sub+1);}}}return $return;}
foreach ($page_data->pages as $key=>$value){if(isid($key,'30')){ echo'<li'; if(isset($page_id) and $page_id==$key) echo ' class="active"';  echo '>'.$value[$key]['href'].'</li>';echo GetSubLinks($page_data->pages,$key);}}
                    ?>
                </ul>
            </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
            <?php if(isset($header)){ ?><div class="alert alert-block"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $text; ?></div><?php } ?>