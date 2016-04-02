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
                <td class="formmail_left">Subject</td>
                <td class="formmail_right">
                    <input class="form-control formmailfield" id="inputSubject" type="text" name="email_subject" value="<?php echo $email_subject;?>">
                </td>
              </tr>
              <tr>
                <td class="formmail_left">Copy CC</td>
                <td class="formmail_right">
                    <input class="form-control formmailfield" id="" type="text" name="copy_cc" placeholder="example@example.com" value="">
                </td>
              </tr>
              <tr>
                <td class="formmail_left">Attachments</td>
                <td class="formmail_right">
                    <input name="attachments" type="file" class="form-control">
                    <input name="attachments" type="file" class="form-control">
                </td>
              </tr>
            </tbody>
          </table>
        
        
           <div class="form-group">
           <br>
           <?php foreach ($emaillanguage_xml as $array){ ?>
             <label class="checkbox-inline">
               <input id="inputCheckboxEmail<?php echo $array['lang'];?>" name="languages-emailvinstall[]" value="<?php echo $array['lang'];?>" onclick="textdrop('<?php echo $array['lang'];?>-emailvinstall');" type="checkbox" <?php if($array['style']==1) echo 'checked';?>> <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: 16_<?php echo $array['lang'];?>'.png"/>
             </label>
           <?php } ?>
           </div>

           <?php foreach ($emaillanguage_xml as $array) { ?>
           <div id="<?php echo $array['lang'];?>" class="form-group <?php if ($array['style']==0) echo 'display_none';?>">
             <label class="control-label" for="inputEmail<?php echo $array['lang'];?>">XML <img src="images/flags/<?php echo $array['lang'];?>.png" alt="Flag: <?php echo $array['lang'];?>.png"/></label>
             <div class="controls">
               <textarea class="form-control" id="inputEmail<?php echo $array['lang'];?>" name="<?php echo $email_setting_name;?>_xml_<?php echo $array['lang'];?>" rows="15"><?php echo $array['xml'];?></textarea>
             </div>
           </div>
           <?php }?>
           <div class="form-group">
             <label class="control-label" for="inlineEmailTemplate"><?php echo $gsprache->template;?></label>
             <div class="controls">
               <textarea class="form-control" id="inlineEmailTemplate" name="email_body" rows="30"><?php echo $email_body;?></textarea>
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
</section>
</form>
<!-- CK Editor -->
<script src="https://cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script>
<script>
  $(function () {
 CKEDITOR.replace('inlineEmailTemplate',{
	 height: 500
	 });
 CKEDITOR.config.extraAllowedContent = '*{*}';
 CKEDITOR.config.allowedContent = true;
 
  });
</script>