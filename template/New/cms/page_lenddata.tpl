<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li><?php echo $page_data->pages['lendserver']['href'];?> <span class="divider">/</li>
            <li class="active"><?php if($servertype=='g'){ echo $page_data->pages['lendservergs']['linkname'];}else{ echo $page_data->pages['lendservervoice']['linkname'];}?></li>
        </ul>
    </div>
</div>
<div class="container">
<div class="row-fluid">
    <div class="span5">
        
        <?php if ($servertype=='g' and $gslallowed==true) { ?>
        <div class="alert alert-success" role="alert">
            <h2> Glückwunsch zu deinem <br>Gameserver !</h2><br>
        <?php if ($volallowed==true) { ?><?php } ?>
        <p><b><?php echo $gssprache->game.'</b> : '.$description;?></p>
        <p><b><?php echo $gssprache->server;?> : </b><a href="steam://connect/<?php echo $serverip.':'.$port.'/'.$password;?>">connect <?php echo $serverip.':'.$port.'; password '.$password;?></a></p>
        <p><b><?php echo $gssprache->slots.'</b> : '.$slots;?></p>
        <p><b><?php echo $sprache->timeleft.'</b> <div class="progress">
  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="'.$timeleft.'" aria-valuemin="0" aria-valuemax="'.$lendtime.'" style="width:"> '.$timeleft.'/'.$lendtime.' '.$sprache->minutes;?></div>
</div></p>
        <p><b><?php echo $gssprache->rcon.'</b> : '.$rcon;?></p>
        <p><b><?php echo $gssprache->password.'</b> : '.$password;?></p>
    </div></div><div hidden="">
        <?php echo $debug;?></div>

        <?php } else if ($volallowed==true) { ?>
         <div class="alert alert-success" role="alert">
            <h2> Glückwunsch zu deinem <br>Teamspeak Server!</h2><br>
        <h2 class="form-signin-heading"></h2>
        <?php if ($gslallowed==true) { ?><?php } ?>
        <p><b>Teamspeak Adresse</b> : <a href="ts3server://<?php echo $server.'?password='.$password;?>"><?php echo $server;?></a></p>
        <p><b><?php echo $gssprache->slots.'</b> : '.$slots;?></p>
        <?php $timer = 30   ; ?>
        <p><b><?php echo $sprache->timeleft.'</b> <div class="progress">
  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="'.$timeleft.'" aria-valuemin="0" aria-valuemax="'.$lendtime.'" style="width:"> '.$timeleft.'/'.$lendtime.' '.$sprache->minutes;?></div>
</div></p>
        <p><b> Admin Token </b> : <?php echo $rcon;?></p>
        <p><b><?php echo $gssprache->password.'</b> : '.$password;?></p>
        <?php } ?>
    </div>
</div></div></span>
<script type="text/javascript">
    $('.progress-bar').each(function() {
  var min = $(this).attr('aria-valuemin');
  var max = $(this).attr('aria-valuemax');
  var now = $(this).attr('aria-valuenow');
  var siz = (now-min)*100/(max-min);
  $(this).css('width', siz+'%');
});
</script>