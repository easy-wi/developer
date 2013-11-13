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
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="<?php echo $page_data->pages['contact']['link'];?>" method="post">
            <div class="control-group">
                <label class="control-label" for="inputName"><?php echo $page_sprache->name;?></label>
                <div class="controls">
                    <input name="name" type="text" id="inputName" class="input-xxlarge" value="<?php echo $name; ?>" required >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMail">E-Mail</label>
                <div class="controls">
                    <input name="email" type="email" id="inputMail" class="input-xxlarge" value="<?php echo $email; ?>" required >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMessage"><?php echo $page_sprache->message;?></label>
                <div class="controls">
                    <textarea name="comments" id="inputMessage" rows="10" class="input-xxlarge" required><?php echo $comments; ?></textarea>
                    <input name="token" type="hidden" value="<?php echo $token;?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSubmit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputSubmit" type="submit">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php } ?>