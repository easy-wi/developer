<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_data->pages['news']['linkname'];?></li>
        </ul>
    </div>
</div>
<?php foreach($news as $single) { ?>
<div class="row-fluid">
    <h2><?php echo $single['href'];?></h2>
    <div class="span11"><?php echo $single['text'];?></div>
    <div class="row-fluid">
        <div class="span6">
            <?php echo $page_sprache->categories.': '.implode(', ',returnPlainArray($single['categories'],'href'));?>
        </div>
        <div class="span6">
            <?php echo $page_sprache->tag.': '.implode(', ',returnPlainArray($single['tags'],'href'));?>
        </div>
    </div>
</div>
<?php } ?>
<div class="row-fluid">
    <div class="pagination">
        <ul>
            <?php $links=array(); for($i=0;$i<$pagesCount;$i++){ $a=$i+1; if($i==$pageOpen){ $links[]='<li class="disabled"><span>'.$a.'</span></li>';} else { $links[]='<li class="active"><span><a href="'.$paginationLink.$i.'">'.$a.'</a></span></li>';}} echo implode(' | ',$links);?>
        </ul>
    </div>
</div>
