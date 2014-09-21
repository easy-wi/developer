<section class="content-header">
    <h1>E-Mail <?php echo $gsprache->logs;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">E-Mail <?php echo $gsprache->logs;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-11">
            <div class="box box-info">
                <div class="box-body table-responsive">
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $sprache->date;?></th>
                            <th><?php echo $sprache->account;?></th>
                            <th><?php echo $sprache->topic;?></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th><?php echo $sprache->date;?></th>
                            <th><?php echo $sprache->account;?></th>
                            <th><?php echo $sprache->topic;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>