<section class="content-header">
    <h1><?php echo $gsprache->userImport.' '.$gsprache->add;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->userImport;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $domain;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="box box-info">
        <div class="box-body">

            <form role="form" action="admin.php?w=ui&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=ui" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
                <input type="hidden" name="token" value="<?php echo token();?>">
                <input type="hidden" name="action" value="dl">
                <div class="form-group">
                    <label class="control-label" for="inputEdit"><?php echo $sprache->domain.' '.$ssl.$domain;?></label>
                </div>
                    <button class="btn btn-danger pull-left" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
            </form>
        </div>
    </div>
</section>