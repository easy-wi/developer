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
                    <?php if(strlen($interface)>0){ ?>
                    <dt><?php echo $sprache->interface;?></dt>
                    <dd><a href="<?php echo $interface;?>" target="_blank"><?php echo $interface;?></a></dd>
                    <?php } ?>
                    <?php if($manage_host_table == 'N'){ ?>
                    <dt><?php echo $sprache->ips;?></dt>
                    <dd><?php echo $ips;?></dd>
                    <?php } ?>
                </dl>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDescription"><?php echo $sprache->description;?></label>
                <div class="controls">
                    <input id=inputDescription type="text" name="description" value="<?php echo $description;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password"><?php echo $sprache->password;?></label>
                <div class="controls">
                    <input id="password" type="text" name="password" value="<?php echo $password;?>">
                </div>
            </div>
            <?php if($manage_host_table == 'Y'){ ?>
            <div class="control-group">
                <label class="control-label" for="ips"><?php echo $sprache->ips;?></label>
                <div class="controls">
                    <textarea id="ips" name="ips" rows="5"><?php echo $ips?></textarea>
                </div>
            </div>
            <?php } ?>
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