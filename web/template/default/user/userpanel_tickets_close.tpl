<section class="content-header">
    <h1><?php echo $gsprache->support;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=ti"><?php echo $gsprache->support;?></a></li>
        <li class="active"><?php echo $sprache->close_heading;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <form role="form" action="userpanel.php?w=ti&amp;d=md&amp;id=<?php echo $id;?>&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="cl">

                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="rating"><?php echo $sprache->rating;?></label>
                            <select class="form-control" id="rating" name="rating">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="comment"><?php echo $sprache->comment;?></label>
                            <textarea class="form-control" id="comment" name="comment" rows="10"></textarea>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-lock"></i> <?php echo $sprache->close_heading;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>