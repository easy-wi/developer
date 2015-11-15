<section class="content-header">
    <h1><?php echo $gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->settings;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=su&amp;r=su" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <?php if(count($serviceProviders) > 0 ) echo '<h3>Social Auth</h3>';?>

                        <?php foreach($serviceProviders as $sp){ ?>
                        <div class="control-group">

                            <label for="sp<?php echo $sp['sp'];?>">
                                <?php echo $sp['sp'];?>
                            </label>

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

                        <h3>Mails</h3>
                        <div class="form-group">
                            <label for="mail_backup">
                                <input id="mail_backup" type="checkbox" name="mail_backup" value="Y" <?php if ($mail_backup=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_backup;?>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="mail_serverdown">
                                <input id="mail_serverdown" type="checkbox" name="mail_serverdown" value="Y" <?php if ($mail_serverdown=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_serverdown;?>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="mail_ticket">
                                <input id="mail_ticket" type="checkbox" name="mail_ticket" value="Y" <?php if ($mail_ticket=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_ticket;?>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="inputMailUpdate">
                                <input id="inputMailUpdate" type="checkbox" name="mail_gsupdate" value="Y" <?php if ($mail_gsupdate=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_gsupdate;?>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="inputMailSecurity">
                                <input id="inputMailSecurity" type="checkbox" name="mail_securitybreach" value="Y" <?php if ($mail_securitybreach=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_securitybreach;?>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="inputMailVserver">
                                <input id="inputMailVserver" type="checkbox" name="mail_vserver" value="Y" <?php if ($mail_vserver=="Y") echo 'checked="checked"'; ?>>
                                <?php echo $sprache->mail_vserver;?>
                            </label>
                        </div>

                        <hr>
                        <h3><?php echo $gsprache->user;?></h3>

                        <div class="form-group">
                            <label for="fname"><?php echo $sprache->fname;?></label>
                            <div class="controls">
                                <input class="form-control" id="fname" type="text" name="name" value="<?php echo $name;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="vname"><?php echo $sprache->vname;?></label>
                            <div class="controls">
                                <input class="form-control" id="vname" type="text" name="vname" value="<?php echo $vname;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mail"><?php echo $sprache->email;?>*</label>
                            <div class="controls">
                                <input class="form-control" id="mail" type="email" name="mail" value="<?php echo $mail;?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tel"><?php echo $sprache->tel;?></label>
                            <div class="controls">
                                <input class="form-control" id="tel" type="text" name="phone" value="<?php echo $phone;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="handy"><?php echo $sprache->han;?></label>
                            <div class="controls">
                                <input class="form-control" id="handy" type="text" name="handy" value="<?php echo $handy;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="stadt"><?php echo $sprache->stadt;?></label>
                            <div class="controls">
                                <input class="form-control" id="stadt" type="text" name="city" value="<?php echo $city;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cityn"><?php echo $sprache->plz;?></label>
                            <div class="controls">
                                <input class="form-control" id="cityn" type="text" name="cityn" value="<?php echo $cityn;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="street"><?php echo $sprache->str;?></label>
                            <div class="controls">
                                <input class="form-control" id="street" type="text" name="street" value="<?php echo $street;?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="streetn"><?php echo $sprache->hnum;?></label>
                            <div class="controls">
                                <input class="form-control" id="streetn" type="text" name="streetn" value="<?php echo $streetn;?>">
                            </div>
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