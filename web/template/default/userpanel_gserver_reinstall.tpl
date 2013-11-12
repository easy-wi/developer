<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $gsprache->gameserver." ".$sprache->reinstall;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid hidden-phone">
    <div class="span12 alert alert-info"><?php echo $sprache->help_reinstall;?></div>
</div>
<hr>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="userpanel.php?w=gs&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            
            <div class="control-group">
                <label class="control-label" for="game"></label>
                <div class="controls">
                    <select id="game" name="game">
                    	<?php foreach ($table as $table_row){ ?>
                        <option value="$table_row['shorten']"><?php echo $table_row['description'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="template"></label>
                <div class="controls">
                    <select id="template" name="template">
                    	<option value="4"><?php echo $gsprache->all;?></option>
                        <option value="1"></option>
						<option value="2">-2</option>
						<option value="3">-3</option>
                    </select>
                </div>
            </div>
            
            
            
            <div class="control-group">
                <label class="control-label"> </label>
                <div class="controls">
                    <div class="btn-group" data-toggle="buttons-radio">
		    			<input type="button" class="btn btn-primary" name="type" id="resync" value="resync"><?php echo $sprache->resync;?></input>
		    			<input type="button" class="btn btn-primary" name="type" id="reinstall" value="reinstall"><?php echo $sprache->reinstall;?></input>
					</div>
                </div>
            </div>
            
            
			<div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->exec;?></button>
                    <input type="hidden" name="action" value="ri">
                </div>
            </div>
            
            
            
            
            <div class="control-group">
                <label class="checkbox inline" for="resync"><?php echo $sprache->resync;?></label>
                <div class="controls checkbox inline">
                    <input id="resync" type="radio" name="type" value="N" checked="checked">
                </div>
                <label class="checkbox inline" for="reinstall"><?php echo $sprache->reinstall;?></label>
                <div class="controls checkbox inline">
                    <input id="reinstall" type="radio" name="type" value="Y">
                </div>
            </div>
            <?php foreach ($table as $table_row){ ?>
            <div class="control-group">
                <label class="control-label" for="template-<?php echo $table_row['id'];?>"><?php echo '<img src="images/games/icons/'.$table_row['shorten'].'.png" alt="'.$table_row['shorten'].'" width="14" /> '.$table_row['description'];?></label>
                <div class="controls">
                    <select id="template-<?php echo $table_row['id'];?>" name="template[<?php echo $table_row['id'];?>]">
                        <option value="0"><?php echo $gsprache->no;?></option>
                        <option value="1" <?php if($table_row['servertemplate']==1) echo "selected";?>><?php echo $table_row['shorten'];?></option>
                        <option value="2" <?php if($table_row['servertemplate']==2) echo "selected";?>><?php echo $table_row['shorten'];?>-2</option>
                        <option value="3" <?php if($table_row['servertemplate']==3) echo "selected";?>><?php echo $table_row['shorten'];?>-3</option>
                        <option value="4"><?php echo $gsprache->all;?></option>
                    </select>
                </div>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i> <?php echo $gsprache->save;?></button>
                    <input type="hidden" name="action" value="ri">
                </div>
            </div>
        </form>
    </div>
</div>