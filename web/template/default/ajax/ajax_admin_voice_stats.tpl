<?php if(count($data)>0) { ?>
<label for="inputSelect"></label>
<?php if($ui->st('w','get')=='se'){ ?>
<select class="form-control" id="inputSelect" name="serverID">
<?php } else if($ui->st('w','get')=='ma'){ ?>
<select class="form-control" id="inputSelect" name="masterID">
<?php } else if($ui->st('w','get')=='us'){ ?>
<select class="form-control" id="inputSelect" name="userID">
<?php } ?>
<?php foreach ($data as $value) echo $value;?>
</select>
<?php } ?>