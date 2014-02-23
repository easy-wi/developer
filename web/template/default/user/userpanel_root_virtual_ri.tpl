<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vm"><?php echo $gsprache->virtual;?></a> <span class="divider">/</span></li>
            <li><?php echo $sprache->rescue.' / '.$sprache->reinstall;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip;?></li>
        </ul>
    </div>
</div>
<?php if(isset($error)){ ?>
<div class="row-fluid">
    <div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-warning-sign"></i> <?php echo $error?></div>
</div>
<?php }else{ ?>
<div class="row-fluid">
    <div class="span8">
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->status?>:</dt>
            <dd><?php echo $status;?></dd>
            <dt><?php echo $sprache->description?>:</dt>
            <dd><?php echo $description." ".$bitversion;?> Bit</dd>
            <dt><?php echo $sprache->initialRescuPass;?>:</dt>
            <dd><?php echo $pass;?></dd>
            <?php foreach(customColumns('S',$id) as $row){ ?>
            <dt><?php echo $row['menu'];?>:</dt>
            <dd><?php echo $row['value'];?></dd>
            <?php }?>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="userpanel.php?w=vm&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=de" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label" for="inputAction"><?php echo $sprache->action?></label>
                <div class="controls">
                    <select id="inputAction" name="action" onchange="SwitchShowHideRows(this.value);">
                        <?php echo implode('',$option);?>
                    </select>
                </div>
            </div>
            <?php if($showImages){ ?>
            <div class="ri display_none switch control-group warning">
                <?php echo $sprache->attention;?>
            </div>
            <div class="ri display_none switch control-group">
                <label class="control-label" for="inputImage"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <select id="inputImage" name="imageid">
                        <?php foreach ($templates as $template){ ?>
                        <option value="<?php echo $template['id']?>"><?php echo $template['description'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-refresh"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php }?>