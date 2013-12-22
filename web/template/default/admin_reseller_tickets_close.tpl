<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=tr"><?php echo $gsprache->support;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $sprache->close_heading;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=tr&amp;d=md&amp;id=<?php echo $id;?>&amp;r=tr" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <div class="control-group">
                <label class="control-label" for="rating"><?php echo $sprache->rating;?></label>
                <div class="controls">
                    <select id="rating" name="rating">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="comment"><?php echo $sprache->comment;?></label>
                <div class="controls">
                    <textarea id="comment" name="comment" rows="10"></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-lock icon-white"></i></button>
                    <input type="hidden" name="action" value="cl">
                </div>
            </div>
        </form>
    </div>
</div>