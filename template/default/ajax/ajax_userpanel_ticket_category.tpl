<?php
$i = 0;
foreach($table as $k=>$v){ ?>
<option value="<?php echo $k;?>" <?php if($i == 0) echo "selected";?>><?php echo $v;?></option>
<?php $i++; } ?>