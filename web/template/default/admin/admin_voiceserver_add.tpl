<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->voiceserver;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form name="form" class="form-horizontal" action="admin.php?w=vo&amp;d=ad" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="control-group">
                <label class="control-label" for="inputUser"><?php echo $sprache->user;?></label>
                <div class="controls">
                    <select id="inputUser" name="customer">
                        <?php foreach ($table as $key=>$val){ ?>
                        <option value="<?php echo $key;?>"><?php echo $val;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRoot"><?php echo $sprache->rootserver;?></label>
                <div class="controls">
                    <select id="inputRoot" name="masterserver" onchange="getdetails('serverallocation.php?d=vo&id=', this.value)">
                        <?php foreach ($table2 as $table_row2){ ?>
                        <option value="<?php echo $table_row2['id'];?>"><?php echo $table_row2['server'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRoot"><?php echo $sprache->usage;?></label>
                <div id="information" class="controls">
                    <?php if (isset($table2[0]['uslots'])) { ?>
                    <?php echo $sprache->installedslots." ".$table2[0]['uslots']."/".$table2[0]['installedslots']."/".$table2[0]['maxslots'];?><br />
                    <?php echo $sprache->installedserver." ".$table2[0]['installedserver']."/".$table2[0]['maxserver'];?>
                    <?php } ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>