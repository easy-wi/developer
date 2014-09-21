<section class="content-header">
    <h1><?php echo $sprache->heading_addons;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ad"><?php echo $gsprache->addon;?></a></li>
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
            <form role="form" action="admin.php?w=ad&amp;d=ad" enctype="multipart/form-data" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                <input type="hidden" name="token" value="<?php echo $token;?>">
                <input type="hidden" name="action" value="ad">
                <input type="hidden" name="import" value="1">

                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title"><?php echo $gsprache->addon;?> XML <?php echo $gsprache->import;?></h3>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="inputUpload"></label>
                            <div class="controls">
                                <input id="inputUpload" type="file" name="file">
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-upload">&nbsp;<?php echo $gsprache->upload;?></i></button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <form role="form" action="admin.php?w=ad&amp;d=ad&amp;r=ad" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo $token;?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputGame2"><?php echo $sprache->game2;?></label>
                            <select class="form-control" id="inputGame2" multiple="multiple" name="shorten[]">
                                <?php foreach ($gamesAssigned as $sid => $shorten){ ?><option value="<?php echo $sid;?>" <?php if(in_array($sid,$shortens)) echo 'selected="selected"';?>><?php echo $shorten;?></option><?php }?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputEditRequires"><?php echo $sprache->requires;?></label>
                            <select class="form-control" id="inputEditRequires" name="depending">
                                <option value="0"></option>
                                <?php foreach ($dependings as $depending) echo $depending; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputProtect"><?php echo $sprache->protect;?></label>
                            <select class="form-control" id="inputProtect" name="paddon">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if($paddon=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group<?php if(isset($errors['type'])) echo ' error';?>">
                            <label for="inputType"><?php echo $sprache->type;?></label>
                            <select class="form-control" id="inputType" name="type">
                                <option value="tool"><?php echo $sprache->tool;?></option>
                                <option value="map" <?php if($type=='map') echo 'selected="selected"';?>><?php echo $sprache->map;?></option>
                            </select>
                        </div>

                        <div class="form-group<?php if(isset($errors['addon'])) echo ' error';?>">
                            <label for="inputAddon"><?php echo $sprache->addon;?></label>
                            <input class="form-control" id="inputAddon" type="text" name="addon" value="<?php echo $addon;?>">
                        </div>

                        <div class="form-group<?php if(isset($errors['menudescription'])) echo ' error';?>">
                            <label for="inputAddon2"><?php echo $sprache->addon2;?></label>
                            <input class="form-control" id="inputAddon2" type="text" name="menudescription" value="<?php echo $menudescription;?>">
                        </div>

                        <div class="form-group<?php if(isset($errors['active'])) echo ' error';?>">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <select class="form-control" id="inputActive" name="active">
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                                <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputFolders"><?php echo $sprache->folders;?></label>
                            <input class="form-control" id="inputFolders" type="text" name="folder" value="<?php echo $folder;?>">
                        </div>

                        <div class="form-group">
                            <label for="inputConfigs"><?php echo $sprache->configs;?></label>
                            <textarea class="form-control" id="inputConfigs" rows="5" name="configs"><?php echo $configs;?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="inputStartCmd"><?php echo $sprache->start;?></label>
                            <textarea class="form-control" id="inputStartCmd" rows="5" name="cmd"><?php echo $cmd;?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="inputRmCmd"><?php echo $sprache->rmcmd;?></label>
                            <textarea class="form-control" id="inputRmCmd" rows="5" name="rmcmd"><?php echo $rmcmd;?></textarea>
                        </div>

                        <h3><?php echo $sprache->description;?></h3>

                        <div class="form-group">
                            <?php foreach ($foundLanguages as $array){ ?>
                            <label class="checkbox-inline">
                                <input name="language[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>');" type="checkbox"> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
                            </label>
                            <?php } ?>
                        </div>

                        <?php foreach ($foundLanguages as $array) { ?>
                        <div id="<?php echo $array['lang'];?>" class="form-group <?php echo $array['display'];?>">
                            <label for="inputLangs-<?php echo $array['lang'];?>"><img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/></label>
                            <textarea class="form-control" id="inputLangs-<?php echo $array['lang'];?>" name="description[<?php echo $array['lang'];?>]"></textarea>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><?php echo $gsprache->add;?> <i class="fa fa-plus-circle"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    window.onDomReady = initReady;

    function initReady(fn) {
        if(document.addEventListener) {
            document.addEventListener("DOMContentLoaded", fn, false);
        } else {
            document.onreadystatechange = function() {
                readyState(fn);
            }
        }
    }

    function readyState(func) {
        if(document.readyState == "interactive" || document.readyState == "complete") {
            func();
        }
    }

    window.onDomReady(onReady); function onReady() {
        SwitchShowHideRows('init_ready');
    }
</script>