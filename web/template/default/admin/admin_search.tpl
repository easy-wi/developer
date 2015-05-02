<section class="content-header">
    <h1><?php echo $gsprache->search;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><a href="admin.php?w=sr"><i class="fa fa-search"></i> <?php echo $gsprache->search;?></a></li>
    </ol>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">

            <form class="form-horizontal" method="get" action="admin.php" id="searchForm">

                <input type="hidden" name="w" value="sr">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="input-group">
                            <input type="text" class="form-control" name="q" value="<?php echo $q;?>">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-flat" type="button" onclick="submitForm();"><i class="fa fa-search"></i></button>
                            </span>
                        </div>

                        <div class="input-group">

                            <?php if($pa['gserver']){ ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="type[]" value="gs" onchange="submitForm();" <?php if($gs==true) echo 'checked="checked"';?>> <?php echo $gsprache->gameserver;?>
                            </label>
                            <?php }?>

                            <?php if($pa['gimages']){ ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="type[]" value="im" onchange="submitForm();" <?php if($im==true) echo 'checked="checked"';?>> <?php echo $gsprache->gameserver.' '.$gsprache->templates;?>
                            </label>
                            <?php }?>

                            <?php if($pa['addons']){ ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="type[]" value="ad" onchange="submitForm();" <?php if($ad==true) echo 'checked="checked"';?>> <?php echo $gsprache->addon;?>
                            </label>
                            <?php }?>

                            <?php if($pa['voiceserver']){ ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="type[]" value="vo" onchange="submitForm();" <?php if($vo==true) echo 'checked="checked"';?>> <?php echo $gsprache->voiceserver;?>
                            </label>
                            <?php }?>

                            <?php if($pa['addvserver'] or $pa['modvserver'] or $pa['delvserver'] or $pa['usevserver']){ ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="type[]" value="vs" onchange="submitForm();" <?php if($vs==true) echo 'checked="checked"';?>> <?php echo $gsprache->virtual;?>
                            </label>
                            <?php }?>

                            <?php if($pa['roots']){ ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="type[]" value="ro" onchange="submitForm();" <?php if($ro==true) echo 'checked="checked"';?>> <?php echo $gsprache->root;?>
                            </label>
                            <?php }?>

                            <?php if($pa['user'] or $pa['user_users']){ ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="type[]" value="us" onchange="submitForm();" <?php if($us==true) echo 'checked="checked"';?>> <?php echo $gsprache->user;?>
                            </label>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </form>

            <div class="box box-primary">
                <div class="box-body table-responsive">
                    <div class="box-body table-responsive">
                        <table id="dataTable" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><?php echo $sprache->name;?></th>
                                <th>ID</th>
                                <th><?php echo $sprache->type;?></th>
                                <th><?php echo $gsprache->user;?></th>
                                <th><?php echo $gsprache->action;?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($results as $r) { ?>
                            <tr>
                                <td><?php echo $r['name'];?></td>
                                <td><?php echo $r['id'];?></td>
                                <td><?php echo $r['type'];?></td>
                                <td><?php echo ($r['owner']!='')?$r['owner']:$r['name'];?></td>
                                <td>
                                    <a href="admin.php<?php echo $r['delete'];?>"><span class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> <?php echo $gsprache->del;?></span></a>
                                    <a href="admin.php<?php echo $r['edit'];?>"><span class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i> <?php echo $gsprache->mod;?></span></a>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><?php echo $sprache->name;?></th>
                                <th>ID</th>
                                <th><?php echo $sprache->type;?></th>
                                <th><?php echo $gsprache->user;?></th>
                                <th><?php echo $gsprache->action;?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
function submitForm() {
    $('#searchForm').submit();
}
</script>