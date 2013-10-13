<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_data->pages['lendserver']['linkname'];?></li>
        </ul>
    </div>
</div>
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
                <td><?php if(strlen($v['runningGame'])>0){ echo '<img src="'.$page_data->pageurl.'/images/games/icons/'.$v['runningGame'].'.png"  width="18" /> ';}; echo $v['ip'].':'.$v['port'].' '.$v['queryName'];?></td>
                <td><?php echo $v['usedslots'].'/'.$v['slots'];?></td>
                <td><?php echo $v['queryMap'];?></td>
                <td><?php echo implode(', ',$v['games']);?></td>
                <td><?php if($v['timeleft']==0) echo $sprache->ready; else echo $v['timeleft'].' '.$sprache->minutes;?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
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
                <td><?php if(strlen($v['connect'])>0){ echo $v['connect'];}else{ echo $v['ip'].':'.$v['port'];}; echo ' '.$v['queryName'];?></td>
                <td><?php echo $v['usedslots'].'/'.$v['slots'];?></td>
                <td><?php if($v['timeleft']==0) echo $sprache->ready; else echo $v['timeleft'].' '.$sprache->minutes;?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>