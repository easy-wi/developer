<section class="content-header">
    <h1><?php echo $gsprache->user;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->user;?></li>
        <li class="active"><?php echo $gsprache->settings;?></li>
    </ol>
</section>


<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <form role="form" action="userpanel.php?w=se&amp;r=se" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="md">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="fname"><?php echo $sprache->fname;?></label>
                            <input class="form-control" id="fname" type="text" name="name" value="<?php echo $name;?>">
                        </div>

                        <div class="form-group">
                            <label for="vname"><?php echo $sprache->vname;?></label>
                            <input class="form-control" id="vname" type="text" name="vname" value="<?php echo $vname;?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="show_help_text"><?php echo $sprache->show_help_text;?></label>
                        <select class="form-control" id="show_help_text" name="show_help_text">
                            <option value="Y"><?php echo $gsprache->yes;?></option>
                            <option value="N"<?php if($show_help_text=='N') echo 'selected';?>><?php echo $gsprache->no;?></option>
                        </select>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-body">
                    <?php foreach($serviceProviders as $sp){ ?>
                    <div class="form-group">
                        <label for="sp<?php echo $sp['sp'];?>"><?php echo $sp['sp'];?></label>
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
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>