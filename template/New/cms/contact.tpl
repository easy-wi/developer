<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_data->pages['contact']['linkname'];?></li>
        </ul>
    </div>
</div>
<?php if (isset($error) and count($error)>0) { ?>
<div class="row-fluid">
    <div class="span9">
        <div class="alert alert-error"><?php echo 'Error: '.implode('<br />',$error);?></div>
    </div>
</div>
<?php }; if (isset($success)) { ?>
<div class="row-fluid">
    <div class="span9">
        <div class="alert alert-success"><?php echo $page_sprache->mailSend;?></div>
    </div>
</div>
<?php }else{ ?>
<div class="container">
<div class="row">
<div class="col-sm-4">
<h3>Schreib uns!</h3>
<hr>

</div>
    
<div class="col-sm-8 contact-form">
<form id="contact" method="post" class="form" role="form">
<div class="row">
<div class="col-xs-6 col-md-6 form-group">
 <input name="name" type="text" placeholder="Name" id="inputName" class="input-xxlarge" value="<?php echo $name; ?>" required >
</div>
<div class="col-xs-6 col-md-6 form-group"> 
<input name="email" type="email" placeholder="E-Mail" id="inputMail" class="input-xxlarge" value="<?php echo $email; ?>" required >
</div>
</div>
<label class="control-label" for="inputMessage">
  <textarea style="width: 300%;" name="comments" id="inputMessage" placeholder="Nachricht" rows="10" class="input-xxlarge" required><?php echo $comments; ?></textarea>
<br />
<div class="row">
<div class="col-xs-12 col-md-12 form-group">
    <label class="control-label" for="inputSubmit"></label>
 <button class="btn btn-primary" id="inputSubmit" type="submit">Submit</button>
</form>
</div>
</div>
<?php } ?>
