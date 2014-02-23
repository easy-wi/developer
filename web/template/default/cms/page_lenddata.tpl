<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li><?php echo $page_data->pages['lendserver']['href'];?> <span class="divider">/</li>
            <li class="active"><?php if($servertype=='g'){ echo $page_data->pages['lendservergs']['linkname'];}else{ echo $page_data->pages['lendservervoice']['linkname'];}?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span5">
        <?php if ($servertype=='g' and $gslallowed==true) { ?>
        <h2 class="form-signin-heading"><?php echo $gsprache->lendserver.' '.$gsprache->gameserver; ?></h2>
        <?php if ($volallowed==true) { ?><h3><a href="lend.php?w=vo"><?php echo $gsprache->voiceserver;?></a></h3><?php } ?>
        <p><?php echo $gssprache->game.' '.$description;?></p>
        <p><?php echo $gssprache->server;?> <a href="steam://connect/<?php echo $serverip.':'.$port.'/'.$password;?>">connect <?php echo $serverip.':'.$port.'; password '.$password;?></a></p>
        <p><?php echo $gssprache->slots.' '.$slots;?></p>
        <p><?php echo $sprache->timeleft.' '.$timeleft.'/'.$lendtime.' '.$sprache->minutes;?></p>
        <p><?php echo $gssprache->rcon.' '.$rcon;?></p>
        <p><?php echo $gssprache->password.' '.$password;?></p>
        <?php } else if ($volallowed==true) { ?>
        <h2 class="form-signin-heading"><?php echo $gsprache->lendserver.' '.$gsprache->voiceserver; ?></h2>
        <?php if ($gslallowed==true) { ?><h3 class="middle"><a href="lend.php?w=gs"><?php echo $gsprache->gameserver;?></a></h3><?php } ?>
        <p><?php echo $gssprache->server;?> <a href="ts3server://<?php echo $server.'?password='.$password;?>"><?php echo $server;?></a></p>
        <p><?php echo $gssprache->slots.' '.$slots;?></p>
        <p><?php echo $sprache->timeleft.' '.$timeleft.'/'.$lendtime.' '.$sprache->minutes;?></p>
        <p>Token <?php echo $rcon;?></p>
        <p><?php echo $gssprache->password.' '.$password;?></p>
        <?php } ?>
    </div>
</div>