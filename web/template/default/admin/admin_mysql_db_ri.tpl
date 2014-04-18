<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=my">MySQL <?php echo $gsprache->databases;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->reinstall;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $dbname.' ('.$ip.' )';?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=my&amp;d=rd&amp;id=<?php echo $id;?>&amp;r=my" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="row-fluid">
                <dl class="dl-horizontal">
                    <dt>IP</dt>
                    <dd><?php echo $ip;?></dd>
                    <dt><?php echo $sprache->user;?></dt>
                    <dd><?php echo $dbname;?></dd>
                    <dt><?php echo $sprache->dbname;?></dt>
                    <dd><?php echo $dbname;?></dd>
                    <?php if(strlen($description)>0){ ?>
                    <dt><?php echo $sprache->description;?></dt>
                    <dd><?php echo $description;?></dd>
                    <?php } ?>
                </dl>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-warning" id="inputEdit" type="submit"><i class="fa fa-refresh"></i> <?php echo $sprache->reinstall;?></button>
                    <input type="hidden" name="action" value="rd">
                </div>
            </div>
        </form>
    </div>
</div>