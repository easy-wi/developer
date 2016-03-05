<section class="content-header">
    <h1><?php echo $gsprache->voiceserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $server;?></li>
    </ol>
</section>

<section class="content">

    <?php if (count($errors)>0){ ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><?php echo $gsprache->errors;?></h4>
                <?php echo implode(', ', $errors);?>
            </div>
        </div>
    </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">

            <div class="box box-primary">

                <form role="form" action="admin.php?w=vo&amp;d=md&amp;id=<?php echo $id;?>&amp;r=vo" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputUser"><?php echo $sprache->user;?></label>
                            <div class="controls"><input class="form-control" id="inputUser" type="text" name="userName" value="<?php echo $userName;?>" disabled="disabled"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputExternalID">External ID</label>
                            <div class="controls"><input class="form-control" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['rootID'])) echo ' has-error';?>">
                            <label for="inputRoot"><?php echo $sprache->rootserver;?></label>
                            <div class="controls">
                                <select class="form-control chosen-select" id="inputRoot" name="rootID">
                                    <?php foreach ($table2 as $key=>$val){ ?>
                                    <option value="<?php echo $key;?>"><?php echo $val;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div id="rootWrapper">
                        </div>

                        <div class="form-group<?php if(isset($errors['slots'])) echo ' has-error';?>">
                            <label for="inputSlots"><?php echo $sprache->slots;?></label>
                            <div class="controls"><input class="form-control" id="inputSlots" type="number" name="slots" value="<?php echo $slots;?>" min="1"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['active'])) echo ' has-error';?>">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputAutorestart">Auto Restart</label>
                            <div class="controls">
                                <select class="form-control" id="inputAutorestart" name="autoRestart">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($autoRestart=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputLend"><?php echo $gsprache->lendserver;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputLend" name="lendserver">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if($lendserver=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword"><?php echo $sprache->password;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputPassword" name="password" onchange="toggleID('#initialPassword',this.value);">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($password=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="initialPassword">
                            <label for="inputInitial"><?php echo $sprache->initialpassword;?></label>
                            <div class="controls"><input class="form-control" id="inputInitial" type="text" name="initialpassword" value="<?php echo $initialpassword; ?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputForceWelcome"><?php echo $sprache->forcewelcome;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputForceWelcome" name="forcewelcome">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($forcewelcome=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputForceBanner"><?php echo $sprache->forcebanner;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputForceBanner" name="forcebanner">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($forcebanner=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputForceButton"><?php echo $sprache->forcebutton;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputForceButton" name="forcebutton">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($forcebutton=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputForceServerTag"><?php echo $sprache->forceservertag;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputForceServerTag" name="forceservertag">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($forceservertag=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputBackup"><?php echo $sprache->backup;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputBackup" name="backup">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($backup=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">

                            <label for="inputMaxtraffic"><?php echo $sprache->maxtraffic;?></label>

                            <div class="input-group">
                                <input class="form-control" id="inputMaxtraffic" type="number" name="maxtraffic" value="<?php echo $maxtraffic;?>">
                                <span class="input-group-addon">MB</span>
                            </div>
                        </div>

                        <div class="form-group">

                            <label for="inputMaxTotalBandwidth"><?php echo $sprache->max_download_total_bandwidth;?></label>

                            <div class="input-group">
                                <input class="form-control" id="inputMaxTotalBandwidth" type="number" name="max_download_total_bandwidth" value="<?php echo $max_download_total_bandwidth;?>">
                                <span class="input-group-addon">Byte</span>
                            </div>
                        </div>

                        <div class="form-group">

                            <label for="inputMaxUploadBandwidth"><?php echo $sprache->max_upload_total_bandwidth;?></label>

                            <div class="input-group">
                                <input class="form-control" id="inputMaxUploadBandwidth" type="number" name="max_upload_total_bandwidth" value="<?php echo $max_upload_total_bandwidth;?>">
                                <span class="input-group-addon">Byte</span>
                            </div>
                        </div>

                        <?php foreach(customColumns('T', $id) as $row){ ?>
                        <div class="form-group">
                            <label for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                            <div class="controls"><input class="form-control" id="inputCustom-<?php echo $row['customID'];?>" type="<?php echo $row['type']=='V' ? 'text' : 'number';?>" name="<?php echo $row['name'];?>" value="" maxlength="<?php echo $row['length'];?>"></div>
                        </div>
                        <?php }?>

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

    $("#inputRoot").chosen({
        disable_search_threshold: 3,
        inherit_select_classes: true,
        no_results_text: '<?php echo $gsprache->chosenNoResult;?>',
        placeholder_text_single: '<?php echo $gsprache->chosenSelect;?>',
        placeholder_text_multiple: '<?php echo $gsprache->chosenSelect;?>',
        width: "100%"
    });

    function loadRootDetails () {
        $.ajax({ url: 'ajax.php?d=voicemasterusage&id=' + $("#inputRoot").val() + '&serverID=<?php echo $id;?>', cache: false } ).done(function(html) {
            $('#rootWrapper').html(html);
            usedPorts($('#inputIP option:selected').val());
        });
    }

    $('#inputRoot').on('change', function() {
        loadRootDetails();
    });

    function usedPorts (ip) {
        $('#portWrapper').load('ajax.php?d=voicemasterportusage&ip=' + encodeURI(ip) + '&serverID=<?php echo $id;?>');
    }

    $(function(){
        loadRootDetails();
    });
</script>