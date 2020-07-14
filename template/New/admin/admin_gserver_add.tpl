<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
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

                <form role="form" action="admin.php?w=gs&amp;d=ad&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputExternalID">External ID</label>
                            <div class="controls"><input class="form-control" id="inputExternalID" type="text" name="externalID" value=""></div>
                        </div>

                        <div class="form-group">
                            <label for="inputUser"><?php echo $sprache->user;?></label>
                            <div class="controls">
                                <select class="form-control chosen-select" id="inputUser" name="userID" required="required">
                                    <option></option>
                                    <?php foreach ($table as $key=>$val){ ?>
                                    <option value="<?php echo $key;?>"><?php echo $val;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputRoot"><?php echo $sprache->root;?></label>
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

                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputAutorestart">Auto Restart</label>
                            <div class="controls">
                                <select class="form-control" id="inputAutorestart" name="autoRestart">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputUpdateRestart">Update Restart</label>
                            <div class="controls">
                                <select class="form-control" id="inputUpdateRestart" name="updateRestart">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputLend"><?php echo $sprache->lendserver;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputLend" name="lendserver">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassFTP">FTP <?php echo $sprache->password;?></label>
                            <div class="controls"><input class="form-control" id="inputPassFTP" type="text" name="ftpPassword" value="<?php echo passwordgenerate(10);?>"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputEAC">Easy Anti Cheat</label>
                            <div class="controls">
                                <select class="form-control" id="inputEAC" name="eacAllowed">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputSlots"><?php echo $sprache->slots;?></label>
                            <div class="controls"><input class="form-control" id="inputSlots" type="number" name="slots" value="12" min="1"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputPmode"><?php echo $sprache->protect;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputPmode" name="protectionAllowed">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputBrand"><?php echo $sprache->brandname;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputBrand" name="brandname">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputWar"><?php echo $sprache->war;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputWar" name="war">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputTV"><?php echo $sprache->tv;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputTV" name="tvEnable">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">

                            <label for="inputMinRam">Min Ram</label>

                            <div class="input-group">
                                <input class="form-control" id="inputMinRam" type="number" name="minRam" value="512">
                                <span class="input-group-addon">MB</span>
                            </div>
                        </div>

                        <div class="form-group">

                            <label for="inputMaxRam">Max Ram</label>

                            <div class="input-group">
                                <input class="form-control" id="inputMaxRam" type="number" name="maxRam" value="1024">
                                <span class="input-group-addon">MB</span>
                            </div>
                        </div>

                        <div class="form-group">

                            <label for="inputDescription"><?php echo $gsprache->description;?></label>

                            <div class="controls">
                                <input class="form-control" id="inputDescription" type="text" name="description" value="<?php echo $description;?>">
                            </div>
                        </div>

                        <?php foreach(customColumns('G') as $row){ ?>
                        <div class="form-group">
                            <label for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                            <div class="controls"><input class="form-control" id="inputCustom-<?php echo $row['customID'];?>" type="<?php echo $row['type']=='V' ? 'text' : 'number';?>" name="<?php echo $row['name'];?>" value="" maxlength="<?php echo $row['length'];?>"></div>
                        </div>
                        <?php }?>

                        <div id="gameDetails">
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

        $('#gameDetails').html('');

        $.ajax({ url: 'ajax.php?d=appmasterusage&id=' + $("#inputRoot").val(), cache: false } ).done(function(html) {
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
        });
    }

    $('#inputRoot').on('change', function() {
        loadRootDetails();
    });

    function gameDetails () {

        var gameID;
        var optionString = '';
        var idString;
        var idsString = '';
        var ids = [];

        $("#inputGames option:selected").each(function(){

            gameID = $(this).val();

            if (gameID > 0) {
                optionString += '<option value="' + gameID + '">' + $(this).text() + '</option>';

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

        $('#inputPrimary').html(optionString);
        $('#portWrapper').load('ajax.php?d=appmasterportbest' + idString);

        $.ajax({ url: 'ajax.php?d=appmasterappdetails' + idsString, cache: false } ).done(function(html) {

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
        $('#portList').load('ajax.php?d=appmasterportusage&ip=' + encodeURI(ip));
        bestPorts($('#inputPrimary option:selected').val());
    }

    function bestPorts (id) {
        $('#portWrapper').load('ajax.php?d=appmasterportbest&id=' + id + '&ip=' + encodeURI($('#inputIP option:selected').val()));
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