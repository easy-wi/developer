<section class="content-header">
    <h1><?php echo $gsprache->feeds.' '.$gsprache->del;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->feeds;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $feedUrl;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=fe&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=fe" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputFeedUrl">URL</label>
                            <input class="form-control" id="inputFeedUrl" type="text" name="feedUrl" value="<?php echo $feedUrl;?>" disabled="disabled">
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>