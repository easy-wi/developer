<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li><?php echo $page_data->pages['news']['href'];?> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_title;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <h2><?php echo $page_title;?></h2>
    <div class="span11"><?php echo $page_text;?></div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $page_sprache->tag.': '.implode(', ',returnPlainArray($allTags,'href'));?>
    </div>
    <div class="span6">
        <?php echo $page_sprache->categories.': '.implode(', ',returnPlainArray($allCategories,'href'));?>
    </div>
</div>
<?php if($comments=='Y'){ ?>
<?php if(count($commentArray)>0){ ?>
<div class="row-fluid">
    <?php foreach($commentArray as $c){ ?>
    <div class="span11">
        <span><a href="<?php echo $c['homepage']; ?>" rel="external nofollow" class="url"><?php echo $c['author']; ?></a></span>
        <span><?php echo $c['date']; ?></span>
        <p><?php echo $c['comment']; ?></p>
    </div>
    <?php } ?>
</div>
<?php } ?>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" action="" method="post">
            <?php if(!isset($admin_id) and !isset($user_id)){ ?>
            <div class="control-group">
                <label class="control-label" for="inputAuthor">Name</label>
                <div class="controls">
                    <input class="input-xxlarge" name="author" id="inputAuthor" type="text" value="<?php echo $author;?>" required >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMail">Email</label>
                <div class="controls">
                    <input class="input-xxlarge" name="inputMail" id="inputAuthor" type="email" value="<?php echo $email;?>" <?php if($mailRequired=='Y') echo 'required';?> >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputWebsite">Website</label>
                <div class="controls">
                    <input class="input-xxlarge" name="url" id="inputWebsite" type="url" value="<?php echo $url;?>" >
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputWebsite">Text</label>
                <div class="controls">
                    <textarea class="input-xxlarge" name="comment" id="inputWebsite" rows="10" required><?php echo $comment;?></textarea>
                </div>
            </div>
            <div class="hide" aria-hidden="true">
                <input type="hidden" name="token" value="<?php echo $token;?>" />
                <input type="text" name="mail" value="" />
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