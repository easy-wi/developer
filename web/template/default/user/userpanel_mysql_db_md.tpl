<section class="content-header">
    <h1>MySQL <?php echo $gsprache->databases;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=my"><i class="fa fa-database"></i> MySQL <?php echo $gsprache->databases;?></a></li>
        <li><i class="fa fa-cog"></i> <?php echo $gsprache->settings;?></li>
		<li class="active"><?php echo $dbname.' ('.$ip.' )';?></li>
    </ol>
</section>


<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <form role="form" action="userpanel.php?w=my&amp;id=<?php echo $id;?>&amp;r=my" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input class="input-group-addon" type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputDescription"><?php echo $sprache->description;?></label>
                            <input class="form-control" id=inputDescription type="text" name="description" value="<?php echo $description;?>">
                        </div>

                        <div class="form-group">
                            <label for="password"><?php echo $sprache->password;?></label>
                            <input class="form-control" id="password" type="text" name="password" value="<?php echo $password;?>">
                        </div>

                        <?php if($manage_host_table == 'Y'){ ?>
                        <div class="form-group">
                            <label for="ips"><?php echo $sprache->ips;?></label>
                            <textarea class="form-control" id="ips" name="ips" rows="5"><?php echo $ips?></textarea>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>