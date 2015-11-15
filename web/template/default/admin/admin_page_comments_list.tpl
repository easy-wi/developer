<section class="content-header">
    <h1><?php echo $gsprache->comments;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=pn"><i class="fa fa-globe"></i> CMS</a></li>
        <li><a href="admin.php?w=pc"><i class="fa fa-comments"></i> <?php echo $gsprache->comments;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body table-responsive">

                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>URL</th>
                            <th>ID</a></th>
                            <th><?php echo $sprache->author;?></th>
                            <th><?php echo $sprache->date;?></th>
                            <th><?php echo $sprache->moderate;?></th>
                            <th>Spam</th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>URL</th>
                            <th>ID</a></th>
                            <th><?php echo $sprache->author;?></th>
                            <th><?php echo $sprache->date;?></th>
                            <th><?php echo $sprache->moderate;?></th>
                            <th>Spam</th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>