<section class="content-header">
    <h1>MySQL DB</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=my"><i class="fa fa-database"></i> MySQL</a></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>

<section class="content">

    <?php if (count($errors)>0){ ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><?php echo $gsprache->errors;?></h4>
                <?php echo implode(', ',$errors);?>
            </div>
        </div>
    </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">

                <form role="form" action="admin.php?w=md&amp;d=ad&amp;r=md" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputExternalID">External ID</label>
                            <div class="controls"><input class="form-control" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['userID'])) echo ' has-error';?>">
                            <label for="inputUser"><?php echo $sprache->user;?></label>
                            <div class="controls">
                                <select class="form-control chosen-select" id="inputUser" name="userID" required="required">
                                    <option></option>
                                    <?php foreach ($table as $key=>$val){ ?>
                                    <option value="<?php echo $key;?>" <?php if($key==$userID) echo 'selected="selected"';?>><?php echo $val;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['rootID'])) echo ' has-error';?>">
                            <label for="inputRoot">MySQL Server</label>
                            <div class="controls">
                                <select class="form-control chosen-select" id="inputRoot" name="rootID">
                                    <?php foreach ($table2 as $key=>$val){ ?>
                                    <option value="<?php echo $key;?>" <?php if($key==$rootID) echo 'selected="selected"';?>><?php echo $val;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div id="rootWrapper">
                        </div>

                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDescription"><?php echo $sprache->description;?></label>
                            <div class="controls">
                                <input class="form-control" id=inputDescription type="text" name="description" value="<?php echo $description;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword"><?php echo $sprache->password;?></label>
                            <div class="controls">
                                <input class="form-control" id=inputPassword type="text" name="password" value="<?php echo $password;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputHostTable"><?php echo $sprache->manage_host_table;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputHostTable" name="manage_host_table">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($manage_host_table=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputIPs"><?php echo $sprache->ips;?></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputIPs" name="ips" rows="5" ><?php echo $ips?></textarea>
                            </div>
                        </div>

                        <?php foreach(customColumns('D') as $row){ ?>
                        <div class="form-group">
                            <label for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                            <div class="controls"><input class="form-control" id="inputCustom-<?php echo $row['customID'];?>" type="<?php echo $row['type']=='V' ? 'text' : 'number';?>" name="<?php echo $row['name'];?>" value="<?php echo $row['value'];?>" maxlength="<?php echo $row['length'];?>"></div>
                        </div>
                        <?php }?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $("#inputUser,#inputRoot").chosen({
        disable_search_threshold: 3,
        inherit_select_classes: true,
        no_results_text: '<?php echo $gsprache->chosenNoResult;?>',
        placeholder_text_single: '<?php echo $gsprache->chosenSelect;?>',
        placeholder_text_multiple: '<?php echo $gsprache->chosenSelect;?>',
        width: "100%"
    });

    function loadRootDetails () {
        $.ajax({ url: 'ajax.php?d=mysqlmasterusage&id=' + $("#inputRoot").val() + '&serverID=<?php echo $id;?>', cache: false } ).done(function(html) {
            $('#rootWrapper').html(html);
        });
    }

    $('#inputRoot').on('change', function() {
        loadRootDetails();
    });

    $(function(){
        loadRootDetails();
    });
</script>