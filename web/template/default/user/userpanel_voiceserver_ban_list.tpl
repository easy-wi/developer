<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->banList;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $server;?></li>
        </ul>
    </div>
</div>
<hr>
<div class="row-fluid">
    <a href="userpanel.php?w=vo&amp;d=bl&amp;e=ad&amp;id=<?php echo $id;?>"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i> <?php echo $sprache->banAdd;?></span></a>
</div>
<br>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-condensed table-striped table-hover">
            <thead>
            <tr>
                <th><?php echo $sprache->user;?></th>
                <th><?php echo $sprache->ip;?></th>
                <th><?php echo $sprache->duration.' '.$sprache->seconds;?></th>
                <th><?php echo $sprache->ends;?></th>
                <th><?php echo $sprache->blocked;?></th>
                <th class="span1"> </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($banList as $k => $row) { ?>
            <tr>
                <td><?php echo $row['lastnickname'];?></td>
                <td><?php echo $row['ip'];?></td>
                <td><?php echo $row['duration'];?></td>
                <td><?php echo $row['ends'];?></td>
                <td><?php echo $row['blocked'];?></td>
                <td class="span1">
                    <form method="post" action="userpanel.php?w=vo&amp;d=bl&amp;id=<?php echo $id;?>&amp;r=vo" name="form" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                        <button class="btn btn-danger btn-mini" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
                        <input type="hidden" name="action" value="dl">
                        <input type="hidden" name="bannID" value="<?php echo $k;?>">
                    </form>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>