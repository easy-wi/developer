<section class="content-header">
    <h1><?php echo $gsprache->userImport;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=jb"><i class="fa fa-cloud"></i> <?php echo $gsprache->jobs.'/'.$gsprache->api;?></a></li>
        <li><a href="admin.php?w=ui"><i class="fa fa-download"></i> <?php echo $gsprache->userImport;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $domain;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=ui&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=ui" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputDomain"><?php echo $sprache->domain;?></label>
                            <input class="form-control" id="inputDomain" type="text" name="domain" value="<?php echo $ssl.$domain;?>" disabled="disabled">
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