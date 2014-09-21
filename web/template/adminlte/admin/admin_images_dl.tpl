<section class="content-header">
    <h1><?php echo $gsprache->template;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=im"><?php echo $gsprache->template;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $description;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=im&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=im" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="inputDesc"><?php echo $sprache->description;?></label>
                            <div class="controls"><input class="form-control" id="inputDesc" type="text" name="description" value="<?php echo $description;?>" disabled="disabled"></div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash">&nbsp;<?php echo $gsprache->del;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>