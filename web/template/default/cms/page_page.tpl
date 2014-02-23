<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <?php if (isset($breadcrumbs)) { foreach($breadcrumbs as $v) { echo '<li>'.$v['href'].' <span class="divider">/</span></li>';?><?php }} ?>
            <li class="active"><?php echo $page_title;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <h2><?php echo $page_title;?></h2>
    <div class="span11"><?php echo $page_text;?></div>
    <p><?php echo implode(', ',$tag_tags);?></p>
</div>