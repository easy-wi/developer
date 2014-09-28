<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=gs"><?php echo $gsprache->gameserver;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $ip.':'.$port;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=gs&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputUser"><?php echo $sprache->user;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputUser" name="user" type="text" value="<?php echo $user;?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputIP"><?php echo $sprache->ip;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputIP" name="ip" type="text" value="<?php echo $ip;?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPort"><?php echo $sprache->port;?></label>
                            <div class="controls">
                                <input class="form-control" id="inputPort" name="port" type="text" value="<?php echo $port;?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputSafe"><?php echo $gsprache->del;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputSafe" name="safeDelete">
                                    <option value="S"><?php echo $gsprache->delSafe;?></option>
                                    <option value="A"><?php echo $gsprache->delAny;?></option>
                                    <option value="D"><?php echo $gsprache->delDB;?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputEdit" type="submit"><i class="fa fa-trash-o">&nbsp;<?php echo $gsprache->del;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>