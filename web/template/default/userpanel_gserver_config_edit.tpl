<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->config;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $serverip.':'.$port;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?php echo $sprache->config;?></th>
                <th><?php echo $sprache->easy;?></th>
                <th><?php echo $sprache->full;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($configs as $config){ ?>
            <tr>
                <td>
                    <?php echo $config['line'];?>
                </td>
                <td>
                    <?php if($config['permission']=="easy" or $config['permission']=="both") { ?>
                    <a href="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $id;?>&amp;type=easy&amp;config=<?php echo urlencode($config['line']);?>"><span class="btn btn-primary btn-mini"><i class="icon-edit icon-white"></i></span></a>
                    <?php } ?>
                </td>
                <td>
                    <?php if($config['permission']=="full" or $config['permission']=="both") { ?>
                    <a href="userpanel.php?w=gs&amp;d=cf&amp;id=<?php echo $id;?>&amp;type=full&amp;config=<?php echo urlencode($config['line']);?>"><span class="btn btn-primary btn-mini"><i class="icon-edit icon-white"></i></span></a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
