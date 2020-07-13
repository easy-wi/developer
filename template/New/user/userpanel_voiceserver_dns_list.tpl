<section class="content-header">
    <h1>TS3 DNS</h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><i class="fa fa-link"></i> TS3 DNS</li>
        <li class="active"><?php echo $gsprache->overview;?></li>
    </ol>
</section>

<section class="content">
    <?php foreach ($table as $row) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-body">

                    <dl class="dl-horizontal">
                        <dt>ID</dt>
                        <dd><?php echo $row['id'];?></dd>
                        <dt>TS3 DNS</dt>
                        <dd><?php echo $row['dns'];?></dd>
                        <dt><?php echo $sprache->ip; ?></dt>
                        <dd><?php echo $row['address'];?></dd>
                    </dl>

                    <form role="form" action="userpanel.php" method="get">

                        <input type="hidden" name="w" value="vd">
                        <input type="hidden" name="d" value="md">
                        <input type="hidden" name="id" value="<?php echo $row['id'];?>">
                        <input type="hidden" name="r" value="vd">

                        <div class="form-group">
                            <label for="inputEdit"></label>
                            <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-edit"></i> <?php echo $gsprache->settings.' '.$gsprache->mod;?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</section>