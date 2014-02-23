<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->pages;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->overview;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <?php echo $gsprache->pages;?> <a href="admin.php?w=pp&amp;d=ad"><span class="btn btn-primary btn-mini"><i class="icon-white icon-plus-sign"></i></span></a>
    </div>
</div>
<hr>
<div class="row-fluid">
    <div class="span11 pagination">
        <ul>
            <li><a href="admin.php?w=pp&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $zur;?>"><i class="icon-step-backward"></i></a></li>
            <li><a href="admin.php?w=pp&amp;o=<?php echo $o;?>&amp;a=20&amp;p=<?php echo $start; ?>">20</a></li>
            <li><a href="admin.php?w=pp&amp;o=<?php echo $o;?>&amp;a=50&amp;p=<?php echo $start; ?>">50</a></li>
            <li><a href="admin.php?w=pp&amp;o=<?php echo $o;?>&amp;a=100&amp;p=<?php echo $start; ?>">100</a></li>
            <li><a href="admin.php?w=pp&amp;o=<?php echo $o;?>&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&amp;p=<?php echo $vor;?>"><i class="icon-step-forward"></i></a></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11">
        <form method="post" action="admin.php?w=pp">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="pageorder" value="true">
            <table class="table table-bordered table-hover table-striped footable">
                <thead>
                <tr>
                    <th data-class="expand"><a href="admin.php?w=pp&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='at') { echo 'dt'; } else { echo 'at'; } ?>"><?php echo $sprache->title;?></a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=pp&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='ai') { echo 'di'; } else { echo 'ai'; } ?>">ID</a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=pp&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='al') { echo 'dl'; } else { echo 'al'; } ?>"><?php echo $sprache->languages;?></a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=pp&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='ar') { echo 'dr'; } else { echo 'ar'; } ?>"><?php echo $sprache->released;?></a></th>
                    <th data-hide="phone"><a href="admin.php?w=pp&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='aa') { echo 'da'; } else { echo 'aa'; } ?>"><?php echo $sprache->author;?></a></th>
                    <th data-hide="phone,tablet"><a href="admin.php?w=pp&amp;a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='ad') { echo 'dd'; } else { echo 'ad'; } ?>"><?php echo $sprache->date;?></a></th>
                    <th data-hide="phone"><a href="admin.php?w=pp&a=<?php if(!isset($amount)) echo "20"; else echo $amount; ?>&p=<?php echo $start;?>&amp;o=<?php if ($o=='as') { echo 'ds'; } else { echo 'as'; } ?>"><?php echo $sprache->sort;?></a></th>
                    <th><?php echo $gsprache->del;?></th>
                    <th><?php echo $gsprache->mod;?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($table as $table_row) { ?>
                <tr>
                    <td><a href="<?php echo $table_row['link'];?>" alt="<?php echo $table_row['title'];?>" target="_blank"><?php echo $table_row['title'];?></a></td>
                    <td><?php echo $table_row['id'];?></td>
                    <td><?php echo implode(', ',$table_row['languages']);?></td>
                    <td><?php echo $table_row['released'];?></td>
                    <td><?php echo $table_row['author'];?></td>
                    <td><?php echo $table_row['date'];?></td>
                    <td><label class="form-inline"><input class="input-mini" type="number" name="pageid[<?php echo $table_row['id'];?>]" value="<?php echo $table_row['sort'];?>" class="page_order" ></label></td>
                    <td><a href="admin.php?w=pp&amp;d=dl&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-danger"><i class="fa fa-trash-o"></i></span></a></td>
                    <td><a href="admin.php?w=pp&amp;d=md&amp;id=<?php echo $table_row['id'];?>" ><span class="btn btn-mini btn-primary"><i class="icon-white icon-edit"></i></span></a></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                </div>
            </div>
        </form>
    </div>
</div>