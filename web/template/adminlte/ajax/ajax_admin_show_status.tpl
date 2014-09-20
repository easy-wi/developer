<?php if($id==null){ ?>
<span class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></span>
<?php }else if($id==1){ ?>
<span class="btn btn-danger btn-sm"><i class="fa fa-ban"></i></span>
<?php }else if($id==2){ ?>
<span class="btn btn-warning btn-sm"><i class="fa fa-exclamation-triangle"></i></span>
<?php }else{ ?>
<span class="btn btn-success btn-sm"><i class="fa fa-check-circle"></i></span>
<?php } ?>
