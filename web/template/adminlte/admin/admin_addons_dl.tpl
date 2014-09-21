<section class="content-header">
    <h1><?php echo $sprache->heading_addons;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ad"><?php echo $gsprache->addon;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $menudescription;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=ad&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=ad" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">

                        <div class="form-group">
                            <label><?php echo $sprache->sure2;?></label>
                        </div>

                        <div class="form-group">
                            <label for="inputAddon2"><?php echo $sprache->addon2;?></label>
                            <input class="form-control" id="inputAddon2" type="text" name="menudescription" value="<?php echo $menudescription;?>" disabled="disabled">
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash-o">&nbsp;<?php echo $gsprache->del;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>