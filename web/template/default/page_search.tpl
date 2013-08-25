<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li><?php echo $page_data->pages['search']['linkname'];?> <span class="divider">/</span></li>
            <li class="active"><?php echo $searchStringValue;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form class="form-signin" action="<?php echo $page_data->pages['search']['link'];?>" method="post">
            <div class="control-group">
                <div class="controls">
                    <label class="control-label" for="inputSearch"></label>
                    <div class="input-append">
                        <input class="input-block-level" id="inputSearch" type="text" name="search" value="<?php echo $searchStringValue;?>" maxlength="22"  >
                        <button class="btn btn-primary" type="submit"><i class="icon-search icon-white"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<hr>
<?php if(isset($results)) { foreach ($results as $v) { ?>
<div class="row-fluid">
    <h2><img src="<?php echo $page_data->pageurl.'/images/flags/'.$v['language'];?>.png" alt="" /> <?php echo $v['type'];?>: <?php echo $v['href'];?></h2>
    <div class="span11">
        <?php echo $v['text'];?>
    </div>
</div>
<?php }} ?>