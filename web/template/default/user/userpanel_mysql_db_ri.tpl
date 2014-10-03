<section class="content-header">
    <h1>MySQL <?php echo $gsprache->databases;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=my"><i class="fa fa-database"></i> MySQL <?php echo $gsprache->databases;?></a></li>
        <li><i class="fa fa-refresh"></i> <?php echo $sprache->reinstall;?></li>
        <li class="active"><?php echo $dbname.' ('.$ip.' )';?></li>
    </ol>
</section>


<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">
                <form role="form" action="userpanel.php?w=my&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=my" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ri">

                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt>IP</dt>
                            <dd><?php echo $ip;?></dd>
                            <dt><?php echo $sprache->user;?></dt>
                            <dd><?php echo $dbname;?></dd>
                            <dt><?php echo $sprache->dbname;?></dt>
                            <dd><?php echo $dbname;?></dd>
                            <?php if(strlen($description)>0){ ?>
                            <dt><?php echo $sprache->description;?></dt>
                            <dd><?php echo $description;?></dd>
                            <?php } ?>
                        </dl>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-warning" id="inputEdit" type="submit"><i class="fa fa-refresh"></i> <?php echo $sprache->reinstall;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>