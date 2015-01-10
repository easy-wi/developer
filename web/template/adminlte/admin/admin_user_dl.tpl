<section class="content-header">
    <h1><?php echo $gsprache->user;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=us"><i class="fa fa-user"></i> <?php echo $gsprache->user;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $cname;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=us&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=us" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputNickname"><?php echo $sprache->nickname;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputNickname" type="text" name="nickname" value="<?php echo $cname;?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputName"><?php echo $sprache->user;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputName" type="text" name="nickname" value="<?php echo $fullName;?>" disabled="disabled">
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputDelete" type="submit"><i class="fa fa-trash-o"></i>&nbsp;<?php echo $gsprache->del;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>