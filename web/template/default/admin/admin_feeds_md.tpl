<section class="content-header">
    <h1><?php echo $gsprache->feeds.' '.$gsprache->mod;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=fn"><i class="fa fa-rss"></i> <?php echo $gsprache->feeds;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php if ($twitter=='Y') echo 'Twitter'; else echo $feedUrl;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=fe&amp;d=md&amp;id=<?php echo $id;?>&amp;r=fe" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <select class="form-control" id="inputActive" name="active">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($active=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputTwitter">Twitter</label>
                            <select class="form-control" id="inputTwitter" name="twitter" onchange="SwitchShowHideRows(this.value);">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($twitter=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="Y<?php if ($twitter=='N') echo ' display_none';?> switch form-group">
                            <label for="inputTwitterLoginname">Twitter Loginname</label>
                            <input class="form-control" id="inputTwitterLoginname" type="text" name="loginName" value="<?php echo $loginName;?>">
                        </div>

                        <div class="N<?php if ($twitter=='Y') echo ' display_none';?> switch form-group">
                            <label for="inputFeedUrl">URL</label>
                            <input class="form-control" id="inputFeedUrl" type="text" name="feedUrl" value="<?php echo $feedUrl;?>">
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