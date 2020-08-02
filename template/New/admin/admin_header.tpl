<!DOCTYPE html>
<html dir="ltr" lang="<?php echo $user_language;?>"><head>
 <?php if(isset($header)) echo $header; ?>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php if(isset($ewCfg['title'])) echo $ewCfg['title']; ?></title>
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
    <?php echo implode('',$htmlExtraInformation['js']);?>
    <?php echo implode('',$htmlExtraInformation['css']);?>



</head>

<body id="page-top" <?php echo implode(' ',$htmlExtraInformation['body']);?>>

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo $rSA['header_href'];?>">
        <img src="images/<?php echo $rSA['header_icon'];?>" title="<?php echo $rSA['header_text'];?>" width="32">
        <div class="sidebar-brand-text "> <?php echo $rSA['header_text'];?></div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Pages Collapse Menu -->
      <li class=" <?php if(in_array($w,array('da','ho','ib','ip','lo','ml','sc')) or isset($customModules['ip'][$ui->smallletters('w',255,'get')])) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
          <i class="fa fa-home fa-fw"></i>
          <span>Home</span>
        </a>
        <div id="collapse" class="collapse <?php if(in_array($w,array('da','ho','ib','ip','lo','ml','sc')) or isset($customModules['ip'][$ui->smallletters('w',255,'get')])) echo 'show in';?>" aria-labelledby="heading" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Home</h6>
            <a <?php if($w=='da' or $w=='ho'){ echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=da"><i class="fa fa-dashboard"></i> Dashboard</a>

            <?php if($pa['ipBans']) { ?><a <?php if($ui->smallletters('w',255,'get')=='ib') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ib"><i class="fa fa-ban"></i> IP Bans</a><?php } ?>

            <?php if($pa['log']) { ?>

            <a <?php if($ui->smallletters('w',255,'get')=='lo') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=lo"><i class="fa fa-list-alt"></i> <?php echo $gsprache->logs;?></a>

            <a <?php if($ui->smallletters('w',255,'get')=='ml')  {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ml"><i class="fa fa-envelope"></i> Mail <?php echo $gsprache->logs;?></a>
             <?php } ?>
             <?php if($easywiModules['ip']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='ip') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ip"><i class="fa fa-legal"></i> <?php echo $gsprache->imprint;?></a><?php }?>
            <?php if($pa['settings'] and $reseller_id==0) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='sc') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=sc"><i class="fa fa-heartbeat"></i> <?php echo $gsprache->system_check;?></a>
            <?php } ?>

          </div>
        </div>
      </li>
      <?php if($pa['settings']) { ?>
      <li class=" <?php if(in_array($w,array('se','sm','si','vc','cc','mo','bu'))) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities1" aria-expanded="false" aria-controls="collapseUtilities1">
         <i class="fa fa-wrench"></i>
        <span><?php echo $gsprache->settings;?></span>
        </a>
        <div id="collapseUtilities1" class="collapse <?php if(in_array($w,array('se','sm','si','vc','cc','mo','bu'))) echo 'show in';?>" aria-labelledby="headingUtilities1" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->settings;?></h6>
            <a <?php if($ui->smallletters('w',255,'get')=='se') {echo 'class="active';} else {echo 'class="';}?> collapse-item"  href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='sm') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=sm"><i class="fa fa-envelope"></i> E-Mail <?php echo $gsprache->settings;?></a>
             <?php if($easywiModules['ip']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='si') {echo 'class="active';} else {echo 'class="';}?> collapse-item"  href="admin.php?w=si"><i class="fa fa-legal"></i> <?php echo $gsprache->imprint.' '.$gsprache->settings;?></a>
            <?php }?>
            <?php if($pa['root'] and $reseller_id==0) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='vc') {echo 'class="active';} else {echo 'class="';}?> collapse-item"  href="admin.php?w=vc"><i class="fa fa-check"></i> <?php echo $gsprache->versioncheck;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='cc') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=cc"><i class="fa fa-list"></i> <?php echo $gsprache->columns;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='mo') {echo 'class="active';} else {echo 'class="';}?> collapse-item"  href="admin.php?w=mo"><i class="fa fa-th-large"></i> <?php echo $gsprache->modules;?></a>
            <?php } ?>
            <?php if($pa['root']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='bu') {echo 'class="active';} else {echo 'class="';}?> collapse-item"  href="admin.php?w=bu"><i class="fa fa-database"></i> <?php echo $gsprache->databases;?></a>
             <?php } ?>
          </div>
        </div>
      </li>
       <?php } ?>

       <?php if($pa['jobs'] or $pa['apiSettings']) { ?>
      <li class="<?php if(in_array($w,array('jb','ap','aa','ui'))) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities2" aria-expanded="false" aria-controls="collapseUtilities2">
           <i class="fa fa-cloud fa-fw"></i>
            <span><?php echo $gsprache->jobs.'/'.$gsprache->api;?></span>
        </a>
        <div id="collapseUtilities2" class="collapse <?php if(in_array($w,array('jb','ap','aa','ui'))) echo 'show in';?>" aria-labelledby="headingUtilities2" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->jobs.'/'.$gsprache->api;?></h6>
             <?php if($pa['jobs']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='jb') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=jb"><i class="fa fa-tasks"></i> <?php echo $gsprache->jobs.' '.$gsprache->overview;?></a>
            <?php } ?>

            <?php if($pa['apiSettings']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='ap') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ap"><i class="fa fa-wrench"></i> <?php echo $gsprache->api.' '.$gsprache->settings;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='aa') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=aa"><i class="fa fa-cloud-download"></i> <?php echo $gsprache->apiAuth;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='ui'){echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ui"><i class="fa fa-download"></i> <?php echo $gsprache->userImport;?>r</a>
            <?php }?>
          </div>
        </div>
      </li>
       <?php } ?>

        <?php if($pa['feeds']) { ?>

            <li class="<?php if(in_array($w,array('fn','fe'))) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities3" aria-expanded="false" aria-controls="collapseUtilities3">
          <i class="fa fa-rss fa-fw"></i>
                            <span><?php echo $gsprache->feeds;?></span>
        </a>
        <div id="collapseUtilities3" class="collapse <?php if(in_array($w,array('fn','fe'))) echo 'show in';?>" aria-labelledby="headingUtilities3" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->feeds;?></h6>
            <a <?php if($ui->smallletters('w',255,'get')=='fn') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=fn"><i class="fa fa-rss"></i> <?php echo $gsprache->news;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='fe' and !in_array($d,array('ad','se'))) {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=fe"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='fe' and $d=='se') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=fe&amp;d=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a>
          </div>
        </div>
      </li>
      <?php } ?>


      <?php if($easywiModules['pn'] and $reseller_id==0 and ($pa['cms_settings'] or $pa['cms_pages'] or $pa['cms_news'])) { ?>

            <li class="<?php if(in_array($w,array('pn','pc','pp','pd','ps')) or isset($customModules['pa'][$ui->smallletters('w',255,'get')])) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities4" aria-expanded="false" aria-controls="collapseUtilities4">
          <i class="fa fa-globe fa-fw"></i>
                            <span>CMS</span>
        </a>
        <div id="collapseUtilities4" class="collapse  <?php if(in_array($w,array('pn','pc','pp','pd','ps')) or isset($customModules['pa'][$ui->smallletters('w',255,'get')])) echo 'show in';?>" aria-labelledby="headingUtilities4" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">CMS</h6>
            <?php if($pa['cms_news']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='pn') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=pn"><i class="fa fa-newspaper-o"></i> <?php echo $gsprache->news;?></a></a>
            <a <?php if($ui->smallletters('w',255,'get')=='pc') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=pc"><i class="fa fa-comments"></i> <?php echo $gsprache->comments;?></a>
            <?php } ?>
            <?php if($pa['cms_pages']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='pp') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=pp"><i class="fa fa-copy"></i> <?php echo $gsprache->pages;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='pd') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=pd"><i class="fa fa-download"></i> <?php echo $gsprache->downloads;?></a>
            <?php } ?>

            <?php if($pa['cms_settings']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='ps') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ps"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a>
            <?php } ?>

            <?php foreach ($customModules['pa'] as $k => $v) { echo '<a '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="collapse-item"' : ''; echo ' href="admin.php?w='.$k.'">'.$v.'</a>'; }; ?>
          </div>
        </div>
      </li>
<?php } ?>


