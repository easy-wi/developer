<section class="content-header">
    <h1><?php echo $sprache->userImport.' '.$gsprache->mod;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $sprache->userImport;?></a></li>
        <li><?php echo $gsprache->mod;?></li>
        <li class="active"><?php echo $domain;?></li>
    </ol>
</section>


<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <form role="form" action="admin.php?w=ui&amp;d=md&amp;id=<?php echo $id;?>&amp;r=ui" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputLastExternalID"><?php echo $sprache->lastExternalID;?></label>
                            <input class="form-control" id="inputLastExternalID" type="text" name="lastExternalID" value="<?php echo $lastID;?>" disabled="disabled">
                        </div>

                        <div class="form-group">
                            <label for="inputLastCheck"><?php echo $sprache->lastCheck;?></label>
                            <input class="form-control" id="inputLastCheck" type="text" name="lastCheck" value="<?php echo $lastCheck;?>" disabled="disabled">
                        </div>

                        <div class="form-group">
                            <label for="inputActive"><?php echo $gsprache->active;?></label>
                            <select class="form-control" id="inputActive" name="active">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($active=="Y") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
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

                        <div class="form-group">
                            <label for="inputToken">Token</label>
                            <input class="form-control" id="inputToken" type="text" name="accessToken" value="<?php echo $token;?>" required>
                        </div>

                        <div class="form-group">
                            <label for="inputFetchUpdates"><?php echo $sprache->fetchUpdates;?></label>
                            <select class="form-control" id="inputFetchUpdates" name="fetchUpdates">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($fetchUpdates=="Y") echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputChunkSize">chunkSize</label>
                            <input class="form-control" id="inputChunkSize" type="number" name="chunkSize" value="<?php echo $chunkSize;?>" maxlength="19" required>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="inputGroupID"><?php echo $sprache->groupID;?></label>
                            <select class="form-control" id="inputGroupID" name="groupID">
                                <?php foreach ($groupIDS as $k=>$v){ ?><option value="<?php echo $k;?>" <?php if ($groupID==$k) echo 'selected="selected"'; ?>><?php echo $v;?></option><?php } ?>
                            </select>
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