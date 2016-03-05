<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->gameserver;?> <a href="admin.php?w=gs&amp;d=ad"><span class="btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body table-responsive">
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $sprache->server;?></a></th>
                            <th>ID</a></th>
                            <th><?php echo $gsprache->status;?></a></th>
                            <th><?php echo $sprache->user;?></a></th>
                            <th><?php echo $gsprache->jobPending;?></th>
                            <th><?php echo $gsprache->start.'/'.$gsprache->stop;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th><?php echo $sprache->server;?></a></th>
                            <th>ID</a></th>
                            <th><?php echo $gsprache->status;?></a></th>
                            <th><?php echo $sprache->user;?></a></th>
                            <th><?php echo $gsprache->jobPending;?></th>
                            <th><?php echo $gsprache->start.'/'.$gsprache->stop;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>