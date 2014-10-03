<section class="content-header">
    <h1><?php echo $gsprache->modules;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=se"><i class="fa fa-wrench"></i> <?php echo $gsprache->settings;?></a></li>
        <li><a href="admin.php?w=mo"><i class="fa fa-th-large"></i> <?php echo $gsprache->modules;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $moduleFile;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=mo&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=mo" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputFile"><?php echo $sprache->file;?></label>
                            <div class="controls"><input type="text" class="form-control" id="inputFile" name="file" value="<?php echo $moduleFile;?>" disabled="disabled"></div>
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