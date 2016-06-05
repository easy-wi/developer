<?php if($ui->st('w','get')=='se'){ ?>
<label for="inputSelect"></label>
<select class="form-control" id="inputSelect" name="serverID">
    <?php foreach ($data as $value) echo $value;?>
</select>
<?php } ?>