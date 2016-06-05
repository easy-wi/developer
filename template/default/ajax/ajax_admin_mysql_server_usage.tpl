<div class="form-group">
    <label for="inputInstalled"><?php echo $sprache->usage;?></label>
    <div class="controls">
        <input class="form-control" id="inputInstalled" type="text" name="installed" value="<?php echo $installed.'/'.$max;?>" disabled="disabled">
    </div>
</div>

<div class="form-group">
    <label for="inputMaxQueriesPerHour"><?php echo $sprache->max_queries_per_hour;?></label>
    <div class="controls">
        <input class="form-control" id="inputMaxQueriesPerHour" type="number" name="max_queries_per_hour" value="<?php echo $max_queries_per_hour;?>" required>
    </div>
</div>

<div class="form-group">
    <label for="inputMaxUpdatesPerHour"><?php echo $sprache->max_updates_per_hour;?></label>
    <div class="controls">
        <input class="form-control" id="inputMaxUpdatesPerHour" type="number" name="max_updates_per_hour" value="<?php echo $max_updates_per_hour;?>" required>
    </div>
</div>
<div class="form-group">
    <label for="inputMaxConnectionsPerHour"><?php echo $sprache->max_connections_per_hour;?></label>
    <div class="controls">
        <input class="form-control" id="inputMaxConnectionsPerHour" type="number" name="max_connections_per_hour" value="<?php echo $max_connections_per_hour;?>" required>
    </div>
</div>

<div class="form-group">
    <label for="inputMaxUserConnectionsPerHour"><?php echo $sprache->max_userconnections_per_hour;?></label>
    <div class="controls">
        <input class="form-control" id="inputMaxUserConnectionsPerHour" type="number" name="max_userconnections_per_hour" value="<?php echo $max_userconnections_per_hour;?>" required>
    </div>
</div>