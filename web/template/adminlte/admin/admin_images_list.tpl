<section class="content-header">
    <h1><?php echo $gsprache->appRoot;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->template;?></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <?php if($reseller_id==0){ ?>
    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->template;?> <a href="admin.php?w=im&amp;d=ad"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
        </div>
    </div>

    <hr>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body table-responsive">
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $sprache->game;?></th>
                            <th>ID</th>
                            <th><?php echo $sprache->abkuerz;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th><?php echo $sprache->game;?></th>
                            <th>ID</th>
                            <th><?php echo $sprache->abkuerz;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>