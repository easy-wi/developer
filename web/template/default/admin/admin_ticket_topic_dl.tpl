<section class="content-header">
    <h1><?php echo $sprache->heading;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ti"><i class="fa fa-life-ring"></i> <?php echo $gsprache->support;?></a></li>
        <li><a href="admin.php?w=ti&amp;d=mt"><i class="fa fa-wrench"></i> <?php echo $sprache->heading;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $topic;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=ti&amp;d=dt&amp;id=<?php echo $id;?>&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputNickname"><?php echo $sprache->topic_name;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputNickname" type="text" name="nickname" value="<?php echo $topic;?>" disabled="disabled">
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputDelete" type="submit"><i class="fa fa-trash-o"></i>&nbsp;<?php echo $gsprache->del;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>