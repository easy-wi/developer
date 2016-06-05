<section class="content-header">
    <h1><?php echo $gsprache->gameserver.' '.$gsprache->template;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li><a href="admin.php?w=im"><i class="fa fa-file-text-o"></i> <?php echo $gsprache->gameserver.' '.$gsprache->template;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->template;?> <a href="admin.php?w=im&amp;d=ad"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
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