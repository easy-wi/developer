<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li class="active"><?php echo $gsprache->search;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-horizontal" method="get" action="admin.php">
            <input type="hidden" name="w" value="sr">
            <div class="row-fluid">
                <label class="control-label input-append" for="inputSearch">
                    <input class="input-block-level" id="inputSearch" type="text" name="q" value="<?php echo $q;?>">
                    <button class="btn btn-primary" type="submit"><i class="icon-search icon-white"></i></button>
                </label>
            </div>
            <div class="row-fluid">
                <label class="checkbox inline">
                    <?php if($pa['gserver']){ ?><input id="inlineCheckboxGS" type="checkbox" name="type[]" value="gs" <?php if($gs==true) echo 'checked="checked"';?>> <?php echo $gsprache->gameserver;?><?php }?>
                </label>
                <label class="checkbox inline">
                    <?php if($pa['gimages']){ ?><input id="inlineCheckboxIM" type="checkbox" name="type[]" value="im" <?php if($im==true) echo 'checked="checked"';?>> <?php echo $gsprache->gameserver.' '.$gsprache->templates;?><?php }?>
                </label>
                <label class="checkbox inline">
                    <?php if($pa['addons']){ ?><input id="inlineCheckboxAD" type="checkbox" name="type[]" value="ad" <?php if($ad==true) echo 'checked="checked"';?>> <?php echo $gsprache->addon;?><?php }?>
                </label>
                <label class="checkbox inline">
                    <?php if($pa['voiceserver']){ ?><input id="inlineCheckboxVO" type="checkbox" name="type[]" value="vo" <?php if($vo==true) echo 'checked="checked"';?>> <?php echo $gsprache->voiceserver;?><?php }?>
                </label>
                <label class="checkbox inline">
                    <?php if($pa['addvserver'] or $pa['modvserver'] or $pa['delvserver'] or $pa['usevserver']){ ?><input id="inlineCheckboxVS" type="checkbox" name="type[]" value="vs" <?php if($vs==true) echo 'checked="checked"';?>> <?php echo $gsprache->virtual;?><?php }?>
                </label>
                <label class="checkbox inline">
                    <?php if($pa['roots']){ ?><input id="inlineCheckboxRO" type="checkbox" name="type[]" value="ro" <?php if($ro==true) echo 'checked="checked"';?>> <?php echo $gsprache->root;?><?php }?>
                </label>
                <label class="checkbox inline">
                    <?php if($pa['user'] or $pa['user_users']){ ?><input id="inlineCheckboxUS" type="checkbox" name="type[]" value="us" <?php if($us==true) echo 'checked="checked"';?>> <?php echo $gsprache->user;?><?php }?>
                </label>
            </div>
        </form>
    </div>
</div>
<hr>
<?php if(isset($results)){ ?>
<div class="row-fluid">
    <div class="span11">
        <table class="table table-bordered table-hover table-striped footable">
            <thead>
            <tr>
                <th data-class="expand"><?php echo $sprache->name;?></th>
                <th data-hide="phone,tablet">ID</th>
                <th data-hide="phone,tablet"><?php echo $sprache->type;?></th>
                <th data-hide="phone,tablet"><?php echo $gsprache->user;?></th>
                <th><?php echo $gsprache->del;?></th>
                <th><?php echo $gsprache->mod;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($results as $r) { ?>
            <tr>
                <td><?php echo $r['name'];?></td>
                <td><?php echo $r['id'];?></td>
                <td><?php echo $r['type'];?></td>
                <td><?php echo ($r['owner']!='')?$r['owner']:$r['name'];?></td>
                <td><a href="admin.php<?php echo $r['delete'];?>"><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                <td><a href="admin.php<?php echo $r['edit'];?>"><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>