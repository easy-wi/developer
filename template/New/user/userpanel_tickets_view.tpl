<section class="content-header">
    <h1><?php echo $gsprache->support;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=ti"><i class="fa fa-life-ring"></i> <?php echo $gsprache->support;?></a></li>
        <li class="active"><?php echo $topic;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-4">
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
                        <dd><a href="userpanel.php?w=ti&d=md&amp;id=<?php echo $id;?>&amp;action=md"><span class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a></dd>
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
 <div class="list-group">
        <?php foreach ($table as $table_row) { ?>
        <?php if($lastdate!=$table_row['writedate']){ ?>
          <li class="list-group-item"><?php echo $table_row['writedate'];?></li>
            <?php }; $lastdate=$table_row['writedate'];?>
             <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                  <h5 class="mb-1"><b><?php echo $sprache->writer.': '.$table_row['writer'];?> ...</b></h5>
              <small><?php echo $table_row['writeTime'];?></small>
            </div>
                <p class="mb-1"><?php echo html_entity_decode($table_row['ticket']);?></p>
             <small></small>
            </a>
         <?php } ?>
            </div>
        </div>
    </div>
</section>

