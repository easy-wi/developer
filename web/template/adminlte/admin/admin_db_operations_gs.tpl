<!-- Content Header -->
<section class="content-header">
    <h1>Easy-WI <?php echo $gsprache->databases.' '.$gsprache->gameserver.' '.$gsprache->template;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li>Easy-WI <?php echo $gsprache->databases;?></li>
        <li class="active"><?php echo $gsprache->gameserver.' '.$gsprache->template;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">

<form role="form" action="admin.php?w=bu&amp;d=rg&amp;r=bu" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
    <input type="hidden" name="action" value="rg">

    <div class="box box-info">
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th><?php echo $gsprache->gameserver.' '.$gsprache->template;?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($gameImages as $image) { ?>
            	<tr>
                	<td><label for="inputGame-<?php echo $image[':shorten'];?>"><img src="images/games/icons/<?php echo $image[':shorten'];?>.png" alt="<?php echo $image[':shorten'];?>" width="16"> <?php echo $image[':description'];?></label></td>
                    <td><input type="checkbox" id="inputGame-<?php echo $image[':shorten'];?>" name="games[]" value="<?php echo $image[':shorten'];?>"></td>
                </tr>
            <?php } ?>
            </tbody>
            </table>
			<div class="box-body">
                <div class="form-group">
                    <div class="checkbox">
                        <label for="checkAll"><?php echo $gsprache->all;?></label>
                            <input id="checkAll" type="checkbox"  value="yes" onclick="checkall(this.checked,'games[]')">
                    </div>
                </div>
                <div class="form-group" id="typeGroup">
                    <label for="actionType"></label>
                        <select class="form-control" name="actionType" id="actionType">
                            <option value="1"><?php echo $gsprache->add;?></option>
                            <option value="2"><?php echo $gsprache->mod;?></option>
                        </select>
                </div>
            
                <label for="inputEdit"></label>
                    <button class="btn btn-primary btn-sm" id="inputEdit" type="submit"><i class="fa fa-edit"></i></button>
			</div>
</form>
		</div>
	</div>
</section>