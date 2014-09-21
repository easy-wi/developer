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
            <div class="box box-success">
                <form role="form" action="userpanel.php?w=su&amp;d=ad&amp;id=<?php echo $id;?>&amp;r=su" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputActive"><?php echo $sprache->active;?></label>
                            <select class="form-control" id="inputActive" name="active">
                                <option value="Y"><?php echo $gsprache->yes;?></option>
                                <option value="N"><?php echo $gsprache->no;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputCname"><?php echo $gsprache->user;?>*</label>
                            <input id="inputCname" type="text" class="form-control" name="loginName" value="" placeholder="<?php echo $gsprache->user;?>" required>
                        </div>

                        <div class="form-group">
                            <label for="inputFname"><?php echo $sprache->fname;?></label>
                            <input id="inputFname" type="text" class="form-control" name="name" value="" placeholder="<?php echo $sprache->fname;?>">
                        </div>

                        <div class="form-group">
                            <label for="inputVname"><?php echo $sprache->vname;?></label>
                            <input id="inputVname" type="text" class="form-control" name="vname" value="" placeholder="<?php echo $sprache->vname;?>">
                        </div>

                        <div class="form-group">
                            <label for="inputPassword"><?php echo $sprache->wipsw;?>*</label>
                            <input id="inputPassword" type="text" class="form-control" name="security" value="<?php echo $randompass;?>" required>
                        </div>


                        <?php if(count($gs)>0){ ?>
                        <hr>
                        <h4><?php echo $gsprache->gameserver;?></h4>
                        <?php foreach($gs as $k=>$v){ ?>

                        <div class="checkbox">
                            <label>
                                <input id="inputGS-<?php echo $k;?>" type="checkbox" name="gs[]" value="<?php echo $k;?>">
                                <?php echo $v;?>
                            </label>
                        </div>
                        <?php }}?>

                        <?php if(count($wv)>0){ ?>
                        <hr>
                        <h4><?php echo $gsprache->webspace;?></h4>
                        <?php foreach($wv as $k=>$v){ ?>
                        <div class="checkbox">
                            <label>
                                <input id="inputWeb-<?php echo $k;?>" type="checkbox" name="wv[]" value="<?php echo $k;?>">
                                <?php echo $v;?>
                            </label>
                        </div>
                        <?php }}?>

                        <?php if(count($vo)>0){ ?>
                        <hr>
                        <h4><?php echo $gsprache->voiceserver;?></h4>
                        <?php foreach($vo as $k=>$v){ ?>
                        <div class="checkbox">
                            <label>
                                <input id="inputVO-<?php echo $k;?>" type="checkbox" name="vo[]" value="<?php echo $k;?>">
                                <?php echo $v;?>
                            </label>
                        </div>
                        <?php }}?>

                        <?php if(count($vd)>0){ ?>
                        <hr>
                        <h4>TS3 DNS</h4>
                        <?php foreach($vd as $k=>$v){ ?>
                        <div class="checkbox">
                            <label>
                                <input id="inputVD-<?php echo $k;?>" type="checkbox" name="vd[]" value="<?php echo $k;?>">
                                <?php echo $v;?>
                            </label>
                        </div>
                        <?php }}?>

                        <?php if(count($vs)>0){ ?>
                        <hr>
                        <h4><?php echo $gsprache->virtual;?></h4>
                        <?php foreach($vs as $k=>$v){ ?>
                        <div class="checkbox">
                            <label>
                                <input id="inputVS-<?php echo $k;?>" type="checkbox" name="vs[]" value="<?php echo $k;?>">
                                <?php echo $v;?>
                            </label>
                        </div>
                        <?php }}?>

                        <?php if(count($ro)>0){ ?>
                        <hr>
                        <h4><?php echo $gsprache->dedicated;?></h4>
                        <?php foreach($ro as $k=>$v){ ?>
                        <div class="checkbox">
                            <label>
                                <input id="inputRO-<?php echo $k;?>" type="checkbox" name="ro[]" value="<?php echo $k;?>">
                                <?php echo $v;?>
                            </label>
                        </div>
                        <?php }}?>

                        <?php if(count($db)>0){ ?>
                        <hr>
                        <h4>MySQL</h4>
                        <?php foreach($db as $k=>$v){ ?>
                        <div class="checkbox">
                            <label>
                                <input id="inputDB-<?php echo $k;?>" type="checkbox" name="db[]" value="<?php echo $k;?>">
                                <?php echo $v;?>
                            </label>
                        </div>
                        <?php }}?>
                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section><!-- /.content -->