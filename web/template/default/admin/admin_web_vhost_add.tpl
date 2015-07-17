<section class="content-header">
    <h1><?php echo $gsprache->webspace;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=wv"><i class="fa fa-cubes"></i> <?php echo $gsprache->webspace;?></a></li>
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

            <form role="form" action="admin.php?w=wv&amp;d=ad&amp;r=wv" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                <input type="hidden" name="token" value="<?php echo token();?>">
                <input type="hidden" name="action" value="ad">

                <div class="box box-success">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputExternalID">External ID</label>
                            <div class="controls"><input class="form-control" id="inputExternalID" type="text" name="externalID" value="<?php echo $externalID;?>"></div>
                        </div>

                        <div class="form-group<?php if(isset($errors['userID'])) echo ' has-error';?>">
                            <label for="inputUser"><?php echo $dedicatedLanguage->user;?></label>
                            <div class="controls">
                                <select class="form-control chosen-select" id="inputUser" name="userID" required="required">
                                    <option></option>
                                    <?php foreach ($table as $k=>$v){ ?>
                                    <option value="<?php echo $k;?>" <?php if ($userID==$k) echo 'selected="selected";'?>><?php echo $v;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['webMasterID'])) echo ' has-error';?>">
                            <label for="webMasterID"><?php echo $gsprache->master;?></label>
                            <div class="controls">
                                <select class="form-control chosen-select" id="webMasterID" name="webMasterID">
                                    <?php foreach ($table2 as $k=>$v){ ?>
                                    <option value="<?php echo $k;?>" <?php if ($webMasterID==$k) echo 'selected="selected";'?>><?php echo $v;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputFtpPassword"><?php echo $sprache->ftpPassword;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputFtpPassword" type="text" name="ftpPassword" value="<?php echo $ftpPassword;?>" required>
                            </div>
                        </div>

                        <div class="form-group<?php if(isset($errors['active'])) echo ' has-error';?>">
                            <label for="inputActive"><?php echo $dedicatedLanguage->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDefaultDomain"><?php echo $sprache->dns;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputDefaultDomain" type="text" name="defaultDomain" value="<?php echo $defaultDomain;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDescription"><?php echo $dedicatedLanguage->description;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputDescription" type="text" name="description" value="<?php echo $description;?>">
                            </div>
                        </div>

                        <?php foreach(customColumns('W') as $row){ ?>
                        <div class="form-group">
                            <label for="inputCustom-<?php echo $row['customID'];?>"><?php echo $row['menu'];?></label>
                            <div class="controls"><input class="form-control" id="inputCustom-<?php echo $row['customID'];?>" type="<?php echo $row['type']=='V' ? 'text' : 'number';?>" name="<?php echo $row['name'];?>" value="" maxlength="<?php echo $row['length'];?>"></div>
                        </div>
                        <?php }?>

                        <div id="rootWrapper">
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>
                </div>
            </form>
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
        $.ajax({ url: 'ajax.php?d=webmaster&id=' + $("#webMasterID").val() + '&serverID=<?php echo $id;?>', cache: false } ).done(function(html) {
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