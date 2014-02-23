<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vd">TS3 DNS <?php echo $gsprache->mod;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $defaultdns; ?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <dl class="dl-horizontal">
        <dt><?php echo $sprache->defaultdns;?></dt>
        <dd><?php echo $defaultdns;?></dd>
    </dl>
</div>
<hr>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=vd&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="dns"><?php echo $sprache->dns;?></label>
                <div class="controls">
                    <input id="dns" type="text" name="dns" value="<?php echo $dns;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="ip"><?php echo $sprache->ip;?></label>
                <div class="controls">
                    <input id="ip" type="text" name="ip" value="<?php echo $ip;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="port"><?php echo $sprache->port;?></label>
                <div class="controls">
                    <input id="port" type="text" name="port" value="<?php echo $port;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>