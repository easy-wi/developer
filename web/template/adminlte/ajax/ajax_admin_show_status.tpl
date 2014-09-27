<?php if($id==null){ ?>
<span class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i> <?php echo $description;?></span>
<?php }else if($id==1){ ?>
<span class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo $description;?></span>
<?php }else if($id==2){ ?>
<span class="btn btn-warning btn-sm"><i class="fa fa-exclamation-triangle"></i> <?php echo $description;?></span>
<?php }else if($id==3){ ?>
<span class="btn btn-info btn-sm"><i class="fa fa-info-circle"></i> <?php echo $description;?></span>
<?php }else{ ?>
<span class="btn btn-success btn-sm"><i class="fa fa-check-circle"></i> <?php echo $description;?></span>
<?php } ?>
