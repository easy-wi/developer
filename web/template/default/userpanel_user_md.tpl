<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->user;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <form class="form-horizontal" action="userpanel.php?w=se&amp;r=se" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label" for="mail_backup"><?php echo $sprache->mail_backup;?></label>
                <div class="controls">
                    <input id="mail_backup" type="checkbox" name="mail_backup" value="Y" <?php if ($mail_backup=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mail_serverdown"><?php echo $sprache->mail_serverdown;?></label>
                <div class="controls">
                    <input id="mail_serverdown" type="checkbox" name="mail_serverdown" value="Y" <?php if ($mail_serverdown=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mail_ticket"><?php echo $sprache->mail_ticket;?></label>
                <div class="controls">
                    <input id="mail_ticket" type="checkbox" name="mail_ticket" value="Y" <?php if ($mail_ticket=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="fname"><?php echo $sprache->fname;?></label>
                <div class="controls">
                    <input id="fname" type="text" name="name" value="<?php echo $name;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="vname"><?php echo $sprache->vname;?></label>
                <div class="controls">
                    <input id="vname" type="text" name="vname" value="<?php echo $vname;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mail"><?php echo $sprache->email;?>*</label>
                <div class="controls">
                    <input id="mail" type="email" name="mail" value="<?php echo $mail;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="tel"><?php echo $sprache->tel;?></label>
                <div class="controls">
                    <input id="tel" type="text" name="phone" value="<?php echo $phone;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="handy"><?php echo $sprache->han;?></label>
                <div class="controls">
                    <input id="handy" type="text" name="handy" value="<?php echo $handy;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="stadt"><?php echo $sprache->stadt;?></label>
                <div class="controls">
                    <input id="stadt" type="text" name="city" value="<?php echo $city;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="cityn"><?php echo $sprache->plz;?></label>
                <div class="controls">
                    <input id="cityn" type="text" name="cityn" value="<?php echo $cityn;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="street"><?php echo $sprache->str;?></label>
                <div class="controls">
                    <input id="street" type="text" name="street" value="<?php echo $street;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="streetn"><?php echo $sprache->hnum;?></label>
                <div class="controls">
                    <input id="streetn" type="text" name="streetn" value="<?php echo $streetn;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="md">
                </div>
            </div>
        </form>
    </div>
</div>