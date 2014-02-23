<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=ti"><?php echo $gsprache->support;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $topic;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <dl class="dl-horizontal">
        <dt><?php echo $gsprache->user.' '.$sprache->priority;?></dt>
        <dd><?php echo $userPriority;?></dd>
    </dl>
</div>
<hr>
<?php foreach ($table as $table_row) { ?>
<div class="row-fluid">
    <h5><a href="#" onclick="textdrop('<?php echo $table_row['writedate'];?>')"><?php echo $table_row['writedate'];?></a> <?php echo $sprache->writer.': '.$table_row['writer'];?></h5>
    <div id="<?php echo $table_row['writedate'];?>" class="span11">
        <?php echo $table_row['ticket'];?>
    </div>
</div>
<?php } ?>
<hr>
<?php if($open=="Y") { ?>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=ti&amp;d=md&amp;id=<?php echo $id;?>&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label" for="priority"><?php echo $sprache->priority;?></label>
                <div class="controls">
                    <select id="priority" class="span10" name="priority">
                        <option value="1"><?php echo $sprache->priority_low;?></option>
                        <option value="2" <?php if($realPriority==2) echo 'selected="selected"'; ?>><?php echo $sprache->priority_medium;?></option>
                        <option value="3" <?php if($realPriority==3) echo 'selected="selected"'; ?>><?php echo $sprache->priority_high;?></option>
                        <option value="4" <?php if($realPriority==4) echo 'selected="selected"'; ?>><?php echo $sprache->priority_very_high;?></option>
                        <option value="5" <?php if($realPriority==5) echo 'selected="selected"'; ?>><?php echo $sprache->priority_critical;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputStatus"><?php echo $sprache->status;?></label>
                <div class="controls">
                    <select id="inputStatus" class="span10" name="state">
                        <option value="A"><?php echo $sprache->status_author;?></option>
                        <option value="D" <?php if($state=='D') echo 'selected="selected"'; ?>><?php echo $sprache->status_done;?></option>
                        <option value="N"><?php echo $sprache->status_new;?></option>
                        <option value="P" <?php if($state=='P' OR $state=='N') echo 'selected="selected"'; ?>><?php echo $sprache->status_process;?></option>
                        <option value="R" <?php if($state=='R') echo 'selected="selected"'; ?>><?php echo $sprache->status_reopen;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSupporter"><?php echo $sprache->edit2;?></label>
                <div class="controls">
                    <select id="inputSupporter" class="span10" name="supporter">
                        <option value=""></option>
                        <?php foreach($supporterList as $k=>$v){ ?><option value="<?php echo $k;?>" <?php if($k==$admin_id) echo 'selected="selected"'; ?>><?php echo $v;?></option><?php }?>
                    </select>
                </div>
            </div>
            <?php if($open=="Y") { ?>
            <div class="control-group">
                <label class="control-label" for="problem"><?php echo $sprache->answer;?></label>
                <div class="controls">
                    <textarea id="problem" name="ticket" rows="10" class="span10"></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="wr">
                </div>
            </div>
            <?php } ?>
        </form>
    </div>
</div>
<?php } ?>