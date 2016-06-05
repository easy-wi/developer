<section class="content-header">
    <h1>TSDNS <?php echo $gsprache->master;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=vo"><i class="fa fa-microphone"></i> <?php echo $gsprache->voiceserver;?></a></li>
        <li><a href="admin.php?w=vd"><i class="fa fa-server"></i> TSDNS <?php echo $gsprache->master;?></a></li>
        <li><?php echo $gsprache->add.'/'.$sprache->import;?></li>
        <li class="active"><?php echo $ssh2ip;?></li>
    </ol>
</section>

<div class="row">
    <div class="col-md-12">
        <div class="box box-success">

            <form role="form" action="admin.php?w=vd&amp;d=ip&amp;id=<?php echo $id;?>&amp;r=vd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                <input type="hidden" name="token" value="<?php echo token();?>">
                <input type="hidden" name="action" value="ip">

                <div class="box-body">

                    <?php if (is_array($dnsarray)) { ?>
                    <?php foreach ($newArray as $k=>$v) { ?>

                    <input type="hidden" name="dns[]" value="<?php echo $v;?>">
                    <input type="hidden" name="<?php echo $v;?>-address" value="<?php echo $k;?>">

                    <h3><?php echo $k.': '.$v;?></h3>

                    <div class="form-group">
                        <label for="inputImport"><?php echo $sprache->import;?></label>
                        <div class="controls">
                            <select class="form-control" id="inputImport" name="<?php echo $v;?>-import">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputCustomer"><?php echo $gsprache->user ;?></label>
                        <div class="controls">
                            <select class="form-control" id="inputCustomer" name="<?php echo $v;?>-customer">
                                <option value="0"><?php echo $sprache->newuser;?></option>
                                <?php foreach ($table as $key=>$value) { ?>
                                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <?php if ($newuser==2) { ?>

                    <div class="form-group">
                        <label for="inputUser"><?php echo $sprache->user;?></label>
                        <div class="controls"><input class="form-control" id="inputUser" type="text" name="<?php echo $v;?>-username"></div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail"><?php echo $usprache->email;?></label>
                        <div class="controls"><input class="form-control" id="inputEmail" type="text" name="<?php echo $v;?>-email" value="ts3@import.mail"></div>
                    </div>

                    <?php }}} else { ?>
                    <?php echo $dnsarray;?>
                    <?php } ?>

                </div>

                <div class="box-footer">
                    <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
</section>