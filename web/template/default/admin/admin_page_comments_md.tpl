<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=pc"><?php echo $gsprache->comments;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->author;?></dt>
            <dd><?php echo $authorname;?> <a href="mailto:<?php echo $email;?>">(<?php echo $email;?>)</a></dd>
            <dt>IP</dt>
            <dd><?php echo $ip;?></dd>
            <dt>DNS</dt>
            <dd><?php echo $dns;?></dd>
            <dt><?php echo $sprache->date;?></dt>
            <dd><?php echo $date;?></dd>
            <?php if($markedSpam=='Y'){ ?>
            <dt>Spam</dt>
            <dd><?php echo $spamReason;?></dd>
            <?php } ?>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=pc&amp;d=md&amp;id=<?php echo $id;?>&amp;r=pc" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputModerate"><?php echo $sprache->moderate;?></label>
                <div class="controls">
                    <select id="inputModerate" name="moderateAccepted">
                        <option value="Y"><?php echo $gsprache->no;?></option>
                        <option value="N" <?php if($moderateAccepted=='N') echo 'selected="selected"';?> ><?php echo $gsprache->yes;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputSpam">Spam</label>
                <div class="controls">
                    <select id="inputSpam" name="markedSpam">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($markedSpam=='N') echo 'selected="selected"';?> ><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputURL">URL</label>
                <div class="controls">
                    <input id="inputURL" type="url" name="homepage" value="<?php echo $homepage;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputComment"></label>
                <div class="controls">
                    <textarea class="span11" rows="10" id="inputComment" name="comment" rows="5" cols="75" required><?php echo $comment;?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>