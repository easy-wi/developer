<section class="content-header">
    <h1><?php echo $gsprache->support;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ti"><i class="fa fa-life-ring"></i> <?php echo $gsprache->support;?></a></li>
        <li class="active"><?php echo $topic;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <form role="form" action="admin.php?w=ti&amp;d=md&amp;id=<?php echo $id;?>&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="wr">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputStatus"><?php echo $sprache->status;?></label>
                            <input id="inputStatus" type="text" class="form-control" placeholder="<?php echo $status;?>" disabled>
                        </div>


                        <div class="form-group">
                            <label for="priority"><?php echo $sprache->priority;?></label>
                            <select class="form-control" id="priority" name="priority">
                                <option value="1"><?php echo $sprache->priority_low;?></option>
                                <option value="2" <?php if($realPriority==2) echo 'selected="selected"'; ?>><?php echo $sprache->priority_medium;?></option>
                                <option value="3" <?php if($realPriority==3) echo 'selected="selected"'; ?>><?php echo $sprache->priority_high;?></option>
                                <option value="4" <?php if($realPriority==4) echo 'selected="selected"'; ?>><?php echo $sprache->priority_very_high;?></option>
                                <option value="5" <?php if($realPriority==5) echo 'selected="selected"'; ?>><?php echo $sprache->priority_critical;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputStatus"><?php echo $sprache->status;?></label>
                            <select id="inputStatus" class="form-control" name="state">
                                <option value="A"><?php echo $sprache->status_author;?></option>
                                <option value="D" <?php if($state=='D') echo 'selected="selected"'; ?>><?php echo $sprache->status_done;?></option>
                                <option value="N"><?php echo $sprache->status_new;?></option>
                                <option value="P" <?php if($state=='P' OR $state=='N') echo 'selected="selected"'; ?>><?php echo $sprache->status_process;?></option>
                                <option value="R" <?php if($state=='R') echo 'selected="selected"'; ?>><?php echo $sprache->status_reopen;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inputSupporter"><?php echo $sprache->edit2;?></label>
                            <select id="inputSupporter" class="form-control" name="supporter">
                                <option value=""></option>
                                <?php foreach($supporterList as $k=>$v){ ?><option value="<?php echo $k;?>" <?php if($k==$admin_id) echo 'selected="selected"'; ?>><?php echo $v;?></option><?php }?>
                            </select>
                        </div>

                        <?php if($open=="Y") { ?>
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