<?php if($easywiModules['ws'] and ($pa['webvhost'] or $pa['webmaster'])) { ?>

            <li class="<?php if(in_array($w,array('wv','wm')) or isset($customModules['ws'][$ui->smallletters('w',255,'get')])) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities5" aria-expanded="false" aria-controls="collapseUtilities5">
         <i class="fa fa-cubes fa-fw"></i>
                            <span><?php echo $gsprache->webspace;?></span>
        </a>
        <div id="collapseUtilities5" class="collapse <?php if(in_array($w,array('wv','wm')) or isset($customModules['ws'][$ui->smallletters('w',255,'get')])) echo 'show in';?>" aria-labelledby="headingUtilities5" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->webspace;?></h6>
            <a <?php if($ui->smallletters('w',255,'get')=='wv') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=wv"><i class="fa fa-columns"></i> Vhosts <?php echo $gsprache->overview;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='wm') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=wm"><i class="fa fa-server"></i> <?php echo $gsprache->master;?></a>
            

            <?php foreach ($customModules['ws'] as $k => $v) { echo '<a '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="collapse-item"' : ''; echo ' href="admin.php?w='.$k.'">'.$v.'</a>'; }; ?>
          </div>
        </div>
      </li>

<?php } ?>
<?php if($easywiModules['my'] and ($pa['mysql_settings'] or $pa['mysql'])) { ?>

            <li class="<?php if(in_array($w,array('my','md')) or isset($customModules['my'][$ui->smallletters('w',255,'get')])) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities6" aria-expanded="false" aria-controls="collapseUtilities6">
          <i class="fa fa-database fa-fw"></i>
                            <span>MySQL</span>
        </a>
        <div id="collapseUtilities6" class="collapse  <?php if(in_array($w,array('my','md')) or isset($customModules['my'][$ui->smallletters('w',255,'get')])) echo 'show in';?>" aria-labelledby="headingUtilities6" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">MySQL</h6>
            <?php if($pa['mysql']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='md') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=md"><i class="fa fa-columns"></i> <?php echo $gsprache->databases.' '.$gsprache->overview;?></a>
             <?php } ?>

                            <?php if($pa['mysql_settings']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='my') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=my"><i class="fa fa-server"></i> <?php echo $gsprache->master;?></a>
             <?php } ?>

            <?php foreach ($customModules['my'] as $k => $v) { echo '<a '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="collapse-item"' : ''; echo ' href="admin.php?w='.$k.'">'.$v.'</a>'; }; ?>

          </div>
        </div>
      </li>
<?php } ?>

