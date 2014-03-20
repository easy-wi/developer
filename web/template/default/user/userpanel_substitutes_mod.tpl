<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=su"><?php echo $gsprache->substitutes;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->mod;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $loginName;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->user;?></dt>
            <dd><?php echo $loginName;?></dd>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=su&amp;d=md&amp;id=<?php echo $id;?>&amp;r=su" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <div class="control-group">
                <label class="control-label" for="inputActive"><?php echo $sprache->active;?></label>
                <div class="controls">
                    <select id="inputActive" name="active">
                        <option value="Y"><?php echo $gsprache->yes;?></option>
                        <option value="N" <?php if($active=='N') echo 'selected="selected"';?>><?php echo $gsprache->no;?></option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputFname"><?php echo $sprache->fname;?></label>
                <div class="controls">
                    <input id="inputFname" type="text" name="name" value="<?php echo $name;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputVname"><?php echo $sprache->vname;?></label>
                <div class="controls">
                    <input id="inputVname" type="text" name="vname" value="<?php echo $vname;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPassword"><?php echo $sprache->wipsw;?>*</label>
                <div class="controls">
                    <input id="inputPassword" type="text" name="security" value="(encrypted)" required>
                </div>
            </div>
            <?php if(count($gs)>0){ ?>
            <hr>
            <h4><?php echo $gsprache->gameserver;?></h4>
            <?php foreach($gs as $k=>$v){ ?>
            <div class="control-group">
                <label class="control-label" for="inputGS-<?php echo $k;?>"><?php echo $v;?></label>
                <div class="controls">
                    <input id="inputGS-<?php echo $k;?>" type="checkbox" name="gs[]" value="<?php echo $k;?>" <?php if(isset($as['gs'][$k])) echo 'checked';?>>
                </div>
            </div>
            <?php }}?>
            <?php if(count($wv)>0){ ?>
            <hr>
            <h4><?php echo $gsprache->webspace;?></h4>
            <?php foreach($wv as $k=>$v){ ?>
            <div class="control-group">
                <label class="control-label" for="inputWeb-<?php echo $k;?>"><?php echo $v;?></label>
                <div class="controls">
                    <input id="inputWeb-<?php echo $k;?>" type="checkbox" name="wv[]" value="<?php echo $k;?>" <?php if(isset($as['wv'][$k])) echo 'checked';?>>
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
                    <input id="inputVO-<?php echo $k;?>" type="checkbox" name="vo[]" value="<?php echo $k;?>" <?php if(isset($as['vo'][$k])) echo 'checked';?>>
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
                    <input id="inputVD-<?php echo $k;?>" type="checkbox" name="vd[]" value="<?php echo $k;?>" <?php if(isset($as['vd'][$k])) echo 'checked';?>>
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
                    <input id="inputVS-<?php echo $k;?>" type="checkbox" name="vs[]" value="<?php echo $k;?>" <?php if(isset($as['vs'][$k])) echo 'checked';?>>
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
                    <input id="inputRO-<?php echo $k;?>" type="checkbox" name="ro[]" value="<?php echo $k;?>" <?php if(isset($as['ro'][$k])) echo 'checked';?>>
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
                    <input id="inputDB-<?php echo $k;?>" type="checkbox" name="db[]" value="<?php echo $k;?>"  <?php if(isset($as['db'][$k])) echo 'checked';?>>
                </div>
            </div>
            <?php }}?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>