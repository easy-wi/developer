<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="<?php echo $page_data->pageurl;?>">Home</a> <span class="divider">/</span></li>
            <li><?php echo $page_data->pages['lendserver']['href'];?> <span class="divider">/</li>
            <li class="active"><?php if(isset($servertype) and $servertype=='g'){ echo $page_data->pages['lendservergs']['linkname'];}else{ echo $page_data->pages['lendservervoice']['linkname'];}?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span5">
        <h2 class="form-signin-heading">Error</h2>
        <p><?php echo $sprache->ipblock;?></p>
    </div>
</div>