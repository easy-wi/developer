<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->user;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="admin.php?w=su&amp;r=su" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <?php if(count($serviceProviders) > 0 ) echo '<h2>Social Auth</h2>';?>
            <?php foreach($serviceProviders as $sp){ ?>
            <div class="control-group">
                <label class="control-label" for="sp<?php echo $sp['sp'];?>"><?php echo $sp['sp'];?></label>
                <div class="controls">
                    <?php if (strlen($sp['spUserId'])==0){ ?>
                    <a class="btn btn-block btn-social btn-<?php echo strtolower($sp['sp']);?> span10" href="login.php?serviceProvider=<?php echo $sp['sp'];?>" id="sp<?php echo $sp['sp'];?>">
                        <i class="fa fa-<?php echo strtolower($sp['sp']);?>"></i> <?php echo $sprache->socialConnect.' '.$sp['sp'];?>
                    </a>
                    <?php } else { ?>
                    <a class="btn btn-block btn-social btn-<?php echo strtolower($sp['sp']);?> span10" href="admin.php?w=su&amp;spUser=<?php echo $sp['spUserId'];?>&amp;spId=<?php echo $sp['spId'];?>&amp;r=su" id="sp<?php echo $sp['sp'];?>">
                        <i class="fa fa-<?php echo strtolower($sp['sp']);?>"></i> <?php echo $sprache->socialRemove.' '.$sp['sp'];?>
                    </a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <?php if(count($serviceProviders) > 0 ) echo '<hr>';?>
            <h2>Mails</h2>
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
                <label class="control-label" for="inputMailUpdate"><?php echo $sprache->mail_gsupdate;?></label>
                <div class="controls">
                    <input id="inputMailUpdate" type="checkbox" name="mail_gsupdate" value="Y" <?php if ($mail_gsupdate=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMailSecurity"><?php echo $sprache->mail_securitybreach;?></label>
                <div class="controls">
                    <input id="inputMailSecurity" type="checkbox" name="mail_securitybreach" value="Y" <?php if ($mail_securitybreach=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMailVserver"><?php echo $sprache->mail_vserver;?></label>
                <div class="controls">
                    <input id="inputMailVserver" type="checkbox" name="mail_vserver" value="Y" <?php if ($mail_vserver=="Y") echo 'checked="checked"'; ?>>
                </div>
            </div>
            <hr>
            <h2><?php echo $gsprache->user;?></h2>
            <div class="control-group">
                <label class="control-label" for="fname"><?php echo $sprache->fname;?></label>
                <div class="controls">
                    <input class="span10" id="fname" type="text" name="name" value="<?php echo $name;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="vname"><?php echo $sprache->vname;?></label>
                <div class="controls">
                    <input class="span10" id="vname" type="text" name="vname" value="<?php echo $vname;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mail"><?php echo $sprache->email;?>*</label>
                <div class="controls">
                    <input class="span10" id="mail" type="email" name="mail" value="<?php echo $mail;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="tel"><?php echo $sprache->tel;?></label>
                <div class="controls">
                    <input class="span10" id="tel" type="text" name="phone" value="<?php echo $phone;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="handy"><?php echo $sprache->han;?></label>
                <div class="controls">
                    <input class="span10" id="handy" type="text" name="handy" value="<?php echo $handy;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="stadt"><?php echo $sprache->stadt;?></label>
                <div class="controls">
                    <input class="span10" id="stadt" type="text" name="city" value="<?php echo $city;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="cityn"><?php echo $sprache->plz;?></label>
                <div class="controls">
                    <input class="span10" id="cityn" type="text" name="cityn" value="<?php echo $cityn;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="street"><?php echo $sprache->str;?></label>
                <div class="controls">
                    <input class="span10" id="street" type="text" name="street" value="<?php echo $street;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="streetn"><?php echo $sprache->hnum;?></label>
                <div class="controls">
                    <input class="span10" id="streetn" type="text" name="streetn" value="<?php echo $streetn;?>">
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