<div id="leihen" class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_data->pages['lendserver']['linkname'];?></li>
        </ul>
    </div>
</div><div class="container">
<div class="row-fluid">
    <div class="span12">
        <h2><?php echo $gsprache->gameserver; ?></h2>
        <table class="table table-striped table-bordered table-hover footable">
            <thead>
            <tr>
                <th data-class="expand"><?php echo $gssprache->servername;?></th>
                <th data-hide="phone,tablet"><?php echo $gssprache->slots;?></th>
                <th data-hide="phone,tablet"><?php echo $gssprache->map;?></th>
                <th data-hide="phone,tablet"><?php echo $gssprache->games;?></th>
                <th><?php echo $sprache->free;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($lendGameServers as $v){ ?>
            <tr>
                <td><?php if(strlen($v['runningGame'])>0){ echo '<img src="'.$page_data->pageurl.'/images/games/icons/'.$v['runningGame'].'.png"  width="18" /> ';}; echo $v['ip'].':'.$v['port'].' <br> '.$v['queryName'];?></td>
                <td><?php echo $v['usedslots'].'/'.$v['slots'];?></td>
                <td><?php echo $v['queryMap'];?></td>
                <td><?php echo implode(',<br> ',$v['games']);?></td>
                <td><?php if($v['timeleft']==0) echo $sprache->ready; else echo $v['timeleft'].' '.$sprache->minutes;?></td>
            </tr>
            <?php } ?>
            
            <div class="alert alert-success" role="alert">Hi there! Here you can rent / borrow one of our Test GameServer free of charge.
</div>
            </tbody>
        </table>
        <a style="text-decoration: none;" href="/index.php?site=lendserver&d=gs"><b><span class="fas fa-gamepad"> start now!</span></a></b><hr> <br><br>
        <h2><?php echo $gsprache->voiceserver; ?></h2>
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th data-class="expand"><?php echo $gssprache->servername;?></th>
                <th data-hide="phone,tablet"><?php echo $gssprache->slots;?></th>
                <th><?php echo $sprache->free;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($lendVoiceServers as $v){ ?>
            <tr>
                <td><?php if(strlen($v['connect'])>0){ echo $v['connect'];}else{ echo $v['ip'].':'.$v['port'];}; echo ' My Voiceserver';?></td>
                <td><?php echo $v['usedslots'].'/'.$v['slots'];?></td>
                <td><?php if($v['timeleft']==0) echo $sprache->ready; else echo $v['timeleft'].' '.$sprache->minutes;?></td>
            </tr>
            <?php } ?>
             <div class="alert alert-success" role="alert">Here you can rent / borrow a Teamspeak server.  <b> <br>Have fun!</b><br><br>
</div>
            </tbody>
        </table> 
    </div><a style="text-decoration: none;" href="/index.php?site=lendserver&d=vo"><b><span class="fab fa-teamspeak"> start now!</span></b></a>
</div></div>