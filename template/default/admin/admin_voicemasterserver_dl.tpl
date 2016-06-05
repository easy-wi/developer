<section class="content-header">
    <h1><?php echo $gsprache->voiceserver.' '.$gsprache->master;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><a href="admin.php?w=vm"><i class="fa fa-server"></i> <?php echo $gsprache->voiceserver.' '.$gsprache->master;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $ip;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=vm&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=vm" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputName"><?php echo $sprache->ssh_ip;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputName" type="text" name="name" value="<?php echo $ip;?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputDescription"><?php echo $sprache->description;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputDescription" type="text" name="type" value="<?php echo $description;?>" disabled="disabled">
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