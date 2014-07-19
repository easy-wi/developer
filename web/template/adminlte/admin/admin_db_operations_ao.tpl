<!-- Content Header -->
<section class="content-header">
    <h1>Easy-WI <?php echo $gsprache->databases.' '.$gsprache->gameserver.' '.$gsprache->addons;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li>Easy-WI <?php echo $gsprache->databases;?></li>
        <li class="active"><?php echo $gsprache->gameserver.' '.$gsprache->addons;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">

<form role="form" action="admin.php?w=bu&amp;d=ra&amp;r=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
    <input type="hidden" name="action" value="ra">

    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Addon</th>
                    <th>Description</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($gameAddons as $image) { ?>
            	<tr>
                    <td><label for="inputGame-<?php echo $image[':addon'];?>"><?php echo $image[':menudescription'];?></label></td>
                    <td><span class="help-inline"><?php echo '('.implode(', ',$image[':supported']).')';?></span></td>
                    <td><input type="checkbox" id="inputGame-<?php echo $image[':addon'];?>" name="addons[]" value="<?php echo $image[':addon'];?>"></td>
                </tr>
            <?php } ?>
            </tbody>
            </table>
            <div class="box-body">
                <div class="form-group">
                    <div class="checkbox">
                        <label for="checkAll"><?php echo $gsprache->all;?></label>
                            <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'addons[]')">
                    </div>
                </div>
                <div class="form-group">
                    <label for="actionType"></label>
                        <select class="form-control" name="actionType" id="actionType">
                            <option value="1"><?php echo $gsprache->add;?></option>
                            <option value="2"><?php echo $gsprache->mod;?></option>
                        </select>
                </div>
                    <label for="inputEdit"></label>
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-edit"></i></button>
            </div>
</form>
        </div>
    </div>
</section>