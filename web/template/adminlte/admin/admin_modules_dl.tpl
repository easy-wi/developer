<section class="content-header">
    <h1><?php echo $gsprache->modules.' '.$gsprache->del;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->modules;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $moduleFile;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
<div class="col-md-6">
    <div class="box box-info">
        <div class="box-body">
            <dl class="dl-horizontal">
                <dt><?php echo $sprache->file;?></dt>
                <dd><?php echo $moduleFile;?></dd>
            </dl>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="box box-info">
        <div class="box-body">
        <form class="form-horizontal" action="admin.php?w=mo&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=mo" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">

        </div>
    </div>
                <label class="control-label" for="inputEdit"></label>
                    <button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
        </form>
</div>
</section>