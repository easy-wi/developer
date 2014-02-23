<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active">API <?php echo $gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=ap&amp;r=ap" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($active=="Y") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUser"><?php echo $sprache->user;?></label>
                <div class="controls">
                    <input id="inputUser" type="text" name="user" value="<?php echo $user?>" maxlength="255" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPwd"><?php echo $sprache->pwd;?></label>
                <div class="controls">
                    <input id="inputPwd" type="text" name="pwd" value="<?php echo $pwd?>" maxlength="255" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIPs"><?php echo $sprache->ipAdd;?></label>
                <div class="controls">
                    <span class="btn btn-mini btn-primary" onclick="AddInput(this.form,'ips','ip[]')"><i class="icon-white icon-plus-sign"></i></span>
                </div>
            </div>
            <div id="ips">
                <?php foreach($ips as $ip) { ?>
                <div id="<?php echo $ip?>" class="control-group">
                    <label class="control-label" for="inputIPs-<?php echo $ip?>">IP</label>
                    <div class="controls">
                        <input id="inputIPs-<?php echo $ip?>" type="text" name="ip[]" value="<?php echo $ip?>" maxlength="15" required> <span class="btn btn-mini btn-danger" onclick="Remove('<?php echo $ip?>')"><i class="fa fa-trash-o"></i></span>
                    </div>
                </div>
                <?php } ?>
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