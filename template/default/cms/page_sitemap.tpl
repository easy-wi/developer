<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_data->pages['sitemap']['linkname'];?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <h3>Pages List</h3>
        <ul class="unstyled">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a></li>
            <li>
                <a href="#">Easy-Wi</a>
                <ul>
                    <?php
function getSubLinks2($getamount,$pagelist,$id,$sub=1){
	if ($getamount==true and isset($pagelist[$id])) {
		return count($pagelist[$id]);
	} else if ($getamount==true) {
		return 0;
	} else {
		$return='';
		if(isset($pagelist[$id]) and count($pagelist[$id])>0){
                    $return.='<ul>';
                        foreach($pagelist[$id] as $k=>$sl){
                        if ($id!=$k){
                        $return.='<li>'.$sl['href'].'</li>';
                        $return.=getSubLinks2(false,$pagelist,$k,$sub+1);
                        }
                        }
                        $return.='</ul>';
                    }
                    return $return;
                    }
                    }
                    foreach ($page_data->pages as $key=>$value){
                    if(isid($key,19) and in_array(getSubLinks2(true,$page_data->pages,$key),array(0,1))){
                    echo '<li>'.$value[$key]['href'].'</li>';
                    } else if (isid($key,19) and getSubLinks2(true,$page_data->pages,$key)>0) {
                    echo '<li>';
                        echo $value[$key]['href'];
                        echo getSubLinks2(false,$page_data->pages,$key);
                        echo '</li>';
                    }
                    }
                    ?>
                    <li><?php echo $page_data->pages['gallery']['href'];?></li>
                </ul>
            </li>
            <li>
                <?php echo $page_data->pages['contact']['href'];?>
                <ul>
                    <?php if($easywiModules['ip']){ ?><li><?php echo $page_data->pages['imprint']['href'];?></li><?php }?>
                    <li><?php echo $page_data->pages['contact']['href'];?></li>
                </ul>
            </li>
            <li><?php echo $page_data->pages['news']['href'];?></li>
        </ul>
    </div>
    <div class="span6">
        <h3><?php echo $page_data->pages['news']['linkname'];?></h3>
        <?php foreach ($page_data->last_news as $v) { ?>
        <div class="row-fluid">
            <h4><a href="<?php echo $v['link'];?>"><?php echo $v['href'];?></a></h4>
            <div class="span12">
                <?php echo $v['text'];?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>