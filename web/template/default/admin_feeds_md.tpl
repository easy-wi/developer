<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=fe"><?php echo $gsprache->feeds;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php if ($twitter=='Y') echo 'Twitter'; else echo $feedUrl;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=fe&amp;d=md&amp;id=<?php echo $id;?>&amp;r=fe" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($active=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTwitter">Twitter</label>
                <div class="controls">
                    <select id="inputTwitter" name="twitter" onchange="SwitchShowHideRows(this.value);">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($twitter=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="Y<?php if ($twitter=='N') echo ' display_none';?> switch control-group">
                <label class="control-label" for="inputTwitterLoginname">Twitter Loginname</label>
                <div class="controls">
                    <input id="inputTwitterLoginname" type="text" name="loginName" value="<?php echo $loginName;?>">
                </div>
            </div>
            <div class="N<?php if ($twitter=='Y') echo ' display_none';?> switch control-group">
                <label class="control-label" for="inputFeedUrl">URL</label>
                <div class="controls">
                    <input id="inputFeedUrl" type="text" name="feedUrl" value="<?php echo $feedUrl;?>">
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