<section class="content-header">
    <h1>E-Mail <?php echo $gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li class="active"><i class="fa fa-envelope"></i> E-Mail <?php echo $gsprache->settings;?></li>
    </ol>
</section>
<form role="form" action="admin.php?w=sm&d=add&r=sm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
<input type="hidden" name="token" value="<?php echo token();?>">
<input type="hidden" name="id" value="<?php echo $email_id;?>">
<input type="hidden" name="email_setting_name" value="<?php echo $email_setting_name;?>">
<input type="hidden" name="email_setting_language" value="<?php echo $email_language;?>">
<input type="hidden" name="email_setting_category" value="<?php echo $email_catid;?>">
<section class="content">
    <div class="row">
     <div class="col-md-12">
       <div class="box box-primary">
        <div class="box-header with-border">
         <h3 class="box-title">E-Mail Template Setting</h3>
        </div>
        <div class="box-body">
         <label class="control-label" for="inlineEmailTemplate"><?php echo $gsprache->template;?> Setting</label>
          <table class="formmail" cellspacing="2" cellpadding="3">
            <tbody>
              <tr>
                <td class="formmail_left">Template name:</td>
                <td class="formmail_right">
                    <input class="form-control formmailfield" type="text" name="email_templatename" value="<?php echo $email_setting_name;?>" disabled>
                </td>
              </tr>
              <tr>
                <td class="formmail_left">Subject:</td>
                <td class="formmail_right">
                    <input class="form-control formmailfield" id="inputSubject" type="text" name="email_subject" value="<?php echo $email_subject;?>">
                </td>
              </tr>
              <tr>
                <td class="formmail_left">Copy CC:</td>
                <td class="formmail_right">
                    <input class="form-control formmailfield" id="" type="text" name="ccmailing" placeholder="example@example.com" value="<?php echo $email_ccmailing;?>">
                </td>
              </tr>
              <tr>
                <td class="formmail_left">Copy BCC:</td>
                <td class="formmail_right">
                    <input class="form-control formmailfield" id="" type="text" name="bccmailing" placeholder="example@example.com" value="<?php echo $email_bccmailing;?>">
                </td>
              </tr>
              <tr style="display:none;">
                <td class="formmail_left" class="display:none;">Attachments:</td>
                <td class="formmail_right" class="display:none;">
                <div id="attachment">
                 <input name="attachments[]" type="file" class="form-control">
                </div>
                  <a href="#" id="addattachments" class="btn btn-success btn-xs" style="margin: 5px 2px;"><i class="fa fa-plus-circle"></i> Add</a>
                </td>
              </tr>
            </tbody>
          </table>
           <div class="email_body_<?php echo $email_language;?> form-group">
             <label class="control-label" for="email_body"><?php echo $gsprache->template;?> <img src="images/flags/<?php echo $email_language;?>.png" alt="Flag: <?php echo $email_language;?>.png"/></label>
             <div class="controls">
               <textarea class="form-control" id="email_body_<?php echo $email_language;?>" name="email_body" rows="30"><?php echo $email_body;?></textarea>
             </div>
           </div>
        </div>
        <div class="box-footer">
          <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
          <div class="pull-right">
            <a href="admin.php?w=sm" class="btn btn-danger">Back</a>
          </div>
        </div>
       </div> 
     </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Variable</h3>
            <div class="box-tools pull-right">
              <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box-tools -->
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover" style="font-size:0.8em;">
                <tbody>
                  <tr>
                    <th>Variable</th>
                    <th>Description/Result</th>
                  </tr>
                  <tr>
                    <td>%topic%</td>
                    <td>Subject</td>
                  </tr>
                  <tr>
                    <td>%id%</td>
                    <td>ID</td>
                  </tr>
                  <tr>
                    <td>%creationTime%</td>
                    <td>Create Time</td>
                  </tr>
                  <tr>
                    <td>%active%</td>
                    <td>Y=active or N=not active</td>
                  </tr>
                  <tr>
                    <td>%salutation%</td>
                    <td>Salutation</td>
                  </tr>
                  <tr>
                    <td>%cname%</td>
                    <td>Username</td>
                  </tr>
                  <tr>
                    <td>%fullname%</td>
                    <td>Firstname with lastname</td>
                  </tr>
                  <tr>
                    <td>%name%</td>
                    <td>Lastname</td>
                  </tr>
                  <tr>
                    <td>%vname%</td>
                    <td>Firstname</td>
                  </tr>
                  <tr>
                    <td>%birthday%</td>
                    <td>Date of Birth</td>
                  </tr>
                  <tr>
                    <td>%mail%</td>
                    <td>E-Mail Adress</td>
                  </tr>
                  <tr>
                    <td>%phone%</td>
                    <td>Phone number</td>
                  </tr>
                  <tr>
                    <td>%fax%</td>
                    <td>Fax number</td>
                  </tr>
                  <tr>
                    <td>%handy%</td>
                    <td>Mobile number</td>
                  </tr>
                  <tr>
                    <td>%country%</td>
                    <td>Country</td>
                  </tr>
                  <tr>
                    <td>%city%</td>
                    <td>City</td>
                  </tr>
                  <tr>
                    <td>%cityn%</td>
                    <td>PLZ</td>
                  </tr>
                  <tr>
                    <td>%street%</td>
                    <td>Streetname</td>
                  </tr>
                  <tr>
                    <td>%streetn%</td>
                    <td>House number</td>
                  </tr>
                  <tr>
                    <td>%language%</td>
                    <td>Languagecode (Ex. de)</td>
                  </tr>
                  <tr>
                    <td>%lastlogin%</td>
                    <td>Date of last login</td>
                  </tr>
                  <tr>
                    <td>%urlhost%</td>
                    <td>URL to login.php</td>
                  </tr>
                  <tr style="color:red;">
                    <td>%password%</td>
                    <td>Login password (only for Template: emailuseradd)</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
      </div>
    </div>
</section>
</form>
<!-- CK Editor -->
<script src="https://cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script>
<script>
  $(function () {
 CKEDITOR.replace('email_body_<?php echo $email_language;?>',{
	 height: 500
	 });
 CKEDITOR.config.extraAllowedContent = '*{*}';
 CKEDITOR.config.allowedContent = true;
 
  });
</script>