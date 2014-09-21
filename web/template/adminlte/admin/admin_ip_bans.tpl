<section class="content-header">
    <h1>IP Bans</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">IP Bans</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" method="post" action="admin.php?w=ib&amp;r=ib" onsubmit="return confirm('<?php echo $gsprache->sure;?>');">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body table-responsive">
                        <table id="dataTable" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><?php echo $sprache->ip;?></a></th>
                                <th>Bann ID</a></th>
                                <th><?php echo $sprache->banned_till;?></a></th>
                                <th><?php echo $sprache->failcount;?></a></th>
                                <th><?php echo $sprache->reason;?></a></th>
                                <th><?php echo $gsprache->del;?></th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th><?php echo $sprache->ip;?></a></th>
                                <th>Bann ID</a></th>
                                <th><?php echo $sprache->banned_till;?></a></th>
                                <th><?php echo $sprache->failcount;?></a></th>
                                <th><?php echo $sprache->reason;?></a></th>
                                <th><?php echo $gsprache->del;?></th>
                            </tr>
                            </tfoot>
                        </table>

                        <div class="checkbox">
                            <label>
                                <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'id[]')">
                                <?php echo $sprache->all;?>
                            </label>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash-o">&nbsp;<?php echo $gsprache->del;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>