<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=ti"><?php echo $gsprache->support;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->support2;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=ti&amp;d=ad&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label" for="priority"><?php echo $sprache->priority;?></label>
                <div class="controls">
                    <select id="priority" name="userPriority">
                        <option value="1"><?php echo $sprache->priority_low;?></option>
                        <option value="2"><?php echo $sprache->priority_medium;?></option>
                        <option value="3"><?php echo $sprache->priority_high;?></option>
                        <option value="4"><?php echo $sprache->priority_very_high;?></option>
                        <option value="5"><?php echo $sprache->priority_critical;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="topic_name"><?php echo $sprache->topic_name;?></label>
                <div class="controls">
                    <select id="topic_name" name="maintopic" onchange="getdetails('serverallocation.php?d=rt&amp;po=', this.value)">
                        <?php foreach ($table as $table_row){ ?>
                        <option value="<?php echo $table_row['id'];?>" ><?php echo $table_row['topic'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="topic_name"><?php echo $sprache->topic_name_sub;?></label>
                <div id="information" class="controls">
                    <select id="topic_name" name="topic">
                        <?php foreach ($table2 as $table_row2){ ?>
                        <option value="<?php echo $table_row2['id'];?>" ><?php echo $table_row2['topic'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="problem"><?php echo $sprache->problem;?></label>
                <div class="controls">
                    <textarea id="problem" name="ticket" rows="10"></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="ad">
                </div>
            </div>
        </form>
    </div>
</div>