<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->migration;?></li>
        </ul>
    </div>
</div>
<?php if(count($error)>0) { ?>
<div class="row-fluid alert alert-error span11">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Error!</strong> <?php echo implode('<br />',$error);?>
</div>
<?php }?>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=ms" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ms">
            <h4><?php echo $sprache->import_source;?></h4>
            <div class="control-group">
                <label class="control-label" for="ftpAddress"><?php echo $sprache->ftp_adresse;?></label>
                <div class="controls">
                    <input id="ftpAddress" type="text" name="ftpAddress" value="<?php echo $ftpAddress;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="ftpPort"><?php echo $sprache->ftp_port;?></label>
                <div class="controls">
                    <input id="ftpPort" type="text" name="ftpPort" value="<?php echo $ftpPort;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="ftpUser"><?php echo $sprache->ftp_user;?></label>
                <div class="controls">
                    <input id="ftpUser" type="text" name="ftpUser" value="<?php echo $ftpUser;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="ftpPassword"><?php echo $sprache->ftp_password;?></label>
                <div class="controls">
                    <input id="ftpPassword" type="text" name="ftpPassword" value="<?php echo $ftpPassword;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="ftpPath"><?php echo $sprache->ftp_path;?></label>
                <div class="controls">
                    <input id="ftpPath" type="text" name="ftpPath" value="<?php echo $ftpPath;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="ssl">SSL</label>
                <div class="controls">
                    <select id="ssl" name="ssl">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y" <?php if($ssl=='Y') echo 'selected="selected"';?>><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <hr>
            <h4><?php echo $sprache->import_destination;?></h4>
            <div class="control-group">
                <label class="control-label" for="server"><?php echo $sprache->server;?></label>
                <div class="controls">
                    <select id="server" name="switchID" onchange="SwitchShowHideRows(this.value)">
                        <option></option>
                        <?php foreach($table as $row){ ?><option value="<?php echo $row['id'];?>" <?php if($thisID==$row['id']) echo 'selected="selected"';?>><?php echo $row['address'];?></option><?php }?>
                    </select>
                </div>
            </div>
            <?php foreach($table as $row){ ?>
            <div class="<?php echo $row['id'];?> control-group switch <?php if($thisID!=$row['id']) echo 'display_none';?>">
                <label class="control-label" for="<?php echo $row['id'];?>-template"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <select id="<?php echo $row['id'];?>-template" name="template[<?php echo $row['id'];?>]">
                        <?php foreach($row['games'] as $game){ ?>
                        <option value="<?php echo $game['shorten'];?>" <?php if($thisTemplate==$game['shorten']) echo 'selected="selected"';?>><?php echo $game['shorten'].' ('.$game['description'].')';?></option>
                        <option value="<?php echo $game['shorten'].'-2';?>" <?php if($thisTemplate==$game['shorten'].'-2') echo 'selected="selected"';?>><?php echo $game['shorten'].'-2 ('.$game['description'].')';?></option>
                        <option value="<?php echo $game['shorten'].'-3';?>" <?php if($thisTemplate==$game['shorten'].'-3') echo 'selected="selected"';?>><?php echo $game['shorten'].'-3 ('.$game['description'].')';?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <?php }?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-play-circle icon-white"></i> <?php echo $gsprache->exec;?></button>
                </div>
            </div>
        </form>
    </div>
</div>