<?php if($pa['user'] or $pa['user_users'] or $pa['userGroups'] ) { ?>

            <li class="<?php if(in_array($w,array('us','ug','up')) or isset($customModules['us'][$ui->smallletters('w',255,'get')])) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities7" aria-expanded="false" aria-controls="collapseUtilities7">
          <i class="fa fa-users fa-fw"></i>
                            <span><?php echo $gsprache->user;?></span>
        </a>
        <div id="collapseUtilities7" class="collapse  <?php if(in_array($w,array('us','ug','up')) or isset($customModules['us'][$ui->smallletters('w',255,'get')])) echo 'show in';?>" aria-labelledby="headingUtilities7" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->user;?></h6>
            <?php if($pa['user'] or $pa['user_users']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='us') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=us"><i class="fa fa-columns"></i> <?php echo $gsprache->user.' '.$gsprache->overview;?></a>
            <?php } ?>

                            <?php if($pa['userGroups']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='ug') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ug"><i class="fa fa-group"></i> <?php echo $gsprache->groups;?></a>
            <?php } ?>
                            <?php if($pa['root'] and $reseller_id==0) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='up') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=up"><i class="fa fa-cloud"></i> Social Auth Provider</a>
            <?php } ?>

            <?php foreach ($customModules['us'] as $k => $v) { echo '<a '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="collapse-item"' : ''; echo ' href="admin.php?w='.$k.'">'.$v.'</a>'; }; ?>
          </div>
        </div>
      </li>
 <?php } ?>
