<section class="content-header">
    <h1>MySQL Server</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=md"><i class="fa fa-database"></i> MySQL</a></li>
        <li><a href="admin.php?w=my"><i class="fa fa-server"></i> MySQL Server</a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            MySQL Server <a href="admin.php?w=my&amp;d=ad"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
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
                            <th>IP</th>
                            <th>ID</th>
                            <th><?php echo $sprache->description;?></th>
                            <th><?php echo $gsprache->status;?></th>
                            <th><?php echo $sprache->interface;?></th>
                            <th><?php echo $sprache->usage;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>IP</th>
                            <th>ID</th>
                            <th><?php echo $sprache->description;?></th>
                            <th><?php echo $gsprache->status;?></th>
                            <th><?php echo $sprache->interface;?></th>
                            <th><?php echo $sprache->usage;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>