<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=tr"><?php echo $gsprache->support;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $topic;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <dl class="dl-horizontal">
        <dt><?php echo $sprache->status;?></dt>
        <dd><?php echo $status;?></dd>
    </dl>
</div>
<hr>
<?php foreach ($table as $table_row) { ?>
<div class="row-fluid">
    <h5><a href="#" onclick="textdrop('<?php echo $table_row['writedate'];?>')"><?php echo $table_row['writedate'];?></a> <?php echo $sprache->writer.': '.$table_row['writer'];?></h5>
    <div id="<?php echo $table_row['writedate'];?>" class="span12">
        <?php echo $table_row['ticket'];?>
    </div>
</div>
<?php } ?>
<hr>
<?php if($open=="Y") { ?>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=tr&amp;d=md&amp;id=<?php echo $id;?>&amp;r=tr" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label" for="priority"><?php echo $sprache->priority;?></label>
                <div class="controls">
                    <select id="priority" name="userPriority">
                        <option value="1"><?php echo $sprache->priority_low;?></option>
                        <option value="2" <?php if($userPriority==2) echo 'selected="selected"'; ?>><?php echo $sprache->priority_medium;?></option>
                        <option value="3" <?php if($userPriority==3) echo 'selected="selected"'; ?>><?php echo $sprache->priority_high;?></option>
                        <option value="4" <?php if($userPriority==4) echo 'selected="selected"'; ?>><?php echo $sprache->priority_very_high;?></option>
                        <option value="5" <?php if($userPriority==5) echo 'selected="selected"'; ?>><?php echo $sprache->priority_critical;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="problem"><?php echo $sprache->answer;?></label>
                <div class="controls">
                    <textarea id="problem" name="ticket" rows="10"></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="wr">
                </div>
            </div>
        </form>
    </div>
</div>
<?php } ?>