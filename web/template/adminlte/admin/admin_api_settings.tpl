<section class="content-header">
    <h1>API <?php echo $gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">API <?php echo $gsprache->settings;?></a></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="box box-info">	
        <div class="box-body">
            <form role="form" action="admin.php?w=ap&amp;r=ap" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
                <input type="hidden" name="token" value="<?php echo token();?>">
                <div class="form-group">
                    <label for="inputActive"><?php echo $sprache->active;?></label>
                        <select class="form-control" id="inputActive" name="active">
                            <option value="N"><?php echo $gsprache->no;?></option>
                            <option value="Y" <?php if ($active=="Y") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                        </select>
                </div>
                
                <div class="form-group">
                    <label for="inputUser"><?php echo $sprache->user;?></label>
                        <input class="form-control" id="inputUser" type="text" name="user" value="<?php echo $user?>" maxlength="255" required>
                </div>
                
                <div class="form-group">
                    <label for="inputPwd"><?php echo $sprache->pwd;?></label>
                        <input class="form-control" id="inputPwd" type="text" name="pwd" value="<?php echo $pwd?>" maxlength="255" required>
                </div>
                
                <div class="form-group">
                    <label for="inputIPs"><?php echo $sprache->ipAdd;?></label>
                        <span class="btn-sm btn-primary" onclick="AddInput(this.form,'ips','ip[]')"><i class="fa fa-plus"></i></span>
                </div>
                
                <div id="ips">
                    <?php foreach($ips as $ip) { ?>
                    <div id="<?php echo $ip?>" class="control-group">
                        <label for="inputIPs-<?php echo $ip?>">IP</label>
                            <input id="inputIPs-<?php echo $ip?>" type="text" name="ip[]" value="<?php echo $ip?>" maxlength="15" required> <span class="btn-sm btn-danger" onclick="Remove('<?php echo $ip?>')"><i class="fa fa-trash-o"></i></span>
                    </div>
                    <?php } ?>
                </div>
    
                    <label for="inputEdit"></label>
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                        <input type="hidden" name="action" value="md">
            </form>
        </div>
    </div>
</section>