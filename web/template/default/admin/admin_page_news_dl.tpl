<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=pn"><?php echo $gsprache->news;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->del;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $page_title;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=pn&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=pn" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">
            <dl class="dl-horizontal">
                <dt><?php echo $sprache->title;?></dt>
                <dd><?php echo $page_title;?></dd>
                <dt><?php echo $sprache->languages;?></dt>
                <dd><?php echo implode(', ',$p_languages);?></dd>
                <dt><?php echo $sprache->released;?></dt>
                <dd><?php echo $page_active;?></dd>
            </dl>
            <div class="control-group">
                <label class="control-label" for="inputDelete"></label>
                <div class="controls">
                    <button class="btn btn-danger pull-right" id="inputDelete" type="submit"><i class="fa fa-trash-o"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>