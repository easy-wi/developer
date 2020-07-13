<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_sprache->$s;?></li>
        </ul>
    </div>
</div>
<?php foreach($table as $n) { ?>
<div class="row-fluid">
    <h2><a href="<?php echo $n['link'];?>" title="<?php echo $n['title'];?>"><?php echo $n['title'];?></a></h2>
    <div class="span11"><?php echo substr($n['text'],0,500);?></div>
</div>
<?php } ?>