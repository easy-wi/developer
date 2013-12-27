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
    <div class="span8">
        <form class="form-horizontal" action="userpanel.php?w=gs&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <script type="text/javascript">
			$(document).ready(function (){
				$('#game').change(function() {
					var shorten=$('#game').find(':selected').data('shorten');
					$('#template1').text(shorten);
					$('#template2').text(shorten + '-2');
					$('#template3').text(shorten + '-3');
				});		
				$('div[data-toggle="buttons-radio"] .btn').click(function() {
					$(this).parent().parent().find('input').val($(this).val());
				});
				
				$('#game').change();
				$('#resync').button('toggle');
			});
			</script>  
            <input type="hidden" name="token" value="<?php echo token();?>">     
            <div id="gameGroup" class="control-group">
                <label class="control-label" for="game"><?php echo $gsprache->game;?></label>
                <div class="controls">
                    <select id="game" name="game">
                    	<?php foreach ($table as $table_row){ ?>
                        <option value="<?php echo $table_row['id'];?>" data-shorten="<?php echo $table_row['shorten'];?>"><?php echo $table_row['description'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div id="templateGroup" class="control-group">
                <label class="control-label" for="template"><?php echo $gsprache->template;?></label>
                <div class="controls">
                    <select id="template" name="template">
                        <option id="template1" value="1"></option>
						<option id="template2" value="2"></option>
						<option id="template3" value="3"></option>
                        <option value="4"><?php echo $gsprache->all;?></option>
                    </select>
                </div>
            </div>    
            <div id="typeGroup" class="control-group">
                <label class="control-label"><?php echo $sprache->type;?></label>
                <div class="controls">
                	<input type="hidden" id="type" name="type" value="N">
                    <div class="btn-group" data-toggle="buttons-radio">
		    			<button id="resync" type="button" class="btn btn-primary" value="N"><?php echo $sprache->resync;?></button>
		    			<button id="reinstall" type="button" class="btn btn-primary" value="Y"><?php echo $sprache->reinstall;?></button>
					</div>
                </div>
            </div>
			<div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary" id="inputEdit" type="submit"><i class="icon-play-circle icon-white"></i> <?php echo $gsprache->exec;?></button>
                    <input type="hidden" name="action" value="ri">
                </div>
            </div>
        </form>
    </div>
</div>