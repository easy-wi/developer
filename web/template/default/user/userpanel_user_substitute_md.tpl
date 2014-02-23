<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->user;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->settings;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <form class="form-horizontal" action="userpanel.php?w=se&amp;r=se" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="md">
            <?php foreach($serviceProviders as $sp){ ?>
            <div class="control-group">
                <label class="control-label" for="sp<?php echo $sp['sp'];?>"><?php echo $sp['sp'];?></label>
                <div class="controls">
                    <?php if (strlen($sp['spUserId'])==0){ ?>
                    <a class="btn btn-block btn-social btn-<?php echo strtolower($sp['sp']);?> span10" href="login.php?serviceProvider=<?php echo $sp['sp'];?>" id="sp<?php echo $sp['sp'];?>">
                        <i class="fa fa-<?php echo strtolower($sp['sp']);?>"></i> <?php echo $sprache->socialConnect.' '.$sp['sp'];?>
                    </a>
                    <?php } else { ?>
                    <a class="btn btn-block btn-social btn-<?php echo strtolower($sp['sp']);?> span10" href="userpanel.php?w=se&amp;spUser=<?php echo $sp['spUserId'];?>&amp;spId=<?php echo $sp['spId'];?>&amp;r=se" id="sp<?php echo $sp['sp'];?>">
                        <i class="fa fa-<?php echo strtolower($sp['sp']);?>"></i> <?php echo $sprache->socialRemove.' '.$sp['sp'];?>
                    </a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <?php if(count($serviceProviders) > 0 ) echo '<hr>';?>
            <div class="control-group">
                <label class="control-label" for="fname"><?php echo $sprache->fname;?></label>
                <div class="controls">
                    <input id="fname" type="text" name="name" value="<?php echo $name;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="vname"><?php echo $sprache->vname;?></label>
                <div class="controls">
                    <input id="vname" type="text" name="vname" value="<?php echo $vname;?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>