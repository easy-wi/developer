<section class="content-header">
    <h1><?php echo $gsprache->feeds.' '.$gsprache->del;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->feeds;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $feedUrl;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="box box-info">
        <div class="box-body">
            <form role="form" action="admin.php?w=fe&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=fe" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
                <input type="hidden" name="token" value="<?php echo token();?>">
                <input type="hidden" name="action" value="dl">
    
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-rss"></i></span>
                    <input for="inputEdit" class="form-control" value="<?php echo $feedUrl;?>" disabled>
                </div>
                <br />
                    <button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
            </form>
        </div>
    </div>
</section>