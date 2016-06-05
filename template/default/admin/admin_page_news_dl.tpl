<section class="content-header">
    <h1><?php echo $gsprache->news;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=pn"><i class="fa fa-globe"></i> CMS</a></li>
        <li><a href="admin.php?w=pn"><i class="fa fa-newspaper-o"></i> <?php echo $gsprache->news;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $page_title;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=pn&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=pn" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputTitle"><?php echo $sprache->title;?></label>
                            <div class="controls"><input class="form-control" id="inputTitle" type="text" name="title" value="<?php echo $page_title;?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputLanguages"><?php echo $sprache->languages;?></label>
                            <div class="controls"><input class="form-control" id="inputLanguages" type="text" name="inputLanguages" value="<?php echo implode(', ',$p_languages);?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputReleased"><?php echo $sprache->released;?></label>
                            <div class="controls"><input class="form-control" id="inputReleased" type="text" name="released" value="<?php echo $page_active;?>" readonly="readonly"></div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputDelete" type="submit"><i class="fa fa-trash">&nbsp;<?php echo $gsprache->del;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>