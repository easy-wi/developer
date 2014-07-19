<section class="content-header">
    <h1><?php echo $sprache->heading_addons.' '.$gsprache->del;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $sprache->heading_addons;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $menudescription;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="box box-info">
        <div class="box-body">
            <?php echo $sprache->sure;?>
            <?php echo $sprache->sure2;?>

            <div class="form-group">
                <form role="form" action="admin.php?w=ad&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=ad" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">
			<br />
                        <label class="control-label" for="inputEdit"></label>
                        <button class="btn btn-danger pull-left" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
                </form>
            </div>
            
        </div>
    </div>
</section>