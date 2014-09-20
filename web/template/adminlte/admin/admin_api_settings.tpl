<section class="content-header">
    <h1>API <?php echo $gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">API <?php echo $gsprache->settings;?></a></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-11">
            <div class="box box-info">

                <form role="form" action="admin.php?w=ap&amp;r=ap" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">
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

                        <div id="ips">
                            <?php foreach($ips as $ip) { ?>
                            <div id="<?php echo $ip?>">
                                <div class="input-group">
                                    <span class="input-group-addon">IP</span>
                                    <input class="form-control" id="inputIPs-<?php echo $ip?>" type="text" name="ip[]" value="<?php echo $ip?>" maxlength="15" required>
                                    <span class="input-group-btn">
                                        <button class="btn btn-danger" type="button" onclick="Remove('<?php echo $ip?>')"><i class="fa fa-trash-o"></i></button>
                                    </span>
                                </div>
                                <br>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="form-group">
                            <label for="inputIPs"></label>
                            <button class="btn btn-sm btn-success" type="button" onclick="AddInput(this.form,'ips','ip[]')"><i class="fa fa-plus-circle"> <?php echo $sprache->ipAdd;?></i></button>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function AddInput (Form,Target,Name) {
        var theTarget = document.getElementById(Target);
        var IPCount = document.getElementsByName(Name).length;
        var newDiv = document.createElement('div');
        IPCount++;
        newDiv.setAttribute('id',IPCount);
        newDiv.innerHTML += '<div id="'+IPCount+'"><div class="input-group"><span class="input-group-addon">IP</span><input class="form-control" id="inputIPs-'+ IPCount +'" type="text" name="ip[]" value="" maxlength="15" required> <span class="input-group-btn"><button class="btn btn-danger" type="button" onclick="Remove('+ IPCount +')"><i class="fa fa-trash-o"></i></span></div><br></div>';
        theTarget.appendChild(newDiv);
    }
    function Remove (ID) {
        var toBeRemoved = document.getElementById(ID);
        toBeRemoved.parentNode.removeChild(toBeRemoved);
    }
</script>