<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->feeds.' '.$gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=fe&amp;d=se" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
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
                <label class="control-label" for="inputSteam">Steam Feeds</label>
                <div class="controls">
                    <select id="inputSteam" name="steamFeeds">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($steamFeeds=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMerge"><?php echo $sprache->merge;?></label>
                <div class="controls">
                    <select id="inputMerge" name="merge">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($merge=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputContent"><?php echo $sprache->displayContent;?></label>
                <div class="controls">
                    <select id="inputContent" name="displayContent">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($displayContent=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputOrderBy"><?php echo $sprache->orderBy;?></label>
                <div class="controls">
                    <select id="inputOrderBy" name="orderBy">
                        <option value="I">URL</option>
                        <option value="D" <?php if ($displayContent=='D') echo 'selected="selected"'; ?>>ID</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDisplay"><?php echo $sprache->limitDisplay;?></label>
                <div class="controls">
                    <select id="inputDisplay" name="limitDisplay">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($limitDisplay=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxKeep"><?php echo $sprache->maxKeep;?></label>
                <div class="controls">
                    <input id="inputMaxKeep" type="number" name="maxKeep" maxlength="6" value="<?php echo $maxKeep;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMaxChars"><?php echo $sprache->maxChars;?></label>
                <div class="controls">
                    <input id="inputMaxChars" type="number" name="maxChars" maxlength="6" value="<?php echo $maxChars;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputNewsAmount"><?php echo $sprache->newsAmount;?></label>
                <div class="controls">
                    <input id="inputNewsAmount" type="number" name="newsAmount" maxlength="6" value="<?php echo $newsAmount;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUpdateMinutes"><?php echo $sprache->updateMinutes;?></label>
                <div class="controls">
                    <input id="inputUpdateMinutes" type="number" name="updateMinutes" maxlength="10" value="<?php echo $updateMinutes;?>">
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