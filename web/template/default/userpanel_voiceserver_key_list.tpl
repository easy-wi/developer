<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a> <span class="divider">/</span></li>
            <li class="active">Token</li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_voiceserver_key;?></div>
</div>
<hr>
<div class="row-fluid">
	<a href="userpanel.php?w=vo&amp;d=pk&amp;id=<?php echo $id;?>&amp;po=1"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i> <?php echo $sprache->token;?></span></a>
</div>
<br>
<div class="row-fluid">
    <div class="span8">
        <table class="table table-condensed table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th><?php echo $sprache->groupname;?></th>
                <th><?php echo $sprache->token;?></th>
                <th class="span1"> </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($pklist as $token) { ?>
            <tr>
                <td><?php echo $token['groupname'];?></td>
                <td><?php echo $token['token'];?></td>
                <td class="span1">
                    <form method="post" action="userpanel.php?w=vo&amp;d=pk&amp;id=<?php echo $id;?>&amp;r=vo" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">
                        <input type="hidden" name="token" value="<?php echo $token['token'];?>" >
                        <input type="hidden" name="action" value="dl" >
                        <button class="btn btn-danger btn-mini" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
                    </form>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>