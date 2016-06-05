<section class="content-header">
    <h1>TSDNS</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><a href="admin.php?w=vr"><i class="fa fa-link"></i> TSDNS</a></li>
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

                <form role="form" action="admin.php?w=vr&amp;d=ad&amp;r=vr" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

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
                            <label for="inputRoot"><?php echo $sprache->rootserver;?></label>
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
                            <label for="inputIp"><?php echo $sprache->ip;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputIp" type="text" name="ip" value="<?php echo $ip;?>" maxlength="15">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPort"><?php echo $sprache->port;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputPort" type="number" name="port" value="<?php echo $port;?>" maxlength="5" min="1" max="65535">
                            </div>
                        </div>
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
        $.ajax({ url: 'ajax.php?d=tsdnsmasterusage&id=' + $("#inputRoot").val() + '&serverID=<?php echo $id;?>', cache: false } ).done(function(html) {
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