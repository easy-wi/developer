<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=my">MySQL <?php echo $gsprache->databases;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $dbname.' ('.$ip.' )';?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=my&amp;id=<?php echo $id;?>&amp;r=my" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="row-fluid">
                <dl class="dl-horizontal">
                    <dt>IP</dt>
                    <dd><?php echo $ip;?></dd>
                    <dt>Port</dt>
                    <dd><?php echo $port;?></dd>
                    <dt><?php echo $sprache->user;?></dt>
                    <dd><?php echo $dbname;?></dd>
                    <dt><?php echo $sprache->dbname;?></dt>
                    <dd><?php echo $dbname;?></dd>
                    <dt><?php echo $sprache->interface;?></dt>
                    <dd><a href="<?php echo $interface;?>" target="_blank"><?php echo $interface;?></a></dd>
                </dl>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDescription"><?php echo $sprache->description;?></label>
                <div class="controls">
                    <input class="span12" id=inputDescription type="text" name="description" value="<?php echo $description;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password"><?php echo $sprache->password;?></label>
                <div class="controls">
                    <input class="span12" id="password" type="text" name="password" value="<?php echo $password;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="ips"><?php echo $sprache->ips;?></label>
                <div class="controls">
                    <textarea class="span12" id="ips" name="ips" rows="5"><?php echo $ips?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="md">
                </div>
            </div>
        </form>
    </div>
</div>