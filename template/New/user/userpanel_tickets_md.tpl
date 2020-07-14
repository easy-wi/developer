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
                <form role="form" action="userpanel.php?w=ti&amp;d=md&amp;id=<?php echo $id;?>&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="wr">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputStatus"><?php echo $sprache->status;?></label>
                            <input id="inputStatus" type="text" class="form-control" placeholder="<?php echo $status;?>" disabled/>
                        </div>

                        <?php if($open=="Y") { ?>

                        <div class="form-group">
                            <label for="priority"><?php echo $sprache->priority;?></label>
                            <select class="form-control" id="priority" name="userPriority">
                                <option value="1"><?php echo $sprache->priority_low;?></option>
                                <option value="2" <?php if($userPriority==2) echo 'selected="selected"'; ?>><?php echo $sprache->priority_medium;?></option>
                                <option value="3" <?php if($userPriority==3) echo 'selected="selected"'; ?>><?php echo $sprache->priority_high;?></option>
                                <option value="4" <?php if($userPriority==4) echo 'selected="selected"'; ?>><?php echo $sprache->priority_very_high;?></option>
                                <option value="5" <?php if($userPriority==5) echo 'selected="selected"'; ?>><?php echo $sprache->priority_critical;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="problem"><?php echo $sprache->answer;?></label>
                            <textarea class="form-control" id="problem" name="ticket" rows="10"></textarea>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                    </div>
                </form>
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
        </div>
    </div>
</section>