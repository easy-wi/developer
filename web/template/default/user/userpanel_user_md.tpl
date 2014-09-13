<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->user;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->user;?></li>
        <li class="active"><?php echo $gsprache->settings;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-info">
                <form role="form" action="userpanel.php?w=se&amp;r=se" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input class="form-control" type="hidden" name="action" value="md">

                    <div class="box-body">
                        <h4><strong><?php echo $gsprache->user;?></strong></h4>

                        <div class="form-group">
                            <label for="fname"><?php echo $sprache->fname;?></label>
                            <input class="form-control" id="fname" type="text" name="name" value="<?php echo $name;?>">
                        </div>

                        <div class="form-group">
                            <label for="vname"><?php echo $sprache->vname;?></label>
                            <input class="form-control" id="vname" type="text" name="vname" value="<?php echo $vname;?>">
                        </div>

                        <div class="form-group">
                            <label for="mail"><?php echo $sprache->email;?>*</label>
                            <input class="form-control" id="mail" type="email" name="mail" value="<?php echo $mail;?>" required>
                        </div>

                        <div class="form-group">
                            <label for="tel"><?php echo $sprache->tel;?></label>
                            <input class="form-control" id="tel" type="text" name="phone" value="<?php echo $phone;?>">
                        </div>

                        <div class="form-group">
                            <label for="handy"><?php echo $sprache->han;?></label>
                            <input class="form-control" id="handy" type="text" name="handy" value="<?php echo $handy;?>">
                        </div>

                        <div class="form-group">
                            <label for="stadt"><?php echo $sprache->stadt;?></label>
                            <input class="form-control" id="stadt" type="text" name="city" value="<?php echo $city;?>">
                        </div>

                        <div class="form-group">
                            <label for="cityn"><?php echo $sprache->plz;?></label>
                            <input class="form-control" id="cityn" type="text" name="cityn" value="<?php echo $cityn;?>">
                        </div>

                        <div class="form-group">
                            <label for="street"><?php echo $sprache->str;?></label>
                            <input class="form-control" id="street" type="text" name="street" value="<?php echo $street;?>">
                        </div>

                        <div class="form-group">
                            <label for="streetn"><?php echo $sprache->hnum;?></label>
                            <input class="form-control" id="streetn" type="text" name="streetn" value="<?php echo $streetn;?>">
                        </div>

                        <div class="form-group">
                            <label for="show_help_text"><?php echo $sprache->show_help_text;?></label>
                            <select class="form-control" id="show_help_text" name="show_help_text">
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                                <option value="N"<?php if($show_help_text=='N') echo 'selected';?>><?php echo $gsprache->no;?></option>
                            </select>
                        </div>

                        <h4><strong><?php echo $sprache->email;?></strong></h4>

                        <div class="form-group">
                            <input id="mail_backup" type="checkbox" name="mail_backup" value="Y" <?php if ($mail_backup=="Y") echo 'checked="checked"'; ?>>
                            <label for="mail_backup"><?php echo $sprache->mail_backup;?></label>
                        </div>

                        <div class="form-group">
                            <input id="mail_serverdown" type="checkbox" name="mail_serverdown" value="Y" <?php if ($mail_serverdown=="Y") echo 'checked="checked"'; ?>>
                            <label for="mail_serverdown"><?php echo $sprache->mail_serverdown;?></label>
                        </div>

                        <div class="form-group">
                            <input id="mail_ticket" type="checkbox" name="mail_ticket" value="Y" <?php if ($mail_ticket=="Y") echo 'checked="checked"'; ?>>
                            <label for="mail_ticket"><?php echo $sprache->mail_ticket;?></label>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-body">
                    <?php if(count($serviceProviders) > 0 ) echo '<h4><strong>Social Auth</strong></h4>';?>
                    <?php foreach($serviceProviders as $sp){ ?>
                    <div class="form-group">
                        <label for="sp<?php echo $sp['sp'];?>"><?php echo $sp['sp'];?></label>
                        <?php if (strlen($sp['spUserId'])==0){ ?>
                        <a class="btn btn-block btn-social btn-<?php echo strtolower($sp['sp']);?> span10" href="login.php?serviceProvider=<?php echo $sp['sp'];?>" id="sp<?php echo $sp['sp'];?>">
                            <i class="fa fa-<?php echo strtolower($sp['sp']);?>"></i> <?php echo $sprache->socialConnect.' '.$sp['sp'];?>
                        </a>
                        <?php } else { ?>
                        <a class="btn btn-block btn-social btn-<?php echo strtolower($sp['sp']);?> span10" href="userpanel.php?w=se&amp;spUser=<?php echo $sp['spUserId'];?>&amp;spId=<?php echo $sp['spId'];?>&amp;r=se" id="sp<?php echo $sp['sp'];?>">
                            <i class="fa fa-<?php echo strtolower($sp['sp']);?>"></i> <?php echo $sprache->socialRemove.' '.$sp['sp'];?>
                        </a>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>