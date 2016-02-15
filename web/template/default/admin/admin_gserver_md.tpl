<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $ip.':'.$port;?></li>
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
            <div class="box box-primary">

                <form role="form" action="admin.php?w=gs&amp;d=md&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">
                    <input type="hidden" id="userID" name="userID" value="<?php echo $userID;?>">
                    <input type="hidden" id="currentRootID" name="currentRootID" value="<?php echo $rootID;?>">
                    <input type="hidden" id="currentIP" name="currentIP" value="<?php echo $ip;?>">
                    <input type="hidden" id="gameServerID" name="gameServerID" value="<?php echo $id;?>">
                    <input type="hidden" id="currentGameID" name="currentGameID" value="<?php echo $currentGameID;?>">
                    <input type="hidden" id="currentPort" name="currentPort" value="<?php echo $port;?>">
                    <input type="hidden" id="currentPort2" name="currentPort2" value="<?php echo $port2;?>">
                    <input type="hidden" id="currentPort3" name="currentPort3" value="<?php echo $port3;?>">
                    <input type="hidden" id="currentPort4" name="currentPort4" value="<?php echo $port4;?>">
                    <input type="hidden" id="currentPort5" name="currentPort5" value="<?php echo $port5;?>">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputExternalID">External ID</label>
                            <div class="controls"><input class="form-control" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputRoot"><?php echo $sprache->root;?></label>
                            <div class="controls">
                                <select class="form-control chosen-select" id="inputRoot" name="rootID">
                                    <?php foreach ($table as $key=>$val){ ?>
                                    <!-- as a first development step will not allow a move from root to root -->
                                    <?php if($key==$rootID){ ?><option value="<?php echo $key;?>" <?php if($key==$rootID) echo 'selected="selected"';?>><?php echo $val;?></option><?php }?>
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
                            <label for="inputAutorestart">Auto Restart</label>
                            <div class="controls">
                                <select class="form-control" id="inputAutorestart" name="autoRestart">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($autoRestart=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputUpdateRestart">Update Restart</label>
                            <div class="controls">
                                <select class="form-control" id="inputUpdateRestart" name="updateRestart">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($updateRestart=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputLend"><?php echo $sprache->lendserver;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputLend" name="lendserver">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if($lendServer=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassFTP">FTP <?php echo $sprache->password;?></label>
                            <div class="controls"><input class="form-control" id="inputPassFTP" type="text" name="ftpPassword" value="<?php echo $ftpPassword;?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputEAC">Easy Anti Cheat</label>
                            <div class="controls">
                                <select class="form-control" id="inputEAC" name="eacAllowed">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if($eacAllowed=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputSlots"><?php echo $sprache->slots;?></label>
                            <div class="controls"><input class="form-control" id="inputSlots" type="number" name="slots" value="<?php echo $slots;?>" min="1"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputPmode"><?php echo $sprache->protect;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputPmode" name="protectionAllowed">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if($protectionAllowed=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputBrand"><?php echo $sprache->brandname;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputBrand" name="brandname">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if($brandname=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputWar"><?php echo $sprache->war;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputWar" name="war">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if($war=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputTV"><?php echo $sprache->tv;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputTV" name="tvEnable">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($tvEnable=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">

                            <label for="inputMinRam">Min Ram</label>

                            <div class="input-group">
                                <input class="form-control" id="inputMinRam" type="number" name="minRam" value="<?php echo $minRam;?>">
                                <span class="input-group-addon">MB</span>
                            </div>
                        </div>

                        <div class="form-group">

                            <label for="inputMaxRam">Max Ram</label>

                            <div class="input-group">
                                <input class="form-control" id="inputMaxRam" type="number" name="maxRam" value="<?php echo $maxRam;?>">
                                <span class="input-group-addon">MB</span>
                            </div>
                        </div>

                        <?php foreach(customColumns('G', $id) as $row){ ?>
                        <div class="form-group">
                            <label for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                            <div class="controls"><input class="form-control" id="inputCustom-<?php echo $row['customID'];?>" type="<?php echo $row['type']=='V' ? 'text' : 'number';?>" name="<?php echo $row['name'];?>" value="<?php echo $row['value'];?>" maxlength="<?php echo $row['length'];?>"></div>
                        </div>
                        <?php }?>

                        <div id="gameDetails">
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

    var currentRootID = $("#currentRootID").val();
    var currentIP = $("#currentIP").val();
    var gameServerID = $("#gameServerID").val();
    var currentGameID = $("#currentGameID").val();

    $("#inputUser,#inputRoot").chosen({
        disable_search_threshold: 3,
        inherit_select_classes: true,
        no_results_text: '<?php echo $gsprache->chosenNoResult;?>',
        placeholder_text_single: '<?php echo $gsprache->chosenSelect;?>',
        placeholder_text_multiple: '<?php echo $gsprache->chosenSelect;?>',
        width: "100%"
    });

    function loadRootDetails (modificationInit) {

        $('#gameDetails').html('');

        $.ajax({ url: 'ajax.php?d=appmasterusage&id=' + $("#inputRoot").val() + '&currentRootID=' + currentRootID + '&currentIP=' + encodeURI(currentIP) + '&gameServerID=' + gameServerID, cache: false } ).done(function(html) {
            $('#rootWrapper').html(html);

            $("#inputGames").chosen({
                disable_search_threshold: 3,
                no_results_text: '<?php echo $gsprache->chosenNoResult;?>',
                placeholder_text_single: '<?php echo $gsprache->chosenSelect;?>',
                placeholder_text_multiple: '<?php echo $gsprache->chosenSelect;?>',
                width: "100%"
            });

            if ($("#inputCoreBind option:selected").val() == 'N') {
                toggleID ('#theCores', 'N')
            }

            gameDetails(true);
        });

        return true;
    }

    $('#inputRoot').on('change', function() {
        loadRootDetails();
    });

    function toggleID (id, value) {
        if (value == 'Y') {
            $(id).show();
        } else {
            $(id).hide();
        }
    }

    function gameDetails (initLoad) {

        var gameID;
        var idString;
        var idsString = '';
        var ids = [];

        $("#inputGames option:selected").each(function(){

            gameID = $(this).val();

            if (typeof gameID !== 'undefined' && gameID > 0) {

                if (typeof idString === 'undefined') {
                    idString = '&id=' + gameID + '&ip=' + encodeURI($('#inputIP option:selected').val());
                }

                idsString += '&id[]=' + gameID;

                ids.push(gameID);
            }
        });

        if (typeof idString === 'undefined') {
            idString = '';
        }

        if (typeof initLoad === 'undefined') {
            $('#portWrapper').load('ajax.php?d=appmasterportbest' + idString + '&currentRootID=' + currentRootID + '&currentIP=' + encodeURI(currentIP) + '&gameServerID=' + gameServerID);
        }

        $.ajax({ url: 'ajax.php?d=appmasterappdetails' + idsString + '&currentRootID=' + currentRootID + '&currentIP=' + encodeURI(currentIP) + '&gameServerID=' + gameServerID, cache: false } ).done(function(html) {

            $('#gameDetails').html(html);

            ids = $.unique(ids);
            var idsLength = ids.length;

            var i;

            for (i = 0; i < idsLength; i++) {
                if (ids[i] > 0) {
                    toggleGameDetail(ids[i]);
                }
            }
        });
    }

    function usedPorts (ip) {
        $('#portList').load('ajax.php?d=appmasterportusage&ip=' + encodeURI(ip) + '&currentRootID=' + currentRootID + '&currentIP=' + encodeURI(currentIP) + '&gameServerID=' + gameServerID);
        bestPorts(currentGameID);
    }

    function bestPorts (id) {

        var ipSelected = $('#inputIP option:selected').val();

        if (ipSelected == currentIP) {
            $('#inputPortMain').val($('#currentPort').val());
            $('#inputPort2').val($('#currentPort2').val());
            $('#inputPort3').val($('#currentPort3').val());
            $('#inputPort4').val($('#currentPort4').val());
            $('#inputPort5').val($('#currentPort5').val());
        } else {
            $('#portWrapper').load('ajax.php?d=appmasterportbest&id=' + id + '&ip=' + encodeURI(ipSelected));
        }
    }

    function toggleGameDetail(id) {

        var iconClass = $('#openCloseIcon-' + id);

        if (typeof iconClass.attr('class') !== 'undefined') {
            if (iconClass.attr('class') == 'fa fa-arrow-circle-o-down') {
                iconClass.attr('class','fa fa-arrow-circle-o-up');
                $('.gameDetail-' + id).show();
            } else {
                iconClass.attr('class', 'fa fa-arrow-circle-o-down');
                $('.gameDetail-' + id).hide();
            }
        }
    }

    $(function(){
        loadRootDetails();
    });
</script>