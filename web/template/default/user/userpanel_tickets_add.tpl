<section class="content-header">
    <h1><?php echo $gsprache->support;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=ti"><?php echo $gsprache->support;?></a></li>
        <li class="active"><?php echo $gsprache->support2;?></li>
    </ol>
</section>

<!-- Main Content -->
<section class="content">
    <div class="row">
        <div class="col-md-11">
            <div class="box box-success">

                <form role="form" action="userpanel.php?w=ti&amp;d=ad&amp;r=ti" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">

                    <input type="hidden" name="token" value="<?php echo token();?>">
                    <input type="hidden" name="action" value="ad">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="priority"><?php echo $sprache->priority;?></label>
                            <select class="form-control" id="priority" name="userPriority">
                                <option value="1"><?php echo $sprache->priority_low;?></option>
                                <option value="2"><?php echo $sprache->priority_medium;?></option>
                                <option value="3"><?php echo $sprache->priority_high;?></option>
                                <option value="4"><?php echo $sprache->priority_very_high;?></option>
                                <option value="5"><?php echo $sprache->priority_critical;?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="topic_maintopic"><?php echo $sprache->topic_name;?></label>
                            <select class="form-control" id="topic_maintopic" name="maintopic" onchange="getdetails('ajax.php?d=userTicketCategories&amp;topicName=', this.value, 'topic_name_sub')">
                                <?php foreach ($table as $table_row){ ?>
                                <option value="<?php echo $table_row['id'];?>" ><?php echo $table_row['topic'];?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="topic_name_sub"><?php echo $sprache->topic_name_sub;?></label>
                            <select class="form-control" id="topic_name_sub" name="topic">
                                <?php foreach ($table2 as $table_row2){ ?>
                                <option value="<?php echo $table_row2['id'];?>" ><?php echo $table_row2['topic'];?></option>>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="problem"><?php echo $sprache->problem;?></label>
                            <textarea class="form-control" id="problem" name="ticket" rows="10"></textarea>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success" id="inputEdit" type="submit"><i class="fa fa-plus-circle">&nbsp;<?php echo $gsprache->add;?></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>