<?php if($easywiModules['gs'] and ($pa['gserver'] or $pa['addons'] or $pa['gimages'] or $pa['eac'] or $pa['masterServer']) and $easywiModules['gs']) { ?>


            <li class=" <?php if(in_array($w,array('gs','im','ad','gt','ea')) or isset($customModules['gs'][$ui->smallletters('w',255,'get')])) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities8" aria-expanded="false" aria-controls="collapseUtilities8">
           <i class="fa fa-gamepad fa-fw"></i>
                            <span><?php echo $gsprache->gameserver;?></span>
        </a>
        <div id="collapseUtilities8" class="collapse   <?php if(in_array($w,array('gs','im','ad','gt','ea')) or isset($customModules['gs'][$ui->smallletters('w',255,'get')])) echo 'show in';?>" aria-labelledby="headingUtilities8" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->gameserver;?></h6>
            <?php if($pa['gserver']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='gs') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=gs"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
            <?php } ?>
                            <?php if($pa['gimages']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='im') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=im"><i class="fa fa-file-text-o"></i> <?php echo $gsprache->gameserver.' '.$gsprache->template;?></a>
             <?php } ?>
                            <?php if($pa['addons']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='ad') {echo 'class="active';} else {echo 'class="';}?> collapse-item"  href="admin.php?w=ad"><i class="fa fa-gears"></i> <?php echo $gsprache->addon;?></a>
            <?php } ?>
                            <?php if($pa['gserver']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='gt') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=gt"><i class="fa fa-floppy-o"></i> <?php echo $gsprache->file.' '.$gsprache->template;?></a>
            <?php } ?>
                            <?php if($easywiModules['ea'] and $pa['eac']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='ea') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ea"><i class="fa fa-eye"></i> Easy Anti Cheat</a>
            <?php } ?>
            <?php foreach ($customModules['gs'] as $k => $v) { echo '<a '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="collapse-item"' : ''; echo ' href="admin.php?w='.$k.'">'.$v.'</a>'; }; ?>

          </div>
        </div>
      </li>
<?php } ?>


<?php if($easywiModules['gs'] and ($pa['roots'] or $pa['masterServer'])) { ?>
            <li class="<?php if(in_array($w,array('ro','ma'))) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities9" aria-expanded="false" aria-controls="collapseUtilities9">
           <i class="fa fa-hdd-o fa-fw"></i>
                            <span><?php echo $gsprache->appRoot;?></span>
        </a>
        <div id="collapseUtilities9" class="collapse <?php if(in_array($w,array('ro','ma'))) echo 'show in';?>" aria-labelledby="headingUtilities9" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->appRoot;?></h6>
            <?php if($pa['roots']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='ro') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ro"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
            <?php } ?>
                            <?php if($pa['masterServer']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='ma' and $d!='ud') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ma&amp;d=md"><i class="fa fa-puzzle-piece"></i> <?php echo $gsprache->master_apps;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='ma' and $d=='ud') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ma&amp;d=ud"><i class="fa fa-spinner"></i> <?php echo $gsprache->master_apps.' '.$gsprache->update;?></a>
             <?php } ?>
          </div>
        </div>
      </li>
      <?php } ?>

<?php if($easywiModules['vo'] and ($pa['voicemasterserver'] or $pa['voiceserver'] or $pa['voiceserverStats'] or $pa['voiceserverSettings'])) { ?>



            <li class="<?php if(in_array($w,array('vo','vm','vr','vd','vu')) or isset($customModules['vo'][$ui->smallletters('w',255,'get')])) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities10" aria-expanded="false" aria-controls="collapseUtilities10">
          <i class="fa fa-microphone fa-fw"></i>
                            <span><?php echo $gsprache->voiceserver;?></span>
        </a>
        <div id="collapseUtilities10" class="collapse  <?php if(in_array($w,array('vo','vm','vr','vd','vu')) or isset($customModules['vo'][$ui->smallletters('w',255,'get')])) echo 'show in';?>" aria-labelledby="headingUtilities10" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->voiceserver;?></h6>
            <?php if($pa['voiceserver']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='vo') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=vo"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
             <?php } ?>
                            <?php if($pa['voicemasterserver']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='vm') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=vm"><i class="fa fa-server"></i> <?php echo $gsprache->master;?></a>
             <?php } ?>
                            <?php if($pa['voiceserver']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='vr') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=vr"><i class="fa fa-columns"></i> TSDNS <?php echo $gsprache->overview;?></a>
            <?php } ?>
                            <?php if($pa['voicemasterserver']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='vd') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=vd"><i class="fa fa-server"></i> TSDNS <?php echo $gsprache->master;?></a>
            <?php } ?>
                            <?php if($pa['voiceserverStats']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='vu') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=vu"><i class="fa fa-area-chart"></i> <?php echo $gsprache->stats;?></a>
            <?php } ?>
            <?php foreach ($customModules['vo'] as $k => $v) { echo '<li '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="active"' : ''; echo '><a href="admin.php?w='.$k.'">'.$v.'</a></li>'; }; ?>
          </div>
        </div>
      </li>
<?php } ?>

 <?php if($easywiModules['le'] and (($easywiModules['gs'] or $easywiModules['vo']) and ($pa['lendserver'] or $pa['lendserverSettings']))) { ?>

            <li class="<?php if(in_array($w,array('le'))) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities11" aria-expanded="false" aria-controls="collapseUtilities11">
          <i class="fa fa-flask fa-fw"></i>
                            <span><?php echo $gsprache->lendserver;?></span>
        </a>
        <div id="collapseUtilities11" class="collapse <?php if(in_array($w,array('le'))) echo 'show in';?>" aria-labelledby="headingUtilities11" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->lendserver;?></h6>
            <?php if($pa['lendserver']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='le' and $d!='se') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=le"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
            <?php } ?>
                            <?php if($pa['lendserverSettings']) { ?>
            <a <?php if($ui->smallletters('w',255,'get')=='le' and $d=='se') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=le&amp;d=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a>
             <?php } ?>
          </div>
        </div>
      </li>
       <?php } ?>


