<section class="content-header">
    <h1><?php echo $gsprache->substitutes;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=su"><?php echo $gsprache->substitutes;?></a></li>
        <li class="active"><?php echo $gsprache->add;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">

            <div class="box box-info">
                <div class="box-body">
                    <form role="form" action="userpanel.php?w=su&amp;d=ad&amp;id=<?php echo $id;?>&amp;r=su" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
                        <input type="hidden" name="token" value="<?php echo token();?>">
                        <input type="hidden" name="action" value="ad">
                        <div class="input-group">
                            <label class="input-group-addon" for="inputCname"><i class="fa fa-check"></i></label>
                            <select class="form-control" id="inputActive" name="active">
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                                <option value="N"><?php echo $gsprache->no;?></option>
                            </select>
                        </div>
                        <br/>
                        <div class="input-group">
                            <label class="input-group-addon" for="inputCname"><i class="fa fa-user"></i></label>
                            <input id="inputCname" type="text" class="form-control" name="loginName" value="" placeholder="<?php echo $gsprache->user;?>" required>
                        </div>
                        <br/>
                        <div class="input-group">
                            <label class="input-group-addon"><i class="fa fa-arrow-right"></i></label>
                            <input id="inputFname" "type="text" class="form-control" name="name" value="" placeholder="<?php echo $sprache->fname;?>">
                        </div>
                        <br/>
                        <div class="input-group">
                            <label class="input-group-addon"><i class="fa fa-arrow-right"></i></label>
                            <input id="inputVname" type="text" class="form-control" name="vname" value="" placeholder="<?php echo $sprache->vname;?>">
                        </div>
                        <br/>
                        <div class="input-group">
                            <label class="input-group-addon" for="inputPassword"><i class="fa fa-key"></i></label>
                            <input id="inputPassword" type="text" class="form-control" name="security" value="<?php echo $randompass;?>" required>
                        </div>


                        <?php if(count($gs)>0){ ?>
                        <hr>
                        <h4><?php echo $gsprache->gameserver;?></h4>
                        <?php foreach($gs as $k=>$v){ ?>
                        <div class="control-group">
                            <label class="control-label" for="inputGS-<?php echo $k;?>"><?php echo $v;?></label>
                            <div class="controls">
                                <input id="inputGS-<?php echo $k;?>" type="checkbox" name="gs[]" value="<?php echo $k;?>">
                            </div>
                        </div>
                        <?php }}?>
                        <?php if(count($fd)>0){ ?>
                        <hr>
                        <h4><?php echo $gsprache->webspace;?></h4>
                        <?php foreach($wv as $k=>$v){ ?>
                        <div class="control-group">
                            <label class="control-label" for="inputWeb-<?php echo $k;?>"><?php echo $v;?></label>
                            <div class="controls">
                                <input id="inputWeb-<?php echo $k;?>" type="checkbox" name="wv[]" value="<?php echo $k;?>">
                            </div>
                        </div>
                        <?php }}?>
                        <?php if(count($vo)>0){ ?>
                        <hr>
                        <h4><?php echo $gsprache->voiceserver;?></h4>
                        <?php foreach($vo as $k=>$v){ ?>
                        <div class="control-group">
                            <label class="control-label" for="inputVO-<?php echo $k;?>"><?php echo $v;?></label>
                            <div class="controls">
                                <input id="inputVO-<?php echo $k;?>" type="checkbox" name="vo[]" value="<?php echo $k;?>">
                            </div>
                        </div>
                        <?php }}?>
                        <?php if(count($vd)>0){ ?>
                        <hr>
                        <h4>TS3 DNS</h4>
                        <?php foreach($vd as $k=>$v){ ?>
                        <div class="control-group">
                            <label class="control-label" for="inputVD-<?php echo $k;?>"><?php echo $v;?></label>
                            <div class="controls">
                                <input id="inputVD-<?php echo $k;?>" type="checkbox" name="vd[]" value="<?php echo $k;?>">
                            </div>
                        </div>
                        <?php }}?>
                        <?php if(count($vs)>0){ ?>
                        <hr>
                        <h4><?php echo $gsprache->virtual;?></h4>
                        <?php foreach($vs as $k=>$v){ ?>
                        <div class="control-group">
                            <label class="control-label" for="inputVS-<?php echo $k;?>"><?php echo $v;?></label>
                            <div class="controls">
                                <input id="inputVS-<?php echo $k;?>" type="checkbox" name="vs[]" value="<?php echo $k;?>">
                            </div>
                        </div>
                        <?php }}?>
                        <?php if(count($ro)>0){ ?>
                        <hr>
                        <h4><?php echo $gsprache->dedicated;?></h4>
                        <?php foreach($ro as $k=>$v){ ?>
                        <div class="control-group">
                            <label class="control-label" for="inputRO-<?php echo $k;?>"><?php echo $v;?></label>
                            <div class="controls">
                                <input id="inputRO-<?php echo $k;?>" type="checkbox" name="ro[]" value="<?php echo $k;?>">
                            </div>
                        </div>
                        <?php }}?>
                        <?php if(count($db)>0){ ?>
                        <hr>
                        <h4>MySQL</h4>
                        <?php foreach($db as $k=>$v){ ?>
                        <div class="control-group">
                            <label class="control-label" for="inputDB-<?php echo $k;?>"><?php echo $v;?></label>
                            <div class="controls">
                                <input id="inputDB-<?php echo $k;?>" type="checkbox" name="db[]" value="<?php echo $k;?>">
                            </div>
                        </div>
                        <?php }}?>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <label class="control-label" for="inputEdit"></label>
            <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
            </form>
        </div>
    </div>
</section><!-- /.content -->