<section class="content-header">
    <h1><?php echo $gsprache->user;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=us"><i class="fa fa-user"></i> <?php echo $gsprache->user;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->user;?> <a href="admin.php?w=us&amp;d=ad"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
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
                            <th><?php echo $sprache->nickname;?></th>
                            <th>ID</a></th>
                            <th><?php echo $sprache->user;?></th>
                            <th><?php echo $gsprache->active;?></th>
                            <th><?php echo $sprache->accounttype;?></th>
                            <th><?php echo $gsprache->jobPending;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th><?php echo $sprache->nickname;?></th>
                            <th>ID</a></th>
                            <th><?php echo $sprache->user;?></th>
                            <th><?php echo $gsprache->active;?></th>
                            <th><?php echo $sprache->accounttype;?></th>
                            <th><?php echo $gsprache->jobPending;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>