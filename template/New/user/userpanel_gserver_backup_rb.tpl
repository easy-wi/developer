<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><i class="fa fa-gamepad"></i> <?php echo $gsprache->gameserver;?></a></li>
        <li><i class="fa fa-floppy-o"></i> <?php echo $gsprache->backup;?></li>
        <li><?php echo $serverip.":".$port;?></li>
        <li class="active"><?php echo $sprache->recover;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="box box-warning">
                <form role="form" action="userpanel.php?w=bu&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="action" value="rb2">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputTemplate"><?php echo $gsprache->template;?></label>
                            <select class="form-control" name="template" id="inputTemplate">
                                <?php foreach($shortens as $shorten) { echo '<option>'.$shorten.'</option>'; } ?>
                            </select>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-warning" id="inputRecover" type="submit"><i class="fa fa-refresh"></i> <?php echo $sprache->recover;?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>