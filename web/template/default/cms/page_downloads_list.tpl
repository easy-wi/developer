<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_data->pages['downloads']['linkname'];?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span10">
        <?php foreach($table as $row){ ?>
        <h4><?php echo $row['description'];?></h4>
        <div class="row-fluid">
            <div class="span8">
                <?php echo $row['text'];?>
            </div>
            <div class="span4">
                <a href="<?php echo $row['link'];?>"><button class="btn btn-primary"><i class="icon-white icon-download"></i></button></a>
            </div>
        </div>
        <?php }?>
    </div>
</div>