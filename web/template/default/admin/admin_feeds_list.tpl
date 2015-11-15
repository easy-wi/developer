<section class="content-header">
    <h1><?php echo $gsprache->feeds;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=fn"><i class="fa fa-rss"></i> <?php echo $gsprache->feeds;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->feeds;?> <a href="admin.php?w=fe&amp;d=ad"<span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body table-responsive">
                    <div class="box-body table-responsive">
                        <table id="dataTable" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>URL</th>
                                <th>ID</th>
                                <th><?php echo $gsprache->action;?></th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>URL</th>
                                <th>ID</th>
                                <th><?php echo $gsprache->action;?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>