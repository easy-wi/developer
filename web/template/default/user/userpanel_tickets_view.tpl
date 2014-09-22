<section class="content-header">
    <h1><?php echo $gsprache->support;?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li><?php echo $gsprache->support;?></li>
        <li class="active"><?php echo $topic;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo $sprache->status;?></dt>
                        <dd><?php echo $status;?></dd>
                        <br>
                        <dt><?php echo $sprache->priority;?></dt>
                        <dd><?php echo $priority;?></dd>
                        <br>
                        <?php if($open=="Y") { ?>
                        <dt><?php echo $gsprache->mod;?></dt>
                        <dd><a href="userpanel.php?w=ti&d=md&amp;id=<?php echo $id;?>&amp;action=md"><span class="btn btn-primary btn-sm"><i class="fa fa-exchange"></i></a></dd>
                        <br>
                        <dt><?php echo $sprache->close_heading;?></dt>
                        <dd><a href="userpanel.php?w=ti&d=md&amp;id=<?php echo $id;?>&amp;action=cl"><span class="btn btn-primary btn-sm"><i class="fa fa-lock"></i></span></a></dd>
                        <br>
                        <?php } else if ($open=="D") { ?>
                        <dt><?php echo $sprache->reopen;?></dt>
                        <dd><a href="userpanel.php?w=ti&d=md&amp;id=<?php echo $id;?>&amp;action=op&amp;r=ti"><span class="btn btn-primary btn-sm"><i class="fa fa-unlock"></i></a></dd>
                        <?php } ?>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <ul class="timeline">
                <?php foreach ($table as $table_row) { ?>
                <?php if($lastdate!=$table_row['writedate']){ ?>
                <li class="time-label"><span class="bg-green"><?php echo $table_row['writedate'];?></span></li>
                <?php }; $lastdate=$table_row['writedate'];?>

                <li>
                    <i class="fa fa-envelope bg-blue"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> <?php echo $table_row['writeTime'];?></span>
                        <h3 class="timeline-header"><?php echo $sprache->writer.': '.$table_row['writer'];?> ...</h3>
                        <div class="timeline-body">
                            <?php echo $table_row['ticket'];?>
                        </div>
                    </div>
                </li>
                <?php } ?>

                <li>
                    <i class="fa fa-clock-o"></i>
                </li>
            </ul>
        </div>
    </div>
</section>