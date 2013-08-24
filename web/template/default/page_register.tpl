<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li><?php echo $page_data->pages['register']['linkname'];?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <?php if(count($error)>0){ ?><div class="span11 alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> <?php echo implode('<br />',$error);?></div><?php }?>
    <div class="span6">
        <form class="form-horizontal" action="<?php echo $page_data->pages['register']['link'];?>" method="post">
            <input type="hidden" name="token" value="<?php echo $token;?>">

            <div class="control-group <?php if(isset($alert['email'])) echo 'error';?>">
                <label class="control-label" for="inputMail"><?php echo $langObject->email;?></label>
                <div class="controls">
                    <input type="text" id="inputMail" name="mail" value="<?php echo $mail;?>" placeholder="Email Address*" required>
                </div>
            </div>
            <div class="control-group <?php if(isset($alert['password'])) echo 'error';?>">
                <label class="control-label" for="inputPassword"><?php echo $langObject->password;?></label>
                <div class="controls">
                    <input type="text" id="inputPassword" name="password" value="<?php echo $password;?>" placeholder="Passwort*" required>
                </div>
            </div>
            <div class="control-group <?php if(isset($alert['password2'])) echo 'error';?>">
                <label class="control-label" for="inputPasswordSecond"><?php echo $langObject->passw_2;?></label>
                <div class="controls">
                    <input type="text" id="inputPasswordSecond" name="passwordsecond" value="<?php echo $passwordsecond;?>" placeholder="Passwort wiederhohlen*" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSalutation"><?php echo $langObject->salutation;?></label>
                <div class="controls">
                    <select id="inputSalutation" name="salutation">
                        <option value="1"><?php echo $langObject->salutation2;?></option>
                        <option value="2"><?php echo $langObject->salutation3;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCountry"><?php echo $langObject->country;?></label>
                <div class="controls">
                    <select id="inputCountry" name="flagmenu">
                        <?php foreach ($selectlanguages as $la) { ?>
                        <option value="<?php echo $la;?>"><?php echo $la;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFname"><?php echo $langObject->fname;?></label>
                <div class="controls">
                    <input id="inputFname" type="text" name="name" value="<?php echo $name;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputVname"><?php echo $langObject->vname;?></label>
                <div class="controls">
                    <input id="inputVname" type="text" name="vname" value="<?php echo $vname;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputBirthday"><?php echo $langObject->birthday;?></label>
                <div class="controls">
                    <input id="inputBirthday" type="text" name="birthday" value="<?php echo $bdayShow;?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputTel"><?php echo $langObject->tel;?></label>
                <div class="controls">
                    <input id="inputTel" type="tel" name="phone" value="">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFax">Fax</label>
                <div class="controls">
                    <input id="inputFax" type="tel" name="fax" value="">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHandy"><?php echo $langObject->han;?></label>
                <div class="controls">
                    <input id="inputHandy" type="tel" name="handy" value="">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCity"><?php echo $langObject->stadt;?></label>
                <div class="controls">
                    <input id="inputCity" type="text" name="city" value="">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputCityn"><?php echo $langObject->plz;?></label>
                <div class="controls">
                    <input id="inputCityn" type="text" name="cityn" value="">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputStreet"><?php echo $langObject->str;?></label>
                <div class="controls">
                    <input id="inputStreet" type="text" name="street" value="">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHnum"><?php echo $langObject->hnum;?></label>
                <div class="controls">
                    <input id="inputHnum" type="text" name="streetn" value="">
                </div>
            </div>
            <?php if(isset($tou)) { ?>
            <div class="control-group <?php if(isset($alert['tou'])) echo 'error';?>">
                <label class="control-label" for="inputTou"><?php echo $tou;?></label>
                <div class="controls">
                    <input type="checkbox" id="inputTou" name="tou" value="Y" <?php if($ui->active('tou','post')=='Y') echo 'checked="checked"';?>>
                </div>
            </div>
            <?php }?>
            <div class="control-group hide">
                <label class="control-label" for="inputEMail"><?php echo $langObject->email;?></label>
                <div class="controls">
                    <input type="text" id="inputEMail" name="email" value="" placeholder="Email Address*">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>