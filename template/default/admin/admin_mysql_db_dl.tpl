<section class="content-header">
    <h1>MySQL DB</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=my"><i class="fa fa-database"></i> MySQL</a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $dbName;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=md&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=md" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputDB"><?php echo $sprache->dbname;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputDB" type="text" name="dbname" value="<?php echo $dbName;?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDescription"><?php echo $sprache->description;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputDescription" type="text" name="description" value="<?php echo $description;?>" disabled="disabled">
                            </div>
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