<section class="content-header">
    <h1><?php echo $gsprache->feeds.' '.$gsprache->settings;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->feeds;?></a></li>
        <li><?php echo $gsprache->settings;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">

                <form role="form" action="admin.php?w=fe&amp;d=se" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <select class="form-control" id="inputActive" name="active">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($active=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputSteam">Steam Feeds</label>
                            <select class="form-control" id="inputSteam" name="steamFeeds">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($steamFeeds=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputMerge"><?php echo $sprache->merge;?></label>
                            <select class="form-control" id="inputMerge" name="merge">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($merge=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputContent"><?php echo $sprache->displayContent;?></label>
                            <select class="form-control" id="inputContent" name="displayContent">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($displayContent=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputOrderBy"><?php echo $sprache->orderBy;?></label>
                            <select class="form-control" id="inputOrderBy" name="orderBy">
                                <option value="I">URL</option>
                                <option value="D" <?php if ($displayContent=='D') echo 'selected="selected"'; ?>>ID</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputDisplay"><?php echo $sprache->limitDisplay;?></label>
                            <select class="form-control" id="inputDisplay" name="limitDisplay">
                                <option value="N"><?php echo $gsprache->no;?></option>
                                <option value="Y" <?php if ($limitDisplay=='Y') echo 'selected="selected"'; ?>><?php echo $gsprache->yes;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputMaxKeep"><?php echo $sprache->maxKeep;?></label>
                            <input class="form-control" id="inputMaxKeep" type="number" name="maxKeep" maxlength="6" value="<?php echo $maxKeep;?>">
                        </div>

                        <div class="form-group">
                            <label for="inputMaxChars"><?php echo $sprache->maxChars;?></label>
                            <input class="form-control" id="inputMaxChars" type="number" name="maxChars" maxlength="6" value="<?php echo $maxChars;?>">
                        </div>

                        <div class="form-group">
                            <label for="inputNewsAmount"><?php echo $sprache->newsAmount;?></label>
                            <input class="form-control" id="inputNewsAmount" type="number" name="newsAmount" maxlength="6" value="<?php echo $newsAmount;?>">
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