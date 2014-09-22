<section class="content-header">
    <h1><?php echo $gsprache->migration;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a></li>
        <li class="active"><?php echo $gsprache->migration;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <form role="form" action="userpanel.php?w=ms" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

            <input class="form-control" class="form-control" type="hidden" name="token" value="<?php echo token();?>">
            <input class="form-control" class="form-control" type="hidden" name="action" value="ms">

            <div class="col-md-6">
                <div class="box box-primary">

                    <div class="box-header">
                        <h4 class="box-title"><?php echo $sprache->import_source;?></h4>
                    </div>

                    <div class="box-body">
                        <?php if(count($error)>0) { ?>
                        <div class="row-fluid alert alert-error span11">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>Error!</strong> <?php echo implode('<br />',$error);?>
                        </div>
                        <?php }?>

                        <div class="form-group">
                            <label for="ftpAddress"><?php echo $sprache->ftp_adresse;?></label>
                            <input class="form-control" id="ftpAddress" type="text" name="ftpAddress" value="<?php echo $ftpAddress;?>" required>
                        </div>
                        <div class="form-group">
                            <label for="ftpPort"><?php echo $sprache->ftp_port;?></label>
                            <input class="form-control" id="ftpPort" type="text" name="ftpPort" value="<?php echo $ftpPort;?>" required>
                        </div>
                        <div class="form-group">
                            <label for="ftpUser"><?php echo $sprache->ftp_user;?></label>
                            <input class="form-control" id="ftpUser" type="text" name="ftpUser" value="<?php echo $ftpUser;?>" required>
                        </div>
                        <div class="form-group">
                            <label for="ftpPassword"><?php echo $sprache->ftp_password;?></label>
                            <input class="form-control" id="ftpPassword" type="text" name="ftpPassword" value="<?php echo $ftpPassword;?>" required>
                        </div>
                        <div class="form-group">
                            <label for="ftpPath"><?php echo $sprache->ftp_path;?></label>
                            <input class="form-control" id="ftpPath" type="text" name="ftpPath" value="<?php echo $ftpPath;?>">
                        </div>
                        <div class="form-group">
                            <label for="ssl">SSL</label>
                            <select class="form-control" id="ssl" name="ssl">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if($ssl=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-primary">

                    <div class="box-header">
                        <h4 class="box-title"><?php echo $sprache->import_destination;?></h4>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="server"><?php echo $sprache->server;?></label>
                            <select class="form-control" id="server" name="switchID" onchange="SwitchShowHideRows(this.value, 'switch', 1)">
                                <option></option>
                                <?php foreach($table as $row){ ?><option value="<?php echo $row['id'];?>" <?php if($thisID==$row['id']) echo 'selected="selected"';?>><?php echo $row['address'];?></option><?php }?>
                            </select>
                        </div>

                        <?php foreach($table as $row){ ?>
                        <div class="<?php echo $row['id'];?> form-group switch <?php if($thisID!=$row['id']) echo 'display_none';?>">
                            <label for="<?php echo $row['id'];?>-template"><?php echo $gsprache->template;?></label>
                            <select class="form-control" id="<?php echo $row['id'];?>-template" name="template[<?php echo $row['id'];?>]">
                                <?php foreach($row['games'] as $game){ ?>
                                <option value="<?php echo $game['shorten'];?>" <?php if($thisTemplate==$game['shorten']) echo 'selected="selected"';?>><?php echo $game['shorten'].' ('.$game['description'].')';?></option>
                                <option value="<?php echo $game['shorten'].'-2';?>" <?php if($thisTemplate==$game['shorten'].'-2') echo 'selected="selected"';?>><?php echo $game['shorten'].'-2 ('.$game['description'].')';?></option>
                                <option value="<?php echo $game['shorten'].'-3';?>" <?php if($thisTemplate==$game['shorten'].'-3') echo 'selected="selected"';?>><?php echo $game['shorten'].'-3 ('.$game['description'].')';?></option>
                                <?php }?>
                            </select>
                        </div>
                        <?php }?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-play"></i> <?php echo $gsprache->exec;?></button>
                    </div>
                </div>
            </div>
        </form>
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