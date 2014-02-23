<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=vr">TSDNS</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $tsdns;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->user;?></dt>
            <dd><?php echo $user;?></dd>
            <dt>TSDNS</dt>
            <dd><?php echo $tsdns;?></dd>
            <dt><?php echo $gsprache->jobPending;?></dt>
            <dd><?php echo $jobPending;?></dd>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form name="form" class="form-horizontal" action="admin.php?w=vr&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vr" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputDns"><?php echo $sprache->dns;?></label>
                <div class="controls"><input id="inputDns" type="text" name="dns" value="<?php echo $dns;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputIp"><?php echo $sprache->ip;?></label>
                <div class="controls"><input id="inputIp" type="text" name="ip" maxlength="15" value="<?php echo $ip;?>"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPort"><?php echo $sprache->port;?></label>
                <div class="controls"><input id="inputPort" type="text" name="port" value="<?php echo $port;?>" maxlength="5"></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputExternalID">externalID</label>
                <div class="controls"><input id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID;?>"></div>
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