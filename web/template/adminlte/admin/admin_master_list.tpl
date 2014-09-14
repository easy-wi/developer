<section class="content-header">
    <h1><?php echo $gsprache->appRoot;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->master;?></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body table-responsive">
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $sprache->haupt_ip;?></a></th>
                            <th>ID</a></th>
                            <th><?php echo $sprache->desc;?></a></th>
                            <th><?php echo $gsprache->master;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th><?php echo $sprache->haupt_ip;?></a></th>
                            <th>ID</a></th>
                            <th><?php echo $sprache->desc;?></a></th>
                            <th><?php echo $gsprache->master;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>