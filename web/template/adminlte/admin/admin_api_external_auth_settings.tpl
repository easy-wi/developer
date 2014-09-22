<section class="content-header">
    <h1><?php echo $gsprache->apiAuth.' '.$gsprache->add;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->apiAuth;?></a></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>


<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=aa&amp;r=aa" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputActiveExternal"><?php echo $sprache->activeExternal;?></label>
                            <select class="form-control" id="inputActiveExternal" name="active">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($active=="Y") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputUser"><?php echo $sprache->user;?></label>
                            <input class="form-control" id="inputUser" type="text" name="user" value="<?php echo $user;?>" maxlength="255" required>
                        </div>

                        <div class="form-group">
                            <label for="inputPwd"><?php echo $sprache->pwd;?></label>
                            <input class="form-control" id="inputPwd" type="text" name="pwd" value="<?php echo $pwd;?>" maxlength="100" required>
                        </div>

                        <div class="form-group">
                            <label for="inputSSL"><?php echo $sprache->ssl;?></label>
                            <select class="form-control" id="inputSSL" name="ssl">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($ssl=="Y") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputDomain"><?php echo $sprache->domain;?></label>
                            <input class="form-control" id="inputDomain" type="text" name="domain" value="<?php echo $domain;?>" required>
                        </div>

                        <div class="form-group">
                            <label for="inputFile"><?php echo $sprache->file;?></label>
                            <input class="form-control" id="inputFile" type="text" name="file" value="<?php echo $file;?>" required>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>