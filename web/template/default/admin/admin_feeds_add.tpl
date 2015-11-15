<section class="content-header">
    <h1><?php echo $gsprache->feeds.' '.$gsprache->add;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=fn"><i class="fa fa-rss"></i> <?php echo $gsprache->feeds;?></a></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">

                <form role="form" action="admin.php?w=fe&amp;d=ad&amp;r=fe" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <select class="form-control" id="inputActive" name="active">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputTwitter">Twitter</label>
                            <select class="form-control" id="inputTwitter" name="twitter" onchange="SwitchShowHideRows(this.value, 'switch', 1);">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="Y display_none switch form-group">
                            <label for="inputTwitterLoginname">Twitter Loginname</label>
                            <input class="form-control" id="inputTwitterLoginname" type="text" name="loginName" value="">
                        </div>

                        <div class="N switch form-group">
                            <label for="inputFeedUrl">URL</label>
                            <input class="form-control" id="inputFeedUrl" type="text" name="feedUrl" value="">
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>