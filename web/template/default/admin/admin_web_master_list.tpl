<section class="content-header">
    <h1><?php echo $gsprache->webspace.' '.$gsprache->master;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=wv"><i class="fa fa-cubes"></i> <?php echo $gsprache->webspace;?></a></li>
        <li><a href="admin.php?w=wm"><i class="fa fa-server"></i> <?php echo $gsprache->webspace.' '.$gsprache->master;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->webspace.' '.$gsprache->master;?> <a href="admin.php?w=wm&amp;d=ad"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
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
                            <th><?php echo $dedicatedLanguage->ip;?></th>
                            <th>ID</th>
                            <th><?php echo $gsprache->status;?></th>
                            <th><?php echo $dedicatedLanguage->description;?></th>
                            <th><?php echo $sprache->installedVhost;?></th>
                            <th><?php echo $sprache->installedHDD;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><?php echo $dedicatedLanguage->ip;?></th>
                            <th>ID</th>
                            <th><?php echo $gsprache->status;?></th>
                            <th><?php echo $dedicatedLanguage->description;?></th>
                            <th><?php echo $sprache->installedVhost;?></th>
                            <th><?php echo $sprache->installedHDD;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>