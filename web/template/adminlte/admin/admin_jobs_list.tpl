<section class="content-header">
    <h1><?php echo $gsprache->jobs.' '.$gsprache->overview;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->jobs.' '.$gsprache->overview;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <form method="post" action="admin.php?w=jb&amp;r=jb" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body table-responsive">
                        <div class="">
                            <table id="dataTable" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th><?php echo $sprache->date;?></a></th>
                                    <th><?php echo $sprache->action;?></a></th>
                                    <th><?php echo $sprache->status;?></a></th>
                                    <th><?php echo $sprache->name;?></a></th>
                                    <th><?php echo $sprache->type;?></a></th>
                                    <th><?php echo $gsprache->del;?></th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th><?php echo $sprache->date;?></a></th>
                                    <th><?php echo $sprache->action;?></a></th>
                                    <th><?php echo $sprache->status;?></a></th>
                                    <th><?php echo $sprache->name;?></a></th>
                                    <th><?php echo $sprache->type;?></a></th>
                                    <th><?php echo $gsprache->del;?></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'id[]')">
                                <?php echo $sprache->all;?>
                            </label>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputDelete" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>