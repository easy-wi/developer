<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->apiAuth;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=aa&amp;r=aa" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActiveExternal"><?php echo $sprache->activeExternal;?></label>
                <div class="controls">
                    <select id="inputActiveExternal" name="active">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($active=="Y") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputUser"><?php echo $sprache->user;?></label>
                <div class="controls">
                    <input id="inputUser" type="text" name="user" value="<?php echo $user;?>" maxlength="255" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPwd"><?php echo $sprache->pwd;?></label>
                <div class="controls">
                    <input id="inputPwd" type="text" name="pwd" value="<?php echo $pwd;?>" maxlength="100" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSSL"><?php echo $sprache->ssl;?></label>
                <div class="controls">
                    <select id="inputSSL" name="ssl">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if ($ssl=="Y") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDomain"><?php echo $sprache->domain;?></label>
                <div class="controls">
                    <input id="inputDomain" type="text" name="domain" value="<?php echo $domain;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFile"><?php echo $sprache->file;?></label>
                <div class="controls">
                    <input id="inputFile" type="text" name="file" value="<?php echo $file;?>" required>
                </div>
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