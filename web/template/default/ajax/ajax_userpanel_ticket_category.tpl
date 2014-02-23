<select id="topic_name" name="topic" class="span10">
    <?php foreach ($table as $row){ ?>
    <option value="<?php echo $row['id'];?>" ><?php echo $row['topic'];?></option>
    <?php } ?>
</select>