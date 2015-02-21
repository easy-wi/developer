<section class="content-header">
    <h1><?php echo $gsprache->voiceserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">

    <?php if((!is_numeric($licenceDetails['lVo']) or $licenceDetails['lVo']>0) and ($licenceDetails['left']>0 or !is_numeric($licenceDetails['left']))) { ?>
    <div class="row">
        <div class="col-md-12">
            <?php echo $gsprache->voiceserver;?> <a href="admin.php?w=vo&amp;d=ad"><span class="btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> <?php echo $gsprache->add;?></span></a>
        </div>
    </div>
    <?php } ?>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body table-responsive">
                    <table id="dataTable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th><?php echo $sprache->server;?></th>
                            <th>ID</th>
                            <th><?php echo $gsprache->status;?></th>
                            <th>VirtualID</th>
                            <th><?php echo $sprache->user;?></th>
                            <th><?php echo $sprache->slots;?></th>
                            <th><?php echo $gsprache->jobs;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><?php echo $sprache->server;?></th>
                            <th>ID</th>
                            <th><?php echo $gsprache->status;?></th>
                            <th>VirtualID</th>
                            <th><?php echo $sprache->user;?></th>
                            <th><?php echo $sprache->slots;?></th>
                            <th><?php echo $gsprache->jobs;?></th>
                            <th><?php echo $gsprache->action;?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>