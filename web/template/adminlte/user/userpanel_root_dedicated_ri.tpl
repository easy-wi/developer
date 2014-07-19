<!-- Content Header -->
<section class="content-header">
    <h1><?php echo $gsprache->dedicated.' '.$sprache->rescue.' / '.$sprache->reinstall;?></h1>
    <ol class="breadcrumb">
        <li><a href="userpanel.php"><i class="fa fa-home"></i> Home</a></li>
		<li><a href="userpanel.php?w=de"><?php echo $gsprache->dedicated;?></a></li>
		<li><?php echo $sprache->rescue.' / '.$sprache->reinstall;?></li>
		<li class="active"><?php echo $ip;?></li>
    </ol>
</section>
<!-- Main Content -->
<section class="content">

<div class="col-md-6">	
    <div class="box box-info">	
        <div class="box-body">
			<?php if(isset($error)){ ?>
				<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fa fa-warning"></i> <?php echo $error?></div>
			<?php }else{ ?>
				<dl class="dl-horizontal">
					<dt><?php echo $sprache->status?>:</dt>
					<dd><?php echo $status;?></dd>
					<dt><?php echo $sprache->description?>:</dt>
					<dd><?php echo $description." ".$bitversion;?> Bit</dd>
					<dt><?php echo $sprache->initialRescuPass;?>:</dt>
					<dd><?php echo $pass;?></dd>
					<?php foreach(customColumns('S',$id) as $row){ ?>
					<dt><?php echo $row['menu'];?>:</dt>
					<dd><?php echo $row['value'];?></dd>
					<?php }?>
				</dl>
		</div>
	</div>
</div>

<div class="col-md-6">	
    <div class="box box-info">	
        <div class="box-body">
			<form role="form" action="userpanel.php?w=de&amp;d=ri&amp;id=<?php echo $id;?>&amp;r=de" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
				<input type="hidden" name="token" value="<?php echo token();?>">
				<div class="form-group">
					<label for="inputAction"><?php echo $sprache->action?></label>
					<div class="controls">
						<select class="form-control" id="inputAction" name="action" onchange="SwitchShowHideRows(this.value);">
							<?php echo implode('',$option);?>
						</select>
					</div>
				</div>
				<?php if($showImages){ ?>
				<div class="ri display_none switch form-group warning">
					<?php echo $sprache->attention;?>
				</div>
				<div class="ri display_none switch form-group">
					<label class="form-label" for="inputImage"><?php echo $gsprache->template;?></label>
					<div class="controls">
						<select class="form-control" id="inputImage" name="imageid">
							<?php foreach ($templates as $template){ ?>
							<option value="<?php echo $template['id']?>"><?php echo $template['description'];?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<?php } ?>
			<?php }?>
		</div>
	</div>
					<label for="inputEdit"></label>
					<button class="btn btn-primary" id="inputEdit" type="submit"><i class="fa fa-refresh"></i> <?php echo $sprache->reinstall;?></button>
			</form>	
</div>		
</section>