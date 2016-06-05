<section class="content-header">
    <h1><?php echo $gsprache->substitutes;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=su"><i class="fa fa-users"></i> <?php echo $gsprache->substitutes;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $loginName;?></li>
    </ol>
</section>


<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <form role="form" action="userpanel.php?w=su&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=su" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputCname"><i class="fa fa-user"></i> <?php echo $sprache->user;?></label>
                            <input id="inputCname" type="text" class="form-control" name="loginName" value="<?php echo $loginName;?>" disabled="disabled">
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash-o"></i> <?php echo $gsprache->del;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section><!-- /.content -->