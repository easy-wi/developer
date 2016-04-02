<section class="content-header">
    <h1>E-Mail <?php echo $gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li class="active"><i class="fa fa-envelope"></i> E-Mail <?php echo $gsprache->settings;?></li>
    </ol>
</section>
<form role="form" action="admin.php?w=sm&amp;r=sm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
<input type="hidden" name="token" value="<?php echo token();?>">
<input type="hidden" name="action" value="md">
<section class="content">
    <div class="row">
       <div class="col-md-6">
              <div class="box box-primary boxtest">
                    <div class="box-header with-border">
                       <h3 class="box-title">E-Mail Settings</h3>
                       <div class="box-tools pull-right">
                         <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                       </div><!-- /.box-tools -->
                      </div>
                      <div class="box-body">
                          <div class="form-group">
                              <label class="control-label" for="inputType">E-Mail</label>
                              <div class="controls">
                                  <select class="form-control" id="inputType" name="email_settings_type" onchange="SwitchShowHideRows(this.value,'switch',1);">
                                      <option value="P">PHP Mail</option>
                                      <option value="S" <?php if($email_settings['email_settings_type']=='S') echo 'selected="selected"';?>>SMTP</option>
                                      <option value="D" <?php if($email_settings['email_settings_type']=='D') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                                  </select>
                              </div>
                          </div>
  
                          <div class="form-group">
                              <label class="control-label" for="inputEmail"><?php echo $sprache->email;?></label>
                              <div class="controls">
                                  <input class="form-control" id="inputEmail" type="email" name="email" value="<?php echo $email_settings['email'];?>">
                              </div>
                          </div>
  
                          <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                              <label class="control-label" for="inputSSL">SSL/TLS</label>
                              <div class="controls">
                                  <select class="form-control" id="inputSSL" name="email_settings_ssl">
                                      <option value="N"><?php echo $gsprache->no;?></option>
                                      <option value="S" <?php if($email_settings['email_settings_ssl']=='S') echo 'selected="selected"';?>>SSL</option>
                                      <option value="T" <?php if($email_settings['email_settings_ssl']=='T') echo 'selected="selected"';?>>TLS</option>
                                  </select>
                              </div>
                          </div>
  
                          <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                              <label class="control-label" for="inputHost">Host</label>
                              <div class="controls">
                                  <input class="form-control" id="inputHost" type="text" name="email_settings_host" value="<?php echo $email_settings['email_settings_host'];?>">
                              </div>
                          </div>
  
                          <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                              <label class="control-label" for="inputPort">Port</label>
                              <div class="controls">
                                  <input class="form-control" id="inputPort" type="text" name="email_settings_port" value="<?php echo $email_settings['email_settings_port'];?>">
                              </div>
                          </div>
  
                          <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                              <label class="control-label" for="inputUser"><?php echo $gsprache->user;?></label>
                              <div class="controls">
                                  <input class="form-control" id="inputUser" type="text" name="email_settings_user" value="<?php echo $email_settings['email_settings_user'];?>">
                              </div>
                          </div>
  
                          <div class="S switch form-group <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?>">
                              <label class="control-label" for="inputPassword"><?php echo $sprache->password;?></label>
                              <div class="controls">
                                  <input class="form-control" id="inputPassword" type="text" name="email_settings_password" value="<?php echo $email_settings['email_settings_password'];?>">
                              </div>
                          </div>
                          <div class="S switch box-footer <?php if($email_settings['email_settings_type']!='S') echo 'display_none';?> smtp">
                           <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                            <div class="pull-right" style="margin-left:5px;"><a class="btn btn-success" id="submitTest"><i class="fa fa-retweet"></i> Testing</a></div>
                            <div class="pull-right" id="smtptestresult"></div>
                          </div>
                      </div>
              </div>
       </div>
       <div class="col-md-6">
              <div class="box box-primary">
                    <div class="box-header with-border">
                       <h3 class="box-title">E-Mail Settings</h3>
                       <div class="box-tools pull-right">
                         <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                       </div><!-- /.box-tools -->
                      </div>
                      <div class="box-body">
                      <p>Hier können Sie alle E-Mail Template und Einstellungen ändern. Um ein Template zu ändern, klicken Sie das gewünschte Template aus der Kategorie und Verändern diese. Bei einer Änderung der SMTP Verbindung ist es ratsam, diese mit dem <a class="btn btn-success btn-sm"><i class="fa fa-retweet"></i> Testing</a>-Button zu testen.</p>                       
                      </div>
              </div>
       </div>
    </div>

   <h3>E-Mail Templates</h3>
   <hr/>
<!-- Categories -->
<?php echo $resultHtmlCategories; ?>
<!-- ./Categories -->
</section>
</form>
<!-- CK Editor -->
<script src="https://cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script>
<script>
  $(function () {
 CKEDITOR.replace('inputEmailRegards');
 CKEDITOR.replace('inputEmailFooter');
 CKEDITOR.config.extraAllowedContent = '*{*}';
 CKEDITOR.config.allowedContent = true;
 
  });
</script>