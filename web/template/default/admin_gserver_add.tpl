<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->add;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form name="form" class="form-horizontal" action="admin.php?w=gs&amp;d=ad" method="post">
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
                <label class="control-label" for="inputRoot"><?php echo $sprache->root;?></label>
                <div class="controls">
                    <select id="inputRoot" name="rserver" onchange="getdetails('serverallocation.php?id=', this.value)">
                        <?php foreach ($table2 as $table_row2){ ?>
                        <option value="<?php echo $table_row2['id'];?>" <?php if ($table_row2['id']==$bestserver) echo 'selected="selected"';?>><?php echo $table_row2['ip'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputRoot"><?php echo $sprache->usage;?></label>
                <div id="information" class="controls">
                    <?php echo $sprache->serverinstalled.": ".$installedserver."/".$maxserver;?><br />
                    <?php echo $sprache->slotsinstalled." ".$max."/".$maxslots;?><br />
                    <?php echo $sprache->slotsused.": ".$used;?>
                </div>
            </div>
            <hr>
            <h4><?php echo $sprache->games;?></h4>
            <?php foreach ($table3 as $table_row3){ ?>
            <div class="control-group">
                <label class="control-label" for="inputGame-<?php echo $table_row3['shorten'];?>"><img src="images/games/icons/<?php echo $table_row3['shorten'];?>.png" alt="<?php echo $table_row3['shorten'];?>" width="14"/> <?php echo $table_row3['description'];?></label>
                <div class="controls"><input id="inputGame-<?php echo $table_row3['shorten'];?>" type="checkbox" name="shorten[]" value="<?php echo $table_row3['shorten'];?>"></div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>