<?php if($easywiModules['ti'] and $pa['usertickets'] and $reseller_id!=0) { ?>


            <li class="<?php if($w=='tr' or isset($customModules['tr'][$ui->smallletters('w',255,'get')])) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities12" aria-expanded="false" aria-controls="collapseUtilities12">
          <i class="fa fa-life-ring fa-fw"></i>
                            <span><?php echo $gsprache->reseller.' '.$gsprache->support;?></span>
        </a>
        <div id="collapseUtilities12" class="collapse <?php if($w=='tr' or isset($customModules['tr'][$ui->smallletters('w',255,'get')])) echo 'show in';?>" aria-labelledby="headingUtilities12" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->reseller.' '.$gsprache->support;?></h6>
            <a <?php if($ui->smallletters('w',255,'get')=='tr' and $ui->smallletters('d',255,'get')!='ad'){echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=tr"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='tr' and $ui->smallletters('d',255,'get')=='ad') {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=tr&amp;d=ad"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->support2;?></a>
          </div>
        </div>
      </li>

<?php } ?>
<?php if($easywiModules['ti'] and $pa['tickets']) { ?>

            <li class="<?php if($w=='ti' or isset($customModules['ti'][$ui->smallletters('w',255,'get')])) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities13" aria-expanded="false" aria-controls="collapseUtilities13">
          <i class="fa fa-life-ring fa-fw"></i>
                            <span><?php echo $gsprache->support;?></span>
        </a>
        <div id="collapseUtilities13" class="collapse <?php if($w=='ti' or isset($customModules['ti'][$ui->smallletters('w',255,'get')])) echo 'show in';?>" aria-labelledby="headingUtilities13" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->support;?></h6>
            <a <?php if($ui->smallletters('w',255,'get')=='ti' and !in_array($d,array('at','mt','dt'))) {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ti"><i class="fa fa-columns"></i> <?php echo $gsprache->overview;?></a>
            <a <?php if($ui->smallletters('w',255,'get')=='ti' and in_array($d,array('at','mt','dt'))) {echo 'class="active';} else {echo 'class="';}?> collapse-item" href="admin.php?w=ti&amp;d=mt"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a>
            
             <?php foreach ($customModules['ti'] as $k => $v) { echo '<a '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="collapse-item"' : ''; echo ' href="admin.php?w='.$k.'">'.$v.'</a>'; }; ?>
          </div>
        </div>
      </li>

<?php } ?>
<?php if(count($customModules['mo'])>0) { ?>


      <li class="<?php if(isset($customModules['mo'][$ui->smallletters('w',255,'get')])) echo 'active';?> nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities16" aria-expanded="false" aria-controls="collapseUtilities16">
          <i class="fa fa-tasks fa-fw"></i>
                            <span><?php echo $gsprache->modules;?></span>
        </a>
        <div id="collapseUtilities16" class="collapse <?php if(isset($customModules['mo'][$ui->smallletters('w',255,'get')])) echo 'show in';?>" aria-labelledby="headingUtilities16" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header"><?php echo $gsprache->modules;?></h6>
            <?php foreach ($customModules['mo'] as $k => $v) { echo '<a '; echo ($ui->smallletters('w',255,'get')==$k) ? 'class="collapse-item"' : ''; echo ' href="admin.php?w='.$k.'">'.$v.'</a>'; }; ?>

          </div>
        </div>
      </li>
       <?php } ?>


      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

      <!-- Divider -->
      <hr class="sidebar-divider">
    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars" aria-hidden="false"></i>
          </button>
			<?php if(isset($easywitweets) && $easywitweets) { ?>
			 <a href="https://twitter.com/easy_wi?ref_src=twsrc%5Etfw" class="twitter-follow-button"  data-show-count="false">Follow @easy_wi</a>
             <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
			<?php } ?>
          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">


    <?php if($statsArray['ticketsTotal']>0){ ?>
    <div class="float-right">
        <a style="color:white;" class="badge badge-success" href="admin.php?w=ti">
            <i class="fa fa-life-ring"></i>
            <span class="label"><?php echo $statsArray['ticketsTotal'];?></span>
        </a>
    </div>
    <div style="width: 10px"></div>
<?php }?>

           
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
          <?php if($pa['gserver'] and $easywiModules['gs']) { ?>
          <?php if($statsArray['gameserverNotRunning']>0){ ?><a class="dropdown-item" href="admin.php?w=gs"><i class="fa fa-warning danger"></i> <?php echo $statsArray['gameserverNotRunning'].' '.$sprache_bad->gserver_crashed;?></a><?php }?>
          <?php if($statsArray['gameserverNoPassword']>0){ ?><a class="dropdown-item" href="admin.php?w=gs"><i class="fa fa-warning warning"></i> <?php echo $statsArray['gameserverNoPassword'].' '.$sprache_bad->gserver_removed;?></a><?php }?>
           <?php if($statsArray['gameserverNoTag']>0){ ?><a class="dropdown-item" href="admin.php?w=gs"><i class="fa fa-warning warning"></i> <?php echo $statsArray['gameserverNoTag'].' '.$sprache_bad->gserver_tag_removed;?></a><?php }?>
          <?php }?>
            <?php if($pa['voiceserver'] and $statsArray['voiceserverCrashed']>0 and $easywiModules['vo']) { ?><a class="dropdown-item" href="admin.php?w=vo"><i class="fa fa-warning danger"></i> <?php echo $statsArray['voiceserverCrashed'].' '.$sprache_bad->voice_crashed;?></a><?php }?>

            <?php if($pa['voicemasterserver'] and $statsArray['voiceMasterCrashed']>0 and $easywiModules['vo']) { ?><a class="dropdown-item" href="admin.php?w=vm"><i class="fa fa-warning danger"></i> <?php echo $statsArray['voiceMasterCrashed'].' '.$sprache_bad->ts3master_crashed;?></a><?php }?>

            <?php if($pa['roots'] and $statsArray['gameMasterCrashed']>0 and $easywiModules['gs']) { ?><a class="dropdown-item" href="admin.php?w=ro"><i class="fa fa-warning danger"></i> <?php echo $statsArray['gameMasterCrashed'].' '.$sprache_bad->ts3master_crashed;?></a><?php }?>




 
        </div>
    </li>
</ul>
</div>
</div>
              
                    <?php } ?>

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $great_user;?></span>
                <img class="img-profile rounded-circle" src="images/Places-user-identity-icon.png" class="img-circle" alt="User Image">
              </a>
              <!-- Dropdown - User Information -->
              <div  class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#"><?php echo $gsprache->last.'<br />'.$great_last;?></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="admin.php?w=su">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <a class="dropdown-item" href="/index.php">
                    <i class="fa fa-sign-in fa-sm fa-fw mr-2 text-gray-400"></i>
                  Frontend
                </a>
                <a class="dropdown-item" href="admin.php?w=su">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  <?php echo $gsprache->settings;?>
                </a>
                <a class="dropdown-item" href="admin.php?w=su&amp;d=pw">
                  <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                  <?php echo $gsprache->password." ".$gsprache->change;?>
                </a>
				 <div class="dropdown-divider"></div>
				<a class="dropdown-item" href="https://easy-wi.com" target="_blank">
					<i class="fa fa-home fa-fw mr-2 text-gray-400"></i> Easy-WI Homepage
				</a>
     			<a class="dropdown-item" href="https://discord.gg/quJvvfF" target="_blank">
					<i class="fab fa-discord fa-fw mr-2 text-gray-400"></i> Easy-WI @ Discord
				</a>
				<a class="dropdown-item" href="https://twitter.com/easy_wi" target="_blank">
					<i class="fa fa-twitter fa-fw mr-2 text-gray-400"></i> Easy-WI @ Twitter
				</a>  
				<a class="dropdown-item" href="https://github.com/easy-wi/developer" target="_blank">
					<i class="fa fa-github fa-fw mr-2 text-gray-400"></i> Easy-WI @ Github
				</a>                        
				<a class="dropdown-item" href="https://steamcommunity.com/groups/easywi" target="_blank">
					<i class="fa fa-steam fa-fw mr-2 text-gray-400"></i> Easy-WI @ Steam
				</a>
				  
				 <div class="dropdown-divider"></div> 
                <a class="dropdown-item ">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>Languages:<div class="text-center">
                    <?php foreach ($languages as $language){ echo '<a  href="admin.php?l='.$language.'"><img src="images/flags/'.$language.'.png" alt="Flag: '.$language.'.png."></a> ';} ?>
                </a></div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="login.php?w=lo">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->





            <?php if(isset($header)){ ?>
            <section class="content" style="min-height: 0px;">
              <div class="row">
                <div class="col-md-12">
                  <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="false">×</button>
                    <h4>
                      <i class="icon fa fa-info"></i> Info!
                    </h4>
                    <p><?php echo $text;?></p>
                  </div>
                </div>
              </div>
            </section>
            <?php } ?>
            <?php if(isset($toooldversion)){ ?>
            <section class="content" style="min-height: 0px;"> 
              <div class="row">
                <div class="col-md-12">
                  <div class="alert alert-info alert-dismissable">
                    <h4>
                      <i class="icon fa fa-info"></i> Info!
                    </h4>
                    <p>
                      <?php echo $toooldversion;?>
                      <br/>
                      <br/>
                      <a href="admin.php?w=vc" class="btn btn-success btn-sm">
                        <i class="fa fa-cloud-download"></i> Update
                      </a>
                    </p>
                  </div>
                </div>
              </div>
            </section>
            <?php } ?>
            <?php if($rSA['lastCronWarnStatus']=='Y' and (time()-$rSA['lastCronStatus'])>600 and $reseller_id==0){ ?>
            <div class="row">
              <div class="col-md-12">
                <div class="alert alert-danger">
                  <p>Cronjob: statuscheck.php</div>
                  </div>
                </div>
                <?php }?>
            <?php if($rSA['lastCronWarnReboot']=='Y' and (time()-$rSA['lastCronReboot'])>5400 and $reseller_id==0){ ?>
            <div class="row">
              <div class="col-md-12">
                <div class="alert alert-danger">
                  <p>Cronjob: reboot.php
                  </div>
                </div>
              </div>
              <?php }?>
            <?php if($rSA['lastCronWarnUpdates']=='Y' and (time()-$rSA['lastCronUpdates'])>300 and $reseller_id==0){ ?>
            <div class="row">
              <div class="col-md-12">
                <div class="alert alert-danger">
                  <p>Cronjob: startupdates.php
                  </div>
                </div>
              </div>
              <?php }?>
            <?php if($rSA['lastCronWarnJobs']=='Y' and (time()-$rSA['lastCronJobs'])>300 and $reseller_id==0){ ?>
            <div class="row">
              <div class="col-md-12">
                <div class="alert alert-danger">
                  <p>Cronjob: jobs.php
                  </div>
                </div>
              </div>
              <?php }?>
            <?php if($rSA['lastCronWarnCloud']=='Y' and (time()-$rSA['lastCronCloud'])>1200 and $reseller_id==0){ ?>
            <div class="row">
              <div class="col-md-12">
                <div class="alert alert-danger">
                  <p>Cronjob: cloud.php
                  </div>
                </div>
              </div>
              <?php }?>
            </p>



            <section style="padding-left: 3%; max-width: 95%">
