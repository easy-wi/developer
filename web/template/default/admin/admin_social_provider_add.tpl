<section class="content-header">
    <h1>Social Auth Provider</h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><i class="fa fa-user"></i> <?php echo $gsprache->user;?></li>
        <li><i class="fa fa-cloud"></i> Social Auth Provider</li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>

<section class="content">

    <?php if (count($errors)>0){ ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><?php echo $gsprache->errors;?></h4>
                <?php echo implode(', ',$errors);?>
            </div>
        </div>
    </div>
    <?php }?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">

                <form role="form" action="admin.php?w=up&amp;d=ad&amp;r=up"" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">

                        <div class="control-group<?php if(isset($errors['active'])) echo ' has-error';?>">
                            <label class="control-label" for="inputActive"><?php echo $gsprache->active;?></label>
                            <div class="controls">
                                <select class="form-control" id="inputActive" name="active">
                                    <option value="N"><?php echo $gsprache->no;?></option>
                                    <option value="Y" <?php if ($active=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group<?php if(isset($errors['name'])) echo ' has-error';?>">
                            <label class="control-label" for="inputName">Social Auth Provider</label>
                            <div class="controls">
                                <select class="form-control" id="inputName" name="name">
                                    <?php foreach($serviceProviders as $sp){ ?>
                                    <option<?php if($sp == $name) echo ' selected="selected"';?>><?php echo $sp;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group<?php if(isset($errors['keyID'])) echo ' has-error';?>">
                            <label class="control-label" for="inputKeyID">ID/Key</label>
                            <div class="controls">
                                <input class="form-control" id="inputKeyID" type="text" name="keyID" value="<?php echo $keyID;?>">
                            </div>
                        </div>

                        <div class="control-group<?php if(isset($errors['providerToken'])) echo ' has-error';?>">
                            <label class="control-label" for="inputToken">Token</label>
                            <div class="controls">
                                <input class="form-control" id="inputToken" type="text" name="providerToken" value="<?php echo $providerToken;?>">
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>