<section class="content-header">
    <h1><?php echo $gsprache->feeds.' '.$gsprache->news;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><i class="fa fa-rss"></i> <?php echo $gsprache->feeds.' '.$gsprache->news;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->feeds.' '.$gsprache->news;?> <a href="admin.php?w=fn&amp;d=ud&amp;r=fn"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <form action="admin.php?w=fn&amp;d=md&amp;r=fn" method="post" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">

                    <input type="hidden" name="action" value="md">

                    <div class="box-body table-responsive">
                        <div class="box-body table-responsive">
                            <table id="dataTable" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th><?php echo $sprache->title;?></th>
                                    <th>ID</th>
                                    <th><?php echo $sprache->pubDate;?></th>
                                    <th><?php echo $sprache->status;?></th>
                                    <th><?php echo $gsprache->del;?></th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th><?php echo $sprache->title;?></th>
                                    <th>ID</th>
                                    <th><?php echo $sprache->pubDate;?></th>
                                    <th><?php echo $sprache->status;?></th>
                                    <th><?php echo $gsprache->del;?></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-warning" id="inputEdit" type="submit"><i class="fa fa-play-circle">&nbsp;<?php echo $gsprache->exec;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>