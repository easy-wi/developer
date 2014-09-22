<!-- Content Header -->
<section class="content-header">
    <h1>Easy-WI <?php echo $gsprache->databases;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Easy-WI <?php echo $gsprache->databases;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">Easy-WI <?php echo $gsprache->databases;?></h3>
                </div>

                <div class="box-body">

                    <?php if($reseller_id==0){ ?>
                    <form role="form" action="admin.php?w=bu&amp;d=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
                        <input type="hidden" name="action" value="bu">
                        <div class="form-group">
                            <label for="inputUpdate"><?php echo $gsprache->backup;?></label>
                            <button class="btn btn-success btn-sm" id="inputUpdate" type="submit"><i class="fa fa-download"></i></button>
                        </div>
                    </form>

                    <form role="form" action="admin.php?w=bu&amp;d=rp&amp;r=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
                        <input type="hidden" name="action" value="rp">
                        <div class="form-group">
                            <label  for="inputUpdate"><?php echo $gsprache->database.' '.$gsprache->repair;?></label>
                            <button class="btn btn-warning btn-sm" id="inputUpdate" type="submit"><i class="fa fa-refresh"></i></button>
                    </form>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label for="inputGS"><?php echo $gsprache->gameserver.' '.$gsprache->template;?></label>
                    <a href="admin.php?w=bu&amp;d=rg" id="inputGS"><button class="btn btn-warning btn-sm" id="inputGS" type="submit"><i class="fa fa-refresh"></i></button></a>
                </div>

                <div class="form-group">
                    <label for="inputAO"><?php echo $gsprache->gameserver.' '.$gsprache->addon;?></label>
                    <a href="admin.php?w=bu&amp;d=ra" id="inputGS"><button class="btn btn-warning btn-sm" id="inputAO" type="submit"><i class="fa fa-refresh"></i></button></a>
                </div>
            </div>
        </div>
    </div>
</section>