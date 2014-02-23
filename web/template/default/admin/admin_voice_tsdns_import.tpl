<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=vd">TSDNS <?php echo $gsprache->master;?></a> <span class="divider">/</span></li>
            <li>Import <span class="divider">/</span></li>
            <li class="active"><?php echo $ssh2ip;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form name="form" class="form-horizontal" action="admin.php?w=vd&amp;d=ip&amp;id=<?php echo $id;?>&amp;r=vd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ip">
            <?php if (is_array($dnsarray)) { ?>
            <?php foreach ($newArray as $k=>$v) { ?>
            <input type="hidden" name="dns[]" value="<?php echo $v;?>">
            <input type="hidden" name="<?php echo $v;?>-address" value="<?php echo $k;?>">
            <h5><?php echo $k.': '.$v;?></h5>
            <div class="control-group">
                <label class="control-label" for="inputImport"><?php echo $sprache->import;?></label>
                <div class="controls">
                    <select id="inputImport" name="<?php echo $v;?>-import">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCustomer"><?php echo $gsprache->user ;?>:</label>
                <div class="controls">
                    <select id="inputCustomer" name="<?php echo $v;?>-customer">
                        <option value="0"><?php echo $sprache->newuser;?></option>
                        <?php foreach ($table as $key=>$value) { ?>
                        <option value="<?php echo $key;?>"><?php echo $value;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php if ($newuser==2) { ?>
            <div class="control-group">
                <label class="control-label" for="inputUser"><?php echo $sprache->user;?></label>
                <div class="controls"><input id="inputUser" type="text" name="<?php echo $v;?>-username" /></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEmail"><?php echo $usprache->email;?></label>
                <div class="controls"><input id="inputEmail" type="text" name="<?php echo $v;?>-email" value="ts3@import.mail" /></div>
            </div>
            <?php }}} else { ?>
            <div class="control-group"><?php echo $dnsarray;?></div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-refresh"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>