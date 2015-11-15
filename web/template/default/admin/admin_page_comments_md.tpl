<section class="content-header">
    <h1><?php echo $gsprache->comments;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=pn"><i class="fa fa-globe"></i> CMS</a></li>
        <li><a href="admin.php?w=pc"><i class="fa fa-comments"></i> <?php echo $gsprache->comments;?></a></li>
        <li class="active"><?php echo $gsprache->mod;?></li>
    </ol>
</section>

<section class="content">


    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=pc&amp;d=md&amp;id=<?php echo $id;?>&amp;r=pc" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputAuthor"><?php echo $sprache->author;?></label>
                            <div class="controls"><input class="form-control" id="inputAuthor" type="text" name="name" value="<?php echo $authorname?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputMail">E-Mail</label>
                            <div class="controls"><input class="form-control" id="inputMail" type="text" name="mail" value="<?php echo $email?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputIP">IP</label>
                            <div class="controls"><input class="form-control" id="inputIP" type="text" name="ip" value="<?php echo $ip?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDNS">DNS</label>
                            <div class="controls"><input class="form-control" id="inputDNS" type="text" name="dns" value="<?php echo $dns?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDate"><?php echo $sprache->date;?></label>
                            <div class="controls"><input class="form-control" id="inputDate" type="text" name="date" value="<?php echo $date?>" readonly="readonly"></div>
                        </div>

                        <?php if($markedSpam=='Y'){ ?>
                        <div class="form-group">
                            <label for="inputSpam">Spam</label>
                            <div class="controls"><input class="form-control" id="inputSpam" type="text" name="spam" value="<?php echo $spamReason?>" readonly="readonly"></div>
                        </div>
                        <?php } ?>

                        <div class="form-group">
                            <label for="inputModerate"><?php echo $sprache->moderate;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputModerate" name="moderateAccepted">
                                    <option value="Y"><?php echo $gsprache->no;?></option>
                                    <option value="N" <?php if($moderateAccepted=='N') echo 'selected="selected"';?> ><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputSpam">Spam</label>
                            <div class="controls">
                                <select class="form-control" id="inputSpam" name="markedSpam">
                                    <option value="Y"><?php echo $gsprache->yes;?></option>
                                    <option value="N" <?php if($markedSpam=='N') echo 'selected="selected"';?> ><?php echo $gsprache->no;?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputURL">URL</label>
                            <div class="controls">
                                <input class="form-control" id="inputURL" type="url" name="homepage" value="<?php echo $homepage;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputComment"></label>
                            <div class="controls">
                                <textarea class="form-control" id="inputComment" name="comment" rows="5" required><?php echo $comment;?></textarea>
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