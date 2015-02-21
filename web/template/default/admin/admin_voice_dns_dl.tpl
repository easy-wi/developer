<section class="content-header">
    <h1>TSDNS</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><a href="admin.php?w=vr"><i class="fa fa-link"></i> TSDNS</a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $dns;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=vr&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=vr" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputTSDNS">TSDNS</label>
                            <div class="controls">
                                <input class="form-control" id="inputTSDNS" type="text" name="dns" value="<?php echo $dns;?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputIP"><?php echo $sprache->ip;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputIP" type="text" name="ip" value="<?php echo $ip;?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPort"><?php echo $sprache->port;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputPort" type="text" name="port" value="<?php echo $port;?>" disabled="disabled">
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