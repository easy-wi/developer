<section class="content-header">
    <h1><?php echo $gsprache->support;?></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active"><?php echo $gsprache->support;?></li>
        <li class="active"><?php echo $topic;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">

    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-body">
            <form role="form" action="userpanel.php?w=ti&amp;d=md&amp;id=<?php echo $id;?>&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
                <input type="hidden" name="token" value="<?php echo token();?>">                 
                
            	<div class="form-group">
                    <label><?php echo $sprache->status;?></label>
                    <input type="text" class="form-control" placeholder="<?php echo $status;?>" disabled/>
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
            <?php } ?>
                </div>
                <div class="input-group">
                    <label class="input-group-addon" for="problem"><?php echo $sprache->answer;?></label>
                    <textarea class="form-control" id="problem" name="ticket" rows="10"></textarea>
                </div>
			</div><!-- /.box-body -->
        </div><!-- /.box -->
					<label class="control-label" for="inputEdit"></label>
						<button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-save">&nbsp;<?php echo $gsprache->save;?></i></button>
                        <input type="hidden" name="action" value="wr">
        </form>
    </div><!-- ./col -->

<!-- timeline time label -->
    <div class="col-md-6">
    <?php foreach ($table as $table_row) { ?>
        <ul class="timeline">

            <li class="time-label">
                <span class="bg-blue">
                    <?php echo $table_row['writedate'];?>
                </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li>
                <!-- timeline icon -->
                <i class="fa fa-envelope bg-blue"></i>
                <div class="timeline-item">
                    <h3 class="timeline-header"><?php echo $sprache->writer.': '.$table_row['writer'];?> ...</h3>
                    <div class="timeline-body">
                        <?php echo $table_row['ticket'];?>
                    </div>
                    <div class='timeline-footer'>
                    </div>
                </div>
            </li> 
        </ul>
	<?php } ?>
	</div><!-- ./col -->
<!-- END timeline item -->
</section>

