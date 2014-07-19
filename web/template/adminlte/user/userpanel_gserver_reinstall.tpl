<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->gameserver;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php><i class="fa fa-home"></i> Home</a></li>
        <li><a href="userpanel.php?w=gs"><?php echo $gsprache->gameserver;?></a></li>
        <li class="active"><?php echo $gsprache->gameserver." ".$sprache->reinstall;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">

	<!-- Content Help -->
    <div class="row hidden-xs">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissable">
                <i class="fa fa-info"></i>
                <?php echo $sprache->help_reinstall;?>
            </div>
        </div>
    </div>

    <div class="box box-info">
        <div class="box-body">
			<form role="form" action="userpanel.php?w=gs&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=gs" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
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
                <div class="form-group">
                    <label for="game"><?php echo $gsprache->game;?></label>
                    <select class="form-control" id="game" name="game">
                    	<?php foreach ($table as $table_row){ ?>
						<option value="<?php echo $table_row['id'];?>" data-shorten="<?php echo $table_row['shorten'];?>"><?php echo $table_row['description'];?></option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="form-group">
                	<label for="template"><?php echo $gsprache->template;?></label>
                    <select class="form-control" id="template" name="template">
                        <option id="template1" value="1"></option>
						<option id="template2" value="2"></option>
						<option id="template3" value="3"></option>
                        <option value="4"><?php echo $gsprache->all;?></option>
                    </select>
            	</div>
                
                	<label><?php echo $sprache->type;?></label>
                	<input class="form-control" type="hidden" id="type" name="type" value="N">
                    <br/>
		    			<button id="resync" type="button" class="btn btn-primary" value="N"><?php echo $sprache->resync;?></button>
		    			<button id="reinstall" type="button" class="btn btn-primary" value="Y"><?php echo $sprache->reinstall;?></button>
        </div>
	</div>
                	<label for="inputEdit"></label>
                        <button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-play-circle"></i> <?php echo $gsprache->exec;?></button>
                        <input class="form-control" type="hidden" name="action" value="ri">
        	</form>
</section>


