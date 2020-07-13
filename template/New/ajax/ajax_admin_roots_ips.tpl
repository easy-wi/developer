<select id="inputIp" class="span11" name="ip">
    <?php foreach($ipsAvailable as $i){ ?>
    <option<?php if($i==$ip) echo ' selected="selected"';?>><?php echo $i;?></option>
    <?php } ?>
</select>