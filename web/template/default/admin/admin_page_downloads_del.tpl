<section class="content-header">
    <h1><?php echo $gsprache->downloads;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=pn"><i class="fa fa-globe"></i> CMS</a></li>
        <li><a href="admin.php?w=pd"><i class="fa fa-download"></i> <?php echo $gsprache->downloads;?></a></li>
        <li><?php echo $gsprache->del;?></li>
        <li class="active"><?php echo $description;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">

                <form role="form" action="admin.php?w=pd&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=pd" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post" >

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="dl">

                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputName"><?php echo $sprache->name;?></label>
                            <div class="controls"><input class="form-control" id="inputName" type="text" name="name" value="<?php echo $description?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputCount"><?php echo $gsprache->downloads;?></label>
                            <div class="controls"><input class="form-control" id="inputCount" type="text" name="downloads" value="<?php echo $count?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputFile"><?php echo $gsprache->file;?></label>
                            <div class="controls"><input class="form-control" id="inputFile" type="text" name="file" value="<?php echo $id.'.'.$fileExtension?>" readonly="readonly"></div>
                        </div>

                        <div class="form-group">
                            <label for="inputDate"><?php echo $sprache->date;?></label>
                            <div class="controls"><input class="form-control" id="inputDate" type="text" name="date" value="<?php echo $date?>" readonly="readonly"></div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-danger" id="inputDelete" type="submit"><i class="fa fa-trash">&nbsp;<?php echo $gsprache->del;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>