<section class="content-header">
    <h1><?php echo $gsprache->feeds.' '.$gsprache->add;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->feeds;?></a></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="box box-info">
        <div class="box-body">
        <form role="form" action="admin.php?w=fe&amp;d=ad&amp;r=fe" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="ad">
            <div class="form-group">
                <label for="inputActive"><?php echo $sprache->active;?></label>
                    <select class="form-control" id="inputActive" name="active">
                        <option value="N"><?php echo $gsprache->no;?></option>
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                    </select>
            </div>
            <div class="form-group">
                <label class="control-label" for="inputTwitter">Twitter</label>
                    <select class="form-control" id="inputTwitter" name="twitter" onchange="SwitchShowHideRows(this.value);">
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
                <label for="inputEdit"></label>
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-edit"></i> <?php echo $gsprache->save;?></button>
        </form>
        </div>
    </div>
</section>