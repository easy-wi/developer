<section class="content-header">
    <h1>MySQL Server</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=md"><i class="fa fa-database"></i> MySQL</a></li>
        <li><a href="admin.php?w=my"><i class="fa fa-server"></i> MySQL Server</a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $ip;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=my&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=my" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label" for="inputIp">IP</label>
                            <div class="controls"><input class="form-control" id="inputIp" type="text" name="ip" value="<?php echo $ip?>" disabled="disabled"></div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputDesc"><?php echo $sprache->interface;?></label>
                            <div class="controls"><input class="form-control" id="inputDesc" type="text" name="desc" value="<?php echo $interface?>" disabled="disabled"></div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputDelete" type="submit"><i class="fa fa-trash">&nbsp;<?php echo $gsprache->del;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>